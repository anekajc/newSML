@extends('newmasterTest')
@section('buttons')

@section('page-title', 'Uang Muka Jual')
@section('title', 'SML - Uang Muka Jual')
    
@endsection

{{-- Rerouted to match Purchase Order's UI 1:1 via so.blade.php's own pattern,
     same as the rest of the marketing folder. Only layout/toolbar/column-header
     interactivity changed -- business logic untouched. --}}
@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
<style>
.custom-tabs {
  display: inline-flex; justify-content: flex-start; align-items: center; gap: 2px;
  background-color: #f1f3f5; border-radius: 20px; padding: 3px;
}
.custom-tabs .nav-link {
  display: inline-block !important; padding: 5px 16px !important; font-size: 0.75rem !important;
  border: none; border-radius: 17px; color: #495057; background: transparent; font-weight: 600;
  transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
}
.custom-tabs .nav-link:hover { background: transparent; color: #007bff; }
.custom-tabs .nav-link.active {
  background: #007bff; border-color: #007bff; color: #fff;
  box-shadow: 0 2px 6px rgba(0, 123, 255, .35);
}
.tab-card {
  display: block !important; align-items: flex-start !important; padding: 0 !important;
  border: none !important; margin-bottom: 6px !important;
}
.tab-card .card-body { padding: 5px 10px !important; }
#contentContainer .card {
  display: block !important; align-items: stretch !important; padding: 0 !important;
  text-align: left !important; cursor: default !important;
}
#contentContainer .card:hover { transform: none !important; box-shadow: none !important; border-color: var(--border) !important; }
.po-len-wrap {
  display: flex; align-items: center; gap: 8px; background: var(--rt-card);
  border: 1.5px solid var(--rt-border); border-radius: 8px; padding: 5px 12px;
}
.po-len-wrap label {
  margin: 0; font-size: 11.5px; font-weight: 700; color: var(--rt-ink-soft);
  text-transform: uppercase; letter-spacing: .05em; white-space: nowrap;
}
.po-len-inp {
  border: none; background: transparent; font-size: 13px; font-weight: 700; color: var(--rt-ink);
  outline: none; cursor: pointer; padding: 2px 20px 2px 0; appearance: none;
  -webkit-appearance: none; -moz-appearance: none;
  background-image: url("data:image/svg+xml;utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%231D2130' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right center;
}
#tabel td:first-child, #tabel2 td:first-child, #tabel3 td:first-child { display: flex; gap: 4px; justify-content: center; align-items: center; }
#tabel td:first-child .btn, #tabel2 td:first-child .btn, #tabel3 td:first-child .btn {
  width: 30px; height: 30px; padding: 0; display: inline-flex; align-items: center;
  justify-content: center; border-radius: 7px; font-size: 13px; border: 1px solid transparent;
  box-shadow: none; transition: all .12s ease;
}
#tabel td:first-child .btn:hover, #tabel2 td:first-child .btn:hover, #tabel3 td:first-child .btn:hover { filter: brightness(0.97); transform: translateY(-1px); }
#tabel td:first-child .btn-primary, #tabel2 td:first-child .btn-primary, #tabel3 td:first-child .btn-primary { color: #2563eb; border-color: #cfdcff; background: #e8edff; }
#tabel2 td:first-child .btn-warning, #tabel3 td:first-child .btn-warning { color: #b45309; border-color: #fbe3bd; background: #fef3e0; }
#tabel2 td:first-child .btn-success, #tabel3 td:first-child .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
#tabel2 td:first-child .btn-danger, #tabel3 td:first-child .btn-danger { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }
#tabel thead th, #tabel2 thead th, #tabel3 thead th {
  background: #f8f9fb !important; color: #6b7280 !important; font-size: 12px; text-transform: uppercase;
  letter-spacing: .04em; font-weight: 600; border-bottom: 1px solid #e7e9ee; border-top: none;
}
#tabel tbody tr:nth-of-type(odd), #tabel2 tbody tr:nth-of-type(odd), #tabel3 tbody tr:nth-of-type(odd) { background-color: #fbfbfc; }
#tabel tbody tr:hover, #tabel2 tbody tr:hover, #tabel3 tbody tr:hover { background-color: #f5f3ff; }

/* Hide action buttons until the row is hovered/focused, port 1:1 dari pola
   .action-buttons-wrap milik master (public/css/tableMaster2.css). */
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

/* Perbesar glyph + pada tombol Add tanpa mengubah ukuran bingkai tombol.
   line-height: 0 membuat ikon tidak menambah tinggi baris, jadi tinggi
   tombol tetap sama dengan tombol Detail di sebelahnya. Port 1:1 dari
   returpenjualangudang.blade.php / returpembeliangudang.blade.php. */
.btn .bi-plus {
  font-size: 1.5rem;
  line-height: 0;
  vertical-align: middle;
}

.btn-chip-biru {
  background-color: #e8edff;
  border-color: #cfdcff;
  color: #2563eb;
}

.btn-chip-biru:hover,
.btn-chip-biru:focus {
  background-color: #dce6ff;
  border-color: #b9c9ff;
  color: #1d4ed8;
}

.btn-chip-biru:active {
  background-color: #cfdcff !important;
  border-color: #a8bdff !important;
  color: #1d4ed8 !important;
}

.data-table th,
.data-table td {
  white-space: nowrap;
}
</style>
@endsection

@section('content')
<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>
<div class="container-fluid">

  <!-- <div id="qrcode"></div> -->
  <div class="row" style="margin-top:-85px;">
    <div class="col-6 text-left">
      <h2>Uang Muka Jual</h2>
    </div>
    <div class="col-6 text-right">
      <!-- <button type="button" class="btn btn-primary btn-lg " style="height: 60px; " onclick="buttonAdd()"  >Add SO</button> -->
    </div>
  </div>
<!-- <button onclick="loadAll()">tes</button> -->
</div>

<div id="printContainer" style="display:none">


</div>
<div id="contentContainer" class="container-fluid" >
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
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">Outstanding Uang Muka</a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">Uang Muka Jual</a>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body" style="padding:0;">
  <div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
      <div class="row">
        <div class="col-12">
          <div class="po-toolbar">
            <div class="po-filter-wrap">
              <label>Periode</label>
              <input type="date" onchange="onChangePeriodeUMJ1()" class="po-filter-inp" id="input_tanggalawal_umj1" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d') !!}">
              <span class="po-filter-sep">s/d</span>
              <input type="date" onchange="onChangePeriodeUMJ1()" class="po-filter-inp" id="input_tanggalakhir_umj1" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d') !!}">
            </div>
            <input type="search" id="umjSearch1" class="po-search-inp" placeholder="Cari data">
            <div class="po-len-wrap"><label for="umjLen1">Tampilkan</label>
              <select id="umjLen1" class="po-len-inp"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="-1">Semua</option></select>
            </div>
          </div>
          <div id="rtBarTabel"></div>
          <table id="tabel" class="data-table">
            <thead style="white-space:nowrap;"></thead>
            <tbody id="tabel_data" class="text-left"></tbody>
          </table>
          <div class="po-rt-hint"><i class="bi bi-info-circle"></i> Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom untuk menyembunyikan kolom atau mengatur jumlah desimal.</div>
        </div>
      </div>
    </div>
    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
      <div class="row">
        <div class="col-12">

          {{-- Filter modal: port 1:1 dari modalFilter milik perintahreturjual.blade.php.
               tabel2 (Belum Otorisasi) + tabel3 (Sudah Otorisasi) digabung jadi satu
               tabel di sini dengan Status dropdown, sama seperti PRJ/RPG/SJ. --}}
          <div class="modal fade rt-filter" id="modalFilterUMJ">
            <div class="modal-dialog modal-md">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">
                    <i class="bi bi-funnel"></i>
                    Filter Data
                    <span class="rt-active-badge" id="umjFilterBadge">0 aktif</span>
                  </h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterUMJ').modal('hide')">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>

                <div class="modal-body">
                  <div class="rt-section">
                    <div class="rt-group-label">Status</div>
                    <div>
                      <label class="rt-field-label" for="input_filterumj">Status Otorisasi</label>
                      <select class="rt-native" id="input_filterumj">
                        <option value=0 selected>Semua</option>
                        <option value=1>Belum Otorisasi</option>
                        <option value=2>Sudah Otorisasi</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="modal-footer">
                  <button type="button" class="rt-reset-link" onclick="umjResetFilterFields()">Reset semua</button>
                  <div class="rt-footer-buttons">
                    <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
                      onclick="$('#modalFilterUMJ').modal('hide')">Batal</button>
                    <button type="button" class="rt-btn rt-btn-primary" onclick="buttonFilterUMJ(); $('#modalFilterUMJ').modal('hide');">Terapkan</button>
                  </div>
                </div>

              </div>
            </div>
          </div>

          <div class="po-toolbar">
            <div class="po-filter-wrap">
              <label>Periode</label>
              <input type="date" onchange="onChangePeriodeUMJ()" class="po-filter-inp" id="input_tanggalawal_umj" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d') !!}">
              <span class="po-filter-sep">s/d</span>
              <input type="date" onchange="onChangePeriodeUMJ()" class="po-filter-inp" id="input_tanggalakhir_umj" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d') !!}">
            </div>
            <input type="search" id="umjSearch2" class="po-search-inp" placeholder="Cari data">
            <div class="po-len-wrap"><label for="umjLen2">Tampilkan</label>
              <select id="umjLen2" class="po-len-inp"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="-1">Semua</option></select>
            </div>
            <button class="po-btn-filter" type="button" onclick="$('#modalFilterUMJ').modal('show')">
              <i class="bi bi-funnel"></i> Filter
            </button>
          </div>
          <div id="rtBarTabel2"></div>
          <table id="tabel2" class="data-table">
            <thead style="white-space:nowrap;"></thead>
            <tbody id="tabel2_data" class="text-left"></tbody>
          </table>
          <div class="po-rt-hint"><i class="bi bi-info-circle"></i> Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom untuk menyembunyikan kolom atau mengatur jumlah desimal.</div>
        </div>
      </div>
    </div>
