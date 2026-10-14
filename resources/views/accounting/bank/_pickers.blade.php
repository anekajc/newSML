<!-- start modal add -->
{{-- rt-picker-v2 — matches accounting/kas/_pickers.blade.php (see
     docs/new-cust-supp-modal-guide.md). #form is shared by all entity-picker sections
     below (one <div class="showhidemodalbodyadd"> each), so this class restyles the
     modal shell (header, table head, sticky columns, and the DataTables pager below)
     for all of them. 16 of the pick-one-row lists here now paginate (default 10 rows/page, with a
     "Tampilkan" page length selector: 10/25/50/100/Semua) via
     bankInitPicker() in public/js/bank.js. Two tables are intentionally left unpaged
     because they aren't "pick one row" lists — their rows carry editable inputs read
     back by index for every row (getElementById), which DataTables would break by
     detaching off-page rows from the DOM: Invoice (checkbox + editable Kurs/Qty per
     row, explicit Submit button) and DPHUHTBBM's main bukti table (editable Qty input
     + per-row add/remove toggle). --}}
{{-- Navigasi antar pane di modal #form: "Kembali" (footer) = buttonAddListKembali(), mundur
     satu pane (di pane pertama menutup modal). "Close" (header, pengganti tombol x) =
     buttonAddListTutup(), menutup seluruh modal sekaligus apa pun riwayat pane-nya. --}}
