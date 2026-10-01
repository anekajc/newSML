<div id="page1" class="container-fluid mainpage">

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

        {{-- .tb-report/.content dilepas (pindah ke skema po-*, lihat catatan di kas.blade.php
         @section('css')) — #modalFilter di luar tetap aman karena selalu sudah berada di luar
         .tb-report (lihat catatannya sendiri di bawah). Kartu + toolbar + tabel disalin dari
         accounting/memorialkoreksi.blade.php supaya sama persis. --}}
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

                    {{-- Jumlah baris per halaman - lihat kasIkatPanjangHalaman() di public/js/kas.js. --}}
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

                {{-- #rtBar diisi lewat JS oleh ReportTable.init() - lihat kasInitReportTableSekali(). --}}
                <div id="rtBar"></div>

                <table id="tabel" class="data-table po-aksi-hover">
                    <thead id="tabel_header" class="text-center">
                        <tr>
                            <th style="padding: 4px 12px;" scope="col">Actions</th>
                            <th style="padding: 4px 12px;" scope="col">No. Bukti</th>
                            <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                            <th style="padding: 4px 12px;" scope="col">Trans</th>
                            <th style="padding: 4px 12px;" scope="col">Perk.</th>
                            <th style="padding: 4px 12px;" scope="col">Ket.</th>
                            <th style="padding: 4px 12px;" scope="col">Jumlah Rp</th>
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
                        <div>
                            <label class="rt-field-label" for="modalTipeTrans">Tipe Transaksi</label>
                            <select class="rt-native" id="modalTipeTrans">
                                <option value="">Semua</option>
                                <option value="BKK">BKK</option>
                                <option value="BKM">BKM</option>
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
{{-- /modal filter --}}
