@extends('newmasterTest')
@section('buttons')

@section('page-title', 'Berita Acara Opname')
@section('title', 'SML - Berita Acara Opname')

@endsection

@section('css')

<link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">

{{-- Port 1:1 ke UI SO -- @extends newmasterTest (bukan lagi gudang.newmaster), custom-tabs
     pill bar, po-toolbar, dan report-table.js column engine untuk kedua tabel (#tabel/
     #tabel2 -- BAP Belum+Sudah Otorisasi digabung jadi satu tabel dengan filter Status
     seperti perintahopname.blade.php), sama seperti transferbarang.blade.php. --}}
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

#tabel td:first-child,
#tabel2 td:first-child,
#tabel_oto td:first-child,
#addTable td:last-child {
  display: flex;
  gap: 4px;
  justify-content: center;
  align-items: center;
}

#tabel td:first-child .btn,
#tabel2 td:first-child .btn,
#tabel_oto td:first-child .btn,
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
#tabel_oto td:first-child .btn:hover,
#addTable td:last-child .btn:hover {
  filter: brightness(0.97);
  transform: translateY(-1px);
}

#tabel td:first-child .btn-success,
#tabel2 td:first-child .btn-success,
#tabel_oto td:first-child .btn-success,
#addTable td:last-child .btn-success {
  color: #16a34a; border-color: #cdebd7; background: #e7f7ed;
}

#tabel td:first-child .btn-warning,
#tabel2 td:first-child .btn-warning,
#tabel_oto td:first-child .btn-warning,
#addTable td:last-child .btn-warning {
  color: #b45309; border-color: #fbe3bd; background: #fef3e0;
}

#tabel td:first-child .btn-primary,
#tabel2 td:first-child .btn-primary,
#tabel_oto td:first-child .btn-primary,
#addTable td:last-child .btn-primary {
  color: #2563eb; border-color: #cfdcff; background: #e8edff;
}

#tabel td:first-child .btn-primary .bi-plus-lg {
  font-size: 20px;
  font-weight: bold;
}

#tabel td:first-child .btn-danger,
#tabel2 td:first-child .btn-danger,
#tabel_oto td:first-child .btn-danger,
#addTable td:last-child .btn-danger {
  color: #dc2626; border-color: #f7cfcf; background: #fdeaea;
}

{{-- Tabel statis (bukan #tabel/#tabel2/#tabel_oto) yang dulu pakai thead bg-primary
     text-white -- diganti abu-abu + teks gelap, sesuai UI baru yang sama di semua
     halaman. --}}
#addTable thead th,
#detailTable thead th,
#tabel_add_list_modal thead th,
#tabel_detailadd_list_modal thead th,
#tabel thead th,
#tabel2 thead th,
#tabel_oto thead th {
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
#tabel2 tbody tr:hover,
#tabel_oto tbody tr:hover {
  background-color: #f5f3ff;
}

.data-table th,
.data-table td {
  white-space: nowrap;
}

#tabel tbody .action-buttons-wrap,
#tabel2 tbody .action-buttons-wrap,
#tabel_oto tbody .action-buttons-wrap {
  opacity: 0;
  visibility: hidden;
  transform: translateX(-6px);
  transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
}
#tabel tbody tr:hover .action-buttons-wrap,
#tabel2 tbody tr:hover .action-buttons-wrap,
#tabel_oto tbody tr:hover .action-buttons-wrap,
#tabel tbody tr:focus-within .action-buttons-wrap,
#tabel2 tbody tr:focus-within .action-buttons-wrap,
#tabel_oto tbody tr:focus-within .action-buttons-wrap {
  opacity: 1;
  visibility: visible;
  transform: translateX(0);
}
</style>


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

  #tabel_filter {
      display: flex;
      align-items: flex-end;
      margin-top: 8px;
      margin-right: 10px;
      margin-bottom: -10px;
    }


  #tabel_filter label input {
      width: 150px;
      padding: 5px 10px;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-shadow: none;
      font-size: 0.65rem;
    }

  #tabel_filter label {
      font-weight: 600;
      font-size: 0.9rem;
      color: #333;
    }
  </style>
{{-- end tampilan search bar 1 --}}

{{-- tampilan search bar 2 --}}
  <style>
  #tabel2_filter {
      display: flex;
      align-items: flex-end;
      margin-top: 8px;
      margin-right: 10px;
      margin-bottom: -10px;
    }

  #tabel2_filter label input {
      width: 150px;
      padding: 5px 10px;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-shadow: none;
      font-size: 0.65rem;
    }

  #tabel2_filter label {
      font-weight: 600;
      font-size: 0.9rem;
      color: #333;
    }

  #tabel2_filter input:focus {
      border-color: #007bff;
      outline: none;
    }
  </style>
{{-- tampilan search bar 2 --}}

{{-- tampilan search bar 3 --}}
  <style>
  #tabel_oto_filter {
      display: flex;
      align-items: flex-end;
      margin-top: 8px;
      margin-right: 10px;
      margin-bottom: -10px;
    }

  #tabel_oto_filter label input {
      width: 150px;
      padding: 5px 10px; 
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-shadow: none; 
      font-size: 0.65rem; 
    }

  #tabel_oto_filter label {
      font-weight: 600; 
      font-size: 0.9rem; 
      color: #333;
    }

  #tabel_oto_filter input:focus {
      border-color: #007bff; 
      outline: none; 
    }
  </style>
{{-- end tampilan search bar 3 --}}
@endsection


