<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
        href="https://fonts.googleapis.com/css?family=Lato:300,400,400i,700|Poppins:300,400,500,600,700|PT+Serif:400,400i&display=swap"
        rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="{!! URL::asset('css/semantic.css') !!}" />
    <link rel="stylesheet" href="{!! URL::asset('css/select2.min.css') !!}" />
    <link rel="stylesheet" href="{!! URL::asset('css/datatables.min.css') !!}" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.css" />
    <link rel="stylesheet" href="{!! URL::asset('css/jquery-ui.min.css') !!}" />

    <link rel="stylesheet" href="{!! URL::asset('css/canvas/bootstrap.css') !!}" />
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/style.css') !!}" />
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/dark.css') !!}" />
    <link href="https://unpkg.com/gridjs/dist/theme/mermaid.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/font-icons.css') !!}" />
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/animate.css') !!}" />
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/magnific-popup.css') !!}" />
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/custom.css') !!}" />
    <link rel="stylesheet" href="{!! URL::asset('css/alertify.css') !!}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css" />

    <link rel="stylesheet" href="{!! URL::asset('css/style.css') !!}" />
    <link rel="stylesheet"
        href="{!! URL::asset('css/newmaster.css') !!}?v={{ @filemtime(base_path('public/css/newmaster.css')) ?: '1' }}">
    <link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">

    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>@yield('title')</title>
    @yield('css')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        :root {
            --sidebar-col: 64px;
            --sidebar-exp: 240px;
            --hdr: 52px;
            --blue: #1a73e8;
            --sidebar-bg: #1e2a3a;
            --sidebar-hover: #2a3a50;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --bg: #f3f4f6;
            --white: #fff;
            --border: #e5e7eb;
            --radius: 12px;
        }

        body {
            font-family: "Segoe UI", system-ui, sans-serif;
            background: var(--bg);
            color: var(--text-main);
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .nav-subchildren {
            padding-left: 20px;
        }

        .nav-subchild {
            padding: 6px 16px 6px 60px;
            font-size: 12px;
            color: rgba(255, 255, 255, .5);
            cursor: pointer;
        }

        .nav-subchild:hover {
            color: white;
            background: rgba(255, 255, 255, .05);
        }

        /* -- SIDEBAR -- */
        .sidebar {
            width: var(--sidebar-col);
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            transition: width 0.22s ease;
            overflow: hidden;
            z-index: 100;
            flex-shrink: 0;
        }

        .sidebar:hover {
            width: var(--sidebar-exp);
        }

        .sidebar-logo {
            height: var(--hdr);
            display: flex;
            align-items: center;
            padding: 0 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            gap: 10px;
            flex-shrink: 0;
        }

        .logout-link {
          color: #e3342f; font-size: 13px; font-weight: 600;
          display: flex; align-items: center; gap: 4px;
          text-decoration: none;
        }
        .logout-link:hover { opacity: 0.75; }

        .logo-icon {
            width: 32px;
            height: 32px;
            background: var(--blue);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: white;
            font-size: 14px;
            flex-shrink: 0;
        }

        .logo-text {
            color: white;
            font-weight: 700;
            font-size: 15px;
            white-space: nowrap;
            opacity: 0;
            transition: opacity 0.18s;
        }

        .sidebar:hover .logo-text {
            opacity: 1;
        }

        .sidebar-nav {
            flex: 1;
            padding: 8px 0;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 3px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.12);
            border-radius: 2px;
        }

        /* Parent nav item */
        .nav-group {
            position: relative;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 11px 16px;
            gap: 12px;
            cursor: pointer;
            border-left: 3px solid transparent;
            transition:
                background 0.15s,
                border-color 0.15s;
            white-space: nowrap;
        }

        .nav-item:hover {
            background: var(--sidebar-hover);
        }

        .nav-group.active>.nav-item {
            background: rgba(26, 115, 232, 0.18);
            border-left-color: var(--blue);
        }

        .nav-icon {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            opacity: 0.7;
            color: white;
        }

        .nav-group.active>.nav-item .nav-icon {
            opacity: 1;
            color: #60a5fa;
        }

        .nav-label {
            color: rgba(255, 255, 255, 0.75);
            font-size: 13px;
            font-weight: 500;
            opacity: 0;
            transition: opacity 0.18s;
            flex: 1;
        }

        .sidebar:hover .nav-label {
            opacity: 1;
        }

        .nav-group.active>.nav-item .nav-label {
            color: #fff;
        }

        .nav-chevron {
            width: 14px;
            height: 14px;
            color: rgba(255, 255, 255, 0.35);
            opacity: 0;
            transition:
                opacity 0.18s,
                transform 0.2s;
            flex-shrink: 0;
        }

        .sidebar:hover .nav-chevron {
            opacity: 1;
        }

        /* newmaster.css (loaded above) still has the CSS-hover accordion this project
           deliberately abandoned — .nav-group:hover>.nav-item .nav-chevron and
           .nav-group:hover .nav-children (moving the cursor down the expanded rail
           flashes every folder open in turn). Its hover selectors have HIGHER
           specificity than a plain override (:hover counts as a class), so
           !important is required here, not just later-in-source-wins. Direct-child
           selectors so nesting can't leak .open state. Same pattern as
           gudang/newmasterx.blade.php. */
        .nav-group>.nav-item .nav-chevron {
            transform: none !important;
        }

        .nav-group.open>.nav-item .nav-chevron {
            transform: rotate(90deg) !important;
            color: rgba(255, 255, 255, 0.6);
        }

        /* Children */
        .nav-children {
            overflow: hidden;
            transition: max-height 0.25s ease;
            background: rgba(0, 0, 0, 0.15);
        }

        /* Blanket-suppress first (kills newmaster.css's .nav-group:hover .nav-children
           600px rule via !important, regardless of source order), then re-open only
           for a genuinely .open group with the sidebar actually expanded — higher
           specificity (4 simple selectors) than the blanket rule (1) so it wins over
           it despite both being !important. */
        .nav-children {
            max-height: 0 !important;
        }

        .sidebar:hover .nav-group.open>.nav-children {
            max-height: 600px !important;
        }

        .nav-child {
            display: flex;
            align-items: center;
            padding: 8px 16px 8px 48px;
            gap: 8px;
            cursor: pointer;
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.6);
            transition:
                background 0.12s,
                color 0.12s;
            white-space: nowrap;
        }

        .nav-child:hover {
            background: rgba(255, 255, 255, 0.06);
            color: #fff;
        }

        .nav-child.active-child {
            color: #93c5fd;
            background: rgba(26, 115, 232, 0.12);
        }

        .nav-child::before {
            content: "";
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: currentColor;
            flex-shrink: 0;
            opacity: 0.6;
        }

        /* -- MAIN -- */
        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .header {
            height: var(--hdr);
            background: var(--white);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 24px;
            gap: 16px;
            flex-shrink: 0;
        }

        .breadcrumb {
            font-size: 13px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .breadcrumb b {
            color: var(--text-main);
            font-weight: 600;
        }

        .bc-sep {
            opacity: 0.4;
        }

        .header-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .period-badge {
            background: #eff6ff;
            color: var(--blue);
            font-size: 12px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            border: 1px solid #bfdbfe;
        }

        .avatar {
            width: 32px;
            height: 32px;
            background: var(--blue);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 13px;
            font-weight: 700;
        }

        /* -- CONTENT -- */
        .content {
            flex: 1;
            overflow-y: auto;
            padding: 28px 32px;
        }

        .page-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .page-subtitle {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 28px;
        }

        /* Card grid (module home) */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 16px;
        }

        .card {
            position: relative;
            background: var(--white);
            border-radius: var(--radius);
            padding: 24px 16px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 14px;
            cursor: pointer;
            border: 1.5px solid var(--border);
            transition:
                transform 0.15s,
                box-shadow 0.15s,
                border-color 0.15s;
            text-align: center;
        }

        .card-arrow {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 14px;
            height: 14px;
            opacity: 0.4;
            color: var(--text-muted);
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.09);
            border-color: transparent;
        }

        .card-icon-wrap {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-icon-wrap svg {
            width: 28px;
            height: 28px;
        }

        .card-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-main);
            line-height: 1.3;
        }

        /* colour themes */
        .c-blue {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .c-green {
            background: #f0fdf4;
            color: #15803d;
        }

        .c-orange {
            background: #fff7ed;
            color: #c2410c;
        }

        .c-purple {
            background: #f5f3ff;
            color: #6d28d9;
        }

        .c-teal {
            background: #f0fdfa;
            color: #0f766e;
        }

        .c-pink {
            background: #fdf2f8;
            color: #9d174d;
        }

        .c-yellow {
            background: #fefce8;
            color: #a16207;
        }

        .c-red {
            background: #fef2f2;
            color: #b91c1c;
        }

        .c-indigo {
            background: #eef2ff;
            color: #3730a3;
        }

        .c-cyan {
            background: #ecfeff;
            color: #0e7490;
        }

        /* -- SUB-PAGE CONTENT -- */
        .subpage {
            display: none;
            flex-direction: column;
            gap: 20px;
        }

        .subpage.visible {
            display: flex;
        }

        .toolbar {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: opacity 0.15s;
        }

        .btn:hover {
            opacity: 0.85;
        }

        .btn-primary {
            background: var(--blue);
            color: white;
            border: 1.5px solid #60a5fa;
        }

        .btn-outline {
            background: white;
            color: var(--text-main);
            border: 1.5px solid var(--border);
        }

        .btn-danger {
            background: #fef2f2;
            color: #b91c1c;
            border: 1.5px solid #fecaca;
        }

        .data-table-wrap {
            background: white;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            overflow: hidden;
        }

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
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-bottom: 1px solid var(--border);
        }

        .data-table tbody td {
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
            color: var(--text-main);
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        .data-table tbody tr:hover td {
            background: #f9fafb;
        }

        .badge {
            display: inline-block;
            padding: 2px 9px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
        }

        .badge-green {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-blue {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-gray {
            background: #f3f4f6;
            color: #6b7280;
        }

        .badge-orange {
            background: #fef3c7;
            color: #b45309;
        }

        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 14px;
            margin-bottom: 4px;
        }

        .summary-card {
            background: white;
            border-radius: var(--radius);
            padding: 18px 20px;
            border: 1px solid var(--border);
        }

        .summary-card .sc-label {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 6px;
            font-weight: 500;
        }

        .summary-card .sc-value {
            font-size: 20px;
            font-weight: 700;
        }

        .summary-card .sc-sub {
            font-size: 11.5px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        .grid-view {
            display: none;
        }

        .grid-view.visible {
            display: block;
        }

        .list-view {
            display: none;
        }

        .list-view.visible {
            display: block;
        }
    </style>

    {{-- The rest of these style blocks come from newmaster.blade.php's sidebar --
         they back the hover-flyout submenus and the sidebar-footer Report
         drill-down page that the dynamic sidebar (below) renders. --}}
    <style>

      .report-card-has-sub {
      position: relative;
      padding-right: 34px;
      }

      .report-card-arrow {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 14px;
        height: 14px;
        opacity: 0.5;
      }
        .hover-tooltip {
        position: relative;
      }

      .hover-tooltip::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 125%;
        left: 50%;
        transform: translateX(-50%);
        background-color: black;
        color: white;
        padding: 6px 8px;
        border-radius: 4px;
        font-size: 12px;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s, visibility 0.3s;
        z-index: 1000;
        pointer-events: none;
      }

      .hover-tooltip::before {
        content: '';
        position: absolute;
        bottom: 115%;
        left: 50%;
        transform: translateX(-50%);
        border: 5px solid transparent;
        border-top-color: black;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s, visibility 0.3s;
        z-index: 1000;
        pointer-events: none;
      }

      .hover-tooltip:hover::after,
      .hover-tooltip:hover::before {
        opacity: 1;
        visibility: visible;
      }

      .bi-plus {
        font-size: 1.5rem;
        line-height: 0;
        vertical-align: middle;
        }
      </style>

    <style>

      .sidebar-footer {
        margin-top: auto;
        border-top: 1px solid rgba(255,255,255,0.08);
        padding-top: 4px;
      }

      .nav-report-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        cursor: pointer;
        color: rgba(255,255,255,0.85);
        transition: background 0.12s ease;
      }

      .nav-report-item:hover,
      .nav-report-item.active {
        background: rgba(255,255,255,0.08);
      }

      .nav-report-item .nav-icon {
        display: flex;
        width: 18px;
        height: 18px;
      }

      /* Report page layout (prototype) */
      .report-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: none;
        border: none;
        color: #ff0000;
        font-size: 14px;
        cursor: pointer;
        margin-bottom: 16px;
        padding: 0;
        transition: color 0.4s ease
      }

      .report-back-btn:hover {
        color: #730202;
      }

      .report-back-btn svg {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
      }

      .report-category {
        margin-bottom: 28px;
      }

      .report-category-title {
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #888;
        margin-bottom: 10px;
      }

      .report-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 14px;
      }

      .report-card {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #fff;
        border: 1px solid rgba(0,0,0,0.08);
        border-radius: 8px;
        padding: 14px;
        cursor: pointer;
        transition: box-shadow 0.12s ease, transform 0.12s ease;
      }

      .report-card:hover {
        box-shadow: 0 4px 14px rgba(0,0,0,0.08);
        transform: translateY(-1px);
      }

      .report-card-icon {
        width: 34px;
        height: 34px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        background: rgba(0,0,0,0.04);
      }

      .report-card-label {
        font-size: 14px;
        font-weight: 500;
        color: #222;
      }

        #sidebar.flyout-pinned {
          width: var(--sidebar-exp, 240px) !important;
        }
        #sidebar.flyout-pinned .logo-text,
        #sidebar.flyout-pinned .nav-label,
        #sidebar.flyout-pinned .nav-chevron {
          opacity: 1 !important;
        }

        #sidebar.flyout-pinned .nav-group.flyout-owner > .nav-children {
          max-height: 600px !important;
        }

        #sidebar.flyout-pinned .nav-group.flyout-owner > .nav-item .nav-chevron {
          transform: rotate(90deg) !important;
          color: rgba(255, 255, 255, 0.6);
        }

        .nav-child.has-sub {
          justify-content: flex-start; /* was space-between */
        }

        .nav-child.has-sub .nav-child-label {
          flex: 1;
          text-align: left;
        }

        .nav-child.has-sub .nav-child-arrow {
          margin-left: auto;
        }

        .nav-child-arrow {
          width: 14px;
          height: 14px;
          flex-shrink: 0;
          opacity: 0.6;
          transition: transform 0.15s ease, opacity 0.15s ease;
        }

        .nav-child.has-sub:hover .nav-child-arrow {
          opacity: 1;
        }

        .nav-flyout {
          position: fixed !important;
          min-width: 200px;
          max-width: 280px;
          background: #fff;
          border: 1px solid rgba(0, 0, 0, 0.08);
          border-radius: 8px;
          box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
          padding: 6px;
          z-index: 9999 !important;
          margin: 0;

          /* hidden by default; JS toggles a .flyout-visible class on hover */
          opacity: 0;
          visibility: hidden;
          transform: translateX(-4px);
          transition: opacity 0.12s ease, transform 0.12s ease, visibility 0.12s ease;
          pointer-events: none;
        }

        .nav-flyout.flyout-visible {
          opacity: 1;
          visibility: visible;
          transform: translateX(0);
          pointer-events: auto;
        }

        .nav-flyout-item {
          padding: 8px 12px;
          border-radius: 6px;
          font-size: 13px;
          white-space: nowrap;
          color: #333;
          cursor: pointer;
          transition: background 0.12s ease;
        }

        .nav-flyout-item:hover {
          background: rgba(0, 0, 0, 0.06);
        }
      </style>
