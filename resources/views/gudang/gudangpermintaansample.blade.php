@extends('newmasterTest')
@section('buttons')

@section('page-title', 'Permintaan Sample')
@section('title', 'SML - Permintaan Sample')

@endsection
{{-- tampilan search bar 1 --}}
  @section('css')

<link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
<link rel="stylesheet" href="{!! URL::asset('css/picker-kas.css') !!}?v={{ @filemtime(base_path('public/css/picker-kas.css')) ?: '1' }}">

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
     po-table-header.css. #tabel2 (tab OutStanding, tanpa kolom Actions) tidak disentuh
     aturan #tabel di sana (id berbeda), jadi tetap butuh min-width manual supaya tidak
     menyempit karena width inline DataTables -- TANPA flex/warna tombol td:first-child,
     karena kolom pertamanya data biasa, bukan Actions. --}}
#tabel2 { min-width: 100%; }

#tabel td:first-child,
#tabel_add td:last-child {
  display: flex;
  gap: 4px;
  justify-content: center;
  align-items: center;
}

#tabel_add td:last-child .btn {
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

#tabel_add td:last-child .btn:hover {
  filter: brightness(0.97);
  transform: translateY(-1px);
}

#tabel_add td:last-child .btn-success {
  color: #16a34a; border-color: #cdebd7; background: #e7f7ed;
}

#tabel_add td:last-child .btn-info {
  color: #0891b2; border-color: #a5f3fc; background: #ecfeff;
}

#tabel_add td:last-child .btn-primary {
  color: #2563eb; border-color: #cfdcff; background: #e8edff;
}

#tabel_add td:last-child .btn-danger {
  color: #dc2626; border-color: #f7cfcf; background: #fdeaea;
}

#tabel_add td:last-child .btn-warning {
  color: #b45309; border-color: #fbe3bd; background: #fef3e0;
}

/* {{-- Badge kolom Status (IsOtorisasi1) di tabel PRS gabungan -- hijau untuk Sudah
     Otorisasi, amber untuk Belum Otorisasi, warna sama dengan palet btn-success/
     btn-warning di atas. --}} */
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
.status-badge-sudah {
  color: #16a34a; border: 1px solid #cdebd7; background: #e7f7ed;
}
.status-badge-belum {
  color: #b45309; border: 1px solid #fbe3bd; background: #fef3e0;
}

/* {{-- Tabel statis (bukan #tabel/#tabel2) yang dulu pakai thead bg-primary
     text-white -- diganti abu-abu + teks gelap, sesuai UI baru yang sama di semua
     halaman. --}} */
#tabel_add thead th,
#tabel_detail thead th,
#tabel_add_list_customer thead th,
#tabel_add_list_sales thead th,
#tabel_add_list_item thead th,
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

/* {{-- "+"-button picker Customer/Sales/Gudang/Barang dikonversi jadi click-anywhere-
     on-row, sama seperti konvensi so.blade.php/perintahopname.blade.php. --}} */
.pick-row { cursor: pointer; }
.pick-row:hover { background-color: #f5f3ff !important; }

#tabel tbody .action-buttons-wrap {
  opacity: 0;
  visibility: hidden;
  transform: translateX(-6px);
  transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
}
#tabel tbody tr:hover .action-buttons-wrap,
#tabel tbody tr:focus-within .action-buttons-wrap {
  opacity: 1;
  visibility: visible;
  transform: translateX(0);
}
</style>
{{-- Search bar 1/2/3 lama (#tabel_filter/#tabel2_filter/#tabel_oto) dihapus -- toolbar
     sekarang pakai po-toolbar/po-search-inp, selector lama itu sudah tidak dipakai lagi. --}}

{{-- Search bar hardcoded modal Barang All dihapus -- sekarang pakai satu-satunya
     search box native DataTables (hasil pickerKasInit), lihat searchBarangAll()
     di @section('js') yang sekarang diikat ke #tabel_add_list_item_filter input. --}}

{{-- tampilan search sales --}}
  <style>
    #tabel_add_list_sales_filter{
      display: flex;
      align-items: flex-end;
      margin-bottom: -10px;
    }
    #tabel_add_list_sales_filter label input {
      width: 150px;
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-shadow: none; 
      font-size: 0.65rem;
    }
  </style>
{{-- end tampilan search sales --}}

{{-- tampilan search customer --}}
  <style>
    #tabel_add_list_customer_filter{
      display: flex;
      align-items: flex-end;
      margin-bottom: -10px;
    }
    #tabel_add_list_customer_filter label input {
      width: 150px;
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-shadow: none; 
      font-size: 0.65rem;
    }
  </style>
{{-- end tampilan search customer --}}

{{-- search gudang dihapus -- Gudang Asal sekarang <select> biasa. --}}
@endsection

@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id='page1' class="container-fluid po-list-page">
  <!-- <div id="qrcode"></div> -->
  <div class="">

{{-- <div id="printContainer" style="display:none">

</div> --}}
<div id="contentContainer" class="container-fluid">
  {{-- Hidden Inputs --}}
  <input type="hidden" id="periode_tahun" value="{{ $periode->tahun }}">
  <input type="hidden" id="periode_bulan" value="{{ $periode->bulan }}">
  <input type="hidden" id="akses_istambah" value="{{ $akses->ISTAMBAH }}">
  <input type="hidden" id="akses_ishapus" value="{{ $akses->ISHAPUS }}">
  <input type="hidden" id="akses_iskoreksi" value="{{ $akses->ISKOREKSI }}">
  <input type="hidden" id="akses_iscetak" value="{{ $akses->ISCETAK }}">
  <input type="hidden" id="akses_isotorisasi1" value="{{ $akses->IsOtorisasi1 }}">
  <input type="hidden" id="akses_isbatal" value="{{ $akses->IsBatal }}">
  <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">

  <div class="card mb-3 tab-card">
    <div class="card-body">
      <div class="nav nav-tabs border-0 custom-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">Permintaan Sample</a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">OutStanding</a>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body" style="padding:0;">
      <div class="tab-content" id="myTabContent">
        {{-- Utama --}}
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
          <div class="row">
            <div class="col-md-12">
              <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                <div class="po-toolbar">
                  <div class="po-filter-wrap">
                    <label>Periode</label>
                    <input type="date" onchange="gpFilterChanged()" class="po-filter-inp" id="gpTanggalAwal" value="{!! \Carbon\Carbon::createFromDate((int) $periode->tahun, (int) $periode->bulan, 1)->startOfMonth()->format('Y-m-d') !!}">
                    <span class="po-filter-sep">s/d</span>
                    <input type="date" onchange="gpFilterChanged()" class="po-filter-inp" id="gpTanggalAkhir" value="{!! \Carbon\Carbon::createFromDate((int) $periode->tahun, (int) $periode->bulan, 1)->endOfMonth()->format('Y-m-d') !!}">
                  </div>
                  <input type="search" id="gpSearch1" class="po-search-inp" placeholder="Cari data">
                  <div class="po-len-wrap">
                    <label for="gpLen1">Tampilkan</label>
                    <select id="gpLen1" class="po-len-inp">
                      <option value="10">10</option>
                      <option value="25">25</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                      <option value="-1">Semua</option>
                    </select>
                  </div>
                  <button class="po-btn-filter" type="button" onclick="$('#modalFilterGP').modal('show')">
                    <i class="bi bi-funnel"></i> Filter
                  </button>
                  <div class="po-toolbar-act">
                    <button class="btn btn-chip-biru" type="button" onclick="buttonAdd()">Tambah</button>
                  </div>
                </div>

                <div class="modal fade rt-filter" id="modalFilterGP">
                  <div class="modal-dialog modal-md">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-funnel"></i> Filter Data</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterGP').modal('hide')"><span aria-hidden="true">&times;</span></button>
                      </div>
                      <div class="modal-body">
                        <div class="rt-section">
                          <div class="rt-group-label">Status</div>
                          <div>
                            <label class="rt-field-label" for="gpStatus">Status Otorisasi</label>
                            <select class="rt-native" id="gpStatus">
                              <option value="" selected>Semua Status</option>
                              <option value="0">Belum Otorisasi</option>
                              <option value="1">Sudah Otorisasi</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="rt-reset-link" onclick="$('#gpStatus').val('')">Reset semua</button>
                        <div class="rt-footer-buttons">
                          <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal" onclick="$('#modalFilterGP').modal('hide')">Batal</button>
                          <button type="button" class="rt-btn rt-btn-primary" onclick="gpFilterChanged(); $('#modalFilterGP').modal('hide');">Terapkan</button>
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
        {{-- OutStanding --}}
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
          <div class="row">
            <div class="col-md-12">
              <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                <div class="po-toolbar">
                  <input type="search" id="gpSearch3" class="po-search-inp" placeholder="Cari data">
                  <div class="po-len-wrap">
                    <label for="gpLen3">Tampilkan</label>
                    <select id="gpLen3" class="po-len-inp">
                      <option value="10">10</option>
                      <option value="25">25</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                      <option value="-1">Semua</option>
                    </select>
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
        </div> {{-- End OutStanding 2 --}}
      </div>
    </div>
  </div>
</div>
</div>
</div>