@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id="page1" class="container-fluid mainpage">
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
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">Perintah Opname</a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">Berita Acara Opname</a>
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
                  <input type="search" id="baSearch1" class="po-search-inp" placeholder="Cari data">
                  <div class="po-len-wrap">
                    <label for="baLen1">Tampilkan</label>
                    <select id="baLen1" class="po-len-inp">
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

        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
          <div class="row">
            <div class="col-md-12">
              <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                <div class="po-toolbar">
                  <div class="po-filter-wrap">
                    <label>Periode</label>
                    <input type="date" onchange="baFilterChanged()" class="po-filter-inp" id="baTanggalAwal" value="{!! \Carbon\Carbon::createFromDate((int) $periode->tahun, (int) $periode->bulan, 1)->startOfMonth()->format('Y-m-d') !!}">
                    <span class="po-filter-sep">s/d</span>
                    <input type="date" onchange="baFilterChanged()" class="po-filter-inp" id="baTanggalAkhir" value="{!! \Carbon\Carbon::createFromDate((int) $periode->tahun, (int) $periode->bulan, 1)->endOfMonth()->format('Y-m-d') !!}">
                  </div>
                  <input type="search" id="baSearch2" class="po-search-inp" placeholder="Cari data">
                  <div class="po-len-wrap">
                    <label for="baLen2">Tampilkan</label>
                    <select id="baLen2" class="po-len-inp">
                      <option value="10">10</option>
                      <option value="25">25</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                      <option value="-1">Semua</option>
                    </select>
                  </div>
                  <button class="po-btn-filter" type="button" onclick="$('#modalFilterBA').modal('show')">
                    <i class="bi bi-funnel"></i> Filter
                  </button>
                </div>

                <div class="modal fade rt-filter" id="modalFilterBA">
                  <div class="modal-dialog modal-md">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">
                          <i class="bi bi-funnel"></i>
                          Filter Data
                          <span class="rt-active-badge" id="baFilterBadge">0 aktif</span>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterBA').modal('hide')"><span aria-hidden="true">&times;</span></button>
                      </div>
                      <div class="modal-body">
                        <div class="rt-section">
                          <div class="rt-group-label">Status</div>
                          <div class="rt-grid-2">
                            <div>
                              <label class="rt-field-label" for="baStatus">Otorisasi</label>
                              <select class="rt-native" id="baStatus">
                                <option value="" selected>Semua Status</option>
                                <option value="0">Belum Otorisasi</option>
                                <option value="1">Sudah Otorisasi</option>
                              </select>
                            </div>
                            <div>
                              <label class="rt-field-label" for="baStatusOpname">Status</label>
                              <select class="rt-native" id="baStatusOpname">
                                <option value="" selected>Semua Status</option>
                                <option value="Belum">Belum</option>
                                <option value="Sudah">Sudah</option>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="rt-reset-link" onclick="$('#baStatus').val(''); $('#baStatusOpname').val('');">Reset semua</button>
                        <div class="rt-footer-buttons">
                          <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal" onclick="$('#modalFilterBA').modal('hide')">Batal</button>
                          <button type="button" class="rt-btn rt-btn-primary" onclick="baFilterChanged(); $('#modalFilterBA').modal('hide');">Terapkan</button>
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
      <button type="button" class="btn btn-danger btn-lg " style="
          height: 30px;
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
            <label>No PR Opname</label>
          </div>
        </div>
        <div class="col-md-8">
          <div class="form-group">
          <input type="text" class="form-control" id="input_add_noperintah" placeholder="" disabled>
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
                <input id="input_add_gudang" type="text" class="form-control" disabled>


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


              </div>
            </div>
          </div>

        </div>
      </div>
      <div class="row" style="margin-top: -10px">
        <div class="col-md-6">
          <div class="row">
            <div class="col-md-2">
              <div class="form-group">
              <label>Keterangan</label>
            </div>
            </div>
            <div class="col-md-10">
              <div class="input-group form-group">
                <input id="input_add_keterangan" type="text" class="form-control" onblur="onChangeHeader()">


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
              <th style="padding: 4px 12px;" scope="col">Saldo Stock</th>
              <th style="padding: 4px 12px;" scope="col">Qty Opname</th>

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
  <button id="buttonSubmitKoreksi" type="button" class="btn btn-chip-biru" onclick="submitKoreksi()" class="btn btn-secondary" style="height: 30px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;">Simpan</button>
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
        <button type="button" class="btn btn-danger btn-lg " style="
          height: 30px;
          padding: 4px 12px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="buttonCloseForm()"  >CLOSE</button>
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
              <label>No PR Opname</label>
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group">
            <input type="text" class="form-control" id="input_detail_noperintah" placeholder="" disabled>
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
        <div class="row" style="margin-top: -10px">
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-2">
                <div class="form-group">
                <label>Keterangan</label>
              </div>
              </div>
              <div class="col-md-10">
                <div class="input-group form-group">
                  <input id="input_detail_keterangan" type="text" class="form-control" disabled>


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
                <th style="padding: 4px 12px;" scope="col">Saldo Stock</th>
                <th style="padding: 4px 12px;" scope="col">Qty Opname</th>

              </tr>
            </thead>

            <tbody id="detailTableData" class="" >
              <tr>
                  <td colspan=5 class="text-center">Belum ada data</td>
            </tr>
            </tbody>

          </table>
    </div>

    <div class="col-md-12 mt-2 text-right">
      <button id="buttonSubmitOtorisasi" type="button" class="btn btn-primary" onclick="submitOtorisasi()" class="btn btn-secondary" style="height: 30px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;" >Otorisasi</button>
  </div>


    </div>
  </div>

      </div>








    </div>










  </div>
</div>







<div class="modal fade" id="formAddItem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg"  role="document" style="min-width: 1400px">
    <div id="" class="modal-content ">

      <div id= "" class="">
      <div class="modal-header">

          <h5 class="modal-title" id="">Tambah Item</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div id="formBsGrid" class="">
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
                  <input type="hidden" class="form-control" id="input_modal_nourut" placeholder="" disabled>
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
                <input type="date" class="form-control text-center" id="input_modal_tanggal" placeholder="" >
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
                  <th style="padding: 4px 12px;" scope="col" class="text-center">v</th>
                  <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Qty</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_modal" class="text-left" >

                <tr >

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
          margin-top: -10px;
          padding: 4px 12px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" data-dismiss="modal" >Batal</button>
        <button type="button" class="btn btn-chip-biru" style="
          height: 30px;
          margin-top: -10px;
          padding: 4px 12px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="submitAdd()">Simpan</button>
      </div>
      </div>



      </div>




    </div>
  </div>

