@extends('newmasterTest')
@section('buttons')

@section('page-title', 'Penyerahan Konsinyasi')
@section('title', 'SML - Penyerahan Konsinyasi')

@endsection

@section('css')

<link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">

{{-- Port 1:1 ke UI SO -- @extends newmasterTest (bukan lagi gudang.newmaster), custom-tabs
     pill bar, po-toolbar, dan report-table.js column engine untuk #tabel (Permintaan Konsinyasi
     -- antrian PRS outstanding, tetap terpisah) dan #tabel2 (SSP Belum+Sudah Otorisasi
     digabung jadi satu tabel dengan kolom Status dan filter Status di toolbar, sama seperti
     merge di perintahopname.blade.php/koreksistock.blade.php/gudangpermintaansample.blade.php). --}}
<style>
.custom-tabs {
  display: inline-flex;
  justify-content: flex-start;
  align-items: center;
  gap: 2px;
  background-color: #f1f3f5;
  border-radius: 20px;
  padding: 3px;
}

.custom-tabs .nav-link {
  display: inline-block !important;
  padding: 5px 16px !important;
  font-size: 0.75rem !important;
  border: none;
  border-radius: 17px;
  color: #495057;
  background: transparent;
  font-weight: 600;
  transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
}

.custom-tabs .nav-link:hover {
  background: transparent;
  color: #007bff;
}

.custom-tabs .nav-link.active {
  background: #007bff;
  border-color: #007bff;
  color: #fff;
  box-shadow: 0 2px 6px rgba(0, 123, 255, .35);
}

.tab-card {
  display: block !important;
  align-items: flex-start !important;
  padding: 0 !important;
  border: none !important;
  margin-bottom: 6px !important;
}

.tab-card .card-body {
  padding: 5px 10px !important;
}

#page1 .card {
  display: block !important;
  align-items: stretch !important;
  padding: 0 !important;
  text-align: left !important;
  cursor: default !important;
}

#page1 .card:hover {
  transform: none !important;
  box-shadow: none !important;
  border-color: var(--border) !important;
}

#page1 .tab-content .col-md-12 {
  min-width: 0;
  max-width: 100%;
}

{{-- #tabel sudah dapat width/min-width/warna tombol Aksi dari CSS terpusat
     (:where(.po-list-page) ...) sejak #page1 diberi class "po-list-page" -- lihat
     po-table-header.css. #tabel2/#koreksiTable tidak disentuh aturan #tabel di sana (id
     berbeda), jadi tetap butuh aturan manual di sini. --}}
#tabel2 { min-width: 100%; }

#tabel2 td:first-child {
  display: flex;
  gap: 4px;
  justify-content: center;
  align-items: center;
}

#tabel2 td:first-child .btn,
#koreksiTable td:last-child .btn {
  width: 30px;
  height: 30px;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 7px;
  font-size: 13px;
  border: 1px solid transparent;
  box-shadow: none;
  transition: all .12s ease;
}

#tabel2 td:first-child .btn:hover,
#koreksiTable td:last-child .btn:hover {
  filter: brightness(0.97);
  transform: translateY(-1px);
}

#tabel2 td:first-child .btn-success,
#koreksiTable td:last-child .btn-success {
  color: #16a34a; border-color: #cdebd7; background: #e7f7ed;
}

#tabel2 td:first-child .btn-info {
  color: #0891b2; border-color: #a5f3fc; background: #ecfeff;
}

#tabel2 td:first-child .btn-primary {
  color: #2563eb; border-color: #cfdcff; background: #e8edff;
}

#tabel2 td:first-child .btn-danger,
#koreksiTable td:last-child .btn-danger {
  color: #dc2626; border-color: #f7cfcf; background: #fdeaea;
}

#tabel2 td:first-child .btn-warning {
  color: #b45309; border-color: #fbe3bd; background: #fef3e0;
}

{{-- Tabel statis (bukan #tabel/#tabel2) yang dulu pakai thead bg-primary text-white --
     diganti abu-abu + teks gelap, sesuai UI baru yang sama di semua halaman. --}}
#addTable thead th,
#koreksiTable thead th,
#detailKoreksiTable thead th,
#DetailAddTable thead th,
#tabel_add_list_gudangasal thead th,
#tabel_add_list_gudangtujuan thead th,
#tabel thead th,
#tabel2 thead th {
  background: #f8f9fb !important;
  color: #6b7280 !important;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: .04em;
  font-weight: 600;
  border-bottom: 1px solid #e7e9ee;
  border-top: none;
}

#tabel tbody tr:hover,
#tabel2 tbody tr:hover {
  background-color: #f5f3ff;
}

.data-table th,
.data-table td {
  white-space: nowrap;
}

#tabel tbody .action-buttons-wrap,
#tabel2 tbody .action-buttons-wrap {
  opacity: 0;
  visibility: hidden;
  transform: translateX(-6px);
  transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
}
#tabel tbody tr:hover .action-buttons-wrap,
#tabel2 tbody tr:hover .action-buttons-wrap,
#tabel tbody tr:focus-within .action-buttons-wrap,
#tabel2 tbody tr:focus-within .action-buttons-wrap {
  opacity: 1;
  visibility: visible;
  transform: translateX(0);
}

{{-- Perbesar glyph + pada tombol Tambah (skTabelActionsCell) tanpa mengubah ukuran
     bingkai tombol -- port 1:1 dari notareturpenjualan.blade.php. --}}
#tabel .action-buttons-wrap .btn-primary .bi-plus {
  font-size: 1.5rem;
  line-height: 0;
  vertical-align: middle;
}
</style>
@endsection
@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id='page1' class="container-fluid mainpage po-list-page">
<div class="container-fluid" >



</div>

<div id="printContainer" style="display:none">



