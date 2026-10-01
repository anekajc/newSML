@extends('report.masterreport2')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .tb-report .table-wrap {
        min-height: 13vh;
    }

    .tb-report .kpi-strip {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    @media (max-width: 900px) {
        .tb-report .kpi-strip {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 560px) {
        .tb-report .kpi-strip {
            grid-template-columns: 1fr;
        }
    }

    .tb-report .kpi-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px 28px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .06);
        display: flex;
        align-items: flex-start;
        gap: 18px;
    }

    .tb-report .kpi-ic {
        width: 35px;
        height: 35px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 22px;
    }

    .tb-report .kpi-label {
        font-size: 13px;
        color: #64748b;
        margin-bottom: 4px;
    }

    .tb-report .kpi-val {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
    }

    .tb-report .chart-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    @media (max-width:900px) {
        .tb-report .chart-grid {
            grid-template-columns: 1fr;
        }
    }

    .tb-report .chart-box {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 20px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .06);
    }

    .tb-report .chart-box h3 {
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 12px;
    }

    .tb-report .chart-holder {
        position: relative;
        height: 260px;
    }

    .tb-report .chart-holder canvas {
        max-height: 260px;
    }
</style>

@section('header2')
    <div class="tb-report main">
        <div class="content">
            {{-- <div class="page-title" style="margin-bottom:8px;">Register Pembelian</div> --}}

            <!-- TOOLBAR -->
            <div class="toolbar">

                <!-- Periode (date range) -->
                <div class="filter-wrap">
                    <label>Periode</label>
                    <input type="date" class="filter-inp" id="inputDate1" value="{!! date('Y-m-d') !!}">
                    <span class="filter-sep">s/d</span>
                    <input type="date" class="filter-inp" id="inputDate2" value="{!! date('Y-m-d') !!}">
                </div>

                {{-- Search --}}
                <div>
                    <input class="search-inp" type="text" id="searchBox2" placeholder="Cari data..."
                        oninput="applyFilters()" style="width:180px">
                </div>

                {{-- Order By (No Bukti/Barang/Supplier) jadi "Tampilan" switcher di bar tabel (diisi
               ReportTable.init({ views: ... })) -- dropdown lama di sini (sudah di-comment
               total, dan sebelumnya TIDAK ada penggantinya sama sekali) dihidupkan lagi di situ,
               bukan dihapus: inputOrd genuinely dikonsumsi Sp_reportBeliAccDet & Sp_reportBeliAccRek. --}}

                <!-- Actions: filter modal + customize + tampilkan + export -->
                <div class="action-group">
                    <button class="btn-load" type="button" data-bs-toggle="modal" data-bs-target="#modalFilter"
                        title="Filter Laporan">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <button class="btn-load" onclick="makeTable('REPORT')" title="Tampilkan laporan"><i
                            class="fas fa-check"></i> Tampilkan</button>
                    <div class="export-wrap" id="exportWrap">
                        <button class="export-btn" onclick="toggleExport()"><i class="bi bi-arrow-down"></i> Export <i
                                class="bi bi-caret-down-fill"></i></button>
                        <div class="export-drop" id="exportDrop">
                            <div class="export-opt" onclick="doExport('Excel')"><i class="bi bi-journals text-success"></i>
                                Ekspor ke <span class="ext">XLSX</span></div>
                            <div class="export-opt" onclick="doExport('CSV')"><i class="bi bi-clipboard"></i> Ekspor ke
                                <span class="ext">CSV</span></div>
                            <div class="export-opt" onclick="doExport('Print')"><i
                                    class="bi bi-printer-fill text-warning"></i> Cetak Laporan</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KPI CARDS -->
            <div class="kpi-strip" id="kpiStrip"></div>

            <!-- CHARTS (dibangun sisi-klien dari data yang dimuat) -->
            <div class="chart-grid" id="chartGrid">
                <div class="chart-box">
                    <h3>Pembelian Per Supplier (Top 6)</h3>
                    <div class="chart-holder"><canvas id="topCustomerChart"></canvas></div>
                </div>
                <div class="chart-box">
                    <h3>Pembelian Vs Penjualan</h3>
                    <div class="chart-holder"><canvas id="agingChart"></canvas></div>
                </div>
            </div>

            <!-- Bar kolom tersembunyi + Tampilan (Order By) (diisi oleh report-table.js / ReportTable) -->
            <div id="rtBar"></div>

            <!-- TABLE — header satu tingkat (tanpa band), dibangun oleh ReportTable.headHtml() di
                 renderRows() (drag-reorder + gear aktif seperti biasa). -->
            <div class="table-outer">
                <div class="table-wrap">
                    <table class="tb" id="mainTable">
                        <thead>
                            <tr>
                                <th style="min-width:130px">No. Bukti</th>
                                <th style="min-width:90px">Tanggal</th>
                                <th style="min-width:110px">No PO</th>
                                <th style="min-width:130px">Nama Supplier</th>
                                <th style="min-width:130px">Nama Barang</th>
                                <th style="min-width:80px">Satuan</th>
                                <th class="num" style="min-width:10px">Qty</th>
                                <th class="num" style="min-width:10px">Harga</th>
                                <th class="num" style="min-width:10px">Discount</th>
                                <th class="num" style="min-width:10px">DPP</th>
                                <th class="num" style="min-width:10px">PPN</th>
                                <th class="num" style="min-width:10px">Total</th>
                                <th style="min-width:100px">Otorisasi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr class="empty-row">
                                <td colspan="13">Atur filter lalu klik <b>Tampilkan</b> untuk memuat laporan.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="table-footer">
                    <span id="footerLabel">Belum ada data dimuat</span>
                </div>
            </div>

            <div class="rt-hint">
                <i class="bi bi-info-circle"></i>
                Seret judul kolom untuk mengurutkan. Klik <i class="bi bi-gear"></i> untuk sembunyikan kolom atau atur
                total.
            </div>

        </div><!-- /content -->

        <!-- TOAST -->
        <div class="toast" id="toast"><span id="ti"></span><span id="tm"></span></div>
    </div><!-- /tb-report -->

    {{-- Modal DILETAKKAN DI LUAR .tb-report supaya reset `.tb-report *{margin:0;padding:0}`
       di report-table.css tidak merusak padding/margin modal Bootstrap. --}}

    <!-- modal filter -->
    <div class="modal fade rt-filter" id="modalFilter">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-filter"></i>
                        Filter Laporan
                        <span class="rt-active-badge" id="filterBadge">0 aktif</span>
                    </h5>
                    <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!--<div class="rt-section">
                        {{-- <div class="rt-group-label">Pengaturan Laporan</div> --}}
                        <div class="rt-grid-1">
                            {{-- Pilihan wajib (bukan filter opsional, tanpa nilai netral "Semua") -- TIDAK
                   ikut dihitung ke badge, sama seperti pola "Mode Report" di
                   reportpengadaanpgoutstandingpo.blade.php. Detail & Rekap genuinely memanggil
                   proc BERBEDA (Sp_reportBeliAccDet vs Sp_reportBeliAccRek) -- toggle ini nyata,
                   bukan bug. Catatan: Sp_reportBeliAccRek TIDAK menerima parameter PPN sama
                   sekali, jadi filter PPN di bawah tidak berpengaruh saat mode Rekap aktif --
                   perilaku lama, dipertahankan apa adanya. --}}
                            <div class="mb-3">
                                <label class="rt-field-label">Report</label>
                                <select class="rt-native" id="modalReport">
                                    <option value="0">Detail</option>
                                    <option value="1">Rekap</option>
                                </select>
                            </div>
                        </div>
                    </div> -->

                    <div class="rt-section">
                        <div class="rt-group-label">Filter Data</div>
                        <div class="rt-grid-2">
                            <div class="mb-3">
                                <label class="rt-field-label">PPN</label>
                                <select class="rt-native" id="modalPPN">
                                    <option value="2">Semua</option>
                                    <option value="1">Non PPN</option>
                                    <option value="0">PPN</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="rt-field-label">Jenis</label>
                                <select class="rt-native" id="modalPjasa">
                                    <option value="2">Semua</option>
                                    <option value="1">PBJ</option>
                                    <option value="0">LPB</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="rt-field-label">Tipe Bayar</label>
                                <select class="rt-native" id="modalTipe">
                                    <option value="2">Semua</option>
                                    <option value="1">Kredit</option>
                                    <option value="0">Tunai</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="rt-field-label">Otorisasi</label>
                                <select class="rt-native" id="modalOtorisasi">
                                    <option value="2">Semua</option>
                                    <option value="1">Belum Otorisasi</option>
                                    <option value="0">Sudah Otorisasi</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="rt-reset-link" onclick="resetAllFilters()">Reset semua</button>
                    <div class="rt-footer-buttons">
                        <button type="button" class="rt-btn rt-btn-ghost" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="rt-btn rt-btn-primary"
                            onclick="applyModalFilter()">Terapkan</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- modal filter -->
