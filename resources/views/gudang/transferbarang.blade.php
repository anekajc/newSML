@extends('newmasterTest')
@section('buttons')

@section('title', 'SML - Transfer Barang')
@section('page-title', 'Transfer Barang')

@endsection
@section('css')

  <link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">

  {{-- Blok CSS lokal halaman ini -- disalin verbatim dari @section('css') purchaseOrder.blade.php
       (custom-tabs/tab-card override layout .card, po-len-wrap/po-len-inp, tombol aksi
       bulat kecil pastel di kolom pertama), sesuai docs/marketing-full-menu-guide.md
       langkah 6. Tidak ada CSS baru -- report-table.js/po-table-header.css sudah
       menyediakan sisanya. --}}
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

  /* layout newmasterTest punya rule .card global (align-items:center, text-align:center)
     yang bikin tab-bar ini ikut rata tengah kalau tidak ditimpa. so.blade.php/purchaseOrder
     .blade.php's .tab-card override tidak eksplisit menimpa text-align, tapi text-align
     ikut menurun (inherited) ke #nav-tab/.custom-tabs (display:inline-flex tetap ikut
     rata tengah teks leluhurnya) -- itu sebabnya baris "Permintaan Transfer Barang" /
     "Transaksi Transfer Barang" / "OutStanding Transfer" sempat muncul di tengah,
     bukan rata kiri. Ditimpa eksplisit di sini karena halaman ini baru terkena masalah
     yang sama. */
  .tab-card {
    display: block !important;
    align-items: flex-start !important;
    text-align: left !important;
    padding: 0 !important;
    border: none !important;
    margin-bottom: 6px !important;
  }

  .tab-card .card-body {
    padding: 5px 10px !important;
  }

  .po-len-wrap {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .po-len-wrap label {
    font-size: 12px;
    font-weight: 700;
    color: #64748b;
    margin: 0;
    white-space: nowrap;
  }

  .po-len-inp {
    height: 34px;
    border-radius: 8px;
    border: 1px solid #dbe0e6;
    font-size: 12.5px;
    padding: 0 8px;
  }

  /* ---------- Kolom Aksi tabel (#tabel/#tabel2/#tabel3) - tombol bulat kecil, warna
     pastel, sama seperti punya purchaseOrder.blade.php. JANGAN pakai display:flex pada
     <td>: sel yang di-flex berhenti jadi sel tabel, tingginya cuma setinggi tombol dan
     tidak ikut vertical-align:middle baris. Cukup text-align + vertical-align;
     font-size:0 membuang spasi antar tombol. */
  #tabel tbody td:first-child:not(.dataTables_empty),
  #tabel2 tbody td:first-child:not(.dataTables_empty),
  #tabel3 tbody td:first-child:not(.dataTables_empty) {
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
    font-size: 0;
  }

  #tabel td:first-child .btn + .btn,
  #tabel2 td:first-child .btn + .btn,
  #tabel3 td:first-child .btn + .btn {
    margin-left: 4px;
  }

  #tabel td:first-child .btn,
  #tabel2 td:first-child .btn,
  #tabel3 td:first-child .btn {
    width: 30px;
    height: 30px;
    padding: 0;
    display: inline-flex;
    vertical-align: middle;
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
  #tabel3 td:first-child .btn:hover {
    filter: brightness(0.97);
    transform: translateY(-1px);
  }

  #tabel td:first-child .btn-success,
  #tabel2 td:first-child .btn-success,
  #tabel3 td:first-child .btn-success {
    color: #16a34a; border-color: #cdebd7; background: #e7f7ed;
  }

  #tabel td:first-child .btn-warning,
  #tabel2 td:first-child .btn-warning,
  #tabel3 td:first-child .btn-warning {
    color: #b45309; border-color: #fbe3bd; background: #fef3e0;
  }

  #tabel td:first-child .btn-primary,
  #tabel2 td:first-child .btn-primary,
  #tabel3 td:first-child .btn-primary {
    color: #2563eb; border-color: #cfdcff; background: #e8edff;
  }

  #tabel td:first-child .btn-danger,
  #tabel2 td:first-child .btn-danger,
  #tabel3 td:first-child .btn-danger {
    color: #dc2626; border-color: #f7cfcf; background: #fdeaea;
  }

  #tabel td:first-child .btn-info,
  #tabel2 td:first-child .btn-info,
  #tabel3 td:first-child .btn-info {
    color: #0891b2; border-color: #a5f3fc; background: #ecfeff;
  }
  </style>


{{-- tampilan search bar modal add pelanggan --}}
  <style>
    #tabel_add_list_pelanggan_filter{
      display: flex;
      align-items: flex-end;
      margin-bottom: -10px;

    }
    #tabel_add_list_pelanggan_filter label input {
      width: 150px;
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-shadow: none; 
      font-size: 0.65rem; 
    }
  </style>
{{-- end tampilan search bar modal add pelanggan --}}

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

{{-- tampilan search modal barang all --}}
  <style>
    #input_search_barang_all {
      width: 150px;
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-shadow: none; 
      font-size: 0.65rem;
      display: flex;
      align-items: flex-end;
      margin-left: 95px;
    }
    .search-label {
    font-weight: bold;
    font-size: 0.75rem;
    margin-right: 155px;
    margin-top : -45px;
    display: inline-block;
    vertical-align: middle;
    }
  </style>
{{-- end tampilan search modal barang all --}}
@endsection
@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id="page1" class="container-fluid">
  <div class="">
    <!-- <div id="qrcode"></div> -->
    <div class="row">
      <div class="col-6 text-left">
        <h2 style="margin-top:-85px;">Transfer Barang</h2>
      </div>
      {{-- <div class="col-6 text-right">
        <button type="button" class="btn btn-primary btn-lg" style="
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
          Add Transfer Barang
        </button>
      </div> --}}
      {{-- <div class="col-6 text-right">
        <button type="button" class="btn btn-primary btn-lg" style="
            height: 30px; 
            margin-top: -150px; 
            padding: 4px 12px; 
            border-radius: 20px; 
            font-size: 0.75rem; 
            font-weight: 600; 
            text-transform: uppercase; 
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
            onclick="loadAll()">
          tes load all
        </button>
      </div> --}}
    </div>
  </div>

  <div id="contentContainer" class="">
    <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
    <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />
    <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
    <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS!!}" />
    <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
    <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />
    <input type="hidden" id="akses_isotorisasi1" value="{!! $akses->IsOtorisasi1 !!}" />
    <input type="hidden" id="akses_isbatal" value="{!! $akses->IsBatal !!}" />
    <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

    {{-- Tab bar: purchaseOrder.blade.php's exact card.tab-card + custom-tabs anchor
         pattern (BS4 data-toggle) -- same wrapper markup, same ordering convention (tab
         utama/transaksi di depan dan aktif, mirip "Purchase Order" di depan "Outstanding
         PR"/"Outstanding SO" milik PO). Non-Otorisasi/Otorisasi digabung jadi satu tab
         "Transaksi Transfer Barang" dengan filter status Otorisasi (lihat toolbar tab
         #profile di bawah) -- 3 tab, bukan 4 lagi. Pane id="1"/#tabelRetur (di bawah,
         tanpa tombol nav sendiri) sengaja tidak disentuh -- sudah dead markup sebelum
         task ini, di luar scope. --}}
    <div class="card mb-3 tab-card">
      <div class="card-body">
        <div class="nav nav-tabs border-0 custom-tabs" id="nav-tab" role="tablist">
          <a class="nav-item nav-link active" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true">
            Transaksi Transfer Barang
          </a>
          <a class="nav-item nav-link" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="false">
            Permintaan Transfer Barang
          </a>
          <a class="nav-item nav-link" id="nav-profile1-tab" data-toggle="tab" href="#profile1" role="tab" aria-controls="profile1" aria-selected="false">
            OutStanding Transfer (Transaksi Belum Terima)
          </a>
        </div>
      </div>
    </div>

      <div class="card">
      <div class="card-body" style="padding:0;">
        <div class="tab-content" id="myTabContent">

          {{-- Transaksi Transfer Barang: gabungan Non-Otorisasi + Otorisasi, dipilah lewat
               dropdown #tbFilterOtorisasi (Semua/Sudah/Belum, berdasar NeedOtorisasi dari
               fetchHeaderList()) -- bukan dua tab terpisah lagi. Tab utama & aktif
               default, sama seperti "Purchase Order" di purchaseOrder.blade.php. Periode
               di sini juga mendorong data tab Outstanding lewat loadAll() yang sama. --}}
          <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="nav-profile-tab">
            <div class="row">
              <div class="col-md-12">
                <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                  <div class="po-toolbar">
                    <div class="po-filter-wrap">
                      <label>Periode</label>
                      <input type="date" onchange="onChangePeriodeTransfer()" class="po-filter-inp" id="tbTanggalAwal" value="{!! $date1 !!}">
                      <span class="po-filter-sep">s/d</span>
                      <input type="date" onchange="onChangePeriodeTransfer()" class="po-filter-inp" id="tbTanggalAkhir" value="{!! $date2 !!}">
                    </div>
                    <div class="po-filter-wrap">
                      <label>Otorisasi</label>
                      <select id="tbFilterOtorisasi" class="po-filter-inp" onchange="tbTerapkanFilterOtorisasi()">
                        <option value="">Semua</option>
                        <option value="0">Sudah Otorisasi</option>
                        <option value="1">Belum Otorisasi</option>
                      </select>
                    </div>
                    <input type="search" id="tbSearch2" class="po-search-inp" placeholder="Cari data">
                    <div class="po-len-wrap">
                      <label for="tbLen2">Tampilkan</label>
                      <select id="tbLen2" class="po-len-inp">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="-1">Semua</option>
                      </select>
                    </div>
                  </div>
                  <div id="rtBarTabel2"></div>
                  <table id="tabel2" class="data-table po-aksi-hover">
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

          <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="nav-home-tab">
            <div class="row">
              <div class="col-md-12">
                <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                  <div class="po-toolbar">
                    <input type="search" id="tbSearch1" class="po-search-inp" placeholder="Cari data">
                    <div class="po-len-wrap">
                      <label for="tbLen1">Tampilkan</label>
                      <select id="tbLen1" class="po-len-inp">
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

          {{-- Outstanding: header-level (bukan per-item lagi), difilter IsTerima=0 dari
               data yang sama dengan tab Transaksi -- lihat tbTerapkanDataOutstanding(). --}}
          <div class="tab-pane fade" id="profile1" role="tabpanel" aria-labelledby="nav-profile1-tab">
            <div class="row">
              <div class="col-md-12">
                <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                  <div class="po-toolbar">
                    <input type="search" id="tbSearch3" class="po-search-inp" placeholder="Cari data">
                    <div class="po-len-wrap">
                      <label for="tbLen3">Tampilkan</label>
                      <select id="tbLen3" class="po-len-inp">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="-1">Semua</option>
                      </select>
                    </div>
                  </div>
                  <div id="rtBarTabel3"></div>
                  <table id="tabel3" class="data-table">
                    <thead style="white-space:nowrap;"></thead>
                    <tbody id="tabel3_data" class="text-left"></tbody>
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

          <div class="tab-pane fade" id="1" role="tabpanel" aria-labelledby="profile-tab">
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

<div id="page2" class="container-fluid" style="display: none" >
  <div class="row">
    <div class="col-6 text-left">
      <h2 style="margin-top: -80px;">Form Transfer Barang</h2>
    </div>
    <div class="col-6 text-right">
      <button type="button" class="btn btn-danger btn-lg" style="
          height: 30px; 
          margin-top: -120px; 
          padding: 4px 12px; 
          border-radius: 20px; 
          font-size: 0.75rem; 
          font-weight: 600; 
          text-transform: uppercase; 
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
          onclick="buttonCloseFormAdd()">
        Close
      </button>
    </div>
  </div>

  <div id="modalBodyAddMain" class="">
    <div class="modal-body" style="margin-top:-60px;">
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
                <input type="text" class="form-control text-center" id="input_add_nobukti" placeholder="" disabled>
              </div>
            </div>

            <div class="col-md-4" style="margin-top:-12px;">
              <div class="form-group">
                <label>No Urut</label>
              </div>
            </div>
            <div class="col-md-8" style="margin-top:-12px;">
              <div class="form-group">
                <input type="text" class="form-control text-center" id="input_add_nourut" placeholder="" readonly>
              </div>
            </div>
            <div class="col-md-8" style="margin-top:-12px;" hidden>
              <div class="form-group">
                <input type="text" class="form-control text-center" id="input_add_urut" placeholder="" readonly>
              </div>
            </div>

            <div class="col-md-4" style="margin-top:-12px;">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-md-8" style="margin-top:-12px;">
              <div class="form-group">
                <input type="date" class="form-control text-center" id="input_add_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>
            
          </div>
        </div>

        <div class="col-md-3">
          <div class="row">

            <div class="col-md-6">
              <div class="form-group">
                <label>Gudang Asal</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group mb-3">
                <input type="text" class="form-control text-center" value='-' id="input_add_kodeGudangAsal" disabled>
                <button class="btn btn-primary btn-sm rounded-end shadow-sm" id="buttonAddListGudangAsal" onclick="buttonAddListGudangAsal()">
                  <i class="bi bi-plus"></i>
                </button>
              </div>
            </div>

            <div class="col-md-12" style="margin-top:-15px;">
              <div class="form-group">
                <textarea style="width: 100%; resize: none;" rows=3 placeholder="Gudang Asal" class="form-control text-center align-items-center" id="input_add_namaGudangAsal"  disabled></textarea>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="row">

            <div class="col-md-6">
              <div class="form-group">
                <label>Gudang Tujuan</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group mb-3">
                <input type="text" class="form-control text-center" value='-' id="input_add_kodeGudangTujuan" disabled>
                <button class="btn btn-primary btn-sm rounded-end shadow-sm" id="buttonAddListGudangTujuan" onclick="buttonAddListGudangTujuan()">
                  <i class="bi bi-plus"></i>
                </button>
              </div>
            </div>

            <div class="col-md-12" style="margin-top:-15px;">
              <div class="form-group">
                <textarea style="width: 100%; resize: none;" rows=3 placeholder="Gudang Tujuan" class="form-control text-center align-items-center" id="input_add_namaGudangTujuan"  disabled></textarea>
              </div>
            </div>
          </div>
        </div>

      <div class="col-md-3">
        <div class="row">
          <div class="col-md-12" style="margin-top:-5px;">
            <div class="form-group">
              <textarea style="width: 100%; resize: none;" rows=5 onblur="onChangeKeterangan()" placeholder="Keterangan" class="form-control" id="input_add_keterangan"></textarea>
            </div>
          </div>
        </div>
      </div>

    </div>

          <hr/>
          <div class="row" hidden>
            <div class="col-md-12 mt-2 text-left">
              <button type="button" class="btn btn-primary btn-lg" style="
                height: 30px; 
                margin-top: -35px;
                padding: 4px 12px; 
                border-radius: 20px; 
                font-size: 0.75rem; 
                font-weight: 600; 
                text-transform: uppercase; 
                transition: background-color 0.3s, box-shadow 0.3s;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
                onclick="buttonShowHideHeader()" class="btn btn-secondary"><b>Show/Hide Header</b></button>
            </div>
          </div>
            <div class="showhidemodalbodyaddmain mt-4" id="modalBodyAddMainHeader" style="display: none">
              <div class="row">

                <div class="col-md-3">
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Dikirim Ke</label>
                      </div>
                    </div>
                    <div class="form-group row">
                      <input class="form-control col-8" id="input_add_kodealamatkirim" readonly >
                      <button onclick="buttonAddListGudang()" id="buttonAddListGudang"  style="height:32px;" class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
                    </div>
                    <div class="col-md-12">
                      <div class="input-group form-group">
                        <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_add_alamatkirim"  disabled></textarea>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Ekspedisi</label>
                      </div>
                    </div>
                    <div class="form-group row">
                      <input class="form-control col-8" id="input_add_kodeekspedisi" readonly >
                      <button onclick="buttonAddListLokasiPenerima()" id="buttonAddListLokasiPenerima" style="height:32px;" class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group">
                        <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_add_ekspedisi"  disabled></textarea>
                      </div>
                    </div>
                  </div>
                </div>
  
                <div class="col-md-3">
                  <div class="row">
                    <div class="col-md-12">
                      <label>Keterangan</label>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group" style="margin-top: 14px">
                        <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_add_keterangan" onblur="onChangeCatatan()"></textarea>
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
                            <label>No SO</label>
                          </div>
                        </div>
                        <div class="col-8" style="margin-top:-5px">
                          <div class="form-group">
                            <input type="text" class="form-control" id="input_add_noso" readonly>
                            <button onclick="buttonAddListNoSO()" id="buttonAddListNoSo" style="height:32px;" class="btn btn-primary btn-sm text-right">
                              <i class="bi bi-plus"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-4" style="margin-top:-10px">
                          <div class="form-group">
                            <label>No. PO Cust</label>
                          </div>
                        </div>
                        <div class="col-8" style="margin-top:-15px">
                          <div class="form-group">
                            <input type="text" class="form-control" id="input_add_nopocust" onBlur="onChangeDP()" readonly>
                          </div>
                        </div>
                      </div>
                    </div>
  
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-4" style="margin-top:-10px">
                          <div class="form-group">
                            <label>Tgl Kirim</label>
                          </div>
                        </div>
                        <div class="col-8" style="margin-top:-15px">
                          <div class="form-group">
                            <input type="date" class="form-control text-center" id="input_add_tanggalkirim" value="{!! date('Y-m-d') !!}" onblur="onChangeTgglKirim()">
                          </div>
                        </div>
                      </div>
                    </div>
  
                  </div>
                </div>

            <div class="col-md-3" hidden>
              <div class="row">

                <div class="col-md-6">
                  <div class="row">
                    <div class="col-9">
                      <div class="form-group">
                        <label>Back Office</label>
                      </div>
                    </div>
                    <div class="col-3 text-right">
                      <div class="form-group">
                    <!-- <button onclick="buttonAddListBackOffice()" id="buttonAddListBackOffice"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
                      </div>
                    </div>
                  </div>
                </div>
  
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-12">
                          <div class="input-group form-group">
                            <input type="hidden" class="form-control" id="input_add_kodebackoffice" >
                            <input type="text" class="form-control" id="input_add_namabackoffice"  disabled>
                            <button onclick="buttonAddListBackOffice()" id="buttonAddListBackOffice"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
  
              </div>
            </div>
  
            {{-- budi sementara 2 --}}
            <div class="col-md-3" hidden>
              <div class="row">
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-9">
                      <div class="form-group">
                        <label>PIC</label>
                      </div>
                    </div>
                    <div class="col-3 text-right">
                      <div class="form-group">
                    <!-- <button onclick="buttonAddListPIC()" id="buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="input-group form-group">
                        <input type="hidden" class="form-control" id="input_add_kodepic"  >
                        <input type="text" class="form-control" id="input_add_namapic"  disabled>
                        <button onclick="buttonAddListPIC()" id="buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
  
            <div class="col-md-3" hidden>
              <div class="row">
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-9">
                      <div class="form-group">
                        <label>Sales</label>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group form-group">
                    <input type="hidden" class="form-control" id="input_add_kodesales" >
                    <input type="text" class="form-control" id="input_add_namasales"  disabled>
                    <button onclick="buttonAddListSales()" id="buttonAddListSales"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
                  </div>
                </div>
              </div>
            </div>
  
            <div class="col-md-3" hidden>
              <div class="row">
                <div class="col-6" style="margin-top:-40px">
                  <div class="form-group">
                    <label>Draft PO</label>
                  </div>
                </div>
  
                <div class="col-md-6" style="margin-top:-40px">
                  <select onchange="onChangeDraftPO()" id="input_add_draftpo" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                    <option value=0 selected>Tidak</option>
                    <option value=1 >Ya</option>
                  </select>
                </div>
              </div>
            </div>
  
          </div>
            
        </div>
            <hr/>
      </div>

    </div>
    
      <div class="showhidemodalbodyaddmain container-fluid" id="modalBodyAddMainItems">
        <div class="container-fluid" style="overflow:auto; margin-top:-35px;">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_add" class="table table-bordered table-hover table-striped table-responsive-lg">
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Satuan</th>
                  <th style="padding: 4px 12px;" scope="col">Qty Minta</th>
                  <th id="qty_transfer" style="padding: 4px 12px;" scope="col">Qty Transfer</th>
                  <th style="padding: 4px 12px;" scope="col">Qty</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add" class="text-left" >
                <tr>
                  <td>1</td>
                  <td>1</td>
                  <td>1</td>
                  <td>1</td>
                  <td>1</td>
                  <td>1</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="row">
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
              onclick="buttonAddAddItem()" class="btn btn-secondary" hidden><b>+ Tambah Item</b></button>
              <button type="button" id="buttonSimpanData" class="btn btn-primary btn-lg" style="
              height: 30px; 
              padding: 4px 12px; 
              border-radius: 20px; 
              font-size: 0.75rem; 
              font-weight: 600; 
              text-transform: uppercase; 
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="buttonCloseFormSimpanData()">Simpan Data</button>
              <button type="button" id="buttonSimpanEdit" class="btn btn-primary btn-lg"
              style="height: 30px; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;"
              onclick="buttonCloseFormEditData()">Simpan Edit</button>
          </div>
        </div>

        <!-- ADD add -->
        <div id="addAddItem" class="container-fluid showhide">
          <hr/>
          
            <div class="row">
              <div class="col-4">
                <h4 id="h4AddAddItem" style="margin-left:-35px;">Add Item</h4>
                <h4 id="h4AddEditItem" style="margin-left:-35px;">Edit Item</h4>
              </div>
            </div>

          <div class="row">
            <div class="col-md-12">
                <div class="row">
              <!-- No Penyerahan -->

              <div class="col-md-3">
                <div class="row">
                  <!-- No Penyerahan -->
                  <div class="col-md-12">
                    <div class="row">
                      <div class="col-6">
                        <div class="form-group">
                          <label>Jasa</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="input-group form-group">
                          <select id="input_add_add_jasa" class="form-control form-select-lg mb-3" tabindex="9" disabled>
                            <option value=0 selected>Tidak</option>
                            <option value=1>Iya</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="row">
                  <!-- No Penyerahan -->
                  <div class="col-md-12">
                    <div class="row">
                      <div class="col-4">
                        <div class="form-group">
                          <label>FOC</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="input-group form-group"> {{-- nama lama : nopenyerahan --}}
                          <select id="input_add_add_foc" class="form-control form-select-lg mb-3" tabindex="9">
                            <option value=0>Tidak</option>
                            <option value=1>Iya</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-3" style="margin-left:-50px;">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>QTY</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <input type="number" class="form-control text-right" id="input_add_add_qty" value="0.00" tabindex="5">
                    </div>
                  </div>
                </div>
              </div>
            
                <div class="col-md-3">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>SatuanRP</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <select id="input_add_add_nosat" class="form-control form-select-lg mb-3" tabindex="9">
                        <option value=0 selected>Tidak</option>
                      </select>
                    </div>
                  </div>
                </div>

              </div>

                <div class="row">
                  <!-- Barang dan Nama Produk -->
                  <div class="col-md-6">

                    <div class="row">
                      <div class="col-3" style="margin-top:-10px;">
                        <div class="form-group">
                          <label>No. PNW PO</label>
                        </div>
                      </div>
                      <div class="col-md-8" style="margin-top:-10px;"> 
                        <div class="input-group form-group">
                          <input type="text" class="form-control" value="-" id="input_add_add_nopnwpo" readonly>
                          <button onclick="buttonAddAddListPWO()" id="buttonAddAddListBarang" class="btn btn-primary btn-sm text-right" tabindex="1">
                            <i class="bi bi-plus"></i>
                          </button>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-3" style="margin-top:-10px;">
                        <div class="form-group">
                          <label>Kode Barang</label>
                        </div>
                      </div>
                      <div class="col-md-8" style="margin-top:-10px;"> 
                        <div class="input-group form-group">
                          <input type="text" class="form-control" id="input_add_add_kodebarang" readonly>
                          <button onclick="buttonAddAddListBarang()" id="buttonAddAddListBarang" class="btn btn-primary btn-sm text-right" tabindex="1">
                            <i class="bi bi-plus"></i>
                          </button>
                        </div>
                      </div>
                    </div>

                    <!-- Nama Produk -->
                    <div class="row">
                      <div class="col-3" style="margin-top:-10px;">
                        <div class="form-group">
                          <label>Nama Barang</label>
                        </div>
                      </div>
                      <div class="col-md-8" style="margin-top:-10px;">
                        <div class="form-group">
                          <input type="text" class="form-control" id="input_add_add_namabarang" readonly>
                        </div>
                      </div>
                    </div>
                    
                  </div>

                <div class="col-md-6" style="margin-left:-50px;">
                    <!-- Harga -->
                  <div class="row">
                    <div class="col-md-3" style="margin-top:-10px;">
                      <div class="form-group">
                        <label>Harga</label>
                      </div>
                    </div>
                    <div class="col-md-6" style="margin-top:-10px;">
                      <div class="form-group">
                        <input type="number" class="form-control text-right" onchange="onChangeInputAddAddHarga()" id="input_add_add_harga" value="0.00" tabindex="6">
                      </div>
                    </div>
                  </div>
                    <!-- Disc RP -->
                    <div class="row">
                      <div class="col-md-3" style="margin-top:-10px;">
                        <div class="form-group">
                          <label>Disc RP</label>
                        </div>
                      </div>
                      <div class="col-md-6" style="margin-top:-10px;">
                        <div class="form-group">
                          <input type="number" class="form-control text-right" id="input_add_add_discrp" tabindex="7">
                        </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-1" style="margin-top:-10px;" hidden>
                  <div class="form-group">
                    <input type="text" min="1" max="100" class="form-control" id="input_add_add_noPPL" tabindex="1"> {{-- nama lama : satuanproduk --}}
                  </div>
                </div>
                <div class="col-md-1" style="margin-top:-10px;" hidden>
                  <div class="form-group">
                    <input type="text" min="1" max="100" class="form-control" id="input_add_add_urutPPL" tabindex="1"> {{-- nama lama : satuanproduk --}}
                  </div>
                </div>

              </div>
            </div>
            
            {{-- DISKON RP PENGHITUNG --}}
            <div class="col-md-12"> 

              <div class="row">
                <div class="col-md-2" style="margin-top:-10px;">
                  <div class="form-group">
                    <label>Disc(%)</label>
                  </div>
                </div>
                <div class="col-md-1" style="margin-top:-10px;">
                  <div class="form-group">
                    <input type="number" min="1" max="100" class="form-control" id="input_add_add_discpersen1" value=0 onChange='calculateDiscRp()' tabindex="1"> {{-- nama lama : satuanproduk --}}
                  </div>
                </div>
                -
                <div class="col-md-1" style="margin-top:-10px;">
                  <div class="form-group">
                    <input type="number" min="1" max="100" class="form-control" id="input_add_add_discpersen2" value=0 onChange='calculateDiscRp()' tabindex="2">
                  </div>
                </div>
                -
                <div class="col-md-1" style="margin-top:-10px;">
                  <div class="form-group">
                    <input type="number" min="1" max="100" class="form-control" id="input_add_add_discpersen3" value=0 onChange='calculateDiscRp()' tabindex="3">
                  </div>
                </div>
              </div>

            </div>
            
            <div class="col-md-12">
              <div id="divhargaterakhir">
                <div class="row">

                  <div class="col-12">
                    <div class="form-group">
                      <label>Harga Terakhir</label>
                    </div>
                  </div>

                  <div class="col-md-12 mb-4" style="overflow:auto;">
                    <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                      <table id="tabel_add_harga_terakhir" class="table table-bordered table-hover table-striped table-responsive-lg">
                        <thead class="text-center bg-primary text-white">
                          <tr>
                            <th style="padding: 4px 12px;" scope="col">Supplier</th>
                            <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                            <th style="padding: 4px 12px;" scope="col">Qnt</th>
                            <th style="padding: 4px 12px;" scope="col">Satuan</th>
                            <th style="padding: 4px 12px;" scope="col">Valas</th>
                            <th style="padding: 4px 12px;" scope="col">Kurs</th>
                            <th style="padding: 4px 12px;" scope="col">Harga</th>
                            <th style="padding: 4px 12px;" scope="col">Disc Rp</th>
                            <th style="padding: 4px 12px;" scope="col">Hrg. Nett</th>
                          </tr>
                        </thead>
                        <tbody id="tabel_data_add_harga_terakhir" class="text-left" >
                          <tr>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
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

            <div class="col-md-12">
              <div id="divStockProyeksi">
                <div class="row">

                  <div class="col-12">
                    <div class="form-group">
                      <label>Stock Proyeksi</label>
                    </div>
                  </div>

                  <div class="col-md-12 mb-4" style="overflow:auto;">
                    <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                      <table id="tabel_add_stock_proyeksi" class="table table-bordered table-hover table-striped table-responsive-lg">
                        <thead class="text-center bg-primary text-white">
                          <tr>
                            <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                            <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                            <th style="padding: 4px 12px;" scope="col">Stock</th>
                            <th style="padding: 4px 12px;" scope="col">Out PO</th>
                            <th style="padding: 4px 12px;" scope="col">Out SO</th>
                            <th style="padding: 4px 12px;" scope="col">S Marketing</th>
                          </tr>
                        </thead>
                        <tbody id="tabel_data_add_stock_proyeksi" class="text-left" >
                          <tr>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
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

          </div>

          <div class="row mt-2">
            <div class="col-md-12 text-right" style="margin-top:-40px;">
              <button type="button" class="btn btn-danger btn-lg" style="
              height: 30px; 
              padding: 4px 12px; 
              border-radius: 20px; 
              font-size: 0.75rem; 
              font-weight: 600; 
              text-transform: uppercase; 
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="closeShowHideAdd()" class="btn btn-secondary">Batal</button>

              <button type="button" class="btn btn-success btn-lg" style="
              height: 30px; 
              padding: 4px 12px; 
              border-radius: 20px; 
              font-size: 0.75rem; 
              font-weight: 600; 
              text-transform: uppercase; 
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="showTableHargaTerakhir()" class="btn btn-secondary">Harga</button>

              <button type="button" class="btn btn-info btn-lg" style="
              height: 30px; 
              padding: 4px 12px; 
              border-radius: 20px; 
              font-size: 0.75rem; 
              font-weight: 600; 
              text-transform: uppercase; 
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="showTableStockProyeksi()" class="btn btn-secondary">Stock Proyeksi</button>

              <button type="button" id="submitAddAdd" class="btn btn-primary btn-lg" style="
              height: 30px; 
              padding: 4px 12px; 
              border-radius: 20px; 
              font-size: 0.75rem; 
              font-weight: 600; 
              text-transform: uppercase; 
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="submitAddAdd()" class="btn btn-secondary">Submit Add</button>

              <button type="button" id="submitAddEdit" class="btn btn-primary btn-lg" style="
              height: 30px; 
              padding: 4px 12px; 
              border-radius: 20px; 
              font-size: 0.75rem; 
              font-weight: 600; 
              text-transform: uppercase; 
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="submitAddEdit()" class="btn btn-secondary">Submit Edit</button>
            </div>

          </div>

        </div>

        <!-- END ADD ADD -->

        <!-- ADD EDIT -->

        <div  id="addEditItem" class="container-fluid showhide">
            <div class="row">
              <div class="col-4">
                <h4>Edit Item</h4>
              </div>
            </div>
            <div class="row">

              <div class="col-md-12">

              <div class="row">

            <div class="col-md-4">

            <div class="row">
              <div class="col-9">
                <div class="form-group">
                  <label>Ref PR</label>
                </div>
              </div>
              <div class="col-3 text-right">
                <div class="form-group">
              <button onclick=""  class="btn btn-primary btn-sm text-right" disabled><i class="bi bi-plus" ></i></button>
              </div>

            </div>

            <div class="col-md-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_edit_refpr" value=""  disabled>
              </div>
            </div>

            </div>

            </div>


            <div class="col-md-4">


            <div class="row">
              <div class="col-9">
                <div class="form-group">
                  <label>No Penyerahan</label>
                </div>
              </div>
              <div class="col-3 text-right">
                <div class="form-group">
              <button onclick=""  class="btn btn-primary btn-sm text-right" disabled><i class="bi bi-plus"></i></button>
              </div>

            </div>

            <div class="col-md-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_edit_nopenyerahan"  disabled>
              </div>
            </div>

            </div>

            </div>
            </div>
            </div>

            <div class="col-md-4">


            <div class="row">
              <div class="col-9">
                <div class="form-group">
                  <label>Barang</label>
                </div>
              </div>
              <div class="col-3 text-right">
                <div class="form-group">
              <button onclick="buttonAddEditListBarang()" id="buttonAddEditListBarang"  class="btn btn-primary btn-sm text-right" disabled><i class="bi bi-plus"></i></button>
              </div>

            </div>

            <div class="col-md-12">
              <div class="form-group">
                <input type="hidden" class="form-control" id="input_add_edit_kodebarang" >
                <input type="text" class="form-control" id="input_add_edit_namabarang"  disabled>
              </div>
            </div>

            </div>

            </div>

            <div class="col-md-4">


            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <label>Nama Produk</label>
                </div>
              </div>


            <div class="col-md-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_edit_namaproduk" >
              </div>
            </div>

            </div>

            </div>