</div>
</div>
</div>


</div>


<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabelAdd">Add</h5>
        <h5 class="modal-title" id="modalLabelEdit">Edit</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_nourut" value="" />

            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">NOBUKTI</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control " id="input_add_nobukti" placeholder="" disabled>
                </div>
              </div>
              <!-- <div class="col-2 ">
                <div class="form-group text-center">
                  <label class="text-left">NO SO</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <select id="input_add_isppn" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                    <option value=0>False</option>
                    <option value=1>True</option>
                  </select>
                </div>
              </div> -->
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">NO SO</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_noso" placeholder="" disabled>
                </div>
              </div>

              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Customer</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_add_customer"  disabled></textarea>
                </div>
              </div>

              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tanggal</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="date" class="form-control text-center" id="input_add_tanggal"  value="{!! date('Y-m-d') !!}">
                </div>
              </div>



            </div>

            <div class="row mt-4">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tipe PPN</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <select disabled onchange="" id="input_add_tipeppn" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                    <option value=0 selected>None</option>
                    <option value=1 >Exclude</option>
                    <option value=2 >Include</option>
                  </select>
                </div>
              </div>

              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">DPP</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control text-right" id="input_add_dpp" value="0.00" disabled>
                </div>
              </div>

              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Valas</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input disabled type="text" class="form-control " id="input_add_valas" placeholder="">
                </div>
              </div>

              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">PPN</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control text-right" id="input_add_ppn" value="0.00" disabled>
                </div>
              </div>

              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kurs</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input disabled type="text" class="form-control text-right" id="input_add_kurs" value="0.00">
                </div>
              </div>


              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Total</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control text-right" id="input_add_total" value="0.00" disabled>
                </div>
              </div>
            </div>

            <div class="row mt-4">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Presentase</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control text-right" id="input_add_presentase" onblur="onChangePresentase()" value="0.00">
                </div>
              </div>

              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">PPN</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control text-right" id="input_add_ppnx" value="0.00" disabled>
                </div>
              </div>

              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">DPP</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control text-right" id="input_add_dppx" onblur="onChangeDPP()" value="0.00">
                </div>
              </div>

              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Subtotal</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control text-right" id="input_add_subtotal" value="0.00" disabled>
                </div>
              </div>
            </div>



    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal" >Batal</button>
    <button id="buttonSubmitAdd" type="button" class="btn btn-chip-biru" onclick="submitAdd()">Simpan</button>
    <button id="buttonSubmitEdit" type="button" class="btn btn-chip-biru" onclick="submitEdit()">Simpan Edit</button>
  </div>
</div>
</div>
</div>
<!-- End modal add-->



<!-- start modal detail -->
<div class="modal fade" id="formDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabelDetail">Detail</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_detail_nourut" value="" />

            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">NOBUKTI</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control " id="input_detail_nobukti" placeholder="" disabled>
                </div>
              </div>
              <!-- <div class="col-2 ">
                <div class="form-group text-center">
                  <label class="text-left">NO SO</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <select id="input_detail_isppn" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                    <option value=0>False</option>
                    <option value=1>True</option>
                  </select>
                </div>
              </div> -->
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">NO SO</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_detail_noso" placeholder="" disabled>
                </div>
              </div>

              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Customer</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_customer"  disabled></textarea>
                </div>
              </div>

              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tanggal</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="date" class="form-control text-center" id="input_detail_tanggal"  value="{!! date('Y-m-d') !!}" disabled>
                </div>
              </div>



            </div>

            <div class="row mt-4">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tipe PPN</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <select disabled onchange="" id="input_detail_tipeppn" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                    <option value=0 selected>None</option>
                    <option value=1 >Exclude</option>
                    <option value=2 >Include</option>
                  </select>
                </div>
              </div>

              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">DPP</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control text-right" id="input_detail_dpp" value="0.00" disabled>
                </div>
              </div>

              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Valas</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input disabled type="text" class="form-control " id="input_detail_valas" placeholder="">
                </div>
              </div>

              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">PPN</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control text-right" id="input_detail_ppn" value="0.00" disabled>
                </div>
              </div>

              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kurs</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input disabled type="text" class="form-control text-right" id="input_detail_kurs" value="0.00">
                </div>
              </div>


              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Total</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control text-right" id="input_detail_total" value="0.00" disabled>
                </div>
              </div>
            </div>

            <div class="row mt-4">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Presentase</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control text-right" id="input_detail_presentase" onblur="onChangePresentase()" value="0.00" disabled>
                </div>
              </div>

              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">PPN</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control text-right" id="input_detail_ppnx" value="0.00" disabled>
                </div>
              </div>

              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">DPP</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control text-right" id="input_detail_dppx" onblur="onChangeDPP()" value="0.00" disabled>
                </div>
              </div>

              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Subtotal</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control text-right" id="input_detail_subtotal" value="0.00" disabled>
                </div>
              </div>
            </div>













    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal" >Batal</button>
  </div>
</div>
</div>
</div>
<!-- End modal detail-->





@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script type="text/javascript">

let tempAdd = {}

/* ============ Header tabel interaktif (window.ReportTable) ============
 * Port 1:1 dari poCart/poAktifkanTabel milik purchaseOrder.blade.php, sama
 * seperti so/invoicejasa/fakturpajak/cetaktandaterima/perintahreturjual/
 * returpenjualangudang/kreditnote/notareturpenjualan/perintahreturjualminus/
 * retursuratjalan.
 */
let umjCart = { 1 : [], 2 : [] }
let umjActiveUrut = 0
const UMJ_HREF = 'uangmukajual'
const UMJ_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date', 3 : 'bool' }
const UMJ_TIPE_KODE = { varchar : 0, float : 1, date : 2, bool : 3 }

function activeVisibleTabKeyUMJ () {
  if ($('#nav-profile-tab').hasClass('active')) { return 2; }
  return 1;
}