</div>
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

  <div class="card mb-3 tab-card">
    <div class="card-body">
      <div class="nav nav-tabs border-0 custom-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">Permintaan Konsinyasi</a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">Serah Konsinyasi</a>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body" style="padding:0;">
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
          <div class="row">
            <div class="col-md-12">
              <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                <div class="po-toolbar">
                  <input type="search" id="skSearch1" class="po-search-inp" placeholder="Cari data">
                  <div class="po-len-wrap">
                    <label for="skLen1">Tampilkan</label>
                    <select id="skLen1" class="po-len-inp">
                      <option value="10">10</option>
                      <option value="25">25</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                      <option value="-1">Semua</option>
                    </select>
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
        {{-- SSP Belum+Sudah Otorisasi digabung jadi satu tabel, dengan filter Status di
             toolbar (client-side), sama seperti merge di perintahopname.blade.php/
             koreksistock.blade.php/gudangpermintaansample.blade.php. --}}
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
          <div class="row">
            <div class="col-md-12">
              <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                <div class="po-toolbar">
                  <div class="po-filter-wrap">
                    <label>Periode</label>
                    <input type="date" onchange="skFilterChanged()" class="po-filter-inp" id="skTanggalAwal" value="{!! \Carbon\Carbon::createFromDate((int) $periode->tahun, (int) $periode->bulan, 1)->startOfMonth()->format('Y-m-d') !!}">
                    <span class="po-filter-sep">s/d</span>
                    <input type="date" onchange="skFilterChanged()" class="po-filter-inp" id="skTanggalAkhir" value="{!! \Carbon\Carbon::createFromDate((int) $periode->tahun, (int) $periode->bulan, 1)->endOfMonth()->format('Y-m-d') !!}">
                  </div>
                  <input type="search" id="skSearch2" class="po-search-inp" placeholder="Cari data">
                  <div class="po-len-wrap">
                    <label for="skLen2">Tampilkan</label>
                    <select id="skLen2" class="po-len-inp">
                      <option value="10">10</option>
                      <option value="25">25</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                      <option value="-1">Semua</option>
                    </select>
                  </div>
                  <button class="po-btn-filter" type="button" onclick="$('#modalFilterSK').modal('show')">
                    <i class="bi bi-funnel"></i> Filter
                  </button>
                </div>

                <div class="modal fade rt-filter" id="modalFilterSK">
                  <div class="modal-dialog modal-md">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-funnel"></i> Filter Data</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterSK').modal('hide')"><span aria-hidden="true">&times;</span></button>
                      </div>
                      <div class="modal-body">
                        <div class="rt-section">
                          <div class="rt-group-label">Status</div>
                          <div>
                            <label class="rt-field-label" for="skStatus">Status Otorisasi</label>
                            <select class="rt-native" id="skStatus">
                              <option value="" selected>Semua Status</option>
                              <option value="0">Belum Otorisasi</option>
                              <option value="1">Sudah Otorisasi</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="rt-reset-link" onclick="$('#skStatus').val('')">Reset semua</button>
                        <div class="rt-footer-buttons">
                          <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal" onclick="$('#modalFilterSK').modal('hide')">Batal</button>
                          <button type="button" class="rt-btn rt-btn-primary" onclick="skFilterChanged(); $('#modalFilterSK').modal('hide');">Terapkan</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div id="rtBarTabel2"></div>
                <table id="tabel2" class="data-table">
                  <thead style="white-space:nowrap;"></thead>
                  <tbody id="tabel2_data" class="text-left"></tbody>
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
      </div>
    </div>
  </div>
</div>
</div>





<div id="page2" style="display: none" class="mainpage container-fluid" >

  <div class="row">
    <div class="col-8 text-left">
      <h2></h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="height: 30px; margin-bottom:30px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()">CLOSE</button>
    </div>
  </div>

  <div class="container-fluid">
    <input type="hidden" name="noUrut" id="input_add_nourut" value="" />
    <div id='formBsGrid' class="row">
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
                <label>Sales</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input id="input_add_sales_nama" type="text" class="form-control text-center" placeholder="Sales" disabled>
                <input id="input_add_sales" type="hidden">
              </div>
            </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Customer</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input id="input_add_customer_nama" type="text" class="form-control text-center" placeholder="Customer" disabled>
                <input id="input_add_customer" type="hidden">
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
        <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">

              <table id="addTable" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>
                    <th style="padding: 4px 12px;" scope="col">Serahkan</th>
                    <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                    <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                    <th style="padding: 4px 12px;" scope="col">Qty</th>
                    <th style="padding: 4px 12px;" scope="col">Satuan</th>
                    <th style="padding: 4px 12px;" scope="col">Stock</th>
                  </tr>
                </thead>
                <tbody id="addTableData" class="" >
                  <tr>
                    <td colspan=6 class="text-center">Belum ada data</td>
                  </tr>
                </tbody>
              </table>
    </div>
    <div class="row mt-2" style="margin-top: 0">
      <div class="col-md-12 text-right mt-4">
        <button id="buttonSubmitAdd" type="button" onclick="submitAdd()" class="btn btn-chip-biru" style="height: 30px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;">Simpan</button>
        <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
      </div>
    </div>
  </div>
</div>


<div id="page3" style="display: none" class="mainpage container-fluid">

  <div class="row">
    <div class="col-8 text-left">
      <h2></h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="height: 30px; margin-bottom:20px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()">CLOSE</button>
    </div>
  </div>

  <div id='formBsGrid' class="container-fluid">
    {{-- <input type="hidden" name="noUrut" id="input_koreksi_nourut" value="" /> --}}
    <div class="row">
        <input type="hidden" class="form-control" id="input_koreksi_nourut" placeholder="No Urut" disabled>
        <!-- Kiri -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">No Bukti</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control text-center" id="input_koreksi_nobukti" placeholder="No Bukti" disabled>
                </div>
            </div>
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Sales</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input id="input_sales_nama" type="text" class="form-control text-center" placeholder="Sales" disabled>
                        <input id="input_koreksi_sales" type="hidden">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tengah -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Customer</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input id="input_customer_nama" type="text" class="form-control text-center" placeholder="Customer" disabled>
                        <input id="input_koreksi_customer" type="hidden">
                    </div>
                </div>
            </div>
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Keterangan</label>
                <div class="col-sm-8">
                    <textarea  style="width: 100%; height:75px !important; resize: none" rows=3 placeholder="" class="form-control" id="input_koreksi_keterangan"  onblur="onChangeHeader('NOTE' , 'input_koreksi_keterangan')"></textarea>
                </div>
            </div>
        </div>
        <!-- Kanan -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Tanggal</label>
                <div class="col-sm-8">
                    <input type="date" class="form-control text-center" id="input_koreksi_tanggal" value="{!! date('Y-m-d') !!}" disabled>
                </div>
            </div>
          </div>
        </div>

        <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">
              <table id="koreksiTable" class="table table-bordered table-striped"  >
                <thead class="text-center">
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
                  <th scope="col">Qty</th>
                  <th scope="col">Sat</th>
                  <th scope="col">Actions</th>
                </tr>
                </thead>
                <tbody id="koreksiTableData" class="" >
                  <tr>
                    <td colspan=7 class="text-center">Belum ada data</td>
                </tr>
                </tbody>
              </table>
    </div>

    <div id="formKoreksiEdit" class="container-fluid showhideitem">
    <div class="row">
      <div class="col-4">
        <h4 id="h4KoreksiEditItem" style="margin-left:-15px;">Edit Item</h4>
      </div>
    </div>

    <div id='formBsGrid' class="row">
      <div class="col-md-6">
        <div class="row mb-1 align-items-center">
          <div class="col-md-3">
            <label class="form-label fw-bold">Kode Barang</label>
          </div>
          <div class="col-md-4">
            <input id="KoreksiEditKodeBrg" type="text" class="form-control text-center" disabled>
          </div>
        </div>

        <div class="row mb-1 align-items-center">
          <div class="col-md-3">
            <label class="form-label fw-bold">Nama Barang</label>
          </div>
          <div class="col-md-8">
            <input id="KoreksiEditNamaBrg" type="text" class="form-control text-center" disabled>
          </div>
        </div>

        <div class="row mb-1 align-items-center">
          <div class="col-md-3">
            <label class="form-label fw-bold">Quantity</label>
          </div>
          <div class="col-md-3">
            <input id="KoreksiEditInputQty" type="text" inputmode="decimal" oninput="formatAngkaInput(event)" class="form-control text-right" value="0,00">
          </div>
          <div class="col-md-2">
            <label class="form-label fw-bold">Satuan</label>
          </div>
          <div class="col-md-3">
            <input id="KoreksiEditInputSat" type="text" class="form-control text-center" disabled>
          </div>
        </div>
      </div>
      <div class="col-md-6" hidden>
        <div class="row mb-1 align-items-center">
          <div class="col-md-3">
            <label class="form-label fw-bold">Gudang Asal</label>
          </div>
          <div class="col-md-8">
            <input id="KoreksiEditGudangAsal" type="text" class="form-control text-center" disabled>
            <input type="hidden" id="input_gudang_asal">
          </div>
        </div>

        <div class="row mb-1 align-items-center">
          <div class="col-md-3">
            <label class="form-label fw-bold">Gudang Tujuan</label>
          </div>
          <div class="col-md-8">
            <input id="KoreksiEditGudangTujuan" type="text" class="form-control text-center" disabled>
            <input type="hidden" id="input_gudang_tujuan">
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-3">
      <div class="col-md-12 text-right">
        <button type="button" class="btn btn-danger" onclick="buttonKoreksiItemBatal()" style="
          height: 30px; 
          padding: 4px 12px; 
          border-radius: 20px; 
          font-size: 0.75rem; 
          font-weight: 600; 
          text-transform: uppercase; 
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">Batal</button>

        <button id="buttonSubmitKoreksiEdit" type="button" onclick="submitKoreksiEdit()" class="btn btn-chip-biru" style="
          height: 30px; 
          padding: 4px 12px; 
          border-radius: 20px; 
          font-size: 0.75rem; 
          font-weight: 600; 
          text-transform: uppercase; 
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">Simpan</button>
      </div>
    </div>
  </div>
    <hr/>

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
                  <thead class="text-center">
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
                  <thead class="text-center">
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

