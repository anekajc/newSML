@extends('newmasterTest')
@section('page-title', 'Cetak Pengajuan DPH')
@section('buttons')
@endsection

@section('css')
    <div id="imagecontainer" class="d-none" style="">
        <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
    </div>

    {{-- Gudang-style list view (#page1 only) — see docs/new-design-gudang-style-guide.md.
     Extends newmasterTest (not accounting.newmaster — that layout's own unscoped
     .btn/.btn-primary/.btn-danger rules collide with tableMaster2.css's .btn-action-*
     tint classes, discolouring the pill buttons — newmasterTest is Bootstrap 4
     already and loads newmaster.css globally itself, but not report-table.css/
     tableMaster2.css — those two (and a redundant-but-harmless newmaster.css link, kept
     for consistency with pengajuandph.blade.php) are added here, page-local, so no other
     page on this shared layout is affected.

     newmasterTest.blade.php (lines ~505-534) ALSO defines its own unscoped .btn/
     .btn-primary/.btn-danger, positioned right after @yield('css') — i.e. loaded AFTER
     tableMaster2.css's .btn-action-primary/.btn-action-danger/.btn-action-success tint
     classes below. Equal specificity (single class each) + later source order means the
     layout's generic colors win over the tint at rest on every button that combines both
     (btn-primary + btn-action-primary, as this whole button convention does), even though
     .btn-pill-* survives untouched (scoped #contentContainer .btn.btn-pill-*, higher
     specificity) — hence the pill shape looking right while the color didn't.

     Fixed centrally in tableMaster2.css — see its "Pill-button rest + hover, pinned" block,
     which raises the tint to .btn.btn-pill-*.btn-action-* (0,3,0 at rest, 0,4,0 on hover) so
     it outranks both the layout's colors and bootstrap's :hover. NOT fixed with !important:
     !important on the .btn-action-* tints also outranks canvas/bootstrap.css's
     .btn-primary:hover, which is what SUPPLIES the solid color these pill buttons animate
     towards, so it wins the rest state at the cost of killing the hover transition entirely.

     Because the fix lives in tableMaster2.css, every page loading that file is covered in one
     place — including the same collision on purchasing/newmasterx.blade.php,
     report/newmasterxreport.blade.php and accounting/newmaster.blade.php, and their pages
     (accounting/kas.blade.php, accounting/bank.blade.php, accounting/pengajuandph.blade.php,
     etc.). Nothing page-local is required here for colors. --}}
    <link rel="stylesheet" href="{!! URL::asset('css/report-table.css') !!}?v={{ @filemtime(base_path('public/css/report-table.css')) ?: '1' }}">
    <link rel="stylesheet" href="{!! URL::asset('css/tableMaster2.css') !!}?v={{ @filemtime(base_path('public/css/tableMaster2.css')) ?: '1' }}">
    <link rel="stylesheet" href="{!! URL::asset('css/newmaster.css') !!}?v={{ @filemtime(base_path('public/css/newmaster.css')) ?: '1' }}">

    {{-- .dph-table-outer/.dph-table-wrap/.dph-tb — skin for tables inside the page2/page3
     forms (outside .tb-report, so report-table.css's .tb classes don't apply here per
     new-design-gudang-style-guide.md §3). Shared with accounting/pengajuandph.blade.php's
     own analogous page2 table — reused rather than duplicated, per that file's own note. --}}
    <link rel="stylesheet"
        href="{!! URL::asset('css/pengajuandphtunai.css') !!}?v={{ @filemtime(base_path('public/css/pengajuandphtunai.css')) ?: '1' }}">

    {{-- #page1's two tabs ("Nota belum cetak tanda terima" / "Nota sudah cetak tanda terima")
     were made byte-for-byte identical to accounting/memorialkoreksi.blade.php /
     accounting/bonsementara.blade.php on request — same `po-*` class family, same
     DataTables-driven pagination — replacing each tab's own hand-rolled .tb-report/
     #mainTableOut+#mainTablePenerimaan/.pg pager (see this page's @section('js') below —
     ctkHeadHtml()/ctkInitReportTableSekali()/ctkAturTinggiTabel() for the conversion,
     shared helpers parameterized by table/bar id since both tabs are two instances of the
     same pattern on one page). Two SEPARATE
     table ids are used (#tabelOut / #tabelPenerimaan, one per tab) rather than reusing
     #tabel, each newly registered in po-table-header.css's ID-scoped drag/gear rules —
     see the note there. report-table.css/tableMaster2.css/newmaster.css/
     pengajuandphtunai.css stay loaded regardless (unlike the reference pages) because
     page2/page3 (the Tambah/Detail/Otorisasi/Koreksi forms) and the tabel_add_list_*
     entity-picker modals still depend on them — the CSS below just neutralizes the few
     rules from those files that would otherwise leak onto #tabelOut/#tabelPenerimaan
     (same technique as accounting/bank.blade.php / accounting/kas.blade.php). --}}
    <link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
    <link rel="stylesheet" href="{!! URL::asset('css/scrollbar-autohide.css') !!}?v={{ @filemtime(base_path('public/css/scrollbar-autohide.css')) ?: '1' }}">

    <style>
    /* Jarak kartu ke bar atas - sama seperti pengajuandph/bank/kas. */
    #content { padding-top: 12px; }

    /* po-table-header.css tidak menulis .po-len-wrap/.po-len-inp - disalin apa adanya dari
       accounting/bank.blade.php. */
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
       (dan bonsementara.blade.php) supaya kedua tab di #page1 memakai class & tampilan
       yang persis sama, dengan selector didobelkan untuk #tabelOut DAN #tabelPenerimaan
       (dua tabel di satu halaman, bukan satu seperti referensinya).
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
    #tabelOut, #tabelPenerimaan { min-width: 100%; }

    /* ---------- Kolom Aksi - tombol bulat kecil warna pastel ---------- */
    #tabelOut td:first-child:not([colspan]),
    #tabelPenerimaan td:first-child:not([colspan]) { vertical-align: middle; }

    #tabelOut td:first-child .po-aksi-wrap,
    #tabelPenerimaan td:first-child .po-aksi-wrap {
      display: flex;
      gap: 4px;
      justify-content: center;
      align-items: center;
    }

    #tabelOut td:first-child .btn,
    #tabelPenerimaan td:first-child .btn {
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

    #tabelOut td:first-child .btn:hover,
    #tabelPenerimaan td:first-child .btn:hover {
      filter: brightness(0.97);
      transform: translateY(-1px);
    }

    #tabelOut td:first-child .btn-success, #tabelPenerimaan td:first-child .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
    #tabelOut td:first-child .btn-warning, #tabelPenerimaan td:first-child .btn-warning { color: #b45309; border-color: #fbe3bd; background: #fef3e0; }
    #tabelOut td:first-child .btn-primary, #tabelPenerimaan td:first-child .btn-primary { color: #2563eb; border-color: #cfdcff; background: #e8edff; }
    #tabelOut td:first-child .btn-danger,  #tabelPenerimaan td:first-child .btn-danger  { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }
    #tabelOut td:first-child .btn-info,    #tabelPenerimaan td:first-child .btn-info    { color: #0891b2; border-color: #a5f3fc; background: #ecfeff; }

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
       Penetral kebocoran gaya - halaman ini (beda dengan memorialkoreksi) masih memuat
       report-table.css/tableMaster2.css/newmaster.css karena page2/page3 (form tambah/
       detail/otorisasi/koreksi) dan modal-modal tabel_add_list_* masih memakainya. Aturan
       di bawah ini HANYA menimpa balik nilai file-file itu supaya #tabelOut/#tabelPenerimaan
       tetap identik dengan referensi, tanpa melepas file-nya (yang akan merusak halaman
       lain). Sama seperti accounting/bank.blade.php / accounting/kas.blade.php.

       font-size/color/padding SENGAJA TANPA !important - tableMaster2.css tidak menandai
       ketiganya !important pada #tabel tbody td (beda dengan border-color di bawah, yang
       DIPAKSA !important lewat aturan terpisah), jadi spesifisitas ekstra dari ID #page1 di
       sini sudah cukup menang tanpa !important. Kalau dipaksa !important, itu akan
       mengalahkan Bootstrap .text-success/.text-danger (yang !important) pada sel Oto -
       itulah yang membuat ikon centang/silang di kolom Oto tampak hitam alih-alih
       hijau/merah (bug yang sama sempat terjadi di pengajuandph/pengajuandphtunai, sudah
       diperbaiki di sana juga).
       ========================================================================== */

    /* newmaster.css: `table tbody td { padding: 0 10px !important }` global.
       Sengaja TIDAK ditimpa lagi - baris dibuat rapat (0 10px) persis seperti memorialkoreksi,
       yang juga membiarkan aturan global itu menang. */
    /* #page1 #tabelOut tbody td, #page1 #tabelPenerimaan tbody td { padding: 12px 16px !important; } */

    /* tableMaster2.css: skin khusus id #tabel (tidak match #tabelOut/#tabelPenerimaan
       langsung, tapi thead th/tbody td-nya dituliskan generik lewat selector lain di file
       itu) - dikembalikan ke nilai .data-table di atas supaya konsisten dengan referensi. */
    #page1 #tabelOut thead th, #page1 #tabelPenerimaan thead th {
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
    #page1 #tabelOut tbody td, #page1 #tabelPenerimaan tbody td {
      font-size: 14px;
      color: var(--text-main, #1f2937);
      border-color: #f3f4f6 !important;
      border-left: none;
      border-right: none;
    }
    #page1 #tabelOut tbody tr:hover, #page1 #tabelPenerimaan tbody tr:hover { background-color: transparent !important; }
    #page1 #tabelOut td:last-child, #page1 #tabelPenerimaan td:last-child { font-weight: inherit !important; }

    /* tableMaster2.css: chrome DataTables (panjang halaman/info/pagination) diwarnai ungu
       (--sp-primary) dan diberi padding tambahan - dikembalikan ke nilai bawaan
       jquery.dataTables.css 1.13.2 supaya sama persis dengan memorialkoreksi. */
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

    {{-- #page3 pill-button SHAPE only -----------------------------------------------
     The colour/hover half of this page's pill buttons is fixed centrally now — see the
     "Pill-button rest + hover, pinned" block in tableMaster2.css, which this page already
     loads above. Nothing page-local is needed for that any more.

     What is still page-local is a structural gap: #page1 and #page2 wrap their content in
     `id="contentContainer"` (lines ~235 and ~417) but #page3 (line ~655) does not, so
     tableMaster2.css's `#contentContainer .btn.btn-pill-*` / `.modal .btn.btn-pill-*` never
     reached #page3's CLOSE and Otorisasi buttons — they rendered as plain rectangles with the
     layout's .btn padding instead of pills. Re-declared here on .mainpage (the class on all
     three #page wrappers) so the shape reaches them.

     Values are copied from tableMaster2.css's .btn-pill-* rule verbatim. NOT added to
     tableMaster2.css itself: `.mainpage .btn.btn-pill-*` is 0,3,0, which loses to
     `#contentContainer .btn` (1,1,0) anyway, so globally it would only ever affect pill
     buttons orphaned outside BOTH #contentContainer and .modal — i.e. this page's #page3 and
     any other page with the same missing wrapper. Changing button shapes on unaudited pages
     is not worth it; the alternative permanent fix is to give #page3 its own
     `id="contentContainer"` wrapper like the other two pages have, after which this block
     can be deleted outright. --}}
    <style>
        .mainpage .btn.btn-pill-primary,
        .mainpage .btn.btn-pill-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 30px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>

    {{-- "Tampilkan" (page-size) dropdown — not part of report-table.css on purpose, copied
     page-locally per new-design-gudang-style-guide.md §8. --}}
    <style>
        .len-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--white);
            border: 1.5px solid var(--border);
            border-radius: 8px;
            padding: 5px 12px;
        }

        .len-wrap label {
            margin: 0;
            font-size: 11.5px;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .05em;
            white-space: nowrap;
        }

        .len-inp {
            border: none;
            background: transparent;
            font-size: 13px;
            font-weight: 700;
            color: #1D2130;
            outline: none;
            cursor: pointer;
            padding: 2px 20px 2px 0;
            appearance: none;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%231D2130' stroke-width='2.5'><polyline points='6 9 12 15 18 9'/></svg>");
            background-repeat: no-repeat;
            background-position: right center;
        }

        .tb-report .pg.disabled {
            opacity: .4;
            cursor: not-allowed;
            pointer-events: none;
        }
    </style>


    <style>
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
        #tabel_add_list_customer_filter {
            display: flex;
            align-items: flex-end;
            margin-bottom: 0px;
        }

        #tabel_add_list_customer_filter label input {
            width: 150px;
            border-radius: 10px;
            border: 1px solid #ccc;
            box-shadow: none;
            font-size: 0.65rem;
        }

        #tabel_add_list_noinvoice_filter {
            display: flex;
            align-items: flex-end;
            margin-bottom: 0px;
        }

        #tabel_add_list_noinvoice_filter label input {
            width: 150px;
            border-radius: 10px;
            border: 1px solid #ccc;
            box-shadow: none;
            font-size: 0.65rem;
        }

        #tabel_add_list_barang_filter {
            display: flex;
            align-items: flex-end;
            margin-bottom: 0px;
        }

        #tabel_add_list_barang_filter label input {
            width: 150px;
            border-radius: 10px;
            border: 1px solid #ccc;
            box-shadow: none;
            font-size: 0.65rem;
        }

        #tabel_add_list_nobeli_filter {
            display: flex;
            align-items: flex-end;
            margin-bottom: 0px;
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
            margin-bottom: 0px;
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
            margin-bottom: 0px;
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
@endsection