function umjPickCI (row, key) {
  if (!row) { return undefined; }
  if (row[key] !== undefined) { return row[key]; }
  let lower = key.toLowerCase();
  for (let k in row) { if (k.toLowerCase() === lower) { return row[k]; } }
  return undefined;
}

function umjDefaultCart (urut) {
  if (urut === 1) {
    return [
      ['NoBukti',      'No. Bukti',      1, 'varchar', 0, 0],
      ['Tanggal',      'Tanggal',        1, 'date',    0, 0],
      ['NamaCust',     'Nama Pelanggan', 1, 'varchar', 0, 0],
      ['OtoPerf',      'Oto Perf',       1, 'bool',    0, 0],
      ['userOtoPerf',  'User Oto Perf',  1, 'varchar', 0, 0],
      ['tglOtoPerf',   'Tgl Oto Perf',   1, 'date',    0, 0],
      ['NAMASLS',      'Sales',          1, 'varchar', 0, 0],
      ['NAMAPIC',      'PIC',            1, 'varchar', 0, 0],
      ['NoPesanan',    'Po. Cust',       1, 'varchar', 0, 0],
      ['DP',           'DP',             1, 'float',   0, 2],
      ['TotDPP',       'DPP',            1, 'float',   0, 2],
      ['TotPPN',       'PPN',            1, 'float',   0, 2],
      ['TotNet',       'Grandtotal',     1, 'float',   0, 2],
      ['IsBatal',      'Batal',          1, 'bool',    0, 0],
      ['userbatal',    'UserBatal',      1, 'varchar', 0, 0],
      ['Tglbatal',     'TglBatal',       1, 'date',    0, 0],
      ['catatan',      'Catatan',        1, 'varchar', 0, 0],
    ]
  }
  // urut 2: Uang Muka Otorisasi -- gabungan kolom tab lama "Belum Otorisasi" +
  // "Sudah Otorisasi" (OtoUser1/TglOto1), sejak keduanya digabung jadi satu tabel.
  return [
    ['NOBUKTI',      'No Bukti', 1, 'varchar', 0, 0],
    ['TANGGAL',      'Tanggal',  1, 'date',    0, 0],
    ['NOSO',         'No SO',    1, 'varchar', 0, 0],
    ['NamaCustSupp', 'Customer', 1, 'varchar', 0, 0],
    ['VALAS',        'Valas',    1, 'varchar', 0, 0],
    ['DPP',          'DPP',      1, 'float',   0, 2],
    ['PPN',          'PPN',      1, 'float',   0, 2],
    ['PERSEN',       'Persen',   1, 'float',   0, 2],
    ['OtoUser1',     'Oto User', 1, 'varchar', 0, 0],
    ['TglOto1',      'Tgl Oto',  1, 'date',    0, 0],
  ]
}

function umjBuatCart (headers, values, isnumerics, isshowns, desimals) {
  headers = headers || []
  let cart = []
  headers.forEach((h, i) => {
    let tipe = Number(isnumerics[i]) || 0
    let des = (desimals && desimals[i] !== undefined && desimals[i] !== null && desimals[i] !== '')
      ? Number(desimals[i]) : (tipe === 1 ? 2 : 0)
    cart.push([values[i], h, Number(isshowns[i]) === 1 ? 1 : 0, UMJ_TIPE_NAMA[tipe] || 'varchar', 0, isNaN(des) ? 0 : des])
  });
  return cart
}

function umjAktifkanTabel (urut) {
  umjActiveUrut = urut
  window.g_modeReport = urut
  window.gcart_header = umjCart[urut]
}

function umjOnChangeAktif () {
  if (umjActiveUrut === 2) { reinitTabel2(); } else { reinitTabel(); }
}

window.g_href = UMJ_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function (href, mode) {
  let urut = mode || 1
  let cart = umjCart[urut] || []
  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[1]); value.push(c[0]); isnumber.push(UMJ_TIPE_KODE[c[3]] ?? 0)
    isshown.push(Number(c[2]) === 1 ? 1 : 0); desimal.push(Number(c[5]) || 0)
  });
  $.ajax({
    url: "{!! url('saveheadertable') !!}", type: "post", async: false,
    data: {
      _token: $("#_token").val(), header: JSON.stringify(header), isnumber: JSON.stringify(isnumber),
      tipe: JSON.stringify(desimal), value: JSON.stringify(value), isshown: JSON.stringify(isshown),
      href: UMJ_HREF, urut: urut
    },
    error: function (err) { console.log(err); alertify.warning('Gagal menyimpan pengaturan kolom') }
  })
}

window.doSetHeader = function (mode, reset) {
  let urut = mode || 1
  $.ajax({
    url: "{!! url('getheadertable') !!}", type: "post", async: false,
    data: { _token: $("#_token").val(), href: UMJ_HREF, urut: urut, reset: reset ? 1 : 0 },
    success: function (res) {
      if (!reset && res && res.headertableheader && res.headertableheader.length) {
        umjCart[urut] = umjBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal || [])
      } else {
        umjCart[urut] = umjDefaultCart(urut)
        window.gcart_header = umjCart[urut]
        window.doSimpanHeader(UMJ_HREF, urut)
      }
      window.gcart_header = umjCart[urut]
    },
    error: function (err) {
      console.log(err)
      alertify.warning(reset ? 'Gagal mengembalikan kolom ke tampilan default' : 'Gagal memuat pengaturan kolom')
      umjCart[urut] = umjDefaultCart(urut)
      window.gcart_header = umjCart[urut]
    }
  })
}

const UMJ_SELEKTOR_TABEL_AKTIF = '#myTabContent .tab-pane.active table.data-table'
const UMJ_SELEKTOR_BAR_AKTIF = '#myTabContent .tab-pane.active [id^="rtBarTabel"]'

let umjRtSudahInit = false
function umjInitReportTableSekali () {
  if (umjRtSudahInit || typeof ReportTable === 'undefined') { return }
  umjRtSudahInit = true
  let urutAktif = activeVisibleTabKeyUMJ()
  let idTabel = { 1 : '#tabel', 2 : '#tabel2' }
  let idBar = { 1 : '#rtBarTabel', 2 : '#rtBarTabel2' }
  Object.keys(idTabel).forEach((u) => {
    if (Number(u) === urutAktif) { return }
    ReportTable.init({ table: idTabel[u], bar: idBar[u], onChange: umjOnChangeAktif })
  });
  ReportTable.init({ table: UMJ_SELEKTOR_TABEL_AKTIF, bar: UMJ_SELEKTOR_BAR_AKTIF, onChange: umjOnChangeAktif })

  let umjGuardUlangKlik = false;
  ['#tabel', '#tabel2'].forEach((sel) => {
    let thead = document.querySelector(sel + ' thead')
    if (!thead) { return }
    thead.addEventListener('click', function (e) {
      if (umjGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }
      e.stopPropagation()
      e.preventDefault()
      umjGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles: false, cancelable: true, view: window })
      Object.defineProperty(ulang, 'target', { value: interaktif, configurable: true })
      thead.dispatchEvent(ulang)
      umjGuardUlangKlik = false
    }, true)
  });
}

function tulisTheadHeaderUMJ (tableSel, cols) {
  let thead = document.querySelector(tableSel + ' thead')
  if (!thead || !window.ReportTable) { return; }
  let headRowHtml = ReportTable.headHtml(cols).replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>');
  thead.setAttribute('style', 'white-space:nowrap;');
  thead.innerHTML = headRowHtml;
}