<div id="page2" class="container-fluid" style="display:none;">
  <div class="row">
    <div class="col-6 text-left">
      <h1></h1>
    </div>
    <div class="col-6 text-right">
      <button type="button" class="btn btn-danger btn-lg" style="
            height: 30px; 
            padding: 4px 12px; 
            border-radius: 20px; 
            font-size: 0.75rem; 
            font-weight: 600; 
            text-transform: uppercase; 
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" 
        onclick="buttonCloseForm()">Close</button>
    </div>
      <div id='formBsGrid' class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <div class="container-fluid">
        <div class="row">
        <input type="hidden" class="form-control" id="input_add_nourut" placeholder="No Urut" disabled>
        <!-- Kiri -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">No Bukti</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control text-center" id="input_add_nobukti" placeholder="No Bukti" disabled>
                </div>
            </div>
            <div class="mb-2 row">
            <label class="col-sm-4 col-form-label">Sales</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input id="input_sales_nama" type="text" class="form-control text-center" placeholder="Sales" disabled>
                        <input id="input_sales" type="hidden">
                        <button type="button" onclick="buttonAddListSales()" class="btn btn-chip-biru btn-sm">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Keterangan</label>
                <div class="col-sm-8">
                    <textarea class="form-control text-left" id="input_keterangan" rows="3" onblur="onChangeHeader('NOTE' , 'input_keterangan')" style="resize: none; height:75px !important;"></textarea>
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
                        <input id="input_customer" type="hidden">
                        <button id="btn_customer" type="button" onclick="buttonAddListCustomer()" class="btn btn-chip-biru btn-sm">
                          <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>
           <div class="mb-2 row align-items-center">
              <label class="col-sm-4 col-form-label text-nowrap">Gudang Asal</label>
              <div class="col-sm-8">
                <div class="input-group">
                  <select id="input_gudang" class="form-control">
                    <option value="">- Pilih Gudang -</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Ref PR</label>
                <div class="col-sm-8">
                    <input id="input_refpr" type="text" class="form-control text-left">
                </div>
            </div>
        </div>

        <!-- Kanan -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Tanggal</label>
                <div class="col-sm-8">
                    <input type="date" class="form-control text-center" id="input_add_tanggal" value="{!! date('Y-m-d') !!}">
                </div>
            </div>
            <div class="mb-2 row align-items-center">
                <label class="col-sm-4 col-form-label text-nowrap">Tanggal Kirim</label>
                <div class="col-sm-8">
                    <input type="date" 
                        class="form-control text-center" 
                        id="input_add_tanggalkirim" 
                        value="{!! date('Y-m-d') !!}" 
                        onchange="onChangeHeader('TglKirim', 'input_add_tanggalkirim')">
                </div>
            </div>
          </div>
        </div>
        </div>
        </div>
    </div>
    <div class="row ">
      <div class="col-md-12 text-right">
      <button type="button" class="btn btn-chip-biru" style="
      height: 30px;  
      padding: 4px 12px; 
      border-radius: 20px; 
      font-size: 0.75rem; 
      font-weight: 600; 
      text-transform: uppercase; 
      transition: background-color 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
      onclick="buttonAddAddItem()" class="btn btn-secondary">Tambah Item</button>
      </div>
    </div>

    <!-- ADD SUBGROUP -->
    <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_add" class="table table-bordered table-striped"  >
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
              <tbody id="tabel_data_add" class="text-left" >
                <tr > 
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td> 
                  <td class="text-center">
                    <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                    <button class="btn btn-success btn-sm" type="button" title="Edit"><i class="bi bi-pen"></i></button>
                    <button class="btn btn-danger btn-sm" type="button" ><i class="bi bi-trash"></i></button>
                    <button class="btn btn-primary btn-sm" type="button" title="Details"><i class="bi bi-list"></i></button>
                  </td>
              </tr>
              </tbody>
            </table>          
          </div>
          {{-- <div class="text-right">
            <button type="button" class="btn btn-primary" style="
            height: 30px; 
            margin-top: 20px; 
            padding: 4px 12px; 
            border-radius: 20px; 
            font-size: 0.75rem; 
            font-weight: 600; 
            text-transform: uppercase; 
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
            onclick="submitAdd()">Submit</button>
        </div> --}}
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
    </div>
    <div id="addAddItem" class="container-fluid showhide">
      <div id='formBsGrid'>
            <!-- <div class="line"></div> -->
            <div class="row">
              <div class="col-4">
                <h4 id="h4AddAddItem" style="margin-left:-15px;">Add Item</h4>
                <h4 id="h4AddEditItem" style="margin-left:-15px;">Edit Item</h4>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="row">
                  <div class="col-md-3" style="margin-top:5px;">
                    <div class="form-group">
                      <label>Kode Barang</label>
                    </div>
                  </div>
                <div class="col-md-4">
                  <div class="input-group mb-3">
                  <input id="input_add_add_kodebarang" type="text" class="form-control text-center" placeholder="Kode Barang">
                  <button type="button" id="buttonAddListKodeBarang" onclick="buttonAddListKodeBarang()" class="btn btn-chip-biru btn-sm"><i class="bi bi-search"></i></button>
                  </div>
                </div>
              </div>
            <div class="row" style="margin-top:-10px;">
              <div class="col-md-3" style="margin-top:5px;">
                <div class="form-group">
                <label>Ket. Barang</label>
              </div>
              </div>
              <div class="col-md-8">
                <input id="input_add_add_keterangannama" type="text" class="form-control text-center" disabled>
              </div>
            </div>
            </div>
            <div class="col-md-6">
            <div class="row">
              <div class="col-md-2" style="margin-top:5px;">
                <div class="form-group">
                <label>Quantity</label>
              </div>
              </div>
              <div class="col-md-3">
                <input id="input_add_add_qnt" type="number" value=0.00 class="form-control text-right">
              </div>
              <div class="col-md-2" style="margin-top:5px;">
                <label for="input_add_add_satuan">Satuan</label>
              </div>
              <div class="col-md-3">
                <select id="input_add_add_satuan" class="form-control">
                  <option value="" disabled selected>Pilih Satuan</option>
                </select>                
              </div>
            </div>
          </div>
          </div>
            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-danger" style="
                height: 30px; 
                padding: 4px 12px; 
                border-radius: 20px; 
                font-size: 0.75rem; 
                font-weight: 600; 
                text-transform: uppercase; 
                transition: background-color 0.3s, box-shadow 0.3s;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
                onclick="closeShowHideAdd()" >Batal</button>

                <button type="button" id="submitAddAdd" class="btn btn-chip-biru" style="
                height: 30px; 
                padding: 4px 12px; 
                border-radius: 20px; 
                font-size: 0.75rem; 
                font-weight: 600; 
                text-transform: uppercase; 
                transition: background-color 0.3s, box-shadow 0.3s; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="submitAddAdd()">Simpan</button>

                <button type="button" id="submitAddEdit" class="btn btn-chip-biru btn-lg" style="
                height: 30px; 
                padding: 4px 12px; 
                border-radius: 20px; 
                font-size: 0.75rem; 
                font-weight: 600; 
                text-transform: uppercase; 
                transition: background-color 0.3s, box-shadow 0.3s; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="submitAddEdit()" style="display: none;">Submit Edit</button>
              </div>
            </div>
          </div>

    <!-- END ADD ADD -->

    <!-- ADD EDIT -->

    <div id="addEditItem" class="container-fluid showhide">
            <!-- <div class="line"></div> -->
            <div class="row">
              <div class="col-4">
                <h4>Edit Item Kedua</h4>
              </div>
            </div>

            {{-- <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Ref SO</label>
              </div>
              </div>
              <div class="col-3">
                <input id="input_add_edit_refso" type="text" class="form-control" value="-" disabled>
              </div>
              <div class="col-1 text-right">

                <button type="button" disabled onclick="" disabled class="btn btn-primary" >+</button>
              </div>
              <div class="col-2">
                <div class="form-group">
                <label>No PO Cust</label>
              </div>
              </div>
              <div class="col-4">

                <input id="input_add_edit_nopocust" type="text" class="form-control" disabled>
              </div>
            </div> --}}
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Kode Barang</label>
              </div>
              </div>
              <div class="col-3">
                <input id="input_add_edit_kodebarang" type="text" class="form-control" disabled>
              </div>
              <div class="col-1 text-right">
                <button type="button" disabled onclick="" class="btn btn-chip-biru"><i class='bi bi-search'></i></button>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Ket. Barang</label>
              </div>
              </div>
              <div class="col-4">
                <input id="input_add_edit_keterangannama" type="text" class="form-control" disabled>
              </div>

            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Quantity</label>
              </div>
              </div>
              <div class="col-4">
                <input id="input_add_edit_qnt" type="number" value=0.00 class="form-control text-right">
              </div>
              <div class="col-md-2">
                <label for="input_add_edit_satuan">Satuan</label>
              </div>
              <div class="col-md-4">
                <select id="input_add_edit_satuan" class="form-control" name="satuan">
                  <option value="" selected disabled>Pilih Satuan</option>
                </select>
              </div>
            </div>
            <div class="row">
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Keterangan</label>
              </div>
              </div>
              <div class="col-10">
                <input id="input_add_edit_keterangan" type="text" class="form-control">
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-danger" onclick="closeShowHideAdd()">Batal</button>
                {{-- <button type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> --}}
              </div>
            </div>
          </div>
        </div>
    <!-- END ADD EDIT -->
  </div>
<!-- End modal add-->

{{-- Start Modal List Customer --}}
<div class="modal fade" id="modalAddListCustomer" role="dialog" aria-labelledby="labelCustomer" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Customer</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body" style="margin-top:-30px;">
        <div class="container-fluid px-3 mt-4">
          <div class="row">
            <div class="table-responsive">
              <table id="tabel_add_list_customer" class="table table-bordered table-striped">
                <thead class="text-center">
                  <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Kota</th>
                  </tr>
                </thead>
                <tbody id="tabel_data_add_list_customer" class="text-left">
                  <tr class="pick-row">
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="d-flex justify-content-end mt-3">
            {{-- <button type="button" class="btn btn-danger btn-lg"
              style="height: 30px; padding: 4px 12px; border-radius: 20px;
              font-size: 0.75rem; font-weight: 600; text-transform: uppercase;"
              onclick="buttonAddListBatal()">Batal</button> --}}
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
{{-- End Modal List Customer --}}

