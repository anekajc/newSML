@extends('newmasterTest')
@section('buttons')


@section('page-title', 'Nota Retur Penjualan')
@section('title', 'SML - Nota Retur Penjualan')

@endsection

{{-- Rerouted to match Purchase Order's UI 1:1 via so.blade.php's own pattern,
     same as invoicejasa/fakturpajak/cetaktandaterima/perintahreturjual/
     returpenjualangudang/kreditnote before it. Only layout/toolbar/column-header
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
#page1 .card {
  display: block !important; align-items: stretch !important; padding: 0 !important;
  text-align: left !important; cursor: default !important;
}
#page1 .card:hover { transform: none !important; box-shadow: none !important; border-color: var(--border) !important; }
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
#tabel td:first-child .btn, #tabel2 td:first-child .btn, #tabel3 td:first-child .btn,
#koreksiTable td:last-child .btn, #addTable td:last-child .btn, #tabel_add_list_pelanggan td:last-child .btn,
#tabel_add_list_pic td:last-child .btn, #tabel_add_list_lokasipenerima td:last-child .btn,
#tabel_add_list_alamatkirim td:last-child .btn, #tabel_add_list_backoffice td:last-child .btn,
#tabel_add_list_sales td:last-child .btn {
  width: 30px; height: 30px; padding: 0; display: inline-flex; align-items: center;
  justify-content: center; border-radius: 7px; font-size: 13px; border: 1px solid transparent;
  box-shadow: none; transition: all .12s ease;
}
#tabel td:first-child .btn:hover, #tabel2 td:first-child .btn:hover, #tabel3 td:first-child .btn:hover,
#koreksiTable td:last-child .btn:hover, #addTable td:last-child .btn:hover, #tabel_add_list_pelanggan td:last-child .btn:hover,
#tabel_add_list_pic td:last-child .btn:hover, #tabel_add_list_lokasipenerima td:last-child .btn:hover,
#tabel_add_list_alamatkirim td:last-child .btn:hover, #tabel_add_list_backoffice td:last-child .btn:hover,
#tabel_add_list_sales td:last-child .btn:hover { filter: brightness(0.97); transform: translateY(-1px); }
#tabel td:first-child .btn-primary, #tabel2 td:first-child .btn-primary, #tabel3 td:first-child .btn-primary,
#koreksiTable td:last-child .btn-primary, #addTable td:last-child .btn-primary, #tabel_add_list_pelanggan td:last-child .btn-primary,
#tabel_add_list_pic td:last-child .btn-primary, #tabel_add_list_lokasipenerima td:last-child .btn-primary,
#tabel_add_list_alamatkirim td:last-child .btn-primary, #tabel_add_list_backoffice td:last-child .btn-primary,
#tabel_add_list_sales td:last-child .btn-primary { color: #2563eb; border-color: #cfdcff; background: #e8edff; }
#tabel2 td:first-child .btn-warning, #tabel3 td:first-child .btn-warning, #koreksiTable td:last-child .btn-warning,
#addTable td:last-child .btn-warning, #tabel_add_list_pelanggan td:last-child .btn-warning, #tabel_add_list_pic td:last-child .btn-warning,
#tabel_add_list_lokasipenerima td:last-child .btn-warning, #tabel_add_list_alamatkirim td:last-child .btn-warning,
#tabel_add_list_backoffice td:last-child .btn-warning, #tabel_add_list_sales td:last-child .btn-warning { color: #b45309; border-color: #fbe3bd; background: #fef3e0; }
#tabel2 td:first-child .btn-success, #tabel3 td:first-child .btn-success, #koreksiTable td:last-child .btn-success, #addTable td:last-child .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
#tabel2 td:first-child .btn-danger, #tabel3 td:first-child .btn-danger, #koreksiTable td:last-child .btn-danger, #addTable td:last-child .btn-danger { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }
#tabel thead th, #tabel2 thead th, #tabel3 thead th {
  background: #f8f9fb !important; color: #6b7280 !important; font-size: 12px; text-transform: uppercase;
  letter-spacing: .04em; font-weight: 600; border-bottom: 1px solid #e7e9ee; border-top: none;
}
#tabel tbody tr:nth-of-type(odd), #tabel2 tbody tr:nth-of-type(odd), #tabel3 tbody tr:nth-of-type(odd) { background-color: #fbfbfc; }
#tabel tbody tr:hover, #tabel2 tbody tr:hover, #tabel3 tbody tr:hover { background-color: #f5f3ff; }

/* Hide action buttons until the row is hovered */
#tabel tbody .action-buttons-wrap,
#tabel2 tbody .action-buttons-wrap {
  opacity: 0;
  visibility: hidden;
  transform: translateX(-6px);
  transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
}
/* Show them when hovering the table row */
#tabel tbody tr:hover .action-buttons-wrap,
#tabel2 tbody tr:hover .action-buttons-wrap,
#tabel tbody tr:focus-within .action-buttons-wrap,
#tabel2 tbody tr:focus-within .action-buttons-wrap {
  opacity: 1;
  visibility: visible;
  transform: translateX(0);
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

/* Perbesar glyph + pada tombol Add tanpa mengubah ukuran bingkai tombol.
   line-height: 0 membuat ikon tidak menambah tinggi baris, jadi tinggi
   tombol tetap sama dengan tombol Detail di sebelahnya. Port 1:1 dari
   returpenjualangudang.blade.php / returpembeliangudang.blade.php. */
.btn .bi-plus {
  font-size: 1.5rem;
  line-height: 0;
  vertical-align: middle;
}

.data-table th,
.data-table td {
  white-space: nowrap;
}
</style>
@endsection



@section('content')
<div id="page1" class="container-fluid mainpage">

  <div class="container-fluid">

    <!-- <div id="qrcode"></div> -->
    <div class="row" >
      <div class="col-6 text-left">
        {{-- <h2 style="margin-top: -85px">Nota Retur Penjualan</h2> --}}
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
          <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">SPB Retur Gudang</a>
          <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">Invoice Otorisasi</a>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body" style="padding:0;">
  <div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
      <div class="row">
        <div class="col-12" style="overflow:auto;">
          <div class="container-fluid" style="padding:0; margin:0; width:100%;">
            <div class="po-toolbar">
              <input type="search" id="nrpSearch1" class="po-search-inp" placeholder="Cari data">
              <div class="po-len-wrap"><label for="nrpLen1">Tampilkan</label>
                <select id="nrpLen1" class="po-len-inp"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="-1">Semua</option></select>
              </div>
            </div>
            <div id="rtBarTabel"></div>
            <table id="tabel" class="data-table">
              <thead style="white-space:nowrap;"></thead>
              <tbody id="tabel_data" class="text-left" ></tbody>
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

            {{-- Filter modal: port 1:1 dari modalFilter milik perintahreturjual.blade.php.
                 tabel2 (Belum Otorisasi) + tabel3 (Sudah Otorisasi) digabung jadi satu
                 tabel di sini dengan Status dropdown, sama seperti PRJ/RPG/SJ/UMJ. --}}
            <div class="modal fade rt-filter" id="modalFilterNRP">
              <div class="modal-dialog modal-md">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">
                      <i class="bi bi-funnel"></i>
                      Filter Data
                      <span class="rt-active-badge" id="nrpFilterBadge">0 aktif</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterNRP').modal('hide')">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>

                  <div class="modal-body">
                    <div class="rt-section">
                      <div class="rt-group-label">Status</div>
                      <div>
                        <label class="rt-field-label" for="input_filternrp">Status Otorisasi</label>
                        <select class="rt-native" id="input_filternrp">
                          <option value=0 selected>Semua</option>
                          <option value=1>Belum Otorisasi</option>
                          <option value=2>Sudah Otorisasi</option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="modal-footer">
                    <button type="button" class="rt-reset-link" onclick="nrpResetFilterFields()">Reset semua</button>
                    <div class="rt-footer-buttons">
                      <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
                        onclick="$('#modalFilterNRP').modal('hide')">Batal</button>
                      <button type="button" class="rt-btn rt-btn-primary" onclick="buttonFilterNRP(); $('#modalFilterNRP').modal('hide');">Terapkan</button>
                    </div>
                  </div>

                </div>
              </div>
            </div>

            <div class="po-toolbar">
              <div class="po-filter-wrap">
                <label>Periode</label>
                <input type="date" onchange="onChangePeriodeNRP()" class="po-filter-inp" id="input_tanggalawal_nrp" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d') !!}">
                <span class="po-filter-sep">s/d</span>
                <input type="date" onchange="onChangePeriodeNRP()" class="po-filter-inp" id="input_tanggalakhir_nrp" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d') !!}">
              </div>
              <input type="search" id="nrpSearch2" class="po-search-inp" placeholder="Cari data">
              <div class="po-len-wrap"><label for="nrpLen2">Tampilkan</label>
                <select id="nrpLen2" class="po-len-inp"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="-1">Semua</option></select>
              </div>
              <button class="po-btn-filter" type="button" onclick="$('#modalFilterNRP').modal('show')">
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

  </div>
  </div>
  </div>

  </div>

</div>

