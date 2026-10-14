@extends('newmasterTest')
@section('buttons')

@section('page-title', 'Koreksi Stock')
@section('title', 'SML - Koreksi Stock')

@endsection

@section('css')

<link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">

{{-- Modal pemilih Gudang/Barang disamakan persis dengan modal pemilih menu Kas -- lihat
     public/css/picker-kas.css, sama seperti so.blade.php/perintahopname.blade.php. --}}
<link rel="stylesheet" href="{!! URL::asset('css/picker-kas.css') !!}?v={{ @filemtime(base_path('public/css/picker-kas.css')) ?: '1' }}">

{{-- Port 1:1 ke UI SO -- @extends newmasterTest (bukan lagi gudang.newmaster), po-toolbar
     dan report-table.js column engine untuk satu tabel (#tabel -- KRS Belum+Sudah
     Otorisasi digabung jadi satu tabel dengan filter Status, sama seperti merge di
     perintahopname.blade.php/beritaacaraopname.blade.php/opnamebarang.blade.php). --}}
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

#tabel td:first-child,
#addTable td:last-child {
  display: flex;
  gap: 4px;
  justify-content: center;
  align-items: center;
}

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

#addTable td:last-child .btn:hover {
  filter: brightness(0.97);
  transform: translateY(-1px);
}

#addTable td:last-child .btn-success {
  color: #16a34a; border-color: #cdebd7; background: #e7f7ed;
}

#addTable td:last-child .btn-primary {
  color: #2563eb; border-color: #cfdcff; background: #e8edff;
}

#addTable td:last-child .btn-danger {
  color: #dc2626; border-color: #f7cfcf; background: #fdeaea;
}

#addTable td:last-child .btn-warning {
  color: #b45309; border-color: #fbe3bd; background: #fef3e0;
}

{{-- Badge kolom Status (IsOtorisasi1) di tabel KRS gabungan -- hijau untuk Sudah
     Otorisasi, amber untuk Belum Otorisasi, warna sama dengan palet btn-success/
     btn-warning di atas (sama seperti gudangpermintaansample.blade.php). --}}
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

