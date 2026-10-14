@extends('newmasterTest')
@section('buttons')

@section('page-title', 'Perintah Opname')
@section('title', 'SML - Perintah Opname')

@endsection

@section('css')


{{-- tampilan search bar 1 --}}
  <style>

  #tabel_add_list_customer_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;
  }

  #tabel_add_list_merk_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;
  }

  #tabel_add_list_merk_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }

  #tabel_add_list_modal_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;
  }

  #tabel_add_list_modal_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }



  #tabel_add_list_customer_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }

  #tabel_add_list_noinvoice_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;
  }
  #tabel_add_list_noinvoice_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }

  #tabel_add_list_barang_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;
  }
  #tabel_add_list_barang_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }

  #tabel_add_list_nobeli_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;
  }
  #tabel_add_list_nobeli_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }

  </style>
{{-- end tampilan search bar 1 --}}

<link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
<link rel="stylesheet" href="{!! URL::asset('css/picker-kas.css') !!}?v={{ @filemtime(base_path('public/css/picker-kas.css')) ?: '1' }}">

{{-- Blok CSS lokal halaman ini -- disalin verbatim dari @section('css') transferbarang.blade.php
     (custom-tabs/tab-card, #page1 .card override layout .card global, po-len-wrap/
     po-len-inp, tombol aksi bulat kecil pastel + action-buttons-wrap hover-reveal di
     kolom pertama, header/row tabel), hanya untuk #tabel/#tabel2 (halaman ini cuma 2
     tab, tidak ada #tabel3). Halaman ini memakai layout newmasterTest (bukan lagi
     gudang.newmaster) -- po-table-header.css di-link manual di atas dan report-table.js
     di-load di @section('js') karena newmasterTest tidak memuat keduanya secara
     otomatis seperti gudang.newmaster. --}}
<style>
.toolbar {
  display: flex;
  align-items: center;
  gap: 10px;
}

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

/* Layout newmasterTest punya rule .card global (align-items:center, text-align:center,
   cursor:pointer, dan :hover-nya translateY+box-shadow buat kartu menu dashboard) yang
   bikin tab-bar & kartu toolbar/tabel ikut rata tengah dan "terangkat" saat di-hover
   kalau tidak ditimpa -- port 1:1 dari #page1 .card milik transferbarang.blade.php/
   so.blade.php. */
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

.po-len-wrap {
  display: flex;
  align-items: center;
  gap: 8px;
  background: var(--rt-card);
  border: 1.5px solid var(--rt-border);
  border-radius: 8px;
  padding: 5px 12px;
}

.po-len-wrap label {
  margin: 0;
  font-size: 11.5px;
  font-weight: 700;
  color: var(--rt-ink-soft);
  text-transform: uppercase;
  letter-spacing: .05em;
  white-space: nowrap;
}

.po-len-inp {
  border: none;
  background: transparent;
  font-size: 13px;
  font-weight: 700;
  color: var(--rt-ink);
  outline: none;
  cursor: pointer;
  padding: 2px 20px 2px 0;
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  background-image: url("data:image/svg+xml;utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%231D2130' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right center;
}

#tabel tbody td:first-child:not(.dataTables_empty),
#tabel2 tbody td:first-child:not(.dataTables_empty) {
  text-align: center;
  vertical-align: middle;
  white-space: nowrap;
  font-size: 0;
}

#tabel td:first-child .btn,
#tabel2 td:first-child .btn,
#addTable td:last-child .btn {
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

#tabel td:first-child .btn:hover,
#tabel2 td:first-child .btn:hover,
#addTable td:last-child .btn:hover {
  filter: brightness(0.97);
  transform: translateY(-1px);
}

#tabel td:first-child .btn-success,
#tabel2 td:first-child .btn-success,
#addTable td:last-child .btn-success {
  color: #16a34a; border-color: #cdebd7; background: #e7f7ed;
}

#tabel td:first-child .btn-warning,
#tabel2 td:first-child .btn-warning,
#addTable td:last-child .btn-warning {
  color: #b45309; border-color: #fbe3bd; background: #fef3e0;
}

#tabel td:first-child .btn-primary,
#tabel2 td:first-child .btn-primary,
#addTable td:last-child .btn-primary {
  color: #2563eb; border-color: #cfdcff; background: #e8edff;
}

#tabel td:first-child .btn-danger,
#tabel2 td:first-child .btn-danger,
#addTable td:last-child .btn-danger {
  color: #dc2626; border-color: #f7cfcf; background: #fdeaea;
}

#tabel td:first-child .btn-info,
#tabel2 td:first-child .btn-info,
#addTable td:last-child .btn-info {
  color: #0891b2; border-color: #a5f3fc; background: #ecfeff;
}

{{-- Tabel statis (bukan #tabel/#tabel2) yang dulu pakai thead bg-primary text-white --
     diganti abu-abu + teks gelap, disamakan dengan thead th di atas, sesuai UI baru
     yang sama di semua halaman. --}}
#addTable thead th,
#detailTable thead th,
#tabel_add_list_gudang thead th,
#tabel_add_list_merk thead th,
#tabel_add_list_modal thead th,
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

#tabel tbody tr:nth-of-type(odd),
#tabel2 tbody tr:nth-of-type(odd) {
  background-color: #fbfbfc;
}

#tabel tbody tr:hover,
#tabel2 tbody tr:hover {
  background-color: #f5f3ff;
}

.data-table th,
.data-table td {
  white-space: nowrap;
}

#tabel_wrapper,
#tabel2_wrapper {
  position: relative;
}

#tabel_wrapper > .dataTables_processing,
#tabel2_wrapper > .dataTables_processing {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  width: auto;
  margin: 0;
  padding: 0;
  border: 0;
  background: rgba(255, 255, 255, .62);
  z-index: 40;
  animation: opMunculLoading .34s ease-out both;
}