<div id="page2" class="container-fluid mainpage" style="display: none">
  <!-- <div id="" class="modal-content "> -->
    <div id= "modalAdd" class="showhideform">
      <div id='formBsGrid'>

      <div class="container-fluid">

        <!-- <div id="qrcode"></div> -->
        <div class="row">
          <div class="col-6 text-left">
            {{-- <h1>Nota Retur Penjualan</h1> --}}
          </div>
          <div class="col-6 text-right">
            <button type="button" class="btn btn-danger btn-lg " style="height: 30px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()">CLOSE</button>
          </div>
        </div>
      <!-- <button onclick="loadAll()">tes</button> -->
      </div>
      <!-- <h5 class="modal-title" id="modalTitleDetail">Detail</h5> -->

    <div class="mt-4">
    <!-- <div class="modal-body"> -->
      <!-- <h1>Tes Modal</h1> -->

      <div class="container-fluid">
        <input type="hidden" name="noUrut" id="input_add_nourut" value="" />
        <div class="row">

          <div class="col-md-3">
            <div class="row">

              <div class="col-md-12">
                <div class="row">
                  <div class="col-4">
                    <div class="form-group">
                      <label>Customer</label>
                    </div>
                  </div>
                  <!-- <div class="col-3 text-right">

                </div> -->
                  <div class="col-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_add_customer" placeholder="" disabled>
                    </div>
                  </div>
                </div>
              </div>


            <div class="col-md-12" style="margin-top: -10px">
              <div class="form-group">
                <textarea  style="width: 100%; height:75px !important; resize: none" rows=5  class="form-control" id="input_add_alamatcustomer"  disabled></textarea>
              </div>

            </div>


            </div>
          </div>
          <!-- <div class="col-md-3">
            <div class="row">

            </div>
          </div> -->
          <div class="col-md-3">
            <div class="row">


              <div class="col-md-12">
                <div class="row">
                  <div class="col-4">
                    <div class="form-group">
                      <label>No Bukti</label>
                    </div>
                  </div>
                  <!-- <div class="col-3 text-right">

                </div> -->
                  <div class="col-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_add_nobukti" placeholder="" disabled>
                    </div>
                  </div>
                </div>
              </div>



              <div class="col-md-12" style="margin-top: -10px">
                <div class="row">
                  <div class="col-4">
                    <div class="form-group">
                      <label>No Invoice</label>
                    </div>
                  </div>
                  <!-- <div class="col-3 text-right">

                </div> -->
                  <div class="col-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_add_noinvoice" placeholder="" disabled>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-12" style="margin-top: -15px">
                <div class="row">
                  <div class="col-4">
                    <div class="form-group">
                      <label>Catatan</label>
                    </div>
                  </div>
                  <div class="col-8" >
                    <div class="form-group">
                      <textarea  style="width: 100%; height: 75px !important; resize: none" rows=3  class="form-control" id="input_add_catatan" onblur="onChangeHeader('catatan' , '' , 'input_add_catatan')"  disabled></textarea>
                    </div>

                  </div>

                </div>

              </div>


            </div>
          </div>

          <!-- <div class="col-8">

          </div> -->
          <div class="col-md-3">
            <div class="row">

              <div class="col-md-12" >
                <div class="row">
                  <div class="col-4">
                    <div class="form-group">
                      <label>Tanggal</label>
                    </div>
                  </div>
                  <div class="col-8">
                    <div class="form-group">
                      <input type="date" class="form-control text-center" id="input_add_tanggal" value="{!! date('Y-m-d') !!}"  >
                    </div>
                  </div>
                </div>

              </div>

              <div class="col-md-12" style="margin-top: -10px">
                <div class="row">
                  <div class="col-4">
                    <div class="form-group">
                      <label>No Retur</label>
                    </div>
                  </div>
                  <!-- <div class="col-3 text-right">

                </div> -->
                  <div class="col-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_add_noretur" placeholder="" disabled>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-12" style="margin-top: -10px">
                <div class="row">
                  <div class="col-4">
                    <div class="form-group">
                      <label>Valas</label>
                    </div>
                  </div>
                  <!-- <div class="col-3 text-right">

                </div> -->
                  <div class="col-8">
                    <div class="form-group input-group">
                      <input type="text" class="form-control" id="input_add_valas" placeholder="" disabled>
                      <button onclick="buttonAddListValas()" id="buttonAddListValas" class="btn btn-chip-biru btn-sm"><i class="bi bi-search"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-12" style="margin-top: -10px">
                <div class="row">
                  <div class="col-4">
                    <div class="form-group">
                      <label>Kurs</label>
                    </div>
                  </div>
                  <!-- <div class="col-3 text-right">

                </div> -->
                  <div class="col-8">
                    <div class="form-group">
                      <input type="number"  class="form-control text-right" id="input_add_kurs" placeholder="" disabled>
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
                  <div class="col-4">
                    <div class="form-group">
                      <label>Tipe PPN</label>
                    </div>
                  </div>
                  <!-- <div class="col-3 text-right">

                </div> -->
                  <div class="col-8">

                    <select id="input_add_tipeppn" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" onchange="onChangeHeader('PPN' , '' , 'input_add_tipeppn')">
                      <option value=0 selected>None</option>
                      <option value=1 >Exclude</option>
                      <option value=2 >Include</option>
                    </select>

                  </div>
                </div>
              </div>
              <div class="col-md-12" style="margin-top: -10px">
                <div class="row">
                  <div class="col-4">
                    <div class="form-group">
                      <label>Pembayaran</label>
                    </div>
                  </div>
                  <!-- <div class="col-3 text-right">

                </div> -->
                  <div class="col-8">
                    <select id="input_add_tipebayar" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" onchange="onChangePembayaran('TIPEBAYAR' , 'input_add_tipebayar')">
                      <option value=0 selected>Tunai</option>
                      <option value=1 >Kredit</option>
                    </select>
                  </div>
                </div>
              </div>


              <div class="col-md-12" style="margin-top: -10px">
                <div class="row">
                  <div class="col-4">
                    <div class="form-group">
                      <label>Hari</label>
                    </div>
                  </div>
                  <!-- <div class="col-3 text-right">

                </div> -->
                  <div class="col-8">
                    <div class="form-group">
                      <input type="text" class="form-control text-right" id="input_add_hari" onblur="onChangeHeader('hari' , '' , 'input_add_hari')" placeholder="">
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-12" style="margin-top: -10px">
                <div class="row">
                  <div class="col-4">
                    <div class="form-group">
                      <label>Sales</label>
                    </div>
                  </div>
                  <!-- <div class="col-3 text-right">

                </div> -->
                  <div class="col-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_add_namasales" placeholder="" disabled>
                    </div>
                  </div>
                </div>
              </div>

            </div>

          </div>






        </div>


        </div>



      <!-- </div> -->



    <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;" >

          <table id="addTable" class="data-table">
            <thead class="text-center">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Kode Brg</th>
                <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
                <th style="padding: 4px 12px;" scope="col">Nama Produk</th>
                <th style="padding: 4px 12px;" scope="col">Sat Produk</th>
                <th style="padding: 4px 12px;" scope="col">Qty</th>
                <th style="padding: 4px 12px;" scope="col">Sat</th>
                <th style="padding: 4px 12px;" scope="col">Harga</th>
                <th style="padding: 4px 12px;" scope="col">Diskon</th>
                <th style="padding: 4px 12px;" scope="col">Sub Total</th>
                <th style="padding: 4px 12px;" scope="col" class="nrp-col-actions">Actions</th>

              </tr>
            </thead>


            <tbody id="addTableData" class="" >
              <tr >

                  <td colspan=10 class="text-center">Belum ada data</td>

            </tr>
            <tr style="background-color: ivory; font-weight: bold" >
              <td colspan=8 class="text-right">Total</td>
              <td colspan=1 class="text-right">123.456</td>
              <td colspan=1 class="text-right">7777</td>
              <td colspan=1 class="text-right"></td>
            </tr>

            </tbody>


          </table>
    </div>

    </div>
    <hr/>
    <div class="container-fluid">
      <div class="row">
        <div class="col" style="width:20%">
          <div class="row">
            <div class="col-3">
              <div class="form-group">
                <label>Disc %</label>
              </div>
            </div>


            <div class="col-9" >
              <div class="form-group">
                <input type="text" class="form-control text-right" id="input_add_headerdiscp" value ="0.00" disabled>
              </div>
            </div>
          </div>
        </div>
        <div class="col" style="width:20%">
          <div class="row">
            <div class="col-3">
              <div class="form-group">
                <label>Disc Rp</label>
              </div>
            </div>


            <div class="col-9" >
              <div class="form-group">
                <input type="text" class="form-control text-right" id="input_add_headerdiscrp" value ="0.00" disabled>
              </div>
            </div>
          </div>
        </div>
        <div class="col" style="width:20%">
          <div class="row">
            <div class="col-3">
              <div class="form-group">
                <label>DPP</label>
              </div>
            </div>


            <div class="col-9" >
              <div class="form-group">
                <input type="text" class="form-control text-right" id="input_add_headerdpp" value ="0.00" disabled>
              </div>
            </div>
          </div>
        </div>
        <div class="col" style="width:20%">
          <div class="row">
            <div class="col-3">
              <div class="form-group">
                <label>PPN</label>
              </div>
            </div>


            <div class="col-9" >
              <div class="form-group">
                <input type="text" class="form-control text-right" id="input_add_headerppn" value ="0.00" disabled>
              </div>
            </div>
          </div>
        </div>
        <div class="col" style="width:20%">
          <div class="row">
            <div class="col-3">
              <div class="form-group">
                <label>Grand Total</label>
              </div>
            </div>


            <div class="col-9" >
              <div class="form-group">
                <input type="text" class="form-control text-right" id="input_add_headergrandtotal" value ="0.00" disabled>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>



    <!-- <div class="container-fluid">
      <div class="row ">
      <div class="col-md-12 mt-2 text-right">
      <button type="button" class="btn btn-primary btn-lg" style="
        height: 30px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        transition: background-color 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
        onclick="buttonAddAddItem()" class="btn btn-secondary"><b>+ Tambah Item</b></button>
      </div>
      </div>

    </div> -->

    <div id="formAddAdd" class="container-fluid showhideitem">
      <!-- <div class="line"></div> -->
      <!-- <div class="row"> -->

      <div class="col-12">


      <hr/>
      <div class="row">
        <div class="col-md-12">
          <h4>Add Item</h4>
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
            <input id="AddAddKodeBrg" type="text" class="form-control" disabled>
            <button type="button" id="buttonAddListBarang" onclick="buttonAddListBarang()" class="btn btn-primary" >+</button>

          </div>
        </div>

      </div>

    </div>
    <div class="col-md-3">

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
          <input id="AddAddNamaBrg" type="text" class="form-control" disabled>
        </div>
        <div class="col-md-4">

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
          <input id="AddAddInputQty" type="number" value='0.00' class="form-control text-right" style="width: 75%">

          <input id="AddAddInputSat" type="text" value='PCS' class="form-control text-center" disabled style="width: 25%">



        </div>


  </div>
  </div>


  <div class="col-md-12" style="margin-top: -10px">

  <div class="row">

      <div class="col-md-2">
        <div class="form-group">
        <label>Harga</label>
      </div>
      </div>

      <div class="col-md-4">
        <input id="AddAddInputHarga" type="number" value='0.00' class="form-control text-right" onchange="onChangeInputAddHarga()">
      </div>


    </div>
  </div>

  <div class="col-md-12" style="margin-top: -10px">

  <div class="row">

      <div class="col-md-2">
        <div class="form-group">
        <label>Disc %</label>
      </div>
      </div>

      <div class="col-md-2">
        <input id="AddAddInputDiscPersen" type="number" value='0.00' class="form-control text-right" onchange="onChangeInputAddDisc()">
      </div>


    </div>
  </div>

  <div class="col-md-12" style="margin-top: -10px">

  <div class="row">

      <div class="col-md-2">
        <div class="form-group">
        <label>Disc Rp</label>
      </div>
      </div>

      <div class="col-md-4">
        <input id="AddAddInputDiscRp" type="number" value='0.00' class="form-control text-right" onchange="onChangeInputAddDiscRp()">
      </div>


    </div>
  </div>






        <!-- <input type="text" class="form-control" placeholder="Email" id="demo" name="email">
    <div class="input-group-append">
      <span class="input-group-text">@example.com</span>
    </div> -->


      </div>
    </div>


    <!-- <div class="col-6 ">
      <div class="row">



      </div> -->
    <!-- </div> -->




    <div class="row mt-2" style="margin-top: 0">
      <div class="col-md-12 text-right mt-4">
        <button type="button" class="btn btn-secondary" onclick="buttonAddBatal()" style="height: 30px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;">Batal</button>

        <button id="buttonSubmitAddAdd" type="button" onclick="submitAddAdd()" class="btn btn-primary" style="height: 30px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;">Submit Add</button>
        <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
      </div>

    </div>

  </div>



  <div id="formAddEdit" class="container-fluid showhideitem">
    <!-- <div class="line"></div> -->
    <!-- <div class="row"> -->

    <div class="col-12">


    <hr/>
    <div class="row">
      <div class="col-md-12">
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
          <input id="AddEditKodeBrg" type="text" class="form-control" disabled>

        </div>
      </div>

    </div>

  </div>
  <div class="col-md-3">

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
        <input id="AddEditNamaBrg" type="text" class="form-control" disabled>
      </div>
      <div class="col-md-4">

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
        <input id="AddEditInputQty" disabled type="number" value='0.00' class="form-control text-right" style="width: 75%">

        <input id="AddEditInputSat" type="text" value='PCS' class="form-control text-center" disabled style="width: 25%">



      </div>


</div>
</div>


<div class="col-md-12" style="margin-top: -10px">

<div class="row">

    <div class="col-md-2">
      <div class="form-group">
      <label>Harga</label>
    </div>
    </div>

    <div class="col-md-4">
      <input id="AddEditInputHarga" type="number" value='0.00' class="form-control text-right" onchange="onChangeInputEditHarga()">
    </div>


  </div>
</div>

<div class="col-md-12" style="margin-top: -10px">

<div class="row">

    <div class="col-md-2">
      <div class="form-group">
      <label>Disc %</label>
    </div>
    </div>

    <div class="col-md-2">
      <input id="AddEditInputDiscPersen" type="number" value='0.00' class="form-control text-right" onchange="onChangeInputEditDisc()">
    </div>


  </div>
</div>

<div class="col-md-12" style="margin-top: -10px">

<div class="row">

    <div class="col-md-2">
      <div class="form-group">
      <label>Disc Rp</label>
    </div>
    </div>

    <div class="col-md-4">
      <input id="AddEditInputDiscRp" type="number" value='0.00' class="form-control text-right" onchange="onChangeInputEditDiscRp()">
    </div>


  </div>
</div>






      <!-- <input type="text" class="form-control" placeholder="Email" id="demo" name="email">
  <div class="input-group-append">
    <span class="input-group-text">@example.com</span>
  </div> -->


    </div>
  </div>


  <!-- <div class="col-6 ">
    <div class="row">



    </div> -->
  <!-- </div> -->




  <div class="row mt-2" style="margin-top: 0">
    <div class="col-md-12 text-right mt-4">
      <button type="button" class="btn btn-danger" onclick="buttonAddBatal()" style="height: 30px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;">Batal</button>

      <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-chip-biru" style="height: 30px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;">Simpan</button>
      <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
    </div>

  </div>

</div>


    </div>



    <div id= "modalKoreksi" class="showhideform">
      <div class="container-fluid">

        <!-- <div id="qrcode"></div> -->
        <div class="row" style="margin-top: -30px">
          <div class="col-6 text-left">
            <h1>Form Koreksi</h1>
          </div>
          <div class="col-6 text-right">
            <button type="button" class="btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >CLOSE</button>
          </div>
        </div>
      <!-- <button onclick="loadAll()">tes</button> -->
      </div>
      <!-- <h5 class="modal-title" id="modalTitleDetail">Detail</h5> -->




    <div id="" class="mt-4">
    <div class="">
      <!-- <h1>Tes Modal</h1> -->

      <div class="container-fluid">
        <input type="hidden" name="noUrut" id="input_koreksi_nourut" value="" />
        <div class="row">

          <div class="col-3">
            <div class="row">


            <div class="col-4">
              <div class="form-group">
                <label>Customer</label>
              </div>
            </div>

            <div class="col-8">
              <div class="form-group input-group">
                <input type="text" class="form-control" id="input_koreksi_customer" placeholder="" disabled>
              </div>
            </div>


            <div class="col-12" style="margin-top: -10px">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none" rows=4  class="form-control" id="input_koreksi_alamat"  disabled></textarea>
              </div>

            </div>
            <div class="col-12" style="margin-top: -10px">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_koreksi_alamatx"  disabled></textarea>
              </div>

            </div>




            </div>
          </div>
          <div class="col-3">
            <div class="row">
              <div class="col-4">
                <div class="form-group">
                  <label>No Bukti</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_koreksi_nobukti" placeholder="No bukti" disabled>
                </div>
              </div>
            </div>

            <div class="row" style="margin-top: -10px">
              <!-- <div class="col-6">
                <div class="row"> -->

                <div class="col-4">
                  <div class="form-group">
                    <label>No PO</label>
                  </div>
                </div>
                <div class="col-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_koreksi_nopo" placeholder="" disabled>
                  </div>
                </div>

                <!-- </div>

              </div> -->



            </div>

            <!-- <div class="row"> -->
              <!-- <div class="col-6"> -->
                <div class="row" style="margin-top: -10px">
                  <div class="col-4">
                    <div class="form-group">
                      <label>Pembayaran</label>
                    </div>
                  </div>
                  <div class="col-8">

                    <select id="input_koreksi_pembayaran" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
                      <option value=0 selected>Tunai</option>
                      <option value=1 >Kredit</option>
                    </select>
                  </div>
                </div>

              <!-- </div> -->
              <!-- <div class="col-6"> -->
                <div class="row" style="margin-top: -10px">
                  <div class="col-4">
                    <div class="form-group">
                      <label>Hari</label>
                    </div>
                  </div>
                  <div class="col-8">
                    <div class="form-group">
                      <input type="text" class="form-control text-right" id="input_koreksi_hari" placeholder="" disabled>
                    </div>
                  </div>
                </div>

              <!-- </div> -->



            <!-- </div> -->






          </div>
          <div class="col-3">
            <div class="row">
              <div class="col-4">
                <div class="form-group">
                  <label>Tgl</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="date" class="form-control text-center" id="input_koreksi_tanggal" value="{!! date('Y-m-d') !!}" disabled >
                </div>
              </div>
            </div>



            <div class="row" style="margin-top: -10px">
              <!-- <div class="col-6">
                <div class="row"> -->
                  <div class="col-4">
                    <div class="form-group">
                      <label>Tipe PPN</label>
                    </div>
                  </div>
                  <div class="col-8">
                    <div class="form-group">
                      <select id="input_koreksi_tipeppn" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" disabled >
                        <option value=0 selected>None</option>
                        <option value=1 >Exclude</option>

                        <option value=2 >Include</option>
                      </select>
                      <!-- <input type="text" class="form-control" id="input_koreksi_tipeppn" placeholder="" disabled> -->
                    </div>
                  </div>
                <!-- </div>

              </div> -->
              <!-- <div class="col-6">
                <div class="row"> -->

                <!-- </div>

              </div> -->





            </div>


            <div class="row" style="margin-top: -10px">
              <!-- <div class="col-6">
                <div class="row"> -->
                  <div class="col-4">
                    <div class="form-group">
                      <label>Sales</label>
                    </div>
                  </div>
                  <div class="col-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_koreksi_sales" placeholder="" disabled>
                    </div>
                  </div>
                <!-- </div>

              </div> -->
              <!-- <div class="col-6">
                <div class="row"> -->



                <!-- </div>

              </div> -->





            </div>


            <div class="row" style="margin-top: -10px">
              <div class="col-4">
                <div class="form-group">
                  <label>Uang Muka</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="number" class="form-control text-right" id="input_koreksi_uangmuka" placeholder="" onblur="onChangeUangMuka('nuangmuka' , 'input_koreksi_uangmuka' , 'uang muka')">
                </div>
              </div>
            </div>



          </div>

          <div class="col-3">
            <div class="row">


              <div class="col-4">
                <div class="form-group">
                  <label>Valas</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_koreksi_valas" placeholder="" disabled>
                </div>
              </div>

              <div class="col-12" style="margin-top: -10px">
                <div class="row">
                  <div class="col-4">
                    <div class="form-group">
                      <label>Kurs</label>
                    </div>
                  </div>
                  <div class="col-8">
                    <div class="form-group">
                      <input type="number" class="form-control text-right" id="input_koreksi_kurs" placeholder="" onblur="onChangeKurs('kurs' , 'input_koreksi_kurs')">
                    </div>
                  </div>
                </div>

              </div>


              <div class="col-12" style="margin-top: -10px">
                <div class="row">
                  <div class="col-4">
                    <div class="form-group">
                      <label>Catatan</label>
                    </div>
                  </div>
                  <div class="col-8">
                    <div class="form-group">
                      <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_koreksi_catatan"  onblur="onChangeHeader('catatan' , '' ,'input_koreksi_catatan')"></textarea>
                    </div>
                  </div>
                </div>

              </div>






            </div>

          </div>

        </div>



        </div>

      </div>



    <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">

          <table id="koreksiTable" class="data-table">
            <thead class="text-center">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Kode brg</th>
                <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
                <th style="padding: 4px 12px;" scope="col">Nama Produk</th>
                <th style="padding: 4px 12px;" scope="col">No SPB</th>
                <th style="padding: 4px 12px;" scope="col">Qty</th>
                <th style="padding: 4px 12px;" scope="col">Sat</th>
                <th style="padding: 4px 12px;" scope="col">Harga</th>
                <th style="padding: 4px 12px;" scope="col">Diskon</th>
                <th style="padding: 4px 12px;" scope="col">Sub Total</th>
                <th style="padding: 4px 12px;" scope="col">Keterangan</th>

                <th style="padding: 4px 12px;" scope="col">Actions</th>

              </tr>
            </thead>


            <tbody id="koreksiTableData" class="" >
              <tr >

                  <td colspan=11 class="text-center">Belum ada data</td>

            </tr>

            </tbody>


          </table>
    </div>

    </div>

    <div class="container-fluid mt-4">
      <div id="" class="row">
        <div class="col-12 text-right">
          <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->

          <!-- <button id="" type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button> -->
        </div>
      </div>
    </div>

    </div>















    <!-- </div> -->



  </div>