{{-- modal detail add --}}
  <div class="modal fade" id="formDetailAddItem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg"  role="document" style="min-width: 1400px">
    <div class="modal-content ">

      <div id= "" class="">
      <div class="modal-header">
          <h5 class="modal-title" id="">Detail Item</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="formBsGrid" class="">
      <div class="modal-body">

        <div class="container-fluid" >
          <div class="row">
            <div class="col-12">
              <h3></h3>
            </div>

          </div>
          <div class="row" style="margin-top:-30px;">
            <div class="col-md-3">
              <div class="row">

            <div class="col-md-4">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                  <input type="hidden" class="form-control" id="detail_modal_nourut" placeholder="" disabled>
                <input type="text" class="form-control" id="detail_modal_nobukti" placeholder="No Bukti" disabled>
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
                <input type="date" class="form-control text-left" id="input_modal_tanggal" placeholder="" >
              </div>
            </div>
          </div>
            </div> --}}
          </div>


          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto;  max-height: 400px">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_detailadd_list_modal" class="table table-bordered table-striped" style="overflow:auto; " >
              <thead class="text-center" style="position: sticky;
            top: 0;
            z-index: 1;">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Barang</th>

                </tr>
              </thead>

              <tbody id="tabel_data_detailadd_list_modal" class="text-left" >

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

      {{-- <div class="modal-footer">
        <button type="button" class="btn btn-secondary" style="
          height: 30px;
          margin-top: -10px;
          padding: 4px 12px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" data-dismiss="modal" >Batal</button>
        <button type="button" class="btn btn-primary" style="
          height: 30px;
          margin-top: -10px;
          padding: 4px 12px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="submitAdd()">Submit</button>
      </div> --}}
      </div>

      </div>

    </div>
  </div>
{{-- modal detail add --}}








@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script type="text/javascript">

let tipeform = ''
let listData = []
let listDataDet = []
let listDataAdd = []
let tempDataAdd = []

/* ============ Separator ribuan untuk input QTY (add_qnt${i}/koreksi_qnt${i}) ============
 * Field QTY sekarang type="text" (bukan type="number", yang tidak bisa menampilkan
 * titik ribuan) + oninput="formatAngkaInput(event)" supaya tampilannya "12.312" /
 * "123.123,50" gaya id-ID. Field disimpan sebagai STRING berformat di DOM, jadi setiap
 * tempat yang baca .val() untuk perhitungan/submit WAJIB lewat parseAngka() dulu --
 * sama persis dengan pola di koreksistock.blade.php/opnamebarang.blade.php. */
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
 * Port 1:1 dari pola opCart/opAktifkanTabel milik perintahopname.blade.php, untuk
 * dua tabel di halaman ini (#tabel = Perintah Opname outstanding, #tabel2 = BAP --
 * Belum+Sudah Otorisasi digabung jadi satu tabel, dengan kolom Status dan filter
 * Status di toolbar, sama seperti merge Belum/Sudah Otorisasi di perintahopname).
 * Tiap render*Rows() mem-force aktivasi urut-nya sendiri sebelum membaca
 * gcart_header, supaya tidak ada drift seperti bug yang ditemukan di
 * perintahopname (gcart_header nyangkut di urut lain saat halaman pertama kali
 * load). */
let baCart = { 1 : [], 2 : [] }
let baActiveUrut = 0
const BA_HREF = 'beritaacaraopname'
const BA_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date', 3 : 'bool' }
const BA_TIPE_KODE = { varchar : 0, float : 1, date : 2, bool : 3 }

function baPickCI (row, key) {
  if (!row) { return undefined; }
  if (row[key] !== undefined) { return row[key]; }
  let lower = key.toLowerCase();
  for (let k in row) {
    if (k.toLowerCase() === lower) { return row[k]; }
  }
  return undefined;
}

function baDefaultCart (urut) {
  if (urut === 2) {
    return [
      ['Nobukti',      'No. Bukti',      1, 'varchar', 0, 0],
      ['Tanggal',      'Tanggal',        1, 'date',    0, 0],
      ['status',       'Status',         1, 'varchar', 0, 0],
      ['NoPerintahOP', 'No Perintah OP', 1, 'varchar', 0, 0],
      ['NamaGdg',      'Gudang',         1, 'varchar', 0, 0],
      ['NamaSubGrp',   'SubGrp',         1, 'varchar', 0, 0],
      ['NAMAMERK',     'Merk',           1, 'varchar', 0, 0],
      ['NAMAHDGRP',    'HeadGrp',        1, 'varchar', 0, 0],
      ['IsOtorisasi1', 'Otorisasi',      1, 'bool',    0, 0],
      ['OtoUser1',     'OtoUser',        1, 'varchar', 0, 0],
      ['TglOto1',      'TglOto',         1, 'date',    0, 0],
    ]
  }
  return [
    ['NoBukti',      'No. Bukti',   1, 'varchar', 0, 0],
    ['Tanggal',      'Tanggal',     1, 'date',    0, 0],
    ['KodeGdg',      'Kode Gudang', 1, 'varchar', 0, 0],
    ['KodeHdGrp',    'Kode Hdgrp',  1, 'varchar', 0, 0],
    ['NAMAHDGRP',    'Nama Hdgrp',  1, 'varchar', 0, 0],
    ['KodeSubGrp',   'Kode Subgrp', 1, 'varchar', 0, 0],
    ['NamaSubGrp',   'Nama Subgrp', 1, 'varchar', 0, 0],
    ['KodeMerk',     'Kode Merk',   1, 'varchar', 0, 0],
    ['NAMAMERK',     'Merk',        1, 'varchar', 0, 0],
  ]
}

function baBuatCart (headers, values, isnumerics, isshowns, desimals) {
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
      BA_TIPE_NAMA[tipe] || 'varchar',
      0,
      isNaN(des) ? 0 : des,
    ])
  });
  return cart
}

function baAktifkanTabel (urut) {
  baActiveUrut = urut
  window.g_modeReport = urut
  window.gcart_header = baCart[urut]
}

function baOnChangeAktif () {
  if (baActiveUrut === 2) { reinitTabel2(); }
  else { reinitTabel(); }
}

window.g_href = BA_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function (href, mode) {
  let urut = mode === 2 ? 2 : 1
  let cart = baCart[urut] || []

  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[1])
    value.push(c[0])
    isnumber.push(BA_TIPE_KODE[c[3]] ?? 0)
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
      href     : BA_HREF,
      urut     : urut
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal menyimpan pengaturan kolom')
    }
  })
}

