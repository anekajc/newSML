{{-- Rerouted to newmasterTest — the root 'newmaster' layout is Bootstrap 5 only (no BS4
     fallback), while every other accounting page (girodibuka, giroditerima,
     pelunasanpiutangdpp, penerimaandpp, pengajuandpp) already runs on newmasterTest (BS4).
     Matches the sibling pages and gets this page onto BS4 with zero shared-layout edit — see
     docs/new-design-gudang-style-guide.md §2a and docs/bootstrap4-version-alignment-guide.md.
     Only the list view (#page1) below was ported; the Add/Otorisasi forms and the six
     entity-picker modals (Valas/Devisi/Lawan/Departemen/Custsupp/Perkiraan) are untouched on
     purpose — see the guide's §13 ask-first note on existing picker patterns.

     #page1 (toolbar + table markup, CSS and table id) was made byte-for-byte identical to
     accounting/memorialkoreksi.blade.php / accounting/bonsementara.blade.php on request — same
     `po-*` class family, same `<table id="tabel">`, same action-button/DataTable markup —
     replacing the page's original `.tb-report`/`.toolbar`/`#mainTable` markup (report-table.css's
     own skin; see accounting/pengajuandph.blade.php, this page's sibling, for the identical
     earlier conversion). report-table.css/tableMaster2.css/pengajuandphtunai.css stay loaded
     regardless (unlike the reference pages) because page2+ (the Add/Detail/Otorisasi forms, the
     .dph-tb tables) and the #formPerkiraan entity-picker modal (.rt-picker-v2, no po-*
     equivalent) still depend on them — #page1 just neutralizes the few rules from those files
     that would otherwise leak onto #tabel (see the CSS block below). --}}
@extends('newmasterTest')
@section('page-title', 'Pengajuan DPH')
@section('buttons')
@endsection

@section('css')
    <div id="imagecontainer" class="d-none" style="">
        <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
    </div>

    {{-- Gudang-style list view (#page1 only) — see docs/new-design-gudang-style-guide.md.
     newmasterTest doesn't load report-table.css/tableMaster2.css itself; added here,
     page-local, so no other page on this shared layout is affected. report-table.css/
     tableMaster2.css stay loaded for page2+ (Add/Detail/Otorisasi forms, .dph-tb tables) and
     the #formPerkiraan entity-picker modal (.rt-picker-v2) — #page1 itself no longer uses
     their .tb-report/.tb skin, see the <style> block below. --}}
    <link rel="stylesheet" href="{!! URL::asset('css/report-table.css') !!}?v={{ @filemtime(base_path('public/css/report-table.css')) ?: '1' }}">
    <link rel="stylesheet" href="{!! URL::asset('css/tableMaster2.css') !!}?v={{ @filemtime(base_path('public/css/tableMaster2.css')) ?: '1' }}">

    {{-- Header tabel interaktif (geser kolom + roda gigi + bar kolom tersembunyi + kotak
     scroll bertajuk sticky) dalam skema po-* — lihat po-table-header.css untuk daftar id
     terdaftar (#tabel, dipakai halaman ini, sudah terdaftar di sana untuk halaman-halaman
     lain). Dimuat SETELAH report-table.css supaya .po-*/.rt-colmenu-nya menang saat
     spesifisitas seri. --}}
    <link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
    <link rel="stylesheet" href="{!! URL::asset('css/scrollbar-autohide.css') !!}?v={{ @filemtime(base_path('public/css/scrollbar-autohide.css')) ?: '1' }}">

    {{-- Page-local styles (the pre-existing #tabel_xxx_filter search-box boxes used by the
     picker modals/old DPH tab further down) — moved out of an inline @section('css') into
     their own file to shrink this blade file. Shared with accounting/pengajuandph.blade.php
     (this page's sibling) plus bank.blade.php/kas.blade.php/cetakpengajuandph.blade.php — the
     .len-wrap/.len-inp rule it still carries is now dead code for #page1 (Tampilkan uses
     .po-len-wrap/.po-len-inp below), left in place for those other consumers; verify before
     deleting. --}}
    <link rel="stylesheet"
        href="{!! URL::asset('css/pengajuandphtunai.css') !!}?v={{ @filemtime(base_path('public/css/pengajuandphtunai.css')) ?: '1' }}">

    <style>
        /* Jarak kartu ke bar atas — sama seperti pengajuandph/pengajuandpp/bonsementara. */
        #content { padding-top: 12px; }

        /* po-table-header.css tidak menulis .po-len-wrap/.po-len-inp — disalin apa adanya dari
           accounting/pengajuandph.blade.php, halaman lain di folder ini yang sudah memakai
           skema po-* dan menulis dropdown Tampilkan ini page-local juga. */
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

        /* ==========================================================================
           Dari sini ke bawah: disalin verbatim dari accounting/memorialkoreksi.blade.php
           (dan bonsementara.blade.php) supaya #page1 (list DPH Tunai) memakai class &
           tampilan yang persis sama — lihat permintaan "samakan dengan bonsementara/
           memorialkoreksi" (sudah diterapkan lebih dulu di accounting/pengajuandph.blade.php).
           ========================================================================== */

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
          font-size: 14px;
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
        #tabel { min-width: 100%; }

        /* ---------- Kolom Aksi - tombol bulat kecil warna pastel ---------- */
        #tabel td:first-child:not([colspan]) { vertical-align: middle; }

        #tabel td:first-child .po-aksi-wrap {
          display: flex;
          gap: 4px;
          justify-content: center;
          align-items: center;
        }

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

        #tabel td:first-child .btn:hover {
          filter: brightness(0.97);
          transform: translateY(-1px);
        }

        #tabel td:first-child .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
        #tabel td:first-child .btn-warning { color: #b45309; border-color: #fbe3bd; background: #fef3e0; }
        #tabel td:first-child .btn-primary { color: #2563eb; border-color: #cfdcff; background: #e8edff; }
        #tabel td:first-child .btn-danger  { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }
        #tabel td:first-child .btn-info    { color: #0891b2; border-color: #a5f3fc; background: #ecfeff; }

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

        /* ==========================================================================
           Penetral kebocoran gaya - halaman ini (beda dengan memorialkoreksi) masih
           memuat report-table.css/tableMaster2.css karena page2+ (form tambah/detail/
           otorisasi) dan modal #formPerkiraan masih memakainya. Aturan di bawah ini HANYA
           menimpa balik nilai file-file itu supaya #page1 tetap identik dengan referensi,
           tanpa melepas file-nya (yang akan merusak halaman lain). newmaster.css TIDAK
           dimuat di halaman ini, jadi tidak perlu penetral untuk itu (beda dengan
           accounting/pengajuandph.blade.php).
           ========================================================================== */

        /* tableMaster2.css: skin khusus id #tabel (dipakai juga oleh halaman lain yang
           memuat file itu) - dikembalikan ke nilai .data-table di atas. */
        #page1 #tabel thead th {
          background: #f9fafb !important;
          color: var(--text-muted, #6b7280) !important;
          font-size: 12px !important;
          text-transform: uppercase;
          letter-spacing: .04em;
          font-weight: 600;
          border-bottom: 1px solid var(--border, #e5e7eb) !important;
          border-top: none;
          white-space: normal;
        }
        /* padding/font-size/color SENGAJA TANPA !important - tableMaster2.css tidak menandai
           ketiganya !important pada #tabel tbody td (beda dengan border-color di bawah, yang
           DIPAKSA !important lewat aturan terpisah `#tabel, #tabel th, #tabel td`), jadi
           spesifisitas ekstra dari ID #page1 di sini sudah cukup menang tanpa !important. Kalau
           dipaksa !important, itu akan mengalahkan Bootstrap .text-success/.text-danger (yang
           !important) pada sel Oto - itulah yang membuat ikon centang/silang di kolom Oto
           tampak hitam alih-alih hijau/merah. */
        #page1 #tabel tbody td {
          padding: 12px 16px;
          font-size: 14px;
          color: var(--text-main, #1f2937);
          border-color: #f3f4f6 !important;
          border-left: none;
          border-right: none;
        }
        #page1 #tabel tbody tr:hover { background-color: transparent !important; }
        #page1 #tabel td:last-child { font-weight: inherit !important; }

        /* tableMaster2.css: chrome DataTables (panjang halaman/info/pagination) diwarnai
           ungu (--sp-primary) dan diberi padding tambahan - dikembalikan ke nilai bawaan
           jquery.dataTables.css 1.13.2 (dari public/css/jquery.dataTables.min.css, versi
           yang sama dimuat layout lewat CDN) supaya sama persis dengan memorialkoreksi,
           yang tidak memuat tableMaster2.css sama sekali. */
        #page1 .dataTables_wrapper { padding: 0; }
        #page1 .dataTables_wrapper .dataTables_paginate .paginate_button {
          border-radius: 2px !important;
          margin-left: 2px;
          border: 1px solid transparent !important;
          color: #333 !important;
        }
        #page1 .dataTables_wrapper .dataTables_paginate .paginate_button.current {
          background: #fff !important;
          border-color: #979797 !important;
          color: #333 !important;
        }
        #page1 .dataTables_wrapper .dataTables_info {
          color: inherit;
          font-size: inherit;
          padding-top: 0.755em !important;
        }
    </style>
@endsection


