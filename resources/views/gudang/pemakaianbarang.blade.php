@extends('newmasterTest')
@section('buttons')
@endsection
@section('page-title', 'Pemakaian Barang')

@section('css')
    {{-- Layout newmasterTest (sama seperti gudang/ubahkemasanbarang.blade.php) sudah memuat
         newmaster.css + po-table-header.css, dan TIDAK memuat tableMaster2.css — tampilan tabel
         daftar diambil dari blok .po-list-page di po-table-header.css (class-nya dipasang di
         div#pageHome), tombol di modal Add/Detail/Koreksi dari blok .po-form-page.
         report-table.css dimuat di sini (dulu dari layout gudang) untuk tabel item di modal
         (.tb-report) dan modal filter (.rt-filter). --}}
    <link rel="stylesheet"
        href="{!! URL::asset('css/report-table.css') !!}?v={{ @filemtime(base_path('public/css/report-table.css')) ?: '1' }}">

    {{-- Dimuat ULANG setelah report-table.css supaya .po-* / .rt-* versi po-table-header.css menang
         saat spesifisitas seri. --}}
    <link rel="stylesheet"
        href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
    <link rel="stylesheet"
        href="{!! URL::asset('css/scrollbar-autohide.css') !!}?v={{ @filemtime(base_path('public/css/scrollbar-autohide.css')) ?: '1' }}">

    <style>
        /* Desain tab disalin dari accounting/bonsementara.blade.php: pill group di latar abu,
           tab aktif biru solid dengan shadow, di kartu kecil sendiri di atas kartu tabel. */
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

        /* layout newmasterTest punya rule .card global (align-items:center) yang override ini */
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

        /* Blok .po-list-page di po-table-header.css hanya menulis kolom Aksi / min-width untuk
           id #tabel — tabel tab kedua (#tabelRetur) diberi aturan yang sama di sini. :where()
           supaya spesifisitasnya persis sama dengan aturan #tabel di sana. */
        :where(#pageHome) #tabelRetur { min-width: 100%; }

        :where(#pageHome) #tabelRetur td:first-child:not([colspan]) { vertical-align: middle; }

        :where(#pageHome) #tabelRetur td:first-child .po-aksi-wrap {
            display: flex;
            gap: 4px;
            justify-content: center;
            align-items: center;
        }

        :where(#pageHome) #tabelRetur td:first-child .btn {
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

        :where(#pageHome) #tabelRetur td:first-child .btn:hover {
            filter: brightness(0.97);
            transform: translateY(-1px);
        }

        :where(#pageHome) #tabelRetur td:first-child .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
        :where(#pageHome) #tabelRetur td:first-child .btn-warning { color: #b45309; border-color: #fbe3bd; background: #fef3e0; }
        :where(#pageHome) #tabelRetur td:first-child .btn-primary { color: #2563eb; border-color: #cfdcff; background: #e8edff; }
        :where(#pageHome) #tabelRetur td:first-child .btn-danger  { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }
        :where(#pageHome) #tabelRetur td:first-child .btn-info    { color: #0891b2; border-color: #a5f3fc; background: #ecfeff; }
    </style>

    {{-- tampilan search bar modal add pelanggan --}}
    <style>
        #tabel_add_list_pelanggan_filter {
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
        #tabel_add_list_sales_filter {
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
            margin-top: -45px;
            display: inline-block;
            vertical-align: middle;
        }
    </style>
    {{-- end tampilan search modal barang all --}}
@endsection
@section('content')
    <div id="imagecontainer" class="d-none" style="">
        <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
    </div>

    <div id="printContainer" style="display:none">

    </div>
    <div id="contentContainer" class="container-fluid">

        <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
        <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />

        <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
        <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS !!}" />
        <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
        <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />

        <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

        {{-- Kartu + toolbar + tabel mengikuti gudang/ubahkemasanbarang.blade.php (skema po-*),
             tab pill di kartu kecil sendiri seperti accounting/bonsementara.blade.php.
             Dua dataset berbeda (PPI outstanding vs dokumen Pemakaian Barang yang sudah dibuat),
             bukan filter belum/sudah otorisasi dari satu list, jadi tetap dua tab. Satu toolbar
             dipakai bersama: loadAll() mengembalikan kedua dataset dalam satu request, dan
             cari/Tampilkan selalu berlaku ke tab yang sedang aktif. --}}
        <div id="pageHome" class="container-fluid po-list-page">

            {{-- class "nav" WAJIB ada — Bootstrap Tab plugin mencari kontainer aktif lewat
                 closest(".nav, .list-group") untuk tahu tab mana yang harus dilepas active-nya. --}}
            <div class="card mb-3 tab-card">
                <div class="card-body">
                    <div class="nav nav-tabs border-0 custom-tabs" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home"
                            role="tab" aria-controls="home" aria-selected="true">Outstanding Permintaan Pemakaian</a>
                        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile"
                            role="tab" aria-controls="profile" aria-selected="false">Pemakaian Barang</a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body" style="padding:0;">

                    <div class="po-toolbar">
                        {{-- Rentang tanggal — berlaku untuk kedua tab sekaligus (lihat ikatPeriode()).
                             Server tetap tahu periode aktif lewat #periode_bulan/#periode_tahun untuk
                             validasi tanggal saat Add/Koreksi. --}}
                        <div class="po-filter-wrap">
                            <label>Periode</label>
                            <input type="date" class="po-filter-inp" id="inputDate1" value="{!! $date1 !!}">
                            <span class="po-filter-sep">s/d</span>
                            <input type="date" class="po-filter-inp" id="inputDate2" value="{!! $date2 !!}">
                        </div>

                        <input class="po-search-inp" type="search" id="searchBox" placeholder="Cari data"
                            oninput="onToolbarSearch()">

                        {{-- Jumlah baris per halaman untuk tab yang sedang aktif. -1 = tampilkan semua
                             data (tanpa pager) — lihat onLenChange() di bawah. --}}
                        <div class="po-len-wrap">
                            <label for="tabelLen">Tampilkan</label>
                            <select id="tabelLen" class="po-len-inp" onchange="onLenChange()">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="-1">Semua</option>
                            </select>
                        </div>

                        {{-- Filter hanya relevan untuk tab "Pemakaian Barang" (Otorisasi/Status ada di
                             sana) — disembunyikan saat tab Outstanding aktif, lihat shown.bs.tab. --}}
                        <button type="button" id="btnFilter" class="po-btn-filter" style="display:none"
                            onclick="$('#modalFilter').modal('show')">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                    </div>

                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="nav-home-tab">
                            <table id="tabel" class="data-table po-aksi-hover">
                                <thead>
                                    <tr>
                                        <th style="padding: 4px 12px;" scope="col">Actions</th>
                                        <th style="padding: 4px 12px;" scope="col">No.Perintah</th>
                                        <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                                        <th style="padding: 4px 12px;" scope="col">Gudang</th>
                                    </tr>
                                </thead>
                                {{-- Diisi oleh renderOutstanding() (lihat @section('js')). --}}
                                <tbody id="tabel_data" class="text-left"></tbody>
                            </table>
                        </div>

                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                            <table id="tabelRetur" class="data-table po-aksi-hover">
                                <thead>
                                    <tr>
                                        <th style="padding: 4px 12px;" scope="col">Actions</th>
                                        <th style="padding: 4px 12px;" scope="col">No.Bukti</th>
                                        <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                                        <th style="padding: 4px 12px;" scope="col">No.Perintah</th>
                                        <th style="padding: 4px 12px;" scope="col">Gudang</th>
                                        <th style="padding: 4px 12px;" scope="col">Status</th>
                                        <th style="padding: 4px 12px;" scope="col">Oto</th>
                                        <th style="padding: 4px 12px;" scope="col">User Oto</th>
                                        <th style="padding: 4px 12px;" scope="col">Tgl Oto</th>
                                    </tr>
                                </thead>
                                {{-- Diisi oleh renderPenerimaan() (lihat @section('js')). --}}
                                <tbody id="tabelRetur_data" class="text-left"></tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- modal filter — DILETAKKAN DI LUAR .tb-report supaya reset `.tb-report *{margin:0;padding:0}`
     di report-table.css tidak merusak padding/margin modal Bootstrap. Hanya relevan untuk tab
     "Pemakaian Barang" (Otorisasi/Status ada di sana) — tombol Filter disembunyikan saat tab
     "Outstanding Permintaan Pemakaian" aktif, lihat JS. --}}
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
                            <div>
                                <label class="rt-field-label" for="modalStatus">Status</label>
                                <select class="rt-native" id="modalStatus">
                                    <option value="2">Semua</option>
                                    <option value="1">Terkirim</option>
                                    <option value="0">Belum Terkirim</option>
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

    <!-- start modal add -->
    <div class="modal fade po-form-page" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" style="width: 90%; max-width:1500px;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- <h1>Tes Modal</h1> -->

                    <div class="container-fluid">
                        <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />
                        <div class="row">
                            <!-- No Bukti Out -->
                            <div class="col-md-4">
                                <div class="row align-items-center">
                                    <label class="col-sm-4" style="white-space: nowrap; width: 120px; margin-top:10px;">No
                                        Bukti Out</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="input_add_noout"
                                            placeholder="No Bukti Out" disabled>
                                    </div>
                                </div>
                            </div>

                            <!-- No PBG -->
                            <div class="col-md-4">
                                <div class="row align-items-center">
                                    <label class="col-sm-4" style="white-space: nowrap; width: 120px; margin-top:10px;">No
                                        Pemakaian</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="input_add_nopbg"
                                            placeholder="No Pemakaian" disabled>
                                    </div>
                                </div>
                            </div>

                            <!-- Gdg -->
                            <div class="col-md-4">
                                <div class="row align-items-center">
                                    <label class="col-sm-4"
                                        style="white-space: nowrap; width: 120px; margin-top:10px;">Gudang</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="input_add_gdg"
                                            placeholder="Gudang Asal" required disabled>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <!-- Tanggal -->
                            <div class="col-md-4">
                                <div class="row align-items-center">
                                    <label class="col-sm-4"
                                        style="white-space: nowrap; width: 120px; margin-top:10px;">Tanggal</label>
                                    <div class="col-sm-8">
                                        <input type="date" class="form-control" id="input_add_tanggal"
                                            value="{!! date('Y-m-d') !!}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tb-report container-fluid mt-2">
                    <div class="table-outer">
                        <div class="table-wrap" style="max-height:40vh;">
                            <table id="addTable" class="tb">
                                <thead>
                                    <tr>
                                        <th scope="col">Terima</th>
                                        <th scope="col">Kode Barang</th>
                                        <th scope="col">Nama Barang</th>
                                        <th scope="col">Qty</th>
                                        {{-- <th scope="col">Qty OS</th> --}}
                                        <th scope="col">Satuan</th>
                                        <th scope="col">Qty Kirim </th>
                                    </tr>
                                </thead>
                                <tbody id="addTableData" class="text-right">
                                    <tr>
                                        <td class="text-center"><input class="" type="checkbox" value=""
                                                id="flexCheckDefault"></td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                        {{-- <td>-</td> --}}
                                        <td>-</td>
                                        <td>-</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="contentContainer">
                    <button type="button" class="btn btn-outline-danger btn-lg" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary btn-lg" onclick="submitAdd()">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End modal add-->

    <!-- start modal detail -->
    <div class="modal fade po-form-page" id="formDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" style="width: 90%; max-width:1500px;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Detail</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- <h1>Tes Modal</h1> -->

                    <div class="container-fluid p-0 m-0">
                        <div class="row">
                            <!-- Kolom 1: Gdg -->
                            <div class="col-md-4">
                                <div class="row align-items-center mb-2">
                                    <div class="col-4 text-end">
                                        <label for="input_detail_gdg" class="form-label mb-0">Gudang</label>
                                    </div>
                                    <div class="col-8">
                                        <input type="text" class="form-control" id="input_detail_gdg"
                                            placeholder="Gudang Asal" required disabled>
                                    </div>
                                </div>
                            </div>

                            <!-- Kolom 2: No Out -->
                            <div class="col-md-4">
                                <div class="row align-items-center mb-2">
                                    <div class="col-4 text-end">
                                        <label for="input_detail_noout" class="form-label mb-0">No Perintah</label>
                                    </div>
                                    <div class="col-8">
                                        <input type="text" class="form-control" id="input_detail_noout"
                                            placeholder="No Perintah" disabled>
                                    </div>
                                </div>
                            </div>

                            <!-- Kolom 3: Tanggal -->
                            <div class="col-md-4">
                                <div class="row align-items-center mb-2">
                                    <div class="col-4 text-end">
                                        <label for="input_detail_tanggal" class="form-label mb-0">Tanggal</label>
                                    </div>
                                    <div class="col-8">
                                        <input type="date" class="form-control" id="input_detail_tanggal"
                                            value="{!! date('Y-m-d') !!}" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                </div>
                <div class="tb-report container-fluid mt-2">
                    <div class="table-outer">
                        <div class="table-wrap" style="max-height:40vh;">
                            <table id="detailTable" class="tb">
                                <thead>
                                    <tr>
                                        <th scope="col">Kode Barang</th>
                                        <th scope="col">Nama Barang</th>
                                        <th scope="col">Qty</th>
                                        <th scope="col">Satuan</th>
                                    </tr>
                                </thead>


                                <tbody id="detailTableData" class="text-right">
                                    <tr>
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
                <div class="modal-footer" id="contentContainer">
                    <button type="button" class="btn btn-danger btn-action-danger btn-pill-primary" data-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End modal detail-->

    <!-- start modal koreksi -->
    <div class="modal fade po-form-page" id="formKoreksi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" style="width: 90%; max-width:1500px;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Koreksi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <!-- Kolom 1 -->
                            <div class="col-md-4">
                                <div class="row align-items-center mb-2">
                                    <div class="col-md-4">
                                        <label for="input_koreksi_nopbg" class="form-label">No Pemakaian</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="input_koreksi_nopbg"
                                            placeholder="No Pemakaian" disabled>
                                    </div>
                                </div>
                                <div class="row align-items-center mb-2">
                                    <div class="col-md-4">
                                        <label for="input_koreksi_noout" class="form-label">No Perintah</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="input_koreksi_noout"
                                            placeholder="No Perintah" disabled>
                                    </div>
                                </div>
                            </div>

                            <!-- Kolom 2 -->
                            <div class="col-md-4">
                                <div class="row align-items-center mb-2">
                                    <div class="col-md-4">
                                        <label for="input_koreksi_gdg" class="form-label">Gudang</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="input_koreksi_gdg"
                                            placeholder="Gudang" required disabled>
                                    </div>
                                </div>
                            </div>

                            <!-- Kolom 3 -->
                            <div class="col-md-4">
                                <div class="row align-items-center mb-2">
                                    <div class="col-md-4">
                                        <label for="input_koreksi_tanggal" class="form-label">Tanggal</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="date" class="form-control" id="input_koreksi_tanggal"
                                            value="{!! date('Y-m-d') !!}" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container-fluid">
                        <div class="row ">
                            <div class="col-md-12 text-right">
                                <!-- <button type="button" class="btn btn-primary" onclick="buttonKoreksiAdd()" class="btn btn-secondary"  >Add Item</button> -->
                            </div>
                            <div class="container-fluid">
                                <!-- koreksi add -->
                                <div id="formKoreksiAdd" class="container-fluid showhide">
                                    <div class="line"></div>
                                    <div class="row">
                                        <div class="col-12">
                                            <h4>Add Item</h4>
                                        </div>
                                    </div>


                                    <div class="container-fluid">
                                        <div class="row">
                                            <!-- Kolom 1 -->
                                            <div class="col-md-4">
                                                <div class="row align-items-center mb-2">
                                                    <div class="col-md-4">
                                                        <label for="koreksiAddSelect" class="form-label">Pilih
                                                            Barang</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <select onchange="changeSelectKoreksiAdd()" id="koreksiAddSelect"
                                                            class="form-control" aria-label="Pilih Barang">
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row align-items-center mb-2">
                                                    <div class="col-md-4">
                                                        <label for="koreksiAddSatuan" class="form-label">Satuan</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" id="koreksiAddSatuan" value="PCS"
                                                            class="form-control" disabled>
                                                    </div>
                                                </div>
                                                <div class="row align-items-center">
                                                    <div class="col-md-4">
                                                        <label for="koreksiAddQtyPO" class="form-label">Qty PO</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input id="koreksiAddQtyPO" type="number" value="0.00"
                                                            class="form-control text-right" disabled>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Kolom 2 -->
                                            <div class="col-md-4">
                                                <div class="row align-items-center mb-2">
                                                    <div class="col-md-4">
                                                        <label for="koreksiAddNamaBrg" class="form-label">Nama
                                                            Barang</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input id="koreksiAddNamaBrg" type="text" class="form-control"
                                                            disabled>
                                                    </div>
                                                </div>
                                                <div class="row align-items-center mb-2">
                                                    <div class="col-md-4">
                                                        <label for="koreksiAddInputQty" class="form-label">Qty</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input id="koreksiAddInputQty" type="number" value="0.00"
                                                            class="form-control text-right">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Kolom 3 -->
                                            <div class="col-md-4">
                                                <div class="row align-items-center mb-2">
                                                    <div class="col-md-4">
                                                        <label for="koreksiAddKodeBrg" class="form-label">Kode
                                                            Barang</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input id="koreksiAddKodeBrg" type="text" class="form-control"
                                                            disabled>
                                                    </div>
                                                </div>
                                                {{-- <div class="row align-items-center mb-2">
                    <div class="col-md-4">
                      <label for="koreksiAddQtyOS" class="form-label">Qty OS</label>
                    </div>
                    <div class="col-md-8">
                      <input id="koreksiAddQtyOS" type="number" value="0.00" class="form-control text-right" disabled>
                    </div>
                  </div> --}}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">


                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12 text-right">

                                            <button type="button" class="btn btn-pill-secondary"
                                                onclick="buttonBatalShowHide()">Batal</button>
                                            <button id="" type="button" onclick="submitAddKoreksi()"
                                                class="btn btn-pill-primary">Simpan</button>

                                        </div>

                                    </div>
                                    <div class="line"></div>
                                </div>

                                <!-- koreksi edit -->

                                <div class="tb-report container-fluid mt-4">
                                    <div class="table-outer">
                                        <div class="table-wrap" style="max-height:40vh;">
                                            <table id="koreksiTable" class="tb">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Kode Barang</th>
                                                        <th scope="col">Nama Barang</th>
                                                        <th scope="col">Qty</th>
                                                        <th scope="col">Satuan</th>
                                                        <th class="rt-fixed-th" scope="col">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="koreksiTableData" class="text-right">
                                                    <tr>

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
                                <div id="formKoreksiEdit" class="container-fluid showhide">
                                    <div class="line"></div>
                                    <div class="row">
                                        <div class="col-12">
                                            <h4>Edit Item</h4>
                                        </div>
                                    </div>

                                    <div class="container-fluid">
                                        <div class="row">
                                            <!-- Kolom 1 -->
                                            <div class="col-md-4">
                                                <div class="row align-items-center mb-2">
                                                    <div class="col-md-4">
                                                        <label for="koreksiEditKodeBrg" class="form-label">Kode
                                                            Barang</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input id="koreksiEditKodeBrg" type="text"
                                                            class="form-control" disabled>
                                                    </div>
                                                </div>
                                                <div class="row align-items-center mb-2">
                                                    <div class="col-md-4">
                                                        <label for="koreksiEditNamaBrg" class="form-label">Nama
                                                            Barang</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input id="koreksiEditNamaBrg" type="text"
                                                            class="form-control" disabled>
                                                    </div>
                                                </div>
                                                {{-- <div class="row align-items-center mb-2">
                    <div class="col-md-4">
                      <label for="koreksiEditInputQty" class="form-label">Qty</label>
                    </div>
                    <div class="col-md-8">
                      <input id="koreksiEditInputQty" type="number" value="0.00" class="form-control text-right">
                    </div>
                  </div> --}}
                                            </div>

                                            <!-- Kolom 2 -->
                                            <div class="col-md-4">
                                                <div class="row align-items-center mb-2">
                                                    <div class="col-md-4">
                                                        <label for="koreksiEditSatuan" class="form-label">Satuan</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" id="koreksiEditSatuan" value="PCS"
                                                            class="form-control text-center" disabled>
                                                    </div>
                                                </div>
                                                <div class="row align-items-center mb-2">
                                                    <div class="col-md-4">
                                                        <label for="koreksiEditInputQty" class="form-label">Qty</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input id="koreksiEditInputQty" type="number" value="0.00"
                                                            class="form-control text-right">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Kolom 3 -->
                                            <div class="col-md-4">
                                                <!-- <div class="row align-items-center mb-2">
                            <div class="col-md-4">
                              <label for="koreksiEditInputQty" class="form-label">Qty</label>
                            </div>
                            <div class="col-md-8">
                              <input id="koreksiEditInputQty" type="number" value="0.00" class="form-control text-right">
                            </div>
                          </div> -->
                                                <!-- <div class="row align-items-center mb-2">
                            <div class="col-md-4">
                              <label for="koreksiEditQtyOS" class="form-label">Qty OS</label>
                            </div>
                            <div class="col-md-8">
                              <input id="koreksiEditQtyOS" type="number" value="0.00" class="form-control text-right" disabled>
                            </div>
                          </div> -->
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-md-12 text-right" id="contentContainer">
                                            <button type="button" class="btn btn-action-danger btn-danger btn-pill-primary"
                                                onclick="buttonBatalShowHide()">Batal</button>
                                            <button id="" type="button" onclick="submitEditKoreksi()"
                                                class="btn btn-action-primary btn-primary btn-pill-primary">Simpan</button>
                                        </div>
                                    </div>
                                    <div class="line"></div>
                                </div>
                                <div class="row">
                                </div>
                                <div class="row">
                                </div>
                            </div>
                            <!-- end  -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- End modal koreksi-->

    <!-- start modal detail koreksi -->
    <div class="modal fade po-form-page" id="formKoreksiDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" style="width: 90%; max-width:1500px;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Detail</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <!-- No PBG -->
                            <div class="col-md-4">
                                <div class="row align-items-center">
                                    <label class="col-sm-4" style="white-space: nowrap; width: 120px; margin-top:10px;">No
                                        Pemakaian</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="input_koreksidetail_nopbg"
                                            placeholder="No Pemakaian" disabled>
                                    </div>
                                </div>
                            </div>
                            <!-- No OUT -->
                            <div class="col-md-4">
                                <div class="row align-items-center">
                                    <label class="col-sm-4" style="white-space: nowrap; width: 120px; margin-top:10px;">No
                                        Perintah</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="input_koreksidetail_noout"
                                            placeholder="No Perintah" disabled>
                                    </div>
                                </div>
                            </div>
                            <!-- Gdg -->
                            <div class="col-md-4">
                                <div class="row align-items-center">
                                    <label class="col-sm-4"
                                        style="white-space: nowrap; width: 120px; margin-top:10px;">Gudang</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="input_koreksidetail_gdg"
                                            placeholder="Gudang" required disabled>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <!-- Tanggal -->
                            <div class="col-md-4">
                                <div class="row align-items-center">
                                    <label class="col-sm-4"
                                        style="white-space: nowrap; width: 120px; margin-top:10px;">Tanggal</label>
                                    <div class="col-sm-8">
                                        <input type="date" class="form-control" id="input_koreksidetail_tanggal"
                                            value="{!! date('Y-m-d') !!}" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container-fluid">
                        <div class="row ">
                            <div class="col-md-12 text-right">
                            </div>
                            <div class="tb-report container-fluid mt-4">
                                <div class="table-outer">
                                    <div class="table-wrap" style="max-height:40vh;">
                                        <table id="koreksiDetailTable" class="tb">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Kode Barang</th>
                                                    <th scope="col">Nama Barang</th>
                                                    <th scope="col">Qty</th>
                                                    <th scope="col">Satuan</th>
                                                </tr>
                                            </thead>
                                            <tbody id="koreksiDetailTableData" class="text-right">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End modal detail koreksi-->
@endsection

@section('js')
    <script type="text/javascript">
        let addDataArray = []

        let koreksiPenerimaanArray = []
        let koreksiDataEdit = {}
        let koreksiDataAddList = []

        // paint pertama tanpa AJAX; loadAll() menyegarkan setelahnya — satu-satunya sumber
        // data dipakai bareng oleh render pertama dan setiap refresh (lihat renderOutstanding()/
        // renderPenerimaan() di bawah), jadi tidak ada lagi drift antara markup Blade dan JS.
        let outstandingRows = @json($outstandingArray);
        let penerimaanRows = @json($penerimaanArray);

        // Jumlah baris per halaman per tabel (DataTables page.len) — dikunci per id tabel
        // supaya dropdown #tabelLen bisa disinkronkan ke tab yang sedang aktif saat berpindah tab.
        // -1 = "Semua", sama seperti konvensi bawaan DataTables untuk page.len().
        let panjangHalaman = {
            tabel: 10,
            tabelRetur: 10
        };

        // dom sama seperti ubahkemasanbarang.blade.php: DataTables membuat sendiri kotak scroll
        // .po-table-wrap (header sticky) + baris info & pager. Kotak cari & dropdown Tampilkan
        // bawaan tetap mati — diganti #searchBox/#tabelLen di toolbar bersama (lihat
        // activeTable()/onToolbarSearch()/onLenChange() di bawah).
        const dtOptionsOutstanding = {
            dom: "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            order: [
                [1, 'asc']
            ],
            lengthChange: false,
            paging: true,
            language: {
                emptyTable: 'Tidak ada data',
                zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
            },
            columnDefs: [{
                    type: 'date',
                    targets: [2]
                },
                {
                    className: 'text-center',
                    targets: [0],
                    orderable: false
                }
            ]
        };

        const dtOptionsPenerimaan = {
            dom: "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            order: [
                [1, 'asc']
            ],
            lengthChange: false,
            paging: true,
            language: {
                emptyTable: 'Tidak ada data',
                zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
            },
            columnDefs: [{
                    type: 'date',
                    targets: [2]
                },
                {
                    className: 'text-center',
                    targets: [0],
                    orderable: false
                }
            ]
        };

        // Filter Otorisasi/Status (tab "Pemakaian Barang" saja) — dibaca dari atribut data-oto /
        // data-os yang ditulis di <tr>, bukan dari teks badge yang sudah dirender.
        let globalOtorisasi = "2"; // 2=Semua, 1=Sudah Otorisasi, 0=Belum Otorisasi
        let globalStatus = "2"; // 2=Semua, 1=Terkirim, 0=Belum Terkirim

        // Didaftarkan SEKALI saja (ext.search adalah array global) — kalau didaftarkan ulang tiap
        // loadAll() dipanggil, filter akan dobel/berlipat karena predicate lama tidak pernah dibuang.
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'tabelRetur') {
                return true;
            }

            const row = settings.aoData[dataIndex].nTr;
            const oto = $(row).attr('data-oto');
            const os = Number($(row).attr('data-os') || 0);

            if (globalOtorisasi !== '2' && oto !== globalOtorisasi) {
                return false;
            }
            if (globalStatus === '1' && os > 0) {
                return false;
            }
            if (globalStatus === '0' && os <= 0) {
                return false;
            }
            return true;
        });

        // Id tabel DataTable yang sedang terlihat ('tabel' atau 'tabelRetur') — dipakai toolbar
        // search & dropdown Tampilkan supaya satu kontrol mengurus tab manapun yang sedang aktif.
        function activeTableId() {
            return $('.tab-pane.active table').eq(0).attr('id');
        }

        function activeTable() {
            return $('#' + activeTableId()).DataTable();
        }

        function onToolbarSearch() {
            activeTable().search($('#searchBox').val() || '').draw();
        }

        // Dropdown "Tampilkan" — ganti jumlah baris/halaman tabel yang sedang aktif lalu balik
        // ke halaman 1 (page.len().draw() bawaan DataTables sudah melakukan ini sendiri).
        function onLenChange() {
            const tableId = activeTableId();
            const v = Number(document.getElementById('tabelLen').value);
            const n = (v === -1 || v > 0) ? v : 10;
            panjangHalaman[tableId] = n;
            $('#' + tableId).DataTable().page.len(n).draw();
        }

        // Rentang yang belum lengkap tidak memicu request, dan urutan tanggal terbalik ditolak
        // dengan peringatan — sama seperti ikatPeriode() di ubahkemasanbarang.blade.php.
        function ikatPeriode() {
            let awal = document.getElementById('inputDate1');
            let akhir = document.getElementById('inputDate2');
            if (!awal || !akhir || awal.dataset.rtBound) {
                return;
            }
            awal.dataset.rtBound = '1';

            let onUbah = function() {
                if (!awal.value || !akhir.value) {
                    return;
                }
                if (awal.value > akhir.value) {
                    alertify.warning('Tanggal awal tidak boleh melebihi tanggal akhir');
                    return;
                }
                loadAll();
            };
            awal.addEventListener('change', onUbah);
            akhir.addEventListener('change', onUbah);
        }

        // Tinggi tabel tab yang sedang aktif mengikuti sisa ruang layar — disalin dari
        // aturTinggiTabel() di ubahkemasanbarang.blade.php. Tabel di tab tersembunyi tidak bisa
        // diukur, jadi dihitung ulang setiap kali tab dibuka (lihat shown.bs.tab).
        function aturTinggiTabel() {
            let page = document.getElementById('pageHome');
            if (!page || page.offsetParent === null) {
                return;
            }

            let area = document.getElementById('content');
            let wrap = document.querySelector('#pageHome .tab-pane.active .po-table-wrap');
            if (!area || !wrap) {
                return;
            }

            wrap.style.maxHeight = 'none';

            let padBawah = parseFloat(getComputedStyle(area).paddingBottom) || 0;
            let batasBawah = area.getBoundingClientRect().bottom - padBawah;
            let kotak = wrap.getBoundingClientRect();
            let bawah = page.getBoundingClientRect().bottom - kotak.bottom;

            let sisa = batasBawah - kotak.top - bawah - 4;
            wrap.style.maxHeight = Math.max(200, Math.floor(sisa)) + 'px';
        }

        /* -- FILTER MODAL (Otorisasi: Semua/Sudah Otorisasi/Belum, Status: Semua/Terkirim/Belum Terkirim) -- */
        function updateFilterBadge() {
            let count = ($('#modalOtorisasi').val() !== '2') ? 1 : 0;
            count += ($('#modalStatus').val() !== '2') ? 1 : 0;
            $('#filterBadge').text(count + ' aktif');
        }

        function resetAllFilters() {
            $('#modalOtorisasi').val('2');
            $('#modalStatus').val('2');
            updateFilterBadge();
        }

        $(document).on('show.bs.modal', '#modalFilter', function() {
            $('#modalOtorisasi').val(globalOtorisasi);
            $('#modalStatus').val(globalStatus);
            updateFilterBadge();
        });

        $(document).on('change', '#modalFilter select.rt-native', updateFilterBadge);

        function applyModalFilter() {
            globalOtorisasi = $('#modalOtorisasi').val();
            globalStatus = $('#modalStatus').val();
            $('#tabelRetur').DataTable().draw();
            $('#modalFilter').modal('hide');
        }

        $(document).ready(function() {
            // $("#form").modal('toggle')
            // $("#formKoreksiDetail").modal('toggle')
            // $("#formKoreksi").modal('toggle')

            // Render pertama — sama persis jalurnya dengan setiap refresh lewat loadAll(),
            // lihat definisi renderOutstanding()/renderPenerimaan() di bawah.
            renderOutstanding();
            renderPenerimaan();
            rebindTooltips();
            ikatPeriode();

            $('#tabelLen').val(String(panjangHalaman[activeTableId()]));

            // Filter hanya berlaku untuk tab "Pemakaian Barang" (Otorisasi/Status ada di sana).
            // columns.adjust() wajib dipanggil setelah tab baru terlihat — DataTables mengukur
            // lebar kolom 0px kalau tabel masih di dalam tab-pane yang hidden saat init.
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                const targetId = $(e.target).attr('href');
                $(targetId + ' table').DataTable().columns.adjust();
                activeTable().search($('#searchBox').val() || '').draw();
                $('#tabelLen').val(String(panjangHalaman[activeTableId()]));
                $('#btnFilter').toggle(targetId === '#profile');
                aturTinggiTabel();
            });

        });


        function buttonBatalShowHide() {
            $('.showhide').hide();
        }

        function submitAddKoreksi() {
            let _token = $("#_token").val();
            let check = document.getElementById("koreksiAddSelect").value;
            console.log(check)
            if (check === "") {
                // console.log('a')
                alertify.warning("Tidak ada item dipilih");
                return
            }
            let dataOut = koreksiDataAddList[check]
            // console.log(dataOut)
            let qntTerima = $("#koreksiAddInputQty").val()
            if (Number(qntTerima) > Number(dataOut.QntOS)) {
                alertify.warning("Qty tidak bisa lebih besar dari Qty OS");
                return
            }
            if (Number(qntTerima) <= 0) {
                alertify.warning("Qty tidak bisa 0 atau negatif");
                return
            }
            console.log('lolos')
            console.log(dataOut.NOSAT)
            let qntTerima1 = 0
            let qntTerima2 = 0
            if (dataOut.NOSAT == 1) {
                qntTerima1 = qntTerima
                qntTerima2 = qntTerima / dataOut.ISI2
            } else if (dataOut.NOSAT == 2) {
                qntTerima1 = qntTerima * dataOut.ISI2
                qntTerima2 = qntTerima
            }
            console.log(qntTerima, qntTerima1, qntTerima2)

            let dataPBG = koreksiDataEdit
            let choice = "I"
            let nopbg = dataPBG.NOBUKTI
            let nourut = dataPBG.NOURUT
            let inputDate = $("#input_koreksi_tanggal").val()
            let kodegdg = dataPBG.Kodegdg
            let urut = 0
            let kodebrg = dataOut.KODEBRG
            let nosat = dataOut.NOSAT
            let sat = dataOut.Satuan
            let isi = dataOut.ISI
            let nobppb = ""
            let urutspk = 0
            let nosatspk = 0
            let issample = 0
            let isbarang = 1
            let keterangan = ""
            let kddep = ""
            let nopr = dataOut.NOBUKTI
            let urutpr = dataOut.Urut

            console.log('choice', choice)
            console.log('nopbg', nopbg)
            console.log('nourut', nourut)
            console.log('inputDate', inputDate)
            console.log('kodegdg', kodegdg)
            console.log('urut', urut)
            console.log('kodebrg', kodebrg)
            console.log('qntTerima', qntTerima)
            console.log('nosat', nosat)
            console.log('sat', sat)
            console.log('isi', isi)
            console.log('nobppb', nobppb)
            console.log('qntTerima2', qntTerima2)
            console.log('urutspk', urutspk)
            console.log('nosatspk', nosatspk)
            console.log('issample', issample)
            console.log('isbarang', isbarang)
            console.log('keterangan', keterangan)
            console.log('kddep', kddep)
            console.log('nopr', nopr)
            console.log('urutpr', urutpr)


            $.ajax({
                url: "{!! url('pemakaianbarangspkoreksi') !!}",
                type: "post",
                async: false,
                data: {
                    _token: _token,
                    choice,
                    nopbg,
                    nourut,
                    inputDate,
                    kodegdg,
                    urut,
                    kodebrg,
                    qntTerima,
                    nosat,
                    sat,
                    isi,
                    nobppb,
                    qntTerima2,
                    urutspk,
                    nosatspk,
                    issample,
                    isbarang,
                    keterangan,
                    kddep,
                    nopr,
                    urutpr,
                },
                success: function(res) {
                    console.log(res, 'succes add koreksi')
                    refreshKoreksi(nopbg)
                    alertify.success('Item telah ditambah');
                    loadAll()
                }
            })
        }

        function submitEditKoreksi() {
            // console.log('submit edit koreksi')
            // console.log(koreksiDataEdit)
            let dataPBG = koreksiDataEdit

            let _token = $("#_token").val();

            let qntTerima = $("#koreksiEditInputQty").val()
            console.log('============')
            console.log(qntTerima)
            // console.log(dataPBG)
            // console.log(qntTerima)
            if (Number(qntTerima) > Number(dataPBG.QntOS) + Number(dataPBG.QNT)) {
                alertify.warning("Qty tidak bisa lebih besar dari Qty OS");
                return
            }
            if (Number(qntTerima) <= 0) {
                alertify.warning("Qty tidak bisa 0 atau negatif");
                return
            }
            console.log('lolos')
            // console.log(dataPBG.NOSAT)
            // console.log(dataPBG.ISI2)
            let qntTerima1 = 0
            let qntTerima2 = 0
            if (dataPBG.NOSAT == 1) {
                qntTerima1 = qntTerima
                qntTerima2 = qntTerima / dataPBG.ISI2
            } else if (dataPBG.NOSAT == 2) {
                qntTerima1 = qntTerima * dataPBG.ISI2
                qntTerima2 = qntTerima
            }



            // let dataTrf = koreksiDataEdit
            let choice = "U"
            let nopbg = dataPBG.NOBUKTI
            let nourut = dataPBG.NOURUT
            let inputDate = $("#input_koreksi_tanggal").val()
            let kodegdg = dataPBG.Kodegdg
            let urut = dataPBG.URUT
            let kodebrg = dataPBG.KODEBRG
            let nosat = dataPBG.NOSAT
            let sat = dataPBG.Satuan
            let isi = dataPBG.ISI
            let nobppb = ""
            let urutspk = 0
            let nosatspk = 0
            let issample = 0
            let isbarang = 1
            let keterangan = ""
            let kddep = ""
            let nopr = ''
            let urutpr = 0

            console.log('choice', choice)
            console.log('nopbg', nopbg)
            console.log('nourut', nourut)
            console.log('inputDate', inputDate)
            console.log('kodegdg', kodegdg)
            console.log('urut', urut)
            console.log('kodebrg', kodebrg)
            console.log('qntTerima', qntTerima)
            console.log('nosat', nosat)
            console.log('sat', sat)
            console.log('isi', isi)
            console.log('nobppb', nobppb)
            console.log('qntTerima2', qntTerima2)
            console.log('urutspk', urutspk)
            console.log('nosatspk', nosatspk)
            console.log('issample', issample)
            console.log('isbarang', isbarang)
            console.log('keterangan', keterangan)
            console.log('kddep', kddep)
            console.log('nopr', nopr)
            console.log('urutpr', urutpr)

            $.ajax({
                url: "{!! url('pemakaianbarangspkoreksi') !!}",
                type: "post",
                async: false,
                data: {
                    _token: _token,
                    choice,
                    nopbg,
                    nourut,
                    inputDate,
                    kodegdg,
                    urut,
                    kodebrg,
                    qntTerima,
                    nosat,
                    sat,
                    isi,
                    nobppb,
                    qntTerima2,
                    urutspk,
                    nosatspk,
                    issample,
                    isbarang,
                    keterangan,
                    kddep,
                    nopr,
                    urutpr,
                },
                success: function(res) {
                    console.log(res, 'succes edit koreksi')
                    refreshKoreksi(nopbg)
                    alertify.success('Item telah diedit');
                    loadAll()
                }
            })
        }



        function buttonKoreksiEdit(index) {
            let akses = $("#akses_iskoreksi").val();

            if (!Number(akses)) {
                alertify.warning('No access')
                return
            }
            $('.showhide').hide();
            // console.log(koreksiPenerimaanArray[index])
            // console.log(index)
            koreksiDataEdit = koreksiPenerimaanArray[index]
            console.log('edit data', koreksiDataEdit)
            let qnt = 0.00
            if (koreksiDataEdit.QNT) {

                qnt = parseFloat(koreksiDataEdit.QNT).toFixed(2)
            }
            document.getElementById("koreksiEditKodeBrg").value = koreksiDataEdit.KODEBRG
            document.getElementById("koreksiEditNamaBrg").value = koreksiDataEdit.NAMABRG
            // document.getElementById("koreksiEditQtyOS").value = koreksiDataEdit.QntOS
            document.getElementById("koreksiEditInputQty").value = qnt
            document.getElementById("koreksiEditSatuan").value = koreksiDataEdit.Satuan
            $('#formKoreksiEdit').show();
            document.getElementById('formKoreksiEdit').scrollIntoView();
        }

        function buttonKoreksiAdd() {
            $('.showhide').hide();
            document.getElementById("koreksiAddKodeBrg").value = ""
            document.getElementById("koreksiAddNamaBrg").value = ""
            // document.getElementById("koreksiAddQtyOS").value = 0.00
            document.getElementById("koreksiAddQtyPO").value = 0.00
            document.getElementById("koreksiAddInputQty").value = "0.00"
            $('#formKoreksiAdd').show();
        }

        function changeSelectKoreksiAdd() {
            let indexBarang = document.getElementById("koreksiAddSelect").value;
            console.log(indexBarang)
            console.log(koreksiDataAddList[indexBarang])
            let qnt = 0.00
            let qntos = 0.00
            if (koreksiDataAddList[indexBarang].QNT) {
                qnt = parseFloat(koreksiDataAddList[indexBarang].QNT).toFixed(2)
            }
            if (koreksiDataAddList[indexBarang].QntOS) {
                qntos = parseFloat(koreksiDataAddList[indexBarang].QntOS).toFixed(2)
            }
            document.getElementById("koreksiAddKodeBrg").value = koreksiDataAddList[indexBarang].KODEBRG
            document.getElementById("koreksiAddNamaBrg").value = koreksiDataAddList[indexBarang].NAMABRG
            document.getElementById("koreksiAddQtyOS").value = qntos
            document.getElementById("koreksiAddQtyPO").value = qnt
            document.getElementById("koreksiAddSatuan").value = koreksiDataAddList[indexBarang].Satuan

        }

        function submitAdd() {
            console.log('Submit Add')
            let _token = $("#_token").val();
            let tempData = []

            // console.log('TES ==========')
            // return

            addDataArray.forEach((item, i) => {
                if (document.getElementById(`add_checkbox${i}`).checked) {
                    addDataArray[i].inputQntTerima = $(`#input_add_qntTerima${i}`).val();
                    tempData.push(addDataArray[i])
                }
            });

            if (!tempData.length) {
                alertify.warning("Tidak ada item dipilih");
                return
            }
            let flag = false
            tempData.forEach((item, i) => {
                console.log(item, '==================')
                console.log(Number(item.inputQntTerima), Number(item.QntOS))
                if (Number(item.inputQntTerima) > Number(item.QntOS)) {
                    console.log('os')

                    // return
                    flag = true
                }
                if (Number(item.inputQntTerima) < 0) {
                    console.log('negatif')

                    // return
                    flag = true
                }
            });
            if (flag) {
                alertify.warning("Qty tidak bisa negatif / melebihi OS");
                return
            }
            console.log('lolos')
            // return


            let inputDate = $("#input_add_tanggal").val();
            let noout = $(`#input_add_noout`).val();
            let nopbg = $(`#input_add_nopbg`).val();
            let nourut = $(`#input_add_noUrut`).val();

            console.log('noout', noout)
            console.log('nopbg', nopbg)
            console.log('nourut', nourut)
            console.log('inputDate', inputDate)
            console.log(tempData)

            let checkDate = new Date(inputDate)

            let periode_bulan = document.getElementById("periode_bulan").value
            let periode_tahun = document.getElementById("periode_tahun").value

            if (checkDate.getFullYear() !== Number(periode_tahun) || (checkDate.getMonth() + 1) !== Number(periode_bulan)) {

                alertify.warning("Tanggal tidak sesuai periode");
                return
            }

            $.ajax({
                url: "{!! url('pemakaianbarangspadd') !!}",
                type: "post",
                async: false,
                data: {
                    _token: _token,
                    nopbg,
                    noout,
                    nourut,
                    tempData,
                    inputDate
                },
                success: function(res) {
                    console.log(res, '!')

                    if (res == 1) {
                        $("#form").modal('toggle')
                        alertify.success('PBG telah ditambah');
                        loadAll()
                    }


                    if (res == 2) {
                        setNewNoBukti()
                        alertify.warning('Nobukti telah di refresh, silahkan submit ulang')
                    }
                },
                error: function(err) {
                    console.log(err)
                    alertify.warning('Terjadi kesalahan pada server, silahkan refresh browser')
                }
            })



        }

        function setNewNoBukti() {
            let _token = $('#_token').val()
            $.ajax({
                url: "{!! url('spnobukti') !!}",
                type: "post",
                async: false,
                data: {
                    _token,
                    kode: 'PBG'
                },
                success: function(res) {
                    console.log(res, 'NoBUkti')
                    console.log(res[0].Nobukti, res[0].Nourut)
                    console.log('===============')
                    document.getElementById("input_add_nopbg").value = res[0].Nobukti
                    document.getElementById("input_add_noUrut").value = res[0].Nourut
                }
            })
        }

        function buttonAdd(NOBUKTI) {
            console.log('button add')
            console.log(NOBUKTI)
            let akses = $("#akses_istambah").val();

            if (!Number(akses)) {
                alertify.warning('No access')
                return
            }

            setNewNoBukti()


            let _token = $("#_token").val();
            $.ajax({
                url: "{!! url('pemakaianbarangdetailadd') !!}",
                type: "post",
                async: false,
                data: {
                    _token: _token,
                    noout: NOBUKTI
                },
                success: function(res) {
                    console.log(res)
                    addDataArray = res
                    console.log('===========================')
                }
            })

            if (!addDataArray.length) {
                alertify.warning("Data habis/ stok tidak mencukupi")
                return
            }
            //
            document.getElementById("input_add_noout").value = addDataArray[0].NOBUKTI
            document.getElementById("input_add_gdg").value = addDataArray[0].NamaGudangAsal
            // document.getElementById("input_add_namacust").value = addDataArray[0].NAMACUSTSUPP
            //
            let rowTable = ""
            addDataArray.forEach((item, i) => {
                let qnt = 0.00
                let qntos = 0.00
                if (item.QNT) {
                    qnt = parseFloat(item.QNT).toFixed(2)
                }
                if (item.QntOS) {
                    qntos = parseFloat(item.QntOS).toFixed(2)
                }
                rowTable +=
                    `<tr class="text-left">
    <td class="text-center">
      <input type="checkbox" id="add_checkbox${i}" style="transform: scale(1.5); margin: 5px;">
    </td>
    <td>${item.KODEBRG}</td>
    <td>${item.NAMABRG}</td>
    <td class="text-right">${qnt}</td>
    <td class="text-center">${item.Satuan}</td>
    <td class="text-center"><input onchange="" id="input_add_qntTerima${i}" style="width: 100px;" class="text-right" type="number" min=0 value=0.00></td></tr>`
            });
            document.getElementById("addTableData").innerHTML = rowTable

            // <td class="text-right">${qntos}</td>

            $("#form").modal('toggle')


        }

        function buttonDetail(NOBUKTI) {
            console.log(NOBUKTI)
            let _token = $("#_token").val();
            $.ajax({
                url: "{!! url('pemakaianbarangdetailoutstanding') !!}",
                type: "post",
                async: false,
                data: {
                    _token: _token,
                    NOBUKTI: NOBUKTI
                },
                success: function(res) {
                    console.log(res)
                    // addDataArray = res
                    console.log('===========================')
                    document.getElementById("input_detail_noout").value = res[0].NOBUKTI
                    document.getElementById("input_detail_gdg").value = res[0].NamaGudangAsal

                    let rowTable = ""
                    res.forEach((item, i) => {
                        let qnt = 0.00
                        let qntos = 0.00
                        if (item.QNT) {
                            qnt = parseFloat(item.QNT).toFixed(2)
                        }
                        if (item.QntOS) {
                            qntos = parseFloat(item.QntOS).toFixed(2)
                        }
                        rowTable += `<tr class="text-left">
        <td>${item.KODEBRG}</td>
        <td>${item.NAMABRG}</td>
        <td class="text-right">${qnt}</td>
        <td class="text-center">${item.Satuan}</td>
        </tr>`
                    });
                    document.getElementById("detailTableData").innerHTML = rowTable

                }
            })
            $("#formDetail").modal('toggle')
        }

        function fmtYMD(v) {
            if (!v) {
                return '';
            }
            let date = new Date(v);
            if (isNaN(date)) {
                return '';
            }
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            return date.getFullYear() + "/" + month + "/" + day;
        }

        // Markup kolom Actions mengikuti aksiButtonsHtml() di gudang/ubahkemasanbarang.blade.php
        // (.po-aksi-wrap, cuma title, tanpa tooltip Bootstrap). Warna/ikon tetap milik halaman ini.
        function outstandingRowHtml(item) {
            return `<tr>
    <td class="text-center">
      <div class="po-aksi-wrap">
        <button type="button" class="btn btn-warning btn-sm" title="Detail" onclick="buttonDetail('${item[0].NOBUKTI}')"><i class="bi bi-info"></i></button>
        <button type="button" class="btn btn-primary btn-sm" title="Add" onclick="buttonAdd('${item[0].NOBUKTI}')"><i class="bi bi-plus"></i></button>
      </div>
    </td>
    <td>${item[0].NOBUKTI}</td>
    <td>${fmtYMD(item[0].TANGGAL)}</td>
    <td>${item[0].NamaGudangAsal}</td>
    </tr>`
        }

        function penerimaanRowHtml(item) {
            let p = item[0]
            let isOto = Number(p.IsOtorisasi1 || 0)
            let qntOS = Number(p.QntOS || 0)
            let isTerkirim = qntOS <= 0
            let statusBadge = isTerkirim ?
                '<span class="sp-badge is-active">Terkirim</span>' :
                '<span class="sp-badge is-inactive">Belum Terkirim</span>'
            // Kolom Oto: centang hijau / silang merah, sama seperti ubahkemasanbarang.blade.php.
            let otoCell = (isOto === 1) ?
                '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>' :
                '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>'

            return `<tr data-oto="${isOto}" data-os="${qntOS}">
    <td class="text-center">
      <div class="po-aksi-wrap">
        <button type="button" class="btn btn-warning btn-sm" title="Detail" onclick="buttonDetailKoreksi('${p.NOBUKTI}')"><i class="bi bi-info"></i></button>
        <button type="button" class="btn btn-success btn-sm" title="Edit" onclick="buttonKoreksi('${p.NOBUKTI}')"><i class="bi bi-pen"></i></button>
        <button type="button" class="btn btn-info btn-sm" title="Print" onclick="submitPrint('${p.NOBUKTI}')"><i class="bi bi-printer"></i></button>
      </div>
    </td>
    <td>${p.NOBUKTI}</td>
    <td>${fmtYMD(p.TANGGAL)}</td>
    <td>${p.NooutBRg}</td>
    <td>${p.Namagdg}</td>
    <td>${statusBadge}</td>
    ${otoCell}
    <td>${p.OtoUser1 || ''}</td>
    <td>${fmtYMD(p.TglOto1)}</td>
    </tr>`
        }

        // Membangun ulang satu DataTable dari array baris terbaru — dipakai bareng oleh render
        // pertama (document ready, tabel belum jadi DataTable) dan setiap kali loadAll() dipanggil
        // (tabel sudah aktif, harus destroy dulu). State (halaman aktif, pencarian, urutan)
        // dipertahankan lewat destroy/reinit — pola sama seperti
        // purchasing/pembelianclosingpr.blade.php — supaya submit Add/Koreksi tidak melempar user
        // balik ke halaman 1 / menghapus pencarian yang sedang diketik.
        function rebuildTable(tableId, tbodyId, rows, rowHtmlFn, dtOptions) {
            const tableSel = '#' + tableId;
            let state = null;

            if ($.fn.dataTable.isDataTable(tableSel)) {
                const dtOld = $(tableSel).DataTable();
                state = {
                    start: dtOld.page.info().start,
                    search: dtOld.search(),
                    order: dtOld.order()
                };
                dtOld.destroy();
            }

            document.getElementById(tbodyId).innerHTML = rows.map(rowHtmlFn).join('');

            const opts = Object.assign({}, dtOptions, {
                pageLength: panjangHalaman[tableId]
            });
            const dt = $(tableSel).DataTable(opts);

            if (state) {
                dt.search(state.search).order(state.order).draw();
                const len = panjangHalaman[tableId];
                if (len > 0) {
                    dt.page(Math.floor(state.start / len)).draw('page');
                }
            }

            aturTinggiTabel();
        }

        function renderOutstanding() {
            rebuildTable('tabel', 'tabel_data', outstandingRows, outstandingRowHtml, dtOptionsOutstanding);
        }

        function renderPenerimaan() {
            rebuildTable('tabelRetur', 'tabelRetur_data', penerimaanRows, penerimaanRowHtml,
                dtOptionsPenerimaan);
        }

        function rebindTooltips() {
            // container:'body', boundary:'window' — lihat penjelasan panjang di
            // permintaanpemakaian.blade.php renderTabel() soal kenapa keduanya wajib di
            // dalam kotak scroll pendek (.table-wrap).
            $('[data-toggle="tooltip"]').tooltip({
                container: 'body',
                boundary: 'window'
            });
        }

        // Menyegarkan kedua tabel sekaligus untuk rentang tanggal yang sedang dipilih (dipanggil
        // saat tanggal berubah dan setelah Add/Koreksi/otorisasi supaya tabel menyegarkan diri
        // sendiri) — sama seperti reloadData() di permintaanpemakaian.blade.php.
        function loadAll() {
            let date1 = $('#inputDate1').val();
            let date2 = $('#inputDate2').val();

            $.ajax({
                url: "{!! url('pemakaianbarangloadall') !!}",
                type: "get",
                async: false,
                data: {
                    date1,
                    date2
                },
                success: function(res) {
                    console.log(res)
                    outstandingRows = res.outstandingArray
                    penerimaanRows = res.penerimaanArray
                }
            })

            renderOutstanding();
            renderPenerimaan();
            rebindTooltips();
        }

        function submitPrint(nobukti) {
            // for (var i = 0; i < 30; i++) {
            //   dataPrint.push(dataPrint[0])
            // }
            let _token = $('#_token').val()
            $.ajax({
                url: "{!! url('pemakaianbarangdetailCetak') !!}",
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
            for (let i = 0; i < dataPrint.length; i += 7) {
                let tempArray = dataPrint.slice(i, i + 7)
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
            hdr = `<div class="" style="display: flex; width: 100%">
              <div class="pe-1" style="width: 50%">
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 15%; margin-top: 15px">
                    ` + imageContent + `
                  </div>
                  <div class="pb-1 ps-3" style="width: 85%; ">
                    <h2 class="m-0 pb-2">CV. SINAR MAHAKAM LESTARI</h2>
                  </div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">Departemen : </div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">Untuk Keperluan : </div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
              </div>


              <div style="width: 38%">
                <div style="display: flex; width: 100%">
                  <h2 class="m-0 pb-2">BUKTI PEMAKAIAN INTERNAL</h2>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">No</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">` + dataPrint[0].NoBukti + `</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Tanggal</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">` + tanggalOnly + `</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 0%"></div>
                </div>
              </div>
              <div
                style="
                  width: 12%;
                  height: 80px;
                  overflow: hidden;
                "
                >
                ` + printContent + `
              </div>
            </div>
      <table

                class="detail-spb-table"
                style="width: 100%; height: 225px; max-height: 225px;font-family: sans-serif;  display: table;
                font-size: 10px">
                <thead>
                  <tr>
                    <td class="text-center" style="width: 2%">No.</td>
                    <td class="text-center" style="width: 50%">URAIAN BARANG</td>
                    <td class="text-center" style="width: 5%">SATUAN</td>
                    <td class="text-center" style="width: 5%">QTY</td>
                  </tr>
                </thead> `;

            let z = 0
            let tempPrintStr = ``
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
                tempPrintStr += `<tbody border="1">`;
                item.forEach((itemSub, j) => {
                    tempPrintStr += ``



                    tempPrintStr += `
         <tr>
         <td class="text-align: center"
               style="width: 2%; ">${z+1}</td>
         <td class="text-align: left"
               style="width: 50%;  ">${itemSub.NamaBrg}</td>
         <td class="text-align: text-center"
               style="width: 5%;">${itemSub.Sat}</td>
         <td class="text-align: text-right"
               style="width: 5%;  ">${itemSub.Qnt ? parseFloat(itemSub.Qnt).toFixed(2) : ''}</td>
         </tr>`;

                    z++;

                });
                tempPrintStr += `
          <tr style>

          </tr>`;

                tempPrintStr += `</tbody>`;

                tempPrintStr += `</table>

          <hr style="margin-top: -6px" />

         <div class="footer-sign font-family: sans-serif;
           font-size: 10px ">

         <div class="row mt-3" style="text-align: left;font-family: sans-serif;
         font-size: 12px ">
         <span style="float: left; display: block; clear: left;">
         </span>

         <span style="float: left; display: block; clear: left;">
         </span>
         </div>


           <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: -15px ; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%">Diterima Oleh</td>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%">Diserahkan Oleh</td>
               <td class="no-border text-center" style="width: 10%"></td>
             </tr>
             <tr style="height: 2.5rem">
               <td class="no-border">&nbsp;</td>
             </tr>

             <tr>
              <td class="no-border px-2">
               </td>
               <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
               </td>
               <td class="no-border px-2">
               </td>
               <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
               </td>
               <td class="no-border px-2">
               </td>
             </tr>
           </table>
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

        function buttonKoreksiDelete(index) {
            let akses = $("#akses_ishapus").val();

            if (!Number(akses)) {
                alertify.warning('No access')
                return
            }

            let dataPBG = koreksiPenerimaanArray[index]
            console.log(dataPBG)

            alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + dataPBG.KODEBRG + dataPBG.NAMABRG + ' ?',
                function() {
                    let _token = $("#_token").val();
                    let choice = "D"
                    console.log('yes')
                    let nopbg = dataPBG.NOBUKTI
                    let nourut = dataPBG.NOURUT
                    let inputDate = $("#input_koreksi_tanggal").val()
                    let kodegdg = dataPBG.Kodegdg
                    let urut = dataPBG.URUT
                    let kodebrg = dataPBG.KODEBRG
                    let nosat = dataPBG.NOSAT
                    let sat = dataPBG.Satuan
                    let isi = dataPBG.ISI
                    let nobppb = ""
                    let urutspk = 0
                    let nosatspk = 0
                    let issample = 0
                    let isbarang = 1
                    let keterangan = ""
                    let kddep = ""
                    let nopr = ''
                    let urutpr = 0
                    let qntTerima = 0
                    let qntTerima2 = 0

                    console.log('choice', choice)
                    console.log('nopbg', nopbg)
                    console.log('nourut', nourut)
                    console.log('inputDate', inputDate)
                    console.log('kodegdg', kodegdg)
                    console.log('urut', urut)
                    console.log('kodebrg', kodebrg)
                    console.log('qntTerima', qntTerima)
                    console.log('nosat', nosat)
                    console.log('sat', sat)
                    console.log('isi', isi)
                    console.log('nobppb', nobppb)
                    console.log('qntTerima2', qntTerima2)
                    console.log('urutspk', urutspk)
                    console.log('nosatspk', nosatspk)
                    console.log('issample', issample)
                    console.log('isbarang', isbarang)
                    console.log('keterangan', keterangan)
                    console.log('kddep', kddep)
                    console.log('nopr', nopr)
                    console.log('urutpr', urutpr)

                    $.ajax({
                        url: "{!! url('pemakaianbarangspkoreksi') !!}",
                        type: "post",
                        async: false,
                        data: {
                            _token: _token,
                            choice,
                            nopbg,
                            nourut,
                            inputDate,
                            kodegdg,
                            urut,
                            kodebrg,
                            qntTerima,
                            nosat,
                            sat,
                            isi,
                            nobppb,
                            qntTerima2,
                            urutspk,
                            nosatspk,
                            issample,
                            isbarang,
                            keterangan,
                            kddep,
                            nopr,
                            urutpr,
                        },
                        success: function(res) {
                            console.log(res, 'succes delete koreksi')
                            refreshKoreksi(nopbg)
                            alertify.success('Item telah didelete');
                            loadAll()
                        }
                    })

                },
                function() {
                    console.log('no')
                });
        }

        function refreshKoreksi(NOBUKTI) {

            $('.showhide').hide();

            let _token = $("#_token").val();
            $.ajax({
                url: "{!! url('pemakaianbarangdetailpenerimaan') !!}",
                type: "post",
                async: false,
                data: {
                    _token: _token,
                    NOBUKTI: NOBUKTI
                },
                success: function(res) {
                    console.log(res)
                    koreksiPenerimaanArray = res
                    koreksiDataEdit = res[0]
                }
            })

            if (!koreksiPenerimaanArray.length) {
                console.log('item habis')
                $("#formKoreksi").modal('toggle')
                return
            }

            let nooutbrg = ''
            console.log(koreksiPenerimaanArray[0])
            if (koreksiPenerimaanArray[0].NooutBRg) {
                nooutbrg = koreksiPenerimaanArray[0].NooutBRg
            }

            document.getElementById("input_koreksi_noout").value = nooutbrg
            document.getElementById("input_koreksi_nopbg").value = koreksiPenerimaanArray[0].NOBUKTI
            document.getElementById("input_koreksi_gdg").value = koreksiPenerimaanArray[0].Namagdg

            let date = new Date(koreksiPenerimaanArray[0].TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear() + "-" + (month) + "-" + (day);
            $('#input_koreksi_tanggal').val(date1)


            let rowTable = ""
            koreksiPenerimaanArray.forEach((item, i) => {
                let qnt = 0.00
                let qntos = 0.00
                if (item.QNT) {
                    qnt = parseFloat(item.QNT).toFixed(2)
                }
                if (item.QntOS) {
                    qntos = parseFloat(item.QntOS).toFixed(2)
                }
                rowTable += `<tr class="text-left">
    <td>${item.KODEBRG}</td>
    <td>${item.NAMABRG}</td>
    <td class="text-right">${qnt}</td>
    <td class="text-center">${item.Satuan}</td>
    <td class="text-center">
    <div class="action-buttons">
    <button type="button" class="btn-action-sm btn-action-success" data-toggle="tooltip" title="Edit" onclick="buttonKoreksiEdit(${i})"><i class="bi bi-pen"></i></button>
    <button type="button" class="btn-action-sm btn-action-danger" data-toggle="tooltip" title="Hapus" onclick="buttonKoreksiDelete(${i})"><i class="bi bi-trash"></i></button>
    </div></td>
    </td>
    </tr>`
            });
            document.getElementById("koreksiTableData").innerHTML = rowTable
            $.ajax({
                url: "{!! url('pemakaianbarangkoreksiaddlist') !!}",
                type: "post",
                async: false,
                data: {
                    _token: _token,
                    nopbg: koreksiPenerimaanArray[0].NOBUKTI,
                    noout: koreksiPenerimaanArray[0].NooutBRg
                },
                success: function(res) {
                    console.log(res, "addlistkoreksi !!!!!!")
                    koreksiDataAddList = res
                }
            })


            let tempKoreksiAddList = ""
            if (koreksiDataAddList.length) {
                tempKoreksiAddList += `<option value="" selected disabled>-- Pilih Barang --</option>`
                koreksiDataAddList.forEach((item, i) => {
                    tempKoreksiAddList += `<option value="${i}">${item.KODEBRG} - ${item.NAMABRG}</option>`
                });
            } else {
                tempKoreksiAddList += `<option value="" selected disabled>Tidak ada barang untuk ditambah</option>`
            }
            document.getElementById("koreksiAddSelect").innerHTML = tempKoreksiAddList
            // $("#formKoreksi").modal('toggle')

        }

        function buttonDetailKoreksi(NOBUKTI) {
            console.log('button detail koreksi')
            console.log(NOBUKTI)

            let _token = $("#_token").val();

            $.ajax({
                url: "{!! url('pemakaianbarangdetailpenerimaan') !!}",
                type: "post",
                async: false,
                data: {
                    _token: _token,
                    NOBUKTI: NOBUKTI
                },
                success: function(res) {
                    console.log(res)
                    // koreksiPenerimaanArray = res
                    // koreksiDataEdit = res[0]
                    console.log(res[0])

                    let nooutbrg = ''
                    console.log(res[0])
                    if (res[0].NooutBRg) {
                        nooutbrg = res[0].NooutBRg
                    }

                    document.getElementById("input_koreksidetail_noout").value = nooutbrg
                    document.getElementById("input_koreksidetail_nopbg").value = res[0].NOBUKTI
                    document.getElementById("input_koreksidetail_gdg").value = res[0].Namagdg

                    let date = new Date(res[0].TANGGAL);
                    let day = ("0" + date.getDate()).slice(-2);
                    let month = ("0" + (date.getMonth() + 1)).slice(-2);
                    date1 = date.getFullYear() + "-" + (month) + "-" + (day);
                    $('#input_koreksidetail_tanggal').val(date1)
                    let rowTable = ""
                    res.forEach((item, i) => {
                        let qnt = 0.00
                        let qntos = 0.00
                        if (item.QNT) {
                            qnt = parseFloat(item.QNT).toFixed(2)
                        }
                        if (item.QntOS) {
                            qntos = parseFloat(item.QntOS).toFixed(2)
                        }
                        rowTable += `<tr class="text-left">
        <td>${item.KODEBRG}</td>
        <td>${item.NAMABRG}</td>
        <td class="text-right">${qnt}</td>
        <td class="text-center">${item.Satuan}</td>
        </tr>`
                    });
                    document.getElementById("koreksiDetailTableData").innerHTML = rowTable



                }
            })
            //
            // let nooutbrg = ''
            // console.log(koreksiPenerimaanArray[0])
            // if (koreksiPenerimaanArray[0].NooutBRg) {
            //   nooutbrg = koreksiPenerimaanArray[0].NooutBRg
            // }
            //
            // document.getElementById("input_koreksi_noout").value = nooutbrg
            // document.getElementById("input_koreksi_nopbg").value = koreksiPenerimaanArray[0].NOBUKTI
            // document.getElementById("input_koreksi_gdg").value = koreksiPenerimaanArray[0].Namagdg
            //
            // let date = new Date(koreksiPenerimaanArray[0].TANGGAL);
            // let day = ("0" + date.getDate()).slice(-2);
            // let month = ("0" + (date.getMonth() + 1)).slice(-2);
            // date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
            // $('#input_koreksi_tanggal').val(date1)
            //
            //
            // let rowTable = ""
            // koreksiPenerimaanArray.forEach((item, i) => {
            //   let qnt = 0.00
            //   let qntos = 0.00
            //   if(item.QNT) {
            //     qnt = parseFloat(item.QNT).toFixed(2)
            //   }
            //   if(item.QntOS) {
            //     qntos = parseFloat(item.QntOS).toFixed(2)
            //   }
            //   rowTable += `<tr>
        //   <td>${item.KODEBRG}</td>
        //   <td>${item.NAMABRG}</td>
        //   <td>${qnt}</td>
        //   <td>${qntos}</td>
        //   <td>${item.Satuan}</td>
        //   <td class="text-center"><div id="containerbarcodeKoreksi${i}"><svg id="barcodeKoreksi${i}"></svg></td>
        //   <td><input id="input_Koreksi_qntPrint${i}" style="width: 100px;" class="text-right" type="number" min=0 value=${qnt}></td>
        //   <td class="text-center">
        //   <button class="btn btn-success btn-sm" type="button" onclick="printBarcode('containerbarcodeKoreksi${i}', 'input_Koreksi_qntPrint${i}')"><i class="bi bi-printer"></i></button>
        //   <button class="btn btn-warning btn-sm" type="button" onclick="buttonKoreksiEdit(${i})"><i class="bi bi-pen"></i></button>
        //   <button class="btn btn-danger btn-sm" type="button" onclick="buttonKoreksiDelete(${i})" ><i class="bi bi-trash"></i></button></td>
        //   </td>
        //   </tr>`
            // });
            // document.getElementById("koreksiTableData").innerHTML = rowTable
            //
            //
            // koreksiPenerimaanArray.forEach((item, i) => {
            //   JsBarcode(`#barcodeKoreksi${i}`, item.KODEBRG ,{width: 2, height: 25,
            //     // displayValue: false
            //   });
            // });

            $("#formKoreksiDetail").modal('toggle')

        }

        function buttonKoreksi(NOBUKTI) {
            console.log('button koreksi')
            console.log(NOBUKTI)

            $('.showhide').hide();
            let _token = $("#_token").val();

            $.ajax({
                url: "{!! url('pemakaianbarangdetailpenerimaan') !!}",
                type: "post",
                async: false,
                data: {
                    _token: _token,
                    NOBUKTI: NOBUKTI
                },
                success: function(res) {
                    console.log(res)
                    koreksiPenerimaanArray = res
                    koreksiDataEdit = res[0]
                }
            })

            let nooutbrg = ''
            console.log(koreksiPenerimaanArray[0])
            if (koreksiPenerimaanArray[0].NooutBRg) {
                nooutbrg = koreksiPenerimaanArray[0].NooutBRg
            }

            document.getElementById("input_koreksi_noout").value = nooutbrg
            document.getElementById("input_koreksi_nopbg").value = koreksiPenerimaanArray[0].NOBUKTI
            document.getElementById("input_koreksi_gdg").value = koreksiPenerimaanArray[0].Namagdg

            let date = new Date(koreksiPenerimaanArray[0].TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear() + "-" + (month) + "-" + (day);
            $('#input_koreksi_tanggal').val(date1)


            let rowTable = ""
            koreksiPenerimaanArray.forEach((item, i) => {
                let qnt = 0.00
                let qntos = 0.00
                if (item.QNT) {
                    qnt = parseFloat(item.QNT).toFixed(2)
                }
                if (item.QntOS) {
                    qntos = parseFloat(item.QntOS).toFixed(2)
                }
                rowTable += `<tr class="text-left">
    <td>${item.KODEBRG}</td>
    <td>${item.NAMABRG}</td>
    <td class="text-right">${qnt}</td>
    <td class="text-center">${item.Satuan}</td>
    <td class="text-center">
    <div class="action-buttons">
    <button type="button" class="btn-action-sm btn-action-success" data-toggle="tooltip" title="Edit" onclick="buttonKoreksiEdit(${i})"><i class="bi bi-pen"></i></button>
    <button type="button" class="btn-action-sm btn-action-danger" data-toggle="tooltip" title="Hapus" onclick="buttonKoreksiDelete(${i})"><i class="bi bi-trash"></i></button>
    </div></td>
    </td>
    </tr>`
            });
            document.getElementById("koreksiTableData").innerHTML = rowTable




            $.ajax({
                url: "{!! url('pemakaianbarangkoreksiaddlist') !!}",
                type: "post",
                async: false,
                data: {
                    _token: _token,
                    nopbg: koreksiPenerimaanArray[0].NOBUKTI,
                    noout: koreksiPenerimaanArray[0].NooutBRg
                },
                success: function(res) {
                    console.log(res, "addlistkoreksi !!!!!!")
                    koreksiDataAddList = res
                }
            })


            let tempKoreksiAddList = ""
            if (koreksiDataAddList.length) {
                tempKoreksiAddList += `<option value="" selected disabled>-- Pilih Barang --</option>`
                koreksiDataAddList.forEach((item, i) => {
                    tempKoreksiAddList += `<option value="${i}">${item.KODEBRG} - ${item.NAMABRG}</option>`
                });
            } else {
                tempKoreksiAddList += `<option value="" selected disabled>Tidak ada barang untuk ditambah</option>`
            }



            document.getElementById("koreksiAddSelect").innerHTML = tempKoreksiAddList








            $("#formKoreksi").modal('toggle')
        }

        function printBarcode(idBarcode, idJumlah) {
            console.log(idBarcode, idJumlah)
            let printContent = document.getElementById(`${idBarcode}`).innerHTML;
            let jumlahPrint = $(`#${idJumlah}`).val();
            console.log(jumlahPrint)
            let printContent1 = ""
            for (let i = 0; i < jumlahPrint; i++) {
                printContent1 += `<div class="row">`
                printContent1 += printContent
                printContent1 += `</div>`
            }
            document.getElementById("printContainer").innerHTML = printContent1
            w = window.open(' ');

            w.document.write($(`#printContainer`).html());
            w.print();
            w.close();
        }
    </script>

    <script>
        const tabHome = document.getElementById('nav-home-tab');
        const tabProfile = document.getElementById('nav-profile-tab');

        function setActiveTab(homeActive) {
            if (homeActive) {
                tabHome.style.backgroundColor = '#007bff';
                tabHome.style.color = '#fff';
                tabProfile.style.backgroundColor = '#f8f9fa';
                tabProfile.style.color = '#007bff';
            } else {
                tabProfile.style.backgroundColor = '#007bff';
                tabProfile.style.color = '#fff';
                tabHome.style.backgroundColor = '#f8f9fa';
                tabHome.style.color = '#007bff';
            }
        }

        // Default warna tab
        setActiveTab(true);

        // buat ganti tab
        tabHome.addEventListener('click', function() {
            setActiveTab(true);
        });

        tabProfile.addEventListener('click', function() {
            setActiveTab(false);
        });
    </script>
@endsection