function umjValueCell (row, col) {
  let raw = umjPickCI(row, col[0]);
  let type = col[3];
  if (type === 'date') { if (!raw) { return '<td></td>'; } return '<td>' + formatDate(raw, '/') + '</td>'; }
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

function tabelActionsCell (row) {
  let nobukti = umjPickCI(row, 'NoBukti');
  let html = '<td class="text-center"><div class="action-buttons-wrap">';
  html += '<button class="btn btn-primary btn-sm" type="button" onclick="buttonAdd(\'' + nobukti + '\')"><i class="bi bi-plus"></i></button>';
  html += '</div></td>';
  return html;
}

// Digabung dari tabel2ActionsCell (Belum Otorisasi: Edit/Otorisasi/Delete) +
// tabel3ActionsCell (Sudah Otorisasi: Batal Otorisasi/Print) sejak keduanya
// digabung jadi satu tabel dengan filter Semua/Belum/Sudah Otorisasi.
function umjTabel2Or3ActionsCell (row) {
  let nobukti = umjPickCI(row, 'NOBUKTI');
  let noso = umjPickCI(row, 'NOSO');
  let isOto = Number(umjPickCI(row, 'IsOtorisasi1'));
  let html = '<td class="text-center"><div class="action-buttons-wrap">';
  html += '<button class="btn btn-warning btn-sm" type="button" onclick="buttonDetailKoreksi(\'' + nobukti + '\', \'' + noso + '\')"><i class="bi bi-info"></i></button>';
  if (isOto) {
    html += '<button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOto(\'' + nobukti + '\' , \'' + noso + '\')"><i class="bi bi-key"></i></button>';
    html += '<button class="btn btn-primary btn-sm" type="button" onclick="submitPrint(\'' + nobukti + '\')"><i class="bi bi-printer"></i></button>';
    html += '<button class="btn btn-primary btn-sm" type="button" onclick="submitPrintTT(\'' + nobukti + '\')"><i class="bi bi-printer-fill"></i></button>';
  } else {
    html += '<button class="btn btn-success btn-sm" type="button" onclick="buttonEdit(\'' + nobukti + '\' , \'' + noso + '\')"><i class="bi bi-pen"></i></button>';
    html += '<button class="btn btn-primary btn-sm" type="button" onclick="buttonOto(\'' + nobukti + '\' , \'' + noso + '\')"><i class="bi bi-key"></i></button>';
    html += '<button class="btn btn-danger btn-sm" type="button" onclick="buttonDelete(\'' + nobukti + '\' , \'' + noso + '\')"><i class="bi bi-trash"></i></button>';
  }
  html += '</div></td>';
  return html;
}

function renderTabelRows (rows) {
  let cols = (umjCart[1].length ? umjCart[1] : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  (rows || []).forEach(function (row) {
    html += '<tr>' + tabelActionsCell(row);
    cols.forEach(function (col) { html += umjValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel_data').innerHTML = html;
  tulisTheadHeaderUMJ('#tabel', cols);
}

function renderTabel2Rows (rows) {
  let cols = (umjCart[2].length ? umjCart[2] : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  (rows || []).forEach(function (row) {
    html += '<tr>' + umjTabel2Or3ActionsCell(row);
    cols.forEach(function (col) { html += umjValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel2_data').innerHTML = html;
  tulisTheadHeaderUMJ('#tabel2', cols);
}

let lastTabelRows = []
let lastTabel2Rows = []
let umjPanjangHalaman = { 1 : 10, 2 : 10 }

function umjIkatSearch (urut) {
  let ids = { 1 : ['umjSearch1', 'tabel'], 2 : ['umjSearch2', 'tabel2'] }
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

function umjIkatPanjangHalaman (urut) {
  let ids = { 1 : ['umjLen1', 'tabel'], 2 : ['umjLen2', 'tabel2'] }
  let sel = document.getElementById(ids[urut][0])
  let idTabel = ids[urut][1]
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(umjPanjangHalaman[urut])
  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    umjPanjangHalaman[urut] = (n === -1 || n > 0) ? n : 10
    if ($.fn.DataTable.isDataTable('#' + idTabel)) { $('#' + idTabel).DataTable().page.len(umjPanjangHalaman[urut]).draw() }
  })
}

const UMJ_DOM_STRING = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"

function reinitTabel () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().destroy(); }
    renderTabelRows(lastTabelRows);
    $('#tabel').DataTable({ dom: UMJ_DOM_STRING, lengthChange: false, pageLength: umjPanjangHalaman[1], paging: true, order: [[0, 'asc']], ordering: false });
    umjIkatSearch(1); umjIkatPanjangHalaman(1);
  } catch (e) { console.error('reinitTabel failed:', e); alertify.error('Gagal memperbarui tabel: ' + e.message); }
}

function reinitTabel2 () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel2')) { $('#tabel2').DataTable().destroy(); }
    renderTabel2Rows(lastTabel2Rows);
    $('#tabel2').DataTable({ dom: UMJ_DOM_STRING, lengthChange: false, pageLength: umjPanjangHalaman[2], paging: true, order: [[0, 'asc']], ordering: false });
    umjIkatSearch(2); umjIkatPanjangHalaman(2);
  } catch (e) { console.error('reinitTabel2 failed:', e); alertify.error('Gagal memperbarui tabel: ' + e.message); }
}

function umjResetFilterFields () {
  $('#input_filterumj').val('0')
}

function umjUpdateFilterBadge () {
  let n = Number($('#input_filterumj').val()) || 0
  $('#umjFilterBadge').text(n === 0 ? '0 aktif' : '1 aktif')
}

function buttonFilterUMJ () {
  let tglawal = $('#input_tanggalawal_umj').val()
  let tglakhir = $('#input_tanggalakhir_umj').val()
  let filterumj = $('#input_filterumj').val()
  $.ajax({
    url: "{!! url('uangmukajualloadall') !!}",
    type: "get", async: false,
    data: { tglawal, tglakhir, filterumj },
    success: function (res) {
      lastTabel2Rows = res.tempOutstanding2
      reinitTabel2()
      umjUpdateFilterBadge()
    },
    error: function (err) { console.log(err); alertify.warning('Terjadi kesalahan silahkan refresh browser') }
  })
}

function onChangePeriodeUMJ () {
  let tglawal = $('#input_tanggalawal_umj').val()
  let tglakhir = $('#input_tanggalakhir_umj').val()
  if (tglawal && tglakhir && tglawal > tglakhir) {
    alertify.warning('Tanggal awal tidak boleh lebih besar dari tanggal akhir')
    return
  }
  buttonFilterUMJ()
}

function buttonFilterUMJ1 () {
  let tglawal1 = $('#input_tanggalawal_umj1').val()
  let tglakhir1 = $('#input_tanggalakhir_umj1').val()
  $.ajax({
    url: "{!! url('uangmukajualloadall') !!}",
    type: "get", async: false,
    data: { tglawal1, tglakhir1 },
    success: function (res) {
      lastTabelRows = res.tempOutstanding
      reinitTabel()
    },
    error: function (err) { console.log(err); alertify.warning('Terjadi kesalahan silahkan refresh browser') }
  })
}

function onChangePeriodeUMJ1 () {
  let tglawal1 = $('#input_tanggalawal_umj1').val()
  let tglakhir1 = $('#input_tanggalakhir_umj1').val()
  if (tglawal1 && tglakhir1 && tglawal1 > tglakhir1) {
    alertify.warning('Tanggal awal tidak boleh lebih besar dari tanggal akhir')
    return
  }
  buttonFilterUMJ1()
}

$(document).ready(function(){
      umjAktifkanTabel(1); window.doSetHeader(1, false);
      lastTabelRows = @json($tempOutstanding);
      reinitTabel();

      umjAktifkanTabel(2); window.doSetHeader(2, false);
      lastTabel2Rows = @json($tempOutstanding2);
      reinitTabel2();

      umjInitReportTableSekali();

      $('#nav-home-tab').on('shown.bs.tab', function () { umjAktifkanTabel(1); if (typeof ReportTable !== 'undefined') { ReportTable.refresh(); } });
      $('#nav-profile-tab').on('shown.bs.tab', function () { umjAktifkanTabel(2); if (typeof ReportTable !== 'undefined') { ReportTable.refresh(); } });
});

function loadAll () {
  let tglawal1 = $('#input_tanggalawal_umj1').val()
  let tglakhir1 = $('#input_tanggalakhir_umj1').val()
  let tglawal = $('#input_tanggalawal_umj').val()
  let tglakhir = $('#input_tanggalakhir_umj').val()
  let filterumj = $('#input_filterumj').val()
  $.ajax({
    url: "{!! url('uangmukajualloadall') !!}",
    type: "get", async: false, data: { tglawal1, tglakhir1, tglawal, tglakhir, filterumj },
    success: function(res) {
      lastTabelRows = res.tempOutstanding;
      lastTabel2Rows = res.tempOutstanding2;
      reinitTabel();
      reinitTabel2();
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function setNewNoBukti () {

  let _token  = $("#_token").val()
  $.ajax({
    url: "{!! url('uangmukajualspnobukti') !!}",
    type: "post",
    async: false,
    data: {
      _token,
    },
    success: function(res) {
      console.log(res)

      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}

function buttonAdd (NOBUKTI) {

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

  $('#modalLabelEdit').hide();
  $('#buttonSubmitEdit').hide();
  $('#modalLabelAdd').show();
  $('#buttonSubmitAdd').show();
  console.log('buttonAdd')
  console.log(NOBUKTI)
  let _token  = $("#_token").val()
  console.log('1')

  let akses = $("#akses_istambah").val();
  console.log('1')
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  console.log('1')

  document.getElementById("input_add_tanggal").value = formatDate(new Date())
  document.getElementById("input_add_presentase").value = '0.00'

  document.getElementById("input_add_dppx").value = '0.00'

  document.getElementById("input_add_ppnx").value = '0.00'
  document.getElementById("input_add_subtotal").value = '0.00'

  console.log('1')
  setNewNoBukti()
  console.log('1')
  $.ajax({
    url: "{!! url('uangmukajualgetdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: NOBUKTI
    },
    success: function(res) {
      console.log(res)
      tempAdd = res.header[0]

      document.getElementById("input_add_noso").value = NOBUKTI
      document.getElementById("input_add_customer").value = tempAdd.NamaCust

      document.getElementById("input_add_valas").value = tempAdd.kodevls
      document.getElementById("input_add_kurs").value = tempAdd.kurs ? formatAngka(parseFloat(tempAdd.kurs).toFixed(2)) : '0.00'
      document.getElementById("input_add_dpp").value = tempAdd.TotDPP ? formatAngka(parseFloat(tempAdd.TotDPP).toFixed(2)) : '0.00'
      document.getElementById("input_add_dppx").value = tempAdd.DP ? formatAngka(parseFloat(tempAdd.DP).toFixed(2)) : '0.00'
      onChangeDPP()
      document.getElementById("input_add_ppn").value = tempAdd.TotPPN ? formatAngka(parseFloat(tempAdd.TotPPN).toFixed(2)) : '0.00'
      document.getElementById("input_add_total").value = tempAdd.TotNet ? formatAngka(parseFloat(tempAdd.TotNet).toFixed(2)) : '0.00'
      document.getElementById("input_add_tipeppn").value = Number(tempAdd.TipePPN)


      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonDetail (NOBUKTI) {

  // $('#modalLabelEdit').hide();
  // $('#buttonSubmitEdit').hide();
  // $('#modalLabelAdd').show();
  // $('#buttonSubmitAdd').show();
  console.log('buttonDetail')
  console.log(NOBUKTI)
  let _token  = $("#_token").val()

  document.getElementById("input_detail_tanggal").value = ''
  document.getElementById("input_detail_presentase").value = '0.00'

  document.getElementById("input_detail_dppx").value = '0.00'

  document.getElementById("input_detail_ppnx").value = '0.00'
  document.getElementById("input_detail_subtotal").value = '0.00'


  // $.ajax({
  //   url: "{!! url('uangmukajualspnobukti') !!}",
  //   type: "post",
  //   async: false,
  //   data: {
  //     _token,
  //   },
  //   success: function(res) {
  //     console.log(res)
  //
  //     document.getElementById("input_detail_nobukti").value = res[0].Nobukti
  //     document.getElementById("input_detail_nourut").value = res[0].Nourut
  //
  //   },
  //   error: function (err) {
  //     console.log(err)
  //     console.log(err.status)
  //     console.log(err.statusText)
  //     alertify.warning('Terjadi kesalahan silahkan refresh browser')
  //   }
  //
  // })

  // setNewNoBukti()
  document.getElementById("input_detail_nobukti").value = '-'

  $.ajax({
    url: "{!! url('uangmukajualgetdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: NOBUKTI
    },
    success: function(res) {
      console.log(res)
      tempAdd = res.header[0]

      document.getElementById("input_detail_noso").value = NOBUKTI
      document.getElementById("input_detail_customer").value = tempAdd.NamaCust

      document.getElementById("input_detail_valas").value = tempAdd.kodevls
      document.getElementById("input_detail_kurs").value = tempAdd.kurs ? formatAngka(parseFloat(tempAdd.kurs).toFixed(2)) : '0.00'
      document.getElementById("input_detail_dpp").value = tempAdd.TotDPP ? formatAngka(parseFloat(tempAdd.TotDPP).toFixed(2)) : '0.00'
      document.getElementById("input_detail_dppx").value = tempAdd.DP ? formatAngka(parseFloat(tempAdd.DP).toFixed(2)) : '0.00'

      document.getElementById("input_detail_ppn").value = tempAdd.TotPPN ? formatAngka(parseFloat(tempAdd.TotPPN).toFixed(2)) : '0.00'
      document.getElementById("input_detail_total").value = tempAdd.TotNet ? formatAngka(parseFloat(tempAdd.TotNet).toFixed(2)) : '0.00'
      document.getElementById("input_detail_tipeppn").value = Number(tempAdd.TipePPN)


      $("#formDetail").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}



function buttonDelete (NOBUKTI, NOSO) {

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

  console.log('buttonEdit')
  console.log(NOBUKTI , NOSO)

  let akses = $("#akses_ishapus").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus UMJ '+ NOBUKTI +' ?',
      function() {
        let _token  = $("#_token").val()


        let choice = 'D'
        let nobukti = NOBUKTI
        let nourut = ''
        let noso = NOSO
        let valas = ''
        let kurs = 0
        let dppx = 0
        let presentase = 0
        let ppnx = 0
        let subtotal = 0
        let tanggal = null
        let bayar = 0
        let flagtipe = 0
        let tglest = null
        let maxol = 1
        let jmlrecord = 1
        let pbeli = 0

        console.log({
          choice,
          nobukti ,
          nourut ,
          noso ,
          valas ,
          kurs ,
          dppx ,
          presentase ,
          ppnx ,
          subtotal ,
          tanggal ,
          bayar ,
          flagtipe ,
          tglest ,
          maxol ,
          jmlrecord,
          pbeli
        })

        $.ajax({
          url: "{!! url('uangmukajualspadd') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            choice,
            nobukti ,
            nourut ,
            noso ,
            valas ,
            kurs ,
            dppx ,
            presentase ,
            ppnx ,
            subtotal ,
            tanggal ,
            bayar ,
            flagtipe ,
            tglest ,
            maxol ,
            jmlrecord
          },
          success: function(res) {
            if (res == 1) {
              console.log(res)
              alertify.success('Berhasil hapus UMJ')
              loadAll()

            }

          },
          error: function (err) {
            console.log(err)
            console.log(err.status)
            console.log(err.statusText)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }

        })
      }
    ,function(){
      console.log('no')
    });







}




function buttonEdit (NOBUKTI, NOSO) {
  console.log('buttonEdit')
  console.log(NOBUKTI , NOSO)
  let _token  = $("#_token").val()

  let akses = $("#akses_iskoreksi").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  $('#modalLabelEdit').show();
  $('#buttonSubmitEdit').show();
  $('#modalLabelAdd').hide();
  $('#buttonSubmitAdd').hide();



  $.ajax({
    url: "{!! url('uangmukajualgetdetailumj') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: NOBUKTI,
      noso: NOSO
    },
    success: function(res) {
      console.log(res)
      tempAdd = res.header[0]
      let tempDetail =  res.detail[0]

      if (Number(tempDetail.IsOtorisasi1)) {
        alertify.warning('Sudah diotorisasi')
        return
      }

      document.getElementById("input_add_noso").value = NOSO
      document.getElementById("input_add_nobukti").value = NOBUKTI
      document.getElementById("input_add_customer").value = tempAdd.NamaCust
      document.getElementById("input_add_tanggal").value = formatDate(new Date(tempDetail.TANGGAL))
      document.getElementById("input_add_valas").value = tempAdd.kodevls
      document.getElementById("input_add_kurs").value = tempAdd.kurs ? formatAngka(parseFloat(tempAdd.kurs).toFixed(2)) : '0.00'
      document.getElementById("input_add_dpp").value = tempAdd.TotDPP ? formatAngka(parseFloat(tempAdd.TotDPP).toFixed(2)) : '0.00'
      document.getElementById("input_add_ppn").value = tempAdd.TotPPN ? formatAngka(parseFloat(tempAdd.TotPPN).toFixed(2)) : '0.00'
      document.getElementById("input_add_total").value = tempAdd.TotTotal ? formatAngka(parseFloat(tempAdd.TotTotal).toFixed(2)) : '0.00'
      document.getElementById("input_add_tipeppn").value = Number(tempAdd.TipePPN)

      document.getElementById("input_add_presentase").value = formatAngka(parseFloat(tempDetail.PERSEN || 0).toFixed(2))

      document.getElementById("input_add_dppx").value = formatAngka(parseFloat(tempDetail.DPP || 0).toFixed(2))

      document.getElementById("input_add_ppnx").value = formatAngka(parseFloat(tempDetail.PPN).toFixed(2))
      document.getElementById("input_add_subtotal").value = formatAngka(parseFloat(tempDetail.SUBTOTAL).toFixed(2))


      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}


function buttonDetailKoreksi (NOBUKTI, NOSO) {

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

  console.log('buttonDetailKoreksi')
  console.log(NOBUKTI , NOSO)
  let _token  = $("#_token").val()



  $.ajax({
    url: "{!! url('uangmukajualgetdetailumj') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: NOBUKTI,
      noso: NOSO
    },
    success: function(res) {
      console.log(res)
      tempAdd = res.header[0]
      let tempDetail =  res.detail[0]

      // if (Number(tempDetail.IsOtorisasi1)) {
      //   alertify.warning('Sudah diotorisasi')
      //   return
      // }

      document.getElementById("input_detail_noso").value = NOSO
      document.getElementById("input_detail_nobukti").value = NOBUKTI
      document.getElementById("input_detail_customer").value = tempAdd.NamaCust
      document.getElementById("input_detail_tanggal").value = formatDate(new Date(tempDetail.TANGGAL))
      document.getElementById("input_detail_valas").value = tempAdd.kodevls
      document.getElementById("input_detail_kurs").value = tempAdd.kurs ? formatAngka(parseFloat(tempAdd.kurs).toFixed(2)) : '0.00'
      document.getElementById("input_detail_dpp").value = tempAdd.TotDPP ? formatAngka(parseFloat(tempAdd.TotDPP).toFixed(2)) : '0.00'
      document.getElementById("input_detail_ppn").value = tempAdd.TotPPN ? formatAngka(parseFloat(tempAdd.TotPPN).toFixed(2)) : '0.00'
      document.getElementById("input_detail_total").value = tempAdd.TotTotal ? formatAngka(parseFloat(tempAdd.TotTotal).toFixed(2)) : '0.00'
      document.getElementById("input_detail_tipeppn").value = Number(tempAdd.TipePPN)

      document.getElementById("input_detail_presentase").value = formatAngka(parseFloat(tempDetail.PERSEN || 0).toFixed(2))

      document.getElementById("input_detail_dppx").value = formatAngka(parseFloat(tempDetail.DPP || 0).toFixed(2))

      document.getElementById("input_detail_ppnx").value = formatAngka(parseFloat(tempDetail.PPN).toFixed(2))
      document.getElementById("input_detail_subtotal").value = formatAngka(parseFloat(tempDetail.SUBTOTAL).toFixed(2))


      $("#formDetail").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}




function submitAdd () {
  let _token  = $("#_token").val()

  let checkDate = new Date($("#input_add_tanggal").val())

  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }
  let choice = 'I'
  let nobukti = $("#input_add_nobukti").val();
  let nourut = $("#input_add_nourut").val();
  let noso = $("#input_add_noso").val();
  let valas = $("#input_add_valas").val();
  let kurs = $("#input_add_kurs").val().split(',').join('')
  let dppx = $("#input_add_dppx").val().split(',').join('')
  let presentase = $("#input_add_presentase").val().split(',').join('')
  let ppnx = $("#input_add_ppnx").val().split(',').join('')
  let subtotal = $("#input_add_subtotal").val().split(',').join('')
  let tanggal = $("#input_add_tanggal").val();
  let bayar = 0
  let flagtipe = Number($("#input_add_tipeppn").val())
  let tglest = null
  let maxol = 1
  let jmlrecord = 0
  let pbeli = 0

  console.log({
    choice,
    nobukti ,
    nourut ,
    noso ,
    valas ,
    kurs ,
    dppx ,
    presentase ,
    ppnx ,
    subtotal ,
    tanggal ,
    bayar ,
    flagtipe ,
    tglest ,
    maxol ,
    jmlrecord,
    pbeli
  })
  // return
  $.ajax({
    url: "{!! url('uangmukajualspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      choice,
      nobukti ,
      nourut ,
      noso ,
      valas ,
      kurs ,
      dppx ,
      presentase ,
      ppnx ,
      subtotal ,
      tanggal ,
      bayar ,
      flagtipe ,
      tglest ,
      maxol ,
      jmlrecord
    },
    success: function(res) {
      if (res == 1) {
        console.log(res)
        alertify.success('Berhasil add UMJ')
        loadAll()
        $("#form").modal('toggle')

      }

      if (res == 2) {
        alertify.warning('Nobukti telah direfresh, silahkan submit ulang')
        setNewNoBukti()
      }

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })



}


function buttonOto (nobukti) {
  console.log(nobukti)



  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }





  alertify.confirm('Otorisasi', 'Otorisasi UMJ ' + nobukti + ' ?',
      function() {
        let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('uangmukajualspoto') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti

          },
          success: function(res) {
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



function buttonBatalOto (nobukti) {
  console.log(nobukti)



  let akses = $("#akses_isotorisasi1").val();
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
          url: "{!! url('uangmukajualspbataloto') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti,
          pket :value

          },
          success: function(res) {
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
	 alertify.error("Action cancelled");
    });



}



function submitEdit () {
  let _token  = $("#_token").val()

  let checkDate = new Date($("#input_add_tanggal").val())

  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }
  let choice = 'U'
  let nobukti = $("#input_add_nobukti").val();
  let nourut = $("#input_add_nourut").val();
  let noso = $("#input_add_noso").val();
  let valas = $("#input_add_valas").val();
  let kurs = $("#input_add_kurs").val().split(',').join('')
  let dppx = $("#input_add_dppx").val().split(',').join('')
  let presentase = $("#input_add_presentase").val().split(',').join('')
  let ppnx = $("#input_add_ppnx").val().split(',').join('')
  let subtotal = $("#input_add_subtotal").val().split(',').join('')
  let tanggal = $("#input_add_tanggal").val();
  let bayar = 0
  let flagtipe = Number($("#input_add_tipeppn").val())
  let tglest = null
  let maxol = 1
  let jmlrecord = 1
  let pbeli = 0

  console.log({
    choice,
    nobukti ,
    nourut ,
    noso ,
    valas ,
    kurs ,
    dppx ,
    presentase ,
    ppnx ,
    subtotal ,
    tanggal ,
    bayar ,
    flagtipe ,
    tglest ,
    maxol ,
    jmlrecord,
    pbeli
  })

  $.ajax({
    url: "{!! url('uangmukajualspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      choice,
      nobukti ,
      nourut ,
      noso ,
      valas ,
      kurs ,
      dppx ,
      presentase ,
      ppnx ,
      subtotal ,
      tanggal ,
      bayar ,
      flagtipe ,
      tglest ,
      maxol ,
      jmlrecord
    },
    success: function(res) {
      if (res == 1) {
        console.log(res)
        alertify.success('Berhasil edit UMJ')
        loadAll()
        $("#form").modal('toggle')

      }

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })



}

function onChangePresentase () {
  let presentase = $("#input_add_presentase").val().split(',').join('')
  let dpp = $("#input_add_dpp").val().split(',').join('')
  let ppn = $("#input_add_ppn").val().split(',').join('')
  let total = $("#input_add_total").val().split(',').join('')
  console.log(dpp, ppn, total)

  let dppx = dpp * presentase / 100
  let ppnx = ppn * presentase / 100
  let subtotal = total * presentase / 100

  document.getElementById("input_add_dppx").value = formatAngka(parseFloat(dppx).toFixed(2))
  document.getElementById("input_add_ppnx").value = formatAngka(parseFloat(ppnx).toFixed(2))
  document.getElementById("input_add_subtotal").value = formatAngka(parseFloat(subtotal).toFixed(2))

}

function onChangeDPP () {
  console.log('onChangeDPP')
  let dpp = $("#input_add_dpp").val().split(',').join('')
  let dppx = $("#input_add_dppx").val().split(',').join('')

  // console.log(dpp, dppx)
  let ppn = $("#input_add_ppn").val().split(',').join('')
  let total = $("#input_add_total").val().split(',').join('')

  let presentase = Number(dpp) > 0 ? parseFloat(Number(dppx) / Number(dpp) * 100).toFixed(2) : 0

  let ppnx = ppn * presentase / 100
  let subtotal = total * presentase / 100
  // console.log(presentase,ppnx, subtotal)
  document.getElementById("input_add_presentase").value = formatAngka(parseFloat(presentase).toFixed(2))
  document.getElementById("input_add_dppx").value = formatAngka(parseFloat(dppx).toFixed(2))
  document.getElementById("input_add_ppnx").value = formatAngka(parseFloat(ppnx).toFixed(2))
  document.getElementById("input_add_subtotal").value = formatAngka(parseFloat(subtotal).toFixed(2))


  // console.log(presentase)


  // 23814
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

function formatAngka (angkaString) {


  console.log('formatAngkaMaster' , angkaString);
  let tempAngka = angkaString.split('.')


  tempAngka[0] = tempAngka[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  return tempAngka.join(".");

}

</script>

<script>
function submitPrint (nobukti) {

  let _token = $('#_token').val()

  let namaTtdCetak = ''
  console.log('0')
  // const options = ['EVY YUSIA', 'JULIA', 'DESTI']

  // const overlay = document.createElement('div')
  // overlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;'

  // overlay.innerHTML = `
  //   <div style="background:#fff;padding:24px;border-radius:8px;min-width:320px;font-family:sans-serif;font-size:14px;">
  //     <h3 style="margin:0 0 16px;">Cetak Purchase Order</h3>
  //     <label style="display:block;margin-bottom:6px;">Ditandatangani oleh :</label>
  //     <select id="selectNamaTtd" style="width:100%;padding:6px;font-size:14px;border:1px solid #ccc;border-radius:4px;">
  //       <option value="">-- Pilih --</option>

  //     </select>
  //     <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:20px;">
  //       <button id="btnBatalTtd" style="padding:6px 16px;border:1px solid #ccc;background:#fff;border-radius:4px;cursor:pointer;">Batal</button>
  //       <button id="btnLanjutTtd" style="padding:6px 16px;background:#333;color:#fff;border:none;border-radius:4px;cursor:pointer;">Cetak</button>
  //     </div>
  //   </div>
  // `

  // document.body.appendChild(overlay)

  // document.getElementById('btnBatalTtd').onclick = () => document.body.removeChild(overlay)

  // document.getElementById('btnLanjutTtd').onclick = () => {
  //   namaTtdCetak = document.getElementById('selectNamaTtd').value
  //   document.body.removeChild(overlay)
  console.log('1')

    $.ajax({
      url: "{!! url('uangmukajualprint') !!}",
      type: "get",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: nobukti
      },
      success: function(res) {
        console.log('x')

        dataPrint = res

        console.log(dataPrint)

      }
    })

    let arrayDataPrint = []
    console.log('err')
    for (let i = 0; i < dataPrint.length; i+=7)
    {
      let tempArray = dataPrint.slice(i,i+7)
      arrayDataPrint.push(tempArray)
    }

    let printContent = ''
    let imageContent = document.getElementById(`imagecontainer`).innerHTML;
    let css = ''
    let hdr = ''
    let str= ''
    let ftr= ''
    console.log(dataPrint[0].Tanggal)
    let tanggalOnly = dataPrint[0].TANGGAL.split(' ')[0];


    const now = new Date()
    const jamCetak = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' })

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

      .border-bottom {
        border: bottom;
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
    hdr = `<div style="display: flex; justify-content: space-between; width: 100%">

                  <div class="pe-1" style="width: 60%">
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 15%; margin-top: 15px">
                        `+ imageContent +`
                      </div>
                      <div class="pb-1 ps-3" style="width: 85%">
                        <h2 class="m-0 pb-2">CV. SINAR MAHAKAM LESTARI</h2>
                        <div class="pb-1" style="width: 100%">JL. PRAMUKA NO. 63 RT. 11 BANJARMASIN 70249</div>
                        <div class="pb-1" style="width: 100%">TELP : 0511 - 3269593 | FAX : 0511 - 3272142</div>
                        <div class="pb-1" style="width: 100%">E-Mail : spl@indo.net</div>
                      </div>
                    </div>

                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">Supplier</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].NamaCust}</div>
                    </div>

                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%"></div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].Alamat}</div>
                    </div>
                  </div>

                  <div style="width: 40%">
                    <div style="display: flex; width: 100%">
                      <h2 class="m-0 pb-2">INVOICE UANG MUKA PENJUALAN</h2>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">No. UMJ</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].NOBUKTI}</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">Tanggal</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">`+tanggalOnly+`</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">NO. SO</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].noSO}</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">No. PO Cust</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].NoPO}</div>
                    </div>
                  </div>

                </div>
   <table
    class="detail-spb-table"
    style="width: 100%; height: 225px; max-height: 225px; font-family: sans-serif; display: table; font-size: 10px; border: 1px solid #3c3c3c;">
                <thead>
                  <tr>
                    <td class="text-center" style="width: 2%" >No.</td>
                    <td class="text-center" style="width: 5%">KODE BRG</td>
                    <td class="text-center" style="width: 30%">NAMA BARANG</td>
                    <td class="text-center" style="width: 5%">SAT</td>
                    <td class="text-center" style="width: 5%">QTY</td>
                    <td class="text-center" style="width: 5%">HARGA</td>
                    <td class="text-center" style="width: 5%">DISKON</td>
                    <td class="text-center" style="width: 5%">JUMLAH</td>
                  </tr>
                </thead> `;

    let z = 0
    let jumlahTotal = 0
    let diskonTotal = 0
    let subTotal = 0
    let ppnTotal = 0
    let totalTotal = 0

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
  tempPrintStr += `
    <tr>
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 2%;">${z+1}</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 5%;">${itemSub.KODEBRG}</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 30%;">${itemSub.namabrg}</td>

      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 5%;">${itemSub.Satuan}</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 5%;">${itemSub.Qnt ? parseFloat(itemSub.Qnt).toFixed(2) : ''}</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 5%;">${formatAngka(parseFloat(itemSub.harga).toFixed(2))}</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 5%;">${formatAngka(parseFloat(itemSub.diskon).toFixed(2))}</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 5%;">${formatAngka(parseFloat(itemSub.total).toFixed(2))}</td>
    </tr>`;
  z++;
});

// Fill remaining empty rows ï¿½ table is 225px, each row ~24px, header ~24px = ~8 total slots
const maxRows = 7;
const fillerCount = Math.max(0, maxRows - item.length);
for (let f = 0; f < fillerCount; f++) {
  tempPrintStr += `
    <tr style="height: 24px;">
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
    </tr>`;
}

tempPrintStr += `</tbody>`;
tempPrintStr += `</table>`;

         tempPrintStr += `<div style="display: flex; width: 100%; margin-top: 10px;">

  <div style="width: 50%; font-family: sans-serif; font-size: 10px;" class='text-right'>
    <table style="width: 100%; table-layout: fixed; border-collapse: collapse; margin-top: 6px;">
      <tr>
        <td class="no-border text-left" style="width: 34%; font-size:13px;">TRANSFER :
        CV. SARANA PRIMA LESTARI
        PT. BANK HSBC INDONESIA CABANG BANJARMASIN
        AC NO : 350-034-120-075 (IDR)</td>
        <td class="no-border text-center" style="width: 34%; font-size:13px;">Disetujui Oleh</td>
        <td class="no-border text-center" style="width: 33%; font-size:13px;">Konfirmasi Supplier</td>
      </tr>
      <tr style="height: 2.5rem;">
        <td class="no-border" colspan="3">&nbsp;</td>
      </tr>
      <tr>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size:12px;">Nama</p>
        </td>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size:12px;">Nama</p>
        </td>
      </tr>
      <tr>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size:12px;">Tanggal</p>
        </td>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size:12px;">Tanggal</p>
        </td>
      </tr>
    </table>
  </div>

  <div style="width: 50%; font-family: sans-serif; font-size: 10px;">

    <div style="display: flex; font-size:13px; justify-content: flex-end; width: 100%; padding-bottom: 2px;">
      <div style="width: 60% margin-left:auto"> SUB TOTAL </div>
      <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].TNNETRp).toFixed(1))}</div>
    </div>
    <div style="display: flex; font-size:13px; justify-content: flex-end; width: 100%; padding-bottom: 2px;">
      <div style="width: 60% margin-left:auto"> DISKON </div>
      <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].Tdiskon).toFixed(1))}</div>
    </div>

    <div style="display: flex; font-size:13px; justify-content: flex-end; width: 100%; padding-bottom: 2px;">
      <div style="width: 60% margin-left:auto"> DPP </div>
      <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].TNDPPRp).toFixed(1))}</div>
    </div>


    <div style="display: flex; font-size:13px; justify-content: flex-end; width: 100%; padding-bottom: 2px;">
      <div style="width: 60% margin-left:auto"> U. MUKA </div>
      <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].DPP).toFixed(1))}</div>
    </div>

    <div style="display: flex; font-size:13px; justify-content: flex-end; width: 100%; padding-bottom: 2px;">
      <div style="width: 60% margin-left:auto"> PPN ${dataPrint[0].nilaippn}</div>
      <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].PPN).toFixed(1))}</div>
    </div>
    <div style="display: flex; font-size:13px; justify-content: flex-end; width: 100%; padding-bottom: 8px; font-weight: bold;">
      <div style="width: 60% margin-left:auto"> TOTAL </div>
      <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].total).toFixed(1))}</div>
    </div>

  </div>

</div>

      <div class="footer-print-date" style='margin-bottom:-100px;'>
        <table class="m-0" style="width: 100% ; font-family: sans-serif;
        font-size: 10px ">
          <tr>
            <td class="no-border">${i+1}/${arrayDataPrint.length}        `+namaTtdCetak+`          `+tanggalOnly+`      `+jamCetak+`</td>
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





  function submitPrintTT (nobukti) {

  let _token = $('#_token').val()

  let namaTtdCetak = ''
  console.log('0')

  console.log('1')

    $.ajax({
      url: "{!! url('uangmukajualprint') !!}",
      type: "get",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: nobukti
      },
      success: function(res) {
        console.log('x')

        dataPrint = res

        console.log(dataPrint)

      }
    })

    let arrayDataPrint = []
    console.log('err')
    for (let i = 0; i < dataPrint.length; i+=7)
    {
      let tempArray = dataPrint.slice(i,i+7)
      arrayDataPrint.push(tempArray)
    }

    let printContent = ''

    let css = ''
    let hdr = ''
    let str= ''
    let ftr= ''
    console.log(dataPrint[0].Tanggal)
    let tanggalOnly = dataPrint[0].TANGGAL.split(' ')[0];


    const now = new Date()
    const jamCetak = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' })

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

      .border-bottom {
        border: bottom;
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
    hdr = `<div style="display: flex; justify-content: space-between; width: 100%">

                  <div class="pe-1" style="width: 60%">

                    <div style="display: flex; width: 100%">
                    <div class="pb-1" style="width: 20%">KEPADA YTH : ${(dataPrint[0].KodeCUST)} </div>


                    </div>


                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 75%">${dataPrint[0].NamaCust}</div>
                    </div>

                    <div style="display: flex; width: 100%">


                      <div class="pb-1" style="width: 75%">${dataPrint[0].Alamat}</div>
                    </div>
                  </div>

                  <div style="width: 40%">
                    <div style="display: flex; width: 100%">
                      <h2 class="m-0 pb-2">TANDA TERIMA</h2>

                    </div>
                    <div style="display: flex; width: 100%">

                      <h2 class="m-0 pb-2">UANG MUKA</h2>
                    </div>


                  </div>

                </div>
   <table
    class="detail-spb-table"
    style="width: 100%; height: 225px; max-height: 225px; font-family: sans-serif; display: table; font-size: 10px; border: 1px solid #3c3c3c;">
                <thead>
                  <tr>
                    <td class="text-center" style="width: 2%" >No.</td>
                    <td class="text-center" style="width: 30%">No. Uang Muka</td>
                    <td class="text-center" style="width: 5%">Nilai Uang Muka</td>
                    <td class="text-center" style="width: 5%">Keterangan</td>

                  </tr>
                </thead> `;

    let z = 0
    let jumlahTotal = 0
    let diskonTotal = 0
    let subTotal = 0
    let ppnTotal = 0
    let totalTotal = 0

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
  tempPrintStr += `
    <tr>
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 2%;">${z+1}</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 5%;">${itemSub.NOBUKTI}</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 5%;">${formatAngka(parseFloat(itemSub.NNETRp).toFixed(2))}</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 30%;">${itemSub.NoPO}</td>
    </tr>`;
  z++;
});

// Fill remaining empty rows ï¿½ table is 225px, each row ~24px, header ~24px = ~8 total slots
const maxRows = 7;
const fillerCount = Math.max(0, maxRows - item.length);
for (let f = 0; f < fillerCount; f++) {
  tempPrintStr += `
    <tr style="height: 24px;">
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>

    </tr>`;
}

tempPrintStr += `</tbody>`;
tempPrintStr += `</table>`;

         tempPrintStr += `<div style="display: flex; width: 100%; margin-top: 10px;">

  <div style="width: 50%; font-family: sans-serif; font-size: 10px;" class='text-right'>
    <table style="width: 100%; table-layout: fixed; border-collapse: collapse; margin-top: 6px;">

      <tr style="height: 2.5rem;">
        <td class="no-border" colspan="3">&nbsp;</td>
         <td class="no-border px-2">
          <p class="m-0" style="">Diterima Oleh</p>
        </td>
      </tr>






      <tr>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size:12px;">Nama</p>
        </td>

        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size:12px;">Nama</p>
        </td>
      </tr>
      <tr>
        <td class="no-border px-2">
          <p class="m-0" style="">Tanggal</p>
        </td>
        <td class="no-border px-2">
          <p class="m-0" style="">Tanggal</p>
        </td>
      </tr>
    </table>
  </div>

  <div style="width: 50%; font-family: sans-serif; font-size: 10px;">

    <div style="display: flex; font-size:13px; justify-content: flex-end; width: 100%; padding-bottom: 2px;">
      <div style="width: 60% margin-left:auto"> SUB TOTAL </div>

    </div>





  </div>

</div>

      <div class="footer-print-date" style='margin-bottom:-100px;'>
        <table class="m-0" style="width: 100% ; font-family: sans-serif;
        font-size: 10px ">
          <tr>
            <td class="no-border">${i+1}/${arrayDataPrint.length}        `+namaTtdCetak+`          `+tanggalOnly+`      `+jamCetak+`</td>
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

</script>


@endsection
