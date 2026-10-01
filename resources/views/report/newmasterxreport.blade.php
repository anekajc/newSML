<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      href="https://fonts.googleapis.com/css?family=Lato:300,400,400i,700|Poppins:300,400,500,600,700|PT+Serif:400,400i&display=swap"
      rel="stylesheet"
      type="text/css"
    />

    <link
      rel="stylesheet"
      href="{!! URL::asset('css/semantic.css') !!}"
    />
    <link
      rel="stylesheet"
      href="{!! URL::asset('css/select2.min.css') !!}"
    />
    <link
      rel="stylesheet"
      href="{!! URL::asset('css/datatables.min.css') !!}"
    />
    <link
      rel="stylesheet"
      type="text/css"
      href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.css"
    />
    <link
      rel="stylesheet"
      href="{!! URL::asset('css/jquery-ui.min.css') !!}"
    />

    <link
      rel="stylesheet"
      href="{!! URL::asset('css/canvas/bootstrap.css') !!}"
    />
    <link
      rel="stylesheet"
      href="{!! URL::asset('css/canvas/style.css') !!}"
    />
    <link
      rel="stylesheet"
      href="{!! URL::asset('css/canvas/dark.css') !!}"
    />
    <link href="https://unpkg.com/gridjs/dist/theme/mermaid.min.css" rel="stylesheet" />
    <link
      rel="stylesheet"
      href="{!! URL::asset('css/canvas/font-icons.css') !!}"
    />
    <link
      rel="stylesheet"
      href="{!! URL::asset('css/canvas/animate.css') !!}"
    />
    <link
      rel="stylesheet"
      href="{!! URL::asset('css/canvas/magnific-popup.css') !!}"
    />
    <link
      rel="stylesheet"
      href="{!! URL::asset('css/canvas/custom.css') !!}"
    />
    <link
      rel="stylesheet"
      href="{!! URL::asset('css/alertify.css') !!}"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css"
    />

    <link rel="stylesheet" href="{!! URL::asset('css/style.css') !!}" />

    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Purchasing</title>
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
  color: rgba(255,255,255,.5);
  cursor: pointer;
}

