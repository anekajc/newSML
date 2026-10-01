@extends('newmasterTest')
@section('buttons')


@section('page-title', 'Retur Penjualan Gudang')
@section('title', 'SML - Retur Penjualan Gudang')

@endsection
{{-- tampilan search bar 1 --}}
{{-- Rerouted to match Purchase Order's UI 1:1 via so.blade.php's own pattern,
     same as invoicejasa/fakturpajak/cetaktandaterima/perintahreturjual before it.
     Only layout/toolbar/column-header interactivity changed -- business logic
     untouched. --}}
  @section('css')
  <link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
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
.custom-tabs .nav-link:hover { background: transparent; color: #007bff; }
.custom-tabs .nav-link.active {
  background: #007bff; border-color: #007bff; color: #fff;
  box-shadow: 0 2px 6px rgba(0, 123, 255, .35);
}
.tab-card {
  display: block !important;
  align-items: flex-start !important;
  padding: 0 !important;
  border: none !important;
  margin-bottom: 6px !important;
}
.tab-card .card-body { padding: 5px 10px !important; }
#page1 .card {
  display: block !important;
  align-items: stretch !important;
  padding: 0 !important;
  text-align: left !important;
  cursor: default !important;
}
#page1 .card:hover { transform: none !important; box-shadow: none !important; border-color: var(--border) !important; }
.po-len-wrap {
  display: flex; align-items: center; gap: 8px;
  background: var(--rt-card); border: 1.5px solid var(--rt-border);
  border-radius: 8px; padding: 5px 12px;
}
.po-len-wrap label {
  margin: 0; font-size: 11.5px; font-weight: 700; color: var(--rt-ink-soft);
  text-transform: uppercase; letter-spacing: .05em; white-space: nowrap;
}
.po-len-inp {
  border: none; background: transparent; font-size: 13px; font-weight: 700;
  color: var(--rt-ink); outline: none; cursor: pointer; padding: 2px 20px 2px 0;
  appearance: none; -webkit-appearance: none; -moz-appearance: none;
  background-image: url("data:image/svg+xml;utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%231D2130' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right center;
}
#tabel td:first-child, #tabel2 td:first-child, #tabel3 td:first-child {
  display: flex; gap: 4px; justify-content: center; align-items: center;
}
#tabel td:first-child .btn, #tabel2 td:first-child .btn, #tabel3 td:first-child .btn {
  width: 30px; height: 30px; padding: 0; display: inline-flex; align-items: center;
  justify-content: center; border-radius: 7px; font-size: 13px;
  border: 1px solid transparent; box-shadow: none; transition: all .12s ease;
}
#tabel td:first-child .btn:hover, #tabel2 td:first-child .btn:hover, #tabel3 td:first-child .btn:hover { filter: brightness(0.97); transform: translateY(-1px); }
#tabel td:first-child .btn-warning, #tabel2 td:first-child .btn-warning, #tabel3 td:first-child .btn-warning { color: #b45309; border-color: #fbe3bd; background: #fef3e0; }
#tabel td:first-child .btn-primary, #tabel2 td:first-child .btn-primary, #tabel3 td:first-child .btn-primary { color: #2563eb; border-color: #cfdcff; background: #e8edff; }
#tabel td:first-child .btn-danger, #tabel2 td:first-child .btn-danger, #tabel3 td:first-child .btn-danger { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }
#tabel td:first-child .btn-success, #tabel2 td:first-child .btn-success, #tabel3 td:first-child .btn-success { color: #15803d; border-color: #bfe6c9; background: #e8f7ee; }

#koreksiTable td:last-child .btn {
  width: 30px; height: 30px; padding: 0; display: inline-flex; align-items: center;
  justify-content: center; border-radius: 7px; font-size: 13px;
  border: 1px solid transparent; box-shadow: none; transition: all .12s ease;
}
#koreksiTable td:last-child .btn:hover { filter: brightness(0.97); transform: translateY(-1px); }
#koreksiTable td:last-child .btn-success { color: #15803d; border-color: #bfe6c9; background: #e8f7ee; }
#koreksiTable td:last-child .btn-danger  { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }

/* Perbesar glyph + pada tombol Add tanpa mengubah ukuran bingkai tombol.
   line-height: 0 membuat ikon tidak menambah tinggi baris, jadi tinggi
   tombol tetap sama dengan tombol Detail di sebelahnya. Port 1:1 dari
   returpembeliangudang.blade.php. */
.btn .bi-plus {
  font-size: 1.5rem;
  line-height: 0;
  vertical-align: middle;
}
#tabel thead th, #tabel2 thead th, #tabel3 thead th {
  background: #f8f9fb !important; color: #6b7280 !important; font-size: 12px;
  text-transform: uppercase; letter-spacing: .04em; font-weight: 600;
  border-bottom: 1px solid #e7e9ee; border-top: none;
}
#tabel tbody tr:nth-of-type(odd), #tabel2 tbody tr:nth-of-type(odd), #tabel3 tbody tr:nth-of-type(odd) { background-color: #fbfbfc; }
#tabel tbody tr:hover, #tabel2 tbody tr:hover, #tabel3 tbody tr:hover { background-color: #f5f3ff; }

/* Hide action buttons until the row is hovered/focused, port 1:1 dari pola
   .action-buttons-wrap milik master (public/css/tableMaster2.css). */
#tabel tbody .action-buttons-wrap, #tabel2 tbody .action-buttons-wrap {
  opacity: 0;
  visibility: hidden;
  transform: translateX(-6px);
  transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
}

#tabel tbody tr:hover .action-buttons-wrap, #tabel tbody tr:focus-within .action-buttons-wrap,
#tabel2 tbody tr:hover .action-buttons-wrap, #tabel2 tbody tr:focus-within .action-buttons-wrap {
  opacity: 1;
  visibility: visible;
  transform: translateX(0);
}

/* Form SPR modal -- port 1:1 dari style #formDetail milik
   returpembeliangudang.blade.php (thead abu-abu, border tipis antar baris,
   tombol Batal abu-abu). */
#addTable thead th {
  background: #f8f9fb !important; color: #6b7280 !important; font-size: 12px;
  text-transform: uppercase; letter-spacing: .04em; font-weight: 600;
  border-bottom: 1px solid #e7e9ee; border-top: none;
}
#addTable tbody tr:nth-of-type(odd) { background-color: #fbfbfc; }
#addTable tbody tr:hover { background-color: #f5f3ff; }
#addTable, #addTable td, #addTable th { border-left: none !important; border-right: none !important; }
#addTable tbody td { border-top: none !important; border-bottom: 1px solid #f1f3f5 !important; font-size: 13px; vertical-align: middle; white-space: nowrap; }
.btn-batal-add {
  background-color: #f1f3f5; border-color: #dee2e6; color: #495057;
}
.btn-batal-add:hover, .btn-batal-add:focus {
  background-color: #e9ecef; border-color: #ced4da; color: #343a40;
}
.btn-batal-add:active {
  background-color: #dee2e6 !important; border-color: #ced4da !important; color: #343a40 !important;
}

.data-table th,
.data-table td {
  white-space: nowrap;
}
</style>
@endsection
@section('content')



<div id="page1" class="container-fluid mainpage">
<div class="">

  <!-- <div id="qrcode"></div> -->
  <div class="row">
    <div class="col-6 text-left">
      <h2 style="margin-top:-85px;">Retur Penjualan Gudang</h2>
    </div>
    <div class="col-6 text-right">
      <!-- <button type="button" class="btn btn-primary btn-lg" style="
          height: 30px;
          margin-top: -150px;
          padding: 4px 12px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
          onclick="buttonAdd()">
        Add SO
      </button> -->
    </div>
  </div>
<!-- <button onclick="loadAll()">tes</button> -->
</div>

<div id="printContainer" style="display:none">