<div id="page4" style="display: none" class="mainpage container-fluid" >
  <div class="row">
    <div class="col-8 text-left">
      <h2></h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="height: 30px; margin-bottom:20px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()">CLOSE</button>
    </div>
  </div>

  <div id='formBsGrid' class="container-fluid">
    <div class="row">
        <input type="hidden" class="form-control" id="input_detailkoreksi_nourut" placeholder="No Urut" disabled>
        <!-- Kiri -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">No Bukti</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control text-center" id="input_detailkoreksi_nobukti" placeholder="No Bukti" disabled>
                </div>
            </div>
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Sales</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input id="input_detailkoreksi_salesnama" type="text" class="form-control text-center" placeholder="Sales" disabled>
                        <input id="input_detailkoreksi_sales" type="hidden">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tengah -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Customer</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input id="input_detailkoreksi_customernama" type="text" class="form-control text-center" placeholder="Customer" disabled>
                        <input id="input_detailkoreksi_customer" type="hidden">
                    </div>
                </div>
            </div>
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Keterangan</label>
                <div class="col-sm-8">
                  <textarea  style="width: 100%; resize: none" rows=3 placeholder="" class="form-control" id="input_detailkoreksi_keterangan" disabled></textarea>
                </div>
            </div>
        </div>
        <!-- Kanan -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Tanggal</label>
                <div class="col-sm-8">
                    <input type="date" class="form-control text-center" id="input_detailkoreksi_tanggal" value="{!! date('Y-m-d') !!}" disabled>
                </div>
            </div>
          </div>
        </div>
    <hr/>
        <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">
              <table id="detailKoreksiTable" class="table table-bordered table-striped"  >
                <thead class="text-center">
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

<div id="page5" style="display: none" class="mainpage container-fluid" >

  <div class="row">
    <div class="col-8 text-left">
      <h2></h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="height: 30px; margin-top:5px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()">CLOSE</button>
    </div>
  </div>

  <div id='formBsGrid' class="container-fluid">
    <input type="hidden" name="noUrut" id="detail_nourut" value="" />
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
                <input type="text" class="form-control" id="detail_nobukti" placeholder="" disabled>
              </div>
            </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Sales</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input id="detail_sales_nama" type="text" class="form-control text-center" placeholder="Sales" disabled>
                <input id="detail_sales" type="hidden">
              </div>
            </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Customer</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input id="detail_customer_nama" type="text" class="form-control text-center" placeholder="Customer" disabled>
                <input id="detail_customer" type="hidden">
              </div>
            </div>
            </div>
          </div>
          {{-- <div class="col-md-3">
            <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="date" class="form-control text-center" id="detail_tanggal" value="{!! date('Y-m-d') !!}"  >
              </div>
            </div>
          </div>
          </div> --}}
        </div>
      </div>
    </div>
    <hr/>
        <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">

              <table id="DetailAddTable" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>
                    <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                    <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                    <th style="padding: 4px 12px;" scope="col">Qty</th>
                    <th style="padding: 4px 12px;" scope="col">Satuan</th>
                    <th style="padding: 4px 12px;" scope="col">Stock</th>
                  </tr>
                </thead>
                <tbody id="DetailAddTableData" class="" >
                  <tr>
                    <td colspan=5 class="text-center">Belum ada data</td>
                  </tr>
                </tbody>
              </table>
    </div>
    {{-- <div class="row mt-2" style="margin-top: 0">
      <div class="col-md-12 text-right mt-4">
        <button id="buttonSubmitAdd" type="button" onclick="submitAdd()" class="btn btn-primary" style="height: 30px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;">Submit</button>
        <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
      </div>
    </div> --}}

  </div>
</div>





@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script type="text/javascript">



let dataTableAdd = []
let dataTableKoreksi = []
let barangKoreksiEdit = {}