{{-- Start Modal List Sales --}}
<div class="modal fade" id="modalAddListSales" role="dialog" aria-labelledby="labelSales" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Sales</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body" style="margin-top:-30px;">
        <div class="container-fluid px-3 mt-4">
          <div class="row">
            <div class="table-responsive">
              <table id="tabel_add_list_sales" class="table table-bordered table-striped">
                <thead class="text-center">
                  <tr>
                    <th>NIK</th>
                    <th>Nama</th>
                  </tr>
                </thead>
                <tbody id="tabel_data_add_list_sales" class="text-left">
                  <tr class="pick-row">
                    <td>-</td>
                    <td>-</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="d-flex justify-content-end mt-3">
            {{-- <button type="button" class="btn btn-danger btn-lg"
              style="height: 30px; padding: 4px 12px; border-radius: 20px;
              font-size: 0.75rem; font-weight: 600; text-transform: uppercase;"
              onclick="buttonAddListBatal()">Batal</button> --}}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{{-- End Modal List Sales --}}

{{-- Start Modal List gudang --}}
{{-- Modal pemilih Gudang dihapus -- Gudang Asal sekarang pakai <select> biasa
     (loadGudangOptions()), sama seperti koreksistock.blade.php/perintahopname.blade.php. --}}

<!-- start modal list item add -->
<div class="modal fade" id="formAddListItem" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"></h5>
        <button type="button" class="btn btn-sm btn-danger rounded-circle shadow-sm ms-auto" 
          data-dismiss="modal" aria-label="Close"
          style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
          <span aria-hidden="true" style="font-size: 1.2rem; font-weight: bold;">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="container-fluid mt-4">

          <div class="row">
            <div class="table-responsive">
            <table id="tabel_add_list_item" class="table table-bordered table-striped">
              <thead class="text-center">
                <tr>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Merk</th>
                  <th scope="col">Part Number</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add_list_item" class="text-left">
                <!-- Diisi lewat JS -->
              </tbody>
            </table>
          </div>
          </div>

          <div class="d-flex justify-content-end mt-3">
            {{-- <button type="button" class="btn btn-danger btn-lg"
              style="height: 30px; padding: 4px 12px; border-radius: 20px;
              font-size: 0.75rem; font-weight: 600; text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="closeListItemAdd()">Close</button> --}}
          </div>

        </div>
      </div>

    </div>
  </div>
</div>
<!-- End modal list item add-->

<!-- start modal detail -->
<div id="page3" class="container-fluid" style="display: none">
        <div class="row">
          <div class="col-6 text-left">
            <h2></h2>
          </div>
          <div class="col-6 text-right">
            <button type="button" class="btn btn-danger btn-lg" style="
            height: 30px; 
            padding: 4px 12px; 
            border-radius: 20px; 
            font-size: 0.75rem; 
            font-weight: 600; 
            text-transform: uppercase; 
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" 
            onclick="buttonCloseForm()">Close</button>
          </div>
        </div>  
        <div id='formBsGrid' class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <div class="container-fluid">
        <div class="row">
        <!-- Kiri -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">No Bukti</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control text-center" id="input_detail_nobukti" placeholder="No Bukti" disabled>
                </div>
            </div>
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Sales</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input id="input_detail_sales_nama" type="text" class="form-control text-center" placeholder="Sales" disabled>
                        <input id="input_detail_sales" type="hidden">
                    </div>
                </div>
            </div>
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Keterangan</label>
                <div class="col-sm-8">
                    <textarea class="form-control text-left" id="input_detail_keterangan" rows="3" style="resize: none; height:75px !important;" disabled></textarea>
                </div>
            </div>
        </div>

        <!-- Tengah -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Customer</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input id="input_detail_customer_nama" type="text" class="form-control text-center" placeholder="Customer" disabled>
                        <input id="input_detail_customer" type="hidden">
                    </div>
                </div>
            </div>
           <div class="mb-2 row align-items-center">
              <label class="col-sm-4 col-form-label text-nowrap">Gudang Asal</label>
              <div class="col-sm-8">
                <div class="input-group">
                  <input id="input_detail_gudang_nama" type="text" class="form-control text-center" placeholder="Gudang Asal" disabled>
                  <input type="hidden" id="input_detail_gudang">
                </div>
              </div>
            </div>
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Ref PR</label>
                <div class="col-sm-8">
                    <input id="input_detail_refpr" type="text" class="form-control text-center" disabled>
                </div>
            </div>
        </div>

        <!-- Kanan -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Tanggal</label>
                <div class="col-sm-8">
                    <input type="date" class="form-control text-center" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
                </div>
            </div>
            <div class="mb-2 row align-items-center">
                <label class="col-sm-4 col-form-label text-nowrap">Tanggal Kirim</label>
                <div class="col-sm-8">
                    <input type="date" class="form-control text-center" id="input_detail_tanggalkirim" value="{!! date('Y-m-d') !!}" disabled>
                </div>
            </div>
          </div>
        </div>
        </div>
        <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_detail_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_detail" class="table table-bordered table-striped"  >
              <thead class="text-center">
                <tr>
                  <th colspan="4">Deskripsi Barang</th>
                  <th colspan="2">Satuan </th>
                </tr>
                <tr>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Gudang Asal</th>
                  <th scope="col">Gudang Tujuan</th>
                  <th scope="col">Satuan</th>
                  <th scope="col">Qty</th>
                </tr>
              </thead>
              <tbody id="tabel_data_detail" class="text-left" >
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
              </tr>
              </tbody>
            </table>
          </div>
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
    </div>
  </div>
  <div class="modal-footer">
    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
    <!-- <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button> -->
  </div>
</div>
<!-- End modal detail-->


@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>
{{-- Modal pemilih Customer/Sales/Gudang/Barang disamakan persis dengan modal pemilih
     menu Kas, pola yang sama persis dengan perintahopname.blade.php/so.blade.php. --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  jQuery('#modalAddListCustomer, #modalAddListSales, #formAddListItem').addClass('picker-kas')
  jQuery('#modalAddListCustomer .modal-footer .btn, #modalAddListSales .modal-footer .btn, #formAddListItem .modal-footer .btn').filter(function () {
    return jQuery.trim(jQuery(this).text()) === 'Batal'
  }).addClass('picker-kas-batal')
})
</script>
<script type="text/javascript">

/* ============ Header tabel interaktif (window.ReportTable) ============
 * Port 1:1 dari pola opCart/opAktifkanTabel milik perintahopname.blade.php. PRS
 * Belum Otorisasi + PRS Sudah Otorisasi digabung jadi satu tabel (#tabel), dengan
 * kolom Status dan filter Status di toolbar -- sama seperti merge Belum/Sudah
 * Otorisasi di perintahopname.blade.php/koreksistock.blade.php. #tabel2
 * (OutStanding) tetap terpisah, datanya flat. renderTabelRows()/dkk mem-force
 * aktivasi urut-nya sendiri sebelum membaca gcart_header, supaya tidak ada drift
 * seperti bug yang ditemukan di perintahopname/opnamebarang. */
let gpCart = { 1 : [], 3 : [] }
let gpActiveUrut = 0
const GP_HREF = 'gudangpermintaansample'
const GP_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date', 3 : 'bool' }
const GP_TIPE_KODE = { varchar : 0, float : 1, date : 2, bool : 3 }

function gpPickCI (row, key) {
  if (!row) { return undefined; }
  if (row[key] !== undefined) { return row[key]; }
  let lower = key.toLowerCase();
  for (let k in row) {
    if (k.toLowerCase() === lower) { return row[k]; }
  }
  return undefined;
}

function gpDefaultCart (urut) {
  if (urut === 3) {
    return [
      ['NoBukti',      'No. Bukti',      1, 'varchar', 0, 0],
      ['Tanggal',      'Tanggal',        1, 'date',    0, 0],
      ['KodeCustSupp', 'Kode CustSupp',  1, 'varchar', 0, 0],
      ['NAMACUSTSUPP', 'Nama Customer',  1, 'varchar', 0, 0],
      ['kodebrg',      'Kode Barang',    1, 'varchar', 0, 0],
      ['namabrg',      'Nama Barang',    1, 'varchar', 0, 0],
      ['namasls',      'Sales',          1, 'varchar', 0, 0],
      ['QNT',          'Qnt',            1, 'float',   0, 2],
      ['SAT',          'Sat',            1, 'varchar', 0, 0],
      ['QNTSSKONSI',   'Qnt SS Konsi',   1, 'float',   0, 2],
      ['QNTSISA',      'Qnt Sisa',       1, 'float',   0, 2],
    ]
  }
  {{-- PRS Belum + Sudah Otorisasi digabung -- kolom Status (IsOtorisasi1) dirender
       sebagai badge hijau/amber di gpValueCell(), bukan ikon check/x biasa. --}}
  return [
    ['NOBUKTI',      'No. Bukti',   1, 'varchar', 0, 0],
    ['TANGGAL',      'Tanggal',     1, 'date',    0, 0],
    ['Keterangan',   'Keterangan',  1, 'varchar', 0, 0],
    ['NamaCustSupp', 'Customer',    1, 'varchar', 0, 0],
    ['NAMASLS',      'Sales',       1, 'varchar', 0, 0],
    ['RefPR',        'Ref PR',      1, 'varchar', 0, 0],
    ['IsOtorisasi1', 'Status',      1, 'bool',    0, 0],
    ['OtoUser1',     'User Oto',    1, 'varchar', 0, 0],
    ['TglOto1',      'Tanggal Oto', 1, 'date',    0, 0],
  ]
}

function gpBuatCart (headers, values, isnumerics, isshowns, desimals) {
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
      GP_TIPE_NAMA[tipe] || 'varchar',
      0,
      isNaN(des) ? 0 : des,
    ])
  });
  return cart
}

function gpAktifkanTabel (urut) {
  gpActiveUrut = urut
  window.g_modeReport = urut
  window.gcart_header = gpCart[urut]
}

function gpOnChangeAktif () {
  if (gpActiveUrut === 3) { reinitTabel2(); }
  else { reinitTabel(); }
}

window.g_href = GP_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function (href, mode) {
  let urut = (mode === 3) ? mode : 1
  let cart = gpCart[urut] || []

  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[1])
    value.push(c[0])
    isnumber.push(GP_TIPE_KODE[c[3]] ?? 0)
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
      href     : GP_HREF,
      urut     : urut
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal menyimpan pengaturan kolom')
    }
  })
}