@keyframes opMunculLoading {
  0%, 45% { opacity: 0; }
  100% { opacity: 1; }
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
</style>
@endsection


@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id="page1" class="container-fluid mainpage">

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
  {{-- Tab Belum/Sudah Otorisasi digabung jadi satu tabel, dengan filter Status +
       Periode di toolbar (client-side, lastTabelRows sudah berisi gabungan
       tempOutstanding+tempPenerimaan dari periode berjalan). --}}
  <div class="card">
    <div class="card-body" style="padding:0;">
      <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
        <div class="po-toolbar">
          <div class="po-filter-wrap">
            <label>Periode</label>
            <input type="date" onchange="opFilterChanged()" class="po-filter-inp" id="opTanggalAwal" value="{!! \Carbon\Carbon::createFromDate((int) $periode->tahun, (int) $periode->bulan, 1)->startOfMonth()->format('Y-m-d') !!}">
            <span class="po-filter-sep">s/d</span>
            <input type="date" onchange="opFilterChanged()" class="po-filter-inp" id="opTanggalAkhir" value="{!! \Carbon\Carbon::createFromDate((int) $periode->tahun, (int) $periode->bulan, 1)->endOfMonth()->format('Y-m-d') !!}">
          </div>
          <input type="search" id="opSearch1" class="po-search-inp" placeholder="Cari data">
          <div class="po-len-wrap">
            <label for="opLen1">Tampilkan</label>
            <select id="opLen1" class="po-len-inp">
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
              <option value="-1">Semua</option>
            </select>
          </div>
          <button class="po-btn-filter" type="button" onclick="$('#modalFilterOP').modal('show')">
            <i class="bi bi-funnel"></i> Filter
          </button>
          @if ((int) ($akses->ISTAMBAH ?? 0) === 1)
            <div class="po-toolbar-act">
              <button class="btn btn-chip-biru" type="button" onclick="buttonAdd()">Tambah</button>
            </div>
          @endif
        </div>

        <div class="modal fade rt-filter" id="modalFilterOP">
          <div class="modal-dialog modal-md">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">
                  <i class="bi bi-funnel"></i>
                  Filter Data
                  <span class="rt-active-badge" id="opFilterBadge">0 aktif</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterOP').modal('hide')"><span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <div class="rt-section">
                  <div class="rt-group-label">Status</div>
                  <div class="rt-grid-2">
                    <div>
                      <label class="rt-field-label" for="opStatus">Status Otorisasi</label>
                      <select class="rt-native" id="opStatus">
                        <option value="" selected>Semua Status</option>
                        <option value="0">Belum Otorisasi</option>
                        <option value="1">Sudah Otorisasi</option>
                      </select>
                    </div>
                    <div>
                      <label class="rt-field-label" for="opStatusOpname">Status Opname</label>
                      <select class="rt-native" id="opStatusOpname">
                        <option value="" selected>Semua Status</option>
                        <option value="Belum">Belum</option>
                        <option value="Sudah">Sudah</option>
                        <option value="Batal">Batal</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="rt-reset-link" onclick="$('#opStatus').val(''); $('#opStatusOpname').val('');">Reset semua</button>
                <div class="rt-footer-buttons">
                  <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal" onclick="$('#modalFilterOP').modal('hide')">Batal</button>
                  <button type="button" class="rt-btn rt-btn-primary" onclick="opFilterChanged(); $('#modalFilterOP').modal('hide');">Terapkan</button>
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

<div id="page2" style="display: none" class="mainpage container-fluid" >

  <div class="row">
    <div class="col-8 text-left">
      <h2></h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="
      height: 30px; 
      margin-top: 20px; 
      padding: 4px 12px; 
      border-radius: 20px; 
      font-size: 0.75rem; 
      font-weight: 600; 
      text-transform: uppercase; 
      transition: background-color 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="buttonCloseForm()">CLOSE</button>
    </div>
  </div>

  <div id= "formAdd" class="">

  <div id="formBsGrid" class="">
  <div class="">
    <!-- <h1>Tes Modal</h1> -->

    <div class="container-fluid">
      <input type="hidden" name="noUrut" id="input_add_nourut" value="" />

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
              <input type="hidden" class="form-control" id="input_add_nourut" placeholder="" disabled>
            <input type="text" class="form-control" id="input_add_nobukti" placeholder="No Bukti" disabled>
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
            <input type="date" class="form-control text-left" id="input_add_tanggal" placeholder="" disabled>
          </div>
        </div>
      </div>

        </div>

        <div class="col-md-3">
          <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label>Tgl Plk.</label>
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group">
              <input type="date" class="form-control text-left" id="input_add_tanggalpelaksanaan" placeholder="" disabled>
            </div>
          </div>
        </div>

        </div>

      </div>

      <div class="row" style="margin-top: -5px">
        <div class="col-md-6">
          <div class="row">
            <div class="col-md-2">
              <div class="form-group">
              <label>Keterangan</label>
            </div>
            </div>
            <!-- <div class="col-4 text-right">

              </div> -->
            <div class="col-md-10">
              <div class="input-group form-group">
                <input id="input_add_keterangan" type="text" class="form-control" disabled>

                <!-- <button id="buttonAddListValas" type="button" onclick="buttonAddListValas()" class="btn btn-primary" disabled >+</button> -->

              </div>
            </div>
          </div>

        </div>

        <div class="col-md-3">
          <div class="row">


        <div class="col-md-4">
          <div class="form-group">
            <label>Tgl Cutoff</label>
          </div>
        </div>
        <div class="col-md-8">
          <div class="form-group">
            <input type="date" class="form-control text-left" id="input_add_tanggalcutoff" placeholder="" disabled>
          </div>
        </div>
      </div>

        </div>


      </div>

      <div class="row" style="margin-top: -10px">


        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
              <label>Headgroup</label>
            </div>
            </div>
            <!-- <div class="col-4 text-right">

              </div> -->
            <div class="col-md-8">
              <div class="input-group form-group">
                <input id="input_add_kodehdgrp" type="text" class="form-control" disabled>

                <button id="buttonAddListHeadGroup" type="button" onclick="buttonAddListHeadGroup()" class="btn btn-chip-biru"><i class="bi bi-search"></i></button>

              </div>
            </div>
          </div>

        </div>
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
              <label>Kategori</label>
            </div>
            </div>
            <!-- <div class="col-4 text-right">

              </div> -->
            <div class="col-md-8">
              <div class="input-group form-group">
                <input id="input_add_kodekategori" type="text" class="form-control" disabled>

                <button id="buttonAddListKategori" type="button" onclick="buttonAddListKategori()" class="btn btn-chip-biru"><i class="bi bi-search"></i></button>

              </div>
            </div>
          </div>

        </div>

        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
              <label>SubKategori</label>
            </div>
            </div>
            <!-- <div class="col-4 text-right">

              </div> -->
            <div class="col-md-8">
              <div class="input-group form-group">
                <input id="input_add_kodesubkategori" type="text" class="form-control" disabled>

                <button id="buttonAddListSubKategori" type="button" onclick="buttonAddListSubKategori()" class="btn btn-chip-biru"><i class="bi bi-search"></i></button>

              </div>
            </div>
          </div>

        </div>




      </div>

      <div class="row" style="margin-top: -10px">
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
              <label>Gudang</label>
            </div>
            </div>
            <!-- <div class="col-4 text-right">

              </div> -->
            <div class="col-md-8">
              <div class="input-group form-group">
                <select id="input_add_gudang" class="form-control" disabled>
                  <option value="">- Pilih Gudang -</option>
                </select>
              </div>
            </div>
          </div>

        </div>
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
              <label>Merk</label>
            </div>
            </div>
            <!-- <div class="col-4 text-right">

              </div> -->
            <div class="col-md-8">
              <div class="input-group form-group">
                <input id="input_add_kodemerk" type="text" class="form-control" disabled>

                <button id="buttonAddListMerk" type="button" onclick="buttonAddListMerk()" class="btn btn-chip-biru"><i class="bi bi-search"></i></button>

              </div>
            </div>
          </div>

        </div>
      </div>




      </div>



<div class="container-fluid">
  <hr/>

</div>



  <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">

        <table id="addTable" class="table table-bordered table-striped"  >
          <thead class="text-center">
            <tr>
              <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
              <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
              <th style="padding: 4px 12px;" scope="col">Satuan</th>
              <th style="padding: 4px 12px;" scope="col">Qty Saldo</th>

              <th style="padding: 4px 12px;" scope="col">Actions</th>

            </tr>
          </thead>


          <tbody id="addTableData" class="" >
            <tr >

                <td colspan=6 class="text-center">Belum ada data</td>

          </tr>

          </tbody>


        </table>
  </div>


  <div class="col-md-12 mt-2 text-right">
  <button id="buttonAddItem" type="button" class="btn btn-chip-biru" onclick="buttonAddItem()" class="btn btn-secondary" style="height: 30px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;">Tambah Item</button>
</div>


  </div>
</div>

    </div>


  </div>


  <div id="page3" style="display: none" class="mainpage container-fluid" >

    <div class="row">
      <div class="col-8 text-left">
        <h2></h2>
      </div>
      <div class="col-4 text-right">
        <button type="button" class="btn btn-danger btn-lg " style="height: 30px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()">CLOSE</button>
      </div>
    </div>

    <div id= "" class="">



    <div id="formBsGrid" class="">
    <div class="">
      <!-- <h1>Tes Modal</h1> -->

      <div class="container-fluid">
        <input type="hidden" name="noUrut" id="input_detail_nourut" value="" />

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
                <input type="hidden" class="form-control" id="input_detail_nourut" placeholder="" disabled>
              <input type="text" class="form-control" id="input_detail_nobukti" placeholder="No Bukti" disabled>
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
              <input type="date" class="form-control text-left" id="input_detail_tanggal" placeholder="" disabled>
            </div>
          </div>
        </div>

          </div>

          <div class="col-md-3">
            <div class="row">


          <div class="col-md-4">
            <div class="form-group">
              <label>Tgl Plk.</label>
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group">
              <input type="date" class="form-control text-left" id="input_detail_tanggalpelaksanaan" placeholder="" disabled>
            </div>
          </div>
        </div>

          </div>

        </div>

        <div class="row" style="margin-top: -5px">
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-2">
                <div class="form-group">
                <label>Keterangan</label>
              </div>
              </div>
              <!-- <div class="col-4 text-right">

                </div> -->
              <div class="col-md-10">
                <div class="input-group form-group">
                  <input id="input_detail_keterangan" type="text" class="form-control" disabled>

                  <!-- <button id="buttonAddListValas" type="button" onclick="buttonAddListValas()" class="btn btn-primary" disabled >+</button> -->

                </div>
              </div>
            </div>

          </div>

          <div class="col-md-3">
            <div class="row">


          <div class="col-md-4">
            <div class="form-group">
              <label>Tgl Cutoff</label>
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group">
              <input type="date" class="form-control text-left" id="input_detail_tanggalcutoff" placeholder="" disabled>
            </div>
          </div>
        </div>

          </div>




        </div>

        <div class="row" style="margin-top: -10px">




          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                <label>Headgroup</label>
              </div>
              </div>
              <!-- <div class="col-4 text-right">

                </div> -->
              <div class="col-md-8">
                <div class="input-group form-group">
                  <input id="input_detail_kodehdgrp" type="text" class="form-control" disabled>


                </div>
              </div>
            </div>

          </div>
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                <label>Kategori</label>
              </div>
              </div>
              <!-- <div class="col-4 text-right">

                </div> -->
              <div class="col-md-8">
                <div class="input-group form-group">
                  <input id="input_detail_kodekategori" type="text" class="form-control" disabled>


                </div>
              </div>
            </div>

          </div>

          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                <label>SubKategori</label>
              </div>
              </div>
              <!-- <div class="col-4 text-right">

                </div> -->
              <div class="col-md-8">
                <div class="input-group form-group">
                  <input id="input_detail_kodesubkategori" type="text" class="form-control" disabled>


                </div>
              </div>
            </div>

          </div>




        </div>

        <div class="row" style="margin-top: -10px">
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                <label>Gudang</label>
              </div>
              </div>
              <!-- <div class="col-4 text-right">

                </div> -->
              <div class="col-md-8">
                <div class="input-group form-group">
                  <input id="input_detail_gudang" type="text" class="form-control" disabled>

                  <!-- <button id="buttonAddListValas" type="button" onclick="buttonAddListValas()" class="btn btn-primary" disabled >+</button> -->

                </div>
              </div>
            </div>

          </div>
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                <label>Merk</label>
              </div>
              </div>
              <!-- <div class="col-4 text-right">

                </div> -->
              <div class="col-md-8">
                <div class="input-group form-group">
                  <input id="input_detail_kodemerk" type="text" class="form-control" disabled>

                </div>
              </div>
            </div>

          </div>
        </div>
















        </div>



  <div class="container-fluid">
    <hr/>

  </div>



    <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">

          <table id="detailTable" class="table table-bordered table-striped"  >
            <thead class="text-center">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                <th style="padding: 4px 12px;" scope="col">Satuan</th>
                <th style="padding: 4px 12px;" scope="col">Qty Saldo</th>


              </tr>
            </thead>


            <tbody id="detailTableData" class="" >
              <tr >

                  <td colspan=5 class="text-center">Belum ada data</td>

            </tr>

            </tbody>


          </table>
    </div>
    <div class="container-fluid">


    <div class="row" style="">
      <div class="col-6 text-left">
      </div>
      <div class="col-6 text-right">
        <button type="button" class="page3showhide otorisasishowhide btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600;  " onclick="submitOtorisasi()"  >Otorisasi</button>
        <button type="button" class="page3showhide printshowhide btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600;  " onclick="submitPrint()"  >Print</button>
      </div>
    </div>
    </div>



    </div>
  </div>

      </div>


    </div>



  </div>
</div>



<!--  -->

<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialo g-centered"  role="document">
    <div id="" class="modal-content ">



      <div id= "modalAddListGudang" class="showhidemodalbodyadd">
      <div class="modal-header">


          <h5 class="modal-title" id="modalAddListGudangTitle">Gudang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-30px;">

            <table id="tabel_add_list_gudang" class="table table-bordered table-striped" style="overflow:auto; " >
              <thead class="text-center">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>
                </tr>
              </thead>


              <tbody id="tabel_data_add_list_gudang" class="text-left" >

                <tr class="pick-row">
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


      {{-- <div id="" class="modal-footer ">
        <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()" >Batal</button>
      </div> --}}
      </div>


      <div id= "modalAddListMerk" class="showhidemodalbodyadd">
      <div class="modal-header">
          <h5 class="modal-title" id="">Merk</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body" style="margin-top:-30px;">

        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto;">

            <table id="tabel_add_list_merk" class="table table-bordered table-striped" style="overflow:auto; " >
              <thead class="text-center">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>
                </tr>
              </thead>


              <tbody id="tabel_data_add_list_merk" class="text-left" >

                <tr class="pick-row">
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


      {{-- <div id="" class="modal-footer ">
        <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()" >Batal</button>
      </div> --}}
      </div>


      </div>







    </div>
  </div>
<!-- End modal add-->



<div class="modal fade" id="formAddItem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialo g-centered"  role="document" style="min-width: 1400px">
    <div id="" class="modal-content ">

      <div id= "" class="">
      <div class="modal-header">


          <h5 class="modal-title" id="">Tambah Item</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="formBsGrid" style='margin-top:-25px;'>
      <div class="modal-body">

        <div class="container-fluid" >
          <div class="row">
            <div class="col-12">
              <h3></h3>
            </div>

          </div>
          <div class="row ">
            <div class="col-md-3">
              <div class="row">

            <div class="col-md-4">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                  <!-- <input type="hidden" class="form-control" id="input_modal_nourut" placeholder="" disabled> -->
                <input type="text" class="form-control" id="input_modal_nobukti" placeholder="No Bukti" disabled>
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
                <input type="date" class="form-control text-left" id="input_modal_tanggal" placeholder="" disabled>
              </div>
            </div>
          </div>

            </div>


            <div class="col-md-3 ">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                  <label>Gudang</label>
                </div>
                </div>
                <!-- <div class="col-4 text-right">

                  </div> -->
                <div class="col-md-8">
                  <div class="input-group form-group">
                    <input id="input_modal_gudang" type="text" class="form-control" disabled>


                  </div>
                </div>
              </div>

            </div>

          </div>

          <div class="row" style="margin-top: -10px">
            <div class="col-md-3 ">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                  <label>Group</label>
                </div>
                </div>
                <!-- <div class="col-4 text-right">

                  </div> -->
                <div class="col-md-8">
                  <div class="input-group form-group">
                    <input id="input_modal_hdgrp" type="text" class="form-control" disabled>


                  </div>
                </div>
              </div>

            </div>

            <div class="col-md-3 ">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                  <label>Kategori</label>
                </div>
                </div>
                <!-- <div class="col-4 text-right">

                  </div> -->
                <div class="col-md-8">
                  <div class="input-group form-group">
                    <input id="input_modal_kategori" type="text" class="form-control" disabled>


                  </div>
                </div>
              </div>

            </div>
            <div class="col-md-3 ">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                  <label>SubKategori</label>
                </div>
                </div>
                <!-- <div class="col-4 text-right">

                  </div> -->
                <div class="col-md-8">
                  <div class="input-group form-group">
                    <input id="input_modal_subkategori" type="text" class="form-control" disabled>


                  </div>
                </div>
              </div>

            </div>

            <div class="col-md-3 ">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                  <label>merk</label>
                </div>
                </div>
                <!-- <div class="col-4 text-right">

                  </div> -->
                <div class="col-md-8">
                  <div class="input-group form-group">
                    <input id="input_modal_merk" type="text" class="form-control" disabled>


                  </div>
                </div>
              </div>

            </div>







          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto;  max-height: 400px">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_modal" class="table table-bordered table-striped" style="overflow:auto; " >
              <thead class="text-center" style="position: sticky;
            top: 0;
            z-index: 1;">
                <tr>
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Pilih</th>
                  <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Satuan</th>
                  <th style="padding: 4px 12px;" scope="col">Qty</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_modal" class="text-left" >

                <tr >

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


      <div class="modal-footer">
        <button type="button" class="btn btn-danger" style="
                height: 30px; 
                padding: 4px 12px; 
                border-radius: 20px; 
                font-size: 0.75rem; 
                font-weight: 600; 
                text-transform: uppercase; 
                transition: background-color 0.3s, box-shadow 0.3s;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" data-dismiss="modal" >Batal</button>
        <button type="button" class="btn btn-chip-biru" style="
                height: 30px; 
                padding: 4px 12px; 
                border-radius: 20px; 
                font-size: 0.75rem; 
                font-weight: 600; 
                text-transform: uppercase; 
                transition: background-color 0.3s, box-shadow 0.3s;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="submitAddItem()">Simpan</button>
      </div>
      </div>



      </div>




    </div>
  </div>


@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  jQuery('#form').addClass('picker-kas')
  jQuery('#form .modal-footer .btn').filter(function () {
    return jQuery.trim(jQuery(this).text()) === 'Batal'
  }).addClass('picker-kas-batal')
})
</script>
<script type="text/javascript">

