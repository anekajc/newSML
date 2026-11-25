@extends('report.masterreport2')

{{-- Laporan Permintaan Sample (2026-11-25): tampilan disamakan dengan Laporan Histori Penyerahan
     Sample (reporthistoripenyerahansample.blade.php). Data dari VWREPORTSERAHSAMPLEPR lewat
     laporanpermintaansample_doReport. Filter: Sales (browse dbKaryawan IsSales=1 Aktif=1, gaya
     picker sama dengan History SO). Order By No Bukti / Barang di bar atas tabel. --}}

<style>
    .tb-report .table-wrap {
        min-height: 10vh;
    }
</style>

@section('header2')
    <div class="tb-report main">
        <div class="content">

            <!-- TOOLBAR -->
            <div class="toolbar">
                <!-- Periode (date range) -->
                <div class="filter-wrap">
                    <label>Periode</label>
                    <input type="date" class="filter-inp" id="inputDate1" value="{!! date('Y-m-01') !!}">
                    <span class="filter-sep">s/d</span>
                    <input type="date" class="filter-inp" id="inputDate2" value="{!! date('Y-m-d') !!}">
                </div>

                {{-- Search --}}
                <div>
                    <input class="search-inp" type="text" id="searchBox2" placeholder="Cari data..."
                        oninput="applyFilters()" style="width:180px">
                </div>

                <!-- Actions: filter + tampilkan + export -->
                <div class="action-group">
                    {{-- Dibuka lewat plugin jQuery (Bootstrap 4), sama seperti report lain. --}}
                    <button class="btn-load" type="button" onclick="$('#modalFilter').modal('show')">
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
                                <span class="ext">CSV</span>
                            </div>
                            <div class="export-opt" onclick="doExport('Print')"><i
                                    class="bi bi-printer-fill text-warning"></i> Cetak Laporan</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bar kolom tersembunyi + Order By (diisi oleh report-table.js / ReportTable) -->
            <div id="rtBar"></div>

            <!-- TABLE -->
            <div class="table-outer">
                <div class="table-wrap">
                    <table class="tb" id="mainTable">
                        <thead>
                            <tr>
                                <th>No. Bukti</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr class="empty-row">
                                <td>Atur filter lalu klik <b>Tampilkan</b> untuk memuat laporan.</td>
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
                Seret judul kolom untuk mengurutkan. Klik <i class="bi bi-gear"></i> pada judul kolom untuk sembunyikan
                kolom atau atur desimal &amp; total.
            </div>

        </div><!-- /content -->

        <!-- TOAST -->
        <div class="toast" id="toast"><span id="ti"></span><span id="tm"></span></div>
    </div><!-- /tb-report -->

    {{-- Modal di luar .tb-report supaya reset CSS report-table.css tidak merusak modal Bootstrap. --}}

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
                    <button type="button" class="btn-close" aria-label="Close" data-dismiss="modal" data-bs-dismiss="modal"
                        onclick="$('#modalFilter').modal('hide')"></button>
                </div>

                <div class="modal-body">
                    <div class="rt-section">
                        <div class="rt-group-label">Filter Data
                            <span class="rt-group-hint">&mdash; klik untuk memilih</span>
                        </div>
                        <div class="rt-grid-2" id="pickFields"></div>

                        {{-- Nilai sebenarnya: KeyNIK sales (dikirim ke server) + namanya (untuk tampilan) --}}
                        <input type="hidden" id="inputSales" value="-">
                        <input type="hidden" id="inputSalesNama" value="">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="rt-reset-link" onclick="resetAllFilters()">Reset semua</button>
                    <div class="rt-footer-buttons">
                        <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal" data-bs-dismiss="modal"
                            onclick="$('#modalFilter').modal('hide')">Batal</button>
                        <button type="button" class="rt-btn rt-btn-primary"
                            onclick="applyModalFilter()">Terapkan</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- modal filter -->

    <!-- modal pilih sales (gaya picker sama dengan modalMarketingHistorySO: baris diklik untuk memilih) -->
    <div class="modal fade rt-picker-v2" id="formSelect" tabindex="-1" role="dialog" aria-labelledby="formSelectLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formSelectLabel">Pilih Sales</h5>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"
                        onclick="$('#formSelect').modal('hide')">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="tabelSelect" class="table table-bordered table-striped">
                        <thead class="text-center">
                            <tr><th>Kode</th><th>Nama</th></tr>
                        </thead>
                        <tbody id="tabel_dataSelect" class="text-left"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal"
                        onclick="$('#formSelect').modal('hide')">Batal</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('jsreport')
    <script type="text/javascript">
        window.g_pickerV2 = true;
        let globalDate1 = "{!! date('Y-m-d') !!}";
        let globalDate2 = "{!! date('Y-m-d') !!}";
        let globalOrderBy = "N"; // N = No Bukti, B = Barang

        let lastRows = [];
        let currentGroupby = 'NOBUKTI';

        const reportUrl = "{{ url('laporanpermintaansample_doReport') }}";
        const salesUrl = "{{ url('laporanpermintaansample_loadsales') }}";

        // Slot kolom tersimpan (DBSIMPANHEADER.reportmode). 11/12 dipakai supaya layout
        // tersimpan dari halaman lama (kolomnya lain) tidak ikut terbaca.
        var modereport_nobukti = 11,
            modereport_barang = 12;
        g_modeReport = modereport_nobukti;

        const ORDER_OPTIONS = [
            { value: 'N', label: 'No Bukti' },
            { value: 'B', label: 'Barang' },
        ];
        let viewsCfg = {
            label: 'Order By',
            options: ORDER_OPTIONS,
            get: function() { return globalOrderBy; },
            set: function(v) {
                setOrderBy(String(v));
                if (lastRows.length) { makeTable('REPORT'); } // re-fetch: urutan dari server
            }
        };

        $(document).ready(function() {
            showPeriode();
            setDefaultHeader();
            setOrderBy(globalOrderBy);

            // Sales terakhir yang di-Terapkan user ini dipulihkan (DBUSERFILTER, kunci 'modalPick').
            UserFilter.track('modalPick', {
                inputSales: UserFilter.input('#inputSales'),
                inputSalesNama: UserFilter.input('#inputSalesNama')
            });
            renderPickFields();
            updateFilterBadge();

            ReportTable.init({
                table: '#mainTable',
                bar: '#rtBar',
                onChange: render,
                views: viewsCfg
            });
        });

        function showPeriode() {
            globalDate1 = $('#inputDate1').val();
            globalDate2 = $('#inputDate2').val();
        }

        function setOrderBy(val) {
            globalOrderBy = val;
            g_modeReport = (val === 'B') ? modereport_barang : modereport_nobukti;
            setDefaultHeader();
            doSetHeader(g_modeReport);
            doShowCustomize();
        }

        /* -- FILTER MODAL -- */
        const PICK_FIELDS = [{ id: 'inputSales', label: 'Sales', modal: 'selectSales' }];

        function renderPickFields() {
            let html = '';
            PICK_FIELDS.forEach(function(f) {
                const val = $('#' + f.id).val() || '-';
                const isSet = (val !== '-' && val !== '');
                const nama = $('#' + f.id + 'Nama').val();
                const tampil = isSet ? (nama ? nama + ' (' + val + ')' : val) : '';
                html += '<div>';
                html += '<label class="rt-field-label">' + f.label + '</label>';
                html += '<div class="rt-combo">';
                html += '<div class="rt-combo-input" onclick="pickFromModal(\'' + f.modal + '\')">';
                if (isSet) {
                    html += '<span class="rt-combo-tag">' + $('<div>').text(tampil).html() +
                        '<button type="button" onclick="event.stopPropagation(); clearPickField(\'' + f.id +
                        '\')">&times;</button></span>';
                } else {
                    html += '<span class="rt-combo-placeholder">Pilih ' + f.label.toLowerCase() + '...</span>';
                }
                html += '<span class="rt-combo-chevron">' +
                    '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>' +
                    '</span>';
                html += '</div></div></div>';
            });
            $('#pickFields').html(html);
        }

        function clearPickField(id) {
            $('#' + id).val('-');
            $('#' + id + 'Nama').val('');
            renderPickFields();
            updateFilterBadge();
        }

        function updateFilterBadge() {
            let count = 0;
            PICK_FIELDS.forEach(function(f) {
                const val = $('#' + f.id).val();
                if (val && val !== '-') { count++; }
            });
            $('#filterBadge').text(count + ' aktif');
        }

        function resetAllFilters() {
            PICK_FIELDS.forEach(function(f) {
                $('#' + f.id).val('-');
                $('#' + f.id + 'Nama').val('');
            });
            renderPickFields();
            updateFilterBadge();
        }

        $('#modalFilter').on('show.bs.modal', function() {
            renderPickFields();
            updateFilterBadge();
        });

        function applyModalFilter() {
            UserFilter.commit('modalPick');   // ingat sales ini untuk user ini (semua device)
            $('#modalFilter').modal('hide');
        }

        // Modal Filter disembunyikan dulu (hindari stacked-modal), lalu dibuka lagi setelah
        // modal pilih sales ditutup.
        let g_reopenFilter = false;

        function pickFromModal(idModal) {
            g_reopenFilter = true;
            $('#modalFilter').modal('hide');
            if (idModal === 'selectSales') { loadSelectSales(); }
            $('#formSelect').modal('show');
        }
        $(document).on('hidden.bs.modal', '#formSelect', function() {
            if (g_reopenFilter) {
                g_reopenFilter = false;
                $('#modalFilter').modal('show');
                renderPickFields();
                updateFilterBadge();
            }
        });

        let salesCache = null;

        function loadSelectSales() {
            if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tabelSelect')) {
                $('#tabelSelect').DataTable().destroy();
            }
            if (!salesCache) {
                $.ajax({
                    url: salesUrl,
                    type: 'get',
                    async: false,
                    success: function(res) { salesCache = res || []; },
                    error: function() { salesCache = null; }
                });
            }
            let rowTable = '';
            (salesCache || []).forEach(function(item) {
                const kode = String(item.KeyNIK);
                const nama = item.Nama || '';
                rowTable += '<tr class="pick-row" data-kode="' + $('<div>').text(kode).html() +
                    '" data-nama="' + $('<div>').text(nama).html() + '">' +
                    '<td>' + $('<div>').text(kode).html() + '</td><td>' + $('<div>').text(nama).html() + '</td></tr>';
            });
            $('#tabel_dataSelect').html(rowTable);
            if ($.fn.DataTable) {
                rtPickerInit('tabelSelect');
            }
        }

        $(document).on('click', '#tabel_dataSelect tr.pick-row', function() {
            $('#inputSales').val($(this).attr('data-kode'));
            $('#inputSalesNama').val($(this).attr('data-nama'));
            $('#formSelect').modal('hide');
        });

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

        function doExport(fmt) {
            document.getElementById('exportDrop').classList.remove('open');
            if (fmt === 'Print') {
                window.print();
                return;
            }
            exportDelimited(fmt);
        }

        function exportDelimited(fmt) {
            const cols = gcart_header.filter(c => c[2] === 1);
            const header = cols.map(c => c[1]);
            const body = (lastRows || []).map(r => cols.map(function(c) {
                const v = pickCI(r, c[0]);
                if (c[3] === 'date') return format_date(v);
                if (c[3] === 'float' || c[3] === 'int') return currencyNormalizer(v);
                return (v == null ? '' : v);
            }));
            const rows = [header].concat(body);
            ReportExport.fromRows(rows, fmt, { customName: 'PermintaanSample_' + (globalDate1 || '') + '_' + (globalDate2 || '') });
            showToast('📄', 'Data diekspor sebagai ' + fmt);
        }

        /* -- TOAST -- */
        function showToast(icon, msg) {
            const t = document.getElementById('toast');
            document.getElementById('ti').textContent = icon;
            document.getElementById('tm').textContent = msg;
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 3000);
        }

        function pickCI(r, key) {
            if (r[key] !== undefined) return r[key];
            const lk = String(key).toLowerCase();
            for (const k in r) {
                if (k.toLowerCase() === lk) return r[k];
            }
            return undefined;
        }

        function setDefaultHeader() {
            gcart_header = [
                ['NOBUKTI', 'No. Bukti', 1, 'varchar', 0, 0],
                ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
                ['KODEBRG', 'Kode Barang', 1, 'varchar', 0, 0],
                ['NAMABRG', 'Nama Barang', 1, 'varchar', 0, 0],
                ['GudangAsal', 'Gudang Asal', 1, 'varchar', 0, 0],
                ['GudangTujuan', 'Gudang Tujuan', 1, 'varchar', 0, 0],
                ['SAT_1', 'Satuan', 1, 'varchar', 0, 0],
                ['QNT', 'Qty', 1, 'float', 1, 2],
                ['NOTE', 'Note', 1, 'varchar', 0, 0]
            ];
            gsum_issubtotal = 1;
            gsum_isgrandtotal = 1;
        }

        function makeTable(_mode) {
            let groupby = (globalOrderBy === 'B') ? 'KODEBRG' : 'NOBUKTI';
            showPeriode();

            setDefaultHeader();
            if (typeof doSetHeader === 'function') {
                doSetHeader(g_modeReport);
            }

            let data = {
                date1: globalDate1,
                date2: globalDate2,
                inputSales: $('#inputSales').val() || '-',
                inputOrd: globalOrderBy
            };

            document.getElementById('footerLabel').innerHTML = loadingHtml('Memuat data...');

            $.ajax({
                url: reportUrl,
                type: 'get',
                data: data,
                success: function(res) {
                    lastRows = res || [];
                    currentGroupby = groupby;
                    $('#searchBox2').val('');
                    render();
                },
                error: function(xhr) {
                    console.error('laporanpermintaansample_doReport gagal:', xhr.status, xhr.responseText);
                    showToast('⚠️', 'Gagal memuat data (' + xhr.status + ')');
                    lastRows = [];
                    currentGroupby = groupby;
                    render();
                }
            });
        }

        // === RENDER KE TABEL STYLED (.tb-report #mainTable) ===
        function render() {
            const cols = gcart_header.filter(c => c[2] === 1);
            const keys = cols.filter(c => c[4] === 1).map(c => c[0]);
            const thead = document.querySelector('#mainTable thead');
            const tbody = document.getElementById('tableBody');
            const showSub = (gsum_issubtotal === 1);
            const showGrand = (gsum_isgrandtotal === 1);

            const search = ($('#searchBox2').val() || '').trim().toLowerCase();
            const rows = !search ? (lastRows || []) : (lastRows || []).filter(function(r) {
                return rowSearchText(r, cols).indexOf(search) !== -1;
            });

            thead.innerHTML = ReportTable.headHtml(cols);

            if (!rows.length) {
                tbody.innerHTML = '<tr class="empty-row"><td colspan="' + cols.length +
                    '">Tidak ada data ditemukan.</td></tr>';
                document.getElementById('footerLabel').textContent = 'Tidak ada data';
                return;
            }

            let html = '',
                prev = null;
            let sub = {},
                grand = {};
            keys.forEach(k => {
                sub[k] = 0;
                grand[k] = 0;
            });

            rows.forEach(function(r, i) {
                const now = pickCI(r, currentGroupby);

                if (showSub && i !== 0 && prev !== now) {
                    html += totalRowTotal('Subtotal', sub, cols, keys, 'subtotal-row');
                    keys.forEach(k => {
                        sub[k] = 0;
                    });
                }

                keys.forEach(function(k) {
                    const v = currencyNormalizer(pickCI(r, k));
                    sub[k] += v;
                    grand[k] += v;
                });

                html += '<tr class="data-row">' + cols.map(function(c) {
                    const key = c[0],
                        type = c[3];
                    if (type === 'date') return '<td>' + format_date(pickCI(r, key)) + '</td>';
                    if (type === 'float' || type === 'int') return '<td class="num">' + format_number(
                        currencyNormalizer(pickCI(r, key)), c[5]) + '</td>';
                    return '<td>' + nullToEmpty(pickCI(r, key)) + '</td>';
                }).join('') + '</tr>';

                prev = now;
            });

            if (showSub) html += totalRowTotal('Subtotal', sub, cols, keys, 'subtotal-row');
            if (showGrand) html += totalRowTotal('GRAND TOTAL', grand, cols, keys, 'grand-total');

            tbody.innerHTML = html;
            document.getElementById('footerLabel').textContent = 'Menampilkan ' + rows.length + ' baris';
        }

        function totalRowTotal(label, total, cols, keys, cls) {
            const labelIdx = cols.findIndex(c => keys.indexOf(c[0]) === -1);

            const tds = cols.map(function(c, idx) {
                if (keys.indexOf(c[0]) !== -1) {
                    return '<td class="num">' + format_number(total[c[0]], c[5]) + '</td>';
                }
                if (idx === labelIdx) return '<td>' + label + '</td>';
                return '<td></td>';
            });

            return '<tr class="' + cls + '">' + tds.join('') + '</tr>';
        }

        // === PENCARIAN SISI-KLIEN ===
        function applyFilters() {
            if (!lastRows.length) return;
            render();
        }

        function rowSearchText(r, cols) {
            return cols.map(function(c) {
                const v = pickCI(r, c[0]);
                if (c[3] === 'date') return format_date(v);
                return (v == null ? '' : String(v));
            }).join(' ').toLowerCase();
        }
    </script>
@endsection
