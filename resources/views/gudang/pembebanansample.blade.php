@extends('newmasterTest')
@section('buttons')

@section('page-title', 'Pembebanan Sample')
@section('title', 'SML - Pembebanan Sample')

@endsection

@section('css')

<link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">

{{-- Port 1:1 ke UI SO -- @extends newmasterTest (bukan lagi gudang.newmaster). BBS
     Belum Otorisasi + Sudah Otorisasi digabung jadi satu tabel (#tabel) dengan kolom
     Status dan filter Status di toolbar, sama seperti merge di
     gudangreturpenyerahansample.blade.php/gudangpermintaansample.blade.php. #pageHome
     diberi class "po-list-page" supaya .data-table/#tabel dapat width:100% + warna
     tombol Aksi dari CSS terpusat. #pageForm diberi class "po-form-page" (lihat
     po-table-header.css) supaya tombol Close/Batal/Submit bergaya pil pastel, sama
     seperti ubahkemasanbarang.blade.php/permintaantransferbarang.blade.php -- JS
     doBrowseMaster/ajc-func-core.js milik halaman ini tidak diubah sama sekali, murni
     port tampilan. --}}
<style>
.status-badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .03em;
  white-space: nowrap;
}
.status-badge-sudah { color: #16a34a; border: 1px solid #cdebd7; background: #e7f7ed; }
.status-badge-belum { color: #b45309; border: 1px solid #fbe3bd; background: #fef3e0; }

{{-- Tabel item form Add/Edit/Detail (#tabelitem) -- bungkus kartu berbingkai + thead
     abu-abu, sama seperti #tabelitem di ubahkemasanbarang.blade.php. --}}
#pageForm .po-item-card {
  background: var(--white, #fff);
  border: 1.5px solid var(--border, #e5e7eb);
  border-radius: var(--radius, 12px);
  overflow: hidden;
}

#tabelitem thead th {
  background: #f8f9fb !important;
  color: #6b7280 !important;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: .04em;
  font-weight: 600;
  border-bottom: 1px solid #e7e9ee;
  border-top: none;
}
</style>
@endsection

@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id="pageHome" class="container-fluid po-list-page">
    	<div id="contentContainer" class="" >
    		<input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
        <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />

        <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
        <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS!!}" />
        <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
        <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />
        <input type="hidden" id="akses_isotorisasi1" value="{!! $akses->IsOtorisasi1 !!}" />
        <input type="hidden" id="akses_isbatal" value="{!! $akses->IsBatal !!}" />

        <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

        {{-- BBS Belum Otorisasi + Sudah Otorisasi digabung jadi satu tabel, dengan kolom
             Status dan filter Status di toolbar (client-side), sama seperti merge di
             gudangreturpenyerahansample.blade.php/gudangpermintaansample.blade.php. --}}
        <div class="card">
          <div class="card-body" style="padding:0;">
            <div class="po-toolbar">
              <input type="search" id="bbsSearch1" class="po-search-inp" placeholder="Cari data">
              <div class="po-len-wrap">
                <label for="bbsLen1">Tampilkan</label>
                <select id="bbsLen1" class="po-len-inp">
                  <option value="10">10</option>
                  <option value="25">25</option>
                  <option value="50">50</option>
                  <option value="100">100</option>
                  <option value="-1">Semua</option>
                </select>
              </div>
              <button class="po-btn-filter" type="button" onclick="$('#modalFilterBBS').modal('show')">
                <i class="bi bi-funnel"></i> Filter
              </button>
              <div class="po-toolbar-act">
                <button id="AddVisibility" class="btn btn-chip-biru" onclick="buttonAdd()">Tambah</button>
              </div>
            </div>

            <div class="modal fade rt-filter" id="modalFilterBBS">
              <div class="modal-dialog modal-md">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-funnel"></i> Filter Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterBBS').modal('hide')"><span aria-hidden="true">&times;</span></button>
                  </div>
                  <div class="modal-body">
                    <div class="rt-section">
                      <div class="rt-group-label">Status</div>
                      <div>
                        <label class="rt-field-label" for="bbsStatus">Status Otorisasi</label>
                        <select class="rt-native" id="bbsStatus">
                          <option value="" selected>Semua Status</option>
                          <option value="0">Belum Otorisasi</option>
                          <option value="1">Sudah Otorisasi</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="rt-reset-link" onclick="$('#bbsStatus').val('')">Reset semua</button>
                    <div class="rt-footer-buttons">
                      <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal" onclick="$('#modalFilterBBS').modal('hide')">Batal</button>
                      <button type="button" class="rt-btn rt-btn-primary" onclick="bbsFilterChanged(); $('#modalFilterBBS').modal('hide');">Terapkan</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div id="rtBarTabel"></div>
            <table id="tabel" class="data-table">
              <thead style="white-space:nowrap;"></thead>
              <tbody id="tabel_data" class="text-left"></tbody>
            </table>
            <div class="po-rt-hint">
              <i class="bi bi-info-circle"></i>
              Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom
              untuk menyembunyikan kolom atau mengatur jumlah desimal.
            </div>
          </div>
        </div>
    	</div>
</div>

<div id="pageForm" class="container-fluid po-form-page" style="display: none" >
      <div class="row">
        <div class="col-6 text-left">
        </div>
        <div class="col-6 text-right" id="contentContainer">
          <button type="button" class="btn btn-danger btn-pill-primary btn-action-danger" onclick="buttonCloseForm()">Close</button>
        </div>
      </div>

      <div id="modalBodyAddMain" class="">
        <div class="modal-body" style="margin-top:-60px;">
          <div class="row">

            <input type="hidden" class="form-control" id="input_nourut">

            <div class="col-md-12">
              <div class="row">
                <div class="col-md-2">
                  <div class="form-group">
                    <label>No Bukti</label>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <input type="text" class="form-control text-center" id="input_nobukti" placeholder="" disabled>
                  </div>
                </div>

                <div class="col-md-1">
                  <div class="form-group">
                    <label>Tanggal</label>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <input type="date" class="form-control text-center" id="input_tanggal" value="{!! date('Y-m-d') !!}" disabled>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-12">
              <div class="row">
                <div class="col-2">
                  <div class="form-group">
                    <label>No. Serah Sample</label>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="input-group form-group">
                    <input type="text" class="form-control lockableModeDetail" id="input_noserahsample" onkeypress="doBrowseDirectFilter('NoSerahSample', '{!! $noserahsample !!}', 'input_noserahsample')" disabled>
                    <button id="buttonbrowse_noserahsample" onclick="doBrowseMaster('NoSerahSample', '{!! $noserahsample !!}')" class="btn btn-primary btn-sm text-right lockableHeader lockableModeDetail">
                      <i class="bi bi-plus"></i>
                    </button>
                  </div>
                </div>

                <div class="col-1">
                  <div class="form-group">
                    <label>Sales</label>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="input-group form-group">
                    <input type="text" class="form-control" id="input_namasales" disabled>
                    <input type="hidden" id="input_sales">
                    {{-- <button id="buttonbrowse_sales" onclick="doBrowseMaster('Sales', '{!! $sales !!}')" class="btn btn-primary btn-sm text-right lockableHeader lockableModeDetail">
                      <i class="bi bi-plus"></i>
                    </button> --}}
                  </div>
                </div>

                <div class="col-1">
                  <div class="form-group">
                    <label>Customer</label>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="input-group form-group">
                    <input type="text" class="form-control" id="input_namacustsupp" disabled>
                    <input type="hidden" id="input_customer">
                    {{-- <button id="buttonbrowse_customer" onclick="doBrowseMaster('Customer', '{!! $customer !!}')" class="btn btn-primary btn-sm text-right lockableHeader lockableModeDetail">
                      <i class="bi bi-plus"></i>
                    </button> --}}
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="showhidemodalbodyaddmain container-fluid" id="modalBodyAddMainItems">
        <div class="container-fluid mt-4">
          <div class="po-item-card">
            <table id="tabelitem" class="table table-bordered table-hover table-striped table-responsive-lg">
              <thead id="tabelitem_header">
                <tr>
                  <th  style="padding: 4px 12px;" scope="col" colspan="3"></th>
                  <th  style="padding: 4px 12px;" scope="col" colspan="2">Satuan</th>
                  <th  style="padding: 4px 12px;" scope="col"></th>
                </tr>
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Gudang</th>
                  <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Qty</th>
                  <th style="padding: 4px 12px;" scope="col">Sat</th>
                  <th style="padding: 4px 12px;" scope="col">Actions</th>
                </tr>
              </thead>
              <tbody id="tabelitem_data" class="text-left lockableModeDetail" >
              </tbody>
            </table>
          </div>
        </div>

        <div class="row ">
          <div class="col-md-12 mt-2 text-right" id="contentContainer">
            <button type="button" class="btn btn-primary btn-pill-primary btn-action-primary hideableModeDetail" onclick="buttonItemAdd()">+ Tambah Item</button>
          </div>
        </div>

        <div id="formItem" class="container-fluid showhide">
          <hr/>
          <div class="row">
            <div class="col-4">
              <h4 id="formItem_labelAdd" style="margin-left:-35px;">Add Item</h4>
              <h4 id="formItem_labelEdit" style="margin-left:-35px;">Edit Item</h4>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="row">

                <!-- START OF ITEM Kode Barang, Nama Barang -->
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
                        <div class="col-md-4">
                          <div class="input-group form-group">
                            <input type="text" class="form-control" id="inputitem_kodebrg" disabled>
                            <button onclick="doBrowseMaster('Barang', '{!! $barang !!}')" id="btnitem_kodebrg" class="btn btn-primary btn-sm text-right lockableItemModeEdit">
                              <i class="bi bi-plus"></i>
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
                            <input type="text" class="form-control" id="inputitem_namabrg" disabled>
                            <input type="text" class="form-control" id="inputitem_urutserahsample" hidden>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
                <!-- END OF ITEM Gudang, Kode Barang, Nama Barang -->


                <!-- START OF ITEM Gudang, Quantity, Satuan -->
                <div class="col-md-4">
                  <div class="row">
                    <div class="col-md-12">

                      <div class="row">
                        <div class="col-3">
                          <div class="form-group">
                            <label>Gudang</label>
                          </div>
                        </div>
                        <div class="col-md-9">
                          <div class="input-group form-group">
                            <input type="text" class="form-control" id="inputitem_gudang" disabled>
                            <button onclick="doBrowseMaster('Gudang', '{!! $gudang !!}')" class="btn btn-primary btn-sm text-right">
                              <i class="bi bi-plus"></i>
                            </button>
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group">
                            <label>Quantity</label>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="input-group form-group">
                            <input type="number" class="form-control" id="inputitem_quantity">
                          </div>
                        </div>

                        <div class="col-md-3">
                          <div class="form-group">
                            <label>Satuan</label>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="input-group form-group">
                            <select id="inputitem_satuan" class="form-control form-select-lg mb-3 text-center" aria-label=".form-select-lg example" disabled>
                            </select>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
                <!-- END OF ITEM Quantity, Satuan, Keterangan -->

              </div>
            </div>
          </div>

          <div class="row mt-2">
            <div class="col-md-12 text-right" id="contentContainer">
              <button type="button" class="btn btn-danger btn-pill-primary btn-action-danger" onclick="closeFormItem()">Batal</button>
              <button type="button" id="buttonSubmitItem" class="btn btn-primary btn-pill-primary btn-action-primary" onclick="submitItem()">Submit</button>
            </div>

          </div>
        </div>
      </div>
</div>

@include('gudang.modalbrowsemaster')

@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/ajc-func-core.js') !!}"></script>
<script src="{!! URL::asset('js/ajc-browsemaster.js') !!}"></script>
<script>
  const BASE_URL = "{{ url('/') }}";

  const g_tipeformNone = "", g_tipeformAdd = "add", g_tipeformEdit = "edit", g_tipeformDetail = "detail";
  var   gtipeform = g_tipeformNone;

  const g_tipeformitemNone = "", g_tipeformitemAdd = "add", g_tipeformitemEdit = "edit",
        g_tipeformitemDelete = "delete";
  var   gtipeformitem = g_tipeformitemNone;

  const g_modalNone = "";
  var   gmodemodal = g_modalNone;

  var dataItem = [];
  var dataBrowse = [];

  {{-- Polyfill helper doX/cekX yang dipanggil halaman ini tapi tidak ada di
       ajc-func-core.js (cuma messageRequired/nullToEmpty yang ada di sana) --
       disalin persis dari ubahkemasanbarang.blade.php/permintaantransferbarang.blade.php,
       dijaga guard typeof supaya tidak dobel kalau suatu saat ajc-func-core.js
       menambahkannya secara global. Tanpa blok ini halaman tidak jalan sama sekali
       (Add/Edit/Detail/Otorisasi semua manggil fungsi yang undefined). --}}
  if (typeof doSetFormatDate !== 'function') {
    window.doSetFormatDate = function (dateVal, sep) {
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

  if (typeof doCekAkses !== 'function') {
    window.doCekAkses = function (inputId) {
      let v = $('#' + inputId).val();
      if (!Number(v)) {
        alertify.warning('No access');
        return false;
      }
      return true;
    };
  }

  if (typeof doCekOtorisasi !== 'function') {
    window.doCekOtorisasi = function (nobukti, urlName) {
      let boleh = true;
      $.ajax({
        url: "{!! url('') !!}/" + urlName,
        type: 'post',
        async: false,
        data: { _token: $('#_token').val(), nobukti: nobukti },
        success: function (res) {
          let row = Array.isArray(res) ? res[0] : res;
          let sudahOto = row ? Number(row.isOtorisasi1 ?? row.IsOtorisasi1 ?? 0) : 0;
          if (sudahOto === 1) {
            alertify.warning('Sudah diotorisasi, tidak bisa diedit');
            boleh = false;
          }
        },
        error: function () {
          alertify.warning('Terjadi kesalahan. Silakan refresh browser.');
          boleh = false;
        }
      });
      return boleh;
    };
  }

  if (typeof doUnlockHeader !== 'function') {
    window.doUnlockHeader = function () {
      $('.lockableHeader').prop('disabled', false);
      $('.lockableModeDetail').prop('disabled', false);
      $('.hideableModeDetail').show();
    };
  }

  if (typeof doLockModeDetail !== 'function') {
    window.doLockModeDetail = function () {
      $('.lockableHeader').prop('disabled', true);
      $('.lockableModeDetail').prop('disabled', true);
      $('.hideableModeDetail').hide();
    };
  }

  if (typeof doUnlockModeEdit !== 'function') {
    window.doUnlockModeEdit = function () {
      $('.lockableItemModeEdit').prop('disabled', false);
      $('.hideableModeDetail').show();
    };
  }

  if (typeof doUnlockModeDetail !== 'function') {
    window.doUnlockModeDetail = function () {
      $('.lockableModeDetail').prop('disabled', false);
      $('.hideableModeDetail').show();
    };
  }

  if (typeof doLockHeader !== 'function') {
    window.doLockHeader = function () {
      $('.lockableHeader').prop('disabled', true);
    };
  }

  if (typeof doLockModeEdit !== 'function') {
    window.doLockModeEdit = function () {
      $('.lockableItemModeEdit').prop('disabled', true);
    };
  }

  if (typeof doSetEmptyTable !== 'function') {
    window.doSetEmptyTable = function (colspan, message) {
      return '<tr><td colspan="' + colspan + '" class="text-center text-muted">' + message + '</td></tr>';
    };
  }

  if (typeof formatCurrency !== 'function') {
    window.formatCurrency = function (v) {
      let n = Number(v) || 0;
      return n.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };
  }

  if (typeof cekNotEmpty !== 'function') {
    window.cekNotEmpty = function (inputId) {
      let v = $('#' + inputId).val();
      return v !== undefined && v !== null && String(v).trim() !== '';
    };
  }

  if (typeof doCekPeriode !== 'function') {
    window.doCekPeriode = function (bulanId, tahunId, tanggalId) {
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
    window.doGenerateNoBukti = function (kode) {
      let _token = $('#_token').val();
      let result = { Nobukti: '', Nourut: '' };
      $.ajax({
        url: "{!! url('spnobukti') !!}",
        type: 'post',
        async: false,
        data: { _token, kode },
        success: function (res) {
          if (res && res[0]) {
            result.Nobukti = res[0].Nobukti;
            result.Nourut = res[0].Nourut;
          }
        },
        error: function (err) {
          console.log(err);
          alertify.warning('Terjadi kesalahan. Silakan refresh browser.');
        }
      });
      return result;
    };
  }

  {{-- BBS Belum + Sudah Otorisasi digabung jadi satu tabel (#tabel), dengan kolom Status
       dan filter Status di toolbar, sama seperti engine bbs/rs/gp/ps di
       gudangreturpenyerahansample.blade.php/gudangpermintaansample.blade.php.

       Judul kolom interaktif (geser urutan/sembunyikan lewat window.ReportTable) --
       port 1:1 dari pola opCart/opDefaultCart milik perintahopname.blade.php (juga
       satu tabel saja, bukan dua/tiga seperti gudangpermintaansample). Ketinggalan
       waktu port awal (halaman ini masih thead statis, tanpa #rtBarTabel/report-table.js
       sama sekali), baru ditambah di sini. --}}
  let bbsCart = []
  const BBS_HREF = 'pembebanansample'
  const BBS_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date', 3 : 'bool' }
  const BBS_TIPE_KODE = { varchar : 0, float : 1, date : 2, bool : 3 }

  function bbsPickCI (row, key) {
    if (!row) { return undefined; }
    if (row[key] !== undefined) { return row[key]; }
    let lower = key.toLowerCase();
    for (let k in row) {
      if (k.toLowerCase() === lower) { return row[k]; }
    }
    return undefined;
  }

  function bbsDefaultCart () {
    return [
      ['NoBukti',      'No. Bukti',     1, 'varchar', 0, 0],
      ['Tanggal',      'Tanggal',       1, 'date',    0, 0],
      ['KodeCustSupp', 'Kode Cust',     1, 'varchar', 0, 0],
      ['NAMACUSTSUPP', 'Nama Customer', 1, 'varchar', 0, 0],
      ['IDUSER',       'User',          1, 'varchar', 0, 0],
      ['IsOtorisasi1', 'Status',        1, 'bool',    0, 0],
      ['OtoUser1',     'User Oto',      1, 'varchar', 0, 0],
      ['TglOto1',      'Tgl Oto',       1, 'date',    0, 0],
    ]
  }

  function bbsBuatCart (headers, values, isnumerics, isshowns, desimals) {
    headers = headers || []
    let cart = []
    headers.forEach((h, i) => {
      let tipe = Number(isnumerics[i]) || 0
      let des = (desimals && desimals[i] !== undefined && desimals[i] !== null && desimals[i] !== '')
        ? Number(desimals[i])
        : (tipe === 1 ? 2 : 0)
      cart.push([
        values[i],
        h,
        Number(isshowns[i]) === 1 ? 1 : 0,
        BBS_TIPE_NAMA[tipe] || 'varchar',
        0,
        isNaN(des) ? 0 : des,
      ])
    });
    return cart
  }

  window.g_href = BBS_HREF
  window.g_modeReport = 1
  window.gcart_header = []

  window.doSimpanHeader = function (href, mode) {
    let header = [], value = [], isnumber = [], isshown = [], desimal = []
    bbsCart.forEach((c) => {
      header.push(c[1])
      value.push(c[0])
      isnumber.push(BBS_TIPE_KODE[c[3]] ?? 0)
      isshown.push(Number(c[2]) === 1 ? 1 : 0)
      desimal.push(Number(c[5]) || 0)
    });

    $.ajax({
      url   : "{!! url('saveheadertable') !!}",
      type  : "post",
      async : false,
      data  : {
        _token   : $("#_token").val(),
        header   : JSON.stringify(header),
        isnumber : JSON.stringify(isnumber),
        tipe     : JSON.stringify(desimal),
        value    : JSON.stringify(value),
        isshown  : JSON.stringify(isshown),
        href     : BBS_HREF,
        urut     : 1
      },
      error : function (err) {
        console.log(err)
        alertify.warning('Gagal menyimpan pengaturan kolom')
      }
    })
  }

  window.doSetHeader = function (mode, reset) {
    $.ajax({
      url   : "{!! url('getheadertable') !!}",
      type  : "post",
      async : false,
      data  : {
        _token : $("#_token").val(),
        href   : BBS_HREF,
        urut   : 1,
        reset  : reset ? 1 : 0
      },
      success : function (res) {
        if (!reset && res && res.headertableheader && res.headertableheader.length) {
          bbsCart = bbsBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal || [])
        } else {
          bbsCart = bbsDefaultCart()
          window.gcart_header = bbsCart
          window.doSimpanHeader(BBS_HREF, 1)
        }
        window.gcart_header = bbsCart
      },
      error : function (err) {
        console.log(err)
        alertify.warning(reset ? 'Gagal mengembalikan kolom ke tampilan default' : 'Gagal memuat pengaturan kolom')
        bbsCart = bbsDefaultCart()
        window.gcart_header = bbsCart
      }
    })
  }

  let bbsRtSudahInit = false
  function bbsInitReportTableSekali () {
    if (bbsRtSudahInit || typeof ReportTable === 'undefined') { return }
    bbsRtSudahInit = true

    ReportTable.init({ table : '#tabel', bar : '#rtBarTabel', onChange : reinitTabel })

    let bbsGuardUlangKlik = false
    let thead = document.querySelector('#tabel thead')
    if (thead) {
      thead.addEventListener('click', function (e) {
        if (bbsGuardUlangKlik) { return }
        let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
        if (!interaktif) { return }
        e.stopPropagation()
        e.preventDefault()
        bbsGuardUlangKlik = true
        let ulang = new MouseEvent('click', { bubbles: false, cancelable: true, view: window })
        Object.defineProperty(ulang, 'target', { value: interaktif, configurable: true })
        thead.dispatchEvent(ulang)
        bbsGuardUlangKlik = false
      }, true)
    }
  }

  function tulisTheadHeaderBBS (tableSel, cols) {
    let thead = document.querySelector(tableSel + ' thead')
    if (!thead || !window.ReportTable) { return; }
    let headRowHtml = ReportTable.headHtml(cols)
      .replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>');
    thead.setAttribute('style', 'white-space:nowrap;');
    thead.innerHTML = headRowHtml;
  }

  function bbsValueCell (row, col) {
    let raw = bbsPickCI(row, col[0]);
    let type = col[3];

    {{-- Status (IsOtorisasi1) -- badge "Sudah/Belum Otorisasi", bukan ikon check/x bool
         biasa. --}}
    if (col[0] === 'IsOtorisasi1') {
      return Number(raw) === 1
        ? '<td class="text-center"><span class="status-badge status-badge-sudah">Sudah Otorisasi</span></td>'
        : '<td class="text-center"><span class="status-badge status-badge-belum">Belum Otorisasi</span></td>';
    }
    if (type === 'date') {
      if (!raw) { return '<td></td>'; }
      return '<td>' + doSetFormatDate(raw, '/') + '</td>';
    }
    if (type === 'float') {
      let dp = Number(col[5]) || 0;
      let n = (raw !== undefined && raw !== null && raw !== '') ? Number(raw) : 0;
      return '<td class="text-right">' + n.toLocaleString('id-ID', { minimumFractionDigits: dp, maximumFractionDigits: dp }) + '</td>';
    }
    if (type === 'bool') {
      return Number(raw)
        ? '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>'
        : '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>';
    }
    return '<td>' + (raw !== undefined && raw !== null ? raw : '') + '</td>';
  }

  function bbsTabelActionsCell (row) {
    let nobukti = bbsPickCI(row, 'NoBukti');
    let sudah = Number(bbsPickCI(row, 'IsOtorisasi1')) === 1;
    let html = '<td class="text-center">';
    html += '<button class="btn btn-warning btn-sm" type="button" title="Details" onclick="buttonDetail(\'' + nobukti + '\')"><i class="bi bi-info"></i></button>';
    if (sudah) {
      html += '<button class="btn btn-danger btn-sm" type="button" title="Batal Otorisasi" onclick="buttonBatalOtorisasi(\'' + nobukti + '\')"><i class="bi bi-key-fill"></i></button>';
      html += '<button class="btn btn-primary btn-sm" type="button" title="Print" onclick="submitPrint(\'' + nobukti + '\')"><i class="bi bi-printer"></i></button>';
    } else {
      html += '<button class="btn btn-success btn-sm" type="button" title="Edit" onclick="buttonEdit(\'' + nobukti + '\')"><i class="bi bi-pencil-fill"></i></button>';
      html += '<button class="btn btn-primary btn-sm" type="button" title="Otorisasi" onclick="buttonOtorisasi(\'' + nobukti + '\')"><i class="bi bi-key-fill"></i></button>';
    }
    html += '</td>';
    return html;
  }

  function bbsFilterRows (rows) {
    let status = $('#bbsStatus').val();
    return (rows || []).filter(function (row) {
      if (status !== '' && String(Number(bbsPickCI(row, 'IsOtorisasi1'))) !== status) { return false; }
      return true;
    });
  }

  function bbsFilterChanged () {
    reinitTabel();
  }

  function renderTabelRows (rows) {
    window.gcart_header = bbsCart
    let cols = (bbsCart.length ? bbsCart : gcart_header).filter(function (c) { return c[2] === 1; });
    let html = '';
    bbsFilterRows(rows).forEach(function (row) {
      html += '<tr>' + bbsTabelActionsCell(row);
      cols.forEach(function (col) { html += bbsValueCell(row, col); });
      html += '</tr>';
    });
    {{-- Baris placeholder colspan buatan sendiri (doSetEmptyTable()) TIDAK dipakai di sini
         -- DataTables menghitung <td> fisik per baris (bukan hasil visual colspan-nya),
         jadi baris 1-sel dianggap "kurang kolom" dibanding jumlah kolom aktif di thead dan
         bikin DataTables crash saat indexing ("Cannot set properties of undefined (setting
         '_DT_CellIndex')"). Serahkan status kosong ke opsi language.emptyTable bawaan
         DataTables (lihat reinitTabel()) yang memang didesain aman untuk kasus ini. --}}
    document.getElementById('tabel_data').innerHTML = html;
    tulisTheadHeaderBBS('#tabel', cols);
  }

  let lastTabelRows = [];
  let bbsPanjangHalaman = 10;

  function bbsIkatSearch () {
    let input = document.getElementById('bbsSearch1');
    if (!input || input.dataset.rtBound) { return; }
    input.dataset.rtBound = '1';

    let timer = null;
    input.addEventListener('input', function () {
      let nilai = input.value;
      if (timer) { clearTimeout(timer); }
      timer = setTimeout(function () {
        if ($.fn.DataTable.isDataTable('#tabel')) {
          $('#tabel').DataTable().search(nilai).draw();
        }
      }, 400);
    });
  }

  function bbsIkatPanjangHalaman () {
    let sel = document.getElementById('bbsLen1');
    if (!sel || sel.dataset.rtBound) { return; }
    sel.dataset.rtBound = '1';
    sel.value = String(bbsPanjangHalaman);

    sel.addEventListener('change', function () {
      let n = Number(sel.value);
      bbsPanjangHalaman = (n === -1 || n > 0) ? n : 10;
      if ($.fn.DataTable.isDataTable('#tabel')) {
        $('#tabel').DataTable().page.len(bbsPanjangHalaman).draw();
      }
    });
  }

  const BBS_DOM_STRING = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>";

  function reinitTabel () {
    try {
      if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().destroy(); }
      renderTabelRows(lastTabelRows);
      $('#tabel').DataTable({
        dom: BBS_DOM_STRING,
        lengthChange: false,
        pageLength: bbsPanjangHalaman,
        paging: true,
        ordering: false,
        language: { emptyTable: 'Tidak ada transaksi', zeroRecords: 'Tidak ada data yang cocok dengan pencarian' }
      });
      bbsIkatSearch();
      bbsIkatPanjangHalaman();
    } catch (e) {
      console.error('reinitTabel failed:', e);
      alertify.error('Gagal memperbarui tabel: ' + e.message);
    }
  }

  $(document).ready(function(){
    window.doSetHeader(1, false);
    lastTabelRows = [].concat(@json($listBBS), @json($listSdhOto));
    reinitTabel();

    bbsInitReportTableSekali();
  });

  function loadAll() {
    $.ajax({
      url: "{!! url('bbsloadall') !!}",
      type: "get",
      async: false,
      data: {
      },
      success: function(res) {
        lastTabelRows = [].concat(res.listBBS, res.listSdhOto);
        reinitTabel();
    }});
  }

  function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: "{!! url('bbsdetailCetak') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
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
    let tanggalOnly = dataPrint[0].TANGGAL.split(' ')[0];

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
                  <div class="pb-1" style="width: 100%">Dari : PT. ${dataPrint[0].namaCustSupp ?? '-'}</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
              </div>


              <div style="width: 38%">
                <div style="display: flex; width: 100%">
                  <h2 class="m-0 pb-2">BEBAN SAMPLE</h2>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">No Bukti</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+dataPrint[0].NOBUKTI+`</div>
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
                  overflow: hidden;">`+printContent+`
              </div>
            </div>
      <table

                class="detail-spb-table"
                style="width: 100%; height: 225px; max-height: 225px;font-family: sans-serif;  display: table;
                font-size: 10px">
                <thead>
                  <tr>
                    <td class="text-center" style="width: 2%">No.</td>
                    <td class="text-center" style="width: 50%">NAMA BARANG</td>
                    <td class="text-center" style="width: 20%">QUANTITY</td>
                    <td class="text-center" style="width: 20%">SATUAN</td>
                    <td class="text-center" style="width: 20%">HARGA</td>
                  </tr>
                </thead> `;

    let z = 0
    let tempPrintStr = ``
    // buat hitung grandtotal
    let grandTotal = 0;
    arrayDataPrint.forEach(group => {
      group.forEach(item => {
        if (item.JumlahHRHPP) {
          grandTotal += parseFloat(item.JumlahHRHPP) || 0;
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



         tempPrintStr += `
         <tr>
         <td class="text-align: center"
               style="width: 2%; ">${z+1}</td>
         <td class="text-align: left"
               style="width: 50%;">${itemSub.NAMABRG}</td>
         <td class="text-align: left"
               style="width: 20%;">${itemSub.Qnt ? parseFloat(itemSub.Qnt).toFixed(2) : ''}</td>
         <td class="text-align: text-right"
               style="width: 20%;  ">${itemSub.Sat}</td>
         <td class="text-align: text-right"
               style="width: 20%;  ">${itemSub.JumlahHRHPP ? parseFloat(itemSub.JumlahHRHPP).toFixed(2) : ''}</td>
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
         
         <div style="width:100%; display:flex; font-weight:bold; margin-top:5px;">

            <div style="width:80%; text-align:right; padding-right:10px;">
              Total :
            </div>

            <div style="width:20%; text-align:right;">
              ${grandTotal.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              })}
            </div>

          </div>
         
         </div>


           <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: 15px ; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%"></td>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%">Mengetahui</td>
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

  function closeFormItem() {
    $('.showhide').hide();
    doUnlockHeader();
    doUnlockModeEdit();
    $("#buttonbrowse_noserahsample").prop("disabled", dataItem.length > 0);
    $("#buttonbrowse_sales").prop("disabled", dataItem.length > 0);
    $("#buttonbrowse_customer").prop("disabled", dataItem.length > 0);
  }

  function refreshForm(_nobukti = "") {
    let rowTable = "";
    dataItem = [];

    if (_nobukti == "") {
      let cHide = (gtipeform == g_tipeformDetail) ? 1 : 0;
      rowTable += doSetEmptyTable(10 - cHide, "Belum ada barang");
    } else {
      let _token  = $("#_token").val();
      $.ajax({
        url: "{!! url('bbsgetdetail') !!}",
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
          } else {
            let dataHeader = res.header[0];
            dataItem = res.list;

            dataItem.forEach((item, i) => {
              rowTable += `<tr>
                <td>${nullToEmpty(item.KODEGDG)}</td>
                <td>${nullToEmpty(item.KODEBRG)}</td>
                <td>${nullToEmpty(item.NAMABRG)}</td>
                <td class="text-right">${item.Qnt ? formatCurrency(item.Qnt) : '0.00'}</td>
                <td>${nullToEmpty(item.Sat)}</td>

                ${gtipeform == g_tipeformDetail ? `` :
                `<td class="text-center">
                  <button class="btn btn-success btn-sm" type="button" onclick="buttonItemEdit(${i})"><i class="bi bi-pen"></i></button>
                  <button class="btn btn-danger btn-sm" type="button" onclick="buttonItemDelete(${i})"><i class="bi bi-trash"></i></button>
                </td>`}
              </tr>`;
            });
            

            $("#input_nobukti").val(dataHeader.NOBUKTI);
            $("#input_nourut").val(dataHeader.nOURUT);
            $("#input_tanggal").val(doSetFormatDate(dataHeader.TANGGAL, "-"));
            $("#input_noserahsample").val(dataHeader.NoRSerahSample);
            $("#input_sales").val(dataHeader.KodeSLS);
            $("#input_namasales").val(dataHeader.NamaSLS);
            $("#input_customer").val(dataHeader.KodeCustSupp);
            $("#input_namacustsupp").val(dataHeader.NamaCustSupp);

            dataBrowse['noserahsample']  = dataHeader.NoRSerahSample;
            dataBrowse['sales']  = dataHeader.KodeSLS;
            dataBrowse['customer']  = dataHeader.KodeCustSupp;
          }
        },
        error: function (err) {
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
      <th  style="padding: 4px 12px;" scope="col" colspan="3"></th>
      <th  style="padding: 4px 12px;" scope="col" colspan="2">Satuan</th>
      ${gtipeform == g_tipeformDetail ? `` : `<th  style="padding: 4px 12px;" scope="col"></th>`}
    </tr>
    <tr>
      <th style="padding: 4px 12px;" scope="col">Gudang</th>
      <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
      <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
      <th style="padding: 4px 12px;" scope="col">Qty</th>
      <th style="padding: 4px 12px;" scope="col">Sat</th>
      ${gtipeform == g_tipeformDetail ? `` : `<th style="padding: 4px 12px;" scope="col">Actions</th>`}
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
  }

  function buttonAdd() {
    if (!doCekAkses("akses_istambah")) return;
    if (!doCekPeriode("periode_bulan", "periode_tahun", "input_tanggal")) return;

    gtipeform = g_tipeformAdd;
    $('.showhide').hide();

    cleanFormHeader();
    refreshForm();
    doUnlockHeader();
    
    let nb = doGenerateNoBukti("BBS");
    $("#input_nobukti").val(nb.Nobukti);
    $("#input_nourut").val(nb.Nourut);

    dataBrowse['noserahsample']  = "";
    dataBrowse['sales']  = "";
    dataBrowse['customer']  = "";

    $('#pageHome').hide();
    $('#pageForm').show();
  }

  function buttonEdit(_nb) {
    if (!doCekAkses("akses_iskoreksi")) return;
    if (!doCekOtorisasi(_nb, "bbscekotorisasi")) return;

    gtipeform = g_tipeformEdit;
    $('.showhide').hide();

    refreshForm(_nb);
    doUnlockHeader();
    $("#buttonbrowse_noserahsample").prop("disabled", dataItem.length > 0);
    $("#input_noserahsample").prop("disabled", dataItem.length > 0);
    $("#buttonbrowse_sales").prop("disabled", dataItem.length > 0);
    $("#buttonbrowse_customer").prop("disabled", dataItem.length > 0);

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

    let _token = $("#_token").val();

    $.ajax({
      url: "{!! url('bbsupdateotorisasi') !!}",
      type: "post",
      async: false,
      data: { _token, nobukti: _nb },
      success: function (res) {
        if (res > 0) {
          alertify.success('Berhasil otorisasi');
          loadAll();
        } else {
          alertify.warning('Gagal otorisasi');
        }
      },
      error: function (err) {
        console.log(err);
        alertify.warning('Terjadi kesalahan. Silakan refresh browser.');
      }
    });
  }


  function buttonBatalOtorisasi (nobukti) {
  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

alertify.prompt("Masukkan keterangan batal otorisasi nomor   " + nobukti, "",
  function(evt, value) {
    // alertify.success("You entered: " + value);
    let xpket = value;

     if (xpket==''){
          alertify.warning('Keterangan harus diisi.');
          $.abort();
        }
      let _token = $("#_token").val();

      $.ajax({
        url: "{!! url('bbsupdatebatalotorisasi') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          nobukti,
          pket :value
        },
        success: function (res) {
          alertify.success('Berhasil batal otorisasi');
          loadAll();
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
}

  function cleanFormHeader() {
    $("#input_nobukti").val("");
    $("#input_nourut").val("");
    $("#input_noserahsample").val("");
    $("#input_sales").val("");
    $("#input_namasales").val("");
    $("#input_customer").val("");
    $("#input_namacustsupp").val("");
  }

  function buttonItemAdd() {
    if (!cekNotEmpty("input_noserahsample")) {
      return alertify.warning(messageRequired("No. Serah Sample"));
    }

    if (dataBrowse['noserahsample'] !== $("#input_noserahsample").val()) {
      return alertify.warning("Data No. Serah Sample tidak sesuai");
    }

    gtipeformitem = g_tipeformitemAdd;
    $('.showhide').hide();
    $('#formItem_labelAdd').show();
    $('#formItem_labelEdit').hide();

    doLockHeader();
    cleanFormItem();

    dataBrowse['barang']  = "";
    dataBrowse['gudang']  = "";

    $('#formItem').show();

    $("#btnitem_kodebrg").prop("disabled", false);
    $("#inputitem_satuan").prop("disabled", false);

    document.getElementById("btnitem_kodebrg").scrollIntoView();
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
    $("#inputitem_urut").val(item.URUT);
    $("#inputitem_gudang").val(item.KODEGDG);
    dataBrowse['gudang'] = item.KODEGDG;

    if (item.NamaBrg != "") {
      showSatuanBarang(item.Nosat, item.KODEBRG, item.NAMABRG,
        item.Qnt, item.UrutRSerahSample,
        item.brgSat1, item.brgSat2, item.brgSat3,
        item.brgIsi1, item.brgIsi2, item.brgIsi3);
    }

    $('#formItem').show();

    document.getElementById("inputitem_kodebrg").scrollIntoView();
  }

  function buttonItemDelete(_urut) {
    if (!doCekAkses("akses_ishapus")) return;

    alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + nullToEmpty(dataItem[_urut].NAMABRG) + ' ?',
      function() {
        gtipeformitem = g_tipeformitemDelete;
        $("#inputitem_urut").val(dataItem[_urut].URUT);
        submitItem();
      },
      function(){
        console.log('Penghapusan dibatalkan')
      }
    );
  }

  function cleanFormItem() {
    $("#inputitem_urut").val(0);
    $("#inputitem_kodebrg").val("");
    $("#inputitem_namabrg").val("");
    $("#inputitem_urutserahsample").val("");
    $("#inputitem_gudang").val("");
    $("#inputitem_quantity").val(0);
    $('#inputitem_satuan').empty();
    $('#inputitem_satuan').prop("disabled", true);
  }

  function buttonBrowsePickNoSerahSample(_nb, _kodeSls, _namaSls, _kodeCust, _namaCust) {
  $("#input_noserahsample").val(_nb);

  $("#input_namasales").val(_namaSls);
  $("#input_namacustsupp").val(_namaCust);

  $("#input_sales").val(_kodeSls);
  $("#input_customer").val(_kodeCust);

  dataBrowse['noserahsample'] = _nb;
  dataBrowse['sales'] = _kodeSls;
  dataBrowse['customer'] = _kodeCust;
}


  // function buttonBrowsePickNoSerahSample(_nb) {
  //   $("#input_noserahsample").val(_nb);
  //   dataBrowse['noserahsample'] = _nb;
  // }

  function buttonBrowsePickSales(_kode) {
    $("#input_sales").val(_kode);
    dataBrowse['sales'] = _kode;
  }

  function buttonBrowsePickCustomer(_kode) {
    $("#input_customer").val(_kode);
    dataBrowse['customer'] = _kode;
  }

  function doBrowseBarang() {
    bm_params['noserahsample'] = $("#input_noserahsample").val();
    return true;
  }

  function buttonBrowsePickBarang(_kode, _nama, _qnt, _urut, _sat1, _sat2, _sat3, _isi1, _isi2, _isi3) {
    $("#inputitem_kodebrg").val(_kode);
    $("#inputitem_namabrg").val(_nama);
    $("#inputitem_quantity").val(_qnt);

    let satuanSelect = $("#inputitem_satuan");
    satuanSelect.empty(); // Clear previous options

    // Array of satuan objects with number and value
    const satuanList = [
      { nosat: 1, sat: _sat1, isi: _isi1, sat2: _sat2 },
      { nosat: 2, sat: _sat2, isi: _isi2, sat2: _sat1 },
      { nosat: 3, sat: _sat3, isi: _isi3, sat2: _sat1 }
    ];

    // Append valid satuan options
    let added = 0;
    satuanList.forEach(item => {
      if (item.sat && item.sat.trim() !== '') {
        satuanSelect.append(`<option value="${item.nosat}||${item.sat}||${item.isi}||${item.sat2}">${item.nosat} - ${item.sat}</option>`);
        added++;
      }
    });

    satuanSelect.prop("disabled", added === 0);

    $("#inputitem_urutserahsample").val(_urut);

    dataBrowse['barang'] = _kode;
  }

  function buttonBrowsePickGudang(_kode) {
    $("#inputitem_gudang").val(_kode);
    dataBrowse['gudang'] = _kode;
  }

  function showSatuanBarang(_nosat, _kode, _nama, _qnt, _urut, _sat1, _sat2, _sat3, _isi1, _isi2, _isi3) {
    buttonBrowsePickBarang(_kode, _nama, _qnt, _urut, _sat1, _sat2, _sat3, _isi1, _isi2, _isi3)

    const satuanSelect = $("#inputitem_satuan");

    // Find and select the matching option
    satuanSelect.find("option").each(function () {
      const value = $(this).val(); // e.g., "2||Box||12"
      const parts = value.split("||");

      if (parseInt(parts[0]) === _nosat) {
        satuanSelect.val(value); // set this option as selected
        return false; // break loop
      }
    });
  }

  function getStockSerahSample(_noserahsample, _urutserahsample) {
    let stock = [];
    $.ajax({
      url: "{!! url('bbsgetstockserahsample') !!}",
      type: "get",
      async: false,
      data: {
        noserahsample    : _noserahsample,
        urutserahsample  : _urutserahsample
      },
      success: function(res) {
        stock = res.stock;
    }});

    return (stock.length > 0) ? stock[0].qntsample : 0;
  }

  function cekValidate(_choice) {
    /* Kolom yang wajib diisi harus dicek. Kolom yang tidak wajib di-set ke default value jika perlu.
       Return pesan jika ada yang tidak valid. Return object cart jika semuanya sudah valid. */

    let cart = {};

    // === untuk hapus item ===
    if (_choice === "D") {
      cart["choice"]  = _choice;
      cart["nobukti"] = $("#input_nobukti").val();
      cart["urut"]    = $("#inputitem_urut").val();
      return cart;
    }
    // === Akhir tambahan ===

    // CEK EMPTY
    if (!cekNotEmpty("inputitem_kodebrg")) {
      return messageRequired("Barang");
    }
    if (!cekNotEmpty("inputitem_gudang")) {
      return messageRequired("Gudang");
    }

    // CEK VALIDASI KODE / NO BUKTI
    if (dataBrowse['barang'] !== $("#inputitem_kodebrg").val()) {
      return "Data Barang tidak sesuai";
    }
    if (dataBrowse['gudang'] !== $("#inputitem_gudang").val()) {
      return "Data Gudang tidak sesuai";
    }

    cart["choice"]          = _choice;
    cart["nobukti"]         = $("#input_nobukti").val();

    cart["nourut"]          = $("#input_nourut").val();
    cart["tanggal"]         = $("#input_tanggal").val();
    cart["urut"]            = $("#inputitem_urut").val();
    cart["kodebrg"]         = $("#inputitem_kodebrg").val();

    let satuan = $('#inputitem_satuan').val();
    if (satuan && satuan.trim() !== "") {
      satuan = satuan.split('||').map((v, i) => i === 2 ? parseFloat(v) : v);
      cart["nosat"]         = satuan[0];
      cart["satuan"]        = satuan[1];
      cart["isi"]           = satuan[2];
      cart["satuan1"]       = satuan[1];
      cart["satuan2"]       = satuan[3];
    } else {
      cart["nosat"]         = 0;
      cart["satuan"]        = "";
      cart["isi"]           = 0.00;
      cart["satuan1"]       = "";
      cart["satuan2"]       = "";
    }

    let stock = getStockSerahSample($("#input_noserahsample").val(), $("#inputitem_urutserahsample").val());

    if (Number($("#inputitem_quantity").val()) <= Number(stock)) {
      cart["qnt"]           = $("#inputitem_quantity").val();
    } else {
      return "Qty Barang " + cart["kodebrg"] + " melebihi quantity sample";
    }

    cart["jmlrecord"]       = (dataItem.length) ? 1 : 0;
    cart["kodegdg"]         = $("#inputitem_gudang").val();
    cart["kodesls"]         = $("#input_sales").val();
    cart["kodecust"]        = $("#input_customer").val();
    cart["noserahsample"]   = $("#input_noserahsample").val();
    cart["urutserahsample"] = $("#inputitem_urutserahsample").val();

    return cart;
}

  function submitItem () {
    doSubmitItem("item", "bbsspadd", "BBS");
  }

  function successAdd(_nobukti) {
    loadAll();
    cleanFormItem();
    refreshForm(_nobukti);

    gtipeform = g_tipeformEdit;
  }

  function successEdit(_nobukti) {
    loadAll();
    $('.showhide').hide();
    cleanFormItem();
    refreshForm(_nobukti);
  }

  function successDelete(_nobukti) {
    loadAll();
    $('.showhide').hide();
    refreshForm(_nobukti);
  }

</script>

@endsection