let tipeform = ''
let listData = []
let listDataAdd = []
let tempDataAdd = []

/* ============ Header tabel interaktif (window.ReportTable) ============
 * Port 1:1 dari pola ctCart/ctAktifkanTabel/ctInitReportTableSekali milik
 * cetaktandaterima.blade.php (dan transferbarang.blade.php), diterapkan ke
 * #tabel/#tabel2 halaman ini. Endpoint persistensinya saveheadertable/
 * getheadertable (HeaderTableController), generik untuk semua halaman --
 * halaman ini tidak punya endpoint sendiri sebelumnya, jadi tidak ada kontrak
 * lama yang perlu dipertahankan.
 *
 * Catatan bentuk data: baris tabel/tabel2 di halaman ini bukan objek datar --
 * setiap baris adalah array dan field aslinya ada di row[0] (mis. row[0].NoBukti),
 * baik dari render Blade awal ($tempOutstanding[$i][0]->...) maupun dari
 * loadAll() (res.tempOutstanding[i][0].NoBukti). opPickCI() karena itu selalu
 * dipanggil dengan row[0], bukan row.
 */

let opCart = []
const OP_HREF = 'perintahopname'
const OP_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date', 3 : 'bool' }
const OP_TIPE_KODE = { varchar : 0, float : 1, date : 2, bool : 3 }