function parseAngka (str) {
  if (str === undefined || str === null || str === '') { return 0; }
  let n = Number(String(str).replace(/\./g, '').replace(',', '.'));
  return isNaN(n) ? 0 : n;
}
function formatAngkaDisplay (n, decimals) {
  decimals = decimals === undefined ? 2 : decimals;
  return Number(n || 0).toLocaleString('id-ID', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
}
function formatAngkaInput (e) {
  let el = e.target;
  let parts = el.value.replace(/[^\d,]/g, '').split(',');
  let intPart = parts[0].replace(/^0+(?=\d)/, '');
  let decPart = parts.length > 1 ? parts.slice(1).join('').slice(0, 2) : null;
  let formattedInt = intPart ? Number(intPart).toLocaleString('id-ID') : '';
  el.value = decPart !== null ? formattedInt + ',' + decPart : formattedInt;
}

/* ============ Header tabel interaktif (window.ReportTable) ============
 * Port 1:1 dari pola opCart/opAktifkanTabel milik perintahopname.blade.php. SSP
 * Belum Otorisasi + SSP Sudah Otorisasi digabung jadi satu tabel (#tabel2), dengan
 * kolom Status dan filter Status di toolbar -- sama seperti merge Belum/Sudah
 * Otorisasi di perintahopname.blade.php/koreksistock.blade.php/
 * gudangpermintaansample.blade.php. #tabel (Permintaan Konsinyasi -- antrian PRS
 * outstanding) tetap terpisah. renderTabelRows()/renderTabel2Rows() mem-force
 * aktivasi urut-nya sendiri sebelum membaca gcart_header, supaya tidak ada drift
 * seperti bug yang ditemukan di perintahopname/opnamebarang. */
let skCart = { 1 : [], 2 : [] }
let skActiveUrut = 0
const SK_HREF = 'gudangpenyerahankonsinyasi'
const SK_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date', 3 : 'bool' }
const SK_TIPE_KODE = { varchar : 0, float : 1, date : 2, bool : 3 }

function skPickCI (row, key) {
  if (!row) { return undefined; }
  if (row[key] !== undefined) { return row[key]; }
  let lower = key.toLowerCase();
  for (let k in row) {
    if (k.toLowerCase() === lower) { return row[k]; }
  }
  return undefined;
}

function skDefaultCart (urut) {
  if (urut === 2) {
    return [
      ['NOBUKTI',      'No. Bukti',    1, 'varchar', 0, 0],
      ['TANGGAL',      'Tanggal',      1, 'date',    0, 0],
      ['NAMASLS',      'Sales',        1, 'varchar', 0, 0],
      ['IDUSER',       'User',         1, 'varchar', 0, 0],
      ['RefPR',        'No. Ref',      1, 'varchar', 0, 0],
      ['IsOtorisasi1', 'Otorisasi',    1, 'bool',    0, 0],
      ['OtoUser1',     'User Oto',     1, 'varchar', 0, 0],
      ['TglOto1',      'Tgl Oto',      1, 'date',    0, 0],
    ]
  }
  return [
    ['NoBukti',  'No. Bukti',     1, 'varchar', 0, 0],
    ['Tanggal',  'Tanggal',       1, 'date',    0, 0],
    ['namasls',  'Sales',         1, 'varchar', 0, 0],
    ['TglKirim', 'Tanggal Kirim', 1, 'date',    0, 0],
  ]
}

function skBuatCart (headers, values, isnumerics, isshowns, desimals) {
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
      SK_TIPE_NAMA[tipe] || 'varchar',
      0,
      isNaN(des) ? 0 : des,
    ])
  });
  return cart
}

function skAktifkanTabel (urut) {
  skActiveUrut = urut
  window.g_modeReport = urut
  window.gcart_header = skCart[urut]
}

function skOnChangeAktif () {
  if (skActiveUrut === 2) { reinitTabel2(); }
  else { reinitTabel(); }
}

window.g_href = SK_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function (href, mode) {
  let urut = (mode === 2) ? mode : 1
  let cart = skCart[urut] || []

  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[1])
    value.push(c[0])
    isnumber.push(SK_TIPE_KODE[c[3]] ?? 0)
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
      href     : SK_HREF,
      urut     : urut
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal menyimpan pengaturan kolom')
    }
  })
}

window.doSetHeader = function (mode, reset) {
  let urut = (mode === 2) ? mode : 1

  $.ajax({
    url   : "{!! url('getheadertable') !!}",
    type  : "post",
    async : false,
    data  : {
      _token : $("#_token").val(),
      href   : SK_HREF,
      urut   : urut,
      reset  : reset ? 1 : 0
    },
    success : function (res) {
      if (!reset && res && res.headertableheader && res.headertableheader.length) {
        skCart[urut] = skBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal || [])
      } else {
        skCart[urut] = skDefaultCart(urut)
        window.gcart_header = skCart[urut]
        window.doSimpanHeader(SK_HREF, urut)
      }
      window.gcart_header = skCart[urut]
    },
    error : function (err) {
      console.log(err)
      alertify.warning(reset ? 'Gagal mengembalikan kolom ke tampilan default' : 'Gagal memuat pengaturan kolom')
      skCart[urut] = skDefaultCart(urut)
      window.gcart_header = skCart[urut]
    }
  })
}

let skRtSudahInit = false
function skInitReportTableSekali () {
  if (skRtSudahInit || typeof ReportTable === 'undefined') { return }
  skRtSudahInit = true

  ReportTable.init({ table : '#tabel',  bar : '#rtBarTabel',  onChange : skOnChangeAktif })
  ReportTable.init({ table : '#tabel2', bar : '#rtBarTabel2', onChange : skOnChangeAktif })

  let skGuardUlangKlik = false;
  ['#tabel', '#tabel2'].forEach((sel) => {
    let thead = document.querySelector(sel + ' thead')
    if (!thead) { return }
    thead.addEventListener('click', function (e) {
      if (skGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }
      e.stopPropagation()
      e.preventDefault()
      skGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles: false, cancelable: true, view: window })
      Object.defineProperty(ulang, 'target', { value: interaktif, configurable: true })
      thead.dispatchEvent(ulang)
      skGuardUlangKlik = false
    }, true)
  });
}

function tulisTheadHeaderSK (tableSel, cols) {
  let thead = document.querySelector(tableSel + ' thead')
  if (!thead || !window.ReportTable) { return; }
  let headRowHtml = ReportTable.headHtml(cols)
    .replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>');
  thead.setAttribute('style', 'white-space:nowrap;');
  thead.innerHTML = headRowHtml;
}

function skValueCell (row, col) {
  let raw = skPickCI(row, col[0]);
  let type = col[3];

  {{-- Kolom Status (IsOtorisasi1) -- ikon check/silang hijau/merah biasa, sama seperti
       kolom Otorisasi bool di perintahopname.blade.php/beritaacaraopname.blade.php. --}}
  if (col[0] === 'IsOtorisasi1') {
    return Number(raw) === 1
      ? '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>'
      : '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>';
  }
  if (type === 'date') {
    if (!raw) { return '<td></td>'; }
    return '<td>' + formatDate(raw, '/') + '</td>';
  }
  if (type === 'float') {
    let dp = Number(col[5]) || 0;
    let n = (raw !== undefined && raw !== null && raw !== '') ? Number(raw) : 0;
    return '<td class="text-right">' + n.toLocaleString('id-ID', { minimumFractionDigits: dp, maximumFractionDigits: dp }) + '</td>';
  }
  return '<td>' + (raw !== undefined && raw !== null ? raw : '') + '</td>';
}