{{-- Tabel statis (bukan #tabel) yang dulu pakai thead bg-primary text-white -- diganti
     abu-abu + teks gelap, sesuai UI baru yang sama di semua halaman. --}}
#addTable thead th,
#detailTable thead th,
#tabel_add_list_merk thead th,
#tabel_add_list_barangall thead th,
#tabel thead th {
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

{{-- "+"-button picker Gudang/Barang dikonversi jadi click-anywhere-on-row, sama
     seperti konvensi so.blade.php/perintahopname.blade.php. --}}
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


{{-- Search bar hardcoded modal Barang All dihapus -- sekarang pakai satu-satunya
     search box native DataTables (hasil pickerKasInit), lihat searchBarangAll()
     di @section('js') yang sekarang diikat ke #tabel_add_list_barangall_filter input. --}}

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
@endsection


@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id="page1" class="container-fluid mainpage po-list-page">
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
  {{-- Tab Belum/Sudah Otorisasi digabung jadi satu tabel, dengan filter Status di
       toolbar (client-side), sama seperti merge di perintahopname.blade.php/
       beritaacaraopname.blade.php/opnamebarang.blade.php. --}}
  <div class="card">
    <div class="card-body" style="padding:0;">
      <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
        <div class="po-toolbar">
          <div class="po-filter-wrap">
            <label>Periode</label>
            <input type="date" onchange="ksFilterChanged()" class="po-filter-inp" id="ksTanggalAwal" value="{!! \Carbon\Carbon::createFromDate((int) $periode->tahun, (int) $periode->bulan, 1)->startOfMonth()->format('Y-m-d') !!}">
            <span class="po-filter-sep">s/d</span>
            <input type="date" onchange="ksFilterChanged()" class="po-filter-inp" id="ksTanggalAkhir" value="{!! \Carbon\Carbon::createFromDate((int) $periode->tahun, (int) $periode->bulan, 1)->endOfMonth()->format('Y-m-d') !!}">
          </div>
          <input type="search" id="ksSearch1" class="po-search-inp" placeholder="Cari data">
          <div class="po-len-wrap">
            <label for="ksLen1">Tampilkan</label>
            <select id="ksLen1" class="po-len-inp">
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
              <option value="-1">Semua</option>
            </select>
          </div>
          <button class="po-btn-filter" type="button" onclick="$('#modalFilterKS').modal('show')">
            <i class="bi bi-funnel"></i> Filter
          </button>
          <div class="po-toolbar-act">
            <button class="btn btn-chip-biru" type="button" onclick="buttonAdd()">Tambah</button>
          </div>
        </div>

        <div class="modal fade rt-filter" id="modalFilterKS">
          <div class="modal-dialog modal-md">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-funnel"></i>Filter Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterKS').modal('hide')"><span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <div class="rt-section">
                  <div class="rt-group-label">Status</div>
                  <div>
                    <label class="rt-field-label" for="ksStatus">Status Otorisasi</label>
                    <select class="rt-native" id="ksStatus">
                      <option value="" selected>Semua Status</option>
                      <option value="0">Belum Otorisasi</option>
                      <option value="1">Sudah Otorisasi</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="rt-reset-link" onclick="$('#ksStatus').val('')">Reset semua</button>
                <div class="rt-footer-buttons">
                  <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal" onclick="$('#modalFilterKS').modal('hide')">Batal</button>
                  <button type="button" class="rt-btn rt-btn-primary" onclick="ksFilterChanged(); $('#modalFilterKS').modal('hide');">Terapkan</button>
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
      <button type="button" class="btn btn-danger btn-lg" style="height: 30px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()">Close</button>
    </div>
  </div>

  <div id= "" class="">

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

      </div>

      <div class="row" style="margin-top: -10px">
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
                <input id="input_add_keterangan" type="text" class="form-control" onchange="onChangeHeader()">

                <!-- <button id="buttonAddListValas" type="button" onclick="buttonAddListValas()" class="btn btn-primary" disabled >+</button> -->

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
              <th style="padding: 4px 12px;" scope="col">Sat</th>
              <th style="padding: 4px 12px;" scope="col">Qty Debet</th>
              <th style="padding: 4px 12px;" scope="col">Qty Kredit</th>
              <th style="padding: 4px 12px;" scope="col">Harga</th>
              <th style="padding: 4px 12px;" scope="col">Total</th>
              <th style="padding: 4px 12px;" scope="col">Actions</th>

            </tr>
          </thead>


          <tbody id="addTableData" class="" >
            <tr>

                <td colspan=8 class="text-center">Belum ada data</td>

          </tr>

          </tbody>

        </table>
  </div>

  <div class="col-md-12 mt-2 text-right">
  <button id="buttonAddItem" type="button" class="btn btn-chip-biru" onclick="buttonAddItem()" class="btn btn-secondary" style="height: 30px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;">Tambah</button>
</div>

<div id="formAddAdd" class="container-fluid showhideitem">
  <!-- <div class="line"></div> -->
  <!-- <div class="row"> -->

  <div class="col-12">


  <hr/>
  <div class="row">
    <div class="col-md-12">
      <h4 id="labelAddItem">Add Item</h4>
      <h4 id="labelEditItem">Edit Item</h4>
    </div>
  </div>

<div class="row" style="margin-top: -10px">
<div class="col-md-3">


  <div class="row">

    <div class="col-md-4">
        <div class="form-group">
        <label>Kode Brg</label>
      </div>
      </div>
      <!-- <div class="col-4 text-right">

        </div> -->
      <div class="col-md-8">
        <div class="input-group form-group">
          <input id="AddAddKodeBrg" type="text" class="form-control" onkeypress="onKeyPressBarang(event)">
          <button id="buttonAddListBarang" type="button" onclick="buttonAddListBarang()" class="btn btn-chip-biru"><i class='bi bi-search'></i></button>

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
        <label>Nama Brg</label>
      </div>
      </div>
      <!-- <div class="col-4 text-right">

        </div> -->
      <div class="col-md-8">
        <div class="input-group form-group">
          <input id="AddAddNamaBrg" type="text" class="form-control" disabled>

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
          <label>Qty Db</label>
        </div>
        </div>
        <!-- <div class="col-4 text-right">

          </div> -->
        <div class="col-md-8" >
          <div class="row" >
            <div class="col-md-8" style="padding-right: 0px">
              <div class="input-group form-group">
                <input id="AddAddQntDb" type="text" inputmode="decimal" class="form-control text-right" oninput="formatAngkaInput(event)" onblur="onchangeqntdb()">
                <!-- <input id="AddAddQntDbSat" type="text" class="form-control" style="width: 5px"  disabled> -->

              </div>
            </div>
            <div class="col-md-4" style="padding-left: 0px">
              <div class="input-group form-group">
                <!-- <input id="AddAddQntDb" type="number" class="form-control text-right" > -->
                <input id="AddAddQntDbSat" type="text" class="form-control text-center" style="width: 5px"  disabled>

              </div>
            </div>
          </div>

        </div>

    </div>

  </div>


<div class="col-md-3">
  <div class="row">

    <div class="col-md-4">
        <div class="form-group">
        <label>Qty Cr</label>
      </div>
      </div>
      <!-- <div class="col-4 text-right">

        </div> -->
      <div class="col-md-8" >
        <div class="row" >
          <div class="col-md-8" style="padding-right: 0px">
            <div class="input-group form-group">
              <input id="AddAddQntCr" type="text" inputmode="decimal" class="form-control text-right" oninput="formatAngkaInput(event)" onblur="onchangeqntcr()">
              <!-- <input id="AddAddQntCrSat" type="text" class="form-control" style="width: 5px"  disabled> -->

            </div>
          </div>
          <div class="col-md-4" style="padding-left: 0px">
            <div class="input-group form-group">
              <!-- <input id="AddAddQntCr" type="number" class="form-control text-right" > -->
              <input id="AddAddQntCrSat" type="text" class="form-control text-center" style="width: 5px"  disabled>

            </div>
          </div>
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
          <label>Harga</label>
        </div>
        </div>
        <!-- <div class="col-4 text-right">

          </div> -->
        <div class="col-md-8" >

          <div class="input-group form-group">
            <input id="AddAddHarga" type="text" inputmode="decimal" class="form-control text-right" oninput="formatAngkaInput(event)">
            <!-- <input id="AddAddQntCrSat" type="text" class="form-control" style="width: 5px"  disabled> -->

          </div>

        </div>

    </div>

  </div>
</div>

</div>

<div class="row mt-2" style="margin-top: 0">
  <div class="col-md-12 text-right mt-4">

    <button type="button" class="btn btn-danger" onclick="buttonAddBatal()" style="height: 30px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;">Batal
    </button>

    <button id="buttonSubmitAddItem" type="button" onclick="submitAddItem()" class="btn btn-chip-biru" style="height: 30px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;">Simpan
    </button>

    <button id="buttonSubmitEditItem" type="button" onclick="submitEditItem()" class="btn btn-chip-biru" style="height: 30px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;">Simpan
    </button>

    <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
  </div>

</div>

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
        <button type="button" class="btn btn-danger btn-lg " style="height: 30px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >CLOSE</button>
      </div>
    </div>

    <div id= "" class="">



    <div id="formBsGrid" class="">
    <div class="">
      <!-- <h1>Tes Modal</h1> -->

      <div class="container-fluid">

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

        </div>

        <div class="row" style="margin-top: -10px">
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
                <th style="padding: 4px 12px;" scope="col">Sat</th>
                <th style="padding: 4px 12px;" scope="col">Qty Debet</th>
                <th style="padding: 4px 12px;" scope="col">Qty Kredit</th>
                <th style="padding: 4px 12px;" scope="col">Harga</th>
                <th style="padding: 4px 12px;" scope="col">Total</th>

              </tr>
            </thead>


            <tbody id="detailTableData" class="" >
              <tr >

                  <td colspan=7 class="text-center">Belum ada data</td>

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
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="modalAddListBarang" class="showhidemodalbodyadd">
        <div class="modal-body" >

        <div class="container-fluid mt-4" >
          <div class="row">
            <div id="modalBodyAddAddListBarangAllTitle" class="col-12" style="">
              {{-- <h3>Barang All</h3> --}}
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-10px;">
            <!-- <div class="container-fluid"> -->
            <table id="tabel_add_list_barangall" class="table table-bordered table-hover table-striped table-responsive-lg">
              <thead class="text-center">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add_list_barangall" class="text-left" >
                @for ($i = 0; $i < count($listBarangAll); $i++)
                <tr class="pick-row" onclick="buttonAddPickBarang('{{ $listBarangAll[$i]->Kodebrg }}')">
                  <td>{{ $listBarangAll[$i]->Kodebrg }}</td>
                  <td>{{ $listBarangAll[$i]->NamaBrg }}</td>
              </tr>
              @endfor
              </tbody>
            </table>
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>
            </div>
            </div>
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
        {{-- <button type="button" class="btn btn-danger btn-lg"
        style="
        margin-top:-10px;
        height: 30px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        transition: background-color 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
        onclick="buttonAddListBatal()">Batal</button> --}}
      </div>
    </div>



      <div id= "modalAddListMerk" class="showhidemodalbodyadd">
      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12">
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-60px; ">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_merk" class="table table-bordered table-striped" style="overflow:auto; " >
              <thead class="text-center">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Actions</th>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>
                  

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_merk" class="text-left" >

                <tr >
                  <td class="text-center">
                      <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                      <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                    </td>
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


      <div id="" class="modal-footer ">
        <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()" >Batal</button>
      </div>
      </div>


      </div>







    </div>
  </div>
<!-- End modal add-->














@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>
{{-- Modal pemilih Gudang/Barang disamakan persis dengan modal pemilih menu Kas, pola
     yang sama persis dengan perintahopname.blade.php/so.blade.php. --}}
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
let itemEdit = {}
let listBarang = []
let barangx = {}

/* ============ Separator ribuan untuk AddAddQntDb/AddAddQntCr/AddAddHarga ============
 * Field-field ini sekarang type="text" (bukan type="number", yang tidak bisa
 * menampilkan titik ribuan) + oninput="formatAngkaInput(event)" supaya tampilannya
 * "12.312" / "123.123,50" gaya id-ID. Field disimpan sebagai STRING berformat di
 * DOM, jadi setiap tempat yang baca .val() untuk perhitungan/submit WAJIB lewat
 * parseAngka() dulu (buang titik ribuan, ganti koma desimal jadi titik) supaya
 * Number(...) dan proses submit ke backend tetap dapat angka mentah yang benar --
 * bukan string "12.312" yang kalau di-Number()-kan langsung akan salah jadi 12.312
 * (dibaca sebagai 12 koma 312, bukan 12 ribu 312). */
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
 * satu tabel di halaman ini (#tabel -- KRS Belum+Sudah Otorisasi digabung jadi
 * satu tabel, dengan kolom Status dan filter Status di toolbar). renderTabelRows()
 * mem-force aktivasi urut-nya sendiri sebelum membaca gcart_header, supaya tidak
 * ada drift seperti bug yang ditemukan di perintahopname. */
let ksCart = { 1 : [] }
let ksActiveUrut = 0
const KS_HREF = 'koreksistock'
const KS_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date', 3 : 'bool' }
const KS_TIPE_KODE = { varchar : 0, float : 1, date : 2, bool : 3 }

function ksPickCI (row, key) {
  if (!row) { return undefined; }
  if (row[key] !== undefined) { return row[key]; }
  let lower = key.toLowerCase();
  for (let k in row) {
    if (k.toLowerCase() === lower) { return row[k]; }
  }
  return undefined;
}

function ksDefaultCart (urut) {
  return [
    ['Nobukti',       'No Bukti',  1, 'varchar', 0, 0],
    ['NamaGdg',       'Gudang',    1, 'varchar', 0, 0],
    ['Tanggal',       'Tanggal',   1, 'date',    0, 0],
    ['IsOtorisasi1',  'Status',    1, 'bool',    0, 0],
    ['OtoUser1',      'OtoUser',   1, 'varchar', 0, 0],
    ['TglOto1',       'TglOto',    1, 'date',    0, 0]
  ]
}

function ksBuatCart (headers, values, isnumerics, isshowns, desimals) {
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
      KS_TIPE_NAMA[tipe] || 'varchar',
      0,
      isNaN(des) ? 0 : des,
    ])
  });
  return cart
}