<div class="col-md-12">
  <div class="row">

    <div class="col-12">
      <div class="form-group">
        <label>Harga Terakhir</label>
      </div>
    </div>

    <div class="col-md-12 mb-4">
      <div class="form-group">
        <table id="tabel_edit_harga_terakhir" class="table table-bordered table-striped"  >
          <thead class="text-center">
            <tr>
              <th scope="col">Tanggal</th>
              <th scope="col">Qnt</th>
              <th scope="col">Satuan</th>
              <th scope="col">Valas</th>
              <th scope="col">Kurs</th>
              <th scope="col">Harga</th>
              <th scope="col">Disc Rp</th>
              <th scope="col">Total Diskon</th>
            </tr>
          </thead>
          <tbody id="tabel_data_edit_harga_terakhir" class="text-left" >
            <tr>
              <td>-</td>
              <td>-</td>
              <td>-</td>
              <td>-</td>
              <td>-</td>
              <td>-</td>
              <td>-</td>
              <td>-</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>


            <div class="col-md-12">
              <div class="row">


              <div class="col-md-2">
                <div class="row">

              <div class="col-md-12">
                <div class="form-group">
                  <label>Qty</label>
                </div>
              </div>


            <div class="col-md-12">
              <div class="form-group">
                <input type="number" class="form-control text-right" id="input_add_edit_qty" value ="0.00" >
              </div>
            </div>

            </div>
          </div>

            <div class="col-md-2">
              <div class="row">


            <div class="col-12">
              <div class="form-group">
                <label>Satuan</label>
              </div>
            </div>


            <div class="col-md-12">
              <select id="input_add_edit_nosat" onchange="onChangeInputAddAddNosat()" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                <option value=0 selected>Pilih Satuan</option>
              </select>
            </div>

          </div>
        </div>

        <div class="col-md-2 row">
          <div class="col-12">
            <div class="form-group">
              <label>Satuan Produk</label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <input type="text" class="form-control" id="input_add_edit_satuanproduk" >
            </div>
          </div>
        </div>

        <div class="col-md-2">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Harga</label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
              <input type="number" class="form-control text-right" onchange="onChangeInputAddAddHarga()" id="input_add_edit_harga" value ="0.00" >
              </div>
            </div>
          </div>
        </div>


    <div class="col-md-2">
      <div class="row">

        <div class="col-12">
          <div class="form-group">
            <label>Disc %</label>
          </div>
        </div>

        <div class="col-md-12">
          <div class="form-group">
            <input type="number" class="form-control text-right" id="input_add_edit_disc" onChange="onChangeInputAddAddDisc()" value ="0.00" >
          </div>
        </div>

      </div>
    </div>

    <div class="col-md-2">
      <div class="row">

        <div class="col-12">
          <div class="form-group">
            <label>Disc Rp</label>
          </div>
        </div>

        <div class="col-md-12">
          <div class="form-group">
            <input type="number" class="form-control text-right" id="input_add_edit_discrp" onChange="onChangeInputAddAddDiscRp()" value ="0.00" >
          </div>
        </div>

      </div>
    </div>

        </div>
        </div>
        <div class="col-md-12">
        <div class="row">


        </div>
        </div>

        <div class="col-md-2">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Tambah ke PO</label>
              </div>
            </div>
            <div class="col-md-12">
              <select onchange="" id="input_add_edit_tambahkepo" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                <option value=0 selected>Pilih</option>
                <option value=1 >Tidak</option>
                <option value=2 >Ya</option>
              </select>
            </div>
          </div>
        </div>

        <div class="col-md-2">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Booking</label>
              </div>
            </div>
            <div class="col-md-12">
            <select onchange="" id="input_add_edit_booking" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
              <option value=0 selected>Tidak</option>
              <option value=1 >Ya</option>
            </select>
            </div>
          </div>
        </div>

        <div class="col-md-2">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
              <label>Urgent</label>
              </div>
            </div>
            <div class="col-md-12">
                <select onchange="" id="input_add_edit_urgent" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                <option value=0 selected>Tidak</option>
                <option value=1 >Ya</option>
              </select>
            </div>
          </div>
        </div>
      </div>



          <div class="row mt-2">
            <div class="col-md-12 text-right">
              <button type="button" class="btn btn-secondary" onclick="closeShowHideAdd()" >Batal</button>
            </div>
          </div>


          <hr/>

          </div>


        <hr/>
    </div>

</div>

<!-- page3 -->

<div id="page3" class="container-fluid" style="display: none" >
      <div class="row">
        <div class="col-6 text-left">
          <h2>Detail SO</h2>
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

<div id="" class="">
  <div class="modal-body" >
<!-- <div class="container-fluid"> -->
  <div class="row">

    <input type="hidden" class="form-control" id="input_detail_nourut" >
    <div class="col-md-3">

      <div class="row">

        <div class="col-md-4" style="margin-top:-40px;">
          <div class="form-group">
            <label>No Bukti</label>
          </div>
        </div>
        <div class="col-md-8" style="margin-top:-40px;">
          <div class="form-group">
            <input type="text" class="form-control text-center" id="input_detail_nobukti" placeholder="" disabled>
          </div>
        </div>

      <div class="col-md-4" style="margin-top:-12px;">
        <div class="form-group">
          <label>Tanggal</label>
        </div>
      </div>
      <div class="col-md-8" style="margin-top:-12px;">
        <div class="form-group">
          <input type="date" class="form-control text-center" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
        </div>
      </div>


      <div class="col-md-4" style="margin-top:-10px;">
        <div class="form-group">
          <label>Pelanggan</label>
        </div>
      </div>


    <div class="col-md-8" style="margin-top:-10px;">
      <div class="input-group form-group">
        <input type="text" class="form-control text-center" id="input_detail_kodepelanggan" disabled>
      </div>
    </div>

      </div>

    </div>

    <div class="col-md-3">

      <div class="row">
        <!-- <div class="col-md-6">
          <div class="row">


        <div class="col-9">
          <div class="form-group">
            <label>Pelanggan</label>
          </div>
        </div>
        <div class="col-3 text-right">
          <div class="form-group">
        <button class="btn btn-primary btn-sm text-right" id="buttonAddListPelanggan" onclick="buttonAddListPelanggan()"><i class="bi bi-plus"></i></button>
        </div>

      </div>
      </div>
    </div>
    <div class="col-md-6">
    </div> -->


      <!-- <div class="col-md-6">
        <div class="row"> -->



        <div class="col-md-12" style="margin-top:-40px;">
          <div class="form-group">
            <input type="text" class="form-control text-center" id="input_detail_namapelanggan"  disabled>
          </div>
        </div>
        <!-- </div>
      </div> -->
      <!-- <div class="col-md-6">
        <div class="row"> -->


        <div class="col-md-12" style="margin-top:-10px;">
          <div class="form-group">
            <textarea  style="width: 100%; resize: none" rows=3  class="form-control text-center" id="input_detail_alamatpelanggan" disabled></textarea>
          </div>
        </div>
        <!-- </div>
      </div> -->

      </div>
    </div>

    <div class="col-md-3">
      <div class="row">
        <div class="col-md-6">

        <div class="row">
          <div class="col-md-4" style="margin-top:-40px;">
            <div class="form-group">
              <label>Valas</label>
            </div>
          </div>
          <div class="col-3 text-right">
            <div class="form-group">
          <!-- <button onclick="buttonAddListValas()" id="buttonAddListValas"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
          </div>

        </div>


      </div>
    </div>
    <div class="col-md-6">
      <div class="row">
        <div class="col-md-12" style="margin-top:-40px;">
          <div class="input-group form-group">
            <input type="text" class="form-control text-center" id="input_detail_valas"  disabled>
            <!-- <button onclick="buttonAddListValas()" id="buttonAddListValas"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->

          </div>
        </div>
      </div>
    </div>
    <div class="col-md-12" style="margin-top:-20px;">
      <div class="row">
        <div class="col-6">
          <div class="form-group">
            <label>Kurs</label>
          </div>
        </div>
          <div class="col-md-6">
            <div class="form-group">
              <input type="text" class="form-control text-center" id="input_detail_kurs"  disabled>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-12" style="margin-top:-12px;">
        <div class="row">
          <div class="col-6">
            <div class="form-group">
              <label>TOP</label>
            </div>
          </div>

        <div class="col-md-6">
          <div class="form-group">
            <input type="number" class="form-control text-center" id="input_detail_hari" disabled value=0 min=0 >
          </div>
        </div>
        </div>
    </div>

      </div>

    </div>



    <div class="col-md-3">
      <div class="row">

        <div class="col-md-12" style="margin-top:-40px;">
        <div class="row">
          <div class="col-6">
            <div class="form-group">
              <label>Pembayaran</label>
            </div>
          </div>

        <div class="col-md-6">
          <div class="form-group">
          <select  id="input_detail_pembayaran" disabled class="form-control text-center form-select-lg mb-3" aria-label=".form-select-lg example">
            <option value=0 selected >Non-Kredit</option>
            <option value=1 >Kredit</option>
          </select>
        </div>
        </div>
        </div>
        </div>

        <div class="col-md-12" style="margin-top:-12px;">
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label>TGL KIRIM</label>
              </div>
            </div>
            <div class="col-md-6">
                <input type="date" class="form-control text-center" id="input_detail_tanggalkirim" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>
          </div>

        <div class="col-md-12" style="margin-top:-12px;">
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label>PPN</label>
              </div>
            </div>
            <div class="col-md-6">
              <select id="input_detail_tipeppn" class="form-control text-center form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
                <option value=0 selected>None</option>
                <option value=1 >Exclude</option>
                <option value=2 >Include</option>
              </select>
            </div>
          </div>
        </div>


      </div>

    </div>
  </div>

  <!-- </div> -->
  <!-- <hr/> -->
  <!-- <div class="row ">
    <div class="col-md-12 text-left">
      <div class="row">
        <div class="col-md-12">

        </div>
      </div>
    <button type="button" class="btn btn-primary" onclick="buttonAddMainHeader()" class="btn btn-secondary"  >Header</button>
    <button type="button" class="btn btn-primary" onclick="buttonAddMainItems()" class="btn btn-secondary"  >Items</button>
</div>
</div> -->
<hr/>
<div class="row ">
<div class="col-md-12 mt-2 text-left">
  <button type="button" class="btn btn-primary btn-lg" style="
  height: 30px; 
  margin-top: -40px;
  padding: 4px 12px; 
  border-radius: 20px; 
  font-size: 0.75rem; 
  font-weight: 600; 
  text-transform: uppercase; 
  transition: background-color 0.3s, box-shadow 0.3s;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
  onclick="buttonShowHideHeaderDetail()" class="btn btn-secondary"><b>Show Hide Header</b></button>