</div>
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
  <div class="card mb-3 tab-card">
    <div class="card-body">
      <div class="nav nav-tabs border-0 custom-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">Outstanding PRJ</a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">Transaksi Retur Gudang</a>
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
            <div class="po-filter-wrap">
              <label>Periode</label>
              <input type="date" onchange="onChangePeriodePRJ()" class="po-filter-inp" id="input_tanggalawal_prj" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d') !!}">
              <span class="po-filter-sep">s/d</span>
              <input type="date" onchange="onChangePeriodePRJ()" class="po-filter-inp" id="input_tanggalakhir_prj" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d') !!}">
            </div>
            <input type="search" id="rpgSearch1" class="po-search-inp" placeholder="Cari data">
            <div class="po-len-wrap"><label for="rpgLen1">Tampilkan</label>
              <select id="rpgLen1" class="po-len-inp"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="-1">Semua</option></select>
            </div>
          </div>
          <div id="rtBarTabel"></div>
          <table id="tabel" class="data-table">
            <thead style="white-space:nowrap;"></thead>


            <tbody id="tabel_data" class="text-left" >

            </tbody>
          </table>
          <div class="po-rt-hint"><i class="bi bi-info-circle"></i> Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom untuk menyembunyikan kolom atau mengatur jumlah desimal.</div>
        </div>
      </div>

    </div>
  </div>
  <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
    <div class="row">
      <div class="col-12" style="overflow:auto;">
        <div class="container-fluid" style="padding:0; margin:0; width:100%;">

          {{-- Filter modal: port 1:1 dari modalFilter milik perintahreturjual.blade.php,
               menggabungkan tab "Belum Otorisasi"/"Sudah Otorisasi" jadi satu tabel
               dengan Status dropdown, sesuai keputusan yang sama dipakai buat PRJ. --}}
          <div class="modal fade rt-filter" id="modalFilterSPR">
            <div class="modal-dialog modal-md">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">
                    <i class="bi bi-funnel"></i>
                    Filter Data
                    <span class="rt-active-badge" id="sprFilterBadge">0 aktif</span>
                  </h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterSPR').modal('hide')">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>

                <div class="modal-body">
                  <div class="rt-section">
                    <div class="rt-group-label">Status</div>
                    <div>
                      <label class="rt-field-label" for="input_filterstatus">Status SPR</label>
                      <select class="rt-native" id="input_filterstatus">
                        <option value=0 selected>Semua Status</option>
                        <option value=1>Belum</option>
                        <option value=2>Sebagian</option>
                        <option value=3>Selesai</option>
                      </select>
                    </div>
                  </div>

                  <div class="rt-section">
                    <div class="rt-group-label">Otorisasi</div>
                    <div>
                      <label class="rt-field-label" for="input_filteroto">Status Otorisasi</label>
                      <select class="rt-native" id="input_filteroto">
                        <option value=0 selected>Semua</option>
                        <option value=1>Belum Otorisasi</option>
                        <option value=2>Sudah Otorisasi</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="modal-footer">
                  <button type="button" class="rt-reset-link" onclick="rpgResetFilterFields()">Reset semua</button>
                  <div class="rt-footer-buttons">
                    <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
                      onclick="$('#modalFilterSPR').modal('hide')">Batal</button>
                    <button type="button" class="rt-btn rt-btn-primary" onclick="buttonFilterSPR(); $('#modalFilterSPR').modal('hide');">Terapkan</button>
                  </div>
                </div>

              </div>
            </div>
          </div>

          <div class="po-toolbar">
            <div class="po-filter-wrap">
              <label>Periode</label>
              <input type="date" onchange="onChangePeriodeSPR()" class="po-filter-inp" id="input_tanggalawal_spr" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d') !!}">
              <span class="po-filter-sep">s/d</span>
              <input type="date" onchange="onChangePeriodeSPR()" class="po-filter-inp" id="input_tanggalakhir_spr" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d') !!}">
            </div>
            <input type="search" id="rpgSearch2" class="po-search-inp" placeholder="Cari data">
            <div class="po-len-wrap"><label for="rpgLen2">Tampilkan</label>
              <select id="rpgLen2" class="po-len-inp"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="-1">Semua</option></select>
            </div>
            <button class="po-btn-filter" type="button" onclick="$('#modalFilterSPR').modal('show')">
              <i class="bi bi-funnel"></i> Filter
            </button>
          </div>
          <div id="rtBarTabel2"></div>
          <table id="tabel2" class="data-table">
            <thead style="white-space:nowrap;"></thead>
            <tbody id="tabel2_data" class="text-left" ></tbody>
          </table>
          <div class="po-rt-hint"><i class="bi bi-info-circle"></i> Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom untuk menyembunyikan kolom atau mengatur jumlah desimal.</div>
        </div>
      </div>
    </div>
  </div>

  <div class="tab-pane fade" id="profile3" role="tabpanel" aria-labelledby="profile-tab">
    <div class="row">
      <div class="col-12" style="overflow:auto;">
        <div class="container-fluid">

              <table id="tabelRetur" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>
                    <th scope="col">Profile 3</th>
                    <th scope="col">No. SSP</th>
                    <th scope="col">Tanggal</th>
                    <th scope="col">No. Out</th>
                    <th scope="col">Gudang</th>
                  </tr>
                </thead>

                <tbody id="tabelRetur_data" class="text-left" >

                </tbody>
              </table>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>
</div>
</div>

<div class="modal fade" id="modalAddPRJ" tabindex="-1" role="dialog" aria-labelledby="modalAddPRJLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalAddPRJLabel">Form SPR</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="buttonCloseModalAddPRJ()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <input type="hidden" name="noUrut" id="input_add_nourut" value="" />
        <div class="container-fluid">
          <!-- Row 1 -->
          <div class="row">
            <div class="col-2">
              <label>No Bukti</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_add_nobukti" placeholder="" disabled>
            </div>

            <div class="col-2">
              <label>No PRJ</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_add_noprj" placeholder="" disabled>
            </div>
          </div>

          <!-- Row 2 -->
          <div class="row mt-2">
            <div class="col-2">
              <label>Customer</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_add_namacustomer" placeholder="" disabled>
            </div>

            <div class="col-2">
              <label>Tanggal</label>
            </div>
            <div class="col-2">
              <input type="date" class="form-control" id="input_add_tanggal" value="{!! date('Y-m-d') !!}">
            </div>
          </div>
        </div>
      </div>

        <div class="container-fluid" style="overflow-x: auto;">

              <table id="addTable" class="table table-bordered">
                <thead class="text-center">
                  <tr>
                    <th scope="col" class="spr-col-terima">Terima</th>
                    <th scope="col">Kode Barang</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col">Sat</th>
                    <th scope="col">Sat Produk</th>
                    <th scope="col">Qty PR Jual</th>
                    <th scope="col">Qty Terima</th>
                    <th scope="col">Qty Reject</th>
                  </tr>
                </thead>

                <tbody id="addTableData" class="text-right">
                  <tr>
                      <td class="text-center" colspan="8">Belum ada data</td>
                  </tr>
                </tbody>

              </table>
        </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-lg btn-batal-add" data-dismiss="modal" onclick="buttonCloseModalAddPRJ()">Batal</button>
        <button id="buttonSubmitAdd" type="button" onclick="submitAdd()" class="btn btn-lg btn-chip-biru">Simpan</button>
      </div>
    </div>
  </div>
</div>