function ksAktifkanTabel (urut) {
  ksActiveUrut = urut
  window.g_modeReport = urut
  window.gcart_header = ksCart[urut]
}

function ksOnChangeAktif () {
  reinitTabel();
}

window.g_href = KS_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function (href, mode) {
  let urut = 1
  let cart = ksCart[urut] || []

  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[1])
    value.push(c[0])
    isnumber.push(KS_TIPE_KODE[c[3]] ?? 0)
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
      href     : KS_HREF,
      urut     : urut
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal menyimpan pengaturan kolom')
    }
  })
}

window.doSetHeader = function (mode, reset) {
  let urut = 1

  $.ajax({
    url   : "{!! url('getheadertable') !!}",
    type  : "post",
    async : false,
    data  : {
      _token : $("#_token").val(),
      href   : KS_HREF,
      urut   : urut,
      reset  : reset ? 1 : 0
    },
    success : function (res) {
      if (!reset && res && res.headertableheader && res.headertableheader.length) {
        ksCart[urut] = ksBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal || [])
      } else {
        ksCart[urut] = ksDefaultCart(urut)
        window.gcart_header = ksCart[urut]
        window.doSimpanHeader(KS_HREF, urut)
      }
      window.gcart_header = ksCart[urut]
    },
    error : function (err) {
      console.log(err)
      alertify.warning(reset ? 'Gagal mengembalikan kolom ke tampilan default' : 'Gagal memuat pengaturan kolom')
      ksCart[urut] = ksDefaultCart(urut)
      window.gcart_header = ksCart[urut]
    }
  })
}