</div>
</div>
  <div class="mt-4" id="modalBodyDetailMainHeader">

  <div class="row">
    <div class="col-md-3">
      <div class="row">

        <div class="col-md-6" style="margin-top:-20px;">
          <div class="form-group">
            <label>Alamat Kirim</label>
          </div>
        </div>

        <div class="col-md-12" style="margin-top:-15px;">
          <div class="form-group">
            <input type="hidden" class="form-control" id="input_detail_kodealamatkirim" >
            <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_alamatkirim"  disabled></textarea>
          </div>
        </div>

      </div>

    </div>

    <div class="col-md-3">
      <div class="row">
        <div class="col-md-8" style="margin-top:-20px;">
          <div class="form-group">
            <label>Ekspedisi</label>
          </div>
        </div>
        <div class="col-3 text-right">
          <div class="form-group">
        <!-- <button onclick="buttonAddListLokasiPenerima()" id="buttonAddListLokasiPenerima"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
        </div>

      </div>

      <!-- <div class="col-md-12">
        <div class="form-group">

        </div>
      </div> -->
      <div class="col-md-12" style="margin-top:-15px;">
        <div class="form-group">
          <input type="hidden" class="form-control" id="input_detail_kodelokasipenerima" >
          <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_alamatlokasipenerima"  disabled></textarea>
        </div>
      </div>

      </div>

      <!-- <div class="row">
        <div class="col-9">
          <div class="form-group">
            <label>PIC</label>
          </div>
        </div>
        <div class="col-3 text-right">
          <div class="form-group">
        <button onclick="buttonAddListPIC()" id="buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
        </div>

      </div>
      </div> -->
      <div class="row">

      <!-- <div class="col-md-12">
        <div class="form-group">

        </div>
      </div> -->

      </div>

    </div>

    <div class="col-md-3">


      <div class="row">

        <div class="col-md-10" style="margin-top:-20px;">
          <label>Keterangan</label>
        </div>

      <div class="col-md-12" style="margin-top:-15px;">
        <div class="form-group" style="margin-top: 14px">
          <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_catatan" disabled></textarea>
        </div>
      </div>

      <!-- <div class="col-md-12">

      </div> -->

      </div>

      <div class="row">

      <!-- <div class="row"> -->

      </div>

      <div class="row ">

  </div>

    </div>

    <div class="col-md-3">
      <div class="row">

        <div class="col-md-12">
          <div class="row">
            <div class="col-md-6" style="margin-top:-20px;">
              <div class="form-group">
                <label>DP</label>
              </div>
            </div>

          <div class="col-md-6" style="margin-top:-20px;">
            <div class="form-group">
              <input type="number" class="form-control text-center" id="input_detail_dp" value='0.00' disabled>
            </div>
          </div>
          </div>

        <div class="row">
          <div class="col-md-6" style="margin-top:-10px;">

            <div class="form-group">
              <label>No PO</label>
            </div>
          </div>

        <div class="col-md-6" style="margin-top:-10px;">
          <div class="form-group">
            <input  type="text" class="form-control text-center" id="input_detail_nopo"  disabled>
          </div>
        </div>
        </div>

        <div class="row">
          <div class="col-md-6" style="margin-top:-10px;">
            <div class="form-group">
              <label>Tgl PO</label>
            </div>
          </div>

        <div class="col-md-6" style="margin-top:-10px;">
          <div class="form-group">
            <input type="date" class="form-control text-center" id="input_detail_tanggalpo" value="{!! date('Y-m-d') !!}" disabled>
          </div>
        </div>
        </div>
        </div>

      </div>

      <div class="row">

      <!-- <div class="col-md-12">

      </div> -->

      </div>

    </div>
    <!-- <div class="col-md-12 mt-2 text-right" style="margin-bottom: 20px">
    <button type="button" class="btn btn-primary" id="buttonSubmitSaveHeader" onclick="submitSaveHeader()" class="btn btn-secondary"  >Save Header</button>
</div> -->

<div class="col-md-3">
  <div class="row">
    <div class="col-md-6">
      <div class="row">

      <div class="col-md-6" style="margin-top:-10px;">
        <div class="form-group">
          <label>PIC</label>
        </div>
      </div>
      <div class="col-3 text-right">
        <div class="form-group">
      <!-- <button onclick="buttonAddListPIC()" id="buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
      </div>

    </div>
    </div>
    </div>
    <div class="col-md-6">
      <div class="row">
        <div class="col-md-12" style="margin-top:-10px;">
          <div class="input-group form-group">
            <input type="hidden" class="form-control" id="input_detail_kodepic"  >
            <input type="text" class="form-control" id="input_detail_namapic"  disabled>
            <!-- <button onclick="buttonAddListPIC()" id="buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->

          </div>
        </div>
      </div>
    </div>

  </div>

</div>

<div class="col-md-3">
  <div class="row">
    <div class="col-md-6">
      <div class="row">

      <div class="col-md-10" style="margin-top:-10px;">
        <div class="form-group">
          <label>Back Office</label>
        </div>
      </div>
      <div class="col-3 text-right">
        <div class="form-group">
      <!-- <button onclick="buttonAddListBackOffice()" id="buttonAddListBackOffice"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
      </div>

    </div>
    </div>
    </div>

    <div class="col-md-6">
      <div class="row">
        <div class="col-md-12">
          <div class="row">

          <!-- <div class="col-4">

          <div class="form-group">

          </div>

          </div> -->
          <div class="col-md-12" style="margin-top:-10px;">
          <div class="input-group form-group">
            <input type="hidden" class="form-control" id="input_detail_kodebackoffice" >
            <input type="text" class="form-control" id="input_detail_namabackoffice"  disabled>
            <!-- <button onclick="buttonAddListBackOffice()" id="buttonAddListBackOffice"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->

          </div>

          </div>
          </div>
        <!-- </div> -->
        </div>
      </div>

    </div>

    <!-- <div class="row"> -->

    <!-- </div> -->

  </div>

</div>

<div class="col-md-3">
  <div class="row">

  <div class="col-md-12">
    <div class="row">
      <div class="col-md-6">
        <div class="row">
          <div class="col-md-8" style="margin-top:-10px;">
            <div class="form-group">
              <label>Sales</label>
            </div>
          </div>
          <div class="col-3 text-right">
            <div class="form-group">
          <!-- <button onclick="buttonAddListSales()" id="buttonAddListSales"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
          </div>

        </div>

        </div>
      </div>
      <div class="col-md-6" style="margin-top:-10px;">
        <div class="input-group form-group">
          <input type="hidden" class="form-control" id="input_detail_kodesales" >
          <input type="text" class="form-control" id="input_detail_namasales"  disabled>
          <!-- <button onclick="buttonAddListSales()" id="buttonAddListSales"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->

        </div>
      </div>

    </div>

  </div>
  <!-- <div class="col-md-12">
    <div class="form-group">
      <input type="hidden" class="form-control" id="input_detail_kodesales" >
      <input type="text" class="form-control" id="input_detail_namasales"  disabled>
    </div>
  </div> -->
  </div>

</div>

<div class="col-md-3">
  <div class="row">
    <div class="col-md-6" style="margin-top:-25px;">
      <div class="form-group">
        <label>Draft PO</label>
      </div>
    </div>

  <div class="col-md-6" style="margin-top:-25px;">
    <select  id="input_detail_draftpo" class="form-control text-center form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
      <option value=0 selected>Tidak</option>
      <option value=1 >Ya</option>
    </select>
  </div>
  </div>

</div>

  </div>
  <hr/>

</div>

</div>
<div class=" container-fluid" id="" style="margin-top:-40px;">

  <!-- sinia -->

<!-- END ADD EDIT -->

<div class="container-fluid mt-4" style="overflow:auto;">
  <!-- <input type="hidden" name="noUrut" id="input_detail_noUrut" value="" /> -->
  <div class="row" style="overflow:auto;">
    <table id="tabel_detail" class="table table-bordered table-hover table-striped table-responsive-lg">
      <thead class="text-center bg-primary text-white">
        <tr>
          <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
          <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
          <th style="padding: 4px 12px;" scope="col">Qty</th>
          <th style="padding: 4px 12px;" scope="col">Sat</th>
          <th style="padding: 4px 12px;" scope="col">Harga</th>
          <th style="padding: 4px 12px;" scope="col">Diskon</th>
          <th style="padding: 4px 12px;" scope="col">NDPP</th>
          <!-- <th scope="col">Actions</th> -->

        </tr>
      </thead>

      <tbody id="tabel_data_detail" class="text-left" >

        <tr >

          <td></td>
          <td></td>

            <td class="text-center">
              <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
              <button class="btn btn-success btn-sm" type="button" ><i class="bi bi-pen"></i></button>
              <button class="btn btn-danger btn-sm" type="button" ><i class="bi bi-trash"></i></button>
              <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-list"></i></button>
            </td>
      </tr>
      </tbody>

    </table>
  </div>
    <!-- <button onclick="buttonSubKategori()">tes</button> -->
</div>

<div class="row ">
<div class="col-md-12 mt-2 text-right">
<!-- <button type="button" class="btn btn-primary" onclick="buttonAddAddItem()" class="btn btn-secondary"  ><b>+ Tambah Item</b></button> -->
</div>
</div>

<hr/>
</div>

  <div class="container-fluid" style="margin-top: -10px;">
    <div class="row" >

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label>Disc %</label>
          </div>
        </div>
        <div class="col-md-9" style="margin-top:-50px; margin-left:60px;">
          <div class="form-group">
            <input type="number" class="form-control text-right" id="input_detail_disc" disabled value ="0.00" >
          </div>
        </div>
      </div>
    </div>

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label style="margin-left:-10px;">DiscRp</label>
          </div>
        </div>
        <div class="col-md-9" style="margin-left:-25px;">
          <div class="form-group">
            <input type="number" class="form-control text-right" id="input_detail_discrp" disabled value ="0.00" >
          </div>
        </div>
      </div>
    </div>

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label style="margin-left:-10px;">DPP</label>
          </div>
        </div>
        <div class="col-md-9" style="margin-left:-50px;">
          <div class="form-group">
            <input type="text" class="form-control text-right" id="input_detail_dpp" value ="0.00" disabled>
          </div>
        </div>
      </div>
    </div>

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label style="margin-left:-40px;">PPN</label>
          </div>
        </div>

        <div class="col-md-9" style="margin-left:-80px;">
          <div class="form-group">
          <input type="text" class="form-control text-right" id="input_detail_ppn" value ="0.00" disabled>
          </div>
        </div>
      </div>
    </div>

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-8">
          <div class="form-group">
            <label style="margin-left:-75px;">Grand Total</label>
          </div>
        </div>

        <div class="col-md-10" style="margin-left:45px; margin-top:-50px;">
          <div class="form-group">
          <input type="text" class="form-control text-right" id="input_detail_grandtotal" value ="0.00" disabled>
          </div>
        </div>
      </div>
    </div>

    </div>

  </div>
</div>
</div>

<!-- page3 end input_add -->

<div id="page4" style="display: none; margin-top: -80px" class="mainpage container-fluid" >
  <div class="row">
    <div class="col-8 text-left">
      <h2>Detail Permintaan Transfer Barang</h2>
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
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="buttonCloseForm()">Close</button>
    </div>
  </div>

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
                <input type="text" class="form-control text-left" id="detail_nobukti" placeholder="" disabled>
              </div>
            </div>
            <div class="col-md-6" style="margin-top:-10px;">
              <div class="form-group">
                <label>Gudang Asal</label>
              </div>
            </div>
            <div class="col-md-6" style="margin-top:-10px;">
              <div class="input-group mb-3">
                <input type="text" class="form-control text-left" value='-' id="detail_kodeGudangAsal" disabled>
                {{-- <button class="btn btn-primary btn-sm rounded-end shadow-sm" id="buttonAddListGudangAsal" onclick="buttonAddListGudangAsal()">
                  <i class="bi bi-plus"></i>
                </button> --}}
              </div>
            </div>
            <div class="col-md-12" style="margin-top:-15px;">
              <div class="form-group">
                <textarea style="width: 100%; resize: none;" rows=3 placeholder="Gudang Asal" class="form-control text-left align-items-center" id="detail_namaGudangAsal"  disabled></textarea>
              </div>
            </div>

          </div>
        </div>

        <div class="col-md-3">
          <div class="row">

            <div class="col-md-6">
              <div class="form-group">
                <label>Gudang Tujuan</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group mb-3">
                <input type="text" class="form-control text-left" value='-' id="detail_kodeGudangTujuan" disabled>
                {{-- <button class="btn btn-primary btn-sm rounded-end shadow-sm" id="buttonAddListGudangTujuan" onclick="buttonAddListGudangTujuan()">
                  <i class="bi bi-plus"></i>
                </button> --}}
              </div>
            </div>

            <div class="col-md-12" style="margin-top:-15px;">
              <div class="form-group">
                <textarea style="width: 100%; resize: none;" rows=5 placeholder="Gudang Tujuan" class="form-control text-left align-items-center" id="detail_namaGudangTujuan"  disabled></textarea>
              </div>
            </div>
          </div>
        </div>

      {{-- <div class="col-md-3">
        <div class="row">
          <div class="col-md-12" style="margin-top:-5px;">
            <div class="form-group">
              <textarea style="width: 100%; resize: none;" rows=5 onblur="onChangeKeterangan()" placeholder="Keterangan" class="form-control" id="input_add_keterangan"></textarea>
            </div>
          </div>
        </div>
      </div> --}}

    </div>
    <hr/>
        <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">
              <table id="detailKoreksiTable" class="table table-bordered table-striped"  >
                <thead class="text-center bg-primary text-white">
                <tr>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Sat</th>
                  <th scope="col">Qty</th>
                </tr>
                </thead>
                <tbody id="detailKoreksiTableData" class="" >
                  <tr>
                    <td colspan=4 class="text-center">Belum ada data</td>
                </tr>
                </tbody>
              </table>
    </div>
  </div>
</div>

@include('gudang.modals/modalTRFAdd')

@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script type="text/javascript">

let dataTableAdd = []
let dataTableEdit = []
let dataHeaderAdd = [];

let dataAddAddListItem = []

let dataRefreshOutstanding = []
let dataRefreshOutstanding2 = []

let dataRefreshPenerimaan = []

let listAlamatKirim = []

let tempAddAdd = {}
let tempAddEdit = {}
let tempIndexEdit = 0
let tempEditAdd = {}
let tempEditEdit = {}

let tipeform = ''
let tipeformitem = ''

let tableIsiRefreshData = {}
let tableIsiRefreshDataList = []
let dataNoBuktiSatuan = {}
let dataNoUrutSatuan = {}
let tempSatuanBarang = []

function buttonOtorisasi (nobukti) {
  console.log(nobukti)

  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

        let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('trfbrgupdateotorisasi') !!}",
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
          url: "{!! url('trfbrgupdatebatalotorisasi') !!}",
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

function onChangeCatatan () {

  if (tipeform == 'edit') {
    let value  = $("#input_add_keterangan").val()
    onChangeHeader('Keterangan' , value)

  }

}
function onChangeNoPO () {
  if (tipeform == 'edit') {
    let value  = $("#input_add_noso").val()
    onChangeHeader('NoPesanan' , value)
  }
}

function onChangeTgglKirim () {
  if (tipeform == 'edit') {
    let value  = $("#input_add_tanggalkirim").val()
    onChangeHeader('TglKirim' , value)
  }
}

function onChangeTipePPN () {
  console.log('onChangeTipePPN')
  if (tipeform == 'edit') {
    let value = $("#input_add_tipeppn").val()
    console.log(value)
    onChangeHeader('TipePPn' , value)
    onChangeHeader('PPN' , value)
    refreshUpdateHeader()
    let nobukti = $("#input_add_nobukti").val()
    refreshDataTableAdd(nobukti)
  }


}

function onChangeDP () {
  console.log('onChangeDP')
  if (tipeform == 'edit') {
    let value = $("#input_add_nopocust").val()
    console.log(value)
    onChangeHeader('DP' , value)
    refreshUpdateHeader()
    let nobukti = $("#input_add_nobukti").val()
    refreshDataTableAdd(nobukti)
  }


}

function onChangeDraftPO () {
  console.log('onChangeDraftPO')
  if (tipeform == 'edit') {
    let value = $("#input_add_draftpo").val()
    console.log(value)
    onChangeHeader('PPO' , value)
    refreshUpdateHeader()
    let nobukti = $("#input_add_nobukti").val()
    refreshDataTableAdd(nobukti)
  }

}

function onChangeHari () {
  console.log('onChangeHari')
  if (tipeform == 'edit') 
  {
    let value = $("#input_add_hari").val()
    console.log(value)
    onChangeHeader('HARI' , value)
    refreshUpdateHeader()
    let nobukti = $("#input_add_nobukti").val()
    refreshDataTableAdd(nobukti)
  }
}

function onChangeInputAddDisc () {
    // document.getElementById("input_add_discrp").value = '0.00'
    console.log('onChangeDisc')
    if (tipeform == 'edit') {
      let value = $("#input_add_disc").val()
      console.log(value)
      onChangeHeader('DISC' , value)
      refreshUpdateHeader()
      let nobukti = $("#input_add_nobukti").val()
      refreshDataTableAdd(nobukti)
    }
}

function onChangeInputAddDiscRp () 
{
  // document.getElementById("input_add_disc").value = '0.00'
  console.log('onChangeDiscRp')
    if (tipeform == 'edit') {
      let value = $("#input_add_discrp").val()
      console.log(dataHeaderAdd)
      let x = Number(value) / Number(dataHeaderAdd.TotSubTotal) * 100
      console.log(x)
      console.log(value)
      onChangeHeader('DISC' , x)
      refreshUpdateHeader()
      let nobukti = $("#input_add_nobukti").val()
      refreshDataTableAdd(nobukti)
    }
}