@endsection


@section('jsreport')
    <script type="text/javascript">
        let DetOrRekap = 0;
        let globalOtorisasi = "2"; // default: Semua
        let globalOrderBy = "N"; // default: Nomor Bukti
        let globalPPN = "2"; // default: Semua
        let globalPjasa = "2"; // default: Semua
        let globalTipebayar = "2"; // default: Semua
        let globalReportMode = "0"; // default: Detail
        let lastRows = []; // hasil fetch terakhir (dipakai renderRows / export / search)
        let currentGroupby = 'NoBukti'; // groupby aktif untuk render ulang saat search

        $(document).ready(function() {
            setOrderBy(globalOrderBy);

            // Header tabel interaktif standar (drag-reorder + gear per kolom + bar "kolom
            // tersembunyi"/"Reset kolom"), plus "Tampilan" switcher untuk Order By.
            ReportTable.init({
                table: '#mainTable',
                bar: '#rtBar',
                onChange: function() {
                    applyFilters();
                },
                views: {
                    label: 'Order By',
                    options: [{
                            value: 'N',
                            label: 'No Bukti',
                            desc: 'Dikelompokkan per No Bukti'
                        },
                        {
                            value: 'B',
                            label: 'Barang',
                            desc: 'Dikelompokkan per Nama Barang'
                        },
                        {
                            value: 'S',
                            label: 'Supplier',
                            desc: 'Dikelompokkan per Nama Supplier'
                        }
                    ],
                    get: function() {
                        return globalOrderBy;
                    },
                    set: function(v) {
                        setOrderBy(String(v));
                        if (lastRows.length) {
                            makeTable('REPORT');
                        }
                    }
                }
            });
        });

        /* -- FILTER MODAL -- */
        $('#modalFilter').on('show.bs.modal', function() {
            $("#modalReport").val(globalReportMode);
            $("#modalPPN").val(globalPPN);
            $("#modalPjasa").val(globalPjasa);
            $("#modalTipe").val(globalTipebayar);
            $("#modalOtorisasi").val(globalOtorisasi);
            updateFilterBadge();
        });

        // Report (Detail/Rekap) TIDAK ikut dihitung: pilihan wajib tanpa nilai netral, bukan filter
        // opsional -- sama seperti komentar di markup modal. PPN/Jasa/Tipe/Otorisasi punya "Semua"
        // (value "2") sebagai nilai netral, jadi ikut dihitung saat diubah dari situ.
        function updateFilterBadge() {
            let count = 0;
            if ($("#modalPPN").val() !== "2") count++;
            if ($("#modalPjasa").val() !== "2") count++;
            if ($("#modalTipe").val() !== "2") count++;
            if ($("#modalOtorisasi").val() !== "2") count++;
            $('#filterBadge').text(count + ' aktif');
        }

        function resetAllFilters() {
            $("#modalPPN").val("2");
            $("#modalPjasa").val("2");
            $("#modalTipe").val("2");
            $("#modalOtorisasi").val("2");
            updateFilterBadge();
        }

        function applyModalFilter() {
            globalReportMode = $("#modalReport").val();
            DetOrRekap = Number(globalReportMode);
            globalPPN = $("#modalPPN").val();
            globalPjasa = $("#modalPjasa").val();
            globalTipebayar = $("#modalTipe").val();
            globalOtorisasi = $("#modalOtorisasi").val();

            $('#modalFilter').modal('hide');
        }

        /* -- EXPORT -- */
        function toggleExport() {
            document.getElementById('exportDrop').classList.toggle('open');
        }
        document.addEventListener('click', function(e) {
            const wrap = document.getElementById('exportWrap');
            if (wrap && !wrap.contains(e.target)) {
                document.getElementById('exportDrop').classList.remove('open');
            }
        });

        // order by (nilai sebenarnya -- UI-nya "Tampilan" switcher di #rtBar, lihat ReportTable.init() di atas)
        function setOrderBy(val) {
            globalOrderBy = val;
        }

        var modereport_detailnobukti = 0,
            modereport_detailbarang = 1,
            modereport_detailcustomer = 2;
        var modereport_rekapnobukti = 3,
            modereport_rekapbarang = 4,
            modereport_rekapcustomer = 5;
        g_modeReport = modereport_detailnobukti;

        function setDefaultHeader() {
            if (g_modeReport == modereport_detailnobukti) {
                gcart_header = [
                    ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
                    ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
                    ['NoPO', 'No PO', 1, 'varchar', 0, 0],
                    ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
                    ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
                    ['satbeli', 'Satuan', 1, 'varchar', 0, 0],
                    ['qntbeli', 'Qty', 1, 'float', 0, 2],
                    ['Harga', 'HARGA', 1, 'float', 0, 2],
                    ['DiscP', 'Discount', 1, 'float', 0, 2],
                    ['KODEVLS', 'VLS', 1, 'varchar', 0, 0],
                    ['NDPPRp', 'DPP', 1, 'float', 1, 2],
                    ['NPPNRp', 'PPN', 1, 'float', 1, 2],
                    ['NNETRp', 'Total', 1, 'float', 1, 2],
                    ['NeedOtorisasi', 'Otorisasi', 1, 'varchar', 0, 0]
                ];
                gsum_issubtotal = 1;
                gsum_isgrandtotal = 1;

            } else if (g_modeReport == modereport_detailbarang) {
                gcart_header = [
                    ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
                    ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
                    ['NoPO', 'No PO', 1, 'varchar', 0, 0],
                    ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
                    ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
                    ['satbeli', 'Satuan', 1, 'varchar', 0, 0],
                    ['qntbeli', 'Qty', 1, 'float', 0, 2],
                    ['Harga', 'HARGA', 1, 'float', 0, 2],
                    ['DiscP', 'Discount', 1, 'float', 0, 2],
                    ['KODEVLS', 'VLS', 1, 'varchar', 0, 0],
                    ['NDPPRp', 'DPP', 1, 'float', 1, 2],
                    ['NPPNRp', 'PPN', 1, 'float', 1, 2],
                    ['NNETRp', 'Total', 1, 'float', 1, 2],
                    ['NeedOtorisasi', 'Otorisasi', 1, 'varchar', 0, 0]
                ];
                gsum_issubtotal = 1;
                gsum_isgrandtotal = 1;

            } else if (g_modeReport == modereport_detailcustomer) {
                gcart_header = [
                    ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
                    ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
                    ['NoPO', 'No PO', 1, 'varchar', 0, 0],
                    ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
                    ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
                    ['satbeli', 'Satuan', 1, 'varchar', 0, 0],
                    ['qntbeli', 'Qty', 1, 'float', 0, 2],
                    ['Harga', 'HARGA', 1, 'float', 0, 2],
                    ['DiscP', 'Discount', 1, 'float', 0, 2],
                    ['KODEVLS', 'VLS', 1, 'varchar', 0, 0],
                    ['NDPPRp', 'DPP', 1, 'float', 1, 2],
                    ['NPPNRp', 'PPN', 1, 'float', 1, 2],
                    ['NNETRp', 'Total', 1, 'float', 1, 2],
                    ['NeedOtorisasi', 'Otorisasi', 1, 'varchar', 0, 0]
                ];
                gsum_issubtotal = 1;
                gsum_isgrandtotal = 1;

            } else if (g_modeReport == modereport_rekapnobukti) {
                gcart_header = [
                    ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
                    ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
                    ['NoPO', 'No PO', 1, 'varchar', 0, 0],
                    ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
                    ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
                    ['satbeli', 'Satuan', 1, 'varchar', 0, 0],
                    ['qntbeli', 'Qty', 1, 'float', 0, 2],
                    ['Harga', 'HARGA', 1, 'float', 0, 2],
                    ['DiscP', 'Discount', 1, 'float', 0, 2],
                    ['KODEVLS', 'VLS', 1, 'varchar', 0, 0],
                    ['NDPPRp', 'DPP', 1, 'float', 1, 2],
                    ['NPPNRp', 'PPN', 1, 'float', 1, 2],
                    ['NNETRp', 'Total', 1, 'float', 1, 2],
                    ['NeedOtorisasi', 'Otorisasi', 1, 'varchar', 0, 0]
                ];
                gsum_issubtotal = 1;
                gsum_isgrandtotal = 1;

            } else if (g_modeReport == modereport_rekapbarang) {
                gcart_header = [
                    ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
                    ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
                    ['NoPO', 'No PO', 1, 'varchar', 0, 0],
                    ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
                    ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
                    ['satbeli', 'Satuan', 1, 'varchar', 0, 0],
                    ['qntbeli', 'Qty', 1, 'float', 0, 2],
                    ['Harga', 'HARGA', 1, 'float', 0, 2],
                    ['DiscP', 'Discount', 1, 'float', 0, 2],
                    ['KODEVLS', 'VLS', 1, 'varchar', 0, 0],
                    ['NDPPRp', 'DPP', 1, 'float', 1, 2],
                    ['NPPNRp', 'PPN', 1, 'float', 1, 2],
                    ['NNETRp', 'Total', 1, 'float', 1, 2],
                    ['NeedOtorisasi', 'Otorisasi', 1, 'varchar', 0, 0]
                ];
                gsum_issubtotal = 1;
                gsum_isgrandtotal = 1;

            } else {
                gcart_header = [
                    ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
                    ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
                    ['NoPO', 'No PO', 1, 'varchar', 0, 0],
                    ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
                    ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
                    ['satbeli', 'Satuan', 1, 'varchar', 0, 0],
                    ['qntbeli', 'Qty', 1, 'float', 0, 2],
                    ['Harga', 'HARGA', 1, 'float', 0, 2],
                    ['DiscP', 'Discount', 1, 'float', 0, 2],
                    ['KODEVLS', 'VLS', 1, 'varchar', 0, 0],
                    ['NDPPRp', 'DPP', 1, 'float', 1, 2],
                    ['NPPNRp', 'PPN', 1, 'float', 1, 2],
                    ['NNETRp', 'Total', 1, 'float', 1, 2],
                    ['NeedOtorisasi', 'Otorisasi', 1, 'varchar', 0, 0]
                ];
                gsum_issubtotal = 1;
                gsum_isgrandtotal = 1;
            }
        }

        const reportUrl = "{{ url('laporanregisterpembelian_doReport') }}"
        const grafikUrl = "{{ url('laporanregisterpembelian_doGrafik') }}";

        function makeTable(_mode) {
            let groupby = '';
            let _date1 = $("#inputDate1").val();
            let _date2 = $("#inputDate2").val();

            let input_order = globalOrderBy;

            // mode report -- groupby dikoreksi supaya cocok dengan field yang genuinely ada di
            // gcart_header masing2 mode (sebelumnya beberapa cabang salah kelompok, lihat catatan di
            // bawah tiap satu).
            if (input_order == "N") {
                if (DetOrRekap === 0) {
                    g_modeReport = modereport_detailnobukti;
                    groupby = 'NoBukti';
                } else {
                    g_modeReport = modereport_rekapnobukti;
                    // Sebelumnya groupby = 'NAMABRG', padahal kolom itu TIDAK ADA di header mode ini
                    // (rekapnobukti tidak punya NamaBrg sama sekali) -- deteksi pergantian grup jadi
                    // selalu undefined===undefined dan subtotal per baris tidak pernah pecah. Dikoreksi
                    // ke NoBukti, field pertama & sesuai nama modenya.
                    groupby = 'NoBukti';
                }
            } else if (input_order == "B") {
                if (DetOrRekap === 0) {
                    g_modeReport = modereport_detailbarang;
                    groupby = 'NamaBrg';
                } else {
                    g_modeReport = modereport_rekapbarang;
                    groupby = 'NAMABRG';
                }
            } else {
                if (DetOrRekap === 0) {
                    g_modeReport = modereport_detailcustomer;
                    // Sebelumnya groupby = 'NamaBrg' walau order-nya "Supplier" -- salin-tempel dari
                    // cabang Barang. Dikoreksi ke NAMACUSTSUPP supaya subtotal pecah per Supplier, sesuai
                    // urutan yang dipilih (dan sama seperti cabang Rekap+Supplier di bawah).
                    groupby = 'NAMACUSTSUPP';
                } else {
                    g_modeReport = modereport_rekapcustomer;
                    groupby = 'NAMACUSTSUPP';
                }
            }

            setDefaultHeader();
            if (typeof doSetHeader === 'function') {
                doSetHeader(g_modeReport);
            }

            document.getElementById('footerLabel').innerHTML = loadingHtml('Memuat data...');

            let data = {
                date1: _date1,
                date2: _date2,
                inputOto: globalOtorisasi,
                inputOrd: input_order,
                inputPPN: globalPPN,
                inputPjasa: globalPjasa,
                inputTipebayar: globalTipebayar,
                inputDetOrRekap: DetOrRekap,
            };

            // Ambil data SEKALI, lalu render langsung ke tabel styled baru (#tableBody).
            $.ajax({
                url: reportUrl,
                type: 'get',
                data: data,
                success: function(res) {
                    lastRows = res || [];
                    currentGroupby = groupby; // simpan utk render ulang saat search
                    $('#searchBox2').val(''); // reset kotak cari tiap muat data baru
                    renderRows(lastRows, groupby); // <-- render ke .tb-report #tableBody
                    renderKpi(lastRows);
                    buildCharts(lastRows);
                    loadLineChart(_date1);
                },
                error: function() {
                    lastRows = [];
                    currentGroupby = groupby;
                    renderRows([], groupby);
                }
            });
        }

        function renderKpi(rows) {
            let totalDPP = 0;
            let totalPPN = 0;

            const supplierSet = new Set();
            const poSet = new Set();

            (rows || []).forEach(r => {
                totalDPP += currencyNormalizer(pickCI(r, 'NDPPRp'));
                totalPPN += currencyNormalizer(pickCI(r, 'NPPNRp'));

                const supplier = (pickCI(r, 'NAMACUSTSUPP') || '').trim();
                if (supplier) supplierSet.add(supplier);

                const po = (pickCI(r, 'NoPO') || '').trim();
                if (po) poSet.add(po);
            });

            const cards = [
                ['Total DPP', totalDPP, '#4f46e5', '#ede9fe', 'bi bi-receipt'],
                ['Total PPN', totalPPN, '#16a34a', '#dcfce7', 'bi bi-percent'],
                ['Jumlah Supplier', supplierSet.size, '#ca8a04', '#fef9c3', 'bi bi-people-fill'],
                ['Jumlah PO', poSet.size, '#dc2626', '#fee2e2', 'bi bi-file-earmark-text']
            ];

            document.getElementById('kpiStrip').innerHTML =
                cards.map(c => `
          <div class="kpi-card">
              <div class="kpi-ic" style="background:${c[3]};color:${c[2]}">
                  <i class="${c[4]}"></i>
              </div>
              <div>
                  <div class="kpi-label">${c[0]}</div>
                  <div class="kpi-val">
                      ${
                        c[0].startsWith('Total')
                            ? 'Rp ' + format_number(c[1], 0)
                            : format_number(c[1], 0)
                      }
                  </div>
              </div>
          </div>
      `).join('');
        }

        function pickCI(r, key) {
            if (r[key] !== undefined) return r[key];

            const lk = key.toLowerCase();

            for (const k in r) {
                if (k.toLowerCase() === lk)
                    return r[k];
            }
            return undefined;
        }

        /* -- CHARTS (Chart.js v4). -- */
        const CHART_PALETTE = ['#4F46E5', '#7C3AED', '#DB2777', '#2563eb', '#16a34a', '#ca8a04', '#ea580c', '#0891b2',
            '#e11d48', '#65a30d'
        ];
        let _charts = {};

        function fmtShort(v) {
            v = Math.round(num(v));
            const a = Math.abs(v);
            if (a >= 1e9) return (v / 1e9).toFixed(1).replace(/\.0$/, '') + ' M';
            if (a >= 1e6) return (v / 1e6).toFixed(1).replace(/\.0$/, '') + ' jt';
            if (a >= 1e3) return (v / 1e3).toFixed(0) + ' rb';
            return String(v);
        }

        function _destroyChart(id) {
            if (_charts[id]) {
                _charts[id].destroy();
                delete _charts[id];
            }
        }

        // Grafik tren tahunan Beli vs Jual, sekarang benar-benar ada backend-nya
        // (LaporanRegisterPembelianController::doGrafik()) -- sebelumnya endpoint ini tidak pernah
        // dibuat sama sekali (404 tiap load), jadi grafik ini tidak pernah tampil. Independen dari
        // filter tabel/KPI di atas (lihat komentar di controller).
        function loadLineChart(date1) {
            $.ajax({
                url: grafikUrl,
                type: "GET",
                data: {
                    date1: date1
                },
                success: function(res) {
                    const bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov',
                        'Des'
                    ];
                    const jual = new Array(12).fill(0);
                    const beli = new Array(12).fill(0);
                    (res || []).forEach(r => {
                        const idx = parseInt(r.Bulan) - 1;
                        if (r.Tipe === 'JUAL')
                            jual[idx] = currencyNormalizer(r.Total);

                        if (r.Tipe === 'BELI')
                            beli[idx] = currencyNormalizer(r.Total);
                    });

                    _destroyChart('aging');
                    _charts.aging = new Chart(document.getElementById('agingChart'), {
                        type: 'line',
                        data: {
                            labels: bulan,
                            datasets: [{
                                    label: 'Penjualan',
                                    data: jual,
                                    borderColor: '#4F46E5',
                                    backgroundColor: '#4F46E5',
                                    tension: .4,
                                    fill: false
                                },
                                {
                                    label: 'Pembelian',
                                    data: beli,
                                    borderColor: '#DB2777',
                                    backgroundColor: '#DB2777',
                                    tension: .4,
                                    fill: false
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            },
                            scales: {
                                y: {
                                    ticks: {
                                        callback: v => fmtShort(v)
                                    }
                                }
                            }
                        }
                    });
                }
            });
        }

        function buildCharts(rows) {
            if (typeof Chart === 'undefined') return;

            Chart.defaults.font.family = "'Segoe UI',system-ui,sans-serif";
            Chart.defaults.font.size = 12;
            Chart.defaults.color = "#64748b";

            //-----------------------------------------
            // TOP SUPPLIER
            //-----------------------------------------

            const supplierMap = {};

            (rows || []).forEach(r => {
                const supplier = pickCI(r, 'NAMACUSTSUPP') || 'Tidak diketahui';
                if (!supplierMap[supplier]) {
                    supplierMap[supplier] = 0;
                }
                supplierMap[supplier] += currencyNormalizer(
                    pickCI(r, 'NNETRp')
                );
            });

            const topSupplier =
                Object.entries(supplierMap)
                .sort((a, b) => b[1] - a[1])
                .slice(0, 6);
            _destroyChart('topCustomer');

            _charts.topCustomer =
                new Chart(
                    document.getElementById('topCustomerChart'), {
                        type: 'bar',
                        data: {
                            labels: topSupplier.map(x => x[0]),
                            datasets: [{
                                label: 'Grand Total',

                                data: topSupplier.map(x => x[1]),

                                backgroundColor: topSupplier.map(
                                    (x, i) =>
                                    CHART_PALETTE[
                                        i % CHART_PALETTE.length
                                    ]
                                ),
                                borderRadius: 6
                            }]
                        },

                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            indexAxis: 'y',
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: (ctx) =>
                                            ' Rp ' + format_number(ctx.parsed.x, 0)
                                    }
                                }
                            },

                            scales: {
                                x: {
                                    ticks: {
                                        callback: (v) =>
                                            fmtShort(v)
                                    }
                                }
                            }
                        }
                    });
        }

        // === RENDER KE TABEL STYLED (.tb-report #mainTable) ===
        // Kolom dibangun DINAMIS dari gcart_header (hanya kolom yang terlihat /
        // item[2]===1, sesuai urutan simpanan). Jadi hasil "Customize Table"
        // (show/hide + urutan kolom) langsung tampil. <thead> ditulis ulang tiap
        // render lewat ReportTable.headHtml() (drag-reorder + gear per kolom).
        // Subtotal/Grand Total = jumlah kolom DPP/PPN/Total, dikelompokkan per
        // `groupby`. (Data sudah terurut dari proc sesuai inputOrd, jadi cukup
        // deteksi pergantian nilai grup.)
        function renderRows(rows, groupby) {
            const cols = gcart_header.filter(c => c[2] === 1); // kolom terlihat, terurut
            const thead = document.querySelector('#mainTable thead');
            const tbody = document.getElementById('tableBody');
            const qntVisible = cols.some(c => c[0] === 'NDPPRp');
            // Baris Subtotal & Grand Total mengikuti toggle di modal Customize Table
            // (#buttonSubtotal -> gsum_issubtotal, #buttonGrandtotal -> gsum_isgrandtotal).
            // gsum_* dimuat oleh doSetHeader() saat klik Tampilkan, jadi pilihan user
            // (sudah tersimpan) langsung berlaku. Total hanya tampil bila kolom DPP ada.
            const showSub = qntVisible && (gsum_issubtotal === 1);
            const showGrand = qntVisible && (gsum_isgrandtotal === 1);

            thead.innerHTML = ReportTable.headHtml(cols);

            if (!rows || !rows.length) {
                tbody.innerHTML = '<tr class="empty-row"><td colspan="' + cols.length +
                    '">Tidak ada data ditemukan.</td></tr>';
                document.getElementById('footerLabel').textContent = 'Tidak ada data';
                return;
            }

            let html = '',
                prev = null,
                sub = {
                    NDPPRp: 0,
                    NPPNRp: 0,
                    NNETRp: 0
                },
                grand = {
                    NDPPRp: 0,
                    NPPNRp: 0,
                    NNETRp: 0
                };

            rows.forEach(function(r, i) {
                const now = r[groupby];

                // subtotal saat nilai grup berganti (kalau toggle Subtotal aktif)
                if (showSub && i !== 0 && prev !== now) {
                    html += totalRowTotal('Subtotal', sub, cols, 'subtotal-row');
                    sub = {
                        NDPPRp: 0,
                        NPPNRp: 0,
                        NNETRp: 0
                    };
                }

                sub.NDPPRp += currencyNormalizer(r.NDPPRp);
                sub.NPPNRp += currencyNormalizer(r.NPPNRp);
                sub.NNETRp += currencyNormalizer(r.NNETRp);

                grand.NDPPRp += currencyNormalizer(r.NDPPRp);
                grand.NPPNRp += currencyNormalizer(r.NPPNRp);
                grand.NNETRp += currencyNormalizer(r.NNETRp);

                // satu sel per kolom terlihat, format menurut tipe (item[3]) & desimal (item[5])
                html += '<tr class="data-row">' + cols.map(function(c) {
                    const key = c[0],
                        type = c[3];
                    // Status Otorisasi
                    if (key === 'NeedOtorisasi') {
                        return `<td> ${r.NeedOtorisasi == 1 ? '<span class="sp-badge is-inactive">Belum</span>' : '<span class="sp-badge is-active">Sudah</span>'} </td>`;
                    }

                    if (type === 'date') return '<td>' + format_date(r[key]) + '</td>';
                    if (type === 'float' || type === 'int') return '<td class="num">' + format_number(
                        currencyNormalizer(r[key]), c[5]) + '</td>';
                    return '<td>' + nullToEmpty(r[key]) + '</td>';
                }).join('') + '</tr>';

                prev = now;
            });

            // subtotal grup terakhir + grand total   mengikuti toggle di modal
            if (showSub) html += totalRowTotal('Subtotal', sub, cols, 'subtotal-row');
            if (showGrand) html += totalRowTotal('GRAND TOTAL', grand, cols, 'grand-total');

            tbody.innerHTML = html;
            document.getElementById('footerLabel').textContent = 'Menampilkan ' + rows.length + ' baris';
        }

        // Baris total (DPP/PPN/Total saja): nilai di kolomnya masing-masing, label di kolom pertama
        // non-total, sel lain dikosongkan   mengikuti urutan kolom terlihat saat ini.
        function totalRowTotal(label, total, cols, cls) {
            const totalKeys = ['NDPPRp', 'NPPNRp', 'NNETRp'];
            const labelIdx = cols.findIndex(c => !totalKeys.includes(c[0]));

            const tds = cols.map(function(c, idx) {
                if (totalKeys.includes(c[0]))
                    return '<td class="num">' + format_number(total[c[0]], 2) + '</td>';
                if (idx === labelIdx)
                    return '<td>' + label + '</td>';
                return '<td></td>';
            });

            return '<tr class="' + cls + '">' + tds.join('') + '</tr>';
        }

        // === PENCARIAN SISI-KLIEN ===
        // Menyaring data yang SUDAH dimuat (lastRows) berdasarkan teks pencarian,
        // dicocokkan ke semua kolom yang sedang terlihat, lalu render ulang tabel
        // styled (renderRows menghitung ulang subtotal/grand total untuk hasil saring).
        function applyFilters() {
            if (!lastRows.length) return; // belum ada data dimuat

            const term = ($('#searchBox2').val() || '').trim().toLowerCase();
            if (!term) {
                renderRows(lastRows, currentGroupby);
                return;
            } // kosong -> tampilkan semua

            const cols = gcart_header.filter(c => c[2] === 1); // kolom yang terlihat
            const filtered = lastRows.filter(function(r) {
                return rowSearchText(r, cols).indexOf(term) !== -1;
            });

            renderRows(filtered, currentGroupby);
        }

        // Gabungan teks satu baris dari kolom terlihat (tanggal pakai format tampil
        // dd/mm/yyyy) supaya pencarian cocok dengan apa yang user lihat di tabel.
        function rowSearchText(r, cols) {
            return cols.map(function(c) {
                const v = r[c[0]];
                if (c[3] === 'date') return format_date(v);
                return (v == null ? '' : String(v));
            }).join(' ').toLowerCase();
        }

        // getKolomFilter() milik ENGINE LAMA (modal "Filter Data" / doShowFormFilterData()), yang
        // TIDAK dipakai lagi di halaman ini (tombolnya sudah dihapus dari toolbar). Stub ini cuma
        // jaga-jaga supaya base script masterreport2 tidak error kalau memanggilnya.
        function getKolomFilter() {
            return [];
        }
    </script>
@endsection