<div id="page3" style="display: none" class="mainpage container-fluid" >

  <div class="row">
    <div class="col-8 text-left">
      {{-- <h2>Koreksi SPR</h2> --}}
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="margin-bottom:20px; height: 30px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >CLOSE</button>
    </div>
  </div>

  <div class="container-fluid">
    <input type="hidden" name="noUrut" id="input_koreksi_nourut" value="" />
    <div class="row">
      <div class="col-md-12">
        <div class="row">

          <div class="col-md-3">
            <div class="row">
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-4">

                    <div class="form-group">
                      <label>Customer</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_koreksi_namacustomer" placeholder="" disabled>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-12" style="margin-top:-10px;">
                    <div class="form-group">
                      <textarea  style="width: 100%; resize: none" rows=3 placeholder="" class="form-control" id="input_koreksi_alamatcustomer"  disabled></textarea>
                    </div>
                  </div>
                </div>

              </div>



          </div>
          </div>

          <div class="col-md-3">
            <div class="row">

              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-4">

                    <div class="form-group">
                      <label>No Bukti</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_koreksi_nobukti" placeholder="" disabled>
                    </div>
                  </div>
                </div>
              </div>


              <div class="col-md-12" style="margin-top: -10px">
                <div class="row">
                  <div class="col-md-4">

                    <div class="form-group">
                      <label>No Inv</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_koreksi_noinv" placeholder="" disabled>
                    </div>
                  </div>
                </div>

              </div>


              <div class="col-md-12" style="margin-top: -10px">
                <div class="row">
                  <div class="col-md-4">

                    <div class="form-group">
                      <label>No SO</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_koreksi_noso" placeholder="" disabled>
                    </div>
                  </div>
                </div>

              </div>



              </div>


            </div>




          <div class="col-md-3">
            <div class="row">
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-4">

                    <div class="form-group">
                      <label>Gudang</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_koreksi_gudang" placeholder="" disabled>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-12" style="margin-top:-10px;">
                <div class="row">
                  <div class="col-md-4">

                    <div class="form-group">
                      <label>No Kend</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_koreksi_nokend" placeholder="" onblur="onChangeHeader('nopolkend' , 'input_koreksi_nokend')">
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-12" style="margin-top:-10px;">
                <div class="row">
                  <div class="col-md-4">

                    <div class="form-group">
                      <label>Sopir</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_koreksi_sopir" placeholder="" onblur="onChangeHeader('sopir' , 'input_koreksi_sopir')">
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <div class="col-md-3">
            <div class="row">
            <div class="col-md-12">
              <div class="row">
                <div class="col-md-4">

                  <div class="form-group">
                    <label>Tanggal</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="date" class="form-control text-center" id="input_koreksi_tanggal" value="{!! date('Y-m-d') !!}"  disabled>

                  </div>
                </div>
              </div>

            </div>

            <div class="col-md-12" style="margin-top:-10px;">
              <div class="row">
                <div class="col-md-4">

                  <div class="form-group">
                    <label>Catatan</label>
                  </div>
                </div>
                <div class="col-md-8" >
                  <div class="form-group">
                    <textarea  style="width: 100%; resize: none" rows=3 placeholder="" class="form-control" id="input_koreksi_catatan"  onblur="onChangeHeader('catatan' , 'input_koreksi_catatan')"></textarea>
                  </div>
                </div>

              </div>

            </div>


          </div>
          </div>

        </div>

      </div>
    </div>
    <hr/>
        <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">

              <table id="koreksiTable" class="data-table">
                <thead class="text-center">
                  <tr>
                    <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                    <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                    <th style="padding: 4px 12px;" scope="col">Nama Produk</th>
                    <th style="padding: 4px 12px;" scope="col">Sat Prd</th>
                    <th style="padding: 4px 12px;" scope="col">Qty</th>
                    <th style="padding: 4px 12px;" scope="col">Qty Reject</th>
                    <th style="padding: 4px 12px;" scope="col">Sat</th>

                    <th style="padding: 4px 12px;" scope="col">Actions</th>

                  </tr>
                </thead>


                <tbody id="koreksiTableData" class="" >
                  <tr >

                      <td colspan=8 class="text-center">Belum ada data</td>

                </tr>

                </tbody>


              </table>

    </div>
    <div class="row mt-2" style="margin-top: 0">


    </div>

    <div id="formKoreksiEdit" class="container-fluid showhideitem">
      <!-- <div class="line"></div> -->
      <!-- <div class="row"> -->

      <div class="col-12">


      <hr/>
      <div class="row">
        <div class="col-12">
          <h4>Edit Item</h4>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="row">

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
            <input id="KoreksiEditKodeBrg" type="text" class="form-control" disabled>
            <!-- <button type="button" onclick="buttonAddListBarang()" class="btn btn-primary" >+</button> -->

          </div>
        </div>

      </div>

    </div>


    </div>
    </div>







    <div class="col-md-6">
      <div class="row">
        <div class="col-md-12" style="margin-top: -10px">
          <div class="row">



        <div class="col-md-2">
          <div class="form-group">
          <label>Nama Brg</label>
        </div>
        </div>
        <div class="col-md-6">
          <input id="KoreksiEditNamaBrg" type="text" class="form-control" disabled>
        </div>

      </div>
    </div>

    <div class="col-md-12" style="margin-top: -10px">
    <div class="row">
        <div class="col-md-2">
          <div class="form-group">
          <label>Qty</label>
        </div>
        </div>

        <div class="input-group col-md-4">
          <input id="KoreksiEditInputQty" type="number" value='0.00' class="form-control text-right" style="width: 75%">

          <input id="KoreksiEditInputSat" type="text" value='PCS' class="form-control text-center" disabled style="width: 25%">



        </div>


      </div>
    </div>


    <div class="col-md-12" style="margin-top: -10px">
    <div class="row">
        <div class="col-md-2">
          <div class="form-group">
          <label>Qty Reject</label>
        </div>
        </div>

        <div class="input-group col-md-4">
          <input id="KoreksiEditInputQtyReject" type="number" value='0.00' class="form-control text-right" style="width: 75%">

          <input id="KoreksiEditInputSatReject" type="text" value='PCS' class="form-control text-center" disabled style="width: 25%">



        </div>


      </div>
    </div>





        <!-- <input type="text" class="form-control" placeholder="Email" id="demo" name="email">
    <div class="input-group-append">
      <span class="input-group-text">@example.com</span>
    </div> -->

    <div class="col-md-12" style="margin-top: -10px">
    <div class="row">

        <div class="col-md-2">
          <div class="form-group">
          <label>Retur Supp</label>
        </div>
        </div>
        <!-- <div class="col-4 text-right">

            <button type="button" onclick="buttonKoreksiListGudang()" class="btn btn-primary" >+</button>
          </div> -->
        <div class="col-md-4">
          <select id="KoreksiEditReturSupp" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" onchange="onChangePPN('ppn' , 'input_add_tipeppn')">
            <option value=0 selected>Tidak</option>
            <option value=1 >Ya</option>
          </select>
          <!-- <input id="KoreksiEditKodeGdg" type="hidden" class="form-control" disabled> -->
        </div>



      </div>
    </div>

      </div>
    </div>


    <!-- <div class="col-6 ">
      <div class="row">



      </div> -->
    <!-- </div> -->





    <div class="col-6 mt-2">
      <div class="row">


      </div>
    </div>
    </div>


      <div class="row mt-2">
        <div class="col-md-12 text-right mt-4">
          <button type="button" class="btn btn-secondary" onclick="buttonKoreksiItemBatal()" style="height: 30px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;">Batal</button>

          <button id="buttonSubmitKoreksiEdit" type="button" onclick="submitKoreksiEdit()" class="btn btn-chip-biru" style="height: 30px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;">Simpan Edit</button>
          <!-- <button id="buttonSubmitKoreksiEdit" type="button" onclick="submitKoreksiEdit()" class="btn btn-primary" >Edit</button> -->
        </div>

      </div>
      <!-- <div class="line"></div> -->
      <!-- <hr/> -->
    </div>
    </div>
  </div>
</div>



<div id="page4" style="display: none" class="mainpage container-fluid" >

  <div class="row mb-4">
    <div class="col-8 text-left">
      <h2></h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-danger btn-lg" style="height: 30px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >Close</button>
    </div>
  </div>

  <div class="container-fluid">
    <input type="hidden" name="noUrut" id="input_detailkoreksi_nourut" value="" />
    <div class="row">
      <div class="col-md-12">
        <div class="row">

          <div class="col-md-3">
            <div class="row">
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-4">

                    <div class="form-group">
                      <label>Customer</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_detailkoreksi_namacustomer" placeholder="" disabled>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-12" style="margin-top:-10px;">
                    <div class="form-group">
                      <textarea  style="width: 100%; resize: none" rows=3 placeholder="" class="form-control" id="input_detailkoreksi_alamatcustomer"  disabled></textarea>
                    </div>
                  </div>
                </div>

              </div>



          </div>
          </div>

          <div class="col-md-3">
            <div class="row">

              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-4">

                    <div class="form-group">
                      <label>No Bukti</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_detailkoreksi_nobukti" placeholder="" disabled>
                    </div>
                  </div>
                </div>
              </div>


              <div class="col-md-12" style="margin-top: -10px">
                <div class="row">
                  <div class="col-md-4">

                    <div class="form-group">
                      <label>No Inv</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_detailkoreksi_noinv" placeholder="" disabled>
                    </div>
                  </div>
                </div>

              </div>


              <div class="col-md-12" style="margin-top: -10px">
                <div class="row">
                  <div class="col-md-4">

                    <div class="form-group">
                      <label>No SO</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_detailkoreksi_noso" placeholder="" disabled>
                    </div>
                  </div>
                </div>

              </div>



              </div>


            </div>




          <div class="col-md-3">
            <div class="row">
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-4">

                    <div class="form-group">
                      <label>Gudang</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_detailkoreksi_gudang" placeholder="" disabled>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-12" style="margin-top:-10px;">
                <div class="row">
                  <div class="col-md-4">

                    <div class="form-group">
                      <label>No Kend</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_detailkoreksi_nokend" placeholder="" disabled>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-12" style="margin-top:-10px;">
                <div class="row">
                  <div class="col-md-4">

                    <div class="form-group">
                      <label>Sopir</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_detailkoreksi_sopir" placeholder="" disabled>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <div class="col-md-3">
            <div class="row">
            <div class="col-md-12">
              <div class="row">
                <div class="col-md-4">

                  <div class="form-group">
                    <label>Tanggal</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="date" class="form-control text-center" id="input_detailkoreksi_tanggal" value="{!! date('Y-m-d') !!}"  disabled>

                  </div>
                </div>
              </div>

            </div>

            <div class="col-md-12" style="margin-top:-10px;">
              <div class="row">
                <div class="col-md-4">

                  <div class="form-group">
                    <label>Catatan</label>
                  </div>
                </div>
                <div class="col-md-8" >
                  <div class="form-group">
                    <textarea  style="width: 100%; resize: none" rows=3 placeholder="" class="form-control" id="input_detailkoreksi_catatan" disabled></textarea>
                  </div>
                </div>

              </div>

            </div>


          </div>
          </div>

        </div>

      </div>
    </div>
    <hr/>
        <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">

              <table id="detailKoreksiTable" class="data-table">
                <thead class="text-center">
                  <tr>
                    <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                    <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                    <th style="padding: 4px 12px;" scope="col">Nama Produk</th>
                    <th style="padding: 4px 12px;" scope="col">Sat Prd</th>
                    <th style="padding: 4px 12px;" scope="col">Qty</th>
                    <th style="padding: 4px 12px;" scope="col">Qty Reject</th>

                    <th style="padding: 4px 12px;" scope="col">Sat</th>


                  </tr>
                </thead>


                <tbody id="detailKoreksiTableData" class="" >
                  <tr >

                      <td colspan=7 class="text-center">Belum ada data</td>

                </tr>

                </tbody>


              </table>

    </div>
    <div class="row mt-2" style="margin-top: 0">


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
let xppn = 0

