@php
    // Route-driven sidebar state, mirrored from report/newmaster2x.blade.php (see
    // docs/sidebar-navigation-migration.md §7-§8) since mastercore consumes the exact
    // same $akses shape: $akses['href'] is the exact route slug the controller passed
    // to cekAkses(), matching DBMENUREPORT.href exactly.
    $currentHref = trim($akses['href'] ?? '', '/');

    // Report menus go up to 4 levels deep (menu0->menu1->menu2->menu3), so find the
    // chain of ancestor GROUPS down to the current page recursively. Tracked by
    // KODEMENU (always present and unique per row), not href: several pure
    // category-header rows share a blank href, and href-based path membership would
    // let those collide with each other.
    $activePath = [];

    $findPath = function ($nodes, $target) use (&$findPath) {
        foreach ($nodes as $node) {
            $nodeHref = trim($node->href ?? '', '/');
            if ($nodeHref !== '' && strcasecmp($nodeHref, $target) === 0) {
                return []; // matched leaf itself — no ancestor groups left to add here
            }
            // ?? []: NewMenuController::getMenuL0Report() only assigns ->child to
            // menu0/menu1/menu2 -- the deepest level (menu3) never gets ->child set on
            // itself, so reading it there returns null and count(null) is a fatal
            // TypeError under PHP 8. Missing child = "no children" here.
            if (count($node->child ?? []) > 0) {
                $childPath = $findPath($node->child ?? [], $target);
                if ($childPath !== null) {
                    return array_merge([$node['KODEMENU']], $childPath);
                }
            }
        }
        return null;
    };

    if ($currentHref !== '') {
        foreach ($akses['menul0'] as $m0) {
            $path = $findPath([$m0], $currentHref);
            if ($path !== null) {
                $activePath = $path;
                break;
            }
        }
    }

    // A node is worth showing in the sidebar only if it — or something underneath it —
    // actually has a real href. Same rule, same name, as report/newmaster2x.blade.php's
    // hasLeafDescendant() and the Report card grid's JS version — kept in sync so this
    // page's sidebar and the Report page always show the same reachable set.
    $hasLeafDescendant = function ($node) use (&$hasLeafDescendant) {
        $href = trim($node->href ?? '', '/');
        if ($href !== '' && $href !== '#') {
            return true;
        }
        foreach ($node->child ?? [] as $child) {
            if ($hasLeafDescendant($child)) {
                return true;
            }
        }
        return false;
    };

    // Icon dictionary keyed by Keterangan — shared vocabulary with the purchasing and
    // report sidebars (docs/sidebar-navigation-migration.md §3.4).
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
      rel="stylesheet"
      type="text/css"
    />
    <link rel="stylesheet" href="{!! URL::asset('css/semantic.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/select2.min.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/datatables.min.css') !!}">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.css">
    <link rel="stylesheet" href="{!! URL::asset('css/jquery-ui.min.css') !!}">

    <link rel="stylesheet" href="{!! URL::asset('css/canvas/bootstrap.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/style.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/dark.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/font-icons.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/animate.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/magnific-popup.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/custom.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/alertify.css') !!}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">

    <link rel="stylesheet" href="{!! URL::asset('css/style.css') !!}">

    <!-- Sidebar/header design — shared with report/newmaster2x.blade.php (see
         docs/sidebar-navigation-migration.md §8). Defines .sidebar/.nav-group/
         .nav-item/.nav-children/.nav-child, .main/.header/.titleText/.period-badge/
         .avatar/.logout-link, .card-grid, etc. Linked last so its
         body{display:flex;height:100vh} wins over canvas/style.css. -->
    <link rel="stylesheet" href="{!! URL::asset('css/newmaster.css') !!}?v={{ @filemtime(base_path('public/css/newmaster.css')) ?: '1' }}">

    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Document Title
  ============================================= -->
    <title>@yield('title', $akses['namamenu'])</title>

    @yield('css')
    <style>
        /* newmaster.css (the shared design) still has the CSS-hover behavior this
           project deliberately removed twice already — .nav-group:hover>.nav-item
           .nav-chevron and .nav-group:hover .nav-children (moving down the collapsed
           rail flashes every folder open in turn). Its hover selectors have HIGHER
           specificity than a plain override (`:hover` counts as a class), so
           `!important` is required, not just later-in-source-wins. See
           docs/sidebar-navigation-migration.md §8.5. */
        .nav-group>.nav-item .nav-chevron {
            transform: none !important;
        }

        .nav-group.open>.nav-item .nav-chevron {
            transform: rotate(90deg) !important;
        }

        .nav-children {
            max-height: 0 !important;
        }

        .sidebar:hover .nav-group.open>.nav-children {
            max-height: 2000px !important;
        }

        /* Old content area sits inside the new flex shell but keeps its own
           Bootstrap container/row markup untouched. */
        #content {
            flex: 1;
            overflow-y: auto;
        }

        /* -- Flyout panels for menu depth beyond the group/child tier (report menus
           nest up to 4 levels; newmaster.css's design only models 3) --
           Rendered OUTSIDE <aside class="sidebar"> (appended into #flyout-root at the
           end of <body>) so the sidebar's own overflow:hidden/max-height accordion
           mechanics can't clip them. */
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

        /* Same unsized-<svg> issue as .nav-icon/.nav-chevron in newmaster.css: without
           this the arrow's inner <svg> (viewBox only, no width/height) defaults to a
           300x150 box instead of filling this 14x14 span. */
        .nav-child-arrow svg {
            display: block;
            width: 100%;
            height: 100%;
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

        .nav-flyout-caption {
            padding: 8px 12px 4px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #9ca3af;
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
    </style>
  </head>

  <body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo" onclick="window.location.href='{{ url('home') }}'">
            <div class="logo-icon">AJC</div>
            <span class="logo-text">{{ $akses['program'] ?? 'Report' }}</span>
        </div>
        <nav class="sidebar-nav" id="nav">
            @foreach ($akses['menul0'] as $menu0)
                @continue(!$hasLeafDescendant($menu0))
                @include('report.partials.sidebar-nav-node', [
                    'node' => $menu0,
                    'depth' => 0,
                    'activePath' => $activePath,
                    'currentHref' => $currentHref,
                    'iconMap' => $iconMap,
                    'hasLeafDescendant' => $hasLeafDescendant,
                ])
            @endforeach
        </nav>
    </aside>

    {{-- Flyout panels for menu depth 3/4 — deliberately rendered OUTSIDE <aside>, so
         the sidebar's own overflow:hidden/max-height accordion can't clip them (see
         docs/sidebar-navigation-migration.md §8). --}}
    <div id="flyout-root">
        @foreach ($akses['menul0'] as $m0)
            @continue(!$hasLeafDescendant($m0))
            {{-- Same "hide unreachable branches" filtering as sidebar-nav-node.blade.php
                 and the Report card grid's hasLeafDescendant(): a depth-1 row is marked
                 .has-sub if and only if this panel has content, and a depth-2 node gets
                 the captioned-sublist treatment if and only if it has a visible
                 depth-3 child. --}}
            @php $m1Visible = array_values(array_filter($m0->child ?? [], fn($c) => $hasLeafDescendant($c))); @endphp
            @foreach ($m1Visible as $m1)
                @php $m2Visible = array_values(array_filter($m1->child ?? [], fn($c) => $hasLeafDescendant($c))); @endphp
                @if (count($m2Visible) > 0)
                    <div class="nav-flyout" id="flyout-{{ $m1['KODEMENU'] }}">
                        @foreach ($m2Visible as $m2)
                            @php $m3Visible = array_values(array_filter($m2->child ?? [], fn($c) => $hasLeafDescendant($c))); @endphp
                            @if (count($m3Visible) > 0)
                                <div class="nav-flyout-caption">{{ $m2['Keterangan'] }}</div>
                                @foreach ($m3Visible as $m3)
                                    {{-- Same href handling as report/partials/sidebar-nav-node.blade.php:
                                         DBMENUREPORT.href is a bare route slug, resolved client-side by
                                         goTo() rather than baking a pre-resolved url() in here; "#"
                                         placeholder rows stay non-clickable rather than resolving to the
                                         app home. --}}
                                    @php $m3Href = trim($m3->href ?? '', '/'); @endphp
                                    <div class="nav-flyout-item"
                                        @if ($m3Href !== '' && $m3Href !== '#') onclick="goTo('{{ rawurlencode($m3Href) }}')" @endif>
                                        {{ $m3['Keterangan'] }}</div>
                                @endforeach
                            @else
                                @php $m2Href = trim($m2->href ?? '', '/'); @endphp
                                <div class="nav-flyout-item"
                                    @if ($m2Href !== '' && $m2Href !== '#') onclick="goTo('{{ rawurlencode($m2Href) }}')" @endif>
                                    {{ $m2['Keterangan'] }}</div>
                            @endif
                        @endforeach
                    </div>
                @endif
            @endforeach
        @endforeach
    </div>

    <div class="main">
        <header class="header">
            <div class="titleText" style="font-weight: bold" id="breadcrumb">@yield('breadcrumb_title', $akses['namamenu'])</div>
            <div class="header-right">
                <ul class="navbar-nav" style="flex-direction: row; align-items: center;">
                    @yield('buttons')
                </ul>
                <div class="period-badge">
                    Username: {{ \Auth::user()->username }} &ndash;
                    Periode: {!! $akses['periode']->bulan !!} / {!! $akses['periode']->tahun !!}
                </div>
                <div class="avatar">{{ strtoupper(\Auth::user()->username[0]) }}</div>
                <a class="logout-link" href="{!! url('logout') !!}">
                    <i class="bi bi-power"></i> Log Out
                </a>
            </div>
        </header>

        <!-- Content
    ============================================= -->
        <section id="content" class="mt-3 mb-6">
          <div class="content-wrap">
            <div class="container-fluid px-5 clearfix">
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
    <script src="{!! URL::asset('js/popper.min.js') !!}"></script>
    <script src="{!! URL::asset('js/bootstrap.min.js') !!}"></script>
    <script src="{!! URL::asset('js/alertify.js') !!}"></script>
    <script src="{!! URL::asset('js/autoNumeric.js') !!}"></script>
    <script src="{!! URL::asset('js/datatables.min.js') !!}"></script>
    <script src="{!! URL::asset('js/jquery-ui.min.js') !!}"></script>
    <script src="{!! URL::asset('js/qrcode.min.js') !!}"></script>
    <script src="{!! URL::asset('js/ajc-func-core.js') !!}"></script>

    <!-- Footer Scripts
  ============================================= -->
  <script src="{!! URL::asset('js/canvas/functions.js') !!}"></script>
  <script src="{!! URL::asset('js/canvas/JsBarcode.all.min.js') !!}"></script>

    <script type="text/javascript">
      $(document).ready(function(){
        doInitialDate();
      });

      document.onkeydown = function(e) {
        if(event.keyCode == 123) { return false; }
        if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)){ return false; }
        if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)){ return false; }
        if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)){ return false; }

        if(e.ctrlKey && e.shiftKey && e.altKey && e.keyCode === 90) {
          // Ctrl + Shift + Alt + Z
          e.preventDefault(); // Prevent any default action
          doAdminMenu();
        }
      }
      $("button").addClass('btn-sm'); $(".form-control").addClass('form-control-sm');
      $(document).on('hidden.bs.modal', '.modal', function () { $('.modal:visible').length && $(document.body).addClass('modal-open'); });
      $('.modal').modal({ show: false, keyboard: false, backdrop: 'static' }); $("title").html($("#title_page").html());
      $(function () { $('[data-toggle="tooltip"]').tooltip() }); $("[rel='tooltip']").tooltip();

      // Resolves a menu href client-side at click time — same helper
      // report/newmaster2x.blade.php uses (both for its own sidebar and the
      // sidebar-footer Report card grid) and shared by
      // report/partials/sidebar-nav-node.blade.php, which this page also includes.
      function goTo(encodedHref) {
          const href = decodeURIComponent(encodedHref);
          if (href && href !== 'undefined' && href !== '') {
              window.location.href = '{{ url('') }}/' + href.replace(/^\//, '');
          }
      }

      // Sidebar accordion: open the clicked group and close whichever sibling group
      // (at the SAME nesting level only) was open before — reports nest up to 4
      // levels deep, so this is scoped per-level (docs/sidebar-navigation-migration.md).
      function toggleNavGroup(itemEl) {
          var group = itemEl.closest(".nav-group");
          var parent = group.parentElement; // #nav (top level) or a .nav-children (nested)
          var wasOpen = group.classList.contains("open");
          parent.querySelectorAll(":scope > .nav-group.open").forEach(function(g) {
              if (g !== group) g.classList.remove("open");
          });
          group.classList.toggle("open", !wasOpen);
      }

      // Position + show/hide flyout panels (menu depth 3/4) on hover, using real
      // coordinates. Ported from report/newmaster2x.blade.php — see
      // docs/sidebar-navigation-migration.md §8.
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
      attachFlyoutHoverHandlers();

      function doAdminMenu() {
        doUpdateDBMENUREPORT();
      }

      function doUpdateDBMENUREPORT() {
        $.ajax({
          url     : "{!! url('functionglobal_doUpdateDBMENUREPORT') !!}",
          type    : "get",
          async   : false,
          success: function(res) {
            if (res == "1") { alertify.success("Update berhasil"); }
          }
        })
      }

      function doInitialDate(_id = "", _isFirstDay = true) {
        if (_id === "") {
          doInitialDate("inputDate1");
          doInitialDate("inputDate2", false);
          return;
        }

        const bulan = {!! $akses['periode']->bulan !!};
        const tahun = {!! $akses['periode']->tahun !!};

        const year = tahun;
        const month = bulan.toString().padStart(2, '0');

        let theDay;
        if (_isFirstDay) {
          theDay = `${year}-${month}-01`;
        } else {
          let lastDayDate = new Date(year, bulan, 0); // month is 1-based
          theDay = `${year}-${month}-${lastDayDate.getDate().toString().padStart(2, '0')}`;
        }

        let $input = $('#' + _id);
        if ($input.length) {
          if ($input.attr('type') === 'date') {
            $input.val(theDay);
          } else if ($input.attr('type') === 'month') {
            $input.val(`${year}-${month}`);
          }
        }
      }

    </script>
    @include('partials.ajax-error-logger')
    @yield('js')
  </body>
</html>