let ksRtSudahInit = false
function ksInitReportTableSekali () {
  if (ksRtSudahInit || typeof ReportTable === 'undefined') { return }
  ksRtSudahInit = true

  ReportTable.init({ table : '#tabel', bar : '#rtBarTabel', onChange : ksOnChangeAktif })

  let ksGuardUlangKlik = false;
  ['#tabel'].forEach((sel) => {
    let thead = document.querySelector(sel + ' thead')
    if (!thead) { return }
    thead.addEventListener('click', function (e) {
      if (ksGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }
      e.stopPropagation()
      e.preventDefault()
      ksGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles: false, cancelable: true, view: window })
      Object.defineProperty(ulang, 'target', { value: interaktif, configurable: true })
      thead.dispatchEvent(ulang)
      ksGuardUlangKlik = false
    }, true)
  });
}

function tulisTheadHeaderKS (tableSel, cols) {
  let thead = document.querySelector(tableSel + ' thead')
  if (!thead || !window.ReportTable) { return; }
  let headRowHtml = ReportTable.headHtml(cols)
    .replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>');
  thead.setAttribute('style', 'white-space:nowrap;');
  thead.innerHTML = headRowHtml;
}

function ksValueCell (row, col) {
  let raw = ksPickCI(row, col[0]);
  let type = col[3];

  {{-- Kolom Status (IsOtorisasi1) dirender sebagai badge hijau (Sudah Otorisasi)
       atau amber (Belum Otorisasi), bukan ikon check/x bool biasa -- sama seperti
       gudangpermintaansample.blade.php. --}}
       
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
  if (type === 'bool') {
    return Number(raw)
      ? '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>'
      : '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>';
  }
  return '<td>' + (raw !== undefined && raw !== null ? raw : '') + '</td>';
}

function ksTabelActionsCell (row) {
  let nobukti = ksPickCI(row, 'Nobukti');
  let sudah = Number(ksPickCI(row, 'IsOtorisasi1')) === 1;
  let html = '<td class="text-center" style="white-space:nowrap;"><div class="action-buttons-wrap">';
  html += '<button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail(\'' + nobukti + '\' , \'detail\')"><i class="bi bi-info"></i></button>';
  if (sudah) {
    html += '<button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi(\'' + nobukti + '\' , \'edit\')"><i class="bi bi-key"></i></button>';
    html += '<button class="btn btn-primary btn-sm" type="button" title="Print" onclick="submitPrint(\'' + nobukti + '\')"><i class="bi bi-printer"></i></button>';
  } else {
    html += '<button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi(\'' + nobukti + '\' , \'edit\')"><i class="bi bi-pen"></i></button>';
    html += '<button class="btn btn-primary btn-sm" type="button" onclick="buttonOtorisasi(\'' + nobukti + '\' , \'otorisasi\')"><i class="bi bi-key"></i></button>';
  }
  html += '</div></td>';
  return html;
}

function ksFilterRows (rows) {
  let status = $('#ksStatus').val()
  let awal = $('#ksTanggalAwal').val()
  let akhir = $('#ksTanggalAkhir').val()
  return (rows || []).filter(function (row) {
    if (status !== '' && String(Number(ksPickCI(row, 'IsOtorisasi1'))) !== status) { return false }
    if (awal || akhir) {
      let tgl = ksPickCI(row, 'Tanggal')
      if (!tgl) { return false }
      let t = String(tgl).slice(0, 10)
      if (awal && t < awal) { return false }
      if (akhir && t > akhir) { return false }
    }
    return true
  })
}

function ksFilterChanged () {
  loadAll()
}