window.doSetHeader = function (mode, reset) {
  let urut = (mode === 3) ? mode : 1

  $.ajax({
    url   : "{!! url('getheadertable') !!}",
    type  : "post",
    async : false,
    data  : {
      _token : $("#_token").val(),
      href   : GP_HREF,
      urut   : urut,
      reset  : reset ? 1 : 0
    },
    success : function (res) {
      if (!reset && res && res.headertableheader && res.headertableheader.length) {
        gpCart[urut] = gpBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal || [])
      } else {
        gpCart[urut] = gpDefaultCart(urut)
        window.gcart_header = gpCart[urut]
        window.doSimpanHeader(GP_HREF, urut)
      }
      window.gcart_header = gpCart[urut]
    },
    error : function (err) {
      console.log(err)
      alertify.warning(reset ? 'Gagal mengembalikan kolom ke tampilan default' : 'Gagal memuat pengaturan kolom')
      gpCart[urut] = gpDefaultCart(urut)
      window.gcart_header = gpCart[urut]
    }
  })
}

let gpRtSudahInit = false
function gpInitReportTableSekali () {
  if (gpRtSudahInit || typeof ReportTable === 'undefined') { return }
  gpRtSudahInit = true

  ReportTable.init({ table : '#tabel',  bar : '#rtBarTabel',  onChange : gpOnChangeAktif })
  ReportTable.init({ table : '#tabel2', bar : '#rtBarTabel2', onChange : gpOnChangeAktif })

  let gpGuardUlangKlik = false;
  ['#tabel', '#tabel2'].forEach((sel) => {
    let thead = document.querySelector(sel + ' thead')
    if (!thead) { return }
    thead.addEventListener('click', function (e) {
      if (gpGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }
      e.stopPropagation()
      e.preventDefault()
      gpGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles: false, cancelable: true, view: window })
      Object.defineProperty(ulang, 'target', { value: interaktif, configurable: true })
      thead.dispatchEvent(ulang)
      gpGuardUlangKlik = false
    }, true)
  });
}

function tulisTheadHeaderGP (tableSel, cols, withActions) {
  if (withActions === undefined) { withActions = true }
  let thead = document.querySelector(tableSel + ' thead')
  if (!thead || !window.ReportTable) { return; }
  let headRowHtml = ReportTable.headHtml(cols)
  if (withActions) {
    headRowHtml = headRowHtml.replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>');
  }
  thead.setAttribute('style', 'white-space:nowrap;');
  thead.innerHTML = headRowHtml;
}

