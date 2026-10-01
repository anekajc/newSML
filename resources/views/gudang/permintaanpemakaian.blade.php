@extends('newmasterTest')
@section('buttons')
@endsection
@section('page-title', 'Permintaan Pemakaian')

{{-- Bootstrap 4 only (CSS canvas/bootstrap.css v4.5.0 / JS v4.0.0 via newmasterTest). BS5 class
     names (fw-bold, form-select, rounded-end, ms-*/me-*, text-end) are undefined here and silently
     do nothing — use the BS4 names. --}}
@section('css')
    {{-- Layout newmasterTest (sama seperti gudang/ubahkemasanbarang.blade.php) sudah memuat
         newmaster.css + po-table-header.css, dan TIDAK memuat tableMaster2.css — tampilan tabel
         daftar diambil dari blok .po-list-page di po-table-header.css (class-nya dipasang di
         div#pageHome), tombol form dari blok .po-form-page (#pageForm/#pageDetail).
         report-table.css dimuat di sini (dulu dari layout gudang) untuk tabel item (.tb-report),
         modal picker Barang (.rt-picker-v2) dan modal filter (.rt-filter). --}}
    <link rel="stylesheet"
        href="{!! URL::asset('css/report-table.css') !!}?v={{ @filemtime(base_path('public/css/report-table.css')) ?: '1' }}">
    {{-- Dimuat ULANG setelah report-table.css supaya .po-* / .rt-* versi po-table-header.css menang
         saat spesifisitas seri. --}}
    <link rel="stylesheet"
        href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
    <link rel="stylesheet"
        href="{!! URL::asset('css/scrollbar-autohide.css') !!}?v={{ @filemtime(base_path('public/css/scrollbar-autohide.css')) ?: '1' }}">
@endsection
@section('content')
    <div id="imagecontainer" class="d-none" style="">
        <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
    </div>

    <div id="loadingContainer" style="display: none" class="container-fluid mt-6 justify-content-middle align-middle">
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>

    <div id="printContainer" style="display:none">

    </div>

    <div id="tempPrintContainer" style="display:none">

    </div>

    <div id="contentContainer" class="container-fluid">
        <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
        <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />
        <input type="hidden" id="akses_isotorisasi1" value="{{ $akses->IsOtorisasi1 }}">
        <input type="hidden" id="akses_isbatal" value="{{ $akses->IsBatal }}">
        <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
        <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS !!}" />
        <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
        <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />

        <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

        {{-- Kartu + toolbar + tabel mengikuti gudang/ubahkemasanbarang.blade.php (skema po-*,
             DataTables, id #tabel yang sudah terdaftar di po-table-header.css). Belum + sudah
             otorisasi digabung di satu tabel; statusnya dibedakan lewat kolom Oto + filter modal. --}}
        <div id="pageHome" class="container-fluid po-list-page">
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
                             ke DataTables lewat ikatPanjangHalaman() di renderTabel(), bukan onchange
                             inline (pola sama seperti ubahkemasanbarang.blade.php). --}}
                        <div class="po-len-wrap">
                            <label for="tampilLen">Tampilkan</label>
                            <select class="po-len-inp" id="tampilLen">
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="-1">Semua</option>
                            </select>
                        </div>

                        <button class="po-btn-filter" type="button" onclick="$('#modalFilter').modal('show')">
                            <i class="bi bi-funnel"></i> Filter
                        </button>

                        @if ((int) ($akses->ISTAMBAH ?? 0) === 1)
                            <div class="po-toolbar-act">
                                <button class="btn btn-chip-biru" type="button" onclick="buttonAdd()">Tambah</button>
                            </div>
                        @endif
                    </div>

                    {{-- Bar kolom tersembunyi — diisi ReportTable.init() (lihat $(document).ready). --}}
                    <div id="rtBar"></div>

                    <table id="tabel" class="data-table po-aksi-hover">
                        <thead>
                            <tr>
                                <th style="padding: 4px 12px;" scope="col">Actions</th>
                                <th style="padding: 4px 12px;" scope="col">No Bukti</th>
                                <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                                <th style="padding: 4px 12px;" scope="col">Status</th>
                                <th style="padding: 4px 12px;" scope="col">Oto</th>
                                <th style="padding: 4px 12px;" scope="col">User Oto</th>
                                <th style="padding: 4px 12px;" scope="col">Tgl Oto</th>
                            </tr>
                        </thead>
                        <tbody id="tabel2_data" class="text-left"></tbody>
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

    {{-- modal filter — DILETAKKAN DI LUAR .tb-report supaya reset `.tb-report *{margin:0;padding:0}`
         di report-table.css tidak merusak padding/margin modal Bootstrap. --}}
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


    <!-- <div> -->


    <!-- start pageForm (Add/Edit, full page) -->
    <div id="pageForm" class="container-fluid po-form-page" style="display:none">
        <div id="formBsGrid">
        <div class="row" style="padding: 1rem 0">
            <div class="col-6 text-left">
                {{-- Judul dikosongkan, disamakan dengan #pageForm di gudang/ubahkemasanbarang.blade.php. --}}
            </div>
            <div class="col-6 text-right" id="contentContainer">
                <button type="button" class="btn btn-danger btn-pill-primary btn-action-danger"
                    onclick="buttonCloseForm()">Close</button>
            </div>
        </div>

        <div class="container-fluid">
            <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="row">
                <!-- No Bukti -->
                <div class="col-md-4">
                    <div class="row align-items-center">
                        <label class="col-sm-4 col-form-label font-weight-bold">No Bukti</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="input_add_nobukti"
                                style="box-shadow: 0 0 5px rgba(81, 203, 238, 1); border: 1px solid rgba(81, 203, 238, 1);"
                                disabled>
                        </div>
                    </div>
                </div>

                <!-- Tanggal -->
                <div class="col-md-4">
                    <div class="row align-items-center">
                        <label class="col-sm-4 col-form-label font-weight-bold">Tanggal</label>
                        <div class="col-sm-8">
                            <input type="date" class="form-control" id="input_add_tanggal"
                                value="{!! date('Y-m-d') !!}">
                        </div>
                    </div>
                </div>

                <!-- Gudang -->
                <div class="col-md-4">
                    <div class="row align-items-center">
                        <label class="col-sm-4 col-form-label font-weight-bold">Gudang</label>
                        <div class="col-sm-8">
                            <select id="input_add_gudang" class="form-control">
                                <option value="0" selected disabled>-- Pilih Gudang --</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <br />
        <div class="container-fluid">
            <div class="row ">
                <div class="col-md-12 text-right" id="contentContainer">
                    <button type="button" class="btn btn-action-primary btn-primary btn-pill-primary"
                        onclick="buttonAddAdd()">
                        Tambah Item
                    </button>
                </div>

                <!-- koreksi add -->
                <div class="tb-report container-fluid mt-4">
                    <div class="table-outer">
                        <div class="table-wrap" style="max-height:40vh;">
                            <table id="addTable" class="tb">
                                <thead>
                                    <tr>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Qty</th>
                                        <th>Satuan</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="addTableData">
                                    <tr>

                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div id="formAddAdd" class="container-fluid showhide">
                    <!-- <div class="line"></div> -->
                    <br />
                    <div class="row">
                        <div class="col-12">
                            <h4>Tambah Item</h4>
                        </div>
                    </div>

                    <div class="container-fluid">
                        <!-- Baris 1 -->
                        <div class="row">
                            <!-- Kode Barang -->
                            <div class="col-6">
                                <div class="row align-items-center">
                                    <label class="col-4 col-form-label font-weight-bold">Kode Barang</label>
                                    <div class="col-6">
                                        <div class="input-group">
                                            <input id="AddAddKodeBrg" type="text" class="form-control text-left"
                                                placeholder="Kode Barang" onkeypress="onKeyPressBarang(event)">
                                            <button type="button" onclick="buttonAddListBarang()"
                                                class="btn btn-chip-biru">
                                                <i class="bi bi-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Qty -->
                            <div class="col-2">
                                <div class="row align-items-center">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Qty</label>
                                    <div class="col-sm-8">

                                        <input type="text" id="AddAddInputQty"
                                            class="form-control text-right input-partial-number">
                                    </div>
                                </div>
                            </div>

                            <!-- Satuan -->
                            <div class="col-3">
                                <div class="row align-items-center">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Satuan</label>
                                    <div class="col-sm-8">
                                        <select id="AddAddSatuan" class="form-control text-center">
                                            <option value="0" selected disabled>-- Pilih Satuan --
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Baris 2 -->
                        <div class="row mt-2">
                            <!-- Nama Barang -->
                            <div class="col-8">
                                <div class="row align-items-center">
                                    <label class="col-3 col-form-label font-weight-bold">Nama Barang</label>
                                    <div class="col">
                                        <input id="AddAddNamaBrg" type="text" class="form-control" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12 text-right" id="contentContainer">
                            <button type="button" class="btn btn-action-danger btn-danger btn-pill-primary"
                                onclick="buttonBatalAdd()">Batal</button>

                            <button type="button" class="btn btn-action-primary btn-primary btn-pill-primary" onclick="submitAddAdd()">Simpan</button>
                        </div>

                    </div>
                    <!-- <div class="line"></div> -->
                    <hr />
                </div>
                <!-- end -->

                <!-- koreksi edit -->
                <div id="formAddEdit" class="container-fluid showhide">
                    <!-- <div class="line"></div> -->
                    <br />
                    <div class="row">
                        <div class="col-12">
                            {{-- <h4>Edit Item</h4> --}}
                        </div>
                    </div>

                    <div class="container-fluid" id="formBsGrid">
                        <!-- Baris 1 -->
                        <div class="row">
                            <!-- Kode Barang -->
                            <div class="col-6">
                                <div class="row align-items-center">
                                    <label class="col-4 col-form-label font-weight-bold">Kode Barang</label>
                                    <div class="col">
                                        <div class="input-group">
                                            <input id="AddEditKodeBrg" type="text" class="form-control text-left"
                                                placeholder="Kode Barang" disabled>
                                            <!-- Tombol Plus (sementara di-comment sesuai kode asli) -->
                                            <!--
                                                                                      <button type="button" onclick="buttonAddListBarang()" class="btn btn-primary btn-sm rounded-right shadow-sm">
                                                                                        <i class="bi bi-plus"></i>
                                                                                      </button>
                                                                                      -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Qty -->
                            <div class="col-2">
                                <div class="row align-items-center">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Qty</label>
                                    <div class="col-sm-8">

                                        <input type="text" id="AddEditInputQty" data-a-sign="" data-a-dec="."
                                            data-a-sep="," class="form-control text-right input-partial-number">
                                    </div>
                                </div>
                            </div>

                            <!-- Satuan -->
                            <div class="col-3">
                                <div class="row align-items-center">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Satuan</label>
                                    <div class="col-sm-8">
                                        <select id="AddEditSatuan" class="form-control text-center">
                                            <option value="0" selected disabled>-- Pilih Satuan --
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Baris 2 -->
                        <div class="row mt-2">
                            <!-- Nama Barang -->
                            <div class="col-8">
                                <div class="row align-items-center">
                                    <label class="col-3 col-form-label font-weight-bold">Nama Barang</label>
                                    <div class="col">
                                        <input id="AddEditNamaBrg" type="text" class="form-control" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-12 text-right" id="contentContainer">
                            <button type="button" class="btn btn-danger btn-action-danger btn-pill-primary"
                                onclick="buttonBatalAdd()">Batal</button>

                            <button type="button" onclick="submitAddEdit()" class="btn btn-primary btn-action-primary btn-pill-primary">Simpan</button>
                        </div>

                    </div>
                    <hr />
                </div>
                <!-- end -->
            </div>
        </div>
        </div>
    </div>
    <!-- End pageForm -->


    <!-- start modal list item add -->
    <div class="modal fade rt-picker-v2" id="formAddListItem" tabindex="-1" role="dialog"
        aria-labelledby="formAddListItemLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formAddListItemLabel">Pilih Barang</h5>
                    <button type="button" class="close" onclick="closeFormList()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <table id="tabel_add_list_item" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Kode Barang</th>
                                <th scope="col">Nama Barang</th>
                                <th scope="col">Part Number</th>
                            </tr>
                        </thead>

                        <tbody>
                        </tbody>

                    </table>

                </div>
            </div>
        </div>
    </div>
    <!-- End modal list item add-->


    <!-- start pageDetail (Detail, full page) -->
    <div id="pageDetail" class="container-fluid po-form-page" style="display:none">

        <div id="formBsGrid">
        <div class="row" style="padding: 1rem 0">
            <div class="col-6 text-left">
                {{-- Judul dikosongkan, disamakan dengan #pageForm di gudang/ubahkemasanbarang.blade.php. --}}
            </div>
            <div class="col-6 text-right" id="contentContainer">
                <button type="button" class="btn btn-danger btn-pill-primary btn-action-danger"
                    onclick="buttonCloseForm()">Close</button>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <input type="hidden" name="noUrut" id="input_detail_koreksi_noUrut" value="" />

                <!-- No Bukti -->
                <div class="col-md-4">
                    <div class="row align-items-center">
                        <label class="col-sm-4 col-form-label">No Bukti</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="input_detail_koreksi_nobukti" placeholder=""
                                disabled>
                        </div>
                    </div>
                </div>

                <!-- Tanggal -->
                <div class="col-md-4">
                    <div class="row align-items-center">
                        <label class="col-sm-4 col-form-label">Tanggal</label>
                        <div class="col-sm-8">
                            <input type="date" class="form-control" id="input_detail_koreksi_tanggal"
                                value="{!! date('Y-m-d') !!}" disabled>
                        </div>
                    </div>
                </div>

                <!-- Gudang -->
                <div class="col-md-4">
                    <div class="row align-items-center">
                        <label class="col-sm-4 col-form-label font-weight-bold">Gudang</label>
                        <div class="col-sm-8">
                            {{-- Diisi refreshTableDetailKoreksi(); disabled seperti No Bukti/Tanggal (Detail read-only). --}}
                            <select id="input_detail_koreksi_gudang" class="form-control" disabled>
                                <option value="0" selected disabled>-- Pilih Gudang --</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row ">
                <div class="tb-report container-fluid mt-4">
                    <div class="table-outer">
                        <div class="table-wrap" style="max-height:40vh;">
                            <table id="detailKoreksiTable" class="tb">
                                <thead>
                                    <tr>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Qty</th>
                                        <th>Satuan</th>
                                    </tr>
                                </thead>
                                <tbody id="detailKoreksiTableData">
                                    <tr class="empty-row">
                                        <td colspan="4">Data tidak ditemukan</td>
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
    <!-- End pageDetail -->
@endsection

@section('js')
    {{-- report-table.js (mesin geser/sembunyi kolom) dulu dimuat layout gudang; newmasterTest tidak. --}}
    <script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
    <script type="text/javascript">
        let listBarang = []
        let listLokasi = []

        let listItemForm = []
        let itemAddObj = {}
        let itemEditObj = {}


        // console.log(Number('0019'))

        jQuery(function($) {
            $('.input-partial-number').autoNumeric('init', {
                minimumValue: '0',
                // negativeSignCharacter: 'z'
            });
        });

        /* ============================================================================
         * Tabel interaktif gabungan (belum + sudah otorisasi) — port halaman-lokal dari
         * mesin gcart_header di report/masterreport2.blade.php, karena halaman ini
         * @@extends('gudang.newmaster') dan bukan report.masterreport2 (lihat
         * docs/new-slider-table-guide.md). doShowCustomize()/doButtonSubtotal()/
         * doButtonGrandtotal() sengaja tidak diikutkan — itu untuk modal "Atur Kolom"
         * yang tidak ada di halaman ini; report-table.js memanggil onChange sendiri.
         * ========================================================================= */
        let lastRows = @json($penerimaanArray); // paint pertama tanpa AJAX; reloadData() menyegarkan setelahnya
        let globalOtorisasi = "2"; // filter modal: 2=Semua, 1=Sudah Otorisasi, 0=Belum Otorisasi
        let globalStatus = "2"; // filter modal: 2=Semua, 1=Terkirim, 0=Belum Terkirim

        let pageSize = 10; // dipakai sebagai pageLength DataTables di renderTabel()
        // No. bukti yang barusan disimpan/diotorisasi: renderTabel() memindahkan tabel ke halaman
        // tempat baris itu berada, lalu mengosongkannya lagi. Diisi reloadData(_focusNobukti).
        let fokusNobukti = '';

        var g_href = 'permintaanpemakaian';
        // '2' (bukan '0'/'1') supaya user lama dengan layout tersimpan otomatis dapat key
        // baru dan setDefaultHeader() jalan lagi — kalau tidak, kolom Status baru tidak
        // akan pernah muncul buat mereka (doLoadHeader mengembalikan string lama), dan
        // yang sempat menyimpan versi 'float' tetap kebawa rata kanan.
        var g_modeReport = '2';
        var gcart_header = [];
        var gsum_issubtotal = 0;
        var gsum_isgrandtotal = 0;
        var gct_desimal_max = 4;

        function setDefaultHeader() {
            // [ field, label, visible, type, total, decimals ]
            gcart_header = [
                ['NOBUKTI', 'No Bukti', 1, 'varchar', 0, 0],
                ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
                // IsOtorisasi1/OtoUser1/TglOto1 bukan lagi kolom geser/sembunyi — ketiganya kolom
                // tetap (Oto/User Oto/Tgl Oto) yang ditambahkan sendiri oleh renderTabel().
                // 'varchar', bukan 'float' — nilainya dirender jadi badge Terkirim/Belum
                // Terkirim, jadi jangan diperlakukan sebagai kolom angka (rata kanan).
                ['QntOS', 'Status', 1, 'varchar', 0, 0],
                ['KodeGdg', 'Kode Gudang', 0, 'varchar', 0, 0],
                ['Namagdg', 'Nama Gudang', 0, 'varchar', 0, 0]
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

        // Ambil field dari row tanpa peduli besar/kecil huruf — VWMASTERPRPENYERAHANBHN
        // mencampur UPPERCASE (NOBUKTI/TANGGAL) dengan PascalCase (OtoUser1/TglOto1).
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

        // #modalOtorisasi: 2=Semua, 1=Sudah, 0=Belum — client-side saja,
        // server selalu mengembalikan seluruh rentang tanggal.
        function filterByOtorisasi(rows, filterVal) {
            if (filterVal === '1') {
                return rows.filter(r => Number(pickCI(r, 'IsOtorisasi1')) === 1);
            }
            if (filterVal === '0') {
                return rows.filter(r => Number(pickCI(r, 'IsOtorisasi1')) === 0);
            }
            return rows;
        }

        // QntOS = sisa qty yang belum diserahkan (dijumlah per NOBUKTI di controller).
        // 0 berarti seluruh item sudah dikirim; angka berapa pun di atas 0 berarti masih
        // ada sisa. Nilai kosong/null diperlakukan 0 supaya tidak jadi "Belum Terkirim" palsu.
        function isTerkirim(r) {
            return Number(pickCI(r, 'QntOS') || 0) <= 0;
        }

        // #modalStatus: 2=Semua, 1=Terkirim, 0=Belum Terkirim — client-side, sama seperti
        // filter otorisasi.
        function filterByStatus(rows, filterVal) {
            if (filterVal === '1') {
                return rows.filter(r => isTerkirim(r));
            }
            if (filterVal === '0') {
                return rows.filter(r => !isTerkirim(r));
            }
            return rows;
        }

        // Markup tombol mengikuti aksiButtonsHtml() di gudang/ubahkemasanbarang.blade.php
        // (.po-aksi-wrap, cuma title, tanpa tooltip Bootstrap). Warna/ikon/urutan tetap milik
        // halaman ini: Detail=amber bi-info, Otorisasi=biru bi-key, Edit=hijau bi-pencil-fill,
        // Batal Otorisasi=merah bi-key-fill, Print=cyan bi-printer.
        function aksiButtonsHtml(r) {
            const nobukti = r.NOBUKTI;
            let tombolAksi =
                '<button type="button" class="btn btn-warning btn-sm" title="Detail" onclick="buttonDetailKoreksi(\'' +
                nobukti + '\')"><i class="bi bi-info"></i></button>';

            if (Number(pickCI(r, 'IsOtorisasi1')) === 1) {
                // Sudah otorisasi — Batal Otorisasi + Print
                tombolAksi +=
                    '<button type="button" class="btn btn-danger btn-sm" title="Batal Otorisasi" onclick="buttonBatalOtorisasi(\'' +
                    nobukti + '\', \'' + r.IsOtorisasi1 + '\')"><i class="bi bi-key"></i></button>' +
                    '<button type="button" class="btn btn-info btn-sm" title="Print" onclick="submitPrint(\'' +
                    nobukti + '\')"><i class="bi bi-printer"></i></button>';
            } else {
                // Belum otorisasi — Otorisasi + Edit
                tombolAksi +=
                    '<button type="button" class="btn btn-primary btn-sm" title="Otorisasi" onclick="buttonOtorisasi(\'' +
                    nobukti + '\', \'' + r.IsOtorisasi1 + '\')"><i class="bi bi-key"></i></button>' +
                    '<button type="button" class="btn btn-success btn-sm" title="Edit" onclick="buttonKoreksi(\'' +
                    nobukti + '\', \'' + r.NoUrut + '\')"><i class="bi bi-pen"></i></button>';
            }

            return '<div class="po-aksi-wrap">' + tombolAksi + '</div>';
        }

        /* Bar kolom tersembunyi harus berada tepat di atas tabelnya. DataTables membungkus
           tabel dengan #<id>_wrapper saat init, jadi acuannya ikut berpindah — sama seperti
           rtPindahBar() di gudang/ubahkemasanbarang.blade.php. */
        function rtPindahBar(idBar, idTabel) {
            let bar = document.getElementById(idBar);
            let tabel = document.getElementById(idTabel);
            if (!bar || !tabel) {
                return;
            }

            let acuan = tabel;
            if ($.fn.DataTable.isDataTable('#' + idTabel)) {
                acuan = document.getElementById(idTabel + '_wrapper') || tabel;
            }

            if (acuan.previousElementSibling !== bar) {
                acuan.parentNode.insertBefore(bar, acuan);
            }
        }

        // Kotak cari, dropdown "Tampilkan", dan periode diikat lewat JS (bukan atribut inline)
        // dengan penjaga dataset.rtBound supaya tidak terpasang dobel tiap renderTabel().
        function ikatSearch() {
            let input = document.getElementById('searchBox2');
            if (!input || input.dataset.rtBound) {
                return;
            }
            input.dataset.rtBound = '1';

            input.addEventListener('input', function() {
                $('#tabel').DataTable().search(input.value).draw();
            });
        }

        function ikatPanjangHalaman() {
            let sel = document.getElementById('tampilLen');
            if (!sel || sel.dataset.rtBound) {
                return;
            }
            sel.dataset.rtBound = '1';
            sel.value = String(pageSize);

            sel.addEventListener('change', function() {
                let n = Number(sel.value);
                pageSize = (n === -1 || n > 0) ? n : 10;
                $('#tabel').DataTable().page.len(pageSize).draw();
            });
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
                reloadData();
            };
            awal.addEventListener('change', onUbah);
            akhir.addEventListener('change', onUbah);
        }

        // Pindahkan tabel ke halaman tempat baris `nobukti` berada (urutan tampil saat ini:
        // setelah filter modal + pencarian + sorting).
        function pindahKeHalamanBaris(nobukti) {
            let dt = $('#tabel').DataTable();
            let panjang = dt.page.len();
            if (!nobukti || panjang === -1) {
                return;
            }

            let idx = dt.rows({
                    order: 'current',
                    search: 'applied'
                }).nodes().toArray()
                .findIndex(function(tr) {
                    return tr.getAttribute('data-nobukti') === String(nobukti);
                });
            if (idx < 0) {
                return;
            }

            dt.page(Math.floor(idx / panjang)).draw('page');
        }

        // Tinggi tabel mengikuti sisa ruang layar — disalin dari aturTinggiTabel() di
        // ubahkemasanbarang.blade.php. Berhenti diam-diam kalau #pageHome sedang disembunyikan
        // (reloadData() masih bisa terpanggil saat #pageForm tampil); dihitung ulang begitu
        // #pageHome ditampilkan lagi (lihat buttonCloseForm()).
        function aturTinggiTabel() {
            let page = document.getElementById('pageHome');
            if (!page || page.offsetParent === null) {
                return;
            }

            let area = document.getElementById('content');
            let wrap = document.querySelector('#pageHome .po-table-wrap');
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

        function renderTabel() {
            // IsOtorisasi1/OtoUser1/TglOto1 dikeluarkan dari cols meski masih tersimpan di susunan
            // kolom lama — ketiganya kolom tetap (Oto/User Oto/Tgl Oto) yang ditambahkan sendiri
            // di bawah, bukan kolom geser/sembunyi (sama seperti ubahkemasanbarang.blade.php).
            const cols = gcart_header.filter(c => c[2] === 1 &&
                c[0] !== 'IsOtorisasi1' && c[0] !== 'OtoUser1' && c[0] !== 'TglOto1');

            if ($.fn.DataTable.isDataTable('#tabel')) {
                $('#tabel').DataTable().destroy();
            }

            const thead = document.querySelector('#tabel thead');
            thead.innerHTML = ReportTable.headHtml(cols).replace('<tr>',
                '<tr><th style="padding: 4px 12px;" scope="col">Actions</th>');
            let barisHead = thead.querySelector('tr');
            if (barisHead) {
                barisHead.insertAdjacentHTML('beforeend', `
                    <th style="padding: 4px 12px;" scope="col">Oto</th>
                    <th style="padding: 4px 12px;" scope="col">User Oto</th>
                    <th style="padding: 4px 12px;" scope="col">Tgl Oto</th>
                `);
            }

            // Cari/paging/sorting dipegang DataTables — di sini cuma filter Otorisasi + Status dari modal.
            const rows = filterByStatus(filterByOtorisasi(lastRows, globalOtorisasi), globalStatus);

            let html = '';
            rows.forEach(function(r) {
                html += '<tr data-nobukti="' + String(nullToEmpty(r.NOBUKTI)).replace(/"/g, '&quot;') + '">';
                html += '<td class="text-center">' + aksiButtonsHtml(r) + '</td>';
                html += cols.map(function(c) {
                    const v = pickCI(r, c[0]);
                    if (c[0] === 'QntOS') {
                        return isTerkirim(r) ?
                            '<td><span class="sp-badge is-active">Terkirim</span></td>' :
                            '<td><span class="sp-badge is-inactive">Belum Terkirim</span></td>';
                    }
                    if (c[3] === 'date') {
                        return '<td>' + fmtYMD(v) + '</td>';
                    }
                    return '<td>' + nullToEmpty(v) + '</td>';
                }).join('');
                let sudahOto = Number(pickCI(r, 'IsOtorisasi1')) === 1;
                let otoUser = pickCI(r, 'OtoUser1');
                let tglOto = pickCI(r, 'TglOto1');
                html += sudahOto ?
                    '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>' :
                    '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>';
                html += '<td>' + nullToEmpty(otoUser) + '</td>';
                html += '<td>' + fmtYMD(tglOto) + '</td>';
                html += '</tr>';
            });

            document.getElementById('tabel2_data').innerHTML = html;

            // Susunan opsi disalin dari ubahkemasanbarang.blade.php: dom membuat sendiri kotak
            // scroll .po-table-wrap (header sticky) + baris info & pager.
            $('#tabel').DataTable({
                lengthChange: false,
                pageLength: pageSize,
                order: [],
                columnDefs: [{
                    targets: [0],
                    orderable: false
                }],
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

            if (fokusNobukti) {
                pindahKeHalamanBaris(fokusNobukti);
                fokusNobukti = '';
            }

            aturTinggiTabel();
        }

        /* -- FILTER MODAL (Otorisasi: Semua/Sudah Otorisasi/Belum, Status: Semua/Terkirim/Belum) -- */
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
            renderTabel(); // DataTables di-init ulang, jadi otomatis kembali ke halaman 1
            $('#modalFilter').modal('hide');
        }

        /* -- FULL PAGE SWITCH (pageHome / pageForm / pageDetail) — pola sama dengan
           gudang/ubahkemasanbarang.blade.php, bukan modal Bootstrap. -- */
        function showPage(id) {
            $('#pageHome, #pageForm, #pageDetail').hide();
            $('#' + id).show();
        }

        function buttonCloseForm() {
            $('.showhide').hide();
            showPage('pageHome');
            // #pageHome bisa saja sudah dirender saat masih disembunyikan (reloadData() dipanggil
            // dari form), jadi tinggi tabel dihitung ulang begitu tampil lagi.
            aturTinggiTabel();
        }



        $(document).ready(function() {

            initBarangTable([])

            // Satu-satunya titik di mana modal pemilih barang dijamin sudah
            // display:block, jadi di sinilah lebar kolom boleh dihitung.
            $('#formAddListItem').on('shown.bs.modal', function() {
                if (barangTablePending !== null) {
                    initBarangTable(barangTablePending, barangSearchPending, barangPesanPending)
                } else if (barangTableDT) {
                    resetBarangTableWidths()
                    barangTableDT.columns.adjust()
                    $('#tabel_add_list_item_filter input').trigger('focus')
                }
            });

            // Tabel gabungan — mesin interaktif (drag/gear/bar), lihat
            // docs/new-slider-table-guide.md. doSetHeader() memuat layout tersimpan
            // milik user ini untuk halaman+mode ini, atau menyimpan setDefaultHeader()
            // kalau ini kunjungan pertama.
            doSetHeader(g_modeReport);
            ReportTable.init({
                table: '#tabel',
                bar: '#rtBar',
                onChange: renderTabel
            });
            renderTabel();

        });

        function buttonOtorisasi(nobukti, isOtorisasi) {
            let akses = $("#akses_isotorisasi1").val();
            if (!Number(akses)) {
                alertify.warning('No access');
                return;
            }
            console.log(nobukti, isOtorisasi)

            if (Number(isOtorisasi) > 0) {
                alertify.warning('Sudah diotorisasi');
                return;
            }

            let _token = $("#_token").val();

            $.ajax({
                url: "{!! url('permintaanpemakaianotorisasi') !!}",
                type: "post",
                async: false,
                data: {
                    _token,
                    nobukti,
                    otorisasi: 1
                },
                success: function(res) {
                    if (res > 0) {
                        alertify.success('Berhasil otorisasi');
                        reloadData(nobukti);
                    } else {
                        alertify.warning('Gagal otorisasi');
                    }
                },
                error: function(err) {
                    console.log(err);
                    alertify.warning('Terjadi kesalahan. Silakan refresh browser.');
                }
            });
        }



        function buttonBatalOtorisasi(nobukti, isOtorisasi) {
            let akses = $("#akses_isbatal").val();
            // if (!Number(akses)) {
            //   alertify.warning('No access');
            //   return;
            // }

            if (Number(isOtorisasi) === 0) {
                alertify.warning('Belum diotorisasi');
                return;
            }

            // alertify.prompt(JUDUL, PESAN, NILAI_AWAL, onOK, onCancel)
            // Argumen pertama = judul di header dialog. Kalau argumen ini tidak diisi
            // (bentuk 4 argumen), AlertifyJS memakai default 'AlertifyJS'
            // (lihat public/js/alertify.js baris 50, alertify.defaults.glossary.title).
            // Kelas 'ajs-app-buttons' + 'is-danger' ditempel ke root dialog supaya CSS
            // tombol OK/Cancel di public/css/report-table.css (blok "gaya bersama")
            // hanya kena dialog ini, bukan semua dialog alertify di aplikasi.
            // 'is-danger' bikin tombol OK merah karena batal otorisasi aksi merusak.
            var dlgBatalOtorisasi = alertify.prompt("Batal Otorisasi", "Masukkan keterangan batal otorisasi nomor   " +
                nobukti, "",
                function(evt, value) {
                    // alertify.success("You entered: " + value);
                    let xpket = value;

                    if (xpket == '') {
                        alertify.warning('Keterangan harus diisi.');
                        $.abort();
                    }
                    let _token = $("#_token").val();

                    $.ajax({
                        url: "{!! url('permintaanpemakaianbatalotorisasi') !!}",
                        type: "post",
                        async: false,
                        data: {
                            _token,
                            nobukti,
                            otorisasi: 0,
                            pket: value
                        },
                        success: function(res) {
                            if (res > 0) {
                                alertify.success('Berhasil batal otorisasi');
                                reloadData(nobukti);
                            } else {
                                alertify.warning('Gagal batal otorisasi');
                            }
                        },
                        error: function(err) {
                            console.log(err);
                            alertify.warning('Terjadi kesalahan. Silakan refresh browser.');
                        }
                    });
                },
                function() {
                    console.log('Pembatalan Otorisasi dibatalkan');
                    alertify.error("Action cancelled");
                }
            );
            dlgBatalOtorisasi.elements.root.classList.add('ajs-app-buttons', 'is-danger');
        }

        // Menggantikan loadAll() lama — satu list gabungan, difilter di server berdasarkan
        // rentang tanggal yang sedang dipilih. Dipanggil saat tanggal berubah dan setelah
        // otorisasi/batal otorisasi/simpan/hapus supaya tabel menyegarkan diri sendiri.
        // _focusNobukti (opsional): no. bukti yang barusan diubah — tabel dipindah ke halaman
        // tempat baris itu berada. Tanpa argumen tabel kembali ke halaman 1.
        function reloadData(_focusNobukti) {
            $.ajax({
                url: "{!! url('permintaanpemakaianloadall') !!}",
                type: "get",
                async: false,
                data: {
                    date1: $('#inputDate1').val(),
                    date2: $('#inputDate2').val()
                },
                success: function(res) {
                    lastRows = res.penerimaan;
                    fokusNobukti = _focusNobukti || '';
                    renderTabel();
                }
            });
        }

        function submitPrint(nobukti) {
            // for (var i = 0; i < 30; i++) {
            //   dataPrint.push(dataPrint[0])
            // }
            let _token = $('#_token').val()
            $.ajax({
                url: "{!! url('permintaanpemakaiandetailCetak') !!}",
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

      .p-2 {
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
                  <h2 class="m-0 pb-2">BUKTI PERMINTAAN PEMAKAIAN INTERNAL</h2>
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
                    <td class="text-center" style="width: 2%" >No.</td>
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
               <td class="no-border text-center" style="width: 35%">Diminta Oleh</td>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%">Disetujui Oleh</td>
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

        function closeListItemAdd() {
            $('#formAddListItem').modal('hide');
        }

        function closeFormList() {
            $('#formAddListItem').modal('hide');
        }


        function submitAddEdit() {
            console.log('submitAddEdit')
            console.log(itemEditObj)
            let _token = $("#_token").val();
            let qntTerima = formatAngkaVal($("#AddEditInputQty").val())
            let satuan = $("#AddEditSatuan").val()
            console.log({
                _token: _token,
                choice: 'U',
                urut: itemEditObj.URUT,
                nobukti: itemEditObj.NOBUKTI,
                nourut: itemEditObj.NoUrut,
                qntTerima,
                kodebrg: itemEditObj.KODEBRG,
                namabrg: itemEditObj.NAMABRG,
                qnt1: 0,
                qnt2: 0,
                qntx: qntTerima,
                nosat: satuan,
                tanggal: itemEditObj.TANGGAL,
                jmlRecord: 1,
                kodegdg: itemEditObj.KodeGdg
            })
            // return

            if (Number(qntTerima) <= 0) {
                alertify.warning("Qnt <= 0")
                return
            }
            if (!satuan) {
                alertify.warning("satuan harus diisi")
                return
            }

            $.ajax({
                url: "{!! url('permintaanpemakaianspadd') !!}",
                type: "post",
                async: false,
                data: {
                    _token: _token,
                    choice: 'U',
                    urut: itemEditObj.URUT,
                    nobukti: itemEditObj.NOBUKTI,
                    nourut: itemEditObj.NoUrut,
                    qntTerima,
                    kodebrg: itemEditObj.KODEBRG,
                    namabrg: itemEditObj.NAMABRG,
                    qnt1: 0,
                    qnt2: 0,
                    qntx: qntTerima,
                    nosat: satuan,
                    tanggal: itemEditObj.TANGGAL,
                    jmlRecord: 1,
                    kodegdg: itemEditObj.KodeGdg
                },
                success: function(res) {
                    console.log(res)
                    console.log('masuk success')
                    if (res == 1) {
                        console.log(res, '!')

                        refreshTableAdd()
                        reloadData(itemEditObj.NOBUKTI)
                        // Form Edit sengaja TIDAK ditutup (cuma tombol Batal yang menutup).
                        // itemEditObj disinkronkan ulang dari listItemForm hasil refresh supaya
                        // submit berikutnya tetap menyasar baris yang sama dan isian di layar
                        // sama dengan yang tersimpan.
                        syncFormEditFromList()
                        alertify.success('Permintaan gudang telah diedit');
                    }
                },
                error: function(err) {
                    console.log('masuk error')
                    console.log(err)
                }
            })


        }

        function submitAddAdd() {
            console.log('submitAddAdd')

            let _token = $("#_token").val();
            let nobukti = $('#input_add_nobukti').val()
            let nourut = $('#input_add_noUrut').val()
            let qntTerima = formatAngkaVal($("#AddAddInputQty").val())
            let kodebrg = $("#AddAddKodeBrg").val()
            let namabrg = $("#AddAddNamaBrg").val()
            let kodegdg = $("#input_add_gudang").val()
            // let lokasiasal = $("#AddAddLokasiAsal").val()
            // let lokasitujuan = $("#AddAddLokasiTujuan").val()
            let satuan = $("#AddAddSatuan").val()
            let tanggal = $("#input_add_tanggal").val()
            // let keterangan = $("#input_add_keterangan").val()

            let checkDate = new Date(tanggal)

            let periode_bulan = document.getElementById("periode_bulan").value
            let periode_tahun = document.getElementById("periode_tahun").value

            let jmlRecord = 0
            if (listItemForm.length) {
                jmlRecord = 1
            }
            console.log(jmlRecord)
            // return

            if (!kodegdg) {
                alertify.warning("Gudang harus diisi")
                return
            }
            if (!kodebrg) {
                alertify.warning("kodebarang harus diisi")
                return
            }

            if (kodebrg != itemAddObj.KODEBRG) {
                alertify.warning("Barang tidak sesuai dengan pilihan")
                return

            }


            // if (!lokasiasal) {
            //   alertify.warning("lokasiasal harus diisi")
            //   return
            // }
            // if (!lokasitujuan) {
            //   alertify.warning("lokasitujuan harus diisi")
            //   return
            // }


            if (Number(qntTerima) <= 0) {
                alertify.warning("Qnt <= 0")
                return
            }
            if (!satuan) {
                alertify.warning("satuan harus diisi")
                return
            }

            if (checkDate.getFullYear() !== Number(periode_tahun) || (checkDate.getMonth() + 1) !== Number(periode_bulan)) {

                alertify.warning("Tanggal tidak sesuai periode");
                return
            }
            // kalo nosat 1 qnt , qnt2 langsung dr input
            //kalo nosat 2/3 , qnt dikali isi qnt 2 dr input
            let qnt2 = qntTerima
            let qnt1 = 0
            if (satuan == 1) {
                qnt1 = qntTerima
            } else if (satuan == 2) {
                qnt1 = qntTerima * itemAddObj.ISI2
            } else if (satuan == 3) {
                qnt1 = qntTerima * itemAddObj.ISI3
            }
            let qntx = 0

            console.log(itemAddObj)
            console.log('nobukti', nobukti)
            console.log('nourut', nourut)
            console.log('qntTerima', qntTerima)
            console.log('kodebrg', kodebrg)
            console.log('namabrg', namabrg)
            // console.log('lokasiasal' , lokasiasal)
            // console.log('lokasitujuan' , lokasitujuan)
            console.log('satuan', satuan)
            console.log('tanggal', tanggal)
            // console.log('keterangan' , keterangan)
            console.log('jmlRecord', jmlRecord)
            console.log('kodegdg', kodegdg)
            console.log(qnt1, qnt2)


            // return

            $.ajax({
                url: "{!! url('permintaanpemakaianspadd') !!}",
                type: "post",
                async: false,
                data: {
                    _token: _token,
                    choice: 'I',
                    urut: 0,
                    nobukti,
                    nourut,
                    qnt1,
                    qnt2: qntTerima,
                    kodebrg,
                    kodegdg,
                    namabrg,
                    nosat: satuan,
                    tanggal,
                    jmlRecord,
                    qntx
                },
                success: function(res) {
                    console.log(res)
                    console.log('masuk success')
                    if (res == 1) {
                        console.log(res, '!')

                        refreshTableAdd()
                        reloadData(nobukti)
                        // Form Add sengaja TIDAK ditutup (cuma tombol Batal yang menutup) —
                        // dikosongkan saja supaya siap untuk item berikutnya. itemAddObj ikut
                        // direset karena validasi membandingkan kodebrg dengan pilihan terakhir.
                        resetTableAddAdd()
                        itemAddObj = {}
                        $('#formAddAdd').show();
                        setTimeout(() => {
                            document.getElementById("AddAddKodeBrg").focus();
                            document.getElementById("AddAddKodeBrg").select();
                        }, 100);
                        alertify.success('Permintaan gudang telah ditambah');
                    }
                    if (res == 2) {
                        setNewNoBukti()
                        alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
                    }
                },
                error: function(err) {
                    console.log('masuk error')
                    console.log(err)
                }
            })





        }

        // Baris pemilih barang (whole-row clickable, tanpa kolom Actions) — dipakai oleh
        // buttonAddListBarang() dan onKeyPressBarang(), lihat docs/new-cust-supp-modal-guide.md.
        // DataTables owns the data (deferRender + paging) instead of a hand-built innerHTML,
        // so opening the picker on a large barang list doesn't freeze the page.
        var barangTableDT = null;
        var barangTablePending = null;
        var barangSearchPending = null;
        var barangPesanPending = null;
        var barangLookupBusy = false;
        // Nomor urut pencarian Enter di picker - jawaban server yang datang terlambat (dari
        // Enter sebelumnya) diabaikan supaya tidak menimpa hasil pencarian terbaru.
        var barangCariSeq = 0;

        // destroy() TIDAK membersihkan style="width:...px" yang ditulis DataTables
        // ke tiap <th> (lihat _fnDestroy, DataTables 1.10.18 — cuma table.style.width
        // yang dikembalikan). Init berikutnya membaca style itu sebagai sWidthOrig,
        // masuk jalur "user defined width", lalu menempelkan lebar px tetap ke
        // <table> — menimpa .rt-picker-v2 table { width:100% } sehingga kolom
        // menyusut tiap kali modal dibuka ulang. Bersihkan dulu sebelum re-init.
        function resetBarangTableWidths() {
            var $t = $('#tabel_add_list_item');
            $t.css('width', '');
            $t.children('colgroup').remove();
            $t.find('thead th').css('width', '');
        }

        // pesanKosong: teks saat tabel tanpa data. Default = petunjuk "ketik lalu Enter" (picker
        // baru dibuka, belum mencari); 'Tidak ada data' saat server tidak menemukan apa pun.
        function initBarangTable(list, searchTerm, pesanKosong) {
            // DataTables menghitung lebar kolom dari container saat init, dan
            // Bootstrap 4 baru memasang display:block setelah transisi backdrop
            // selesai. Kalau init dipanggil tepat setelah .modal('show'), modal
            // masih hidden → lebar diukur di container 0px. Antre saja, biar
            // dieksekusi di shown.bs.modal.
            if (!$('#formAddListItem').is(':visible')) {
                barangTablePending = list;
                barangSearchPending = searchTerm || '';
                barangPesanPending = pesanKosong || null;
                return;
            }
            barangTablePending = null;
            barangPesanPending = null;

            if ($.fn.DataTable.isDataTable('#tabel_add_list_item')) {
                $('#tabel_add_list_item').DataTable().clear().destroy();
            }
            resetBarangTableWidths();
            // Dropdown "Tampilkan" (DataTables length menu) 10/25/50/100/Semua, sama seperti
            // picker Kas/Bank (kasInitPicker() di public/js/kas.js). Kembali ke 10 tiap kali
            // picker dibuka, karena tabelnya di-destroy dan di-init ulang di sini.
            barangTableDT = $('#tabel_add_list_item').DataTable({
                data: list,
                deferRender: true,
                paging: true,
                pageLength: 10,
                lengthChange: true,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Semua']],
                // Hanya tabel yang di-scroll; info + tombol halaman terkunci di bawahnya
                // (.rt-picker-scroll / .rt-picker-bawah di public/css/report-table.css).
                dom: 'lfr<"rt-picker-scroll"t><"rt-picker-bawah"ip>',
                searching: true,
                order: [],
                language: {
                    lengthMenu: 'Tampilkan _MENU_',
                    emptyTable: pesanKosong || 'Ketik kode / nama barang di kotak Search, lalu tekan Enter',
                    zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
                },
                columns: [{
                        data: 'KODEBRG'
                    },
                    {
                        data: 'NAMABRG'
                    },
                    {
                        // PartNumber boleh NULL di DBBARANG - defaultContent mencegah
                        // peringatan DataTables "Requested unknown parameter".
                        data: 'PartNumber',
                        defaultContent: ''
                    }
                ],
                createdRow: function(row, data, dataIndex) {
                    row.className = 'pick-row';
                    row.setAttribute('onclick', 'buttonAddAddInsertItem(' + dataIndex + ')');
                }
            });

            // Dorong kode/nama yang sudah diketik user ke kotak search DataTables, supaya
            // picker terbuka langsung menyaring — lihat resolveBarang()/openBarangPicker().
            barangTableDT.search(searchTerm || '').draw();
            barangSearchPending = null;

            // Kotak Search DataTables sekaligus kotak cari server: Enter -> cariBarangPicker().
            // Sambil mengetik, DataTables tetap hanya menyaring baris yang sedang tampil.
            $('#tabel_add_list_item_filter input')
                .off('keypress.barang')
                .on('keypress.barang', cariBarangPicker)
                .trigger('focus');
        }

        function fetchBarangList(search, callback) {
            $.ajax({
                url: "{!! url('permintaanpemakaianlistbarang') !!}",
                type: "get",
                async: true,
                data: {
                    search
                },
                success: function(res) {
                    if (callback) callback(res)
                },
                error: function(err) {
                    console.log(err)
                    alertify.warning('Terjadi kesalahan saat memuat daftar barang')
                }
            })
        }

        // Enter di kotak Search picker -> cari ke server, sama seperti searchBarangAll() di
        // purchasing/pembelianpermintaannonagen.blade.php. Search kosong + Enter -> tabel
        // dikosongkan lagi (kembali ke petunjuk "ketik lalu Enter").
        function cariBarangPicker(e) {
            if (e.which !== 13) {
                return
            }
            e.preventDefault()

            let term = $(e.target).val().trim()
            let seq = ++barangCariSeq

            listBarang = []
            if (!term) {
                initBarangTable([], '')
                return
            }

            initBarangTable([], term, 'Mencari...')
            fetchBarangList(term, function(res) {
                if (seq !== barangCariSeq) {
                    return
                }
                listBarang = res
                initBarangTable(listBarang, term, 'Tidak ada data')
            })
        }

        // Buka picker. Tanpa `hasil`: tabel kosong, menunggu user mengetik di kotak Search
        // lalu Enter (cariBarangPicker). Dengan `hasil` (hasil cari server milik resolveBarang()):
        // langsung tampil dengan `term` di kotak Search. listBarang HARUS ditunjuk ke array
        // yang sama yang dioper ke initBarangTable() — createdRow menempelkan index array itu
        // ke onclick, jadi buttonAddAddInsertItem(index) salah baris kalau keduanya beda.
        function openBarangPicker(term, hasil) {
            barangCariSeq++
            listBarang = hasil || []
            $("#formAddListItem").modal('show')
            initBarangTable(listBarang, term || '', hasil ? 'Tidak ada data' : null)
        }

        // Titik masuk tunggal buat Enter dan tombol plus di Kode Barang (Add Item).
        // Kosong -> picker dibuka kosong, menunggu Enter di kotak Search-nya. Terisi -> cari
        // ke server; kode yang PERSIS cocok langsung mengisi form tanpa membuka modal, selain
        // itu (kode sebagian / nama) picker dibuka dengan hasil pencarian itu dan `term` di
        // kotak Search-nya. Form Edit sengaja tidak memakai ini — Kode Barang di sana disabled.
        function resolveBarang(term) {
            term = (term || '').trim()

            if (!term) {
                openBarangPicker('')
                return
            }

            if (barangLookupBusy) {
                return
            }

            let findExact = function(list) {
                let needle = term.toLowerCase()
                return list.find(b => String(b.KODEBRG || '').trim().toLowerCase() === needle)
            }

            barangLookupBusy = true
            $.ajax({
                url: "{!! url('permintaanpemakaianlistbarang') !!}",
                type: "get",
                async: true,
                data: {
                    search: term
                },
                success: function(res) {
                    barangLookupBusy = false
                    let hit = findExact(res)
                    if (hit) {
                        applyBarangToForm(hit)
                    } else {
                        openBarangPicker(term, res)
                    }
                },
                error: function(err) {
                    barangLookupBusy = false
                    console.log(err)
                    alertify.warning('Terjadi kesalahan saat memuat daftar barang')
                }
            })
        }

        function onKeyPressBarang(e) {
            if (e.which === 13) {
                resolveBarang($('#AddAddKodeBrg').val())
            }
        }

        function buttonAddListBarang() {
            resolveBarang($('#AddAddKodeBrg').val())
        }

        function buttonBatalAdd() {
            $('.showhide').hide();
        }



        // Dipakai baik oleh klik-baris di picker (buttonAddAddInsertItem) maupun oleh
        // resolveBarang() saat kode yang diketik persis cocok — satu tempat untuk isi
        // Kode/Nama/Satuan form Add Item lalu pindah fokus ke Qty.
        function applyBarangToForm(item) {
            document.getElementById("AddAddKodeBrg").value = item.KODEBRG
            document.getElementById("AddAddNamaBrg").value = item.NAMABRG
            itemAddObj = item

            let rowSelect = ""
            if (item.SAT1) {
                rowSelect += `<option value=1 selected>${item.SAT1}</option>`
            }
            if (item.SAT2) {
                rowSelect += `<option value=2>${item.SAT2}</option>`
            }
            if (item.SAT3) {
                rowSelect += `<option value=3>${item.SAT3}</option>`
            }
            document.getElementById("AddAddSatuan").innerHTML = rowSelect

            setTimeout(() => {
                document.getElementById("AddAddInputQty").focus();
                document.getElementById("AddAddInputQty").select();
            }, 300);
        }

        function buttonAddAddInsertItem(index) {
            closeListItemAdd()
            applyBarangToForm(listBarang[index])
        }


        function setNewNoBukti() {
            let _token = $("#_token").val()
            $.ajax({
                url: "{!! url('spnobukti') !!}",
                type: "post",
                async: false,
                data: {
                    _token,
                    kode: "PRP"
                },
                success: function(res) {
                    console.log(res)
                    document.getElementById("input_add_nobukti").value = res[0].Nobukti
                    document.getElementById("input_add_noUrut").value = res[0].Nourut
                },

                error: function(err) {
                    console.log(err)
                    alertify.warning('Terjadi kesalahan silahkan refresh browser')
                }
            })
        }

        function buttonKoreksi(nobukti, nourut) {

            // $('#formAddListLokasi').css('display', '')
            $('#formAddListItem').css('display', '')

            $('.showhide').hide();
            console.log(nobukti, nourut)

            $('#input_add_nobukti').css('border', '')
            $('#input_add_nobukti').css('box-shadow', '')
            document.getElementById("input_add_nobukti").value = nobukti
            document.getElementById("input_add_noUrut").value = nourut


            refreshTableAdd()
            let rowSelect =
                `<option value='${listItemForm[0].KodeGdg}' selected>${listItemForm[0].KodeGdg} - ${listItemForm[0].Namagdg}</option>`

            // refreshTableDetailKoreksi
            console.log('tes44')

            document.getElementById("input_add_gudang").innerHTML = rowSelect

            showPage('pageForm')

        }


        function buttonAdd() {
            console.log('buttonAdd')

            let akses = $("#akses_istambah").val();

            if (!Number(akses)) {
                alertify.warning('No access')
                return
            }

            $('.showhide').hide();
            console.log('buttonAdd')
            $('#input_add_nobukti').css('border', '')
            $('#input_add_nobukti').css('box-shadow', '')
            // document.getElementById("input_add_keterangan").value = ''

            $.ajax({
                url: "{!! url('permintaanpemakaianlistgudang') !!}",
                type: "get",
                async: false,
                data: {
                    // _token,
                    // nobukti
                },
                success: function(res) {
                    console.log(res)
                    let rowSelect = '<option value=0 selected disabled>-- Pilih Gudang --</option>'

                    res.forEach((item, i) => {

                        rowSelect +=
                            `<option value='${item.KODEGDG}'>${item.KODEGDG} - ${item.NAMA}</option>`
                    });




                    document.getElementById("input_add_gudang").innerHTML = rowSelect

                }
            })




            setNewNoBukti()
            refreshTableAdd()
            console.log('1')

            showPage('pageForm')
        }

        function buttonDetailKoreksi(nobukti) {
            console.log('buttonDetailKoreksi')
            console.log(nobukti)
            refreshTableDetailKoreksi(nobukti)

            showPage('pageDetail')

        }

        function refreshTableDetailKoreksi(nobukti) {
            let _token = $("#_token").val();

            $.ajax({
                url: "{!! url('permintaanpemakaiandetailpenerimaan') !!}",
                type: "post",
                async: false,
                data: {
                    _token,
                    nobukti
                },
                success: function(res) {
                    console.log(res)

                    let tableRow = ''
                    document.getElementById("input_detail_koreksi_nobukti").value = res[0].NOBUKTI
                    // document.getElementById("input_detail_koreksi_keterangan").value = res[0].KETERANGAN
                    let date = new Date(res[0].TANGGAL);
                    let day = ("0" + date.getDate()).slice(-2);
                    let month = ("0" + (date.getMonth() + 1)).slice(-2);
                    date1 = date.getFullYear() + "-" + (month) + "-" + (day);
                    console.log(date1)
                    // console.log($("#input_detail_koreksi_tanggal").val())
                    $('#input_detail_koreksi_tanggal').val(date1)

                    // Gudang header - satu opsi terpilih dari baris pertama, sama seperti
                    // buttonKoreksi() mengisi #input_add_gudang. Detail hanya tampilan, jadi
                    // tidak perlu memuat seluruh daftar gudang.
                    document.getElementById("input_detail_koreksi_gudang").innerHTML = res[0].KodeGdg ?
                        `<option value='${res[0].KodeGdg}' selected>${res[0].KodeGdg} - ${res[0].Namagdg || ''}</option>` :
                        `<option value="0" selected disabled>-- Pilih Gudang --</option>`

                    res.forEach((item, i) => {
                        tableRow += `
        <tr class="data-row">
        <td>${item.KODEBRG}</td>
        <td>${item.NAMABRG}</td>
        <td class="text-right">${formatAngka(parseFloat(item.Qntx).toFixed(2))}</td>
        <td class="text-center">${item.SATUAN}</td>
        </tr>
        `
                    });
                    console.log(res.length)
                    if (!res.length) {
                        tableRow = `<tr class="empty-row">
        <td colspan="4">Data tidak ditemukan</td>
        </tr>`
                    }


                    document.getElementById("detailKoreksiTableData").innerHTML = tableRow
                }
            })
        }

        function buttonEdit(index) {
            console.log('buttonEdit')

            let akses = $("#akses_iskoreksi").val();

            if (!Number(akses)) {
                alertify.warning('No access')
                return
            }

            $('.showhide').hide();
            console.log(index)
            itemEditObj = listItemForm[index]
            console.log(itemEditObj)

            document.getElementById("AddEditKodeBrg").value = itemEditObj.KODEBRG
            document.getElementById("AddEditNamaBrg").value = itemEditObj.NAMABRG
            // document.getElementById("AddEditLokasiAsal").value = itemEditObj.KodeLokasi
            // document.getElementById("AddEditLokasiTujuan").value = itemEditObj.KodeLokTujuan
            document.getElementById("AddEditInputQty").value = formatAngka(parseFloat(itemEditObj.Qntx).toFixed(2))
            let rowSelect = '<option value=0 selected disabled>-- Pilih Satuan --</option>'

            if (itemEditObj.SAT1) {
                rowSelect += `<option value=1>Sat 1 - ${itemEditObj.SAT1}</option>`
            }
            if (itemEditObj.SAT2) {
                rowSelect += `<option value=2>Sat 2 - ${itemEditObj.SAT2}</option>`
            }
            if (itemEditObj.SAT3) {
                rowSelect += `<option value=3>Sat 3 - ${itemEditObj.SAT3}</option>`
            }
            document.getElementById("AddEditSatuan").innerHTML = rowSelect
            document.getElementById("AddEditSatuan").value = itemEditObj.NOSAT


            $('#formAddEdit').show();

        }

        // Dipanggil setelah submitAddEdit() sukses: form Edit tetap terbuka, jadi objek
        // acuannya harus ditarik ulang dari listItemForm yang baru saja di-refresh
        // (dicocokkan NOBUKTI + URUT, bukan index — urutan array bisa berubah).
        // Kalau barisnya sudah tidak ada, form ditutup supaya tidak menyimpan ke baris hantu.
        function syncFormEditFromList() {
            let row = listItemForm.find(function(item) {
                return String(item.NOBUKTI) === String(itemEditObj.NOBUKTI) &&
                    Number(item.URUT) === Number(itemEditObj.URUT);
            });

            if (!row) {
                $('#formAddEdit').hide();
                return;
            }

            itemEditObj = row;
            document.getElementById("AddEditKodeBrg").value = row.KODEBRG
            document.getElementById("AddEditNamaBrg").value = row.NAMABRG
            document.getElementById("AddEditInputQty").value = formatAngka(parseFloat(row.Qntx).toFixed(2))
            document.getElementById("AddEditSatuan").value = row.NOSAT
            $('#formAddEdit').show();
        }

        function refreshTableAdd(action = 'I') {
            console.log('refreshTableAdd', action)
            // $('#formAddListLokasi').css('display', '')
            // $('#formAddListItem').css('display', '')
            listItemForm = []
            console.log(action)
            let nobukti = $('#input_add_nobukti').val();
            let _token = $("#_token").val();
            console.log('nobukti', nobukti)
            console.log('token', _token)

            $.ajax({
                url: "{!! url('permintaanpemakaiandetailpenerimaan') !!}",
                type: "post",
                async: false,
                data: {
                    _token,
                    nobukti
                },
                success: function(res) {
                    console.log(res)
                    listItemForm = res
                }
            })
            console.log(listItemForm)

            // addTableData
            let tableRow = ''
            listItemForm.forEach((item, i) => {
                tableRow += `
      <tr class="data-row">
      <td>${item.KODEBRG}</td>
      <td>${item.NAMABRG}</td>
      <td class="text-right">${formatAngka(parseFloat(item.Qntx).toFixed(2))}</td>
      <td class="text-center">${item.SATUAN}</td>
      <td class="text-center">
        <button class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit(${i})" ><i class="bi bi-pen"></i></button>
        <button class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${item.NOBUKTI}', ${item.URUT})" ><i class="bi bi-trash"></i></button>

      </td>
      </tr>
      `
            });
            console.log(listItemForm.length)
            // if (!listItemForm.length && action == 'D') {
            //   alertify.warning('Data item habis')
            // }
            if (!listItemForm.length) {
                tableRow = `<tr class="empty-row">
      <td colspan="5">Belum ada data</td>
      </tr>`
                console.log('masok !')
                $('#input_add_tanggal').prop('disabled', false)
                // $('#input_add_keterangan').prop('disabled', false)
                $('#input_add_gudang').prop('disabled', false)
            }
            if (listItemForm.length && listItemForm[0].TANGGAL) {
                let date = new Date(listItemForm[0].TANGGAL);
                let day = ("0" + date.getDate()).slice(-2);
                let month = ("0" + (date.getMonth() + 1)).slice(-2);
                date1 = date.getFullYear() + "-" + (month) + "-" + (day);
                console.log(date1)
                $('#input_add_tanggal').val(date1)
                // document.getElementById("input_add_keterangan").value = listItemForm[0].KETERANGAN
                $('#input_add_tanggal').prop('disabled', true)
                // $('#input_add_keterangan').prop('disabled', true)
                $('#input_add_gudang').prop('disabled', true)

            }

            document.getElementById("addTableData").innerHTML = tableRow
            return listItemForm.length
        }

        function buttonDelete(nobukti, urut) {
            console.log('buttonDelete')
            console.log(nobukti, urut)

            let akses = $("#akses_ishapus").val();
            console.log(akses)

            if (!Number(akses)) {
                alertify.warning('No access')
                return
            }
            let _token = $("#_token").val();

            alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + ' ?',
                function() {
                    $.ajax({
                        url: "{!! url('permintaanpemakaianspadd') !!}",
                        type: "post",
                        async: false,
                        data: {
                            _token: _token,
                            choice: 'D',
                            urut: urut,
                            nobukti: nobukti,
                            nourut: '',
                            qnt1: 0,
                            qnt2: 0,
                            qntx: 0,
                            kodebrg: '',
                            namabrg: '',
                            kodegdg: '',
                            nosat: 0,
                            tanggal: '',
                            jmlRecord: 0
                        },
                        success: function(res) {
                            console.log(res)
                            console.log('masuk success')
                            if (res == 1) {
                                let sisaItem = refreshTableAdd('D')
                                console.log(res, '!')

                                refreshTableAdd('D')
                                reloadData(nobukti)
                                // Form Edit sekarang bisa tetap terbuka saat item dihapus —
                                // sinkronkan supaya ia menutup sendiri kalau baris acuannya hilang.
                                if ($('#formAddEdit').is(':visible')) {
                                    syncFormEditFromList()
                                }
                                alertify.success('Permintaan gudang telah didelete');
                                if (sisaItem === 0) {
                                    buttonCloseForm()
                                }
                            }
                        },
                        error: function(err) {
                            console.log('masuk error')
                            console.log(err)
                        }
                    })


                },
                function() {
                    console.log('no')
                });
        }

        function buttonAddAdd() {
            let akses = $("#akses_istambah").val();

            if (!Number(akses)) {
                alertify.warning('No access')
                return
            }
            $('.showhide').hide();

            resetTableAddAdd()
            // document.getElementById("AddAddInputQty").value = '-1.00'
            $('#formAddAdd').show();

        }


        function generateInputNumber(id, style, classes, onchange) {
            return `<input type="text" id="${id}" onchange="${onchange}" style="${style}" data-a-sign="" data-a-dec="." data-a-sep="," class="form-control text-right input-partial-number ${classes}">`
        }

        function formatAngkaX(angka) {
            if (!angka) {
                return '0.00'
            } else {
                return formatAngka(parseFloat(angka).toFixed(2))
            }

        }

        function formatAngkaParse(angka) {

            return parseFloat(angka).toFixed(2)
        }

        function formatAngkaVal(angka) {
            return Number(angka.split(',').join(''))
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

        function resetTableAddAdd() {
            document.getElementById("AddAddKodeBrg").value = ''
            document.getElementById("AddAddNamaBrg").value = ''
            // document.getElementById("AddAddLokasiAsal").value = ''
            // document.getElementById("AddAddLokasiTujuan").value = ''
            document.getElementById("AddAddInputQty").value = '0.00'
            let rowSelect = '<option value=0 selected disabled>-- Pilih Satuan --</option>'
            document.getElementById("AddAddSatuan").innerHTML = rowSelect
        }
    </script>
@endsection