</div>


<div id="page3" class="container-fluid mainpage" style="display: none">

  <!-- <div id="" class="modal-content "> -->




    <div id= "" class="">
      <div class="container-fluid">

        <!-- <div id="qrcode"></div> -->
        <div class="row" style="margin-top: -30px">
          <div class="col-6 text-left">
            <h1>Form Otorisasi</h1>
          </div>
          <div class="col-6 text-right">
            <button type="button" class="btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >CLOSE</button>
          </div>
        </div>
      <!-- <button onclick="loadAll()">tes</button> -->
      </div>


    <div id="" class="">
    <div class="modal-body">
      <!-- <h1>Tes Modal</h1> -->

      <div class="container-fluid">
        <!-- <input type="hidden" name="noUrut" id="input_koreksi_nourut" value="" /> -->
        <div class="row">

          <div class="col-3">
            <div class="row">


            <div class="col-4">
              <div class="form-group">
                <label>Customer</label>
              </div>
            </div>
            <!-- <div class="col-3 text-right">

          </div> -->
            <div class="col-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_otorisasi_customer" placeholder="" disabled>
              </div>
            </div>


            <div class="col-12" style="margin-top: -10px">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none" rows=4  class="form-control" id="input_otorisasi_alamat"  disabled></textarea>
              </div>

            </div>
            <div class="col-12" style="margin-top: -10px">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_otorisasi_alamatx"  disabled></textarea>
              </div>

            </div>




            </div>
          </div>
          <div class="col-3">
            <div class="row">
              <div class="col-4">
                <div class="form-group">
                  <label>No Bukti</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_otorisasi_nobukti" placeholder="No bukti" disabled>
                </div>
              </div>


              <div class="col-12" style="margin-top: -10px">
                <div class="row">
                  <div class="col-4">
                    <div class="form-group">
                      <label>No PO</label>
                    </div>
                  </div>
                  <div class="col-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_otorisasi_nopo" placeholder="" disabled>
                    </div>
                  </div>
                </div>

              </div>

              <div class="col-12" style="margin-top: -10px">
                <div class="row">
                  <div class="col-4">
                    <div class="form-group">
                      <label>Pembayaran</label>
                    </div>
                  </div>
                  <div class="col-8">
                    <div class="form-group">
                      <select id="input_otorisasi_pembayaran" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
                        <option value=0 selected>Tunai</option>
                        <option value=1 >Kredit</option>
                      </select>

                    </div>
                  </div>

                </div>

              </div>

              <div class="col-12" style="margin-top: -10px">
                <div class="row">
                  <div class="col-4">
                    <div class="form-group">
                      <label>Hari</label>
                    </div>
                  </div>
                  <div class="col-8">
                    <div class="form-group">
                      <input type="text" class="form-control text-right" id="input_otorisasi_hari" placeholder="" disabled>
                    </div>
                  </div>

                </div>

              </div>




            </div>

            <div class="row">
              <!-- <div class="col-6">
                <div class="row"> -->

                <!-- </div>

              </div> -->



            </div>

            <div class="row">
              <!-- <div class="col-6">
                <div class="row"> -->

                <!-- </div>

              </div> -->
              <!-- <div class="col-6">
                <div class="row"> -->

                <!-- </div>

              </div> -->



            </div>






          </div>
          <div class="col-3">
            <div class="row">
              <div class="col-4">
                <div class="form-group">
                  <label>Tgl</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="date" class="form-control text-center" id="input_otorisasi_tanggal" value="{!! date('Y-m-d') !!}" disabled >
                </div>
              </div>
            </div>



            <div class="row" style="margin-top: -10px">
              <!-- <div class="col-6">
                <div class="row"> -->
                  <div class="col-4">
                    <div class="form-group">
                      <label>Tipe PPN</label>
                    </div>
                  </div>
                  <div class="col-8">
                    <div class="form-group">
                      <select id="input_otorisasi_tipeppn" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" disabled >
                        <option value=0 selected>None</option>
                        <option value=1 >Exclude</option>

                        <option value=2 >Include</option>
                      </select>
                      <!-- <input type="text" class="form-control" id="input_otorisasi_tipeppn" placeholder="" disabled> -->
                    </div>
                  </div>
                <!-- </div>

              </div> -->
              <!-- <div class="col-6">
                <div class="row"> -->

                <!-- </div>

              </div> -->





            </div>


            <div class="row" style="margin-top: -10px">
              <!-- <div class="col-6">
                <div class="row"> -->
                  <div class="col-4">
                    <div class="form-group">
                      <label>Sales</label>
                    </div>
                  </div>
                  <div class="col-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_otorisasi_sales" placeholder="" disabled>
                    </div>
                  </div>
                <!-- </div>

              </div> -->
              <!-- <div class="col-6">
                <div class="row"> -->

                <!-- </div>

              </div> -->





            </div>

            <div class="row" style="margin-top: -10px">
              <div class="col-4">
                <div class="form-group">
                  <label>Uang Muka</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="number" class="form-control text-right" id="input_otorisasi_uangmuka" placeholder="" disabled>
                </div>
              </div>
            </div>



          </div>

          <div class="col-3">
            <div class="row">

              <div class="col-4">
                <div class="form-group">
                  <label>Valas</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_otorisasi_valas" placeholder="" disabled>
                </div>
              </div>

              <div class="col-12" style="margin-top: -10px">
                <div class="row">
                  <div class="col-4">
                    <div class="form-group">
                      <label>Kurs</label>
                    </div>
                  </div>
                  <div class="col-8">
                    <div class="form-group">
                      <input type="number" class="form-control text-right" id="input_otorisasi_kurs" placeholder="" disabled>
                    </div>
                  </div>
                </div>

              </div>

              <div class="col-12" style="margin-top: -10px">
                <div class="row">
                  <div class="col-4">
                    <div class="form-group">
                      <label>Catatan</label>
                    </div>
                  </div>
                  <div class="col-8">
                    <div class="form-group">
                      <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_otorisasi_catatan"  disabled></textarea>
                    </div>
                  </div>
                </div>

              </div>






            </div>

          </div>

          <!-- <div class="col-8">

          </div>
          <div class="col-4">
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <label>Tanggal</label>
                </div>
              </div>
              <div class="col-12">
                <div class="form-group">
                  <input type="date" class="form-control text-center" id="input_otorisasi_tanggal" value="{!! date('Y-m-d') !!}"  >
                </div>
              </div>
            </div>

          </div> -->






        </div>










        </div>



      </div>



    <div class="container-fluid" style="overflow-x: auto;padding:0; margin:0;">

          <table id="otorisasiTable" class="data-table">
            <thead class="text-center">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Kode brg</th>
                <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
                <th style="padding: 4px 12px;" scope="col">Nama Produk</th>
                <th style="padding: 4px 12px;" scope="col">No SPB</th>
                <th style="padding: 4px 12px;" scope="col">Qty</th>
                <th style="padding: 4px 12px;" scope="col">Sat</th>
                <th style="padding: 4px 12px;" scope="col">Harga</th>
                <th style="padding: 4px 12px;" scope="col">Diskon</th>
                <th style="padding: 4px 12px;" scope="col">Sub Total</th>
                <th style="padding: 4px 12px;" scope="col">Keterangan</th>

                <!-- <th scope="col">Actions</th> -->

              </tr>
            </thead>


            <tbody id="otorisasiTableData" class="" >
              <tr >

                  <td colspan=10 class="text-center">Belum ada data</td>

            </tr>

            </tbody>


          </table>
    </div>

    </div>


    <div class="container-fluid">
      <div id="" class="row ">
        <div class="col-12 text-right">


          <button id="" type="button" onclick="submitOtorisasi()" class="btn btn-primary" style="height: 30px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;">Submit</button>
          <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
        </div>
        </div>
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
      </div>
    </div>


    </div>




    <div class="modal fade"  id="formNew" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" >
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="exampleModalLabel">Proses IVRJ</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div id="" class="">
            <div class="modal-body" >

            <div class="container-fluid mt-4" >

              <div class="row">


                <div class="col-md-4">
                  <div class="row">
                    <!-- <div class="col-md-12" >
                      <div class="row"> -->


                    <div class="col-md-4">
                      <div class="form-group">
                        <label>No Bukti</label>
                      </div>
                    </div>
                    <div class="col-md-8">
                      <div class="form-group">
                        <input type="text" class="form-control" id="input_addnew_nobukti" placeholder="No Bukti" disabled>
                        <input type="hidden" class="form-control" id="input_addnew_nourut" placeholder="No Bukti" disabled>
                      </div>
                    <!-- </div>
                  </div> -->
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="row">
                <!-- <div class="col-md-12" >
                  <div class="row"> -->


                <div class="col-md-4">
                  <div class="form-group">
                    <label>No Retur</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_addnew_noretur" placeholder="" disabled>
                  </div>
                <!-- </div>
              </div> -->
            </div>
          </div>
        </div>


        <div class="col-md-4">
          <div class="row">
            <!-- <div class="col-md-12" >
              <div class="row"> -->


            <div class="col-md-4">
              <div class="form-group">
                <label>Customer</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_addnew_customer" placeholder="" disabled>
              </div>
            <!-- </div>
          </div> -->
        </div>
      </div>
    </div>
          </div>

          <div class='row'>
            <div class="col-md-4">
              <div class="row">
                <!-- <div class="col-md-12" >
                  <div class="row"> -->


                <div class="col-md-4">
                  <div class="form-group">
                    <label>No Invoice</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_addnew_noinvoice" placeholder="" disabled>
                  </div>
                <!-- </div>
              </div> -->
            </div>
          </div>
        </div>
          </div>


              <!-- <div class="row">
                <div class="col-md-4" style="margin-top:-40px;">
                  <h3></h3>
                </div>
              </div> -->
              <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
              <div class="row">
                <div class="col-12" style="overflow:auto; margin-top:0px;">
                <!-- <div class="container-fluid"> -->
                <table id="tabel_add_list_addnew" class="data-table">
                  <thead class="text-center">
                    <tr>
                      <th style="padding: 4px 12px;" scope="col">KodeBrg</th>
                      <th style="padding: 4px 12px;" scope="col">NamaBrg</th>
                      <th style="padding: 4px 12px;" scope="col">Qty</th>
                      <th style="padding: 4px 12px;" scope="col">Sat</th>
                      <th style="padding: 4px 12px;" scope="col">Harga</th>
                    </tr>
                  </thead>
                  <tbody id="tabel_data_add_list_addnew" class="text-left" >
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
          <div class="modal-footer">
            <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
            <!-- <button type="button" class="btn btn-danger btn-lg"
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
            onclick="buttonAddListBatal()">Batal</button> -->

            <button type="button" class="btn btn-primary btn-lg"
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
            onclick="submitAddNew()">Submit</button>
          </div>
        </div>

        <div class="container-fluid" style="margin-top: -10px;">




          <div class="row justify-content-end">




      </div>



        </div>


      </div>


    </div>
    </div>




    <div class="modal fade"  id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" >
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="exampleModalLabel">Add</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>



            <!-- <h1>Tes Modal</h1> -->

            <div id="modalBodyAddListPelanggan" class="showhidemodalbodyadd">
              <div class="modal-body" >

              <div class="container-fluid mt-4" >
                <div class="row">
                  <div class="col-md-4" style="margin-top:-40px;">
                    <h3>Pelanggan</h3>
                  </div>
                </div>
                <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
                <div class="row">
                  <div class="col-12" style="overflow:auto; margin-top:-60px;">
                  <!-- <div class="container-fluid"> -->
                  <table id="tabel_add_list_pelanggan" class="data-table">
                    <thead class="text-center">
                      <tr>
                        <th style="padding: 4px 12px;" scope="col">Kode</th>
                        <th style="padding: 4px 12px;" scope="col">Nama</th>
                        <th style="padding: 4px 12px;" scope="col">Alamat</th>
                        <th style="padding: 4px 12px;" scope="col">Actions</th>
                      </tr>
                    </thead>
                    <tbody id="tabel_data_add_list_pelanggan" class="text-left" >
                      <tr >
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                          <td class="text-center">
                            <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                            <button class="btn btn-primary btn-sm" style="padding-top:10px;" type="button" ><i class="bi bi-plus"></i></button>
                          </td>
                    </tr>
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
              <button type="button" class="btn btn-danger btn-lg"
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
              onclick="buttonAddListBatal()">Batal</button>
            </div>
          </div>

          <div id="modalAddListBarang" class="showhidemodalbodyadd">
            <div class="modal-body" >

            <div class="container-fluid mt-4" >
              <div class="row">
                <div class="col-md-4" style="margin-top:-40px;">
                  <h3>Barang</h3>
                </div>
              </div>
              <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
              <div class="row">
                <div class="col-12" style="overflow:auto; margin-top:-30px;">
                <!-- <div class="container-fluid"> -->
                <table id="tabel_add_list_barang" class="data-table">
                  <thead class="text-center">
                    <tr>
                      <th style="padding: 4px 12px;" scope="col">Kode</th>
                      <th style="padding: 4px 12px;" scope="col">Nama</th>
                      <th style="padding: 4px 12px;" scope="col">QntSisa</th>
                      <th style="padding: 4px 12px;" scope="col">Qnt</th>
                      <th style="padding: 4px 12px;" scope="col">Sat</th>
                    </tr>
                  </thead>
                  <tbody id="tabel_data_add_list_barang" class="text-left" >
                    <tr class="pick-row">
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
          <div class="modal-footer">
            <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
            <button type="button" class="btn btn-danger btn-lg"
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
            onclick="buttonAddListBatal()">Batal</button>
          </div>
        </div>




          <div id="modalBodyAddListPIC" class="showhidemodalbodyadd">
            <div class="modal-body" >

            <div class="container-fluid mt-4" >
              <div class="row">
                <div class="col-md-4" style="margin-top:-40px;">
                  <h3>PIC</h3>
                </div>
              </div>
              <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
              <div class="row">
                <div class="col-12" style="overflow:auto; margin-top:-30px;">
                <!-- <div class="container-fluid"> -->
                <table id="tabel_add_list_pic" class="data-table">
                  <thead class="text-center">
                    <tr>
                      <th style="padding: 4px 12px;" scope="col">Kode</th>
                      <th style="padding: 4px 12px;" scope="col">Nama</th>
                      <th style="padding: 4px 12px;" scope="col">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="tabel_data_add_list_pic" class="text-left" >
                    <tr >
                      <td>-</td>
                      <td>-</td>
                        <td class="text-center">
                          <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                          <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                        </td>
                  </tr>
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
            <button type="button" class="btn btn-danger btn-lg"
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
            onclick="buttonAddListBatal()">Batal</button>
          </div>
        </div>

        <div id="modalBodyAddListLokasiPenerima" class="showhidemodalbodyadd">
          <div class="modal-body" >

          <div class="container-fluid mt-4" >
            <div class="row">
              <div class="col-md-4" style="margin-top:-40px;">
                <h3>Lokasi Penerima</h3>
              </div>
            </div>
            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
            <div class="row">
              <div class="col-12" style="overflow:auto; margin-top:-30px;">
              <!-- <div class="container-fluid"> -->
              <table id="tabel_add_list_lokasipenerima" class="data-table">
                <thead class="text-center">
                  <tr>
                    <th style="padding: 4px 12px;" scope="col">Kode</th>
                    <th style="padding: 4px 12px;" scope="col">Nama</th>
                    <th style="padding: 4px 12px;" scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody id="tabel_data_add_list_lokasipenerima" class="text-left" >
                  <tr >
                    <td>-</td>
                    <td>-</td>
                      <td class="text-center">
                        <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                        <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                      </td>
                </tr>
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
          <button type="button" class="btn btn-danger btn-lg"
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
          onclick="buttonAddListBatal()">Batal</button>
        </div>
      </div>

          <div id="modalBodyAddListAlamatKirim" class="showhidemodalbodyadd">
            <div class="modal-body" >

            <div class="container-fluid mt-4" >
              <div class="row">
                <div class="col-md-4" style="margin-top:-40px;">
                  <h3>Alamat Kirim</h3>
                </div>
              </div>
              <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
              <div class="row">
                <div class="col-12" style="overflow:auto; margin-top:-30px;">
                <!-- <div class="container-fluid"> -->
                <table id="tabel_add_list_alamatkirim" class="data-table">
                  <thead class="text-center">
                    <tr>
                      <th style="padding: 4px 12px;" scope="col">Nomor</th>
                      <th style="padding: 4px 12px;" scope="col">Nama</th>
                      <th style="padding: 4px 12px;" scope="col">Alamat</th>
                      <th style="padding: 4px 12px;" scope="col">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="tabel_data_add_list_alamatkirim" class="text-left" >
                    <tr>
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>
                        <td class="text-center">
                          <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                          <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                        </td>
                  </tr>
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
            <button type="button" class="btn btn-danger btn-lg"
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
            onclick="buttonAddListBatal()">Batal</button>
          </div>
        </div>

        <div id="modalBodyAddListBackOffice" class="showhidemodalbodyadd">
          <div class="modal-body" >

          <div class="container-fluid mt-4" >
            <div class="row">
              <div class="col-md-4" style="margin-top:-40px;">
                <h3>Back Office</h3>
              </div>
            </div>
            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
            <div class="row">
              <div class="col-12" style="overflow:auto; margin-top:-30px;">
              <!-- <div class="container-fluid"> -->
              <table id="tabel_add_list_backoffice" class="data-table">
                <thead class="text-center">
                  <tr>
                    <th style="padding: 4px 12px;" scope="col">Kode</th>
                    <th style="padding: 4px 12px;" scope="col">Nama</th>
                    <th style="padding: 4px 12px;" scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody id="tabel_data_add_list_backoffice" class="text-left" >
                  <tr >
                    <td>-</td>
                    <td>-</td>
                      <td class="text-center">
                        <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                        <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                      </td>
                </tr>
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
          <button type="button" class="btn btn-danger btn-lg"
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
          onclick="buttonAddListBatal()">Batal</button>
        </div>
      </div>

          <div id="modalBodyAddListSales" class="showhidemodalbodyadd">
            <div class="modal-body" >

            <div class="container-fluid mt-4" >
              <div class="row">
                <div class="col-md-4" style="margin-top:-40px;">
                  <h3>Sales</h3>
                </div>
              </div>
              <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
              <div class="row">
                <div class="col-12" style="overflow:auto; margin-top:-60px;">
                <!-- <div class="container-fluid"> -->
                <table id="tabel_add_list_sales" class="data-table">
                  <thead class="text-center">
                    <tr>
                      <th style="padding: 4px 12px;" scope="col">Kode</th>
                      <th style="padding: 4px 12px;" scope="col">Nama</th>
                      <th style="padding: 4px 12px;" scope="col">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="tabel_data_add_list_sales" class="text-left" >
                    <tr >
                      <td>-</td>
                      <td>-</td>
                        <td class="text-center">
                          <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                          <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                        </td>
                  </tr>
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
            <button type="button" class="btn btn-danger btn-lg"
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
            onclick="buttonAddListBatal()">Batal</button>
          </div>
        </div>

        <div id="modalAddListValas" class="showhidemodalbodyadd">
          <div class="modal-body" >

          <div class="container-fluid mt-4" >
            <div class="row">
              <div class="col-md-4" style="margin-top: -40px;">
                <h3>Valas</h3>
              </div>
            </div>
            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
            <div class="row">
              <div class="col-12" style="overflow:auto; margin-top:-30px;">
              <!-- <div class="container-fluid"> -->
              <table id="tabel_add_list_valas" class="data-table">
                <thead class="text-center">
                  <tr>
                    <th style="padding: 4px 12px;" scope="col">Kode</th>
                    <th style="padding: 4px 12px;" scope="col">Nama</th>
                    <th style="padding: 4px 12px;" scope="col">Kurs</th>
                  </tr>
                </thead>

                <tbody id="tabel_data_add_list_valas" class="text-left" >
                  <tr class="pick-row">
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
        <div class="modal-footer">
          <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
          <button type="button" class="btn btn-danger btn-lg"
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
          onclick="buttonAddListBatal()">Batal</button>
        </div>
      </div>



        <div class="container-fluid" style="margin-top: -10px;">




          <div class="row justify-content-end">




      </div>



        </div>


      </div>


    </div>
    </div>




    <!-- </div> -->


