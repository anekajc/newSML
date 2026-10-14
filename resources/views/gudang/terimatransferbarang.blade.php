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
  #detailKoreksiTable_header th {
    background: #f8f9fb !important;
    color: #6b7280 !important;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .04em;
    font-weight: 600;
    border-bottom: 1px solid #e7e9ee;
    border-top: none;
  }

  #detailKoreksiTable.table-bordered th,
  #detailKoreksiTable.table-bordered td {
    border-color: #e7e9ee !important;
  }

  #detailKoreksiTable tbody tr:nth-of-type(odd) {
    background-color: #fbfbfc;
  }

  #detailKoreksiTable tbody tr:hover {
    background-color: #f5f3ff;
  }

  #detailKoreksiTable tbody td {
    font-size: 12px;
    padding: 6px 12px;
    vertical-align: middle;
  }

  /* Tabel item #page2 (Form Terima: #addTable) dan #page3 (Koreksi: #koreksiTable) — disamakan dengan tabel .dph-tb di
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

  /* Tabel item #page5 (Detail Transfer Barang) — disamakan dengan tabel item #tabelitem di
     gudang/ubahkemasanbarang.blade.php: kartu berbingkai, judul kolom abu huruf besar yang menempel
     (sticky) di kotak scroll, garis baris tipis, dan warna hover. Padding sel isi sengaja tidak
     ditulis: `table tbody td { padding: 0 10px !important }` di newmaster.css berlaku sama seperti
     di tabel daftar #tabel (baris rapat). */
  #page5 .po-item-card {
    background: var(--white, #fff);
    border: 1.5px solid var(--border, #e5e7eb);
    border-radius: var(--radius, 12px);
    overflow: hidden;
  }

  #detailTransferBarangTable {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
  }

  #detailTransferBarangTable thead th {
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

  #detailTransferBarangTable thead th.num {
    text-align: right;
  }

  #detailTransferBarangTable tbody td {
    border-bottom: 1px solid #f3f4f6;
    color: var(--text-main, #1f2937);
    vertical-align: middle;
  }

  #detailTransferBarangTable tbody tr:last-child td {
    border-bottom: none;
  }

  #detailTransferBarangTable tbody tr:hover td {
    background: #f9fafb;
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
</style>
@endsection
@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id="page1" class="container-fluid po-list-page mainpage">

  {{-- Kartu + toolbar + tabel mengikuti gudang/permintaantransferbarang.blade.php (skema po-*,
       DataTables). Status Terima/Belum dibedakan lewat kolom Status + filter di modal Filter. --}}
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

    <div class="card">
      <div class="card-body" style="padding:0;">

        <div class="po-toolbar">
          <div class="po-filter-wrap">
            <label>Periode</label>
            <input type="date" class="po-filter-inp" id="inputDate1" value="{!! $date1 !!}" autocomplete="off">
            <span class="po-filter-sep">s/d</span>
            <input type="date" class="po-filter-inp" id="inputDate2" value="{!! $date2 !!}" autocomplete="off">
          </div>

          <input class="po-search-inp" type="search" id="searchBox2" placeholder="Cari data">

          {{-- Jumlah baris per halaman. -1 = tampilkan semua data (tanpa pager) — diikat ke
               DataTables lewat ikatPanjangHalaman() di renderTabel(), bukan onchange inline. --}}
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
        </div>

        {{-- Bar kolom tersembunyi — diisi ReportTable.init() (lihat $(document).ready). --}}
        <div id="rtBar"></div>

        <table id="tabel" class="data-table po-aksi-hover">
          <thead>
            <tr>
              <th style="padding: 4px 12px;" scope="col">Aksi</th>
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

{{-- modal filter --}}
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
              <label class="rt-field-label" for="modalStatusTerima">Status</label>
              <select class="rt-native" id="modalStatusTerima">
                <option value="2">Semua</option>
                <option value="1">Terima</option>
                <option value="0">Belum</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="rt-reset-link" onclick="resetAllFilters()">Reset semua</button>
        <div class="rt-footer-buttons">
          <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal">Batal</button>
          <button type="button" class="rt-btn rt-btn-primary" onclick="applyModalFilter()">Terapkan</button>
        </div>
      </div>

    </div>
  </div>