/* ============ Header tabel interaktif (window.ReportTable) ============
 * Port 1:1 dari poCart/poAktifkanTabel milik purchaseOrder.blade.php, sama
 * seperti so/invoicejasa/fakturpajak/cetaktandaterima/perintahreturjual.
 */
let rpgCart = { 1 : [], 2 : [] }
let rpgActiveUrut = 0
const RPG_HREF = 'returpenjualangudang'
const RPG_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date', 3 : 'bool' }
const RPG_TIPE_KODE = { varchar : 0, float : 1, date : 2, bool : 3 }
let rpgPerluGambar = { 1 : false, 2 : false }

function rpgPickCI (row, key) {
  if (!row) { return undefined; }
  if (row[key] !== undefined) { return row[key]; }
  let lower = key.toLowerCase();
  for (let k in row) { if (k.toLowerCase() === lower) { return row[k]; } }
  return undefined;
}

function rpgDefaultCart (urut) {
  if (urut === 1) {
    return [
      ['NoBukti',      'No. Bukti', 1, 'varchar', 0, 0],
      ['Tanggal',      'Tanggal',   1, 'date',    0, 0],
      ['NAMACUSTSUPP', 'Nama Cust', 1, 'varchar', 0, 0],
    ]
  }
  // urut 2: Transaksi Retur Gudang -- gabungan kolom tab lama "Belum Otorisasi"
  // + "Sudah Otorisasi" (OtoUser1/TglOto1), sejak keduanya digabung jadi satu tabel.
  return [
    ['NoBukti',      'No. Bukti',  1, 'varchar', 01, 0],
    ['Tanggal',      'Tanggal',    1, 'date',    0, 0],
    ['NAMACUSTSUPP', 'Nama Cust',  1, 'varchar', 0, 0],
    ['Noinv',        'No Invoice', 1, 'varchar', 0, 0],
    ['NOSO',         'No SO',      1, 'varchar', 0, 0],
    ['IDUser',       'User',       1, 'varchar', 0, 0],
    ['OtoUser1',     'User Oto',   1, 'varchar', 0, 0],
    ['TglOto1',      'Tgl Oto',    1, 'date',    0, 0],
    ['xstatus',      'Status',    1, 'varchar',    0, 0]
  ]
}

function rpgBuatCart (headers, values, isnumerics, isshowns, desimals) {
  headers = headers || []
  let cart = []
  headers.forEach((h, i) => {
    let tipe = Number(isnumerics[i]) || 0
    let des = (desimals && desimals[i] !== undefined && desimals[i] !== null && desimals[i] !== '')
      ? Number(desimals[i]) : (tipe === 1 ? 2 : 0)
    cart.push([values[i], h, Number(isshowns[i]) === 1 ? 1 : 0, RPG_TIPE_NAMA[tipe] || 'varchar', 0, isNaN(des) ? 0 : des])
  });
  return cart
}

function rpgAktifkanTabel (urut) {
  rpgActiveUrut = urut
  window.g_modeReport = urut
  window.gcart_header = rpgCart[urut]
}

function rpgOnChangeAktif () {
  if (rpgActiveUrut === 2) { reinitTabel2(); } else { reinitTabel(); }
}

window.g_href = RPG_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function (href, mode) {
  let urut = mode || 1
  let cart = rpgCart[urut] || []
  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[1]); value.push(c[0]); isnumber.push(RPG_TIPE_KODE[c[3]] ?? 0)
    isshown.push(Number(c[2]) === 1 ? 1 : 0); desimal.push(Number(c[5]) || 0)
  });
  $.ajax({
    url: "{!! url('saveheadertable') !!}", type: "post", async: false,
    data: {
      _token: $("#_token").val(), header: JSON.stringify(header), isnumber: JSON.stringify(isnumber),
      tipe: JSON.stringify(desimal), value: JSON.stringify(value), isshown: JSON.stringify(isshown),
      href: RPG_HREF, urut: urut
    },
    error: function (err) { console.log(err); alertify.warning('Gagal menyimpan pengaturan kolom') }
  })
}

window.doSetHeader = function (mode, reset) {
  let urut = mode || 1
  $.ajax({
    url: "{!! url('getheadertable') !!}", type: "post", async: false,
    data: { _token: $("#_token").val(), href: RPG_HREF, urut: urut, reset: reset ? 1 : 0 },
    success: function (res) {
      if (!reset && res && res.headertableheader && res.headertableheader.length) {
        rpgCart[urut] = rpgBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal || [])
      } else {
        rpgCart[urut] = rpgDefaultCart(urut)
        window.gcart_header = rpgCart[urut]
        window.doSimpanHeader(RPG_HREF, urut)
      }
      window.gcart_header = rpgCart[urut]
    },
    error: function (err) {
      console.log(err)
      alertify.warning(reset ? 'Gagal mengembalikan kolom ke tampilan default' : 'Gagal memuat pengaturan kolom')
      rpgCart[urut] = rpgDefaultCart(urut)
      window.gcart_header = rpgCart[urut]
    }
  })
}

function activeVisibleTabKeyRPG () {
  if ($('#nav-profile-tab').hasClass('active')) { return 2; }
  return 1;
}

const RPG_SELEKTOR_TABEL_AKTIF = '#myTabContent .tab-pane.active table.data-table'
const RPG_SELEKTOR_BAR_AKTIF = '#myTabContent .tab-pane.active [id^="rtBarTabel"]'

let rpgRtSudahInit = false
function rpgInitReportTableSekali () {
  if (rpgRtSudahInit || typeof ReportTable === 'undefined') { return }
  rpgRtSudahInit = true
  let urutAktif = activeVisibleTabKeyRPG()
  let idTabel = { 1 : '#tabel', 2 : '#tabel2' }
  let idBar = { 1 : '#rtBarTabel', 2 : '#rtBarTabel2' }
  Object.keys(idTabel).forEach((u) => {
    if (Number(u) === urutAktif) { return }
    ReportTable.init({ table: idTabel[u], bar: idBar[u], onChange: rpgOnChangeAktif })
  });
  ReportTable.init({ table: RPG_SELEKTOR_TABEL_AKTIF, bar: RPG_SELEKTOR_BAR_AKTIF, onChange: rpgOnChangeAktif })

  let rpgGuardUlangKlik = false;
  ['#tabel', '#tabel2'].forEach((sel) => {
    let thead = document.querySelector(sel + ' thead')
    if (!thead) { return }
    thead.addEventListener('click', function (e) {
      if (rpgGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }
      e.stopPropagation()
      e.preventDefault()
      rpgGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles: false, cancelable: true, view: window })
      Object.defineProperty(ulang, 'target', { value: interaktif, configurable: true })
      thead.dispatchEvent(ulang)
      rpgGuardUlangKlik = false
    }, true)
  });
}