</div>









<!-- start modal oto -->
<div class="modal fade" id="formOtorisasi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialo g-centered"  role="document">








    </div>
  </div>

<!-- End modal oto-->







@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script type="text/javascript">


let dataTableAdd = []
let dataTableAddNew = []
let dataTableItemAddNew = []

let dataTableKoreksi = []
let listBarang = []

let tempBarangAddAdd = {}
let tempBarangAddEdit = {}
let tipeform =''
let xppn = 0

/* ============ Header tabel interaktif (window.ReportTable) ============
 * Port 1:1 dari poCart/poAktifkanTabel milik purchaseOrder.blade.php, sama
 * seperti so/invoicejasa/fakturpajak/cetaktandaterima/perintahreturjual/
 * returpenjualangudang/kreditnote.
 */
let nrpCart = { 1 : [], 2 : [] }
let nrpActiveUrut = 0
const NRP_HREF = 'notareturpenjualan'
const NRP_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date', 3 : 'bool' }
const NRP_TIPE_KODE = { varchar : 0, float : 1, date : 2, bool : 3 }
let nrpPerluGambar = { 1 : false, 2 : false }

function nrpPickCI (row, key) {
  if (!row) { return undefined; }
  if (row[key] !== undefined) { return row[key]; }
  let lower = key.toLowerCase();
  for (let k in row) { if (k.toLowerCase() === lower) { return row[k]; } }
  return undefined;
}

function nrpDefaultCart (urut) {
  if (urut === 1) {
    return [
      ['NOBUKTI',      'No. Bukti', 1, 'varchar', 0, 0],
      ['TANGGAL',      'Tanggal',   1, 'date',    0, 0],
      ['NAMACUSTSUPP', 'Customer',  1, 'varchar', 0, 0],
      ['Noinv',        'No Invoice',1, 'varchar', 0, 0],
    ]
  }
  // urut 2: Invoice Otorisasi -- gabungan kolom tab lama "Belum Otorisasi" +
  // "Sudah Otorisasi" (OtoUser1/TglOto1), sejak keduanya digabung jadi satu tabel.
  return [
    ['NoBukti',      'Nobukti',    1, 'varchar', 0, 0],
    ['Tanggal',      'Tanggal',    1, 'date',    0, 0],
    ['NAMACUSTSUPP', 'Customer',   1, 'varchar', 0, 0],
    ['NoInvoice',    'No Invoice', 1, 'varchar', 0, 0],
    ['NORPJ',        'No Retur',   1, 'varchar', 0, 0],
    ['TotDPPRp',     'DPP Rp',     1, 'float',   0, 2],
    ['TotPPNRp',     'PPN Rp',     1, 'float',   0, 2],
    ['TotNetRp',     'Total Rp',   1, 'float',   0, 2],
    ['IDUser',       'User',       1, 'varchar', 0, 0],
    ['OtoUser1',     'User Oto1',  1, 'varchar', 0, 0],
    ['TglOto1',      'Tgl Oto1',   1, 'date',    0, 0],
  ]
}

function nrpBuatCart (headers, values, isnumerics, isshowns, desimals) {
  headers = headers || []
  let cart = []
  headers.forEach((h, i) => {
    let tipe = Number(isnumerics[i]) || 0
    let des = (desimals && desimals[i] !== undefined && desimals[i] !== null && desimals[i] !== '')
      ? Number(desimals[i]) : (tipe === 1 ? 2 : 0)
    cart.push([values[i], h, Number(isshowns[i]) === 1 ? 1 : 0, NRP_TIPE_NAMA[tipe] || 'varchar', 0, isNaN(des) ? 0 : des])
  });
  return cart
}

function nrpAktifkanTabel (urut) {
  nrpActiveUrut = urut
  window.g_modeReport = urut
  window.gcart_header = nrpCart[urut]
}

function nrpOnChangeAktif () {
  if (nrpActiveUrut === 2) { reinitTabel2(); } else { reinitTabel(); }
}

window.g_href = NRP_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function (href, mode) {
  let urut = mode || 1
  let cart = nrpCart[urut] || []
  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[1]); value.push(c[0]); isnumber.push(NRP_TIPE_KODE[c[3]] ?? 0)
    isshown.push(Number(c[2]) === 1 ? 1 : 0); desimal.push(Number(c[5]) || 0)
  });
  $.ajax({
    url: "{!! url('saveheadertable') !!}", type: "post", async: false,
    data: {
      _token: $("#_token").val(), header: JSON.stringify(header), isnumber: JSON.stringify(isnumber),
      tipe: JSON.stringify(desimal), value: JSON.stringify(value), isshown: JSON.stringify(isshown),
      href: NRP_HREF, urut: urut
    },
    error: function (err) { console.log(err); alertify.warning('Gagal menyimpan pengaturan kolom') }
  })
}

window.doSetHeader = function (mode, reset) {
  let urut = mode || 1
  $.ajax({
    url: "{!! url('getheadertable') !!}", type: "post", async: false,
    data: { _token: $("#_token").val(), href: NRP_HREF, urut: urut, reset: reset ? 1 : 0 },
    success: function (res) {
      if (!reset && res && res.headertableheader && res.headertableheader.length) {
        nrpCart[urut] = nrpBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal || [])
      } else {
        nrpCart[urut] = nrpDefaultCart(urut)
        window.gcart_header = nrpCart[urut]
        window.doSimpanHeader(NRP_HREF, urut)
      }
      window.gcart_header = nrpCart[urut]
    },
    error: function (err) {
      console.log(err)
      alertify.warning(reset ? 'Gagal mengembalikan kolom ke tampilan default' : 'Gagal memuat pengaturan kolom')
      nrpCart[urut] = nrpDefaultCart(urut)
      window.gcart_header = nrpCart[urut]
    }
  })
}

function activeVisibleTabKeyNRP () {
  if ($('#nav-profile-tab').hasClass('active')) { return 2; }
  return 1;
}

const NRP_SELEKTOR_TABEL_AKTIF = '#myTabContent .tab-pane.active table.data-table'
const NRP_SELEKTOR_BAR_AKTIF = '#myTabContent .tab-pane.active [id^="rtBarTabel"]'

let nrpRtSudahInit = false
function nrpInitReportTableSekali () {
  if (nrpRtSudahInit || typeof ReportTable === 'undefined') { return }
  nrpRtSudahInit = true
  let urutAktif = activeVisibleTabKeyNRP()
  let idTabel = { 1 : '#tabel', 2 : '#tabel2' }
  let idBar = { 1 : '#rtBarTabel', 2 : '#rtBarTabel2' }
  Object.keys(idTabel).forEach((u) => {
    if (Number(u) === urutAktif) { return }
    ReportTable.init({ table: idTabel[u], bar: idBar[u], onChange: nrpOnChangeAktif })
  });
  ReportTable.init({ table: NRP_SELEKTOR_TABEL_AKTIF, bar: NRP_SELEKTOR_BAR_AKTIF, onChange: nrpOnChangeAktif })

  let nrpGuardUlangKlik = false;
  ['#tabel', '#tabel2'].forEach((sel) => {
    let thead = document.querySelector(sel + ' thead')
    if (!thead) { return }
    thead.addEventListener('click', function (e) {
      if (nrpGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }
      e.stopPropagation()
      e.preventDefault()
      nrpGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles: false, cancelable: true, view: window })
      Object.defineProperty(ulang, 'target', { value: interaktif, configurable: true })
      thead.dispatchEvent(ulang)
      nrpGuardUlangKlik = false
    }, true)
  });
}

function tulisTheadHeaderNRP (tableSel, cols) {
  let thead = document.querySelector(tableSel + ' thead')
  if (!thead || !window.ReportTable) { return; }
  let headRowHtml = ReportTable.headHtml(cols).replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>');
  thead.setAttribute('style', 'white-space:nowrap;');
  thead.innerHTML = headRowHtml;
}