window.doSetHeader = function (mode, reset) {
  let urut = mode === 2 ? 2 : 1

  $.ajax({
    url   : "{!! url('getheadertable') !!}",
    type  : "post",
    async : false,
    data  : {
      _token : $("#_token").val(),
      href   : BA_HREF,
      urut   : urut,
      reset  : reset ? 1 : 0
    },
    success : function (res) {
      if (!reset && res && res.headertableheader && res.headertableheader.length) {
        baCart[urut] = baBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal || [])
      } else {
        baCart[urut] = baDefaultCart(urut)
        window.gcart_header = baCart[urut]
        window.doSimpanHeader(BA_HREF, urut)
      }
      window.gcart_header = baCart[urut]
    },
    error : function (err) {
      console.log(err)
      alertify.warning(reset ? 'Gagal mengembalikan kolom ke tampilan default' : 'Gagal memuat pengaturan kolom')
      baCart[urut] = baDefaultCart(urut)
      window.gcart_header = baCart[urut]
    }
  })
}

let baRtSudahInit = false
function baInitReportTableSekali () {
  if (baRtSudahInit || typeof ReportTable === 'undefined') { return }
  baRtSudahInit = true

  ReportTable.init({ table : '#tabel',  bar : '#rtBarTabel',  onChange : baOnChangeAktif })
  ReportTable.init({ table : '#tabel2', bar : '#rtBarTabel2', onChange : baOnChangeAktif })

  let baGuardUlangKlik = false;
  ['#tabel', '#tabel2'].forEach((sel) => {
    let thead = document.querySelector(sel + ' thead')
    if (!thead) { return }
    thead.addEventListener('click', function (e) {
      if (baGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }
      e.stopPropagation()
      e.preventDefault()
      baGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles: false, cancelable: true, view: window })
      Object.defineProperty(ulang, 'target', { value: interaktif, configurable: true })
      thead.dispatchEvent(ulang)
      baGuardUlangKlik = false
    }, true)
  });
}

function tulisTheadHeaderBA (tableSel, cols) {
  let thead = document.querySelector(tableSel + ' thead')
  if (!thead || !window.ReportTable) { return; }
  let headRowHtml = ReportTable.headHtml(cols)
    .replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>');
  thead.setAttribute('style', 'white-space:nowrap;');
  thead.innerHTML = headRowHtml;
}

