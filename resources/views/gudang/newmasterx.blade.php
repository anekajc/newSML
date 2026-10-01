@php
    // Route-driven sidebar state, same approach as purchasing/newmasterx.blade.php and
    // report/newmaster2x.blade.php: no page-specific hardcoding, so every one of the 22
    // gudang pages that may extend this layout gets correct active-module/header-title
    // behavior for free. Gudang controllers pass $menul0 (bare, from
    // NewMenuController::getMenuL0(6)) and $periode — there is no $akses['href'] here like
    // the report module has, so the current page is resolved from request()->path()
    // against DBMENU.href instead.
    $currentHref = trim(request()->path(), '/');

    // Gudang menus go 3 levels deep (menu0->menu1->menu2), one shallower than report's 4.
    // Same recursive walk as report/newmaster2x.blade.php to find the chain of ancestor
    // GROUPS down to the current page, tracked by KODEMENU (unique per row) rather than
    // href — several pure category-header rows can share a blank href, and href-based
    // path membership would let those collide with each other.
    $activePath = [];
    $autoPageTitle = null;

    $findPath = function ($nodes, $target) use (&$findPath) {
        foreach ($nodes as $node) {
            $nodeHref = trim($node->href ?? '', '/');
            if ($nodeHref !== '' && strcasecmp($nodeHref, $target) === 0) {
                return ['path' => [], 'title' => $node['Keterangan']];
            }
            // ?? []: NewMenuController::getMenuL0() only assigns ->child to menu0/menu1 --
            // the deepest level (menu2) is pushed into its parent's child array but never
            // gets ->child set on itself. Reading it there returns null (Eloquent's magic
            // __get), and count(null) is a fatal TypeError under PHP 8. Missing child is
            // structurally "no children" here (menu2 is the deepest level), so default to [].
            if (count($node->child ?? []) > 0) {
                $found = $findPath($node->child ?? [], $target);
                if ($found !== null) {
                    return ['path' => array_merge([$node['KODEMENU']], $found['path']), 'title' => $found['title']];
                }
            }
        }
        return null;
    };

    if ($currentHref !== '') {
        foreach ($menul0 as $m0) {
            $found = $findPath([$m0], $currentHref);
            if ($found !== null) {
                $activePath = $found['path'];
                $autoPageTitle = $found['title'];
                break;
            }
        }
    }

    // Icon dictionary keyed by Keterangan — shared vocabulary with the purchasing and
    // report sidebars (docs/sidebar-navigation-migration.md §3.4/§8): DBMENU's top-level
    // Keterangan values are the same module names across all three menu tables.
    $iconMap = [
        'Berkas' =>
            '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M21 8v13H3V8M1 3h22v5H1zM10 12h4"/></svg>',
        'Master' =>
            '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path stroke-linecap="round" d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>',
        'Accounting' =>
            '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><path stroke-linecap="round" d="M1 10h22"/></svg>',
        'Pengadaan' =>
            '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line stroke-linecap="round" x1="3" y1="6" x2="21" y2="6"/><path stroke-linecap="round" d="M16 10a4 4 0 01-8 0"/></svg>',
        'Marketing' =>
            '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><polyline stroke-linecap="round" points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline stroke-linecap="round" points="17 6 23 6 23 12"/></svg>',
        'Gudang' =>
            '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path stroke-linecap="round" d="M9 22V12h6v10"/></svg>',
        'Report' =>
            '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M18 20V10M12 20V4M6 20v-6"/></svg>',
    ];