function gpValueCell (row, col) {
  let raw = gpPickCI(row, col[0]);
  let type = col[3];

  {{-- Kolom Status (IsOtorisasi1) dirender sebagai badge hijau (Sudah Otorisasi)
       atau amber (Belum Otorisasi), bukan ikon check/x bool biasa. --}}
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

{{-- PRS Belum + Sudah Otorisasi digabung -- tombol aksi dicabang sesuai status,
     sama seperti opTabelActionsCell di perintahopname.blade.php. --}}
function gpTabelActionsCell (row) {
  let nobukti = gpPickCI(row, 'NOBUKTI');
  let sudah = Number(gpPickCI(row, 'IsOtorisasi1')) === 1;
  let html = '<td class="text-center" style="white-space:nowrap;"><div class="action-buttons-wrap">';
  html += '<button class="btn btn-warning btn-sm" type="button" title="Details" onclick="buttonDetail(\'' + nobukti + '\')"><i class="bi bi-info"></i></button>';
  if (sudah) {
    html += '<button class="btn btn-danger btn-sm" type="button" title="Batal Otorisasi" onclick="buttonBatalOtorisasi(\'' + nobukti + '\')"><i class="bi bi-key"></i></button>';
    html += '<button class="btn btn-primary btn-sm" type="button" title="Print" onclick="submitPrint(\'' + nobukti + '\')"><i class="bi bi-printer"></i></button>';
  } else {
    html += '<button class="btn btn-success btn-sm" type="button" title="Edit" onclick="buttonEdit(\'' + nobukti + '\')"><i class="bi bi-pen"></i></button>';
    html += '<button class="btn btn-info btn-sm" type="button" title="Otorisasi" onclick="buttonOtorisasi(\'' + nobukti + '\')"><i class="bi bi-key"></i></button>';
  }
  html += '</div></td>';
  return html;
}

function gpFilterRows (rows) {
  let status = $('#gpStatus').val()
  return (rows || []).filter(function (group) {
    let row = group[0];
    if (status !== '' && String(Number(gpPickCI(row, 'IsOtorisasi1'))) !== status) { return false }
    return true
  })
}

function gpFilterChanged () {
  loadAll()
}

function renderTabelRows (rows) {
  if (gpActiveUrut !== 1) { gpAktifkanTabel(1); }
  let cols = (gpCart[1].length ? gpCart[1] : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  gpFilterRows(rows).forEach(function (group) {
    let row = group[0];
    html += '<tr>' + gpTabelActionsCell(row);
    cols.forEach(function (col) { html += gpValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel_data').innerHTML = html;
  tulisTheadHeaderGP('#tabel', cols);
}

function renderTabel2Rows (rows) {
  if (gpActiveUrut !== 3) { gpAktifkanTabel(3); }
  let cols = (gpCart[3].length ? gpCart[3] : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  (rows || []).forEach(function (row) {
    html += '<tr>';
    cols.forEach(function (col) { html += gpValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel2_data').innerHTML = html;
  tulisTheadHeaderGP('#tabel2', cols, false);
}

let lastTabelRows = []
let lastTabel2Rows = []
let gpPanjangHalaman = { 1 : 10, 3 : 10 }

function gpIkatSearch (urut) {
  let ids = { 1 : ['gpSearch1', 'tabel'], 3 : ['gpSearch3', 'tabel2'] }
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

function gpIkatPanjangHalaman (urut) {
  let ids = { 1 : ['gpLen1', 'tabel'], 3 : ['gpLen3', 'tabel2'] }
  let sel = document.getElementById(ids[urut][0])
  let idTabel = ids[urut][1]
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(gpPanjangHalaman[urut])

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    gpPanjangHalaman[urut] = (n === -1 || n > 0) ? n : 10
    if ($.fn.DataTable.isDataTable('#' + idTabel)) {
      $('#' + idTabel).DataTable().page.len(gpPanjangHalaman[urut]).draw()
    }
  })
}

const GP_DOM_STRING = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"

function reinitTabel () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().destroy(); }
    renderTabelRows(lastTabelRows);
    $('#tabel').DataTable({ dom: GP_DOM_STRING, lengthChange: false, pageLength: gpPanjangHalaman[1], paging: true, ordering: false });
    gpIkatSearch(1);
    gpIkatPanjangHalaman(1);
  } catch (e) {
    console.error('reinitTabel failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

function reinitTabel2 () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel2')) { $('#tabel2').DataTable().destroy(); }
    renderTabel2Rows(lastTabel2Rows);
    $('#tabel2').DataTable({ dom: GP_DOM_STRING, lengthChange: false, pageLength: gpPanjangHalaman[3], paging: true, ordering: false });
    gpIkatSearch(3);
    gpIkatPanjangHalaman(3);
  } catch (e) {
    console.error('reinitTabel2 failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

let dataAddListItem = []
let dataRefresh = []

let dataTableAdd = []
let dataTableEdit = []

let dataEditListItem = []

let tempAdd = {} /// kalau di so tempAddAdd
let tempEdit = {} //// kalau di so tempAddEdit
let tempIndexEdit = 0
let tempEditAdd = {}
let tempEditEdit = {}
let tipeform = ''
let tipeformitem = ''

$(document).ready(function () {
  gpAktifkanTabel(1);
  window.doSetHeader(1, false);
  {{-- PRS Belum + Sudah Otorisasi digabung jadi satu tabel. --}}
  lastTabelRows = [].concat(@json($listData), @json($listData3));
  reinitTabel();

  gpAktifkanTabel(3);
  window.doSetHeader(3, false);
  lastTabel2Rows = @json($listData2);
  reinitTabel2();

  gpAktifkanTabel(1);

  gpInitReportTableSekali();

  loadGudangOptions();

  $('#nav-home-tab').on('shown.bs.tab', function () {
    gpAktifkanTabel(1);
    if (typeof ReportTable !== 'undefined') { ReportTable.refresh(); }
  });
  $('#nav-profile-tab').on('shown.bs.tab', function () {
    gpAktifkanTabel(3);
    if (typeof ReportTable !== 'undefined') { ReportTable.refresh(); }
  });

  {{-- Box "Search:" bawaan DataTables di modal Barang dibuat ulang setiap
       pickerKasInit() memanggil .destroy(), jadi diikat via delegated event
       supaya tetap jalan walau elemennya diganti. keyup dipakai (bukan keypress)
       -- input bawaan DataTables type="search" tidak selalu memicu event
       keypress saat Enter ditekan di semua browser. --}}
  $(document).on('keyup', '#tabel_add_list_item_filter input', searchBarangAll);

  // === SEARCH BARANG via ENTER ===
  document.getElementById("input_add_add_kodebarang").addEventListener("keypress", function (e) {
    if (e.which == 13) {
      let search = this.value.trim();

      const sales = document.getElementById("input_sales_nama").value.trim();
      const customer = document.getElementById("input_customer_nama").value.trim();
      const gudang = document.getElementById("input_gudang").value.trim();

      if (!sales || !customer || !gudang) {
        alertify.warning("Silakan isi terlebih dahulu Customer, Sales, dan Gudang Asal");
        return;
      }

      if (!search) {
        alertify.warning("Silakan ketik kode atau nama barang terlebih dahulu.");
        return;
      }

      $("#tabel_data_add_list_item")
        .empty()
        .append(`<tr><td class="text-center" colspan="4">Mencari data...</td></tr>`);

      $.ajax({
        url: "{!! url('permintaansamplelistbarang') !!}",
        type: "get",
        async: false,
        data: { search: search },
        success: function (res) {
          dataAddListItem = res;

          if (!res.length) {
            $("#formAddListItem").modal("show");
            $("#tabel_data_add_list_item")
              .empty()
              .append(`<tr><td class="text-center" colspan="4">Tidak ada data</td></tr>`);
            return;
          }

          if (res.length === 1) {
            buttonAddAddInsertItem(0);
            return;
          }

          document.getElementById("exampleModalLabel").innerHTML = 'Barang'
          $("#formAddListItem").modal("show");

          let rowTable = "";
          res.forEach((item, i) => {
            rowTable += `
              <tr class="pick-row" onclick="buttonAddAddInsertItem(${i})">
                <td>${item.Kodebrg}</td>
                <td>${item.NamaBrg}</td>
                <td>${item.NamaMerk ?? ""}</td>
                <td>${item.partNumber ?? ""}</td>
              </tr>`;
          });

          $("#tabel_data_add_list_item").empty().append(rowTable);
          pickerKasInit('tabel_add_list_item', { order: [[0, 'asc']] })
        },
        error: function (err) {
          console.log(err);
          alertify.warning("Terjadi kesalahan, silakan refresh browser");
        },
      });
    }
  });

  // customer
  const elCustomer = document.getElementById("input_customer_nama");
  if (elCustomer) {
    elCustomer.addEventListener("keypress", function (e) {
      if (e.which == 13) {
        const search = this.value.trim();
        if (!search) {
          alertify.warning("Silakan ketik kode atau nama customer terlebih dahulu.");
          return;
        }

        $("#tabel_data_add_list_customer").html(
          `<tr><td class="text-center" colspan="4">Mencari data...</td></tr>`
        );

        $.ajax({
          url: "{{ url('permintaansamplelistcustomer') }}",
          type: "get",
          async: false,
          data: { search: search },
          success: function (res) {
            if (!res.length) {
              $("#modalAddListCustomer").modal("show");
              $("#tabel_data_add_list_customer").html(
                `<tr><td class="text-center" colspan="4">Tidak ada data</td></tr>`
              );
              return;
            }

            if (res.length === 1) {
              const c = res[0];
              buttonAddPickCustomer(c.KodeCustSupp, c.NamaCustSupp, c.Alamat);
              return;
            }

            let rows = "";
            res.forEach((item) => {
              rows += `
                <tr class="pick-row"
                  onclick="buttonAddPickCustomer('${item.KodeCustSupp ?? ""}', '${item.NamaCustSupp ?? ""}', '${item.Alamat ?? ""}')">
                  <td>${item.KodeCustSupp ?? ""}</td>
                  <td>${item.NamaCustSupp ?? ""}</td>
                  <td>${item.Alamat ?? ""}</td>
                  <td>${item.NamaKota ?? ""}</td>
                </tr>`;
            });

            $("#tabel_data_add_list_customer").html(rows);
            $("#modalAddListCustomer").modal("show");
            pickerKasInit('tabel_add_list_customer', { order: [[0, 'asc']] })
          },
          error: function (err) {
            console.log(err);
            alertify.warning("Terjadi kesalahan, silakan refresh browser.");
          },
        });
      }
    });
  }

  // sales
  const elSales = document.getElementById("input_sales_nama");
  if (elSales) {
    elSales.addEventListener("keypress", function (e) {
      if (e.which == 13) {
        const search = this.value.trim();
        if (!search) {
          alertify.warning("Silakan ketik kode/NIK/nama sales terlebih dahulu.");
          return;
        }

        $("#tabel_data_add_list_sales").html(
          `<tr><td class="text-center" colspan="2">Mencari data...</td></tr>`
        );

        $.ajax({
          url: "{{ url('permintaansamplelistsales') }}",
          type: "get",
          async: false,
          data: { search: search },
          success: function (res) {
            if (!res.length) {
              $("#modalAddListSales").modal("show");
              $("#tabel_data_add_list_sales").html(
                `<tr><td class="text-center" colspan="2">Tidak ada data</td></tr>`
              );
              return;
            }

            if (res.length === 1) {
              const s = res[0];
              buttonAddPickSales(s.namaSls, s.KodeSls);
              return;
            }

            let rows = "";
            res.forEach((item) => {
              rows += `
                <tr class="pick-row" onclick="buttonAddPickSales('${item.namaSls ?? ""}', '${item.KodeSls ?? ""}')">
                  <td>${item.NIK ?? ""}</td>
                  <td>${item.namaSls ?? ""}</td>
                </tr>`;
            });

            $("#tabel_data_add_list_sales").html(rows);
            $("#modalAddListSales").modal("show");
            pickerKasInit('tabel_add_list_sales', { order: [[1, 'asc']] })
          },
          error: function (err) {
            console.log(err);
            alertify.warning("Terjadi kesalahan, silakan refresh browser.");
          },
        });
      }
    });
  }

  // loadAll();
});



function onChangeHeader (field , idvalue) {
  let _token  = $("#_token").val()
  console.log(field, idvalue)
  let onChangeValue  = $(`#${idvalue}`).val()
  let nobukti  = $(`#input_add_nobukti`).val()
  console.log(onChangeValue , nobukti)


  console.log({
    _token : _token,
    field,
    nobukti,
    value: onChangeValue

  })

  $.ajax({
      url: "{!! url('permintaansampleonchangeheader') !!}",
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
        const fieldLabels = {
            KODESLS: 'Sales',
            TGLKIRIM: 'Tanggal Kirim',
            NOTE: 'Keterangan'
        };

        const label = fieldLabels[field] || field;
        alertify.success(`${label} berhasil diupdate`);
        }

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })

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

function loadAll () {
  let _token = $("#_token").val();
  let awal = $('#gpTanggalAwal').val()
  let akhir = $('#gpTanggalAkhir').val()

  $.ajax({
    url: "{!! url('permintaansampleloadall') !!}",
    type: "get",
    async: false,
    data: {
      date1: awal,
      date2: akhir
    },
    success: function (res) {
      lastTabelRows = [].concat(res.belum_otorisasi, res.sudah_otorisasi)
      lastTabel2Rows = res.outstanding
      reinitTabel()
      reinitTabel2()
    },
    error: function (xhr, status, error) {
      alert("Gagal load data: " + error);
    }
  });
}

function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: "{!! url('permintaansampledetailCetak') !!}",
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
                  <div class="pb-1" style="width: 100%">Dari : ${dataPrint[0].NAMACUSTSUPP ?? '-'}</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
              </div>


              <div style="width: 38%">
                <div style="display: flex; width: 100%">
                  <h2 class="m-0 pb-2">PERMINTAAN SAMPLE</h2>
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
                    <td class="text-center" style="width: 20%">QTY</td>
                    <td class="text-center" style="width: 20%">SATUAN</td>
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



         tempPrintStr += `
         <tr>
         <td class="text-align: center"
               style="width: 2%; ">${z+1}</td>
         <td class="text-align: left"
               style="width: 50%;">${itemSub.NAMABRG}</td>
         <td class="text-align: text-right"
               style="width: 20%;  "> ${itemSub.QNTCETAK ? parseFloat(itemSub.QNTCETAK).toFixed(2) : ''}</td>
         <td class="text-align: text-right"
               style="width: 20%;  "> ${itemSub.SATX}</td>
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

         </span>
         </div>


           <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: -15px ; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%">Diajukan Oleh</td>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%">Menyetujui</td>
               <td class="no-border text-center" style="width: 10%"></td>
             </tr>
             <tr style="height: 2.5rem">
               <td class="no-border">&nbsp;</td>
             </tr>

             <tr>
              <td class="no-border px-2">
               </td>
               <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
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
    url: "{!! url('permintaansampleupdateotorisasi') !!}",
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
        url: "{!! url('permintaansampleupdatebatalotorisasi') !!}",
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


function buttonAddListCustomer () {
  console.log('buttonAddListCustomer');

  $.ajax({
    url: "{{ url('permintaansamplelistcustomer') }}",
    type: "get",
    async: false,
    success: function(res) {
      console.log(res);

      let rowTable = ``;
      res.forEach((item, i) => {
        rowTable += `
          <tr class="pick-row" onclick="buttonAddPickCustomer('${item.KodeCustSupp}', '${item.NamaCustSupp}', '${item.Alamat}')">
            <td>${item.KodeCustSupp}</td>
            <td>${item.NamaCustSupp}</td>
            <td>${item.Alamat}</td>
            <td>${item.NamaKota}</td>
          </tr>`;
      });

      if (!res.length) {
        rowTable = `<tr><td class="text-center" colspan="4">Tidak ada data</td></tr>`;
      }

      document.getElementById("tabel_data_add_list_customer").innerHTML = rowTable;
      pickerKasInit('tabel_add_list_customer', { order: [[0, 'asc']] })

      $('#modalAddListCustomer').modal('show');
    },
    error: function(err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser.');
    }
  });
}

function buttonAddPickCustomer (kode, nama, alamat) {
  $('#input_customer_nama').val(nama);  
  $('#input_customer').val(kode);
  $('#modalAddListCustomer').modal('hide');
}

function buttonAddListSales () {
  console.log('buttonAddListSales');

  $.ajax({
    url: "{{ url('permintaansamplelistsales') }}",
    type: "get",
    async: false,
    success: function(res) {
      console.log(res);

      let rowTable = ``;
      res.forEach((item, i) => {
        rowTable += `
          <tr class="pick-row" onclick="buttonAddPickSales('${item.namaSls}', '${item.KodeSls}')">
            <td>${item.NIK}</td>
            <td>${item.namaSls}</td>
          </tr>`;
      });

      if (!res.length) {
        rowTable = `<tr><td class="text-center" colspan="2">Tidak ada data</td></tr>`;
      }

      document.getElementById("tabel_data_add_list_sales").innerHTML = rowTable;
      pickerKasInit('tabel_add_list_sales', { order: [[1, 'asc']] })

      $('#modalAddListSales').modal('show');
    },
    error: function(err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser.');
    }
  });
}

function buttonAddPickSales (nama, kode) {
  $('#input_sales_nama').val(nama);
  $('#input_sales').val(kode);     
  $('#modalAddListSales').modal('hide');
  onChangeHeader('KODESLS', 'input_sales');
}

{{-- Gudang Asal sekarang <select> biasa (bukan picker modal lagi), sama seperti
     koreksistock.blade.php/perintahopname.blade.php. --}}
function loadGudangOptions () {
  $.ajax({
    url: "{{ url('permintaansamplelistgudang') }}",
    type: "get",
    async: false,
    success: function (res) {
      let options = '<option value="">- Pilih Gudang -</option>'
      res.forEach((item) => {
        options += `<option value="${item.KodeGdg}">${item.KodeGdg} - ${item.NamaGdg}</option>`
      });
      document.getElementById("input_gudang").innerHTML = options
    },
    error: function (err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan saat mengambil data gudang.');
    }
  });
}

function buttonAddListBatal() {
  $('#modalAddListCustomer').modal('hide');
  $('#modalAddListSales').modal('hide');
}

// function closeListItemAdd () {
//   $("#formAddListItem").modal('toggle')
//   // document.getElementById("input_add_add_kodebarang").value = dataAddListItem[i].KODEBRG
//   // document.getElementById("input_add_add_keterangannama").value = dataAddListItem[i].NAMABRG
//   var modal = document.getElementById("page2");
//   modal.style.display = "block";
// }

function submitAddEdit () {
    console.log('submitAddEdit');

    let checkDate = new Date($("#input_add_tanggal").val());
    let periode_bulan = document.getElementById("periode_bulan").value;
    let periode_tahun = document.getElementById("periode_tahun").value;

    if (checkDate.getFullYear() !== Number(periode_tahun) || (checkDate.getMonth() + 1) !== Number(periode_bulan)) {
        alertify.warning("Tanggal tidak sesuai periode");
        return;
    }

    let jmlrecord = (tipeform === "edit") ? 1 : 0;

    let _token = $("#_token").val();
    let choice = "U";
    let nobukti = $("#input_add_nobukti").val();
    let tanggal = $("#input_add_tanggal").val();
    let tanggalkirim = $("#input_add_tanggalkirim").val();
    let sales = $("#input_sales").val();
    let kodebarang = $("#input_add_add_kodebarang").val();
    let keterangannama = $("#input_add_add_keterangannama").val();
    let satuan = $("#input_add_add_satuan").val();
    let qnt = parseFloat($("#input_add_add_qnt").val()) || 0;
    let keterangan = $("#input_keterangan").val() || '';

    if (!kodebarang || !satuan || qnt <= 0) {
        alertify.warning("Lengkapi semua data wajib");
        return;
    }

    let barang = tempEdit;
    console.log("tempEdit:", tempEdit);
    let isi = 0;
    let nosat = parseInt(satuan);
    let qnt1 = 0;
    let sat1 = '';
    let sat2 = '';

    barang.ISI1 = barang.ISI1 || 1;
    barang.ISI2 = barang.ISI2 || 1;
    barang.ISI3 = barang.ISI3 || 1;

    if (!barang.SAT1 || !barang.SAT2) {
        alertify.warning("Data satuan tidak lengkap, silakan refresh atau pilih ulang barang");
        return;
    }

    if (nosat === 1) {
        qnt1 = qnt * barang.ISI1;
        satuan = barang.SAT1;
        isi = barang.ISI1;
    } else if (nosat === 2) {
        qnt1 = qnt * barang.ISI2;
        satuan = barang.SAT2;
        isi = barang.ISI2;
    } else if (nosat === 3) {
        qnt1 = qnt * barang.ISI3;
        satuan = barang.SAT3;
        isi = barang.ISI3;
    } else {
        alertify.warning("Satuan tidak valid");
        return;
    }

    sat1 = barang.SAT1 ?? satuan ?? 'PCS';
    sat2 = barang.SAT2 ?? satuan ?? 'PCS';

    keterangannama = keterangannama.replace(/["']/g, '');
    keterangan = keterangan.replace(/["']/g, '');

    let nourut = parseInt(barang.NOURUT); 
    let urut = parseInt(barang.URUT);

    console.log("URUT yg dikirim:", urut);
    console.log("QNT yg dikirim:", qnt);
    console.log("SAT1 dikirim:", sat1);
    console.log("SAT2 dikirim:", sat2);

    $.ajax({
        url: "{!! url('permintaansamplespadd') !!}",
        type: "POST",
        async: false,
        data: {
            _token,
            choice,
            nobukti,
            nourut,
            tanggal,
            note: keterangan,
            urut: urut,              
            kodebarang,
            gdgasal: barang.gdgAsal,
            gdgtujuan: barang.gdgTujuan || '',
            satuan,                      
            sat_1: sat1,
            sat_2: sat2,
            qnt,
            qnt2: qnt,
            nosat,
            isi,
            kodecustsupp: barang.KODECUSTSUPP,
            kodesls: sales,
            pbonus: barang.pbonus || 0,
            maxol: 0,
            tglkirim: tanggalkirim,
            refpr: barang.RefPR,
            pkonsi: 0,
            lokasi: barang.Lokasi || '',
            keterangannama,
            jmlrecord
        },
        success: function(res) {
            console.log('respoedit', res);
            loadAll();
            $('.showhide').hide();
            refreshDataTableAdd(nobukti);
            alertify.success('Berhasil edit item');
        },
        error: function(err) {
            console.log('Error saat submit:', err);
            alertify.warning('Terjadi kesalahan, silakan refresh browser');
        }
    });
}

function buttonAddEditItem (index) {
  tipeformitem = 'edit';
  let _token = $("#_token").val();
  console.log('buttonAddEditItem');

  $('.showhide').hide();
  document.getElementById("buttonAddListKodeBarang").disabled = true;
  document.getElementById("input_add_add_kodebarang").disabled = true;

  tempEdit = dataTableAdd[index];
  tempIndexEdit = index;

  tempEdit.SAT1 = tempEdit.SAT_1;
  tempEdit.SAT2 = tempEdit.SAT_2;
  tempEdit.SAT3 = tempEdit.SAT_3;
  tempEdit.ISI1 = tempEdit.ISI1;
  tempEdit.ISI2 = tempEdit.ISI2;
  tempEdit.ISI3 = tempEdit.ISI3;

  // Isi dropdown satuan
  let selectOption = '<option value=0 selected>Pilih Satuan</option>';
  if (tempEdit.SAT_1) {
    selectOption += `<option value=1>${tempEdit.SAT_1}</option>`;
  }
  if (tempEdit.SAT_2) {
    selectOption += `<option value=2>${tempEdit.SAT_2}</option>`;
  }
  if (tempEdit.SAT_3) {
    selectOption += `<option value=3>${tempEdit.SAT_3}</option>`;
  }
  document.getElementById("input_add_add_satuan").innerHTML = selectOption;

  // Isi input
  document.getElementById("input_add_add_kodebarang").value = tempEdit.KODEBRG;
  document.getElementById("input_add_add_keterangannama").value = tempEdit.NamaBrg;
  document.getElementById("input_add_add_qnt").value = parseFloat(tempEdit.QNT).toFixed(2);
  document.getElementById("input_add_add_satuan").value = String(tempEdit.NoSat);

  // Tampilkan mode edit
  $('#h4AddAddItem').hide();
  $('#h4AddEditItem').show();
  $('#submitAddAdd').hide();
  $('#submitAddEdit').show();
  $('#addAddItem').show();

  document.getElementById("input_add_add_kodebarang").scrollIntoView();
}

function buttonEdit (nobukti) {
  tipeform = 'edit';

  let akses = $("#akses_iskoreksi").val();
  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  $.ajax({
    url: "{!! url('permintaansamplespdetail') !!}",
    type: "get",
    async: false,
    data: { nobukti },
    success: function (res) {
      if (!res || !res.length) {
        alertify.error("Data tidak ditemukan");
        return;
      }

      const data = res[0];

      if (data.IsOtorisasi1 == 1) {
        alertify.warning("Data sudah diotorisasi dan tidak dapat diedit");
        return;
      }

      $('.showhide').hide();
      cleanFormAdd();
      lockFormAdd();

      dataTableAdd = res;
      dataHeaderAdd = data;

      // Format tanggal
      let date = new Date(data.TANGGAL);
      let dateFormatted = data.TANGGAL?.substring(0, 10) ?? '';

      let dateKirim = new Date(data.TglKirim);
      let tglKirimFormatted = data.TglKirim?.substring(0, 10) ?? '';

      // Isi form header
      $('#input_add_tanggal').val(dateFormatted);
      $('#input_add_tanggalkirim').val(tglKirimFormatted);
      $('#input_add_nobukti').val(data.NOBUKTI);
      $('#input_add_nourut').val(data.Nourut);
      $('#input_refpr').val(data.RefPR);
      $('#input_keterangan').val(data.Keterangan);
      $('#input_sales_nama').val(data.NAMASLS);
      $('#input_sales').val(data.KODESLS);
      $('#input_customer_nama').val(data.NamaCustSupp);
      $('#input_customer').val(data.KODECUSTSUPP);
      $('#input_gudang').val(data.gdgAsal);

      // Isi tabel item
      let rowTable = "";
      dataTableAdd.forEach((item, i) => {
        rowTable += `
          <tr>
            <td>${item.KODEBRG}</td>
            <td>${item.NamaBrg}</td>
            <td>${item.gdgAsal} - ${item.NamaGgdAsal}</td>
            <td>${item.gdgTujuan} - ${item.NamaGgdTujuan}</td>
            <td class="text-right">${parseFloat(item.QNT).toLocaleString()}</td>
            <td>${item.SAT_1}</td>
            <td class="text-center">
              <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})" title="Edit"><i class="bi bi-pen"></i></button>
              <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDeleteItem(${i})" title="Hapus"><i class="bi bi-trash"></i></button>
            </td>
          </tr>`;
      });

      $("#tabel_data_add").html(rowTable);

      $('#page1').hide();
      $('#page3').hide();
      $('#page2').show();
    },
    error: function (err) {
      console.error("Error saat load detail:", err);
      alertify.error("Gagal load detail");
    }
  });
}


function buttonAddDeleteItem (index) {
  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  let data = dataTableAdd[index];

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + data.KODEBRG + ' ?',
    function () {
      let _token = $("#_token").val();
      let choice = "D";
      let nobukti = $("#input_add_nobukti").val();
      let tanggal = $("#input_add_tanggal").val();
      let kodebarang = data.KODEBRG;
      let qnt = data.QNT;
      let nosat = data.NoSat;
      let satuan = data.SAT_1;
      let isi = data.ISI;
      let urut = data.URUT;
      let keterangan = data.NamaBrg;
      let refpr = data.RefPR || '';
      let gudang = data.GDGAsal || '';
      let sales = $("#input_sales").val();
      let customer = $("#input_customer").val();
      let tglkirim = data.TglKirim || tanggal;
      let jmlrecord = 0;

      $.ajax({
        url: "{!! url('permintaansamplespdelete') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          choice,
          nobukti,
          tanggal,
          kodebarang,
          qnt,
          nosat,
          satuan,
          isi,
          urut,
          note: keterangan,
          refpr,
          gdgasal: gudang,
          kodesls: sales,
          kodecustsupp: customer,
          tglkirim,
          jmlrecord
        },
        success: function (res) {
          alertify.success("Item sudah dihapus");
          loadAll();
          refreshDataTableAdd(nobukti);

          if (!dataTableAdd || dataTableAdd.length === 0) {
            document.getElementById("btn_customer").disabled = false;
            document.getElementById("input_customer_nama").disabled = false;
            document.getElementById("input_gudang").disabled = false;
            unlockFormAdd();
            tipeform = 'new';
          }
        },
        error: function (err) {
          console.log(err);
          alertify.error("Gagal menghapus item");
        }
      });
    },
    function () {
      console.log('User cancelled delete');
    });
}


function buttonDetail (nobukti) {
  
  $.ajax({
    url: "{!! url('permintaansamplespdetail') !!}",
    type: "get",
    async: false,
    data: {
      nobukti
    },
    success: function(res) {

      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `<tr>
        <td>${item.KODEBRG}</td>
        <td>${item.NamaBrg}</td>
        <td>${item.gdgAsal} - ${item.NamaGgdAsal}</td>
        <td>${item.gdgTujuan} - ${item.NamaGgdTujuan}</td>
        <td class="text-right">${parseFloat(item.QNT).toLocaleString()}</td>
        <td>${item.SAT_1}</td>
        </tr>`
      });

      let date = new Date(res[0].TANGGAL);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
      $('#input_detail_tanggal').val(date1)

      let dateKirim = new Date(res[0].TglKirim);
      let dayKirim = ("0" + dateKirim.getDate()).slice(-2);
      let monthKirim = ("0" + (dateKirim.getMonth() + 1)).slice(-2);
      let tglKirimFormatted = dateKirim.getFullYear() + "-" + monthKirim + "-" + dayKirim;
      $('#input_detail_tanggalkirim').val(tglKirimFormatted);

      document.getElementById("tabel_data_detail").innerHTML  = rowTable
      document.getElementById("input_detail_nobukti").value  = res[0].NOBUKTI
      document.getElementById("input_detail_customer_nama").value  = res[0].NamaCustSupp
      document.getElementById("input_detail_sales_nama").value  = res[0].NAMASLS
      document.getElementById("input_detail_gudang_nama").value  = res[0].NamaGgdAsal
      document.getElementById("input_detail_refpr").value  = res[0].RefPR
      document.getElementById("input_detail_keterangan").value  = res[0].Keterangan

  }})
  $("#page3").show();
  $("#page1").hide();
}


function setNewNoBukti () {
  $.ajax({
    url: "{!! url('permintaansamplespnobukti') !!}",
    type: "get",
    async: false,
    success: function(res) {
      console.log("RESPON NOBUKTI", res);
      if (res && res.length > 0) {
        const nobukti = res[0].NoBukti || res[0].Nobukti;
        const nourut = res[0].NoUrut || res[0].Nourut;

        document.getElementById("input_add_nobukti").value = nobukti;
        document.getElementById("input_add_nourut").value = nourut;
      } else {
        alertify.error("Gagal mendapatkan No Bukti dari server.");
      }
    },
    error: function(err) {
      console.error("Gagal ambil nobukti", err);
      alertify.error("Gagal mengambil data dari server.");
    }
  });
}

function buttonAdd () {
  tipeform = 'add'
  $('.showhide').hide();
  cleanFormAdd()
  unlockFormAdd();

  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  $.ajax({
    url: "{!! url('permintaansamplespnobukti') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    }})
    dataTableAdd = []

  refreshDataTableAdd()
  // $("#form").modal('toggle')
  $('#page1').hide();
  $('#page2').show();
}

function closeListItemAdd () {
  $("#formAddListItem").modal('toggle')
  // document.getElementById("input_add_add_kodebarang").value = dataAddListItem[i].KODEBRG
  // document.getElementById("input_add_add_keterangannama").value = dataAddListItem[i].NAMABRG
  var modal = document.getElementById("page2");
  modal.style.display = "block";
}

function buttonCloseForm () {
  $('#page2').hide();
  $('#page3').hide();
  $('#page1').show();
  // loadAll();
}

function buttonAddListKodeBarang () {
  const sales = document.getElementById("input_sales_nama").value.trim();
  const customer = document.getElementById("input_customer_nama").value.trim();
  const gudang = document.getElementById("input_gudang").value.trim();

  if (!sales || !customer || !gudang) {
    alertify.warning("Silakan isi terlebih dahulu Customer, Sales, dan Gudang Asal");
    return;
  }

  document.getElementById("exampleModalLabel").innerHTML = 'Barang'
  document.getElementById("tabel_data_add_list_item").innerHTML = `
    <tr><td class="text-center" colspan="4">Silakan ketik pencarian</td></tr>`;
  {{-- Box "Search:" bawaan DataTables dibuat begitu modal dibuka, supaya langsung
       bisa dipakai tanpa menunggu pencarian pertama. --}}
  pickerKasInit('tabel_add_list_item', { order: [[0, 'asc']], language: { emptyTable: 'Silakan ketik pencarian' } })

  // Tampilkan modal
  $('#formAddListItem').modal('show');

  $('#formAddListItem').on('shown.bs.modal', function () {
    $('#tabel_add_list_item_filter input').trigger('focus')
  })
}

{{-- Dulu diikat ke box search hardcoded (#input_search_barang_all) yang terpisah
     dari box "Search:" bawaan DataTables -- sekarang diikat langsung ke box
     bawaan itu (#tabel_add_list_item_filter input), lihat binding delegated di
     $(document).ready() di bawah. --}}
function searchBarangAll (e) {
  if (e.which === 13) {
    console.log('Enter ditekan');

    const search = e.target.value.trim();

    if (!search) {
      document.getElementById("tabel_data_add_list_item").innerHTML = `
        <tr><td class="text-center" colspan="4">Silakan ketik pencarian</td></tr>
      `;
      return;
    }

    // AJAX untuk cari barang
    $.ajax({
      url: "{!! url('permintaansamplelistbarang') !!}",
      type: "get",
      async: false,
      data: {
        search,
        isagen: 0
      },
      success: function (res) {
        console.log(res);
        dataAddListItem = res;
        let rowTable = "";

        if (res.length === 0) {
          document.getElementById("tabel_data_add_list_item").innerHTML = `
            <tr><td class="text-center" colspan="4">Tidak ada data</td></tr>
          `;
          return;
        }

        res.forEach((item, i) => {
          rowTable += `
            <tr class="pick-row" onclick="buttonAddAddInsertItem(${i})">
              <td>${item.Kodebrg}</td>
              <td>${item.NamaBrg}</td>
              <td>${item.NamaMerk ?? ''}</td>
              <td>${item.partNumber ?? ''}</td>
            </tr>`;
        });

        document.getElementById("tabel_data_add_list_item").innerHTML = rowTable;
        pickerKasInit('tabel_add_list_item', { order: [[0, 'asc']] })
      },
      error: function (err) {
        console.log(err);
        alertify.warning('Terjadi kesalahan, silakan refresh browser');
      }
    });
  }
}

function buttonAddAddInsertItem (i) {
  console.log('index:', i);
  console.log('dataAddListItem:', dataAddListItem);

  // Cek jika data valid
  if (!dataAddListItem[i]) {
    alertify.warning('Data tidak valid');
    return;
  }

  let item = dataAddListItem[i];
  
  $('#input_add_add_kodebarang').val(item.Kodebrg);
  $('#input_add_add_keterangannama').val(item.NamaBrg);

  let satuanOptions = '';
  if (item.SAT1) satuanOptions += `<option value="${item.SAT1}">${item.SAT1}</option>`;
  if (item.SAT2) satuanOptions += `<option value="${item.SAT2}">${item.SAT2}</option>`;
  if (item.SAT3) satuanOptions += `<option value="${item.SAT3}">${item.SAT3}</option>`;
  $('#input_add_add_satuan').html(satuanOptions);

  $('#formAddListItem').modal('hide');

  setTimeout(() => {
    document.getElementById("input_add_add_qnt").focus();
    document.getElementById("input_add_add_qnt").select();
  }, 300);
  // (Opsional) Reset nilai lainnya jika perlu
  // $('#input_add_add_qnt').val(0.00);
  // $('#input_add_add_keterangan').val('');
}

  function submitAddAdd () {
  console.log('submitAddAdd');

  let checkDate = new Date($("#input_add_tanggal").val());
  let periode_bulan = document.getElementById("periode_bulan").value;
  let periode_tahun = document.getElementById("periode_tahun").value;

  if (checkDate.getFullYear() !== Number(periode_tahun) || (checkDate.getMonth() + 1) !== Number(periode_bulan)) {
    alertify.warning("Tanggal tidak sesuai periode");
    return;
  }

  let jmlrecord = (tipeform === 'edit') ? 1 : 0;
  let _token = $("#_token").val();
  let choice = "I";
  let nobukti = $("#input_add_nobukti").val();
  let nourut = $("#input_add_nourut").val();
  let sales = $("#input_sales").val();
  let customer = $("#input_customer").val();
  let gudang = $("#input_gudang").val();
  let refpr = $("#input_refpr").val();
  let tanggalkirim = $("#input_add_tanggalkirim").val();
  let tanggal = $("#input_add_tanggal").val();
  let kodebarang = $("#input_add_add_kodebarang").val();
  let keterangannama = $("#input_add_add_keterangannama").val();
  let satuan = $("#input_add_add_satuan").val();
  let qnt = parseFloat($("#input_add_add_qnt").val()) || 0;
  let keterangan = $("#input_keterangan").val() || '';

  console.log({
  sales,
  customer,
  gudang,
  refpr,
  tanggal,
  tanggalkirim,
  kodebarang,
  satuan,
  qnt
});

  if (!sales || !customer || !gudang || !tanggal || !tanggalkirim || !kodebarang || !satuan || qnt <= 0) {
    alertify.warning("Lengkapi semua data wajib");
    return;
  }

  let barang = dataAddListItem.find(item => item.Kodebrg === kodebarang);
  if (!barang) {
    alertify.warning("Barang tidak ditemukan di daftar");
    return;
  }

  let refpr_valid = true;
  $.ajax({
    url: "{!! url('permintaansamplecekrefpr') !!}",
    type: "get",
    data: { refpr, nobukti },
    async: false,
    success: function (res) {
      if (res.exists) {
        alertify.warning("Ref PR sudah ada");
        refpr_valid = false;
      }
    },
    error: function (err) {
      console.log(err);
      alertify.warning("Gagal mengecek Ref PR");
      refpr_valid = false;
    }
  });
  if (!refpr_valid) return;

  let nosat = 0;
  let isi = 0;
  if (satuan === barang.SAT1) {
    nosat = 1;
    isi = barang.ISI1;
  } else if (satuan === barang.SAT2) {
    nosat = 2;
    isi = barang.ISI2;
  } else if (satuan === barang.SAT3) {
    nosat = 3;
    isi = barang.ISI3;
  } else {
    alertify.warning("Satuan tidak valid");
    return;
  }

  let sat_1 = satuan;
  let sat_2 = satuan;
  let qnt2 = qnt;

  keterangannama = keterangannama.replace(/["']/g, '');
  keterangan = keterangan.replace(/["']/g, '');

  $.ajax({
    url: "{!! url('permintaansamplespadd') !!}",
    type: "POST",
    async: false,
    data: {
      _token,
      choice,
      nobukti,
      nourut,
      tanggal,
      note: keterangan,
      urut: 0,
      kodebarang,
      gdgasal: gudang,
      gdgtujuan: '',
      satuan,
      sat_1,
      sat_2,
      qnt,
      qnt2,
      nosat,
      isi,
      kodecustsupp: customer,
      kodesls: sales,
      pbonus: 0,
      maxol: 0,
      tglkirim: tanggalkirim,
      refpr,
      pkonsi: 0,
      lokasi: '',
      keterangannama,
      jmlrecord
    },
    success: function (res) {
      console.log('respoadd', res);
      if (res == 1) {
        loadAll();
        tipeform = 'edit';
        cleanFormAddAdd();
        refreshDataTableAdd(nobukti);

        document.getElementById("btn_customer").disabled = true;
        document.getElementById("input_customer_nama").disabled = true;
        document.getElementById("input_gudang").disabled = true;
        document.getElementById("input_add_tanggal").disabled = true;

        alertify.success('Berhasil menambah item');
      } else if (res == 2) {
        setNewNoBukti();
        alertify.warning('Nobukti telah direfresh silahkan submit ulang');
      }
    },
    error: function (err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan silakan refresh browser');
    }
  });
}

function buttonEditAddItem () {

  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  $('.showhideedit').hide();

  tempEditAdd = {}
  document.getElementById("input_edit_add_refso").value = "-"
  document.getElementById("input_edit_add_nopocust").value = ""
  document.getElementById("input_edit_add_kodebarang").value = ""
  document.getElementById("input_edit_add_keterangannama").value = ""
  document.getElementById("input_edit_add_qnt").value = "0.00"
  document.getElementById("input_edit_add_keterangan").value = ""
  document.getElementById("input_edit_add_satuan").innerHTML = '<option value=0 selected>Pilih Satuan</option>'

  $('#editAddItem').show();
}

function buttonAddAddItem () {
  tipeformitem = 'add'
  $('.showhide').hide();
  tempAdd = {}
  // document.getElementById("inlineRadio1").checked = false
  // document.getElementById("input_add_add_refso").value = "-"
  // document.getElementById("input_add_add_nopocust").value = ""
  document.getElementById("buttonAddListKodeBarang").disabled = false;
  document.getElementById("input_add_add_kodebarang").disabled = false;
  document.getElementById("input_add_add_kodebarang").value = ""
  document.getElementById("input_add_add_keterangannama").value = ""
  document.getElementById("input_add_add_qnt").value = "0.00"
  // Menentukan isi dropdown berdasarkan tempAdd
  let satuanOptions = `<option value="" selected disabled>Pilih Satuan</option>`;
  
  if (tempAdd.SAT1) {
    satuanOptions += `<option value="${tempAdd.SAT1}">[1] ${tempAdd.SAT1}</option>`;
  }
  if (tempAdd.SAT2) {
    satuanOptions += `<option value="${tempAdd.SAT2}">[2] ${tempAdd.SAT2}</option>`;
  }
  if (tempAdd.SAT3) {
    satuanOptions += `<option value="${tempAdd.SAT3}">[3] ${tempAdd.SAT3}</option>`;
  }
  document.getElementById("input_add_add_satuan").innerHTML = satuanOptions;


  $('#h4AddAddItem').show();
  $('#h4AddEditItem').hide();
  $('#submitAddAdd').show();
  $('#submitAddEdit').hide();
  $('#addAddItem').show();
}

function closeShowHideAdd () {
  $('.showhide').hide();
}

function closeShowHideEdit () {
  $('.showhideedit').hide();
}


function refreshDataTableAdd (NOBUKTI = "") {
  console.log('refreshDataTableAdd', NOBUKTI);

  let _token = $("#_token").val();

  if (!NOBUKTI) {
    document.getElementById("tabel_data_add").innerHTML = `
      <tr>
        <td class="text-center" colspan="9">Belum ada barang</td>
      </tr>`;
    
    document.getElementById("btn_customer").disabled = false;
    tipeform = "new";
    return;
  }

  $.ajax({
    url: "{!! url('permintaansamplespdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token,
      nobukti: NOBUKTI
    },
    success: function (res) {
      console.log('res', res);

      if (!res.length) {
        alertify.warning("Data habis");
        document.getElementById("tabel_data_add").innerHTML = `
          <tr>
            <td class="text-center" colspan="9">Belum ada barang</td>
          </tr>`;

        $('#page3').hide();
        $('#page2').hide();
        $('#page1').show();
        document.getElementById("btn_customer").disabled = false;
        tipeform = "new";
        return;
      }

      dataTableAdd = res;
      dataHeaderAdd = res[0];

      let rowTable = "";
      dataTableAdd.forEach((item, i) => {
        rowTable += `
          <tr>
            <td>${item.KODEBRG}</td>
            <td>${item.NamaBrg}</td>
            <td>${item.gdgAsal} - ${item.NamaGgdAsal || ''}</td>
            <td>${item.gdgTujuan} - ${item.NamaGgdTujuan || ''}</td>
            <td class="text-right">${parseFloat(item.QNT).toLocaleString()}</td>
            <td>${item.SAT_1}</td>
            <td class="text-center">
              <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})" title="Edit"><i class="bi bi-pen"></i></button>
              <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDeleteItem(${i})" title="Hapus"><i class="bi bi-trash"></i></button>
            </td>
          </tr>`;
      });

      document.getElementById("tabel_data_add").innerHTML = rowTable;
    },
    error: function (err) {
      console.error("Error fetching detail:", err);
      alertify.error("Gagal memuat data");
    }
  });
}


function cleanFormAddAdd (){
  document.getElementById("input_add_add_kodebarang").value = ''
  document.getElementById("input_add_add_keterangannama").value = ''
  document.getElementById("input_add_add_qnt").value = '0.00'
  document.getElementById("input_add_add_satuan").innerHTML = '<option value=0 selected>Pilih Satuan</option>'
}

function cleanFormAdd (){
  document.getElementById("input_add_tanggal").valueAsDate = new Date()
  document.getElementById("input_add_tanggalkirim").valueAsDate = new Date()
  document.getElementById("input_customer").value = ''
  document.getElementById("input_customer_nama").value = ''
  document.getElementById("input_sales").value = ''
  document.getElementById("input_sales_nama").value = ''
  document.getElementById("input_gudang").value = ''
  document.getElementById("input_refpr").value = ''
  document.getElementById("input_keterangan").value = ''
}

function lockFormAdd (){
  document.getElementById("input_add_tanggal").disabled = true
  document.getElementById("btn_customer").disabled = true
  document.getElementById("input_customer_nama").disabled = true
  document.getElementById("input_gudang").disabled = true
  document.getElementById("input_refpr").disabled = true
}

function unlockFormAdd () {
  document.getElementById("input_add_tanggal").disabled = false
  document.getElementById("input_refpr").disabled = false
  // document.getElementById("input_gudang").disabled = false
  // document.getElementById("input_customer_nama").disabled = false
}
</script>


@endsection