</div>
{{-- end modal filter --}}

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
      <div class="row">
        <div class="col-md-12">
          <div class="row">
            <div class="col-md-3">
              <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>No Bukti</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_nobukti" placeholder="" disabled>
                </div>
              </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Tanggal</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="date" class="form-control text-center" id="input_add_tanggal" value="{!! date('Y-m-d') !!}"  >
                </div>
              </div>
            </div>
            </div>
          </div>
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
        <button id="buttonSubmitAdd" type="button" onclick="submitAdd()" class="btn btn-action-primary btn-primary btn-pill-primary">Submit</button>
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
    <div class="row">
        <input type="hidden" class="form-control" id="input_koreksi_nourut" placeholder="No Urut" disabled>
        <!-- Kiri -->
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control text-left" id="input_koreksi_nobukti" placeholder="" disabled>
              </div>
            </div>

            <div class="col-md-4" style="margin-top:-12px;">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-md-8" style="margin-top:-12px;">
              <div class="form-group">
                <input type="date" class="form-control text-left" id="input_koreksi_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>
          </div>
        </div>

        <!-- Tengah -->
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Gudang Asal</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input id="input_gudangasal_nama" type="text" class="form-control text-left" placeholder="" disabled>
              </div>
            </div>

            <div class="col-md-4" style="margin-top:-10px;">
              <div class="form-group">
                <label>Keterangan</label>
              </div>
            </div>
            <div class="col-md-8" style="margin-top:-10px;">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none" rows=3 placeholder="" class="form-control text-left" id="input_koreksi_keterangan"  onblur="onChangeHeader('NOTE' , 'input_koreksi_keterangan')"></textarea>
              </div>
            </div>
          </div>
        </div>
        <!-- Kanan -->
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Gudang Tujuan</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input id="input_gudangtujuan_nama" type="text" class="form-control text-left" placeholder="" disabled>
              </div>
            </div>
          </div>
        </div>
        </div>

        <div class="container-fluid mt-4" style="padding:0; margin:0;">
          <div class="dph-table-outer">
            <div class="dph-table-wrap">
              <table id="koreksiTable" class="dph-tb">
                <thead>
                  <tr>
                    <th colspan="4">Deskripsi Barang</th>
                    <th colspan="2">Satuan</th>
                    <th colspan="1"></th>
                  </tr>
                  <tr>
                    <th scope="col">Kode Barang</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col">Gudang Asal</th>
                    <th scope="col">Gudang Tujuan</th>
                    <th scope="col" class="num">Qty</th>
                    <th scope="col" class="text-center">Sat</th>
                    <th scope="col" class="text-center">Actions</th>
                  </tr>
                </thead>
                <tbody id="koreksiTableData">
                  <tr>
                    <td colspan=7 class="text-center">Belum ada data</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
    <div id="formKoreksiEdit" class="container-fluid showhideitem">
    <div class="row">
      <div class="col-4">
        <h4 id="h4KoreksiEditItem" style="margin-left:-15px;">Edit Item</h4>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="row">
          <div class="col-md-3" style="margin-top:5px;">
            <label>Kode Barang</label>
          </div>
          <div class="col-md-4">
            <input id="KoreksiEditKodeBrg" type="text" class="form-control text-center" disabled>
          </div>
        </div>

        <div class="row">
          <div class="col-md-3" style="margin-top:5px;">
            <label>Nama Barang</label>
          </div>
          <div class="col-md-8">
            <input id="KoreksiEditNamaBrg" type="text" class="form-control text-center" disabled>
          </div>
        </div>

        <div class="row">
          <div class="col-md-3" style="margin-top:5px;">
            <label>Quantity</label>
          </div>
          <div class="col-md-3">
            <input id="KoreksiEditInputQty" type="number" step="0.01" class="form-control text-right" value="0.00">
          </div>
          <div class="col-md-2" style="margin-top:5px;">
            <label>Satuan</label>
          </div>
          <div class="col-md-3">
            <input id="KoreksiEditInputSat" type="text" class="form-control text-center" disabled>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="row">
          <div class="col-md-3" style="margin-top:5px;">
            <label>Gudang Asal</label>
          </div>
          <div class="col-md-8">
            <input id="KoreksiEditGudangAsal" type="text" class="form-control text-center" disabled>
            <input type="hidden" id="input_gudang_asal">
          </div>
        </div>

        <div class="row">
          <div class="col-md-3" style="margin-top:5px;">
            <label>Gudang Tujuan</label>
          </div>
          <div class="col-md-8">
            <input id="KoreksiEditGudangTujuan" type="text" class="form-control text-center" disabled>
            <input type="hidden" id="input_gudang_tujuan">
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-3">
      <div class="col-md-12 text-right" id="contentContainer">
        <button type="button" class="btn btn-action-danger btn-danger btn-pill-primary" onclick="buttonKoreksiItemBatal()">Batal</button>

        <button id="buttonSubmitKoreksiEdit" type="button" onclick="submitKoreksiEdit()" class="btn btn-primary btn-action-primary btn-pill-primary">Submit Edit</button>
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
      <h2>Detail Terima Transfer Barang</h2>
    </div>
    <div class="col-4 text-right" id="contentContainer">
      <button type="button" class="btn btn-danger btn-pill-primary btn-action-danger" onclick="buttonCloseForm()">Close</button>
    </div>
  </div>

  <div class="modal-body">
    <div class="row">
        <input type="hidden" class="form-control" id="input_detailkoreksi_nourut" placeholder="No Urut" disabled>
        <!-- Kiri -->
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control text-left" id="input_detailkoreksi_nobukti" placeholder="" disabled>
              </div>
            </div>

            <div class="col-md-4" style="margin-top:-12px;">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-md-8" style="margin-top:-12px;">
              <div class="form-group">
                <input type="date" class="form-control text-left" id="input_detailkoreksi_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>
          </div>
        </div>

        <!-- Tengah -->
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Gudang Asal</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input id="input_detailkoreksi_gudangasalnama" type="text" class="form-control text-left" placeholder="" disabled>
                <input id="input_detailkoreksi_gudangasal" type="hidden">
              </div>
            </div>

            <div class="col-md-4" style="margin-top:-10px;">
              <div class="form-group">
                <label>Keterangan</label>
              </div>
            </div>
            <div class="col-md-8" style="margin-top:-10px;">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none" rows=3 placeholder="" class="form-control text-left" id="input_detailkoreksi_keterangan" disabled></textarea>
              </div>
            </div>
          </div>
        </div>
        <!-- Kanan -->
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Gudang Tujuan</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input id="input_detailkoreksi_gudangtujuannama" type="text" class="form-control text-left" placeholder="" disabled>
                <input id="input_detailkoreksi_gudangtujuan" type="hidden">
              </div>
            </div>
          </div>
        </div>
        </div>
    <hr/>
        <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">
              <table id="detailKoreksiTable" class="table table-bordered table-hover table-responsive-lg"  >
                <thead id="detailKoreksiTable_header" class="text-center">
                <tr>
                  <th colspan="4">Deskripsi Barang</th>
                  <th colspan="2">Satuan</th>
                </tr>
                <tr>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Gudang Asal</th>
                  <th scope="col">Gudang Tujuan</th>
                  <th scope="col">Qty</th>
                  <th scope="col">Sat</th>
                </tr>
                </thead>
                <tbody id="detailKoreksiTableData" class="" >
                  <tr>
                    <td colspan=6 class="text-center">Belum ada data</td>
                </tr>
                </tbody>
              </table>
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
    <div class="row">
        <input type="hidden" class="form-control" id="input_detailkoreksi_nourut" placeholder="No Urut" disabled>
        <!-- Kiri -->
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control text-left" id="input_detail_nobukti" placeholder="" disabled>
              </div>
            </div>
          </div>
        </div>
        <!-- Tengah -->
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Gudang Asal</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input id="input_detail_gudangasalnama" type="text" class="form-control text-left" placeholder="" disabled>
                <input id="input_detail_gudangasal" type="hidden">
              </div>
            </div>
          </div>
        </div>
        <!-- Kanan -->
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Gudang Tujuan</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input id="input_detail_gudangtujuannama" type="text" class="form-control text-left" placeholder="" disabled>
                <input id="input_detail_gudangtujuan" type="hidden">
              </div>
            </div>
          </div>
        </div>
        </div>
        <div class="container-fluid mt-4" style="padding:0; margin:0;">
          <div class="po-item-card">
            <div class="po-table-wrap" style="max-height:40vh;">
              <table id="detailTransferBarangTable" class="data-table">
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