function skTabelActionsCell (row) {
  let nobukti = skPickCI(row, 'NoBukti');
  let html = '<td class="text-center" style="white-space:nowrap;"><div class="action-buttons-wrap">';
  html += '<button class="btn btn-warning btn-sm" type="button" title="Detail" onclick="buttonDetailAdd(\'' + nobukti + '\')"><i class="bi bi-info"></i></button>';
  html += '<button class="btn btn-primary btn-sm" type="button" title="Tambah" onclick="buttonAdd(\'' + nobukti + '\')"><i class="bi bi-plus"></i></button>';
  html += '</div></td>';
  return html;
}

{{-- SSP Belum + Sudah Otorisasi digabung -- tombol aksi dicabang sesuai status,
     sama seperti opTabelActionsCell di perintahopname.blade.php. --}}
function skTabel2ActionsCell (row) {
  let nobukti = skPickCI(row, 'NOBUKTI');
  let sudah = Number(skPickCI(row, 'IsOtorisasi1')) === 1;
  let html = '<td class="text-center" style="white-space:nowrap;"><div class="action-buttons-wrap">';
  html += '<button class="btn btn-warning btn-sm" type="button" title="Detail" onclick="buttonDetailKoreksi(\'' + nobukti + '\')"><i class="bi bi-info"></i></button>';
  if (sudah) {
    html += '<button class="btn btn-danger btn-sm" type="button" title="Batal Otorisasi" onclick="buttonBatalOtorisasi(\'' + nobukti + '\')"><i class="bi bi-key"></i></button>';
    html += '<button class="btn btn-primary btn-sm" type="button" title="Print" onclick="submitPrint(\'' + nobukti + '\')"><i class="bi bi-printer"></i></button>';
  } else {
    html += '<button class="btn btn-success btn-sm" type="button" title="Edit" onclick="buttonKoreksi(\'' + nobukti + '\')"><i class="bi bi-pen"></i></button>';
    html += '<button class="btn btn-info btn-sm" type="button" title="Otorisasi" onclick="buttonOtorisasi(\'' + nobukti + '\', 0)"><i class="bi bi-key"></i></button>';
  }
  html += '</div></td>';
  return html;
}

function skFilterRows (rows) {
  let status = $('#skStatus').val()
  let awal = $('#skTanggalAwal').val()
  let akhir = $('#skTanggalAkhir').val()
  return (rows || []).filter(function (row) {
    if (status !== '' && String(Number(skPickCI(row, 'IsOtorisasi1'))) !== status) { return false }
    if (awal || akhir) {
      let tgl = skPickCI(row, 'TANGGAL')
      if (!tgl) { return false }
      let t = String(tgl).slice(0, 10)
      if (awal && t < awal) { return false }
      if (akhir && t > akhir) { return false }
    }
    return true
  })
}

function skFilterChanged () {
  reinitTabel2()
}

