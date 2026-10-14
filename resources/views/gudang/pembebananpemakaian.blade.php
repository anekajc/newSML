@extends('newmasterTest')
@section('buttons')
@endsection
@section('page-title', 'Biaya Pemakaian')

@section('css')
    {{-- Layout newmasterTest (sama seperti gudang/ubahkemasanbarang.blade.php) sudah memuat
         newmaster.css + po-table-header.css, dan TIDAK memuat tableMaster2.css — tampilan tabel
         daftar diambil dari blok .po-list-page di po-table-header.css (class-nya dipasang di
         div#page1), tombol form dari blok .po-form-page (#page2/#page3/#page4).
         report-table.css dimuat di sini (dulu dari layout gudang) untuk tabel item (.tb-report),
         modal picker Perkiraan/Costing/Sub Costing (.rt-picker-v2) dan modal filter (.rt-filter). --}}
    <link rel="stylesheet"
        href="{!! URL::asset('css/report-table.css') !!}?v={{ @filemtime(base_path('public/css/report-table.css')) ?: '1' }}">
    {{-- Dimuat ULANG setelah report-table.css supaya .po-* / .rt-* versi po-table-header.css menang
         saat spesifisitas seri. --}}
    <link rel="stylesheet"
        href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
    <link rel="stylesheet"
        href="{!! URL::asset('css/scrollbar-autohide.css') !!}?v={{ @filemtime(base_path('public/css/scrollbar-autohide.css')) ?: '1' }}">

    {{-- Search box #tabel_add_list_perkiraan_filter / _costing_filter / _subcosting_filter
         sengaja tidak ditata di sini — modal Perkiraan/Costing/Sub Costing memakai .rt-picker-v2,
         yang sudah menata kotak search DataTables-nya sendiri (lihat report-table.css). --}}
@endsection
@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

{{-- Kartu + toolbar + tabel mengikuti gudang/ubahkemasanbarang.blade.php (skema po-*, DataTables,
     id #tabel yang sudah terdaftar di po-table-header.css). Belum + sudah otorisasi digabung di satu
     tabel; statusnya dibedakan lewat kolom Oto + filter modal. --}}
<div id="page1" class="container-fluid mainpage po-list-page">

  <div id="printContainer" style="display:none"></div>

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
              <th style="padding: 4px 12px;" scope="col">Actions</th>
              <th style="padding: 4px 12px;" scope="col">No Bukti</th>
              <th style="padding: 4px 12px;" scope="col">Tanggal</th>
              <th style="padding: 4px 12px;" scope="col">Keterangan</th>
              <th style="padding: 4px 12px;" scope="col">Perkiraan</th>
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



<!-- modal edit / koreksi -->

<div id="page2" style="display: none; margin-top: 10vh;" class="mainpage container-fluid po-form-page" >

  <div class="row">
    <div class="col-8 text-left">
      <h2>Form Penyerahan Sample</h2>
    </div>
    <div class="col-4 text-right action-group" id="contentContainer">
      <button type="button" class="btn btn-action-danger btn-danger btn-pill-primary " onclick="buttonCloseForm()">CLOSE</button>
    </div>
  </div>

  <div class="container-fluid">
    <input type="hidden" name="noUrut" id="input_add_nourut" value="" />
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
    <hr/>
        <div class="tb-report container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">
          <div class="table-outer">
            <div class="table-wrap">
              <table id="addTable" class="tb">
                <thead>
                  <tr>
                    <th>Serahkan</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Qty</th>
                    <th>Satuan</th>
                    <th>Stock</th>
                  </tr>
                </thead>
                <tbody id="addTableData">
                  <tr>
                    <td colspan="8" class="text-center">Belum ada data</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
    </div>
    <div class="row mt-2" style="margin-top: 0">
      <div class="col-md-12 text-right mt-4">
        <button id="buttonSubmitAdd" type="button" onclick="submitAdd()" class="btn btn-primary" style="height: 30px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;">Submit</button>
        <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
      </div>
    </div>
  </div>
</div>


<div id="page3" style="display: none;" class="mainpage container-fluid po-form-page">
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
    {{-- <input type="hidden" name="noUrut" id="input_koreksi_nourut" value="" /> --}}
    <div class="row" id="formBsGrid">
        <input type="hidden" class="form-control" id="input_koreksi_nourut" placeholder="No Urut" disabled>
        <!-- Kiri -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label" style="margin-top:-5px;">No Bukti</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control text-left" id="input_koreksi_nobukti" placeholder="No Bukti" disabled>
                </div>
            </div>
        </div>
        {{-- Tanggal --}}
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label" style="margin-top:-5px;">Tanggal</label>
                <div class="col-sm-8">
                    <input type="date" class="form-control text-center" id="input_koreksi_tanggal" disabled>
                </div>
            </div>
        </div>
        {{-- Gudang --}}
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label" style="margin-top:-5px;">Gudang</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control text-left" id="input_koreksi_gudang" placeholder="Gudang" disabled>
                </div>
            </div>
        </div>

        <div class="tb-report container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">
          <div class="table-outer">
            <div class="table-wrap">
              <table id="koreksiTable" class="tb">
                <thead>
                <tr>
                  <th colspan="6">Deskripsi Barang</th>
                  <th colspan="1"></th>
                </tr>
                <tr>
                  <th>Kode Barang</th>
                  <th>Nama Barang</th>
                  <th>Qty</th>
                  <th>Satuan</th>
                  <th>Perkiraan</th>
                  <th>Nama Perkiraan</th>
                  <th class="rt-fixed-th">Actions</th>
                </tr>
                </thead>
                <tbody id="koreksiTableData">
                  <tr>
                    <td colspan="11" class="text-center">Belum ada data</td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>
    </div>

    <div id="formKoreksiEdit" class="container-fluid showhideitem">
    {{-- <div class="row">
      <div class="col-4">
        <h4 id="h4KoreksiEditItem" style="margin-left:-15px;">Edit Item</h4>
      </div>
    </div> --}}

    <div class="row">
      <div class="col-md-6">
        <div class="row align-items-center mt-2">
          <label class="col-4 col-form-label font-weight-bold">Perkiraan</label>
          <div class="col">
            <div class="input-group">
              <input id="KoreksiEditPerkiraan" type="text" class="form-control text-left" placeholder="Perkiraan" onkeypress="onKeyPressPicker(event,'perkiraan')">
              <button type="button" onclick="openPicker('perkiraan')" class="btn btn-chip-biru btn-sm"><i class="bi bi-search"></i></button>
              <input type="hidden" id="KoreksiEditNamaPerkiraan">
            </div>
          </div>
        </div>

        <div class="row align-items-center mt-2">
          <label class="col-4 col-form-label font-weight-bold">Costing</label>
          <div class="col">
            <div class="input-group">
              <input id="KoreksiEditCosting" type="text" class="form-control text-left" placeholder="Costing" onkeypress="onKeyPressPicker(event,'costing')">
              <button type="button" onclick="openPicker('costing')" class="btn btn-chip-biru btn-sm"><i class="bi bi-search"></i></button>
              <input type="hidden" id="input_costing">
            </div>
          </div>
        </div>

        <div class="row align-items-center mt-2">
          <label class="col-4 col-form-label font-weight-bold">Sub Costing</label>
          <div class="col">
            <div class="input-group">
              <input id="KoreksiEditSubCosting" type="text" class="form-control text-left" placeholder="Sub Costing" onkeypress="onKeyPressPicker(event,'subcosting')">
              <button type="button" onclick="openPicker('subcosting')" class="btn btn-chip-biru btn-sm"><i class="bi bi-search"></i></button>
              <input type="hidden" id="input_sub_costing">
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-3">
      <div class="col-md-12 text-right" id="contentContainer">
        <button type="button" class="btn btn-action-danger btn-danger btn-pill-primary" onclick="buttonKoreksiItemBatal()">Batal</button>

        <button id="buttonSubmitKoreksiEdit" type="button" onclick="submitKoreksiEdit()" class="btn btn-action-primary btn-primary btn-pill-primary" >Submit Edit</button>
      </div>
    </div>
  </div>
    <hr/>

  </div>
</div>
</div>
{{-- Start Modal List perkiraan --}}
  <div class="modal fade rt-picker-v2" id="modalAddListPerkiraan" role="dialog" aria-labelledby="labelPerkiraan" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="labelPerkiraan">Pilih Perkiraan</h5>
          <button type="button" class="close" onclick="buttonAddListBatal()" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <table id="tabel_add_list_perkiraan" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th scope="col">Perkiraan</th>
                <th scope="col">Keterangan</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
{{-- End Modal List perkiraan --}}

{{-- Start Modal List costing --}}
  <div class="modal fade rt-picker-v2" id="modalAddListCosting" role="dialog" aria-labelledby="labelCosting" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="labelCosting">Pilih Costing</h5>
          <button type="button" class="close" onclick="buttonAddListBatal()" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <table id="tabel_add_list_costing" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th scope="col">Kode Cost</th>
                <th scope="col">Nama Cost</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
{{-- End Modal List costing --}}

{{-- Start Modal List subcosting --}}
  <div class="modal fade rt-picker-v2" id="modalAddListSubCosting" role="dialog" aria-labelledby="labelSubCosting" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="labelSubCosting">Pilih Sub Costing</h5>
          <button type="button" class="close" onclick="buttonAddListBatal()" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <table id="tabel_add_list_subcosting" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th scope="col">Kode Cost</th>
                <th scope="col">Kode Sub Cost</th>
                <th scope="col">Nama Sub Cost</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
{{-- End Modal List subcosting --}}

<div id="page4" style="display: none;" class="mainpage container-fluid po-form-page">
  <div class="row" style="padding: 1rem 0">
    <div class="col-6 text-left">
      {{-- Judul dikosongkan, disamakan dengan #pageForm di gudang/ubahkemasanbarang.blade.php. --}}
    </div>
    <div class="col-6 text-right" id="contentContainer">
      <button type="button" class="btn btn-danger btn-pill-primary btn-action-danger"
        onclick="buttonCloseForm()">Close</button>
    </div>
  </div>

  <div class="container-fluid" id="formBsGrid">
    <div class="row">
        <input type="hidden" class="form-control" id="input_detailkoreksi_nourut" placeholder="No Urut" disabled>
        <!-- Kiri -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">No Bukti</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control text-left" id="input_detailkoreksi_nobukti" placeholder="No Bukti" disabled>
                </div>
            </div>
        </div>
        {{-- Tanggal --}}
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Tanggal</label>
                <div class="col-sm-8">
                    <input type="date" class="form-control text-center" id="input_detailkoreksi_tanggal" disabled>
                </div>
            </div>
        </div>
        {{-- Gudang --}}
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Gudang</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control text-left" id="input_detailkoreksi_gudang" placeholder="Gudang" disabled>
                </div>
            </div>
        </div>
        </div>
    <hr/>
        <div class="tb-report container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">
          <div class="table-outer">
            <div class="table-wrap">
              <table id="detailKoreksiTable" class="tb">
                <thead>
                <tr>
                  <th colspan="6">Deskripsi Barang</th>
                </tr>
                <tr>
                  <th>Kode Barang</th>
                  <th>Nama Barang</th>
                  <th>Qty</th>
                  <th>Satuan</th>
                  <th>Perkiraan</th>
                  <th>Nama Perkiraan</th>
                </tr>
                </thead>
                <tbody id="detailKoreksiTableData">
                  <tr>
                    <td colspan="6" class="text-center">Belum ada data</td>
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
{{-- report-table.js (mesin geser/sembunyi kolom) dulu dimuat layout gudang; newmasterTest tidak. --}}
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script type="text/javascript">



let dataTableAdd = []
let dataTableKoreksi = []
let barangKoreksiEdit = {}

/* ============================================================================
 * Tabel interaktif gabungan (belum + sudah otorisasi) — port dari
 * gudang/permintaanpemakaian.blade.php (mesin gcart_header, lihat
 * docs/new-slider-table-guide.md). doShowCustomize()/doButtonSubtotal()/
 * doButtonGrandtotal() sengaja tidak diikutkan — itu untuk modal "Atur Kolom"
 * yang tidak ada di halaman ini; report-table.js memanggil onChange sendiri.
 * ========================================================================= */
let lastRows = @json($penerimaanArray); // paint pertama tanpa AJAX; reloadData() menyegarkan setelahnya
let globalOtorisasi = "2"; // filter modal: 2=Semua, 1=Sudah Otorisasi, 0=Belum Otorisasi

let pageSize = 10; // dipakai sebagai pageLength DataTables di renderTabel()
// No. bukti yang barusan disimpan/diotorisasi: renderTabel() memindahkan tabel ke halaman
// tempat baris itu berada, lalu mengosongkannya lagi. Diisi reloadData(_focusNobukti).
let fokusNobukti = '';

var g_href = 'pembebananpemakaian';
var g_modeReport = '1';
var gcart_header = [];
var gsum_issubtotal = 0;
var gsum_isgrandtotal = 0;
var gct_desimal_max = 4;

function setDefaultHeader() {
  // [ field, label, visible, type, total, decimals ]
  gcart_header = [
    ['NOBUKTI', 'No Bukti', 1, 'varchar', 0, 0],
    ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
    ['Keterangan', 'Keterangan', 1, 'varchar', 0, 0],
    ['Perkiraan', 'Perkiraan', 1, 'varchar', 0, 0],
    // IsOtorisasi1/OtoUser1/TglOto1 bukan lagi kolom geser/sembunyi — ketiganya kolom tetap
    // (Oto/User Oto/Tgl Oto) yang ditambahkan sendiri oleh renderTabel().
    ['Kodegdg', 'Kode Gudang', 0, 'varchar', 0, 0],
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

// Ambil field dari row tanpa peduli besar/kecil huruf — hasil grouping controller
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
// server selalu mengembalikan seluruh rentang tanggal yang dipilih.
function filterByOtorisasi(rows, filterVal) {
  if (filterVal === '1') {
    return rows.filter(r => Number(pickCI(r, 'IsOtorisasi1')) === 1);
  }
  if (filterVal === '0') {
    return rows.filter(r => Number(pickCI(r, 'IsOtorisasi1')) === 0);
  }
  return rows;
}

// Markup tombol mengikuti aksiButtonsHtml() di gudang/ubahkemasanbarang.blade.php
// (.po-aksi-wrap, cuma title, tanpa tooltip Bootstrap). Warna/ikon/urutan tetap milik halaman
// ini: Detail=amber bi-info, Otorisasi=biru bi-key, Edit=hijau bi-pencil-fill,
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
      nobukti + '\')"><i class="bi bi-pen"></i></button>';
  }

  return '<div class="po-aksi-wrap">' + tombolAksi + '</div>';
}

/* Bar kolom tersembunyi harus berada tepat di atas tabelnya. DataTables membungkus tabel dengan
   #<id>_wrapper saat init, jadi acuannya ikut berpindah — sama seperti rtPindahBar() di
   gudang/ubahkemasanbarang.blade.php. */
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
// peringatan — sama seperti ikatPeriode() di ubahkemasanbarang.blade.php.
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
    reloadData();
  };
  awal.addEventListener('change', onUbah);
  akhir.addEventListener('change', onUbah);
}

// Pindahkan tabel ke halaman tempat baris `nobukti` berada (urutan tampil saat ini: setelah
// filter modal + pencarian + sorting).
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
// ubahkemasanbarang.blade.php. Berhenti diam-diam kalau #page1 sedang disembunyikan
// (reloadData() masih bisa terpanggil saat #page3/#page4 tampil); dihitung ulang begitu
// #page1 ditampilkan lagi (buttonCloseForm() memanggil reloadData() setelah #page1 tampil).
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
  // IsOtorisasi1/OtoUser1/TglOto1 dikeluarkan dari cols meski masih tersimpan di susunan kolom
  // lama — ketiganya kolom tetap (Oto/User Oto/Tgl Oto) yang ditambahkan sendiri di bawah,
  // bukan kolom geser/sembunyi (sama seperti ubahkemasanbarang.blade.php).
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

  // Cari/paging/sorting dipegang DataTables — di sini cuma filter Otorisasi dari modal.
  const rows = filterByOtorisasi(lastRows, globalOtorisasi);

  let html = '';
  rows.forEach(function(r) {
    html += '<tr data-nobukti="' + String(nullToEmpty(r.NOBUKTI)).replace(/"/g, '&quot;') + '">';
    html += '<td class="text-center">' + aksiButtonsHtml(r) + '</td>';
    html += cols.map(function(c) {
      const v = pickCI(r, c[0]);
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

  // Susunan opsi disalin dari ubahkemasanbarang.blade.php: dom membuat sendiri kotak scroll
  // .po-table-wrap (header sticky) + baris info & pager.
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

/* -- FILTER MODAL (Otorisasi: Semua/Sudah Otorisasi/Belum) -- */
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

// Menggantikan loadAll() lama — satu list gabungan, difilter di server berdasarkan
// rentang tanggal yang sedang dipilih. Dipanggil saat tanggal berubah dan setelah
// otorisasi/batal otorisasi/koreksi supaya tabel menyegarkan diri sendiri.
// _focusNobukti (opsional): no. bukti yang barusan diubah — tabel dipindah ke halaman tempat
// baris itu berada. Tanpa argumen tabel kembali ke halaman 1.
function reloadData(_focusNobukti) {
  $.ajax({
    url: "{!! url('pembebananpemakaianloadall') !!}",
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

$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip({
    container: 'body',
    boundary: 'window'
  });

  // Satu-satunya titik di mana modal pemilih Perkiraan/Costing/Sub Costing dijamin
  // sudah display:block, jadi di sinilah lebar kolom DataTables boleh dihitung —
  // pola sama dengan #formAddListItem di permintaanpemakaian.blade.php.
  $('#modalAddListPerkiraan').on('shown.bs.modal', function() { flushPickerPending('perkiraan'); });
  $('#modalAddListCosting').on('shown.bs.modal', function() { flushPickerPending('costing'); });
  $('#modalAddListSubCosting').on('shown.bs.modal', function() { flushPickerPending('subcosting'); });

  // Tabel gabungan — mesin interaktif (drag/gear/bar), lihat
  // docs/new-slider-table-guide.md. doSetHeader() memuat layout tersimpan milik
  // user ini untuk halaman+mode ini, atau menyimpan setDefaultHeader() kalau ini
  // kunjungan pertama.
  doSetHeader(g_modeReport);
  ReportTable.init({
    table: '#tabel',
    bar: '#rtBar',
    onChange: renderTabel
  });
  renderTabel();
});

// ===================== Perkiraan / Costing / Sub Costing picker =====================
// Satu mesin generik dipakai untuk ketiga field, mengikuti pola resolveBarang()/
// initBarangTable() di permintaanpemakaian.blade.php: ketik kode + Enter (atau klik +)
// -> kode yang PERSIS cocok langsung mengisi field tanpa modal; selain itu modal
// .rt-picker-v2 dibuka dengan pencarian sudah terisi, dan klik baris langsung memilih.
//
// Beda dengan referensinya: Costing/Sub Costing bergantung pada field induk
// (Perkiraan/Costing), jadi cache-nya di-key per nilai induk (pickerCacheKey), dan
// openPicker() TETAP menunggu hasil fetch sebelum modal ditampilkan (bukan optimistic
// show-dulu-isi-belakangan) supaya guard "induk belum dipilih" / "induk tidak punya
// anak" — perilaku asli buttonKoreksiListCosting/SubCosting — tetap berlaku persis
// sebelum modal sempat terbuka.
const PICKERS = {
  perkiraan: {
    modal: '#modalAddListPerkiraan',
    table: '#tabel_add_list_perkiraan',
    codeInput: '#KoreksiEditPerkiraan',
    nameInput: '#KoreksiEditNamaPerkiraan',
    url: "{{ url('pembebananpemakaianlistperkiraan') }}",
    codeField: 'Perkiraan',
    nameField: 'Keterangan',
    columns: [{ data: 'Perkiraan' }, { data: 'Keterangan' }],
    params: () => ({}),
    parent: null,
    clears: ['costing', 'subcosting'],
  },
  costing: {
    modal: '#modalAddListCosting',
    table: '#tabel_add_list_costing',
    codeInput: '#KoreksiEditCosting',
    nameInput: '#input_costing',
    url: "{{ url('pembebananpemakaianlistcosting') }}",
    codeField: 'KodeCost',
    nameField: 'NamaCost',
    columns: [{ data: 'KodeCost' }, { data: 'NamaCost' }],
    params: () => ({ perkiraan: $('#KoreksiEditPerkiraan').val() }),
    parent: {
      input: '#KoreksiEditPerkiraan',
      msg: 'Silakan pilih perkiraan terlebih dahulu.',
      emptyMsg: 'Perkiraan ini tidak memiliki costing.',
    },
    clears: ['subcosting'],
  },
  subcosting: {
    modal: '#modalAddListSubCosting',
    table: '#tabel_add_list_subcosting',
    codeInput: '#KoreksiEditSubCosting',
    nameInput: '#input_sub_costing',
    url: "{{ url('pembebananpemakaianlistsubcosting') }}",
    codeField: 'KodeSubCost',
    nameField: 'NamaSubCost',
    columns: [{ data: 'KodeCost' }, { data: 'KodeSubCost' }, { data: 'NamaSubCost' }],
    params: () => ({ kodeCost: $('#KoreksiEditCosting').val() }),
    parent: {
      input: '#KoreksiEditCosting',
      msg: 'Silakan pilih costing terlebih dahulu.',
      emptyMsg: 'Costing ini tidak memiliki sub costing.',
    },
    clears: [],
  },
};

// cache: daftar per nilai induk saat ini (key '' untuk Perkiraan, yang tidak punya induk).
// dt: instance DataTables aktif. busy: guard Enter-mashing. pendingList/pendingTerm:
// dipakai initPickerTable() kalau modal belum display:block saat init dipanggil.
const pickerState = {
  perkiraan: { cache: {}, dt: null, busy: false, pendingList: null, pendingTerm: '' },
  costing: { cache: {}, dt: null, busy: false, pendingList: null, pendingTerm: '' },
  subcosting: { cache: {}, dt: null, busy: false, pendingList: null, pendingTerm: '' },
};

function pickerCacheKey(key) {
  let cfg = PICKERS[key];
  return cfg.parent ? ($(cfg.parent.input).val() || '') : '';
}

function pickerCachedList(key) {
  return pickerState[key].cache[pickerCacheKey(key)];
}

function fetchPickerList(key, callback) {
  let cfg = PICKERS[key];
  $.ajax({
    url: cfg.url,
    type: 'get',
    data: cfg.params(),
    success: function(res) {
      let list = res || [];
      pickerState[key].cache[pickerCacheKey(key)] = list;
      if (callback) callback(list);
    },
    error: function(err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan saat mengambil data.');
    }
  });
}

// destroy() TIDAK membersihkan style="width:...px" yang ditulis DataTables ke tiap
// <th> — init berikutnya membacanya sebagai lebar tetap dan kolom menyusut tiap kali
// modal dibuka ulang. Bersihkan dulu sebelum re-init (sama seperti
// resetBarangTableWidths() di permintaanpemakaian.blade.php).
function resetPickerTableWidths(key) {
  let $t = $(PICKERS[key].table);
  $t.css('width', '');
  $t.children('colgroup').remove();
  $t.find('thead th').css('width', '');
}

function initPickerTable(key, list, term) {
  let cfg = PICKERS[key];
  let st = pickerState[key];

  // DataTables menghitung lebar kolom dari container saat init, dan modal BS4 baru
  // display:block setelah transisi fade selesai — kalau init dipanggil sebelum itu,
  // lebar diukur di container 0px. Antre saja, dieksekusi di shown.bs.modal.
  if (!$(cfg.modal).is(':visible')) {
    st.pendingList = list;
    st.pendingTerm = term || '';
    return;
  }
  st.pendingList = null;

  if ($.fn.DataTable.isDataTable(cfg.table)) {
    $(cfg.table).DataTable().clear().destroy();
  }
  resetPickerTableWidths(key);

  st.dt = $(cfg.table).DataTable({
    data: list,
    deferRender: true,
    paging: true,
    pageLength: 25,
    lengthChange: false,
    searching: true,
    order: [],
    language: {
      emptyTable: 'Tidak ada data'
    },
    columns: cfg.columns,
    createdRow: function(row, data) {
      row.className = 'pick-row';
      row.onclick = function() {
        applyPick(key, data);
      };
    }
  });

  st.dt.search(term || '').draw();
  st.pendingTerm = '';
}

function flushPickerPending(key) {
  let st = pickerState[key];
  if (st.pendingList !== null) {
    initPickerTable(key, st.pendingList, st.pendingTerm);
  }
}

// Buka modal untuk `key`. Menjaga guard field induk yang sama seperti
// buttonKoreksiListCosting()/buttonKoreksiListSubCosting() versi lama: induk kosong
// -> peringatan, tidak fetch; induk terisi tapi tidak punya anak -> field dikosongkan
// + pesan info, modal TIDAK dibuka.
function openPicker(key, term) {
  let cfg = PICKERS[key];
  term = term || '';

  if (cfg.parent && !$(cfg.parent.input).val()) {
    alertify.warning(cfg.parent.msg);
    return;
  }

  let showList = function(list) {
    if (cfg.parent && !list.length) {
      $(cfg.codeInput).val('');
      $(cfg.nameInput).val('');
      alertify.message(cfg.parent.emptyMsg);
      return;
    }
    initPickerTable(key, list, term);
    $(cfg.modal).modal('show');
  };

  let cached = pickerCachedList(key);
  if (cached) {
    showList(cached);
    return;
  }

  fetchPickerList(key, showList);
}

// Titik masuk tunggal buat Enter dan tombol plus — pola sama dengan resolveBarang() di
// permintaanpemakaian.blade.php. Kode yang PERSIS cocok (case-insensitive, trimmed)
// langsung mengisi field tanpa modal; selain itu (sebagian, nama, atau kosong) modal
// dibuka dengan `term` sudah terisi di kotak search-nya.
function resolvePicker(key, term) {
  let cfg = PICKERS[key];
  term = (term || '').trim();

  if (cfg.parent && !$(cfg.parent.input).val()) {
    alertify.warning(cfg.parent.msg);
    return;
  }

  if (!term) {
    openPicker(key, '');
    return;
  }

  if (pickerState[key].busy) {
    return;
  }

  let findExact = function(list) {
    let needle = term.toLowerCase();
    return (list || []).find(item => String(item[cfg.codeField] || '').trim().toLowerCase() === needle);
  };

  let cached = pickerCachedList(key);
  if (cached) {
    let hit = findExact(cached);
    if (hit) {
      applyPick(key, hit);
    } else {
      openPicker(key, term);
    }
    return;
  }

  pickerState[key].busy = true;
  fetchPickerList(key, function(list) {
    pickerState[key].busy = false;
    let hit = findExact(list);
    if (hit) {
      applyPick(key, hit);
    } else {
      openPicker(key, term);
    }
  });
}

function onKeyPressPicker(e, key) {
  if (e.which === 13) {
    e.preventDefault();
    resolvePicker(key, $(PICKERS[key].codeInput).val());
  }
}

// Satu-satunya tempat yang mengisi kode+nama field, baik dari klik baris di picker
// maupun dari kecocokan persis di resolvePicker() — lalu membuang pilihan turunan
// yang jadi usang (mis. ganti Perkiraan membuang Costing & Sub Costing).
function applyPick(key, item) {
  let cfg = PICKERS[key];
  $(cfg.codeInput).val(item[cfg.codeField]);
  $(cfg.nameInput).val(item[cfg.nameField]);
  $(cfg.modal).modal('hide');

  cfg.clears.forEach(function(childKey) {
    let childCfg = PICKERS[childKey];
    $(childCfg.codeInput).val('');
    $(childCfg.nameInput).val('');
  });
}

function buttonAddListBatal() {
  $('#modalAddListPerkiraan').modal('hide');
  $('#modalAddListCosting').modal('hide');
  $('#modalAddListSubCosting').modal('hide');
}


function buttonKoreksiEditItem (i) {
  let barang = dataTableKoreksi[i];
  barangKoreksiEdit = barang;

  document.getElementById("KoreksiEditPerkiraan").value = barang.KodePerkiraan;
  document.getElementById("KoreksiEditNamaPerkiraan").value = barang.namaPerkiraan;

  document.getElementById("KoreksiEditCosting").value = barang.KODECOST;
  document.getElementById("input_costing").value = barang.NamaCost;

  document.getElementById("KoreksiEditSubCosting").value = barang.KODESUBCOST;
  document.getElementById("input_sub_costing").value = barang.NamaSubCost;

  $('#formKoreksiEdit').show();
}

function refreshDataTableKoreksi (nobukti) {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('pembebananpemakaiangetdetailpenerimaan') !!}",
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
        rowTable += `
        <tr>
          <td>${item.KodeBrg}</td>
          <td>${item.NamaBrg}</td>
          <td class="text-right">${parseFloat(item.Qnt).toLocaleString()}</td>
          <td class="text-center">${item.Satuan}</td>
          <td>${item.KodePerkiraan ?? ''}</td>
          <td>${item.namaPerkiraan ?? ''}</td>
          <td class="text-center">
            <button type="button" class="btn-action-sm btn-action-success" data-toggle="tooltip" title="Edit" onclick="buttonKoreksiEditItem(${i})"><i class="bi bi-pen"></i></button>
          </td>
        </tr>`;
      });

      document.getElementById("koreksiTableData").innerHTML = rowTable;

      let header = res[0];

      $("#input_koreksi_nobukti").val(header.NoBukti || header.NOBUKTI);
      $("#input_koreksi_tanggal").val(String(header.Tanggal || '').substring(0, 10));
      $("#input_koreksi_gudang").val(header.gudang || '');

      buttonKoreksiItemBatal();
    },
    error: function (err) {
      console.error(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}



function buttonOtorisasi(nobukti, isOtorisasi) {
  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  if (Number(isOtorisasi) > 0) {
    alertify.warning('Sudah diotorisasi');
    return;
  }

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('pembebananpemakaianspotorisasi') !!}",
    type: "post",
    dataType: "json",
    data: {
      _token,
      nobukti,
      otorisasi: 1
    },
    success: function (res) {
      if (res.status > 0) {
        alertify.success(res.msg);
        reloadData(nobukti);
      } else {
        alertify.warning(res.msg);
      }
    },
    error: function (err) {
      console.log(err);
      alertify.error('Terjadi kesalahan. Silakan refresh browser.');
    }
  });
}

function buttonBatalOtorisasi (nobukti) {
  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  // alertify.prompt(JUDUL, PESAN, NILAI_AWAL, onOK, onCancel) — argumen pertama jadi
  // judul header dialog; classList di bawah menyamakan gaya tombol OK/Cancel dengan
  // dialog Batal Otorisasi di permintaanpemakaian.blade.php ('is-danger' karena aksi ini
  // merusak/membalik otorisasi).
  var dlgBatalOtorisasi = alertify.prompt("Batal Otorisasi", "Masukkan keterangan batal otorisasi nomor   " + nobukti, "",
  function(evt, value) {
    // alertify.success("You entered: " + value);
    let xpket = value;

     if (xpket==''){
          alertify.warning('Keterangan harus diisi.');
          $.abort();
        }
      let _token = $("#_token").val();

      $.ajax({
        url: "{!! url('pembebananpemakaianspbatalotorisasi') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          nobukti,
          pket :value
        },
        success: function (res) {
          alertify.success('Berhasil batal otorisasi');
          reloadData(nobukti);
        },
        error: function (err) {
          console.error(err);
          alertify.warning('Terjadi kesalahan, silakan refresh browser');
        }
      });
    },
    function () {
      console.log('Batal otorisasi dibatalkan');
      alertify.error("Action cancelled");
    }
  );
  dlgBatalOtorisasi.elements.root.classList.add('ajs-app-buttons', 'is-danger');
}


function submitKoreksiEdit () {
  let _token = $("#_token").val();

  if (!barangKoreksiEdit) {
    alertify.warning('Tidak ada data yang dipilih untuk koreksi.');
    return;
  }

  let nobukti = barangKoreksiEdit.NoBukti || barangKoreksiEdit.NOBUKTI;
  let urut    = barangKoreksiEdit.Urut || barangKoreksiEdit.URUT;

  let kodePerkiraan = ($('#KoreksiEditPerkiraan').val() || '').trim();
  let kodeCost      = ($('#KoreksiEditCosting').val() || '').trim();
  let kodeSubCost   = ($('#KoreksiEditSubCosting').val() || '').trim();

  if (!nobukti || !urut) {
    alertify.warning('Data item tidak lengkap (NoBukti/Urut).');
    return;
  }
  if (!kodePerkiraan) {
    alertify.warning('Perkiraan wajib diisi.');
    return;
  }

  // Field-nya sekarang bisa diketik langsung (lihat resolvePicker()), jadi ketikan
  // yang tidak pernah di-resolve ke kode yang benar (mis. diketik lalu modal-nya
  // dibatalkan) bisa lolos sampai sini. Divalidasi HANYA kalau cache picker untuk
  // field itu sudah pernah terisi di sesi ini (artinya field ini memang sempat
  // disentuh lewat picker) — field yang tidak pernah disentuh (nilai bawaan dari
  // buttonKoreksiEditItem() saat form dibuka) dipercaya begitu saja, sama seperti
  // dulu saat field-nya masih disabled.
  let invalidField = ['perkiraan', 'costing', 'subcosting'].find(function(key) {
    let code = { perkiraan: kodePerkiraan, costing: kodeCost, subcosting: kodeSubCost }[key];
    if (!code) return false;
    let list = pickerCachedList(key);
    if (!list) return false;
    let needle = code.toLowerCase();
    return !list.some(item => String(item[PICKERS[key].codeField] || '').trim().toLowerCase() === needle);
  });
  if (invalidField) {
    let label = { perkiraan: 'Perkiraan', costing: 'Costing', subcosting: 'Sub Costing' }[invalidField];
    alertify.warning('Kode ' + label + ' tidak dikenali, silakan pilih dari daftar.');
    return;
  }

  $.ajax({
    url: "{!! url('pembebananpemakaianspkoreksi') !!}",
    type: "post",
    data: {
      _token,
      nobukti,
      urut,
      kodeperkiraan: kodePerkiraan,
      kodecost: kodeCost,
      kodesubcost: kodeSubCost
    },
    success: function (res) {
      if (res && res.success) {
        $("#modalKoreksiEdit").modal("hide");
        refreshDataTableKoreksi(nobukti);
        reloadData(nobukti);
        alertify.success(res.message || 'Koreksi akun berhasil disimpan.');
      } else {
        alertify.warning(res.message || 'Koreksi gagal disimpan.');
      }
    },
    error: function (err) {
      console.log(err);
      const msg = err?.responseJSON?.message || 'Terjadi kesalahan, silakan refresh browser';
      alertify.warning(msg);
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
    url: "{!! url('pembebananpemakaiangetdetailpenerimaan') !!}",
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
          <td>${item.KodeBrg}</td>
          <td>${item.NamaBrg}</td>
          <td class="text-right">${parseFloat(item.Qnt).toLocaleString()}</td>
          <td class="text-center">${item.Satuan}</td>
          <td>${item.KodePerkiraan ?? ''}</td>
          <td>${item.namaPerkiraan ?? ''}</td>
          <td class="text-center">
            <button type="button" class="btn-action-sm btn-action-success" data-toggle="tooltip" title="Edit" onclick="buttonKoreksiEditItem(${i})"><i class="bi bi-pen"></i></button>
          </td>
        </tr>`;
      });

      $("#koreksiTableData").html(rowTable);

      // Isi Form Header
      $("#input_koreksi_nobukti").val(data.NoBukti);

      // Tanggal dari SQL Server berbentuk 'YYYY-MM-DD HH:MM:SS.mmm' — input type="date" hanya
      // menerima 'YYYY-MM-DD'.
      $("#input_koreksi_tanggal").val(String(data.Tanggal || '').substring(0, 10));
      $("#input_koreksi_gudang").val(data.gudang || '');

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
    url: "{!! url('pembebananpemakaiangetdetailpenerimaan') !!}",
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
          <td>${item.KodeBrg}</td>
          <td>${item.NamaBrg}</td>
          <td class="text-right">${parseFloat(item.Qnt).toLocaleString()}</td>
          <td class="text-center">${item.Satuan}</td>
          <td>${item.KodePerkiraan ?? ''}</td>
          <td>${item.namaPerkiraan ?? ''}</td>
        </tr>`;
      });

      $("#detailKoreksiTableData").html(rowTable);

      // Isi Header Detail
      const data = res[0];

      $("#input_detailkoreksi_nobukti").val(data.NoBukti);
      $("#input_detailkoreksi_tanggal").val(String(data.Tanggal || '').substring(0, 10));
      $("#input_detailkoreksi_gudang").val(data.gudang || '');

      $('.mainpage').hide();
      $('#page4').show();
    },
    error: function (err) {
      console.error('Error response:', err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}


function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: "{!! url('pembebananpemakaiandetailCetak') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: nobukti
      },
      success: function(res) {
        console.log(res,'zzzzz')

        dataPrint = res
        console.log(res[0])
        console.log(res[0][0])

        // console.log(res[0][0].IsOtorisasi1)

      }
    })

    let arrayDataPrint = []
    for (let i = 0; i < dataPrint.length; i+=7) {
      let tempArray = dataPrint.slice(i,i+7)
      arrayDataPrint.push(tempArray)
    }

    let printContent = ''
    let imageContent = document.getElementById(`imagecontainer`).innerHTML;
    let css = ''
    let hdr = ''
    let str= ''
    let ftr= ''
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
                    `+ imageContent +`
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
                  <h2 class="m-0 pb-2">BUKTI PEMAKAIAN INTERNAL ACC</h2>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">No</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+dataPrint[0].NoBukti+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Tanggal</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+tanggalOnly+`</div>
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
                `+printContent+`
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
                    <td class="text-center" style="width: 10%">HARGA SAT</td>
                    <td class="text-center" style="width: 10%">TOTAL</td>
                    <td class="text-center" style="width: 10%">COST</td>
                    <td class="text-center" style="width: 10%">COA</td>
                    <td class="text-center" style="width: 50%">COA</td>
                  </tr>
                </thead> `;

    let z = 0
    let tempPrintStr = ``
    // buat hitung grandtotal
    let grandTotal = 0;
    arrayDataPrint.forEach(group => {
      group.forEach(item => {
        if (item.Total) {
          grandTotal += parseFloat(item.Total) || 0;
        }
      });
    });
    // end
    tempPrintStr += `<html>
    <head>
      <title></title>
    </head>

    <body onload="window.print()">
      ` + css

      arrayDataPrint.forEach((item, i) => {
        console.log('arrayDataPrint' , i)
        if (i == 0) {

          tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; margin-top:5px">`
        // } else if ( i < 1) {
        //   tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; padding-top:15px; page-break-before: always">`
        } else {
          tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px;padding-top:7px; ">`
        }
        tempPrintStr += hdr
        tempPrintStr += `<tbody border="1">`;
        item.forEach((itemSub, j) => {
          tempPrintStr += ``
	console.log('oooo',itemSub);


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
         <td style="width: 10%; text-align: right;">
            ${itemSub.HPP
              ? Number(itemSub.HPP).toLocaleString('id-ID', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                })
              : ''}
         </td>
         <td style="width: 10%; text-align: right;">
            ${itemSub.Total
              ? Number(itemSub.Total).toLocaleString('id-ID', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                })
              : ''}
         </td>
         <td class="text-align: left"
               style="width: 10%;">${itemSub.KODESUBCOST ? parseFloat(itemSub.KODESUBCOST).toFixed(2) : ''}</td>
         <td class="text-align: left"
               style="width: 10%;">${itemSub.KodePerkiraan}</td>
         <td class="text-align: left"
               style="width: 50%;">${itemSub.NamaPerkiraan}</td>
         </tr>`;

           z++;

        });
        tempPrintStr +=`
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
          <h5>
          Total : ${grandTotal.toLocaleString('id-ID', {minimumFractionDigits: 2,maximumFractionDigits: 2
                })}
          </h5>
         </span>
         </div>


           <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: -15px ; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%"></td>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%">Dibuat Oleh</td>
               <td class="no-border text-center" style="width: 10%"></td>
             </tr>
             <tr style="height: 2.5rem">
               <td class="no-border">&nbsp;</td>
             </tr>

             <tr>
              <td class="no-border px-2">
               </td>
               <td class="no-border px-2">
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


      tempPrintStr +=  `</body></html>`



    w=window.open(' ')
    w.document.write(tempPrintStr)

    w.print()
    w.close()

  }

function buttonCloseForm () {
  // No. bukti dokumen yang sedang dibuka (Koreksi = #page3, Detail = #page4) — dibaca sebelum
  // halamannya disembunyikan, supaya daftar kembali ke halaman tempat baris itu berada.
  let nobuktiTerbuka = '';
  if ($('#page3').is(':visible')) {
    nobuktiTerbuka = $('#input_koreksi_nobukti').val();
  } else if ($('#page4').is(':visible')) {
    nobuktiTerbuka = $('#input_detailkoreksi_nobukti').val();
  }

  $('.mainpage').hide();
  // $('#page2').hide();
  $('#page1').show();
  reloadData(nobuktiTerbuka);
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