function baValueCell (row, col) {
  let raw = baPickCI(row, col[0]);
  let type = col[3];

  {{-- Status -- badge titik+pill .sp-badge, sama seperti kolom Status di
       permintaanpemakaian.blade.php/perintahopname.blade.php. Backend Berita Acara
       Opname tidak punya kolom 'status' sendiri (tidak seperti Perintah Opname yang
       punya CASE Belum/Batal/Sudah) dan tidak ada menu closing BAP, jadi di sini
       Status diturunkan langsung dari IsOtorisasi1 -- cuma Sudah/Belum, tanpa Batal. --}}
  if (col[0] === 'status') {
    let sudah = Number(baPickCI(row, 'IsOtorisasi1')) === 1;
    return '<td><span class="sp-badge ' + (sudah ? 'is-active' : 'is-inactive') + '">' + (sudah ? 'Sudah' : 'Belum') + '</span></td>';
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
  {{-- Otorisasi (bool) -- ikon check/silang, sama seperti perintahopname.blade.php. --}}
  if (type === 'bool') {
    return Number(raw)
      ? '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>'
      : '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>';
  }
  return '<td>' + (raw !== undefined && raw !== null ? raw : '') + '</td>';
}

function baTabelActionsCell (row) {
  let nobukti = baPickCI(row, 'NoBukti');
  let html = '<td class="text-center" style="white-space:nowrap;"><div class="action-buttons-wrap">';
  html += '<button class="btn btn-warning btn-sm" title="Detail" type="button" onclick="buttonDetailAdd(\'' + nobukti + '\')"><i class="bi bi-info"></i></button>';
  html += '<button class="btn btn-primary btn-sm" title="Tambah" type="button" onclick="buttonAdd(\'' + nobukti + '\')"><i class="bi bi-plus-lg"></i></button>';
  html += '</div></td>';
  return html;
}

function baTabel2ActionsCell (row) {
  let nobukti = baPickCI(row, 'Nobukti');
  let sudah = Number(baPickCI(row, 'IsOtorisasi1')) === 1;
  let html = '<td class="text-center" style="white-space:nowrap;"><div class="action-buttons-wrap">';
  html += '<button class="btn btn-warning btn-sm" title="Detail" type="button" onclick="buttonDetail(\'' + nobukti + '\' , \'detail\')"><i class="bi bi-info"></i></button>';
  if (sudah) {
    html += '<button class="btn btn-danger btn-sm" title="Batal Otorisasi" type="Batal" onclick="buttonBatalOtorisasi(\'' + nobukti + '\')"><i class="bi bi-key"></i></button>';
    html += '<button class="btn btn-primary btn-sm" title="Print" type="button" onclick="submitPrint(\'' + nobukti + '\')"><i class="bi bi-printer"></i></button>';
  } else {
    html += '<button class="btn btn-success btn-sm" title="Edit" type="button" onclick="buttonKoreksi(\'' + nobukti + '\')"><i class="bi bi-pen"></i></button>';
    html += '<button class="btn btn-primary btn-sm" title="Otorisasi" type="button" onclick="submitOtorisasi(\'' + nobukti + '\' , \'otorisasi\')"><i class="bi bi-key"></i></button>';
  }
  html += '</div></td>';
  return html;
}

function baFilterRows (rows) {
  let oto = $('#baStatus').val()
  let statusOpname = $('#baStatusOpname').val()
  let awal = $('#baTanggalAwal').val()
  let akhir = $('#baTanggalAkhir').val()
  return (rows || []).filter(function (row) {
    {{-- Kolom Otorisasi dan Status sama-sama diturunkan dari IsOtorisasi1 (lihat
         baValueCell() -- BAP tidak punya field 'status' sendiri di backend), tapi tetap
         dua dropdown terpisah di UI (sama seperti Status Otorisasi + Status Opname di
         perintahopname.blade.php), jadi disaring independen. --}}
    if (oto !== '' && String(Number(baPickCI(row, 'IsOtorisasi1'))) !== oto) { return false }
    if (statusOpname !== '') {
      let sudah = Number(baPickCI(row, 'IsOtorisasi1')) === 1
      if ((statusOpname === 'Sudah') !== sudah) { return false }
    }
    if (awal || akhir) {
      let tgl = baPickCI(row, 'Tanggal')
      if (!tgl) { return false }
      let t = String(tgl).slice(0, 10)
      if (awal && t < awal) { return false }
      if (akhir && t > akhir) { return false }
    }
    return true
  })
}

function baFilterChanged () {
  reinitTabel2()
}

function renderTabelRows (rows) {
  if (baActiveUrut !== 1) { baAktifkanTabel(1); }
  let cols = (baCart[1].length ? baCart[1] : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  (rows || []).forEach(function (row) {
    html += '<tr>' + baTabelActionsCell(row);
    cols.forEach(function (col) { html += baValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel_data').innerHTML = html;
  tulisTheadHeaderBA('#tabel', cols);
}

function renderTabel2Rows (rows) {
  if (baActiveUrut !== 2) { baAktifkanTabel(2); }
  let cols = (baCart[2].length ? baCart[2] : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  (rows || []).forEach(function (row) {
    html += '<tr>' + baTabel2ActionsCell(row);
    cols.forEach(function (col) { html += baValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel2_data').innerHTML = html;
  tulisTheadHeaderBA('#tabel2', cols);
}

let lastTabelRows = []
let lastTabel2Rows = []
let baPanjangHalaman = { 1 : 10, 2 : 10 }

function baIkatSearch (urut) {
  let ids = { 1 : ['baSearch1', 'tabel'], 2 : ['baSearch2', 'tabel2'] }
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

function baIkatPanjangHalaman (urut) {
  let ids = { 1 : ['baLen1', 'tabel'], 2 : ['baLen2', 'tabel2'] }
  let sel = document.getElementById(ids[urut][0])
  let idTabel = ids[urut][1]
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(baPanjangHalaman[urut])

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    baPanjangHalaman[urut] = (n === -1 || n > 0) ? n : 10
    if ($.fn.DataTable.isDataTable('#' + idTabel)) {
      $('#' + idTabel).DataTable().page.len(baPanjangHalaman[urut]).draw()
    }
  })
}

const BA_DOM_STRING = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"

function reinitTabel () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().destroy(); }
    renderTabelRows(lastTabelRows);
    $('#tabel').DataTable({ dom: BA_DOM_STRING, lengthChange: false, pageLength: baPanjangHalaman[1], paging: true, ordering: false });
    baIkatSearch(1);
    baIkatPanjangHalaman(1);
  } catch (e) {
    console.error('reinitTabel failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

function reinitTabel2 () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel2')) { $('#tabel2').DataTable().destroy(); }
    renderTabel2Rows(baFilterRows(lastTabel2Rows));
    $('#tabel2').DataTable({ dom: BA_DOM_STRING, lengthChange: false, pageLength: baPanjangHalaman[2], paging: true, ordering: false });
    baIkatSearch(2);
    baIkatPanjangHalaman(2);
  } catch (e) {
    console.error('reinitTabel2 failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

$(document).ready(function(){
      baAktifkanTabel(1);
      window.doSetHeader(1, false);
      lastTabelRows = @json($tempOutstanding);
      reinitTabel();

      baAktifkanTabel(2);
      window.doSetHeader(2, false);
      lastTabel2Rows = [].concat(@json($tempPenerimaan), @json($tempPenerimaan1));
      reinitTabel2();

      baAktifkanTabel(1);

      baInitReportTableSekali();

      {{-- Tanpa ini baActiveUrut tetap nyangkut di 1 selamanya setelah load pertama --
           baOnChangeAktif() (dipanggil tombol "Reset kolom"/geser-sembunyikan kolom
           bawaan ReportTable) baca baActiveUrut buat milih reinitTabel() vs
           reinitTabel2(), jadi tabel Berita Acara Opname (urut 2) tidak pernah
           ke-update saat tab-nya lagi aktif. Port 1:1 dari pola yang sama di
           gudangpenyerahansample.blade.php. --}}
      $('#nav-home-tab').on('shown.bs.tab', function () {
        baAktifkanTabel(1);
        if (typeof ReportTable !== 'undefined') { ReportTable.refresh(); }
      });
      $('#nav-profile-tab').on('shown.bs.tab', function () {
        baAktifkanTabel(2);
        if (typeof ReportTable !== 'undefined') { ReportTable.refresh(); }
      });
});


function loadAll () {

  console.log('loadall')
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('beritaacaraopnameloadall') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      console.log(res)
      lastTabelRows = res.tempOutstanding
      lastTabel2Rows = [].concat(res.tempPenerimaan, res.tempPenerimaan1)
      reinitTabel()
      reinitTabel2()
    }})

}

function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: "{!! url('beritaacaraopnamedetailCetak') !!}",
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
                  <div class="pb-1" style="width: 100%">Tanggal: `+tanggalOnly+`</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">No Bukti : `+dataPrint[0].Nobukti+`</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
              </div>


              <div style="width: 38%">
                <div style="display: flex; width: 100%">
                  <h2 class="m-0 pb-2">BERITA ACARA STOK OPNAME</h2>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Perintah OP</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+dataPrint[0].nopropname+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Gudang</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+dataPrint[0].KodeGdg+` - `+dataPrint[0].NamaGDG+`</div>
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
                    <td rowspan="2" class="text-center" style="width: 2%">No.</td>
                    <td rowspan="2" class="text-center" style="width: 15%">KODE</td>
                    <td rowspan="2" class="text-center" style="width: 30%">NAMA BARANG</td>
                    <td rowspan="2" class="text-center" style="width: 10%">PART NUMBER</td>
                    <td rowspan="2" class="text-center" style="width: 10%">MERK</td>
                    <td rowspan="2" class="text-center" style="width: 10%">SAT</td>
                    <td colspan="2" class="text-center" style="width: 10%">SALDO FISIK</td>
                    <td colspan="2" class="text-center" style="width: 10%">SALDO SYSTEM</td>
                    <td colspan="2" class="text-center" style="width: 10%">SELISIH</td>
                  </tr>
                  <tr>
                    <td class="text-center">QTY</td>
                    <td class="text-center">RP</td>

                    <td class="text-center">QTY</td>
                    <td class="text-center">RP</td>

                    <td class="text-center">QTY</td>
                    <td class="text-center">RP</td>
                  </tr>
                </thead> `;

    let z = 0
    let tempPrintStr = ``
    // buat hitung grandtotal
    let grandTotalFisik = 0;
    let grandTotalFisik2 = 0;
    let grandTotalSistem = 0;
    let grandTotalSistem2 = 0;
    let grandTotalSelisih = 0;
    let grandTotalSelisih2 = 0;

    dataPrint.forEach(item => {

      if (item.QntOpname) {
        grandTotalFisik += Number(item.QntOpname) || 0;
      }

      if (item.SALDOOPNAME) {
        grandTotalFisik2 += Number(item.SALDOOPNAME) || 0;
      }

      if (item.SaldoComp) {
        grandTotalSistem += Number(item.SaldoComp) || 0;
      }

      if (item.TOTALSALDO) {
        grandTotalSistem2 += Number(item.TOTALSALDO) || 0;
      }

      if (item.QNTSELISIH) {
        grandTotalSelisih += Number(item.QNTSELISIH) || 0;
      }

      if (item.SALDOSELISIH) {
        grandTotalSelisih2 += Number(item.SALDOSELISIH) || 0;
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
               style="width: 15%;  ">${itemSub.kodebrg}</td>
         <td class="text-align: left"
               style="width: 30%;">${itemSub.namaBrg}</td>
         <td class="text-align: left"
               style="width: 10%;">${itemSub.PartNumber}</td>
         <td class="text-align: left"
               style="width: 10%;">${itemSub.NAMAMERK}</td>
         <td class="text-align: text-center"
               style="width: 10%;">${itemSub.SAT1}</td>
         <td class="text-align: text-right"
               style="width: 10%;">${itemSub.QntOpname}</td>
         <td class="text-align: text-right"
               style="width: 10%;">${itemSub.SALDOOPNAME}</td>
         <td class="text-align: text-right"
               style="width: 10%;">${itemSub.SaldoComp ? parseFloat(itemSub.SaldoComp).toFixed(2) : ''}</td>
         <td class="text-align: text-right"
               style="width: 10%;">${itemSub.TOTALSALDO}</td>
         <td class="text-align: text-right"
               style="width: 10%;">${itemSub.QNTSELISIH}</td>
         <td class="text-align: text-right"
               style="width: 10%;">${itemSub.SALDOSELISIH}</td>
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

            <div style="width:5%; text-align:right;">
              ${grandTotalFisik.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              })}
            </div>

            <div style="width:5%; text-align:right;">
              ${grandTotalFisik2.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              })}
            </div>

            <div style="width:5%; text-align:right;">
              ${grandTotalSistem.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              })}
            </div>

            <div style="width:5%; text-align:right;">
              ${grandTotalSistem2.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              })}
            </div>

            <div style="width:5%; text-align:right;">
              ${grandTotalSelisih.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              })}
            </div>

            <div style="width:5%; text-align:right;">
              ${grandTotalSelisih2.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              })}
            </div>

          </div>

         </div>


           <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: 20px; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 20%">Kabag IT</td>
               <td class="no-border text-center" style="width: 20%">Direksi</td>
               <td class="no-border text-center" style="width: 20%">Supervisor Stok</td>
               <td class="no-border text-center" style="width: 20%">Kepala Gudang</td>
               <td class="no-border text-center" style="width: 20%">Admin Gudang</td>
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