function tulisTheadHeaderRPG (tableSel, cols, withActions) {
  let thead = document.querySelector(tableSel + ' thead')
  if (!thead || !window.ReportTable) { return; }
  let headRowHtml = ReportTable.headHtml(cols)
  if (withActions) { headRowHtml = headRowHtml.replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>'); }
  thead.setAttribute('style', 'white-space:nowrap;');
  thead.innerHTML = headRowHtml;
}

// Belum=merah, Sebagian=kuning, Selesai=biru -- sama seperti badge status xstatus
// di perintahreturjual.blade.php, pakai .sp-badge yang sudah dimuat lewat
// po-table-header.css. queryPenerimaan() di ReturPenjualanGudangController
// menghasilkan xstatus dengan nilai string yang sama persis (Belum/Sebagian/Selesai).
const RPG_BADGE_STATUS = {
  'Belum':    'is-inactive',
  'Sebagian': 'is-supervisor',
  'Selesai':  'is-user',
}

function rpgValueCell (row, col) {
  let raw = rpgPickCI(row, col[0]);
  let type = col[3];

  if (col[0] === 'xstatus') {
    let kelas = RPG_BADGE_STATUS[raw] || '';
    return '<td><span class="sp-badge ' + kelas + '">' + (raw || '') + '</span></td>';
  }

  if (type === 'date') { if (!raw) { return '<td></td>'; } return '<td>' + formatDate(raw, '/') + '</td>'; }
  if (type === 'float') {
    let dp = Number(col[5]) || 0;
    let n = (raw !== undefined && raw !== null && raw !== '') ? Number(raw) : 0;
    return '<td class="text-right">' + n.toLocaleString('id-ID', { minimumFractionDigits: dp, maximumFractionDigits: dp }) + '</td>';
  }
  return '<td>' + (raw !== undefined && raw !== null ? raw : '') + '</td>';
}

// Actions utk tabel "Outstanding PRJ" -- buttonAdd() sudah ada & cocok (dipakai
// utk bikin dokumen Retur Gudang baru dari baris PRJ ini). buttonDetail() adalah
// wrapper tipis di atas buttonAdd() yang sama (data source-nya identik & benar
// utk baris PRJ), tapi menyembunyikan tombol submit di form-nya supaya cuma
// dipakai utk lihat detail barangnya, bukan bikin dokumen baru.
function tabelActionsCell (row) {
  let nobukti = rpgPickCI(row, 'NoBukti');
  let namacustsupp = rpgPickCI(row, 'NAMACUSTSUPP');
  let ppn = rpgPickCI(row, 'PPN');
  let html = '<td class="text-center"><div class="action-buttons-wrap">';
  html += '<button class="btn btn-warning btn-sm" title="Detail" type="button" onclick="buttonDetail(\'' + nobukti + '\' , \'' + (namacustsupp || '').replace(/'/g, "\\'") + '\' , ' + Number(ppn || 0) + ')"><i class="bi bi-info"></i></button>';
  html += '<button class="btn btn-primary btn-sm" title="Tambah" type="button" onclick="buttonAdd(\'' + nobukti + '\' , \'' + (namacustsupp || '').replace(/'/g, "\\'") + '\' , ' + Number(ppn || 0) + ')"><i class="bi bi-plus"></i></button>';
  html += '</div></td>';
  return html;
}

function tabel2ActionsCell (row) {
  let nobukti = rpgPickCI(row, 'NoBukti');
  let isOto = Number(rpgPickCI(row, 'IsOtorisasi1'));
  let html = '<td class="text-center"><div class="action-buttons-wrap">';
  html += '<button class="btn btn-warning btn-sm" title="Detail" type="button" onclick="buttonDetailKoreksi(\'' + nobukti + '\')"><i class="bi bi-info"></i></button>';
  if (isOto) {
    html += '<button class="btn btn-danger btn-sm" title="Batal Otorisasi" type="button" onclick="buttonBatalOtorisasiPenerimaan(\'' + nobukti + '\' , \'edit\')"><i class="bi bi-key"></i></button>';
  } else {
    // Edit cuma muncul selagi belum diotorisasi -- ini pintu masuk ke form koreksi
    // (#page3, buttonKoreksi) yang tabel barangnya sudah punya tombol hapus per-item
    // (buttonKoreksiDeleteItem, choice 'D' ke sp_TransSPBRJUAL) utk mengembalikan
    // qty barang itu jadi outstanding lagi di tabel "Outstanding PRJ".
    html += '<button class="btn btn-success btn-sm" title="Edit" type="button" onclick="buttonKoreksi(\'' + nobukti + '\')"><i class="bi bi-pen"></i></button>';
    html += '<button class="btn btn-primary btn-sm" title="Otorisasi" type="button" onclick="buttonOtorisasiPenerimaan(\'' + nobukti + '\' , \'add\')"><i class="bi bi-key"></i></button>';
  }
  html += '</div></td>';
  return html;
}

function renderTabelRows (rows) {
  let cols = (rpgCart[1].length ? rpgCart[1] : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  (rows || []).forEach(function (row) {
    html += '<tr>' + tabelActionsCell(row);
    cols.forEach(function (col) { html += rpgValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel_data').innerHTML = html;
  tulisTheadHeaderRPG('#tabel', cols, true);
}

function renderTabel2Rows (rows) {
  let cols = (rpgCart[2].length ? rpgCart[2] : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  (rows || []).forEach(function (row) {
    html += '<tr>' + tabel2ActionsCell(row);
    cols.forEach(function (col) { html += rpgValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel2_data').innerHTML = html;
  tulisTheadHeaderRPG('#tabel2', cols, true);
}

let lastTabelRows = []
let lastTabel2Rows = []
let rpgPanjangHalaman = { 1 : 10, 2 : 10 }

function rpgIkatSearch (urut) {
  let ids = { 1 : ['rpgSearch1', 'tabel'], 2 : ['rpgSearch2', 'tabel2'] }
  let input = document.getElementById(ids[urut][0])
  let idTabel = ids[urut][1]
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'
  let timer = null
  input.addEventListener('input', function () {
    let nilai = input.value
    if (timer) { clearTimeout(timer) }
    timer = setTimeout(function () {
      if ($.fn.DataTable.isDataTable('#' + idTabel)) { $('#' + idTabel).DataTable().search(nilai).draw() }
    }, 400)
  })
}

function rpgIkatPanjangHalaman (urut) {
  let ids = { 1 : ['rpgLen1', 'tabel'], 2 : ['rpgLen2', 'tabel2'] }
  let sel = document.getElementById(ids[urut][0])
  let idTabel = ids[urut][1]
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(rpgPanjangHalaman[urut])
  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    rpgPanjangHalaman[urut] = (n === -1 || n > 0) ? n : 10
    if ($.fn.DataTable.isDataTable('#' + idTabel)) { $('#' + idTabel).DataTable().page.len(rpgPanjangHalaman[urut]).draw() }
  })
}

const RPG_DOM_STRING = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"

function reinitTabel () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().destroy(); }
    renderTabelRows(lastTabelRows);
    $('#tabel').DataTable({ dom: RPG_DOM_STRING, lengthChange: false, pageLength: rpgPanjangHalaman[1], paging: true, order: [[0, 'asc']], ordering: false });
    rpgIkatSearch(1); rpgIkatPanjangHalaman(1); rpgPerluGambar[1] = false;
  } catch (e) { console.error('reinitTabel failed:', e); alertify.error('Gagal memperbarui tabel: ' + e.message); }
}

function reinitTabel2 () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel2')) { $('#tabel2').DataTable().destroy(); }
    renderTabel2Rows(lastTabel2Rows);
    $('#tabel2').DataTable({ dom: RPG_DOM_STRING, lengthChange: false, pageLength: rpgPanjangHalaman[2], paging: true, order: [[1, 'asc']], ordering: false });
    rpgIkatSearch(2); rpgIkatPanjangHalaman(2); rpgPerluGambar[2] = false;
  } catch (e) { console.error('reinitTabel2 failed:', e); alertify.error('Gagal memperbarui tabel: ' + e.message); }
}

function rpgResetFilterFields () {
  $('#input_filterstatus').val('0')
  $('#input_filteroto').val('0')
}

function rpgUpdateFilterBadge () {
  let n = 0
  if ((Number($('#input_filterstatus').val()) || 0) !== 0) { n++ }
  if ((Number($('#input_filteroto').val()) || 0) !== 0) { n++ }
  $('#sprFilterBadge').text(n + ' aktif')
}

function buttonFilterSPR () {
  let tglawal = $('#input_tanggalawal_spr').val()
  let tglakhir = $('#input_tanggalakhir_spr').val()
  let filterstatus = $('#input_filterstatus').val()
  let filteroto = $('#input_filteroto').val()
  $.ajax({
    url: "{!! url('returpenjualangudangloadall') !!}",
    type: "get", async: false,
    data: { tglawal, tglakhir, filterstatus, filteroto },
    success: function (res) {
      lastTabel2Rows = res.tempPenerimaan
      reinitTabel2()
      rpgUpdateFilterBadge()
    },
    error: function (err) { console.log(err); alertify.warning('Terjadi kesalahan silahkan refresh browser') }
  })
}

function onChangePeriodeSPR () {
  let tglawal = $('#input_tanggalawal_spr').val()
  let tglakhir = $('#input_tanggalakhir_spr').val()
  if (tglawal && tglakhir && tglawal > tglakhir) {
    alertify.warning('Tanggal awal tidak boleh lebih besar dari tanggal akhir')
    return
  }
  buttonFilterSPR()
}

function onChangePeriodePRJ () {
  let tglawal = $('#input_tanggalawal_prj').val()
  let tglakhir = $('#input_tanggalakhir_prj').val()
  if (tglawal && tglakhir && tglawal > tglakhir) {
    alertify.warning('Tanggal awal tidak boleh lebih besar dari tanggal akhir')
    return
  }
  loadAll()
}

$(document).ready(function(){
      rpgAktifkanTabel(1); window.doSetHeader(1, false);
      lastTabelRows = @json($tempOutstanding);
      reinitTabel();

      rpgAktifkanTabel(2); window.doSetHeader(2, false);
      lastTabel2Rows = @json($tempPenerimaan);
      reinitTabel2();

      rpgInitReportTableSekali();

      $('#nav-home-tab').on('shown.bs.tab', function () { rpgAktifkanTabel(1); if (typeof ReportTable !== 'undefined') { ReportTable.refresh(); } if (rpgPerluGambar[1]) { reinitTabel(); } });
      $('#nav-profile-tab').on('shown.bs.tab', function () { rpgAktifkanTabel(2); if (typeof ReportTable !== 'undefined') { ReportTable.refresh(); } if (rpgPerluGambar[2]) { reinitTabel2(); } });

  //   formAddListItem
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
      url: "{!! url('returpenjualangudangonchangeheader') !!}",
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
          alertify.success(`${field} sudah diupdate`)
        }


      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })

}


function buttonKoreksiEditItem (i) {
  let barang = dataTableKoreksi[i]
  barangKoreksiEdit = barang
  console.log(barang)
  document.getElementById("KoreksiEditKodeBrg").value = barang.KodeBrg
  document.getElementById("KoreksiEditNamaBrg").value = barang.NAMABRG
  document.getElementById("KoreksiEditInputQty").value = barang.QNT ? parseFloat(barang.QNT).toFixed(2) : '0.00'
  document.getElementById("KoreksiEditInputQtyReject").value = barang.QNTREJECT ? parseFloat(barang.QNTREJECT).toFixed(2) : '0.00'
  document.getElementById("KoreksiEditInputSat").value = barang.NOSAT == 1 ? barang.SAT_1 : barang.SAT_2
  document.getElementById("KoreksiEditInputSatReject").value = barang.NOSAT == 1 ? barang.SAT_1 : barang.SAT_2
  document.getElementById("KoreksiEditReturSupp").value = barang.FlagKembali
  $('#formKoreksiEdit').show();
}


function refreshDataTableKoreksi (nobukti) {

  let _token  = $("#_token").val()
  $.ajax({
    url: "{!! url('returpenjualangudanggetdetailpenerimaan') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: nobukti
    },
    success: function(res) {
      console.log('aaa')
      console.log('res' , res)

      // res.header.forEach((item, i) => {
      //   console.log('a' , i)
      // });
      if (res.length == 0) {
        buttonCloseForm()
        alertify.warning("Data habis")
      } else {
        dataTableKoreksi = res

        let rowTable = ""

        res.forEach((item, i) => {
          rowTable += `<tr>
          <td>${item.KodeBrg}</td>
          <td>${item.NAMABRG}</td>
          <td>${item.Namabrg}</td>
          <td>${item.SATX}</td>

          <td class="text-right">${formatAngkaX(item.QNT)}</td>
          <td class="text-right">${formatAngkaX(item.QNTREJECT)}</td>
          <td>${item.NOSAT == 1 ? item.SAT_1 : item.SAT_2}</td>

          <td class="text-center">
          <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksiEditItem(${i})"><i class="bi bi-pen"></i></button>
           <button class="btn btn-danger btn-sm" type="button" onclick="buttonKoreksiDeleteItem(${i})"><i class="bi bi-trash"></i></button></td>

          </tr>`
        });

        // <td>${item.KodeGdg}</td>
        // <td class="text-right">${item.QNT1 ? parseFloat(item.QNT1).toFixed(2) : '0.00'}</td>
        // <td>${item.SAT_1}</td>
        // <td class="text-right">${item.QNT2 ? parseFloat(item.QNT2).toFixed(2) : '0.00'}</td>
        // <td>${item.SAT_2}</td>

        // if(!dataTableAdd.length) {
        //   rowTable = `<tr>
        //   <td class="text-center" colspan="8">Belum ada barang</td>
        //   </tr>`
        // }
        document.getElementById("koreksiTableData").innerHTML = rowTable

        document.getElementById("input_koreksi_namacustomer").value = dataTableKoreksi[0].NAMACUSTSUPP
        document.getElementById("input_koreksi_alamatcustomer").value = dataTableKoreksi[0].ALAMAT1
        document.getElementById("input_koreksi_nobukti").value = dataTableKoreksi[0].NoBukti

        document.getElementById("input_koreksi_gudang").value = dataTableKoreksi[0].KodeGdg

        document.getElementById("input_koreksi_noinv").value = dataTableKoreksi[0].Noinv
        document.getElementById("input_koreksi_catatan").value = dataTableKoreksi[0].CATATAN
        document.getElementById("input_koreksi_nokend").value = dataTableKoreksi[0].NoPolKend
        document.getElementById("input_koreksi_sopir").value = dataTableKoreksi[0].Sopir
        document.getElementById("input_koreksi_noso").value = dataTableKoreksi[0].NOSO
        buttonKoreksiItemBatal()
      }



      // res.list.forEach((item, i) => {
      //   console.log('b' , i)
      // });



    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}


function buttonKoreksiDeleteItem (i) {
  console.log(i)
  let barang = dataTableKoreksi[i]

  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + barang.NAMABRG + ' ?',
      function() {
        let _token  = $("#_token").val()
        let choice = "D"
        let qntTerima = 0
        let qntReject = 0
        let retursupp = 0

        let nobukti = barang.NoBukti
        let urut = barang.Urut
        let namabrg = barang.NAMABRG
        let kodebrg = barang.KodeBrg
        let kodegdg = barang.KodeGdg
        let nosat = barang.NOSAT
        let isi = barang.ISI

        let sat1 = barang.SAT_1
        let sat2 = barang.SAT_2

        let qnt2 = qntTerima
        let qnt2reject = qntReject
        let qnt1 = qntTerima * Number(barang.ISI)
        let qnt1Reject = qntReject * Number(barang.ISI)



        console.log({choice,
        qntTerima,
        qntReject,
        qnt1,
        qnt2,
        qnt2reject,
        qnt1Reject,
        namabrg,
        nobukti,
        urut,
        nosat,
        isi,
        sat1,
        sat2})

        $.ajax({
            url: "{!! url('returpenjualangudangspkoreksi') !!}",
            type: "post",
            async: false,
            data: {
              _token : _token,
              choice,
              qntTerima,
              qntReject,
              qnt1,
              qnt2,
              qnt2reject,
              qnt1Reject,
              namabrg,
              nobukti,
              urut,
              nosat,
              isi,
              sat1,
              sat2,
              namabrg,
              kodebrg,
              kodegdg,
              retursupp

            },
            success: function(res) {
              console.log(res ,'!')

              if (res == 1) {
                // $("#form").modal('toggle')
                loadAll()
                refreshDataTableKoreksi(nobukti)
                alertify.success('Item telah dihapus');

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

function buttonOtorisasiPenerimaan (nobukti) {
  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  alertify.confirm('Otorisasi', 'Otorisasi Retur Gudang ' + nobukti + ' ?',
      function() {
        let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('returpenjualangudangspotorisasi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti

          },
          success: function(res) {
            console.log(res)
            alertify.success('Berhasil update otorisasi')
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
    });
}

function buttonBatalOtorisasiPenerimaan (nobukti) {
  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }


  alertify.prompt("Masukkan keterangan batal otorisasi nomor   " + nobukti, "",
  function(evt, value) {
    // alertify.success("You entered: " + value);
    let xpket = value;
        let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('returpenjualangudangspbatalotorisasi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti,
          pket :value

          },
          success: function(res) {
            console.log(res)
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


function submitKoreksiEdit () {
  let _token  = $("#_token").val()
  let barang = barangKoreksiEdit
  let choice = "U"
  let qntTerima = $("#KoreksiEditInputQty").val()
  let qntReject = $("#KoreksiEditInputQtyReject").val()
  let retursupp = $("#KoreksiEditReturSupp").val()

  let nobukti = barang.NoBukti
  let urut = barang.Urut
  let namabrg = barang.NAMABRG
  let kodebrg = barang.KodeBrg
  let kodegdg = barang.KodeGdg
  let nosat = barang.NOSAT
  let isi = barang.ISI

  let sat1 = barang.SAT_1
  let sat2 = barang.SAT_2

  let qnt2 = qntTerima
  let qnt2reject = qntReject
  let qnt1 = qntTerima * Number(barang.ISI)
  let qnt1Reject = qntReject * Number(barang.ISI)
  if (Number(qntTerima) > Number(barang.QntSisa) + Number(barang.QNT)) {
    alertify.warning("Qty melebihi qty sisa")
    return
  }
  if (Number(qntReject) > Number(barang.QntSisa) + Number(barang.QNT)) {
    alertify.warning("Qty melebihi qty sisa")
    return
  }


  if(Number(qntTerima)  < 0 || Number(qntReject)  < 0) {
    alertify.warning("Qty kurang dari 0")
    return
  }

  console.log({choice,
  qntTerima,
  qntReject,
  qnt1,
  qnt2,
  qnt2reject,
  qnt1Reject,
  namabrg,
  nobukti,
  urut,
  nosat,
  isi,
  sat1,
  sat2})

  $.ajax({
      url: "{!! url('returpenjualangudangspkoreksi') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        choice,
        qntTerima,
        qntReject,
        qnt1,
        qnt2,
        qnt2reject,
        qnt1Reject,
        namabrg,
        nobukti,
        urut,
        nosat,
        isi,
        sat1,
        sat2,
        namabrg,
        kodebrg,
        kodegdg,
        retursupp

      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          // $("#form").modal('toggle')
          loadAll()
          refreshDataTableKoreksi(nobukti)
          alertify.success('Item telah dikoreksi');

        }


      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })


}

function buttonKoreksiItemBatal () {

  $('.showhideitem').hide();
}


function buttonKoreksi (nobukti) {

let pcekglobal = 0
  $.ajax({
    url: "{!! url('ceklockperiode') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      if (res.length ) {
        pcekglobal = 1
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

if (pcekglobal) {
  alertify.warning("Periode sudah dikunci")
  return
}


  console.log('buttonKoreksi' , nobukti)
  let akses = $("#akses_iskoreksi").val();

  $('.showhideitem').hide();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  let _token  = $("#_token").val()


  $.ajax({
    url: "{!! url('returpenjualangudanggetdetailpenerimaan') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: nobukti
    },
    success: function(res) {
      console.log('aaa')
      console.log('res' , res)

      // res.header.forEach((item, i) => {
      //   console.log('a' , i)
      // });
      if (res.length == 0) {
        alertify.warning("Data tidak ditemukkan")
      } else if (res[0].IsOtorisasi1 == 1) {

        alertify.warning("Data sudah diotorisasi")
      } else {
        dataTableKoreksi = res





        let rowTable = ""

        res.forEach((item, i) => {
          rowTable += `<tr>
          <td>${item.KodeBrg}</td>
          <td>${item.NAMABRG}</td>
          <td>${item.Namabrg}</td>
          <td>${item.SATX}</td>

          <td class="text-right">${formatAngkaX(item.QNT)}</td>
          <td class="text-right">${formatAngkaX(item.QNTREJECT)}</td>

          <td>${item.NOSAT == 1 ? item.SAT_1 : item.SAT_2}</td>

          <td class="text-center">
          <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksiEditItem(${i})"><i class="bi bi-pen"></i></button>
           <button class="btn btn-danger btn-sm" type="button" onclick="buttonKoreksiDeleteItem(${i})"><i class="bi bi-trash"></i></button></td>

          </tr>`
        });

        // if(!dataTableAdd.length) {
        //   rowTable = `<tr>
        //   <td class="text-center" colspan="8">Belum ada barang</td>
        //   </tr>`
        // }
        document.getElementById("koreksiTableData").innerHTML = rowTable

        document.getElementById("input_koreksi_namacustomer").value = dataTableKoreksi[0].NAMACUSTSUPP
        document.getElementById("input_koreksi_alamatcustomer").value = dataTableKoreksi[0].ALAMAT1
        document.getElementById("input_koreksi_nobukti").value = dataTableKoreksi[0].NoBukti

        document.getElementById("input_koreksi_gudang").value = dataTableKoreksi[0].KodeGdg

        document.getElementById("input_koreksi_noinv").value = dataTableKoreksi[0].Noinv
        document.getElementById("input_koreksi_catatan").value = dataTableKoreksi[0].CATATAN
        document.getElementById("input_koreksi_nokend").value = dataTableKoreksi[0].NoPolKend
        document.getElementById("input_koreksi_sopir").value = dataTableKoreksi[0].Sopir
        document.getElementById("input_koreksi_noso").value = dataTableKoreksi[0].NOSO
        $('.mainpage').hide();
        $('#page3').show();
      }



      // res.list.forEach((item, i) => {
      //   console.log('b' , i)
      // });




    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function buttonDetailKoreksi (nobukti) {

let pcekglobal = 0
  $.ajax({
    url: "{!! url('ceklockperiode') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      if (res.length ) {
        pcekglobal = 1
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

if (pcekglobal) {
  alertify.warning("Periode sudah dikunci")
  return
}


  $('.showhideitem').hide();

  let _token  = $("#_token").val()


  $.ajax({
    url: "{!! url('returpenjualangudanggetdetailpenerimaan') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: nobukti
    },
    success: function(res) {
      console.log('aaa')
      console.log('res' , res)

      // res.header.forEach((item, i) => {
      //   console.log('a' , i)
      // });
      if (res.length == 0) {
        alertify.warning("Data tidak ditemukkan")
      }  else {
        // dataTableKoreksi = res

        let rowTable = ""

        res.forEach((item, i) => {
          rowTable += `<tr>
          <td>${item.KodeBrg}</td>
          <td>${item.NAMABRG}</td>
          <td>${item.Namabrg}</td>
          <td>${item.SATX}</td>

          <td class="text-right">${formatAngkaX(item.QNT)}</td>
          <td class="text-right">${formatAngkaX(item.QNTREJECT)}</td>

          <td>${item.NOSAT == 1 ? item.SAT_1 : item.SAT_2}</td>



          </tr>`
        });

        // if(!dataTableAdd.length) {
        //   rowTable = `<tr>
        //   <td class="text-center" colspan="8">Belum ada barang</td>
        //   </tr>`
        // }
        document.getElementById("detailKoreksiTableData").innerHTML = rowTable

        document.getElementById("input_detailkoreksi_namacustomer").value = res[0].NAMACUSTSUPP
        document.getElementById("input_detailkoreksi_alamatcustomer").value = res[0].ALAMAT1
        document.getElementById("input_detailkoreksi_nobukti").value = res[0].NoBukti

        document.getElementById("input_detailkoreksi_gudang").value = res[0].KodeGdg

        document.getElementById("input_detailkoreksi_noinv").value = res[0].Noinv
        document.getElementById("input_detailkoreksi_catatan").value = res[0].CATATAN
        document.getElementById("input_detailkoreksi_nokend").value = res[0].NoPolKend
        document.getElementById("input_detailkoreksi_sopir").value = res[0].Sopir
        document.getElementById("input_detailkoreksi_noso").value = res[0].NOSO
        $('.mainpage').hide();
        $('#page4').show();
      }



      // res.list.forEach((item, i) => {
      //   console.log('b' , i)
      // });




    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function loadAll () {
  let tglawal = $('#input_tanggalawal_spr').val()
  let tglakhir = $('#input_tanggalakhir_spr').val()
  let filterstatus = $('#input_filterstatus').val()
  let filteroto = $('#input_filteroto').val()
  let tglawalprj = $('#input_tanggalawal_prj').val()
  let tglakhirprj = $('#input_tanggalakhir_prj').val()
  $.ajax({
    url: "{!! url('returpenjualangudangloadall') !!}",
    type: "get", async: false, data: { tglawal, tglakhir, filterstatus, filteroto, tglawalprj, tglakhirprj },
    success: function(res) {
      lastTabelRows = res.tempOutstanding;
      lastTabel2Rows = res.tempPenerimaan;
      reinitTabel();
      reinitTabel2();
    }})
}


function buttonAdd (nobukti , namacustsupp , ppn, mode = 'add') {

let pcekglobal = 0
  $.ajax({
    url: "{!! url('ceklockperiode') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      if (res.length ) {
        pcekglobal = 1
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

if (pcekglobal) {
  alertify.warning("Periode sudah dikunci")
  return
}


    console.log('buttonAdd' , nobukti)
    let akses = $("#akses_istambah").val();

    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }


      if (ppn && Number(ppn) == 1) {
        setNewNoBukti(1)
        xppn =1

      } else {
        setNewNoBukti(0)
        xppn =0

      }


    let _token  = $("#_token").val()
    $.ajax({
      url: "{!! url('returpenjualangudanggetdetail') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti: nobukti
      },
      success: function(res) {
        console.log('aaa')
        console.log('res' , res)

        // res.header.forEach((item, i) => {
        //   console.log('a' , i)
        // });
        if (res.length == 0) {
          alertify.warning("Data tidak ditemukkan")
        } else {
          $('#buttonSubmitAdd').show()
          dataTableAdd = res

          let rowTable = ""

          $('#addTable thead th.spr-col-terima').toggle(mode !== 'detail')

          res.forEach((item, i) => {
            let qnt = item.QNT ? parseFloat(item.QNT).toFixed(2) : '0.00'
            let qntsisa = item.QntSisa ? parseFloat(item.QntSisa).toFixed(2) : '0.00'
            // Detail mode: QntSisa dari server sudah = QNT - qty yang sudah diterima,
            // jadi itu yang seharusnya tampil di kolom "Qty PR Jual" (sisa yang masih
            // outstanding), sedangkan "Qty Terima" adalah bagian yang sudah diterima
            // (QNT - QntSisa) -- sebelumnya QntSisa malah ditaruh langsung di kolom
            // "Qty Terima" dan "Qty PR Jual" menampilkan QNT mentah, jadi tidak sinkron.
            let qntPrJualCol = mode === 'detail' ? qntsisa : qnt
            let qntSudahTerima = mode === 'detail' ? (parseFloat(qnt) - parseFloat(qntsisa)).toFixed(2) : qntsisa
            let terimaCol = mode === 'detail' ? '' : `<td class="text-center"><input class="" type="checkbox" value="" id="add_checkbox${i}"></td>`
            let qntTerimaCol = mode === 'detail'
              ? `<td class="text-right">${qntSudahTerima}</td>`
              : `<td class="text-center"><input onchange="checkRejectVsTerima(${i})" id="input_add_qntTerima${i}" style="width: 100px;" class="text-right" type="number" min=0 value=${qntsisa}></td>`
            let qntRejectCol = mode === 'detail'
              ? `<td class="text-right">0.00</td>`
              : `<td class="text-center"><input onchange="checkRejectVsTerima(${i})" id="input_add_qntReject${i}" style="width: 100px;" class="text-right" type="number" min=0 value='0.00'></td>`
            rowTable += `<tr>
            ${terimaCol}
            <td>${item.KODEBRG}</td>
            <td>${item.NAMABRG}</td>
            <td>${item.SATUAN}</td>
            <td></td>
            <td class="text-right">${qntPrJualCol}</td>
            ${qntTerimaCol}
            ${qntRejectCol}
            </tr>`
          });

          // if(!dataTableAdd.length) {
          //   rowTable = `<tr>
          //   <td class="text-center" colspan="8">Belum ada barang</td>
          //   </tr>`
          // }
          document.getElementById("addTableData").innerHTML = rowTable

          document.getElementById("input_add_namacustomer").value = namacustsupp
          document.getElementById("input_add_noprj").value = nobukti
            //
          // document.getElementById("input_add_nobukti").value = dataHeaderAdd.NoBukti
          // document.getElementById("input_add_namapelanggan").value = dataHeaderAdd.NamaCust
          // document.getElementById("input_add_kodepelanggan").value = dataHeaderAdd.KodeCUST
          // document.getElementById("input_add_alamatpelanggan").value = dataHeaderAdd.ALAMAT
          // document.getElementById("input_add_kodesales").value = dataHeaderAdd.kodesls
          // document.getElementById("input_add_namasales").value = dataHeaderAdd.NamaSls
          // document.getElementById("input_add_kodepic").value = dataHeaderAdd.kodePF
          // document.getElementById("input_add_namapic").value = dataHeaderAdd.NamaPF
          // document.getElementById("input_add_valas").value = dataHeaderAdd.KodeVls
          // document.getElementById("input_add_kurs").value = dataHeaderAdd.Kurs
          //


          $('#modalAddPRJ').modal('show');
        }



        // res.list.forEach((item, i) => {
        //   console.log('b' , i)
        // });



      },
      error: function (err) {
        console.log(err)
        console.log(err.status)
        console.log(err.statusText)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })

}

// Detail (view-only) utk baris "Outstanding PRJ" -- pakai data fetch yang sama
// persis dengan buttonAdd() (satu-satunya sumber data yang cocok utk baris
// ini), tapi sembunyikan tombol submit-nya supaya cuma dipakai utk melihat
// rincian barang, bukan bikin dokumen Retur Gudang baru.
function buttonDetail (nobukti, namacustsupp, ppn) {
  buttonAdd(nobukti, namacustsupp, ppn, 'detail')
  $('#buttonSubmitAdd').hide()
}

// Validasi live per-baris di Form SPR: Qty Reject tidak boleh melebihi Qty
// Terima. Dipasang di onchange kedua input (Terima & Reject) supaya
// pengecekan jalan begitu salah satu nilainya diubah, bukan menunggu submit.
function checkRejectVsTerima (i) {
  let terima = Number($(`#input_add_qntTerima${i}`).val()) || 0
  let reject = Number($(`#input_add_qntReject${i}`).val()) || 0
  if (reject > terima) {
    alertify.warning("QTY Reject tidak boleh melebihi qty terima")
  }
}

function submitAdd () {
  let checkDate = new Date($("#input_add_tanggal").val())
  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value


  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }
  let _token = $("#_token").val();
  let tempData = []
  let checkQnt = 0
  let checkMinus = 0

  let nobukti = $("#input_add_nobukti").val();
  let nourut = $("#input_add_nourut").val();

  let noprj = $("#input_add_noprj").val();
  let tanggal = $("#input_add_tanggal").val();

  dataTableAdd.forEach((item, i) => {
    if (document.getElementById(`add_checkbox${i}`).checked) {
      let checkQntTerima = $(`#input_add_qntTerima${i}`).val();
      dataTableAdd[i].inputQntTerima = $(`#input_add_qntTerima${i}`).val();

        let checkQntReject = $(`#input_add_qntReject${i}`).val();
        dataTableAdd[i].inputQntReject = $(`#input_add_qntReject${i}`).val();
        if (Number(checkQntTerima)  > Number(dataTableAdd[i].QntSisa)) {
          checkQnt = 1
        }

        if (Number(checkQntReject)  > Number(dataTableAdd[i].QntSisa)) {
          checkQnt = 1
        }

        if(Number(checkQntTerima)  < 0) {
          checkMinus = 1
        }
        if(Number(checkQntReject)  < 0) {
          checkMinus = 1
        }
        // check qntsisa
      tempData.push(dataTableAdd[i])
    }
  });
  if (checkQnt) {
    alertify.warning("Qnt terima / reject melebihi qntsisa");
    return
  }
  if (checkMinus) {
    alertify.warning("Qnt < 0");
    return
  }


  if (!tempData.length) {
    alertify.warning("Tidak ada item dipilih");
    return
  }
  console.log(tempData)
  $.ajax({
      url: "{!! url('returpenjualangudangspadd') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        tempData,
        tanggal: tanggal,
        nobukti,
        nourut,
        noprj
      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          // $("#form").modal('toggle')
          alertify.success('SPR telah ditambah');
          loadAll()
          buttonCloseModalAddPRJ()

        }
        // if (res == 2) {
        //   setNewNoBukti()
        //   alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
        // }
        //
        // if (res == 3 ) {
        //   alertify.warning('Stok gudang tidak mencukupi');
        // }

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })


}

function setNewNoBukti (xval = 1) {
  $.ajax({
    url: "{!! url('returpenjualangudangspnobukti') !!}",
    type: "get",
    async: false,
    data: {
      ppn: xval
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

}

function buttonCloseModalAddPRJ () {
  $('#modalAddPRJ').modal('hide');
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

function formatAngkaX (angka) {
  if (Number(angka) == 0) {
    return '0.00'
  } else {

    return formatAngka(parseFloat(angka).toFixed(2))
  }

}

function formatAngka (angkaString) {
  console.log('formatAngka' , angkaString);
  let tempAngka = angkaString.split('.')
  let temp1 = ''
  for (let i = 0; i < tempAngka[0].length; i++) {
    if (i != 0 && i % 3 == 0) {
      temp1 = ',' + temp1
    }
    temp1 = tempAngka[0][tempAngka[0].length - i -1] + temp1
    // console.log(i, temp1)
  }
  temp1 += '.' + tempAngka[1]
  return temp1
}





</script>

@endsection
