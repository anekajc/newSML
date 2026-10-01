@extends('newmasterTest')
@section('buttons')
@endsection
@section('page-title', 'Ubah Kemasan Barang')

@section('css')
    {{-- Layout newmasterTest (sama seperti accounting/memorialkoreksi) sudah memuat newmaster.css +
         po-table-header.css, dan TIDAK memuat tableMaster2.css — jadi tabel daftar tidak perlu lagi
         aturan penetral. Tampilan tabel daftar diambil dari blok .po-list-page di po-table-header.css
         (class-nya dipasang di div#pageHome).
         report-table.css dimuat di sini (dulu dari layout gudang) untuk modal picker Barang
         (.rt-picker-v2) dan modal filter (.rt-filter). Tabel item #tabelitem tidak lagi memakai
         .tb-report — tampilannya ditulis di blok CSS #tabelitem di bawah. --}}
    <link rel="stylesheet" href="{!! URL::asset('css/report-table.css') !!}?v={{ @filemtime(base_path('public/css/report-table.css')) ?: '1' }}">
    {{-- Dimuat ULANG setelah report-table.css supaya .po-* / .rt-* versi po-table-header.css menang
         saat spesifisitas seri. --}}
    <link rel="stylesheet"
        href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
    <link rel="stylesheet"
        href="{!! URL::asset('css/scrollbar-autohide.css') !!}?v={{ @filemtime(base_path('public/css/scrollbar-autohide.css')) ?: '1' }}">

    <style>
        /* Kotak cari di modal picker Barang: pencarian jalan lewat AJAX (bukan search bawaan
                       DataTables, lihat searchBarangModal()), jadi dirapikan di sini. Skin modalnya sendiri
                       (.rt-picker-v2) ada di report-table.css. */
        #modalPickBarang .barang-search {
            width: 260px;
            max-width: 100%;
            margin-left: auto;
        }

        /* Tabel item form Add/Edit/Detail (#tabelitem) — tampilannya disamakan dengan tabel daftar
               #tabel di #pageHome: kartu berbingkai (sama seperti .card di layout newmasterTest), judul
               kolom abu huruf besar yang menempel (sticky) di kotak scroll, garis baris tipis, dan warna
               hover. Nilai disalin dari blok .po-list-page + #tabel th.rt-th .th-inner di
               po-table-header.css (sama seperti #tabel_add di permintaantransferbarang.blade.php); blok
               itu sendiri tidak dipakai di sini karena #pageForm bukan .po-list-page. Padding sel isi
               sengaja tidak ditulis: `table tbody td { padding: 0 10px !important }` di newmaster.css
               berlaku sama seperti di #tabel (baris rapat). */
        #pageForm .po-item-card {
            background: var(--white, #fff);
            border: 1.5px solid var(--border, #e5e7eb);
            border-radius: var(--radius, 12px);
            overflow: hidden;
        }

        #tabelitem {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        #tabelitem thead th {
            position: sticky;
            top: 0;
            z-index: 20;
            background: #f9fafb;
            padding: 10px 14px;
            text-align: left;
            font-weight: 600;
            font-size: 12px;
            color: var(--text-muted, #6b7280);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            white-space: nowrap;
            border-bottom: 1px solid var(--border, #e5e7eb);
        }

        #tabelitem thead th.num {
            text-align: right;
        }

        #tabelitem tbody td {
            border-bottom: 1px solid #f3f4f6;
            color: var(--text-main, #1f2937);
            vertical-align: middle;
        }

        #tabelitem tbody tr:last-child td {
            border-bottom: none;
        }

        #tabelitem tbody tr:hover td {
            background: #f9fafb;
        }
    </style>
@endsection

@section('content')
    <div id="imagecontainer" class="d-none" style="">
        <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
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

        {{-- Kartu + toolbar + tabel mengikuti accounting/pengajuandph.blade.php (skema po-*,
             DataTables, id #tabel yang sudah terdaftar di po-table-header.css). .tb-report/.content/
             .main dilepas — .main (newmaster.css) memaksa flex-column + overflow:hidden. --}}
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
                             inline (pola sama seperti pengajuandph.blade.php). --}}
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

    <div id="pageForm" class="container-fluid po-form-page" style="display: none">
        <div class="row">
            <div class="col-6 text-left">
                {{-- Judul H2 besar dihapus, disamakan dengan #page2 di
               gudang/purchaseOrder.blade.php (judulnya juga dikosongkan). --}}
            </div>
            <div class="col-6 text-right" id="contentContainer">
                <button type="button" class="btn btn-danger btn-pill-primary btn-action-danger"
                    onclick="buttonCloseForm()">Close</button>
            </div>
        </div>

        <div id="modalBodyAddMain" class="">
            <div class="modal-body" id="formBsGrid">
                <div class="row">

                    <input type="hidden" class="form-control" id="input_nourut">

                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>No Bukti</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <input type="text" class="form-control text-left" id="input_nobukti"
                                        placeholder="" disabled>
                                </div>
                            </div>

                            <div class="col-1">
                                <div class="form-group">
                                    <label>Gudang</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    {{-- Gudang: dropdown select
                         Opsi dimuat sekali lewat muatDropdownGudang() (endpoint
                         polistgudang), lalu dipilih user langsung tanpa modal. --}}
                                    <select class="form-control text-left lockableHeader lockableModeDetail"
                                        id="input_gudang" onchange="onChangeInputGudang()">
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>Tanggal</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <input type="date" class="form-control text-center" id="input_tanggal"
                                        value="{!! date('Y-m-d') !!}" disabled>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>Keterangan</label>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <textarea style="width: 100%; resize: none" rows="2" placeholder="Keterangan"
                                        class="form-control text-left lockableHeader lockableModeDetail" id="input_keterangan"
                                        onblur="onChangeKeterangan()"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="showhidemodalbodyaddmain container-fluid" id="modalBodyAddMainItems">
            {{-- Tampilan tabel item disamakan dengan tabel daftar #tabel (lihat blok CSS #tabelitem di
                 @section('css')): kartu berbingkai + kotak scroll .po-table-wrap dengan judul kolom
                 sticky. Header tabel ini dirender ulang oleh refreshForm() (kolom Actions hilang di
                 mode Detail). --}}
            <div class="container-fluid mt-4">
                <div class="po-item-card">
                    <div class="po-table-wrap" style="max-height:40vh;">
                        <table id="tabelitem" class="data-table">
                            <thead id="tabelitem_header">
                                <tr>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th class="text-center">Sat</th>
                                    <th class="num">Qty Asal</th>
                                    <th class="num">Qty Jadi</th>
                                    <th class="num">HPP</th>
                                    <th class="num">Rp Kredit</th>
                                    <th class="num">Rp Debet</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tabelitem_data" class="lockableModeDetail">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row ">
                <div class="col-md-12 mt-2 text-right" id="contentContainer">
                    <button type="button" class="btn btn-primary btn-action-primary btn-pill-primary hideableModeDetail"
                        onclick="buttonItemAdd()">Tambah Item</button>
                </div>
            </div>

            <div id="formItem" class="container-fluid showhide">
                <div class="row">
                    <div class="col-4">
                        <h4 id="formItem_labelAdd" style="margin-left:-35px;">Add Item</h4>
                        <h4 id="formItem_labelEdit" style="margin-left:-35px;">Edit Item</h4>
                    </div>
                </div>

                <div class="row" id="formBsGrid">
                    <div class="col-md-12">
                        <div class="row">

                            <!-- START OF ITEM Kode Barang & Nama Barang -->
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-12">

                                        <input type="text" class="form-control" id="inputitem_urut" hidden>

                                        <div class="row">
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label>Kode Barang</label>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="input-group form-group">
                                                    {{-- Barang: pola "resolve-or-pick" sama seperti Gudang (lihat guide-update-barang.md) --
                                 bisa diketik langsung + Enter, atau pakai tombol search; kalau match
                                 persis KodeBrg langsung isi field ini, kalau tidak baru modal
                                 #modalPickBarang terbuka (bukan lagi #formBrowseMaster generik). --}}
                                                    <input type="text" class="form-control lockableItemModeEdit"
                                                        id="inputitem_kodebrg" placeholder="Kode Barang"
                                                        onkeypress="if (event.key === 'Enter') { event.preventDefault(); resolveBarang($('#inputitem_kodebrg').val()); }">
                                                    <button type="button" id="btnitem_kodebrg"
                                                        class="btn btn-chip-biru " onclick="browseBarang()">
                                                        <i class="bi bi-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label>Nama Barang</label>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="input-group form-group">
                                                    <input type="text" class="form-control" id="inputitem_namabrg"
                                                        disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- END OF ITEM Kode Barang & Nama Barang -->


                            <!-- START OF ITEM Qty Asal & Qty Jadi -->
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Qty Asal</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group form-group">
                                                    <input type="text" class="form-control text-right"
                                                        id="inputitem_qtyasal" oninput="formatAngkaKetik(this)"
                                                        onblur="formatAngkaInput(this); onChangeQtyAsal()">
                                                    <input type="number" class="form-control" id="inputitem_qtylama"
                                                        hidden>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Satuan</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group form-group">
                                                    <select id="inputitem_satuanasal"
                                                        class="form-control form-select-lg mb-3 text-center"
                                                        aria-label=".form-select-lg example" disabled>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-3" style="margin-top:-10px">
                                                <div class="form-group">
                                                    <label>Qty Jadi</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3" style="margin-top:-10px">
                                                <div class="input-group form-group">
                                                    <input type="text" class="form-control text-right"
                                                        id="inputitem_qtyjadi" oninput="formatAngkaKetik(this)"
                                                        onblur="formatAngkaInput(this); onChangeQtyJadi()">
                                                </div>
                                            </div>

                                            <div class="col-md-3" style="margin-top:-10px">
                                                <div class="form-group">
                                                    <label>Satuan</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3" style="margin-top:-10px">
                                                <div class="input-group form-group">
                                                    <select id="inputitem_satuanjadi"
                                                        class="form-control form-select-lg mb-3 text-center"
                                                        aria-label=".form-select-lg example" disabled>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <!-- END OF ITEM Qty Asal & Qty Jadi -->


                            <!-- START OF ITEM HPP & Biaya -->
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>HPP</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group form-group">
                                                    <input type="text" class="form-control text-right"
                                                        id="inputitem_hpp" oninput="formatAngkaKetik(this)"
                                                        onblur="formatAngkaInput(this)">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Biaya</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group form-group">
                                                    <input type="text" class="form-control text-right"
                                                        id="inputitem_biaya" oninput="formatAngkaKetik(this)"
                                                        onblur="formatAngkaInput(this)">
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <!-- END OF ITEM HPP & Biaya -->

                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-12 text-right" id="contentContainer">
                        <button type="button" class="btn btn-action-danger btn-danger btn-pill-primary"
                            onclick="closeFormItem()">Batal</button>
                        <button type="button" id="buttonSubmitItem"
                            class="btn btn-primary btn-action-primary btn-pill-primary"
                            onclick="submitItem()">Simpan</button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @include('gudang.modalbrowsemaster')

    {{-- Modal picker Barang — skin .rt-picker-v2 dari report-table.css, sama seperti modal picker
         di permintaanpemakaian. Pencarian jalan lewat AJAX ke server (searchBarangModal()), jadi
         kotak cari-nya input biasa, bukan search bawaan DataTables. --}}
    <div class="modal fade rt-picker-v2" id="modalPickBarang" tabindex="-1" role="dialog"
        aria-labelledby="modalPickBarangLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPickBarangLabel">Barang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="text" id="inputBarangPickerSearch" class="form-control barang-search"
                        placeholder="Cari Data" autocomplete="off" oninput="searchBarangModal(this.value)">

                    <table id="tabelPickBarang" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Kode</th>
                                <th scope="col">Nama</th>
                                <th scope="col">Satuan</th>
                            </tr>
                        </thead>
                        <tbody id="tabelPickBarang_data"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    {{-- report-table.js dulu dimuat layout gudang; newmasterTest tidak memuatnya. Tanpa ini
         window.ReportTable undefined (geser & sembunyikan kolom #tabel mati). --}}
    <script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
    <script src="{!! URL::asset('js/ajc-func-core.js') !!}"></script>
    <script src="{!! URL::asset('js/ajc-browsemaster.js') !!}"></script>
    <script>
        const BASE_URL = "{{ url('/') }}";

        const g_tipeformNone = "",
            g_tipeformAdd = "add",
            g_tipeformEdit = "edit",
            g_tipeformDetail = "detail";
        var gtipeform = g_tipeformNone;

        const g_tipeformitemNone = "",
            g_tipeformitemAdd = "add",
            g_tipeformitemEdit = "edit",
            g_tipeformitemDelete = "delete";
        var gtipeformitem = g_tipeformitemNone;

        // Keterangan terakhir yang tersimpan di DB (diisi refreshForm) — onChangeKeterangan() hanya
        // menyimpan kalau isi textarea berbeda dari nilai ini.
        var g_noteTersimpan = "";

        const g_modalNone = "";
        var gmodemodal = g_modalNone;
        var g_modeModal = g_modalNone;

        var dataItem = [];
        var dataBrowse = {};

        if (typeof doSetFormatDate !== 'function') {
            window.doSetFormatDate = function(dateVal, sep) {
                if (!dateVal) return '';
                sep = sep || '/';
                let d = new Date(dateVal);
                if (isNaN(d.getTime())) return String(dateVal);
                let yyyy = d.getFullYear();
                let mm = String(d.getMonth() + 1).padStart(2, '0');
                let dd = String(d.getDate()).padStart(2, '0');
                return yyyy + sep + mm + sep + dd;
            };
        }

        if (typeof nullToEmpty !== 'function') {
            window.nullToEmpty = function(v) {
                return (v === null || v === undefined) ? '' : v;
            };
        }

        if (typeof doCekAkses !== 'function') {
            window.doCekAkses = function(inputId) {
                let v = $('#' + inputId).val();
                if (!Number(v)) {
                    alertify.warning('No access');
                    return false;
                }
                return true;
            };
        }

        // cek status otorisasi terkini ke server sebelum masuk mode edit
        // jaga-jaga klo dokumennya baru aja diotorisasi user lain
        if (typeof doCekOtorisasi !== 'function') {
            window.doCekOtorisasi = function(nobukti, urlName) {
                let boleh = true;
                $.ajax({
                    url: "{!! url('') !!}/" + urlName,
                    type: 'post',
                    async: false,
                    data: {
                        _token: $('#_token').val(),
                        nobukti: nobukti
                    },
                    success: function(res) {
                        let row = Array.isArray(res) ? res[0] : res;
                        let sudahOto = row ? Number(row.isOtorisasi1 ?? row.IsOtorisasi1 ?? 0) : 0;
                        if (sudahOto === 1) {
                            alertify.warning('Sudah diotorisasi, tidak bisa diedit');
                            boleh = false;
                        }
                    },
                    error: function() {
                        alertify.warning('Terjadi kesalahan. Silakan refresh browser.');
                        boleh = false;
                    }
                });
                return boleh;
            };
        }

        // nyala/matiin field header + item berdasarkan class yang sudah dipasang di
        // markup form: lockableHeader, lockableModeDetail, hideableModeDetail, lockableItemModeEdit.
        if (typeof doUnlockHeader !== 'function') {
            window.doUnlockHeader = function() {
                $('.lockableHeader').prop('disabled', false);
                $('.lockableModeDetail').prop('disabled', false);
                $('.hideableModeDetail').show();
            };
        }

        if (typeof doLockModeDetail !== 'function') {
            window.doLockModeDetail = function() {
                $('.lockableHeader').prop('disabled', true);
                $('.lockableModeDetail').prop('disabled', true);
                $('.hideableModeDetail').hide();
            };
        }

        if (typeof doUnlockModeEdit !== 'function') {
            window.doUnlockModeEdit = function() {
                $('.lockableItemModeEdit').prop('disabled', false);
                $('.hideableModeDetail').show();
            };
        }

        if (typeof doUnlockModeDetail !== 'function') {
            window.doUnlockModeDetail = function() {
                $('.lockableModeDetail').prop('disabled', false);
                $('.hideableModeDetail').show();
            };
        }

        if (typeof doLockHeader !== 'function') {
            window.doLockHeader = function() {
                $('.lockableHeader').prop('disabled', true);
            };
        }

        if (typeof doLockModeEdit !== 'function') {
            window.doLockModeEdit = function() {
                $('.lockableItemModeEdit').prop('disabled', true);
            };
        }

        if (typeof doSetEmptyTable !== 'function') {
            window.doSetEmptyTable = function(colspan, message) {
                return '<tr><td colspan="' + colspan + '" class="text-center text-muted">' + message + '</td></tr>';
            };
        }

        // Format angka ke Rupiah 2 desimal (dipakai nampilin Qty/HPP/Harga di tabel item saat mode Edit/Detail).
        if (typeof formatCurrency !== 'function') {
            window.formatCurrency = function(v) {
                let n = Number(v) || 0;
                return n.toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            };
        }

        function unformatAngka(angka) {
            if (!angka) return 0;
            return parseFloat(String(angka).replace(/,/g, '')) || 0;
        }

        // Disalin dari gudang/newmasterx.blade.php: dulu tersedia global lewat layout gudang,
        // layout newmasterTest tidak mendefinisikannya.
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

        function formatAngkaInput(el) {
            el.value = formatAngka(unformatAngka(el.value).toFixed(2));
        }

        // Dipasang di oninput supaya separator ribuan langsung muncul sambil mengetik, tidak menunggu
        // pindah fokus (onblur formatAngkaInput() tetap menormalkan ke 2 desimal). Sama seperti
        // formatAngkaKetik() di accounting/memorialkoreksi.blade.php.
        function formatAngkaKetik(el) {
            let posDariKanan = el.value.length - el.selectionStart;
            let minus = el.value.trim().startsWith('-') ? '-' : '';
            let raw = el.value.replace(/[^0-9.]/g, '');

            let titikIndex = raw.indexOf('.');
            let bulat = titikIndex === -1 ? raw : raw.slice(0, titikIndex);
            let desimal = titikIndex === -1 ? '' : raw.slice(titikIndex + 1).replace(/\./g, '').slice(0, 2);

            bulat = bulat.replace(/^0+(?=\d)/, '');
            if (bulat === '') {
                bulat = '0';
            }

            let bulatFormatted = '';
            for (let i = 0; i < bulat.length; i++) {
                if (i != 0 && (bulat.length - i) % 3 == 0) {
                    bulatFormatted += ',';
                }
                bulatFormatted += bulat[i];
            }

            el.value = minus + bulatFormatted + (titikIndex !== -1 ? '.' + desimal : '');

            let posBaru = Math.max(0, el.value.length - posDariKanan);
            el.setSelectionRange(posBaru, posBaru);
        }

        if (typeof cekNotEmpty !== 'function') {
            window.cekNotEmpty = function(inputId) {
                let v = $('#' + inputId).val();
                return v !== undefined && v !== null && String(v).trim() !== '';
            };
        }

        if (typeof cekNotZero !== 'function') {
            window.cekNotZero = function(inputId) {
                return Number(String($('#' + inputId).val()).replace(/,/g, '')) !== 0;
            };
        }

        if (typeof messageRequired !== 'function') {
            window.messageRequired = function(label) {
                return label + ' harus diisi';
            };
        }

        if (typeof cekNotDuplicate !== 'function') {
            window.cekNotDuplicate = function(list, key, value) {
                let v = String(nullToEmpty(value)).trim().toLowerCase();
                return !(list || []).some(function(row) {
                    let k = Object.keys(row).find(function(x) {
                        return x.toLowerCase() === String(key).toLowerCase();
                    });
                    return k !== undefined && String(nullToEmpty(row[k])).trim().toLowerCase() === v;
                });
            };
        }

        if (typeof messageDuplicate !== 'function') {
            window.messageDuplicate = function(label) {
                return label + ' sudah ada';
            };
        }

        if (typeof setEmptyNumberToZero !== 'function') {
            window.setEmptyNumberToZero = function(inputId) {
                if ($('#' + inputId).val() === '') {
                    $('#' + inputId).val(0);
                }
                return $('#' + inputId).val();
            };
        }

        if (typeof doCekPeriode !== 'function') {
            window.doCekPeriode = function(bulanId, tahunId, tanggalId) {
                let bulan = Number($('#' + bulanId).val());
                let tahun = Number($('#' + tahunId).val());
                let tgl = $('#' + tanggalId).val();
                if (!tgl) return true;
                let d = new Date(tgl);
                if (isNaN(d.getTime())) return true;
                if ((d.getMonth() + 1) !== bulan || d.getFullYear() !== tahun) {
                    alertify.warning('Tanggal tidak sesuai periode');
                    return false;
                }
                return true;
            };
        }

        if (typeof doGenerateNoBukti !== 'function') {
            window.doGenerateNoBukti = function(kode) {
                let _token = $('#_token').val();
                let result = {
                    Nobukti: '',
                    Nourut: ''
                };
                $.ajax({
                    url: "{!! url('spnobukti') !!}",
                    type: 'post',
                    async: false,
                    data: {
                        _token,
                        kode
                    },
                    success: function(res) {
                        if (res && res[0]) {
                            result.Nobukti = res[0].Nobukti;
                            result.Nourut = res[0].Nourut;
                        }
                    },
                    error: function(err) {
                        console.log(err);
                        alertify.warning('Terjadi kesalahan. Silakan refresh browser.');
                    }
                });
                return result;
            };
        }

        let lastRows = (function() {
            // paint pertama tanpa AJAX; reloadData() menyegarkan setelahnya.
            let belum = @json($listKMBJ);
            let sudah = @json($listSdhOto);
            return belum.concat(sudah);
        })();
        let globalOtorisasi = "2"; // filter modal: 2=Semua, 1=Sudah Otorisasi, 0=Belum Otorisasi
        let pageSize = 10; // dipakai sebagai pageLength DataTables di renderTabel()
        // No. bukti yang barusan disimpan/diotorisasi: renderTabel() memindahkan tabel ke halaman
        // tempat baris itu berada, lalu mengosongkannya lagi. Diisi reloadData(_focusNobukti).
        let fokusNobukti = '';

        var g_href = 'ubahkemasanbarang';
        var g_modeReport = '2';
        var gcart_header = [];
        var gsum_issubtotal = 0;
        var gsum_isgrandtotal = 0;
        var gct_desimal_max = 4;

        function setDefaultHeader() {
            // [ field, label, visible, type, total, decimals ]
            gcart_header = [
                ['GroupNobukti', 'No Bukti', 1, 'varchar', 0, 0],
                ['Tanggal', 'Tanggal', 1, 'date', 0, 0]
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

        function filterByOtorisasi(rows, filterVal) {
            if (filterVal === '1') { // NeedOtorisasi = 0 itu sudah
                return rows.filter(r => Number(pickCI(r, 'NeedOtorisasi')) === 0);
            }
            if (filterVal === '0') { // NeedOtorisasi = 1 itu belum
                return rows.filter(r => Number(pickCI(r, 'NeedOtorisasi')) === 1);
            }
            return rows;
        }

        // Markup + pemetaan warna/ikon tombol disalin dari aksiButtonsHtml() di
        // accounting/pengajuandph.blade.php (.po-aksi-wrap, cuma title, tanpa tooltip Bootstrap).
        // Fungsi yang dipanggil tetap milik halaman ini (buttonEdit/submitPrint/...).
        function aksiButtonsHtml(r) {
            const nobukti = r.Nobukti;
            let tombolAksi =
                '<button class="btn btn-warning btn-sm" type="button" title="Detail" onclick="buttonDetail(\'' +
                nobukti + '\')"><i class="bi bi-info"></i></button>';

            if (Number(pickCI(r, 'NeedOtorisasi')) === 0) {
                // Sudah otorisasi — Batal Otorisasi + Print
                tombolAksi +=
                    '<button class="btn btn-danger btn-sm" type="button" title="Batal Otorisasi" onclick="buttonBatalOtorisasi(\'' +
                    nobukti + '\')"><i class="bi bi-key"></i></button>' +
                    '<button class="btn btn-primary btn-sm" type="button" title="Print" onclick="submitPrint(\'' +
                    nobukti + '\')"><i class="bi bi-printer"></i></button>';
            } else {
                // Belum otorisasi — Edit + Otorisasi
                tombolAksi +=
                    '<button class="btn btn-info btn-sm" type="button" title="Otorisasi" onclick="buttonOtorisasi(\'' +
                    nobukti + '\')"><i class="bi bi-key"></i></button>' +
                    '<button class="btn btn-success btn-sm" type="button" title="Edit" onclick="buttonEdit(\'' +
                    nobukti + '\')"><i class="bi bi-pen"></i></button>';
            }

            return '<div class="po-aksi-wrap">' + tombolAksi + '</div>';
        }

        function stripTanggalSuffix(v) {
            if (!v) return v;
            let idx = String(v).search(/\s*Tanggal\s*:/i);
            return (idx >= 0) ? String(v).substring(0, idx).trim() : v;
        }

        /* Bar kolom tersembunyi harus berada tepat di atas tabelnya. DataTables membungkus
           tabel dengan #<id>_wrapper saat init, jadi acuannya ikut berpindah — sama seperti
           rtPindahBar() di pengajuandph.blade.php / bonsementara.blade.php. */
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
        // dengan peringatan — sama seperti ikatPeriode() di pengajuandph.blade.php.
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
        // setelah filter Otorisasi + pencarian + sorting). Menggantikan gotoRowPage() versi
        // manual yang menghitung indeks dari array sendiri.
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

        // Tinggi tabel mengikuti sisa ruang layar — disalin dari dphAturTinggiTabel() di
        // pengajuandph.blade.php (#page1 -> #pageHome). Berhenti diam-diam kalau #pageHome sedang
        // disembunyikan (reloadData() masih bisa terpanggil saat #pageForm tampil); dihitung ulang
        // begitu #pageHome ditampilkan lagi (lihat buttonCloseForm()).
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
            // NeedOtorisasi/OtoUser1/TglOto1 dikeluarkan dari cols meski masih tersimpan di susunan
            // kolom lama — ketiganya kolom tetap (Oto/User Oto/Tgl Oto) yang ditambahkan sendiri
            // di bawah, bukan kolom geser/sembunyi (sama seperti pengajuandphtunai.blade.php).
            const cols = gcart_header.filter(c => c[2] === 1 &&
                c[0] !== 'NeedOtorisasi' && c[0] !== 'OtoUser1' && c[0] !== 'TglOto1');

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

            // Cari/paging/sorting dipegang DataTables — di sini cuma filter Otorisasi dari modal.
            const rows = filterByOtorisasi(lastRows, globalOtorisasi);

            let html = '';
            rows.forEach(function(r) {
                html += '<tr data-nobukti="' + String(nullToEmpty(r.Nobukti)).replace(/"/g, '&quot;') + '">';
                html += '<td class="text-center">' + aksiButtonsHtml(r) + '</td>';
                html += cols.map(function(c) {
                    const v = pickCI(r, c[0]);
                    if (c[0] === 'GroupNobukti') {
                        return '<td>' + nullToEmpty(stripTanggalSuffix(v)) + '</td>';
                    }
                    if (c[3] === 'date') {
                        return '<td>' + (v ? doSetFormatDate(v, '/') : '') + '</td>';
                    }
                    return '<td>' + nullToEmpty(v) + '</td>';
                }).join('');
                let sudahOto = Number(pickCI(r, 'NeedOtorisasi')) === 0;
                let otoUser = pickCI(r, 'OtoUser1');
                let tglOto = pickCI(r, 'TglOto1');
                html += sudahOto ?
                    '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>' :
                    '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>';
                html += '<td>' + nullToEmpty(otoUser) + '</td>';
                html += '<td>' + (tglOto ? doSetFormatDate(tglOto, '/') : '') + '</td>';
                html += '</tr>';
            });

            document.getElementById('tabel2_data').innerHTML = html;

            // Susunan opsi disalin dari pengajuandph.blade.php / bonsementara.blade.php: dom
            // membuat sendiri kotak scroll .po-table-wrap (header sticky) + baris info & pager.
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

        // Filter Modal (Otorisasi: Semua/Sudah Otorisasi/Belum)
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
            renderTabel(); // DataTables di-init ulang, jadi otomatis kembali ke halaman 1
            $('#modalFilter').modal('hide');
        }

        $(document).ready(function() {
            doSetHeader(g_modeReport);
            ReportTable.init({
                table: '#tabel',
                bar: '#rtBar',
                onChange: renderTabel
            });
            renderTabel();
            muatDropdownGudang();
        });

        function reloadData(_focusNobukti) {
            let listKMBJ = [],
                listSdhOto = [];

            $.ajax({
                url: "{!! url('kmbjloadall') !!}",
                type: "get",
                async: false,
                data: {
                    date1: $('#inputDate1').val(),
                    date2: $('#inputDate2').val()
                },
                success: function(res) {
                    listKMBJ = res.listKMBJ || [];
                    listSdhOto = res.listSdhOto || [];
                }
            });

            lastRows = listKMBJ.concat(listSdhOto);
            // Tanpa _focusNobukti tabel selalu kembali ke halaman 1 (DataTables di-init ulang).
            fokusNobukti = _focusNobukti || '';
            renderTabel();
        }

        function submitPrint(nobukti) {
            // for (var i = 0; i < 30; i++) {
            //   dataPrint.push(dataPrint[0])
            // }
            let _token = $('#_token').val()
            $.ajax({
                url: "{!! url('kmbjdetailCetak') !!}",
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

                },
                error: function(err) {
                    console.log(err);
                    alertify.warning('Terjadi kesalahan. Silakan refresh browser.');
                }
            })

            if (!dataPrint || !dataPrint.length) {
                alertify.warning('Data cetak tidak ditemukan');
                return;
            }

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
            let tanggalOnly = (dataPrint[0].Tanggal || '').split(' ')[0];

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
                  <div class="pb-1" style="width: 100%">No Bukti : ` + dataPrint[0].Nobukti + `</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">Tanggal : ` + tanggalOnly + `</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">Keterangan : ${dataPrint[0].note ?? '-'}</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
              </div>


              <div style="width: 38%">
                <div style="display: flex; width: 100%">
                  <h2 class="m-0 pb-2">UBAH KEMASAN</h2>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Gudang</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">` + dataPrint[0].NamaGDG + `</div>
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
                    <td class="text-center" style="width: 30%">KODE BARANG</td>
                    <td class="text-center" style="width: 50%">NAMA BARANG</td>
                    <td class="text-center" style="width: 10%">SAT</td>
                    <td class="text-center" style="width: 15%">HASIL</td>
                    <td class="text-center" style="width: 15%">BAHAN</td>
                  </tr>
                </thead> `;

            let z = 0
            let tempPrintStr = ``
            // buat hitung grandtotal
            let grandTotalHasil = 0;
            let grandTotalBahan = 0;

            dataPrint.forEach(item => {

                if (item.Qntdb) {
                    grandTotalHasil += Number(item.Qntdb) || 0;
                }

                if (item.QntCr) {
                    grandTotalBahan += Number(item.QntCr) || 0;
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
                tempPrintStr += `<tbody border="1">`;
                item.forEach((itemSub, j) => {
                    tempPrintStr += ``



                    tempPrintStr += `
         <tr>
         <td class="text-align: center"
               style="width: 2%; ">${z+1}</td>
         <td class="text-align: left"
               style="width: 30%;  ">${itemSub.kodebrg}</td>
         <td class="text-align: left"
               style="width: 50%;">${itemSub.namaBrg}</td>
         <td class="text-align: text-center"
               style="width: 10%;">${itemSub.Satuan}</td>
         <td class="text-align: text-right"
               style="width: 15%;">${itemSub.Qntdb ? parseFloat(itemSub.Qntdb).toFixed(2) : ''}</td>
         <td class="text-align: text-right"
               style="width: 15%;">${itemSub.QntCr ? parseFloat(itemSub.QntCr).toFixed(2) : ''}</td>
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



         <div style="width:100%; display:flex; font-weight:bold; margin-top:-130px;">

            <div style="width:77%; text-align:right; padding-right:10px;">
              Total :
            </div>

            <div style="width:11%; text-align:right;">
              ${grandTotalHasil.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              })}
            </div>

            <div style="width:12%; text-align:right;">
              ${grandTotalBahan.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              })}
            </div>

          </div>

         </div>


           <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: 20px ; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%">Dibuat Oleh</td>
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

        function closeFormItem() {
            $('.showhide').hide();
            doUnlockHeader();
            doUnlockModeEdit();

            $("#input_gudang").prop("disabled", dataItem.length > 0);
        }

        function refreshForm(_nobukti = "") {
            let rowTable = "";
            dataItem = [];

            if (_nobukti == "") {
                let cHide = (gtipeform == g_tipeformDetail) ? 1 : 0;
                rowTable += doSetEmptyTable(9 - cHide, "Belum ada barang");
            } else {
                let _token = $("#_token").val();
                $.ajax({
                    url: "{!! url('kmbjgetdetail') !!}",
                    type: "post",
                    async: false,
                    data: {
                        _token,
                        nobukti: _nobukti
                    },
                    success: function(res) {
                        if (!res.list.length) {
                            alertify.warning("Data habis");
                            $('#pageForm').hide();
                            $('#pageHome').show();
                            aturTinggiTabel();
                        } else {
                            let dataHeader = res.header[0];
                            dataItem = res.list;

                            dataItem.forEach((item, i) => {
                                rowTable += `<tr>
                <td>${nullToEmpty(item.kodebrg)}</td>
                <td>${nullToEmpty(item.namaBrg)}</td>
                <td class="text-center">${nullToEmpty(item.Satuan)}</td>
                <td class="text-right">${item.QntCr ? formatCurrency(item.QntCr) : '0.00'}</td>
                <td class="text-right">${item.Qntdb ? formatCurrency(item.Qntdb) : '0.00'}</td>
                <td class="text-right">${item.Hpp ? formatCurrency(item.Hpp) : '0.00'}</td>
                <td class="text-right">${item.HrgADO ? formatCurrency(item.HrgADO) : '0.00'}</td>
                <td class="text-right">${item.HrgAdi ? formatCurrency(item.HrgAdi) : '0.00'}</td>
                ${gtipeform == g_tipeformDetail ? `` :
                `<td>
                                  <div class="action-buttons">
                                    <button class="btn btn-action-sm btn-action-success" type="button" onclick="buttonItemEdit(${i})"><i class="bi bi-pen"></i></button>
                                    <button class="btn btn-action-sm btn-action-danger" type="button" onclick="buttonItemDelete(${i})"><i class="bi bi-trash"></i></button>
                                  </div>
                                </td>`}
              </tr>`;
                            });

                            $("#input_nobukti").val(dataHeader.Nobukti);
                            $("#input_nourut").val(dataHeader.NOURUT);
                            $("#input_tanggal").val(doSetFormatDate(dataHeader.tanggal, "-"));
                            $("#input_gudang").val(dataHeader.Kodegdg);
                            g_noteTersimpan = nullToEmpty(dataHeader.note);
                            $("#input_keterangan").val(g_noteTersimpan);

                            dataBrowse['gudang'] = dataHeader.Kodegdg;
                        }
                    },
                    error: function(err) {
                        console.log(err)
                        console.log(err.status)
                        console.log(err.statusText)
                        alertify.warning('Terjadi kesalahan, silahkan refresh browser')
                    }
                });
            }

            $("#tabelitem_data").html(rowTable);

            let rowHeader = `
    <tr>
      <th>Kode Barang</th>
      <th>Nama Barang</th>
      <th class="text-center">Sat</th>
      <th class="num">Qty Asal</th>
      <th class="num">Qty Jadi</th>
      <th class="num">HPP</th>
      <th class="num">Rp Kredit</th>
      <th class="num">Rp Debet</th>
      ${gtipeform == g_tipeformDetail ? `` : `<th class="text-center">Actions</th>`}
    </tr>
    `;
            $("#tabelitem_header").html(rowHeader);
        }

        function buttonCloseForm() {
            gtipeform = g_tipeformNone;
            cleanFormItem();
            doUnlockModeDetail();
            $('#pageForm').hide();
            $('#pageHome').show();
            // #pageHome bisa saja sudah dirender saat masih disembunyikan (reloadData() dipanggil
            // dari #pageForm) — aturTinggiTabel() melewati perhitungan saat itu, jadi dihitung
            // ulang sekarang setelah #pageHome benar-benar terlihat lagi.
            aturTinggiTabel();
        }

        function buttonAdd() {
            if (!doCekAkses("akses_istambah")) return;
            if (!doCekPeriode("periode_bulan", "periode_tahun", "input_tanggal")) return;

            gtipeform = g_tipeformAdd;
            $('.showhide').hide();

            cleanFormHeader();
            refreshForm();
            doUnlockHeader();

            let nb = doGenerateNoBukti("KMBJ");
            $("#input_nobukti").val(nb.Nobukti);
            $("#input_nourut").val(nb.Nourut);

            dataBrowse['gudang'] = "";

            $('#pageHome').hide();
            $('#pageForm').show();
        }

        function cleanFormHeader() {
            $("#input_nobukti").val("");
            $("#input_nourut").val("");
            $("#input_gudang").val("");
            $("#input_keterangan").val("");
            g_noteTersimpan = "";
        }

        function buttonEdit(_nb) {
            if (!doCekAkses("akses_iskoreksi")) return;
            if (!doCekOtorisasi(_nb, "kmbjcekotorisasi")) return;

            gtipeform = g_tipeformEdit;
            $('.showhide').hide();

            refreshForm(_nb);
            doUnlockHeader();

            $("#input_gudang").prop("disabled", dataItem.length > 0);

            $('#pageHome').hide();
            $('#pageForm').show();
        }

        function buttonDetail(_nb) {
            if (!doCekAkses("akses_iskoreksi")) return;
            gtipeform = g_tipeformDetail;
            $('.showhide').hide();

            refreshForm(_nb);
            doLockModeDetail();

            $('#pageHome').hide();
            $('#pageForm').show();
        }

        function buttonOtorisasi(_nb) {
            if (!doCekAkses("akses_isotorisasi1")) return;

            let _token = $('#_token').val();

            $.ajax({
                url: "{!! url('kmbjupdateotorisasi') !!}",
                type: "post",
                async: false,
                data: {
                    _token,
                    nobukti: _nb
                },
                success: function(res) {
                    if (res > 0) {
                        alertify.success('Berhasil otorisasi');
                        reloadData(_nb);
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

        function buttonBatalOtorisasi(_nb) {
            if (!doCekAkses("akses_isbatal")) return;

            var dlgBatalOtorisasi = alertify.confirm(
                'Batal Otorisasi',
                'Yakin ingin membatalkan otorisasi No Bukti ' + _nb + ' ?',
                function() {
                    let _token = $('#_token').val();

                    $.ajax({
                        url: "{!! url('kmbjupdatebatalotorisasi') !!}",
                        type: "post",
                        async: false,
                        data: {
                            _token,
                            nobukti: _nb
                        },
                        success: function(res) {
                            if (res > 0) {
                                alertify.success('Berhasil batal otorisasi');
                                reloadData(_nb);
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
                    alertify.error('Action cancelled');
                }
            );
            dlgBatalOtorisasi.elements.root.classList.add('ajs-app-buttons', 'is-danger');
        }

        // Mode Add: keterangan ikut terkirim bersama item pertama (cart["note"] di cekValidate), dan
        // setelah item pertama tersimpan successAdd() memindah form ke mode Edit. Mode Edit: header
        // sudah ada di DBUBAHKEMASAN, jadi keterangan disimpan langsung saat textarea kehilangan fokus.
        function onChangeKeterangan() {
            if (gtipeform != g_tipeformEdit) return;

            let nb = $("#input_nobukti").val();
            let value = $("#input_keterangan").val();
            if (value === g_noteTersimpan) return;

            doOnChangeHeader(nb, "NOTE", value, "kmbjonchangeheader", function() {
                g_noteTersimpan = value;
                alertify.success("Keterangan tersimpan");
            });
        }

        // Update satu kolom header lewat endpoint onChangeHeader (controller hanya menerima kolom
        // yang ada di allow-list-nya). Respon = jumlah baris ter-update.
        function doOnChangeHeader(_nobukti, _field, _value, _urlName, _onSuccess) {
            if (!_nobukti) return;

            $.ajax({
                url: "{!! url('') !!}/" + _urlName,
                type: "post",
                data: {
                    _token: $("#_token").val(),
                    nobukti: _nobukti,
                    field: _field,
                    value: _value
                },
                success: function(res) {
                    if (Number(res) > 0) {
                        if (_onSuccess) _onSuccess(res);
                    } else {
                        console.log(res);
                        alertify.warning("Gagal menyimpan, No Bukti " + _nobukti + " tidak ditemukan");
                    }
                },
                error: function(err) {
                    console.log(err);
                    alertify.warning("Terjadi kesalahan, silahkan refresh browser");
                }
            });
        }

        function onChangeQtyAsal() {
            let lock = cekNotZero("inputitem_qtyasal", false); // jika tidak 0, kunci
            $('#inputitem_qtyjadi').prop('disabled', lock);
            $('#inputitem_satuanjadi').prop('disabled', lock);
            $('#inputitem_biaya').prop('disabled', lock);
        }

        function onChangeQtyJadi() {
            let lock = cekNotZero("inputitem_qtyjadi", false); // jika tidak 0, kunci
            $('#inputitem_qtyasal').prop('disabled', lock);
            $('#inputitem_satuanasal').prop('disabled', lock);
        }

        function buttonItemAdd() {
            if (!cekNotEmpty("input_gudang")) {
                return alertify.warning(messageRequired("Gudang"));
            }
            if (dataBrowse['gudang'] !== $("#input_gudang").val()) {
                return alertify.warning("Data Gudang tidak sesuai");
            }

            gtipeformitem = g_tipeformitemAdd;
            $('.showhide').hide();
            $('#formItem_labelAdd').show();
            $('#formItem_labelEdit').hide();

            doLockHeader();
            cleanFormItem();

            dataBrowse['barang'] = "";

            $('#formItem').show();

            $("#btnitem_kodebrg").prop("disabled", false);
            $("#inputitem_kodebrg").prop("disabled", false);
            $("#inputitem_qtyasal").prop("disabled", false);
            $("#inputitem_satuanasal").prop("disabled", false);
            $("#inputitem_qtyjadi").prop("disabled", false);
            $("#inputitem_satuanjadi").prop("disabled", false);
            $("#inputitem_biaya").prop("disabled", false);

            document.getElementById("inputitem_kodebrg").scrollIntoView();
        }

        function buttonItemEdit(_urut) {
            gtipeformitem = g_tipeformitemEdit;
            $('.showhide').hide();
            $('#formItem_labelEdit').show();
            $('#formItem_labelAdd').hide();

            doLockHeader();
            doLockModeEdit();
            cleanFormItem();

            let item = dataItem[_urut];
            $("#inputitem_urut").val(item.urut);
            $("#inputitem_hpp").val(formatAngka((parseFloat(item.Hpp) || 0).toFixed(2)));
            $("#inputitem_biaya").val(formatAngka((parseFloat(item.biaya) || 0).toFixed(2)));

            if (item.NamaBrg != "") {
                showSatuanBarang(item.nosat, item.kodebrg, item.namaBrg,
                    item.brgSat1, item.brgSat2, item.brgSat3,
                    item.brgIsi1, item.brgIsi2, item.brgIsi3,
                    item.QntCr, item.Qntdb);
            }

            dataBrowse['barang'] = item.kodebrg;

            $('#formItem').show();

            document.getElementById("inputitem_kodebrg").scrollIntoView();
        }

        function buttonItemDelete(_urut) {
            if (!doCekAkses("akses_ishapus")) return;

            var dlgHapusItem = alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + nullToEmpty(dataItem[
                    _urut].NamaBrg) +
                ' ?',
                function() {
                    gtipeformitem = g_tipeformitemDelete;
                    $("#inputitem_urut").val(dataItem[_urut].urut);
                    submitItem();
                },
                function() {
                    console.log('Penghapusan dibatalkan')
                }
            );
            dlgHapusItem.elements.root.classList.add('ajs-app-buttons', 'is-danger');
        }

        function cleanFormItem() {
            $("#inputitem_urut").val(0);
            $("#inputitem_kodebrg").val("");
            $("#inputitem_namabrg").val("");
            $("#inputitem_qtyasal").val(0);
            $("#inputitem_qtyjadi").val(0);
            $("#inputitem_qtylama").val(0);

            $('#inputitem_satuanasal').empty();
            $('#inputitem_satuanasal').prop("disabled", true);
            $('#inputitem_satuanjadi').empty();
            $('#inputitem_satuanjadi').prop("disabled", true);

            $("#inputitem_hpp").val(0);
            $("#inputitem_biaya").val(0);
        }

        // Gudang: dropdown select
        let listGudangDropdown = [];

        function muatDropdownGudang() {
            let _token = $("#_token").val();

            $.ajax({
                url: "{!! url('polistgudang') !!}",
                type: "post",
                async: false,
                data: {
                    _token
                },
                success: function(res) {
                    listGudangDropdown = res;

                    let selectEl = document.getElementById("input_gudang");
                    let kodeTerpilih = selectEl.value;

                    selectEl.innerHTML = '<option value="">Pilih Gudang</option>';
                    listGudangDropdown.forEach((item) => {
                        let opt = document.createElement('option');
                        opt.value = item.KODEGDG;
                        opt.textContent = `${item.KODEGDG} - ${item.NAMA}`;
                        selectEl.appendChild(opt);
                    });

                    if (listGudangDropdown.some(item => item.KODEGDG === kodeTerpilih)) {
                        selectEl.value = kodeTerpilih;
                    }
                },
                error: function(err) {
                    console.log(err);
                    alertify.warning('Terjadi kesalahan, silahkan refresh browser');
                }
            });
        }

        function onChangeInputGudang() {
            dataBrowse['gudang'] = $('#input_gudang').val();
        }

        function doBrowseBarang() {
            bm_filterMode = true;
            return true;
        }

        let barangLookupBusy = false;
        let listBarangPicker = [];
        let barangSearchDebounce = null;

        let barangRequestSeq = 0;
        let currentBarangXhr = null;

        function fetchBarangList(term, callback) {
            if (!cekNotEmpty("input_gudang")) {
                alertify.warning(messageRequired("Gudang"));
                return;
            }

            if (currentBarangXhr) {
                currentBarangXhr.abort();
            }

            const seq = ++barangRequestSeq;
            barangLookupBusy = true;
            currentBarangXhr = $.ajax({
                url: "{!! $barang !!}",
                type: "get",
                data: {
                    filter: term,
                    kodegdg: $("#input_gudang").val()
                },
                success: function(res) {
                    barangLookupBusy = false;
                    if (seq !== barangRequestSeq) return; // response basi, abaikan
                    callback(res.table || []);
                },
                error: function(err) {
                    barangLookupBusy = false;
                    if (err.statusText === 'abort') return;
                    console.log(err);
                    alertify.warning('Terjadi kesalahan, silahkan refresh browser');
                }
            });
        }

        function findExactBarang(list, term) {
            let needle = term.toLowerCase();
            return list.find(b => String(b.KodeBrg || '').trim().toLowerCase() === needle);
        }

        function resolveBarang(term) {
            term = (term || '').trim();

            if (!cekNotEmpty("input_gudang")) {
                return alertify.warning(messageRequired("Gudang"));
            }

            if (term === '') {
                return openBarangPicker('');
            }

            if (barangLookupBusy) return;

            fetchBarangList(term, function(res) {
                let hit = findExactBarang(res, term);
                hit ? applyBarangToForm(hit) : openBarangPicker(term, res);
            });
        }

        // Tombol search: selalu buka picker (tidak auto-resolve), supaya barang yang sudah dipilih bisa diganti.
        function browseBarang() {
            if (!cekNotEmpty("input_gudang")) {
                return alertify.warning(messageRequired("Gudang"));
            }

            openBarangPicker(($('#inputitem_kodebrg').val() || '').trim());
        }

        function applyBarangToForm(item) {
            buttonBrowsePickBarang(item.KodeBrg, item.NamaBrg, item.Sat1, item.Sat2, item.sat3, item.isi1, item.isi2, item
                .isi3);
            $('#modalPickBarang').modal('hide');
        }

        function initBarangTable(list) {
            listBarangPicker = list;

            if ($.fn.DataTable.isDataTable('#tabelPickBarang')) {
                $('#tabelPickBarang').DataTable().destroy();
            }

            let rowTable = '';
            list.forEach((item, i) => {
                rowTable += `<tr class="pick-row" onclick="buttonPickBarang(${i})">
        <td>${item.KodeBrg}</td>
        <td>${item.NamaBrg}</td>
        <td>${nullToEmpty(item.Sat1)}</td>
      </tr>`;
            });
            $('#tabelPickBarang_data').html(rowTable);


            $('#tabelPickBarang').DataTable({
                lengthChange: false,
                paging: true,
                searching: false,
                info: list.length > 0
            });
        }

        function openBarangPicker(term, preFetched) {
            let showWith = function(list) {
                $('#inputBarangPickerSearch').val(term || '');
                $('#modalPickBarang').modal('show');
                initBarangTable(list);
            };

            preFetched ? showWith(preFetched) : fetchBarangList(term || '', showWith);
        }

        function searchBarangModal(term) {
            clearTimeout(barangSearchDebounce);
            barangSearchDebounce = setTimeout(function() {
                fetchBarangList(term, initBarangTable);
            }, 300);
        }

        function buttonPickBarang(index) {
            applyBarangToForm(listBarangPicker[index]);
        }

        function buttonBrowsePickBarang(_kode, _nama, _sat1, _sat2, _sat3, _isi1, _isi2, _isi3) {
            $("#inputitem_kodebrg").val(_kode);
            $("#inputitem_namabrg").val(_nama);

            // Ganti barang -> reset qty & buka kunci saling-kunci Qty Asal/Qty Jadi
            $("#inputitem_qtyasal").val(0);
            $("#inputitem_qtyjadi").val(0);
            $("#inputitem_qtyasal, #inputitem_qtyjadi").prop("disabled", false);
            onChangeQtyAsal();
            onChangeQtyJadi();

            let satuanAsalSelect = $("#inputitem_satuanasal");
            satuanAsalSelect.empty(); // Clear previous options

            let satuanJadiSelect = $("#inputitem_satuanjadi");
            satuanJadiSelect.empty(); // Clear previous options

            // Array of satuan objects with number and value
            const satuanList = [{
                    nosat: 1,
                    sat: _sat1,
                    isi: _isi1
                },
                {
                    nosat: 2,
                    sat: _sat2,
                    isi: _isi2
                },
                {
                    nosat: 3,
                    sat: _sat3,
                    isi: _isi3
                }
            ];

            // Append valid satuan options
            let added = 0;
            satuanList.forEach(item => {
                if (item.sat && item.sat.trim() !== '') {
                    satuanAsalSelect.append(
                        `<option value="${item.nosat}||${item.sat}||${item.isi}">${item.nosat} - ${item.sat}</option>`
                    );
                    satuanJadiSelect.append(
                        `<option value="${item.nosat}||${item.sat}||${item.isi}">${item.nosat} - ${item.sat}</option>`
                    );
                    added++;
                }
            });

            satuanAsalSelect.prop("disabled", added === 0);
            satuanJadiSelect.prop("disabled", added === 0);

            dataBrowse['barang'] = _kode;
        }

        function showSatuanBarang(_nosat, _kode, _nama, _sat1, _sat2, _sat3, _isi1, _isi2, _isi3, _qtyasal, _qtyjadi) {
            buttonBrowsePickBarang(_kode, _nama, _sat1, _sat2, _sat3, _isi1, _isi2, _isi3);

            const satuanSelect = $("#inputitem_satuan");

            // Find and select the matching option
            satuanSelect.find("option").each(function() {
                const value = $(this).val(); // e.g., "2||Box||12"
                const parts = value.split("||");

                if (parseInt(parts[0]) === _nosat) {
                    satuanSelect.val(value); // set this option as selected
                    return false; // break loop
                }
            });

            $("#inputitem_qtyasal").val(formatAngka((parseFloat(_qtyasal) || 0).toFixed(2)));
            $("#inputitem_qtyjadi").val(formatAngka((parseFloat(_qtyjadi) || 0).toFixed(2)));
            $("#inputitem_qtylama").val(parseFloat(_qtyasal).toFixed(2));

            onChangeQtyAsal();
            onChangeQtyJadi();
        }

        function getStockAkhir(_nosat, _date, _kodegdg, _kodebrg) {
            let stock = [];
            $.ajax({
                url: "{!! url('spgetstockakhir') !!}",
                type: "get",
                async: false,
                data: {
                    nosat: _nosat,
                    date: _date,
                    kodegdg: _kodegdg,
                    kodebrg: _kodebrg
                },
                success: function(res) {
                    stock = res.stock;
                }
            });

            return (stock.length > 0) ? stock[0].SALDOQNT : 0;
        }

        function cekValidate(_choice) {
            let cart = {};

            cart["choice"] = _choice;
            cart["nobukti"] = $("#input_nobukti").val();
            if (_choice == "D") {
                cart["urut"] = $("#inputitem_urut").val();
                return cart;
            }

            // CEK EMPTY
            if (!cekNotEmpty("inputitem_kodebrg")) {
                return messageRequired("Barang");
            }

            // CEK VALIDASI KODE / NO BUKTI
            if (dataBrowse['gudang'] !== $("#input_gudang").val()) {
                return "Data Gudang tidak sesuai";
            }
            if (dataBrowse['barang'] !== $("#inputitem_kodebrg").val()) {
                return "Data Barang tidak sesuai";
            }

            // CEK LAIN-LAIN
            let brg = $("#inputitem_kodebrg").val();
            if (_choice === "I") {
                if (!cekNotDuplicate(dataItem, "kodebrg", brg)) {
                    return messageDuplicate("Barang " + brg);
                }
            }

            cart["tanggal"] = $("#input_tanggal").val();
            cart["note"] = $("#input_keterangan").val();
            cart["urut"] = $("#inputitem_urut").val();
            cart["kodebrg"] = brg;
            cart["kodegdg"] = $("#input_gudang").val();

            let satuan = $('#inputitem_satuanasal').val();
            if (satuan && satuan.trim() !== "") {
                satuan = satuan.split('||').map((v, i) => i === 2 ? parseFloat(v) : v);
                cart["satuan"] = satuan[1];
                cart["nosat"] = satuan[0];
                cart["isi"] = satuan[2];
            } else {
                cart["satuan"] = "";
                cart["nosat"] = 0;
                cart["isi"] = 0.00;
            }

            let stock = getStockAkhir(cart["nosat"], cart["tanggal"], cart["kodegdg"], cart["kodebrg"]);

            let qtylama = Number($("#inputitem_qtylama").val());
            if (unformatAngka($("#inputitem_qtyasal").val()) <= (Number(stock) + qtylama)) {
                cart["qntdb"] = setEmptyNumberToZero("inputitem_qtyjadi");
                cart["qntcr"] = setEmptyNumberToZero("inputitem_qtyasal");
            } else {
                return "Qty Barang " + cart["kodebrg"] + " melebihi stok yang ada di gudang";
            }

            cart["nourut"] = $("#input_nourut").val();
            cart["biaya"] = setEmptyNumberToZero("inputitem_biaya");
            cart["hpp"] = setEmptyNumberToZero("inputitem_hpp");

            cart["jmlrecord"] = (dataItem.length) ? 1 : 0;

            return cart;
        }

        function submitItem() {
            doSubmitItem("item", "kmbjspadd", "KMBJ");
        }

        // Kirim item ke controller spAdd: I = tambah, U = ubah, D = hapus (sesuai gtipeformitem).
        // Respon controller: "T" = sukses
        let g_submittingItem = false;

        function doSubmitItem(_type, _urlName, _kode, _isRetry = false) {
            if (g_submittingItem) return;

            let choice;
            if (gtipeformitem === g_tipeformitemAdd) choice = "I";
            else if (gtipeformitem === g_tipeformitemEdit) choice = "U";
            else if (gtipeformitem === g_tipeformitemDelete) choice = "D";
            else return alertify.warning("Mode item tidak dikenali, silakan refresh browser");

            if (choice !== "D") {
                if (!cekNotEmpty("input_tanggal")) return alertify.warning(messageRequired("Tanggal"));
                if (!cekNotEmpty("input_gudang")) return alertify.warning(messageRequired("Gudang"));
            }
            if (!cekNotEmpty("input_nobukti")) {
                return alertify.warning("No Bukti belum terbentuk, silakan tutup form lalu klik Tambah lagi");
            }

            let cart = cekValidate(choice);
            if (typeof cart !== "object" || cart === null) {
                return alertify.warning(cart ? String(cart) : "Data tidak valid");
            }

            let _nobukti = cart["nobukti"];
            let _token = $("#_token").val();

            const release = () => {
                g_submittingItem = false;
                $("#buttonSubmitItem").prop("disabled", false);
            };

            g_submittingItem = true;
            $("#buttonSubmitItem").prop("disabled", true);

            $.ajax({
                url: "{!! url('') !!}/" + _urlName,
                type: "post",
                data: {
                    _token,
                    data: cart,
                    urut: cart["urut"]
                },
                success: function(res) {
                    release();

                    if (res === "T") {
                        if (choice === "I") successAdd(_nobukti);
                        else if (choice === "U") successEdit(_nobukti);
                        else successDelete(_nobukti);
                        return;
                    }

                    if (res === "D;;trans" && !_isRetry) {
                        // No Bukti bentrok dengan transaksi lain -> ambil nomor baru, coba sekali lagi
                        let nb = doGenerateNoBukti(_kode);
                        $("#input_nobukti").val(nb.Nobukti);
                        $("#input_nourut").val(nb.Nourut);
                        return doSubmitItem(_type, _urlName, _kode, true);
                    }

                    console.log(res);
                    alertify.warning("Gagal menyimpan item, silakan coba lagi");
                },
                error: function(err) {
                    release();
                    console.log(err);
                    console.log(err.status, err.statusText);
                    alertify.warning("Terjadi kesalahan, silahkan refresh browser");
                }
            });
        }

        function successAdd(_nobukti) {
            reloadData();
            cleanFormItem();
            refreshForm(_nobukti);

            gtipeform = g_tipeformEdit;
        }

        function successEdit(_nobukti) {
            reloadData();
            $('.showhide').hide();
            cleanFormItem();
            refreshForm(_nobukti);
        }

        function successDelete(_nobukti) {
            reloadData();
            $('.showhide').hide();
            refreshForm(_nobukti);
        }

        function doExtractDataFromTable(_cart, _item) {
            let _strValue = "";
            let _strAction = "";

            // field center
            let centerFields = ['_sat1', '_sat2'];

            _cart.forEach((itemcart) => {
                // itemcart: [0] nama kolom, [1] nama header, [2] tipe data, [3] isDesimal, [4] isParameter
                let _data;

                if (itemcart[2] === "date") {
                    _data = format_date(_item[itemcart[0]]);
                } else if (itemcart[2] === "float") {
                    let _value = currencyNormalizer(_item[itemcart[0]]);
                    let _decimal = itemcart[3];
                    _data = format_number(_value, _decimal);
                } else if (itemcart[2] === "int") {
                    _data = currencyNormalizer(_item[itemcart[0]]);
                } else {
                    _data = nullToEmpty(_item[itemcart[0]]);
                }

                // table cell
                if (itemcart[1] !== "") {
                    if (itemcart[1].toLowerCase().includes('sat')) {
                        _strValue += `<td class="text-center align-middle">${_data}</td>`;
                    } else {
                        _strValue += `<td>${_data}</td>`;
                    }
                }

                // action values
                if (itemcart[4] === 1) {
                    if (_strAction !== "") _strAction += ",";

                    if (itemcart[2] === "date") {
                        _strAction += `'${_data}'`;
                    } else if (itemcart[2] === "varchar") {
                        _strAction += `'${stringHtmlNormalizer(_data)}'`;
                    } else {
                        _strAction += _data;
                    }
                }
            });

            return {
                strValue: _strValue,
                strAction: _strAction
            };
        }
    </script>
@endsection
