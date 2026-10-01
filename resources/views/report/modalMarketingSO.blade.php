<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title></title>
</head>

<body>

    <!-- Modal -->
    <div class="modal fade" id="formSelect" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 1200px">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    {{-- PAKAI data-bs-dismiss (Bootstrap 5), JANGAN data-dismiss (Bootstrap 4).
                         Halaman report memuat dua Bootstrap: BS 4.0.0 (lokal, newmaster2) dan
                         BS 5.3.3 (CDN, masterreport2). BS5 mendaftarkan plugin jQuery-nya saat
                         DOMContentLoaded, jadi SESUDAH BS4 yang mendaftar langsung saat script
                         dieksekusi — BS5 menang, dan itu berlaku juga untuk $("...").modal(...)
                         di script bawah. Karena modal ini dibuka & ditutup lewat BS5, cukup
                         data-bs-dismiss. Sudah diuji di browser. --}}
                    <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table id="tabelSelect" class="table table-bordered table-striped">
                        <thead class="text-center" id="tabelHeader">
                            <tr>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody id="tabel_dataSelect" class="text-left">
                            <tr>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End modal aktiva select customer-->

</body>

</html>


<script>
    // v2 hanya aktif di halaman yang menyalakan window.g_pickerV2 (saat ini: SO) —
    // saat falsy, output identik dengan versi lama (kolom Actions + tombol Select),
    // supaya ~34 halaman lain yang meng-include modal ini tidak berubah sama sekali.
    function pickerHeadHtml(cols) {
        return '<tr>' + cols.map(c => '<th>' + c + '</th>').join('') +
            (window.g_pickerV2 ? '' : '<th>Actions</th>') + '</tr>';
    }

    function pickerRowHtml(idPart, kode, cellsHtml) {
        if (window.g_pickerV2) {
            return '<tr class="pick-row" onclick="buttonPilih(\'' + idPart + '\',\'' + kode + '\')">' +
                cellsHtml + '</tr>';
        }
        return '<tr>' + cellsHtml + '<td class="text-center">' +
            '<button class="btn btn-success btn-sm" type="button" onclick="buttonPilih(\'' + idPart +
            '\',\'' + kode + '\')">Select</button></td></tr>';
    }

    function buttonPilih(idPart, selectedData) {
        console.log(idPart, 'asdqwdqwdqwd', selectedData)
        if (idPart == 1) {
            $("#inputCustomer").val(selectedData);
            $("#formSelect").modal("hide");
        } else if (idPart == 2) {
            $("#inputGroup").val(selectedData);
            $("#formSelect").modal("hide");
        } else if (idPart == 3) {
            $("#inputPIC").val(selectedData);
            $("#formSelect").modal("hide");
        } else if (idPart == 4) {
            $("#inputKategori").val(selectedData);
            $("#formSelect").modal("hide");
        } else if (idPart == 5) {
            $("#inputSubKategori").val(selectedData);
            $("#formSelect").modal("hide");
        } else if (idPart == 6) {
            $("#inputMerk").val(selectedData);
            $("#formSelect").modal("hide");
        }

    }

    function buttonSelect(idModal) {
        $("#formSelect").toggleClass('rt-picker-v2', !!window.g_pickerV2);
        $("#formSelect").modal('toggle')

        if (idModal == "selectCustomer") {
            loadSelectCustomer();
        } else if (idModal == "selectGroup") {
            loadSelectGroup();
        } else if (idModal == "selectPIC") {
            loadSelectPIC();
        } else if (idModal == "selectKategori") {
            loadSelectKategori();
        } else if (idModal == "selectSubKategori") {
            loadSelectSubKategori();
        } else if (idModal == "selectMerk") {
            loadSelectMerk();
        }

    }

    function loadSelectCustomer() {
        let _token = $("#_token").val();

        $('#tabelSelect').DataTable().destroy();

        $.ajax({
            url: "{!! url('laporanmarketingso_loadcustomer') !!}",
            type: "get",
            async: false,
            data: {
                _token: _token,
            },
            success: function(res) {
                console.log(res);
                dataRefresh = res;
            },
        });
        let namaSelect = "Select Customer"
        document.getElementById('exampleModalLabel').innerHTML = namaSelect;

        document.getElementById("tabelHeader").innerHTML = pickerHeadHtml(['Kode', 'Nama', 'Kota']);

        let rowTable = "";
        dataRefresh.forEach((item, i) => {
            rowTable += pickerRowHtml('1', item.KodeCustSupp,
                `<td>${item.KodeCustSupp}</td><td>${item.NamaCustSupp}</td><td>${item.NamaKota}</td>`);
        });

        document.getElementById("tabel_dataSelect").innerHTML = rowTable;
        $("#tabelSelect").DataTable({
            "lengthChange": false,
            "paging": true,
        });
    }

    function loadSelectGroup() {
        let _token = $("#_token").val();

        $('#tabelSelect').DataTable().destroy();

        $.ajax({
            url: "{!! url('laporanmarketingso_loadgroup') !!}",
            type: "get",
            async: false,
            data: {
                _token: _token,
            },
            success: function(res) {
                console.log(res);
                dataRefresh = res;
            },
        });

        let namaSelect = "Select Group"
        document.getElementById('exampleModalLabel').innerHTML = namaSelect;

        document.getElementById("tabelHeader").innerHTML = pickerHeadHtml(['Kode', 'Nama']);

        let rowTable = "";
        dataRefresh.forEach((item, i) => {
            rowTable += pickerRowHtml('2', item.KodeHDGRP,
                `<td>${item.KodeHDGRP}</td><td>${item.NamaHDGRP}</td>`);
        });

        document.getElementById("tabel_dataSelect").innerHTML = rowTable;
        $("#tabelSelect").DataTable({
            "lengthChange": false,
            "paging": true,
        });
    }

    function loadSelectPIC() {
        let _token = $("#_token").val();

        $('#tabelSelect').DataTable().destroy();

        $.ajax({
            url: "{!! url('laporanmarketingso_loadpic') !!}",
            type: "get",
            async: false,
            data: {
                _token: _token,
            },
            success: function(res) {
                console.log(res);
                dataRefresh = res;
            },
        });

        let namaSelect = "Select PIC"
        document.getElementById('exampleModalLabel').innerHTML = namaSelect;

        document.getElementById("tabelHeader").innerHTML = pickerHeadHtml(['Kode Penerima', 'Nama Penerima']);

        let rowTable = "";
        dataRefresh.forEach((item, i) => {
            rowTable += pickerRowHtml('3', item.KODEPIC,
                `<td>${item.KODEPIC}</td><td>${item.NamaPIC}</td>`);
        });

        document.getElementById("tabel_dataSelect").innerHTML = rowTable;
        $("#tabelSelect").DataTable({
            "lengthChange": false,
            "paging": true,
        });
    }

    function loadSelectKategori() {
        let _token = $("#_token").val();

        $('#tabelSelect').DataTable().destroy();

        $.ajax({
            url: "{!! url('laporanmarketingso_loadkategori') !!}",
            type: "get",
            async: false,
            data: {
                _token: _token,
            },
            success: function(res) {
                console.log(res);
                dataRefresh = res;
            },
        });

        let namaSelect = "Select Kategori"
        document.getElementById('exampleModalLabel').innerHTML = namaSelect;

        document.getElementById("tabelHeader").innerHTML = pickerHeadHtml(['Kode Sub Group', 'Nama Sub Group']);

        let rowTable = "";
        dataRefresh.forEach((item, i) => {
            rowTable += pickerRowHtml('4', item.KOdeSubGrp,
                `<td>${item.KOdeSubGrp}</td><td>${item.NamaSubGrp}</td>`);
        });

        document.getElementById("tabel_dataSelect").innerHTML = rowTable;
        $("#tabelSelect").DataTable({
            "lengthChange": false,
            "paging": true,
        });
    }

    function loadSelectSubKategori() {
        let _token = $("#_token").val();

        $('#tabelSelect').DataTable().destroy();

        $.ajax({
            url: "{!! url('laporanmarketingso_loadsubkategori') !!}",
            type: "get",
            async: false,
            data: {
                _token: _token,
            },
            success: function(res) {
                console.log(res);
                dataRefresh = res;
            },
        });

        let namaSelect = "Select Sub-Kategori"
        document.getElementById('exampleModalLabel').innerHTML = namaSelect;

        document.getElementById("tabelHeader").innerHTML = pickerHeadHtml(['Kode Jenis', 'Nama Jenis']);

        let rowTable = "";
        dataRefresh.forEach((item, i) => {
            rowTable += pickerRowHtml('5', item.Urut,
                `<td>${item.Urut}</td><td>${item.Keterangan}</td>`);
        });

        document.getElementById("tabel_dataSelect").innerHTML = rowTable;
        $("#tabelSelect").DataTable({
            "lengthChange": false,
            "paging": true,
        });
    }

    function loadSelectMerk() {
        let _token = $("#_token").val();

        $('#tabelSelect').DataTable().destroy();

        $.ajax({
            url: "{!! url('laporanmarketingso_loadmerk') !!}",
            type: "get",
            async: false,
            data: {
                _token: _token,
            },
            success: function(res) {
                console.log(res);
                dataRefresh = res;
            },
        });

        let namaSelect = "Select Merk"
        document.getElementById('exampleModalLabel').innerHTML = namaSelect;

        // NB: urutan header "Nama, Kode Merk" vs urutan sel KodeMerk/NamaMerk sudah
        // tertukar sebelum perubahan ini — dipertahankan apa adanya (bukan bagian dari
        // task ini untuk diperbaiki, dan mengubahnya berisiko ke 34 halaman lain).
        document.getElementById("tabelHeader").innerHTML = pickerHeadHtml(['Nama', 'Kode Merk']);

        let rowTable = "";
        dataRefresh.forEach((item, i) => {
            rowTable += pickerRowHtml('6', item.KodeMerk,
                `<td>${item.KodeMerk}</td><td>${item.NamaMerk}</td>`);
        });

        document.getElementById("tabel_dataSelect").innerHTML = rowTable;
        $("#tabelSelect").DataTable({
            "lengthChange": false,
            "paging": true,
        });
    }
</script>