function buttonAdd (nobukti) {
    tempDataAdd = []
    let _token = $("#_token").val();


    $.ajax({
      url: "{!! url('beritaacaraopnamelistadd') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti
      },
      success: function(res) {
        console.log("RES !")
        console.log(res)
        // return
        // return
        listDataAdd  = res
        let rowTable = `
        `
        res.forEach((item, i) => {
          rowTable += `
          <tr>
          <td class="text-center"><input class="" type="checkbox" value="" id="add_checkbox${i}" onchange="onchangeChecklist(${i},this.id)"></td>

          <td>${item.KODEBRG}</td>
          <td>${item.NAMABRG}</td>
          <td class="text-center"><input id="add_qnt${i}" class="text-right" type="text" inputmode="decimal" oninput="formatAngkaInput(event)" value="${formatAngkaDisplay(0)}"></td>

          </tr>`
        });

        document.getElementById("input_modal_tanggal").valueAsDate = new Date()

        document.getElementById("tabel_data_add_list_modal").innerHTML = rowTable
        setNewNoBukti()

      $("#formAddItem").modal('toggle');



      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })

}

function buttonDetailAdd (nobukti) {
  console.log('buttonDetailAdd', nobukti);
  tempDataAdd = [];
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('beritaacaraopnamelistdetailadd') !!}",
    type: "POST",
    async: false,
    data: { _token, nobukti },
    success: function (res) {
      console.log("Response:", res);

      document.getElementById("detail_modal_nobukti").value = nobukti;

      let rowTable = "";

      if (res && res.length > 0) {
        res.forEach((item, i) => {
          rowTable += `
            <tr>
              <td>${item.KODEBRG || ''}</td>
              <td>${item.NAMABRG || ''}</td>
            </tr>`;
        });
      } else {

        rowTable = `
          <tr>
            <td colspan="2" class="text-center text-muted">Tidak ada detail barang.</td>
          </tr>`;
      }

      document.getElementById("tabel_data_detailadd_list_modal").innerHTML = rowTable;

      $("#formDetailAddItem").modal('toggle');
    },
    error: function (err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}