function nrpValueCell (row, col) {
  let raw = nrpPickCI(row, col[0]);
  let type = col[3];
  if (type === 'date') { if (!raw) { return '<td></td>'; } return '<td>' + formatDate(raw, '/') + '</td>'; }
  if (type === 'float') {
    let dp = Number(col[5]) || 0;
    let n = (raw !== undefined && raw !== null && raw !== '') ? Number(raw) : 0;
    return '<td class="text-right">' + n.toLocaleString('id-ID', { minimumFractionDigits: dp, maximumFractionDigits: dp }) + '</td>';
  }
  return '<td>' + (raw !== undefined && raw !== null ? raw : '') + '</td>';
}

function tabelActionsCell (row) {
  let nobukti = nrpPickCI(row, 'NOBUKTI');
  let html = '<td class="text-center"><div class="action-buttons-wrap">';
  html += '<button class="btn btn-primary btn-sm" type="button" onclick="submitAddAllNew(\'' + nobukti + '\')"><i class="bi bi-plus"></i></button>';
  html += '</div></td>';
  return html;
}

// Digabung dari tabel2ActionsCell (Belum Otorisasi: Koreksi/Otorisasi/Delete) +
// tabel3ActionsCell (Sudah Otorisasi: Koreksi/Batal Otorisasi) sejak keduanya
// digabung jadi satu tabel dengan filter Semua/Belum/Sudah Otorisasi.
function nrpTabel2Or3ActionsCell (row) {
  let nobukti = nrpPickCI(row, 'NoBukti');
  let norpj = nrpPickCI(row, 'NORPJ');
  let isOto = Number(nrpPickCI(row, 'IsOtorisasi1'));
  let html = '<td class="text-center"><div class="action-buttons-wrap">';
  if (isOto) {
    // Sudah diotorisasi -- tidak boleh diedit lagi, jadi cuma Detail (buttonEdit
    // versi read-only) + Batal Otorisasi.
    html += '<button class="btn btn-warning btn-sm" title="Detail" type="button" onclick="buttonDetail(\'' + nobukti + '\' , \'' + norpj + '\')"><i class="bi bi-info"></i></button>';
    html += '<button class="btn btn-danger btn-sm" title="Batal Otorisasi" type="button" onclick="buttonBatalOtorisasi(\'' + nobukti + '\' , \'' + norpj + '\')"><i class="bi bi-key"></i></button>';
  } else {
    html += '<button class="btn btn-success btn-sm" title="Edit" type="button" onclick="buttonKoreksi(\'' + nobukti + '\' , \'' + norpj + '\')"><i class="bi bi-pen"></i></button>';
    html += '<button class="btn btn-primary btn-sm" title="Otorisasi" type="button" onclick="buttonOtorisasi(\'' + nobukti + '\' , \'' + norpj + '\')"><i class="bi bi-key"></i></button>';
    html += '<button class="btn btn-danger btn-sm" title="Hapus" type="button" onclick="submitDeleteAll(\'' + nobukti + '\')"><i class="bi bi-trash"></i></button>';
  }
  html += '</div></td>';
  return html;
}