function opPickCI (row, key) {
  if (!row) { return undefined; }
  if (row[key] !== undefined) { return row[key]; }
  let lower = key.toLowerCase();
  for (let k in row) {
    if (k.toLowerCase() === lower) { return row[k]; }
  }
  return undefined;
}

function opDefaultCart () {
  return [
    ['NoBukti',      'No. Bukti',   1, 'varchar', 0, 0],
    ['Tanggal',      'Tanggal',     1, 'date',    0, 0],
    ['Status',       'Status',      1, 'varchar', 0, 0],
    ['Keterangan',   'Keterangan',  1, 'varchar', 0, 0],
    ['KodeGdg',      'Gudang',      1, 'varchar', 0, 0],
    ['KodeHdGrp',    'Kode Hdgrp',  1, 'varchar', 0, 0],
    ['NAMAHDGRP',    'Nama Hdgrp',  1, 'varchar', 0, 0],
    ['KodeSubGrp',   'Kode Subgrp', 1, 'varchar', 0, 0],
    ['NamaSubGrp',   'Nama Subgrp', 1, 'varchar', 0, 0],
    ['KodeMerk',     'Kode Merk',   1, 'varchar', 0, 0],
    ['NAMAMERK',     'Merk',        1, 'varchar', 0, 0],
    ['IsOtorisasi1', 'Otorisasi',   1, 'bool',    0, 0],
    ['OtoUser1',     'OtoUser',     1, 'varchar', 0, 0],
    ['TglOto1',      'TglOto',      1, 'date',    0, 0]
  ]
}

function opBuatCart (headers, values, isnumerics, isshowns, desimals) {
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
      OP_TIPE_NAMA[tipe] || 'varchar',
      0,
      isNaN(des) ? 0 : des,
    ])
  });
  return cart
}

window.g_href = OP_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function (href, mode) {
  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  opCart.forEach((c) => {
    header.push(c[1])
    value.push(c[0])
    isnumber.push(OP_TIPE_KODE[c[3]] ?? 0)
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
      href     : OP_HREF,
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
      href   : OP_HREF,
      urut   : 1,
      reset  : reset ? 1 : 0
    },
    success : function (res) {
      if (!reset && res && res.headertableheader && res.headertableheader.length) {
        opCart = opBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal || [])
      } else {
        opCart = opDefaultCart()
        window.gcart_header = opCart
        window.doSimpanHeader(OP_HREF, 1)
      }
      window.gcart_header = opCart
    },
    error : function (err) {
      console.log(err)
      alertify.warning(reset ? 'Gagal mengembalikan kolom ke tampilan default' : 'Gagal memuat pengaturan kolom')
      opCart = opDefaultCart()
      window.gcart_header = opCart
    }
  })
}

let opRtSudahInit = false
function opInitReportTableSekali () {
  if (opRtSudahInit || typeof ReportTable === 'undefined') { return }
  opRtSudahInit = true

  ReportTable.init({ table : '#tabel', bar : '#rtBarTabel', onChange : reinitTabel })

  let opGuardUlangKlik = false;
  let thead = document.querySelector('#tabel thead')
  if (thead) {
    thead.addEventListener('click', function (e) {
      if (opGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }
      e.stopPropagation()
      e.preventDefault()
      opGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles: false, cancelable: true, view: window })
      Object.defineProperty(ulang, 'target', { value: interaktif, configurable: true })
      thead.dispatchEvent(ulang)
      opGuardUlangKlik = false
    }, true)
  }
}

function tulisTheadHeaderOP (tableSel, cols) {
  let thead = document.querySelector(tableSel + ' thead')
  if (!thead || !window.ReportTable) { return; }
  let headRowHtml = ReportTable.headHtml(cols)
    .replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>');
  thead.setAttribute('style', 'white-space:nowrap;');
  thead.innerHTML = headRowHtml;
}