function buttonDetail (nobukti, tipe = "detail") {
  let _token = $("#_token").val();
  listData = []
  if (tipe =="otorisasi") {
    let akses = $("#akses_isotorisasi1").val();
    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }
  }

  $.ajax({
    url: "{!! url('beritaacaraopnamedetailkoreksi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {
      console.log(res)
      listDataDet = res
      // return
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
      listDataDet.forEach((item, i) => {

        // <td>${item.TipeTrans == 'BBK' ? item.Lawan : item.Perkiraan}</td>
        // <td>${item.TipeTrans == 'BBK' ? item.NamaLawan : item.NamaPerkiraan}</td>
        // <td>${item.TipeTrans == 'BBK' ? item.Perkiraan : item.Lawan }</td>
        // <td>${item.TipeTrans == 'BBK' ?  item.NamaPerkiraan : item.NamaLawan }</td>

        rowTable += `
          <tr>

            <td>${item.kodebrg}</td>
            <td>${item.namaBrg}</td>
            <td class="text-center">${item.Satuan}</td>
            <td class="text-right">${item.SaldoComp ? formatAngka(parseFloat(item.SaldoComp).toFixed(2)) : '0.00'}</td>
            <td class="text-right">${formatAngka(parseFloat(item.QntOpname).toFixed(2))}</td></td>

          </tr>

        `

        // <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
      });

      document.getElementById("detailTableData").innerHTML = rowTable
      document.getElementById("input_detail_nobukti").value = listDataDet[0].Nobukti
      document.getElementById("input_detail_nourut").value = listDataDet[0].Nourut
      document.getElementById("input_detail_tanggal").value = formatDate(listDataDet[0].Tanggal)
      document.getElementById("input_detail_keterangan").value = listDataDet[0].note
      document.getElementById("input_detail_gudang").value = listDataDet[0].KodeGdg
      document.getElementById("input_detail_kodehdgrp").value = listDataDet[0].KodeHdGrp
      document.getElementById("input_detail_kodekategori").value = listDataDet[0].KodeSubGrp
      document.getElementById("input_detail_kodemerk").value = listDataDet[0].KodeMerk
      document.getElementById("input_detail_noperintah").value = listDataDet[0].NoPerintahOP

      if (tipe == 'otorisasi') {
        $("#buttonSubmitOtorisasi").show()

      } else {
        $("#buttonSubmitOtorisasi").hide()

      }
      $('.mainpage').hide()
      $('#page3').show()


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      resRefresh = 0;
    }

  })
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
  // if (!Number(akses)) {
  //   alertify.warning('No access')
  //   return
  // }

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
          url: "{!! url('beritaacaraopnamespbatalotorisasi') !!}",
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
    url: "{!! url('beritaacaraopnamespotorisasi') !!}",
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
//     url: "{!! url('beritaacaraopnamespotorisasi') !!}",
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