<div class="modal fade rt-picker-v2" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div id="" class="modal-content ">

            <div id= "modalAddListAkumulasiBiaya" class="showhidemodalbodyadd">
                <div class="modal-header">

                    <h5 class="modal-title" id="">Akumulasi / Biaya</h5>
                    <button type="button" class="close" onclick="buttonAddListTutup()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div id="" class="">
                    <div class="modal-body">

                        <div class="container-fluid mt-4">
                            <div class="row">
                                <div class="col-12">
                                    {{-- <h3>Akumulasi / Biaya</h3> --}}
                                </div>
                            </div>
                            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
                            <div class="row">
                                <div class="col-12" style="overflow:auto; margin-top:0px; ">
                                    <!-- <div class="container-fluid"> -->


                                    <table id="tabel_add_list_akumulasibiaya" class="dph-tb">
                                        <thead>
                                            <tr>
                                                <th scope="col">Perkiraan</th>
                                                <th scope="col">Keterangan</th>

                                            </tr>
                                        </thead>


                                        <tbody id="tabel_data_add_list_akumulasibiaya" class="text-left">

                                            <tr>

                                                <td>-</td>
                                                <td>-</td>


                                            </tr>
                                        </tbody>


                                    </table>
                                    <!-- </div> -->
                                    <!-- <button onclick="buttonSubKategori()">tes</button> -->
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div id="contentContainer" class="modal-footer ">

                    <button type="button" class="btn btn-secondary btn-pill-secondary"
                        onclick="buttonAddListKembali()">Kembali</button>
                </div>
            </div>
            <div id= "modalAddListAktivaDetailX" class="showhidemodalbodyadd">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Aktiva</h5>
                    <button type="button" class="close" onclick="buttonAddListTutup()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div id="formBsGrid" class="" style="margin-top: 1rem;">
                    <div class="modal-body">
                        {{-- Layout grid mengikuti modalAddFormAktiva di memorialkoreksi.blade.php:
                             semua label col-md-3 supaya setiap kolom input mulai di titik x yang sama. --}}
                        <div class="container-fluid p-0">

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Group Aktiva</label>
                                    </div>
                                </div>
                                <div class="col-md-2" style="padding-right:0">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="input_aktivax_groupaktiva"
                                            placeholder="" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3" style="padding-left:0">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="input_aktivax_namagroupaktiva"
                                            placeholder="" disabled>
                                    </div>
                                </div>
                                <div class="col-md-2 text-md-right">
                                    <div class="form-group">
                                        <label style="font-size: 11px">Tgl Perolehan</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="date" class="form-control text-center"
                                            id="input_aktivax_tglperolehan" placeholder="">
                                    </div>
                                </div>
                            </div>

                            <div class="row kas-row-tight">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Divisi</label>
                                    </div>
                                </div>
                                <div class="col-md-2" style="padding-right:0">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="input_aktivax_devisi"
                                            placeholder="" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3" style="padding-left:0">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="input_aktivax_namadevisi"
                                            placeholder="" disabled>
                                    </div>
                                </div>
                                <div class="col-md-2 text-md-right">
                                    <div class="form-group">
                                        <label style="font-size: 11px">Tgl Pemakaian</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="date" class="form-control text-center"
                                            id="input_aktivax_tglpemakaian" placeholder="">
                                    </div>
                                </div>
                            </div>

                            <div class="row kas-row-tight">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>No. Aktiva</label>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="input_aktivax_noaktiva"
                                            placeholder="" disabled>
                                        <input type="hidden" class="form-control" id="input_aktivax_nobelakang"
                                            placeholder="">
                                    </div>
                                </div>
                            </div>

                            <div class="row kas-row-tight">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Tipe Aktiva</label>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <select id="input_aktivax_tipeaktiva" class="form-control form-select-lg mb-3"
                                            aria-label=".form-select-lg example" disabled>
                                            <option value=0>Aktiva Tetap</option>
                                            <option value=1 selected>Aktiva yang dibiayakan</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row kas-row-tight">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label style="font-size: 11px">Keterangan</label>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="input_aktivax_keterangan"
                                            placeholder="">
                                    </div>
                                </div>
                            </div>

                            <div class="row kas-row-tight">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Kuantum</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="number" class="form-control text-right"
                                            id="input_aktivax_kuantum" placeholder="" disabled>
                                    </div>
                                </div>
                                <div class="col-md-2 text-md-right">
                                    <div class="form-group">
                                        <label> % Susut</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="number" class="form-control text-right"
                                            id="input_aktivax_susut" placeholder="">
                                    </div>
                                </div>
                                <div class="col-md-1 text-md-right">
                                    <div class="form-group">
                                        <label>Metode</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <select id="input_aktivax_metodepenyusutan"
                                            class="form-control form-select-lg mb-3"
                                            aria-label=".form-select-lg example">
                                            <option value='L'>[L]urus</option>
                                            <option value='M' selected>[M]enurun</option>
                                            <option value='P'>[P]ajak</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row kas-row-tight">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Akum. Penyusutan</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group input-group">
                                        <input type="text" class="form-control" id="input_aktivax_akumulasi"
                                            placeholder="" disabled>
                                        <button class="btn btn-chip-biru text-right" id="buttonAddListXAkumulasi"
                                            onclick="buttonAddListXAkumulasi()"><i class="bi bi-search"></i></button>
                                    </div>
                                </div>
                                <div class="col-md-2"></div>
                            </div>

                            <div class="row kas-row-tight">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Biaya Penyusutan 1</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group input-group">
                                        <input type="text" class="form-control text-left"
                                            id="input_aktivax_biaya1" placeholder="" disabled>
                                        <button class="btn btn-chip-biru text-right" id="buttonAddListXBiaya1"
                                            onclick="buttonAddListXBiaya('input_aktivax_biaya1')"><i
                                                class="bi bi-search"></i></button>
                                    </div>
                                </div>
                                <div class="col-md-2" style="padding-left:0">
                                    <div class="form-group">
                                        <input type="number" class="form-control text-right"
                                            id="input_aktivax_persen1" placeholder="">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group text-left">
                                        %
                                    </div>
                                </div>
                            </div>

                            <div class="row kas-row-tight">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Biaya Penyusutan 2</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group input-group">
                                        <input type="text" class="form-control text-left"
                                            id="input_aktivax_biaya2" placeholder="" disabled>
                                        <button class="btn btn-chip-biru text-right" id="buttonAddListXBiaya2"
                                            onclick="buttonAddListXBiaya('input_aktivax_biaya2')"><i
                                                class="bi bi-search"></i></button>
                                    </div>
                                </div>
                                <div class="col-md-2" style="padding-left:0">
                                    <div class="form-group">
                                        <input type="number" class="form-control text-right"
                                            id="input_aktivax_persen2" placeholder="">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group text-left">
                                        %
                                    </div>
                                </div>
                            </div>

                            <div class="row kas-row-tight">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Biaya Penyusutan 3</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group input-group">
                                        <input type="text" class="form-control text-left"
                                            id="input_aktivax_biaya3" placeholder="" disabled>
                                        <button class="btn btn-chip-biru text-right" id="buttonAddListXBiaya3"
                                            onclick="buttonAddListXBiaya('input_aktivax_biaya3')"><i
                                                class="bi bi-search"></i></button>
                                    </div>
                                </div>
                                <div class="col-md-2" style="padding-left:0">
                                    <div class="form-group">
                                        <input type="number" class="form-control text-right"
                                            id="input_aktivax_persen3" placeholder="">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group text-left">
                                        %
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div id="contentContainer" class="modal-footer ">
                    <button type="button" class="btn btn-secondary btn-pill-secondary"
                        onclick="buttonAddListKembali()">Kembali</button>
                    <button type="button" class="btn btn-primary btn-action-primary btn-pill-primary"
                        onclick="submitAddAktivaX()">Simpan</button>
                </div>
            </div>
            <div id= "modalAddListValas" class="showhidemodalbodyadd">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Valas</h5>
                    <button type="button" class="close" onclick="buttonAddListTutup()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>


                <div id="" class="">
                    <div class="modal-body">

                        <div class="container-fluid mt-4">
                            <div class="row">
                                <div class="col-12">
                                    {{-- <h3>Valas</h3> --}}
                                </div>
                            </div>
                            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
                            <div class="row">
                                <div class="col-12" style="overflow:auto; margin-top:0px; ">
                                    <!-- <div class="container-fluid"> -->

                                    <table id="tabel_add_list_valas" class="dph-tb">
                                        <thead>
                                            <tr>
                                                <th scope="col">Kode</th>
                                                <th scope="col">Nama</th>
                                                <th scope="col">Kurs</th>

                                            </tr>
                                        </thead>


                                        <tbody id="tabel_data_add_list_valas" class="text-left">

                                            <tr>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>
                                        </tbody>


                                    </table>
                                    <!-- </div> -->
                                    <!-- <button onclick="buttonSubKategori()">tes</button> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="contentContainer" class="modal-footer ">
                    <button type="button" class="btn btn-secondary btn-pill-secondary"
                        onclick="buttonAddListKembali()">Kembali</button>
                </div>
            </div>

            <div id= "modalAddListCustsupp" class="showhidemodalbodyadd">
                <div class="modal-header">
                    <h5 class="modal-title" id="">CustSupp</h5>
                    <button type="button" class="close" onclick="buttonAddListTutup()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div id="" class="">
                    <div class="modal-body">
                        <div class="container-fluid mt-4">
                            <div class="row">
                                <div class="col-12">
                                    {{-- <h3>CustSupp</h3> --}}
                                </div>
                            </div>
                            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
                            <div class="row">
                                <div class="col-12" style="overflow:auto; margin-top:0px; ">
                                    <!-- <div class="container-fluid"> -->


                                    <table id="tabel_add_list_custsupp" class="dph-tb">
                                        <thead>
                                            <tr>
                                                <th scope="col">Kode</th>
                                                <th scope="col">Nama</th>
                                                <th scope="col">Kota</th>
                                            </tr>
                                        </thead>

                                        <tbody id="tabel_data_add_list_custsupp" class="text-left">
                                            <tr>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <!-- </div> -->
                                    <!-- <button onclick="buttonSubKategori()">tes</button> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="contentContainer" class="modal-footer ">
                    <button type="button" class="btn btn-secondary btn-pill-secondary"
                        onclick="buttonAddListKembali()">Kembali</button>
                </div>
            </div>

            {{-- No Titipan (BBK + Lawan 113400). Baris diisi bukaListTitipan() di bank.js; klik
                 baris = buttonAddPickTitipan(index). Data: BankController::getNoTitipan(). --}}
            <div id= "modalAddListTitipan" class="showhidemodalbodyadd">
                <div class="modal-header">
                    <h5 class="modal-title" id="">No Titipan</h5>
                    <button type="button" class="close" onclick="buttonAddListTutup()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div id="" class="">
                    <div class="modal-body">
                        <div class="container-fluid mt-4">
                            <div class="row">
                                <div class="col-12" style="overflow:auto; margin-top:0px; ">
                                    <table id="tabel_add_list_titipan" class="dph-tb">
                                        <thead>
                                            <tr>
                                                <th scope="col">No Bukti</th>
                                                <th scope="col">Tanggal</th>
                                                <th scope="col">Customer</th>
                                                <th scope="col">Keterangan</th>
                                                <th scope="col">JumlahRp</th>
                                                <th scope="col">Sisa</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tabel_data_add_list_titipan" class="text-left">
                                            <tr>
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
                    </div>
                </div>

                <div id="contentContainer" class="modal-footer ">
                    <button type="button" class="btn btn-secondary btn-pill-secondary"
                        onclick="buttonAddListKembali()">Kembali</button>
                </div>
            </div>
            <div id= "modalAddListAktiva" class="showhidemodalbodyadd">
                <div class="modal-header">


                    <h5 class="modal-title" id="">Aktiva</h5>
                    <button type="button" class="close" onclick="buttonAddListTutup()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>


                <div id="" class="">
                    <div class="modal-body">

                        <div class="container-fluid mt-4">
                            <div class="row">
                                <div class="col-12">
                                    {{-- <h3>Aktiva</h3> --}}
                                </div>
                            </div>
                            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
                            {{-- Tempat parkir tombol "+ Aktiva baru". Setelah DataTables di-init,
                                 bankPasangTombolAktivaBaru() di bank.js memindahkannya ke sebelah dropdown
                                 Tampilkan (hanya AKV + BBK), dan bankParkirTombolAktivaBaru() mengembalikannya
                                 ke sini sebelum destroy() supaya tombolnya tidak ikut terhapus. --}}
                            <div id="holderButtonAddNewAktiva" style="display:none">
                                <button id="buttonAddNewAktiva" type="button" class="btn btn-chip-biru ml-2"
                                    onclick="buttonAddNewAktiva()">+ Aktiva baru</button>
                            </div>
                            <div class="row">
                                <div class="col-12" style="overflow:auto; margin-top:0px; ">
                                    <!-- <div class="container-fluid"> -->


                                    <table id="tabel_add_list_aktiva" class="dph-tb">
                                        <thead>
                                            <tr>
                                                <th scope="col">Kode</th>
                                                <th scope="col">Keterangan</th>
                                                <th scope="col">Tanggal</th>

                                            </tr>
                                        </thead>


                                        <tbody id="tabel_data_add_list_aktiva" class="text-left">

                                            <tr>

                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>


                                            </tr>
                                        </tbody>


                                    </table>
                                    <!-- </div> -->
                                    <!-- <button onclick="buttonSubKategori()">tes</button> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div id="contentContainer" class="modal-footer ">
                    <button type="button" class="btn btn-secondary btn-pill-secondary"
                        onclick="buttonAddListKembali()">Kembali</button>
                </div>
            </div>

            <div id= "modalAddListAktivaDetail" class="showhidemodalbodyadd">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Aktiva</h5>
                    <button type="button" class="close" onclick="buttonAddListTutup()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div id="formBsGrid" class="" style="margin-top: 1rem;">
                    <div class="modal-body">
                        {{-- Layout grid sama dengan modalAddListAktivaDetailX (form aktiva baru), tapi
                             read-only: tanpa tombol cari. Yang bisa diubah hanya kedua tanggal, plus
                             Keterangan yang dibuka oleh buttonAddPickAktiva(). --}}
                        <div class="container-fluid p-0">

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Group Aktiva</label>
                                    </div>
                                </div>
                                <div class="col-md-2" style="padding-right:0">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="input_aktiva_groupaktiva"
                                            placeholder="" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3" style="padding-left:0">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="input_aktiva_namagroupaktiva"
                                            placeholder="" disabled>
                                    </div>
                                </div>
                                <div class="col-md-2 text-md-right">
                                    <div class="form-group">
                                        <label style="font-size: 11px">Tgl Perolehan</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="date" class="form-control text-center"
                                            id="input_aktiva_tglperolehan" placeholder="" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="row kas-row-tight">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Divisi</label>
                                    </div>
                                </div>
                                <div class="col-md-2" style="padding-right:0">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="input_aktiva_devisi"
                                            placeholder="" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3" style="padding-left:0">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="input_aktiva_namadevisi"
                                            placeholder="" disabled>
                                    </div>
                                </div>
                                <div class="col-md-2 text-md-right">
                                    <div class="form-group">
                                        <label style="font-size: 11px">Tgl Pemakaian</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="date" class="form-control text-center"
                                            id="input_aktiva_tglpemakaian" placeholder="" disabled  >
                                    </div>
                                </div>
                            </div>

                            <div class="row kas-row-tight">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>No. Aktiva</label>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="input_aktiva_noaktiva"
                                            placeholder="" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="row kas-row-tight">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Tipe Aktiva</label>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <select id="input_aktiva_tipeaktiva" class="form-control form-select-lg mb-3"
                                            aria-label=".form-select-lg example" disabled>
                                            <option value=0>Aktiva Tetap</option>
                                            <option value=1 selected>Aktiva yang dibiayakan</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row kas-row-tight">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label style="font-size: 11px">Keterangan</label>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="input_aktiva_keterangan"
                                            placeholder="" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="row kas-row-tight">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Kuantum</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="number" class="form-control text-right"
                                            id="input_aktiva_kuantum" placeholder="" disabled>
                                    </div>
                                </div>
                                <div class="col-md-2 text-md-right">
                                    <div class="form-group">
                                        <label> % Susut</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="number" class="form-control text-right"
                                            id="input_aktiva_susut" placeholder="" disabled>
                                    </div>
                                </div>
                                <div class="col-md-1 text-md-right">
                                    <div class="form-group">
                                        <label>Metode</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <select id="input_aktiva_metodepenyusutan"
                                            class="form-control form-select-lg mb-3"
                                            aria-label=".form-select-lg example" disabled>
                                            <option value='L'>[L]urus</option>
                                            <option value='M' selected>[M]enurun</option>
                                            <option value='P'>[P]ajak</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row kas-row-tight">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Akum. Penyusutan</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="input_aktiva_akumulasi"
                                            placeholder="" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="row kas-row-tight">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Biaya Penyusutan 1</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control text-left"
                                            id="input_aktiva_biaya1" placeholder="" disabled>
                                    </div>
                                </div>
                                <div class="col-md-2" style="padding-left:0">
                                    <div class="form-group">
                                        <input type="number" class="form-control text-right"
                                            id="input_aktiva_persen1" placeholder="" disabled>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group text-left">
                                        %
                                    </div>
                                </div>
                            </div>

                            <div class="row kas-row-tight">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Biaya Penyusutan 2</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control text-left"
                                            id="input_aktiva_biaya2" placeholder="" disabled>
                                    </div>
                                </div>
                                <div class="col-md-2" style="padding-left:0">
                                    <div class="form-group">
                                        <input type="number" class="form-control text-right"
                                            id="input_aktiva_persen2" placeholder="" disabled>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group text-left">
                                        %
                                    </div>
                                </div>
                            </div>

                            <div class="row kas-row-tight">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Biaya Penyusutan 3</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control text-left"
                                            id="input_aktiva_biaya3" placeholder="" disabled>
                                    </div>
                                </div>
                                <div class="col-md-2" style="padding-left:0">
                                    <div class="form-group">
                                        <input type="number" class="form-control text-right"
                                            id="input_aktiva_persen3" placeholder="" disabled>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group text-left">
                                        %
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div id="contentContainer" class="modal-footer ">
                    <button type="button" class="btn btn-secondary btn-pill-secondary"
                        onclick="buttonAddListKembali()">Kembali</button>
                    <button type="button" class="btn btn-primary btn-action-primary btn-pill-primary"
                        onclick="submitAddAktiva()">Simpan</button>
                </div>
            </div>


            <div id= "modalAddListDPP" class="showhidemodalbodyadd">
                <div class="modal-header">


                    <h5 class="modal-title" id="">DPP</h5>
                    <button type="button" class="close" onclick="buttonAddListTutup()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>


                <div id="" class="">
                    <div class="modal-body">

                        <div class="container-fluid mt-4">
                            <div class="row">
                                <div class="col-12">
                                    {{-- <h3>DPP</h3> --}}
                                </div>
                            </div>
                            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
                            <div class="row">
                                <div class="col-12" style="overflow:auto; margin-top:0px; ">
                                    <!-- <div class="container-fluid"> -->


                                    <table id="tabel_add_list_dpp" class="dph-tb">
                                        <thead>
                                            <tr>
                                                <th scope="col">No DPP</th>
                                                <th scope="col">Kode Supp</th>
                                                <th scope="col">Nama Supp</th>
                                                <th scope="col">Nominal</th>
                                                <th scope="col">K. Bayar</th>
                                                <th scope="col">L. Bayar</th>

                                            </tr>
                                        </thead>


                                        <tbody id="tabel_data_add_list_dpp" class="text-left">

                                            <tr>

                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>


                                            </tr>
                                        </tbody>


                                    </table>
                                    <!-- </div> -->
                                    <!-- <button onclick="buttonSubKategori()">tes</button> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="contentContainer" class="modal-footer ">
                    <button type="button" class="btn btn-secondary btn-pill-secondary"
                        onclick="buttonAddListKembali()">Kembali</button>
                </div>
            </div>

            <div id= "modalAddListDPH" class="showhidemodalbodyadd">
                <div class="modal-header">


                    <h5 class="modal-title" id="">DPH</h5>
                    <button type="button" class="close" onclick="buttonAddListTutup()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>


                <div id="" class="">
                    <div class="modal-body">

                        <div class="container-fluid mt-4">
                            <div class="row">
                                <div class="col-12">
                                    {{-- <h3>DPH</h3> --}}
                                </div>
                            </div>
                            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
                            <div class="row">
                                <div class="col-12" style="overflow:auto; margin-top:0px; ">
                                    <!-- <div class="container-fluid"> -->


                                    <table id="tabel_add_list_dph" class="dph-tb">
                                        <thead>
                                            <tr>
                                                <th scope="col">No DPH</th>
                                                <th scope="col">Kode Supp</th>
                                                <th scope="col">Nama Supp</th>
                                                <th scope="col">Nominal</th>
                                                <th scope="col">K. Bayar</th>
                                                <th scope="col">L. Bayar</th>

                                            </tr>
                                        </thead>


                                        <tbody id="tabel_data_add_list_dph" class="text-left">

                                            <tr>

                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>


                                            </tr>
                                        </tbody>


                                    </table>
                                    <!-- </div> -->
                                    <!-- <button onclick="buttonSubKategori()">tes</button> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div id="contentContainer" class="modal-footer ">
                    <button type="button" class="btn btn-secondary btn-pill-secondary"
                        onclick="buttonAddListKembali()">Kembali</button>
                </div>
            </div>


            <div id= "modalAddListDPHUHT" class="showhidemodalbodyadd">
                <div class="modal-header">


                    <h5 class="modal-title" id="">DPH</h5>
                    <button type="button" class="close" onclick="buttonAddListTutup()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>


                <div id="" class="">
                    <div class="modal-body">

                        <div class="container-fluid mt-4">
                            <div class="row">
                                <div class="col-12">
                                    {{-- <h3>DPH</h3> --}}
                                </div>
                            </div>
                            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
                            <div class="row">
                                <div class="col-12" style="overflow:auto; margin-top:0px; ">
                                    <!-- <div class="container-fluid"> -->


                                    <table id="tabel_add_list_dphuht" class="dph-tb">
                                        <thead>
                                            <tr>
                                                <th scope="col">No DPH</th>
                                                <th scope="col">No Faktur</th>
                                                <th scope="col">Kode Supp</th>
                                                <th scope="col">Nama Supp</th>
                                                <th scope="col">Nominal</th>
                                                <th scope="col">K. Bayar</th>
                                                <th scope="col">L. Bayar</th>

                                            </tr>
                                        </thead>


                                        <tbody id="tabel_data_add_list_dphuht" class="text-left">

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
                                    <!-- </div> -->
                                    <!-- <button onclick="buttonSubKategori()">tes</button> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div id="contentContainer" class="modal-footer ">
                    <button type="button" class="btn btn-secondary btn-pill-secondary"
                        onclick="buttonAddListKembali()">Kembali</button>
                </div>
            </div>

            <div id= "modalAddListDPHUHTBBM" class="showhidemodalbodyadd">
                <div class="modal-header">


                    <h5 class="modal-title" id="">Proses - Retur Uang Muka</h5>
                    <button type="button" class="close" onclick="buttonAddListTutup()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>


                <div id="" class="">
                    <div class="modal-body">

                        <div class="container-fluid mt-4">

                            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
                            <div class="row">
                                <div class="col-12" style="overflow:auto;">
                                    <!-- <div class="container-fluid"> -->


                                    <table id="tabel_add_list_dphuhtbbm_custsupp" class="dph-tb">
                                        <thead>
                                            <tr>
                                                <th scope="col">KODE</th>
                                                <th scope="col">Nama</th>

                                            </tr>
                                        </thead>


                                        <tbody id="tabel_data_add_list_dphuhtbbm_custsupp" class="text-left">

                                            <tr>
                                                <td>-</td>
                                                <td>-</td>

                                            </tr>
                                        </tbody>


                                    </table>
                                    <!-- </div> -->
                                    <!-- <button onclick="buttonSubKategori()">tes</button> -->
                                </div>
                            </div>
                            <hr>

                            <div class="row" style="margin-top: 20px">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>CustSupp</label>
                                    </div>
                                </div>
                                <!-- <div class="col-3 text-right">
                <div class="form-group">
              </div>
            </div> -->
                                <div class="col-md-2">
                                    <div class="form-group input-group">
                                        <input type="text" class="form-control" id="input_dphuhtbbm_kodecustsupp"
                                            placeholder="" disabled>

                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group input-group">

                                        <input type="text" class="form-control" id="input_dphuhtbbm_namacustsupp"
                                            placeholder="" disabled>
                                    </div>
                                </div>

                            </div>



                            <div class="row">
                                <div class="col-12" style="overflow:auto; ">
                                    <!-- <div class="container-fluid"> -->


                                    <table id="tabel_add_list_dphuhtbbm" class="dph-tb">
                                        <thead>
                                            <tr>
                                                <th scope="col">Nobukti</th>
                                                <th scope="col">NoRetur</th>
                                                <th scope="col">Tanggal</th>
                                                <th scope="col">PO</th>
                                                <th scope="col">Valas</th>

                                                <th scope="col">Kurs</th>
                                                <th scope="col">DPP</th>

                                                <th scope="col">PPN</th>

                                                <th scope="col">Subtotal</th>
                                                <th scope="col">Actions</th>

                                            </tr>
                                        </thead>


                                        <tbody id="tabel_data_add_list_dphuhtbbm" class="text-left">

                                            <tr>
                                                <td colspan=10 class="text-center">Belum ada data</td>
                                            </tr>
                                        </tbody>

                                    </table>
                                    <!-- </div> -->
                                    <!-- <button onclick="buttonSubKategori()">tes</button> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="contentContainer" class="modal-footer ">
                    <button type="button" class="btn btn-secondary btn-pill-secondary"
                        onclick="buttonAddListKembali()">Kembali</button>
                </div>
            </div>



            <div id= "modalAddListDevisi" class="showhidemodalbodyadd">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Devisi</h5>
                    <button type="button" class="close" onclick="buttonAddListTutup()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div id="" class="">
                    <div class="modal-body">
                        <div class="container-fluid mt-4">
                            <div class="row">
                                <div class="col-12">
                                    {{-- <h3>Devisi</h3> --}}
                                </div>
                            </div>
                            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
                            <div class="row">
                                <div class="col-12" style="overflow:auto; margin-top:0px; ">
                                    <!-- <div class="container-fluid"> -->


                                    <table id="tabel_add_list_devisi" class="dph-tb">
                                        <thead>
                                            <tr>
                                                <th scope="col">Kode</th>
                                                <th scope="col">Nama</th>

                                            </tr>
                                        </thead>


                                        <tbody id="tabel_data_add_list_devisi" class="text-left">

                                            <tr>

                                                <td>-</td>
                                                <td>-</td>


                                            </tr>
                                        </tbody>


                                    </table>
                                    <!-- </div> -->
                                    <!-- <button onclick="buttonSubKategori()">tes</button> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div id="contentContainer" class="modal-footer ">
                    <button type="button" class="btn btn-secondary btn-pill-secondary"
                        onclick="buttonAddListKembali()">Kembali</button>
                </div>
            </div>


            <div id= "modalAddListBon" class="showhidemodalbodyadd">
                <div class="modal-header">


                    <h5 class="modal-title" id="">Perkiraan</h5>
                    <button type="button" class="close" onclick="buttonAddListTutup()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>


                <div id="" class="">
                    <div class="modal-body">

                        <div class="container-fluid mt-4">
                            <div class="row">
                                <div class="col-12">
                                    {{-- <h3>Bon</h3> --}}
                                </div>
                            </div>
                            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
                            <div class="row">
                                <div class="col-12" style="overflow:auto; margin-top:0px; ">
                                    <!-- <div class="container-fluid"> -->


                                    <table id="tabel_add_list_bon" class="dph-tb">
                                        <thead>
                                            <tr>
                                                <th scope="col">Nobon</th>
                                                <th scope="col">Penerima</th>
                                                <th scope="col">Keterangan</th>
                                                <th scope="col">Perkiraan</th>
                                                <th scope="col">Jumlah</th>

                                            </tr>
                                        </thead>


                                        <tbody id="tabel_data_add_list_bon" class="text-left">

                                            <tr>

                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>



                                            </tr>
                                        </tbody>


                                    </table>
                                    <!-- </div> -->
                                    <!-- <button onclick="buttonSubKategori()">tes</button> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div id="contentContainer" class="modal-footer ">
                    <button type="button" class="btn btn-secondary btn-pill-secondary"
                        onclick="buttonAddListKembali()">Kembali</button>
                </div>
            </div>

            <div id= "modalAddListPerkiraan" class="showhidemodalbodyadd">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Perkiraan</h5>
                    <button type="button" class="close" onclick="buttonAddListTutup()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div id="" class="">
                    <div class="modal-body">
                        <div class="container-fluid mt-4">
                            <div class="row">
                                <div class="col-12">
                                    {{-- <h3>Perkiraan</h3> --}}
                                </div>
                            </div>
                            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
                            <div class="row">
                                <div class="col-12" style="overflow:auto; margin-top:0px; ">
                                    <!-- <div class="container-fluid"> -->


                                    <table id="tabel_add_list_perkiraan" class="dph-tb">
                                        <thead>
                                            <tr>
                                                <th scope="col">Perkiraan</th>
                                                <th scope="col">Nama</th>
                                                <th scope="col">Simbol</th>

                                            </tr>
                                        </thead>


                                        <tbody id="tabel_data_add_list_perkiraan" class="text-left">

                                            <tr>

                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>
                                        </tbody>


                                    </table>
                                    <!-- </div> -->
                                    <!-- <button onclick="buttonSubKategori()">tes</button> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div id="contentContainer" class="modal-footer ">
                    <button type="button" class="btn btn-secondary btn-pill-secondary"
                        onclick="buttonAddListKembali()">Kembali</button>
                </div>
            </div>

            <div id= "modalAddListDepartemen" class="showhidemodalbodyadd">
                <div class="modal-header">


                    <h5 class="modal-title" id="">Departemen</h5>
                    <button type="button" class="close" onclick="buttonAddListTutup()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>


                <div id="" class="">
                    <div class="modal-body">

                        <div class="container-fluid mt-4">
                            <div class="row">
                                <div class="col-12">
                                    {{-- <h3>Departemen</h3> --}}
                                </div>
                            </div>
                            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
                            <div class="row">
                                <div class="col-12" style="overflow:auto; margin-top:0px; ">
                                    <!-- <div class="container-fluid"> -->


                                    <table id="tabel_add_list_departemen" class="dph-tb">
                                        <thead>
                                            <tr>
                                                <th scope="col">Kode</th>
                                                <th scope="col">Nama</th>

                                            </tr>
                                        </thead>


                                        <tbody id="tabel_data_add_list_departemen" class="text-left">

                                            <tr>

                                                <td>-</td>
                                                <td>-</td>


                                            </tr>
                                        </tbody>


                                    </table>
                                    <!-- </div> -->
                                    <!-- <button onclick="buttonSubKategori()">tes</button> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div id="contentContainer" class="modal-footer ">
                    <button type="button" class="btn btn-secondary btn-pill-secondary"
                        onclick="buttonAddListKembali()">Kembali</button>
                </div>
            </div>

            <div id= "modalAddListLawan" class="showhidemodalbodyadd">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Lawan</h5>
                    <button type="button" class="close" onclick="buttonAddListTutup()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div id="" class="">
                    <div class="modal-body">
                        <div class="container-fluid mt-4">
                            <div class="row">
                                <div class="col-12">
                                    {{-- <h3>Lawan</h3> --}}
                                </div>
                            </div>
                            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
                            <div class="row">
                                <div class="col-12" style="overflow:auto; margin-top:0px; ">
                                    <!-- <div class="container-fluid"> -->
                                    <table id="tabel_add_list_lawan" class="dph-tb">
                                        <thead>
                                            <tr>
                                                <th scope="col">Perkiraan</th>
                                                <th scope="col">Nama</th>
                                                <th scope="col">Simbol</th>

                                            </tr>
                                        </thead>

                                        <tbody id="tabel_data_add_list_lawan" class="text-left">

                                            <tr>

                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>


                                            </tr>
                                        </tbody>


                                    </table>
                                    <!-- </div> -->
                                    <!-- <button onclick="buttonSubKategori()">tes</button> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div id="contentContainer" class="modal-footer ">
                    <button type="button" class="btn btn-secondary btn-pill-secondary"
                        onclick="buttonAddListKembali()">Kembali</button>
                </div>
            </div>

            <div id= "modalAddListCosting" class="showhidemodalbodyadd">
                <div class="modal-header">


                    <h5 class="modal-title" id="">Costing</h5>
                    <button type="button" class="close" onclick="buttonAddListTutup()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>


                <div id="" class="">
                    <div class="modal-body">

                        <div class="container-fluid mt-4">
                            <div class="row">
                                <div class="col-12">
                                    <h3>Costing</h3>
                                </div>
                            </div>
                            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
                            <div class="row">
                                <div class="col-12" style="overflow:auto; margin-top:0px; ">

                                    <table id="tabel_add_list_costing" class="dph-tb">
                                        <thead>
                                            <tr>
                                                <th scope="col">Kode</th>
                                                <th scope="col">Nama</th>

                                            </tr>
                                        </thead>


                                        <tbody id="tabel_data_add_list_costing" class="text-left">

                                            <tr>

                                                <td>-</td>
                                                <td>-</td>


                                            </tr>
                                        </tbody>


                                    </table>
                                    <!-- </div> -->
                                    <!-- <button onclick="buttonSubKategori()">tes</button> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div id="contentContainer" class="modal-footer ">
                    <button type="button" class="btn btn-secondary btn-pill-secondary"
                        onclick="buttonAddListKembali()">Kembali</button>
                </div>
            </div>

            <div id= "modalAddListSubCosting" class="showhidemodalbodyadd">
                <div class="modal-header">


                    <h5 class="modal-title" id="">SubCosting</h5>
                    <button type="button" class="close" onclick="buttonAddListTutup()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>


                <div id="" class="">
                    <div class="modal-body">

                        <div class="container-fluid mt-4">
                            <div class="row">
                                <div class="col-12">
                                    {{-- <h3>SubCosting</h3> --}}
                                </div>
                            </div>
                            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
                            <div class="row">
                                <div class="col-12" style="overflow:auto; margin-top:0px; ">
                                    <!-- <div class="container-fluid"> -->


                                    <table id="tabel_add_list_subcosting" class="dph-tb">
                                        <thead>
                                            <tr>
                                                <th scope="col">Kode</th>
                                                <th scope="col">Nama</th>

                                            </tr>
                                        </thead>


                                        <tbody id="tabel_data_add_list_subcosting" class="text-left">

                                            <tr>

                                                <td>-</td>
                                                <td>-</td>


                                            </tr>
                                        </tbody>


                                    </table>
                                    <!-- </div> -->
                                    <!-- <button onclick="buttonSubKategori()">tes</button> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div id="contentContainer" class="modal-footer ">
                    <button type="button" class="btn btn-secondary btn-pill-secondary"
                        onclick="buttonAddListKembali()">Kembali</button>
                </div>
            </div>


            <div id= "modalAddListCustomer" class="showhidemodalbodyadd">
                <div class="modal-header">


                    <h5 class="modal-title" id="">Supplier</h5>
                    <button type="button" class="close" onclick="buttonAddListTutup()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>


                <div id="" class="">
                    <div class="modal-body">

                        <div class="container-fluid mt-4">
                            <div class="row">
                                <div class="col-12">
                                    {{-- <h3>Customer</h3> --}}
                                </div>
                            </div>
                            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
                            <div class="row">
                                <div class="col-12" style="overflow:auto; margin-top:0px; ">
                                    <!-- <div class="container-fluid"> -->


                                    <table id="tabel_add_list_customer" class="dph-tb">
                                        <thead>
                                            <tr>
                                                <th scope="col">Kode</th>
                                                <th scope="col">Nama</th>
                                                <th scope="col">Alamat</th>
                                                <th scope="col">Kota</th>

                                            </tr>
                                        </thead>


                                        <tbody id="tabel_data_add_list_customer" class="text-left">

                                            <tr>

                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>


                                            </tr>
                                        </tbody>


                                    </table>
                                    <!-- </div> -->
                                    <!-- <button onclick="buttonSubKategori()">tes</button> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div id="contentContainer" class="modal-footer ">
                    <button type="button" class="btn btn-secondary btn-pill-secondary"
                        onclick="buttonAddListKembali()">Kembali</button>
                </div>
            </div>

            <div id= "modalAddListInvoice" class="showhidemodalbodyadd">
                <div class="modal-header">


                    <h5 class="modal-title" id="">Invoice</h5>
                    <button type="button" class="close" onclick="buttonAddListTutup()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>


                <div id="" class="">
                    <div class="modal-body">

                        <div class="container-fluid mt-4">
                            <div class="row">
                                <div class="col-12">
                                    <h3>Invoice</h3>
                                </div>
                            </div>
                            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
                            <div class="row">
                                <div class="col-12" style="overflow:auto; margin-top:-60px; ">
                                    <!-- <div class="container-fluid"> -->


                                    <table id="tabel_add_list_invoice" class="dph-tb">
                                        <thead>
                                            <tr>
                                                <th class="text-center" scope="col">v</th>
                                                <th scope="col">No Faktur</th>
                                                <th scope="col">Tanggal</th>
                                                <th scope="col">Jatuh Tempo</th>
                                                <th scope="col">Valas</th>
                                                <th scope="col">Nilai Kredit Note</th>
                                                <th scope="col">Kurs</th>
                                                <th scope="col">Nilai KN (Rp)</th>
                                                <th scope="col">Piutang (Valas)</th>
                                                <th scope="col">Piutang (Rp)</th>
                                                <th scope="col">Keterangan</th>

                                            </tr>
                                        </thead>


                                        <tbody id="tabel_data_add_list_invoice" class="text-left">

                                            <tr>

                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
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
                                    <!-- </div> -->
                                    <!-- <button onclick="buttonSubKategori()">tes</button> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div id="contentContainer" class="modal-footer ">
                    <button type="button" class="btn btn-secondary btn-pill-secondary"
                        onclick="buttonAddListKembali()">Kembali</button>
                    <button type="button" class="btn btn-primary btn-action-primary btn-pill-primary"
                        onclick="buttonAddPickInvoice()">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>
