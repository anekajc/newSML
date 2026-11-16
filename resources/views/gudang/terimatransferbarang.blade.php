@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Terima Transfer Barang')

@section('css')
{{-- Layout newmasterTest (sama seperti gudang/permintaantransferbarang) sudah memuat newmaster.css +
     po-table-header.css, dan TIDAK memuat tableMaster2.css — jadi tabel daftar tidak perlu aturan
     penetral. Tampilan tabel daftar diambil dari blok .po-list-page di po-table-header.css (class-nya
     dipasang di div#page1). report-table.css tetap dimuat untuk modal filter (.rt-filter). --}}
<link rel="stylesheet" href="{!! URL::asset('css/report-table.css') !!}?v={{ @filemtime(base_path('public/css/report-table.css')) ?: '1' }}">

{{-- Dimuat ULANG setelah report-table.css supaya .po-* / .rt-* versi po-table-header.css menang
     saat spesifisitas seri. --}}
<link rel="stylesheet"
  href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
<link rel="stylesheet"
  href="{!! URL::asset('css/scrollbar-autohide.css') !!}?v={{ @filemtime(base_path('public/css/scrollbar-autohide.css')) ?: '1' }}">

<style>
  /* Tabel item #page2 (Form Terima: #addTable), #page3 (Koreksi: #koreksiTable), #page4 (Detail Terima:
     #detailKoreksiTable) dan #page5 (Detail Transfer
     Barang: #detailTransferBarangTable, sama seperti #detailTable di accounting/kas/_detail.blade.php) — disamakan dengan tabel .dph-tb di
     accounting/pengajuandphtunai.blade.php (kotak bergaris, header menempel di kotak scroll, baris
     hover). Aturan .dph-* di bawah disalin dari public/css/pengajuandphtunai.css; file itu sendiri
     tidak dimuat di sini karena membawa banyak aturan page-local lain. Padding sel isi memakai
     !important untuk mengalahkan `table tbody td { padding: 0 10px !important }` di newmaster.css. */
  .dph-table-outer {
    border: 1px solid #E3E6F0;
    border-radius: 10px;
    overflow: hidden;
  }

  .dph-table-wrap {
    max-height: 65vh;
    overflow: auto;
    background: #fff;
  }

  .dph-tb {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
  }

  .dph-tb thead th {
    position: sticky;
    top: 0;
    z-index: 1;
    background: #FAFBFF;
    padding: 10px 12px;
    font-size: 12px;
    font-weight: 700;
    color: #6B7280;
    text-transform: uppercase;
    letter-spacing: .05em;
    border-bottom: 2px solid #E3E6F0;
    white-space: nowrap;
    text-align: left;
  }

  .dph-tb thead th.num {
    text-align: right;
  }

  .dph-tb tbody td {
    padding: 8px 12px !important;
    border-bottom: 1px solid #F1F5F9;
    vertical-align: middle;
  }

  .dph-tb tbody tr:hover {
    background: #F8F9FF;
  }

  /* #koreksiTable punya dua baris judul. Baris pengelompokan (Deskripsi Barang / Satuan) dibiarkan
     ikut tergulung; hanya baris judul kolom yang menempel — kalau keduanya sticky di top:0 saling
     menimpa. */
  #koreksiTable thead tr:first-child th {
    position: static;
    text-align: center;
  }

  .sp-badge {
    display: inline-block;
    padding: 2px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .03em;
  }

  .sp-badge.is-active {
    color: #16a34a; background: #e7f7ed;
  }

  .sp-badge.is-inactive {
    color: #b45309; background: #fef3e0;
  }

  /* Tab #page1 (Transfer Belum Terima / Terima Transfer Barang) — disalin dari .tab-toggle di
     public/css/tableMaster2.css (file itu tidak dimuat layout newmasterTest). Bootstrap sendiri
     yang memasang class active lewat data-toggle="tab". */
  .tab-toggle {
    display: inline-flex;
    align-self: flex-start;
    background-color: #f1f3f5;
    border-radius: 20px;
    padding: 3px;
    gap: 2px;
  }

  .tab-toggle-btn {
    display: inline-block;
    border: none;
    background: transparent;
    color: #495057;
    border-radius: 17px;
    padding: 5px 16px;
    font-weight: 600;
    font-size: 0.75rem;
    text-decoration: none;
    cursor: pointer;
    transition: background-color 0.2s, color 0.2s, box-shadow 0.2s;
  }

  .tab-toggle-btn:hover {
    color: #007bff;
    text-decoration: none;
  }

  .tab-toggle-btn.active {
    background-color: #007bff;
    color: #fff;
    box-shadow: 0 2px 6px rgba(0, 123, 255, .35);
  }

  .btn-pill-action {
    height: 30px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    transition: background-color 0.3s, box-shadow 0.3s;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1)
  }

  /* newmaster.css memaku `#formBsGrid .form-control` setinggi 38px, sehingga atribut rows pada
     textarea (alamat Gudang rows=3, Keterangan rows=5/3) tidak berefek dan kotaknya hanya satu
     baris. Textarea dikembalikan ke tinggi aslinya — sama seperti di
     gudang/permintaantransferbarang.blade.php. */
  #formBsGrid textarea.form-control {
    height: auto;
  }

  /* Header form page2/3/4/5: grid 4 kolom supaya baris sejajar.
       baris 1: No Bukti | Gudang Asal | Gudang Tujuan | Keterangan
       baris 2: Tanggal  | Alamat      | Alamat        | (Keterangan memanjang 2 baris)
     Label langsung di atas field (tanpa .form-group) supaya jaraknya rapat. */
  .ttb-header {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    grid-template-areas:
      "nobukti asal       tujuan       ket"
      "tanggal alamatasal alamattujuan ket";
    column-gap: 24px;
    row-gap: 16px;
    align-items: start;
  }
  .ttb-header .ttb-field {
    display: flex;
    flex-direction: column;
    min-width: 0;
  }
  .ttb-header .ttb-field > label {
    margin: 0 0 6px;
  }
  .ttb-f-nobukti      { grid-area: nobukti; }
  .ttb-f-tanggal      { grid-area: tanggal; }
  .ttb-f-asal         { grid-area: asal; }
  .ttb-f-alamatasal   { grid-area: alamatasal; }
  .ttb-f-tujuan       { grid-area: tujuan; }
  .ttb-f-alamattujuan { grid-area: alamattujuan; }
  .ttb-header .ttb-f-ket {
    grid-area: ket;
    align-self: stretch;
  }
  #formBsGrid .ttb-header .ttb-f-ket textarea.form-control {
    flex: 1 1 auto;
  }
  /* Alamat (tanpa label) mengisi tinggi baris 2 yang ditentukan blok Tanggal (label + input), supaya
     atas & bawahnya sejajar dengan Tanggal. Textarea absolute supaya rows=3 tidak ikut memperbesar baris. */
  .ttb-header .ttb-f-alamatasal,
  .ttb-header .ttb-f-alamattujuan {
    align-self: stretch;
    position: relative;
  }
  #formBsGrid .ttb-header .ttb-f-alamatasal textarea.form-control,
  #formBsGrid .ttb-header .ttb-f-alamattujuan textarea.form-control {
    position: absolute;
    inset: 0;
    height: auto;
  }
  @media (max-width: 767.98px) {
    .ttb-header {
      grid-template-columns: minmax(0, 1fr);
      grid-template-areas: "nobukti" "tanggal" "asal" "alamatasal" "tujuan" "alamattujuan" "ket";
    }
    #formBsGrid .ttb-header .ttb-f-alamatasal textarea.form-control,
    #formBsGrid .ttb-header .ttb-f-alamattujuan textarea.form-control {
      position: static;
    }
  }
