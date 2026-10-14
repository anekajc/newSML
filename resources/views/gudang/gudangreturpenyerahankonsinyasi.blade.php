@extends('newmasterTest')
@section('buttons')

@section('page-title', 'Retur Penyerahan Konsinyasi')
@section('title', 'SML - Retur Penyerahan Konsinyasi')

@endsection

@section('css')

<link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
<link rel="stylesheet" href="{!! URL::asset('css/picker-kas.css') !!}?v={{ @filemtime(base_path('public/css/picker-kas.css')) ?: '1' }}">

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

#tabel thead th,
#tabel_add thead th,
#tabel_detail thead th,
#tabel_add_list_sales thead th,
#tabel_add_list_serahsample thead th,
#tabel_add_list_item thead th {
  background: #f8f9fb !important;
  color: #6b7280 !important;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: .04em;
  font-weight: 600;
  border-bottom: 1px solid #e7e9ee;
  border-top: none;
}

#tabel tbody tr:hover {
  background-color: #f5f3ff;
}

.data-table th,
.data-table td {
  white-space: nowrap;
}

.pick-row { cursor: pointer; }
.pick-row:hover { background-color: #f5f3ff !important; }

{{-- Wadah tombol Aksi (action-buttons-wrap, bukan .po-aksi-wrap bawaan po-table-header.css)
     tetap butuh flex manual -- selector .po-list-page tidak menjangkau nama class ini. --}}
#tabel td:first-child {
  display: flex;
  gap: 4px;
  justify-content: center;
  align-items: center;
}

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
@endsection
@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id='page1' class="container-fluid mainpage po-list-page">

<div id="printContainer" style="display:none">

</div>
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

  <div class="card">
    <div class="card-body" style="padding:0;">
      <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
        {{-- RPS Belum Otorisasi + RPS Sudah Otorisasi digabung jadi satu tabel, dengan
             kolom Status dan filter Status di toolbar (client-side), sama seperti merge
             di gudangpermintaansample.blade.php/gudangpenyerahansample.blade.php. --}}
        <div class="po-toolbar">
          <input type="search" id="rkSearch1" class="po-search-inp" placeholder="Cari data">
          <div class="po-len-wrap">
            <label for="rkLen1">Tampilkan</label>
            <select id="rkLen1" class="po-len-inp">
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
              <option value="-1">Semua</option>
            </select>
          </div>
          <button class="po-btn-filter" type="button" onclick="$('#modalFilterRK').modal('show')">
            <i class="bi bi-funnel"></i> Filter
          </button>
          <div class="po-toolbar-act">
            <button id="AddVisibility" class="btn btn-chip-biru" onclick="buttonAdd()">Tambah</button>
          </div>
        </div>

        <div class="modal fade rt-filter" id="modalFilterRK">
          <div class="modal-dialog modal-md">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-funnel"></i> Filter Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterRK').modal('hide')"><span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <div class="rt-section">
                  <div class="rt-group-label">Status</div>
                  <div>
                    <label class="rt-field-label" for="rkStatus">Status Otorisasi</label>
                    <select class="rt-native" id="rkStatus">
                      <option value="" selected>Semua Status</option>
                      <option value="0">Belum Otorisasi</option>
                      <option value="1">Sudah Otorisasi</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="rt-reset-link" onclick="$('#rkStatus').val('')">Reset semua</button>
                <div class="rt-footer-buttons">
                  <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal" onclick="$('#modalFilterRK').modal('hide')">Batal</button>
                  <button type="button" class="rt-btn rt-btn-primary" onclick="rkFilterChanged(); $('#modalFilterRK').modal('hide');">Terapkan</button>
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
</div>

<div id="page2" class="container-fluid mainpage" style="display: none;">
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
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <div id='formBsGrid' class="container-fluid">
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
        </div>

        <!-- Tengah -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-3 col-form-label">Sales</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input id="input_sales_nama" type="text" class="form-control text-center" placeholder="Sales" disabled>
                        <input id="input_sales" type="hidden">
                        <button type="button" id="btn_sales" onclick="buttonAddListSales()" class="btn btn-chip-biru">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
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
      onclick="buttonAddAddItem()">Tambah Item</button>
      </div>
    </div>

    <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_add" class="table table-bordered table-striped"  >
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th colspan="3">Deskripsi Barang</th>
                  <th colspan="2">Satuan</th>
                  <th colspan="1"></th>
                </tr>
                <tr>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Gudang</th>
                  <th scope="col">Qty</th>
                  <th scope="col">Sat</th>
                  <th scope="col">Actions</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add" class="text-left" >
                <tr>
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
    <!-- ADD SUBGROUP -->
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
                    <label>No. Serah Sample</label>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="input-group mb-3">
                    <input id="input_add_add_serahsample" type="text" class="form-control text-center" placeholder="No. Serah Sample" readonly>
                    <button type="button" id="buttonAddListSerahSample" onclick="buttonAddListSerahSample()" class="btn btn-chip-biru">
                      <i class="bi bi-search"></i>
                    </button>
                  </div>
                  <input type="hidden" id="input_urut_ref">
                </div>
              </div>
            <div class="row" style="margin-top:-15px;">
                <div class="col-md-3" style="margin-top:5px;">
                  <div class="form-group">
                    <label>Gudang</label>
                  </div>
                </div>
                <div class="col-md-4">
                  <input id="input_add_add_gudang" type="text" class="form-control text-center" readonly>
                  <input type="hidden" id="input_add_add_namagudang">
                </div>
              </div>
              <div class="row" style="margin-top:-15px;">
                  <div class="col-md-3" style="margin-top:5px;">
                    <div class="form-group">
                      <label>Kode Barang</label>
                    </div>
                  </div>
                <div class="col-md-4">
                  <div class="input-group mb-4">
                  <input id="input_add_add_kodebarang" type="text" class="form-control text-center" placeholder="Kode Barang" disabled>
                  {{-- <button type="button" id="buttonAddListKodeBarang" onclick="buttonAddListKodeBarang()" class="btn btn-primary btn-sm rounded-end shadow-sm"><i class="bi bi-plus"></i></button> --}}
                  </div>
                </div>
              </div>
              <div class="row" style="margin-top:-20px;">
              <div class="col-md-3" style="margin-top:5px;">
                <div class="form-group">
                <label>Nama Barang</label>
              </div>
              </div>
              <div class="col-md-8">
                <input id="input_add_add_keterangannama" type="text" class="form-control text-center" disabled>
              </div>
            </div>
            <div class="row" style="margin-top:-15px;">
              <div class="col-md-3" style="margin-top:5px;">
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
                transition: background-color 0.3s, box-shadow 0.3s; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="submitAddAdd()">Submit Add</button>

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
                <button type="button" disabled onclick="" class="btn btn-primary">+</button>
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
                <button type="button" class="btn btn-secondary" onclick="closeShowHideAdd()">Batal</button>
                {{-- <button type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> --}}
              </div>
            </div>
          </div>

    <!-- END ADD EDIT -->

        </div>
  </div>
    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