.nav-subchild:hover {
  color: white;
  background: rgba(255,255,255,.05);
}

      /* ── SIDEBAR ── */
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
      .nav-group.active > .nav-item {
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
      .nav-group.active > .nav-item .nav-icon {
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
      .nav-group.active > .nav-item .nav-label {
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
      .nav-group.open > .nav-item .nav-chevron,
      .nav-group:hover > .nav-item .nav-chevron {
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
      /* Show children when: group is hovered (sidebar expanded) OR group is open/active */
      .nav-group:hover .nav-children,
      .nav-group.open .nav-children {
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

      /* ── MAIN ── */
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

      /* ── CONTENT ── */
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

      /* ── SUB-PAGE CONTENT ── */
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

      /* .data-table-wrap {
    overflow: visible !important;
} */

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
  </head>
  <body>
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">AJC</div>
        <span class="logo-text">Report</span>
    </div>

    <nav class="sidebar-nav" id="nav">

        @foreach ($menul0 as $menu0)

            <div class="nav-group {{ count($menu0['child']) > 0 ? 'open' : '' }}">

                <div class="nav-item"
                     onclick="window.location.href='{{ url($menu0->href) }}'">

                    <span class="nav-icon">
                        {!! [
                            'Berkas' => '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M21 8v13H3V8M1 3h22v5H1zM10 12h4"/></svg>',
                            'Master' => '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path stroke-linecap="round" d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>',
                            'Accounting' => '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><path stroke-linecap="round" d="M1 10h22"/></svg>',
                            'Pengadaan' => '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line stroke-linecap="round" x1="3" y1="6" x2="21" y2="6"/><path stroke-linecap="round" d="M16 10a4 4 0 01-8 0"/></svg>',
                            'Marketing' => '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><polyline stroke-linecap="round" points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline stroke-linecap="round" points="17 6 23 6 23 12"/></svg>',
                            'Gudang' => '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path stroke-linecap="round" d="M9 22V12h6v10"/></svg>',
                            'Report' => '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M18 20V10M12 20V4M6 20v-6"/></svg>',
                        ][$menu0['Keterangan']] ?? '' !!}
                    </span>

                    <span class="nav-label">
                        {{ $menu0['Keterangan'] }}
                    </span>

                    @if(count($menu0['child']) > 0)
                        <span class="nav-chevron">▼</span>
                    @endif

                </div>

                {{-- LEVEL 1 --}}
                @if(count($menu0['child']) > 0)

                    <div class="nav-children">

                        @foreach($menu0['child'] as $menu1)

                            <div class="nav-child-group">

                                <div class="nav-child"
                                     onclick="window.location.href='{{ url($menu1->href) }}'">

                                    {{ $menu1['Keterangan'] }}

                                </div>

                                {{-- LEVEL 2 --}}
                                @if(count($menu1['child']) > 0)

                                    <div class="nav-subchildren">

                                        @foreach($menu1['child'] as $menu2)

                                            <div class="nav-subchild"
                                                 onclick="window.location.href='{{ url($menu2->href) }}'">

                                                ↳ {{ $menu2['Keterangan'] }}

                                            </div>

                                            {{-- LEVEL 3 --}}
                                            @if(count($menu2['child']) > 0)

                                                <div class="nav-level3">

                                                    @foreach($menu2['child'] as $menu3)

                                                        <div class="nav-level3-item"
                                                             onclick="window.location.href='{{ url($menu3->href) }}'">

                                                            ↳↳ {{ $menu3['Keterangan'] }}

                                                        </div>

                                                    @endforeach

                                                </div>

                                            @endif

                                        @endforeach

                                    </div>

                                @endif

                            </div>

                        @endforeach

                    </div>

                @endif

            </div>

        @endforeach

        <!-- <div class="nav-group" id="ng-berkas">
          <div class="nav-item" onclick="navModuleClick('berkas')">
            <span class="nav-icon"
              ><svg
                class=""
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  d="M21 8v13H3V8M1 3h22v5H1zM10 12h4"
                ></path></svg
            ></span>
            <span class="nav-label">Berkas</span>
            <span class="nav-chevron"
              ><svg
                class=""
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                viewBox="0 0 24 24"
              >
                <polyline points="9 18 15 12 9 6"></polyline></svg
            ></span>
          </div>
          <div class="nav-children">
            <div
              class="nav-child"
              onclick="
                navChildClick('berkas', 'setup-periode', 'Setup Periode Kerja')
              "
            >
              Setup Periode Kerja
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick('berkas', 'kunci-periode', 'Kunci Periode Kerja')
              "
            >
              Kunci Periode Kerja
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick(
                  'berkas',
                  'nomor-transaksi',
                  'Set Nomor Transaksi &amp; Perusahaan',
                )
              "
            >
              Set Nomor Transaksi &amp; Perusahaan
            </div>

            <div
              class="nav-child"
              onclick="navChildClick('berkas', 'menu', 'Menu')"
            >
              Menu
            </div>

            <div
              class="nav-child"
              onclick="navChildClick('berkas', 'set-pemakai', 'Set Pemakai')"
            >
              Set Pemakai
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick('berkas', 'set-pemakai-acc', 'Set Pemakai ACC')
              "
            >
              Set Pemakai ACC
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick(
                  'berkas',
                  'set-pemakai-non',
                  'Set Pemakai Non ACC',
                )
              "
            >
              Set Pemakai Non ACC
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick('berkas', 'ganti-password', 'Ganti Password')
              "
            >
              Ganti Password
            </div>
          </div>
        </div> -->

        <!-- <div class="nav-group" id="ng-master">
          <div class="nav-item" onclick="navModuleClick('master')">
            <span class="nav-icon"
              ><svg
                class=""
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"
                ></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path
                  stroke-linecap="round"
                  d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"
                ></path></svg
            ></span>
            <span class="nav-label">Master</span>
            <span class="nav-chevron"
              ><svg
                class=""
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                viewBox="0 0 24 24"
              >
                <polyline points="9 18 15 12 9 6"></polyline></svg
            ></span>
          </div>
          <div class="nav-children">
            <div
              class="nav-child"
              onclick="navChildClick('master', 'pelanggan', 'Data Pelanggan')"
            >
              Data Pelanggan
            </div>

            <div
              class="nav-child"
              onclick="navChildClick('master', 'pemasok', 'Data Pemasok')"
            >
              Data Pemasok
            </div>

            <div
              class="nav-child"
              onclick="navChildClick('master', 'produk', 'Data Produk')"
            >
              Data Produk
            </div>

            <div
              class="nav-child"
              onclick="navChildClick('master', 'satuan', 'Satuan Barang')"
            >
              Satuan Barang
            </div>

            <div
              class="nav-child"
              onclick="navChildClick('master', 'harga-jual', 'Harga Jual')"
            >
              Harga Jual
            </div>

            <div
              class="nav-child"
              onclick="navChildClick('master', 'pajak', 'Pajak')"
            >
              Pajak
            </div>

            <div
              class="nav-child"
              onclick="navChildClick('master', 'departemen', 'Departemen')"
            >
              Departemen
            </div>

            <div
              class="nav-child"
              onclick="navChildClick('master', 'wilayah', 'Wilayah')"
            >
              Wilayah
            </div>
          </div>
        </div>

        <div class="nav-group active open" id="ng-accounting">
          <div class="nav-item" onclick="navModuleClick('accounting')">
            <span class="nav-icon"
              ><svg
                class=""
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                viewBox="0 0 24 24"
              >
                <rect x="1" y="4" width="22" height="16" rx="2"></rect>
                <path stroke-linecap="round" d="M1 10h22"></path></svg
            ></span>
            <span class="nav-label">Accounting</span>
            <span class="nav-chevron"
              ><svg
                class=""
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                viewBox="0 0 24 24"
              >
                <polyline points="9 18 15 12 9 6"></polyline></svg
            ></span>
          </div>
          <div class="nav-children">
            <div
              class="nav-child active-child"
              onclick="
                navChildClick('accounting', 'akun-perkiraan', 'Akun Perkiraan')
              "
            >
              Akun Perkiraan
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick(
                  'accounting',
                  'pencatatan-beban',
                  'Pencatatan Beban',
                )
              "
            >
              Pencatatan Beban
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick(
                  'accounting',
                  'pencatatan-gaji',
                  'Pencatatan Gaji',
                )
              "
            >
              Pencatatan Gaji
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick('accounting', 'jurnal-umum', 'Jurnal Umum')
              "
            >
              Jurnal Umum
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick(
                  'accounting',
                  'monitor-anggaran',
                  'Monitor Anggaran',
                )
              "
            >
              Monitor Anggaran
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick(
                  'accounting',
                  'transfer-anggaran',
                  'Transfer Anggaran',
                )
              "
            >
              Transfer Anggaran
            </div>

            <div
              class="nav-child"
              onclick="navChildClick('accounting', 'anggaran', 'Anggaran')"
            >
              Anggaran
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick('accounting', 'histori-akun', 'Histori Akun')
              "
            >
              Histori Akun
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick('accounting', 'log-aktifitas', 'Log Aktifitas')
              "
            >
              Log Aktifitas
            </div>
          </div>
        </div>

        <div class="nav-group active open" id="ng-accounting">
          <div class="nav-item" onclick="navModuleClick('accounting')">
            <span class="nav-icon"
              ><svg
                class=""
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                viewBox="0 0 24 24"
              >
                <rect x="1" y="4" width="22" height="16" rx="2"></rect>
                <path stroke-linecap="round" d="M1 10h22"></path></svg
            ></span>
            <span class="nav-label">Accounting</span>
            <span class="nav-chevron"
              ><svg
                class=""
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                viewBox="0 0 24 24"
              >
                <polyline points="9 18 15 12 9 6"></polyline></svg
            ></span>
          </div>
          <div class="nav-children">
            <div
              class="nav-child active-child"
              onclick="
                navChildClick('accounting', 'akun-perkiraan', 'Akun Perkiraan')
              "
            >
              Akun Perkiraan
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick(
                  'accounting',
                  'pencatatan-beban',
                  'Pencatatan Beban',
                )
              "
            >
              Pencatatan Beban
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick(
                  'accounting',
                  'pencatatan-gaji',
                  'Pencatatan Gaji',
                )
              "
            >
              Pencatatan Gaji
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick('accounting', 'jurnal-umum', 'Jurnal Umum')
              "
            >
              Jurnal Umum
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick(
                  'accounting',
                  'monitor-anggaran',
                  'Monitor Anggaran',
                )
              "
            >
              Monitor Anggaran
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick(
                  'accounting',
                  'transfer-anggaran',
                  'Transfer Anggaran',
                )
              "
            >
              Transfer Anggaran
            </div>

            <div
              class="nav-child"
              onclick="navChildClick('accounting', 'anggaran', 'Anggaran')"
            >
              Anggaran
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick('accounting', 'histori-akun', 'Histori Akun')
              "
            >
              Histori Akun
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick('accounting', 'log-aktifitas', 'Log Aktifitas')
              "
            >
              Log Aktifitas
            </div>
          </div>
        </div>

        <div class="nav-group" id="ng-purchasing">
          <div class="nav-item" onclick="navModuleClick('purchasing')">
            <span class="nav-icon"
              ><svg
                class=""
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"
                ></path>
                <line
                  stroke-linecap="round"
                  x1="3"
                  y1="6"
                  x2="21"
                  y2="6"
                ></line>
                <path
                  stroke-linecap="round"
                  d="M16 10a4 4 0 01-8 0"
                ></path></svg
            ></span>
            <span class="nav-label">Purchasing</span>
            <span class="nav-chevron"
              ><svg
                class=""
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                viewBox="0 0 24 24"
              >
                <polyline points="9 18 15 12 9 6"></polyline></svg
            ></span>
          </div>
          <div class="nav-children">
            <div
              class="nav-child"
              onclick="
                navChildClick('purchasing', 'purchase-order', 'Purchase Order')
              "
            >
              Purchase Order
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick(
                  'purchasing',
                  'penerimaan-barang',
                  'Penerimaan Barang',
                )
              "
            >
              Penerimaan Barang
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick(
                  'purchasing',
                  'faktur-pembelian',
                  'Faktur Pembelian',
                )
              "
            >
              Faktur Pembelian
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick(
                  'purchasing',
                  'retur-pembelian',
                  'Retur Pembelian',
                )
              "
            >
              Retur Pembelian
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick(
                  'purchasing',
                  'bayar-pemasok',
                  'Pembayaran Pemasok',
                )
              "
            >
              Pembayaran Pemasok
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick(
                  'purchasing',
                  'permintaan-beli',
                  'Permintaan Pembelian',
                )
              "
            >
              Permintaan Pembelian
            </div>
          </div>
        </div>

        <div class="nav-group" id="ng-marketing">
          <div class="nav-item" onclick="navModuleClick('marketing')">
            <span class="nav-icon"
              ><svg
                class=""
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                viewBox="0 0 24 24"
              >
                <polyline
                  stroke-linecap="round"
                  points="23 6 13.5 15.5 8.5 10.5 1 18"
                ></polyline>
                <polyline
                  stroke-linecap="round"
                  points="17 6 23 6 23 12"
                ></polyline></svg
            ></span>
            <!-- Menu -->
            <!-- <span class="nav-label">Marketing</span>
            <span class="nav-chevron"
              ><svg
                class=""
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                viewBox="0 0 24 24"
              >
                <polyline points="9 18 15 12 9 6"></polyline></svg
            ></span>
          </div>
          <div class="nav-children"> -->
            <!-- submenu -->
            <!-- <div
              class="nav-child"
              onclick="navChildClick('marketing', 'quotation', 'Quotation')"
            >
              Quotation
            </div>

            <div
              class="nav-child"
              onclick="navChildClick('marketing', 'sales-order', 'Sales Order')"
            >
              Sales Order
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick('marketing', 'pengiriman', 'Pengiriman Barang')
              "
            >
              Pengiriman Barang
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick('marketing', 'faktur-jual', 'Faktur Penjualan')
              "
            >
              Faktur Penjualan
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick('marketing', 'retur-jual', 'Retur Penjualan')
              "
            >
              Retur Penjualan
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick('marketing', 'terima-kas', 'Penerimaan Kas')
              "
            >
              Penerimaan Kas
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick(
                  'marketing',
                  'diskon-promosi',
                  'Diskon &amp; Promosi',
                )
              "
            >
              Diskon &amp; Promosi
            </div>
          </div>
        </div> -->

        <!-- <div class="nav-group" id="ng-gudang">
          <div class="nav-item" onclick="navModuleClick('gudang')">
            <span class="nav-icon"
              ><svg
                class=""
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"
                ></path>
                <path stroke-linecap="round" d="M9 22V12h6v10"></path></svg
            ></span>
            <span class="nav-label">Gudang</span>
            <span class="nav-chevron"
              ><svg
                class=""
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                viewBox="0 0 24 24"
              >
                <polyline points="9 18 15 12 9 6"></polyline></svg
            ></span>
          </div>
          <div class="nav-children">
            <div
              class="nav-child"
              onclick="navChildClick('gudang', 'stok-barang', 'Stok Barang')"
            >
              Stok Barang
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick('gudang', 'transfer-gudang', 'Transfer Gudang')"
            >
              Transfer Gudang
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick('gudang', 'penyesuaian', 'Penyesuaian Stok')
              "
            >
              Penyesuaian Stok
            </div>

            <div
              class="nav-child"
              onclick="navChildClick('gudang', 'produksi', 'Produksi')"
            >
              Produksi
            </div>

            <div
              class="nav-child"
              onclick="navChildClick('gudang', 'opname', 'Opname')"
            >
              Opname
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick('gudang', 'lokasi-gudang', 'Lokasi Gudang')
              "
            >
              Lokasi Gudang
            </div>
          </div>
        </div>

        <div class="nav-group" id="ng-report">
          <div class="nav-item" onclick="navModuleClick('report')">
            <span class="nav-icon"
              ><svg
                class=""
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  d="M18 20V10M12 20V4M6 20v-6"
                ></path></svg
            ></span>
            <span class="nav-label">Report</span>
            <span class="nav-chevron"
              ><svg
                class=""
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                viewBox="0 0 24 24"
              >
                <polyline points="9 18 15 12 9 6"></polyline></svg
            ></span>
          </div>
          <div class="nav-children">
            <div
              class="nav-child"
              onclick="
                navChildClick('report', 'laba-rugi', 'Laporan Laba Rugi')
              "
            >
              Laporan Laba Rugi
            </div>

            <div
              class="nav-child"
              onclick="navChildClick('report', 'neraca', 'Neraca')"
            >
              Neraca
            </div>

            <div
              class="nav-child"
              onclick="navChildClick('report', 'arus-kas', 'Arus Kas')"
            >
              Arus Kas
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick('report', 'lap-penjualan', 'Lap. Penjualan')
              "
            >
              Lap. Penjualan
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick('report', 'lap-pembelian', 'Lap. Pembelian')
              "
            >
              Lap. Pembelian
            </div>

            <div
              class="nav-child"
              onclick="navChildClick('report', 'lap-stok', 'Laporan Stok')"
            >
              Laporan Stok
            </div>

            <div
              class="nav-child"
              onclick="navChildClick('report', 'lap-pajak', 'Laporan Pajak')"
            >
              Laporan Pajak
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick('report', 'analisa-piutang', 'Analisa Piutang')
              "
            >
              Analisa Piutang
            </div>

            <div
              class="nav-child"
              onclick="
                navChildClick('report', 'analisa-hutang', 'Analisa Hutang')
              "
            >
              Analisa Hutang
            </div>
          </div>
        </div> -->
      </nav>
    </aside>

    <!-- MAIN -->
    <div class="main">
      <!-- PAGE1 -->
      <header class="header">
        <div class="breadcrumb" id="breadcrumb">
          <span
            style="cursor: pointer; color: var(--blue)"
            onclick="navModuleClick('accounting')"
            >Beranda</span
          >
          <span class="bc-sep">›</span>

          <span
            style="cursor: pointer; color: var(--blue)"
            onclick="window.location.href = '#'"
            >Purchasing</span
          >
          <span class="bc-sep">›</span>
          <b>Permintaan Pembelian Non-Agen</b>
        </div>
        <div class="header-right">
          <div class="period-badge">
            Periode:
            {{
      [
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
      ][$periode->bulan] ?? ''
  }}
             {!! $periode->tahun !!}</div>
          <div id="avatar" class="avatar">{{\Auth::user()->username[0] }}</div>
        </div>
      </header>
      <div class="content" id="content">@yield('content')</div>
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
    <!-- <script type="text/javascript" src="http://www.example.co.uk/assets/js/autoNumeric.js"></script> -->

    <script type="text/javascript">
      document.onkeydown = function (e) {
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
      $(document).on("hidden.bs.modal", ".modal", function () {
        $(".modal:visible").length && $(document.body).addClass("modal-open");
      });
      $(".modal").modal({ show: false, keyboard: false, backdrop: "static" });
      $("title").html($("#title_page").html());
      $(function () {
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
      function round(value, precision) {
        var multiplier = Math.pow(10, precision || 0);
        return Math.round(value * multiplier) / multiplier;
      }
      function middleTD() {}
      function format_date(date) {
        if (date == "" || date == null) {
          return "";
        }
        return (
          date.split("-")[2] +
          "/" +
          date.split("-")[1] +
          "/" +
          date.split("-")[0]
        );
      }
      function format_timestamp(date) {
        if (date == "" || date == null) return "";
        tgl = date.split(" ")[0];
        waktu = date.split(" ")[1];
        return (
          tgl.split("-")[2] +
          "/" +
          tgl.split("-")[1] +
          "/" +
          tgl.split("-")[0] +
          " " +
          waktu
        );
      }
      // $(function() {
      //     new AutoNumeric('.format-Rp', {
      //         currencySymbol : ' Rp',
      //         decimalCharacter : ',',
      //         digitGroupSeparator : '.',
      //     });
      // });
    </script>
    <script src="https://unpkg.com/gridjs/dist/gridjs.umd.js"></script>

    @yield('js')
  </body>
</html>