function opValueCell (row, col) {
  let raw = opPickCI(row, col[0]);
  let type = col[3];

  {{-- Status (varchar "Belum"/"Batal"/"Sudah") -- badge titik+pill .sp-badge, sama
       seperti kolom Status di permintaanpemakaian.blade.php (hijau untuk Sudah, merah
       untuk Belum/Batal). Label aslinya dipertahankan (bukan di-hardcode "Sudah"/
       "Belum") supaya "Batal" tetap tampil apa adanya. --}}
  if (col[0] === 'Status') {
    let label = (raw !== undefined && raw !== null) ? raw : '';
    let sudah = String(raw).trim().toLowerCase() === 'sudah';
    return '<td><span class="sp-badge ' + (sudah ? 'is-active' : 'is-inactive') + '">' + label + '</span></td>';
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
  if (type === 'bool') {
    return Number(raw)
      ? '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>'
      : '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>';
  }
  return '<td>' + (raw !== undefined && raw !== null ? raw : '') + '</td>';
}

function opTabelActionsCell (row) {
  let r = row[0];
  let nobukti = opPickCI(r, 'NoBukti');
  let sudah = Number(opPickCI(r, 'IsOtorisasi1')) === 1;
  let html = '<td class="text-center" style="white-space:nowrap;"><div class="action-buttons-wrap">';
  html += '<button class="btn btn-warning btn-sm" title="Detail" type="button" onclick="buttonDetail(\'' + nobukti + '\' , \'detail\')"><i class="bi bi-info"></i></button>';
  if (sudah) {
    html += '<button class="btn btn-danger btn-sm" title="Batal Otorisasi" type="button" onclick="buttonBatalOtorisasi(\'' + nobukti + '\' , \'edit\')"><i class="bi bi-key"></i></button>';
    html += '<button class="btn btn-primary btn-sm" title="Print" type="button" title="Print" onclick="submitPrint(\'' + nobukti + '\')"><i class="bi bi-printer"></i></button>';
  } else {
    html += '<button class="btn btn-success btn-sm" title="Edit" type="button" onclick="buttonKoreksi(\'' + nobukti + '\' , \'edit\')"><i class="bi bi-pen"></i></button>';
    html += '<button class="btn btn-primary btn-sm" title="Otorisasi" type="button" onclick="submitOtorisasi(\'' + nobukti + '\' , \'otorisasi\')"><i class="bi bi-key"></i></button>';
  }
  html += '</div></td>';
  return html;
}

function opFilterRows (rows) {
  let oto = $('#opStatus').val()
  let statusOpname = $('#opStatusOpname').val()
  let awal = $('#opTanggalAwal').val()
  let akhir = $('#opTanggalAkhir').val()
  return (rows || []).filter(function (row) {
    let r = row[0]
    {{-- Status Otorisasi (IsOtorisasi1 bool) dan Status Opname (varchar
         "Belum"/"Sudah"/"Batal", mencerminkan progres di Opname Barang) adalah dua
         konsep berbeda -- SPK bisa saja sudah diotorisasi tapi belum "di-opname",
         atau sebaliknya -- jadi disaring independen, bukan AND dipaksa cocok. --}}
    if (oto !== '' && String(Number(opPickCI(r, 'IsOtorisasi1'))) !== oto) { return false }
    if (statusOpname !== '') {
      let statusText = String(opPickCI(r, 'Status') ?? '').trim().toLowerCase()
      if (statusText !== statusOpname.toLowerCase()) { return false }
    }
    if (awal || akhir) {
      let tgl = opPickCI(r, 'Tanggal')
      if (!tgl) { return false }
      let t = String(tgl).slice(0, 10)
      if (awal && t < awal) { return false }
      if (akhir && t > akhir) { return false }
    }
    return true
  })
}

function opFilterChanged () {
  loadAll()
}

function renderTabelRows (rows) {
  window.gcart_header = opCart
  let cols = (opCart.length ? opCart : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  (rows || []).forEach(function (row) {
    html += '<tr>' + opTabelActionsCell(row);
    cols.forEach(function (col) { html += opValueCell(row[0], col); });
    html += '</tr>';
  });
  document.getElementById('tabel_data').innerHTML = html;
  tulisTheadHeaderOP('#tabel', cols);
}

let lastTabelRows = []
let opPanjangHalaman = 10

function opIkatSearch () {
  let input = document.getElementById('opSearch1')
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

function opIkatPanjangHalaman () {
  let sel = document.getElementById('opLen1')
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(opPanjangHalaman)

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    opPanjangHalaman = (n === -1 || n > 0) ? n : 10
    if ($.fn.DataTable.isDataTable('#tabel')) {
      $('#tabel').DataTable().page.len(opPanjangHalaman).draw()
    }
  })
}

const OP_DOM_STRING = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"

function reinitTabel () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().destroy(); }
    renderTabelRows(opFilterRows(lastTabelRows));
    $('#tabel').DataTable({
      dom: OP_DOM_STRING,
      lengthChange: false,
      pageLength: opPanjangHalaman,
      paging: true,
      ordering: false,
    });
    opIkatSearch();
    opIkatPanjangHalaman();
  } catch (e) {
    console.error('reinitTabel failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

function buttonHeaderTable () {
  alertify.confirm('Reset Kolom', 'Kembalikan kolom tabel ke tampilan default?', function () {
    window.doSetHeader(1, true)
    reinitTabel()
    alertify.success('Kolom telah direset ke tampilan default')
  }, function () {})
}

$(document).ready(function(){
      window.doSetHeader(1, false);
      lastTabelRows = [].concat(@json($tempOutstanding), @json($tempPenerimaan));
      reinitTabel();

      opInitReportTableSekali();

      loadGudangOptions();

        // $('.mainpage').hide()
        // $('#page2').show()
        //
        // $("#formAddItem").modal('toggle');

      $("#tabel_add_list_modal").DataTable({
        "lengthChange": false,
          "paging": false ,
          "columnDefs": [
     ]
    });







        $("#tabel_add_list_custsupp").DataTable({
          "lengthChange": false,
            "paging": false ,
      });
});


function buttonAddListBatal () {
  $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddMain').show();

  $("#form").modal('toggle')
}


function loadAll () {
  console.log('loadall')
  let awal = $('#opTanggalAwal').val()
  let akhir = $('#opTanggalAkhir').val()
  $.ajax({
    url: "{!! url('perintahopnameloadall') !!}",
    type: "get",
    async: false,
    data: {
      date1: awal,
      date2: akhir
    },
    success: function(res) {
      console.log(res)

      lastTabelRows = [].concat(res.tempOutstanding, res.tempPenerimaan)
      reinitTabel()
    }
  })
}

function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: "{!! url('perintahopnamedetailCetak') !!}",
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
    let tanggalOnly = dataPrint[0].Tanggal.split(' ')[0];
    let tanggalcutoff = dataPrint[0].tglcutoff.split(' ')[0];
    let tanggalpelaksanaan = dataPrint[0].TglPelaksanaan.split(' ')[0];

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
                  <div class="pb-1" style="width: 100%">Gudang : `+dataPrint[0].Kodegdg+`</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">Tgl Cut Off: `+tanggalcutoff+`</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">Tgl Opname : `+tanggalpelaksanaan+`</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
              </div>


              <div style="width: 38%">
                <div style="display: flex; width: 100%">
                  <h2 class="m-0 pb-2">PERINTAH OPNAME</h2>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Tanggal</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+tanggalOnly+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">No Bukti</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+dataPrint[0].NoBukti+`</div>
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
                    <td class="text-center" style="width: 15%">KODE BARANG</td>
                    <td class="text-center" style="width: 30%">URAIAN BARANG</td>
                    <td class="text-center" style="width: 10%">PART NO</td>
                    <td class="text-center" style="width: 10%">MERK</td>
                    <td class="text-center" style="width: 5%">SAT</td>
                    <td class="text-center" style="width: 10%">SALDO FISIK</td>
                    <td class="text-center" style="width: 10%">SALDO KARTU</td>
                    <td class="text-center" style="width: 10%">SELISIH</td>
                    <td class="text-center" style="width: 10%">KETERANGAN</td>
                  </tr>
                </thead> `;

    let z = 0
    let tempPrintStr = ``
    // buat hitung grandtotal
    let grandTotalFisik = 0;
    let grandTotalKartu = 0;
    let grandTotalSelisih = 0;

    dataPrint.forEach(item => {

      if (item.saldofisik) {
        grandTotalFisik += Number(item.saldofisik) || 0;
      }

      if (item.saldokartu) {
        grandTotalKartu += Number(item.saldokartu) || 0;
      }

      if (item.selisih) {
        grandTotalSelisih += Number(item.selisih) || 0;
      }
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
               style="width: 15%;  ">${itemSub.Kodebrg}</td>
         <td class="text-align: left"
               style="width: 30%;">${itemSub.NAMABRG}</td>
         <td class="text-align: left"
               style="width: 10%;">${itemSub.PartNumber}</td>
         <td class="text-align: left"
               style="width: 10%;">${itemSub.NAMAMERK}</td>
         <td class="text-align: text-center"
               style="width: 5%;">${itemSub.Satuan}</td>
         <td class="text-align: text-right"
               style="width: 10%;">${itemSub.saldofisik}</td>
         <td class="text-align: text-right"
               style="width: 10%;">${itemSub.saldokartu}</td>
         <td class="text-align: text-right"
               style="width: 10%;">${itemSub.selisih}</td>
         <td class="text-align: left"
               style="width: 10%;">${itemSub.keterangan}</td>
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

         <div style="display:flex; justify-content:space-between; width:300px">
         <h5 style="margin:0">
          Jumlah : ${grandTotalFisik.toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
          })}
         </h5>
         <h5 style="margin:0">
            ${grandTotalKartu.toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
          })}
         </h5>
         <h5 style="margin:0">
            ${grandTotalSelisih.toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
          })}
         </h5>
         </div>

         </span>
         </div>


           <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: 20px ; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 20%">Adm.Gudang</td>
               <td class="no-border text-center" style="width: 20%">Supervisor Stock</td>
               <td class="no-border text-center" style="width: 20%">PIC Gudang</td>
               <td class="no-border text-center" style="width: 20%">Kepala Gudang</td>
               <td class="no-border text-center" style="width: 20%">Dibuat Oleh</td>
             </tr>
             <tr style="height: 2.5rem">
               <td class="no-border">&nbsp;</td>
             </tr>

             <tr>
               <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
               </td>
               <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
               </td>
               <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
               </td>
               <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
               </td>
               <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
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

function buttonAddItem () {
    tempDataAdd = []
    let _token = $("#_token").val();
    let nobukti = $("#input_add_nobukti").val();
    let kodehdgrp = $("#input_add_kodehdgrp").val();
    let kodekategori = $("#input_add_kodekategori").val();
    let kodesubkategori = $("#input_add_kodesubkategori").val();
    let gudang = $("#input_add_gudang").val();
    let kodemerk = $("#input_add_kodemerk").val();
    let tanggal = $("#input_add_tanggal").val();

    if (!kodehdgrp || !gudang ||  gudang == '-' || kodehdgrp == '-') {
      alertify.warning("Kode gudang dan group harus diisi")
      return
    }

    let checkDate = new Date($("#input_add_tanggal").val())
    let periode_bulan = document.getElementById("periode_bulan").value
    let periode_tahun = document.getElementById("periode_tahun").value


    if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

        alertify.warning("Tanggal tidak sesuai periode");
        return
    }

    document.getElementById("input_modal_nobukti").value = nobukti
    document.getElementById("input_modal_hdgrp").value = kodehdgrp
    document.getElementById("input_modal_kategori").value = kodekategori
    document.getElementById("input_modal_subkategori").value = kodesubkategori
    document.getElementById("input_modal_gudang").value = gudang
    document.getElementById("input_modal_tanggal").value = tanggal
    document.getElementById("input_modal_merk").value = kodemerk


    $.ajax({
      url: "{!! url('perintahopnamelistbarang') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        gudang,
        kodehdgrp,
        kodekategori,
        kodesubkategori,
        kodemerk,
        tanggal
      },
      success: function(res) {
        console.log("RES !")
        console.log(res)
        // return
        listDataAdd  = res
        let rowTable = `
        `
        res.forEach((item, i) => {
          rowTable += `
          <tr>
          <td class="text-center"><input class="" type="checkbox" value="" id="add_checkbox${i}" onchange="onchangeChecklist(${i},this.id)"></td>

          <td>${item.KodeBrg}</td>
          <td>${item.NamaBrg}</td>
          <td class="text-center">${item.Satuan}</td>
          <td class="text-right">${item.Qnt1 ? parseFloat(item.Qnt1).toFixed(2) : '0'}</td>
          </tr>`
        });

        $('#tabel_add_list_modal').DataTable().destroy();
        document.getElementById("tabel_data_add_list_modal").innerHTML = rowTable

        $("#tabel_add_list_modal").DataTable({
          "lengthChange": false,
            "paging": false ,
            "columnDefs": [
       ]
      });

      $("#formAddItem").modal('toggle');


      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })





}




function refreshDataTableDetail (nobukti) {

    let _token = $("#_token").val();
    listData = []
    $.ajax({
      url: "{!! url('perintahopnamespdetail') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti

      },
      success: function(res) {
        console.log(res)
        listData = res
        // console.log(res)
        // return
        // $('#formAddAdd').hide();

        // dataTableAdd = res

        let rowTable = ``
        listData.forEach((item, i) => {

          // <td>${item.TipeTrans == 'BBK' ? item.Lawan : item.Perkiraan}</td>
          // <td>${item.TipeTrans == 'BBK' ? item.NamaLawan : item.NamaPerkiraan}</td>
          // <td>${item.TipeTrans == 'BBK' ? item.Perkiraan : item.Lawan }</td>
          // <td>${item.TipeTrans == 'BBK' ?  item.NamaPerkiraan : item.NamaLawan }</td>

                rowTable += `
                  <tr>
                    <td>${item.KodeBrg}</td>
                    <td>${item.NamaBrg}</td>
                    <td class="text-center">${item.Satuan}</td>
                    <td class="text-right">0</td>



                  </tr>

                `

                // <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
        });

        document.getElementById("detailTableData").innerHTML = rowTable
        document.getElementById("input_detail_nobukti").value = listData[0].NoBukti
        document.getElementById("input_detail_nourut").value = listData[0].NoUrut


        document.getElementById("input_detail_tanggal").value = formatDate(listData[0].Tanggal)

        if (listData[0].TglPelaksanaan && Number(new Date(listData[0].TglPelaksanaan).getFullYear()) > 1999 ) {
          document.getElementById("input_detail_tanggalpelaksanaan").value = formatDate(listData[0].TglPelaksanaan)

        } else {
          document.getElementById("input_detail_tanggalpelaksanaan").value = ''

        }

        if (listData[0].TglCutOFF && Number(new Date(listData[0].TglCutOFF).getFullYear()) > 1999 ) {
          document.getElementById("input_detail_tanggalcutoff").value = formatDate(listData[0].TglCutOFF)

        } else {
          document.getElementById("input_detail_tanggalcutoff").value = ''

        }

        // document.getElementById("input_detail_tanggalpelaksanaan").value = listData[0].TglPelaksanaan ? formatDate(listData[0].TglPelaksanaan) : ''
        // document.getElementById("input_detail_tanggalcutoff").value = listData[0].TglCutOFF ? formatDate(listData[0].TglCutOFF) : ''
        document.getElementById("input_detail_keterangan").value = listData[0].Keterangan
        document.getElementById("input_detail_gudang").value = listData[0].KodeGdg
        document.getElementById("input_detail_kodehdgrp").value = listData[0].KodeHdGrp
        document.getElementById("input_detail_kodekategori").value = listData[0].KodeSubGrp
        document.getElementById("input_detail_kodesubkategori").value = listData[0].Jenis
        document.getElementById("input_detail_kodemerk").value = listData[0].KodeMerk



      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
        resRefresh = 0;
      }

    })
}


function buttonDetail (nobukti , tipe = 'detail') {
  console.log(tipe)
  let _token = $("#_token").val();

  $('.page3showhide').hide()


  refreshDataTableDetail(nobukti)
  if(!listData.length) {
    alertify.warning("Data tidak ditemukkan")
    return
  } else {

  }

  if (tipe == 'otorisasi') {
    let akses = $("#akses_isotorisasi1").val();
    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }

    $('.otorisasishowhide').show()
  } else {

  }

  $('.mainpage').hide()
  $('#page3').show()


}


function onchangeChecklist ( index ,id) {
  // console.log(index, data,id)
  let data = listDataAdd[index]
  console.log(data)


  if (document.getElementById(`add_checkbox${index}`).checked) {

    tempDataAdd.push(data)

  } else {
    console.log(data)
    let check = tempDataAdd.findIndex(el => el.KodeBrg === data.KodeBrg );
    console.log('hapus')
    tempDataAdd.splice(check, 1)
  }
}
function buttonBatalOtorisasi (nobukti) {
  let akses = $("#akses_isbatal").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
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
          url: "{!! url('perintahopnamespbatalotorisasi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti,
          pket :value

          },
          success: function(res) {
            alertify.success('Berhasil batal otorisasi')
            loadAll()



          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }

        })
      }
    ,function(){
      console.log('no')
      alertify.error("Action cancelled");
    });
}

function submitOtorisasi (nobukti) {
  console.log(nobukti);

  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('perintahopnamespotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti
    },
    success: function(res) {
      alertify.success('Berhasil update otorisasi');
      loadAll();
    },
    error: function(err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan silahkan refresh browser');
    }
  });
}

// function submitOtorisasi () {

//   let _token = $("#_token").val();
//   let nobukti = $("#input_detail_nobukti").val();
//   $.ajax({
//     url: "{!! url('perintahopnamespotorisasi') !!}",
//     type: "post",
//     async: false,
//     data: {
//       _token,
//       nobukti

//     },
//     success: function(res) {
//       alertify.success('Berhasil update otorisasi')
//       loadAll()
//       buttonCloseForm()

//     },
//     error: function (err) {
//       console.log(err)
//       alertify.warning('Terjadi kesalahan silahkan refresh browser')
//     }

//   })

// }

function submitAddItem () {
  let checkDate = new Date($("#input_add_tanggal").val())
  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }
  let _token = $("#_token").val();

  console.log(tempDataAdd)

  if(!tempDataAdd.length) {
    alertify.warning("Tidak ada data dipilih")
    return
  }

  let nobukti = $("#input_add_nobukti").val();
  let nourut = $("#input_add_nourut").val();
  let kodehdgrp = $("#input_add_kodehdgrp").val();
  let kodekategori = $("#input_add_kodekategori").val();
  let kodesubkategori = $("#input_add_kodesubkategori").val();
  let gudang = $("#input_add_gudang").val();
  let kodemerk = $("#input_add_kodemerk").val();
  let keterangan = $("#input_add_keterangan").val();
  let tanggal = $("#input_add_tanggal").val();
  let tanggalpelaksanaan = $("#input_add_tanggalpelaksanaan").val();
  let tanggalcutoff = $("#input_add_tanggalcutoff").val();
  let jmlrecord = tipeform == 'add' ? 0 : 1

  console.log({
    _token,
    choice: 'I',
    tempData : tempDataAdd ,
    nobukti,
    kodehdgrp,
    kodekategori,
    kodesubkategori,
    gudang,
    kodemerk,
    tanggal,
    keterangan,
    tanggal,
    tanggalpelaksanaan,
    tanggalcutoff,
    jmlrecord
  })

  $.ajax({
      url: "{!! url('perintahopnamespadd') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        tempData : tempDataAdd ,
        choice: 'I',
        nobukti,
        nourut,
        kodehdgrp,
        kodekategori,
        kodesubkategori,
        gudang,
        kodemerk,
        tanggal,
        keterangan,
        tanggal,
        tanggalpelaksanaan,
        tanggalcutoff,
        jmlrecord
      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          // $("#form").modal('toggle')
          alertify.success('POP telah ditambah');

          tipeform = 'edit'
          loadAll()
          $("#formAddItem").modal('toggle');
          buttonKoreksi(nobukti)

        }
        if (res == 2) {
          setNewNoBukti()
          alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
        }

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })



  return
  let tempData = []

  console.log(listDataAdd)
  listDataAdd.forEach((item, i) => {
    console.log(i)
    if (document.getElementById(`add_checkbox${i}`).checked) {

      tempData.push(listDataAdd[i])
    }
  });
  console.log(tempData)
  if (!tempData.length) {
    alertify.warning("Tidak ada data dipilih")
    return
  }





}



function buttonDeleteItem (index) {


    let barangDelete = listData[index]



    console.log(barangDelete)

    // return


    alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item '+ barangDelete.KodeBrg +' ?',
        function() {


            let nobukti = $("#input_add_nobukti").val();
            let nourut = $("#input_add_nourut").val();
            let kodehdgrp = $("#input_add_kodehdgrp").val();
            let kodekategori = $("#input_add_kodekategori").val();
            let kodesubkategori = $("#input_add_kodesubkategori").val();
            let gudang = $("#input_add_gudang").val();
            let kodemerk = $("#input_add_kodemerk").val();
            let keterangan = $("#input_add_keterangan").val();
            let tanggal = $("#input_add_tanggal").val();
            let tanggalpelaksanaan = $("#input_add_tanggalpelaksanaan").val();
            let tanggalcutoff = $("#input_add_tanggalcutoff").val();
            let jmlrecord = tipeform == 'add' ? 0 : 1
            let urut = barangDelete.Urut


            let _token  = $("#_token").val()

            $.ajax({
                url: "{!! url('perintahopnamespkoreksi') !!}",
                type: "post",
                async: false,
                data: {
                  _token,
                  tempData : tempDataAdd ,
                  choice: 'D',
                  nobukti,
                  nourut,
                  kodehdgrp,
                  kodekategori,
                  kodesubkategori,
                  gudang,
                  kodemerk,
                  tanggal,
                  keterangan,
                  tanggal,
                  tanggalpelaksanaan,
                  tanggalcutoff,
                  jmlrecord,
                  urut,
                  kodebrg: '',
                  namabrg: '',
                  satuan: ''
                },
                success: function(res) {
                  console.log(res ,'!')

                  if (res == 1) {
                    // $("#form").modal('toggle')
                    alertify.success('Item telah dihapus');

                    tipeform = 'edit'
                    loadAll()
                    refreshDataTable(nobukti)

                  }
                },
                error: function (err) {
                  console.log(err)
                  alertify.warning('Terjadi kesalahan silahkan refresh browser')
                }
              })
        }
      ,function(){
        console.log('no')
      });







}


function buttonAddPickKategori (index, kode,nama) {

  document.getElementById("input_add_kodesubkategori").value = '-'
  document.getElementById("input_add_kodekategori").value = kode
          $("#form").modal('toggle')

}

function buttonAddPickSubKategori (index, kode,nama) {


  document.getElementById("input_add_kodesubkategori").value = kode
          $("#form").modal('toggle')

}

function buttonAddPickHeadGroup (index, kode,nama) {

  document.getElementById("input_add_kodesubkategori").value = '-'
  document.getElementById("input_add_kodekategori").value = '-'
  document.getElementById("input_add_kodehdgrp").value = kode
          $("#form").modal('toggle')

}

function buttonAddPickMerk (index, kode,nama) {

  console.log()
  document.getElementById("input_add_kodemerk").value = kode
          $("#form").modal('toggle')

}


function buttonAddListMerk () {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('perintahopnamelistmerk') !!}",
    type: "get",
    async: false,
    data: {
      _token,
    },
    success: function(res) {
      console.log(res)
      // listLawan  = res
      let rowTable = `
      <tr class="pick-row" onclick="buttonAddPickMerk('-','-' , '-'  )">
      <td>-</td>
      <td>-</td>
      </tr>
      `
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickMerk(${i},'${item.KODEMERK}' , '${item.NAMAMERK}'  )">
        <td>${item.KODEMERK}</td>
        <td>${item.NAMAMERK}</td>
        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_merk").innerHTML = rowTable
      pickerKasInit('tabel_add_list_merk', { order: [[0, 'asc']] })

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListMerk').show();
        $("#form").modal('toggle')
      } else {
        alertify.warning("Merk tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}


function buttonAddListSubKategori () {
  let _token = $("#_token").val();
  let kode = $("#input_add_kodehdgrp").val();
  let kode1 = $("#input_add_kodekategori").val();
  if ( kode == '-' || !kode) {
    alertify.warning("Pilih headgroup")
    return


  }

  if ( kode1 == '-' || !kode1) {
    alertify.warning("Pilih kategori")
    return


  }

  $.ajax({
    url: "{!! url('perintahopnamelistsubkategori') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kode,
      kode1
    },
    success: function(res) {
      console.log(res)
      // listLawan  = res
      let rowTable = `
      <tr class="pick-row" onclick="buttonAddPickSubKategori('-','-' , '-'  )">
      <td>-</td>
      <td>-</td>
      </tr>
      `
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickSubKategori(${i},'${item.Urut}' , '${item.Keterangan}'  )">
        <td>${item.Urut}</td>
        <td>${item.Keterangan}</td>
        </tr>`
      });








      document.getElementById("tabel_data_add_list_gudang").innerHTML = rowTable
      pickerKasInit('tabel_add_list_gudang', { order: [[0, 'asc']] })
      document.getElementById("modalAddListGudangTitle").innerHTML= 'Sub Kategori'

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListGudang').show();
        $("#form").modal('toggle')
      } else {
        alertify.warning("Merk tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}


function buttonAddListKategori () {
  let _token = $("#_token").val();
  let kode = $("#input_add_kodehdgrp").val();

  if ( kode == '-' || !kode) {
    alertify.warning("Pilih headgroup")
    return


  }

  $.ajax({
    url: "{!! url('perintahopnamelistkategori') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kode
    },
    success: function(res) {
      console.log(res)
      // listLawan  = res
      let rowTable = `
      <tr class="pick-row" onclick="buttonAddPickKategori('-','-' , '-'  )">
      <td>-</td>
      <td>-</td>
      </tr>
      `
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickKategori(${i},'${item.KodeSubGrp}' , '${item.NamaSubGrp}'  )">
        <td>${item.KodeSubGrp}</td>
        <td>${item.NamaSubGrp}</td>
        </tr>`
      });








      document.getElementById("tabel_data_add_list_gudang").innerHTML = rowTable
      pickerKasInit('tabel_add_list_gudang', { order: [[0, 'asc']] })
      document.getElementById("modalAddListGudangTitle").innerHTML= 'Kategori'

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListGudang').show();
        $("#form").modal('toggle')
      } else {
        alertify.warning("Merk tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function buttonAddListHeadGroup () {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('perintahopnamelistheadgroup') !!}",
    type: "get",
    async: false,
    data: {
      _token,
    },
    success: function(res) {
      console.log(res)
      // listLawan  = res
      let rowTable = `
      <tr class="pick-row" onclick="buttonAddPickHeadGroup('-','-' , '-'  )">
      <td>-</td>
      <td>-</td>
      </tr>
      `
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickHeadGroup(${i},'${item.KodeHDGrp}' , '${item.NamaHDGRP}'  )">
        <td>${item.KodeHDGrp}</td>
        <td>${item.NamaHDGRP}</td>
        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }

      document.getElementById("tabel_data_add_list_gudang").innerHTML = rowTable
      pickerKasInit('tabel_add_list_gudang', { order: [[0, 'asc']] })
      document.getElementById("modalAddListGudangTitle").innerHTML= 'Headgroup'

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListGudang').show();
        $("#form").modal('toggle')
      } else {
        alertify.warning("Merk tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function loadGudangOptions () {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('perintahopnamelistgudang') !!}",
    type: "get",
    async: false,
    data: {
      _token,
    },
    success: function(res) {
      let options = '<option value="">- Pilih Gudang -</option>'
      res.forEach((item) => {
        options += `<option value="${item.KodeGdg}">${item.KodeGdg} - ${item.Nama}</option>`
      });
      document.getElementById("input_add_gudang").innerHTML = options
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function lockForm (value = false) {
  document.getElementById("input_add_tanggal").disabled = value
  document.getElementById("input_add_tanggalpelaksanaan").disabled = value
  document.getElementById("input_add_tanggalcutoff").disabled = value
  document.getElementById("input_add_keterangan").disabled = value
  document.getElementById("buttonAddListHeadGroup").disabled = value
  document.getElementById("input_add_gudang").disabled = value
  document.getElementById("buttonAddListMerk").disabled = value
  document.getElementById("buttonAddListKategori").disabled = value
  document.getElementById("buttonAddListSubKategori").disabled = value

}

function cleanFormAdd () {
  document.getElementById("input_add_tanggal").valueAsDate = new Date()
  document.getElementById("input_add_tanggalpelaksanaan").value = ''
  document.getElementById("input_add_tanggalcutoff").value = ''
  document.getElementById("input_add_keterangan").value = ''
  document.getElementById("input_add_kodehdgrp").value = '-'
  document.getElementById("input_add_gudang").value = ''
  document.getElementById("input_add_kodemerk").value = '-'
  document.getElementById("input_add_kodesubkategori").value = '-'
  document.getElementById("input_add_kodekategori").value = '-'
  document.getElementById("addTableData").innerHTML = `<tr><td colspan=6 class="text-center">Belum ada data</td></tr>`

}

function refreshDataTable (nobukti) {

    let _token = $("#_token").val();
    listData = []
    $.ajax({
      url: "{!! url('perintahopnamespdetail') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti

      },
      success: function(res) {
        console.log(res)
        listData = res
        // console.log(res)
        // return
        // $('#formAddAdd').hide();
        if (!res.length) {
            alertify.success('Data Habis')
            // $("#form").modal('toggle')
            $('.mainpage').hide();
            $('#page1').show();
            return
        }
        // dataTableAdd = res

        let rowTable = ``
        listData.forEach((item, i) => {

          // <td>${item.TipeTrans == 'BBK' ? item.Lawan : item.Perkiraan}</td>
          // <td>${item.TipeTrans == 'BBK' ? item.NamaLawan : item.NamaPerkiraan}</td>
          // <td>${item.TipeTrans == 'BBK' ? item.Perkiraan : item.Lawan }</td>
          // <td>${item.TipeTrans == 'BBK' ?  item.NamaPerkiraan : item.NamaLawan }</td>

                rowTable += `
                  <tr>

                    <td>${item.KodeBrg}</td>
                    <td>${item.NamaBrg}</td>
                    <td class="text-center">${item.Satuan}</td>
                    <td class="text-right">0</td>
                    <td class='text-center'>
                      <button class="btn btn-danger btn-sm" type="button" onclick="buttonDeleteItem('${i}' )"><i class="bi bi-trash"></i></button>


                    </td>


                  </tr>

                `

                // <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
        });

        document.getElementById("addTableData").innerHTML = rowTable
        document.getElementById("input_add_nobukti").value = listData[0].NoBukti
        document.getElementById("input_add_nourut").value = listData[0].NoUrut
        document.getElementById("input_add_tanggal").value = formatDate(listData[0].Tanggal)

        if (listData[0].TglPelaksanaan && Number(new Date(listData[0].TglPelaksanaan).getFullYear()) > 1999 ) {
          document.getElementById("input_add_tanggalpelaksanaan").value = formatDate(listData[0].TglPelaksanaan)

        } else {
          document.getElementById("input_add_tanggalpelaksanaan").value = ''

        }

        if (listData[0].TglCutOFF && Number(new Date(listData[0].TglCutOFF).getFullYear()) > 1999 ) {
          document.getElementById("input_add_tanggalcutoff").value = formatDate(listData[0].TglCutOFF)

        } else {
          document.getElementById("input_add_tanggalcutoff").value = ''

        }

        // document.getElementById("input_add_tanggalpelaksanaan").value = listData[0].TglPelaksanaan ? formatDate(listData[0].TglPelaksanaan) : ''
        // document.getElementById("input_add_tanggalcutoff").value = listData[0].TglCutOFF ? formatDate(listData[0].TglCutOFF) : ''
        document.getElementById("input_add_keterangan").value = listData[0].Keterangan
        document.getElementById("input_add_gudang").value = listData[0].KodeGdg
        document.getElementById("input_add_kodehdgrp").value = listData[0].KodeHdGrp
        document.getElementById("input_add_kodekategori").value = listData[0].KodeSubGrp
        document.getElementById("input_add_kodesubkategori").value = listData[0].Jenis
        document.getElementById("input_add_kodemerk").value = listData[0].KodeMerk



      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
        resRefresh = 0;
      }

    })
}

function buttonKoreksi (nobukti) {
  tipeform = 'edit'
  listData = []

  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  lockForm(true)
  refreshDataTable(nobukti)


  if (listData.length) {

    if (listData[0].IsOtorisasi1 == 1) {
      alertify.warning("Nobukti sudah diotorisasi")
      return
    } else {
      $('.mainpage').hide()
      $('.showhideitem').hide()
      $('#page2').show()

    }
  }



}



function buttonAdd () {
  listData = []
  tipeform = 'add'
  cleanFormAdd()
  lockForm()
  setNewNoBukti()
  $('.mainpage').hide()
  $('.showhideitem').hide()
  $('#page2').show()
}

function buttonCloseForm () {
  $('.mainpage').hide();
  // $('#page2').hide();
  $('#page1').show();

}

function setNewNoBukti () {
  console.log('setNewNoBukti')
  let _token  = $("#_token").val()
  let kode  = 'POP'
  $.ajax({
    url: "{!! url('spnobukti') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kode
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    }})
}


function formatDate (date , pemisah = '-') {
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
function formatAngka (angkaString) {
  let tempAngka = angkaString.split('.')

  if (tempAngka[0][0] == '-') {
    let temp2=''

    let tempAngka1 = tempAngka[0].split('-')
    for (let i = 0; i < tempAngka1[1].length; i++) {
      if (i != 0 && i % 3 == 0) {
        temp2 = ',' + temp2
      }
      temp2 = tempAngka1[1][tempAngka1[1].length - i -1] + temp2

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
    temp1 = tempAngka[0][tempAngka[0].length - i -1] + temp1

  }
  temp1 += '.' + tempAngka[1]
  return temp1
}


</script>

@endsection