function renderTabelRows (rows) {
  if (skActiveUrut !== 1) { skAktifkanTabel(1); }
  let cols = (skCart[1].length ? skCart[1] : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  (rows || []).forEach(function (row) {
    html += '<tr>' + skTabelActionsCell(row);
    cols.forEach(function (col) { html += skValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel_data').innerHTML = html;
  tulisTheadHeaderSK('#tabel', cols);
}

function renderTabel2Rows (rows) {
  if (skActiveUrut !== 2) { skAktifkanTabel(2); }
  let cols = (skCart[2].length ? skCart[2] : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  skFilterRows(rows).forEach(function (row) {
    html += '<tr>' + skTabel2ActionsCell(row);
    cols.forEach(function (col) { html += skValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel2_data').innerHTML = html;
  tulisTheadHeaderSK('#tabel2', cols);
}

let lastTabelRows = []
let lastTabel2Rows = []
let skPanjangHalaman = { 1 : 10, 2 : 10 }

// loadAll() dipanggil dari dalam aksi (Tambah/Hapus/Otorisasi) yang masih berada di
// page2/3/4/5 (page1 tersembunyi) -- reinitTabel()/reinitTabel2() yang langsung
// menginisialisasi DataTables di atas tabel yang hidden membuat instance-nya korup
// (lebar kolom 0/salah), dan korupsi itu bertahan sampai halaman di-refresh manual,
// termasuk mempengaruhi klik Otorisasi berikutnya walau saat itu page1 sudah
// terlihat. Redraw ditunda lewat flag ini sampai page1 benar-benar tampil lagi
// (lihat buttonCloseForm()), sama seperti pola psPerluGambar di
// gudangpenyerahansample.blade.php/tbPerluGambar di transferbarang.blade.php.
let skPerluGambar = { tabel: false, tabel2: false }

function skIkatSearch (urut) {
  let ids = { 1 : ['skSearch1', 'tabel'], 2 : ['skSearch2', 'tabel2'] }
  let input = document.getElementById(ids[urut][0])
  let idTabel = ids[urut][1]
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  let timer = null
  input.addEventListener('input', function () {
    let nilai = input.value
    if (timer) { clearTimeout(timer) }
    timer = setTimeout(function () {
      if ($.fn.DataTable.isDataTable('#' + idTabel)) {
        $('#' + idTabel).DataTable().search(nilai).draw()
      }
    }, 400)
  })
}

function skIkatPanjangHalaman (urut) {
  let ids = { 1 : ['skLen1', 'tabel'], 2 : ['skLen2', 'tabel2'] }
  let sel = document.getElementById(ids[urut][0])
  let idTabel = ids[urut][1]
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(skPanjangHalaman[urut])

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    skPanjangHalaman[urut] = (n === -1 || n > 0) ? n : 10
    if ($.fn.DataTable.isDataTable('#' + idTabel)) {
      $('#' + idTabel).DataTable().page.len(skPanjangHalaman[urut]).draw()
    }
  })
}

const SK_DOM_STRING = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"

function reinitTabel () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().destroy(); }
    renderTabelRows(lastTabelRows);
    $('#tabel').DataTable({ dom: SK_DOM_STRING, lengthChange: false, pageLength: skPanjangHalaman[1], paging: true, ordering: false });
    skIkatSearch(1);
    skIkatPanjangHalaman(1);
  } catch (e) {
    console.error('reinitTabel failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

function reinitTabel2 () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel2')) { $('#tabel2').DataTable().destroy(); }
    renderTabel2Rows(lastTabel2Rows);
    $('#tabel2').DataTable({ dom: SK_DOM_STRING, lengthChange: false, pageLength: skPanjangHalaman[2], paging: true, ordering: false });
    skIkatSearch(2);
    skIkatPanjangHalaman(2);
  } catch (e) {
    console.error('reinitTabel2 failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

$(document).ready(function(){
  skAktifkanTabel(1);
  window.doSetHeader(1, false);
  lastTabelRows = @json($tempOutstanding);
  reinitTabel();

  skAktifkanTabel(2);
  window.doSetHeader(2, false);
  {{-- SSP Belum + Sudah Otorisasi digabung jadi satu tabel. --}}
  lastTabel2Rows = [].concat(@json($tempPenerimaan), @json($tempPenerimaan2));
  reinitTabel2();

  skAktifkanTabel(1);

  skInitReportTableSekali();

  $('#nav-home-tab').on('shown.bs.tab', function () {
    skAktifkanTabel(1);
    if (typeof ReportTable !== 'undefined') { ReportTable.refresh(); }
    if (skPerluGambar.tabel) { reinitTabel(); skPerluGambar.tabel = false; }
  });
  $('#nav-profile-tab').on('shown.bs.tab', function () {
    skAktifkanTabel(2);
    if (typeof ReportTable !== 'undefined') { ReportTable.refresh(); }
    if (skPerluGambar.tabel2) { reinitTabel2(); skPerluGambar.tabel2 = false; }
  });
});

// function buttonKoreksiListGudangAsal () {
//   console.log('buttonKoreksiListGudangAsal');
//   $('#tabel_add_list_gudangasal').DataTable().destroy();

//   $.ajax({
//     url: "{{ url('penyerahankonsinyasilistgudangasal') }}",
//     type: "get",
//     async: false,
//     success: function(res) {
//       console.log(res);

//       let rowTable = ``;
//       res.forEach((item, i) => {
//         rowTable += `
//           <tr>
//             <td>${item.KodeGdg}</td>
//             <td>${item.NamaGdg}</td>
//             <td class="text-center">
//               <button class="btn btn-primary btn-sm" type="button"
//                 onclick="buttonAddPickGudangAsal('${item.NamaGdg}', '${item.KodeGdg}')">
//                 <i class="bi bi-plus"></i>
//               </button>
//             </td>
//           </tr>`;
//       });

//       if (!res.length) {
//         rowTable = `<tr><td class="text-center" colspan="3">Tidak ada data</td></tr>`;
//       }

//       document.getElementById("tabel_data_add_list_gudangasal").innerHTML = rowTable;
//       $("#tabel_add_list_gudangasal").DataTable({
//         "lengthChange": false,
//         "paging": false,
//       });

//       $('#modalAddListGudangAsal').modal('show');
//     },
//     error: function(err) {
//       console.log(err);
//       alertify.warning('Terjadi kesalahan saat mengambil data gudang.');
//     }
//   });
// }

// function buttonAddPickGudangAsal (nama, kode) {
//   $('#KoreksiEditGudangAsal').val(nama);
//   $('#input_gudang_asal').val(kode);
//   $('#modalAddListGudangAsal').modal('hide');
// }

// function buttonKoreksiListGudangTujuan () {
//   console.log('buttonKoreksiListGudangTujuan');
//   $('#tabel_add_list_gudangtujuan').DataTable().destroy();

//   $.ajax({
//     url: "{{ url('penyerahankonsinyasilistgudangtujuan') }}",
//     type: "get",
//     async: false,
//     success: function(res) {
//       console.log(res);

//       let rowTable = ``;
//       res.forEach((item, i) => {
//         rowTable += `
//           <tr>
//             <td>${item.KodeGdg}</td>
//             <td>${item.NamaGdg}</td>
//             <td class="text-center">
//               <button class="btn btn-primary btn-sm" type="button"
//                 onclick="buttonAddPickGudangTujuan('${item.NamaGdg}', '${item.KodeGdg}')">
//                 <i class="bi bi-plus"></i>
//               </button>
//             </td>
//           </tr>`;
//       });

//       if (!res.length) {
//         rowTable = `<tr><td class="text-center" colspan="3">Tidak ada data</td></tr>`;
//       }

//       document.getElementById("tabel_data_add_list_gudangtujuan").innerHTML = rowTable;
//       $("#tabel_add_list_gudangtujuan").DataTable({
//         "lengthChange": false,
//         "paging": false,
//       });

//       $('#modalAddListGudangTujuan').modal('show');
//     },
//     error: function(err) {
//       console.log(err);
//       alertify.warning('Terjadi kesalahan saat mengambil data gudang.');
//     }
//   });
// }

// function buttonAddPickGudangTujuan (nama, kode) {
//   $('#KoreksiEditGudangAsal').val(nama);
//   $('#input_gudang_tujuan').val(kode);
//   $('#modalAddListGudangTujuan').modal('hide');
// }

// function buttonAddListBatal() {
//   $('#modalAddListGudangAsal').modal('hide');
//   $('#modalAddListGudangTujuan').modal('hide');
// }


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
      url: "{!! url('penyerahankonsinyasionchangeheader') !!}",
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

  document.getElementById("KoreksiEditInputQty").value = formatAngkaDisplay(barang.QNT || 0);
  document.getElementById("KoreksiEditInputSat").value = barang.NOSAT == 1 ? barang.SAT_1 : barang.SAT_2;

  document.getElementById("KoreksiEditGudangAsal").value = barang.NAMA_GDGASAL;
  document.getElementById("input_gudang_asal").value = barang.GDGASAL;

  document.getElementById("KoreksiEditGudangTujuan").value = barang.NAMA_GDGTUJUAN;
  document.getElementById("input_gudang_tujuan").value = barang.GDGTUJUAN;

  $('#formKoreksiEdit').show();
}

function refreshDataTableKoreksi (nobukti) {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('penyerahankonsinyasigetdetailpenerimaan') !!}",
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
          <td>${item.KODEBRG}</td>
          <td>${item.NAMABRG}</td>
          <td>${item.GDGASAL} - ${item.NAMA_GDGASAL}</td>
          <td>${item.GDGTUJUAN} - ${item.NAMA_GDGTUJUAN}</td>
          <td class="text-right">${item.QNT ? parseFloat(item.QNT).toLocaleString() : '0.00'}</td>
          <td class="text-center">${item.SAT_1}</td>
          <td class="text-center">
            <button class="btn btn-success btn-sm" onclick="buttonKoreksiEditItem(${i})">
              <i class="bi bi-pen"></i>
            </button>
            <button class="btn btn-danger btn-sm" onclick="buttonKoreksiDeleteItem(${i})">
              <i class="bi bi-trash"></i>
            </button>
          </td>
        </tr>`;
      });

      document.getElementById("koreksiTableData").innerHTML = rowTable;

      let header = res[0];

      $("#input_koreksi_namacustomer").val(header.NAMACUSTSUPP);
      $("#input_koreksi_nobukti").val(header.NOBUKTI);
      $("#input_koreksi_catatan").val(header.KETERANGAN);
      $("#input_koreksi_sales_nama").val(header.NAMASLS);
      $("#input_koreksi_sales").val(header.KODESLS);
      $("#input_koreksi_customer").val(header.KODECUSTSUPP);
      $("#input_customer_nama").val(header.NAMACUSTSUPP);
      $("#input_koreksi_tanggal").val(header.TANGGAL);

      buttonKoreksiItemBatal();
    },
    error: function (err) {
      console.error(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}


function buttonKoreksiDeleteItem (i) {
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
      let nopr = barang.NOPRSAMPLE;
      let urutpr = barang.URUTPRSAMPLE;

      let qnt2 = qnt;
      let qnt1 = qnt;

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
        nopr,
        urutpr
      });

      $.ajax({
        url: "{!! url('penyerahankonsinyasispkoreksi') !!}",
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
          nopr,
          urutpr
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

function buttonOtorisasi (nobukti, isOtorisasi) {
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
    url: "{!! url('penyerahankonsinyasispotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti,
      otorisasi: 1
    },
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


alertify.prompt("Batal Otorisasi","Masukkan keterangan batal otorisasi nomor   " + nobukti, "",
  function(evt, value) {
    // alertify.success("You entered: " + value);
    let xpket = value;

     if (xpket==''){
          alertify.warning('Keterangan harus diisi.');
          $.abort();
        }
      let _token = $("#_token").val();

      $.ajax({
        url: "{!! url('penyerahankonsinyasispbatalotorisasi') !!}",
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


function submitKoreksiEdit () {
  let _token = $("#_token").val();
  let barang = barangKoreksiEdit;
  let choice = "U";

  let qnt = parseAngka($("#KoreksiEditInputQty").val());

  if (qnt < 0) {
    alertify.warning("Qty tidak boleh kurang dari 0");
    
    return;
  }

  // Data item
  let nobukti = barang.NOBUKTI;
  let urut = barang.URUT;
  let kodebrg = barang.KODEBRG;
  let namabrg = barang.NAMABRG;

  let kodegdgasal = $("#input_gudang_asal").val();
  let kodegdgtujuan = $("#input_gudang_tujuan").val();

  let nosat = barang.NoSat;
  let isi = parseFloat(barang.ISI); 

  let sat1 = barang.SAT_1;
  let sat2 = barang.SAT_2;

  let qnt2 = qnt;
  let qnt1 = qnt;

  let nopr = barang.NOPRSAMPLE;
  let urutpr = barang.URUTPRSAMPLE;

  let keterangan = $("#input_koreksi_keterangan").val();

  $.ajax({
    url: "{!! url('penyerahankonsinyasispkoreksi') !!}",
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
      nopr,
      urutpr
    },
    success: function (res) {
      if (res == 1) {
        refreshDataTableKoreksi(nobukti);
        loadAll()
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
    url: "{!! url('penyerahankonsinyasigetdetailpenerimaan') !!}",
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
          <td>${item.GDGASAL} - ${item.NAMA_GDGASAL}</td>
          <td>${item.GDGTUJUAN} - ${item.NAMA_GDGTUJUAN}</td>
          <td class="text-right">${parseFloat(item.QNT).toLocaleString()}</td>
          <td>${item.SAT_1}</td>
          <td class="text-center">
            <button class="btn btn-success btn-sm" onclick="buttonKoreksiEditItem(${i})"><i class="bi bi-pen"></i></button>
            <button class="btn btn-danger btn-sm" onclick="buttonKoreksiDeleteItem(${i})"><i class="bi bi-trash"></i></button>
          </td>
        </tr>`;
      });

      $("#koreksiTableData").html(rowTable);

      // Isi Form Header
      $("#input_koreksi_nobukti").val(data.NOBUKTI);
      $("#input_customer_nama").val(data.NAMACUSTSUPP);
      $("#input_koreksi_customer").val(data.KODECUSTSUPP);
      $("#input_sales_nama").val(data.NAMASLS);
      $("#input_koreksi_sales").val(data.KODESLS);
      $("#input_koreksi_keterangan").val(data.KETERANGAN);

      const tanggal = data.TANGGAL;
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
    url: "{!! url('penyerahankonsinyasigetdetailpenerimaan') !!}",
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
          <td>${item.GDGASAL} - ${item.NAMA_GDGASAL}</td>
          <td>${item.GDGTUJUAN} - ${item.NAMA_GDGTUJUAN}</td>
          <td class="text-right">${parseFloat(item.QNT).toLocaleString()}</td>
          <td>${item.SAT_1}</td>
        </tr>`;
      });

      $("#detailKoreksiTableData").html(rowTable);

      // Isi Header Detail
      const data = res[0];

      $("#input_detailkoreksi_nobukti").val(data.NOBUKTI);
      $("#input_detailkoreksi_customernama").val(data.NAMACUSTSUPP);
      $("#input_detailkoreksi_customer").val(data.KODECUSTSUPP);
      $("#input_detailkoreksi_salesnama").val(data.NAMASLS);
      $("#input_detailkoreksi_sales").val(data.KODESLS);
      $("#input_detailkoreksi_keterangan").val(data.KETERANGAN);

      const tanggal = data.TANGGAL;
      $("#input_detailkoreksi_tanggal").val(tanggal);;

      $('.mainpage').hide();
      $('#page4').show();
    },
    error: function (err) {
      console.error('Error response:', err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}

function buttonDetailAdd (nobukti) {
  console.log('buttonDetailAdd', nobukti);
  $('.showhideitem').hide();

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('penyerahankonsinyasigetdetailpenerimaanadd') !!}",
    type: "POST",
    data: { _token, nobukti },
    success: function (res) {
      console.log('gudangpenyerahankonsinyasi:3729 res', res);

      if (!Array.isArray(res) || res.length === 0) {
        alertify.warning("Data tidak ditemukan");
        return;
      }

      let firstItem = res[0];
      if (firstItem) {
        $("#detail_nobukti").val(firstItem.NOBUKTI || "");
        $("#detail_customer_nama").val(firstItem.NAMACUSTSUPP || "");
        $("#detail_customer").val(firstItem.KodeCustSupp || "");
        $("#detail_sales_nama").val(firstItem.NamaSls || "");
        $("#detail_sales").val(firstItem.KodeSls || "");
        $("#detail_keterangan").val(firstItem.Note || "");
        $("#detail_tanggal").val(firstItem.TANGGAL || "");
      }

      let rowTable = "";

      res.forEach((item, i) => {
        let kodeBrg = item.KODEBRG || "";
        let namaBrg = item.NAMABRG || "";
        let satuan = item.SAT_1 || "";
        let qntStock = formatAngkaDisplay(item.QNTSTOCK || 0);
        let qnt = formatAngkaDisplay(item.QNT || 0);

        // struktur tabel sama seperti buttonAdd tapi tanpa input/checkbox
        rowTable += `
          <tr>
            <td>${kodeBrg}</td>
            <td>${namaBrg}</td>
            <td class="text-right">${qnt}</td>
            <td>${satuan}</td>
            <td class="text-right">${qntStock}</td>
          </tr>`;
      });

      document.getElementById("DetailAddTableData").innerHTML = rowTable;

      $(".mainpage").hide();
      $("#page5").show();
    },
    error: function (err) {
      console.log("AJAX Error:", err);
      alertify.error("Terjadi kesalahan koneksi");
    }
  });
}


function loadAll () {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('penyerahankonsinyasiloadall') !!}",
    type: "get",
    async: false,
    data: {},
    success: function (res) {
      lastTabelRows = res.tempOutstanding
      lastTabel2Rows = [].concat(res.tempPenerimaan, res.tempPenerimaan2)

      {{-- #page1 bisa terlihat tapi tab "Serah Konsinyasi" (#profile) belum aktif
           (mis. user kembali dari Tambah lewat tab "Permintaan Konsinyasi") --
           reinitTabel2() yang tetap dipaksa jalan di situ meng-init DataTables di
           atas pane yang hidden (sama kasusnya dengan #page1 sendiri hidden), jadi
           visibilitas dicek per-pane (#home/#profile), bukan cuma per-page. --}}
      if ($('#home').is(':visible')) {
        reinitTabel()
      } else {
        skPerluGambar.tabel = true
      }

      if ($('#profile').is(':visible')) {
        reinitTabel2()
      } else {
        skPerluGambar.tabel2 = true
      }
    }
  });
}

function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: "{!! url('penyerahankonsinyasidetailCetak') !!}",
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
                    <p class="m-0">
                      JL. AMPERA PERGUDANGAN MANGKUPALAS BISNIS CENTRE BLOK D NO.18
                      RT. 022 SIMPANG PASIR PALARAN SAMARINDA-KALIMANTAN TIMUR
                    </p>
                    <p class="m-0">Telp (0541) 4104142 , Fax (0541) 4104195</p>
                    <p class="m-0">E-mail : sml@indo.net.id</p>
                  </div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">Kepada : PT. ${dataPrint[0].NamaCustSupp ?? '-'}</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
              </div>


              <div style="width: 38%">
                <div style="display: flex; width: 100%">
                  <h2 class="m-0 pb-2">SURAT PENYERAHAN BARANG</h2>
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
                  <div class="pb-1" style="width: 20%">Reff. PR</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">${dataPrint[0].refPR ?? '-'}</div>
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
                    <td class="text-center" style="width: 30%">KODE BARANG</td>
                    <td class="text-center" style="width: 50%">NAMA BARANG</td>
                    <td class="text-center" style="width: 20%">QUANTITY</td>
                    <td class="text-center" style="width: 20%">SATUAN</td>
                  </tr>
                </thead> `;

    let z = 0
    let tempPrintStr = ``
    // buat hitung grandtotal
    let grandTotal = 0;
    arrayDataPrint.forEach(group => {
      group.forEach(item => {
        if (item.QNT) {
          grandTotal += parseFloat(item.QNT) || 0;
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
               style="width: 30%;">${itemSub.KODEBRG}</td>
         <td class="text-align: left"
               style="width: 50%;">${itemSub.NamaBrg}</td>
         <td class="text-align: text-right"
               style="width: 20%;  "> ${itemSub.QNT ? parseFloat(itemSub.QNT).toFixed(2) : ''}</td>
         <td class="text-align: text-right"
               style="width: 20%;  "> ${itemSub.SATUAN}</td>
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

            <div style="width:70%; text-align:right; padding-right:10px;">
              Total :
            </div>

            <div style="width:10%; text-align:right;">
              ${grandTotal.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              })}
            </div>

          </div>
         
         </div>


           <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: -15px ; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%"></td>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%"></td>
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
    url: "{!! url('penyerahankonsinyasigetdetail') !!}",
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

      let firstItem = res[0];
      if (firstItem) {
        $("#input_add_sales_nama").val(firstItem.NamaSls || "");
        $("#input_add_sales").val(firstItem.KodeSls || "");

        $("#input_add_customer_nama").val(firstItem.NAMACUSTSUPP || "");
        $("#input_add_customer").val(firstItem.KodeCustSupp || "");
      }

      res.forEach((item, i) => {
        let kodeBrg = item.KODEBRG;
        let namaBrg = item.NAMABRG;
        let satuan = item.SAT_1;
        let qntStock = formatAngkaDisplay(item.QNTSTOCK || 0);
        let qnt = formatAngkaDisplay(item.QNT || 0);

        rowTable += `
          <tr>
            <td class="text-center">
              <input type="checkbox" id="add_checkbox${i}" style="transform: scale(1.5); margin: 5px;">
            </td>
            <td>${kodeBrg}</td>
            <td>${namaBrg}</td>
            <td class="text-right">
              <input id="input_add_qnt${i}" style="width: 100px;" class="text-right" type="text" inputmode="decimal" oninput="formatAngkaInput(event)" value="${qnt}">
            </td>
            <td>${satuan}</td>
            <td class="text-right">${qntStock}</td>
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
  let checkMinus = false;

  const nobukti = $("#input_add_nobukti").val();
  const nourut = $("#input_add_nourut").val();
  const tanggal = $("#input_add_tanggal").val();
  const kodeSales = $("#input_add_sales").val();
  const namaSales = $("#input_add_sales_nama").val();
  const kodeCustomer = $("#input_add_customer").val();
  const namaCustomer = $("#input_add_customer_nama").val();

  dataTableAdd.forEach((item, i) => {
    const checkbox = document.getElementById(`add_checkbox${i}`);
    if (checkbox && checkbox.checked) {
      const inputQty = parseAngka($(`#input_add_qnt${i}`).val());

      if (inputQty < 0) {
        checkMinus = true;
      }

      const itemData = {
        ...item,
        inputQnt: inputQty,
        KodeSls: kodeSales,
        NamaSls: namaSales,
        KodeCustSupp: kodeCustomer,
        NamaCustSupp: namaCustomer
      };

      tempData.push(itemData);
    }
  });

  if (checkMinus) {
    alertify.warning("Qty tidak boleh negatif");
    return;
  }

  if (!tempData.length) {
    alertify.warning("Tidak ada item yang dipilih");
    return;
  }

  console.log("submitAdd payload", tempData);

  $.ajax({
    url: "{!! url('penyerahankonsinyasispadd') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      tempData,
      tanggal,
      nobukti,
      nourut
    },
    success: function(res) {
    console.log('submitAdd response', res);
    // if (res == 1) {
    //     alertify.success('SSP telah ditambah');
    //     loadAll();
    //     buttonCloseForm();
    //   }
      if (res == 1) {
        alertify.success('SSP telah ditambah');
        loadAll();
        setTimeout(() => {
          $('.mainpage').hide();
          $('#page3').show();  
          buttonKoreksi(nobukti, 'edit');
        }, 500);
      } else if (res == 2) {
        setNewNoBukti();
        alertify.warning('Nobukti telah direfresh silahkan submit ulang');
      } else if (res == 3) {
        alertify.warning('Stok tidak mencukupi');
        return;
      } else {
        alertify.error('Respon tidak diketahui dari server');
      }
    }
  });
}


function setNewNoBukti () {
  $.ajax({
    url: "{!! url('penyerahankonsinyasispnobukti') !!}",
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

{{-- Script hover-warna tab lama dihapus -- custom-tabs CSS sudah menangani warna
     tab aktif/hover (lihat .custom-tabs .nav-link.active di @section('css')). --}}



@endsection