function refreshUpdateHeader () 
{
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  $.ajax({
    url: "{!! url('pospupdatepo') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti
    },
    success: function(res) {
      // alertify.success('update header berhasil')
      // return
      console.log('check')

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function onChangeHeaderSP (field, value , field1 = null , value2 = null) 
{
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  $.ajax({
    url: "{!! url('soonchangeheadersp') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      field,
      value,
      nobukti
    },
    success: function(res) {
      alertify.success('update header berhasil')
      return
      console.log('check')

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function onChangeHeader (field, value) {
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  $.ajax({
    url: "{!! url('poonchangeheader') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      field,
      value,
      nobukti
    },
    success: function(res) {
      alertify.success(`update ${field} berhasil`)

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function deleteTransfer () {
  
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()

  console.log(nobukti)
  $.ajax({
    url: "{!! url('trfbrgdeletetransfer') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti
    },
    success: function(res) {
      alertify.success(`Transfer Telah Dihapus`)

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function deleteTransferData (nobukti) {
  
  let _token  = $("#_token").val()

  console.log(nobukti)
  $.ajax({
    url: "{!! url('trfbrgdeletetransfer') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti
    },
    success: function(res) {
      alertify.success(`Transfer Telah Dihapus`)
      loadAll();

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function deleteTransferSimpanData () {

  let _token = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  let bisaDelete = 1;
  console.log(nobukti)

  // Check if ANY input_table_qty input has data (is filled)
  for (let i = 0; i < dataTableAdd.length; i++) {
  let urut = dataTableAdd[i].URUT;
  let qtyInput = document.getElementById(`input_table_qty${urut}`);
  
  // console.log(`[${i}] URUT: ${urut} | Element:`, qtyInput, `| Value: "${qtyInput?.value}" | Trimmed: "${qtyInput?.value.trim()}" | Empty: ${qtyInput?.value.trim() === ''}`);
  
  if (qtyInput) {
    let qtyValue = qtyInput.value.trim();
    
    if (qtyValue !== '' && qtyValue !== null) {
      bisaDelete = 0;
      break;
    }
  }
}

  if (bisaDelete === 1) {
    console.log('All qty inputs are empty, can delete, value bisaDelete = ' + bisaDelete)
  } else {
    console.log('Some qty inputs are filled, cannot delete, value bisaDelete = ' + bisaDelete)
  }

  if (bisaDelete == 1) {
    $.ajax({
      url: "{!! url('trfbrgdeletetransfer') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti
      },
      success: function(res) {
        alertify.success(`Tidak ada update QNT, data dihapus.`)
        loadAll()
      },
      error: function(err) {
        console.log(err)
      }
    })
  } else {
    console.log('Data tersimpan.')
    
  }
}

function submitAddAdd () {

  console.log('submitAddAdd')

  let checkDate = new Date($("#input_add_tanggal").val())
  
  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if (checkDate.getFullYear() !== Number(periode_tahun) || (checkDate.getMonth() +1) !== Number(periode_bulan)) {
      alertify.warning("Tanggal tidak sesuai periode");
      return
  }

  let _token  = $("#_token").val()
  let Nobukti = dataNoBuktiSatuan
  let Nourut = dataNoUrutSatuan
  let Nob = tableIsiRefreshData[0].nobukti
  // let IdUser dari controller 
  // let MaxOL -1 
  // let DBASAL
  // let DBTUJUAN
  let tgl = new Date().toISOString().split('T')[0];

  $.ajax({
    url: "{!! url('trfbrgspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      Nobukti,
      Nourut,
      Nob,
      tgl

    },
    success: function(res) {
      
      if (res == 1) {

        loadAll()
        tipeform = 'add'
        document.getElementById("buttonAddListPelanggan").disabled = true
        $('#divhargaterakhir').hide();
        $('#divStockProyeksi').hide();
        cleanFormAddAdd()

        refreshDataTableAdd(NoBukti)

        alertify.success('Berhasil menambah item')
      }
      if(res == 2) {
        setNewNoBukti()
        alertify.warning('Nobukti telah direfresh silahkan submit ulang')
      }

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function submitAddEdit () {

  console.log('submitAddEdits')

  let checkDate = new Date($("#input_add_tanggal").val())
  
  // let periode_bulan = document.getElementById("periode_bulan").value
  // let periode_tahun = document.getElementById("periode_tahun").value

  // if (checkDate.getFullYear() !== Number(periode_tahun) || (checkDate.getMonth() +1) !== Number(periode_bulan)) {
  //     alertify.warning("Tanggal tidak sesuai periode");
  //     return
  // }

  let TglJatuhTempo = new Date($("#input_add_tanggal").val())

  let hari = $("#input_add_hari").val()

  TglJatuhTempo.setDate(TglJatuhTempo.getDate() + Number(hari))
  console.log(TglJatuhTempo)

  let Jmlrecord = 0
  if (dataTableAdd.length) {
    Jmlrecord = 1
  }

  let _token  = $("#_token").val()
  let Choice = "U"
  let NoBukti = $("#input_add_nobukti").val()
  let NoUrut = $("#input_add_nourut").val()
  let Tanggal = $("#input_add_tanggal").val()
  let KodeSupp = $("#input_add_kodesupplier").val()
  //handling kosong
  let KodeExp = $("#input_add_kodeekspedisi").val()
  let Keterangan = $("#input_add_keterangan").val()
  //faktursupp kosong
  let KodeVls = $("#input_add_valas").val()
  let Kurs = $("#input_add_kurs").val()
  let PPn = $("#input_add_tipeppn").val()
  let TipeBayar = $("#input_add_pembayaran").val()
  let Hari = $("#input_add_hari").val()
  //TipeDisc kosong
  //Disc = 0 
  //DiscRp
  let Urut = tempAddEdit.Urut
  let KodeBrg =  $("#input_add_add_kodebarang").val()
  let Qnt =  $("#input_add_add_qty").val()
  let NoSat =  $("#input_add_add_nosat").val()
  //satuan  
  //isi teko dbbarang
  let Harga = $("#input_add_add_harga").val()
  let DiscP = $("#input_add_add_discpersen1").val()
  let DiscTot = $("#input_add_add_discrp").val()
  let NoPPL = $("#input_add_add_noPPL").val()
  //isclose kosong
  //isCloseD kosong
  //catatan kosong
  //IsExp = false 
  //Tolerate kosong 
  let UrutPPL = $("#input_add_add_urutPPL").val()
  let Kodegdg = $("#input_add_kodealamatkirim").val()
  let Discpdet2 = $("#input_add_add_discpersen2").val()
  let Discpdet3 = $("#input_add_add_discpersen3").val()
  //discpdet4 kosong
  //discpdet5 kosong
  //flagtipe 1
  let NamaBrg =  $("#input_add_add_namabarang").val()
  //isjasa = 0
  //pFirst = 0
  let pFOC = $("#input_add_add_foc").val()
  let Noso = $("#input_add_noso").val()
  //jmlrecord no bukti duplikat
  let NOPOCUST = $("#input_add_nopocust").val()
  //iduser = $user->name
  //pJasa = 0
  //npph23 0
  //perkiraan 
  //satX
  //cost
  //subcost
  let TglKirim = $("#input_add_tanggalkirim").val()
  //pph21
  let NOPNw = $("#input_add_add_nopnwpo").val()
  let UrutPNW = 0

  // console.log(kodesupplier,'*')
  // if (!kodesupplier || !kodebackoffice || !nobukti || !valas || !kodealamatkirim || !kodelokasipenerima) {
  //   alertify.warning("Data tidak lengkap")
  //   return
  //}

  if (!NoPPL){
    NoPPL = ''
  };

  let date1 = ""
  if (TglJatuhTempo) {
      let date = new Date(TglJatuhTempo);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
    }

  TglJatuhTempo  = date1

  // let tipediskon = 0
  // if (disc) {
  //   tipediskon = 1
  // }
  // if (discrp) {
  //   tipediskon = 1
  // }

  console.log(tempAddEdit)

  let Satuan = ''
  let qnt1 = 0
  let Isi = 0
  if (NoSat == 1) {
    qnt1 = Qnt * tempAddEdit.Isi
    Satuan = tempAddEdit.Satuan
    Isi = tempAddEdit.Isi
  }

  if (NOPNw == '-') {
    UrutPNW = 0
  }

  if (!Keterangan) {
    Keterangan = '-'
  }

  console.log({
    _token,
    Choice,
    NoBukti,
    NoUrut,
    Tanggal,
    TglJatuhTempo,
    KodeSupp,
    // Handling,
    KodeExp,
    Keterangan,
    // FakturSupp,
    KodeVls,
    Kurs,
    PPn,
    TipeBayar,
    Hari,
    // TipeDisc,
    // Disc,
    // DiscRp,
    Urut,
    KodeBrg,
    Qnt,
    NoSat,
    Satuan,
    Isi,
    Harga,
    DiscP,
    DiscTot,
    NoPPL,
    // IsClose,
    // IsCloseD,
    // Catatan,
    // IsExp,
    // Tolerate,
    UrutPPL,
    Kodegdg,
    Discpdet2,
    Discpdet3,
    // Discpdet4,
    // Discpdet5,
    // FlagTipe,
    NamaBrg,
    // IsJasa,
    // pFirst,
    pFOC,
    Noso,
    Jmlrecord,
    NOPOCUST,
    // IdUser,
    // pJasa,
    // NPPH23,
    // PERKIRAAN,
    // SatX,
    // COST,
    // SUBCOST,
    TglKirim,
    // PPH21,
    NOPNw,
    UrutPNW
  })

  console.log('==========' , Number(NoSat))
  if (!KodeBrg || !Kodegdg) {
    alertify.warning("Data belum lengkap")
    return
  }
  if (Number(Hari) < 0 || Number(Qnt) <= 0 || Number(Harga) < 0 || Number(DiscTot) < 0)  {
    alertify.warning("Angka negatif")
    return
  }

  $.ajax({
    url: "{!! url('pospadd') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      Choice,
      NoBukti,
      NoUrut,
      Tanggal,
      TglJatuhTempo,
      KodeSupp,
      // Handling,
      KodeExp,
      Keterangan,
      // FakturSupp,
      KodeVls,
      Kurs,
      PPn,
      TipeBayar,
      Hari,
      // TipeDisc,
      // Disc,
      // DiscRp,
      Urut,
      KodeBrg,
      Qnt,
      NoSat,
      Satuan,
      Isi,
      Harga,
      DiscP,
      DiscTot,
      NoPPL,
      // IsClose,
      // IsCloseD,
      // Catatan,
      // IsExp,
      // Tolerate,
      UrutPPL,
      Kodegdg,
      Discpdet2,
      Discpdet3,
      // Discpdet4,
      // Discpdet5,
      // FlagTipe,
      NamaBrg,
      // IsJasa,
      // pFirst,
      pFOC,
      Noso,
      Jmlrecord,
      NOPOCUST,
      // IdUser,
      // pJasa,
      // NPPH23,
      // PERKIRAAN,
      // SatX,
      // COST,
      // SUBCOST,
      TglKirim,
      // PPH21,
      NOPNw,
      UrutPNW

    },
    success: function(res) {
      console.log('resspsoaddedit', res)
      loadAll()

      // lockFormAdd()
      $('.showhide').hide();
      refreshDataTableAdd(NoBukti)

      alertify.success('Berhasil edit item')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function onChangeInputAddPembayaran () {
  console.log("onChangeInputAddPembayaran")
  let check = Number($("#input_add_pembayaran").val())
  console.log(typeof check)
  console.log(check)

  if (dataTableAdd.length) {

    onChangeHeader('TIPEBAYAR' , check)
  }
  let nobukti = $("#input_add_nobukti").val()
  console.log('len',dataTableAdd.length)
  if (check) {
    let _token = $("#_token").val();
    let kodesupplier = $("#input_add_kodesupplier").val();

    $.ajax({
      url: "{!! url('socekkredithari') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        kodesupplier
      },
      success: function(res) {
        console.log(res)
        if(res.length && res[0].hari) {
          document.getElementById("input_add_hari").value = res[0].hari

          if (dataTableAdd.length) {
            console.log('masokk')
            onChangeHeader('HARI' , res[0].hari)
            refreshUpdateHeader()
            // let nobukti = $("#input_add_nobukti").val()
            refreshDataTableAdd(nobukti)

          }
        }

      }})

  } else {
    document.getElementById("input_add_hari").value = 0
    // console.log('onChangeHari')
    if (tipeform == 'edit') {
      console.log('len', dataTableAdd.length)
      console.log(value)
      // onChangeHeader('TIPEBAYAR' , check)
      if (dataTableAdd.length) {
        console.log('masokk 2')
        onChangeHeader('HARI' , 0)
        refreshUpdateHeader()
        // let nobukti = $("#input_add_nobukti").val()
        refreshDataTableAdd(nobukti)

      }
    }
  }
}

function onChangeInputAddAddDisc () {
  console.log("onChangeInputAddAddDisc")
  let harga = $("#input_add_add_harga").val();

  if (!Number(harga)) {

    document.getElementById("input_add_add_discrp").value = '0.00'
    return
  }

  let disc = $("#input_add_add_disc").val();
  let discRp = Number(harga) * Number(disc) / 100
  document.getElementById("input_add_add_discrp").value = parseFloat(discRp).toFixed(2)

}

function onChangeInputAddAddHarga () {
  document.getElementById("input_add_add_discrp").value = '0.00'
  document.getElementById("input_add_add_disc").value = '0.00'
}

function onChangeInputAddEditHarga () {
  document.getElementById("input_add_edit_discrp").value = '0.00'
  document.getElementById("input_add_edit_disc").value = '0.00'
}

function onChangeInputAddAddDiscRp () {
  console.log("onChangeInputAddAddDiscRp")
  let harga = $("#input_add_add_harga").val();

  if (!Number(harga)) {

    document.getElementById("input_add_add_disc").value = '0.00'
    return
  }

  let discRp = $("#input_add_add_discrp").val();
  let disc = Number(discRp) / Number(harga) * 100
  document.getElementById("input_add_add_disc").value = parseFloat(disc).toFixed(2)
}

function buttonAddAddItem () {
  tipeformitem = 'add'
  $('.showhide').hide();

  $('#divhargaterakhir').hide();

  cleanFormAddAdd()
  document.getElementById("buttonAddAddListBarang").disabled = false
  $('#h4AddAddItem').show();
  $('#h4AddEditItem').hide();
  $('#submitAddAdd').show();
  $('#submitAddEdit').hide();
  $('#addAddItem').show();
  document.getElementById("input_add_add_namabarang").scrollIntoView();
}

function showTableHargaTerakhir () {
  if ( $("#divStockProyeksi").is(':visible')) 
  {
    $('#divStockProyeksi').hide();
  }

  if (!$("#divhargaterakhir").is(':visible')) 
  {
    $('#divhargaterakhir').show();
  } else 
  {
    $('#divhargaterakhir').hide();
  }
}

function showTableStockProyeksi () {
  if ($("#divhargaterakhir").is(':visible')) 
  {
    $('#divhargaterakhir').hide();
  }
  
  if (!$("#divStockProyeksi").is(':visible'))
  {
    $('#divStockProyeksi').show();
  } else 
  {
    $('#divStockProyeksi').hide();
  }
}

function buttonAddEditItem (i) {
  tipeformitem = 'edit'
  let _token = $("#_token").val();
  $('.showhide').hide();
  // cleanFormAddAdd()
  console.log(dataTableAdd[i])
  tempAddEdit = dataTableAdd[i]

  console.log(typeof tempAddEdit.Harga);
  console.log(tempAddEdit.Harga + " ini harga")

  let selectOption = ''
  if (tempAddEdit.Satuan) {
    selectOption += `<option value=1 selected>${tempAddEdit.Satuan}</option>`
  }

  if (tempAddEdit.NoPNW == ''){
    tempAddEdit.NoPNW = '-' 
  }

  document.getElementById("input_add_add_jasa").value = tempAddEdit.Isjasa
  document.getElementById("input_add_add_foc").value = tempAddEdit.PFOC
  document.getElementById("input_add_add_nopnwpo").value = tempAddEdit.NoPNW
  document.getElementById("input_add_add_kodebarang").value = tempAddEdit.KodeBrg
  document.getElementById("input_add_add_namabarang").value = tempAddEdit.NamaBrg
  document.getElementById("input_add_add_discpersen1").value = Number(tempAddEdit.DiscP1) ?  tempAddEdit.DiscP1 : '0.00'
  document.getElementById("input_add_add_discpersen2").value = Number(tempAddEdit.Discp2) ?  tempAddEdit.Discp2 : '0.00'
  document.getElementById("input_add_add_discpersen3").value = Number(tempAddEdit.Discp3) ?  tempAddEdit.Discp3 : '0.00'
  document.getElementById("input_add_add_qty").value = parseFloat(tempAddEdit.Qnt).toFixed(2)
  document.getElementById("input_add_add_nosat").innerHTML = selectOption
  document.getElementById("input_add_add_harga").value = Number(tempAddEdit.Harga) ? parseFloat(tempAddEdit.Harga).toFixed(2) : '0.00'
  document.getElementById("input_add_add_discrp").value = Number(tempAddEdit.DISCTOT) ? parseFloat(tempAddEdit.DISCTOT).toFixed(2) : '0.00'
  document.getElementById("input_add_add_noPPL").value = tempAddEdit.NoPPL
  document.getElementById("input_add_add_urutPPL").value = tempAddEdit.UrutPPL

  $.ajax({
    url: "{!! url('pocekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang : tempAddAdd.KodeBrg
    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        let date1 = ""
        if (item.TANGGAL) {
            let date = new Date(item.TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }
        rowTable += `
        <tr>
          <td>${item.NamaCustSupp}</td>
          <td>${date1}</td>
          <td>${item.QNT}</td>
          <td>${item.SATUAN}</td>
          <td>${item.KODEVLS}</td>
          <td>${item.KURS}</td>
          <td class="text-right">${Number(item.HARGA) ? parseFloat(item.HARGA).toFixed(2) : '0.00'}</td>
          <td>${item.DISCRP}</td>
          <td class="text-right">${Number(item.HrgNetto) ? parseFloat(item.HrgNetto).toFixed(2) : '0.00'}</td>
        </tr>`
      });

      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=9>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      document.getElementById("input_add_add_kodebarang").scrollIntoView();

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

  $('#divhargaterakhir').hide();
  $('#divStockProyeksi').hide();
  $('#h4AddAddItem').hide();
  $('#h4AddEditItem').show();
  $('#submitAddAdd').hide();
  $('#submitAddEdit').show();
  $('#addAddItem').show();

  document.getElementById("input_add_add_namabarang").scrollIntoView();
}

function closeShowHideAdd () {
  $('.showhide').hide();

}

function setNewNoBukti () {
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('spnobukti') !!}",
    type: "post",
    async: false,
    data: {
      kode:'TRS',
      _token
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

      dataNoBuktiSatuan = res[0].Nobukti
      dataNoUrutSatuan = res[0].Nourut

    }})
}


function buttonAddListPIC () {

  let _token = $("#_token").val();
  let kodecustsupp = $("#input_add_kodesupplier").val();

  if (!kodecustsupp) {
    alertify.warning("Isi pelanggan terlebih dahulu")
    return
  }

  $.ajax({
    url: "{!! url('solistpic') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodecustsupp
    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.kodepic}</td>
        <td>${item.nama}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickPIC('${item.kodepic}' , '${item.nama}')" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });

      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_pic").innerHTML = rowTable

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListPIC').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddAddListPWO () {

  let _token = $("#_token").val();
  let noSo = $("#input_add_noso").val();

  if (!noSo) {
    alertify.warning("Isi Nomor SO terlebih dahulu")
    return
  }

  $.ajax({
    url: "{!! url('polistpwo') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      noSo
    },
    success: function(res) {
      let rowTable = `
        <tr>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickPWO('-' , '-')" type="button" ><i class="bi bi-plus"></i></button></td>
        </tr>`
      res.forEach((item, i) => {
        rowTable += `
        <tr>
          <td>${item.no_bukti}</td>
          <td>${item.tanggal}</td>
          <td>${item.supplier}</td>
          <td>${item.kode}</td>
          <td>${item.NAMABRG}</td>
          <td>${item.qty}</td>
          <td>${item.satuan}</td>
          <td>${item.harga}</td>
          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickPWO('${item.no_bukti}' , '${item.tanggal}')" type="button" ><i class="bi bi-plus"></i></button></td>
        </tr>`
      });

      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=9>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_pwo").innerHTML = rowTable

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListPWO').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function buttonAddAddListBarang () {

  // let _token = $("#_token").val();
  let foc = $("#input_add_add_foc").val();
  let noSo = $("#input_add_noso").val();
  
  if (!noSo) {
    alertify.warning("Isi Nomor SO terlebih dahulu")
    return
  }

  if (foc == 0 & noSo == '-') {

    $('#tabel_add_list_barang_nonfoc').DataTable().destroy();

    $.ajax({
      url: "{!! url('polistbarangnosominus') !!}",
      type: "get",
      async: false,
      data: {
      },
      success: function(res) {
        let rowTable = ``
        dataAddAddListItem = res
        dataAddAddListItem.forEach((item, i) => {
          rowTable += `
          <tr>
            <td style="white-space:nowrap;">${item.KodeBrg}</td>
            <td style="white-space:nowrap;">${item.NamaBrg}</td>
            <td style="white-space:nowrap;">${item.PartNumber}</td>
            <td style="white-space:nowrap;">${item.NAMAMERK ? item.NAMAMERK : ''}</td>
            <td style="white-space:nowrap;">${item.Sat}</td>
            <td style="white-space:nowrap;">${item.Qnt}</td>
            <td style="white-space:nowrap;">${item.QntPO}</td>
            <td style="white-space:nowrap;">${item.SisaPPL}</td>
            <td style="white-space:nowrap;">${item.NoBukti}</td>
            <td style="white-space:nowrap;">${item.NosoCust}</td>
            <td style="white-space:nowrap;" class="text-center">
              <button class="btn btn-primary btn-sm" onclick="buttonAddAddPickBarangNonFOC(${i})" type="button" ><i class="bi bi-plus"></i>
              </button>
            </td>
          </tr>`
        });

        if(!res.length) {
          rowTable= ``
        }
        document.getElementById("tabel_data_add_list_barang_nonfoc").innerHTML = rowTable

        $("#tabel_add_list_barang_nonfoc").DataTable({
          "lengthChange": false,
            "paging": false ,
        });

        $('.showhidemodalbodyadd').hide();
        $('#modalBodyAddAddListBarangNonFOC').show();

        $("#form").modal('toggle')

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })
  } else if (foc == 1) {
    console.log(foc + "- FOC")

    $('#tabel_add_list_barang_foc').DataTable().destroy();

    $.ajax({
      url: "{!! url('polistbarangfoc') !!}",
      type: "get",
      async: false,
      data: {
      },
      success: function(res) {
        let rowTable = ``
        dataAddAddListItem = res
        dataAddAddListItem.forEach((item, i) => {
          rowTable += `
          <tr>
            <td style="white-space:nowrap;">${item.Kodebrg}</td>
            <td style="white-space:nowrap;">${item.NamaBrg}</td>
            <td style="white-space:nowrap;">${item.partNumber}</td>
            <td style="white-space:nowrap;">${item.NamaMerk}</td>
            <td style="white-space:nowrap;" class="text-center">
              <button class="btn btn-primary btn-sm" onclick="buttonAddAddPickBarangFOCPlus(${i})" type="button" ><i class="bi bi-plus"></i></button>
            </td>
          </tr>`
        });

        if(!res.length) {
          rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
        }
        document.getElementById("tabel_data_add_list_barang_foc").innerHTML = rowTable

        $("#tabel_add_list_barang_foc").DataTable({
          "lengthChange": false,
            "paging": true ,
        });

        $('.showhidemodalbodyadd').hide();
        $('#modalBodyAddAddListBarangFOC').show();

        $("#form").modal('toggle')

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })
  } else {
    console.log(foc + " - FOC " +" //// "+ noSo + " - NOSO")

    $('#tabel_add_list_barang_nonfocplus').DataTable().destroy();

    $.ajax({
      url: "{!! url('polistbarangnosoplus') !!}",
      type: "get",
      async: false,
      data: {
        noSo
      },
      success: function(res) {
        let rowTable = ``
        dataAddAddListItem = res
        dataAddAddListItem.forEach((item, i) => {
          rowTable += `
          <tr>
            <td>${item.KodeBrg}</td>
            <td style="white-space:nowrap;">${item.NamaBrg}</td>
            <td>${item.Qnt}</td>
            <td>${item.Qnt2}</td>
            <td>${item.Sat}</td>
            <td>${item.SisaPPL}</td>
            <td>${item.Sisa2PPL}</td>
            <td>${item.NoBukti}</td>
            <td>${item.PartNumber}</td>
            <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddAddPickBarangNonFOCPlus(${i})" type="button" ><i class="bi bi-plus"></i></button></td>

          </tr>`
        });

        if(!res.length) {
          rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
        }
        document.getElementById("tabel_data_add_list_barang_nonfocplus").innerHTML = rowTable

        $("#tabel_add_list_barang_nonfocplus").DataTable({
          "lengthChange": false,
            "paging": true ,
        });

        $('.showhidemodalbodyadd').hide();
        $('#modalBodyAddAddListBarangNonFOCPlus').show();

        $("#form").modal('toggle')

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })
  }
}

function buttonAddListNoSO () {

  let _token = $("#_token").val();

  $('#tabel_add_list_noSo').DataTable().destroy();
  $.ajax({
    url: "{!! url('polistnoso') !!}",
    type: "post",
    async: false,
    data: {
      _token
    },
    success: function(res) {
      let rowTable = `
        <tr>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td class="text-center"><button class="btn btn-primary btn-sm" style="margin-top:5px; margin-bottom:5px;" onclick="buttonAddPickNoSO('-' , '-')" type="button" ><i class="bi bi-plus"></i></button></td>
        </tr>`

      listNoSo = res

      listNoSo.forEach((item, i) => {
        rowTable += `
        <tr>
          <td>${item.NOBUKTI}</td>
          <td>${item.Tanggal}</td>
          <td>${item.NoPesanan}</td>
          <td class="text-center"><button class="btn btn-primary btn-sm" style="margin-top:5px; margin-bottom:5px;" onclick="buttonAddPickNoSO('${item.NOBUKTI}' , '${item.NoPesanan}')" type="button" ><i class="bi bi-plus"></i></button></td>
        </tr>`
      });

      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=4>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_noSo").innerHTML = rowTable
      $("#tabel_add_list_noSo").DataTable({
        "lengthChange": false,
        "paging": true,
      });

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListNoSo').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function buttonAddListGudang () {

  let _token = $("#_token").val();

  $('#tabel_add_list_alamatkirim').DataTable().destroy();
  $.ajax({
    url: "{!! url('polistgudang') !!}",
    type: "post",
    async: false,
    data: {
      _token
    },
    success: function(res) {
      let rowTable = ``

      listAlamatKirim = res

      listAlamatKirim.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.KODEGDG}</td>
        <td>${item.NAMA}</td>
        <td>${item.Alamat}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" style="margin-top:5px; margin-bottom:5px;" onclick="buttonAddPickAlamatKirim(${i} )" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`

        // '
        // <tr>
        // <td> '+ item.nomor + '</td>
        // <td> '+ item.nama + '</td>
        // <td>+ ' + item.alamat + '</td>
        // <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickAlamatKirim( `' + item.nomor + '` , `'+ item.nama + '` , `' + item.alamat +'` )" type="button" ><i class="bi bi-plus"></i></button></td>
        //
        // </tr>'
      });


      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=4>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_alamatkirim").innerHTML = rowTable
      $("#tabel_add_list_alamatkirim").DataTable({
        "lengthChange": false,
        "paging": true,
      });

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListAlamatKirim').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}


function buttonAddListLokasiPenerima () {

  let _token = $("#_token").val();

  $('#tabel_add_list_lokasipenerima').DataTable().destroy();

  $.ajax({
    url: "{!! url('polistlokasipenerima') !!}",
    type: "post",
    async: false,
    data: {
      _token
    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.KodeCustsupp}</td>
        <td>${item.NamaCust}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickLokasiPenerima('${item.KodeCustsupp}' , '${item.NamaCust}' )" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_lokasipenerima").innerHTML = rowTable
      $("#tabel_add_list_lokasipenerima").DataTable({
        "lengthChange": false,
        "paging": true,
      });

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListLokasiPenerima').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddListValas () {
  
  $('#tabel_add_list_valas').DataTable().destroy();
  $.ajax({
    url: "{!! url('polistvalas') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.kodevls}</td>
        <td>${item.namavls}</td>
        <td>${item.kurs ? parseFloat(item.kurs).toFixed(2) : '0.00'}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickValas('${item.kodevls}' , '${item.kurs ? parseFloat(item.kurs).toFixed(2) : '0.00'}' )" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=4>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_valas").innerHTML = rowTable
      $("#tabel_add_list_valas").DataTable({
        "lengthChange": false,
        "paging": true,
      });

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListValas').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddListPelanggan () 
{
  $('#tabel_add_list_pelanggan').DataTable().destroy();
  $.ajax({
    url: "{!! url('polistpelanggan') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.KodeCustSupp}</td>
        <td>${item.NamaCustSupp}</td>
        <td>${item.Alamat}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" style="margin-top:10px;" onclick="buttonAddPickPelanggan('${item.KodeCustSupp}' , '${item.NamaCustSupp}' , '${item.Alamat}')" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });

      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=4>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_pelanggan").innerHTML = rowTable
      $("#tabel_add_list_pelanggan").DataTable({
        "lengthChange": false,
          "paging": true ,
      });

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListPelanggan').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function buttonAddListBackOffice () {
  $.ajax({
    url: "{!! url('solistbackoffice') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.keynik}</td>
        <td>${item.fullname}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickBackOffice('${item.keynik}' , '${item.fullname}')" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_backoffice").innerHTML = rowTable

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListBackOffice').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddListSales () {
  $('#tabel_add_list_sales').DataTable().destroy();
  $.ajax({
    url: "{!! url('solistsales') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        console.log(item.keynik)
        console.log(item.nama)
        rowTable += `
        <tr>
        <td>${item.keynik}</td>
        <td>${item.nama}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickSales('${item.keynik}' , '${String(item.nama)}')" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });


      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_sales").innerHTML = rowTable
      $("#tabel_add_list_sales").DataTable({
        "lengthChange": false,
          "paging": false ,
    });
      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListSales').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function onChangeInputAddAddNosat () {
  console.log('onChangeInputAddAddNosat')
  let _token  = $("#_token").val()
  let nosat = $("#input_add_add_nosat").val()
  console.log(nosat)
  console.log(Number(nosat))
  let kodebarang = $("#input_add_add_kodebarang").val()

  if (!kodebarang) {
    return
  }

  $.ajax({
    url: "{!! url('socekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang ,
      nosat
    },
    success: function(res) {
      console.log(res)

      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.TANGGAL}</td>
        <td>-</td>
        <td>${item.SATUAN}</td>
        <td>-</td>
        <td>-</td>
        <td>${item.Xharga}</td>
        <td>-</td>
        <td>-</td>

        </tr>`
      });


      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=8>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      // let rowTable = ``
      // res.forEach((item, i) => {
      //   rowTable += `
      //   <tr>
      //   <td>${item.keynik}</td>
      //   <td>${item.nama}</td>
      //   <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickSales('${item.keynik}' , '${item.nama}')" type="button" ><i class="bi bi-plus"></i></button></td>
      //
      //   </tr>`
      // });
      //
      //
      //
      //
      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      // }
      // document.getElementById("tabel_data_add_list_sales").innerHTML = rowTable

      if (tipeformitem == 'add') {
        console.log(tempAddAdd[`Hrg${nosat}_1`])
        if (res.length && res[0].Xharga) {
          console.log('if1')
          document.getElementById("input_add_add_harga").value = res[0].Xharga
        } else {
          console.log('else1')
          if (tempAddAdd[`Hrg${nosat}_1`]) {
            console.log('if2')
            document.getElementById("input_add_add_harga").value = tempAddAdd[`Hrg${nosat}_1`]
          } else {
            console.log('else2')
            document.getElementById("input_add_add_harga").value = '0.00'
          }
        }
      } else {

      }

      // if (res.length && res[0].Xharga) {
      //   document.getElementById("input_add_add_harga").value = res[0].Xharga
      // } else {
      //   if ( nosat == 1) {
      //     if (tempAddAdd.Hrg1_1) {
      //       document.getElementById("input_add_add_harga").value = tempAddAdd.Hrg1_1
      //     } else {
      //       document.getElementById("input_add_add_harga").value = '0.00'
      //     }
      //   }
      //
      //   if ( nosat == 2) {
      //     if (tempAddAdd.Hrg2_1) {
      //       document.getElementById("input_add_add_harga").value = tempAddAdd.Hrg2_1
      //     } else {
      //       document.getElementById("input_add_add_harga").value = '0.00'
      //     }
      //   }
      //
      //   if ( nosat == 3) {
      //     if (tempAddAdd.Hrg3_1) {
      //       document.getElementById("input_add_add_harga").value = tempAddAdd.Hrg3_1
      //     } else {
      //       document.getElementById("input_add_add_harga").value = '0.00'
      //     }
      //   }
      //
      // }


    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

/* ============ Header tabel interaktif (window.ReportTable) ============
 * Port dari engine so.blade.php sendiri (soCart/soAktifkanTabel/soInitReportTableSekali,
 * itu sendiri port dari purchaseOrder.blade.php) -- lihat docs/marketing-full-menu-guide.md.
 * 3 tabel independen: #tabel (Permintaan Transfer Barang, dari dbPRTransfer -- domain
 * berbeda dari dua lainnya), #tabel2 (Transaksi Transfer Barang -- gabungan Non-Otorisasi
 * + Otorisasi, dipilah client-side lewat dropdown #tbFilterOtorisasi/NeedOtorisasi) dan
 * #tabel3 (Outstanding Transfer -- data SAMA dengan #tabel2, difilter IsTerima=0).
 */
let tbCart = { 1 : [], 2 : [], 3 : [] } // 1 = #tabel (Permintaan), 2 = #tabel2 (Transaksi), 3 = #tabel3 (Outstanding)
let tbActiveUrut = 0
const TB_HREF = 'gudangtransferbarang'
const TB_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date', 3 : 'bool' }
const TB_TIPE_KODE = { varchar : 0, float : 1, date : 2, bool : 3 }

var lastTabelRows = []       // #tabel  -- listPermintaan
var lastTabel2Rows = []      // #tabel2 -- listTransfer, difilter status Otorisasi
var lastTabel3Rows = []      // #tabel3 -- listTransfer, difilter IsTerima=0
var tbRawListTransfer = []   // listTransfer APA ADANYA dari server (sebelum difilter client-side)

// Tabel yang tabnya sedang tidak aktif tetap ditandai di sini, baru benar-benar digambar
// ulang saat tabnya dibuka (lihat handler shown.bs.tab di bawah) -- port dari
// soPerluGambar/poPerluGambar milik so.blade.php/purchaseOrder.blade.php.
var tbPerluGambar = { tabel: false, tabel2: false, tabel3: false }

function activeVisibleTabKey() {
  if ($('#home').hasClass('active')) { return 'tabel' }
  if ($('#profile').hasClass('active')) { return 'tabel2' }
  return 'tabel3'
}

function tbUrutDariKey(key) {
  return key === 'tabel' ? 1 : (key === 'tabel2' ? 2 : 3)
}

// Dua sumber data di halaman ini (dbPRTransfer utk #tabel, fetchHeaderList() utk
// #tabel2/#tabel3) punya casing field yang beda (nobukti vs NOBUKTI, Tanggal vs
// TANGGAL) -- cari case-insensitive, port dari soPickCI() milik so.blade.php.
function tbPickCI(row, key) {
  if (!row) { return undefined }
  if (row[key] !== undefined) { return row[key] }
  let lower = key.toLowerCase()
  for (let k in row) {
    if (k.toLowerCase() === lower) { return row[k] }
  }
  return undefined
}

// Default kolom tiap tabel -- dipakai saat kunjungan pertama (belum ada yang tersimpan
// di DBHEADERTABLE) maupun saat tombol "Reset kolom" diklik. Port dari soDefaultCart().
function tbDefaultCart(urut) {
  if (urut === 1) {
    return [
      ['nobukti',      'No. Bukti',      1, 'varchar', 0, 0],
      ['Tanggal',      'Tanggal',        1, 'date',    0, 0],
      ['Keterangan',   'Keterangan',     1, 'varchar', 0, 0],
      ['NoPenyerahan', 'No. Penyerahan', 1, 'varchar', 0, 0]
    ]
  }
  if (urut === 3) {
    return [
      ['NOBUKTI',       'No. Bukti',     1, 'varchar', 0, 0],
      ['TANGGAL',       'Tanggal',       1, 'date',    0, 0],
      ['NOTE',          'Keterangan',    1, 'varchar', 0, 0],
      ['NamagdgAsal',   'Gudang Asal',   1, 'varchar', 0, 0],
      ['NamagdgTujuan', 'Gudang Tujuan', 1, 'varchar', 0, 0],
      ['JmlItem',       'Jml Item',      1, 'float',   0, 0]
    ]
  }
  return [
    ['NOBUKTI',       'No. Bukti',     1, 'varchar', 0, 0],
    ['TANGGAL',       'Tanggal',       1, 'date',    0, 0],
    ['NOTE',          'Keterangan',    1, 'varchar', 0, 0],
    ['NamagdgAsal',   'Gudang Asal',   1, 'varchar', 0, 0],
    ['NamagdgTujuan', 'Gudang Tujuan', 1, 'varchar', 0, 0],
    ['JmlItem',       'Jml Item',      1, 'float',   0, 0],
    ['OtoUser1',      'User Oto',      1, 'varchar', 0, 0],
    ['TglOto1',       'Tgl Oto',       1, 'date',    0, 0]
  ]
}

// Port dari soBuatCart().
function tbBuatCart (headers, values, isnumerics, isshowns, desimals) {
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
      TB_TIPE_NAMA[tipe] || 'varchar',
      0,
      isNaN(des) ? 0 : des
    ])
  });
  return cart
}

// Arahkan komponen ke tabel tertentu. Port dari soAktifkanTabel().
function tbAktifkanTabel (urut) {
  tbActiveUrut = urut
  window.g_modeReport = urut
  window.gcart_header = tbCart[urut]
}

// Dipanggil report-table.js sendiri tiap kali kolom digeser/disembunyikan/diubah
// desimalnya. Port dari soOnChangeAktif().
function tbOnChangeAktif () {
  if (tbActiveUrut === 1) { reinitTabel() }
  else if (tbActiveUrut === 2) { reinitTabel2() }
  else { reinitTabel3() }
}

window.g_href = TB_HREF
window.g_modeReport = 1
window.gcart_header = []

// Port dari doSimpanHeader() milik so.blade.php.
window.doSimpanHeader = function (href, mode) {
  let urut = mode || 1
  let cart = tbCart[urut] || []

  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[1])
    value.push(c[0])
    isnumber.push(TB_TIPE_KODE[c[3]] ?? 0)
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
      href     : TB_HREF,
      urut     : urut
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal menyimpan pengaturan kolom')
    }
  })
}

// Port dari doSetHeader() milik so.blade.php -- getheadertable tidak punya pembuat
// default sisi server untuk href ini, jadi jatuh ke tbDefaultCart() kalau kosong.
window.doSetHeader = function (mode, reset) {
  let urut = mode || 1

  $.ajax({
    url   : "{!! url('getheadertable') !!}",
    type  : "post",
    async : false,
    data  : {
      _token : $("#_token").val(),
      href   : TB_HREF,
      urut   : urut,
      reset  : reset ? 1 : 0
    },
    success : function (res) {
      if (!reset && res && res.headertableheader && res.headertableheader.length) {
        let header = res.headertableheader
        let value = res.headertablevalue
        let isnumeric = res.isnumeric
        let isshown = res.isshown
        let tipe = res.desimal || []
        tbCart[urut] = tbBuatCart(header, value, isnumeric, isshown, tipe)
      } else {
        tbCart[urut] = tbDefaultCart(urut)
        window.gcart_header = tbCart[urut]
        window.doSimpanHeader(TB_HREF, urut)
      }
      window.gcart_header = tbCart[urut]
    },
    error : function (err) {
      console.log(err)
      alertify.warning(reset ? 'Gagal mengembalikan kolom ke tampilan default' : 'Gagal memuat pengaturan kolom')
      tbCart[urut] = tbDefaultCart(urut)
      window.gcart_header = tbCart[urut]
    }
  })
}

const TB_SELEKTOR_TABEL_AKTIF = '#myTabContent .tab-pane.active table.data-table'
const TB_SELEKTOR_BAR_AKTIF = '#myTabContent .tab-pane.active [id^="rtBarTabel"]'

// Ikat handler drag & roda gigi ke <thead> TEPAT SEKALI seumur halaman. Port 1:1 dari
// soInitReportTableSekali().
let tbRtSudahInit = false
function tbInitReportTableSekali () {
  if (tbRtSudahInit || typeof ReportTable === 'undefined') { return }
  tbRtSudahInit = true

  let urutAktif = tbUrutDariKey(activeVisibleTabKey())
  let idTabel = { 1: '#tabel', 2: '#tabel2', 3: '#tabel3' }
  let idBar = { 1: '#rtBarTabel', 2: '#rtBarTabel2', 3: '#rtBarTabel3' }
  Object.keys(idTabel).forEach((u) => {
    if (Number(u) === urutAktif) { return }
    ReportTable.init({ table: idTabel[u], bar: idBar[u], onChange: tbOnChangeAktif })
  });

  ReportTable.init({
    table    : TB_SELEKTOR_TABEL_AKTIF,
    bar      : TB_SELEKTOR_BAR_AKTIF,
    onChange : tbOnChangeAktif
  })

  let tbGuardUlangKlik = false;
  ['#tabel', '#tabel2', '#tabel3'].forEach((sel) => {
    let thead = document.querySelector(sel + ' thead')
    if (!thead) { return }
    thead.addEventListener('click', function (e) {
      if (tbGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }

      e.stopPropagation()
      e.preventDefault()

      tbGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles: false, cancelable: true, view: window })
      Object.defineProperty(ulang, 'target', { value: interaktif, configurable: true })
      thead.dispatchEvent(ulang)
      tbGuardUlangKlik = false
    }, true)
  });
}

function tbFormatDate (raw) {
  if (!raw) { return '' }
  let d = new Date(raw)
  if (isNaN(d.getTime())) { return raw }
  let dd = ('0' + d.getDate()).slice(-2)
  let mm = ('0' + (d.getMonth() + 1)).slice(-2)
  return dd + '/' + mm + '/' + d.getFullYear()
}

function tulisTheadHeader (sel, cols, withActions) {
  if (withActions === undefined) { withActions = true }
  let thead = document.querySelector(sel + ' thead')
  if (!thead) { return }
  let cols2 = cols.filter(function (c) { return c[2] === 1 })
  let headRowHtml = ReportTable.headHtml(cols2)
  if (withActions) {
    headRowHtml = headRowHtml.replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>')
  }
  thead.setAttribute('style', 'white-space:nowrap;')
  thead.innerHTML = headRowHtml
}

// col[5] (jumlah desimal) bisa diubah pengguna lewat roda gigi, jadi dibaca live dari
// cart, bukan diasumsikan tetap. Port dari tabelValueCell() milik so.blade.php.
function tbValueCell(row, col) {
  let raw = tbPickCI(row, col[0])
  let type = col[3]

  if (type === 'date') {
    if (!raw) { return '<td></td>' }
    return '<td>' + tbFormatDate(raw) + '</td>'
  }
  if (type === 'float') {
    let dp = Number(col[5]) || 0
    let n = (raw !== undefined && raw !== null && raw !== '') ? Number(raw) : 0
    return '<td style="text-align:right;">' + n.toLocaleString('id-ID', { minimumFractionDigits: dp, maximumFractionDigits: dp }) + '</td>'
  }
  if (type === 'bool') {
    return Number(raw)
      ? '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>'
      : '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>'
  }
  return '<td>' + (raw !== undefined && raw !== null ? raw : '') + '</td>'
}

// Markup tombol flat, tanpa div pembungkus -- persis pola purchaseOrder.blade.php
// (poRenderTabelPO()/tombolAksiPO(): <td class="text-center" style=''>${tombol}</td>,
// tiap <button> langsung bertetangga, memakai data-toggle="tooltip" bukan title-hover
// bawaan browser). #tabel/#tabel2 di-init dengan tooltip Bootstrap lewat
// reinitTabel()/reinitTabel2() di bawah, sama seperti so.blade.php melakukannya.
function tbTabelActionsCell (row) {
  let nobukti = tbPickCI(row, 'nobukti')
  return '<td class="text-center" style="white-space:nowrap;">'
    + '<button class="btn btn-warning btn-sm" type="button" data-toggle="tooltip" title="Detail" onclick="buttonDetailAdd(\'' + nobukti + '\')"><i class="bi bi-info"></i></button>'
    + '<button class="btn btn-success btn-sm" type="button" data-toggle="tooltip" title="Add" onclick="buttonAdd(\'' + nobukti + '\')"><i class="bi bi-plus-lg"></i></button>'
    + '</td>'
}

// Gabungan tombol lama tabel2 (Non-Otorisasi: Detail/Edit/Hapus/Otorisasi) dan tabel4
// (Otorisasi: Detail/BatalOtorisasi/Print) -- dipilih berdasar IsOtorisasi1 tiap baris,
// bukan lagi berdasar tab mana yang sedang dibuka. Warna/ikon Otorisasi (btn-info) dan
// Batal Otorisasi/Print (btn-danger/btn-primary) disamakan persis dengan tombolAksiPO()
// milik purchaseOrder.blade.php; Hapus (btn-danger) tetap ada karena itu kebutuhan
// bisnis transferbarang sendiri, PO tidak punya delete di tab ini.
function tbTabel2ActionsCell (row) {
  let nobukti = tbPickCI(row, 'NOBUKTI')
  let html = '<td class="text-center" style="white-space:nowrap;">'
  html += '<button class="btn btn-warning btn-sm" type="button" data-toggle="tooltip" title="Detail" onclick="buttonDetail(\'' + nobukti + '\')"><i class="bi bi-info"></i></button>'
  if (Number(tbPickCI(row, 'IsOtorisasi1')) === 0) {
    html += '<button class="btn btn-info btn-sm" type="button" data-toggle="tooltip" title="Otorisasi" onclick="buttonOtorisasi(\'' + nobukti + '\')"><i class="bi bi-key"></i></button>'
    html += '<button class="btn btn-success btn-sm" type="button" data-toggle="tooltip" title="Edit" onclick="buttonEdit(\'' + nobukti + '\')"><i class="bi bi-pen"></i></button>'
    html += '<button class="btn btn-danger btn-sm" type="button" data-toggle="tooltip" title="Hapus" onclick="deleteTransferData(\'' + nobukti + '\')"><i class="bi bi-trash"></i></button>'
  } else {
    html += '<button class="btn btn-danger btn-sm" type="button" data-toggle="tooltip" title="Batal Otorisasi" onclick="buttonBatalOtorisasi(\'' + nobukti + '\')"><i class="bi bi-key"></i></button>'
    html += '<button class="btn btn-primary btn-sm" type="button" data-toggle="tooltip" title="Print" onclick="submitPrint(\'' + nobukti + '\')"><i class="bi bi-printer"></i></button>'
  }
  html += '</td>'
  return html
}

function renderTabelRows (rows) {
  if (tbActiveUrut !== 1) { tbAktifkanTabel(1) }
  let cols = gcart_header || []
  let colsTampil = cols.filter(function (c) { return c[2] === 1 })
  let html = ""
  ;(rows || []).forEach(function (row) {
    html += '<tr>' + tbTabelActionsCell(row)
    colsTampil.forEach(function (col) { html += tbValueCell(row, col) })
    html += '</tr>'
  });
  document.getElementById('tabel_data').innerHTML = html
  tulisTheadHeader('#tabel', cols, true)
}

function renderTabel2Rows (rows) {
  if (tbActiveUrut !== 2) { tbAktifkanTabel(2) }
  let cols = gcart_header || []
  let colsTampil = cols.filter(function (c) { return c[2] === 1 })
  let html = ""
  ;(rows || []).forEach(function (row) {
    html += '<tr>' + tbTabel2ActionsCell(row)
    colsTampil.forEach(function (col) { html += tbValueCell(row, col) })
    html += '</tr>'
  });
  document.getElementById('tabel2_data').innerHTML = html
  tulisTheadHeader('#tabel2', cols, true)
}

function renderTabel3Rows (rows) {
  if (tbActiveUrut !== 3) { tbAktifkanTabel(3) }
  let cols = gcart_header || []
  let colsTampil = cols.filter(function (c) { return c[2] === 1 })
  let html = ""
  ;(rows || []).forEach(function (row) {
    html += '<tr>'
    colsTampil.forEach(function (col) { html += tbValueCell(row, col) })
    html += '</tr>'
  });
  document.getElementById('tabel3_data').innerHTML = html
  tulisTheadHeader('#tabel3', cols, false)
}

const TB_DOM_STRING = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
let tbPanjangHalaman = { 1: 10, 2: 10, 3: 10 }

function tbIkatSearch (urut) {
  let inputId = { 1: 'tbSearch1', 2: 'tbSearch2', 3: 'tbSearch3' }[urut]
  let idTabel = { 1: 'tabel', 2: 'tabel2', 3: 'tabel3' }[urut]
  let input = document.getElementById(inputId)
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

function tbIkatPanjangHalaman (urut) {
  let selId = { 1: 'tbLen1', 2: 'tbLen2', 3: 'tbLen3' }[urut]
  let idTabel = { 1: 'tabel', 2: 'tabel2', 3: 'tabel3' }[urut]
  let sel = document.getElementById(selId)
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(tbPanjangHalaman[urut])

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    tbPanjangHalaman[urut] = (n === -1 || n > 0) ? n : 10
    if ($.fn.DataTable.isDataTable('#' + idTabel)) {
      $('#' + idTabel).DataTable().page.len(tbPanjangHalaman[urut]).draw()
    }
  })
}

function reinitTabel () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().destroy() }
    renderTabelRows(lastTabelRows)
    $('#tabel').DataTable({
      dom: TB_DOM_STRING,
      lengthChange: false,
      pageLength: tbPanjangHalaman[1],
      paging: true,
      ordering: false
    });
    tbIkatSearch(1)
    tbIkatPanjangHalaman(1)
    $('#tabel_data [data-toggle="tooltip"]').tooltip({ container: 'body', boundary: 'window' })
    tbPerluGambar.tabel = false
  } catch (e) {
    console.error('reinitTabel failed:', e)
    alertify.error('Gagal memperbarui tabel: ' + e.message)
  }
}

function reinitTabel2 () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel2')) { $('#tabel2').DataTable().destroy() }
    renderTabel2Rows(lastTabel2Rows)
    $('#tabel2').DataTable({
      dom: TB_DOM_STRING,
      lengthChange: false,
      pageLength: tbPanjangHalaman[2],
      paging: true,
      ordering: false
    });
    tbIkatSearch(2)
    tbIkatPanjangHalaman(2)
    $('#tabel2_data [data-toggle="tooltip"]').tooltip({ container: 'body', boundary: 'window' })
    tbPerluGambar.tabel2 = false
  } catch (e) {
    console.error('reinitTabel2 failed:', e)
    alertify.error('Gagal memperbarui tabel: ' + e.message)
  }
}

function reinitTabel3 () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel3')) { $('#tabel3').DataTable().destroy() }
    renderTabel3Rows(lastTabel3Rows)
    $('#tabel3').DataTable({
      dom: TB_DOM_STRING,
      lengthChange: false,
      pageLength: tbPanjangHalaman[3],
      paging: true,
      ordering: false
    });
    tbIkatSearch(3)
    tbIkatPanjangHalaman(3)
    tbPerluGambar.tabel3 = false
  } catch (e) {
    console.error('reinitTabel3 failed:', e)
    alertify.error('Gagal memperbarui tabel: ' + e.message)
  }
}

// #tabel2 (Transaksi Transfer Barang) & #tabel3 (Outstanding) berbagi SATU sumber data
// (tbRawListTransfer, dari fetchHeaderList()/trfbrgloadall) -- dipilah lagi di client
// lewat dua fungsi ini, bukan dua panggilan server terpisah.
function tbFilterListTransferOtorisasi (list) {
  let sel = document.getElementById('tbFilterOtorisasi')
  let val = sel ? sel.value : ''
  if (val === '') { return list || [] }
  return (list || []).filter(function (r) { return String(Number(tbPickCI(r, 'NeedOtorisasi'))) === val })
}

function tbFilterListTransferOutstanding (list) {
  return (list || []).filter(function (r) { return Number(tbPickCI(r, 'IsTerima')) === 0 })
}

// Dipanggil dropdown #tbFilterOtorisasi onchange -- filter ulang di client saja, tidak
// perlu memanggil server lagi.
function tbTerapkanFilterOtorisasi () {
  lastTabel2Rows = tbFilterListTransferOtorisasi(tbRawListTransfer)
  if (activeVisibleTabKey() === 'tabel2') { reinitTabel2() } else { tbPerluGambar.tabel2 = true }
}

// Dipanggil onchange periode (#tbTanggalAwal/#tbTanggalAkhir) -- periode ini yang
// mendorong ulang fetchHeaderList() lewat trfbrgloadall, dipakai bersama oleh #tabel2
// dan #tabel3 (port dari pola periode-SO-tab-only milik so.blade.php).
function onChangePeriodeTransfer () {
  loadAll()
}

function loadAll () {
  let date1 = document.getElementById('tbTanggalAwal') ? document.getElementById('tbTanggalAwal').value : ''
  let date2 = document.getElementById('tbTanggalAkhir') ? document.getElementById('tbTanggalAkhir').value : ''

  $.ajax({
    url: "{!! url('trfbrgloadall') !!}",
    type: "get",
    async: false,
    data: { date1: date1, date2: date2 },
    success: function (res) {
      lastTabelRows = res.listPermintaan || []
      tbRawListTransfer = res.listTransfer || []
    },
    error: function (err) {
      console.log(err)
      alertify.error('Gagal memuat data transfer barang')
    }
  })

  lastTabel2Rows = tbFilterListTransferOtorisasi(tbRawListTransfer)
  lastTabel3Rows = tbFilterListTransferOutstanding(tbRawListTransfer)

  let visibleKey = activeVisibleTabKey()
  tbPerluGambar = { tabel: visibleKey !== 'tabel', tabel2: visibleKey !== 'tabel2', tabel3: visibleKey !== 'tabel3' }
  if (visibleKey === 'tabel') { reinitTabel() }
  else if (visibleKey === 'tabel2') { reinitTabel2() }
  else { reinitTabel3() }
}

$(function () {
  if (document.getElementById('breadcrumb')) { document.getElementById('breadcrumb').innerHTML = 'Transfer Barang' }

  window.doSetHeader(1, false)
  window.doSetHeader(2, false)
  window.doSetHeader(3, false)

  loadAll()

  tbInitReportTableSekali()

  $('#nav-home-tab').on('shown.bs.tab', function () {
    tbAktifkanTabel(1)
    if (typeof ReportTable !== 'undefined') { ReportTable.refresh() }
    if (tbPerluGambar.tabel) { reinitTabel() }
  })
  $('#nav-profile-tab').on('shown.bs.tab', function () {
    tbAktifkanTabel(2)
    if (typeof ReportTable !== 'undefined') { ReportTable.refresh() }
    if (tbPerluGambar.tabel2) { reinitTabel2() }
  })
  $('#nav-profile1-tab').on('shown.bs.tab', function () {
    tbAktifkanTabel(3)
    if (typeof ReportTable !== 'undefined') { ReportTable.refresh() }
    if (tbPerluGambar.tabel3) { reinitTabel3() }
  })
})

function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: "{!! url('trfbrgdetailCetak') !!}",
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
                  <div class="pb-1" style="width: 100%">No Permintaan : ${dataPrint[0].NOMINTA ?? '-'}</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">Keterangan : `+dataPrint[0].NOTE+`</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
              </div>


              <div style="width: 38%">
                <div style="display: flex; width: 100%">
                  <h2 class="m-0 pb-2"SURAT JALAN TRANSFER BARANG</h2>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">No Bukti</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+dataPrint[0].Nobukti+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Tanggal</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+tanggalOnly+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Gudang Asal</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+dataPrint[0].GDGASAL+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Gudang Tujuan</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+dataPrint[0].GDGTUJUAN+`</div>
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
                    <td class="text-center" style="width: 20%">QNT</td>
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
               style="width: 30%;  ">${itemSub.KODEBRG}</td>
         <td class="text-align: left"
               style="width: 50%;">${itemSub.NAMABRG}</td>
         <td class="text-align: text-right"
               style="width: 20%;  "> ${itemSub.QNT1 ? parseFloat(itemSub.QNT1).toFixed(2) + ' ' + itemSub.SAT_1 : ''}</td>
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
               <td class="no-border text-center" style="width: 35%">Dikirim Oleh</td>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%">Diterima Oleh</td>
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

function buttonAddAddPickBarangAll (kodebrg) {

  let _token  = $("#_token").val()

  $.ajax({
    url: "{!! url('sodetailbarangall') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebrg : kodebrg,
      nosat : 1
    },
    success: function(res) {
      console.log(res)

      if (!res.barang.length) {

        alertify.warning("Terjadi kesalahan pada server")
        return
      }

      tempAddAdd = res.barang[0]
      document.getElementById("input_add_add_kodebarang").value = tempAddAdd.Kodebrg
      document.getElementById("input_add_add_namabarang").value = tempAddAdd.NamaBrg
      document.getElementById("input_add_add_disc").value = '0.00'
      document.getElementById("input_add_add_discrp").value = '0.00'
      let selectOption = ''
      if (tempAddAdd.Sat1) {
        selectOption += `<option value=1 selected>${tempAddAdd.Sat1}</option>`
      }
      if (tempAddAdd.Sat2) {
        selectOption += `<option value=2>${tempAddAdd.Sat2}</option>`
      }
      if (tempAddAdd.Sat3) {
        selectOption += `<option value=3>${tempAddAdd.Sat3}</option>`
      }
      document.getElementById("input_add_add_nosat").innerHTML = selectOption

      console.log(res.harga)
      let rowTable = ``
      res.harga.forEach((item, i) => {
        let date1 = ""
        if (item.TANGGAL) {
            let date = new Date(item.TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }
        rowTable += `
        <tr>
        <td>${date1}</td>
        <td>-</td>
        <td>${item.SATUAN}</td>
        <td>-</td>
        <td>-</td>
        <td class="text-right">${Number(item.Xharga) ? parseFloat(item.Xharga).toFixed(2) : '0.00'}</td>
        <td>-</td>
        <td>-</td>

        </tr>`
      });

      if(!res.harga.length) {
        rowTable= `<tr><td class="text-center" colspan=8>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      if (res.harga.length && Number(res.harga[0].Xharga)) {
        document.getElementById("input_add_add_harga").value = parseFloat(res.harga[0].Xharga).toFixed(2)
      } else {
        if (Number(tempAddAdd.Hrg1_1)) {
          document.getElementById("input_add_add_harga").value = parseFloat(tempAddAdd.Hrg1_1).toFixed(2)
        } else {
          document.getElementById("input_add_add_harga").value = '0.00'
        }
      }

      buttonAddListBatal()
      document.getElementById("input_add_add_kodebarang").scrollIntoView();

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refres.hargah browser')
    }

  })

}

function buttonAddAddPickBarangFOCPlus (index , pEdit = 0) {
  let _token  = $("#_token").val()

  console.log(dataAddAddListItem[index])
  tempAddAdd = dataAddAddListItem[index]

  cekSatuanBarang(tempAddAdd.Kodebrg)

  document.getElementById("input_add_add_kodebarang").value = tempAddAdd.Kodebrg
  document.getElementById("input_add_add_namabarang").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_noPPL").value = ''
  document.getElementById("input_add_add_urutPPL").value = 0
  // document.getElementById("input_add_add_disc").value = '0.00'
  // document.getElementById("input_add_add_discrp").value = '0.00'

  let selectOption = ''
  if (tempSatuanBarang[0].SAT1) {
    selectOption += `<option value=1 selected>1-${tempSatuanBarang[0].SAT1}</option>`
  }
  if (tempSatuanBarang[0].SAT2) {
    selectOption += `<option value=2>2-${tempSatuanBarang[0].SAT2}</option>`
  }
  if (tempSatuanBarang[0].SAT3) {
    selectOption += `<option value=3>3-${tempSatuanBarang[0].SAT3}</option>`
  }
  document.getElementById("input_add_add_nosat").innerHTML = selectOption

  $.ajax({
    url: "{!! url('pocekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang : tempAddAdd.Kodebrg,
      nosat : 1
    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        let date1 = ""
        if (item.TANGGAL) {
            let date = new Date(item.TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }
        rowTable += `
        <tr>
          <td>${date1}</td>
          <td>-</td>
          <td>${item.SATUAN}</td>
          <td>-</td>
          <td>-</td>
          <td class="text-right">${Number(item.Xharga) ? parseFloat(item.Xharga).toFixed(2) : '0.00'}</td>
          <td>-</td>
          <td>-</td>

        </tr>`
      });

      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=8>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      if (res.length && Number(res[0].Xharga)) {
        document.getElementById("input_add_add_harga").value = parseFloat(res[0].Xharga).toFixed(2)
      } else {
        if (Number(tempAddAdd.Hrg1_1)) {
          document.getElementById("input_add_add_harga").value = parseFloat(tempAddAdd.Hrg1_1).toFixed(2)
        } else {
          document.getElementById("input_add_add_harga").value = '0.00'
        }
      }

      buttonAddListBatal()
      document.getElementById("input_add_add_kodebarang").scrollIntoView();

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

}

function buttonAddAddPickBarangNonFOC (index , pEdit = 0) {

  let _token  = $("#_token").val()
  console.log(dataAddAddListItem[index])
  tempAddAdd = dataAddAddListItem[index]
  
  cekSatuanBarang(tempAddAdd.KodeBrg)

  document.getElementById("input_add_add_kodebarang").value = tempAddAdd.KodeBrg
  document.getElementById("input_add_add_namabarang").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_qty").value = tempAddAdd.Qnt
  document.getElementById("input_add_add_noPPL").value = tempAddAdd.NoBukti
  document.getElementById("input_add_add_urutPPL").value = tempAddAdd.Urut
  // document.getElementById("input_add_add_discrp").value = '0.00'
  let selectOption = ''
  if (tempSatuanBarang[0].SAT1) {
    selectOption += `<option value=1>1-${tempSatuanBarang[0].SAT1}</option>`
  }
  if (tempSatuanBarang[0].SAT2) {
    selectOption += `<option value=2>2-${tempSatuanBarang[0].SAT2}</option>`
  }
  if (tempSatuanBarang[0].SAT3) {
    selectOption += `<option value=3>3-${tempSatuanBarang[0].SAT3}</option>`
  }
  document.getElementById("input_add_add_nosat").innerHTML = selectOption

  $.ajax({
    url: "{!! url('pocekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang : tempAddAdd.KodeBrg
    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        let date1 = ""
        if (item.TANGGAL) {
            let date = new Date(item.TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }
        rowTable += `
        <tr>
          <td>${item.NamaCustSupp}</td>
          <td>${date1}</td>
          <td>${item.QNT}</td>
          <td>${item.SATUAN}</td>
          <td>${item.KODEVLS}</td>
          <td>${item.KURS}</td>
          <td class="text-right">${Number(item.HARGA) ? parseFloat(item.HARGA).toFixed(2) : '0.00'}</td>
          <td>${item.DISCRP}</td>
          <td class="text-right">${Number(item.HrgNetto) ? parseFloat(item.HrgNetto).toFixed(2) : '0.00'}</td>
        </tr>`
      });

      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=9>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      if (res.length && Number(res[0].HARGA)) {
        document.getElementById("input_add_add_harga").value = parseFloat(res[0].HARGA).toFixed(2)
      } else {
        if (Number(tempAddAdd.Hrg1_1)) {
          document.getElementById("input_add_add_harga").value = parseFloat(tempAddAdd.Hrg1_1).toFixed(2)
        } else {
          document.getElementById("input_add_add_harga").value = '0.00'
        }
      }

      buttonAddListBatal()
      document.getElementById("input_add_add_kodebarang").scrollIntoView();

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

}

function buttonAddAddPickBarangNonFOCPlus (index , pEdit = 0) {
  let _token  = $("#_token").val()
  console.log(dataAddAddListItem[index])
  tempAddAdd = dataAddAddListItem[index]

  cekSatuanBarang(tempAddAdd.KodeBrg)

  document.getElementById("input_add_add_kodebarang").value = tempAddAdd.KodeBrg
  document.getElementById("input_add_add_namabarang").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_qty").value = tempAddAdd.Qnt
  document.getElementById("input_add_add_noPPL").value = tempAddAdd.NoBukti
  document.getElementById("input_add_add_urutPPL").value = tempAddAdd.Urut
  // document.getElementById("input_add_add_discrp").value = '0.00'

  let selectOption = ''
  if (tempSatuanBarang[0].SAT1) {
    selectOption += `<option value=1 selected>1-${tempSatuanBarang[0].SAT1}</option>`
  }
  if (tempSatuanBarang[0].SAT2) {
    selectOption += `<option value=2>2-${tempSatuanBarang[0].SAT2}</option>`
  }
  if (tempSatuanBarang[0].SAT3) {
    selectOption += `<option value=3>3-${tempSatuanBarang[0].SAT3}</option>`
  }
  document.getElementById("input_add_add_nosat").innerHTML = selectOption

  $.ajax({
    url: "{!! url('pocekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang : tempAddAdd.KodeBrg
    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        let date1 = ""
        if (item.TANGGAL) {
            let date = new Date(item.TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }
        rowTable += `
        <tr>
          <td>${item.NamaCustSupp}</td>
          <td>${date1}</td>
          <td>${item.QNT}</td>
          <td>${item.SATUAN}</td>
          <td>${item.KODEVLS}</td>
          <td>${item.KURS}</td>
          <td class="text-right">${Number(item.HARGA) ? parseFloat(item.HARGA).toFixed(2) : '0.00'}</td>
          <td>${item.DISCRP}</td>
          <td class="text-right">${Number(item.HrgNetto) ? parseFloat(item.HrgNetto).toFixed(2) : '0.00'}</td>
        </tr>`
      });

      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=9>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      if (res.length && Number(res[0].HARGA)) {
        document.getElementById("input_add_add_harga").value = parseFloat(res[0].HARGA).toFixed(2)
      } else {
        if (Number(tempAddAdd.Hrg1_1)) {
          document.getElementById("input_add_add_harga").value = parseFloat(tempAddAdd.Hrg1_1).toFixed(2)
        } else {
          document.getElementById("input_add_add_harga").value = '0.00'
        }
      }

      buttonAddListBatal()
      document.getElementById("input_add_add_kodebarang").scrollIntoView();

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

}

function cekSatuanBarang (KodeBrg){
  let _token = $("#_token").val()

  $.ajax({
    url: "{!! url('ceksatuanbarang') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      KodeBrg : KodeBrg
    },
    success: function(res) {
      tempSatuanBarang = res
    },
    error: function (err) {
      console.log(err)
    }
  })
}

function buttonAddPickPelanggan (kode, nama , alamat) {
  console.log('buttonAddPickPelanggan')
  console.log(kode,nama,alamat)
  document.getElementById("input_add_kodesupplier").value = kode
  document.getElementById("input_add_namasupplier").value = nama
  document.getElementById("input_add_alamatsupplier").value = alamat
  document.getElementById("input_add_pembayaran").value = 0
  document.getElementById("input_add_hari").value = 0
  document.getElementById("input_add_kodealamatkirim").value = ''
  document.getElementById("input_add_alamatkirim").value = ''
  document.getElementById("input_add_kodepic").value = ''
  document.getElementById("input_add_namapic").value = ''
  document.getElementById("input_add_kodeekspedisi").value = ''
  document.getElementById("input_add_ekspedisi").value = ''
  buttonAddListBatal()
  // $("#form").modal('toggle')
}

function buttonAddPickAlamatKirim (index) {

  let itemX = listAlamatKirim[index]
  console.log(itemX)
  // console.log(kode,nama,alamat)
  if (tipeform == 'edit') {
    onChangeHeader('NoAlamatKirim' , itemX.KODEGDG)
    onChangeHeader('AlamatKirim' , itemX.Alamat)
  }

  document.getElementById("input_add_kodealamatkirim").value = itemX.KODEGDG
  document.getElementById("input_add_alamatkirim").value = itemX.Alamat
  buttonAddListBatal()

}

function buttonAddPickNoSO (kode, nama) {
  console.log('buttonAddPickLokasiPenerima')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('KODEKEBUN' , kode)

  }
  document.getElementById("input_add_noso").value = kode
  document.getElementById("input_add_nopocust").value = nama

  buttonAddListBatal()
}

function buttonAddPickLokasiPenerima (kode, nama ) {
  console.log('buttonAddPickLokasiPenerima')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('KODEKEBUN' , kode)

  }
  document.getElementById("input_add_kodeekspedisi").value = kode
  document.getElementById("input_add_ekspedisi").value = nama

  buttonAddListBatal()
  document.getElementById("input_add_kodeekspedisi").scrollIntoView();
}

function buttonAddPickPIC (kode, nama ) {
  console.log('buttonAddPickPIC')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('KodePF' , kode)
  }
  document.getElementById("input_add_kodepic").value = kode
  document.getElementById("input_add_namapic").value = nama
  buttonAddListBatal()
}

function buttonAddPickPWO (kode, nama) {
  console.log('buttonAddPickPWO')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('KodePF' , kode)
  }
  document.getElementById("input_add_kodepic").value = kode
  document.getElementById("input_add_namapic").value = nama
  buttonAddListBatal()
}

function buttonAddPickValas (kode, kurs) {
  console.log('buttonAddPickValas')
  console.log(kode,kurs)
  if (tipeform == 'edit') {
    onChangeHeader('KODEVLS' , kode)
    onChangeHeader('KURS' , kurs)
  }
  document.getElementById("input_add_valas").value = kode
  document.getElementById("input_add_kurs").value = kurs
  buttonAddListBatal()
}

function buttonAddPickSales (kode, nama ) {
  console.log('buttonAddPickSales')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('KODESLS' , kode)

  }
  document.getElementById("input_add_kodesales").value = kode
  document.getElementById("input_add_namasales").value = nama
  buttonAddListBatal()
  // $("#form").modal('toggle')
}

function buttonAddPickBackOffice (kode, nama ) {
  console.log('buttonAddPickBackOffice')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('Boffice' , kode)

  }
  document.getElementById("input_add_kodebackoffice").value = kode
  document.getElementById("input_add_namabackoffice").value = nama
  buttonAddListBatal()

  document.getElementById("input_add_kodebackoffice").scrollIntoView();

}

function buttonAddListBatal () {
  $('.showhidemodalbodyadd').hide();
  $('#modalBodyAddMain').show();

  $("#form").modal('toggle')
}

function cleanFormAddAdd () {
  // document.getElementById("input_add_add_kodebarang").value = ''
  // document.getElementById("input_add_add_namabarang").value = ''
  // document.getElementById("input_add_add_nopnwpo").value = '-'
  // document.getElementById("input_add_add_qty").value = '0.00'
  // document.getElementById("input_add_add_nosat").innerHTML = '<option value=0 selected>Pilih Satuan</option>'
  // // document.getElementById("input_add_add_satuanproduk").value = ''
  // document.getElementById("input_add_add_harga").value = '0.00'
  // // document.getElementById("input_add_add_disc").value = '0.00'
  // document.getElementById("input_add_add_discrp").value = '0.00'
  // document.getElementById("input_add_add_discpersen1").value = '0.00'
  // document.getElementById("input_add_add_discpersen2").value = '0.00'
  // document.getElementById("input_add_add_discpersen3").value = '0.00'
  // // document.getElementById("input_add_add_tambahkepo").value = 0


}

function lockFormAdd () {
  document.getElementById("buttonAddListGudangAsal").hidden = true;
  document.getElementById("buttonAddListGudangTujuan").hidden = true;
  document.getElementById("input_add_keterangan").disabled = true;
  document.getElementById("buttonSimpanData").hidden = true;
}

function buttonShowHideHeader () 
{
  var modal = document.getElementById("modalBodyAddMainHeader");
  console.log($('#modalBodyAddMainHeader').css('display'))
  if($('#modalBodyAddMainHeader').css('display') === 'block') {
    modal.style.display = "none";
  } else {
    modal.style.display = "block";
  }
}

function buttonShowHideHeaderDetail () {
  var modal = document.getElementById("modalBodyDetailMainHeader");
  console.log($('#modalBodyDetailMainHeader').css('display'))
  if($('#modalBodyDetailMainHeader').css('display') === 'block') {
    modal.style.display = "none";
  } else {
    modal.style.display = "block";
  }
}

function unlockFormAdd () {
  document.getElementById("buttonAddListGudangAsal").hidden = false;
  document.getElementById("buttonAddListGudangTujuan").hidden = false;
  document.getElementById("input_add_keterangan").disabled = false;
  document.getElementById("buttonSimpanData").hidden = false;
  // document.getElementById("input_add_tipeppn").disabled = false
  // document.getElementById("input_add_pembayaran").disabled = false
  // document.getElementById("input_add_nopocust").disabled = false
  // document.getElementById("input_add_noso").disabled = false
  // document.getElementById("input_add_keterangan").disabled = false
  // document.getElementById("input_add_tanggalkirim").disabled = false
  // document.getElementById("input_add_tanggalkirim").disabled = false
  // document.getElementById("input_add_hari").disabled = false
  // document.getElementById("input_add_draftpo").disabled = false

  // document.getElementById("buttonAddListPelanggan").disabled = false
  // document.getElementById("buttonAddListGudang").disabled = false
  // document.getElementById("buttonAddListSales").disabled = false
  // document.getElementById("buttonAddListValas").disabled = false
  // document.getElementById("buttonAddListPIC").disabled = false
  // document.getElementById("buttonAddListLokasiPenerima").disabled = false
  // document.getElementById("buttonAddListBackOffice").disabled = false

  // document.getElementById("input_add_disc").disabled = false
  // document.getElementById("input_add_discrp").disabled = false
}

function cleanFormAdd () {
  // document.getElementById("input_add_tanggalkirim").valueAsDate = new Date()
  // document.getElementById("input_add_tanggalkirim").valueAsDate = new Date()
  // document.getElementById("input_add_kodesupplier").value = ''
  // document.getElementById("input_add_namasupplier").value = ''
  // document.getElementById("input_add_alamatsupplier").value = ''
  // document.getElementById("input_add_kodealamatkirim").value = ''
  // document.getElementById("input_add_alamatkirim").value = ''
  // document.getElementById("input_add_kodepic").value = ''
  // document.getElementById("input_add_namapic").value = ''
  // document.getElementById("input_add_kodeekspedisi").value = ''
  // document.getElementById("input_add_ekspedisi").value = ''
  // document.getElementById("input_add_keterangan").value = ''
  // document.getElementById("input_add_valas").value = ''
  // document.getElementById("input_add_kurs").value = ''
  // document.getElementById("input_add_nopocust").value = '0.00'
  // document.getElementById("input_add_noso").value = ''
  // document.getElementById("input_add_kodebackoffice").value = ''
  // document.getElementById("input_add_namabackoffice").value = ''
  // document.getElementById("input_add_tipeppn").value = 0
  // document.getElementById("input_add_pembayaran").value = 0
  // document.getElementById("input_add_kodesales").value = ''
  // document.getElementById("input_add_namasales").value = ''
  // document.getElementById("input_add_hari").value = 0
  // document.getElementById("input_add_draftpo").value = 0

  // document.getElementById("input_add_tipeppn").disabled = false
  // document.getElementById("input_add_pembayaran").disabled = false
  // document.getElementById("input_add_nopocust").disabled = false
  // document.getElementById("input_add_noso").disabled = false
  // document.getElementById("input_add_keterangan").disabled = false
  // document.getElementById("input_add_tanggalkirim").disabled = false
  // document.getElementById("input_add_tanggalkirim").disabled = false
  // document.getElementById("input_add_hari").disabled = false
  // document.getElementById("input_add_draftpo").disabled = false

  // document.getElementById("buttonAddListPelanggan").disabled = false
  // document.getElementById("buttonAddListGudang").disabled = false
  // document.getElementById("buttonAddListSales").disabled = false
  // document.getElementById("buttonAddListValas").disabled = false
  // document.getElementById("buttonAddListPIC").disabled = false
  // document.getElementById("buttonAddListLokasiPenerima").disabled = false
  // document.getElementById("buttonAddListBackOffice").disabled = false

  // document.getElementById("input_add_disc").disabled = false
  // document.getElementById("input_add_discrp").disabled = false

  // document.getElementById("input_add_disc").value = '0.00'
  // document.getElementById("input_add_discrp").value = '0.00'
  // document.getElementById("input_add_ppn").value = '0.00'
  // document.getElementById("input_add_dpp").value = '0.00'
  // document.getElementById("input_add_grandtotal").value = '0.00'
}

function buttonEdit (NOBUKTI) {
  tipeform = 'edit';
  console.log('buttonEdit', NOBUKTI);

  $('.showhide').hide();
  $('#buttonSubmitSaveHeader').show();
  unlockFormAdd();

  let akses = $("#akses_iskoreksi").val();
  let _token = $("#_token").val();

  $('.showhidemodalbodyadd').hide();
  $('#modalBodyAddMain').show();

  $('#buttonSimpanData').hide();
  $('#buttonSimpanEdit').show();

  $('#page1').hide();
  $('#page2').show();

  refreshDataTableEdit(NOBUKTI);
}


// function buttonEdit (NOBUKTI) {
//   tipeform = 'edit'
//   console.log('buttonEdit' , NOBUKTI)

//   $('.showhide').hide();
//   // $('.showhidemodalbodyaddmain').hide();
//   $('#buttonSubmitSaveHeader').show();
//   unlockFormAdd()

//   let akses = $("#akses_iskoreksi").val();

//   let _token  = $("#_token").val()

//   $('.showhidemodalbodyadd').hide();
//   // $('#modalBodyAddListPelanggan').show();
//   $('#modalBodyAddMain').show();
//   refreshDataTableEdit(NOBUKTI)
//   // $("#form").modal('toggle')
//   $('#page1').hide();
//   $('#page2').show();
// }


function buttonAdd (NOBUKTI) {
  tipeform = 'add'
  $('.showhide').hide();
  
  $('#buttonSubmitSaveHeader').hide();
  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  dataTableAdd = []
  cleanFormAdd()
  unlockFormAdd()
  setNewNoBukti()
  refreshDataTableAdd(NOBUKTI)
  
  submitAddAdd()
  $('#buttonSimpanEdit').hide();
  $('#buttonSimpanData').show();
  $('#page1').hide();
  $('#page2').show();

}

function buttonCloseFormAdd () {
  $('#page3').hide();
  $('#page4').hide();
  $('#page2').hide();
  $('#page1').show();

  if(tipeform == 'edit' || tipeform == 'detail'){
    
  } else {
  deleteTransfer()
  }

  loadAll()
}

function buttonCloseFormEditData() {
  console.log('Menutup form edit');

  $('#page3, #page2').hide();
  $('#page1').show();

  loadAll();
}


function buttonCloseFormSimpanData () {
  
    $('#page3').hide();
    $('#page2').hide();
    $('#page1').show();

  deleteTransferSimpanData()
  loadAll();

}

function changeQnt (urut, kodebrg, gudangAsal) {
  console.log('change qnt bro');

  let _token = $("#_token").val();
  let qntValue = document.getElementById('input_table_qty' + urut).value;

  if (!tableIsiRefreshDataList || !tableIsiRefreshDataList[urut - 1]) {
    alertify.warning('Data item tidak ditemukan');
    return;
  }

  let item = tableIsiRefreshDataList[urut - 1];
  console.log(item);

  let QNT = 0;
  let QNT2 = 0;

  console.log('Debug NOSAT:', item.NOSAT, 'ISI:', item.ISI, 'QTY:', qntValue);

  // Hitung QNT dan QNT2 berdasarkan satuan
  if (item.NOSAT == 1) {
    QNT = qntValue;
    QNT2 = qntValue * item.ISI;
  } else if (item.NOSAT == 2) {
    QNT = qntValue * item.ISI;
    QNT2 = qntValue;
  }

  console.log('QNT:', QNT, 'QNT2:', QNT2);
  console.log('Qty Minta (maksimum):', item.QTY);

  // Cek apakah QNT > Qty Minta
  if (Number(QNT) > Number(item.QTY)) {
    alertify.warning('Qty melebihi Qty Minta.');
    document.getElementById('input_table_qty' + urut).value = '';
    return;
  }

  // Cek stok gudang asal
  let qntstockchecker = 0;

  $.ajax({
    url: "{!! url('trfbrgCekQntStock') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebrg,
      gudangAsal
    },
    success: function (res) {
      console.log(res);
      let qntCheckerTemp = 0;

      if (res && res.length > 0 && res[0].SALDOQNT !== undefined) {
        qntCheckerTemp = res[0].SALDOQNT;
      } else {
        alertify.warning('Tidak dapat membaca saldo stok.');
        qntstockchecker = 1;
        return;
      }

      console.log('Cek stok:', QNT, '>', qntCheckerTemp);

      if (Number(QNT) > Number(qntCheckerTemp)) {
        qntstockchecker = 1;
      } else {
        qntstockchecker = 0;
      }
    },
    error: function (err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan saat cek stok, silakan refresh browser.');
      qntstockchecker = 1;
    }
  });

  // Jika melebihi stok berhentikan proses
  if (qntstockchecker == 1) {
    alertify.warning('Qty melebihi stok di gudang asal.');
    document.getElementById('input_table_qty' + urut).value = '';
    return;
  }

  // Simpan perubahan QNT
  $.ajax({
    url: "{!! url('trfbrgonchangeqnt') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      NoBukti: dataNoBuktiSatuan,
      Urut: urut,
      QNT: QNT,
      QNT2: QNT2
    },
    success: function (res) {
      console.log(res)
      $('#divhargaterakhir').hide();
      $('#divStockProyeksi').hide();

      alertify.success('Berhasil mengganti QNT (Urut ' + urut + ')'); 
      console.log(' QNT tersimpan:', QNT, QNT2);
    },
    error: function (err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser.');
    }
  });
}

function changeQntEdit (urut, kodebrg, gudangAsal) {
  console.log('changeQntEdit urut:', urut);

  let _token = $("#_token").val();
  let qntValue = parseFloat(document.getElementById('input_table_qty' + urut).value || 0);

  if (!dataTableAdd || !dataTableAdd.length) {
    alertify.warning('Data item tidak ditemukan');
    return;
  }

  // cari item berdasarkan URUT
  let item = dataTableAdd.find(x => Number(x.URUT) === Number(urut));
  if (!item) {
    alertify.warning('Data item tidak ditemukan');
    return;
  }

  console.log('item edit:', item);

  // Hitung QNT dan QNT2
  let QNT = 0;
  let QNT2 = 0;
  const isi = parseFloat(item.ISI) || 1;

  if (Number(item.NOSAT) === 1) {
    QNT = qntValue;
    QNT2 = qntValue * isi;
  } else if (Number(item.NOSAT) === 2) {
    QNT = qntValue * isi;
    QNT2 = qntValue;
  }

  // Ambil sisa (Qty Transfer)
  const batasSisa = parseFloat(item.sisa ?? item.QtyMinta ?? 0);
  console.log('QNT:', QNT, 'QNT2:', QNT2, 'Batas sisa (QtyTransfer):', batasSisa);

  // qty tidak boleh melebihi sisa (Qty Transfer)
  if (Number(QNT) > Number(batasSisa)) {
    alertify.warning(`Qty tidak boleh melebihi Qty Transfer (${batasSisa.toLocaleString()})`);
    document.getElementById('input_table_qty' + urut).value = '';
    return;
  }

  // Cek stok 
  $.ajax({
    url: "{!! url('trfbrgCekQntStock') !!}",
    type: "post",
    async: false,
    data: { _token, kodebrg, gudangAsal },
    success: function (res) {
      console.log('cek stok sukses:', res);

      if (!res || res.length === 0 || res[0].SALDOQNT === undefined) {
        alertify.warning('Tidak dapat membaca saldo stok.');
        return;
      }

      const stokReal = parseFloat(res[0].SALDOQNT ?? 0);

      // QNT lama (sebelum diubah)
      let qntLama = 0;
      if (Number(item.NOSAT) === 1) {
        qntLama = parseFloat(item.QNT ?? 0);
      } else if (Number(item.NOSAT) === 2) {
        qntLama = parseFloat(item.QNT ?? item.QNT2 ?? 0);
      }

      const stokKoreksi = stokReal + qntLama;
      console.log(`Stok real: ${stokReal}, QNT lama: ${qntLama}, stok koreksi: ${stokKoreksi}`);

      if (Number(QNT) > Number(stokKoreksi)) {
        const msg = `
          Qty melebihi stok gudang ${gudangAsal}.
          <br>Stok tersedia: <b>${stokReal.toLocaleString()}</b>
          <br>QTY lama: <b>${qntLama.toLocaleString()}</b>
          <br>Stok koreksi: <b>${stokKoreksi.toLocaleString()}</b>
          <br>QTY baru: <b>${QNT.toLocaleString()}</b>
        `;
        alertify.alert('Stok Tidak Mencukupi', msg);
        document.getElementById('input_table_qty' + urut).value = '';
        return;
      }

      $.ajax({
        url: "{!! url('trfbrgonchangeqnt') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          NoBukti: item.NOBUKTI,
          Urut: urut,
          QNT: QNT,
          QNT2: QNT2
        },
        success: function (res2) {
          alertify.success(`Qty berhasil diperbarui (Urut ${urut})`);
        },
        error: function (err) {
          console.error('Error simpan change qnt:', err);
          alertify.warning('Terjadi kesalahan saat menyimpan, silakan refresh halaman.');
        }
      });
    },
    error: function (err) {
      console.error('Error cek stok:', err);
      alertify.warning('Terjadi kesalahan saat cek stok, silakan refresh browser.');
    }
  });
}

// function changeQntEdit (urut, kodebrg, gudangAsal){

//   let _token  = $("#_token").val()
//   console.log(urut + " ini urutnya")

//   console.log(tableIsiRefreshDataList[urut-1].NOBUKTI + 'nobukti')

//   let qntValue = document.getElementById('input_table_qty'+urut).value

//   console.log(qntValue)
  
//   console.log(tableIsiRefreshDataList[urut-1])
  
//   let QNT = 0
//   let QNT2 = 0

//   if (tableIsiRefreshDataList[urut-1].NOSAT == 1){
//     QNT = qntValue
//     QNT2 = qntValue * tableIsiRefreshDataList[urut-1].ISI
//   }
//   if(tableIsiRefreshDataList[urut-1].NOSAT == 2){
//     QNT = qntValue * tableIsiRefreshDataList[urut-1].ISI
//     QNT2 = qntValue 
//   }

//   console.log(QNT + 'QNT1')
//   console.log(QNT2 + 'QNT2')

//   let qntstockchecker = 0

//   console.log(QNT + ' QNT1')
//   console.log(QNT2 + ' QNT2')

//   $.ajax({
//     url: "{!! url('trfbrgCekQntStock') !!}",
//     type: "post",
//     async: false,
//     data: {
//       _token,
//       kodebrg,
//       gudangAsal

//     },
//     success: function(res) {
//       console.log(res)
//       let qntCheckerTemp = 0

//       qntCheckerTemp = res[0].SALDOQNT

//       if(QNT >= qntCheckerTemp){
//       qntstockchecker = 0
//       }
//       else{
//         qntstockchecker = 1
//       }
      
//     },
//     error: function (err) {
//       console.log(err)
//       alertify.warning('Terjadi kesalahan silahkan refresh browser')
//     }

//   })

//   if ( qntstockchecker == 1){
//     alertify.warning('QNT melebihi Stok.')
//     return;
//   }

//   $.ajax({
//     url: "{!! url('trfbrgonchangeqnt') !!}",
//     type: "post",
//     async: false,
//     data: {
//       _token,
//       NoBukti: tableIsiRefreshDataList[urut-1].NOBUKTI,
//       Urut: urut,
//       QNT: QNT,
//       QNT2: QNT2

//     },
//     success: function(res) {
      
//         // loadAll()
//         // tipeform = 'edit'
//         // document.getElementById("buttonAddListPelanggan").disabled = true
//         $('#divhargaterakhir').hide();
//         $('#divStockProyeksi').hide();
//         // cleanFormAddAdd()

//         // refreshDataTableEdit(NoBukti)

//         alertify.success('Berhasil mengganti QNT dengan Urut' + urut)
      
//     },
//     error: function (err) {
//       console.log(err)
//       alertify.warning('Terjadi kesalahan silahkan refresh browser')
//     }

//   })

// }

function buttonCloseForm () {
  $('#page3').hide();
  $('#page4').hide();
  $('#page2').hide();
  $('#page1').show();

  // deleteHeader(NOBUKTI)

}

function buttonCloseFormDetail () {
  $('#page3').hide();
  $('#page4').hide();
  $('#page1').show();

}

function buttonAddMainHeader() {
  $('.showhidemodalbodyaddmain').hide();
  $('#modalBodyAddMainHeader').show();
  // $('#buttonAddListPelanggan').hide();
}

function buttonAddMainItems() {
  $('.showhide').hide();
  $('.showhidemodalbodyaddmain').hide();
  $('#modalBodyAddMainItems').show();
}

function buttonDetailMainHeader() {
  $('.showhidemodalbodydetailmain').hide();
  $('#modalBodyDetailMainHeader').show();
  // $('#buttonDetailListPelanggan').hide();
}

function buttonDetailMainItems() {
  $('.showhide').hide();
  $('.showhidemodalbodydetailmain').hide();
  $('#modalBodyDetailMainItems').show();
}

function cekPoDet () {
  
  let _token  = $("#_token").val()
  $.ajax({
    url: "{!! url('cekPoDet') !!}",
    type: "post",
    async: false,
    data: {
      _token
    },
    success: function(res){
      console.log(res)
    },
    error: function (err) {
      console.log(err)
    }
  })
}

function buttonDetail (NOBUKTI) {
  tipeform = 'detail'
  console.log('buttonEdit' , NOBUKTI)

  $('.showhide').hide();
  // $('.showhidemodalbodyaddmain').hide();
  $('#buttonSubmitSaveHeader').show();
  lockFormAdd()

  let akses = $("#akses_iskoreksi").val();

  let _token  = $("#_token").val()

  $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddListPelanggan').show();
  $('#modalBodyAddMain').show();
  refreshDataTableDetail(NOBUKTI)
  // $("#form").modal('toggle')
  $('#page1').hide();
  $('#page2').show();
}

function buttonDetailAdd (NOBUKTI) {
  tipeform = 'detail'
  console.log('buttonEdit' , NOBUKTI)

  $('.showhide').hide();
  // $('.showhidemodalbodyaddmain').hide();
  $('#buttonSubmitSaveHeader').show();
  lockFormAdd()

  let akses = $("#akses_iskoreksi").val();

  let _token  = $("#_token").val()

  $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddListPelanggan').show();
  $('#modalBodyAddMain').show();
  refreshDataTableDetailAdd(NOBUKTI)
  // $("#form").modal('toggle')
  $('#page1').hide();
  $('#page4').show();
}

function refreshDataTableAdd (NOBUKTI) {
  if ($.fn.DataTable.isDataTable("#tabel_add")) {
    $('#tabel_add').DataTable().clear().destroy();
  }

  let showQtyTransfer = (tipeform === 'edit');

  let headerTable = `
    <tr>
      <th style="padding:4px 12px;">Kode Barang</th>
      <th style="padding:4px 12px;">Nama Barang</th>
      <th style="padding:4px 12px;">Satuan</th>
      <th style="padding:4px 12px;">Qty Minta</th>
      ${showQtyTransfer ? '<th style="padding:4px 12px;">Qty Transfer</th>' : ''}
      <th style="padding:4px 12px;">Qty</th>
    </tr>
  `;
  $('#tabel_add thead').html(headerTable);

  if (!NOBUKTI) {
    document.getElementById("tabel_data_add").innerHTML = `
      <tr>
        <td class="text-center" colspan="${showQtyTransfer ? 6 : 5}">Belum ada barang</td>
      </tr>
    `;
    return;
  }

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('trfbrggetdetail') !!}",
    type: "POST",
    async: false,
    data: { _token, nobukti: NOBUKTI },
    success: function (res) {
      console.log('Data diterima', res);

      if (!res.list.length) {
        $('#page3').hide();
        $('#page2').hide();
        $('#page1').show();
        return;
      }

      dataHeaderAdd = res.listHeader;
      dataTableAdd = res.list;
      tableIsiRefreshData = res.listHeader;
      tableIsiRefreshDataList = res.list;

      let rowTable = "";
      dataTableAdd.forEach((item) => {
        rowTable += `
          <tr>
            <td style="white-space:nowrap; vertical-align:middle;">${item.KODEBRG}</td>
            <td style="white-space:nowrap; vertical-align:middle;">${item.NAMABRG}</td>
            <td style="white-space:nowrap; text-align:center; vertical-align:middle;">${item.Satuan}</td>
            <td style="white-space:nowrap; text-align:right; vertical-align:middle;">${parseFloat(item.QTY).toLocaleString()}</td>
            ${showQtyTransfer ? `<td style="white-space:nowrap; text-align:right; vertical-align:middle;">${parseFloat(item.QTYTRANSFER || 0).toLocaleString()}</td>` : ''}
            <td style="white-space:nowrap; text-align:center; vertical-align:middle;">
              <input 
                type="number"
                class="form-control text-right"
                id="input_table_qty${item.URUT}"
                onchange="changeQnt(${item.URUT}, '${item.KODEBRG}', '${item.GdgAsal}')"
                style="width:80px; height:30px; padding:2px 6px; margin:0; line-height:1.2; vertical-align:middle;"
              >
            </td>
          </tr>`;
      });

      document.getElementById("tabel_data_add").innerHTML = rowTable;

      document.getElementById("input_add_kodeGudangAsal").value = dataTableAdd[0].GdgAsal;
      document.getElementById("input_add_namaGudangAsal").value = dataTableAdd[0].NamagdgAsal;
      document.getElementById("input_add_kodeGudangTujuan").value = dataTableAdd[0].GdgTujuan;
      document.getElementById("input_add_namaGudangTujuan").value = dataTableAdd[0].NamagdgTujuan;
      document.getElementById("input_add_keterangan").value = dataHeaderAdd[0].Keterangan;

      $('#tabel_add').DataTable({
        autoWidth: false,
        paging: false,
        info: false,
        searching: false,
        columnDefs: [
          { width: "170px", targets: 1 },
          { width: "775px", targets: 2 },
          { width: "100px", targets: 3 },
          ...(showQtyTransfer
            ? [{ width: "100px", targets: 4 }, { width: "100px", targets: 5 }]
            : [{ width: "100px", targets: 4 }]),
        ]
      });
	if (tipeform === 'add') {
        $('#buttonSimpanEdit').hide();
        $('#buttonSimpanData').show();
      } else if (tipeform === 'edit') {
        $('#buttonSimpanData').hide();
        $('#buttonSimpanEdit').show();
      }
    },
    error: function (err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}

function refreshDataTableEdit (NOBUKTI) {
  console.log('refreshDataTableEdit', NOBUKTI);

  const headerTable = `
    <tr>
      <th style="padding:4px 12px;">Kode Barang</th>
      <th style="padding:4px 12px;">Nama Barang</th>
      <th style="padding:4px 12px;">Satuan</th>
      <th style="padding:4px 12px;">Qty Minta</th>
      <th id="qty_transfer" style="padding:4px 12px;">Qty Transfer</th>
      <th style="padding:4px 12px;">Qty</th>
    </tr>
  `;
  $('#tabel_add thead').html(headerTable);

  if ($.fn.DataTable.isDataTable("#tabel_add")) {
    $('#tabel_add').DataTable().clear().destroy();
  }

  if (!NOBUKTI) {
    document.getElementById("tabel_data_add").innerHTML = `
      <tr>
        <td class="text-center" colspan="6">Belum ada barang</td>
      </tr>`;
    return;
  }

  const _token = $("#_token").val();

  $.ajax({
    url: "{!! url('trfbrggetdetailedit') !!}",
    type: "POST",
    data: { _token, nobukti: NOBUKTI },
    success: function (res) {
      console.log("refreshDataTableEdit success", res);

      if (!res.list || !res.list.length) {
        $('#page3, #page2').hide();
        $('#page1').show();
        return;
      }

      dataHeaderAdd = res.listHeader || [];
      dataTableAdd = res.list || [];

      const filteredList = dataTableAdd.filter(item => {
        const qtyValue = parseFloat(item.QTY ?? item.QNT ?? 0);
        return qtyValue > 0;
      });

      let rowTable = "";
      if (filteredList.length) {
        filteredList.forEach((item) => {
          const qnt = parseFloat(item.QNT ?? 0);
          const qtyDisplay = (qnt > 0 ? qnt : "").toLocaleString();
          const qtyMinta = parseFloat(item.QtyMinta ?? 0).toLocaleString();
          const sisa = parseFloat(item.sisa ?? 0).toLocaleString();

          rowTable += `
            <tr>
              <td style="white-space:nowrap; vertical-align:middle;">${item.KODEBRG ?? ''}</td>
              <td style="white-space:nowrap; vertical-align:middle;">${item.NAMABRG ?? ''}</td>
              <td style="white-space:nowrap; text-align:center; vertical-align:middle;">${item.SAT_1 ?? ''}</td>
              <td style="white-space:nowrap; text-align:right; vertical-align:middle;">${qtyMinta}</td>
              <td id="td_qty_transfer_${item.URUT}" 
                  style="white-space:nowrap; text-align:right; vertical-align:middle;">
                  ${sisa}
              </td>
              <td style="white-space:nowrap; text-align:center; vertical-align:middle;">
                <input 
                  type="number"
                  class="form-control text-right"
                  id="input_table_qty${item.URUT}" 
                  step="any"
                  min="0"
                  value="${qnt > 0 ? qnt : ''}"
                  onchange="changeQntEdit(${item.URUT}, '${item.KODEBRG}', '${item.GdgAsal}')"
                  style="width:80px; height:30px; padding:2px 6px; margin:0; line-height:1.2; vertical-align:middle;"
                >
              </td>
            </tr>`;
        });
      } else {
        rowTable = `
          <tr>
            <td class="text-center" colspan="6">Belum ada barang yang tersisa</td>
          </tr>`;
      }

      document.getElementById("tabel_data_add").innerHTML = rowTable;

      // isi data header
      if (dataHeaderAdd.length) {
        const hdr = dataHeaderAdd[0];
        $("#input_add_kodeGudangAsal").val(hdr.GDGASAL ?? filteredList[0]?.GdgAsal ?? '');
        $("#input_add_namaGudangAsal").val(filteredList[0]?.NamagdgAsal ?? '');
        $("#input_add_kodeGudangTujuan").val(filteredList[0]?.GdgTujuan ?? '');
        $("#input_add_namaGudangTujuan").val(filteredList[0]?.NamagdgTujuan ?? '');
        $("#input_add_nobukti").val(hdr.nobukti ?? '');
        $("#input_add_nourut").val(hdr.NoUrut ?? '');
        $("#input_add_tanggal").val(formatDate(hdr.Tanggal ?? ''));
        $("#input_add_keterangan").val(hdr.Keterangan ?? '');
      }

      $('#tabel_add').DataTable({
        autoWidth: false,
        paging: false,
        info: false,
        searching: false,
        order: [[1, 'asc']],
        columnDefs: [
          { width: "170px", targets: 0 },
          { width: "775px", targets: 1 },
          { width: "100px", targets: 2 },
          { width: "100px", targets: 3, className: "text-end" },
          { width: "100px", targets: 4, className: "text-end" },
          { width: "100px", targets: 5, orderable: false, className: "text-end" },
        ]
      });

      console.log("DataTable refreshed successfully.");
    },
    error: function (err) {
      console.error("refreshDataTableEdit error", err);
      alertify.warning("Terjadi kesalahan, silakan refresh halaman.");
    }
  });
}

function refreshDataTableDetail (NOBUKTI) {
  console.log('refreshDataTableDetail', NOBUKTI);

  const headerTable = `
    <tr>
      <th style="padding:4px 12px;">Kode Barang</th>
      <th style="padding:4px 12px;">Nama Barang</th>
      <th style="padding:4px 12px;">Satuan</th>
      <th style="padding:4px 12px;">Qty Minta</th>
      <th style="padding:4px 12px;">Qty Transfer</th>
      <th style="padding:4px 12px;">Qty</th>
    </tr>
  `;
  $('#tabel_add thead').html(headerTable);

  if ($.fn.DataTable.isDataTable("#tabel_add")) {
    $('#tabel_add').DataTable().clear().destroy();
  }

  if (!NOBUKTI) {
    document.getElementById("tabel_data_add").innerHTML = `
      <tr>
        <td class="text-center" colspan="6">Belum ada barang</td>
      </tr>`;
    return;
  }

  const _token = $("#_token").val();

  $.ajax({
    url: "{!! url('trfbrggetdetailedit') !!}",
    type: "POST",
    data: { _token, nobukti: NOBUKTI },
    success: function (res) {
      console.log("refreshDataTableDetail success", res);

      if (!res.list || !res.list.length) {
        $('#page3, #page2').hide();
        $('#page1').show();
        return;
      }

      dataHeaderAdd = res.listHeader || [];
      dataTableAdd = res.list || [];

      const filteredList = dataTableAdd.filter(item => parseFloat(item.QNT ?? 0) > 0);

      let rowTable = "";
      if (filteredList.length) {
        filteredList.forEach((item) => {
          const qnt = parseFloat(item.QNT ?? 0);
          const qtyMinta = parseFloat(item.QtyMinta ?? 0).toLocaleString();
          const qtyTransfer = parseFloat(item.QTY ?? 0).toLocaleString(); 
          const sisa = parseFloat(item.sisa ?? 0).toLocaleString();

          rowTable += `
            <tr>
              <td style="white-space:nowrap; vertical-align:middle;">${item.KODEBRG ?? ''}</td>
              <td style="white-space:nowrap; vertical-align:middle;">${item.NAMABRG ?? ''}</td>
              <td style="white-space:nowrap; text-align:center; vertical-align:middle;">${item.SAT_1 ?? ''}</td>
              <td style="white-space:nowrap; text-align:right; vertical-align:middle;">${qtyMinta}</td>
              <td id="td_qty_transfer_${item.URUT}" 
                  style="white-space:nowrap; text-align:right; vertical-align:middle;">
                  ${sisa}
              </td>
              <td style="white-space:nowrap; text-align:center; vertical-align:middle;">
                <input 
                  type="number"
                  class="form-control text-right"
                  id="input_table_qty${item.URUT}" 
                  step="any"
                  value="${qnt}"
                  disabled
                  style="width:80px; height:30px; padding:2px 6px; margin:0; line-height:1.2; vertical-align:middle;"
                >
              </td>
            </tr>`;
        });
      } else {
        rowTable = `
          <tr>
            <td class="text-center" colspan="6">Tidak ada barang dengan QTY > 0</td>
          </tr>`;
      }

      document.getElementById("tabel_data_add").innerHTML = rowTable;

      if (dataHeaderAdd.length) {
        const hdr = dataHeaderAdd[0];
        $("#input_add_kodeGudangAsal").val(hdr.GDGASAL ?? filteredList[0]?.GdgAsal ?? '');
        $("#input_add_namaGudangAsal").val(filteredList[0]?.NamagdgAsal ?? '');
        $("#input_add_kodeGudangTujuan").val(filteredList[0]?.GdgTujuan ?? '');
        $("#input_add_namaGudangTujuan").val(filteredList[0]?.NamagdgTujuan ?? '');
        $("#input_add_nobukti").val(hdr.nobukti ?? '');
        $("#input_add_nourut").val(hdr.NoUrut ?? '');
        $("#input_add_tanggal").val(formatDate(hdr.Tanggal ?? ''));
        $("#input_add_keterangan").val(hdr.Keterangan ?? '');
      }

      $('#tabel_add').DataTable({
        autoWidth: false,
        paging: false,
        info: false,
        searching: false,
        order: [[1, 'asc']],
        columnDefs: [
          { width: "170px", targets: 0 },
          { width: "775px", targets: 1 },
          { width: "100px", targets: 2 },
          { width: "100px", targets: 3, className: "text-end" },
          { width: "100px", targets: 4, className: "text-end" },
          { width: "100px", targets: 5, orderable: false, className: "text-end" },
        ]
      });

      console.log("Tabel detail berhasil di-refresh (Qty Transfer tampil)");
    },
    error: function (err) {
      console.error("refreshDataTableDetail error", err);
      alertify.warning("Terjadi kesalahan, silakan refresh browser.");
    }
  });
}

function refreshDataTableDetailAdd (NOBUKTI) {
  console.log('refreshDataTableDetailAdd', NOBUKTI);

  if ($.fn.DataTable.isDataTable("#detailKoreksiTable")) {
    $('#detailKoreksiTable').DataTable().clear().destroy();
  }

  if (!NOBUKTI) {
    document.getElementById("detailKoreksiTableData").innerHTML = `
      <tr><td class="text-center" colspan="4">Belum ada data</td></tr>`;
    return;
  }

  const _token = $("#_token").val();

  $.ajax({
    url: "{!! url('trfbrggetdetaileditAdd') !!}",
    type: "POST",
    data: { _token, nobukti: NOBUKTI },
    success: function (res) {
      console.log('refreshDataTableDetailAdd success', res);

      if (!res.list || !res.list.length) {
        ;
        $('#page4').hide();
        $('#page1').show();
        return;
      }

      const header = res.listHeader?.[0] ?? {};
      const list = res.list ?? [];

      $("#detail_nobukti").val(header.nobukti ?? '');
      $("#detail_kodeGudangAsal").val(header.GDGASAL ?? '');
      $("#detail_namaGudangAsal").val(list[0]?.NamagdgAsal ?? '');
      $("#detail_kodeGudangTujuan").val(list[0].GdgTujuan ?? '');
      $("#detail_namaGudangTujuan").val(list[0]?.NamagdgTujuan ?? '');
      $("#detail_keterangan").val(header.Keterangan ?? '');

      let rowTable = "";
      if (list.length) {
        list.forEach((item, i) => {
          rowTable += `
            <tr>
              <td style="white-space:nowrap; vertical-align:middle;">${item.KODEBRG ?? ''}</td>
              <td style="white-space:nowrap; vertical-align:middle;">${item.NAMABRG ?? ''}</td>
              <td style="white-space:nowrap; text-align:center; vertical-align:middle;">${item.Satuan ?? item.SAT_1 ?? ''}</td>
              <td style="white-space:nowrap; text-align:right; vertical-align:middle;">${parseFloat(item.QTY ?? item.QtyMinta ?? 0).toLocaleString()}</td>
            </tr>`;
        });
      } else {
        rowTable = `<tr><td colspan="4" class="text-center">Belum ada data</td></tr>`;
      }

      document.getElementById("detailKoreksiTableData").innerHTML = rowTable;

      $('#detailKoreksiTable').DataTable({
        paging: false,
        searching: false,
        info: false,
        autoWidth: false,
        order: [[0, 'asc']],
        columnDefs: [
          { width: "170px", targets: 0 },
          { width: "775px", targets: 1 },
          { width: "50px", targets: 2 },
          { width: "50px", targets: 3 },
        ]
      });

      $('#page1, #page2, #page3').hide();
      $('#page4').show();

      console.log(" Header & tabel detail berhasil di-refresh (page4)");
    },
    error: function (err) {
      console.error("refreshDataTableDetailAdd error", err);
      alertify.warning("Terjadi kesalahan, silakan refresh browser.");
    }
  });
}

function buttonAddDeleteItem (i) {
  console.log(i)

  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  console.log(dataTableAdd[i])
  let dataDelete = dataTableAdd[i]

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + dataDelete.NamaBrg + ' ?',
      function() {

        let _token = $("#_token").val();
        let Choice = "D"

        let NoBukti = dataDelete.NoBukti
        let Urut = dataDelete.Urut

        $.ajax({
          url: "{!! url('pospadd') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            Choice,
            NoBukti,
            NoUrut:0,
            Tanggal: '',
            TglJatuhTempo: '',
            KodeSupp: '',
            // Handling,
            KodeExp: '',
            Keterangan: '',
            // FakturSupp,
            KodeVls: '',
            Kurs: 0,
            PPn: 0,
            TipeBayar: 0,
            Hari: 0,
            // TipeDisc,
            // Disc,
            DiscRp: 0,
            Urut,
            KodeBrg: 0,
            Qnt: 0,
            NoSat: 0,
            Satuan: '',
            Isi: 0,
            Harga: 0,
            DiscP: 0,
            // DiscTot,
            NoPPL: '',
            // IsClose,
            // IsCloseD,
            // Catatan,
            // IsExp,
            // Tolerate,
            UrutPPL: 0,
            Kodegdg: '',
            Discpdet2: 0,
            Discpdet3: 0,
            // Discpdet4,
            // Discpdet5,
            // FlagTipe,
            NamaBrg: '',
            // IsJasa,
            // pFirst,
            pFOC: 0,
            Noso: '',
            Jmlrecord: 0,
            NOPOCUST: '',
            // IdUser,
            // pJasa,
            // NPPH23,
            // PERKIRAAN,
            // SatX,
            // COST,
            // SUBCOST,
            TglKirim: '',
            // PPH21,
            NOPNw: '',
            UrutPNW: 0

          },
          success: function(res) {
            console.log('resspsoadd', res)
            loadAll()

            // lockFormAdd()
            $('.showhide').hide();

            refreshDataTableAdd(NoBukti)

            alertify.success('Berhasil menghapus item')

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

function formatDate (date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;

    return [year, month, day].join('-');
}

function searchBarangAll (e) {
  if (e.which == 13) {
    console.log('enter')

    let search = $("#input_search_barang_all").val();

    $('#tabel_add_list_barangall').DataTable().destroy();

    $.ajax({
      url: "{!! url('solistbarang') !!}",
      type: "get",
      async: false,
      data: {
        search
      },
      success: function(res) {

        console.log(res)

        let rowTable = ""
        res.forEach((item, i) => {

          rowTable +=`
          <tr>
            <td>${item.Kodebrg}</td>
            <td>${item.NamaBrg}</td>
            <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddAddPickBarangAll('${item.Kodebrg}')" type="button" ><i class="bi bi-plus"></i></button></td>
          </tr>
        `
        });
        // $('#tabel_add_list_barangall').DataTable().destroy();

        document.getElementById("tabel_data_add_list_barangall").innerHTML = rowTable

        $("#tabel_add_list_barangall").DataTable({
          "lengthChange": false,
            "paging": false ,
            "searching" : false
        });

      }})

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
  }
  temp1 += '.' + tempAngka[1]
  return temp1
};

function calculateDiscRp() {
  let disc1 = document.getElementById('input_add_add_discpersen1').value
  let disc2 = document.getElementById('input_add_add_discpersen2').value
  let disc3 = document.getElementById('input_add_add_discpersen3').value
  
  let discRp = $('#input_add_add_harga').val()

  disc1 = parseFloat(disc1) || 0
  disc2 = parseFloat(disc2) || 0
  disc3 = parseFloat(disc3) || 0
  discRp = parseFloat(discRp) || 0

  if (disc1 > 100) {
    alert("Diskon tidak boleh melebihi angka 100")
    document.getElementById('input_add_add_discpersen1').value = ""
    return
  }
  if (disc2 > 100) {
    alert("Diskon tidak boleh melebihi angka 100")
    document.getElementById('input_add_add_discpersen2').value = ""
    return
  }
  if (disc3 > 100) {
    alert("Diskon tidak boleh melebihi angka 100")
    document.getElementById('input_add_add_discpersen3').value = ""
    return
  }

  let currentAmount = discRp
  let totalDiscount = 0

  if (disc1 > 0) {
    let afterDiskon1 = currentAmount * (disc1/100)
    currentAmount = currentAmount - afterDiskon1
    totalDiscount += afterDiskon1
  }

  if (disc2 > 0) {
    let afterDiskon2 = currentAmount * (disc2/100)
    currentAmount = currentAmount - afterDiskon2
    totalDiscount += afterDiskon2
  }

  if (disc3 > 0) {
    let afterDiskon3 = currentAmount * (disc3/100)
    currentAmount = currentAmount - afterDiskon3
    totalDiscount += afterDiskon3
  }

  document.getElementById('input_add_add_discrp').value = totalDiscount
}


</script>
{{-- script buat hover po belum otorisasi dan sudah otorisasi --}}
  <script>
    const tabHome = document.getElementById('nav-home-tab');
    const tabProfile = document.getElementById('nav-profile-tab');
    const tabProfile1 = document.getElementById('nav-profile1-tab');
    const tabProfile2 = document.getElementById('nav-profile2-tab');
  
    function setActiveTab(homeActive) {
      if (homeActive == 0) {
        tabHome.style.backgroundColor = '#007bff';
        tabHome.style.color = '#fff';
        tabProfile.style.backgroundColor = '#f8f9fa';
        tabProfile.style.color = '#007bff';

        tabProfile1.style.backgroundColor = '#f8f9fa';
        tabProfile1.style.color = '#007bff';
        
        tabProfile2.style.backgroundColor = '#f8f9fa';
        tabProfile2.style.color = '#007bff';

      } else if (homeActive == 1){
        tabHome.style.backgroundColor = '#f8f9fa';
        tabHome.style.color = '#007bff';

        tabProfile.style.backgroundColor = '#007bff';
        tabProfile.style.color = '#fff';

        tabProfile1.style.backgroundColor = '#f8f9fa';
        tabProfile1.style.color = '#007bff';
        
        tabProfile2.style.backgroundColor = '#f8f9fa';
        tabProfile2.style.color = '#007bff';
      }
      else if (homeActive == 2){
        tabProfile.style.backgroundColor = '#f8f9fa';
        tabProfile.style.color = '#007bff';

        tabHome.style.backgroundColor = '#f8f9fa';
        tabHome.style.color = '#007bff';

        tabProfile1.style.backgroundColor = '#007bff';
        tabProfile1.style.color = '#fff';

        tabProfile2.style.backgroundColor = '#f8f9fa';
        tabProfile2.style.color = '#007bff';
      }
      
      else if (homeActive == 3){
        tabProfile.style.backgroundColor = '#f8f9fa';
        tabProfile.style.color = '#007bff';

        tabHome.style.backgroundColor = '#f8f9fa';
        tabHome.style.color = '#007bff';

        tabProfile1.style.backgroundColor = '#f8f9fa';
        tabProfile1.style.color = '#007bff';
        
        tabProfile2.style.backgroundColor = '#007bff';
        tabProfile2.style.color = '#fff';
      }
    }
  
    // Default warna tab
    setActiveTab(0);
  
    // buat ganti tab
    tabHome.addEventListener('click', function () {
      setActiveTab(0);
    });
  
    tabProfile.addEventListener('click', function () {
      setActiveTab(1);
    });

    tabProfile1.addEventListener('click', function () {
      setActiveTab(2);
    });
    tabProfile2.addEventListener('click', function () {
      setActiveTab(3);
    });

    window.onload = function(){
      loadAll();
    }
  </script>

@endsection