function submitAdd () {
  let checkDate = new Date($("#input_add_tanggal").val())
  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value


  let tempData = []



  // console.log(listDataAdd)
  let checkQnt = 0
  listDataAdd.forEach((item, i) => {
    console.log(i)
    if (document.getElementById(`add_checkbox${i}`).checked) {
      console.log($(`#add_qnt${i}`).val())
      let x = parseAngka($(`#add_qnt${i}`).val())
      if (Number(x)  < 0) {
              checkQnt = 1
            }
      tempData.push({
        ...item,
        inputQntTerima : x
      })
      console.log(tempData , x)
    }
  });
  console.log(tempData)
  if (checkQnt) {
    alertify.warning("Qnt < 0")
    return
  }
  if (!tempData.length) {
    alertify.warning("Tidak ada data dipilih")
    return
  }
  let _token  = $("#_token").val()
  let nob = tempData[0].NOBUKTI
  let tanggal = $("#input_modal_tanggal").val();
  let nobukti = $("#input_modal_nobukti").val();
  let nourut = $("#input_modal_nourut").val();
  let jmlrecord = 0
  $.ajax({
      url: "{!! url('beritaacaraopnamespadd') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        tempData : tempData ,
        nobukti,
        nourut,
        tanggal,
        jmlrecord,
        nob
      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          // $("#form").modal('toggle')
          alertify.success('BAP telah ditambah');

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

}

function onChangeHeader () {

  let _token = $("#_token").val();
  let nobukti = $("#input_add_nobukti").val();
  let keterangan = $("#input_add_keterangan").val();
  console.log(keterangan, nobukti)
  $.ajax({
    url: "{!! url('beritaacaraopnamespupdateheader') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti,
      keterangan

    },
    success: function(res) {
      console.log(res)
      alertify.success('Berhasil update keterangan')
      // loadAll()




    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}


function submitKoreksi () {
  console.log('submitedit')
  let tempData = []



  // console.log(listDataAdd)
  let checkQnt = 0
  listData.forEach((item, i) => {
    console.log(i)
    // if (document.getElementById(`add_checkbox${i}`).checked) {
      console.log($(`#koreksi_qnt${i}`).val())
      let x = parseAngka($(`#koreksi_qnt${i}`).val())
      if (Number(x)  < 0) {
              checkQnt = 1
            }
      tempData.push({
        ...item,
        qntedit : x
      })
      console.log(tempData , x)
    })
  console.log(tempData)
  if (checkQnt) {
    alertify.warning("Qnt < 0")
    return
  }
  let _token  = $("#_token").val()
  let tanggal = $("#input_add_tanggal").val();
  let nobukti = $("#input_add_nobukti").val();
  let nourut = $("#input_add_nourut").val();
  let jmlrecord = 1

  console.log({
    _token,
    tempData : tempData ,
    nobukti,
    nourut,
    tanggal,
    jmlrecord,
  })
  $.ajax({
      url: "{!! url('beritaacaraopnamespkoreksi') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        tempData : tempData ,
        nobukti,
        nourut,
        tanggal,
        jmlrecord,
      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          // $("#form").modal('toggle')
          alertify.success('BAP telah diedit');

          tipeform = 'edit'
          loadAll()
          // $("#formAddItem").modal('toggle');
          refreshDataTable(nobukti)

        }

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })





}



function buttonDeleteItem (index) {


    let barangDelete = listData[index]



    console.log(barangDelete)

    // return


    alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item '+ barangDelete.kodebrg +' ?',
        function() {


            let nobukti = $("#input_add_nobukti").val();
            let urut = barangDelete.Urut


            let _token  = $("#_token").val()

            $.ajax({
                url: "{!! url('beritaacaraopnamespdelete') !!}",
                type: "post",
                async: false,
                data: {
                  _token,
                  nobukti,
                  urut
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


function buttonAddPickGudang (index, kode,nama) {


  document.getElementById("input_add_gudang").value = kode
          $("#form").modal('toggle')

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
      <tr class="text-center">
      <td >-</td>
      <td>-</td>
      <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickMerk('-','-' , '-'  )" type="button" ><i class="bi bi-plus"></i></button></td>

      </tr>
      `
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.KODEMERK}</td>
        <td>${item.NAMAMERK}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickMerk(${i},'${item.KODEMERK}' , '${item.NAMAMERK}'  )" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      $('#tabel_add_list_merk').DataTable().destroy();
      document.getElementById("tabel_data_add_list_merk").innerHTML = rowTable

      $("#tabel_add_list_merk").DataTable({
        "lengthChange": false,
          "paging": false ,
          // 'order': [[1, 'asc']],
          // "searching" : false,
          "columnDefs": [
        // {"targets" :[0] , 'orderable' : false}
       // {  "className": "text-center", "targets": [4] },
     ]
    });

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
      <tr class="text-center">
      <td >-</td>
      <td>-</td>
      <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickSubKategori('-','-' , '-'  )" type="button" ><i class="bi bi-plus"></i></button></td>

      </tr>
      `
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.Urut}</td>
        <td>${item.Keterangan}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickSubKategori(${i},'${item.Urut}' , '${item.Keterangan}'  )" type="button" ><i class="bi bi-plus"></i></button></td>
        </tr>`
      });








      document.getElementById("tabel_data_add_list_gudang").innerHTML = rowTable
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
      <tr class="text-center">
      <td >-</td>
      <td>-</td>
      <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickKategori('-','-' , '-'  )" type="button" ><i class="bi bi-plus"></i></button></td>

      </tr>
      `
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.KodeSubGrp}</td>
        <td>${item.NamaSubGrp}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickKategori(${i},'${item.KodeSubGrp}' , '${item.NamaSubGrp}'  )" type="button" ><i class="bi bi-plus"></i></button></td>
        </tr>`
      });








      document.getElementById("tabel_data_add_list_gudang").innerHTML = rowTable
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
      <tr class="text-center">
      <td >-</td>
      <td>-</td>
      <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickHeadGroup('-','-' , '-'  )" type="button" ><i class="bi bi-plus"></i></button></td>

      </tr>
      `
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.KodeHDGrp}</td>
        <td>${item.NamaHDGRP}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickHeadGroup(${i},'${item.KodeHDGrp}' , '${item.NamaHDGRP}'  )" type="button" ><i class="bi bi-plus"></i></button></td>
        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }

      document.getElementById("tabel_data_add_list_gudang").innerHTML = rowTable
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

function buttonAddListGudang () {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('perintahopnamelistgudang') !!}",
    type: "get",
    async: false,
    data: {
      _token,
    },
    success: function(res) {
      console.log(res)
      // listLawan  = res
      let rowTable = `
      <tr class="text-center">
      <td >-</td>
      <td>-</td>
      <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickGudang('-','-' , '-'  )" type="button" ><i class="bi bi-plus"></i></button></td>

      </tr>
      `
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.KodeGdg}</td>
        <td>${item.Nama}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickGudang(${i},'${item.KodeGdg}' , '${item.Nama}'  )" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_gudang").innerHTML = rowTable

      document.getElementById("modalAddListGudangTitle").innerHTML= 'Gudang'
      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListGudang').show();
        $("#form").modal('toggle')
      } else {
        alertify.warning("Gudang tidak ditemukkan")
      }


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
  document.getElementById("buttonAddListGudang").disabled = value
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
  document.getElementById("input_add_gudang").value = '-'
  document.getElementById("input_add_kodemerk").value = '-'
  document.getElementById("input_add_kodesubkategori").value = '-'
  document.getElementById("input_add_kodekategori").value = '-'
  document.getElementById("addTableData").innerHTML = `<tr><td colspan=6 class="text-center">Belum ada data</td></tr>`

}

function refreshDataTable (nobukti) {

    let _token = $("#_token").val();
    listData = []
    $.ajax({
      url: "{!! url('beritaacaraopnamedetailkoreksi') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti

      },
      success: function(res) {
        console.log(res)
        listData = res
        // return
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

                    <td>${item.kodebrg}</td>
                    <td>${item.namaBrg}</td>
                    <td class="text-center">${item.Satuan}</td>
                    <td class="text-right">${item.SaldoComp ? parseFloat(item.SaldoComp).toFixed(2) : '0.00'}</td>
                    <td class="text-center"><input id="koreksi_qnt${i}" class="text-right" type="text" inputmode="decimal" oninput="formatAngkaInput(event)" value="${formatAngkaDisplay(item.QntOpname)}"></td>
                    <td class='text-center'>
                      <button class="btn btn-danger btn-sm" type="button" onclick="buttonDeleteItem('${i}' )"><i class="bi bi-trash"></i></button>


                    </td>


                  </tr>

                `

                // <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
        });

        document.getElementById("addTableData").innerHTML = rowTable
        document.getElementById("input_add_nobukti").value = listData[0].Nobukti
        document.getElementById("input_add_nourut").value = listData[0].Nourut
        document.getElementById("input_add_tanggal").value = formatDate(listData[0].Tanggal)
        document.getElementById("input_add_keterangan").value = listData[0].note
        document.getElementById("input_add_gudang").value = listData[0].KodeGdg
        document.getElementById("input_add_kodehdgrp").value = listData[0].KodeHdGrp
        document.getElementById("input_add_kodekategori").value = listData[0].KodeSubGrp
        document.getElementById("input_add_kodemerk").value = listData[0].KodeMerk
        document.getElementById("input_add_noperintah").value = listData[0].NoPerintahOP



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

  // lockForm(true)
  refreshDataTable(nobukti)
  // return


  if (listData.length) {
    console.log(listData[0])
    console.log(listData[0].IsOtorisasi1)
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


function buttonCloseForm () {
  $('.mainpage').hide();
  // $('#page2').hide();
  $('#page1').show();

}

function setNewNoBukti () {
  console.log('setNewNoBukti')
  let _token  = $("#_token").val()
  let kode  = 'BAP'
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
      document.getElementById("input_modal_nobukti").value = res[0].Nobukti
      document.getElementById("input_modal_nourut").value = res[0].Nourut

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