</style>
@endsection
@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id="page1" class="container-fluid po-list-page mainpage">

  {{-- Kartu + toolbar + tabel mengikuti gudang/permintaantransferbarang.blade.php (skema po-*,
       DataTables). Dua tab: kiri = transfer belum diterima (#tabel, tanpa periode), kanan = terima
       transfer barang (#tabel2, pakai rentang tanggal) — pola dua tabel/ReportTable mengikuti
       accounting/bonsementara.blade.php, markup tab mengikuti accounting/cetakpengajuandph.blade.php. --}}
  <div id="contentContainer" class="container-fluid">
    <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
    <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />

    <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
    <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS!!}" />
    <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
    <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />
    <input type="hidden" id="akses_isotorisasi1" value="{!! $akses->IsOtorisasi1 !!}" />
    <input type="hidden" id="akses_isbatal" value="{!! $akses->IsBatal !!}" />

    <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

    <div class="tab-toggle nav" id="nav-tab" role="tablist" style="margin-bottom: 10px">
      <a class="tab-toggle-btn active" id="nav-belum-tab" data-toggle="tab" href="#tabBelum" role="tab"
        aria-controls="tabBelum" aria-selected="true">Transfer Belum Terima</a>
      <a class="tab-toggle-btn" id="nav-terima-tab" data-toggle="tab" href="#tabTerima" role="tab"
        aria-controls="tabTerima" aria-selected="false">Terima Transfer Barang</a>
    </div>

    <div class="tab-content">
      {{-- Tab kiri: semua transfer yang belum diterima — tanpa filter periode. --}}
      <div class="tab-pane fade show active" id="tabBelum" role="tabpanel" aria-labelledby="nav-belum-tab">
        <div class="card">
          <div class="card-body" style="padding:0;">

            <div class="po-toolbar">
              <input class="po-search-inp" type="search" id="searchBox2" placeholder="Cari data">

              {{-- Jumlah baris per halaman. -1 = tampilkan semua data (tanpa pager) — diikat ke
                   DataTables lewat ikatPanjangHalaman(), bukan onchange inline. --}}
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
            </div>

            {{-- Bar kolom tersembunyi — diisi ReportTable.init() (lihat initReportTableSekali()). --}}
            <div id="rtBar"></div>

            <table id="tabel" class="data-table po-aksi-hover">
              <thead id="tabel_header">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Action</th>
                </tr>
              </thead>
              <tbody id="tabel_data" class="text-left"></tbody>
            </table>

            <div class="po-rt-hint">
              <i class="bi bi-info-circle"></i>
              Seret judul kolom untuk mengurutkan. Klik <i class="bi bi-gear"></i> pada judul kolom untuk
              sembunyikan kolom.
            </div>

          </div>
        </div>
      </div>

      {{-- Tab kanan: transfer yang sudah diterima, difilter rentang tanggal. --}}
      <div class="tab-pane fade" id="tabTerima" role="tabpanel" aria-labelledby="nav-terima-tab">
        <div class="card">
          <div class="card-body" style="padding:0;">

            <div class="po-toolbar">
              <div class="po-filter-wrap">
                <label>Periode</label>
                <input type="date" class="po-filter-inp" id="inputDate1" value="{!! $date1 !!}" autocomplete="off">
                <span class="po-filter-sep">s/d</span>
                <input type="date" class="po-filter-inp" id="inputDate2" value="{!! $date2 !!}" autocomplete="off">
              </div>

              <input class="po-search-inp" type="search" id="searchBoxTerima" placeholder="Cari data">

              <div class="po-len-wrap">
                <label for="tampilLenTerima">Tampilkan</label>
                <select class="po-len-inp" id="tampilLenTerima">
                  <option value="10" selected>10</option>
                  <option value="25">25</option>
                  <option value="50">50</option>
                  <option value="100">100</option>
                  <option value="-1">Semua</option>
                </select>
              </div>
            </div>

            <div id="rtBar2"></div>

            <table id="tabel2" class="data-table po-aksi-hover">
              <thead id="tabel2_header">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Action</th>
                </tr>
              </thead>
              <tbody id="tabel2_data" class="text-left"></tbody>
            </table>

            <div class="po-rt-hint">
              <i class="bi bi-info-circle"></i>
              Seret judul kolom untuk mengurutkan. Klik <i class="bi bi-gear"></i> pada judul kolom untuk
              sembunyikan kolom.
            </div>

          </div>
        </div>
      </div>
    </div>

  </div>

</div>

<div id="printContainer" style="display:none">
</div>


<div id="page2" style="display: none" class="mainpage container-fluid po-form-page">

  <div class="row">
    <div class="col-8 text-left">
      <h2></h2>
    </div>
    <div class="col-4 text-right" id="contentContainer">
      <button type="button" class="btn btn-danger btn-pill-primary btn-action-danger" onclick="buttonCloseForm()">Close</button>
    </div>
  </div>

  <div class="container-fluid">
    <input type="hidden" name="noUrut" id="input_add_nourut" value="" />
    <div class="modal-body" id="formBsGrid">
      {{-- Header: grid 4 kolom (lihat .ttb-header) — sama di page2/3/4/5. Gudang read-only (ditentukan
           oleh transfer yang diterima, diisi di buttonAdd() dari getDetail); Keterangan disimpan ke
           DBTRANSFER.NOTE dokumen terima lewat spAdd. --}}
      <div class="ttb-header">
        <div class="ttb-field ttb-f-nobukti">
          <label>No Bukti</label>
          <input type="text" class="form-control text-left" id="input_add_nobukti" placeholder="" disabled>
        </div>
        <div class="ttb-field ttb-f-tanggal">
          <label>Tanggal</label>
          <input type="date" class="form-control text-left" id="input_add_tanggal" value="{!! date('Y-m-d') !!}" disabled>
        </div>
        <div class="ttb-field ttb-f-asal">
          <label>Gudang Asal</label>
          <input type="text" class="form-control text-left" id="input_add_gudangAsal" placeholder="" disabled>
        </div>
        <div class="ttb-field ttb-f-alamatasal">
          <textarea style="width: 100%; resize: none;" rows=3 placeholder="Alamat Gudang Asal"
            class="form-control text-left" id="input_add_alamatGudangAsal" disabled></textarea>
        </div>
        <div class="ttb-field ttb-f-tujuan">
          <label>Gudang Tujuan</label>
          <input type="text" class="form-control text-left" id="input_add_gudangTujuan" placeholder="" disabled>
        </div>
        <div class="ttb-field ttb-f-alamattujuan">
          <textarea style="width: 100%; resize: none;" rows=3 placeholder="Alamat Gudang Tujuan"
            class="form-control text-left" id="input_add_alamatGudangTujuan" disabled></textarea>
        </div>
        <div class="ttb-field ttb-f-ket">
          <label>Keterangan</label>
          <textarea style="width: 100%; resize: none;" rows=3 placeholder="Keterangan"
            class="form-control" id="input_add_keterangan"></textarea>
        </div>
      </div>
    </div>

    <div class="container-fluid mt-4" style="padding:0; margin:0;" id="formBsGrid">
      <div class="dph-table-outer">
        <div class="dph-table-wrap">
          <table id="addTable" class="dph-tb">
            <thead id="addTable_header">
              <tr>
                <th scope="col" class="text-center">Terima</th>
                <th scope="col">Kode Barang</th>
                <th scope="col">No Bukti</th>
                <th scope="col">Nama Barang</th>
                <th scope="col">Satuan</th>
                <th scope="col" class="num">Qty Transfer</th>
                <th scope="col" class="text-center">Qty Terima</th>
              </tr>
            </thead>
            <tbody id="addTableData">
              <tr>
                <td colspan=7 class="text-center">Belum ada data</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="row mt-2" style="margin-top: 0">
      <div class="col-md-12 text-right mt-4" id="contentContainer">
        <button id="buttonSubmitAdd" type="button" onclick="submitAdd()" class="btn btn-action-primary btn-primary btn-pill-primary">Simpan</button>
      </div>
    </div>
  </div>
</div>


<div id="page3" style="display: none" class="mainpage container-fluid po-form-page">
  <div class="row" >
    <div class="col-8 text-left">
      <h2></h2>
    </div>
    <div class="col-4 text-right" id="contentContainer">
      <button type="button" class="btn btn-danger btn-pill-primary btn-action-danger" onclick="buttonCloseForm()">Close</button>
    </div>
  </div>

  <div class="modal-body" id="formBsGrid">
    {{-- <input type="hidden" name="noUrut" id="input_koreksi_nourut" value="" /> --}}
    {{-- Header: grid 4 kolom (lihat .ttb-header) — sama di page2/3/4/5. Keterangan editable,
         disimpan lewat onChangeHeader saat blur. --}}
    <div class="ttb-header">
      <input type="hidden" class="form-control" id="input_koreksi_nourut" placeholder="No Urut" disabled>
      <div class="ttb-field ttb-f-nobukti">
        <label>No Bukti</label>
        <input type="text" class="form-control text-left" id="input_koreksi_nobukti" placeholder="" disabled>
      </div>
      <div class="ttb-field ttb-f-tanggal">
        <label>Tanggal</label>
        <input type="date" class="form-control text-left" id="input_koreksi_tanggal" value="{!! date('Y-m-d') !!}" disabled>
      </div>
      <div class="ttb-field ttb-f-asal">
        <label>Gudang Asal</label>
        <input type="text" class="form-control text-left" id="input_gudangasal_nama" placeholder="" disabled>
      </div>
      <div class="ttb-field ttb-f-alamatasal">
        <textarea style="width: 100%; resize: none;" rows=3 placeholder="Alamat Gudang Asal"
          class="form-control text-left" id="input_koreksi_alamatGudangAsal" disabled></textarea>
      </div>
      <div class="ttb-field ttb-f-tujuan">
        <label>Gudang Tujuan</label>
        <input type="text" class="form-control text-left" id="input_gudangtujuan_nama" placeholder="" disabled>
      </div>
      <div class="ttb-field ttb-f-alamattujuan">
        <textarea style="width: 100%; resize: none;" rows=3 placeholder="Alamat Gudang Tujuan"
          class="form-control text-left" id="input_koreksi_alamatGudangTujuan" disabled></textarea>
      </div>
      <div class="ttb-field ttb-f-ket">
        <label>Keterangan</label>
        <textarea style="width: 100%; resize: none;" rows=3 placeholder="Keterangan"
          class="form-control" id="input_koreksi_keterangan" onblur="onChangeHeader('NOTE' , 'input_koreksi_keterangan')"></textarea>
      </div>
    </div>

        <div class="container-fluid mt-4" style="padding:0; margin:0;">
          <div class="dph-table-outer">
            <div class="dph-table-wrap">
              <table id="koreksiTable" class="dph-tb">
                <thead>
                  <tr>
                    <th scope="col" class="text-left">Kode Barang</th>
                    <th scope="col" class="text-left">Nama Barang</th>
                    <th scope="col" class="num">Qty</th>
                    <th scope="col" class="text-center">Sat</th>
                    <th scope="col" class="text-center">Actions</th>
                  </tr>
                </thead>
                <tbody id="koreksiTableData">
                  <tr>
                    <td colspan=5 class="text-center">Belum ada data</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
    <div id="formKoreksiEdit" class="container-fluid showhideitem mt-4">
    <div class="row mb-3">
      <div class="col-4">
        <h4 id="h4KoreksiEditItem" style="margin-left:-15px;">Edit Item</h4>
      </div>
    </div>

    {{-- Jarak antar baris (mb-3) + label sejajar tengah dengan input (align-items-center, label mb-0). --}}
    <div class="row" id="formBsGrid">
      <div class="col-md-6">
        <div class="row mb-3 align-items-center">
          <div class="col-md-3" >
            <label class="mb-0">Kode Barang</label>
          </div>
          <div class="col-md-4">
            <input id="KoreksiEditKodeBrg" type="text" class="form-control text-center" disabled>
          </div>
        </div>

        <div class="row mb-3 align-items-center">
          <div class="col-md-3" >
            <label class="mb-0">Nama Barang</label>
          </div>
          <div class="col-md-4">
            <input id="KoreksiEditNamaBrg" type="text" class="form-control text-center" disabled>
          </div>
        </div>

        <div class="row mb-3 align-items-center">
          <div class="col-md-3" >
            <label class="mb-0">Quantity</label>
          </div>
          <div class="col-md-3">
            <input id="KoreksiEditInputQty" type="text" class="form-control text-right" value="0.00" oninput="formatAngkaKetik(this)" onblur="formatAngkaInput(this)">
          </div>
          <div class="col-md-2" >
            <label class="mb-0">Satuan</label>
          </div>
          <div class="col-md-3">
            <input id="KoreksiEditInputSat" type="text" class="form-control text-center" disabled>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-3">
      <div class="col-md-12 text-right" id="contentContainer">
        <button type="button" class="btn btn-action-danger btn-danger btn-pill-primary" onclick="buttonKoreksiItemBatal()">Batal</button>

        <button id="buttonSubmitKoreksiEdit" type="button" onclick="submitKoreksiEdit()" class="btn btn-primary btn-action-primary btn-pill-primary">Simpan</button>
      </div>
    </div>
  </div>

  </div>
</div>
{{-- Start Modal List gudang asal --}}
  <div class="modal fade" id="modalAddListGudangAsal" role="dialog" aria-labelledby="labelGudangAsal" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="labelGudangAsal">Pilih Gudang Asal</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="container-fluid px-3 mt-4">
            <div class="row">
              <div class="table-responsive">
                <table id="tabel_add_list_gudangasal" class="table table-bordered table-striped">
                  <thead class="text-center bg-primary text-white">
                    <tr>
                      <th>Kode</th>
                      <th>Nama Gudang</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="tabel_data_add_list_gudangasal" class="text-left">
                    <tr>
                      <td>-</td>
                      <td>-</td>
                      <td class="text-center">
                        <button class="btn btn-primary btn-sm" type="button"><i class="bi bi-plus"></i></button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
              <button type="button" class="btn btn-danger btn-lg"
                style="height: 30px; padding: 4px 12px; border-radius: 20px;
                font-size: 0.75rem; font-weight: 600; text-transform: uppercase;"
                onclick="buttonAddListBatal()">Batal</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{{-- End Modal List gudang asal --}}

{{-- Start Modal List gudang tujuan --}}
  <div class="modal fade" id="modalAddListGudangTujuan" role="dialog" aria-labelledby="labelGudangTujuan" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="labelGudangTujuan">Pilih Gudang Tujuan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="container-fluid px-3 mt-4">
            <div class="row">
              <div class="table-responsive">
                <table id="tabel_add_list_gudangtujuan" class="table table-bordered table-striped">
                  <thead class="text-center bg-primary text-white">
                    <tr>
                      <th>Kode</th>
                      <th>Nama Gudang</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="tabel_data_add_list_gudangtujuan" class="text-left">
                    <tr>
                      <td>-</td>
                      <td>-</td>
                      <td class="text-center">
                        <button class="btn btn-primary btn-sm" type="button"><i class="bi bi-plus"></i></button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
              <button type="button" class="btn btn-danger btn-lg"
                style="height: 30px; padding: 4px 12px; border-radius: 20px;
                font-size: 0.75rem; font-weight: 600; text-transform: uppercase;"
                onclick="buttonAddListBatal()">Batal</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{{-- End Modal List gudang tujuan --}}

<div id="page4" style="display: none" class="mainpage container-fluid po-form-page">
  <div class="row">
    <div class="col-8 text-left">
      <h2></h2>
    </div>
    <div class="col-4 text-right" id="contentContainer">
      <button type="button" class="btn btn-danger btn-pill-primary btn-action-danger" onclick="buttonCloseForm()">Close</button>
    </div>
  </div>

  <div class="modal-body" id="formBsGrid">
    {{-- Header: grid 4 kolom (lihat .ttb-header) — sama di page2/3/4/5. Semua read-only. --}}
    <div class="ttb-header">
      <input type="hidden" class="form-control" id="input_detailkoreksi_nourut" placeholder="No Urut" disabled>
      <div class="ttb-field ttb-f-nobukti">
        <label>No Bukti</label>
        <input type="text" class="form-control text-left" id="input_detailkoreksi_nobukti" placeholder="" disabled>
      </div>
      <div class="ttb-field ttb-f-tanggal">
        <label>Tanggal</label>
        <input type="date" class="form-control text-left" id="input_detailkoreksi_tanggal" value="{!! date('Y-m-d') !!}" disabled>
      </div>
      <div class="ttb-field ttb-f-asal">
        <label>Gudang Asal</label>
        <input type="text" class="form-control text-left" id="input_detailkoreksi_gudangasalnama" placeholder="" disabled>
        <input type="hidden" id="input_detailkoreksi_gudangasal">
      </div>
      <div class="ttb-field ttb-f-alamatasal">
        <textarea style="width: 100%; resize: none;" rows=3 placeholder="Alamat Gudang Asal"
          class="form-control text-left" id="input_detailkoreksi_alamatGudangAsal" disabled></textarea>
      </div>
      <div class="ttb-field ttb-f-tujuan">
        <label>Gudang Tujuan</label>
        <input type="text" class="form-control text-left" id="input_detailkoreksi_gudangtujuannama" placeholder="" disabled>
        <input type="hidden" id="input_detailkoreksi_gudangtujuan">
      </div>
      <div class="ttb-field ttb-f-alamattujuan">
        <textarea style="width: 100%; resize: none;" rows=3 placeholder="Alamat Gudang Tujuan"
          class="form-control text-left" id="input_detailkoreksi_alamatGudangTujuan" disabled></textarea>
      </div>
      <div class="ttb-field ttb-f-ket">
        <label>Keterangan</label>
        <textarea style="width: 100%; resize: none;" rows=3 placeholder="Keterangan"
          class="form-control" id="input_detailkoreksi_keterangan" disabled></textarea>
      </div>
    </div>
        <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">
          <div class="dph-table-outer">
            <div class="dph-table-wrap">
              <table id="detailKoreksiTable" class="dph-tb">
                <thead id="detailKoreksiTable_header">
                  <tr>
                    <th scope="col">Kode Barang</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col" class="num">Qty</th>
                    <th scope="col">Sat</th>
                  </tr>
                </thead>
                <tbody id="detailKoreksiTableData">
                  <tr>
                    <td colspan=4 class="text-center">Belum ada data</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
  </div>
</div>

<div id="page5" style="display: none" class="mainpage container-fluid po-form-page">
  <div class="row">
    <div class="col-8 text-left">

    </div>
    <div class="col-4 text-right" id="contentContainer">
      <button type="button" class="btn btn-danger btn-pill-primary btn-action-danger" onclick="buttonCloseForm()">Close</button>
    </div>
  </div>

  <div class="modal-body" id="formBsGrid">
    {{-- Header: grid 4 kolom (lihat .ttb-header) — sama di page2/3/4/5. Semua read-only. --}}
    <div class="ttb-header">
      <input type="hidden" class="form-control" id="input_detailkoreksi_nourut" placeholder="No Urut" disabled>
      <div class="ttb-field ttb-f-nobukti">
        <label>No Bukti</label>
        <input type="text" class="form-control text-left" id="input_detail_nobukti" placeholder="" disabled>
      </div>
      <div class="ttb-field ttb-f-tanggal">
        <label>Tanggal</label>
        <input type="date" class="form-control text-left" id="input_detail_tanggal" disabled>
      </div>
      <div class="ttb-field ttb-f-asal">
        <label>Gudang Asal</label>
        <input type="text" class="form-control text-left" id="input_detail_gudangasalnama" placeholder="" disabled>
        <input type="hidden" id="input_detail_gudangasal">
      </div>
      <div class="ttb-field ttb-f-alamatasal">
        <textarea style="width: 100%; resize: none;" rows=3 placeholder="Alamat Gudang Asal"
          class="form-control text-left" id="input_detail_alamatGudangAsal" disabled></textarea>
      </div>
      <div class="ttb-field ttb-f-tujuan">
        <label>Gudang Tujuan</label>
        <input type="text" class="form-control text-left" id="input_detail_gudangtujuannama" placeholder="" disabled>
        <input type="hidden" id="input_detail_gudangtujuan">
      </div>
      <div class="ttb-field ttb-f-alamattujuan">
        <textarea style="width: 100%; resize: none;" rows=3 placeholder="Alamat Gudang Tujuan"
          class="form-control text-left" id="input_detail_alamatGudangTujuan" disabled></textarea>
      </div>
      <div class="ttb-field ttb-f-ket">
        <label>Keterangan</label>
        <textarea style="width: 100%; resize: none;" rows=3 placeholder="Keterangan"
          class="form-control" id="input_detail_keterangan" disabled></textarea>
      </div>
    </div>
        <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">
          <div class="dph-table-outer">
            <div class="dph-table-wrap">
              <table id="detailTransferBarangTable" class="dph-tb">
                <thead id="detailTransferBarangTable_header">
                  <tr>
                    <th scope="col">Kode Barang</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col">Sat</th>
                    <th scope="col" class="num">Qty</th>
                  </tr>
                </thead>
                <tbody id="detailTransferBarangTableData">
                  <tr>
                    <td colspan=4 class="text-center">Belum ada data</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
  </div>
</div>




@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script type="text/javascript">



let dataTableAdd = []
let dataTableKoreksi = []
let barangKoreksiEdit = {}

// Dua dataset terpisah, satu per tab. Paint pertama tanpa AJAX; loadBelum()/loadTerima() menyegarkan.
let lastRowsBelum = @json($listBelumTerima); // tab kiri  (#tabel)
let lastRowsTerima = @json($listSdhTerima);  // tab kanan (#tabel2)
// jumlah baris per halaman (pageLength DataTables) per tabel; -1 = semua
let pageSizes = { tabel: 10, tabel2: 10 };

// Batas karakter kolom Keterangan di tabel daftar. Lebih dari ini dipotong dan diakhiri '...'
// (teks lengkap tetap ada di tooltip dan ikut dicari). Ubah angka ini untuk mengatur batasnya.
const KETERANGAN_MAX_CHARS = 50;

function potongKeterangan(teks) {
  let str = String(nullToEmpty(teks));
  return (str.length > KETERANGAN_MAX_CHARS) ? str.slice(0, KETERANGAN_MAX_CHARS) + '...' : str;
}

function escapeAttr(teks) {
  return String(teks).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
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

if (typeof nullToEmpty !== 'function') {
  window.nullToEmpty = function(v) {
    return (v === null || v === undefined) ? '' : v;
  };
}

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

var g_href = 'terimatransferbarang';
var g_modeReport = '2';
var gcart_header = [];
var gsum_issubtotal = 0;
var gsum_isgrandtotal = 0;
var gct_desimal_max = 4;

// Tiap tab menyimpan susunan kolomnya sendiri (g_href sama, mode beda):
// '2' = tab kiri (Transfer Belum Terima), '3' = tab kanan (Terima Transfer Barang).
const MODE_BELUM = '2';
const MODE_TERIMA = '3';
let cartBelum = [];
let cartTerima = [];

function setDefaultHeader() {
  // [ field, label, visible, type, total, decimals ]
  gcart_header = [
    ['NOBUKTI', 'No. Bukti', 1, 'varchar', 0, 0],
    ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
    ['NamagdgAsal', 'Gudang Asal', 1, 'varchar', 0, 0],
    ['NamagdgTujuan', 'Gudang Tujuan', 1, 'varchar', 0, 0],
    ['NOTE', 'Keterangan', 1, 'varchar', 0, 0]
  ];
}

// Muat susunan kolom satu mode. Kolom Status (IsTerima) sudah tidak dipakai sejak daftar dipecah
// jadi dua tab — buang dari susunan yang terlanjur tersimpan, lalu simpan ulang.
function muatKolom(mode) {
  g_modeReport = mode;
  doSetHeader(mode);

  let adaStatus = gcart_header.some(c => c[0] === 'IsTerima');
  if (adaStatus) {
    gcart_header = gcart_header.filter(c => c[0] !== 'IsTerima');
    doSimpanHeader(g_href, mode, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
  }
  return gcart_header;
}

/* ReportTable membaca susunan kolom dari window.gcart_header, dan doSimpanHeader() dkk menyimpan
   lewat g_modeReport — keduanya ditukar setiap kali tabel yang dipegang berganti (onActivate di
   ReportTable.init() + dipanggil di awal tiap render), sama seperti accounting/bonsementara.blade.php. */
function pakaiKolomBelum() {
  g_modeReport = MODE_BELUM;
  gcart_header = cartBelum;
}

function pakaiKolomTerima() {
  g_modeReport = MODE_TERIMA;
  gcart_header = cartTerima;
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

// Markup + pemetaan warna/ikon tombol mengikuti aksiButtonsHtml() di gudang/ubahkemasanbarang.blade.php
// (.po-aksi-wrap, cuma title, tanpa tooltip Bootstrap). Fungsi yang dipanggil tetap milik halaman ini.

// Tab kiri (belum diterima): Detail + Terima.
function aksiButtonsBelum(r) {
  const nobukti = pickCI(r, 'NOBUKTI');
  return '<div class="po-aksi-wrap">' +
    '<button type="button" class="btn btn-warning btn-sm" title="Detail" onclick="buttonDetailTransferBarang(\'' +
    nobukti + '\')"><i class="bi bi-info"></i></button>' +
    '<button type="button" class="btn btn-primary btn-sm" title="Terima" onclick="buttonAdd(\'' +
    nobukti + '\')"><i class="bi bi-plus"></i></button>' +
    '</div>';
}

// Tab kanan (sudah diterima): Detail + Edit (koreksi) + Hapus. Hapus hanya dari sini — halaman
// koreksi (#page3) tidak lagi punya tombol hapus per item.
function aksiButtonsTerima(r) {
  const nobukti = pickCI(r, 'NOBUKTI');
  return '<div class="po-aksi-wrap">' +
    '<button type="button" class="btn btn-warning btn-sm" title="Detail" onclick="buttonDetailKoreksi(\'' +
    nobukti + '\')"><i class="bi bi-info"></i></button>' +
    '<button type="button" class="btn btn-success btn-sm" title="Edit" onclick="buttonKoreksi(\'' +
    nobukti + '\')"><i class="bi bi-pen"></i></button>' +
    '<button type="button" class="btn btn-danger btn-sm" title="Hapus" onclick="buttonHapus(\'' +
    nobukti + '\')"><i class="bi bi-trash"></i></button>' +
    '</div>';
}

/* Bar kolom tersembunyi harus berada tepat di atas tabelnya. DataTables membungkus tabel dengan
   #<id>_wrapper saat init, jadi acuannya ikut berpindah — sama seperti rtPindahBar() di
   gudang/permintaantransferbarang.blade.php. */
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

// Kotak cari, dropdown "Tampilkan", dan periode diikat lewat JS (bukan atribut inline) dengan
// penjaga dataset.rtBound supaya tidak terpasang dobel tiap render.
function ikatSearch(idInput, idTabel) {
  let input = document.getElementById(idInput);
  if (!input || input.dataset.rtBound) {
    return;
  }
  input.dataset.rtBound = '1';

  input.addEventListener('input', function() {
    $('#' + idTabel).DataTable().search(input.value).draw();
  });
}

function ikatPanjangHalaman(idSelect, idTabel) {
  let sel = document.getElementById(idSelect);
  if (!sel || sel.dataset.rtBound) {
    return;
  }
  sel.dataset.rtBound = '1';
  sel.value = String(pageSizes[idTabel]);

  sel.addEventListener('change', function() {
    let n = Number(sel.value);
    pageSizes[idTabel] = (n === -1 || n > 0) ? n : 10;
    $('#' + idTabel).DataTable().page.len(pageSizes[idTabel]).draw();
  });
}

// Periode hanya untuk tab kanan. Rentang yang belum lengkap tidak memicu request, dan urutan
// tanggal terbalik ditolak dengan peringatan — sama seperti ikatPeriode() di
// gudang/permintaantransferbarang.blade.php.
function ikatPeriode() {
  let awal = document.getElementById('inputDate1');
  let akhir = document.getElementById('inputDate2');
  if (!awal || !akhir || awal.dataset.rtBound) {
    return;
  }
  awal.dataset.rtBound = '1';

  // Refresh biasa (F5) membuat browser mengembalikan tanggal terakhir yang dipilih tanpa
  // memicu change, padahal data awal dirender server untuk rentang bawaan (atribut value) —
  // kembalikan ke nilai bawaan supaya filter dan isi tabel selalu cocok.
  awal.value = awal.defaultValue;
  akhir.value = akhir.defaultValue;

  let onUbah = function() {
    if (!awal.value || !akhir.value) {
      return;
    }
    if (awal.value > akhir.value) {
      alertify.warning('Tanggal awal tidak boleh melebihi tanggal akhir');
      return;
    }
    loadTerima();
  };
  awal.addEventListener('change', onUbah);
  akhir.addEventListener('change', onUbah);
}

// Tinggi tabel mengikuti sisa ruang layar. Berhenti diam-diam kalau tabelnya sedang tidak tampil
// (#page1 disembunyikan atau tab-nya tidak aktif); dihitung ulang saat tab dibuka (shown.bs.tab)
// atau #page1 ditampilkan lagi (buttonCloseForm() -> loadAll() -> render).
function aturTinggiTabel(idTabel) {
  let page = document.getElementById('page1');
  let area = document.getElementById('content');
  let wrap = document.querySelector('#' + idTabel + '_wrapper .po-table-wrap');
  if (!page || !area || !wrap || wrap.offsetParent === null) {
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

// Render satu tabel daftar. opt: { tabel, bar, search, len, rows, aksi, pakaiKolom }
function renderTabelDaftar(opt) {
  if (typeof ReportTable !== 'undefined' && ReportTable.use) {
    ReportTable.use('#' + opt.tabel);
  }
  opt.pakaiKolom();

  const cols = gcart_header.filter(c => c[2] === 1);
  const selTabel = '#' + opt.tabel;

  if ($.fn.DataTable.isDataTable(selTabel)) {
    $(selTabel).DataTable().destroy();
  }

  const thead = document.querySelector(selTabel + ' thead');
  thead.innerHTML = ReportTable.headHtml(cols).replace('<tr>',
    '<tr><th style="padding: 4px 12px;" scope="col">Action</th>');

  let html = '';
  opt.rows.forEach(function(r) {
    html += '<tr>';
    html += '<td class="text-center">' + opt.aksi(r) + '</td>';
    html += cols.map(function(c) {
      const v = pickCI(r, c[0]);
      if (c[3] === 'date') {
        return '<td>' + (v ? doSetFormatDate(v, '/') : '') + '</td>';
      }
      if (c[0] === 'NOTE') {
        // data-search = teks lengkap supaya pencarian DataTables tidak hanya mencocokkan yang terpotong.
        const penuh = String(nullToEmpty(v));
        return '<td data-search="' + escapeAttr(penuh) + '" title="' + escapeAttr(penuh) + '">' +
          potongKeterangan(penuh) + '</td>';
      }
      return '<td>' + nullToEmpty(v) + '</td>';
    }).join('');
    html += '</tr>';
  });

  document.querySelector(selTabel + ' tbody').innerHTML = html;

  // dom membuat sendiri kotak scroll .po-table-wrap (header sticky) + baris info & pager.
  $(selTabel).DataTable({
    lengthChange: false,
    pageLength: pageSizes[opt.tabel],
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

  rtPindahBar(opt.bar, opt.tabel);
  ikatSearch(opt.search, opt.tabel);
  ikatPanjangHalaman(opt.len, opt.tabel);

  let inputSearch = document.getElementById(opt.search);
  if (inputSearch && inputSearch.value) {
    $(selTabel).DataTable().search(inputSearch.value).draw();
  }

  aturTinggiTabel(opt.tabel);
}

function renderTabelBelum() {
  renderTabelDaftar({
    tabel: 'tabel',
    bar: 'rtBar',
    search: 'searchBox2',
    len: 'tampilLen',
    rows: lastRowsBelum,
    aksi: aksiButtonsBelum,
    pakaiKolom: pakaiKolomBelum
  });
}

function renderTabelTerima() {
  renderTabelDaftar({
    tabel: 'tabel2',
    bar: 'rtBar2',
    search: 'searchBoxTerima',
    len: 'tampilLenTerima',
    rows: lastRowsTerima,
    aksi: aksiButtonsTerima,
    pakaiKolom: pakaiKolomTerima
  });
}

$(document).ready(function(){
  cartBelum = muatKolom(MODE_BELUM);
  cartTerima = muatKolom(MODE_TERIMA);

  ReportTable.init({
    table: '#tabel',
    bar: '#rtBar',
    onChange: renderTabelBelum,
    onActivate: pakaiKolomBelum
  });

  ReportTable.init({
    table: '#tabel2',
    bar: '#rtBar2',
    onChange: renderTabelTerima,
    onActivate: pakaiKolomTerima
  });

  renderTabelBelum();
  renderTabelTerima();
  ikatPeriode();

  // Tabel di tab yang tersembunyi di-init saat lebarnya 0 — rapikan kolom & tinggi saat tab dibuka.
  $('#nav-belum-tab').on('shown.bs.tab', function() {
    $('#tabel').DataTable().columns.adjust();
    aturTinggiTabel('tabel');
  });
  $('#nav-terima-tab').on('shown.bs.tab', function() {
    $('#tabel2').DataTable().columns.adjust();
    aturTinggiTabel('tabel2');
  });
});

function onChangeHeader (field , idvalue) {
  let _token  = $("#_token").val()
  console.log(field, idvalue)
  let onChangeValue  = $(`#${idvalue}`).val()
  let nobukti  = $(`#input_koreksi_nobukti`).val()
  console.log(onChangeValue , nobukti)


  console.log({
    _token : _token,
    field,
    nobukti,
    value: onChangeValue

  })

  $.ajax({
      url: "{!! url('terimatransferbarangonchangeheader') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        field,
        nobukti,
        value: onChangeValue

      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          alertify.warning(`${field} sudah diupdate`)
        }


      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })

}


function buttonKoreksiEditItem (i) {
  let barang = dataTableKoreksi[i];
  barangKoreksiEdit = barang;

  document.getElementById("KoreksiEditKodeBrg").value = barang.KODEBRG;
  document.getElementById("KoreksiEditNamaBrg").value = barang.NAMABRG;

  document.getElementById("KoreksiEditInputQty").value = formatAngka((parseFloat(barang.QNT) || 0).toFixed(2));
  document.getElementById("KoreksiEditInputSat").value = barang.NOSAT == 1 ? barang.SAT_1 : barang.SAT_2;

  $('#formKoreksiEdit').show();
}

function refreshDataTableKoreksi (nobukti) {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('terimatransferbaranggetdetailpenerimaan') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: nobukti
    },
    success: function (res) {
      console.log('res', res);

      if (!res || res.length === 0) {
        buttonCloseForm();
        alertify.warning("Data tidak ditemukan");
        return;
      }

      dataTableKoreksi = res;

      let rowTable = "";

      res.forEach((item, i) => {
        rowTable += `<tr>
          <td>${item.KODEBRG}</td>
          <td>${item.NAMABRG}</td>
          <td class="text-right">${item.QNT ? parseFloat(item.QNT).toLocaleString() : '0.00'}</td>
          <td class="text-center">${item.SAT_1}</td>
          <td class="text-center">
            <div class="action-buttons">
              <button class="btn btn-action-sm btn-action-success" type="button" onclick="buttonKoreksiEditItem(${i})"><i class="bi bi-pen"></i></button>
            </div>
          </td>
        </tr>`;
      });

      document.getElementById("koreksiTableData").innerHTML = rowTable;

      let header = res[0];
      let tanggal = "";
      if (header.TANGGAL) {
        tanggal = header.TANGGAL.split(" ")[0];
      }
      $("#input_koreksi_tanggal").val(tanggal);
      $("#input_koreksi_nobukti").val(header.NOBUKTI);
      $("#input_gudangasal_nama").val(header.NamagdgAsal || '');
      $("#input_gudangtujuan_nama").val(header.NamagdgTujuan || '');
      $("#input_koreksi_alamatGudangAsal").val(header.AlamatGdgAsal || '');
      $("#input_koreksi_alamatGudangTujuan").val(header.AlamatGdgTujuan || '');
      $("#input_koreksi_keterangan").val(header.NOTE || '');

      buttonKoreksiItemBatal();
    },
    error: function (err) {
      console.error(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}



// function buttonOtorisasi (nobukti, isOtorisasi) {
//   let akses = $("#akses_isotorisasi1").val();
//   if (!Number(akses)) {
//     alertify.warning('No access');
//     return;
//   }

//   if (Number(isOtorisasi) > 0) {
//     alertify.warning('Sudah diotorisasi');
//     return;
//   }

//   let _token = $("#_token").val();

//   $.ajax({
//     url: "{!! url('terimatransferbarangspotorisasi') !!}",
//     type: "post",
//     async: false,
//     data: {
//       _token,
//       nobukti,
//       otorisasi: 1
//     },
//     success: function (res) {
//       if (res > 0) {
//         alertify.success('Berhasil otorisasi');
//         loadAll();
//       } else {
//         alertify.warning('Gagal otorisasi');
//       }
//     },
//     error: function (err) {
//       console.log(err);
//       alertify.warning('Terjadi kesalahan. Silakan refresh browser.');
//     }
//   });
// }

// function buttonBatalOtorisasi (nobukti) {
//   let akses = $("#akses_isotorisasi1").val();
//   if (!Number(akses)) {
//     alertify.warning('No access');
//     return;
//   }

//   alertify.confirm('Batal Otorisasi', 'Batalkan otorisasi ' + nobukti + ' ?',
//     function () {
//       let _token = $("#_token").val();

//       $.ajax({
//         url: "{!! url('terimatransferbarangspbatalotorisasi') !!}",
//         type: "post",
//         async: false,
//         data: {
//           _token,
//           nobukti
//         },
//         success: function (res) {
//           alertify.success('Berhasil batal otorisasi');
//           loadAll();
//         },
//         error: function (err) {
//           console.error(err);
//           alertify.warning('Terjadi kesalahan, silakan refresh browser');
//         }
//       });
//     },
//     function () {
//       console.log('Batal otorisasi dibatalkan');
//     }
//   );
// }

function submitKoreksiEdit () {
  let _token = $("#_token").val();
  let barang = barangKoreksiEdit;
  let choice = "U";

  let qntInput = unformatAngka($("#KoreksiEditInputQty").val());

  if (qntInput < 0) {
    alertify.warning("Qty tidak boleh kurang dari 0");
    return;
  }

  let qntAwal = parseFloat(barang.QNT);
  if (qntInput > qntAwal) {
    alertify.warning("Qty tidak boleh lebih besar dari qty awal (" + qntAwal + ")");
    return;
  }

  // Data item
  let nobukti = barang.NOBUKTI;
  let urut = barang.URUT;
  let kodebrg = barang.KODEBRG;
  let namabrg = barang.NAMABRG;

  let kodegdgasal = barang.GdgAsal;
  let kodegdgtujuan = barang.GdgTujuan;

  let nosat = barang.NOSAT;
  let isi = parseFloat(barang.ISI) || 1;

  let sat1 = barang.SAT_1;
  let sat2 = barang.SAT_2;

  // Hitung qnt1 & qnt2 sesuai nosat
  let qnt1 = 0;
  let qnt2 = 0;

  if (nosat == 1) {
    qnt1 = qntInput;
    qnt2 = qntInput * isi;
  } else {
    qnt1 = qntInput / isi;
    qnt2 = qntInput;
  }

  // dari DBTRANSFERDET
  let noTransfer = barang.NoTransfer;
  let urutTransfer = barang.UrutTransfer;

  let keterangan = $("#input_koreksi_keterangan").val();

  $.ajax({
    url: "{!! url('terimatransferbarangspkoreksi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      choice,
      qnt: qntInput,
      qnt1,
      qnt2,
      namabrg,
      nobukti,
      urut,
      nosat,
      isi,
      sat1,
      sat2,
      kodebrg,
      kodegdgasal,
      kodegdgtujuan,
      note: keterangan,
      noTransfer,
      urutTransfer
    },
    success: function (res) {
      if (res == 1) {
        refreshDataTableKoreksi(nobukti);
        loadAll();
        alertify.success('Item telah dikoreksi');
      } else {
        alertify.warning("Koreksi gagal disimpan");
      }
    },
    error: function (err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}


function buttonKoreksiItemBatal () {

  $('.showhideitem').hide();
}


function buttonKoreksi (nobukti) {
  console.log('buttonKoreksi', nobukti);

  let akses = $("#akses_iskoreksi").val();
  $('.showhideitem').hide();

  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('terimatransferbaranggetdetailpenerimaan') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: nobukti
    },
    success: function (res) {
      console.log('res', res);

      if (!res || res.length === 0) {
        alertify.warning("Data tidak ditemukan");
        return;
      }

      const data = res[0];

      if (data.IsOtorisasi1 == 1) {
        alertify.warning("Data sudah diotorisasi");
        return;
      }

      dataTableKoreksi = res;

      // Isi Tabel Item
      let rowTable = "";
      res.forEach((item, i) => {
        rowTable += `<tr>
          <td>${item.KODEBRG}</td>
          <td>${item.NAMABRG}</td>
          <td class="text-right">${parseFloat(item.QNT).toLocaleString()}</td>
          <td class="text-center">${item.SAT_1}</td>
          <td class="text-center">
            <div class="action-buttons">
              <button class="btn btn-action-sm btn-action-success" type="button" onclick="buttonKoreksiEditItem(${i})"><i class="bi bi-pen"></i></button>
            </div>
          </td>
        </tr>`;
      });

      $("#koreksiTableData").html(rowTable);

      // Isi Form Header
      $("#input_koreksi_nobukti").val(data.NOBUKTI);
      $("#input_gudangasal_nama").val(data.NamagdgAsal || '');
      $("#input_gudangtujuan_nama").val(data.NamagdgTujuan || '');
      $("#input_koreksi_alamatGudangAsal").val(data.AlamatGdgAsal || '');
      $("#input_koreksi_alamatGudangTujuan").val(data.AlamatGdgTujuan || '');
      $("#input_koreksi_keterangan").val(data.NOTE || '');

      const tanggal = data.TANGGAL ? data.TANGGAL.split(" ")[0] : "";
      $("#input_koreksi_tanggal").val(tanggal);

      $('.mainpage').hide();
      $('#page3').show();
    },
    error: function (err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}

function buttonDetailKoreksi (nobukti) {
  console.log('buttonDetailKoreksi', nobukti);

  $('.showhideitem').hide();

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('terimatransferbaranggetdetailpenerimaan') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti
    },
    success: function (res) {
      console.log('res', res);

      if (!res || res.length === 0) {
        alertify.warning("Data tidak ditemukan");
        return;
      }

      // Isi Tabel Detail Item
      let rowTable = "";
      res.forEach((item, i) => {
        rowTable += `<tr>
          <td>${item.KODEBRG}</td>
          <td>${item.NAMABRG}</td>
          <td class="text-right">${parseFloat(item.QNT).toLocaleString()}</td>
          <td>${item.SAT_1}</td>
        </tr>`;
      });

      $("#detailKoreksiTableData").html(rowTable);

      // Isi Header Detail
      const data = res[0];

      $("#input_detailkoreksi_nobukti").val(data.NOBUKTI);
      $("#input_detailkoreksi_gudangasal").val(data.GdgAsal || '');
      $("#input_detailkoreksi_gudangtujuan").val(data.GdgTujuan || '');
      $("#input_detailkoreksi_gudangasalnama").val(data.NamagdgAsal || '');
      $("#input_detailkoreksi_gudangtujuannama").val(data.NamagdgTujuan || '');
      $("#input_detailkoreksi_alamatGudangAsal").val(data.AlamatGdgAsal || '');
      $("#input_detailkoreksi_alamatGudangTujuan").val(data.AlamatGdgTujuan || '');
      $("#input_detailkoreksi_keterangan").val(data.NOTE || '');

      const tanggal = data.TANGGAL ? data.TANGGAL.split(" ")[0] : "";
      $("#input_detailkoreksi_tanggal").val(tanggal);

      $('.mainpage').hide();
      $('#page4').show();
    },
    error: function (err) {
      console.error('Error response:', err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}

function buttonDetailTransferBarang (nobukti) {
  console.log('buttonDetailTransferBarang', nobukti);

  $('.showhideitem').hide();

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('terimatransferbaranggetdetailtransferbarang') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti
    },
    success: function (res) {
      console.log('res', res);

      if (!res || res.length === 0) {
        alertify.warning("Data tidak ditemukan");
        return;
      }

      // Isi Tabel Detail Item
      let rowTable = "";
      res.forEach((item, i) => {
        let kodeBrg = item.KODEBRG || '';
        let nobukti = item.NOBUKTI || '';
        let namaBrg = item.NAMABRG || '';
        let satuan = item.Satx || item.SAT_1 || '';
        let qnt = item.Qntx || item.QNT || 0;
        rowTable += `<tr>
          <td>${kodeBrg}</td>
          <td>${namaBrg}</td>
          <td>${satuan}</td>
          <td class="text-right">${qnt}</td>
        </tr>`;
      });

      $("#detailTransferBarangTableData").html(rowTable);

      // Isi Header Detail
      const data = res[0];

      $("#input_detail_nobukti").val(data.NOBUKTI);
      $("#input_detail_gudangasal").val(data.GdgAsal || '');
      $("#input_detail_gudangtujuan").val(data.GdgTujuan || '');
      $("#input_detail_gudangasalnama").val(data.NamagdgAsal || '');
      $("#input_detail_gudangtujuannama").val(data.NamagdgTujuan || '');
      $("#input_detail_alamatGudangAsal").val(data.AlamatGdgAsal || '');
      $("#input_detail_alamatGudangTujuan").val(data.AlamatGdgTujuan || '');
      $("#input_detail_keterangan").val(data.NOTE || '');
      $("#input_detail_tanggal").val(data.TANGGAL ? data.TANGGAL.split(" ")[0] : '');

      $('.mainpage').hide();
      $('#page5').show();
    },
    error: function (err) {
      console.error('Error response:', err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}

// Tab kiri — semua transfer belum diterima, tanpa tanggal.
function loadBelum() {
  $.ajax({
    url: "{!! url('terimatransferbarangloadall') !!}",
    type: "get",
    async: false,
    success: function(res) {
      lastRowsBelum = res.listBelumTerima || [];
    }
  });

  renderTabelBelum();
}

// Tab kanan — transfer sudah diterima dalam rentang #inputDate1 s/d #inputDate2.
function loadTerima() {
  $.ajax({
    url: "{!! url('terimatransferbarangloadterima') !!}",
    type: "get",
    async: false,
    data: {
      date1: $('#inputDate1').val(),
      date2: $('#inputDate2').val()
    },
    success: function(res) {
      lastRowsTerima = res.listSdhTerima || [];
    }
  });

  renderTabelTerima();
}

// Terima/koreksi/hapus memindahkan transfer antar tab, jadi kedua daftar selalu disegarkan.
function loadAll() {
  loadBelum();
  loadTerima();
}

// Hapus seluruh item terima transfer untuk satu NOBUKTI (SP_TRANSFER 'D' per item, dijalankan
// server dalam satu transaksi — lihat TerimaTransferBarangController::spDelete).
function buttonHapus(nobukti) {
  let akses = $("#akses_ishapus").val();
  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  var dlgHapusItem = alertify.confirm('Hapus', 'Apakah yakin ingin menghapus terima transfer ' + nobukti + ' ?',
    function () {
      $.ajax({
        url: "{!! url('terimatransferbarangspdelete') !!}",
        type: "post",
        data: {
          _token: $("#_token").val(),
          nobukti
        },
        success: function (res) {
          if (res.success) {
            alertify.success(res.message || 'Data telah dihapus');
            loadAll();
          } else {
            alertify.warning(res.message || 'Gagal menghapus data');
          }
        },
        error: function (xhr) {
          console.error(xhr.responseText);
          let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message :
            'Terjadi kesalahan, silakan refresh browser';
          alertify.error(msg);
        }
      });
    },
    function () {}
  );
  dlgHapusItem.elements.root.classList.add('ajs-app-buttons', 'is-danger');
}

function buttonAdd (nobukti) {
  console.log('buttonAdd', nobukti);

  let akses = $("#akses_istambah").val();
  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  setNewNoBukti();

  let _token = $("#_token").val();
  let tanggal = $("#input_add_tanggal").val();
  if (!tanggal) {
    alertify.warning("Tanggal harus diisi.");
    return;
  }

  $.ajax({
    url: "{!! url('terimatransferbaranggetdetail') !!}",
    type: "POST",
    async: false,
    data: {
      _token,
      nobukti,
      tanggal
    },
    success: function (res) {
      console.log('res', res);

      if (!Array.isArray(res) || res.length === 0) {
        alertify.warning("Data tidak ditemukan");
        return;
      }

      dataTableAdd = res;

      // Header: gudang asal/tujuan sama untuk semua item satu transfer — ambil dari baris pertama.
      $("#input_add_gudangAsal").val(res[0].NamagdgAsal || '');
      $("#input_add_alamatGudangAsal").val(res[0].AlamatGdgAsal || '');
      $("#input_add_gudangTujuan").val(res[0].NamagdgTujuan || '');
      $("#input_add_alamatGudangTujuan").val(res[0].AlamatGdgTujuan || '');
      $("#input_add_keterangan").val('');

      let rowTable = "";

      res.forEach((item, i) => {
        let kodeBrg   = item.KODEBRG ?? '';
        let namaBrg   = item.NAMABRG ?? '';
        let nobukti   = item.NOBUKTI ?? '';
        let satuan    = item.SAT_1;
        let qnt       = item.Qntx !== undefined ? parseFloat(item.Qntx).toFixed(2) : '0.00';

        rowTable += `
          <tr>
            <td class="text-center">
              <input type="checkbox" class="add_checkbox" data-index="${i}" style="transform: scale(1.5); margin: 5px;">
            </td>
            <td>${kodeBrg}</td>
            <td>${nobukti}</td>
            <td>${namaBrg}</td>
            <td>${satuan}</td>
            <td class="text-right">${qnt}</td>
            <td class="text-center">
              <input
                class="input_add_qnt  text-right" data-index="${i}" type="text" value="" placeholder="0" oninput="formatAngkaKetik(this)" onblur="formatAngkaInput(this)">
            </td>
          </tr>`;
      });

      document.getElementById("addTableData").innerHTML = rowTable;

      $('.mainpage').hide();
      $('#page2').show();
    },
    error: function (err) {
      console.error("AJAX Error", err);
      alertify.warning('Terjadi kesalahan. Silakan refresh browser.');
    }
  });
}

function submitAdd () {
  const checkDate = new Date($("#input_add_tanggal").val());
  const periode_bulan = parseInt(document.getElementById("periode_bulan").value);
  const periode_tahun = parseInt(document.getElementById("periode_tahun").value);

  if (checkDate.getFullYear() !== periode_tahun || (checkDate.getMonth() + 1) !== periode_bulan) {
    alertify.warning("Tanggal tidak sesuai periode");
    return;
  }

  const _token = $("#_token").val();
  const tempData = [];
  let checkInvalid = false;

  const nobukti = $("#input_add_nobukti").val();
  const nourut = $("#input_add_nourut").val();
  const tanggal = $("#input_add_tanggal").val();
  const note = $("#input_add_keterangan").val();

  $(".add_checkbox:checked").each(function() {
    const i = $(this).data("index");
    const item = dataTableAdd[i];
    const inputQty = unformatAngka($(`.input_add_qnt[data-index='${i}']`).val());
    const maxQty   = parseFloat(item.Qntx || 0);

    if (inputQty < 0) {
      alertify.warning(`Qty tidak boleh negatif (row ${i+1})`);
      checkInvalid = true;
      return false;
    }

    if (inputQty > maxQty) {
      alertify.warning(`Qty tidak boleh lebih dari Qty awal (${maxQty})`);
      checkInvalid = true;
      return false;
    }

    tempData.push({
      NOBUKTI: item.NOBUKTI,
      URUT: item.URUT,
      KODEBRG: item.KODEBRG,
      inputQnt: inputQty,
      checked: true
    });
  });

  if (checkInvalid) {
    return;
  }

  if (!tempData.length) {
    alertify.warning("Tidak ada item yang dipilih");
    return;
  }

  console.log("submitAdd payload", tempData);

  $.ajax({
    url: "{!! url('terimatransferbarangspadd') !!}",
    type: "post",
    data: {
      _token,
      tempData,
      tanggal,
      nobukti,
      nourut,
      note
    },
    success: function(res) {
      console.log('submitAdd response', res);

      if (res.success) {
        alertify.success(res.message);
        loadAll();
        setTimeout(() => {
          $('.mainpage').hide();
          $('#page3').show();
          buttonKoreksi(nobukti, 'edit');
        }, 100);
      } else {
        alertify.error(res.message || 'Gagal menyimpan data');
      }
    },
    error: function(xhr) {
      console.error(xhr.responseText);
      alertify.error("Terjadi error saat simpan data");
    }
  });
}

function setNewNoBukti () {
  $.ajax({
    url: "{!! url('terimatransferbarangspnobukti') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      console.log(res)
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut
    }})
}



function buttonCloseForm () {
  $('.mainpage').hide();
  // $('#page2').hide();
  $('#page1').show();
  loadAll();
}

function formatDate(date , pemisah = '-') {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;

    return [year, month, day].join(pemisah);
}

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

function unformatAngka(angka) {
  if (!angka) return 0
  return parseFloat(String(angka).replace(/,/g, '')) || 0
}

function formatAngkaInput(el) {
  el.value = formatAngka(unformatAngka(el.value).toFixed(2))
}

// Dipasang di oninput supaya separator ribuan langsung muncul sambil mengetik, tidak
// menunggu pindah fokus (onblur formatAngkaInput() tetap jalan untuk menormalkan ke 2
// desimal). Sama seperti formatAngkaKetik() di accounting/memorialkoreksi.blade.php.
function formatAngkaKetik(el) {
  let posDariKanan = el.value.length - el.selectionStart
  let minus = el.value.trim().startsWith('-') ? '-' : ''
  let raw = el.value.replace(/[^0-9.]/g, '')

  let titikIndex = raw.indexOf('.')
  let bulat = titikIndex === -1 ? raw : raw.slice(0, titikIndex)
  let desimal = titikIndex === -1 ? '' : raw.slice(titikIndex + 1).replace(/\./g, '').slice(0, 2)

  bulat = bulat.replace(/^0+(?=\d)/, '')
  if (bulat === '') {
    bulat = '0'
  }

  let bulatFormatted = ''
  for (let i = 0; i < bulat.length; i++) {
    if (i != 0 && (bulat.length - i) % 3 == 0) {
      bulatFormatted += ','
    }
    bulatFormatted += bulat[i]
  }

  el.value = minus + bulatFormatted + (titikIndex !== -1 ? '.' + desimal : '')

  let posBaru = Math.max(0, el.value.length - posDariKanan)
  el.setSelectionRange(posBaru, posBaru)
}

</script>




@endsection