@section('content')
    <div id="page1" class="container-fluid mainpage">
        {{-- <div class="container-fluid">
            <div class="row" style="margin-top: -30px">
                <div class="col-12 text-left">
                    <h2>Cetak Tanda Terima DPH</h2>
                </div>
            </div>
        </div> --}}

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

            <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

            {{-- Dua tab: dua dataset yang benar-benar berbeda (belum-cetak vs sudah-cetak, bentuk
       kolom beda) — bukan satu list dengan filter status, jadi tetap dua tab per
       new-design-gudang-style-guide.md §11. Bootstrap sendiri yang mengatur class
       active lewat data-toggle="tab", cukup styling .tab-toggle/.tab-toggle-btn. --}}
            <div class="tab-toggle nav" id="nav-tab" role="tablist" style="margin-bottom: 10px">
                <a class="tab-toggle-btn active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab"
                    aria-controls="home" aria-selected="true">Nota belum cetak tanda terima</a>
                <a class="tab-toggle-btn" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab"
                    aria-controls="profile" aria-selected="false">Nota sudah cetak tanda terima</a>
            </div>

            {{-- Kartu + toolbar + tabel disalin dari accounting/memorialkoreksi.blade.php per
             tab, sama seperti accounting/bank.blade.php / accounting/kas.blade.php - lihat
             catatan di @section('css') soal dua id tabel terpisah. --}}
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="nav-home-tab">
                    <div class="card">
                        <div class="card-body" style="padding:0;">

                            <div class="po-toolbar">
                                <input class="po-search-inp" type="search" id="searchBoxOut" placeholder="Cari data">

                                <div class="po-len-wrap">
                                    <label for="tabelLenOut">Tampilkan</label>
                                    <select id="tabelLenOut" class="po-len-inp">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                        <option value="-1">Semua</option>
                                    </select>
                                </div>
                            </div>

                            {{-- #rtBarOut diisi lewat JS oleh ReportTable.init() - lihat ctkInitReportTableSekali(). --}}
                            <div id="rtBarOut"></div>

                            <table id="tabelOut" class="data-table po-aksi-hover">
                                <thead id="tabelOut_header" class="text-center">
                                    <tr>
                                        <th style="padding: 4px 12px;" scope="col">Actions</th>
                                        <th style="padding: 4px 12px;" scope="col">No. Bukti</th>
                                        <th style="padding: 4px 12px;" scope="col">Supplier</th>
                                        <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                                        <th style="padding: 4px 12px;" scope="col">Valas</th>
                                        <th style="padding: 4px 12px;" scope="col">Nilai</th>
                                        <th style="padding: 4px 12px;" scope="col">KL</th>
                                    </tr>
                                </thead>
                                <tbody id="tabel_data" class="text-left">
                                    {{-- Baris digambar renderTabelOut() lewat JS, supaya susunan kolom hasil
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

                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                    <div class="card">
                        <div class="card-body" style="padding:0;">

                            <div class="po-toolbar">
                                <input class="po-search-inp" type="search" id="searchBoxPenerimaan" placeholder="Cari data">

                                <div class="po-len-wrap">
                                    <label for="tabelLenPenerimaan">Tampilkan</label>
                                    <select id="tabelLenPenerimaan" class="po-len-inp">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                        <option value="-1">Semua</option>
                                    </select>
                                </div>

                                <button class="po-btn-filter" type="button" onclick="$('#modalFilterPenerimaan').modal('show')">
                                    <i class="bi bi-funnel"></i> Filter
                                </button>
                            </div>

                            {{-- #rtBarPenerimaan diisi lewat JS oleh ReportTable.init() - lihat ctkInitReportTableSekali(). --}}
                            <div id="rtBarPenerimaan"></div>

                            <table id="tabelPenerimaan" class="data-table po-aksi-hover">
                                <thead id="tabelPenerimaan_header" class="text-center">
                                    <tr>
                                        <th style="padding: 4px 12px;" scope="col">Actions</th>
                                        <th style="padding: 4px 12px;" scope="col">No Bukti</th>
                                        <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                                        <th style="padding: 4px 12px;" scope="col">Kode Cust</th>
                                        <th style="padding: 4px 12px;" scope="col">Nama Cust</th>
                                        <th style="padding: 4px 12px;" scope="col">No DPP</th>
                                        <th style="padding: 4px 12px;" scope="col">Dibayar</th>
                                        <th style="padding: 4px 12px;" scope="col">LB</th>
                                        <th style="padding: 4px 12px;" scope="col">KL</th>
                                        <th style="padding: 4px 12px;" scope="col">Oto</th>
                                        <th style="padding: 4px 12px;" scope="col">User Oto</th>
                                        <th style="padding: 4px 12px;" scope="col">Tgl Oto</th>
                                    </tr>
                                </thead>
                                <tbody id="tabel2_data" class="text-left">
                                    {{-- Baris digambar renderTabelPenerimaan() lewat JS, supaya susunan kolom hasil
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

        </div>
    </div>
    <!-- closes #page1 (container-fluid mainpage) -->

    {{-- modal filter (tab "sudah cetak" saja) — DILETAKKAN DI LUAR .tb-report, lihat
     new-design-gudang-style-guide.md §3. --}}
    <div class="modal fade rt-filter" id="modalFilterPenerimaan">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-filter"></i>
                        Filter Laporan
                        <span class="rt-active-badge" id="filterBadgePenerimaan">0 aktif</span>
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
                                <label class="rt-field-label" for="modalOtorisasiPenerimaan">Otorisasi</label>
                                <select class="rt-native" id="modalOtorisasiPenerimaan">
                                    <option value="2">Semua</option>
                                    <option value="1">Sudah Otorisasi</option>
                                    <option value="0">Belum Otorisasi</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="rt-reset-link" onclick="resetAllFiltersPenerimaan()">Reset
                        semua</button>
                    <div class="rt-footer-buttons">
                        <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal">Batal</button>
                        <button type="button" class="rt-btn rt-btn-primary"
                            onclick="applyModalFilterPenerimaan()">Terapkan</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- modal filter -->

    <div id="page2" style="display: none" class="mainpage container-fluid">

        <div class="row" style="margin-top: 0" id="contentContainer">
            <div class="col-8 text-left">
                {{-- <h2>Cetak Tanda Terima DPH</h2> --}}
            </div>
            <div class="col-4 text-right">
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
                                            <input type="date" class="form-control text-center" id="input_add_tanggal"
                                                placeholder="" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="container-fluid">
                            <hr />
                        </div>
                        <div class="col-md-12 mt-2 text-right">
                            <button type="button" class="btn btn-chip-biru" onclick="submitPrint()">Cetak</button>
                        </div>
                        <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">

                            <div class="dph-table-outer">
                                <div class="dph-table-wrap">
                                    <table id="addTable" class="dph-tb">
                                        <thead id="addTableHead">
                                            <tr>
                                                <th scope="col">No. Invoice</th>
                                                <th scope="col">Supplier</th>
                                                <th scope="col">Valas</th>
                                                <th scope="col" class="num">Dibayar</th>
                                                <th scope="col" class="num">KL</th>
                                            </tr>
                                        </thead>
                                        <tbody id="addTableData" class="text-left">
                                            <tr>
                                                <td colspan="5" class="text-center">Belum ada data</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div id="formAddAdd" class="container-fluid showhideitem">
                            <div class="col-12">
                                <hr />
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
                                                    <input id="AddAddFaktur" type="text" class="form-control"
                                                        disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" style="margin-top: 0px">
                                    <div class="col-md-3">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Dibayar</label>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="input-group form-group">
                                                    <input id="AddAddDibayar" type="number"
                                                        class="form-control text-right" disabled>
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
                                                    <input id="AddAddLebihBayar" type="number"
                                                        class="form-control text-right" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-top: 0px">
                                    <div class="col-md-3">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Kurang Bayar</label>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="input-group form-group">
                                                    <input id="AddAddKurangBayar" type="number"
                                                        class="form-control text-right" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" style="margin-top: 0px">
                                    <div class="col-md-3">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Perkiraan</label>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="input-group form-group">
                                                    <input id="AddAddKodePerkiraan" type="text" class="form-control"
                                                        disabled>
                                                    <input type="text" class="form-control" id="AddAddNamaPerkiraan"
                                                        disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-2" style="margin-top: 0" id="contentContainer">
                                <div class="col-md-12 text-right mt-4" id="contentContainer">
                                    <button type="button" class="btn btn-action-danger btn-danger btn-pill-primary"
                                        onclick="buttonAddBatal()">Batal</button>
                                    <button id="buttonSubmitEdit" type="button" onclick="submitEdit()"
                                        class="btn btn-primary btn-action-primary btn-pill-primary">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="page3" style="display: none" class="mainpage container-fluid">

        <div class="row" style="margin-top: 0">
            <div class="col-8 text-left">
                {{-- <h2 class="page3showhide detailshowhide"> Detail Piutang DPP</h2>
                <h2 class="page3showhide otorisasishowhide"> Otorisasi Piutang DPP</h2> --}}
            </div>
            <div class="col-4 text-right">
                <button type="button" class="btn btn-action-danger btn-danger btn-pill-primary"
                    onclick="buttonCloseForm()">CLOSE</button>
            </div>
        </div>

        <div id= "" class="">
            <div id="formBsGrid" class="">
                <div class="">
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

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row" style="margin-top: 0px">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>BKM/BBM</label>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="input-group form-group">
                                                    <input id="input_detail_nobkmbbm" type="text" class="form-control"
                                                        disabled>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" style="margin-top: 0px">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Valas</label>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="input-group form-group">
                                                    <input id="input_detail_valas" type="text" class="form-control"
                                                        disabled>
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
                                            <div class="col-md-8">
                                                <div class="input-group form-group">
                                                    <input id="input_detail_kodecust" type="text" class="form-control"
                                                        disabled>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: 0px">
                                            <div class="col-md-4">
                                            </div>
                                            <div class="col-md-8">
                                                <div class="input-group form-group">
                                                    <input id="input_detail_namacust" type="text" class="form-control"
                                                        disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 0px">
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Jumlah</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="input-group form-group">
                                            <input id="input_detail_jumlah" type="number"
                                                class="form-control text-right" disabled>
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
                                            <input id="input_detail_dibayar" type="number"
                                                class="form-control text-right" disabled>
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
                                    <div class="col-md-8">
                                        <div class="input-group form-group">
                                            <input id="input_detail_sisa" type="number" class="form-control text-right"
                                                disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="container-fluid">
                            <hr />
                        </div>
                        <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">
                            <div class="dph-table-outer">
                                <div class="dph-table-wrap">
                                    <table id="detailTable" class="dph-tb">
                                        <thead>
                                            <tr>
                                                <th scope="col">Nama Kas Bank</th>
                                                <th scope="col">No. Invoice</th>
                                                <th scope="col" class="text-right">Dibayar</th>
                                                <th scope="col" class="text-right">LB</th>
                                                <th scope="col" class="text-right">KL</th>
                                                <th scope="col">Perkiraan</th>
                                                <th scope="col">Kode</th>
                                                <th scope="col">Supp. / Cust.</th>
                                            </tr>
                                        </thead>
                                        <tbody id="detailTableData" class="">
                                            <tr>
                                                <td colspan="8" class="text-center">Belum ada data</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12 text-right">
                                    <button type="button"
                                        class="page3showhide otorisasishowhide btn btn-primary btn-action-primary btn-pill-primary"
                                        onclick="submitOtorisasi()">Otorisasi</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- start modal add -->
    <div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="min-width: 1400px">
            <div id="" class="modal-content ">
                <div id= "" class="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="">Proses Terima DPP</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div id="formBsGrid" class="">
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Jumlah</label>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <input type="number" class="form-control text-right"
                                                        id="input_modal_jumlah" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" style="margin-top: 0px">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Dibayar</label>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <input type="number" class="form-control text-right"
                                                        id="input_modal_dibayar" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" style="margin-top: 0px">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Sisa</label>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <input type="number" class="form-control text-right"
                                                        id="input_modal_sisa" disabled>
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
                                                    <input type="text" class="form-control" id="input_modal_nobukti"
                                                        placeholder="" disabled>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row" style="margin-top: 0px">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Nama Cust</label>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="input_modal_namacust"
                                                        placeholder="" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12" style="overflow:auto;  max-height: 400px">
                                        <table id="tabel_add_list_modal" class="table table-bordered table-striped"
                                            style="overflow:auto; ">
                                            <thead class="text-center bg-primary text-white"
                                                style="position: sticky;
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

                                            <tbody id="tabel_data_add_list_modal" class="text-left">

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

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-pill-secondary"
                            data-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary btn-action-primary btn-pill-primary"
                            onclick="submitAdd()">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- End modal add-->

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

                    <div id="formBsGrid" class="">
                        <div class="modal-body">
                            <div class="container-fluid">
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
                                                    <input type="number" class="form-control text-right"
                                                        id="input_modalx_nilainotadibayar" disabled>
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
                                                    <input type="number" class="form-control text-right"
                                                        id="input_modalx_sisanotadibayar" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" style="margin-top: 0px">
                                    <div class="col-md-4">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Dibayar</label>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <input type="number" class="form-control text-right"
                                                        id="input_modalx_dibayar">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" style="margin-top: 0px">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Lebih Bayar</label>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <input type="number" class="form-control text-right"
                                                        id="input_modalx_lebihbayar">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: 0px">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Perk LB</label>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group input-group">
                                                    <input type="text" class="form-control text-right"
                                                        id="input_modalx_perkiraanlebihbayar" disabled>

                                                    <input type="text" class="form-control"
                                                        id="input_modalx_namaperkiraanlebihbayar" disabled>
                                                    <button id="buttonAddListPerkiraanLebihBayar" type="button"
                                                        onclick="buttonAddListPerkiraanLebihBayar('lebihbayar')"
                                                        class="btn btn-chip-biru">+</button>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2" style="margin-top: 0">
                                    <div class="col-md-12 text-right mt-4">

                                        <button id="buttonSaveLB" type="button" onclick="buttonSaveLB()"
                                            class="btn btn-success btn-action-success btn-pill-primary">Save</button>
                                        <button type="button" id="buttonAddKL" class="btn btn-chip-biru"
                                            onclick="buttonAddKL()">+ KL</button>
                                    </div>
                                </div>
                            </div>
                            <div id="formAddKL" class="container-fluid showhideitemKL">
                                <div class="col-12">
                                    <hr />
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h4 id="">Add KL</h4>
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
                                                <div class="col-md-4">
                                                    <div class="input-group form-group">
                                                        <input id="input_modalx_kurangbayar" type="number"
                                                            value="0.00" class="text-right form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row" style="margin-top: 0px">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label>Perk KL</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group input-group">
                                                        <input type="text" class="form-control"
                                                            id="input_modalx_perkiraankurangbayar" disabled>
                                                        <input type="text" class="form-control"
                                                            id="input_modalx_namaperkiraankurangbayar" disabled>
                                                        <button id="buttonAddListPerkiraanKurangBayar" type="button"
                                                            onclick="buttonAddListPerkiraanLebihBayar('kurangbayar')"
                                                            class="btn btn-chip-biru">+</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2" style="margin-top: 0">
                                    <div class="col-md-12 text-right mt-4">
                                        <button type="button" class="btn btn-secondary btn-pill-secondary"
                                            onclick="buttonAddBatalKL()">Batal</button>
                                        <button id="buttonSubmitAddKL" type="button" onclick="submitAddKL()"
                                            class="btn btn-primary btn-action-primary btn-pill-primary">Submit Add</button>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="margin-top:20px">
                                <div class="col-12" style="overflow:auto;  max-height: 400px">
                                    <table id="tabel_add_list_modalx" class="table table-bordered table-striped"
                                        style="overflow:auto; ">
                                        <thead class="text-center bg-primary text-white"
                                            style="position: sticky;
            top: 0;
            z-index: 1;">
                                            <tr>
                                                <th style="padding: 4px 12px;" scope="col">Kurang Bayar</th>
                                                <th style="padding: 4px 12px;" scope="col">Perkiraan</th>
                                                <th style="padding: 4px 12px;" scope="col">Nama perkiraan</th>

                                            </tr>
                                        </thead>


                                        <tbody id="tabel_data_add_list_modalx" class="text-left">
                                            <tr>
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

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-pill-secondary"
                        data-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="formPerkiraan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="min-width: 1400px">
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
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-12">
                                        <h3>Perkiraan</h3>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12" style="overflow:auto;  max-height: 400px">
                                        <table id="tabel_add_list_perkiraan" class="table table-bordered table-striped"
                                            style="overflow:auto; ">
                                            <thead class="text-center bg-primary text-white"
                                                style="position: sticky;
                top: 0;
                z-index: 1;">
                                                <tr>
                                                    <th style="padding: 4px 12px;" scope="col">Perkiraan</th>
                                                    <th style="padding: 4px 12px;" scope="col">Nama</th>
                                                    <th style="padding: 4px 12px;" scope="col">Aksi</th>

                                                </tr>
                                            </thead>

                                            <tbody id="tabel_data_add_list_perkiraan" class="text-left">

                                                @for ($i = 0; $i < count($tempListPerkiraan); $i++)
                                                    <tr>
                                                        <td>{{ $tempListPerkiraan[$i]->Perkiraan }}</td>
                                                        <td>{{ $tempListPerkiraan[$i]->Keterangan }}</td>

                                                        <td class="text-center">
                                                            <div class="action-buttons">
                                                                <button class="btn-action-sm btn-action-primary"
                                                                    data-toggle="tooltip" title="Pilih"
                                                                    onclick="buttonAddPickPerkiraanLebihBayar('{{ $tempListPerkiraan[$i]->Perkiraan }}' , '{{ $tempListPerkiraan[$i]->Keterangan }}')"
                                                                    type="button"><i class="bi bi-plus"></i></button>
                                                            </div>
                                                        </td>
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
                        <button type="button" class="btn btn-secondary btn-pill-secondary"
                            data-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary btn-action-primary btn-pill-primary"
                            onclick="submitAdd()">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
@endsection

@section('js')
    <script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
    <script type="text/javascript">
        let listData = []
        let listOutstanding = []
        // let addTableData = []
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

        /* =========================================================================
         * List "Nota belum cetak" (#tabelOut, tbody #tabel_data) dan "Nota sudah cetak"
         * (#tabelPenerimaan, tbody #tabel2_data) — draggable/hideable columns + DataTables
         * pagination, byte-for-byte sama dengan accounting/memorialkoreksi.blade.php /
         * accounting/bonsementara.blade.php per tab (lihat section('css') di atas). Dua
         * tab = dua dataset yang benar-benar berbeda, jadi dua instance ReportTable dan
         * dua instance DataTables terpisah — lihat ctkInitReportTableSekali()/
         * ctkHeadHtml()/ctkAturTinggiTabel() di bawah, diparameterkan lewat tableId/barId
         * supaya tidak perlu menduplikasi logikanya.
         * lastRowsOut/lastRowsPenerimaan diisi dari data Blade saat render pertama
         * DAN dari loadAll() saat refresh — satu-satunya fungsi render dipakai untuk
         * keduanya supaya tidak lagi bisa berbeda seperti sebelumnya.
         * ========================================================================= */
        let lastRowsOut = (@json($tempOutstanding)).map(g => g[0]);
        let lastRowsPenerimaan = (@json($tempPenerimaan)).map(g => g[0]);
        let globalOtorisasiPenerimaan = "2"; // filter modal: 2=Semua, 1=Sudah Otorisasi, 0=Belum Otorisasi

        let tabelLenOut = 10;
        let tabelLenPenerimaan = 10;

        // ---- Tab "Nota belum cetak" (Outstanding) ----
        var g_hrefOut = 'cetakpengajuandph_out';
        var g_modeReportOut = '1';
        var gcart_headerOut = [];
        var gsum_issubtotalOut = 0,
            gsum_isgrandtotalOut = 0,
            gct_desimal_maxOut = 4;

        function setDefaultHeaderOut() {
            // [ field, label, visible, type, total, decimals ]
            gcart_headerOut = [
                ['nobukti', 'No. Bukti', 1, 'varchar', 0, 0],
                ['namacustsupp', 'Supplier', 1, 'varchar', 0, 0],
                ['tanggal', 'Tanggal', 1, 'date', 0, 0],
                ['valas', 'Valas', 1, 'varchar', 0, 0],
                ['dibayar', 'Nilai', 1, 'float', 0, 2],
                ['kl', 'KL', 1, 'float', 0, 2],
            ];
        }

        function doSetHeaderOut(_modereport, _isReset = false) {
            let _strHeader = (!_isReset) ? doLoadHeaderOut(g_hrefOut, _modereport) : "";
            if (_strHeader != "") {
                gcart_headerOut = doGetHeaderOut(_strHeader);
            } else {
                setDefaultHeaderOut();
                doSimpanHeaderOut(g_hrefOut, g_modeReportOut, gcart_headerOut, gsum_issubtotalOut, gsum_isgrandtotalOut);
            }
        }

        function doLoadHeaderOut(_href, _mode) {
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
                        gsum_issubtotalOut = Number(res[0].issubtotal);
                        gsum_isgrandtotalOut = Number(res[0].isgrandtotal);
                    }
                }
            });
            return _header;
        }

        function doGetHeaderOut(_strHeader) {
            let _cart = [];
            _strHeader.split("||").forEach((item) => {
                let p = item.split(";;");
                _cart.push([p[0], p[1], Number(p[2]), p[3], Number(p[4]), Number(p[5])]);
            });
            return _cart;
        }

        function doSimpanHeaderOut(_href, _mode, _cart, _issubtotal, _isgrandtotal) {
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
                }
            });
        }

        function doMoveHeaderOut(_from, _to) {
            if (_from < 0 || _to < 0 || _from === _to) {
                return;
            }
            if (_from >= gcart_headerOut.length || _to >= gcart_headerOut.length) {
                return;
            }
            let _moved = gcart_headerOut.splice(_from, 1)[0];
            gcart_headerOut.splice(_to, 0, _moved);
            doSimpanHeaderOut(g_hrefOut, g_modeReportOut, gcart_headerOut, gsum_issubtotalOut, gsum_isgrandtotalOut);
        }

        function doButtonVisibilityOut(_id) {
            gcart_headerOut[_id][2] = (Number(gcart_headerOut[_id][2]) === 1) ? 0 : 1;
            doSimpanHeaderOut(g_hrefOut, g_modeReportOut, gcart_headerOut, gsum_issubtotalOut, gsum_isgrandtotalOut);
        }

        function doSetDesimalOut(_index, _step) {
            let _next = Number(gcart_headerOut[_index][5]) + _step;
            if (_next < 0 || _next > gct_desimal_maxOut) {
                return;
            }
            gcart_headerOut[_index][5] = _next;
            doSimpanHeaderOut(g_hrefOut, g_modeReportOut, gcart_headerOut, gsum_issubtotalOut, gsum_isgrandtotalOut);
        }

        function doButtonTotalOut(_index) {
            gcart_headerOut[_index][4] = (Number(gcart_headerOut[_index][4]) === 1) ? 0 : 1;
            doSimpanHeaderOut(g_hrefOut, g_modeReportOut, gcart_headerOut, gsum_issubtotalOut, gsum_isgrandtotalOut);
        }

        // ---- Tab "Nota sudah cetak" (Penerimaan) ----
        var g_hrefPenerimaan = 'cetakpengajuandph_penerimaan';
        var g_modeReportPenerimaan = '1';
        var gcart_headerPenerimaan = [];
        var gsum_issubtotalPenerimaan = 0,
            gsum_isgrandtotalPenerimaan = 0,
            gct_desimal_maxPenerimaan = 4;

        // IsOtorisasi1/OtoUser1/TglOto1 sengaja TIDAK ada di sini lagi — sekarang kolom
        // tetap (Oto/User Oto/Tgl Oto) yang ditambahkan sendiri di renderTabelPenerimaan(),
        // bukan kolom geser/sembunyi, sama seperti accounting/bank.blade.php / kas.blade.php.
        function setDefaultHeaderPenerimaan() {
            gcart_headerPenerimaan = [
                ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
                ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
                ['KODECUSTSUPP', 'Kode Cust', 1, 'varchar', 0, 0],
                ['NamaCustSupp', 'Nama Cust', 1, 'varchar', 0, 0],
                ['NoDPP', 'No DPP', 1, 'varchar', 0, 0],
                ['TotDIBAYAR', 'Dibayar', 1, 'float', 0, 2],
                ['TotLB', 'LB', 1, 'float', 0, 2],
                ['TotKL', 'KL', 1, 'float', 0, 2],
            ];
        }

        function doSetHeaderPenerimaan(_modereport, _isReset = false) {
            let _strHeader = (!_isReset) ? doLoadHeaderPenerimaan(g_hrefPenerimaan, _modereport) : "";
            if (_strHeader != "") {
                gcart_headerPenerimaan = doGetHeaderPenerimaan(_strHeader);
            } else {
                setDefaultHeaderPenerimaan();
                doSimpanHeaderPenerimaan(g_hrefPenerimaan, g_modeReportPenerimaan, gcart_headerPenerimaan,
                    gsum_issubtotalPenerimaan, gsum_isgrandtotalPenerimaan);
            }
        }

        function doLoadHeaderPenerimaan(_href, _mode) {
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
                        gsum_issubtotalPenerimaan = Number(res[0].issubtotal);
                        gsum_isgrandtotalPenerimaan = Number(res[0].isgrandtotal);
                    }
                }
            });
            return _header;
        }

        function doGetHeaderPenerimaan(_strHeader) {
            let _cart = [];
            _strHeader.split("||").forEach((item) => {
                let p = item.split(";;");
                _cart.push([p[0], p[1], Number(p[2]), p[3], Number(p[4]), Number(p[5])]);
            });
            return _cart;
        }

        function doSimpanHeaderPenerimaan(_href, _mode, _cart, _issubtotal, _isgrandtotal) {
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
                }
            });
        }

        function doMoveHeaderPenerimaan(_from, _to) {
            if (_from < 0 || _to < 0 || _from === _to) {
                return;
            }
            if (_from >= gcart_headerPenerimaan.length || _to >= gcart_headerPenerimaan.length) {
                return;
            }
            let _moved = gcart_headerPenerimaan.splice(_from, 1)[0];
            gcart_headerPenerimaan.splice(_to, 0, _moved);
            doSimpanHeaderPenerimaan(g_hrefPenerimaan, g_modeReportPenerimaan, gcart_headerPenerimaan,
                gsum_issubtotalPenerimaan, gsum_isgrandtotalPenerimaan);
        }

        function doButtonVisibilityPenerimaan(_id) {
            gcart_headerPenerimaan[_id][2] = (Number(gcart_headerPenerimaan[_id][2]) === 1) ? 0 : 1;
            doSimpanHeaderPenerimaan(g_hrefPenerimaan, g_modeReportPenerimaan, gcart_headerPenerimaan,
                gsum_issubtotalPenerimaan, gsum_isgrandtotalPenerimaan);
        }

        function doSetDesimalPenerimaan(_index, _step) {
            let _next = Number(gcart_headerPenerimaan[_index][5]) + _step;
            if (_next < 0 || _next > gct_desimal_maxPenerimaan) {
                return;
            }
            gcart_headerPenerimaan[_index][5] = _next;
            doSimpanHeaderPenerimaan(g_hrefPenerimaan, g_modeReportPenerimaan, gcart_headerPenerimaan,
                gsum_issubtotalPenerimaan, gsum_isgrandtotalPenerimaan);
        }

        function doButtonTotalPenerimaan(_index) {
            gcart_headerPenerimaan[_index][4] = (Number(gcart_headerPenerimaan[_index][4]) === 1) ? 0 : 1;
            doSimpanHeaderPenerimaan(g_hrefPenerimaan, g_modeReportPenerimaan, gcart_headerPenerimaan,
                gsum_issubtotalPenerimaan, gsum_isgrandtotalPenerimaan);
        }

        // ---- Helper umum ----
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

        // Beberapa field form "Koreksi" (Valas/Kode Cust/Nama Cust/Dibayar/Jumlah/Sisa)
        // sudah tidak ada di markup page2 (dihapus/dikomentari sebelum restyle ini),
        // padahal JS-nya masih mencoba mengisi .value-nya — dulu ini melempar
        // "getElementById(...) is null" dan menghentikan render baris addTable di
        // tengah jalan. Guard ini bikin pengisian field itu skip dengan aman kalau
        // elemennya memang tidak ada, tanpa mengubah field yang masih live.
        function setValIfExists(id, val) {
            const el = document.getElementById(id);
            if (el) {
                el.value = val;
            }
        }

        // ---- Util bersama untuk kedua tabel (Out/Penerimaan) - sama seperti
        // rtPindahBar()/ikatSearch()/ikatPanjangHalaman()/bankHeadHtml()/
        // bankInitReportTableSekali()/bankAturTinggiTabel() di public/js/bank.js, hanya
        // diparameterkan lewat tableId/barId karena satu halaman ini punya dua tabel. ----
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

        function ctkIkatSearch(tableId, inputId) {
            let input = document.getElementById(inputId);
            if (!input || input.dataset.rtBound) { return; }
            input.dataset.rtBound = '1';

            input.addEventListener('input', function() {
                $('#' + tableId).DataTable().search(input.value).draw();
            });
        }

        function ctkIkatPanjangHalaman(tableId, selectId, getLen, setLen) {
            let sel = document.getElementById(selectId);
            if (!sel || sel.dataset.rtBound) { return; }
            sel.dataset.rtBound = '1';
            sel.value = String(getLen());

            sel.addEventListener('change', function() {
                let n = Number(sel.value);
                setLen((n === -1 || n > 0) ? n : 10);
                $('#' + tableId).DataTable().page.len(getLen()).draw();
            });
        }

        // ReportTable.headHtml() dengan fallback bila report-table.js belum termuat - sama
        // seperti bankHeadHtml() di public/js/bank.js.
        function ctkHeadHtml(cols) {
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

        let ctkRtSudahInit = {};

        function ctkInitReportTableSekali(tableId, barId, theadId, onChange) {
            if (ctkRtSudahInit[tableId] || typeof ReportTable === 'undefined') { return; }
            ctkRtSudahInit[tableId] = true;

            ReportTable.init({
                table: '#' + tableId,
                bar: '#' + barId,
                onChange: onChange
            });

            // Sebagian layout memasang penangan klik sendiri di <thead>; teruskan klik pada
            // roda gigi / pegangan geser ke penangan milik ReportTable - sama seperti
            // bankInitReportTableSekali() di public/js/bank.js.
            let guardUlangKlik = false;
            let thead = document.getElementById(theadId);
            if (thead) {
                thead.addEventListener('click', function(e) {
                    if (guardUlangKlik) { return; }
                    let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip');
                    if (!interaktif) { return; }

                    e.stopPropagation();
                    e.preventDefault();

                    guardUlangKlik = true;
                    let ulang = new MouseEvent('click', { bubbles: false, cancelable: true, view: window });
                    Object.defineProperty(ulang, 'target', { value: interaktif, configurable: true });
                    thead.dispatchEvent(ulang);
                    guardUlangKlik = false;
                }, true);
            }
        }

        // Tinggi tabel mengikuti sisa ruang layar - sama seperti bankAturTinggiTabel() di
        // public/js/bank.js. Dilewatkan bila tab yang memuat tabelId ini sedang tidak aktif
        // (wrap.offsetParent === null), dipanggil ulang lewat shown.bs.tab saat tab dibuka.
        function ctkAturTinggiTabel(tableId) {
            let page = document.getElementById('page1');
            if (!page || page.offsetParent === null) { return; }

            let table = document.getElementById(tableId);
            let wrap = table ? table.closest('.po-table-wrap') : null;
            let area = document.getElementById('content');
            if (!area || !wrap || wrap.offsetParent === null) { return; }

            wrap.style.maxHeight = 'none';

            let padBawah = parseFloat(getComputedStyle(area).paddingBottom) || 0;
            let batasBawah = area.getBoundingClientRect().bottom - padBawah;
            let kotak = wrap.getBoundingClientRect();
            let bawah = page.getBoundingClientRect().bottom - kotak.bottom;

            let sisa = batasBawah - kotak.top - bawah - 4;
            wrap.style.maxHeight = Math.max(200, Math.floor(sisa)) + 'px';
        }

        function aksiButtonsHtmlOut(r) {
            const nobukti = pickCI(r, 'nobukti');
            return '<div class="po-aksi-wrap"><button class="btn btn-primary btn-sm" type="button" title="Tambah" onclick="buttonAdd(\'' +
                nobukti + '\')"><i class="bi bi-plus"></i></button></div>';
        }

        function renderTabelOut() {
            const cols = gcart_headerOut.filter(c => c[2] === 1);
            if ($.fn.DataTable.isDataTable('#tabelOut')) { $('#tabelOut').DataTable().destroy(); }
            let thead = document.getElementById('tabelOut_header');
            thead.innerHTML = ctkHeadHtml(cols);
            let baris = thead.querySelector('tr');
            if (baris) {
                baris.insertAdjacentHTML('afterbegin', '<th style="padding: 4px 12px;" scope="col">Actions</th>');
            }

            const rows = lastRowsOut;
            let rowTable = '';
            rows.forEach(function(r) {
                rowTable += '<tr><td class="text-center">' + aksiButtonsHtmlOut(r) + '</td>';
                rowTable += cols.map(function(c) {
                    const v = pickCI(r, c[0]);
                    if (c[3] === 'date') { return '<td>' + (v ? formatDate(v) : '') + '</td>'; }
                    if (c[3] === 'float') {
                        return '<td style="text-align: right;">' +
                            formatAngka(parseFloat(v || 0).toFixed(Number(c[5]) || 0)) + '</td>';
                    }
                    return '<td>' + nullToEmpty(v) + '</td>';
                }).join('');
                rowTable += '</tr>';
            });
            document.getElementById('tabel_data').innerHTML = rowTable;

            $('#tabelOut').DataTable({
                lengthChange: false, pageLength: tabelLenOut, order: [],
                columnDefs: [{ targets: [0], orderable: false }],
                dom: "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                language: { emptyTable: 'Tidak ada data', zeroRecords: 'Tidak ada data yang cocok dengan pencarian' }
            });
            rtPindahBar('rtBarOut', 'tabelOut');
            ctkIkatSearch('tabelOut', 'searchBoxOut');
            ctkIkatPanjangHalaman('tabelOut', 'tabelLenOut', () => tabelLenOut, (v) => { tabelLenOut = v; });
            let inputSearchOut = document.getElementById('searchBoxOut');
            if (inputSearchOut && inputSearchOut.value) { $('#tabelOut').DataTable().search(inputSearchOut.value).draw(); }
            ctkAturTinggiTabel('tabelOut');
        }

        // Satu-satunya tempat yang menentukan tombol Aksi tab "sudah cetak" — dipakai
        // renderTabelPenerimaan() untuk paint pertama MAUPUN tiap refresh loadAll().
        function aksiButtonsHtmlPenerimaan(r) {
            const nobukti = pickCI(r, 'NoBukti');
            const nodpp = pickCI(r, 'NoDPP');
            const detailBtn = '<button class="btn btn-warning btn-sm" type="button" title="Detail" onclick="buttonDetail(\'' +
                nobukti + '\', 0, \'' + nullToEmpty(nodpp) + '\')"><i class="bi bi-info"></i></button>';
            const koreksiBtn = '<button class="btn btn-success btn-sm" type="button" title="Koreksi" onclick="buttonKoreksi(\'' +
                nobukti + '\', \'' + nullToEmpty(nodpp) + '\')"><i class="bi bi-pen"></i></button>';

            let tombolAksi = detailBtn;
            if (Number(pickCI(r, 'IsOtorisasi1')) === 1) {
                tombolAksi += '<button class="btn btn-danger btn-sm" type="button" title="Batal Otorisasi" onclick="buttonBatalOtorisasi(\'' +
                    nobukti + '\')"><i class="bi bi-key"></i></button>';
            } else {
                tombolAksi += '<button class="btn btn-info btn-sm" type="button" title="Otorisasi" onclick="buttonDetail(\'' +
                    nobukti + '\', 1, \'' + nullToEmpty(nodpp) + '\')"><i class="bi bi-key"></i></button>';
            }
            tombolAksi += koreksiBtn;

            return '<div class="po-aksi-wrap">' + tombolAksi + '</div>';
        }

        function filterByOtorisasiPenerimaan(rows, filterVal) {
            if (filterVal === '1') {
                return rows.filter(r => Number(pickCI(r, 'IsOtorisasi1')) === 1);
            }
            if (filterVal === '0') {
                return rows.filter(r => Number(pickCI(r, 'IsOtorisasi1')) === 0);
            }
            return rows;
        }

        function renderTabelPenerimaan() {
            const cols = gcart_headerPenerimaan.filter(c => c[2] === 1);
            if ($.fn.DataTable.isDataTable('#tabelPenerimaan')) { $('#tabelPenerimaan').DataTable().destroy(); }
            let thead = document.getElementById('tabelPenerimaan_header');
            thead.innerHTML = ctkHeadHtml(cols);
            let baris = thead.querySelector('tr');
            if (baris) {
                baris.insertAdjacentHTML('afterbegin', '<th style="padding: 4px 12px;" scope="col">Actions</th>');
                baris.insertAdjacentHTML('beforeend', `
                    <th style="padding: 4px 12px;" scope="col">Oto</th>
                    <th style="padding: 4px 12px;" scope="col">User Oto</th>
                    <th style="padding: 4px 12px;" scope="col">Tgl Oto</th>
                `);
            }

            const rows = filterByOtorisasiPenerimaan(lastRowsPenerimaan, globalOtorisasiPenerimaan);
            let rowTable = '';
            rows.forEach(function(r) {
                let isOtorisasi = Number(pickCI(r, 'IsOtorisasi1')) || 0;
                let otoUser = pickCI(r, 'OtoUser1');
                let tglOto = pickCI(r, 'TglOto1');
                rowTable += '<tr><td class="text-center">' + aksiButtonsHtmlPenerimaan(r) + '</td>';
                rowTable += cols.map(function(c) {
                    const v = pickCI(r, c[0]);
                    if (c[3] === 'date') { return '<td>' + (v ? formatDate(v) : '') + '</td>'; }
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
            document.getElementById('tabel2_data').innerHTML = rowTable;

            $('#tabelPenerimaan').DataTable({
                lengthChange: false, pageLength: tabelLenPenerimaan, order: [],
                columnDefs: [{ targets: [0], orderable: false }],
                dom: "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                language: { emptyTable: 'Tidak ada data', zeroRecords: 'Tidak ada data yang cocok dengan pencarian' }
            });
            rtPindahBar('rtBarPenerimaan', 'tabelPenerimaan');
            ctkIkatSearch('tabelPenerimaan', 'searchBoxPenerimaan');
            ctkIkatPanjangHalaman('tabelPenerimaan', 'tabelLenPenerimaan', () => tabelLenPenerimaan, (v) => { tabelLenPenerimaan = v; });
            let inputSearchPenerimaan = document.getElementById('searchBoxPenerimaan');
            if (inputSearchPenerimaan && inputSearchPenerimaan.value) {
                $('#tabelPenerimaan').DataTable().search(inputSearchPenerimaan.value).draw();
            }
            ctkAturTinggiTabel('tabelPenerimaan');
        }

        /* -- FILTER MODAL tab "sudah cetak" (Otorisasi: Semua/Sudah/Belum) -- */
        function updateFilterBadgePenerimaan() {
            let count = ($('#modalOtorisasiPenerimaan').val() !== '2') ? 1 : 0;
            $('#filterBadgePenerimaan').text(count + ' aktif');
        }

        function resetAllFiltersPenerimaan() {
            $('#modalOtorisasiPenerimaan').val('2');
            updateFilterBadgePenerimaan();
        }

        $(document).on('show.bs.modal', '#modalFilterPenerimaan', function() {
            $('#modalOtorisasiPenerimaan').val(globalOtorisasiPenerimaan);
            updateFilterBadgePenerimaan();
        });

        $(document).on('change', '#modalFilterPenerimaan select.rt-native', updateFilterBadgePenerimaan);

        function applyModalFilterPenerimaan() {
            globalOtorisasiPenerimaan = $('#modalOtorisasiPenerimaan').val();
            renderTabelPenerimaan();
            $('#modalFilterPenerimaan').modal('hide');
        }

        $(document).ready(function() {
            doSetHeaderOut(g_modeReportOut);
            ctkInitReportTableSekali('tabelOut', 'rtBarOut', 'tabelOut_header', renderTabelOut);
            renderTabelOut();

            doSetHeaderPenerimaan(g_modeReportPenerimaan);
            ctkInitReportTableSekali('tabelPenerimaan', 'rtBarPenerimaan', 'tabelPenerimaan_header', renderTabelPenerimaan);
            renderTabelPenerimaan();

            // Render pertama di atas memakai data awal dari Blade (@json($tempOutstanding)/
            // @json($tempPenerimaan)) supaya tabel selalu terisi begitu halaman dibuka, tanpa
            // menunggu/bergantung pada AJAX. loadAll() lalu menimpanya dengan data paling baru
            // (dan berlaku juga sebagai refresh setelah aksi seperti Otorisasi/Koreksi/dst) —
            // kalau AJAX-nya gagal, tabel tetap menampilkan data awal ini, bukan kosong.
            loadAll();

            // Tab "Nota sudah cetak" (Penerimaan) mulai tersembunyi (display:none dari
            // .tab-pane tanpa .show/.active) - ctkAturTinggiTabel('tabelPenerimaan') di atas
            // dilewatkan saat itu (wrap.offsetParent === null), jadi dihitung ulang begitu tab
            // itu benar-benar ditampilkan. Tab "Nota belum cetak" (Out) dihitung ulang juga
            // untuk jaga-jaga kalau ukurannya berubah selagi tab lain terbuka.
            $('#nav-home-tab').on('shown.bs.tab', function() { ctkAturTinggiTabel('tabelOut'); });
            $('#nav-profile-tab').on('shown.bs.tab', function() { ctkAturTinggiTabel('tabelPenerimaan'); });

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
                "paging": false,
            });

        });

        // function testes () {
        //   $("#formX").modal('toggle')
        // }

        function buttonAddListPerkiraanLebihBayar(id) {
            toId = id
            $("#formPerkiraan").modal('toggle')
        }

        function buttonAddPickPerkiraanLebihBayar(perkiraan, nama) {
            document.getElementById(`input_modalx_perkiraan${toId}`).value = perkiraan
            document.getElementById(`input_modalx_namaperkiraan${toId}`).value = nama
            $("#formPerkiraan").modal('toggle')

        }

        function buttonSaveLB() {
            let xnilainota = $("#input_modalx_nilainotadibayar").val()
            let xdibayar = $("#input_modalx_dibayar").val()
            let xlebihbayar = $("#input_modalx_lebihbayar").val()
            let xperkiraanlebihbayar = $("#input_modalx_perkiraanlebihbayar").val()
            let xnamaperkiraanlebihbayar = $("#input_modalx_namaperkiraanlebihbayar").val()
            let xsisa = $("#input_modalx_sisanotadibayar").val()

            let checksisadibayar = Number(listProsesTerimaDPP[saveHeaderIndex].DIBAYAR)
            let checksisalb = Number(listTambahLB[listProsesTerimaDPP[saveHeaderIndex].NOFAKTUR]) ? Number(listTambahLB[
                listProsesTerimaDPP[saveHeaderIndex].NOFAKTUR]) : 0
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
            console.log(Number(checksisalb), Number(xlebihbayar), Number(xsisa))
            if (Number(xdibayar) + Number(xTempTotalKL) > Number(xnilainota)) {
                alertify.warning("Dibayar + KL melebihi nilai nota")
                return
            }

            if (Number(xlebihbayar) + Number(xdibayar) > Number(checksisa) + Number(xsisa)) {
                alertify.warning("Melebihi sisa nota")
                return
            }
            // totfaktur
            if (Number(xdibayar) <= 0 && Number(xlebihbayar) <= 0) {
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
            if (Number(xlebihbayar) <= 0 && xperkiraanlebihbayar) {
                alertify.warning("Lebih bayar belum diisi")
                return
            }

            if (Number(xlebihbayar) + Number(xdibayar) > Number(xsisa) + Number(checksisa)) {
                alertify.warning("Jumlah melebihi sisa")
                return
            }

            if (Number(xlebihbayar) > 0) {


                let x = {
                    ...saveHeaderInvoice
                }
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

        function prosesCheckbox(index) {

            // id="list_proses_checkbox${i}"

            let xdata = listProsesTerimaDPP[index]
            console.log(xdata)
            // console.log(listProsesTerimaDPP[index])
            let xcheck = document.getElementById(`list_proses_checkbox${index}`).checked
            let xsisa = Number($("#input_modal_sisa").val())
            let xdibayar = Number($(`#list_proses_dibayar${index}`).val())
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
                console.log('1', Number(xsisa))
                if (Number(xsisa) > 0) {

                    console.log('2', Number(xdibayar))
                    if (Number(xdibayar) == 0) {
                        if (Number(xdata.TOTFAKTUR) - Number(xdata.SDHBAYAR) < Number(xsisa)) {
                            document.getElementById(`list_proses_dibayar${index}`).value = Number(xdata.TOTFAKTUR) - Number(
                                xdata.SDHBAYAR)
                            listProsesTerimaDPP[index].DIBAYAR = Number(xdata.TOTFAKTUR) - Number(xdata.SDHBAYAR)
                        } else {
                            document.getElementById(`list_proses_dibayar${index}`).value = Number(xsisa)
                            listProsesTerimaDPP[index].DIBAYAR = Number(xsisa)
                        }
                        refreshSisa()
                    }


                }
            } else {
                document.getElementById(`list_proses_dibayar${index}`).value = '0.00'
                document.getElementById(`list_proses_LB${index}`).value = '0.00'
                document.getElementById(`list_proses_KL${index}`).value = '0.00'
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

        function refreshSisa() {
            console.log('refreshSisa')
            let totdibayar = Number($("#input_add_dibayar").val())
            let totlb = 0
            listProsesTerimaDPP.forEach((item, i) => {
                console.log('wwwwwwwwwwwww')
                console.log('listProsesTerimaDPP', listTambahLB[item.NOFAKTUR])
                totdibayar += Number(item.DIBAYAR)

                if (listTambahLB[item.NOFAKTUR]) {
                    totlb += Number(listTambahLB[item.NOFAKTUR].inputLB)
                    document.getElementById(`list_proses_LB${i}`).value = parseFloat(listTambahLB[item.NOFAKTUR]
                        .inputLB).toFixed(2)
                }

                document.getElementById(`list_proses_dibayar${i}`).value = parseFloat(item.DIBAYAR).toFixed(2)
                console.log(totdibayar)
                console.log(totlb)

            });
            let xtot = Number(totdibayar) + Number(totlb)
            let xjumlah = $("#input_modal_jumlah").val()

            console.log(parseFloat(xtot).toFixed(2))
            console.log(parseFloat(Number(xjumlah) - xtot).toFixed(2))
            document.getElementById(`input_modal_dibayar`).value = parseFloat(xtot).toFixed(2)
            document.getElementById(`input_modal_sisa`).value = parseFloat(Number(xjumlah) - xtot).toFixed(2)
            document.getElementById(`input_modalx_sisanotadibayar`).value = parseFloat(Number(xjumlah) - xtot).toFixed(2)



        }

        function buttonAddKL() {

            $('.showhideitemKL').show()

            document.getElementById("input_modalx_kurangbayar").value = '0.00'
            document.getElementById("input_modalx_perkiraankurangbayar").value = ''
            document.getElementById("input_modalx_namaperkiraankurangbayar").value = ''
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
            let xnilainotadibayar = $("#input_modalx_nilainotadibayar").val()
            let xdibayar = $("#input_modalx_dibayar").val()
            if (Number(xnilainotadibayar) + Number(xdibayar) < Number(kl)) {
                alertify.warning('KL melebihi nilai nota + dibayar')

                return
            }


            let xsisa = $("#input_modalx_sisanotadibayar").val()
            if (Number(kl) > Number(xsisa)) {
                alertify.warning('Melebihi sisa nota')
                return
            }
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

        function buttonAddBatalKL() {
            $('.showhideitemKL').hide()
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
            let kode = 'TTD'
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

        function tesConcat() {
            let x = []
            let y = ['a', 'b']
            let z = [1, 2, 3]

            let a = x.concat(y)
            console.log(a)
            a = a.concat(z)
            console.log(a)
        }


        function submitAdd() {

            let _token = $("#_token").val()
            let choice = "I"
            let nobukti = $("#input_add_nobukti").val()
            let nourut = $("#input_add_nourut").val()
            // let valas  = $("#input_add_valas").val()
            let tipe = 'DPP'

            let kodecustsupp = $("#input_add_kodecust").val()
            let checkDate = new Date($("#input_add_tanggal").val())
            let periode_bulan = document.getElementById("periode_bulan").value
            let periode_tahun = document.getElementById("periode_tahun").value
            // let nobkmbbm = $("#input_add_nobkmbbm").val();
            if (checkDate.getFullYear() !== Number(periode_tahun) || (checkDate.getMonth() + 1) !== Number(periode_bulan)) {
                alertify.warning("Tanggal tidak sesuai periode");
                return
            }
            let tanggal = $("#input_add_tanggal").val()
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

            if (!xlisttambah.length) {
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
                    tempData: xlisttambah,
                    tempDataKL: xlisttambahkl,
                    tempDataLB: xlisttambahlb,
                    nobukti,
                    nourut,
                    tipe,
                    tanggal,
                    jmlrecord,
                    kodecustsupp,
                    urutTrans
                },
                success: function(res) {
                    console.log(res, '!')

                    if (res == 1) {
                        alertify.success('DPH telah ditambah');
                        document.getElementById("input_add_tanggal").disabled = true

                        tipeform = 'edit'
                        $("#form").modal('toggle')
                        refreshTableKoreksi(nobukti, nobkmbbm)
                        loadAll()

                    }
                    if (res == 2) {
                        setNewNoBukti()
                        alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
                    }

                },
                error: function(err) {
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

            if (checkDate.getFullYear() !== Number(periode_tahun) || (checkDate.getMonth() + 1) !== Number(periode_bulan)) {
                console.log(checkDate.getFullYear())
                console.log(Number(periode_tahun))
                console.log((checkDate.getMonth() + 1))
                console.log(Number(periode_bulan))
                alertify.warning("Tanggal tidak sesuai periode");
                return
            }


            // let tanggal  = $("#input_modal_tanggal").val()

            if (!listCheckListPengajuan.length) {
                alertify.warning("Tidak ada item dipilih")
            }

            // let jmlrecord = tipeform == "add" ? 0 : 1

            console.log({
                tempData: listCheckListPengajuan,
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
                    tempData: listCheckListPengajuan,
                    choice,
                    valas,
                    nobukti,
                    nourut,
                    tipe,
                    tanggal,
                    jmlrecord
                },
                success: function(res) {
                    console.log(res, '!')

                    if (res == 1) {
                        alertify.success('DPH telah ditambah');

                        tipeform = 'edit'
                        $("#form").modal('toggle')
                        buttonKoreksi(nobukti)

                    }
                    if (res == 2) {
                        setNewNoBukti()
                        alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
                    }

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
            // let transaksi  = $("#input_add_transaksi").val()
            // let note  = $("#input_add_kepadaterima").val()
            // let kodeperkiraan  = $("#input_add_kodeperkiraan").val()
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

        function submitEdit() {






            let choice = "U"
            let _token = $("#_token").val()
            let nobukti = $("#input_add_nobukti").val()
            let nourut = $("#input_add_nourut").val()
            let tanggal = $("#input_add_tanggal").val()

            // let nobkmbbm = $("#input_add_nobkmbbm").val()
            let dibayar = $("#AddAddDibayar").val()
            // let kl  = $("#AddAddKurangBayar").val()
            // let lb = $("#AddAddLebihBayar").val()
            // let perkiraan = $("#AddAddKodePerkiraan").val()

            if (Number(dibayar) > 0 || Number(lb) > 0 || Number(kl) > 0) {

            } else {
                alertify.warning("Nilai <= 0")
                return
            }
            let urut = dataEdit.URUT
            console.log({

                choice,
                _token,
                nobukti,
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
                    nobukti,
                    nourut,
                    dibayar,
                    perkiraan,
                    kl,
                    lb,
                    tanggal,
                    urut
                },
                success: function(res) {
                    console.log(res, '!')

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




        function refreshDataTable(nobukti) {
            console.log('refreshDataTable', nobukti)
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
                    setValIfExists("input_add_valas", listData[0].Valas)










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
                error: function(err) {
                    console.log(err)
                    alertify.warning('Terjadi kesalahan silahkan refresh browser')
                }

            })


        }

















        function buttonAddItem() {
            let _token = $("#_token").val();

            let kodecust = $("#input_add_kodecust").val();
            // let nobkmbbm = $("#input_add_nobkmbbm").val();
            listTambah = []
            listTambahKL = []
            $.ajax({
                url: "{!! url('pelunasanpiutangdppgetlistterimadpp') !!}",
                type: "post",
                async: false,
                data: {
                    _token,
                    nodpp: nobkmbbm,
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
              <input style="height:30px; width:160px" id="list_proses_dibayar${i}" type="number" value='${parseFloat(item.DIBAYAR).toFixed(2)}' class="form-control text-right" disabled>

              <button id="buttonChangeDibayar${i}" style="height:30px; padding: 0px; width: 25px" type="button" onclick="buttonChangeDibayar(${i})" class="btn btn-primary" >+</button>

            </div></td>
            <td class="text-center">
            <input style="height:30px; width:160px" id="list_proses_LB${i}" type="number" value='${parseFloat(item.LB).toFixed(2)}' class="form-control text-right" disabled>
            </td>
            <td class="text-center">
            <input style="height:30px; width:160px" id="list_proses_KL${i}" type="number" value='0.00' class="form-control text-right" disabled>
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
                    document.getElementById("input_modal_jumlah").value = $("#input_add_jumlah").val();
                    document.getElementById("input_modal_dibayar").value = $("#input_add_dibayar").val();
                    document.getElementById("input_modal_sisa").value = $("#input_add_sisa").val();
                    document.getElementById("tabel_data_add_list_modal").innerHTML = rowTable


                    $("#form").modal('toggle')
                },
                error: function(err) {
                    console.log(err)
                    alertify.warning('Terjadi kesalahan silahkan refresh browser')
                }

            })


            // buttonRefreshListPengajuan()


        }


        function refreshTableKL() {

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
          <td class="text-right">${item.inputKL}</td>
          <td>${item.inputPerkiraanKL}</td>
          <td>${item.inputNamaPerkiraanKL}</td>
        </tr>
      `

                });

                document.getElementById("tabel_data_add_list_modalx").innerHTML = rowTablex
                document.getElementById(`list_proses_KL${saveHeaderIndex}`).value = parseFloat(xTempTotalKL).toFixed(2)


            }

        }


        function buttonChangeDibayar(index) {
            // sp_TempTerimaDPP

            let xcheck = document.getElementById(`list_proses_checkbox${index}`).checked
            if (!xcheck) {
                alertify.warning("Pilih invoice terlebih dahulu")
                return
            }
            let x = listProsesTerimaDPP[index]
            console.log(x)
            // listProsesTerimaDPP[saveHeaderIndex]
            saveHeaderInvoice = listProsesTerimaDPP[index]
            saveHeaderIndex = index
            let xdibayar = $(`#list_proses_dibayar${index}`).val();
            let xLB = $(`#list_proses_LB${index}`).val();
            let sisa = $(`#input_modal_sisa`).val();
            console.log(saveHeaderInvoice.NoBukti)
            console.log(listTambahKL[saveHeaderInvoice.NoBukti])

            console.log(x.TOTFAKTUR)
            console.log(formatAngka(parseFloat(x.TOTFAKTUR).toFixed(2)))
            console.log('==')
            console.log(xdibayar, xLB, sisa)
            document.getElementById("input_modalx_nilainotadibayar").value = parseFloat(Number(x.TOTFAKTUR) - Number(x
                .SDHBAYAR)).toFixed(2)
            document.getElementById("input_modalx_dibayar").value = parseFloat(xdibayar).toFixed(2)

            document.getElementById("input_modalx_lebihbayar").value = parseFloat(xLB).toFixed(2)
            document.getElementById("input_modalx_sisanotadibayar").value = parseFloat(sisa).toFixed(2)
            if (xLB > 0) {
                document.getElementById("input_modalx_perkiraanlebihbayar").value = listTambahLB[saveHeaderInvoice.NOFAKTUR]
                    .inputPerkiraanLB
                document.getElementById("input_modalx_namaperkiraanlebihbayar").value = listTambahLB[saveHeaderInvoice
                    .NOFAKTUR].inputNamaPerkiraanLB

            } else {
                document.getElementById("input_modalx_perkiraanlebihbayar").value = ''
                document.getElementById("input_modalx_namaperkiraanlebihbayar").value = ''

            }

            refreshTableKL()


            $('.showhideitemKL').hide()


            $("#formX").modal('toggle')


        }


        function buttonDeleteItem(index) {
            let akses = $("#akses_ishapus").val();
            tipeform = 'edit'
            if (!Number(akses)) {
                alertify.warning('No access')
                return
            }

            let dataEdit = listPenerimaan[index]


            alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus faktur ' + dataEdit.NOFAKTUR + ' ?',
                function() {

                    let choice = "D"
                    let _token = $("#_token").val()
                    let nobukti = $("#input_add_nobukti").val()
                    let nourut = $("#input_add_nourut").val()
                    let tanggal = $("#input_add_tanggal").val()
                    // let nobkmbbm = $("#input_add_nobkmbbm").val()

                    let dibayar = 0
                    let kl = 0
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
                            nobukti,
                            nourut,
                            dibayar,
                            perkiraan,
                            kl,
                            lb,
                            tanggal,
                            urut
                        },
                        success: function(res) {
                            console.log(res, '!')

                            if (res == 1) {
                                // $("#form").modal('toggle')
                                alertify.success('Faktur telah dihapus');
                                loadAll()
                                // buttonCloseForm()
                                tipeform = 'edit'
                                // document.getElementById("buttonAddListCustomer").disabled = true
                                // document.getElementById("input_add_tanggal").disabled = true
                                $('.showhideitem').hide();
                                refreshTableKoreksi(nobukti, nobkmbbm)

                                // $("#form").modal('toggle')

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




        }

        function buttonEditItem(index) {

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
                document.getElementById("AddAddDibayar").value = parseFloat(editDibayar).toFixed(2)

            } else if (Number(editKL) > 0) {
                document.getElementById("AddAddKurangBayar").disabled = false
                document.getElementById("AddAddKurangBayar").value = parseFloat(editKL).toFixed(2)

            } else {
                document.getElementById("AddAddLebihBayar").disabled = false
                document.getElementById("AddAddLebihBayar").value = parseFloat(editLB).toFixed(2)

            }
            $('.showhideitem').show()



        }


        // #addTable/#addTableData dipakai untuk dua bentuk data yang berbeda: daftar
        // invoice outstanding saat "Tambah" (buttonAdd) vs daftar alokasi pembayaran yang
        // sudah tercatat saat "Koreksi" (buttonKoreksi/refreshTableKoreksi). Header harus
        // ikut berganti, bukan statis, supaya jumlah kolom selalu cocok dengan isinya.
        function setAddTableHeadTambah() {
            document.getElementById("addTableHead").innerHTML = `
              <tr>
                <th scope="col">No. Invoice</th>
                <th scope="col">Supplier</th>
                <th scope="col">Valas</th>
                <th scope="col" class="num">Dibayar</th>
                <th scope="col" class="num">KL</th>
              </tr>
            `;
        }

        function setAddTableHeadKoreksi() {
            document.getElementById("addTableHead").innerHTML = `
              <tr>
                <th scope="col">Bank</th>
                <th scope="col">No Faktur</th>
                <th scope="col" class="num">Dibayar</th>
                <th scope="col" class="num">LB</th>
                <th scope="col" class="num">KL</th>
                <th scope="col">Perkiraan</th>
                <th scope="col">Kode Cust</th>
                <th scope="col">Nama Cust</th>
                <th scope="col">Aksi</th>
              </tr>
            `;
        }

        function refreshTableKoreksi(nobukti, nodpp) {
            setAddTableHeadKoreksi();

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
                    if (Number(res.X[0].Dibayar)) {
                        xxx += Number(res.X[0].Dibayar)
                    }
                    if (Number(res.X[0].LB)) {
                        xxx += Number(res.X[0].LB)
                    }

                    // document.getElementById("input_add_nobkmbbm").value = res.header[0].NoDPP
                    document.getElementById("input_add_nobukti").value = res.header[0].NoBukti

                    document.getElementById("input_add_tanggal").value = formatDate(res.header[0].Tanggal, '-')
                    setValIfExists("input_add_kodecust", res.header[0].KODECUSTSUPP)
                    setValIfExists("input_add_namacust", res.header[0].NamaCustSupp)
                    setValIfExists("input_add_valas", res.detail[0].Valas)
                    let dibayarx = parseFloat(xxx).toFixed(2)
                    let jumlahx = res.header[0].Debet ? parseFloat(res.header[0].Debet).toFixed(2) : '0.00'
                    console.log(dibayarx, jumlahx)
                    setValIfExists("input_add_dibayar", parseFloat(xxx).toFixed(2))
                    setValIfExists("input_add_jumlah", res.header[0].Debet ? parseFloat(res.header[0].Debet)
                        .toFixed(2) : '0.00')
                    setValIfExists("input_add_sisa", parseFloat(Number(jumlahx) - Number(xxx)).toFixed(2))
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
              <div class="action-buttons">
                <button class="btn-action-sm btn-action-success" data-toggle="tooltip" title="Edit" type="button" onclick="buttonEditItem('${i}' )"><i class="bi bi-pen-fill"></i></button>
                <button class="btn-action-sm btn-action-danger" data-toggle="tooltip" title="Hapus" type="button" onclick="buttonDeleteItem('${i}' )"><i class="bi bi-trash"></i></button>
              </div>
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
                error: function(err) {
                    console.log(err)
                    alertify.warning('Terjadi kesalahan silahkan refresh browser')
                }

            })
        }


        function buttonDetail(nobukti, tipe = 0, nodpp = '') {
            console.log('buttonDetail')
            console.log(nobukti, tipe, nodpp)
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
                    if (Number(res.X[0].Dibayar)) {
                        xxx += Number(res.X[0].Dibayar)
                    }
                    if (Number(res.X[0].LB)) {
                        xxx += Number(res.X[0].LB)
                    }
                    document.getElementById("input_detail_tanggal").value = formatDate(res.header[0].Tanggal,
                        '-')
                    document.getElementById("input_detail_kodecust").value = res.header[0].KODECUSTSUPP
                    document.getElementById("input_detail_namacust").value = res.header[0].NamaCustSupp
                    document.getElementById("input_detail_valas").value = res.detail[0].Valas
                    let dibayarx = parseFloat(xxx).toFixed(2)
                    let jumlahx = res.header[0].Debet ? parseFloat(res.header[0].Debet).toFixed(2) : '0.00'
                    console.log(dibayarx, jumlahx)
                    document.getElementById("input_detail_dibayar").value = parseFloat(xxx).toFixed(2)
                    document.getElementById("input_detail_jumlah").value = res.header[0].Debet ? parseFloat(res
                        .header[0].Debet).toFixed(2) : '0.00'
                    document.getElementById("input_detail_sisa").value = parseFloat(Number(jumlahx) - Number(
                        xxx)).toFixed(2)

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
                error: function(err) {
                    console.log(err)
                    alertify.warning('Terjadi kesalahan silahkan refresh browser')
                }

            })

        }


        function buttonKoreksi(nobukti, nodpp) {
            resRefresh = 0
            let akses = $("#akses_iskoreksi").val();
            tipeform = 'edit'
            if (!Number(akses)) {
                alertify.warning('No access')
                return
            }
            setAddTableHeadKoreksi();


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

                    if (res.detail[0].IsOtorisasi1 == 1) {
                        alertify.warning("Data sudah diotorisasi")
                        return
                    }
                    // document.getElementById("input_add_nobkmbbm").value = res.header[0].NoDPP
                    document.getElementById("input_add_nobukti").value = res.header[0].NoBukti
                    let xxx = 0
                    if (Number(res.X[0].Dibayar)) {
                        xxx += Number(res.X[0].Dibayar)
                    }
                    if (Number(res.X[0].LB)) {
                        xxx += Number(res.X[0].LB)
                    }
                    document.getElementById("input_add_tanggal").value = formatDate(res.header[0].Tanggal, '-')
                    setValIfExists("input_add_kodecust", res.header[0].KODECUSTSUPP)
                    setValIfExists("input_add_namacust", res.header[0].NamaCustSupp)
                    setValIfExists("input_add_valas", res.detail[0].Valas)
                    let dibayarx = parseFloat(xxx).toFixed(2)
                    let jumlahx = res.header[0].Debet ? parseFloat(res.header[0].Debet).toFixed(2) : '0.00'
                    console.log(dibayarx, jumlahx)
                    setValIfExists("input_add_dibayar", parseFloat(xxx).toFixed(2))
                    setValIfExists("input_add_jumlah", res.header[0].Debet ? parseFloat(res.header[0].Debet)
                        .toFixed(2) : '0.00')
                    setValIfExists("input_add_sisa", parseFloat(Number(jumlahx) - Number(xxx)).toFixed(2))
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
                <div class="action-buttons">
                  <button class="btn-action-sm btn-action-success" data-toggle="tooltip" title="Edit" type="button" onclick="buttonEditItem('${i}' )"><i class="bi bi-pen"></i></button>
                  <button class="btn-action-sm btn-action-danger" data-toggle="tooltip" title="Hapus" type="button" onclick="buttonDeleteItem('${i}' )"><i class="bi bi-trash"></i></button>
                </div>
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
                error: function(err) {
                    console.log(err)
                    alertify.warning('Terjadi kesalahan silahkan refresh browser')
                }

            })

        }


        function buttonAdd(nobukti) {
            let akses = $("#akses_istambah").val();
            console.log(nobukti)
            if (!Number(akses)) {
                alertify.warning('No access')
                return
            }
            setAddTableHeadTambah();
            tipeform = 'add'
            setNewNoBukti()
            document.getElementById("input_add_tanggal").disabled = false
            document.getElementById("input_add_tanggal").valueAsDate = new Date()

            let _token = $("#_token").val();

            $.ajax({
                url: "{!! url('cetakpengajuandphdetailoutstanding') !!}",
                type: "post",
                async: false,
                data: {
                    _token,
                    nobukti

                },
                success: function(res) {
                    console.log(res, '!!!')

                    listOutstanding = res
                    let rowTable = ""


                    listOutstanding.forEach((item, i) => {
                        rowTable += `
      <tr>
        <td>${item.noinvoice }</td>

        <td>${item.namacustsupp }</td>


        <td>${item.valas }</td>
        <td >${formatAngka(parseFloat(item.dibayar).toFixed(2))}</td>
        <td >${formatAngka(parseFloat(item.kl).toFixed(2))}</td>



      </tr>
        `




                    });






                    // let xxx = 0
                    // if ( Number(listOutstanding.dibayar) ) {
                    //   xxx += Number(listOutstanding.dibayar)
                    // }
                    // if ( Number(listOutstanding.kl)) {
                    //   xxx += Number(listOutstanding.kl)
                    // }

                    document.getElementById("addTableData").innerHTML = rowTable








                    $(".showhideitem").hide()
                    $(".mainpage").hide()
                    $("#page2").show()
                },
                error: function(err) {
                    console.log(err)
                    alertify.warning('Terjadi kesalahan silahkan refresh browser')
                }

            })





            return

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

        }

        function loadAll() {

            console.log('loadall')

            document.getElementById('tabel_data').innerHTML =
                '<tr><td colspan="20" class="text-center">' + loadingHtml('Memuat data...') + '</td></tr>';
            document.getElementById('tabel2_data').innerHTML =
                '<tr><td colspan="20" class="text-center">' + loadingHtml('Memuat data...') + '</td></tr>';

            $.ajax({
                url: "{!! url('cetakpengajuandphloadall') !!}",
                type: "get",
                async: true,
                data: {},
                success: function(res) {
                    lastRowsOut = (res.tempOutstanding || []).map(g => g[0]);
                    lastRowsPenerimaan = (res.tempPenerimaan || []).map(g => g[0]);
                    renderTabelOut();
                    renderTabelPenerimaan();
                },
                error: function(err) {
                    console.log(err)
                    alertify.warning('Gagal memuat data terbaru, menampilkan data terakhir')
                }
            })

        }

        function submitPrint(nobukti) {
            // for (var i = 0; i < 30; i++) {
            //   dataPrint.push(dataPrint[0])
            // }
            let _token = $('#_token').val()
            $.ajax({
                url: "{!! url('cetakpengajuandphdetailCetak') !!}",
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
                REKAP DPH
              </td>
            </tr>
              </tr>
                  <tr>
                    <td rowspan="2" class="text-center" style="width: 1%">No.</td>
                    <td colspan="2" class="text-center" style="width: 10%">DPH</td>
                    <td rowspan="2" class="text-center" style="width: 20%">SUPPLIER</td>
                    <td rowspan="2" class="text-center" style="width: 20%">JUMLAH</td>
                    <td rowspan="2" class="text-center" style="width: 10%">BANK</td>
                    <td rowspan="2" class="text-center" style="width: 10%">NO ACC</td>
                    <td rowspan="2" class="text-center" style="width: 30%">KETERANGAN</td>
                  </tr>
                  <tr>
                    <td class="text-center">TGL</td>
                    <td class="text-center">NO</td>
                  </tr>
                </thead> `;

            let z = 0
            let maxRow = 8;
            let tempPrintStr = ``
            // buat hitung grandtotal
            let grandTotalJumlah = 0;

            dataPrint.forEach(item => {

                if (item.NILAINOTA) {
                    grandTotalJumlah += Number(item.NILAINOTA) || 0;
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
               style="width: 10%;  ">${itemSub.Tanggal ? itemSub.Tanggal.split(' ')[0] : ''}</td>
         <td class="text-align: left"
               style="width: 10%;  ">${itemSub.NoBukti ?? ''}</td>
         <td class="text-align: left"
               style="width: 20%;  ">${itemSub.NamaCustSupp ?? ''}</td>
         <td style="width: 20%; text-align: right;">
            ${itemSub.NILAINOTA
              ? Number(itemSub.NILAINOTA).toLocaleString('id-ID', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                })
              : ''}
          </td>
          <td class="text-align: left"
               style="width: 10%;  ">${itemSub.bank ?? ''}</td>
          <td class="text-align: left"
               style="width: 10%;  ">${itemSub.NoAcc ?? ''}</td>
          <td class="text-align: left"
               style="width: 30%;  ">${itemSub.ATN ?? ''}</td>
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
          <td colspan="3" style="border:1px solid; padding:5px; font-weight:bold;">
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
          <td colspan="5" style="border:1px solid;"></td>
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
          <div style="width:50%;">
          <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: 20px; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 20%">Diajukan Oleh</td>
               <td class="no-border text-center" style="width: 20%">Disetujui Oleh</td>
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

          <!-- KANAN -->
          <div style="width:50%;">
          <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: 20px; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 20%">Dijalankan</td>
               <td class="no-border text-center" style="width: 20%"></td>
             </tr>
             <tr style="height: 2.5rem">
               <td class="no-border">&nbsp;</td>
             </tr>

             <tr>
               <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
               </td>
               <td class="no-border px-2">
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
                        error: function(err) {
                            console.log(err)
                            alertify.warning('Terjadi kesalahan silahkan refresh browser')
                        }

                    })
                },
                function() {
                    console.log('no')
                });

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


        function formatAngkaX(angka) {
            if (!angka) {
                return '0.00'
            } else {
                return formatAngka(parseFloat(angka).toFixed(2))
            }

        }

        function formatAngka(angkaString) {

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
    </script>

    {{-- Warna/active state tab sekarang murni CSS (.tab-toggle-btn.active) — Bootstrap
     4's data-toggle="tab" sendiri yang menambah/menghapus class active saat tab
     diklik, jadi script manual setActiveTab() di atas tidak diperlukan lagi. --}}
@endsection