@section('content')
    <div id="page1" class="container-fluid mainpage">

        <!-- <div id="qrcode"></div> -->
        {{-- <div class="row" style="margin-top: -30px">
    <div class="col-6 text-left">
      <h2>Pelunasan Hutang</h2>
    </div>
    <div class="col-6 text-right">
      <button type="button" class="btn btn-chip-biru" onclick="buttonAdd()">+ Tambah DPH</button>
    </div>
  </div>
</div> --}}

        <!-- <button onclick="loadAll()">tes</button> -->
        <div id="printContainer" style="display:none">


        </div>
        <div id="contentContainer" class="container-fluid">
            <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
            <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />

            <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
            <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS !!}" />
            <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
            <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />
            <input type="hidden" id="akses_isotorisasi1" value="{!! $akses->IsOtorisasi1 !!}" />
            <input type="hidden" id="akses_isbatal" value="{!! $akses->IsBatal !!}" />
            <input type="hidden" id="akses_pembatalan" value="{!! $akses->pembatalan !!}" />

            <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

            {{-- .tb-report/.content dilepas (pindah ke skema po-*, lihat catatan di
             @section('css')) — #modalFilter di luar tetap aman karena selalu sudah berada
             di luar .tb-report (lihat catatannya sendiri di bawah). Kartu + toolbar + tabel
             disalin dari accounting/memorialkoreksi.blade.php supaya sama persis. --}}
            <div class="card">
                <div class="card-body" style="padding:0;">

                    <div class="po-toolbar">
                        <div class="po-filter-wrap">
                            <label>Periode</label>
                            <input type="date" class="po-filter-inp" id="inputDate1" value="{!! $date1 !!}">
                            <span class="po-filter-sep">s/d</span>
                            <input type="date" class="po-filter-inp" id="inputDate2" value="{!! $date2 !!}">
                        </div>

                        <input class="po-search-inp" type="search" id="searchBox2" placeholder="Cari data">

                        {{-- Jumlah baris per halaman. -1 = tampilkan semua data (tanpa pager) — diikat
                 ke DataTables lewat ikatPanjangHalaman() di renderTabel(), bukan onchange inline
                 (pola sama seperti outIkatPanjangHalaman() di bonsementara.blade.php). --}}
                        <div class="po-len-wrap">
                            <label for="tabelLen2">Tampilkan</label>
                            <select id="tabelLen2" class="po-len-inp">
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

                    {{-- #rtBar diisi lewat JS oleh ReportTable.init() - lihat dphtInitReportTableSekali(). --}}
                    <div id="rtBar"></div>

                    <table id="tabel" class="data-table po-aksi-hover">
                        <thead id="tabel_header" class="text-center">
                            <tr>
                                <th style="padding: 4px 12px;" scope="col">Actions</th>
                                <th style="padding: 4px 12px;" scope="col">No Bukti</th>
                                <th style="padding: 4px 12px;" scope="col">Supplier</th>
                                <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                                <th style="padding: 4px 12px;" scope="col">Valas</th>
                                <th style="padding: 4px 12px;" scope="col">Nilai</th>
                                <th style="padding: 4px 12px;" scope="col">K/L</th>
                                <th style="padding: 4px 12px;" scope="col">Oto</th>
                                <th style="padding: 4px 12px;" scope="col">User Oto</th>
                                <th style="padding: 4px 12px;" scope="col">Tgl Oto</th>
                            </tr>
                        </thead>
                        <tbody id="tabel_data" class="text-left">
                            {{-- Baris digambar renderTabel() lewat JS, supaya susunan kolom hasil
                                 geser/sembunyi selalu konsisten dengan hasil render ulang. --}}
                        </tbody>
                    </table>

                    <div class="po-rt-hint">
                        <i class="bi bi-info-circle"></i>
                        Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom
                        untuk menyembunyikan kolom.
                    </div>

                </div>
            </div>

        </div>
    </div>
    <!-- closes #page1 (container-fluid mainpage) — was missing, which left #page2/#page3/#page4
         nested INSIDE #page1 instead of siblings. Since buttonDetail()/buttonOtorisasi()/
         buttonKoreksi() all call $('.mainpage').hide() (which also matches and hides #page1
         itself), any nested page's own .show() had no visible effect — its ancestor #page1
         stayed display:none, so Detail/Otorisasi/Edit all rendered blank white. -->

    {{-- modal filter — DILETAKKAN DI LUAR .tb-report, lihat catatan .tb-report * {margin:0;padding:0}
     di new-design-gudang-style-guide.md §3 / new-filter-modal-ui-guide.md §1. --}}
    <div class="modal fade rt-filter" id="modalFilter">
            <div class="modal-dialog modal-md">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-filter"></i>
                            Filter Laporan
                            <span class="rt-active-badge" id="filterBadge">0 aktif</span>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="rt-section">
                            <div class="rt-group-label">Pengaturan Laporan</div>
                            <div class="rt-grid-2">
                                <div>
                                    <label class="rt-field-label" for="modalOtorisasi">Otorisasi</label>
                                    <select class="rt-native" id="modalOtorisasi">
                                        <option value="2">Semua</option>
                                        <option value="1">Sudah Otorisasi</option>
                                        <option value="0">Belum Otorisasi</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="rt-reset-link" onclick="resetAllFilters()">Reset semua</button>
                        <div class="rt-footer-buttons">
                            <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal">Batal</button>
                            <button type="button" class="rt-btn rt-btn-primary"
                                onclick="applyModalFilter()">Terapkan</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- modal filter -->

        <div id="page2" style="display: none" class="mainpage container-fluid">

            <div class="row " style="margin-top: 0" id="contentContainer">
                <div class="col-8 text-left">
                    {{-- <h2>Pengajuan DPH</h2> --}}
                </div>
                <div class="col-4 text-right action-group">
                    <button type="button" class="btn btn-action-danger btn-danger btn-pill-primary"
                        onclick="buttonCloseForm()">CLOSE</button>
                </div>
            </div>

            <div id= "formAdd" class="">
                <div id="formBsGrid" class="">
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
                                                <input type="hidden" class="form-control" id="input_add_nourut"
                                                    placeholder="" disabled>
                                                <input type="text" class="form-control" id="input_add_nobukti"
                                                    placeholder="No Bukti" disabled>
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
                                                <input type="date" class="form-control text-center"
                                                    id="input_add_tanggal" placeholder="" disabled>
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
                                        <div class="col-md-8">
                                            <div class="input-group form-group">
                                                <input id="input_add_valas" type="text" class="form-control" disabled>

                                                {{-- <button id="buttonAddListValas" type="button"
                                                    onclick="buttonAddListValas()" class="btn btn-primary"
                                                    disabled>+</button> --}}

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="container-fluid">
                            <hr />
                        </div>
                        <div class="container-fluid mt-4" style="padding:0; margin:0;">
                            <div class="dph-table-outer">
                                <div class="dph-table-wrap">
                                    <table id="addTable" class="dph-tb">
                                        <thead>
                                            <tr>
                                                <th scope="col">Supplier</th>
                                                <th scope="col">Faktur</th>
                                                <th scope="col" class="num">diBayar</th>
                                                <th scope="col" class="num">kurangBayar</th>
                                                <th scope="col" class="num">lebihBayar</th>
                                                <th scope="col">No.Invoice</th>
                                                <th scope="col">Tgl Invoice</th>
                                                <th scope="col">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="addTableData" class="text-left">
                                            <tr>
                                                <td colspan="8" class="text-center">Belum ada data</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2 text-right">
                            <button id="buttonAddItem" type="button" class="btn btn-chip-biru"
                                onclick="buttonAddItem()">Tambah</button>
                        </div>
                        <div id="formAddAdd" class="container-fluid showhideitem">
                            <!-- <div class="line"></div> -->
                            <!-- <div class="row"> -->
                            <div class="col-12">
                                <hr />
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
                                            <div class="col-md-3">
                                                <div class="input-group form-group">
                                                    <input id="AddAddKodeDevisi" type="text" class="form-control"
                                                        disabled>

                                                    <button id="buttonAddListDevisi" type="button"
                                                        onclick="buttonAddListDevisi()" class="btn btn-chip-biru">+</button>

                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="input-group form-group">
                                                    <input id="AddAddNamaDevisi" type="text" class="form-control"
                                                        disabled>

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
                                            <div class="col-md-3">
                                                <div class="input-group form-group">
                                                    <input id="AddAddValas" type="text" class="form-control"
                                                        value="IDR" disabled>
                                                    {{-- <button id="buttonAddListValas" type="button"
                                                        onclick="buttonAddListValas()" class="btn btn-primary">+</button> --}}

                                                </div>
                                            </div>

                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <label>Kurs</label>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="input-group form-group">
                                                    <input id="AddAddKurs" type="number" value="1.00"
                                                        class="text-right form-control" disabled>
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
                                                    <div class="col-md-3">
                                                        <div class="input-group form-group">
                                                            <input id="AddAddLawan" type="text" class="form-control"
                                                                disabled>
                                                            <input id="AddAddKodeLawan" type="hidden"
                                                                class="form-control" disabled>
                                                            <button id="buttonAddListLawan" type="button"
                                                                onclick="buttonAddListLawan()"
                                                                class="btn btn-chip-biru">+</button>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="input-group form-group">
                                                            <input id="AddAddKeteranganLawan" type="text"
                                                                class="form-control" disabled>

                                                        </div>
                                                    </div>

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
                                                    <label>Jumlah</label>
                                                </div>
                                            </div>
                                            <!-- <div class="col-4 text-right">

            </div> -->
                                            <div class="col-md-3">
                                                <div class="input-group form-group">
                                                    <input id="AddAddJumlah" type="number" value="0.00"
                                                        class="text-right form-control">

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
                                                    <input id="AddAddKeterangan" type="text" value=""
                                                        class="form-control">

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
                                                    <input id="AddAddKeteranganDetail" type="text" value=""
                                                        class="form-control">

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
                                                    <input id="AddAddKodeDepartemen" type="text" class="form-control"
                                                        disabled>
                                                    <button id="buttonAddListDepartemen" type="button"
                                                        onclick="buttonAddListDepartemen()"
                                                        class="btn btn-chip-biru">+</button>

                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="input-group form-group">
                                                    <input id="AddAddNamaDepartemen" type="text" class="form-control"
                                                        disabled>

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
                                                    <input id="AddAddKodeCustsupp" type="text" class="form-control"
                                                        disabled>
                                                    <button id="buttonAddListCustsupp" type="button"
                                                        onclick="buttonAddListCustsupp()"
                                                        class="btn btn-chip-biru">+</button>

                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="input-group form-group">
                                                    <input id="AddAddNamaCustsupp" type="text" class="form-control"
                                                        disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2" style="margin-top: 0">
                                <div class="col-md-12 text-right mt-4" id="contentContainer">
                                    <button type="button" class="btn btn-secondary btn-pill-secondary"
                                        onclick="buttonAddBatal()">Batal</button>

                                    <button id="buttonSubmitAddAdd" type="button" onclick="submitAddAdd()"
                                        class="btn btn-primary btn-pill-primary">Simpan</button>

                                    <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()"
                                        class="btn btn-primary btn-pill-primary">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="page3" style="display: none" class="mainpage container-fluid">

            <div class="row" style="margin-top: 0" id="contentContainer">
                <div class="col-8 text-left">
                    {{-- <h2 class="page3showhide detailshowhide"> Detail Pengajuan DPH</h2> --}}
                    {{-- <h2 class="page3showhide otorisasishowhide"> Otorisasi Pengajuan DPH</h2> --}}
                </div>
                <div class="col-4 text-right action-group">
                    <button type="button" class="btn btn-action-danger btn-danger btn-pill-primary"
                        onclick="buttonCloseForm()">CLOSE</button>
                </div>
            </div>

            <div id= "" class="">
                <div id="formBsGrid" class="">
                    <div class="">
                        <!-- <h1>Tes Modal</h1> -->

                        <div class="container-fluid">
                            <!-- <input type="hidden" name="noUrut" id="input_add_nourut" value="" /> -->

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
                                                <input type="hidden" class="form-control" id="input_detail_nourut"
                                                    placeholder="" disabled>
                                                <input type="text" class="form-control" id="input_detail_nobukti"
                                                    placeholder="No Bukti" disabled>
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
                                                <input type="date" class="form-control text-center"
                                                    id="input_detail_tanggal" placeholder="" disabled>
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
                                                <input id="input_detail_valas" type="text" class="form-control"
                                                    disabled>


                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="container-fluid">
                            <hr />
                        </div>
                        <div class="container-fluid mt-4" style="padding:0; margin:0;">

                            <div class="dph-table-outer">
                                <div class="dph-table-wrap">
                                    <table id="detailTable" class="dph-tb">
                                        <thead>
                                            <tr>
                                                <th scope="col">Supplier</th>
                                                <th scope="col">Faktur</th>
                                                <th scope="col" class="num">diBayar</th>
                                                <th scope="col" class="num">kurangBayar</th>
                                                <th scope="col" class="num">lebihBayar</th>
                                                <th scope="col">No.Invoice</th>
                                                <th scope="col">Tgl Invoice</th>
                                            </tr>
                                        </thead>
                                        <tbody id="detailTableData" class="text-left">
                                            <tr>
                                                <td colspan="7" class="text-center">Belum ada data</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="container-fluid">
                            <div class="row" style="margin-top: 12px">
                                <div class="col-12 text-right" id="contentContainer">
                                    <button type="button"
                                        class="page3showhide otorisasishowhide btn btn-action-primary btn-primary btn-pill-primary"
                                        onclick="submitOtorisasi()">Otorisasi</button>
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
        </div>
    <!-- Two stray, unmatched </div> tags used to sit here (pre-existing legacy cruft — harmless
         before, since everything that followed was a Bootstrap modal, position:fixed and
         indifferent to DOM nesting). Removed: they were escaping past #content-blade and
         #content (.content) into .main, which is exactly why #page4 — a real page div, not a
         modal — rendered as a sibling of #content instead of inside it. -->

    <!-- start page4: Tambah DPH (invoice picker) - was modal "#form", converted to a full page so the
         invoice table below can use a real scroll height instead of a modal's max-height:400px box -->
    <div id="page4" style="display: none" class="mainpage container-fluid">

        <div id="formBsGrid">
            <div class="row "  id="contentContainer">
            <div class="col-8 text-left">
                {{-- <h2>Tambah DPH</h2> --}}
            </div>
            <div class="col-4 text-right action-group">
                <button type="button" class="btn btn-action-danger btn-danger btn-pill-primary"
                    onclick="buttonClosePage4()">Close</button>
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
                            <input type="hidden" class="form-control" id="input_modal_nourut"
                                placeholder="" disabled>
                            <input type="text" class="form-control" id="input_modal_nobukti"
                                placeholder="No Bukti" disabled>
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
                            <input type="date" class="form-control text-center"
                                id="input_modal_tanggal" placeholder="">
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
                    <div class="col-md-8">
                        <div class="input-group form-group">
                            <input id="input_modal_valas" type="text" class="form-control"
                                disabled>

                            {{-- <button id="buttonAddListValas" type="button"
                                onclick="buttonAddListValas()" class="btn btn-primary">+</button> --}}

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
                    <div class="col-md-8">
                        <div class="input-group form-group">
                            <input id="input_modal_tanggaljatuhtempo" type="date"
                                class="form-control text-center">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group form-group">
                            <button id="buttonRefreshListPengajuan" type="button"
                                onclick="buttonRefreshListPengajuan()"
                                class="btn btn-chip-biru">Proses</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="dph-table-outer">
                    <div class="dph-table-wrap">
                        <table id="tabel_add_list_modal" class="dph-tb">
                            <thead>
                                <tr>
                                    <th scope="col">v</th>
                                    <th scope="col">Supplier</th>
                                    <th scope="col">JTHTempo</th>
                                    <th scope="col">Faktur</th>
                                    <th scope="col" class="num">N. Faktur</th>
                                    <th scope="col" class="num">Sdh Dibayar</th>
                                    <th scope="col" class="num">Dibayar</th>
                                    <th scope="col" class="num">K.Bayar</th>
                                    <th scope="col">No.Invoice</th>
                                    <th scope="col">TglInvoice</th>
                                </tr>
                            </thead>

                            <tbody id="tabel_data_add_list_modal" class="text-left">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 12px" >
            <div class="col-12 text-right" id="contentContainer">
                <button type="button" class="btn btn-action-primary btn-primary btn-pill-primary"
                    onclick="submitAdd()">Simpan</button>
            </div>
        </div>
        </div>

    </div>
    <!-- end page4 -->

    <div class="modal fade" id="formX" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="min-width: 1400px">
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
                            <div class="container-fluid">
                                <div class="row">

                                    <div class="col-md-3">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Nilai Nota</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input type="text" class="form-control text-right"
                                                        id="input_modalx_nilainotadibayar" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>No Invoice</label>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <input type="text" class="form-control "
                                                        id="input_modalx_noinvoice">
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
                                                    <label>Dibayar</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input type="text" class="form-control text-right"
                                                        id="input_modalx_dibayar">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Tgl Invoice</label>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <input type="date" class="form-control text-center"
                                                        id="input_modalx_tanggalinvoice">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2" style="margin-top: 0">
                                    <div class="col-md-12 text-right mt-4">

                                        <button id="buttonSaveLB" type="button" onclick="buttonSaveLB()"
                                            class="btn btn-success btn-action-success btn-pill-primary">Simpan</button>
                                        <button type="button" id="buttonAddKL"
                                            class="btn btn-primary btn-action-primary btn-pill-primary"
                                            onclick="buttonAddKL()">+ KL</button>
                                        <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
                                    </div>
                                </div>
                            </div>
                            <div id="formAddKL" class="container-fluid showhideitemKL">
                                <!-- <div class="line"></div> -->
                                <!-- <div class="row"> -->
                                <div class="col-12">
                                    <hr />
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h4 id="">Tambah KL</h4>
                                        </div>
                                    </div>
                                    <div class="row">
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
                                                        <input id="input_modalx_kurangbayar" type="number"
                                                            value="0.00" class="text-right form-control">

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
                                                        <input type="text" class="form-control" placeholder="Perkiraan"
                                                            id="input_modalx_perkiraankurangbayar"
                                                            onkeypress="onKeyPressPerkiraanLB(event, 'kurangbayar')">
                                                        <input type="text" class="form-control"
                                                            id="input_modalx_namaperkiraankurangbayar" disabled>
                                                        <button type="button"
                                                            onclick="openPickerPerkiraanLB('kurangbayar')"
                                                            class="btn btn-chip-biru"><i class="bi bi-search"></i></button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2" style="margin-top: 0" id="contentContainer">
                                    <div class="col-md-12 text-right mt-4">
                                        <button type="button" class="btn btn-action-danger btn-danger btn-pill-primary"
                                            onclick="buttonAddBatalKL()">Batal</button>

                                        <button id="buttonSubmitAddKL" type="button" onclick="submitAddKL()"
                                            class="btn btn-primary btn-action-primary btn-pill-primary">Simpan</button>
                                        <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-top:20px">
                                <div class="col-12">
                                    <div class="dph-table-outer">
                                        <div class="dph-table-wrap">
                                            <table id="tabel_add_list_modalx" class="dph-tb">
                                                <thead>
                                                    <tr>
                                                        <th scope="col" class="num">Kurang Bayar</th>
                                                        <th scope="col">Perkiraan</th>
                                                        <th scope="col">Nama perkiraan</th>
                                                        <th scope="col">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tabel_data_add_list_modalx" class="text-left">
                                                    <tr>
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
                            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="contentContainer">
                    <button type="button" class="btn btn-action-danger btn-danger btn-pill-primary" data-dismiss="modal">Batal</button>
                    <!-- <button type="button" class="btn btn-primary" onclick="submitAddModalX()">Submit</button> -->
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="formXedit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="min-width: 1400px">
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

                            <div class="container-fluid">

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Nilai Nota</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input type="text" class="form-control text-right"
                                                        id="input_modalxedit_nilainotadibayar" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>No Invoice</label>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <input type="text" class="form-control "
                                                        id="input_modalxedit_noinvoice">
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
                                                    <label>Dibayar</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input type="text" class="form-control text-right"
                                                        id="input_modalxedit_dibayar" oninput="formatAngkaKetik(this)"
                                                        onblur="formatAngkaInput(this)">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Tgl Invoice</label>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <input type="date" class="form-control text-center"
                                                        id="input_modalxedit_tanggalinvoice">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2" style="margin-top: 0">
                                    <div class="col-md-12 text-right mt-4">

                                        <button id="buttonSaveLBEdit" type="button" onclick="buttonSaveLBEdit()"
                                            class="btn btn-success btn-action-success btn-pill-primary">Save</button>
                                        <button type="button" id="buttonAddKLEdit"
                                            class="btn btn-primary btn-action-primary btn-pill-primary"
                                            onclick="buttonAddKLEdit()">+ KL</button>
                                        <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
                                    </div>
                                </div>
                            </div>
                            <div id="formAddKLEdit" class="container-fluid showhideitemKLEdit">
                                <!-- <div class="line"></div> -->
                                <!-- <div class="row"> -->

                                <div class="col-12">
                                    <hr />
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h4 id="">Tambah KL</h4>
                                        </div>
                                    </div>
                                    <div class="row">
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
                                                        <input id="input_modalxedit_kurangbayar" type="text"
                                                            value="0.00" class="text-right form-control"
                                                            oninput="formatAngkaKetik(this)" onblur="formatAngkaInput(this)">

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
                                                        <input type="text" class="form-control" placeholder="Perkiraan"
                                                            id="input_modalx_perkiraankurangbayaredit"
                                                            onkeypress="onKeyPressPerkiraanLB(event, 'kurangbayaredit')">
                                                        <input type="text" class="form-control"
                                                            id="input_modalx_namaperkiraankurangbayaredit" disabled>
                                                        <button type="button"
                                                            onclick="openPickerPerkiraanLB('kurangbayaredit')"
                                                            class="btn btn-chip-biru"><i class="bi bi-search"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2" style="margin-top: 0">
                                    <div class="col-md-12 text-right mt-4" id="contentContainer">
                                        <button type="button" class="btn btn-action-danger btn-danger btn-pill-primary"
                                            onclick="buttonAddBatalKLEdit()">Batal</button>

                                        <button id="buttonSubmitAddKLEdit" type="button" onclick="submitAddKLEdit()"
                                            class="btn btn-primary btn-action-primary btn-pill-primary">Simpan</button>
                                        <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
                                    </div>

                                </div>

                            </div>



                            <div class="row" style="margin-top:20px">
                                <div class="col-12">
                                    <div class="dph-table-outer">
                                        <div class="dph-table-wrap">
                                            <table id="tabel_add_list_modalxedit" class="dph-tb">
                                                <thead>
                                                    <tr>
                                                        <th scope="col" class="num">Kurang Bayar</th>
                                                        <th scope="col">Perkiraan</th>
                                                        <th scope="col">Nama perkiraan</th>
                                                        <th scope="col">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tabel_data_add_list_modalxedit" class="text-left">
                                                    <tr>
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
                            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="contentContainer">
                    <button type="button" class="btn btn-action-danger btn-danger btn-pill-primary" data-dismiss="modal">Batal</button>
                    <!-- <button type="button" class="btn btn-primary" onclick="submitAddModalX()">Submit</button> -->
                </div>
            </div>
        </div>
    </div>
    {{-- Perkiraan (LB/KL) picker — dikonversi ke pola search-icon
         (new-design-gudang-style-guide.md §10): baris langsung diklik (rt-picker-v2, tanpa
         kolom Actions/tombol +), dan bisa dibuka lewat ketik-kode+Enter dari kedua field
         "Perk KL" (formX/formXedit) tanpa modal ini terbuka sama sekali kalau kodenya persis
         cocok — lihat openPickerPerkiraanLB()/resolvePerkiraanLB() di @section('js'). --}}
    <div class="modal fade rt-picker-v2" id="formPerkiraan" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="min-width: 1400px">
            <div id="" class="modal-content ">

                <div id= "" class="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="">Pilih Perkiraan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div id="" class="">
                        <div class="modal-body">

                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-12" style="overflow:auto;  max-height: 400px">
                                        <table id="tabel_add_list_perkiraan" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Perkiraan</th>
                                                    <th scope="col">Nama</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tabel_data_add_list_perkiraan" class="text-left"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-pill-secondary" data-dismiss="modal">Batal</button>
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
        let listTambahKL = []
        let barangEdit = {}
        let listKLEdit = []

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

        /* ============================================================================
         * Tabel interaktif #page1 (list DPH) — port dari gudang/pembebananpemakaian.blade.php,
         * lihat docs/new-design-gudang-style-guide.md. Menggantikan DataTable() polos + render
         * gabungan for-loop Blade lama plus loadAll() lama, yang sempat berbeda perilaku satu sama lain
         * (tombol Edit hilang setelah loadAll() — lihat catatan di aksiButtonsHtml()/renderTabel()
         * di bawah, sekarang satu fungsi dipakai untuk paint pertama maupun tiap refresh).
         * ========================================================================= */
        let lastRows = (@json($tempOutstanding)).map(g => g[
        0]); // tempOutstanding dikelompokkan per NoBukti di controller — ambil baris pertama tiap grup
        let globalOtorisasi = "2"; // filter modal: 2=Semua, 1=Sudah Otorisasi, 0=Belum Otorisasi

        let tabelLen2 = 10; // dipakai sebagai pageLength DataTables di renderTabel()

        var g_href = 'pengajuandphtunai';
        var g_modeReport = '1';
        var gcart_header = [];
        var gsum_issubtotal = 0;
        var gsum_isgrandtotal = 0;
        var gct_desimal_max = 4;

        // IsOtorisasi1/OtoUser1/TglOto1 SENGAJA tidak ada di sini — seperti
        // memorialkoreksi.blade.php, ketiganya bukan kolom yang bisa digeser/disembunyikan,
        // melainkan tiga kolom tetap (Oto/User Oto/Tgl Oto) yang selalu ditambahkan di ujung
        // kanan tabel oleh renderTabel(), lihat catatan di sana.
        function setDefaultHeader() {
            // [ field, label, visible, type, total, decimals ]
            gcart_header = [
                ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
                ['NAMACUSTSUPP', 'Supplier', 1, 'varchar', 0, 0],
                ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
                ['Valas', 'Valas', 1, 'varchar', 0, 0],
                ['DIBAYAR', 'Nilai', 1, 'float', 0, 2],
                ['KL', 'K/L', 1, 'float', 0, 2],
                ['Userbatal', 'User Btl', 0, 'varchar', 0, 0],
                ['TglBatal', 'Tgl Btl', 0, 'date', 0, 0],
            ];
        }

        function doSetHeader(_modereport, _isReset = false) {
            let _strHeader = (!_isReset) ? doLoadHeader(g_href, _modereport) : "";

            if (_strHeader != "") {
                gcart_header = doGetHeader(_strHeader);
            } else if ($.isFunction(window.setDefaultHeader)) {
                setDefaultHeader();
                doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
            }
        }

        function doLoadHeader(_href, _mode) {
            let _header = "";

            $.ajax({
                url: "{!! url('globalfunctions_doLoadHeader') !!}",
                type: "get",
                async: false,
                data: {
                    href: _href,
                    mode: _mode
                },
                success: function(res) {
                    _header = (res.length > 0) ? res[0].header : "";
                    if (res.length > 0) {
                        gsum_issubtotal = Number(res[0].issubtotal);
                        gsum_isgrandtotal = Number(res[0].isgrandtotal);
                    }
                }
            })

            return _header;
        }

        function doGetHeader(_strHeader) {
            let _cart = [];

            _strHeader.split("||").forEach((item, i) => {
                let temp = [];
                temp.push(item.split(";;")[0]);
                temp.push(item.split(";;")[1]);
                temp.push(Number(item.split(";;")[2]));
                temp.push(item.split(";;")[3]);
                temp.push(Number(item.split(";;")[4]));
                temp.push(Number(item.split(";;")[5]));
                _cart.push(temp);
            });

            return _cart;
        }

        function doSimpanHeader(_href, _mode, _cart, _issubtotal, _isgrandtotal) {
            let _strHeader = "";

            _cart.forEach((item, i) => {
                if (i != 0) {
                    _strHeader += '||';
                }
                _strHeader += item[0] + ';;' + item[1] + ';;' + item[2] + ';;' + item[3] + ';;' + item[4] + ';;' +
                    item[5];
            });

            $.ajax({
                url: "{!! url('globalfunctions_doSimpanHeader') !!}",
                type: "get",
                async: false,
                data: {
                    href: _href,
                    mode: _mode,
                    header: _strHeader,
                    issubtotal: _issubtotal,
                    isgrandtotal: _isgrandtotal
                },
                success: function(res) {
                    // nothing to do
                }
            })
        }

        function doMoveHeader(_from, _to) {
            if (_from < 0 || _to < 0 || _from === _to) {
                return;
            }
            if (_from >= gcart_header.length || _to >= gcart_header.length) {
                return;
            }

            let _moved = gcart_header.splice(_from, 1)[0];
            gcart_header.splice(_to, 0, _moved);

            doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
        }

        function doButtonVisibility(_id) {
            gcart_header[_id][2] = (Number(gcart_header[_id][2]) === 1) ? 0 : 1;

            doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
        }

        function doSetDesimal(_index, _step) {
            let _next = Number(gcart_header[_index][5]) + _step;
            if (_next < 0 || _next > gct_desimal_max) {
                return;
            }

            gcart_header[_index][5] = _next;
            doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
        }

        function doButtonTotal(_index) {
            gcart_header[_index][4] = (Number(gcart_header[_index][4]) === 1) ? 0 : 1;

            doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
        }

        // Ambil field dari row tanpa peduli besar/kecil huruf.
        function pickCI(r, key) {
            if (r[key] !== undefined) {
                return r[key];
            }
            let lk = String(key).toLowerCase();
            for (let k in r) {
                if (k.toLowerCase() === lk) {
                    return r[k];
                }
            }
            return null;
        }

        function nullToEmpty(v) {
            return (v === null || v === undefined) ? '' : v;
        }

        // #modalOtorisasi: 2=Semua, 1=Sudah, 0=Belum — client-side saja.
        function filterByOtorisasi(rows, filterVal) {
            if (filterVal === '1') {
                return rows.filter(r => Number(pickCI(r, 'IsOtorisasi1')) === 1);
            }
            if (filterVal === '0') {
                return rows.filter(r => Number(pickCI(r, 'IsOtorisasi1')) === 0);
            }
            return rows;
        }

        // Satu-satunya tempat yang menentukan tombol Aksi — dipakai renderTabel() untuk paint
        // pertama MAUPUN tiap refresh loadAll(), jadi tidak bisa lagi berbeda seperti sebelumnya
        // (versi loadAll() lama kehilangan tombol Edit dan tombol "Otorisasi"-nya diam-diam cuma
        // membuka Detail — buttonDetail()/buttonOtorisasi() menerima SATU argumen saja, argumen
        // kedua yang dulu dikirim ('detail'/'edit'/'otorisasi') tidak pernah dibaca fungsinya).
        // Markup + pemetaan warna/ikon disalin persis dari renderTabelMk() di
        // memorialkoreksi.blade.php (.po-aksi-wrap, cuma title, tanpa tooltip Bootstrap).
        function aksiButtonsHtml(r) {
            const nobukti = r.NoBukti;
            let tombolAksi = '<button class="btn btn-warning btn-sm" type="button" title="Detail" onclick="buttonDetail(\'' +
                nobukti + '\')"><i class="bi bi-info"></i></button>';

            if (Number(pickCI(r, 'IsOtorisasi1')) === 1) {
                // Sudah otorisasi — Batal Otorisasi + Cetak
                tombolAksi += '<button class="btn btn-danger btn-sm" type="button" title="Batal Otorisasi" onclick="buttonBatalOtorisasi(\'' +
                    nobukti + '\')"><i class="bi bi-key-fill"></i></button>' +
                    '<button class="btn btn-primary btn-sm" type="button" title="Cetak" onclick="submitPrint(\'' +
                    nobukti + '\')"><i class="bi bi-printer"></i></button>';
            } else {
                // Belum otorisasi — Koreksi + Otorisasi
                tombolAksi += '<button class="btn btn-success btn-sm" type="button" title="Koreksi" onclick="buttonKoreksi(\'' +
                    nobukti + '\')"><i class="bi bi-pen"></i></button>' +
                    '<button class="btn btn-info btn-sm" type="button" title="Otorisasi" onclick="buttonOtorisasi(\'' +
                    nobukti + '\')"><i class="bi bi-key"></i></button>';
            }

            return '<div class="po-aksi-wrap">' + tombolAksi + '</div>';
        }

        /* Bar kolom tersembunyi harus berada tepat di atas tabelnya. DataTables membungkus
           tabel dengan #<id>_wrapper saat init, jadi acuannya ikut berpindah — sama seperti
           rtPindahBar() di bonsementara.blade.php. */
        function rtPindahBar(idBar, idTabel) {
            let bar = document.getElementById(idBar);
            let tabel = document.getElementById(idTabel);
            if (!bar || !tabel) { return; }

            let acuan = tabel;
            if ($.fn.DataTable.isDataTable('#' + idTabel)) {
                acuan = document.getElementById(idTabel + '_wrapper') || tabel;
            }

            if (acuan.previousElementSibling !== bar) {
                acuan.parentNode.insertBefore(bar, acuan);
            }
        }

        function ikatSearch() {
            let input = document.getElementById('searchBox2');
            if (!input || input.dataset.rtBound) { return; }
            input.dataset.rtBound = '1';

            input.addEventListener('input', function() {
                $('#tabel').DataTable().search(input.value).draw();
            });
        }

        function ikatPanjangHalaman() {
            let sel = document.getElementById('tabelLen2');
            if (!sel || sel.dataset.rtBound) { return; }
            sel.dataset.rtBound = '1';
            sel.value = String(tabelLen2);

            sel.addEventListener('change', function() {
                let n = Number(sel.value);
                tabelLen2 = (n === -1 || n > 0) ? n : 10;
                $('#tabel').DataTable().page.len(tabelLen2).draw();
            });
        }

        // Diikat lewat JS (bukan onchange="loadAll()" inline lagi) supaya rentang yang belum
        // lengkap tidak memicu request, dan urutan tanggal terbalik ditolak dengan peringatan —
        // sama seperti outIkatPeriode() di bonsementara.blade.php.
        function ikatPeriode() {
            let awal = document.getElementById('inputDate1');
            let akhir = document.getElementById('inputDate2');
            if (!awal || !akhir || awal.dataset.rtBound) { return; }
            awal.dataset.rtBound = '1';

            let onUbah = function() {
                if (!awal.value || !akhir.value) { return; }
                if (awal.value > akhir.value) {
                    alertify.warning('Tanggal awal tidak boleh melebihi tanggal akhir');
                    return;
                }
                loadAll();
            };
            awal.addEventListener('change', onUbah);
            akhir.addEventListener('change', onUbah);
        }

        // ReportTable.headHtml() dengan fallback bila report-table.js belum termuat — sama
        // seperti mkHeadHtml() di memorialkoreksi.blade.php.
        function dphtHeadHtml(cols) {
            if (typeof ReportTable !== 'undefined' && ReportTable.headHtml) {
                return ReportTable.headHtml(cols);
            }
            console.warn('report-table.js tidak termuat - fitur geser & sembunyikan kolom dimatikan. Pastikan public/js/report-table.js ada di server.');
            let html = '<tr>';
            cols.forEach((c) => {
                html += `<th style="padding: 4px 12px;" scope="col">${c[1]}</th>`;
            });
            return html + '</tr>';
        }

        let dphtRtSudahInit = false;

        function dphtInitReportTableSekali() {
            if (dphtRtSudahInit || typeof ReportTable === 'undefined') { return; }
            dphtRtSudahInit = true;

            ReportTable.init({
                table: '#tabel',
                bar: '#rtBar',
                onChange: renderTabel
            });

            // Sebagian layout memasang penangan klik sendiri di <thead>; teruskan klik pada
            // roda gigi / pegangan geser ke penangan milik ReportTable — sama seperti
            // mkInitReportTableSekali() di memorialkoreksi.blade.php.
            let dphtGuardUlangKlik = false;
            let thead = document.getElementById('tabel_header');
            if (thead) {
                thead.addEventListener('click', function(e) {
                    if (dphtGuardUlangKlik) { return; }
                    let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip');
                    if (!interaktif) { return; }

                    e.stopPropagation();
                    e.preventDefault();

                    dphtGuardUlangKlik = true;
                    let ulang = new MouseEvent('click', { bubbles: false, cancelable: true, view: window });
                    Object.defineProperty(ulang, 'target', { value: interaktif, configurable: true });
                    thead.dispatchEvent(ulang);
                    dphtGuardUlangKlik = false;
                }, true);
            }
        }

        // Tinggi tabel mengikuti sisa ruang layar - sama seperti mkAturTinggiTabel() di
        // memorialkoreksi.blade.php. Beda dengan referensi, halaman ini punya page2/3/4 yang
        // memakai .mainpage yang sama, jadi ditambah jaga-jaga: berhenti diam-diam kalau
        // #page1 sedang disembunyikan (loadAll() masih bisa terpanggil saat page2/3/4 aktif).
        function dphtAturTinggiTabel() {
            let page = document.getElementById('page1');
            if (!page || page.offsetParent === null) { return; }

            let area = document.getElementById('content');
            let wrap = document.querySelector('#page1 .po-table-wrap');
            if (!area || !wrap) { return; }

            wrap.style.maxHeight = 'none';

            let padBawah = parseFloat(getComputedStyle(area).paddingBottom) || 0;
            let batasBawah = area.getBoundingClientRect().bottom - padBawah;
            let kotak = wrap.getBoundingClientRect();
            let bawah = page.getBoundingClientRect().bottom - kotak.bottom;

            let sisa = batasBawah - kotak.top - bawah - 4;
            wrap.style.maxHeight = Math.max(200, Math.floor(sisa)) + 'px';
        }

        // IsOtorisasi1/OtoUser1/TglOto1 dikeluarkan dari cols meski masih tersimpan di
        // susunan kolom lama (sebelum perubahan ini) — ketiganya sekarang kolom tetap
        // (Oto/User Oto/Tgl Oto) yang ditambahkan sendiri di bawah, bukan kolom geser/sembunyi.
        function renderTabel() {
            const cols = gcart_header.filter(c => c[2] === 1 &&
                c[0] !== 'IsOtorisasi1' && c[0] !== 'OtoUser1' && c[0] !== 'TglOto1');

            if ($.fn.DataTable.isDataTable('#tabel')) {
                $('#tabel').DataTable().destroy();
            }

            let thead = document.getElementById('tabel_header');
            thead.innerHTML = dphtHeadHtml(cols);
            let baris = thead.querySelector('tr');
            if (baris) {
                baris.insertAdjacentHTML('afterbegin', '<th style="padding: 4px 12px;" scope="col">Actions</th>');
                baris.insertAdjacentHTML('beforeend', `
                    <th style="padding: 4px 12px;" scope="col">Oto</th>
                    <th style="padding: 4px 12px;" scope="col">User Oto</th>
                    <th style="padding: 4px 12px;" scope="col">Tgl Oto</th>
                `);
            }

            const rows = filterByOtorisasi(lastRows, globalOtorisasi);

            let rowTable = '';
            rows.forEach(function(r) {
                let isOtorisasi = Number(pickCI(r, 'IsOtorisasi1')) || 0;
                let otoUser = pickCI(r, 'OtoUser1');
                let tglOto = pickCI(r, 'TglOto1');

                rowTable += '<tr><td class="text-center">' + aksiButtonsHtml(r) + '</td>';
                rowTable += cols.map(function(c) {
                    const v = pickCI(r, c[0]);
                    if (c[3] === 'date') {
                        return '<td>' + (v ? formatDate(v) : '') + '</td>';
                    }
                    if (c[3] === 'float') {
                        return '<td style="text-align: right;">' +
                            formatAngka(parseFloat(v || 0).toFixed(Number(c[5]) || 0)) + '</td>';
                    }
                    return '<td>' + nullToEmpty(v) + '</td>';
                }).join('');
                rowTable += `
                    ${isOtorisasi ?
                        '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>'
                      :
                        '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>'
                    }
                    <td>${otoUser || ''}</td>
                    <td>${tglOto ? formatDate(tglOto) : ''}</td>
                </tr>`;
            });

            document.getElementById('tabel_data').innerHTML = rowTable;

            $('#tabel').DataTable({
                lengthChange: false,
                pageLength: tabelLen2,
                order: [],
                columnDefs: [{ targets: [0], orderable: false }],
                dom: "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                language: {
                    emptyTable: 'Tidak ada data',
                    zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
                }
            });

            rtPindahBar('rtBar', 'tabel');
            ikatSearch();
            ikatPanjangHalaman();
            ikatPeriode();

            let inputSearch = document.getElementById('searchBox2');
            if (inputSearch && inputSearch.value) {
                $('#tabel').DataTable().search(inputSearch.value).draw();
            }
            dphtAturTinggiTabel();
        }

        /* -- FILTER MODAL (Otorisasi: Semua/Sudah Otorisasi/Belum) -- */
        function updateFilterBadge() {
            let count = ($('#modalOtorisasi').val() !== '2') ? 1 : 0;
            $('#filterBadge').text(count + ' aktif');
        }

        function resetAllFilters() {
            $('#modalOtorisasi').val('2');
            updateFilterBadge();
        }

        $(document).on('show.bs.modal', '#modalFilter', function() {
            $('#modalOtorisasi').val(globalOtorisasi);
            updateFilterBadge();
        });

        $(document).on('change', '#modalFilter select.rt-native', updateFilterBadge);

        function applyModalFilter() {
            globalOtorisasi = $('#modalOtorisasi').val();
            renderTabel();
            $('#modalFilter').modal('hide');
        }

        $(document).ready(function() {
            $("#tabel_add_list_custsupp").DataTable({
                "lengthChange": false,
                "paging": false,
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

            // Satu-satunya titik di mana modal picker Perkiraan (LB/KL) dijamin sudah
            // display:block, jadi di sinilah lebar kolom DataTables boleh dihitung.
            $('#formPerkiraan').on('shown.bs.modal', function() {
                flushPerkiraanLBPending();
            });

            // Tabel gabungan — mesin interaktif (drag/gear/bar), lihat
            // docs/new-design-gudang-style-guide.md.
            doSetHeader(g_modeReport);
            dphtInitReportTableSekali();
            renderTabel();
        });


        // ===================== Perkiraan (LB/KL) picker =====================
        // Port dari gudang/pembebananpemakaian.blade.php (new-design-gudang-style-guide.md
        // §10): ketik kode + Enter -> kode yang PERSIS cocok langsung mengisi field tanpa
        // modal; selain itu modal .rt-picker-v2 dibuka dengan pencarian sudah terisi, klik
        // baris langsung memilih. Datanya statis ($tempListPerkiraan, sudah ada di halaman
        // saat load) — dipakai bergantian oleh field 'kurangbayar' (formX) dan
        // 'kurangbayaredit' (formXedit) lewat parameter id, sama seperti mekanisme
        // buttonAddListPerkiraanLebihBayar()/buttonAddPickPerkiraanLebihBayar() lama yang
        // digantikannya.
        let listPerkiraanLB = @json($tempListPerkiraan);
        let toId = '';
        let perkiraanLBDT = null;
        let perkiraanLBPendingTerm = null;

        // destroy() TIDAK membersihkan style="width:...px" yang ditulis DataTables ke tiap
        // <th> — init berikutnya membacanya sebagai lebar tetap dan kolom menyusut tiap kali
        // modal dibuka ulang.
        function resetPerkiraanLBTableWidths() {
            let $t = $('#tabel_add_list_perkiraan');
            $t.css('width', '');
            $t.children('colgroup').remove();
            $t.find('thead th').css('width', '');
        }

        function initPerkiraanLBTable(term) {
            // DataTables menghitung lebar kolom dari container saat init, dan modal BS4 baru
            // display:block setelah transisi fade selesai — kalau init dipanggil sebelum itu,
            // lebar diukur di container 0px. Antre saja, dieksekusi di shown.bs.modal.
            if (!$('#formPerkiraan').is(':visible')) {
                perkiraanLBPendingTerm = term || '';
                return;
            }
            perkiraanLBPendingTerm = null;

            if ($.fn.DataTable.isDataTable('#tabel_add_list_perkiraan')) {
                $('#tabel_add_list_perkiraan').DataTable().clear().destroy();
            }
            resetPerkiraanLBTableWidths();

            perkiraanLBDT = $('#tabel_add_list_perkiraan').DataTable({
                data: listPerkiraanLB,
                deferRender: true,
                paging: true,
                pageLength: 25,
                lengthChange: false,
                searching: true,
                order: [],
                language: {
                    emptyTable: 'Tidak ada data'
                },
                columns: [{
                    data: 'Perkiraan'
                }, {
                    data: 'Keterangan'
                }],
                createdRow: function(row, data) {
                    row.className = 'pick-row';
                    row.onclick = function() {
                        applyPickPerkiraanLB(data);
                    };
                }
            });

            perkiraanLBDT.search(term || '').draw();
        }

        function flushPerkiraanLBPending() {
            if (perkiraanLBPendingTerm !== null) {
                initPerkiraanLBTable(perkiraanLBPendingTerm);
            }
        }

        function openPickerPerkiraanLB(id, term) {
            toId = id;
            initPerkiraanLBTable(term || '');
            $('#formPerkiraan').modal('show');
        }

        // Titik masuk tunggal buat Enter — kode yang PERSIS cocok (case-insensitive, trimmed)
        // langsung mengisi field tanpa modal; selain itu (sebagian, nama, atau kosong) modal
        // dibuka dengan `term` sudah terisi di kotak search-nya.
        function resolvePerkiraanLB(id, term) {
            toId = id;
            term = (term || '').trim();

            if (!term) {
                openPickerPerkiraanLB(id, '');
                return;
            }

            let needle = term.toLowerCase();
            let hit = listPerkiraanLB.find(item => String(item.Perkiraan || '').trim().toLowerCase() === needle);
            if (hit) {
                applyPickPerkiraanLB(hit);
            } else {
                openPickerPerkiraanLB(id, term);
            }
        }

        function onKeyPressPerkiraanLB(e, id) {
            if (e.which === 13) {
                e.preventDefault();
                resolvePerkiraanLB(id, document.getElementById('input_modalx_perkiraan' + id).value);
            }
        }

        // Satu-satunya tempat yang mengisi kode+nama field, baik dari klik baris di picker
        // maupun dari kecocokan persis di resolvePerkiraanLB().
        function applyPickPerkiraanLB(item) {
            document.getElementById('input_modalx_perkiraan' + toId).value = item.Perkiraan;
            document.getElementById('input_modalx_namaperkiraan' + toId).value = item.Keterangan;
            $('#formPerkiraan').modal('hide');
        }

        function buttonSaveLBEdit() {
            // let xlist = listKLEdit

            let xnilainota = $("#input_modalxinvoice_nilainotadibayar").val()
            let xtanggalinvoice = $("#input_modalxinvoice_tanggalinvoice").val()
            let xnoinvoice = $("#input_modalxinvoice_noinvoice").val()

            let xdibayar = $("#input_modalx_dibayar").val().toString().replace(/,/g, '')

            let dibayar = unformatAngka($("#input_modalxedit_dibayar").val())
            let noinvoice = $("#input_modalxedit_noinvoice").val()
            let tanggalinvoice = $("#input_modalxedit_tanggalinvoice").val()
            let nofaktur = barangEdit.NoFaktur
            let nobukti = barangEdit.NoBukti
            let urut = barangEdit.urut
            let _token = $("#_token").val()

            let xlist = listKLEdit
            console.log(xlist)
            if (!xlist) {
                xlist = []
            }
            let xtotalKL = 0

            xlist.forEach((item, i) => {
                xtotalKL += Number(item.inputKL)


            });
            if (Number(xnilainotadibayar) < Number(xdibayar) + Number(xtotalKL)) {
                alertify.warning('KL + dibayar melebihi nilai nota ')

                return
            }


            $.ajax({
                url: "{!! url('pengajuandphtunaispupdatedphdet') !!}",
                type: "post",
                async: false,
                data: {
                    _token,
                    dibayar,
                    noinvoice,
                    tanggalinvoice,
                    nobukti,
                    urut

                },
                success: function(res) {
                    console.log(res)
                    // $('.showhideitemKLedit').hide()

                    $(".showhideitemKLEdit").hide()
                    refreshDataTable(nobukti)
                    alertify.success("Berhasil update DPH")

                },
                error: function(err) {
                    console.log(err)
                    alertify.warning('Terjadi kesalahan silahkan refresh browser')
                    resRefresh = 0;
                }

            })
        }


        function submitAddKL() {
            let kl = $("#input_modalx_kurangbayar").val()
            let perkkl = $("#input_modalx_perkiraankurangbayar").val()
            let namaperkkl = $("#input_modalx_namaperkiraankurangbayar").val()
            // perkkl = '444'
            // namaperkkl = 'TESTESwiu'

            if (Number(kl) <= 0 || !perkkl) {

                alertify.warning("Data tidak lengkap")
                return
            }
            let xnilainotadibayar = $("#input_modalx_nilainotadibayar").val().toString().replace(/,/g, '')
            let xdibayar = $("#input_modalx_dibayar").val().toString().replace(/,/g, '')
            // if (Number(xnilainotadibayar) + Number(xdibayar) < Number(kl)) {
            //   alertify.warning('KL melebihi nilai nota + dibayar')
            //
            //   return
            // }

            let xlist = listTambahKL[saveHeaderInvoice.NoFaktur]
            console.log(xlist)
            if (!xlist) {
                xlist = []
            }

            let xtotalKL = 0

            xlist.forEach((item, i) => {
                xtotalKL += Number(item.inputKL)


            });
            console.log(Number(xnilainotadibayar), Number(xdibayar), Number(xtotalKL))
            if (Number(xnilainotadibayar) < Number(xdibayar) + Number(xtotalKL) + Number(kl)) {
                alertify.warning('KL + dibayar melebihi nilai nota ')

                return
            }


            // let xsisa = $("#input_modalx_sisanotadibayar").val()
            // if (Number(kl) > Number(xsisa)) {
            //   alertify.warning('Melebihi sisa nota')
            //   return
            // }
            alertify.success("KL berhasil ditambah")
            let x = {
                ...saveHeaderInvoice
            }
            x.inputKL = kl


            x.inputPerkiraanKL = perkkl
            x.inputNamaPerkiraanKL = namaperkkl
            console.log(x)
            console.log(listTambahKL)
            console.log('!!!!')
            console.log(saveHeaderInvoice.NoFaktur)
            if (!listTambahKL[saveHeaderInvoice.NoFaktur]) {
                listTambahKL[saveHeaderInvoice.NoFaktur] = []
            }
            console.log(listTambahKL)
            listTambahKL[saveHeaderInvoice.NoFaktur].push(x)
            console.log(listTambahKL)
            refreshTableKL()
            $('.showhideitemKL').hide()

        }


        function refreshTableKLEdit(nobukti, nofaktur) {
            let _token = $("#_token").val()
            $.ajax({
                url: "{!! url('pengajuandphtunaispdetailkledit') !!}",
                type: "post",
                async: false,
                data: {
                    _token: _token,
                    nobukti,

                    nofaktur,
                },
                success: function(res) {
                    console.log(res)

                    listKLEdit = res

                    if (!res.length) {
                        document.getElementById("tabel_data_add_list_modalxedit").innerHTML = `
          <tr>
            <td class="text-center" colspan=4>Belum ada data</td>
          </tr>
          `

                    } else {
                        rowTablex = ''
                        res.forEach((item, i) => {
                            // xTempTotalKL += Number(item.inputKL)
                            rowTablex += `
              <tr>
                <td class="text-right">${item.NilaiK}</td>
                <td>${item.Perkiraan}</td>
                <td>${item.Keterangan}</td>
                <td class="text-center"><div class="action-buttons"><button class="btn-action-sm btn-action-danger" data-toggle="tooltip" title="Hapus" type="button" onclick="buttonDeleteKLEdit(${i})"><i class="bi bi-trash"></i></button></div></td>
              </tr>
            `

                        });

                        document.getElementById("tabel_data_add_list_modalxedit").innerHTML = rowTablex
                        // document.getElementById(`list_proses_KL${saveHeaderIndex}`).value = parseFloat(xTempTotalKL).toFixed(2)

                        $('#tabel_data_add_list_modalxedit [data-toggle="tooltip"]').tooltip({
                            container: 'body',
                            boundary: 'window'
                        });
                    }
                },
                error: function(err) {
                    console.log(err)
                    alertify.warning('Terjadi kesalahan silahkan refresh browser')
                }
            })

            return

            console.log(saveHeaderInvoice)
            console.log('refreshTableKL')
            console.log(saveHeaderInvoice.NoFaktur)
            console.log(listTambahKL[saveHeaderInvoice.NoFaktur])
            let xlist = listTambahKL[saveHeaderInvoice.NoFaktur]
            console.log(xlist)
            if (!xlist) {
                xlist = []
            }
            console.log(xlist)
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
          <td class="text-right">${item.inputKL}</td>
          <td>${item.inputPerkiraanKL}</td>
          <td>${item.inputNamaPerkiraanKL}</td>
          <td class="text-center"><button class="btn btn-danger btn-sm" type="button" onclick="buttonDeleteKLEdit(${i})"><i class="bi bi-trash"></i></button></td>
        </tr>
      `

                });

                document.getElementById("tabel_data_add_list_modalxedit").innerHTML = rowTablex
                document.getElementById(`list_proses_KL${saveHeaderIndex}`).value = formatAngka(parseFloat(xTempTotalKL).toFixed(2))


            }

        }


        function submitAddKLEdit() {
            let nofaktur = barangEdit.NoFaktur
            let nobukti = barangEdit.NoBukti
            let kodecustsupp = barangEdit.KodeCustSupp
            let inputKL = unformatAngka($('#input_modalxedit_kurangbayar').val())
            let perkiraan = $('#input_modalx_perkiraankurangbayaredit').val()
            let _token = $("#_token").val()

            if (Number(inputKL) <= 0 || !perkiraan) {

                alertify.warning("Data tidak lengkap")
                return
            }

            let xlist = listKLEdit
            console.log(xlist)
            if (!xlist) {
                xlist = []
            }
            let xtotalKL = 0
            let xnilainotadibayar = unformatAngka($("#input_modalxedit_nilainotadibayar").val())
            let xdibayar = unformatAngka($("#input_modalxedit_dibayar").val())


            xlist.forEach((item, i) => {
                xtotalKL += Number(item.inputKL)


            });
            if (Number(xnilainotadibayar) < Number(xdibayar) + Number(xtotalKL)) {
                alertify.warning('KL + dibayar melebihi nilai nota ')

                return
            }

            $.ajax({
                url: "{!! url('pengajuandphtunaispaddkledit') !!}",
                type: "post",
                async: false,
                data: {
                    _token,
                    nofaktur,
                    nobukti,
                    kodecustsupp,
                    inputKL,
                    perkiraan


                },
                success: function(res) {
                    console.log(res)
                    $('.showhideitemKLEdit').hide()
                    refreshTableKLEdit(nobukti, nofaktur)
                    refreshDataTable(nobukti)
                    alertify.success("Berhasil menambah KL")

                },
                error: function(err) {
                    console.log(err)
                    alertify.warning('Terjadi kesalahan silahkan refresh browser')
                    resRefresh = 0;
                }

            })

        }

        function buttonDeleteKLEdit(index) {
            let nofaktur = barangEdit.NoFaktur
            let nobukti = barangEdit.NoBukti
            let xkledit = listKLEdit[index]

            let urut = xkledit.Urut
            let _token = $("#_token").val()
            $.ajax({
                url: "{!! url('pengajuandphtunaispdeletekledit') !!}",
                type: "post",
                async: false,
                data: {
                    _token,
                    nofaktur,
                    nobukti,
                    urut

                },
                success: function(res) {
                    console.log(res)
                    $('.showhideitemKLedit').hide()
                    refreshTableKLEdit(nobukti, nofaktur)
                    refreshDataTable(nobukti)
                    alertify.success("Berhasil update DPH")

                },
                error: function(err) {
                    console.log(err)
                    alertify.warning('Terjadi kesalahan silahkan refresh browser')
                    resRefresh = 0;
                }

            })

        }

        function buttonDeleteKL(index) {

            listTambahKL[saveHeaderInvoice.NoFaktur].splice(index, 1)

            refreshTableKL()
            $('.showhideitemKL').hide()
        }



        function refreshTableKL() {

            console.log(saveHeaderInvoice)
            console.log('refreshTableKL')
            console.log(saveHeaderInvoice.NoFaktur)
            console.log(listTambahKL[saveHeaderInvoice.NoFaktur])
            let xlist = listTambahKL[saveHeaderInvoice.NoFaktur]
            console.log(xlist)
            if (!xlist) {
                xlist = []
            }
            console.log(xlist)
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
          <td class="text-right">${item.inputKL}</td>
          <td>${item.inputPerkiraanKL}</td>
          <td>${item.inputNamaPerkiraanKL}</td>
          <td class="text-center"><div class="action-buttons"><button class="btn-action-sm btn-action-danger" data-toggle="tooltip" title="Hapus" type="button" onclick="buttonDeleteKL(${i})"><i class="bi bi-trash"></i></button></div></td>

        </tr>
      `

                });

                document.getElementById("tabel_data_add_list_modalx").innerHTML = rowTablex
                document.getElementById(`list_proses_KL${saveHeaderIndex}`).value = formatAngka(parseFloat(xTempTotalKL).toFixed(2))


            }

            $('#tabel_data_add_list_modalx [data-toggle="tooltip"]').tooltip({
                container: 'body',
                boundary: 'window'
            });
        }


        function buttonSaveLB() {
            console.log(saveHeaderInvoice)
            console.log(saveHeaderInvoice.NoFaktur)
            let xlist = listTambahKL[saveHeaderInvoice.NoFaktur]
            let check = listCheckListPengajuan.findIndex(el => el.NoFaktur === saveHeaderInvoice.NoFaktur);
            console.log(check)
            let xnilainota = $("#input_modalx_nilainotadibayar").val().toString().replace(/,/g, '')
            let xtanggalinvoice = $("#input_modalx_tanggalinvoice").val()
            let xnoinvoice = $("#input_modalx_noinvoice").val()

            let xdibayar = $("#input_modalx_dibayar").val().toString().replace(/,/g, '')
            // if (xnilainota < xdibayar ) {
            //   alertify.warning("Melebihi nilai nota")
            //   return
            //
            // }

            // let xlist = listTambahKL[saveHeaderInvoice.NoFaktur]
            console.log(xlist)
            if (!xlist) {
                xlist = []
            }

            let xtotalKL = 0

            xlist.forEach((item, i) => {
                xtotalKL += Number(item.inputKL)


            });

            if (Number(xnilainota) < Number(xdibayar) + Number(xtotalKL)) {
                alertify.warning('KL + dibayar melebihi nilai nota ')

                return
            }

            //pengecekan
            console.log(listCheckListPengajuan)
            console.log(listCheckListPengajuan[check])
            listCheckListPengajuan[check].diBayar = xdibayar
            listCheckListPengajuan[check].tanggalinvoice = xtanggalinvoice
            listCheckListPengajuan[check].noinvoice = xnoinvoice

            document.getElementById(`list_proses_dibayar${saveHeaderIndex}`).value = formatAngka(parseFloat(xdibayar).toFixed(2))
            alertify.success("Berhasil update dibayar")

        }

        function onChangeTransaksi() {
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

        function setNewNoBukti() {
            console.log('setNewNoBukti')
            let _token = $("#_token").val()
            let kode = 'DPH'
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

                }
            })
        }


        function cleanFormAddAdd() {
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


        function closeShowHideAdd() {
            $('.showhide').hide();

        }

        function cleanModalAdd() {


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


        function submitAdd() {
            console.log("submitAdd")
            console.log(listCheckListPengajuan)
            let _token = $("#_token").val()
            let choice = "I"
            let nobukti = $("#input_modal_nobukti").val()
            let nourut = $("#input_modal_nourut").val()
            let valas = $("#input_modal_valas").val()
            let tipe = 'DPH'
            let checkDate = new Date($("#input_modal_tanggal").val())
            let periode_bulan = document.getElementById("periode_bulan").value
            let periode_tahun = document.getElementById("periode_tahun").value

            if (checkDate.getFullYear() !== Number(periode_tahun) || (checkDate.getMonth() + 1) !== Number(periode_bulan)) {
                console.log(checkDate.getFullYear())
                console.log(Number(periode_tahun))
                console.log((checkDate.getMonth() + 1))
                console.log(Number(periode_bulan))
                alertify.warning("Tanggal tidak sesuai periode");
                return
            }


            let tanggal = $("#input_modal_tanggal").val()

            if (!listCheckListPengajuan.length) {
                alertify.warning("Tidak ada item dipilih")

            }
            let xlisttambahkl = []

            listCheckListPengajuan.forEach((item, i) => {
                if (listTambahKL[item.NoFaktur]) {
                    let tempkl = listTambahKL[item.NoFaktur]
                    tempkl.forEach((item, i) => {

                        xlisttambahkl.push(item)
                    });


                }
            });

            for (let i = 0; i < listCheckListPengajuan.length; i++) {
                let num = 0
                // console.log(num)
                if (Number(listCheckListPengajuan[i].JmlDibayar) > 0) {

                    num = listCheckListPengajuan[i].JmlDibayar
                    // console.log(num , 'b')
                } else {
                    num = Number(listCheckListPengajuan[i].Kredit) - Number(listCheckListPengajuan[i].JmlDibayar)
                    // console.log(num , 'a')
                }
                listCheckListPengajuan[i].tempKL = num
            }
            let jmlrecord = tipeform == "add" ? 0 : 1

            console.log({
                tempData: listCheckListPengajuan,
                choice,
                valas,
                nobukti,
                tempDataKL: xlisttambahkl,
                nourut,
                tipe,
                tanggal
            })


            // listCheckListPengajuan

            $.ajax({
                url: "{!! url('pengajuandphtunaispadd') !!}",
                type: "post",
                async: false,
                data: {
                    _token,
                    tempData: listCheckListPengajuan,
                    choice,
                    valas,

                    tempDataKL: xlisttambahkl,
                    nobukti,
                    nourut,
                    tipe,
                    tanggal,
                    jmlrecord
                },
                success: function(res) {
                    console.log(res, '!')

                    if (res == 1) {
                        // $("#form").modal('toggle')
                        alertify.success('DPH telah ditambah');

                        // $('.showhideitem').hide();
                        loadAll()
                        buttonCloseForm()
                        // document.getElementById("buttonAddListCustomer").disabled = true
                        // document.getElementById("input_add_tanggal").disabled = true

                        // refreshDataTable(nobukti)

                    }
                    if (res == 4) {
                        // setNewNoBukti()
                        alertify.warning('Nobukti dan nofaktur sudah ada');
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
                error: function(err) {
                    console.log(err)
                    alertify.warning('Terjadi kesalahan silahkan refresh browser')
                }
            })


        }


        function submitAddAdd() {

            let checkDate = new Date($("#input_add_tanggal").val())

            let periode_bulan = document.getElementById("periode_bulan").value
            let periode_tahun = document.getElementById("periode_tahun").value

            if (checkDate.getFullYear() !== Number(periode_tahun) || (checkDate.getMonth() + 1) !== Number(periode_bulan)) {

                alertify.warning("Tanggal tidak sesuai periode");
                return
            }

            let _token = $("#_token").val()
            let nobukti = $("#input_add_nobukti").val()
            let nourut = $("#input_add_nourut").val()
            let transaksi = $("#input_add_transaksi").val()
            let note = $("#input_add_kepadaterima").val()
            let kodeperkiraan = $("#input_add_kodeperkiraan").val()
            let tanggal = $("#input_add_tanggal").val()

            let lampiran = 0
            let keterangan2 = ''
            let choice = "I"


            let kodedevisi = $("#AddAddKodeDevisi").val()
            let valas = $("#AddAddValas").val()
            let kurs = $("#AddAddKurs").val()
            let lawan = $("#AddAddLawan").val()
            // let kodelawan  = $("#AddAddKodeLawan").val()
            let jumlah = $("#AddAddJumlah").val()
            let keterangan = $("#AddAddKeterangan").val()
            let keterangandetail = $("#AddAddKeteranganDetail").val()
            let kodedepartemen = $("#AddAddKodeDepartemen").val()

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


            if (kodeFlag == "UHT" && transaksi == "BBM") {


                custsuppL = $("#input_dphuhtbbm_kodecustsupp").val();
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
                        nobukti,
                        nourut,
                        transaksi,
                        note,
                        kodeperkiraan,
                        tanggal,

                        lampiran,
                        keterangan2,
                        perkiraanx,
                        lawanx,

                        kodedevisi,
                        valas,
                        kurs,
                        lawan,
                        jumlah,
                        keterangan,
                        keterangandetail,
                        kodedepartemen,

                        kredit,
                        kreditrp,

                        tphc,
                        jumlahrp,


                        urut,

                        custsuppP,
                        custsuppL,
                        noaktivaP,
                        noaktivaL,
                        statusaktivaP,
                        statusaktivaL,

                        nobon,
                        kodebag,

                        kodeP,
                        kodeL,
                        statusgiro,
                        simbol,
                        flagsimbol,
                        kodecost,
                        kodesubcost,
                        nodph,
                        urutdph,
                        dppdph,
                        tp,
                        ppklx,
                        nofaktur,
                        plok,
                        nobons,
                        jmlrecord,
                        notitipan,
                        uruttitipan,
                        pSKB,
                        custsupp
                    },
                    success: function(res) {
                        console.log(res, '!')

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
                    error: function(err) {
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
                        nobukti,
                        nourut,
                        transaksi,
                        note,
                        kodeperkiraan,
                        tanggal,

                        lampiran,
                        keterangan2,
                        perkiraanx,
                        lawanx,

                        kodedevisi,
                        valas,
                        kurs,
                        lawan,
                        jumlah,
                        keterangan,
                        keterangandetail,
                        kodedepartemen,

                        kredit,
                        kreditrp,

                        tphc,
                        jumlahrp,


                        urut,

                        custsuppP,
                        custsuppL,
                        noaktivaP,
                        noaktivaL,
                        statusaktivaP,
                        statusaktivaL,

                        nobon,
                        kodebag,

                        kodeP,
                        kodeL,
                        statusgiro,
                        simbol,
                        flagsimbol,
                        kodecost,
                        kodesubcost,
                        nodph,
                        urutdph,
                        dppdph,
                        tp,
                        ppklx,
                        nofaktur,
                        plok,
                        nobons,
                        jmlrecord,
                        notitipan,
                        uruttitipan,
                        pSKB,
                        custsupp
                    },
                    success: function(res) {
                        console.log(res, '!')

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
                    error: function(err) {
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
                        nobukti,
                        nourut,
                        transaksi,
                        note,
                        kodeperkiraan,
                        tanggal,

                        lampiran,
                        keterangan2,
                        perkiraanx,
                        lawanx,

                        kodedevisi,
                        valas,
                        kurs,
                        lawan,
                        jumlah,
                        keterangan,
                        keterangandetail,
                        kodedepartemen,

                        kredit,
                        kreditrp,

                        tphc,
                        jumlahrp,


                        urut,

                        custsuppP,
                        custsuppL,
                        noaktivaP,
                        noaktivaL,
                        statusaktivaP,
                        statusaktivaL,

                        nobon,
                        kodebag,

                        kodeP,
                        kodeL,
                        statusgiro,
                        simbol,
                        flagsimbol,
                        kodecost,
                        kodesubcost,
                        nodph,
                        urutdph,
                        dppdph,
                        tp,
                        ppklx,
                        nofaktur,
                        plok,
                        nobons,
                        jmlrecord,
                        notitipan,
                        uruttitipan,
                        pSKB,
                        custsupp
                    },
                    success: function(res) {
                        console.log(res, '!')

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
                    error: function(err) {
                        console.log(err)
                        alertify.warning('Terjadi kesalahan silahkan refresh browser')
                    }
                })
            }












        }

        function submitAddEdit() {



            let checkDate = new Date($("#input_add_tanggal").val())

            let periode_bulan = document.getElementById("periode_bulan").value
            let periode_tahun = document.getElementById("periode_tahun").value

            if (checkDate.getFullYear() !== Number(periode_tahun) || (checkDate.getMonth() + 1) !== Number(periode_bulan)) {

                alertify.warning("Tanggal tidak sesuai periode");
                return
            }
            let choice = "U"
            let _token = $("#_token").val()
            let nobukti = $("#input_add_nobukti").val()
            let nourut = $("#input_add_nourut").val()
            let transaksi = $("#input_add_transaksi").val()
            let note = $("#input_add_kepadaterima").val()
            let kodeperkiraan = $("#input_add_kodeperkiraan").val()
            let tanggal = $("#input_add_tanggal").val()

            let lampiran = 0
            let keterangan2 = ''


            let kodedevisi = $("#AddAddKodeDevisi").val()
            let valas = $("#AddAddValas").val()
            let kurs = $("#AddAddKurs").val()
            let lawan = $("#AddAddLawan").val()
            let jumlah = $("#AddAddJumlah").val()
            let keterangan = $("#AddAddKeterangan").val()
            let keterangandetail = $("#AddAddKeteranganDetail").val()
            let kodedepartemen = $("#AddAddKodeDepartemen").val()

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
                nobukti,
                nourut,
                transaksi,
                note,
                kodeperkiraan,
                tanggal,
                lampiran,
                keterangan2,
                kodedevisi,
                valas,
                kurs,
                lawan,
                jumlah,
                keterangan,
                keterangandetail,
                kodedepartemen,
                kredit,
                kreditrp,
                tphc,
                jumlahrp,
                urut,
                custsuppP,
                custsuppL,
                noaktivaP,
                noaktivaL,
                statusaktivaP,
                statusaktivaL,
                nobon,
                kodebag,
                kodeP,
                kodeL,
                statusgiro,
                simbol,
                flagsimbol,
                kodecost,
                kodesubcost,
                nodph,
                urutdph,
                dppdph,
                tp,
                ppklx,
                nofaktur,
                plok,
                nobons,
                jmlrecord,
                notitipan,
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
                    nobukti,
                    nourut,
                    transaksi,
                    note,
                    kodeperkiraan,
                    tanggal,

                    lampiran,
                    keterangan2,
                    perkiraanx,
                    lawanx,

                    kodedevisi,
                    valas,
                    kurs,
                    lawan,
                    jumlah,
                    keterangan,
                    keterangandetail,
                    kodedepartemen,

                    kredit,
                    kreditrp,

                    tphc,
                    jumlahrp,


                    urut,

                    custsuppP,
                    custsuppL,
                    noaktivaP,
                    noaktivaL,
                    statusaktivaP,
                    statusaktivaL,

                    nobon,
                    kodebag,

                    kodeP,
                    kodeL,
                    statusgiro,
                    simbol,
                    flagsimbol,
                    kodecost,
                    kodesubcost,
                    nodph,
                    urutdph,
                    dppdph,
                    tp,
                    ppklx,
                    nofaktur,
                    plok,
                    nobons,
                    jmlrecord,
                    notitipan,
                    uruttitipan,
                    pSKB
                },
                success: function(res) {
                    console.log(res, '!')

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
                error: function(err) {
                    console.log(err)
                    alertify.warning('Terjadi kesalahan silahkan refresh browser')
                }
            })









        }




        function buttonAddEdit(index) {


            barangEdit = listData[index]
            console.log(barangEdit)

            if (barangEdit.NoFaktur.match('IVRJ')) {
                console.log('z')
                alertify.warning("IVRJ tidak bisa diedit")

                return

            }

            if (barangEdit.NoFaktur.match('RPB')) {
                console.log('z')
                alertify.warning("RPB tidak bisa diedit")

                return

            }
            document.getElementById("input_modalxedit_nilainotadibayar").value = formatAngka(parseFloat(Number(barangEdit
                .NilaiNota)).toFixed(2))
            document.getElementById("input_modalxedit_dibayar").value = formatAngka(parseFloat(Number(barangEdit.dibayar))
                .toFixed(2))

            // document.getElementById("input_modalxedit_dibayar").value = parseFloat(Number(barangEdit.dibayar)).toFixed(2)

            document.getElementById("input_modalxedit_noinvoice").value = barangEdit.Noinvoice ? barangEdit.Noinvoice : ''
            console.log(barangEdit.TglInv)

            console.log(new Date(barangEdit.TglInv))
            document.getElementById("input_modalxedit_tanggalinvoice").value = formatDate(barangEdit.TglInv, '-')
            refreshTableKLEdit(barangEdit.NoBukti, barangEdit.NoFaktur)

            $('.showhideitemKLEdit').hide()
            $("#formXedit").modal('toggle')





        }


        function buttonAddDelete(index) {


            let barangDelete = listData[index]



            console.log(barangDelete)

            // return


            var dlgHapusItem = alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus faktur ' + barangDelete.NoFaktur + ' ?',
                function() {
                    let _token = $("#_token").val()
                    let choice = "D"


                    let nobukti = barangDelete.NoBukti
                    let nourut = ''
                    let valas = ''
                    let urut = barangDelete.urut
                    let kodecustsupp = barangDelete.KodeCustSupp
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
                        url: "{!! url('pengajuandphtunaispkoreksi') !!}",
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
                            console.log(res, '!')

                            if (res == 1) {
                                // $("#form").modal('toggle')
                                alertify.success('DPH telah dihapus');

                                // $('.showhideitem').hide();
                                loadAll()
                                // buttonCloseForm()
                                tipeform = 'edit'

                                refreshDataTable(nobukti)

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
                        error: function(err) {
                            console.log(err)
                            alertify.warning('Terjadi kesalahan silahkan refresh browser')
                        }
                    })
                },
                function() {
                    console.log('no')
                });
                dlgHapusItem.elements.root.classList.add('ajs-app-buttons', 'is-danger');
        }



        function buttonAddPickInvoice() {
            let checkDate = new Date($("#input_add_tanggal").val())
            let periode_bulan = document.getElementById("periode_bulan").value
            let periode_tahun = document.getElementById("periode_tahun").value
            let nobukti = $("#input_add_nobukti").val();
            let nourut = $("#input_add_nourut").val();

            let tanggal = $("#input_add_tanggal").val();
            let kodecustsupp = $("#input_add_kodecustomer").val();

            if (checkDate.getFullYear() !== Number(periode_tahun) || (checkDate.getMonth() + 1) !== Number(periode_bulan)) {

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
                    if (Number(checkNilai) < 0 || Number(checkKurs) < 0) {
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
                    _token: _token,
                    tempData,
                    tanggal: tanggal,
                    nobukti,
                    nourut,
                    kodecustsupp,
                    tipeform,
                    nourut
                },
                success: function(res) {
                    console.log(res, '!')

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
                error: function(err) {
                    console.log(err)
                    alertify.warning('Terjadi kesalahan silahkan refresh browser')
                }
            })




        }

        function buttonAddListLawan() {
            listLawan = []

            console.log('buttonAddListLawan')


            let _token = $("#_token").val();
            let perkiraan = $("#input_add_kodeperkiraan").val();
            let transaksi = $("#input_add_transaksi").val();
            if (!perkiraan) {
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
                    listLawan = res
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
                error: function(err) {
                    console.log(err)
                    alertify.warning('Terjadi kesalahan silahkan refresh browser')
                }

            })


        }

        function modalDPP(dataLawan) {
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
                error: function(err) {
                    console.log(err)
                    alertify.warning('Terjadi kesalahan silahkan refresh browser')
                }

            })


        }

        function modalDPHUHTBBM(dataLawan) {


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
                error: function(err) {
                    console.log(err)
                    alertify.warning('Terjadi kesalahan silahkan refresh browser')
                }

            })


        }
















        function buttonAddBatal() {

            $('.showhideitem').hide();
        }

        function buttonAddListBatal() {
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


        function closeShowHideItem() {
            $('.showhideitem').hide();

        }

        function unlockFormAdd() {
            document.getElementById("input_add_catatan").disabled = false
            document.getElementById("input_add_tanggal").disabled = false


            document.getElementById("buttonAddListCustomer").disabled = false
            document.getElementById("buttonAddListNoInvoice").disabled = false

        }

        function lockFormAdd() {
            document.getElementById("input_add_tanggal").disabled = true
            document.getElementById("input_add_bon").disabled = true
            document.getElementById("input_add_kepadaterima").disabled = true
            document.getElementById("buttonAddListPerkiraan").disabled = true
            document.getElementById("input_add_transaksi").disabled = true

        }

        function lockFormAddAdd() {
            document.getElementById("buttonAddListDepartemen").disabled = true
            document.getElementById("buttonAddListLawan").disabled = true
            document.getElementById("buttonAddListValas").disabled = true
            document.getElementById("buttonAddListDevisi").disabled = true

        }

        function unlockFormAddAdd() {
            document.getElementById("buttonAddListDepartemen").disabled = false
            document.getElementById("buttonAddListLawan").disabled = false
            document.getElementById("buttonAddListValas").disabled = false
            document.getElementById("buttonAddListDevisi").disabled = false
        }


        function refreshDataTableDet(nobukti) {
            console.log('refreshDataTableDet', nobukti)
            let _token = $("#_token").val();
            listData = []
            $.ajax({
                url: "{!! url('pengajuandphtunaispdetail') !!}",
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
                  <td class="text-right">${item.KL ? formatAngka(parseFloat(Number(item.KL)).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.LB ? formatAngka(parseFloat(Number(item.LB)).toFixed(2)) : '0.00'}</td>



                  <td>${item.Noinvoice ? item.Noinvoice : '' }</td>
                  <td>${item.TglInv && new Date(item.TglInv).getFullYear() > 2000 ? formatDate(item.TglInv) : '' }</td>


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
                error: function(err) {
                    console.log(err)
                    alertify.warning('Terjadi kesalahan silahkan refresh browser')
                    resRefresh = 0;
                }

            })
        }




        function refreshDataTable(nobukti) {
            console.log('refreshDataTable', nobukti)
            let _token = $("#_token").val();
            listData = []
            $.ajax({
                url: "{!! url('pengajuandphtunaispdetail') !!}",
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
                        // <td class="text-right">${formatAngka(parseFloat(Number(item.Nilai) - Number(item.dibayar)).toFixed(2)) }</td>
                        // <td class="text-right">${formatAngka(parseFloat(item.dibayar).toFixed(2))}</td>
                        // <td class="text-right">${formatAngka(parseFloat(Number(item.dibayar) - Number(item.Nilai)).toFixed(2)) }</td>
                        rowTable += `
                <tr>
                  <td>${item.NamaCustSupp}</td>

                  <td>${item.NoFaktur}</td>

                  <td class="text-right">${formatAngka(parseFloat(Number(item.dibayar)).toFixed(2)) }</td>
                  <td class="text-right">${item.KL ? formatAngka(parseFloat(Number(item.KL)).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.LB ? formatAngka(parseFloat(Number(item.LB)).toFixed(2)) : '0.00'}</td>


                  <td>${item.Noinvoice ? item.Noinvoice : '' }</td>
                  <td>${item.TglInv && new Date(item.TglInv).getFullYear() > 2000 ? formatDate(item.TglInv) : '' }</td>


                  <td class="text-center">
                    <div class="action-buttons">
                      <button class="btn-action-sm btn-action-success" type="button" data-toggle="tooltip" title="Edit" onclick="buttonAddEdit(${i})"><i class="bi bi-pen"></i></button>
                      <button class="btn-action-sm btn-action-danger" type="button" data-toggle="tooltip" title="Hapus" onclick="buttonAddDelete(${i})"><i class="bi bi-trash"></i></button>
                    </div>
                  </td>
                </tr>
              `
                        // <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
                    });

                    document.getElementById("addTableData").innerHTML = rowTable
                    $('#addTableData [data-toggle="tooltip"]').tooltip('dispose').tooltip({container: 'body', boundary: 'window'});


                    document.getElementById("input_add_nobukti").value = listData[0].NoBukti

                    // document.getElementById("input_add_transaksi").value = listData[0].NamaCustSupp
                    // document.getElementById("input_add_alamatcustomer").value = listData[0].Alamat1
                    // document.getElementById("input_add_nobukti").value = listData[0].NoBukti
                    document.getElementById("input_add_tanggal").valueAsDate = new Date(listData[0].Tanggal)
                    document.getElementById("input_add_valas").value = listData[0].Valas
                },
                error: function(err) {
                    console.log(err)
                    alertify.warning('Terjadi kesalahan silahkan refresh browser')
                    resRefresh = 0;
                }

            })
        }


        function refreshDataTableDetail(nobukti) {
            console.log('refreshDataDetail', nobukti)
            let _token = $("#_token").val();
            $.ajax({
                url: "{!! url('bankspdetail') !!}",
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
                    if (!res.length) {
                        alertify.success('Data Habis')
                        // $("#form").modal('toggle')
                        $('#page3').hide();
                        // $('#page3').hide();
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
                  <td>${item.Devisi}</td>

                  <td>${item.Perkiraan}</td>
                  <td>${item.NamaPerkiraan}</td>
                  <td>${item.Lawan }</td>
                  <td>${item.NamaLawan }</td>




                  <td>${item.TPHC}</td>
                  <td class="text-right">${item.DebetRp ?  formatAngka(parseFloat(item.DebetRp).toFixed(2)) : '0.00'}</td>
                  <td>${item.Keterangan }</td>
                  <td class="text-right">${item.JumlahGiroRp ? formatAngka(parseFloat(item.JumlahGiroRp).toFixed(2)) : '0.00'}</td>
                  <td>${item.NamaCost ? item.NamaCost : '' }</td>
                  <td>${item.NamaSubCost ? item.NamaSubCost : ''}</td>

                </tr>

              `
                    });

                    document.getElementById("detailTableData").innerHTML = rowTable


                    document.getElementById("input_detail_transaksi").value = listData[0].TipeTransHD
                    document.getElementById("input_detail_kodeperkiraan").value = listData[0].PerkiraanHd
                    document.getElementById("input_detail_keteranganperkiraan").value = listData[0]
                        .NamaPerkiraanHd
                    document.getElementById("input_detail_kepadaterima").value = listData[0].Note
                    document.getElementById("input_detail_nobukti").value = listData[0].NoBukti

                    // document.getElementById("input_detail_transaksi").value = listData[0].NamaCustSupp
                    // document.getElementById("input_detail_alamatcustomer").value = listData[0].Alamat1
                    // document.getElementById("input_detail_nobukti").value = listData[0].NoBukti
                    document.getElementById("input_detail_tanggal").valueAsDate = new Date(listData[0].Tanggal)










                },
                error: function(err) {
                    console.log(err)
                    alertify.warning('Terjadi kesalahan silahkan refresh browser')
                    resRefresh = 0;
                }

            })
        }



        function submitOtorisasi() {

            let _token = $("#_token").val();
            let nobukti = $("#input_detail_nobukti").val();
            $.ajax({
                url: "{!! url('pengajuandphtunaispotorisasi') !!}",
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
                error: function(err) {
                    console.log(err)
                    alertify.warning('Terjadi kesalahan silahkan refresh browser')
                }

            })


        }

        // function buttonDetail (nobukti) {
        //   document.getElementById("divOto").style.display = "none";
        //
        //   let _token = $("#_token").val();
        //   $.ajax({
        //     url: "{!! url('kreditnotespdetail') !!}",
        //     type: "post",
        //     async: false,
        //     data: {
        //       _token,
        //       nobukti
        //
        //     },
        //     success: function(res) {
        //       console.log(res)
        //       // listData = res
        //       // console.log(res)
        //       if (!res.length) {
        //           alertify.success('Data tidak ditemukkan')
        //           // $("#form").modal('toggle')
        //           return
        //       }
        //       // dataTableAdd = res
        //
        //       let rowTable = ``
        //       res.forEach((item, i) => {
        //               rowTable += `
    //                 <tr>
    //                   <td>${item.NoInv}</td>
    //                   <td>${item.Keterangan}</td>
    //                   <td class="text-right">${item.Nilai ? formatAngka(parseFloat(item.Nilai).toFixed(2)) : '0.00'}</td>
    //                   <td class="text-right">${item.Saldo ?  formatAngka(parseFloat(item.Saldo).toFixed(2)) : '0.00'}</td>
    //
    //                   <td>${item.kodeVls}</td>
    //                   <td class="text-right">${item.Kurs ?  formatAngka(parseFloat(item.Kurs).toFixed(2)) : '0.00'}</td>
    //                   <td class="text-right">${item.NilaiRp ? formatAngka(parseFloat(item.NilaiRp).toFixed(2)) : '0.00'}</td>
    //                   <td class="text-right">${item.Saldo ?  formatAngka(parseFloat(item.Saldo).toFixed(2)) : '0.00'}</td>
    //
    //
    //
    //                 </tr>
    //
    //               `
        //       });
        //
        //       document.getElementById("detailTableData").innerHTML = rowTable
        //
        //
        //         document.getElementById("input_detail_kodecustomer").value = res[0].KodeSupp
        //         document.getElementById("input_detail_namacustomer").value = res[0].NamaCustSupp
        //         document.getElementById("input_detail_alamatcustomer").value = res[0].Alamat1
        //         document.getElementById("input_detail_nobukti").value = res[0].NoBukti
        //         document.getElementById("input_detail_tanggal").valueAsDate = new Date(res[0].tanggal)
        //
        //         $('#modalDetail').show();
        //         $('.mainpage').hide();
        //         $('#page3').show();
        //
        //
        //
        //
        //
        //
        //
        //
        //     },
        //     error: function (err) {
        //       console.log(err)
        //       alertify.warning('Terjadi kesalahan silahkan refresh browser')
        //       resRefresh = 0;
        //     }
        //
        //   })
        //
        // }

        function buttonDetail(nobukti) {
            console.log('buttonKoreksi', nobukti)

            // cleanFormAdd()
            refreshDataTableDet(nobukti)
            $('.page3showhide').hide();
            $('.detailshowhide').show();
            $('.mainpage').hide();
            $('#page3').show();

        }


        function buttonOtorisasi(nobukti) {
            console.log('buttonOtorisasi', nobukti)

            let akses = $("#akses_isotorisasi1").val();
            if (!Number(akses)) {
                alertify.warning('No access')
                return
            }

            // cleanFormAdd()
            refreshDataTableDet(nobukti)
            $('.page3showhide').hide();
            $('.otorisasishowhide').show();
            $('.mainpage').hide();
            $('#page3').show();

        }




        function buttonKoreksi(nobukti) {
            console.log('buttonKoreksi', nobukti)

            let akses = $("#akses_iskoreksi").val();

            if (!Number(akses)) {
                alertify.warning('No access')
                return
            }


            tipeform = 'edit'
            // cleanFormAdd()
            refreshDataTable(nobukti)


            if (listData.length) {
                if (listData[0].IsOtorisasi1 == 1) {
                    alertify.warning("Data sudah diotorisasi")

                } else {
                    $('.mainpage').hide();
                    $('#page2').show();
                }
            }

            return

            // $.ajax({
            //   url: "{!! url('pengajuandphtunaispdetail') !!}",
            //   type: "post",
            //   async: false,
            //   data: {
            //     _token,
            //     tglawal,
            //     tglakhir,
            //     valas,
            //     tipe,
            //     tipelist,
            //     kodecustsupp
            //   },
            //   success: function(res) {
            //     console.log(res)
            //     dataTable = res
            //     // listPengajuan = res
            //     // listCheckListPengajuan = []
            //
            //     rowTable = ''
            //
            //     $('#tabel_add_list').DataTable().destroy();
            //     res.forEach((item, i) => {
            //       rowTable += `
        //     <tr>
        //     <td><div class="form-check text-center">
        //         <input id="pengajuanCheckList${i}" onchange="pengajuanCheckList(${i},this.id)" class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
        //         </div></td>
        //     <td>${item.NamaCustSupp}</td>
        //     <td>${formatDate(item.JatuhTempo)}</td>
        //     <td>${item.NoFaktur}</td>
        //     <td class="text-right">${formatAngka(parseFloat(item.Kredit).toFixed(2))}</td>
        //     <td class="text-right">${formatAngka(parseFloat(item.JmlDibayar).toFixed(2))}</td>
        //     <td class="text-right">${formatAngka(parseFloat(item.diBayar).toFixed(2))}</td>
        //     <td class="text-right">${formatAngka(parseFloat(item.KL).toFixed(2))}</td>
        //     <td class="text-right">${formatAngka(parseFloat(item.LB).toFixed(2))}</td>
        //     <td>${ item.Perkiraan ? item.Perkiraan: ''}</td>
        //     <td>${ item.NOInvoice ? item.NOInvoice: ''}</td>
        //     <td>${ item.TglInvoice ? formatDate(item.TglInvoice) : ''}</td>
        //     </tr>`
            //     });
            //
            //
            //     document.getElementById("tabel_data_add_list_modal").innerHTML = rowTable
            //     // document.getElementById("tabel_data_add_list_modal").innerHTML = `<td colspan=12 class="text-center">Belum ada data</td>`
            //
            //     $('#page1').hide();
            //     $('#page2').show();
            //     $('#formAddAdd').show();
            //   //   $("#tabel_add_list_modal").DataTable({
            //   //     "lengthChange": false,
            //   //       "paging": false ,'order': [[1, 'asc']],
            //   //       "searching" : false,
            //   //       "columnDefs": [
            //   //     {"targets" :[0] , 'orderable' : false}
            //   //    // {  "className": "text-center", "targets": [4] },
            //   //  ]
            //   // });
            //
            //   },
            //   error: function (err) {
            //     console.log(err)
            //     alertify.warning('Terjadi kesalahan silahkan refresh browser')
            //   }
            //
            // })
            //
            //
            //
            //
            //
            // $('.mainpage').hide();
            // $('#page2').show();
        }

        function pengajuanCheckList(index, id) {
            let data = listPengajuan[index]
            console.log(data)
            if (document.getElementById(`pengajuanCheckList${index}`).checked) {
                console.log('add baru')
                // kalo tidak ada data di adddataarray langsung add , kalo ada data cek namacustsupp

                if (!listCheckListPengajuan.length) {
                    document.getElementById(`list_proses_dibayar${index}`).value = formatAngka(parseFloat(Number(data.Kredit) - Number(
                        data.JmlDibayar)).toFixed(2))
                    listPengajuan[index].diBayar = Number(data.Kredit) - Number(data.JmlDibayar)
                    listPengajuan[index].noinvoice = data.NOInvoice
                    listPengajuan[index].tanggalinvoice = formatDate(data.TglInvoice)
                    listCheckListPengajuan.push(listPengajuan[index])
                    console.log(listCheckListPengajuan)
                    return
                }


                if (listCheckListPengajuan[0].KodeCustSupp != data.KodeCustSupp) {
                    alertify.warning('Cust Supp berbeda')
                    document.getElementById(`pengajuanCheckList${index}`).checked = !document.getElementById(
                        `pengajuanCheckList${index}`).checked
                    console.log(listCheckListPengajuan)
                    return
                }
                document.getElementById(`list_proses_dibayar${index}`).value = formatAngka(parseFloat(Number(data.Kredit) - Number(data
                    .JmlDibayar)).toFixed(2))
                listPengajuan[index].diBayar = Number(data.Kredit) - Number(data.JmlDibayar)
                listPengajuan[index].noinvoice = data.NOInvoice
                listPengajuan[index].tanggalinvoice = formatDate(data.TglInvoice)
                listCheckListPengajuan.push(listPengajuan[index])
                console.log(listCheckListPengajuan)
                // console.log(document.getElementById(`outstandingCheckList${index}`).checked)
                // console.log(!document.getElementById(`outstandingCheckList${index}`).checked)
                // document.getElementById(`outstandingCheckList${index}`).checked = !document.getElementById(`outstandingCheckList${index}`).checked
            } else {
                console.log(data)
                console.log(listCheckListPengajuan)
                let check = listCheckListPengajuan.findIndex(el => el.NoFaktur === data.NoFaktur);
                console.log('hapus')

                console.log(check)
                listCheckListPengajuan.splice(check, 1)

                document.getElementById(`list_proses_dibayar${index}`).value = '0.00'
                // document.getElementById(`list_proses_LB${index}`).value = '0.00'
                document.getElementById(`list_proses_KL${index}`).value = '0.00'
                listPengajuan[index].diBayar = 0
                console.log('---')
                console.log(listTambahKL)
                delete listTambahKL[data.NoFaktur];
            }

        }

        function buttonAddKL() {

            console.log(saveHeaderInvoice.NoFaktur)
            let xlist = listTambahKL[saveHeaderInvoice.NoFaktur]
            let check = listCheckListPengajuan.findIndex(el => el.NoFaktur === saveHeaderInvoice.NoFaktur);
            console.log(check)
            let xnilainota = $("#input_modalx_nilainotadibayar").val().toString().replace(/,/g, '')
            let xtanggalinvoice = $("#input_modalx_tanggalinvoice").val()
            let xnoinvoice = $("#input_modalx_noinvoice").val()

            let xdibayar = $("#input_modalx_dibayar").val().toString().replace(/,/g, '')
            // if (xnilainota < xdibayar ) {
            //   alertify.warning("Melebihi nilai nota")
            //   return
            //
            // }

            // let xlist = listTambahKL[saveHeaderInvoice.NoFaktur]
            console.log(xlist)
            if (!xlist) {
                xlist = []
            }

            let xtotalKL = 0

            xlist.forEach((item, i) => {
                xtotalKL += Number(item.inputKL)


            });

            if (Number(xnilainota) < Number(xdibayar) + Number(xtotalKL)) {
                alertify.warning('Save Dibayar / LB terlebih dahulu ')

                return
            }

            //pengecekan
            console.log(listCheckListPengajuan)
            console.log(listCheckListPengajuan[check])
            listCheckListPengajuan[check].diBayar = xdibayar
            listCheckListPengajuan[check].tanggalinvoice = xtanggalinvoice
            listCheckListPengajuan[check].noinvoice = xnoinvoice

            document.getElementById(`list_proses_dibayar${saveHeaderIndex}`).value = formatAngka(parseFloat(xdibayar).toFixed(2))
            alertify.success("Berhasil update dibayar")



            $('.showhideitemKL').show()

            document.getElementById("input_modalx_kurangbayar").value = parseFloat(Number(xnilainota) - Number(xdibayar) -
                Number(xtotalKL)).toFixed(2)
            document.getElementById("input_modalx_perkiraankurangbayar").value = ''
            document.getElementById("input_modalx_namaperkiraankurangbayar").value = ''
        }


        function buttonAddKLEdit() {

            $('.showhideitemKLEdit').show()

            document.getElementById("input_modalxedit_kurangbayar").value = '0.00'
            document.getElementById("input_modalx_perkiraankurangbayaredit").value = ''
            document.getElementById("input_modalx_namaperkiraankurangbayaredit").value = ''
        }

        // function updateListPengajuan

        function buttonRefreshListPengajuan(tipelist = 0, kodecustsupp = '') {
            let tglawal = formatDate(new Date())

            let tglakhir = $("#input_modal_tanggaljatuhtempo").val();
            let valas = $("#input_modal_valas").val();


            let tipe = 'HT'


            if (tipeform == 'edit') {
                tipelist = 1
                kodecustsupp = listData[0].KodeCustSupp
            }

            let _token = $("#_token").val();
            console.log("buttonRefreshListPengajuan")
            console.log({
                tglawal,
                tglakhir,
                valas,
                tipe,
                tipelist,
                kodecustsupp
            })
            $.ajax({
                url: "{!! url('pengajuandphtunaisplistpengajuan') !!}",
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

                    // $('#tabel_add_list_modal').DataTable().destroy();
                    res.forEach((item, i) => {
                        // <input style="height:30px; width: 150px" id="list_proses_dibayar${i}" type="number" value='${parseFloat(item.diBayar).toFixed(2)}' class="form-control text-right" disabled>

                        rowTable += `
      <tr>
      <td><div class="form-check">
          <input id="pengajuanCheckList${i}" onchange="pengajuanCheckList(${i},this.id)" class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
          </div></td>
          <td class="dph-col-supplier">${item.NamaCustSupp}</td>
          <td class="dph-col-jth">${formatDate(item.JatuhTempo)}</td>
          <td>${item.NoFaktur}</td>
      <td class="text-right">${formatAngka(parseFloat(item.Kredit).toFixed(2))}</td>
      <td class="text-right">${formatAngka(parseFloat(item.JmlDibayar).toFixed(2))}</td>
      <td class="text-center">
      <div class="input-group form-group dph-dibayar-group">
        <input class="dph-inp-sm form-control text-right" id="list_proses_dibayar${i}" type="text" value='0.00' disabled>

        <button id="buttonChangeDibayar${i}" class="btn btn-chip-biru" type="button" onclick="buttonChangeDibayar(${i})"><i class="bi bi-plus"></i></button>

      </div></td>

      <td class="text-center">
      <input class="dph-inp-kl form-control text-right" id="list_proses_KL${i}" type="text" value='0.00' disabled>
      </td>

      <td>${ item.NOInvoice ? item.NOInvoice: ''}</td>
      <td>${ item.TglInvoice ? formatDate(item.TglInvoice) : ''}</td>
      </tr>`
                    });
                    // <td>${ item.Perkiraan ? item.Perkiraan: ''}</td>


                    document.getElementById("tabel_data_add_list_modal").innerHTML = rowTable
                    // document.getElementById("tabel_data_add_list_modal").innerHTML = `<td colspan=12 class="text-center">Belum ada data</td>`

                    //   $("#tabel_add_list_modal").DataTable({
                    //     "lengthChange": false,
                    //       "paging": false ,'order': [[1, 'asc']],
                    //       "searching" : false,
                    //       "columnDefs": [
                    //     {"targets" :[0] , 'orderable' : false} , { width: '2000px', targets: [6] }
                    //    // {  "className": "text-center", "targets": [4] },
                    //  ]
                    // });

                },
                error: function(err) {
                    console.log(err)
                    alertify.warning('Terjadi kesalahan silahkan refresh browser')
                }

            })

        }





        function buttonAddBatalKL() {
            $('.showhideitemKL').hide()
        }

        function buttonAddBatalKLEdit() {
            $('.showhideitemKLEdit').hide()
        }

        function buttonChangeDibayar(index) {
            // sp_TempTerimaDPP

            let xcheck = document.getElementById(`pengajuanCheckList${index}`).checked
            if (!xcheck) {
                alertify.warning("Pilih invoice terlebih dahulu")
                return
            }

            let x = listPengajuan[index]
            console.log(x)

            if (x.NoFaktur.match('IVRJ')) {
                console.log('z')
                alertify.warning("IVRJ tidak bisa diedit")

                return
            }

            if (x.NoFaktur.match('RPB')) {
                console.log('z')
                alertify.warning("RPB tidak bisa diedit")

                return
            }
            // listPengajuan[saveHeaderIndex]
            saveHeaderInvoice = listPengajuan[index]
            saveHeaderIndex = index
            let xdibayar = $(`#list_proses_dibayar${index}`).val().toString().replace(/,/g, '');
            // let xLB = $(`#list_proses_LB${index}`).val();
            // let sisa = $(`#input_modal_sisa`).val();
            console.log(saveHeaderInvoice.NoBukti)
            // console.log(listTambahKL[saveHeaderInvoice.NoBukti])

            console.log(x.TOTFAKTUR)
            console.log(formatAngka(parseFloat(x.TOTFAKTUR).toFixed(2)))
            console.log('==')
            // console.log(xdibayar, xLB , sisa)
            document.getElementById("input_modalx_nilainotadibayar").value = formatAngka(parseFloat(Number(x.Kredit) - Number(x
                .JmlDibayar)).toFixed(2))
            document.getElementById("input_modalx_dibayar").value = formatAngka(parseFloat(xdibayar).toFixed(2))

            document.getElementById("input_modalx_dibayar").value = formatAngka(parseFloat(xdibayar).toFixed(2))

            document.getElementById("input_modalx_noinvoice").value = x.noinvoice ? x.noinvoice : ''
            document.getElementById("input_modalx_tanggalinvoice").value = x.tanggalinvoice
            refreshTableKL()

            $('.showhideitemKL').hide()


            $("#formX").modal('toggle')


        }

        function buttonAddItem() {

            listTambahKL = []
            let kodecustsuppx = listData[0].KodeCustSupp
            let tipelist = 1

            let periode_bulan = document.getElementById("periode_bulan").value
            let periode_tahun = document.getElementById("periode_tahun").value
            // console.log(periode_bulan)
            // let lastDay = new Date(y, Number(periode_bulan), 1);
            // console.log(lastDay)

            var lastDayOfMonth = new Date(periode_tahun, periode_bulan, 0);
            console.log(lastDayOfMonth)


            document.getElementById("input_modal_valas").value = 'IDR'
            document.getElementById("input_modal_tanggaljatuhtempo").value = formatDate(lastDayOfMonth)
            document.getElementById("input_modal_nobukti").value = $("#input_add_nobukti").val();
            document.getElementById("input_modal_tanggal").value = $("#input_add_tanggal").val();

            console.log('buttonAddItem', tipelist, kodecustsuppx)
            $(".showhidelistpengajuandph").hide();
            buttonRefreshListPengajuan(tipelist, kodecustsuppx)


            formPageOrigin = 'page2';
            $('.mainpage').hide();
            $('#page4').show();
        }

        function buttonAdd(nobukti) {
            console.log('buttonAdd', nobukti)

            let akses = $("#akses_istambah").val();
            let _token = $("#_token").val();
            if (!Number(akses)) {
                alertify.warning('No access')
                return
            }


            tipeform = 'add'
            cleanModalAdd()
            setNewNoBukti()

            $(".showhidelistpengajuandph").show();
            buttonRefreshListPengajuan()

            // $('.showhideitem').hide();
            // document.getElementById("input_modal_tanggal").valueAsDate = new Date()
            // document.getElementById("input_modal_valas").value = 'IDR'
            // document.getElementById("input_modal_tanggaljatuhtempo").value = formatDate(lastDayOfMonth)

            formPageOrigin = 'page1';
            $('.mainpage').hide();
            $('#page4').show();
            return
            //
            //
            // document.getElementById("input_add_tanggal").disabled = false
            // document.getElementById("input_add_bon").disabled = false
            // document.getElementById("input_add_kepadaterima").disabled = false
            // document.getElementById("buttonAddListPerkiraan").disabled = false
            // document.getElementById("input_add_transaksi").disabled = false
            //
            //
            //
            //
            // document.getElementById("addTableData").innerHTML = `<td colspan=12 class="text-center">Belum ada data</td>`
            //
            // // unlockFormAdd()
            // $('.showhideitem').hide();
            // // $('.showhideform').hide();
            // $('#formAdd').show();
            // // $("#form").modal('toggle')
            //
            // // input_add_nobukti
            // // document.getElementById("input_add_nobukti").value = nobukti
            //
            // cleanFormAdd()
            // // setNewNoBukti()
            // document.getElementById("input_add_transaksi").value = 'BBK'
            // onChangeTransaksi()
            //
            // $('.mainpage').hide();
            // $('#page2').show();

        }

        function buttonAddAddItem() {
            let value = $("#input_add_kodeperkiraan").val();
            if (!value) {
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

        function buttonAddEditItem(i) {
            tempBarangAddEdit = listData[i]
            console.log(tempBarangAddEdit)
            lockFormAddAdd()
            cleanFormAddAdd()
            let value = $("#input_add_transaksi").val();

            console.log(value, tempBarangAddEdit.KodeL)
            if (value == 'BBM' && tempBarangAddEdit.KodeL == 'UHT') {
                document.getElementById("AddAddJumlah").disabled = true
            } else {
                document.getElementById("AddAddJumlah").disabled = false
            }

            document.getElementById("AddAddKodeDevisi").value = tempBarangAddEdit.Devisi
            document.getElementById("AddAddNamaDevisi").value = tempBarangAddEdit.NamaDevisi

            document.getElementById("AddAddValas").value = tempBarangAddEdit.Valas
            document.getElementById("AddAddKurs").value = parseFloat(tempBarangAddEdit.Kurs).toFixed(2)

            document.getElementById("AddAddLawan").value = tempBarangAddEdit.TipeTrans == 'BBK' ? tempBarangAddEdit
                .Perkiraan : tempBarangAddEdit.Lawan
            document.getElementById("AddAddKeteranganLawan").value = tempBarangAddEdit.TipeTrans == 'BBK' ?
                tempBarangAddEdit.NamaPerkiraan : tempBarangAddEdit.NamaLawan

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


        function buttonCloseForm() {
            $('.mainpage').hide();
            // $('#page2').hide();
            $('#page1').show();
            // #page1 bisa saja sudah dirender saat masih disembunyikan (loadAll() dipanggil
            // dari page2/3/4) - dphtAturTinggiTabel() melewati perhitungan tinggi saat itu,
            // jadi dihitung ulang sekarang setelah #page1 benar-benar terlihat lagi.
            dphtAturTinggiTabel();

        }

        let formPageOrigin = 'page1';

        function buttonClosePage4() {
            $('.mainpage').hide();
            $('#' + formPageOrigin).show();
            // no-op kalau yang ditampilkan kembali bukan #page1 - lihat dphtAturTinggiTabel().
            dphtAturTinggiTabel();
        }

        // Menggantikan render for-loop Blade / DataTable() lama — satu list, ditulis ke lastRows lalu
        // digambar lewat renderTabel() (sama seperti paint pertama), supaya tombol Aksi/kolom/
        // pager/Tampilkan konsisten sebelum dan sesudah refresh. tempOutstanding masih dikelompokkan
        // per NoBukti di controller (tidak diubah — lihat catatan di new-design-gudang-style-guide.md
        // soal menjaga perubahan tetap view-only), jadi tetap diratakan di sini seperti seed awal.
        function loadAll() {
            document.getElementById('tabel_data').innerHTML =
                '<tr><td colspan="20" class="text-center">' + loadingHtml('Memuat data...') + '</td></tr>';

            let date1 = $('#inputDate1').val();
            let date2 = $('#inputDate2').val();
            $.ajax({
                url: "{!! url('pengajuandphtunailoadall') !!}",
                type: "get",
                async: true,
                data: {
                    date1,
                    date2
                },
                success: function(res) {
                    lastRows = (res.tempOutstanding || []).map(g => g[0]);
                    renderTabel();
                },
                error: function(err) {
                    console.error("Load failed:", err);
                    alertify.warning('Terjadi kesalahan silahkan refresh browser');
                }
            });
        }

        function submitPrint(nobukti) {
            // for (var i = 0; i < 30; i++) {
            //   dataPrint.push(dataPrint[0])
            // }
            let _token = $('#_token').val()
            $.ajax({
                url: "{!! url('pengajuandphtunaidetailCetak') !!}",
                type: "post",
                async: false,
                data: {
                    _token: _token,
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
            for (let i = 0; i < dataPrint.length; i += 8) {
                let tempArray = dataPrint.slice(i, i + 8)
                arrayDataPrint.push(tempArray)
            }

            let printContent = ''
            let imageContent = document.getElementById(`imagecontainer`).innerHTML;
            let css = ''
            let hdr = ''
            let str = ''
            let ftr = ''
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
              <td colspan="9" style="text-align:center; font-weight:bold; font-size:16px; border:none;">
                DPH
              </td>
             </tr>

              <!-- TGL DAN NOBUKTI -->
              <tr>
                <td colspan="4" style="border:1px solid;">
                  Tgl : ${tanggalOnly}
                </td>
                <td colspan="6" style="border:1px solid;">
                  No : ${dataPrint[0].NoBukti}
                </td>
              </tr>

              <!-- SUPPLIER -->
              <tr>
                <td colspan="9" style="border:1px solid;">
                  Supplier : ${dataPrint[0].Note ? dataPrint[0].Note : '-'}
                </td>
              </tr>
                  <tr>
                    <td rowspan="2" class="text-center" style="width: 1%">No.</td>
                    <td colspan="3" class="text-center" style="width: 20%">FAKTUR BELI</td>
                    <td colspan="2" class="text-center" style="width: 20%">INVOICE SUPPLIER</td>
                    <td rowspan="2" class="text-center" style="width: 25%">JUMLAH</td>
                    <td colspan="2" class="text-center" style="width: 10%">INVOICE</td>
                  </tr>
                  <tr>
                    <td class="text-center">TGL</td>
                    <td class="text-center">NO</td>
                    <td class="text-center">JTH</td>

                    <td class="text-center">TGL</td>
                    <td class="text-center">NO</td>

                    <td class="text-center">ASLI</td>
                    <td class="text-center">COPY</td>
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
                console.log('arrayDataPrint', i)
                if (i == 0) {

                    tempPrintStr +=
                        `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; margin-top:5px">`
                    // } else if ( i < 1) {
                    //   tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; padding-top:15px; page-break-before: always">`
                } else {
                    tempPrintStr +=
                        `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px;padding-top:7px; ">`
                }
                tempPrintStr += hdr
                item.forEach((itemSub, j) => {
                    tempPrintStr += ``



                    tempPrintStr += `
         <tr>
         <td class="text-align: center"
               style="width: 1%; ">${z+1}</td>
         <td class="text-align: left"
               style="width: 20%;  ">${itemSub.Tglbeli ? itemSub.Tglbeli.split(' ')[0] : ''}</td>
         <td class="text-align: left"
               style="width: 20%;  ">${itemSub.NOFAKTUR ?? ''}</td>
         <td class="text-align: left"
               style="width: 20%;  ">${itemSub.Tgljth ? itemSub.Tgljth.split(' ')[0] : ''}</td>
         <td class="text-align: left"
               style="width: 20%;">${itemSub.Tglinv ? itemSub.Tglinv.split(' ')[0] : ''}</td>
         <td class="text-align: left"
               style="width: 20%;">${itemSub.Noinv ?? ''}</td>
         <td style="width: 25%; text-align: right;">
            ${itemSub.DIBAYAR
              ? Number(itemSub.DIBAYAR).toLocaleString('id-ID', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                })
              : ''}
          </td>
          <td style="text-align:center; font-weight:bold; font-size:14px;">
            ${itemSub.Asli == 1 ? '&#10003;' : ''}
          </td>
          <td style="text-align:center; font-weight:bold; font-size:14px;">
            ${itemSub.Copy == 1 ? '&#10003;' : ''}
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
	  <td colspan="5" style="border:1px solid; padding:5px; font-weight:bold;">
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
	  <td colspan="3" style="border:1px solid;"></td>
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
          <div style="width:50%; font-size:15px;">
            <p class="m-0">No.Rek : ${dataPrint[0].NoAcc ?? '-'}</p>
            <p class="m-0">A/N : ${dataPrint[0].ATN ?? '-'}</p>
            <p class="m-0">Bank : ${dataPrint[0].bank ?? '-'}</p>
          </div>

          <!-- KANAN -->
          <div style="width:50%;">
            <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: 20px; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 20%">Disetujui</td>
               <td class="no-border text-center" style="width: 20%">Diperiksa</td>
             </tr>
             <tr style="height: 2.5rem">
               <td class="no-border">&nbsp;</td>
             </tr>

             <tr>
               <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
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


            tempPrintStr += `</body></html>`



            w = window.open(' ')
            w.document.write(tempPrintStr)

            w.print()
            w.close()

        }

        function buttonBatalOtorisasi(nobukti) {

            console.log(nobukti)



            let akses = $("#akses_isbatal").val();
            if (!Number(akses)) {
                alertify.warning('No access')
                return
            }

            var dlgBatalOtorisasi = alertify.confirm('Batal Otorisasi', 'Batal Otorisasi DPH ' + nobukti + ' ?',
                function() {
                    let _token = $("#_token").val();

                    $.ajax({
                        url: "{!! url('pengajuandphtunaispbatalotorisasi') !!}",
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
                        error: function(err) {
                            console.log(err)
                            alertify.warning('Terjadi kesalahan silahkan refresh browser')
                        }

                    })
                },
                function() {
                    console.log('no')
                });
                dlgBatalOtorisasi.elements.root.classList.add('ajs-app-buttons', 'is-danger');
        }




        function formatDate(date, pemisah = '-') {
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

        function formatAngka(angkaString) {
            // console.log('formatAngka' , angkaString);
            let tempAngka = angkaString.split('.')

            if (tempAngka[0][0] == '-') {
                let temp2 = ''

                let tempAngka1 = tempAngka[0].split('-')
                for (let i = 0; i < tempAngka1[1].length; i++) {
                    if (i != 0 && i % 3 == 0) {
                        temp2 = ',' + temp2
                    }
                    temp2 = tempAngka1[1][tempAngka1[1].length - i - 1] + temp2
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
                temp1 = tempAngka[0][tempAngka[0].length - i - 1] + temp1
                // console.log(i, temp1)
            }
            temp1 += '.' + tempAngka[1]
            return temp1
        }

        function unformatAngka(angka) {
            if (!angka) return 0
            return parseFloat(String(angka).replace(/,/g, '')) || 0
        }

        function formatAngkaInput(el) {
            el.value = formatAngka(unformatAngka(el.value).toFixed(2))
        }

        // Dipasang di oninput supaya separator ribuan langsung muncul sambil mengetik, tidak
        // menunggu pindah fokus (onblur formatAngkaInput() tetap jalan untuk menormalkan ke 2
        // desimal). Sama seperti formatAngkaKetik() di accounting/memorialkoreksi.blade.php.
        function formatAngkaKetik(el) {
            let posDariKanan = el.value.length - el.selectionStart
            let minus = el.value.trim().startsWith('-') ? '-' : ''
            let raw = el.value.replace(/[^0-9.]/g, '')

            let titikIndex = raw.indexOf('.')
            let bulat = titikIndex === -1 ? raw : raw.slice(0, titikIndex)
            let desimal = titikIndex === -1 ? '' : raw.slice(titikIndex + 1).replace(/\./g, '').slice(0, 2)

            bulat = bulat.replace(/^0+(?=\d)/, '')
            if (bulat === '') {
                bulat = '0'
            }

            let bulatFormatted = ''
            for (let i = 0; i < bulat.length; i++) {
                if (i != 0 && (bulat.length - i) % 3 == 0) {
                    bulatFormatted += ','
                }
                bulatFormatted += bulat[i]
            }

            el.value = minus + bulatFormatted + (titikIndex !== -1 ? '.' + desimal : '')

            let posBaru = Math.max(0, el.value.length - posDariKanan)
            el.setSelectionRange(posBaru, posBaru)
        }
    </script>
@endsection