</div>

</div> {{-- end page 2 --}}


<!-- End modal add-->

{{-- Start Modal List Serah Sample --}}
  <div class="modal fade" id="modalAddListSerahSample" role="dialog" aria-labelledby="labelSerahSample" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="max-width:95%;">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="labelSerahSample">Pilih No Penyerahan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body" style="margin-top:-30px;">
          <div class="container-fluid px-3 mt-4">
            <div class="row">
              <div class="table-responsive" style="overflow-x:auto;">
                <table id="tabel_add_list_serahsample" class="table table-bordered table-striped" style="width:100%;">
                  <thead class="text-center bg-primary text-white">
                    <tr>
                      <th style="width:160px;">No Penyerahan</th>
                      <th style="width:90px;">Tanggal</th>
                      <th style="width:50px;">Kode Customer</th>
                      <th style="width:160px;">Nama Customer</th>
                      <th style="width:110px;">Kode Barang</th>
                      <th style="width:320px;">Nama Barang</th>
                      <th style="width:50px;">Qty</th>
                      <th style="width:40px;">Gudang</th>
                    </tr>
                  </thead>
                  <tbody id="tabel_data_add_list_serahsample" class="text-left">
                    <!-- Diisi lewat JS -->
                  </tbody>
                </table>
              </div>
            </div>

            {{-- <div class="d-flex justify-content-end mt-3">
              <button type="button" class="btn btn-danger btn-lg"
                style="height: 30px; padding: 4px 12px; border-radius: 20px;
                font-size: 0.75rem; font-weight: 600; text-transform: uppercase;"
                onclick="buttonAddListBatal()">Batal</button>
            </div> --}}
          </div>
        </div>

      </div>
    </div>
  </div>
{{-- End Modal List serah sample --}}

{{-- Start Modal List Sales --}}
  <div class="modal fade" id="modalAddListSales" role="dialog" aria-labelledby="labelSales" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="labelSales">Pilih Sales</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body" style="margin-top:-30px;">
          <div class="container-fluid px-3 mt-4">
            <div class="row">
              <div class="table-responsive">
                <table id="tabel_add_list_sales" class="table table-bordered table-striped">
                  <thead class="text-center bg-primary text-white">
                    <tr>
                      <th>NIK</th>
                      <th>Nama</th>
                    </tr>
                  </thead>
                  <tbody id="tabel_data_add_list_sales" class="text-left">
                    <!-- Diisi lewat JS -->
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
{{-- End Modal List Sales --}}

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

        <div class="modal-body" style="margin-top:-30px;">
          <div class="container-fluid mt-4">

            <div class="row">
              <div class="table-responsive">
              <table id="tabel_add_list_item" class="table table-bordered table-striped">
                <thead class="text-center bg-primary text-white">
                  <tr>
                    <th scope="col">Kode Barang</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col">Qty</th>
                  </tr>
                </thead>
                <tbody id="tabel_data_add_list_item" class="text-left">
                  <!-- Diisi lewat JS -->
                </tbody>
              </table>
            </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
              <button type="button" class="btn btn-danger btn-lg"
                style="height: 30px; padding: 4px 12px; border-radius: 20px;
                font-size: 0.75rem; font-weight: 600; text-transform: uppercase;
                transition: background-color 0.3s, box-shadow 0.3s;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
                onclick="closeListItemAdd()">Close</button>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>
<!-- End modal list item add-->

<!-- start modal detail -->
<div id="page3" class="container-fluid mainpage" style="display: none; margin-top:-80px;">
        <div class="row">
          <div class="col-6 text-left">
            <h2>Detail Retur Penyerahan Konsinyasi</h2>
          </div>
          <div class="col-6 text-right">
            <button type="button" class="btn btn-danger btn-lg" style="
            height: 30px; 
            margin-top: 20px; 
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
        <div class="modal-body">
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
        </div>

        <!-- Tengah -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Sales</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input id="input_detail_sales_nama" type="text" class="form-control text-center" placeholder="Sales" disabled>
                        <input id="input_detail_sales" type="hidden">
                    </div>
                </div>
            </div>
        </div>

        <!-- Kanan -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Tanggal</label>
                <div class="col-sm-8">
                    <input type="date" class="form-control text-center" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}"disabled>
                </div>
            </div>
          </div>
        </div>
        </div>
        <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_detail_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_detail" class="table table-bordered table-striped"  >
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th colspan="3">Deskripsi Barang</th>
                  <th colspan="2">Satuan</th>
                </tr>
                <tr>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Gudang</th>
                  <th scope="col">Sat</th>
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
</div>
</div>
<!-- End modal detail-->


