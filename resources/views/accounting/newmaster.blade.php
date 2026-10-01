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

        .nav-group.open>.nav-item .nav-chevron {
            transform: rotate(90deg);
            color: rgba(255, 255, 255, 0.6);
        }

        /* Children */
        .nav-children {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.25s ease;
            background: rgba(0, 0, 0, 0.15);
        }

        /* Show children only when the group has been CLICKED open — no :hover trigger.
           Direct child (>) so a group can never open another group's children. */
        .nav-group.open>.nav-children {
            max-height: 600px;
        }

        /* But only show text when sidebar is expanded */
        .sidebar:not(:hover) .nav-children {
            max-height: 0 !important;
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
        color: #555;
        font-size: 14px;
        cursor: pointer;
        margin-bottom: 16px;
        padding: 0;
      }

      .report-back-btn:hover {
        color: #000;
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

        #sidebar.flyout-pinned .nav-group.flyout-owner .nav-children {
          max-height: 600px !important;
        }

        #sidebar.flyout-pinned .nav-group.flyout-owner > .nav-item .nav-chevron {
          transform: rotate(90deg);
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
        <div class="logo-icon">SPL</div>
        <span class="logo-text">PT. SPL</span>
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
                <div class="page-title">
                    @yield('page-title')
                </div>
                <div class="breadcrumb" id="breadcrumb"><span>Beranda</span></div>
            </div>
            <div class="header-right">
                <div class="period-badge">
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
  const icons = {
    archive:         `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M21 8v13H3V8M1 3h22v5H1zM10 12h4"/></svg>`,
    users:           `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path stroke-linecap="round" d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>`,
    'credit-card':   `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><path stroke-linecap="round" d="M1 10h22"/></svg>`,
    'shopping-cart': `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line stroke-linecap="round" x1="3" y1="6" x2="21" y2="6"/><path stroke-linecap="round" d="M16 10a4 4 0 01-8 0"/></svg>`,
    'trending-up':   `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><polyline stroke-linecap="round" points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline stroke-linecap="round" points="17 6 23 6 23 12"/></svg>`,
    warehouse:       `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path stroke-linecap="round" d="M9 22V12h6v10"/></svg>`,
    'bar-chart':     `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M18 20V10M12 20V4M6 20v-6"/></svg>`,
    box:             `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline stroke-linecap="round" points="3.27 6.96 12 12.01 20.73 6.96"/><line stroke-linecap="round" x1="12" y1="22.08" x2="12" y2="12"/></svg>`,
    chevron:         `<svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>`,

    'file-text':     `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline stroke-linecap="round" points="14 2 14 8 20 8"/><line stroke-linecap="round" x1="16" y1="13" x2="8" y2="13"/><line stroke-linecap="round" x1="16" y1="17" x2="8" y2="17"/><polyline stroke-linecap="round" points="10 9 9 9 8 9"/></svg>`,
    tool:            `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>`,
    monitor:         `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><path stroke-linecap="round" d="M8 21h8M12 17v4"/></svg>`,
    settings:        `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path stroke-linecap="round" d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>`,
    truck:           `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><path stroke-linecap="round" d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>`,
    clipboard:       `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>`,
    tag:             `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line stroke-linecap="round" x1="7" y1="7" x2="7.01" y2="7"/></svg>`,
    grid:            `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>`,
    lock:            `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path stroke-linecap="round" d="M7 11V7a5 5 0 0110 0v4"/></svg>`,
    dollar:          `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><line stroke-linecap="round" x1="12" y1="1" x2="12" y2="23"/><path stroke-linecap="round" d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>`,
    layers:          `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><polygon stroke-linecap="round" points="12 2 2 7 12 12 22 7 12 2"/><polyline stroke-linecap="round" points="2 17 12 22 22 17"/><polyline stroke-linecap="round" points="2 12 12 17 22 12"/></svg>`,
    repeat:          `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><polyline stroke-linecap="round" points="17 1 21 5 17 9"/><path stroke-linecap="round" d="M3 11V9a4 4 0 014-4h14M7 23l-4-4 4-4"/><path stroke-linecap="round" d="M21 13v2a4 4 0 01-4 4H3"/></svg>`,
    'map-pin':       `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>`,
    percent:         `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><line stroke-linecap="round" x1="19" y1="5" x2="5" y2="19"/><circle cx="6.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/></svg>`,
    'rotate-ccw':    `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><polyline stroke-linecap="round" points="1 4 1 10 7 10"/><path stroke-linecap="round" d="M3.51 15a9 9 0 102.13-9.36L1 10"/></svg>`,
    send:            `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><line stroke-linecap="round" x1="22" y1="2" x2="11" y2="13"/><polygon stroke-linecap="round" points="22 2 15 22 11 13 2 9 22 2"/></svg>`,
    zap:             `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><polygon stroke-linecap="round" points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>`,
    package:         `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline stroke-linecap="round" points="3.27 6.96 12 12.01 20.73 6.96"/><line stroke-linecap="round" x1="12" y1="22.08" x2="12" y2="12"/></svg>`,
    'check-square':  `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><polyline stroke-linecap="round" points="9 11 12 14 22 4"/><path stroke-linecap="round" d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>`,
    printer:         `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><polyline stroke-linecap="round" points="6 9 6 2 18 2 18 9"/><path stroke-linecap="round" d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>`,
    sliders:         `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><line stroke-linecap="round" x1="4" y1="21" x2="4" y2="14"/><line stroke-linecap="round" x1="4" y1="10" x2="4" y2="3"/><line stroke-linecap="round" x1="12" y1="21" x2="12" y2="12"/><line stroke-linecap="round" x1="12" y1="8" x2="12" y2="3"/><line stroke-linecap="round" x1="20" y1="21" x2="20" y2="16"/><line stroke-linecap="round" x1="20" y1="12" x2="20" y2="3"/><line stroke-linecap="round" x1="1" y1="14" x2="7" y2="14"/><line stroke-linecap="round" x1="9" y1="8" x2="15" y2="8"/><line stroke-linecap="round" x1="17" y1="16" x2="23" y2="16"/></svg>`,
  };

  function icon(name) {
    return (icons[name] || icons['box']);
  }

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
      children: (row.child || []).map(mapMenuNode)
    };
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

  // ── Render the card-grid home for a module ───────────────────────────
  function showModuleHome(moduleKey) {
    closeReportPage();
    const mod = modules.find(m => m.key === moduleKey);
    if (!mod) return;

    activeModuleKey = moduleKey;

    document.querySelectorAll('.nav-group').forEach(g => g.classList.remove('active'));
    const ng = document.getElementById('ng-' + moduleKey);
    if (ng) ng.classList.add('active');

    document.getElementById('breadcrumb').innerHTML =
      `<span>Beranda</span><span class="bc-sep">›</span><b>${mod.label}</b>`;

    const cards = mod.children.map((c, i) => {
      const color    = cardColors[i % cardColors.length];
      const iconName = getChildIcon(c.label, c.icon);
      return `
        <div class="card" onclick="navToChild('${moduleKey}','${c.key}','${encodeURIComponent(c.href || '')}')">
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
      <button class="report-back-btn" onclick="goHome()">
        ${icon('chevron')} Kembali
      </button></div>
      <div class="page-title">${mod.label}</div>
      <div class="page-subtitle">${mod.subtitle ?? ''}</div>
      <div class="card-grid">${cards}</div>
    `;
  }

  function navToChild(moduleKey, cardKey, encodedHref) {
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
            return `
            <div class="nav-child ${hasSub ? 'has-sub' : ''}"
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
        ${icon('chevron')} Kembali
      </button>
      <div class="page-title">Report</div>
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
    const color    = cardColors[i % cardColors.length];
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
}

function openReport(encodedHref) {
  goTo(encodedHref);
}

function goHome() {
  closeReportPage();

  activeModuleKey = null;
  document.querySelectorAll('.nav-group').forEach(g => g.classList.remove('active'));

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