function renderTabelRows (rows) {
  let cols = (nrpCart[1].length ? nrpCart[1] : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  (rows || []).forEach(function (row) {
    html += '<tr>' + tabelActionsCell(row);
    cols.forEach(function (col) { html += nrpValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel_data').innerHTML = html;
  tulisTheadHeaderNRP('#tabel', cols);
}

function renderTabel2Rows (rows) {
  let cols = (nrpCart[2].length ? nrpCart[2] : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  (rows || []).forEach(function (row) {
    html += '<tr>' + nrpTabel2Or3ActionsCell(row);
    cols.forEach(function (col) { html += nrpValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel2_data').innerHTML = html;
  tulisTheadHeaderNRP('#tabel2', cols);
}

let lastTabelRows = []
let lastTabel2Rows = []
let nrpPanjangHalaman = { 1 : 10, 2 : 10 }

function nrpIkatSearch (urut) {
  let ids = { 1 : ['nrpSearch1', 'tabel'], 2 : ['nrpSearch2', 'tabel2'] }
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

function nrpIkatPanjangHalaman (urut) {
  let ids = { 1 : ['nrpLen1', 'tabel'], 2 : ['nrpLen2', 'tabel2'] }
  let sel = document.getElementById(ids[urut][0])
  let idTabel = ids[urut][1]
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(nrpPanjangHalaman[urut])
  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    nrpPanjangHalaman[urut] = (n === -1 || n > 0) ? n : 10
    if ($.fn.DataTable.isDataTable('#' + idTabel)) { $('#' + idTabel).DataTable().page.len(nrpPanjangHalaman[urut]).draw() }
  })
}

const NRP_DOM_STRING = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"

function reinitTabel () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().destroy(); }
    renderTabelRows(lastTabelRows);
    $('#tabel').DataTable({ dom: NRP_DOM_STRING, lengthChange: false, pageLength: nrpPanjangHalaman[1], paging: true, order: [[0, 'asc']], ordering: false });
    nrpIkatSearch(1); nrpIkatPanjangHalaman(1); nrpPerluGambar[1] = false;
  } catch (e) { console.error('reinitTabel failed:', e); alertify.error('Gagal memperbarui tabel: ' + e.message); }
}

function reinitTabel2 () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel2')) { $('#tabel2').DataTable().destroy(); }
    renderTabel2Rows(lastTabel2Rows);
    $('#tabel2').DataTable({ dom: NRP_DOM_STRING, lengthChange: false, pageLength: nrpPanjangHalaman[2], paging: true, order: [[1, 'asc']], ordering: false });
    nrpIkatSearch(2); nrpIkatPanjangHalaman(2); nrpPerluGambar[2] = false;
  } catch (e) { console.error('reinitTabel2 failed:', e); alertify.error('Gagal memperbarui tabel: ' + e.message); }
}

function nrpResetFilterFields () {
  $('#input_filternrp').val('0')
}

function nrpUpdateFilterBadge () {
  let n = Number($('#input_filternrp').val()) || 0
  $('#nrpFilterBadge').text(n === 0 ? '0 aktif' : '1 aktif')
}

function buttonFilterNRP () {
  let tglawal = $('#input_tanggalawal_nrp').val()
  let tglakhir = $('#input_tanggalakhir_nrp').val()
  let filternrp = $('#input_filternrp').val()
  $.ajax({
    url: "{!! url('notareturpenjualanloadall') !!}",
    type: "get", async: false,
    data: { tglawal, tglakhir, filternrp },
    success: function (res) {
      lastTabel2Rows = res.tempOutstanding2
      reinitTabel2()
      nrpUpdateFilterBadge()
    },
    error: function (err) { console.log(err); alertify.warning('Terjadi kesalahan silahkan refresh browser') }
  })
}

function onChangePeriodeNRP () {
  let tglawal = $('#input_tanggalawal_nrp').val()
  let tglakhir = $('#input_tanggalakhir_nrp').val()
  if (tglawal && tglakhir && tglawal > tglakhir) {
    alertify.warning('Tanggal awal tidak boleh lebih besar dari tanggal akhir')
    return
  }
  buttonFilterNRP()
}

$(document).ready(function(){
      nrpAktifkanTabel(1); window.doSetHeader(1, false);
      lastTabelRows = @json($tempOutstanding);
      reinitTabel();

      nrpAktifkanTabel(2); window.doSetHeader(2, false);
      lastTabel2Rows = @json($tempOutstanding2);
      reinitTabel2();

      nrpInitReportTableSekali();

      $('#nav-home-tab').on('shown.bs.tab', function () { nrpAktifkanTabel(1); if (typeof ReportTable !== 'undefined') { ReportTable.refresh(); } if (nrpPerluGambar[1]) { reinitTabel(); } });
      $('#nav-profile-tab').on('shown.bs.tab', function () { nrpAktifkanTabel(2); if (typeof ReportTable !== 'undefined') { ReportTable.refresh(); } if (nrpPerluGambar[2]) { reinitTabel2(); } });

  //   formAddListItem
});


function loadAll () {
  let tglawal = $('#input_tanggalawal_nrp').val()
  let tglakhir = $('#input_tanggalakhir_nrp').val()
  let filternrp = $('#input_filternrp').val()
  $.ajax({
    url: "{!! url('notareturpenjualanloadall') !!}",
    type: "get", async: false, data: { tglawal, tglakhir, filternrp },
    success: function(res) {
      lastTabelRows = res.tempOutstanding;
      lastTabel2Rows = res.tempOutstanding2;
      reinitTabel();
      reinitTabel2();
    }})
}

function buttonOtorisasi (nobukti) {

  console.log(nobukti)



  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }





  alertify.confirm('Otorisasi', 'Otorisasi IVRJ ' + nobukti + ' ?',
      function() {
        let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('notareturpenjualanspotorisasi') !!}",
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

function buttonBatalOtorisasi (nobukti) {

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
          url: "{!! url('notareturpenjualanspbatalotorisasi') !!}",
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


function setNewNoBuktiNew (xval = 1) {
  $.ajax({
    url: "{!! url('notareturpenjualanspnobukti') !!}",
    type: "get",
    async: false,
    data: {
      ppn: xval
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_addnew_nobukti").value = res[0].Nobukti
      document.getElementById("input_addnew_nourut").value = res[0].Nourut

    }})
}


function setNewNoBukti (xval = 1) {
  $.ajax({
    url: "{!! url('notareturpenjualanspnobukti') !!}",
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

function submitAdd () {

}




function buttonCloseForm () {
  $('.mainpage').hide();
  // $('#page2').hide();
  $('#page1').show();

}

function buttonAddBatal () {

  $('.showhideitem').hide();
}

function closeShowHideItem () {
  $('.showhide').hide();

}

function lockForm () {

  if (tipeform = 'add') {

  } else {

  }
}

function buttonAddListValas () {
  $.ajax({
    url: "{!! url('notareturpenjualanlistvalas') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickValas('${item.kodevls}' , '${item.kurs ? parseFloat(item.kurs).toFixed(2) : '0.00'}' )">

        <td>${item.kodevls}</td>
        <td>${item.namavls}</td>
        <td class="text-right">${item.kurs ? parseFloat(item.kurs).toFixed(2) : '0.00'}</td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_valas").innerHTML = rowTable

      $('.showhidemodalbodyadd').hide();
      $('#modalAddListValas').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddListBarang () {
  let _token = $("#_token").val();
  let noretur = $("#input_add_noretur").val();
  if (!noretur) {
    alertify.warning("No retur tidak ditemukkan")
    return
  }


  $.ajax({
    url: "{!! url('notareturpenjualanlistbarang') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      noretur
    },
    success: function(res) {
      listBarang = res

      if (!res.length) {
        alertify.warning('Tidak ada barang untuk ditambah')
      } else {
        let rowTable = ``
        res.forEach((item, i) => {
          rowTable += `
          <tr class="pick-row" onclick="buttonAddPickBarang( ${i})">

          <td>${item.KODEBRG}</td>
          <td>${item.NAMABRG}</td>
          <td class="text-right">${item.QntSisa}</td>
          <td class="text-right">${item.Qnt}</td>
          <td>${item.Satuan}</td>

          </tr>`
        });




        if(!res.length) {
          rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
        }
        document.getElementById("tabel_data_add_list_barang").innerHTML = rowTable

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListBarang').show();
        $("#form").modal('toggle')
      }




    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })





}


function buttonAddPickValas (kode, kurs) {
  console.log('buttonAddPickValas')
  console.log(kode,kurs)
  document.getElementById("input_add_valas").value = kode
  document.getElementById("input_add_kurs").value = kurs
  // if (tipeform == 'edit') {
    onChangeHeader('KODEVLS' , kode)
    onChangeHeader('KURS' , kurs)
  // }

  buttonAddListBatal()
}

function cleanFormAddAdd () {
  document.getElementById("AddAddKodeBrg").value = ''
  document.getElementById("AddAddNamaBrg").value = ''
  document.getElementById("AddAddInputQty").value = '0.00'

  document.getElementById("AddAddInputSat").value = 'PCS'


  document.getElementById("AddAddInputHarga").value = '0.00'


  document.getElementById("AddAddInputDiscPersen").value = '0.00'
  document.getElementById("AddAddInputDiscRp").value = '0.00'
}

function buttonAddPickBarang (index) {
  cleanFormAddAdd()
  tempBarangAddAdd = listBarang[index]
  document.getElementById("AddAddKodeBrg").value = tempBarangAddAdd.KODEBRG
  document.getElementById("AddAddNamaBrg").value = tempBarangAddAdd.NAMABRG
  document.getElementById("AddAddInputQty").value = tempBarangAddAdd.QntSisa
  document.getElementById("AddAddInputQty").value = tempBarangAddAdd.QntSisa

  document.getElementById("AddAddInputSat").value = tempBarangAddAdd.Satuan


  document.getElementById("AddAddInputHarga").value = Number(tempBarangAddAdd.HARGA) ? parseFloat(tempBarangAddAdd.HARGA).toFixed(2) : '0.00'


  document.getElementById("AddAddInputDiscPersen").value = '0.00'
  document.getElementById("AddAddInputDiscRp").value = '0.00'
  $("#form").modal('toggle')
}

function buttonAddListBatal () {

  $("#form").modal('toggle')
}


function submitAddEdit () {
    // $('.formA').hide();
    // $('#modalA').show();
    // return



    let _token = $("#_token").val();
    let choice = "U"

    let nobukti = $("#input_add_nobukti").val()
    let nourut = $("#input_add_nourut").val()
    console.log(dataTableAdd)
    console.log(tempBarangAddAdd)
    let tanggal = $("#input_add_tanggal").val()
    let kodecustsupp = tempBarangAddEdit.KODECUSTSUPP
    let noinvoice = $("#input_add_noinvoice").val()
    let disc = 0
    let kodevalas = $("#input_add_valas").val()
    let kurs = $("#input_add_kurs").val()
    let ppn = $("#input_add_tipeppn").val()
    let tipebayar = $("#input_add_tipebayar").val()
    let hari = $("#input_add_hari").val()
    // iduser
    let urut = tempBarangAddEdit.Urut
    let kodebrg = $("#AddEditKodeBrg").val()
    let norpj = $("#input_add_noretur").val()
    let urutrpj = tempBarangAddEdit.UrutSPR
    let sat_1 = tempBarangAddEdit.SAT1
    let qnt = $("#AddEditInputQty").val()
    let nosat = tempBarangAddEdit.Nosat
    let qnt1 = 0
    let qnt2 = 0
    let isi = tempBarangAddEdit.Isi
    if (nosat == 1) {
      qnt1 = qnt
      qnt2 = qnt
    } else {
      qnt2 = qnt
      qnt1 =  qnt * isi
    }
    let harga = $("#AddEditInputHarga").val()
    let discp = $("#AddEditInputDiscPersen").val()
    let discrp = $("#AddEditInputDiscRp").val()
    let disctot = $("#AddEditInputDiscRp").val()
    let keterangan = ''
    let flagmenu = 0
    let flagtipe = 0
    if ( ppn != 0) {
      flagtipe = 1
    }
    let tipeppn = ppn
    let kodesls = tempBarangAddEdit.KodeSls
    let catatan = $("#input_add_catatan").val()

    if (qnt <= 0 || harga <= 0) {


      alertify.warning("Qnt dan harga <= 0")
      return
    }
    if(!kodebrg) {
      alertify.warning("Isi kodebarang")
      return
    }


    console.log({

      _token ,
      choice ,

      nobukti ,
      nourut ,


      kodecustsupp ,
      noinvoice ,
      disc ,
      kodevalas ,
      kurs ,
      ppn ,
      tipebayar ,
      hari ,
      urut ,
      kodebrg ,
      norpj ,
      urutrpj ,
      sat_1 ,
      qnt ,
      nosat ,
      qnt1 ,
      qnt2 ,
      isi ,

      harga ,
      discp ,
      discrp ,
      disctot ,
      keterangan ,
      flagmenu ,
      flagtipe ,
      tipeppn ,
      kodesls ,
      catatan

    })


    // return

    $.ajax({
      url: "{!! url('notareturpenjualanspadd') !!}",
      type: "post",
      async: false,
      data: {

        _token ,
        choice ,

        nobukti ,
        nourut ,
        tanggal,

        kodecustsupp ,
        noinvoice ,
        disc ,
        kodevalas ,
        kurs ,
        ppn ,
        tipebayar ,
        hari ,
        urut ,
        kodebrg ,
        norpj ,
        urutrpj ,
        sat_1 ,
        qnt ,
        nosat ,
        qnt1 ,
        qnt2 ,
        isi ,
        tipeform,

        harga ,
        discp ,
        discrp ,
        disctot ,
        keterangan ,
        flagmenu ,
        flagtipe ,
        tipeppn ,
        kodesls ,
        catatan

      },
      success: function(res) {
        console.log('resspadd', res)
        tipeform = 'edit'
        loadAll()

        // lockFormAdd()
        // $('.showhide').hide();
        refreshDataTableKoreksi(nobukti , norpj)
        $('.showhideitem').hide();
        alertify.success('Berhasil edit item')

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })











}


function submitAddAdd () {
    // $('.formA').hide();
    // $('#modalA').show();
    // return
    let checkDate = new Date($("#input_add_tanggal").val())


    let periode_bulan = document.getElementById("periode_bulan").value
    let periode_tahun = document.getElementById("periode_tahun").value
    if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

        alertify.warning("Tanggal tidak sesuai periode");
        return
    }


    let _token = $("#_token").val();
    let choice = "I"

    let nobukti = $("#input_add_nobukti").val()
    let nourut = $("#input_add_nourut").val()
    console.log(dataTableAdd)
    console.log(tempBarangAddAdd)
    let tanggal = $("#input_add_tanggal").val()
    let kodecustsupp = tempBarangAddAdd.KODECUSTSUPP
    let noinvoice = $("#input_add_noinvoice").val()
    let disc = 0
    let kodevalas = $("#input_add_valas").val()
    let kurs = $("#input_add_kurs").val()
    let ppn = $("#input_add_tipeppn").val()
    let tipebayar = $("#input_add_tipebayar").val()
    let hari = $("#input_add_hari").val()
    // iduser
    let urut = 0
    let kodebrg = $("#AddAddKodeBrg").val()
    let norpj = $("#input_add_noretur").val()
    let urutrpj = tempBarangAddAdd.URUT
    let sat_1 = tempBarangAddAdd.SAT1
    let qnt = $("#AddAddInputQty").val()
    let nosat = tempBarangAddAdd.NoSat
    let qnt1 = 0
    let qnt2 = 0
    let isi = tempBarangAddAdd.Isi
    if (nosat == 1) {
      qnt1 = qnt
      qnt2 = qnt
    } else {
      qnt2 = qnt
      qnt1 =  qnt * isi
    }
    let harga = $("#AddAddInputHarga").val()
    let discp = $("#AddAddInputDiscPersen").val()
    let discrp = $("#AddAddInputDiscRp").val()
    let disctot = $("#AddAddInputDiscRp").val()
    let keterangan = ''
    let flagmenu = 0
    let flagtipe = 0
    if ( ppn != 0) {
      flagtipe = 1
    }
    let tipeppn = ppn
    let kodesls = dataTableAdd[0].kodeSls
    let catatan = $("#input_add_catatan").val()

    if (qnt <= 0 || harga <= 0) {


      alertify.warning("Qnt dan harga <= 0")
      return
    }
    if(!kodebrg) {
      alertify.warning("Isi kodebarang")
      return
    }

    console.log({

      _token ,
      choice ,

      nobukti ,
      nourut ,


      kodecustsupp ,
      noinvoice ,
      disc ,
      kodevalas ,
      kurs ,
      ppn ,
      tipebayar ,
      hari ,
      urut ,
      kodebrg ,
      norpj ,
      urutrpj ,
      sat_1 ,
      qnt ,
      nosat ,
      qnt1 ,
      qnt2 ,
      isi ,

      harga ,
      discp ,
      discrp ,
      disctot ,
      keterangan ,
      flagmenu ,
      flagtipe ,
      tipeppn ,
      kodesls ,
      catatan,
      tipeform

    })


    // return

    $.ajax({
      url: "{!! url('notareturpenjualanspadd') !!}",
      type: "post",
      async: false,
      data: {

        _token ,
        choice ,

        nobukti ,
        nourut ,
        tanggal,

        kodecustsupp ,
        noinvoice ,
        disc ,
        kodevalas ,
        kurs ,
        ppn ,
        tipebayar ,
        hari ,
        urut ,
        kodebrg ,
        norpj ,
        urutrpj ,
        sat_1 ,
        qnt ,
        nosat ,
        qnt1 ,
        qnt2 ,
        isi ,

        harga ,
        discp ,
        discrp ,
        disctot ,
        keterangan ,
        flagmenu ,
        flagtipe ,
        tipeppn ,
        kodesls ,
        catatan

      },
      success: function(res) {
        if (res == 2) {
          setNewNoBukti(xppn)
          alertify.warning('Nobukti telah direfresh silahkan submit ulang')
        } else {
          console.log('resspadd', res)
          tipeform = 'edit'
          loadAll()
          lockFormAdd()
          $('.showhideitem').hide();
          refreshDataTableKoreksi(nobukti, norpj)
          // $('.showhide').hide();
          // refreshDataTableAdd(nobukti)

          alertify.success('Berhasil menambah item')
        }


      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })


}

function refreshDataTableKoreksi (nobukti , noretur) {
    console.log('refreshDataTableKoreksi')
    let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('notareturpenjualangetdetailpenerimaan') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti,
      noretur

    },
    success: function(res) {

      console.log(res)
      dataTableAdd = res.detailAdd

      dataTableKoreksi = res.detailEdit
      document.getElementById("input_add_customer").value = dataTableKoreksi[0].KODECUSTSUPP + ' - ' + dataTableKoreksi[0].NAMACUSTSUPP
      document.getElementById("input_add_alamatcustomer").value = dataTableKoreksi[0].Alamat
      document.getElementById("input_add_noinvoice").value = dataTableKoreksi[0].NoInvoice
      document.getElementById("input_add_noretur").value = dataTableKoreksi[0].NORPJ
      document.getElementById("input_add_nobukti").value = dataTableKoreksi[0].NoBukti
      document.getElementById("input_add_valas").value = dataTableKoreksi[0].KODEVLS ? dataTableKoreksi[0].KODEVLS : 'IDR'
      // document.getElementById("input_add_kurs").value = dataTableKoreksi[0].KURS ? dataTableKoreksi[0].KURS : '1.00'
      // console.log(dataTableKoreksi[0].KURS)
      if (dataTableKoreksi[0].PPN && Number(dataTableKoreksi[0].PPN) >0 ) {
      document.getElementById("input_add_tipeppn").innerHTML = `
      <option value=1 >Exclude</option>
      <option value=2 >Include</option>
      `
    } else {

      document.getElementById("input_add_tipeppn").innerHTML = `
      <option value=0 selected>None</option>
      `
    }
      document.getElementById("input_add_kurs").value = dataTableKoreksi[0].KURS ? parseFloat(dataTableKoreksi[0].KURS).toFixed(2) : '1.00'
      document.getElementById("input_add_tipeppn").value = dataTableKoreksi[0].PPN
      document.getElementById("input_add_tipebayar").value = dataTableKoreksi[0].TIPEBAYAR

      document.getElementById("input_add_headerdiscp").value = '0.00'
      document.getElementById("input_add_headerdiscrp").value = '0.00'
      document.getElementById("input_add_headerdpp").value =  formatAngka(parseFloat(dataTableKoreksi[0].NDPP).toFixed(2))
      document.getElementById("input_add_headerppn").value = formatAngka(parseFloat(dataTableKoreksi[0].NPPN).toFixed(2))
      document.getElementById("input_add_headergrandtotal").value = formatAngka(parseFloat(dataTableKoreksi[0].NNET).toFixed(2))
      document.getElementById("input_add_catatan").value = dataTableKoreksi[0].Catatan


      document.getElementById("input_add_hari").value = dataTableKoreksi[0].HARI
      document.getElementById("input_add_namasales").value = dataTableKoreksi[0].NamaSls
      let isDetailView = tipeform === 'detail'
      $('#addTable .nrp-col-actions').toggle(!isDetailView)

      let xSubtotalD = 0
      let xSubtotal = 0
      let rowTable = ''
      dataTableKoreksi.forEach((item, i) => {
        xSubtotalD += Number(item.SUBTOTALD)
        xSubtotal += Number(item.SUBTOTALRp)
        let actionsCol = isDetailView ? '' : `
            <td class="text-center">
              <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksiEdit(${i})"><i class="bi bi-pen"></i></button>
            </td>`
        rowTable+= `
          <tr>
            <td>${item.Kodebrg}</td>
            <td>${item.NAMABRG}</td>
            <td>${item.NamaBrgKom ? item.NamaBrgKom : ''}</td>
            <td>${item.satx ? item.satx : '' }</td>
            <td class="text-right">${item.Qnt}</td>
            <td>${item.Satuan}</td>
            <td class="text-right">${formatAngka(parseFloat(item.Harga).toFixed(2))}</td>


            <td class="text-right">${formatAngka(parseFloat(item.DiscRp).toFixed(2))}</td>
            <td class="text-right">${formatAngka(parseFloat(item.SUBTOTALRp).toFixed(2))}</td>
            ${actionsCol}
          </tr>
        `
      });
      // <button class="btn btn-danger btn-sm" type="button" onclick="buttonKoreksiDelete(${i})"><i class="bi bi-trash"></i></button>

      rowTable += `<tr style="background-color: ivory; font-weight: bold" >
        <td colspan=8 class="text-right">Total</td>
        <td colspan=1 class="text-right">${formatAngka(parseFloat(xSubtotal).toFixed(2))}</td>
        ${isDetailView ? '' : '<td colspan=1 class="text-right"></td>'}
      </tr>`

      document.getElementById("addTableData").innerHTML = rowTable

      document.getElementById("input_add_tipebayar").value = dataTableKoreksi[0].TIPEBAYAR




    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}


function onChangePembayaran () {
  console.log("onChangeInputAddPembayaran")
  let check = Number($("#input_add_tipebayar").val())

  // if (dataTableAdd.length) {
    // console.log('asdddddddddddddddddddddddddddddddddd')
    onChangeHeader('TIPEBAYAR' , '' , 'input_add_tipebayar')
    let nobukti = $("#input_add_nobukti").val()
    let kodepelanggan = dataTableKoreksi[0].KODECUSTSUPP
    let _token = $("#_token").val();
    console.log('heck',check)
    if (check) {


        $.ajax({
          url: "{!! url('notareturpenjualancekkredithari') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            kodepelanggan
          },
          success: function(res) {
            // console.log('hari',res)
            if(res.length && res[0].hari) {
              document.getElementById("input_add_hari").value = Number(res[0].hari) > 0  ? res[0].hari : 30

              // if (dataTableAdd.length) {
                console.log('masokk')
                onChangeHeader('HARI' , '' , 'input_add_hari')
                // refreshUpdateHeader()
                // let nobukti = $("#input_add_nobukti").val()
                refreshDataTableKoreksi(nobukti)

              // }
            }
            // onChangeHeader('TIPEBAYAR' , check)


          }})
    } else {
      document.getElementById("input_add_hari").value = 0
      // onChangeHeader('TIPEBAYAR' , 'input_add_tipebayar')
      onChangeHeader('HARI', '' , 'input_add_hari')
      refreshDataTableKoreksi(nobukti)
    }

  // }


}


function buttonKoreksi (nobukti , noretur) {

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

  console.log(nobukti , noretur)
  tipeform = 'edit'
  cleanFormAdd()
  lockFormAdd()
  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  // tipeform = 'edit'
  // loadAll()
  // lockFormAdd()
  // $('.showhideitem').hide();
  // refreshDataTableKoreksi(nobukti, norpj)

  let _token = $("#_token").val();

  refreshDataTableKoreksi(nobukti, noretur)

  console.log(dataTableKoreksi)
  if (dataTableKoreksi.length) {
    if (Number(dataTableKoreksi[0].IsOtorisasi1) == 1) {
      alertify.warning("Nota sudah diotorisasi")
      return
    }
  }

  document.getElementById("input_add_tipeppn").disabled = false
  document.getElementById("input_add_tipebayar").disabled = false
  document.getElementById("input_add_hari").disabled = false
  document.getElementById("input_add_catatan").disabled = false
  document.getElementById("buttonAddListValas").disabled = false

  $('#buttonSubmitAddAdd').show()

  $('.showhideitem').hide();
  $('#page1').hide();
  $('.showhideform').hide();

  $('#modalAdd').show();
  $('#page2').show();
}

// Detail (view-only) utk baris "Invoice Otorisasi" yang sudah diotorisasi --
// fetch data & tampilan form-nya sama persis dengan buttonKoreksi() (dipakai
// ulang lewat refreshDataTableKoreksi), tapi TANPA membuka lagi field header
// yang dikunci lockFormAdd() (jadi semua field tetap disabled/tertutup) dan
// tanpa cek "sudah diotorisasi" (di sinilah justru dokumen yang diotorisasi
// dilihat), serta tombol Submit disembunyikan supaya form ini murni tempat
// melihat isi nota, bukan mengedit.
function buttonDetail (nobukti, noretur) {

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

  tipeform = 'detail'
  cleanFormAdd()
  lockFormAdd()

  refreshDataTableKoreksi(nobukti, noretur)

  $('#buttonSubmitAddAdd').hide()

  $('.showhideitem').hide();
  $('#page1').hide();
  $('.showhideform').hide();

  $('#modalAdd').show();
  $('#page2').show();
}



function cleanFormAdd () {
  $('#addTable .nrp-col-actions').show()
  document.getElementById("addTableData").innerHTML = `<tr >

      <td colspan=10 class="text-center">Belum ada data</td>

</tr><tr style="background-color: ivory; font-weight: bold" >
  <td colspan=8 class="text-right">Total</td>
  <td colspan=1 class="text-right">0.00</td>
  <td colspan=1 class="text-right">0.00</td>
  <td colspan=1 class="text-right"></td>
</tr>`
document.getElementById("input_add_nobukti").value = ''
document.getElementById("input_add_customer").value = ''
document.getElementById("input_add_alamatcustomer").value = ''
document.getElementById("input_add_noinvoice").value = ''
document.getElementById("input_add_noretur").value = ''
document.getElementById("input_add_valas").value = 'IDR'
document.getElementById("input_add_catatan").value = ''
document.getElementById("input_add_kurs").value = '0.00'
document.getElementById("input_add_tipeppn").value = 0
document.getElementById("input_add_tipebayar").value = 0
document.getElementById("input_add_namasales").value = 0
document.getElementById("input_add_hari").value = 0
document.getElementById("input_add_tanggal").value = formatDate(new Date())


}

function lockFormAdd () {
  document.getElementById("buttonAddListValas").disabled = true
  document.getElementById("input_add_hari").disabled = true
  document.getElementById("input_add_tipebayar").disabled = true
  document.getElementById("input_add_tipeppn").disabled = true
  document.getElementById("input_add_tanggal").disabled = true

}

function unlockFormAdd () {
  document.getElementById("buttonAddListValas").disabled = false
  document.getElementById("input_add_hari").disabled = false
  document.getElementById("input_add_tipebayar").disabled = false
  document.getElementById("input_add_tipeppn").disabled = false
  document.getElementById("input_add_tanggal").disabled = false

}

function submitDeleteAll (nobukti) {
  let akses = $("#akses_ishapus").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  let _token = $("#_token").val();


  alertify.confirm('Hapus Nota Retur Penjualan', 'Apakah yakin ingin menghapus Nota ' + nobukti + ' ?',
      function() {


            $.ajax({
              url: "{!! url('notareturpenjualanspdeleteall') !!}",
              type: "post",
              async: false,
              data: {
                _token,
                nobukti

              },
              success: function(res) {

                console.log(res)
                if (res == 1) {
                  loadAll()
                  alertify.success("Berhasil menghapus nota retur penjualan")
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



function submitAddAllNew (nobukti) {

  let akses = $("#akses_istambah").val();
  let checkDate = new Date()

  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value
  console.log(periode_bulan , checkDate)
  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  // setNewNoBukti()
  let _token = $("#_token").val();

    $.ajax({
      url: "{!! url('notareturpenjualanspaddallnew') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti

      },
      success: function(res) {

        console.log(res)
        if (res.status == 1) {
          loadAll()
          buttonKoreksi(res.nobukti)
          alertify.success("Berhasil menambah nota retur penjualan")
        } else if (res.status ==2 ) {
          alertify.warning("Data tidak ditemukkan")
        }
      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })

}

function buttonAdd (nobukti) {

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

  unlockFormAdd()
  cleanFormAdd()
  $('#buttonSubmitAddAdd').show()

  tipeform = 'add'

  console.log('buttonAdd' , nobukti )

  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  // setNewNoBukti()
  let _token = $("#_token").val();


  document.getElementById("addTableData").innerHTML = `<tr >

      <td colspan=10 class="text-center">Belum ada data</td>

</tr><tr style="background-color: ivory; font-weight: bold" >
  <td colspan=8 class="text-right">Total</td>
  <td colspan=1 class="text-right">0.00</td>
  <td colspan=1 class="text-right">0.00</td>
  <td colspan=1 class="text-right"></td>
</tr>`




  $.ajax({
    url: "{!! url('notareturpenjualangetdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {

      console.log(res)
      dataTableAdd = res

      if (res[0].PPNCUST && Number(res[0].PPNCUST) > 0) {
        setNewNoBukti(1)
        xppn = 1
        document.getElementById("input_add_tipeppn").innerHTML = `
        <option value=1 selected >Exclude</option>
        <option value=2 >Include</option>
        `
      } else {
        setNewNoBukti(0)
        xppn = 0
        document.getElementById("input_add_tipeppn").innerHTML = `
        <option value=0 selected>None</option>
        `

      }


      document.getElementById("input_add_customer").value = dataTableAdd[0].KODECUSTSUPP + ' - ' + dataTableAdd[0].NAMACUSTSUPP
      document.getElementById("input_add_alamatcustomer").value = dataTableAdd[0].ALAMATCUSTSUPP
      document.getElementById("input_add_noinvoice").value = dataTableAdd[0].Noinv
      document.getElementById("input_add_noretur").value = dataTableAdd[0].NOBUKTI
      document.getElementById("input_add_valas").value = dataTableAdd[0].Valas ? dataTableAdd[0].Valas : 'IDR'

      document.getElementById("input_add_kurs").value = dataTableAdd[0].Kurs ? dataTableAdd[0].Kurs : '1.00'
      document.getElementById("input_add_tipebayar").value = dataTableAdd[0].TipeBayar ? dataTableAdd[0].TipeBayar : 0

      document.getElementById("input_add_hari").value = Number(dataTableAdd[0].HARI) ? dataTableAdd[0].HARI : 0
      document.getElementById("input_add_namasales").value = dataTableAdd[0].NamaSLS
      document.getElementById("buttonAddListValas").disabled = false
      document.getElementById("input_add_headerdiscp").value = '0.00'
      document.getElementById("input_add_headerdiscrp").value = '0.00'
      document.getElementById("input_add_headerdpp").value = '0.00'
      document.getElementById("input_add_headerppn").value = '0.00'
      document.getElementById("input_add_headergrandtotal").value = '0.00'


      $('.showhideitem').hide();
      $('#page1').hide();
      $('.showhideform').hide();

      $('#modalAdd').show();
      $('#page2').show();





    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function submitAddNew () {
console.log('submitAddNew')
//   dataTableAddNew
//   dataTableItemAddNew
//
// 'notareturpenjualanspaddnew'

let nobukti = $("#input_addnew_nobukti").val()
let nourut = $("#input_addnew_nourut").val()
let tanggal = formatDate(new Date())
let kodecustsupp = dataTableAddNew[0].KODECUSTSUPP
// let hari =

let alamatcust = dataTableAddNew[0].ALAMATCUSTSUPP
let noinvoice = dataTableAddNew[0].Noinv
let norpj = dataTableAddNew[0].NOBUKTI
let kodevalas = dataTableAddNew[0].Valas ? dataTableAddNew[0].Valas : 'IDR'
let kurs = dataTableAddNew[0].Kurs ? dataTableAddNew[0].Kurs : '1.00'

let ppn = 0 //.value = dataTableAdd[0].Valas ? dataTableAdd[0].Valas : 'IDR'
if (dataTableAddNew[0].PPNCUST && Number(dataTableAddNew[0].PPNCUST) > 0) {
  ppn = 1
} else {
  ppn = 0
}
let tipeppn = ppn
let flagmenu = 0
let flagtipe = 0
if ( ppn != 0) {
  flagtipe = 1
}
let tipebayar = Number(dataTableAddNew[0].HARI) > 0 ? 1 : 0
//
let hari =  Number(dataTableAddNew[0].HARI) ? dataTableAddNew[0].HARI : 0
let kodesls = dataTableAddNew[0].kodeSls
let keterangan = ''
let catatan = ''

let _token = $("#_token").val()

console.log({

  _token ,
  // choice ,

  nobukti ,
  nourut ,
  tanggal,

  kodecustsupp ,
  noinvoice ,
  // disc : 0,
  kodevalas ,
  kurs ,
  ppn ,
  tipebayar ,
  hari ,
  // urut ,
  // kodebrg ,
  // norpj ,
  // urutrpj ,
  // sat_1 ,
  // qnt ,
  // nosat ,
  // qnt1 ,
  // qnt2 ,
  // isi ,
  norpj,
  // harga ,
  // discp , //
  // discrp , //
  // disctot , //
  keterangan: '' ,
  flagmenu , //
  flagtipe , //
  tipeppn , //
  kodesls ,
  catatan

})

// return
$.ajax({
  url: "{!! url('notareturpenjualanspaddall') !!}",
  type: "post",
  async: false,
  data: {

    _token ,
    // choice ,
    norpj,
    nobukti ,
    nourut ,
    tanggal,

    kodecustsupp ,
    noinvoice ,
    // disc : 0,
    kodevalas ,
    kurs ,
    ppn ,
    tipebayar ,
    hari ,
    tempData : dataTableItemAddNew,
    // urut ,
    // kodebrg ,
    // norpj ,
    // urutrpj ,
    // sat_1 ,
    // qnt ,
    // nosat ,
    // qnt1 ,
    // qnt2 ,
    // isi ,

    // harga ,
    // discp , //
    // discrp , //
    // disctot , //
    keterangan: '' ,
    flagmenu , //
    flagtipe , //
    tipeppn , //
    kodesls ,
    catatan

  },
  success: function(res) {

    console.log(res)
    if (res == 2) {
      setNewNoBuktiNew(xppn)
      alertify.warning('Nobukti telah direfresh silahkan submit ulang')
    } else {
      // console.log('resspadd', res)
      // tipeform = 'edit'
      loadAll()
      // lockFormAdd()
      // $('.showhideitem').hide();
      // refreshDataTableKoreksi(nobukti, norpj)
      // $('.showhide').hide();
      buttonKoreksi(nobukti)
      $("#formNew").modal('toggle');
      // refreshDataTableAdd(nobukti)
      alertify.success('Berhasil menambah Invoice')
    }


  },
  error: function (err) {
    console.log(err)
    alertify.warning('Terjadi kesalahan silahkan refresh browser')
  }

})


}

function onChangeHeader (field, value = '' , idvalue = '') {
  console.log('onChangeHeader' , field, idvalue)
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  let valuex = ''


  if(value == '') {

      console.log('idbalue')
      valuex = $(`#${idvalue}`).val();
  } else {

    console.log(' petik')
    if (idvalue) {
      document.getElementById(`${idvalue}`).value = value

    }
    valuex = value
  }
  console.log(idvalue)
  console.log(valuex)

  $.ajax({
    url: "{!! url('notareturpenjualanonchangeheader') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      field,
      valuex,
      nobukti
    },
    success: function(res) {
      alertify.success(`update ${field} berhasil`)
      refreshDataTableKoreksi(nobukti)

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}


function buttonAddNew (nobukti) {
  // unlockFormAdd()
  // cleanFormAdd()

  // tipeform = 'add'

  console.log('buttonAddNew' , nobukti )

  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  // setNewNoBukti()
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('notareturpenjualangetdetailnew') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {

      console.log(res)
      dataTableAddNew = res.detail
      dataTableItemAddNew = res.listData

      if (dataTableAddNew[0].PPNCUST && Number(dataTableAddNew[0].PPNCUST) > 0) {
        setNewNoBuktiNew(1)
        xppn = 1
        document.getElementById("input_add_tipeppn").innerHTML = `
        <option value=1 selected >Exclude</option>
        <option value=2 >Include</option>
        `
      } else {
        setNewNoBuktiNew(0)
        xppn = 0
        document.getElementById("input_add_tipeppn").innerHTML = `
        <option value=0 selected>None</option>
        `

      }
      console.log(dataTableItemAddNew)

      document.getElementById("input_addnew_customer").value = dataTableAddNew[0].KODECUSTSUPP + ' - ' + dataTableAddNew[0].NAMACUSTSUPP
      document.getElementById("input_addnew_noinvoice").value = dataTableAddNew[0].Noinv
      document.getElementById("input_addnew_noretur").value = dataTableAddNew[0].NOBUKTI
      let rowTableNew = ''
      dataTableItemAddNew.forEach((item, i) => {
        rowTableNew += `
          <tr>
            <td>${item.KODEBRG}</td>
            <td>${item.NAMABRG}</td>
            <td class="text-right">${formatAngkaX(item.QntSisa)}</td>
            <td>${item.Satuan}</td>
            <td class="text-right">${formatAngkaX(item.HARGA)}</td>
          </tr>
        `
       });


        document.getElementById("tabel_data_add_list_addnew").innerHTML = rowTableNew

      $('#formNew').modal('toggle');

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


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


function buttonAddAddItem ( ) {
  $('.showhideitem').hide();
  document.getElementById("buttonAddListBarang").disabled = false
  cleanFormAddAdd()
  $('#formAddAdd').show();


}

function buttonKoreksiDelete (index) {
  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  let dataDelete = dataTableKoreksi[index]



  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + dataDelete.NAMABRG + ' ?',
      function() {
        let _token = $("#_token").val();
        let choice = "D"

        let nobukti = $("#input_add_nobukti").val()
        let nourut = $("#input_add_nourut").val()
        console.log(dataTableAdd)
        console.log(tempBarangAddAdd)
        let tanggal = $("#input_add_tanggal").val()
        let kodecustsupp = dataDelete.KODECUSTSUPP
        let noinvoice = $("#input_add_noinvoice").val()
        let disc = 0
        let kodevalas = $("#input_add_valas").val()
        let kurs = $("#input_add_kurs").val()
        let ppn = $("#input_add_tipeppn").val()
        let tipebayar = $("#input_add_tipebayar").val()
        let hari = $("#input_add_hari").val()
        // iduser
        let urut = dataDelete.Urut
        let kodebrg = $("#AddEditKodeBrg").val()
        let norpj = $("#input_add_noretur").val()
        let urutrpj = dataDelete.UrutSPR
        let sat_1 = dataDelete.SAT1
        let qnt = $("#AddEditInputQty").val()
        let nosat = dataDelete.Nosat
        let qnt1 = 0
        let qnt2 = 0
        let isi = dataDelete.Isi

        let harga = $("#AddEditInputHarga").val()
        let discp = $("#AddEditInputDiscPersen").val()
        let discrp = $("#AddEditInputDiscRp").val()
        let disctot = $("#AddEditInputDiscRp").val()
        let keterangan = ''
        let flagmenu = 0
        let flagtipe = 0
        if ( ppn != 0) {
          flagtipe = 1
        }
        let tipeppn = ppn
        let kodesls = dataDelete.KodeSls
        let catatan = $("#input_add_catatan").val()




        console.log({

          _token ,
          choice ,

          nobukti ,
          nourut ,


          kodecustsupp ,
          noinvoice ,
          disc ,
          kodevalas ,
          kurs ,
          ppn ,
          tipebayar ,
          hari ,
          urut ,
          kodebrg ,
          norpj ,
          urutrpj ,
          sat_1 ,
          qnt ,
          nosat ,
          qnt1 ,
          qnt2 ,
          isi ,

          harga ,
          discp ,
          discrp ,
          disctot ,
          keterangan ,
          flagmenu ,
          flagtipe ,
          tipeppn ,
          kodesls ,
          catatan

        })


        // return

        $.ajax({
          url: "{!! url('notareturpenjualanspadd') !!}",
          type: "post",
          async: false,
          data: {

            _token ,
            choice ,

            nobukti ,
            nourut ,
            tanggal,

            kodecustsupp ,
            noinvoice ,
            disc ,
            kodevalas ,
            kurs ,
            ppn ,
            tipebayar ,
            hari ,
            urut ,
            kodebrg ,
            norpj ,
            urutrpj ,
            sat_1 ,
            qnt ,
            nosat ,
            qnt1 ,
            qnt2 ,
            isi ,
            tipeform,

            harga ,
            discp ,
            discrp ,
            disctot ,
            keterangan ,
            flagmenu ,
            flagtipe ,
            tipeppn ,
            kodesls ,
            catatan

          },
          success: function(res) {
            console.log('resspadd', res)
            tipeform = 'edit'
            loadAll()

            // lockFormAdd()
            // $('.showhide').hide();
            refreshDataTableKoreksi(nobukti , norpj)
            $('.showhideitem').hide();
            alertify.success('Berhasil delete item')

            if (!dataTableKoreksi.length) {

              alertify.warning("Item habis")
              buttonCloseForm()



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

function buttonKoreksiEdit (index ) {
  console.log('buttonKoreksiEdit')
  tempBarangAddEdit = dataTableKoreksi[index]
  console.log(tempBarangAddEdit)

  document.getElementById("AddEditKodeBrg").value = tempBarangAddEdit.Kodebrg
  document.getElementById("AddEditNamaBrg").value = tempBarangAddEdit.NAMABRG
  document.getElementById("AddEditInputQty").value = parseFloat(tempBarangAddEdit.Qnt).toFixed(2)
  document.getElementById("AddEditInputHarga").value = parseFloat(tempBarangAddEdit.Harga).toFixed(2)
  document.getElementById("AddEditInputDiscPersen").value = parseFloat(tempBarangAddEdit.DiscP).toFixed(2)
  document.getElementById("AddEditInputDiscRp").value = parseFloat(tempBarangAddEdit.DiscRp).toFixed(2)

  $('.showhideitem').hide();
  // document.getElementById("buttonAddListBarang").disabled = false
  // cleanFormAddAdd()
  $('#formAddEdit').show();


}


// function buttonBatalOtorisasi (nobukti) {
//   console.log('buttonBatalOtorisasi' , nobukti)
//   let akses = $("#akses_isotorisasi1").val();
//
//   if (!Number(akses)) {
//     alertify.warning('No access')
//     return
//   }
//   alertify.confirm('Batal Otorisasi', 'Batal Otorisasi Invoice ' + nobukti + ' ?',
//       function() {
//         let _token = $("#_token").val();
//
//
//
//         $.ajax({
//           url: "{!! url('invoicepenjualanspbatalotorisasi') !!}",
//           type: "post",
//           async: false,
//           data: {
//             _token,
//             nobukti
//
//           },
//           success: function(res) {
//             console.log('!', res)
//             loadAll()
//
//             // lockFormAdd()
//
//             alertify.success('Berhasil Batal Otorisasi Invoice')
//
//           },
//           error: function (err) {
//             console.log(err)
//             alertify.warning('Terjadi kesalahan silahkan refresh browser')
//           }
//
//         })
//       }
//     ,function(){
//       console.log('no')
//     });
//
// }

// function submitOtorisasi () {
//   let _token = $("#_token").val();
//   let nobukti = $("#input_otorisasi_nobukti").val();
//
//   $.ajax({
//     url: "{!! url('invoicepenjualanspotorisasi') !!}",
//     type: "post",
//     async: false,
//     data: {
//       _token,
//       nobukti
//
//     },
//     success: function(res) {
//       console.log('!', res)
//       loadAll()
//
//       // lockFormAdd()
//       // $("#formOtorisasi").modal('toggle')
//       buttonCloseFormDetail()
//       alertify.success('Berhasil Otorisasi')
//
//     },
//     error: function (err) {
//       console.log(err)
//       alertify.warning('Terjadi kesalahan silahkan refresh browser')
//     }
//
//   })
//
// }

// function buttonOtorisasi (nobukti) {
//   let akses = $("#akses_isotorisasi1").val();
//
//   if (!Number(akses)) {
//     alertify.warning('No access')
//     return
//   }
//
//   console.log('buttonOtorisasi' , nobukti)
//   let _token = $("#_token").val();
//
//   $.ajax({
//     url: "{!! url('invoicepenjualanspdetailkoreksi') !!}",
//     type: "post",
//     async: false,
//     data: {
//       _token,
//       nobukti
//
//     },
//     success: function(res) {
//
//       console.log(res)
//       if ( !res.length) {
//         alertify.warning("Data tidak ditemukkan")
//
//       }
//       if (res.length) {
//
//         rowTable = ``
//         res.forEach((item, i) => {
//             rowTable += `
//               <tr>
//                 <td>${item.KodeBrg}</td>
//                 <td>${item.NAMABRG}</td>
//                 <td>${item.NamabrgKom}</td>
//                 <td>${item.NoSPB}</td>
//                 <td class="text-right">${item.Qnt ? formatAngka(parseFloat(item.Qnt).toFixed(2)) : '0.00'}</td>
//                 <td>${item.Satuan}</td>
//                 <td class="text-right">${item.HARGA ? formatAngka(parseFloat(item.HARGA).toFixed(2)) : '0.00'}</td>
//                 <td class="text-right">${item.DISCTOT ? formatAngka(parseFloat(item.DISCTOT).toFixed(2)) : '0.00'}</td>
//                 <td class="text-right">${item.SubTotal ? formatAngka(parseFloat(item.SubTotal).toFixed(2)) : '0.00'}</td>
//                 <td>${item.KetDetail}</td>
//
//               </tr>
//
//             `
//         });
//         document.getElementById("otorisasiTableData").innerHTML = rowTable
//         document.getElementById("input_otorisasi_customer").value = res[0].NamaCustSupp
//
//         document.getElementById("input_otorisasi_alamat").value = res[0].Alamat
//         document.getElementById("input_otorisasi_alamatx").value = res[0].AlamatX
//         document.getElementById("input_otorisasi_catatan").value = res[0].FootNote
//         document.getElementById("input_otorisasi_nobukti").value = res[0].NoBukti
//         document.getElementById("input_otorisasi_tanggal").value = formatDate(res[0].Tanggal)
//         document.getElementById("input_otorisasi_valas").value = res[0].Valas
//         document.getElementById("input_otorisasi_kurs").value = res[0].Kurs
//         document.getElementById("input_otorisasi_tipeppn").value = res[0].PPN
//         document.getElementById("input_otorisasi_nopo").value = res[0].NoPesanan
//         document.getElementById("input_otorisasi_pembayaran").value = res[0].TIPEBAYAR
//         document.getElementById("input_otorisasi_hari").value = res[0].HARI
//         document.getElementById("input_otorisasi_sales").value = res[0].NamaSls
//         document.getElementById("input_otorisasi_uangmuka").value = parseFloat(res[0].nUangMuka).toFixed(2)
//
//
//         // $("#formOtorisasi").modal('toggle')
//
//         $('#page1').hide();
//         $('#page3').show();
//       }
//
//
//
//
//     },
//     error: function (err) {
//       console.log(err)
//       alertify.warning('Terjadi kesalahan silahkan refresh browser')
//     }
//
//   })
//
//
//
// }

// function refreshDataTableKoreksi (nobukti , tipeRefresh = '') {
//   console.log('refreshDataTableKoreksi')
//   let _token = $("#_token").val();
//
//   let resRefresh = 0
//   $.ajax({
//     url: "{!! url('invoicepenjualanspdetailkoreksi') !!}",
//     type: "post",
//     async: false,
//     data: {
//       _token,
//       nobukti
//
//     },
//     success: function(res) {
//
//       console.log(res)
//       if (tipeRefresh && !res.length) {
//         alertify.warning("Data habis")
//         // $("#form").modal('toggle')
//         buttonCloseForm()
//       }
//       if (res.length) {
//
//         dataTableKoreksi = res
//         rowTable = ``
//         dataTableKoreksi.forEach((item, i) => {
//             rowTable += `
//               <tr>
//                 <td>${item.KodeBrg}</td>
//                 <td>${item.NAMABRG}</td>
//                 <td>${item.NamabrgKom}</td>
//                 <td>${item.NoSPB}</td>
//                 <td class="text-right">${item.Qnt ? formatAngka(parseFloat(item.Qnt).toFixed(2)) : '0.00'}</td>
//                 <td>${item.Satuan}</td>
//                 <td class="text-right">${item.HARGA  ? formatAngka(parseFloat(item.HARGA).toFixed(2))  : '0.00'}</td>
//                 <td class="text-right">${item.DISCTOT ? formatAngka(parseFloat(item.DISCTOT).toFixed(2))  : '0.00'}</td>
//                 <td class="text-right">${item.SubTotal ? formatAngka(parseFloat(item.SubTotal).toFixed(2))  : '0.00'}</td>
//                 <td>${item.KetDetail}</td>
//                 <td class="text-center">
//                   <button class="btn btn-danger btn-sm" type="button" onclick="buttonKoreksiDelete(${i} ,'${item.NoBukti}' , '${item.NAMABRG}' , '${item.Urut}'  )"><i class="bi bi-trash"></i></button>
//                 </td>
//               </tr>
//
//             `
//         });
//         document.getElementById("koreksiTableData").innerHTML = rowTable
//         document.getElementById("input_koreksi_customer").value = dataTableKoreksi[0].NamaCustSupp
//
//         document.getElementById("input_koreksi_alamat").value = dataTableKoreksi[0].Alamat
//         document.getElementById("input_koreksi_alamatx").value = dataTableKoreksi[0].AlamatX
//         document.getElementById("input_koreksi_catatan").value = dataTableKoreksi[0].FootNote
//         document.getElementById("input_koreksi_nobukti").value = dataTableKoreksi[0].NoBukti
//         document.getElementById("input_koreksi_tanggal").value = formatDate(dataTableKoreksi[0].Tanggal)
//         document.getElementById("input_koreksi_valas").value = dataTableKoreksi[0].Valas
//         document.getElementById("input_koreksi_kurs").value = dataTableKoreksi[0].Kurs
//         document.getElementById("input_koreksi_tipeppn").value = dataTableKoreksi[0].PPN
//         document.getElementById("input_koreksi_nopo").value = dataTableKoreksi[0].NoPesanan
//         document.getElementById("input_koreksi_pembayaran").value = dataTableKoreksi[0].TIPEBAYAR
//         document.getElementById("input_koreksi_hari").value = dataTableKoreksi[0].HARI
//         document.getElementById("input_koreksi_sales").value = dataTableKoreksi[0].NamaSls
//         document.getElementById("input_koreksi_uangmuka").value = parseFloat(dataTableKoreksi[0].nUangMuka).toFixed(2)
//
//         resRefresh =  1;
//
//
//       }
//
//
//
//     },
//     error: function (err) {
//       console.log(err)
//       alertify.warning('Terjadi kesalahan silahkan refresh browser')
//       resRefresh = 0;
//     }
//
//   })
//   return resRefresh
//
// }

// function buttonKoreksiDelete (index, nobukti, namabrg, urut) {
//   let akses = $("#akses_ishapus").val();
//
//   if (!Number(akses)) {
//     alertify.warning('No access')
//     return
//   }
//
//   console.log('buttonKoreksiDelete' , index, nobukti, namabrg, urut )
//
//
//   alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + namabrg + ' ?',
//       function() {
//         let _token = $("#_token").val();
//         let choice = "D"
//
//
//
//
//
//         $.ajax({
//           url: "{!! url('invoicepenjualanspdelete') !!}",
//           type: "post",
//           async: false,
//           data: {
//             _token : _token,
//             nobukti,
//             urut
//
//           },
//           success: function(res) {
//             console.log('res', res)
//             loadAll()
//             refreshDataTableKoreksi(nobukti , 'D')
//
//             // lockFormAdd()
//             // $('.showhide').hide();
//             // refreshDataTableAdd(nobukti)
//
//             alertify.success('Berhasil menghapus item')
//
//           },
//           error: function (err) {
//             console.log(err)
//             alertify.warning('Terjadi kesalahan silahkan refresh browser')
//           }
//
//         })
//       }
//     ,function(){
//       console.log('no')
//     });
//
// }











function onChangeInputAddDisc () {
  console.log("onChangeInputAddAddDisc")
  let harga = $("#AddAddInputHarga").val();

  if (!Number(harga)) {

    document.getElementById("AddAddInputDiscRp").value = '0.00'
    return
  }

  let disc = $("#AddAddInputDiscPersen").val();
  if (disc <= 0) {
    document.getElementById("AddAddInputDiscRp").value = '0.00'
    document.getElementById("AddAddInputDiscPersen").value = '0.00'
    alertify.warning("Angka kurang dari 0")
    return
  }

  let discRp = Number(harga) * Number(disc) / 100
  document.getElementById("AddAddInputDiscRp").value = parseFloat(discRp).toFixed(2)

}



function onChangeInputEditDisc () {
  console.log("onChangeInputAddEditDisc")
  let harga = $("#AddEditInputHarga").val();

  if (!Number(harga)) {

    document.getElementById("AddEditInputDiscRp").value = '0.00'
    return
  }

  let disc = $("#AddEditInputDiscPersen").val();
  if (disc <= 0) {
    document.getElementById("AddEditInputDiscRp").value = '0.00'
    document.getElementById("AddEditInputDiscPersen").value = '0.00'
    alertify.warning("Angka kurang dari 0")
    return
  }

  let discRp = Number(harga) * Number(disc) / 100
  document.getElementById("AddEditInputDiscRp").value = parseFloat(discRp).toFixed(2)

}

function onChangeInputEditHarga () {
  document.getElementById("AddEditInputDiscRp").value = '0.00'
  document.getElementById("AddEditInputDiscPersen").value = '0.00'
}



function onChangeInputAddHarga () {
  document.getElementById("AddAddInputDiscRp").value = '0.00'
  document.getElementById("AddAddInputDiscPersen").value = '0.00'
}

function onChangeInputAddDiscRp () {
  console.log("onChangeInputAddAddDiscRp")
  let harga = $("#AddAddInputHarga").val();

  if (!Number(harga)) {

    document.getElementById("AddAddInputDiscPersen").value = '0.00'
    return
  }

  let discRp = $("#AddAddInputDiscRp").val();

  if (discRp <= 0) {
    document.getElementById("AddAddInputDiscRp").value = '0.00'
    document.getElementById("AddAddInputDiscPersen").value = '0.00'
    alertify.warning("Angka kurang dari 0")
    return
  }

  let disc = Number(discRp) / Number(harga) * 100
  document.getElementById("AddAddInputDiscPersen").value = parseFloat(disc).toFixed(2)
}


function onChangeInputEditDiscRp () {
  console.log("onChangeInputAddEditDiscRp")
  let harga = $("#AddEditInputHarga").val();

  if (!Number(harga)) {

    document.getElementById("AddEditInputDiscPersen").value = '0.00'
    return
  }

  let discRp = $("#AddEditInputDiscRp").val();

  if (discRp <= 0) {
    document.getElementById("AddEditInputDiscRp").value = '0.00'
    document.getElementById("AddEditInputDiscPersen").value = '0.00'
    alertify.warning("Angka kurang dari 0")
    return
  }

  let disc = Number(discRp) / Number(harga) * 100
  document.getElementById("AddEditInputDiscPersen").value = parseFloat(disc).toFixed(2)
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