@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>
{{-- Modal pemilih Barang (#formAddListItem) disamakan persis dengan modal pemilih menu
     Kas, pola yang sama persis dengan koreksistock.blade.php/perintahopname.blade.php. --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  jQuery('#modalAddListSerahSample, #modalAddListSales, #formAddListItem').addClass('picker-kas')
  jQuery('#modalAddListSerahSample .d-flex.justify-content-end .btn, #modalAddListSales .d-flex.justify-content-end .btn, #formAddListItem .d-flex.justify-content-end .btn').filter(function () {
    return jQuery.trim(jQuery(this).text()) === 'Close' || jQuery.trim(jQuery(this).text()) === 'Batal'
  }).addClass('picker-kas-batal')
})
</script>
<script type="text/javascript">
let dataAddListItem = []
let dataRefresh = []

let dataTableAdd = []
let dataTableEdit = []

let dataEditListItem = []
let excludeSerahSamples = [];

let tempAdd = {} /// kalau di so tempAddAdd
let tempEdit = {} //// kalau di so tempAddEdit
let tempIndexEdit = 0
let tempEditAdd = {}
let tempEditEdit = {}
let tipeform = ''
let tipeformitem = ''

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

/* ============ Header tabel interaktif (window.ReportTable) ============
 * Port 1:1 dari pola psCart/psAktifkanTabel milik gudangpenyerahansample.blade.php.
 * RPS Belum Otorisasi + RPS Sudah Otorisasi digabung jadi satu tabel (#tabel), dengan
 * kolom Status dan filter Status di toolbar -- sama seperti merge Belum/Sudah
 * Otorisasi di gudangpermintaansample.blade.php/gudangpenyerahansample.blade.php.
 * Hanya ada satu dataset di sini (tidak ada tab antrian terpisah), jadi hanya urut 1. */
let rkCart = { 1 : [] }
const RK_HREF = 'gudangreturpenyerahankonsinyasi'
const RK_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date', 3 : 'bool' }
const RK_TIPE_KODE = { varchar : 0, float : 1, date : 2, bool : 3 }

function rkPickCI (row, key) {
  if (!row) { return undefined; }
  if (row[key] !== undefined) { return row[key]; }
  let lower = key.toLowerCase();
  for (let k in row) {
    if (k.toLowerCase() === lower) { return row[k]; }
  }
  return undefined;
}

function rkDefaultCart () {
  return [
    ['NoBukti',      'No. Bukti', 1, 'varchar', 0, 0],
    ['Tanggal',      'Tanggal',   1, 'date',    0, 0],
    ['IDUser',       'User',      1, 'varchar', 0, 0],
    ['IsOtorisasi1', 'Status',    1, 'bool',    0, 0],
    ['OtoUser1',     'User Oto',  1, 'varchar', 0, 0],
    ['TglOto1',      'Tgl Oto',   1, 'date',    0, 0],
  ]
}

function rkBuatCart (headers, values, isnumerics, isshowns, desimals) {
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
      RK_TIPE_NAMA[tipe] || 'varchar',
      0,
      isNaN(des) ? 0 : des,
    ])
  });
  return cart
}

function rkAktifkanTabel () {
  window.g_modeReport = 1
  window.gcart_header = rkCart[1]
}

function rkOnChangeAktif () {
  reinitTabel();
}

window.g_href = RK_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function (href, mode) {
  let cart = rkCart[1] || []

  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[1])
    value.push(c[0])
    isnumber.push(RK_TIPE_KODE[c[3]] ?? 0)
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
      href     : RK_HREF,
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
      href   : RK_HREF,
      urut   : 1,
      reset  : reset ? 1 : 0
    },
    success : function (res) {
      if (!reset && res && res.headertableheader && res.headertableheader.length) {
        rkCart[1] = rkBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal || [])
      } else {
        rkCart[1] = rkDefaultCart()
        window.gcart_header = rkCart[1]
        window.doSimpanHeader(RK_HREF, 1)
      }
      window.gcart_header = rkCart[1]
    },
    error : function (err) {
      console.log(err)
      alertify.warning(reset ? 'Gagal mengembalikan kolom ke tampilan default' : 'Gagal memuat pengaturan kolom')
      rkCart[1] = rkDefaultCart()
      window.gcart_header = rkCart[1]
    }
  })
}

let rkRtSudahInit = false
function rkInitReportTableSekali () {
  if (rkRtSudahInit || typeof ReportTable === 'undefined') { return }
  rkRtSudahInit = true

  ReportTable.init({ table : '#tabel', bar : '#rtBarTabel', onChange : rkOnChangeAktif })

  let rkGuardUlangKlik = false;
  let thead = document.querySelector('#tabel thead')
  if (thead) {
    thead.addEventListener('click', function (e) {
      if (rkGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }
      e.stopPropagation()
      e.preventDefault()
      rkGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles: false, cancelable: true, view: window })
      Object.defineProperty(ulang, 'target', { value: interaktif, configurable: true })
      thead.dispatchEvent(ulang)
      rkGuardUlangKlik = false
    }, true)
  }
}

function tulisTheadHeaderRK (tableSel, cols) {
  let thead = document.querySelector(tableSel + ' thead')
  if (!thead || !window.ReportTable) { return; }
  let headRowHtml = ReportTable.headHtml(cols)
    .replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>');
  thead.setAttribute('style', 'white-space:nowrap;');
  thead.innerHTML = headRowHtml;
}