@endphp
<!DOCTYPE html>
<html dir="ltr" lang="en-US">

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="author" content="SemiColonWeb" />

    <!-- Stylesheets
 ============================================= -->
    <link
        href="https://fonts.googleapis.com/css?family=Lato:300,400,400i,700|Poppins:300,400,500,600,700|PT+Serif:400,400i&display=swap"
        rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="{!! URL::asset('css/semantic.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/select2.min.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/datatables.min.css') !!}">
    {{-- CDN jquery.dataTables.css (versi mentah, non-Bootstrap) sengaja dilepas -
         dia dimuat SETELAH datatables.min.css lokal (yang sudah termasuk integrasi
         Bootstrap 4, lihat catatan di purchaseOrder.blade.php), jadi rule-nya menang
         dan bikin elemen seperti .paginate_button (margin, dst) beda dari halaman
         yang cuma load versi lokal - lihat perbandingan modal Pilih Gudang vs
         Supplier di purchaseOrder. --}}
    <link rel="stylesheet" href="{!! URL::asset('css/jquery-ui.min.css') !!}">

    <link rel="stylesheet" href="{!! URL::asset('css/canvas/bootstrap.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/style.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/dark.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/font-icons.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/animate.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/magnific-popup.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/custom.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/alertify.css') !!}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">

    <link rel="stylesheet" href="{!! URL::asset('css/style.css') !!}">

    {{-- Styling untuk tabel interaktif ala report (drag kolom, gear menu, dst) —
         lihat docs/new-slider-table-guide.md. --}}
    <link rel="stylesheet"
        href="{!! URL::asset('css/report-table.css') !!}?v={{ @filemtime(base_path('public/css/report-table.css')) ?: '1' }}">
    <link rel="stylesheet"
        href="{!! URL::asset('css/tableMaster2.css') !!}?v={{ @filemtime(base_path('public/css/tableMaster2.css')) ?: '1' }}">

    <!-- Sidebar/header design — shared with the report module's layout and the other
         PT.SPL codebase (see references/newmaster.blade.php + report/newmaster2x.blade.php +
         docs/sidebar-navigation-migration.md §9). Defines .sidebar/.nav-group/.nav-item/
         .nav-children/.nav-child, .main/.header/.titleText/.period-badge/.avatar/
         .logout-link, etc. Loaded LAST so its body{display:flex;height:100vh} wins over
         canvas/style.css. -->
    <link rel="stylesheet"
        href="{!! URL::asset('css/newmaster.css') !!}?v={{ @filemtime(base_path('public/css/newmaster.css')) ?: '1' }}">

    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Document Title
 ============================================= -->
    <title>@yield('title', 'Gudang')</title>

    @yield('css')
    <style>
        table.dataTable tbody th,
        table.dataTable tbody td {
            padding: 0 10px !important;
        }

        table tbody th,
        table tbody td {
            padding: 0 10px !important;
        }

        /* newmaster.css still has the CSS-hover behavior this project deliberately
           removed already (see report/newmaster2x.blade.php and
           docs/sidebar-navigation-migration.md §8) — .nav-group:hover>.nav-item
           .nav-chevron and .nav-group:hover .nav-children (moving down the collapsed
           rail flashes every folder open in turn). Its hover selectors have HIGHER
           specificity than a plain override (`:hover` counts as a class), so
           `!important` is required, not just later-in-source-wins. Direct-child
           selectors so nesting can't leak .open state. */
        .nav-group>.nav-item .nav-chevron {
            transform: none !important;
        }

        .nav-group.open>.nav-item .nav-chevron {
            transform: rotate(90deg) !important;
        }

        /* Blanket-suppress first (kills newmaster.css's .nav-group:hover .nav-children
           600px rule via !important, regardless of source order), then re-open only
           for a genuinely .open group with the sidebar actually expanded — higher
           specificity (5 simple selectors) than the blanket rule (1) so it wins over
           it despite both being !important. newmaster.css's own
           .sidebar:not(:hover) .nav-children guard still applies underneath this. */
        .nav-children {
            max-height: 0 !important;
        }

        .sidebar:hover .nav-group.open>.nav-children {
            max-height: 2000px !important;
        }

        #content {
            flex: 1;
            overflow-y: auto;
            padding: 28px 32px;
        }

        /* -- Flyout panel for menu depth beyond the group/child tier -- rendered OUTSIDE
           <aside class="sidebar"> (appended into #flyout-root at the end of <body>) so
           the sidebar's own overflow:hidden/max-height accordion mechanics can't clip
           it, regardless of the flyout's own position/z-index. Gudang menus only go 3
           levels deep, so there is a single flyout tier here (no 4th-level flatten,
           unlike report's). */
        .nav-child.has-sub {
            justify-content: flex-start;
        }

        .nav-child.has-sub .nav-child-label {
            flex: 1;
            text-align: left;
        }

        .nav-child-arrow {
            width: 14px;
            height: 14px;
            flex-shrink: 0;
            opacity: 0.6;
            margin-left: auto;
            transition: opacity 0.15s ease;
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

        /* Keep the sidebar pinned wide while a flyout is open, and keep the owning
           group visually expanded, even though the cursor has left the rail to reach
           the flyout panel. */
        #sidebar.flyout-pinned {
            width: var(--sidebar-exp, 240px) !important;
        }

        #sidebar.flyout-pinned .logo-text,
        #sidebar.flyout-pinned .nav-label,
        #sidebar.flyout-pinned .nav-chevron {
            opacity: 1 !important;
        }

        #sidebar.flyout-pinned .nav-group.flyout-owner .nav-children {
            max-height: 2000px !important;
        }

        #sidebar.flyout-pinned .nav-group.flyout-owner>.nav-item .nav-chevron {
            transform: rotate(90deg);
        }

        /* Sidebar-footer "Report" widget + in-page Report browser (ported from
           report/newmaster2x.blade.php so this layout has the same footer button and
           card-grid drill-down behavior). */
        .sidebar-footer {
            margin-top: auto;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 4px;
        }

        .nav-report-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            cursor: pointer;
            color: rgba(255, 255, 255, 0.85);
            transition: background 0.12s ease;
        }

        .nav-report-item:hover,
        .nav-report-item.active {
            background: rgba(255, 255, 255, 0.08);
        }

        .nav-report-item .nav-icon {
            display: flex;
            width: 18px;
            height: 18px;
        }

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
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            padding: 14px;
            cursor: pointer;
            transition: box-shadow 0.12s ease, transform 0.12s ease;
        }

        .report-card:hover {
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
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
            background: rgba(0, 0, 0, 0.04);
        }

        .report-card-label {
            font-size: 14px;
            font-weight: 500;
            color: #222;
        }

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

        /* Module-home card grid (ported from newmasterTest.blade.php) — the "drill into
           this card" chevron. .card/.card-grid/.card-icon-wrap/.page-title/.page-subtitle/
           .c-blue…c-cyan already exist scoped under .nm-ui in public/css/newmaster.css
           (kept scoped there specifically to avoid colliding with Bootstrap 4's own .card,
           which this layout also loads — see the scoping comment at newmaster.css:180);
           .card-arrow itself doesn't exist there yet, so it's added here locally instead of
           touching the shared file, same precedent as .report-card-arrow above.
           .nm-ui .card doesn't set position:relative there either (never needed an
           absolutely-positioned child before) -- add it here too, purely additive, so
           .card-arrow has something to anchor to. */
        .nm-ui .card {
            position: relative;
        }

        .nm-ui .card-arrow {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 14px;
            height: 14px;
            opacity: 0.4;
            color: var(--text-muted);
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
    </style>
</head>

<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo" onclick="window.location.href='{{ url('home') }}'" style="cursor:pointer">
            <div class="logo-icon">AJC</div>
            <span class="logo-text">Gudang</span>
        </div>
        <nav class="sidebar-nav" id="nav">
            {{-- Not present in $menul0 (DBMENU) — pinned in manually so Set Periode stays
                 reachable, same as the hardcoded "BERKAS" entry in gudang/newmaster.blade.php. --}}
            <div class="nav-group">
                <div class="nav-item" onclick="window.location.href='{{ url('setperiode') }}'">
                    <span class="nav-icon">{!! $iconMap['Berkas'] ?? '' !!}</span>
                    <span class="nav-label">Set Periode</span>
                </div>
            </div>

            {{-- AJAX-rendered menu tree (see renderNav() below), ported from
                 newmasterTest.blade.php -- a nested div rather than #nav itself, so
                 renderNav()'s innerHTML replacement doesn't wipe out Set Periode above. --}}
            <div id="nav-dynamic"></div>
        </nav>

        <div class="sidebar-footer" id="sidebar-footer">
            <div class="nav-report-item" id="nav-report-item" onclick="showReportPage()">
                <span class="nav-icon">{!! $iconMap['Report'] !!}</span>
                <span class="nav-label">Report</span>
            </div>
        </div>
    </aside>

    {{-- Flyout panels for menu depth 2 (the deepest gudang level) — populated client-side
         by renderNav() (see script below), same pattern as newmasterTest.blade.php. Kept
         as a static empty container here (rather than letting JS create it) so its DOM
         position — outside <aside>, near the end of <body>, so the sidebar's own
         overflow:hidden/max-height accordion can't clip it — is guaranteed regardless of
         script timing. --}}
    <div id="flyout-root"></div>

    <div class="main">
        <header class="header">
            <div class="titleText" style="font-weight: bold" id="breadcrumb">
                @yield('breadcrumb_title', $autoPageTitle ?: 'Gudang')
            </div>
            <div class="header-right">
                <ul class="navbar-nav" style="list-style:none; display:flex; align-items:center; margin:0;">
                    @yield('buttons')
                </ul>
                <div class="period-badge">
                    Username: {{ \Auth::user()->username }} &ndash;
                    Periode: {!! $periode->bulan !!} / {!! $periode->tahun !!}
                </div>
                <div class="avatar">{{ strtoupper(\Auth::user()->username[0]) }}</div>
                <a class="logout-link" href="{!! url('logout') !!}">
                    <i class="bi bi-power"></i> Log Out
                </a>
            </div>
        </header>

        <!-- Content
    ============================================= -->
        <section id="content">
            <div id="content-dynamic" class="nm-ui" style="display:none;"></div>
            <div id="content-report" class="nm-ui" style="display:none;"></div>
            <div class="content-wrap" id="content-blade">
                <div class="container-fluid clearfix">
                    <div class="row gutter-40 col-mb-80">
                        @yield('content')
                    </div>
                </div>
            </div>
        </section>
        <!-- #content end -->
    </div>

    <!-- External JavaScripts
    ============================================= -->

    <script src="{!! URL::asset('js/canvas/jquery.js') !!}"></script>
    <script src="{!! URL::asset('js/jquery.min.js') !!}"></script>
    <script src="{!! URL::asset('js/jquery-3.3.1.min.js') !!}"></script>
    <script src="{!! URL::asset('js/select2.min.js') !!}"></script>
    {{-- Pinned to Bootstrap 4.6.2 (bundle = Popper 1.16.1 + Bootstrap JS), matching the
         v4.5.0 canvas/bootstrap.css loaded above. This layout is intentionally NOT on the
         shared public/js/bootstrap.min.js (v4.0.0) used elsewhere in the app — see
         docs/bootstrap4-version-alignment-guide.md before touching this. --}}
    <script src="{!! URL::asset('js/bootstrap.bundle-4.6.2.min.js') !!}"></script>
    <script src="{!! URL::asset('js/alertify.js') !!}"></script>
    <script src="{!! URL::asset('js/autoNumeric.js') !!}"></script>
    <script src="{!! URL::asset('js/datatables.min.js') !!}"></script>
    <script src="{!! URL::asset('js/jquery-ui.min.js') !!}"></script>
    <script src="{!! URL::asset('js/qrcode.min.js') !!}"></script>

    <!-- Footer Scripts
 ============================================= -->
    <script src="{!! URL::asset('js/canvas/functions.js') !!}"></script>
    <script src="{!! URL::asset('js/canvas/JsBarcode.all.min.js') !!}"></script>
    <script src="{!! URL::asset('js/AutoNumeric.js') !!}" type="text/javascript"></script>



    <!-- Sidebar/card icon dictionary (icons object + icon() helper) — needed by the
         sidebar-footer Report browser below. -->
    <script src="{!! URL::asset('js/sidebar-icons.js') !!}?v={{ @filemtime(public_path('js/sidebar-icons.js')) ?: '1' }}"></script>

    <script type="text/javascript">
        document.onkeydown = function(e) {
            if (event.keyCode == 123) {
                return false;
            }
            if (e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
                return false;
            }
            if (e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
                return false;
            }
            if (e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
                return false;
            }
        }
        $("button").addClass('btn-sm');
        $(".form-control").addClass('form-control-sm');
        $(document).on('hidden.bs.modal', '.modal', function() {
            $('.modal:visible').length && $(document.body).addClass('modal-open');
        });
        $('.modal').modal({
            show: false,
            keyboard: false,
            backdrop: 'static'
        });
        $("title").html($("#title_page").html());
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        });
        $("[rel='tooltip']").tooltip();

        // Position + show/hide flyout panels (menu depth 2) on hover, using real
        // coordinates. Ported from report/newmaster2x.blade.php / references/newmaster.blade.php.
        // The panels live in #flyout-root (appended near the end of <body>, outside
        // .sidebar) specifically so the sidebar's own overflow:hidden/max-height
        // accordion can't clip them.
        function attachFlyoutHoverHandlers() {
            const allFlyouts = Array.from(document.querySelectorAll(".nav-flyout"));
            const hideTimers = new Map(); // flyoutEl -> timer id, one per flyout
            const HIDE_DELAY = 400; // ms grace period to travel from row to flyout
            const sidebarEl = document.getElementById("sidebar");

            function anyFlyoutOpen() {
                return allFlyouts.some(f => f.classList.contains("flyout-visible"));
            }

            function syncSidebarPin() {
                if (!sidebarEl) return;
                const open = anyFlyoutOpen();
                sidebarEl.classList.toggle("flyout-pinned", open);
                document.querySelectorAll(".nav-group.flyout-owner").forEach(g => {
                    if (!open) g.classList.remove("flyout-owner");
                });
            }

            function hideAllExcept(keepEl) {
                allFlyouts.forEach(f => {
                    if (f !== keepEl) {
                        clearTimeout(hideTimers.get(f));
                        f.classList.remove("flyout-visible");
                    }
                });
                syncSidebarPin();
            }

            document.querySelectorAll(".nav-child.has-sub").forEach(rowEl => {
                const flyoutId = rowEl.getAttribute("data-flyout-id");
                const flyoutEl = document.getElementById(flyoutId);
                if (!flyoutEl) return;

                function showFlyout() {
                    clearTimeout(hideTimers.get(flyoutEl));
                    hideAllExcept(flyoutEl);

                    const ownerGroup = rowEl.closest(".nav-group");
                    if (ownerGroup) ownerGroup.classList.add("flyout-owner");

                    const rect = rowEl.getBoundingClientRect();
                    const OVERLAP = 6;

                    flyoutEl.style.visibility = "hidden";
                    flyoutEl.style.opacity = "0";
                    flyoutEl.style.display = "block";

                    const flyoutWidth = flyoutEl.offsetWidth || 220;
                    const flyoutHeight = flyoutEl.offsetHeight || 0;

                    flyoutEl.style.display = "";
                    flyoutEl.style.visibility = "";
                    flyoutEl.style.opacity = "";

                    let left = rect.right - OVERLAP;
                    let top = rect.top;

                    if (left + flyoutWidth > window.innerWidth) {
                        left = rect.left - flyoutWidth + OVERLAP;
                    }
                    if (top + flyoutHeight > window.innerHeight) {
                        top = Math.max(8, window.innerHeight - flyoutHeight - 8);
                    }

                    flyoutEl.style.left = left + "px";
                    flyoutEl.style.top = top + "px";
                    flyoutEl.classList.add("flyout-visible");
                    syncSidebarPin();
                }

                function scheduleHide() {
                    const t = setTimeout(() => {
                        flyoutEl.classList.remove("flyout-visible");
                        syncSidebarPin();
                    }, HIDE_DELAY);
                    hideTimers.set(flyoutEl, t);
                }

                rowEl.addEventListener("mouseenter", showFlyout);
                rowEl.addEventListener("mouseleave", scheduleHide);
                flyoutEl.addEventListener("mouseenter", () => clearTimeout(hideTimers.get(flyoutEl)));
                flyoutEl.addEventListener("mouseleave", scheduleHide);
            });
        }

        // ── AJAX-driven sidebar (ported from newmasterTest.blade.php), replacing the old
        // server-rendered gudang.partials.sidebar-nav-node.blade.php tree. The menu is
        // fetched from /getmenu/1 -- verified to run the exact same query as this file's
        // controllers already use via NewMenuController::getMenuL0(6) (same
        // NewMenu::where('L0',...)->join('DBFLMENUWEB',...)->where('USERID',...)->
        // where('HASACCESS',1) chain; the $headermenu value only branches on '== 2',
        // which neither 6 nor 1 triggers), so this is the same access-filtered tree,
        // just fetched client-side instead of server-side. ──────────────────────────────

        let modules = [];
        let activeModuleKey = null;
        let moduleViewStack = [];

        // $activePath/$currentHref: computed server-side (the @@php block at the top of
        // this file, unchanged) from the SAME $menul0 the controller already passes for
        // this exact purpose. Embedding them lets renderNav()/applyActiveState()
        // re-derive the on-load active/open sidebar state after the AJAX-built DOM
        // replaces the old server-rendered one -- the breadcrumb itself needs no such
        // handling, since its own yield already renders correctly in the initial HTML
        // and is never touched by renderNav().
        const activePath = @json($activePath);
        const currentHref = @json($currentHref);

        const moduleIcons = {
            'berkas': 'archive',
            'master data': 'users',
            'accounting': 'bar-chart',
            'pengadaan': 'credit-card',
            'marketing': 'shopping-cart',
            'gudang': 'warehouse',
            'pos': 'trending-up',
            'laporan-laporan': 'file-text',
            'utilitas': 'tool',
            'jendela': 'monitor',
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

        function toggleModuleSubmenu(moduleKey) {
            const ng = document.getElementById('ng-' + moduleKey);
            if (!ng) return;
            const willOpen = !ng.classList.contains('open');
            document.querySelectorAll('.nav-group.open').forEach(g => g.classList.remove('open'));
            if (willOpen) ng.classList.add('open');
        }

        // Re-derives which module group should start expanded/highlighted, since
        // renderNav() rebuilds #nav-dynamic from scratch -- mirrors what
        // sidebar-nav-node.blade.php used to compute server-side via $isOnPath before
        // this sidebar became AJAX-driven. Also used to restore that same state when
        // exiting the Report page or a module-home view (see closeReportPage()/goHome()).
        function applyActiveState() {
            document.querySelectorAll('.nav-group').forEach(g => g.classList.remove('active', 'open'));
            if (activePath.length > 0) {
                const ng = document.getElementById('ng-' + activePath[0]);
                if (ng) ng.classList.add('active', 'open');
            }
        }

        // ── Module-home card grid: clicking a module's icon (not its label) shows a
        // drill-down card grid of that module's own children in #content-dynamic,
        // independent of the Report browser below. Ported from newmasterTest.blade.php. ──

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

        // Not currently wired to the rendered "Kembali" button (see renderModuleView()
        // below) -- ported as-is from newmasterTest.blade.php, which has the same
        // disconnect: its button always calls goHome() directly instead of stepping back
        // one level via this function. Kept for parity with the reference file rather
        // than silently "fixing" it.
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

            const cards = (currentNode.children || []).map((c, i) => {
                const color = cardColors[i % cardColors.length];
                const iconName = getChildIcon(c.label, c.icon);
                const hasSub = c.children && c.children.length > 0;

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

            const dyn = document.getElementById('content-dynamic');
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

        // newmasterTest.blade.php's own navToChild() navigates via a RAW, unprefixed
        // href (window.location.href = href) instead of resolving it through the app
        // base URL like goTo() does. That's fragile even there and would be actively
        // broken here: gudang pages live under a path (e.g. /permintaanpemakaian), so an
        // unprefixed relative href would resolve against the CURRENT page's URL, not the
        // app root. Routed through goTo() instead -- same fix already applied to
        // openReport() below for the same reason.
        function navToChild(encodedHref) {
            goTo(encodedHref);
        }

        function renderNav() {
            const nav = document.getElementById('nav-dynamic');
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
                            // Mirrors sidebar-nav-node.blade.php's $isCurrent: trim both
                            // sides like PHP's trim($href, '/'), compare case-insensitively
                            // like strcasecmp() against the server-computed $currentHref.
                            const cHref = (c.href || '').replace(/^\/+|\/+$/g, '');
                            const isCurrent = !hasSub && cHref !== '' && cHref.toLowerCase() === currentHref.toLowerCase();
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

        // Module-home exit point (used by moduleGoBack() and the rendered "Kembali"
        // button). closeReportPage() already restores the original breadcrumb + active
        // sidebar group (see its own comment) -- reused here rather than re-deriving the
        // same thing twice, unlike newmasterTest.blade.php's version, which hardcodes
        // the breadcrumb back to a generic "Beranda" (gudang's initial breadcrumb is a
        // real page title and must be restored, not replaced).
        function goHome() {
            closeReportPage();

            activeModuleKey = null;
            moduleViewStack = [];

            const dyn = document.getElementById('content-dynamic');
            if (dyn) {
                dyn.style.display = 'none';
                dyn.innerHTML = '';
            }

            document.getElementById('content-blade').style.display = 'block';
        }

        // ── Boot ─────────────────────────────────────────────────────────────
        $.get('{{ url('getmenu/1') }}', function(data) {
            modules = buildMenu(data);
            renderNav();
        }).fail(function() {
            console.error('Failed to load menu from /getmenu');
        });

        // ── Sidebar-footer "Report" widget (ported from report/newmaster2x.blade.php)
        // Renders an in-page card-grid browser (category sections -> cards -> drill into
        // folder cards -> "Kembali" back) fed by /getmenureport/1 (dbmenureportweb) — the
        // same full report catalog newmaster2x's Report button shows, not filtered to
        // Gudang and not access-filtered (consistent with that layout). ─────────────────

        const cardColors = ['c-blue', 'c-green', 'c-orange', 'c-purple', 'c-teal', 'c-pink', 'c-yellow', 'c-red', 'c-indigo', 'c-cyan'];

        const childIconMap = [
            ['valas', 'dollar'],
            ['devisi', 'layers'],
            ['perkiraan', 'layers'],
            ['aktiva', 'package'],
            ['hutang', 'credit-card'],
            ['piutang', 'credit-card'],
            ['giro', 'repeat'],
            ['laba', 'trending-up'],
            ['neraca', 'bar-chart'],
            ['costing', 'sliders'],
            ['posting', 'send'],
            ['supplier', 'truck'],
            ['gudang', 'warehouse'],
            ['group', 'grid'],
            ['merk', 'tag'],
            ['bahan', 'package'],
            ['barang', 'box'],
            ['jasa', 'clipboard'],
            ['lokasi', 'map-pin'],
            ['satuan', 'sliders'],
            ['area', 'map-pin'],
            ['kota', 'map-pin'],
            ['customer', 'users'],
            ['sales', 'trending-up'],
            ['expedisi', 'truck'],
            ['departemen', 'grid'],
            ['jabatan', 'layers'],
            ['karyawan', 'users'],
            ['biaya', 'dollar'],
            ['pajak', 'percent'],
            ['kendaraan', 'truck'],
            ['sopir', 'truck'],
            ['periode', 'settings'],
            ['kunci', 'lock'],
            ['nomor', 'settings'],
            ['pemakai', 'users'],
            ['password', 'lock'],
            ['kalkulator', 'sliders'],
            ['log', 'file-text'],
            ['jurnal', 'file-text'],
            ['kas', 'dollar'],
            ['bank', 'credit-card'],
            ['bon', 'clipboard'],
            ['memorial', 'file-text'],
            ['koreksi', 'rotate-ccw'],
            ['pelunasan', 'check-square'],
            ['permintaan', 'clipboard'],
            ['penerimaan', 'package'],
            ['inspeksi', 'check-square'],
            ['invoice', 'file-text'],
            ['retur', 'rotate-ccw'],
            ['debet', 'dollar'],
            ['penawaran', 'tag'],
            ['verifikasi', 'check-square'],
            ['uang muka', 'dollar'],
            ['surat jalan', 'send'],
            ['closing', 'lock'],
            ['performance', 'trending-up'],
            ['opname', 'check-square'],
            ['transfer', 'repeat'],
            ['sample', 'package'],
            ['konsinyasi', 'package'],
            ['kasir', 'dollar'],
            ['laporan', 'bar-chart'],
            ['dashboard', 'bar-chart'],
            ['hitung', 'sliders'],
            ['proses', 'zap'],
            ['aktivitas', 'file-text'],
            ['cascade', 'layers'],
            ['tile', 'grid'],
            ['arrange', 'grid'],
            ['po', 'clipboard'],
            ['so', 'clipboard'],
            ['faktur', 'file-text'],
            ['nota', 'file-text'],
            ['kredit', 'credit-card'],
            ['pemakaian', 'package'],
            ['informasi', 'layers'],
            ['cetak', 'printer'],
        ];

        function getChildIcon(label, dbIcon) {
            if (dbIcon && icons[dbIcon]) return dbIcon;
            const l = (label || '').toLowerCase();
            for (const [kw, ic] of childIconMap) {
                if (l.includes(kw)) return ic;
            }
            return 'box';
        }

        function getCardColor(dbColor, index) {
            if (dbColor && cardColors.includes(dbColor)) return dbColor;
            return cardColors[index % cardColors.length];
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

        function buildMenu(rows) {
            return (rows || []).map(mapMenuNode);
        }

        function goTo(encodedHref) {
            const href = decodeURIComponent(encodedHref);
            if (href && href !== 'undefined' && href !== '') {
                window.location.href = '{{ url('') }}/' + href.replace(/^\//, '');
            }
        }

        function openReport(encodedHref) {
            goTo(encodedHref);
        }

        // A node is worth showing only if it -- or something underneath it -- actually
        // has a real href. Pure "#" folders with no working leaf anywhere below them get
        // filtered out entirely.
        function hasLeafDescendant(node) {
            if (node.href && node.href !== '#' && node.href !== '') return true;
            return (node.children || []).some(hasLeafDescendant);
        }

        let reportCategories = [];
        let reportViewStack = []; // breadcrumb trail of nodes drilled into

        // Snapshot the server-rendered breadcrumb ONCE, before the Report page ever
        // touches it, so closeReportPage() can restore exactly what the Blade template
        // rendered for this page ($autoPageTitle is computed server-side -- there is no
        // client-side "Beranda" concept to fall back on). This is safe to read
        // synchronously here (unlike the active sidebar group below): #breadcrumb's
        // content comes from the breadcrumb_title yield directly in the initial page
        // HTML, untouched by the AJAX-driven sidebar.
        //
        // The active *sidebar group*, in contrast, can't be snapshotted from the DOM at
        // this point -- #nav-dynamic is still empty (renderNav() hasn't run yet, since
        // /getmenu/1 hasn't resolved), so querySelectorAll('.nav-group.active') would
        // always find nothing. applyActiveState() (below) re-derives it instead, from
        // the same $activePath data the old server-rendered partial used.
        const originalBreadcrumbHtml = document.getElementById('breadcrumb').innerHTML;

        function loadReportMenu(callback) {
            $.get('{{ url("getmenureport/1") }}', function(data) {
                const tree = buildMenu(data);
                reportCategories = tree.filter(hasLeafDescendant);
                if (callback) callback();
            }).fail(function() {
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

            document.getElementById('breadcrumb').innerHTML = `<span class="bc-sep"></span><b>Report</b>`;

            const blade = document.getElementById('content-blade');
            const dyn = document.getElementById('content-dynamic');
            const report = document.getElementById('content-report');

            if (blade) blade.style.display = 'none';
            if (dyn) dyn.style.display = 'none';

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

        // Drill into a folder node -- stays on the Report page, just changes what's shown.
        function reportDrillInto(node) {
            reportViewStack.push(node);
            renderReportView();
        }

        // One level up. If already at the root, exit the Report page entirely.
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
            const crumbEl = document.getElementById('report-crumb');
            if (!container) return;

            if (crumbEl) {
                const trail = reportViewStack.map(n => n.label);
                crumbEl.innerHTML = trail.length
                    ? trail.map((label, i) => i === trail.length - 1 ? `<b>${label}</b>` : `${label} <span class="bc-sep"></span> `).join('')
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
                const color = getCardColor(node.color, i);
                const iconName = getChildIcon(node.label, node.icon);
                const subChildren = (node.children || []).filter(hasLeafDescendant);
                const hasSub = subChildren.length > 0;

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

            document.getElementById('breadcrumb').innerHTML = originalBreadcrumbHtml;
            applyActiveState();
        }

        function formatAngkaParse(angka) {

            return parseFloat(angka).toFixed(2)
        }

        function formatAngkaVal(angka) {
            return Number(angka.split(',').join(''))
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
            }
            temp1 += '.' + tempAngka[1]
            return temp1
        }


        function numberWithCommas(n) {
            var parts = n.toString().split(".");
            return parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",") + (parts[1] ? "." + parts[1] : "");
        }

        function toInteger(n) {
            return parseInt(n.replace(/,/g, ""));
        }

        function toFloat(n) {
            return parseFloat(n.replace(/,/g, ""));
        }

        function round(value, precision) {
            var multiplier = Math.pow(10, precision || 0);
            return Math.round(value * multiplier) / multiplier;
        }

        function middleTD() {}

        function format_date(date) {
            if (date == "" || date == null) {
                return "";
            }
            return date.split("-")[2] + "/" + date.split("-")[1] + "/" + date.split("-")[0];
        }

        function format_timestamp(date) {
            if (date == "" || date == null) return "";
            tgl = date.split(" ")[0];
            waktu = date.split(" ")[1];
            return tgl.split("-")[2] + "/" + tgl.split("-")[1] + "/" + tgl.split("-")[0] + " " + waktu;
        }
    </script>

    {{-- window.ReportTable — tabel interaktif ala report (drag kolom, gear menu, dst),
         lihat docs/new-slider-table-guide.md. --}}
    <script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>

    @include('partials.ajax-error-logger')
    @yield('js')
</body>

</html>