function renderTabelRows (rows) {
  if (ksActiveUrut !== 1) { ksAktifkanTabel(1); }
  let cols = (ksCart[1].length ? ksCart[1] : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  (rows || []).forEach(function (row) {
    html += '<tr>' + ksTabelActionsCell(row);
    cols.forEach(function (col) { html += ksValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel_data').innerHTML = html;
  tulisTheadHeaderKS('#tabel', cols);
}

let lastTabelRows = []
let ksPanjangHalaman = { 1 : 10 }

function ksIkatSearch (urut) {
  let ids = { 1 : ['ksSearch1', 'tabel'] }
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

function ksIkatPanjangHalaman (urut) {
  let ids = { 1 : ['ksLen1', 'tabel'] }
  let sel = document.getElementById(ids[urut][0])
  let idTabel = ids[urut][1]
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(ksPanjangHalaman[urut])

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    ksPanjangHalaman[urut] = (n === -1 || n > 0) ? n : 10
    if ($.fn.DataTable.isDataTable('#' + idTabel)) {
      $('#' + idTabel).DataTable().page.len(ksPanjangHalaman[urut]).draw()
    }
  })
}

const KS_DOM_STRING = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"

function reinitTabel () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().destroy(); }
    renderTabelRows(ksFilterRows(lastTabelRows));
    $('#tabel').DataTable({ dom: KS_DOM_STRING, lengthChange: false, pageLength: ksPanjangHalaman[1], paging: true, ordering: false });
    ksIkatSearch(1);
    ksIkatPanjangHalaman(1);
  } catch (e) {
    console.error('reinitTabel failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

$(document).ready(function(){
      ksAktifkanTabel(1);
      window.doSetHeader(1, false);
      lastTabelRows = [].concat(@json($tempOutstanding), @json($tempOutstanding2));
      reinitTabel();

      ksInitReportTableSekali();

      loadGudangOptions();

      {{-- Box "Search:" bawaan DataTables di modal Barang All dibuat ulang setiap
           pickerKasInit() memanggil .destroy(), jadi diikat via delegated event
           supaya tetap jalan walau elemennya diganti. --}}
      {{-- keyup dipakai (bukan keypress) -- input bawaan DataTables type="search"
           tidak selalu memicu event keypress saat Enter ditekan di semua browser. --}}
      $(document).on('keyup', '#tabel_add_list_barangall_filter input', searchBarangAll);
});


function onKeyPressBarang (e) {
  if (e.which === 13) {
    let _token = $("#_token").val()
    let kodebrg = $('#AddAddKodeBrg').val();
    let search = kodebrg
    console.log(search)
    let gudang = $("#input_add_gudang").val();

    $.ajax({
      url: "{!! url('koreksistocklistbarang') !!}",
      type: "post",
      async: false,
      data: {
        search,
        _token,
        gudang
      },
      success: function(res) {

        console.log(res)
        listBarang = res
        let rowTable = ""

        if (res.length == 1) {
          buttonAddPickBarang( 0 , 1)
          $('#AddAddQntDb').focus();
          return
        }

        res.forEach((item, i) => {

          rowTable +=          `
          <tr class="pick-row" onclick="buttonAddPickBarang('${i}')">
            <td>${item.KodeBrg}</td>
            <td>${item.NamaBrg}</td>

        </tr>
        `
        });
        document.getElementById("tabel_data_add_list_barangall").innerHTML = rowTable
        pickerKasInit('tabel_add_list_barangall', { order: [[0, 'asc']] })

      document.getElementById("exampleModalLabel").innerHTML = 'Barang'
      $('.showhidemodalbodyadd').hide();
        $('#modalAddListBarang').show();

        $("#form").modal('toggle')

      }})

  }
}

function buttonAddBatal () {

  $('.showhideitem').hide();
}


function onchangeqntdb () {
    let qnt = parseAngka($("#AddAddQntDb").val())
    if (qnt == 0) {
      document.getElementById("AddAddQntCr").disabled = false
    } else {
      document.getElementById("AddAddQntCr").disabled = true
    }
}

function onchangeqntcr () {

    let qnt = parseAngka($("#AddAddQntCr").val())
    if (qnt == 0) {
      document.getElementById("AddAddQntDb").disabled = false;
      document.getElementById("AddAddHarga").disabled = false;
    } else {
      document.getElementById("AddAddQntDb").disabled = true;
      document.getElementById("AddAddHarga").disabled = true;
    }
}

function buttonAddListBatal () {
  $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddMain').show();

  $("#form").modal('toggle')
}


function loadAll () {
  console.log('loadall');
  let _token = $("#_token").val();
  let awal = $('#ksTanggalAwal').val()
  let akhir = $('#ksTanggalAkhir').val()

  $.ajax({
    url: "{!! url('koreksistockloadall') !!}",
    type: "get",
    async: false,
    data: {
      date1: awal,
      date2: akhir
    },
    success: function(res) {
      console.log(res);
      lastTabelRows = [].concat(res.tempOutstanding, res.tempOutstanding2)
      reinitTabel()
    }
  });
}

function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: "{!! url('koreksistockdetailCetak') !!}",
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
                  <div class="pb-1" style="width: 100%">Nomor : `+dataPrint[0].Nobukti+`</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">Tanggal : `+tanggalOnly+`</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
              </div>


              <div style="width: 38%">
                <div style="display: flex; width: 100%">
                  <h2 class="m-0 pb-2">KOREKSI STOCK</h2>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Gudang</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+dataPrint[0].KodeGdg+`</div>
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
                    <td class="text-center" style="width: 30%">KODE BARANG</td>
                    <td class="text-center" style="width: 50%">NAMA BARANG</td>
                    <td class="text-center" style="width: 10%">SATUAN</td>
                    <td class="text-center" style="width: 10%">DEBET</td>
                    <td class="text-center" style="width: 10%">KREDIT</td>
                    <td class="text-center" style="width: 10%">HARGA</td>
                  </tr>
                </thead> `;

    let z = 0
    let tempPrintStr = ``
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
               style="width: 50%;  ">${itemSub.kodebrg}</td>
         <td class="text-align: left"
               style="width: 50%;  ">${itemSub.namaBrg}</td>
         <td class="text-align: text-center"
               style="width: 10%;">${itemSub.Satuan}</td>
         <td class="text-align: text-right"
               style="width: 10%;  ">${itemSub.Qntdb ? parseFloat(itemSub.Qntdb).toFixed(2) : ''}</td>
         <td class="text-align: text-right"
               style="width: 10%;  ">${itemSub.QntCr ? parseFloat(itemSub.QntCr).toFixed(2) : ''}</td>
         <td class="text-align: text-right"
               style="width: 10%;  ">${itemSub.Harga 
               ? parseFloat(itemSub.Harga).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '0,00'}</td>
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
               <td class="no-border text-center" style="width: 25%">Kepala Gudang</td>
               <td class="no-border text-center" style="width: 25%">Internal Audit</td>
               <td class="no-border text-center" style="width: 25%">Mengetahui</td>
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
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
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

function buttonAddItem () {
  let gudang = $("#input_add_gudang").val()
  if(!gudang) {
    alertify.warning("Pilih gudang")
    return
  }

  $("#labelAddItem").show()
  $("#labelEditItem").hide()
  $("#buttonSubmitAddItem").show()
  $("#buttonSubmitEditItem").hide()
  lockFormAddItem(false)
  cleanFormAddItem()
  document.getElementById("AddAddKodeBrg").disabled = false

  document.getElementById("AddAddQntCr").disabled = false
  document.getElementById("AddAddQntDb").disabled = false

  $('.showhideitem').show()

}




function refreshDataTableDetail (nobukti) {

  let _token = $("#_token").val();
  listData = []
  $.ajax({
    url: "{!! url('koreksistockspdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {
      console.log(res)
      listData = res

      let rowTable = ``
      listData.forEach((item, i) => {


              rowTable += `
                <tr>
                  <td>${item.kodebrg}</td>
                  <td>${item.namaBrg}</td>
                  <td class="text-center">${item.Satuan}</td>
                  <td class="text-right">${formatAngkaX(item.Qntdb)}</td>
                  <td class="text-right">${formatAngkaX(item.QntCr)}</td>
                  <td class="text-right">${formatAngkaX(item.Harga)}</td>
                  <td class="text-right">${formatAngkaX(item.Total)}</td>
                </tr>

              `

              // <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
      });

      document.getElementById("detailTableData").innerHTML = rowTable
      document.getElementById("input_detail_nobukti").value = listData[0].Nobukti
      document.getElementById("input_detail_nourut").value = listData[0].NoUrut
      document.getElementById("input_detail_tanggal").value = formatDate(listData[0].Tanggal)
      document.getElementById("input_detail_keterangan").value = listData[0].note
      document.getElementById("input_detail_gudang").value = listData[0].KodeGdg



    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      // resRefresh = 0;
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


function buttonBatalOtorisasi (nobukti) {
  let akses = $("#akses_isbatal").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
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
          url: "{!! url('koreksistockspbatalotorisasi') !!}",
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
    url: "{!! url('koreksistockspotorisasi') !!}",
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


function submitOtorisasi () {

  let _token = $("#_token").val();
  let nobukti = $("#input_detail_nobukti").val();
  $.ajax({
    url: "{!! url('koreksistockspotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {
      alertify.success('Berhasil update otorisasi')
      loadAll()
      buttonCloseForm()




    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}


function onChangeHeader () {

  if (tipeform == 'add') {
    return
  }

  let _token = $("#_token").val();
  let nobukti = $("#input_add_nobukti").val();
  let keterangan = $("#input_add_keterangan").val();
  console.log(keterangan, nobukti)
  $.ajax({
    url: "{!! url('koreksistockspupdateheader') !!}",
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

function submitAddItem () {
  let checkDate = new Date($("#input_add_tanggal").val())
  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value


  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }

  let _token = $("#_token").val();
  let kodebrg = $("#AddAddKodeBrg").val();

  if ( !kodebrg ) {
    alertify.warning("Kode barang harus diisi")
    return
  }

  if (kodebrg != barangx.KodeBrg) {
    alertify.warning("Barang tidak sesuai")
    return
  }
  let choice= 'I'
  let nobukti = $("#input_add_nobukti").val();
  let nourut = $("#input_add_nourut").val();
  let tanggal = $("#input_add_tanggal").val();
  let gudang = $("#input_add_gudang").val();
  let keterangan = $("#input_add_keterangan").val();

  let qntcr = parseAngka($("#AddAddQntCr").val());
  let harga = parseAngka($("#AddAddHarga").val());
  let qntdb = parseAngka($("#AddAddQntDb").val());
  let satuan = $("#AddAddQntDbSat").val();
  let qntsaldo = Number(barangx.QntSaldo) ? barangx.QntSaldo : '0.00'
  let selisih = Number(qntcr) > 0 ? qntcr : qntdb
  let urut = 0
  let qntopname = 0
  let nosat = 1
  let isi = 1
  let jmlrecord = tipeform == 'add' ? 0 : 1
  if (Number(qntcr) <= 0  && Number(qntdb) <= 0 ) {
    alertify.warning("Qnt <= 0")
    return
  }

  if (Number(qntcr) > 0  && Number(qntdb) > 0 ) {
    alertify.warning("Qnt DB/CR")
    return
  }

  if (Number(qntcr) > 0){
    if (Number(qntcr) > Number(qntsaldo)){
      alertify.warning("Qnt CR Melebihi Stock")
      return
    }
  }
  // if (Number(qntcr) > Number(qntsaldo)) {
  //   alertify.warning("Qnt cr melebihi stock")
  //   return
  // }





  $.ajax({
      url: "{!! url('koreksistockspadd') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        choice,
        nobukti,
        nourut,
        tanggal,
        keterangan,
        gudang,
        qntcr,
        qntdb,
        satuan,
        kodebrg,
        selisih,
        urut,
        qntsaldo,
        qntopname,
        jmlrecord,
        nosat,
        isi,
        harga
      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          // $("#form").modal('toggle')
          alertify.success('Koreksi Stock telah ditambah');

          tipeform = 'edit'
          loadAll()
          $('.showhideitem').hide()
          lockForm()
          // $("#formAddItem").modal('toggle');
          buttonKoreksi(nobukti)

        }
        if (res == 2) {
          setNewNoBukti()
          alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
        }

        if (res == 3) {
          alertify.warning("Barang tidak ditemukkan")
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



function loadGudangOptions () {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('koreksistocklistgudang') !!}",
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



function lockForm (value = false) {
  document.getElementById("input_add_tanggal").disabled = value
  // document.getElementById("input_add_keterangan").disabled = value
  document.getElementById("input_add_gudang").disabled = value

}



{{-- Dulu diikat ke box search hardcoded (#input_search_barang_all) yang terpisah
     dari box "Search:" bawaan DataTables di modal ini -- sekarang diikat langsung
     ke box bawaan itu (#tabel_add_list_barangall_filter input), lihat binding
     delegated di $(document).ready() di bawah. --}}
function searchBarangAll (e) {
  let _token = $("#_token").val()
  if (e.which == 13) {
    console.log('enter')

    let search = e.target.value;
    let gudang = $("#input_add_gudang").val();

    $.ajax({
      url: "{!! url('koreksistocklistbarang') !!}",
      type: "post",
      async: false,
      data: {
        search,
        _token,
        gudang
      },
      success: function(res) {

        console.log(res)
        listBarang = res
        let rowTable = ""
        res.forEach((item, i) => {

          rowTable +=          `
          <tr class="pick-row" onclick="buttonAddPickBarang('${i}')">
            <td>${item.KodeBrg}</td>
            <td>${item.NamaBrg}</td>
        </tr>
        `
        });

        document.getElementById("tabel_data_add_list_barangall").innerHTML = rowTable
        pickerKasInit('tabel_add_list_barangall', { order: [[0, 'asc']] })

      }})

  }

}

function buttonAddListBarang () {
  let gudang = $("#input_add_gudang").val()
  if(!gudang) {
    alertify.warning("Pilih gudang")
    return
  }

  console.log("buttonAddListBarang")
  document.getElementById("exampleModalLabel").innerHTML = 'Barang'
  $('.showhidemodalbodyadd').hide();
    $('#modalAddListBarang').show();

    document.getElementById("tabel_data_add_list_barangall").innerHTML = ''
    pickerKasInit('tabel_add_list_barangall', { order: [[0, 'asc']] })

    $("#form").modal('toggle')


    document.getElementById("modalBodyAddAddListBarangAllTitle").scrollIntoView();

    $('#form').on('shown.bs.modal', function () {
    $('#tabel_add_list_barangall_filter input').trigger('focus')
    })
}


function buttonAddPickBarang (index , x = 0) {

  let _token  = $("#_token").val()
  barangx = listBarang[index]

  document.getElementById("AddAddKodeBrg").value = barangx.KodeBrg
  document.getElementById("AddAddNamaBrg").value = barangx.NamaBrg
  document.getElementById("AddAddQntCrSat").value = barangx.Sat1
  document.getElementById("AddAddQntDbSat").value = barangx.Sat1
  document.getElementById("AddAddQntDb").value = formatAngkaDisplay(0)
  document.getElementById("AddAddQntCr").value = formatAngkaDisplay(0)
  document.getElementById("AddAddHarga").value = formatAngkaDisplay(0)

  if (x == 1) {
    return
  }
  $("#form").modal('toggle')


}

function cleanFormAdd () {
  document.getElementById("input_add_tanggal").valueAsDate = new Date()
  document.getElementById("input_add_keterangan").value = ''
  document.getElementById("input_add_gudang").value = ''
  document.getElementById("addTableData").innerHTML = `<tr><td colspan=11 class="text-center">Belum ada data</td></tr>`

}

function lockFormAddItem (value = false) {
  document.getElementById("buttonAddListBarang").disabled = value

}

function cleanFormAddItem () {
  document.getElementById("AddAddKodeBrg").value = ''
  document.getElementById("AddAddNamaBrg").value = ''
  document.getElementById("AddAddQntCr").value = formatAngkaDisplay(0)
  document.getElementById("AddAddQntDb").value = formatAngkaDisplay(0)
  document.getElementById("AddAddQntCrSat").value = ''
  document.getElementById("AddAddQntDbSat").value = ''
  document.getElementById("AddAddHarga").value = formatAngkaDisplay(0)


}



function refreshDataTable (nobukti) {

    let _token = $("#_token").val();
    listData = []
    $.ajax({
      url: "{!! url('koreksistockspdetail') !!}",
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


                rowTable += `
                  <tr>
                    <td>${item.kodebrg}</td>
                    <td>${item.namaBrg}</td>
                    <td class="text-center">${item.Satuan}</td>
                    <td class="text-right">${formatAngkaX(item.Qntdb)}</td>
                    <td class="text-right">${formatAngkaX(item.QntCr)}</td>
                    <td class="text-right">${formatAngkaX(item.Harga)}</td>
                    <td class="text-right">${formatAngkaX(item.Total)}</td>
                    <td class='text-center'>
                      <button class="btn btn-success btn-sm" type="button" onclick="buttonEditItem('${i}' )"><i class="bi bi-pen"></i></button>

                      <button class="btn btn-danger btn-sm" type="button" onclick="buttonDeleteItem('${i}' )"><i class="bi bi-trash"></i></button>


                    </td>


                  </tr>

                `

                // <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
        });

        document.getElementById("addTableData").innerHTML = rowTable
        document.getElementById("input_add_nobukti").value = listData[0].Nobukti
        document.getElementById("input_add_nourut").value = listData[0].NoUrut
        document.getElementById("input_add_tanggal").value = formatDate(listData[0].Tanggal)
        document.getElementById("input_add_keterangan").value = listData[0].note
        document.getElementById("input_add_gudang").value = listData[0].KodeGdg



      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
        // resRefresh = 0;
      }

    })
}


function buttonDeleteItem (index) {


  itemEdit = listData[index]
  let _token = $("#_token").val();
  let kodebrg = itemEdit.kodebrg

    alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item '+ itemEdit.kodebrg +' ?',
        function() {
          let choice= 'D'
          let nobukti = $("#input_add_nobukti").val();
          let nourut = $("#input_add_nourut").val();
          let tanggal = $("#input_add_tanggal").val();
          let gudang = $("#input_add_gudang").val();
          let keterangan = $("#input_add_keterangan").val();

          let qntcr = 0
          let harga = 0
          let qntdb = 0
          let satuan = ''
          let qntsaldo = 0
          let selisih = 0
          let urut = itemEdit.Urut
          let qntopname = 0
          let nosat = 1
          let isi = 1
          let jmlrecord = tipeform == 'add' ? 0 : 1

          $.ajax({
              url: "{!! url('koreksistockspadd') !!}",
              type: "post",
              async: false,
              data: {
                _token,
                choice,
                nobukti,
                nourut,
                tanggal,
                keterangan,
                gudang,
                qntcr,
                qntdb,
                satuan,
                kodebrg,
                selisih,
                urut,
                qntsaldo,
                qntopname,
                jmlrecord,
                nosat,
                isi,
                harga
              },
              success: function(res) {
                console.log(res ,'!')

                if (res == 1) {
                  // $("#form").modal('toggle')
                  alertify.success('Koreksi Stock telah diedit');

                  tipeform = 'edit'
                  loadAll()
                  $('.showhideitem').hide()
                  lockForm()
                  // $("#formAddItem").modal('toggle');
                  buttonKoreksi(nobukti)

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

function submitEditItem () {
  let _token = $("#_token").val();
  let kodebrg = $("#AddAddKodeBrg").val();

  if ( !kodebrg ) {
    alertify.warning("Kode barang harus diisi")
    return
  }
  let choice= 'U'
  let nobukti = $("#input_add_nobukti").val();
  let nourut = $("#input_add_nourut").val();
  let tanggal = $("#input_add_tanggal").val();
  let gudang = $("#input_add_gudang").val();
  let keterangan = $("#input_add_keterangan").val();

  let qntcr = parseAngka($("#AddAddQntCr").val());
  let harga = parseAngka($("#AddAddHarga").val());
  let qntdb = parseAngka($("#AddAddQntDb").val());
  let satuan = $("#AddAddQntDbSat").val();
  let qntsaldo = Number(itemEdit.SaldoComp) ? itemEdit.SaldoComp : '0.00'
  let selisih = Number(qntcr) > 0 ? qntcr : qntdb
  let urut = itemEdit.Urut
  let qntopname = 0
  let nosat = 1
  let isi = 1
  let jmlrecord = tipeform == 'add' ? 0 : 1
  if (Number(qntcr) <= 0  && Number(qntdb) <= 0 ) {
    alertify.warning("Qnt <= 0")
    return
  }

  if (Number(qntcr) > 0  && Number(qntdb) > 0 ) {
    alertify.warning("Qnt DB/CR")
    return
  }

  if (Number(qntcr) > 0){
    if (Number(qntcr) > Number(qntsaldo)){
      alertify.warning("Qnt CR Melebihi Stock")
      return
    }
  }
  // if (Number(qntcr) > Number(qntsaldo)) {
  //   alertify.warning("Qnt cr melebihi stock")
  //   return
  // }





  $.ajax({
      url: "{!! url('koreksistockspadd') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        choice,
        nobukti,
        nourut,
        tanggal,
        keterangan,
        gudang,
        qntcr,
        qntdb,
        satuan,
        kodebrg,
        selisih,
        urut,
        qntsaldo,
        qntopname,
        jmlrecord,
        nosat,
        isi,
        harga
      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          // $("#form").modal('toggle')
          alertify.success('Koreksi Stock telah diedit');

          tipeform = 'edit'
          loadAll()
          $('.showhideitem').hide()
          lockForm()
          // $("#formAddItem").modal('toggle');
          buttonKoreksi(nobukti)

        }

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })




}



function buttonEditItem (index) {
  itemEdit = {}

  itemEdit = listData[index]
  console.log(itemEdit)
  lockFormAddItem(true)
  // return
  $("#labelAddItem").hide()
  $("#labelEditItem").show()
  $("#buttonSubmitAddItem").hide()
  $("#buttonSubmitEditItem").show()
  document.getElementById("AddAddKodeBrg").value = itemEdit.kodebrg

  document.getElementById("AddAddNamaBrg").value = itemEdit.namaBrg
  document.getElementById("AddAddQntCr").value = formatAngkaDisplay(itemEdit.QntCr)
  document.getElementById("AddAddQntCrSat").value = itemEdit.Satuan
  document.getElementById("AddAddQntDb").value = formatAngkaDisplay(itemEdit.Qntdb)
  document.getElementById("AddAddQntDbSat").value = itemEdit.Satuan
  document.getElementById("AddAddHarga").value = formatAngkaDisplay(itemEdit.Harga)
  document.getElementById("AddAddKodeBrg").disabled = true

  if (Number(itemEdit.QntCr)) {
    document.getElementById("AddAddQntCr").disabled = false
    document.getElementById("AddAddQntDb").disabled = true

  } else {
    document.getElementById("AddAddQntCr").disabled = true
    document.getElementById("AddAddQntDb").disabled = false

  }

  $('.showhideitem').show()
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
    console.log("=")
    console.log(listData[0].IsOtorisasi1 )
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
  let kode  = 'KRS'
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

function formatAngkaX (angka) {
  if (!Number(angka)) {
    return '0.00'
  } else {
    return formatAngka(parseFloat(angka).toFixed(2))

  }

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