let lastRows = (function() {
  // paint pertama tanpa AJAX; reloadData() menyegarkan setelahnya.
  let belum = @json($listBelumTerima);
  let sudah = @json($listSdhTerima);
  return belum.concat(sudah);
})();
let globalStatusTerima = "2"; // filter modal: 2=Semua, 1=Terima saja
let pageSize = 10; // jumlah baris per halaman (pageLength DataTables); -1 = semua

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

function setDefaultHeader() {
  // [ field, label, visible, type, total, decimals ]
  gcart_header = [
    ['NOBUKTI', 'No. Bukti', 1, 'varchar', 0, 0],
    ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
    ['NamagdgAsal', 'Gudang Asal', 1, 'varchar', 0, 0],
    ['NamagdgTujuan', 'Gudang Tujuan', 1, 'varchar', 0, 0],
    ['NOTE', 'Keterangan', 1, 'varchar', 0, 0],
    ['IsTerima', 'Status', 1, 'varchar', 0, 0]
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

function filterByStatusTerima(rows, filterVal) {
  if (filterVal === '1') { // hanya yang sudah diterima
    return rows.filter(r => Number(pickCI(r, 'IsTerima')) === 1);
  }
  if (filterVal === '0') { // hanya yang belum diterima
    return rows.filter(r => Number(pickCI(r, 'IsTerima')) === 0);
  }
  return rows; // Semua
}

// Markup + pemetaan warna/ikon tombol mengikuti aksiButtonsHtml() di gudang/ubahkemasanbarang.blade.php
// (.po-aksi-wrap, cuma title, tanpa tooltip Bootstrap). Fungsi yang dipanggil tetap milik halaman ini.
function aksiButtonsHtml(r) {
  const nobukti = pickCI(r, 'NOBUKTI');
  const isTerima = Number(pickCI(r, 'IsTerima')) === 1;
  let tombolAksi = '';

  if (isTerima) {
    // Sudah diterima
    tombolAksi =
      '<button type="button" class="btn btn-warning btn-sm" title="Detail" onclick="buttonDetailKoreksi(\'' +
      nobukti + '\')"><i class="bi bi-info"></i></button>' +
      '<button type="button" class="btn btn-success btn-sm" title="Koreksi" onclick="buttonKoreksi(\'' +
      nobukti + '\', \'edit\')"><i class="bi bi-pen"></i></button>';
  } else {
    // Belum diterima
    tombolAksi =
      '<button type="button" class="btn btn-warning btn-sm" title="Detail" onclick="buttonDetailTransferBarang(\'' +
      nobukti + '\')"><i class="bi bi-info"></i></button>' +
      '<button type="button" class="btn btn-primary btn-sm" title="Terima" onclick="buttonAdd(\'' +
      nobukti + '\')"><i class="bi bi-plus"></i></button>';
  }

  return '<div class="po-aksi-wrap">' + tombolAksi + '</div>';
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
// penjaga dataset.rtBound supaya tidak terpasang dobel tiap renderTabel().
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

// Rentang yang belum lengkap tidak memicu request, dan urutan tanggal terbalik ditolak dengan
// peringatan — sama seperti ikatPeriode() di gudang/permintaantransferbarang.blade.php.
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
    loadAll();
  };
  awal.addEventListener('change', onUbah);
  akhir.addEventListener('change', onUbah);
}

// Tinggi tabel mengikuti sisa ruang layar. Berhenti diam-diam kalau #page1 sedang disembunyikan
// (loadAll() masih bisa terpanggil saat #page2/#page3 tampil); dihitung ulang begitu #page1
// ditampilkan lagi (lihat buttonCloseForm()).
function aturTinggiTabel() {
  let page = document.getElementById('page1');
  if (!page || page.offsetParent === null) {
    return;
  }

  let area = document.getElementById('content');
  let wrap = document.querySelector('#page1 .po-table-wrap');
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
  const cols = gcart_header.filter(c => c[2] === 1);

  if ($.fn.DataTable.isDataTable('#tabel')) {
    $('#tabel').DataTable().destroy();
  }

  const thead = document.querySelector('#tabel thead');
  thead.innerHTML = ReportTable.headHtml(cols).replace('<tr>',
    '<tr><th style="padding: 4px 12px;" scope="col">Aksi</th>');

  // Cari/paging/sorting dipegang DataTables — di sini cuma filter Status dari modal.
  const rows = filterByStatusTerima(lastRows, globalStatusTerima);

  let html = '';
  rows.forEach(function(r) {
    html += '<tr>';
    html += '<td class="text-center">' + aksiButtonsHtml(r) + '</td>';
    html += cols.map(function(c) {
      const v = pickCI(r, c[0]);
      if (c[0] === 'IsTerima') {
        return (Number(v) === 1) ?
          '<td><span class="sp-badge is-active">Sudah Terima</span></td>' :
          '<td><span class="sp-badge is-inactive">Belum Terima</span></td>';
      }
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

  document.getElementById('tabel2_data').innerHTML = html;

  // dom membuat sendiri kotak scroll .po-table-wrap (header sticky) + baris info & pager.
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

  aturTinggiTabel();
}

// Filter Modal
function updateFilterBadge() {
  let count = ($('#modalStatusTerima').val() !== '2') ? 1 : 0;
  $('#filterBadge').text(count + ' aktif');
}

function resetAllFilters() {
  $('#modalStatusTerima').val('2');
  updateFilterBadge();
}

$(document).on('show.bs.modal', '#modalFilter', function() {
  $('#modalStatusTerima').val(globalStatusTerima);
  updateFilterBadge();
});

$(document).on('change', '#modalFilter select.rt-native', updateFilterBadge);

function applyModalFilter() {
  globalStatusTerima = $('#modalStatusTerima').val();
  renderTabel(); // DataTables di-init ulang, jadi otomatis kembali ke halaman 1
  $('#modalFilter').modal('hide');
}

$(document).ready(function(){
  doSetHeader(g_modeReport);
  ReportTable.init({
    table: '#tabel',
    bar: '#rtBar',
    onChange: renderTabel
  });
  renderTabel();
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

  document.getElementById("KoreksiEditInputQty").value = barang.QNT ? parseFloat(barang.QNT).toFixed(2) : "0.00";
  document.getElementById("KoreksiEditInputSat").value = barang.NOSAT == 1 ? barang.SAT_1 : barang.SAT_2;

  document.getElementById("KoreksiEditGudangAsal").value = barang.GdgAsal;
  document.getElementById("input_gudang_asal").value = barang.GdgAsal;

  document.getElementById("KoreksiEditGudangTujuan").value = barang.GdgTujuan;
  document.getElementById("input_gudang_tujuan").value = barang.GdgTujuan;

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
          <td>${item.GdgAsal}</td>
          <td>${item.GdgTujuan}</td>
          <td class="text-right">${item.QNT ? parseFloat(item.QNT).toLocaleString() : '0.00'}</td>
          <td class="text-center">${item.SAT_1}</td>
          <td class="text-center">
            <div class="action-buttons">
              <button class="btn btn-action-sm btn-action-success" type="button" onclick="buttonKoreksiEditItem(${i})"><i class="bi bi-pen"></i></button>
              <button class="btn btn-action-sm btn-action-danger" type="button" onclick="buttonKoreksiDeleteItem(${i})"><i class="bi bi-trash"></i></button>
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
      $("#input_koreksi_catatan").val(header.KETERANGAN);
      $("#input_koreksi_gudangtujuan_nama").val(header.GdgTujuan);
      $("#input_gudangasal_nama").val(header.GdgAsal);

      buttonKoreksiItemBatal();
    },
    error: function (err) {
      console.error(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}


function buttonKoreksiDeleteItem(i) {
  console.log(i);
  let barang = dataTableKoreksi[i];

  let akses = $("#akses_ishapus").val();
  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + barang.NAMABRG + ' ?',
    function () {
      let _token = $("#_token").val();
      let choice = "D";
      let qnt = 0;
      let qnt1 = 0;
      let qnt2 = 0;

      // Data dari barang
      let nobukti = barang.NOBUKTI;
      let urut = barang.URUT;
      let kodebrg = barang.KODEBRG;
      let namabrg = barang.NAMABRG;
      let kodegdgasal = barang.GDGASAL;
      let kodegdgtujuan = barang.GDGTUJUAN;
      let nosat = barang.NOSAT;
      let isi = parseFloat(barang.ISI);
      let sat1 = barang.SAT_1;
      let sat2 = barang.SAT_2;

      // Dari DBTRANSFERDET
      let noTransfer = barang.NoTransfer;
      let urutTransfer = barang.UrutTransfer;

      let keterangan = $("#input_koreksi_keterangan").val();

      // log data
      console.log({
        choice,
        qnt,
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
      });

      $.ajax({
        url: "{!! url('terimatransferbarangspkoreksi') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          choice,
          qnt,
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
            alertify.success('Item telah dihapus');
          } else {
            alertify.warning("Gagal menghapus item");
          }
        },
        error: function (err) {
          console.log(err);
          alertify.warning('Terjadi kesalahan, silakan refresh browser');
        }
      });
    },
    function () {
      console.log('Hapus dibatalkan');
    }
  );
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

  let qntInput = parseFloat($("#KoreksiEditInputQty").val());
  if (isNaN(qntInput)) {
    alertify.warning("Qty tidak valid");
    return;
  }

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

  let kodegdgasal = $("#input_gudang_asal").val();
  let kodegdgtujuan = $("#input_gudang_tujuan").val();

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
          <td>${item.GdgAsal}</td>
          <td>${item.GdgTujuan}</td>
          <td class="text-right">${parseFloat(item.QNT).toLocaleString()}</td>
          <td class="text-center">${item.SAT_1}</td>
          <td class="text-center">
            <div class="action-buttons">
              <button class="btn btn-action-sm btn-action-success" type="button" onclick="buttonKoreksiEditItem(${i})"><i class="bi bi-pen"></i></button>
              <button class="btn btn-action-sm btn-action-danger" type="button" onclick="buttonKoreksiDeleteItem(${i})"><i class="bi bi-trash"></i></button>
            </div>
          </td>
        </tr>`;
      });

      $("#koreksiTableData").html(rowTable);

      // Isi Form Header
      $("#input_koreksi_nobukti").val(data.NOBUKTI);
      $("#input_gudangasal_nama").val(data.GdgAsal);
      $("#input_gudangtujuan_nama").val(data.GdgTujuan);
      $("#input_koreksi_keterangan").val(data.NOTE);

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
          <td>${item.GdgAsal}</td>
          <td>${item.GdgTujuan}</td>
          <td class="text-right">${parseFloat(item.QNT).toLocaleString()}</td>
          <td>${item.SAT_1}</td>
        </tr>`;
      });

      $("#detailKoreksiTableData").html(rowTable);

      // Isi Header Detail
      const data = res[0];

      $("#input_detailkoreksi_nobukti").val(data.NOBUKTI);
      $("#input_detailkoreksi_gudangasalnama").val(data.GdgAsal);
      $("#input_detailkoreksi_gudangtujuannama").val(data.GdgTujuan);
      $("#input_detailkoreksi_keterangan").val(data.KETERANGAN);

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
      $("#input_detail_gudangasalnama").val(data.NamagdgAsal || '');
      $("#input_detail_gudangtujuannama").val(data.NamagdgTujuan || '');

      $('.mainpage').hide();
      $('#page5').show();
    },
    error: function (err) {
      console.error('Error response:', err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}

function reloadData(_focusNobukti) {
  let listBelumTerima = [], listSdhTerima = [];

  $.ajax({
    url: "{!! url('terimatransferbarangloadall') !!}",
    type: "get",
    async: false,
    data: {
      date1: $('#inputDate1').val(),
      date2: $('#inputDate2').val()
    },
    success: function(res) {
      listBelumTerima = res.listBelumTerima || [];
      listSdhTerima = res.listSdhTerima || [];
    }
  });

  lastRows = listBelumTerima.concat(listSdhTerima);
  renderTabel();
}

function loadAll(_focusNobukti) {
  reloadData(_focusNobukti);
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
                class="input_add_qnt  text-right" data-index="${i}" type="number" min="0" value="" placeholder="0">
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

  $(".add_checkbox:checked").each(function() {
    const i = $(this).data("index");
    const item = dataTableAdd[i];
    const inputQty = parseFloat($(`.input_add_qnt[data-index='${i}']`).val() || 0);
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
      nourut
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


</script>




@endsection