</head>

<body>
    {{-- SIDEBAR — ported from newmaster.blade.php: AJAX-loaded menu tree,
         hover flyouts, module-home card grid and the Report drill-down page
         (see the boot script near the end of this file). --}}
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-logo" onclick="goHome()" style="cursor:pointer;">
        {{-- Ikon sidebar memakai Logo NPWP 1 dari menu Set Nomor Transaksi
             (resources/views/berkas/img/logoperusahaan/npwp1.*). Belum ada logo = kotak "SPL" lama. --}}
        @php
          $fileLogoSidebar = collect(glob(resource_path('views/berkas/img/logoperusahaan/npwp1.*')) ?: [])->first();
        @endphp
        @if ($fileLogoSidebar)
          <div class="logo-icon" style="background:#fff; overflow:hidden;">
            <img src="{{ url('setnomortransaksilogo') }}?v={{ @filemtime($fileLogoSidebar) }}" alt="Logo" style="width:100%; height:100%; object-fit:contain;">
          </div>
        @else
          <div class="logo-icon">SPL</div>
        @endif
        {{-- Nama di sidebar mengikuti Inisial Perusahaan di menu Set Nomor Transaksi (DBNOMOR.ALIAS),
             bukan hardcode lagi. Kalau query gagal, tetap tampil teks lama supaya layout tidak error. --}}
        @php
          try {
            $inisialPerusahaan = trim((string) (\DB::connection('SML')->select('select top 1 ALIAS from DBNOMOR')[0]->ALIAS ?? ''));
          } catch (\Throwable $e) {
            $inisialPerusahaan = '';
          }
        @endphp
        <span class="logo-text">{{ $inisialPerusahaan !== '' ? $inisialPerusahaan : 'PT. SPL' }}</span>
      </div>
      <nav class="sidebar-nav" id="nav"></nav>

      <div class="sidebar-footer" id="sidebar-footer">
        <!-- filled in by JS, see renderSidebarFooter() below -->
      </div>
    </aside>

    <!-- MAIN -->
    <div class="main">
        <!-- PAGE1 -->
        <header class="header">
            <div class="header-left">
                <div id='pageTitleBreadcrumb'class="page-title">
                    @yield('page-title')
                </div>
                <div class="breadcrumb" id="breadcrumb"><span>Beranda</span></div>
            </div>
            <div class="header-right">
                <div class="period-badge">
                  Username: {{ Auth::user()->username }}
          &nbsp;–&nbsp;
                    Periode:
                    {{ [
                        1 => 'Januari',
                        2 => 'Februari',
                        3 => 'Maret',
                        4 => 'April',
                        5 => 'Mei',
                        6 => 'Juni',
                        7 => 'Juli',
                        8 => 'Agustus',
                        9 => 'September',
                        10 => 'Oktober',
                        11 => 'November',
                        12 => 'Desember',
                    ][$periode->bulan] ?? '' }}
                    {!! $periode->tahun !!}</div>
                <div id="avatar" class="avatar">{{ \Auth::user()->username[0] }}</div>
                <a class="logout-link" href="{{ route('logout') }}">
                  <i class="bi bi-power"></i> Log Out
                </a>
            </div>
        </header>
        <div class="content" id="content">
          <div id="content-dynamic" style="display:none;"></div>
          <div id="content-report" style="display:none;"></div>
          <div id="content-blade">@yield('content')</div>
        </div>
    </div>

    <script src="{!! URL::asset('js/canvas/jquery.js') !!}"></script>
    <script src="{!! URL::asset('js/jquery.min.js') !!}"></script>
    <script src="{!! URL::asset('js/jquery-3.3.1.min.js') !!}"></script>
    <script src="{!! URL::asset('js/select2.min.js') !!}"></script>
    <script src="{!! URL::asset('js/popper.min.js') !!}"></script>
    <script src="{!! URL::asset('js/bootstrap.min.js') !!}"></script>
    <script src="{!! URL::asset('js/alertify.js') !!}"></script>
    <script src="{!! URL::asset('js/autoNumeric.js') !!}"></script>
    <script src="{!! URL::asset('js/datatables.min.js') !!}"></script>
    <script src="{!! URL::asset('js/jquery-ui.min.js') !!}"></script>
    <script src="{!! URL::asset('js/qrcode.min.js') !!}"></script>

    <!-- Footer Scripts
 ============================================= -->
    <script src="{!! URL::asset('js/canvas/functions.js') !!}"></script>
    <script src="{!! URL::asset('js/canvas/JsBarcode.all.min.js') !!}"></script>

    <script type="text/javascript">
        document.onkeydown = function(e) {
            if (event.keyCode == 123) {
                return false;
            }
            if (e.ctrlKey && e.shiftKey && e.keyCode == "I".charCodeAt(0)) {
                return false;
            }
            if (e.ctrlKey && e.shiftKey && e.keyCode == "J".charCodeAt(0)) {
                return false;
            }
            if (e.ctrlKey && e.keyCode == "U".charCodeAt(0)) {
                return false;
            }
        };
        $("button").addClass("btn-sm");
        $(".form-control").addClass("form-control-sm");
        $(document).on("hidden.bs.modal", ".modal", function() {
            $(".modal:visible").length && $(document.body).addClass("modal-open");
        });
        $(".modal").modal({
            show: false,
            keyboard: false,
            backdrop: "static"
        });
        $("title").html($("#title_page").html());
        $(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
        $("[rel='tooltip']").tooltip();

        function numberWithCommas(n) {
            var parts = n.toString().split(".");
            return (
                parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",") +
                (parts[1] ? "." + parts[1] : "")
            );
        }

        function toInteger(n) {
            return parseInt(n.replace(/,/g, ""));
        }

        function toFloat(n) {
            return parseFloat(n.replace(/,/g, ""));
        }

        function middleTD() {}
    </script>
    <script src="https://unpkg.com/gridjs/dist/gridjs.umd.js"></script>

    {{-- Sidebar/card icon dictionary (icons object + icon() helper) — shared with
         gudang/newmasterx.blade.php, report/newmaster2x.blade.php, etc. Used to be an
         inline copy here (frozen at ~25 keys, missing 'arrow-left' among others, which
         is why DB-driven icons and the Report/module-home back button silently fell
         back to a generic box icon). Loaded from the shared file instead so all layouts
         stay in sync. --}}
    <script src="{!! URL::asset('js/sidebar-icons.js') !!}?v={{ @filemtime(public_path('js/sidebar-icons.js')) ?: '1' }}"></script>

    {{-- Sidebar behavior — ported from newmaster.blade.php. Builds the nav
         tree via AJAX (/getmenu/1), renders hover flyouts for L2 submenus,
         and drives the module-home card grid + Report drill-down page that
         live in #content-dynamic / #content-report above. Defines round(),
         format_date() and format_timestamp() too — these intentionally
         override the simpler versions in the script block above so the
         module-home/report views format consistently with the rest of
         newmaster's sidebar. --}}
    <script>

  function round(value, precision) {
    const multiplier = Math.pow(10, precision || 0);
    return Math.round(value * multiplier) / multiplier;
  }

  function format_date(date) {
    if (!date) return '';
    const [y, m, d] = date.split('-');
    return `${d}/${m}/${y}`;
  }

  function formatNumber(input) {
      let value = input.value.replace(/[^\d.]/g, '');
      let parts = value.split('.');
      parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
      input.value = parts.join('.');
  }

  function formatNumberDisplay(value) {
      if (value === null || value === undefined || value === '') return '';
      let parts = String(value).replace(/[^\d.]/g, '').split('.');
      parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
      return parts.join('.');
  }

  function format_timestamp(date) {
    if (!date) return '';
    const [tgl, waktu] = date.split(' ');
    const [y, m, d] = tgl.split('-');
    return `${d}/${m}/${y} ${waktu}`;
  }

  // ── Icon SVGs ────────────────────────────────────────────────────────
  // The `icons` object + `icon()` helper used to be defined inline here (frozen at
  // ~25 keys, missing 'arrow-left' among others). Now loaded from the shared
  // public/js/sidebar-icons.js file (see the <script> tag added above this block),
  // same file gudang/newmasterx.blade.php and report/newmaster2x.blade.php use, so DB
  // `icon` values and the Report/module-home back button render consistently across
  // layouts and stay in sync going forward.

  // ── Color palette cycling for cards ─────────────────────────────────
  const cardColors = ['c-blue','c-green','c-orange','c-purple','c-teal','c-pink','c-yellow','c-red','c-indigo','c-cyan'];

  // ── Per-child icon mapping (by partial label keyword) ────────────────
  const childIconMap = [
    ['valas',         'dollar'],
    ['devisi',        'layers'],
    ['perkiraan',     'layers'],
    ['aktiva',        'package'],
    ['hutang',        'credit-card'],
    ['piutang',       'credit-card'],
    ['giro',          'repeat'],
    ['laba',          'trending-up'],
    ['neraca',        'bar-chart'],
    ['costing',       'sliders'],
    ['posting',       'send'],
    ['supplier',      'truck'],
    ['gudang',        'warehouse'],
    ['group',         'grid'],
    ['merk',          'tag'],
    ['bahan',         'package'],
    ['barang',        'box'],
    ['jasa',          'clipboard'],
    ['lokasi',        'map-pin'],
    ['satuan',        'sliders'],
    ['area',          'map-pin'],
    ['kota',          'map-pin'],
    ['customer',      'users'],
    ['sales',         'trending-up'],
    ['expedisi',      'truck'],
    ['departemen',    'grid'],
    ['jabatan',       'layers'],
    ['karyawan',      'users'],
    ['biaya',         'dollar'],
    ['pajak',         'percent'],
    ['kendaraan',     'truck'],
    ['sopir',         'truck'],
    ['periode',       'settings'],
    ['kunci',         'lock'],
    ['nomor',         'settings'],
    ['pemakai',       'users'],
    ['password',      'lock'],
    ['kalkulator',    'sliders'],
    ['log',           'file-text'],
    ['jurnal',        'file-text'],
    ['kas',           'dollar'],
    ['bank',          'credit-card'],
    ['bon',           'clipboard'],
    ['memorial',      'file-text'],
    ['koreksi',       'rotate-ccw'],
    ['pelunasan',     'check-square'],
    ['permintaan',    'clipboard'],
    ['penerimaan',    'package'],
    ['inspeksi',      'check-square'],
    ['invoice',       'file-text'],
    ['retur',         'rotate-ccw'],
    ['debet',         'dollar'],
    ['penawaran',     'tag'],
    ['verifikasi',    'check-square'],
    ['uang muka',     'dollar'],
    ['surat jalan',   'send'],
    ['closing',       'lock'],
    ['performance',   'trending-up'],
    ['opname',        'check-square'],
    ['transfer',      'repeat'],
    ['sample',        'package'],
    ['konsinyasi',    'package'],
    ['kasir',         'dollar'],
    ['laporan',       'bar-chart'],
    ['dashboard',     'bar-chart'],
    ['hitung',        'sliders'],
    ['proses',        'zap'],
    ['aktivitas',     'file-text'],
    ['cascade',       'layers'],
    ['tile',          'grid'],
    ['arrange',       'grid'],
    ['po',            'clipboard'],
    ['so',            'clipboard'],
    ['faktur',        'file-text'],
    ['nota',          'file-text'],
    ['kredit',        'credit-card'],
    ['pemakaian',     'package'],
    ['informasi',     'layers'],
    ['cetak',         'printer'],
  ];

  function getChildIcon(label, dbIcon) {
    if (dbIcon && icons[dbIcon]) return dbIcon;
    const l = (label || '').toLowerCase();
    for (const [kw, ic] of childIconMap) {
      if (l.includes(kw)) return ic;
    }
    return 'box';
  }

  // ── Menu state ───────────────────────────────────────────────────────
  let modules = [];
  let activeModuleKey = null;

  // Current page, for highlighting the matching sidebar row/module on load —
  // same client-side approach as gudang/newmasterx.blade.php's $activePath, but
  // resolved from the AJAX /getmenu/1 tree instead of a server-side $menul0,
  // since most controllers on this shared layout don't pass one.
  const currentHref = @json(trim(request()->path(), '/'));

  const moduleIcons = {
    'berkas':          'archive',
    'master data':     'users',
    'accounting':      'bar-chart',
    'pengadaan':       'credit-card',
    'marketing':       'shopping-cart',
    'gudang':          'warehouse',
    'pos':             'trending-up',
    'laporan-laporan': 'file-text',
    'utilitas':        'tool',
    'jendela':         'monitor',
  };

  function getModuleIcon(label, dbIcon) {
    if (dbIcon && icons[dbIcon]) return dbIcon;
    const k = (label || '').toLowerCase().trim();
    if (moduleIcons[k]) return moduleIcons[k];
    for (const [pattern, iconName] of Object.entries(moduleIcons)) {
      if (k.includes(pattern)) return iconName;
    }
    return 'box';
  }

  function mapMenuNode(row) {
    return {
      key: row.KODEMENU,
      label: row.Keterangan,
      href: row.href,
      access: row.ACCESS,
      icon: row.icon || null,
      color: row.color || null,
      children: (row.child || []).map(mapMenuNode)
    };
  }

  // Prefer the DB-supplied color for report cards, same as
  // gudang/newmasterx.blade.php's getCardColor() -- falls back to cycling through
  // cardColors by index when the row has no color (or an unrecognized one).
  function getCardColor(dbColor, index) {
    if (dbColor && cardColors.includes(dbColor)) return dbColor;
    return cardColors[index % cardColors.length];
  }

  function buildMenu(rows) {
    return (rows || []).map(mapMenuNode);
  }

  function toggleModuleSubmenu(moduleKey) {
    const ng = document.getElementById('ng-' + moduleKey);
    if (!ng) return;
    const willOpen = !ng.classList.contains('open');
    document.querySelectorAll('.nav-group.open').forEach(g => g.classList.remove('open'));
    if (willOpen) ng.classList.add('open');
  }

  // ── Module-home card grid, with drill-down for cards that have their own
  // children -- port 1:1 dari stack-based drill milik Report (reportViewStack/
  // reportDrillInto/reportGoBack/renderReportView), scoped per module lewat
  // moduleViewStack. Cards without children still navigate straight to href.
  let moduleViewStack = [];

  function showModuleHome(moduleKey) {
    closeReportPage();
    const mod = modules.find(m => m.key === moduleKey);
    if (!mod) return;

    activeModuleKey = moduleKey;
    moduleViewStack = [];

    document.querySelectorAll('.nav-group').forEach(g => g.classList.remove('active'));
    const ng = document.getElementById('ng-' + moduleKey);
    if (ng) ng.classList.add('active');

    renderModuleView();
  }

  function moduleDrillInto(node) {
    moduleViewStack.push(node);
    renderModuleView();
  }

  function moduleGoBack() {
    if (moduleViewStack.length > 0) {
      moduleViewStack.pop();
      renderModuleView();
    } else {
      goHome();
    }
  }

  function renderModuleView() {
    const mod = modules.find(m => m.key === activeModuleKey);
    if (!mod) return;

    const currentNode = moduleViewStack[moduleViewStack.length - 1] || mod;
    const trail = moduleViewStack.map(n => n.label);

    document.getElementById('breadcrumb').innerHTML =
      `<span>Beranda</span><span class="bc-sep">›</span>` +
      (trail.length
        ? `<span>${mod.label}</span><span class="bc-sep">›</span>` +
          trail.map((label, i) => i === trail.length - 1 ? `<b>${label}</b>` : `${label} <span class="bc-sep">›</span> `).join('')
        : `<b>${mod.label}</b>`);

    const cards = (currentNode.children || []).map((c, i) => {
      const color    = cardColors[i % cardColors.length];
      const iconName = getChildIcon(c.label, c.icon);
      const hasSub   = c.children && c.children.length > 0;

      if (hasSub) {
        return `
          <div class="card" onclick='moduleDrillInto(${JSON.stringify(c).replace(/'/g, "&#39;")})'>
            <div class="card-icon-wrap ${color}">${icon(iconName)}</div>
            <div class="card-label">${c.label}</div>
            <span class="card-arrow">${icon('chevron')}</span>
          </div>`;
      }

      return `
        <div class="card" onclick="navToChild('${encodeURIComponent(c.href || '')}')">
          <div class="card-icon-wrap ${color}">${icon(iconName)}</div>
          <div class="card-label">${c.label}</div>
        </div>`;
    }).join('');

    const dyn   = document.getElementById('content-dynamic');
    const blade = document.getElementById('content-blade');
    if (blade) blade.style.display = 'none';
    dyn.style.display = 'block';
    dyn.innerHTML = `
      <div class="page-subtitle">
      <button class="report-back-btn" id="report-back-btn" onclick="goHome()">
        ${icon('arrow-left')} Kembali
      </button></div>
      <div class="page-title">${currentNode.label}</div>
      <div class="page-subtitle">${moduleViewStack.length ? '' : (mod.subtitle ?? '')}</div>
      <div class="card-grid">${cards}</div>
    `;
  }

  function navToChild(encodedHref) {
    const href = decodeURIComponent(encodedHref);
    if (href && href !== 'undefined' && href !== '') {
      window.location.href = href;
    }
  }

  function goTo(encodedHref) {
    const href = decodeURIComponent(encodedHref);
    if (href && href !== 'undefined' && href !== '') {
      window.location.href = '{{ url('') }}/' + href.replace(/^\//, '');
    }
  }

  // Matches a menu node's href against the current page's path — trim both sides
  // like PHP's trim($href, '/'), compare case-insensitively, same as newmasterx's
  // $isCurrent check.
  function nodeMatchesCurrent(node) {
    const h = (node.href || '').replace(/^\/+|\/+$/g, '');
    return h !== '' && h.toLowerCase() === currentHref.toLowerCase();
  }

  function subtreeHasCurrent(node) {
    return nodeMatchesCurrent(node) || (node.children || []).some(subtreeHasCurrent);
  }

  // Re-derives which module group should start expanded/highlighted, by walking
  // the client-side menu tree for the node matching currentHref. Ported from
  // gudang/newmasterx.blade.php's applyActiveState(), adapted since this layout
  // has no server-side $activePath available.
  function applyActiveState() {
    document.querySelectorAll('.nav-group').forEach(g => g.classList.remove('active', 'open'));
    if (!currentHref) return;
    const mod = modules.find(subtreeHasCurrent);
    if (!mod) return;
    const ng = document.getElementById('ng-' + mod.key);
    if (ng) ng.classList.add('active', 'open');
  }

  function renderNav() {
    const nav = document.getElementById('nav');
    nav.innerHTML = modules.map(m => `
      <div class="nav-group" id="ng-${m.key}">
        <div class="nav-item" onclick="toggleModuleSubmenu('${m.key}')">
          <span class="nav-icon" onclick="event.stopPropagation(); showModuleHome('${m.key}')">${icon(getModuleIcon(m.label, m.icon))}</span>
          <span class="nav-label">${m.label}</span>
          <span class="nav-chevron">${icon('chevron')}</span>
        </div>
        <div class="nav-children">
          ${m.children.map(c => {
            const hasSub = c.children && c.children.length > 0;
            const isCurrent = !hasSub && nodeMatchesCurrent(c);
            return `
            <div class="nav-child ${hasSub ? 'has-sub' : ''} ${isCurrent ? 'active-child' : ''}"
                 data-flyout-id="${hasSub ? 'flyout-' + c.key : ''}"
                 data-access="${c.access ?? ''}"
                 onclick="event.stopPropagation(); ${hasSub ? '' : `goTo('${encodeURIComponent(c.href || '')}')`}">
              <span class="nav-child-label">${c.label}</span>
              ${hasSub ? `<span class="nav-child-arrow">${icon('chevron')}</span>` : ''}
            </div>`;
          }).join('')}
        </div>
      </div>
    `).join('');

    let flyoutRoot = document.getElementById('flyout-root');
    if (!flyoutRoot) {
      flyoutRoot = document.createElement('div');
      flyoutRoot.id = 'flyout-root';
      document.body.appendChild(flyoutRoot);
    }
    flyoutRoot.innerHTML = modules.flatMap(m =>
      m.children.filter(c => c.children && c.children.length > 0).map(c => `
        <div class="nav-flyout" id="flyout-${c.key}">
          ${c.children.map(sub => `
            <div class="nav-flyout-item"
                 data-access="${sub.access ?? ''}"
                 onclick="goTo('${encodeURIComponent(sub.href || '')}')">${sub.label}</div>
          `).join('')}
        </div>
      `)
    ).join('');

    attachFlyoutHoverHandlers();
    applyActiveState();
  }

  // ── Position + show/hide flyouts on hover using real coordinates ─────
  function attachFlyoutHoverHandlers() {
    const allFlyouts = Array.from(document.querySelectorAll('.nav-flyout'));
    const hideTimers = new Map();
    const HIDE_DELAY = 400;
    const sidebarEl = document.getElementById('sidebar');

    function anyFlyoutOpen() {
      return allFlyouts.some(f => f.classList.contains('flyout-visible'));
    }

    function syncSidebarPin() {
      if (!sidebarEl) return;
      const open = anyFlyoutOpen();
      sidebarEl.classList.toggle('flyout-pinned', open);
      document.querySelectorAll('.nav-group.flyout-owner').forEach(g => {
        if (!open) g.classList.remove('flyout-owner');
      });
    }

    function hideAllExcept(keepEl) {
      allFlyouts.forEach(f => {
        if (f !== keepEl) {
          clearTimeout(hideTimers.get(f));
          f.classList.remove('flyout-visible');
        }
      });
      syncSidebarPin();
    }

    document.querySelectorAll('.nav-child.has-sub').forEach(rowEl => {
      const flyoutId = rowEl.getAttribute('data-flyout-id');
      const flyoutEl = document.getElementById(flyoutId);
      if (!flyoutEl) return;

      function showFlyout() {
        clearTimeout(hideTimers.get(flyoutEl));
        hideAllExcept(flyoutEl);

        const ownerGroup = rowEl.closest('.nav-group');
        if (ownerGroup) ownerGroup.classList.add('flyout-owner');

        const rect = rowEl.getBoundingClientRect();
        const OVERLAP = 6;

        flyoutEl.style.visibility = 'hidden';
        flyoutEl.style.opacity = '0';
        flyoutEl.style.display = 'block';

        const flyoutWidth  = flyoutEl.offsetWidth  || 220;
        const flyoutHeight = flyoutEl.offsetHeight || 0;

        flyoutEl.style.display = '';
        flyoutEl.style.visibility = '';
        flyoutEl.style.opacity = '';

        let left = rect.right - OVERLAP;
        let top  = rect.top;

        if (left + flyoutWidth > window.innerWidth) {
          left = rect.left - flyoutWidth + OVERLAP;
        }
        if (top + flyoutHeight > window.innerHeight) {
          top = Math.max(8, window.innerHeight - flyoutHeight - 8);
        }

        flyoutEl.style.left = left + 'px';
        flyoutEl.style.top  = top  + 'px';
        flyoutEl.classList.add('flyout-visible');
        syncSidebarPin();
      }

      function scheduleHide() {
        const t = setTimeout(() => {
          flyoutEl.classList.remove('flyout-visible');
          syncSidebarPin();
        }, HIDE_DELAY);
        hideTimers.set(flyoutEl, t);
      }

      rowEl.addEventListener('mouseenter', showFlyout);
      rowEl.addEventListener('mouseleave', scheduleHide);
      flyoutEl.addEventListener('mouseenter', () => clearTimeout(hideTimers.get(flyoutEl)));
      flyoutEl.addEventListener('mouseleave', scheduleHide);
    });
  }

  // ── Boot ─────────────────────────────────────────────────────────────
  $.get('{{ url('getmenu/1') }}', function (data) {
    modules = buildMenu(data);
    renderNav();
    renderSidebarFooter();
  }).fail(function () {
    console.error('Failed to load menu from /getmenu');
  });

let reportCategories = [];

function renderSidebarFooter() {
  const footer = document.getElementById('sidebar-footer');
  if (!footer) return;
  footer.innerHTML = `
    <div class="nav-report-item" id="nav-report-item" onclick="showReportPage()">
      <span class="nav-icon">${icon('bar-chart')}</span>
      <span class="nav-label">Report</span>
    </div>
  `;
}

let reportViewStack = [];

function hasLeafDescendant(node) {
  if (node.href && node.href !== '#' && node.href !== '') return true;
  return (node.children || []).some(hasLeafDescendant);
}

function loadReportMenu(callback) {
  $.get('{{ url("getmenureport/1") }}', function (data) {
    const tree = buildMenu(data);
    reportCategories = tree.filter(hasLeafDescendant);
    if (callback) callback();
  }).fail(function () {
    console.error('Failed to load report menu from /getmenureport');
    reportCategories = [];
    if (callback) callback();
  });
}

function showReportPage() {
  activeModuleKey = null;
  reportViewStack = [];

  document.querySelectorAll('.nav-group').forEach(g => g.classList.remove('active'));
  const reportItem = document.getElementById('nav-report-item');
  if (reportItem) reportItem.classList.add('active');

  document.getElementById('breadcrumb').innerHTML =
    `<span>Beranda</span><span class="bc-sep">›</span><b>Report</b>`;

  const blade  = document.getElementById('content-blade');
  const dyn    = document.getElementById('content-dynamic');
  const report = document.getElementById('content-report');

  if (blade) blade.style.display = 'none';
  if (dyn)   dyn.style.display = 'none';

  report.style.display = 'block';
  report.innerHTML = `
    <div class="container-fluid clearfix">
      <button class="report-back-btn" id="report-back-btn" onclick="reportGoBack()">
        ${icon('arrow-left')} Kembali
      </button>
      <div id="report-crumb" class="page-subtitle"></div>
      <div id="report-categories-container" class="text-muted">Memuat data laporan...</div>
    </div>
  `;

  loadReportMenu(renderReportView);
}

function reportDrillInto(node) {
  reportViewStack.push(node);
  renderReportView();
}

function reportGoBack() {
  if (reportViewStack.length > 0) {
    reportViewStack.pop();
    renderReportView();
  } else {
    closeReportPage();
  }
}

function renderReportView() {
  const container = document.getElementById('report-categories-container');
  const crumbEl   = document.getElementById('report-crumb');
  if (!container) return;

  if (crumbEl) {
    const trail = reportViewStack.map(n => n.label);
    crumbEl.innerHTML = trail.length
      ? trail.map((label, i) => i === trail.length - 1 ? `<b>${label}</b>` : `${label} <span class="bc-sep">›</span> `).join('')
      : '';
  }

  const currentNode = reportViewStack[reportViewStack.length - 1] || null;

  if (currentNode) {
    const children = (currentNode.children || []).filter(hasLeafDescendant);
    container.className = '';
    container.innerHTML = `<div class="report-grid">${renderReportCards(children)}</div>`;
    return;
  }

  if (!reportCategories.length) {
    container.className = 'text-muted';
    container.innerHTML = `Tidak ada laporan tersedia.`;
    return;
  }

  container.className = '';
  container.innerHTML = reportCategories.map(cat => {
    const children = (cat.children || []).filter(hasLeafDescendant);
    return `
      <div class="report-category">
        <div class="report-category-title">${cat.label}</div>
        <div class="report-grid">${renderReportCards(children)}</div>
      </div>`;
  }).join('');
}

function renderReportCards(nodes) {
  return nodes.map((node, i) => {
    const color    = getCardColor(node.color, i);
    const iconName = getChildIcon(node.label, node.icon);
    const subChildren = (node.children || []).filter(hasLeafDescendant);
    const hasSub   = subChildren.length > 0;

    if (hasSub) {
      return `
        <div class="report-card report-card-has-sub" onclick='reportDrillInto(${JSON.stringify(node).replace(/'/g, "&#39;")})'>
          <div class="report-card-icon ${color}">${icon(iconName)}</div>
          <div class="report-card-label">${node.label}</div>
          <span class="report-card-arrow">${icon('chevron')}</span>
        </div>`;
    }

    return `
      <div class="report-card" onclick="openReport('${encodeURIComponent(node.href)}')">
        <div class="report-card-icon ${color}">${icon(iconName)}</div>
        <div class="report-card-label">${node.label}</div>
      </div>`;
  }).join('');
}

function closeReportPage() {
  const reportItem = document.getElementById('nav-report-item');
  if (reportItem) reportItem.classList.remove('active');

  reportViewStack = [];

  document.getElementById('content-report').style.display = 'none';
  document.getElementById('content-blade').style.display = 'block';

  document.getElementById('breadcrumb').innerHTML =
    `<span>Beranda</span>`;
  applyActiveState();
}

function openReport(encodedHref) {
  goTo(encodedHref);
}

function goHome() {
  closeReportPage();

  activeModuleKey = null;

  const dyn = document.getElementById('content-dynamic');
  if (dyn) {
    dyn.style.display = 'none';
    dyn.innerHTML = '';
  }

  document.getElementById('content-blade').style.display = 'block';
  document.getElementById('breadcrumb').innerHTML = `<span>Beranda</span>`;
}

</script>

    @include('partials.ajax-error-logger')
    @yield('js')
</body>

</html>