function rkValueCell (row, col) {
  let raw = rkPickCI(row, col[0]);
  let type = col[3];

  {{-- Kolom Status (IsOtorisasi1) dirender sebagai badge hijau (Sudah Otorisasi) atau
       amber (Belum Otorisasi), bukan ikon check/x bool biasa. --}}
  if (col[0] === 'IsOtorisasi1') {
    return Number(raw) === 1
      ? '<td class="text-center"><span class="status-badge status-badge-sudah">Sudah Otorisasi</span></td>'
      : '<td class="text-center"><span class="status-badge status-badge-belum">Belum Otorisasi</span></td>';
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

{{-- RPS Belum + Sudah Otorisasi digabung -- tombol aksi dicabang sesuai status, sama
     seperti psTabel2ActionsCell di gudangpenyerahansample.blade.php. --}}
function rkTabelActionsCell (row) {
  let nobukti = rkPickCI(row, 'NoBukti');
  let sudah = Number(rkPickCI(row, 'IsOtorisasi1')) === 1;
  let html = '<td class="text-center" style="white-space:nowrap;"><div class="action-buttons-wrap">';
  html += '<button class="btn btn-warning btn-sm" type="button" title="Details" onclick="buttonDetail(\'' + nobukti + '\')"><i class="bi bi-info"></i></button>';
  if (sudah) {
    html += '<button class="btn btn-danger btn-sm" type="button" title="Batal Otorisasi" onclick="buttonBatalOtorisasi(\'' + nobukti + '\')"><i class="bi bi-key"></i></button>';
    html += '<button class="btn btn-primary btn-sm" type="button" title="Print" onclick="submitPrint(\'' + nobukti + '\')"><i class="bi bi-printer"></i></button>';
  } else {
    html += '<button class="btn btn-success btn-sm" type="button" title="Edit" onclick="buttonEdit(\'' + nobukti + '\', 0)"><i class="bi bi-pen"></i></button>';
    html += '<button class="btn btn-info btn-sm" type="button" title="Otorisasi" onclick="buttonOtorisasi(\'' + nobukti + '\', 0)"><i class="bi bi-key"></i></button>';
  }
  html += '</div></td>';
  return html;
}

function rkFilterRows (rows) {
  let status = $('#rkStatus').val()
  return (rows || []).filter(function (row) {
    if (status !== '' && String(Number(rkPickCI(row, 'IsOtorisasi1'))) !== status) { return false }
    return true
  })
}

function rkFilterChanged () {
  reinitTabel()
}

function renderTabelRows (rows) {
  rkAktifkanTabel();
  let cols = (rkCart[1].length ? rkCart[1] : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  rkFilterRows(rows).forEach(function (row) {
    html += '<tr>' + rkTabelActionsCell(row);
    cols.forEach(function (col) { html += rkValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel_data').innerHTML = html;
  tulisTheadHeaderRK('#tabel', cols);
}

let lastTabelRows = []
let rkPanjangHalaman = 10

function rkIkatSearch () {
  let input = document.getElementById('rkSearch1')
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  let timer = null
  input.addEventListener('input', function () {
    let nilai = input.value
    if (timer) { clearTimeout(timer) }
    timer = setTimeout(function () {
      if ($.fn.DataTable.isDataTable('#tabel')) {
        $('#tabel').DataTable().search(nilai).draw()
      }
    }, 400)
  })
}

function rkIkatPanjangHalaman () {
  let sel = document.getElementById('rkLen1')
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(rkPanjangHalaman)

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    rkPanjangHalaman = (n === -1 || n > 0) ? n : 10
    if ($.fn.DataTable.isDataTable('#tabel')) {
      $('#tabel').DataTable().page.len(rkPanjangHalaman).draw()
    }
  })
}

const RK_DOM_STRING = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"

function reinitTabel () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().destroy(); }
    renderTabelRows(lastTabelRows);
    $('#tabel').DataTable({ dom: RK_DOM_STRING, lengthChange: false, pageLength: rkPanjangHalaman, paging: true, ordering: false });
    rkIkatSearch();
    rkIkatPanjangHalaman();
  } catch (e) {
    console.error('reinitTabel failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

$(document).ready(function(){
  rkAktifkanTabel();
  window.doSetHeader(1, false);
  lastTabelRows = [].concat(@json($listData), @json($listData2));
  reinitTabel();

  rkInitReportTableSekali();

  {{-- Box "Search:" bawaan DataTables di modal Barang All dibuat ulang setiap
       pickerKasInit() memanggil .destroy(), jadi diikat via delegated event supaya
       tetap jalan walau elemennya diganti. keyup dipakai (bukan keypress) -- input
       bawaan DataTables type="search" tidak selalu memicu event keypress saat Enter
       ditekan di semua browser. --}}
  $(document).on('keyup', '#tabel_add_list_item_filter input', searchBarangAll);
});

function loadAll () {
  const _token = document.getElementById("_token").value;

  $.ajax({
    url: "{!! url('returpenyerahankonsinyasiloadall') !!}",
    type: "GET",
    data: { _token },
    success: function (res) {
      lastTabelRows = [].concat(res.outstanding, res.outstanding2);
      reinitTabel();
    },

    error: function (xhr, status, error) {
      alertify.error("Gagal load data: " + error);
      console.error('LoadAll error:', error);
    }
  });
}

function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: "{!! url('returpenyerahankonsinyasidetailCetak') !!}",
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
              </div>


              <div style="width: 38%">
                <div style="display: flex; width: 100%">
                  <h2 class="m-0 pb-2">SURAT PENYERAHAN BARANG</h2>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Nomor</div>
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
                    <td class="text-center" style="width: 30%">MERK</td>
                    <td class="text-center" style="width: 30%">KODE BARANG</td>
                    <td class="text-center" style="width: 20%">SATUAN</td>
                    <td class="text-center" style="width: 20%">QUANTITY</td>
                    <td class="text-center" style="width: 20%">GUDANG</td>
                  </tr>
                </thead> `;

    let z = 0
    let tempPrintStr = ``
    // buat hitung grandtotal
    let grandTotal = 0;
    arrayDataPrint.forEach(group => {
      group.forEach(item => {
        if (item.QNTSAT) {
          grandTotal += parseFloat(item.QNTSAT) || 0;
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
               style="width: 30%;">${itemSub.NAMAMERK}</td>
         <td class="text-align: left"
               style="width: 30%;">${itemSub.KODEBRG}</td>
         <td class="text-align: text-right"
               style="width: 20%;  "> ${itemSub.SATuan}</td>
         <td class="text-align: text-right"
               style="width: 20%;  "> ${itemSub.QNTSAT ? parseFloat(itemSub.QNTSAT).toFixed(2) : ''}</td>
         <td class="text-align: text-right"
               style="width: 20%;  "> ${itemSub.namaGDG}</td>
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
    url: "{!! url('returpenyerahankonsinyasiupdateotorisasi') !!}",
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
        url: "{!! url('returpenyerahankonsinyasiupdatebatalotorisasi') !!}",
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

function buttonAddListSerahSample () {
  console.log('buttonAddListSerahSample');

  const sales = $('#input_sales').val(); 
  const nobukti = $('#input_add_add_serahsample').val().trim();
  const urut = $('#input_urut_ref').val().trim();

  if (!sales) {
    alertify.warning("Silakan pilih Sales terlebih dahulu");
    return;
  }

  // Kosongkan field barang
  $('#input_add_add_kodebarang').val('');
  $('#input_add_add_keterangannama').val('');
  $('#input_add_add_satuan').html(''); 
  $('#input_urut_ref').val(''); 
  $('#tabel_data_add_list_serahsample').html('');

  // exclude
  let excludeToSend = excludeSerahSamples.filter(e => !(e.nobukti === nobukti && e.urut === urut));

  $.ajax({
    url: "{{ url('returpenyerahankonsinyasinokonsinyasi') }}",
    type: "get",
    data: {
      sales: sales,
      nobukti: nobukti,
      exclude: JSON.stringify(excludeToSend) 
    },
    success: function(res) {
      console.log('Data Sample:', res);

      let rowTable = "";
      if (res && res.length > 0) {
        res.forEach((item) => {
          rowTable += `
            <tr class="pick-row" onclick="buttonPickSerahSample('${item.NOBUKTI}', '${item.Urut}', '${item.Kodebrg}', '${item.GdgAsal}', '${item.NamaGgdAsal ?? ''}')">
              <td>${item.NOBUKTI}</td>
              <td>${item.TANGGAL ? item.TANGGAL.substring(0, 10) : '-'}</td>
              <td>${item.KODECUSTSUPP ?? ''}</td>
              <td>${item.NAMACUSTSUPP ?? ''}</td>
              <td>${item.Kodebrg ?? ''}</td>
              <td>${item.NamaBrg}</td>
              <td>${item.QntSisa}</td>
              <td>${item.GdgAsal}</td>
            </tr>`;
        });
      }

      $('#tabel_data_add_list_serahsample').html(rowTable);

      pickerKasInit('tabel_add_list_serahsample');

      $('#modalAddListSerahSample').modal('show');
    },
    error: function(err) {
      console.error("AJAX Error:", err);
      alertify.warning('Terjadi kesalahan saat mengambil data.');
    }
  });
}

function buttonPickSerahSample (nobukti, urut, kodebrg, gdgasal, namagudang) {
  console.log('Data dipilih:', nobukti, urut, kodebrg, gdgasal, namagudang);

  // Isi data header
  $('#input_add_add_serahsample').val(nobukti);
  $('#input_urut_ref').val(urut);
  $('#input_add_add_gudang').val(gdgasal);
  $('#input_add_add_namagudang').val(namagudang);
  $('#input_add_add_qnt').val('0.00');
  $('#input_add_add_kodebarang').val('');
  $('#input_add_add_keterangannama').val('');
  $('#input_add_add_satuan').html('');

  $('#modalAddListSerahSample').modal('hide');

  loadBarangSerahSample(nobukti, urut, kodebrg);
}

function loadBarangSerahSample (nobukti, urut, kodebrg) {
  console.log("Ambil barang untuk:", nobukti, urut, kodebrg);

  $.ajax({
    url: "{{ url('returpenyerahankonsinyasilistbarang') }}",
    type: "get",
    data: { nobukti: nobukti, urut: urut, kodebrg: kodebrg },
    success: function(res) {
      console.log("Data Barang:", res);

      dataAddListItem = res;

      if (res && res.length > 0) {
        const item = res[0]; 

        $("#input_add_add_kodebarang").val(item.Kodebrg);
        $("#input_add_add_keterangannama").val(item.NamaBrg);

        // Satuan
        let satuanOptions = "";
        if (item.SAT1) satuanOptions += `<option value="${item.SAT1}">${item.SAT1}</option>`;
        if (item.SAT2) satuanOptions += `<option value="${item.SAT2}">${item.SAT2}</option>`;
        if (item.SAT3) satuanOptions += `<option value="${item.SAT3}">${item.SAT3}</option>`;
        $("#input_add_add_satuan").html(satuanOptions);

        // isi QNT otomatis dengan sisa
        const qntSisa = item.QntSisa ?? item.Qnt ?? 0;
        $("#input_add_add_qnt").val(parseFloat(qntSisa).toFixed(2))
          .attr("max", qntSisa)
          .attr("min", 0);

        setTimeout(() => {
          const qntInput = document.getElementById("input_add_add_qnt");
          qntInput.focus();
          qntInput.select();
        }, 300);
      } else {
        alertify.warning("Barang tidak ditemukan untuk No Serah Sample ini");
      }
    },
    error: function(err) {
      console.error("AJAX Error:", err);
      alertify.warning("Terjadi kesalahan saat mengambil data barang.");
    }
  });
}

function buttonAddListSales () {
  console.log('buttonAddListSales');

  $.ajax({
    url: "{{ url('returpenyerahankonsinyasilistsales') }}",
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

      document.getElementById("tabel_data_add_list_sales").innerHTML = rowTable;
      pickerKasInit('tabel_add_list_sales', { order: [[1, 'asc']] });

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

  // Reset 
  $('#input_add_add_serahsample').val('');   
  $('#input_add_add_kodebarang').val('');     
  $('#input_add_add_keterangannama').val('');
  $('#input_add_add_satuan').html('');
  $('#input_urut_ref').val('');
  $('#input_add_add_gudang').val('');
  $('#input_add_add_namagudang').val('');
  $('#input_add_add_qnt').val('0.00');

  $('#tabel_data_add_list_serahsample').html('');
  if ($.fn.DataTable.isDataTable('#tabel_add_list_serahsample')) {
    $('#tabel_add_list_serahsample').DataTable().clear().destroy();
  }

  console.log("Sales dipilih/ganti, semua data serah sample & barang direset.");
}


function buttonAddListBatal() {
  $('#modalAddListSerahSample').modal('hide');
  $('#modalAddListSales').modal('hide');
}

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
  let sales = $("#input_sales").val();
  let namasls = $("#input_sales_nama").val();
  let kodebarang = $("#input_add_add_kodebarang").val();
  let keterangannama = $("#input_add_add_keterangannama").val();
  let satuan = $("#input_add_add_satuan").val();
  let qnt = parseFloat($("#input_add_add_qnt").val()) || 0;
  let noserahsample = $("#input_add_add_serahsample").val();
  let urutserahsample = $("#input_urut_ref").val();
  let gudang = $("#input_add_add_gudang").val();
  let namagudang = $("#input_add_add_namagudang").val();
  let keterangan = $("#input_keterangan").val() || '';

  if (!kodebarang || !satuan || qnt <= 0) {
    alertify.warning("Lengkapi semua data wajib");
    return;
  }

  let barang = tempEdit;
  console.log("tempEdit:", tempEdit);

  // ================= QNT SISA ===================
  let qntSisaStored = parseFloat(barang.QntSisa ?? 0) || 0;
  let qntLama = parseFloat(barang.QNT ?? barang.Qnt ?? 0) || 0;

  // Total QNT max 
  let qntMax = qntLama + qntSisaStored;

  console.log(`Mode: ${tipeform} | Qty Lama: ${qntLama} | Qty Sisa: ${qntSisaStored} | Qty Maks: ${qntMax}`);

  if (qnt > qntMax) {
    alertify.warning(`Qty tidak boleh lebih besar dari Qty maksimal (${qntMax})`);
    return;
  }

  if (qnt <= 0) {
    alertify.warning("Qty tidak boleh nol atau negatif");
    return;
  }
  // ===============================================

  let isi = 0;
  let nosat = parseInt(satuan);
  let qnt1 = 0;
  let sat_1 = '';
  let sat_2 = '';

  barang.ISI1 = parseFloat(barang.ISI1) || 1;
  barang.ISI2 = parseFloat(barang.ISI2) || 1;
  barang.ISI3 = parseFloat(barang.ISI3) || 1;

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

  sat_1 = barang.SAT1 ?? satuan ?? 'PCS';
  sat_2 = barang.SAT2 ?? satuan ?? 'PCS';

  keterangannama = keterangannama.replace(/["']/g, '');
  keterangan = keterangan.replace(/["']/g, '');

  let nourut = parseInt(barang.NOURUT);
  let urut = parseInt(barang.URUT);

  console.log("URUT yg dikirim:", urut);
  console.log("QNT yg dikirim:", qnt);
  console.log("SAT1 dikirim:", sat_1);
  console.log("SAT2 dikirim:", sat_2);
  console.log("NOSERAH:", noserahsample);
  console.log("URUTSERAH:", urutserahsample);
  console.log("KODEGUDANG:", gudang);
  console.log("NAMAGUDANG:", namagudang);

  $.ajax({
    url: "{!! url('returpenyerahankonsinyasispadd') !!}",
    type: "POST",
    async: false,
    data: {
      _token,
      choice,
      nobukti,
      nourut,
      tanggal,
      urut,
      kodebarang,
      gdgasal: gudang,
      namagdgasal: namagudang,
      gdgtujuan: '',
      sat_1,
      sat_2,
      qnt,
      qnt2: qnt,
      nosat,
      isi,
      kodesls: sales,
      namasls: namasls,
      pbonus: 0,
      maxol: 0,
      pkonsi: 0,
      noserahsample,
      urutserahsample,
      jmlrecord
    },
    success: function (res) {
      console.log('respoedit', res);
      loadAll();
      $('.showhide').hide();
      refreshDataTableAdd(nobukti);
      alertify.success('Berhasil edit item');
    },
    error: function (err) {
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
  // document.getElementById("buttonAddListKodeBarang").disabled = true;
  document.getElementById("buttonAddListSerahSample").disabled = true;

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
  document.getElementById("input_add_add_serahsample").value = tempEdit.Noserahsample;
  document.getElementById("input_urut_ref").value = tempEdit.UrutSerahSample;
  document.getElementById("input_add_add_gudang").value = tempEdit.gdgAsal;
  document.getElementById("input_add_add_namagudang").value = tempEdit.NamaGgdAsal;

  // Tampilkan mode edit
  $('#h4AddAddItem').hide();
  $('#h4AddEditItem').show();
  $('#submitAddAdd').hide();
  $('#submitAddEdit').show();
  $('#addAddItem').show();

  document.getElementById("input_add_add_kodebarang").scrollIntoView();
}

function buttonEdit (nobukti, isOtorisasi1) {
  tipeform = 'edit';

  let akses = $("#akses_iskoreksi").val();
  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  $.ajax({
    url: "{!! url('returpenyerahankonsinyasispdetail') !!}",
    type: "get",
    async: false,
    data: { nobukti },
    success: function (res) {
      if (!res || !res.length) {
        alertify.error("Data tidak ditemukan");
        return;
      }

      const data = res[0];

      if (isOtorisasi1 == 1 || data.IsOtorisasi1 == 1) {
        alertify.warning("Data sudah Di Otorisasi");
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

      // Isi form header
      $('#input_add_tanggal').val(dateFormatted);
      $('#input_add_nobukti').val(data.NOBUKTI);
      $('#input_add_nourut').val(data.Nourut);
      $('#input_sales_nama').val(data.NAMASLS);
      $('#input_sales').val(data.KODESLS);

      // Isi tabel item
      let rowTable = "";
      dataTableAdd.forEach((item, i) => {
        rowTable += `
          <tr>
            <td>${item.KODEBRG}</td>
            <td>${item.NamaBrg}</td>
            <td>${item.gdgAsal}</td>
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

  const data = dataTableAdd[index];

  alertify.confirm('Hapus Item', `Apakah yakin ingin menghapus item ${data.KODEBRG}?`, function () {
    const _token = $("#_token").val();
    const choice = "D";
    const nobukti = $("#input_add_nobukti").val();
    const tanggal = $("#input_add_tanggal").val();
    const nourut = $("#input_add_nourut").val();
    const kodebarang = data.KODEBRG;
    const urut = data.URUT;
    const qnt = parseFloat(data.QNT);
    const qnt2 = parseFloat(data.QNT2 ?? data.QNT);
    const nosat = parseInt(data.NoSat);
    const isi = parseFloat(data.ISI);
    const sat_1 = data.SAT_1 ?? data.satuan;
    const sat_2 = data.SAT_2 ?? data.satuan;
    const note = data.NamaBrg;
    const gdgasal = data.gdgAsal ?? '';
    const gdgtujuan = data.gdgTujuan ?? '';
    const kodesls = $("#input_sales").val();
    const maxol = parseInt($("#input_maxol").val() || 0);
    const pbonus = parseInt(data.pbonus ?? 0);
    const noserahsample = data.NoserahSample ?? '';
    const urutserahsample = parseInt(data.UrutSerahSample ?? 0);
    const pkonsi = parseInt($("#input_pkonsi").val() || 0);
    const jmlrecord = 0;
    const kodecustsupp = data.KODECUSTSUPP ?? '';

    $.ajax({
      url: "{!! url('returpenyerahankonsinyasispdelete') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        choice,
        nobukti,
        nourut,
        tanggal,
        note,
        urut,
        kodebarang,
        gdgasal,
        gdgtujuan,
        sat_1,
        sat_2,
        qnt,
        qnt2,
        nosat,
        isi,
        kodecustsupp, 
        kodesls,
        pbonus,
        maxol,
        noserahsample,
        urutserahsample,
        pkonsi,
        jmlrecord
      },
      success: function (res) {
        alertify.success("Item sudah dihapus");

        refreshDataTableAdd(nobukti);

        if (!dataTableAdd || dataTableAdd.length === 0) {
          $("#btn_customer").prop("disabled", false);
          $("#btn_gudang").prop("disabled", false);
          tipeform = 'new';
        }

        loadAll();
      },
      error: function (err) {
        console.log(err);
        alertify.error("Gagal menghapus item");
      }
    });
  }, function () {
    console.log('Batal Menghapus data');
  });
}

function buttonDetail (nobukti) {
  $.ajax({
    url: "{!! url('returpenyerahankonsinyasispdetail') !!}",
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
            <td>${item.gdgAsal}</td>
            <td class="text-right">${parseFloat(item.QNT).toLocaleString()}</td>
            <td>${item.SAT_1}</td>
        </tr>`
      });

      let date = new Date(res[0].TANGGAL);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
      $('#input_detail_tanggal').val(date1)

      document.getElementById("tabel_data_detail").innerHTML  = rowTable
      document.getElementById("input_detail_nobukti").value  = res[0].NOBUKTI
      document.getElementById("input_detail_sales_nama").value  = res[0].NAMASLS


  }})
  $("#page3").show();
  $("#page1").hide();
}


function setNewNoBukti () {
  $.ajax({
    url: "{!! url('returpenyerahankonsinyasispnobukti') !!}",
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
    url: "{!! url('returpenyerahankonsinyasispnobukti') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    }})
    dataTableAdd = []
    cleanFormAdd()

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
}

function buttonAddListKodeBarang () {
  console.log("buttonAddListKodeBarang");

  const serahsample = $("#input_add_add_serahsample").val().trim();

  if (!serahsample) {
    alertify.warning("Silakan pilih Serah Sample terlebih dahulu.");
    return;
  }

  // Kosongkan body tabel
  $("#tabel_data_add_list_item").html("");

  if ($.fn.DataTable.isDataTable("#tabel_add_list_item")) {
    $("#tabel_add_list_item").DataTable().clear().destroy();
  }

  $.ajax({
    url: "{{ url('returpenyerahankonsinyasilistbarang') }}",
    type: "get",
    data: {
      nobukti: serahsample,
      search: "" 
    },
    success: function (res) {
      console.log("Data Barang:", res);

      let rowTable = "";

      if (res && res.length > 0) {
        res.forEach((item, i) => {
          rowTable += `
            <tr class="pick-row" onclick="buttonPickBarang(${i})">
              <td>${item.Kodebrg}</td>
              <td>${item.NamaBrg}</td>
              <td>${item.QntSisa ?? ""}</td>
            </tr>`;
        });
      }

      $("#tabel_data_add_list_item").html(rowTable);

      pickerKasInit('tabel_add_list_item', { order: [[1, 'asc']] });

      dataAddListItem = res;

      $("#formAddListItem").modal("show");
      $('#formAddListItem').on('shown.bs.modal', function () {
        $('#tabel_add_list_item_filter input').trigger('focus');
      });
    },
    error: function (err) {
      console.error("AJAX Error:", err);
      alertify.warning("Terjadi kesalahan saat mengambil data.");
    }
  });
}

function buttonPickBarang (index) {
  if (!dataAddListItem[index]) {
    alertify.warning("Data barang tidak valid");
    return;
  }

  const item = dataAddListItem[index];
  console.log("Barang dipilih:", item);

  $("#input_add_add_kodebarang").val(item.Kodebrg);
  $("#input_add_add_keterangannama").val(item.NamaBrg);

  // Reset Serah Sample & Gudang
  $("#input_add_add_serahsample").val(item.NOBUKTI);
  $("#input_urut_ref").val(item.Urut);
  $("#input_add_add_gudang").val(item.GdgAsal);
  $("#input_add_add_namagudang").val(item.NamaGdgAsal ?? "");

  // Satuan
  let satuanOptions = "";
  if (item.SAT1) satuanOptions += `<option value="${item.SAT1}">${item.SAT1}</option>`;
  if (item.SAT2) satuanOptions += `<option value="${item.SAT2}">${item.SAT2}</option>`;
  if (item.SAT3) satuanOptions += `<option value="${item.SAT3}">${item.SAT3}</option>`;
  $("#input_add_add_satuan").html(satuanOptions);

  // isi QNT otomatis dengan sisa
  const qntSisa = item.QntSisa ?? item.Qnt ?? 0;
  $("#input_add_add_qnt")
    .val(parseFloat(qntSisa).toFixed(2)) 
    .attr("max", qntSisa)   
    .attr("min", 0);     

  $("#formAddListItem").modal("hide");

  setTimeout(() => {
    const qntInput = document.getElementById("input_add_add_qnt");
    qntInput.focus();
    qntInput.select();
  }, 300);
}


{{-- Kotak "Search:" bawaan DataTables (hasil pickerKasInit) dipakai langsung sebagai
     pemicu pencarian server-side -- keyup diikat ke #tabel_add_list_item_filter input
     di $(document).ready(), sama seperti searchBarangAll di koreksistock.blade.php. --}}
function searchBarangAll (e) {
  if (e.which == 13) {
    console.log("Enter ditekan");

    const search = e.target.value;
    const serahsample = $("#input_add_add_serahsample").val().trim();

    if (!serahsample) {
      alertify.warning("Silakan pilih Serah Sample terlebih dahulu.");
      return;
    }

    $("#tabel_data_add_list_item").html(`
      <tr><td class="text-center" colspan="5">Loading data...</td></tr>`);

    $.ajax({
      url: "{{ url('returpenyerahankonsinyasilistbarang') }}",
      type: "get",
      data: {
        nobukti: serahsample,
        search: search
      },
      success: function (res) {
        console.log("Hasil search barang:", res);

        let rowTable = "";

        if (res && res.length > 0) {
          res.forEach((item, i) => {
            rowTable += `
              <tr class="pick-row" onclick="buttonPickBarang(${i})">
                <td>${item.Kodebrg}</td>
                <td>${item.NamaBrg}</td>
                <td>${item.QntSisa ?? ""}</td>
              </tr>`;
          });
        }

        $("#tabel_data_add_list_item").html(rowTable);

        pickerKasInit('tabel_add_list_item', { order: [[1, 'asc']] });

        dataAddListItem = res;
      },
      error: function (err) {
        console.error("AJAX Error:", err);
        alertify.warning("Terjadi kesalahan saat mengambil data.");
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

  // Ambil item yang dipilih dari dataAddListItem
  let item = dataAddListItem[i];
  
  // Masukkan data ke dalam form
  $('#input_add_add_kodebarang').val(item.Kodebrg);
  $('#input_add_add_keterangannama').val(item.NamaBrg);

  // Menampilkan satuan 
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
  let namasls = $("#input_sales_nama").val();
  let gudang = $("#input_add_add_gudang").val();
  let namagudang = $("#input_add_add_namagudang").val();
  let tanggal = $("#input_add_tanggal").val();
  let kodebarang = $("#input_add_add_kodebarang").val();
  let keterangannama = $("#input_add_add_keterangannama").val();
  let satuan = $("#input_add_add_satuan").val();
  let qnt = parseFloat($("#input_add_add_qnt").val()) || 0;
  let noserahsample = $("#input_add_add_serahsample").val();
  let urutserahsample = $("#input_urut_ref").val();
  let keterangan = "";

  if (!sales || !kodebarang || !satuan || qnt <= 0 || !noserahsample) {
    alertify.warning("Lengkapi semua data wajib");
    return;
  }

  let barang = dataAddListItem.find(item => item.Kodebrg === kodebarang);
  if (!barang) {
    alertify.warning("Barang tidak ditemukan di daftar");
    return;
  }

  let qntSisa = barang.QntSisa ?? barang.Qnt ?? 0;
  if (qnt > qntSisa) {
    alertify.warning("Qty tidak boleh lebih besar dari Qty sisa (" + qntSisa + ")");
    return;
  }
  if (qnt <= 0) {
    alertify.warning("Qty tidak boleh nol atau negatif");
    return;
  }

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
    url: "{!! url('returpenyerahankonsinyasispadd') !!}",
    type: "POST",
    async: false,
    data: {
      _token,
      choice,
      nobukti,
      nourut,
      tanggal,
      urut: 0,
      kodebarang,
      gdgasal: gudang,
      namagdgasal: namagudang,
      gdgtujuan: '',
      sat_1,
      sat_2,
      qnt,
      qnt2,
      nosat,
      isi,
      kodesls: sales,
      namasls: namasls,
      pbonus: 0,
      maxol: 0,
      pkonsi: 0,
      noserahsample,
      urutserahsample,
      jmlrecord
    },
    success: function (res) {
      console.log('respoadd', res);
      if (res == 1) {
        tipeform = 'edit';
        loadAll();
        cleanFormAddAdd();
        refreshDataTableAdd(nobukti);
        alertify.success('Berhasil menambah item');
        document.getElementById("input_add_tanggal").disabled = true;
        document.getElementById("btn_sales").disabled = true;

        const nobuktiSerah = $('#input_add_add_serahsample').val();
        const urutSerah = $('#input_urut_ref').val();

        if (nobuktiSerah && urutSerah) {
          excludeSerahSamples.push({ nobukti: nobuktiSerah, urut: urutSerah });
          console.log("Exclude updated:", excludeSerahSamples);

          const addedQnt = qnt;
          for (let i = 0; i < dataAddListItem.length; i++) {
            const it = dataAddListItem[i];
            if (
              String(it.NOBUKTI) === String(nobuktiSerah) &&
              String(it.Urut) === String(urutSerah) &&
              String(it.Kodebrg) === String(kodebarang)
            ) {
              const oldSisa = parseFloat(it.QntSisa ?? it.Qnt ?? 0) || 0;
              let newSisa = oldSisa - addedQnt;
              if (newSisa < 0) newSisa = 0;

              it.QntSisa = newSisa;
              it.Qnt = newSisa;

              // Update tampilan tabel modal kalau masih terbuka
              $("#tabel_data_add_list_item tr").each(function () {
                const kodeCell = $(this).find("td").eq(1).text().trim();
                if (kodeCell === String(kodebarang)) {
                  $(this).find("td").eq(3).text(newSisa.toFixed(2));
                }
              });
              break;
            }
          }
        }

      } else if (res == 2) {
        setNewNoBukti();
        alertify.warning('Nobukti telah direfresh silahkan submit ulang');
      }
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
  // document.getElementById("buttonAddListKodeBarang").disabled = false;
  document.getElementById("buttonAddListSerahSample").disabled = false;
  document.getElementById("input_add_add_satuan").disabled = false;
  document.getElementById("input_add_add_kodebarang").value = ""
  document.getElementById("input_add_add_serahsample").value = ""
  document.getElementById("input_add_add_gudang").value = ""
  document.getElementById("input_add_add_namagudang").value = ""
  document.getElementById("input_urut_ref").value = ""
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
    
    tipeform = "new";
    return;
  }

  $.ajax({
    url: "{!! url('returpenyerahankonsinyasispdetail') !!}",
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
            <td>${item.gdgAsal}</td>
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
  document.getElementById("input_urut_ref").value = ''
  document.getElementById("input_add_add_gudang").value = ''
  document.getElementById("input_add_add_serahsample").value = ''
  document.getElementById("input_add_add_namagudang").value = ''
  document.getElementById("input_add_add_qnt").value = '0.00'
  document.getElementById("input_add_add_satuan").innerHTML = '<option value=0 selected>Pilih Satuan</option>'
}

function cleanFormAdd (){
  document.getElementById("input_add_tanggal").valueAsDate = new Date()
  document.getElementById("input_sales").value = ''
  document.getElementById("input_sales_nama").value = ''
}

function lockFormAdd (){
  document.getElementById("input_add_tanggal").disabled = true
  document.getElementById("btn_sales").disabled = true
}

function unlockFormAdd () {
  document.getElementById("input_add_tanggal").disabled = false
  document.getElementById("btn_sales").disabled = false
}
</script>


@endsection
