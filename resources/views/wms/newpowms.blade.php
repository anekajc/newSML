@extends('newmasterTest')
@section('page-title', 'Penerimaan Barang')
@section('css')
{{-- Tampilan baru (gaya newSML / purchasing purchaseOrder). Hanya visual: id, onclick, dan
     alur halaman tidak berubah. Tab tetap seperti semula. --}}
<link rel="stylesheet" href="{!! URL::asset('css/scrollbar-autohide.css') !!}?v={{ @filemtime(public_path('css/scrollbar-autohide.css')) ?: '1' }}">
<link rel="stylesheet" href="{!! URL::asset('css/master-list.css') !!}?v={{ @filemtime(public_path('css/master-list.css')) ?: '1' }}">
<link rel="stylesheet" href="{!! URL::asset('css/wms-tampilan-baru.css') !!}?v={{ @filemtime(public_path('css/wms-tampilan-baru.css')) ?: '1' }}">
{{-- Tab + tabel daftar disamakan dengan newSML purchasing/newpo.blade.php (menu yang sama).
     Tanpa filter tanggal dan tanpa geser/sembunyikan kolom (report-table.js). --}}
<style>
  .po-table-wrap table.dataTable thead th,
  .po-table-wrap table.dataTable tbody td {
    white-space: nowrap;
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

  #content { padding-top: 12px; }

  /* Kartu: di newSML gayanya datang dari rule .card global layout-nya (latar putih, sudut 12px,
     garis 1.5px); layout ajcwms memakai .card Bootstrap, jadi ditulis di sini untuk #page1. */
  #page1 .card {
    display: block !important;
    align-items: stretch !important;
    padding: 0 !important;
    text-align: left !important;
    cursor: default !important;
    background: var(--white, #fff);
    border-radius: var(--radius, 12px);
    border: 1.5px solid var(--border, #e5e7eb);
  }

  #page1 .card.tab-card { border: none !important; }

  /* ---------- Gaya dasar tabel daftar (.data-table) - di newSML ada di layout-nya ---------- */
  #page1 .data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
  }

  #page1 .data-table thead th {
    background: #f9fafb;
    padding: 11px 16px;
    text-align: left;
    font-weight: 600;
    font-size: 12px;
    color: var(--text-muted, #6b7280);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    border-bottom: 1px solid var(--border, #e5e7eb);
  }

  #page1 .data-table tbody td {
    padding: 12px 16px;
    border-bottom: 1px solid #f3f4f6;
    color: var(--text-main, #1f2937);
  }

  #page1 .data-table tbody tr:last-child td { border-bottom: none; }
  #page1 .data-table tbody tr:hover td { background: #f9fafb; }

  /* ---------- Kolom Aksi - tombol kecil warna pastel ---------- */
  #tabel td:first-child:not([colspan]),
  #tabel_pembelian td:first-child:not([colspan]) {
    display: flex;
    gap: 4px;
    justify-content: center;
    align-items: center;
  }

  #tabel td:first-child .btn,
  #tabel_pembelian td:first-child .btn {
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
  #tabel_pembelian td:first-child .btn:hover {
    filter: brightness(0.97);
    transform: translateY(-1px);
  }

  #tabel td:first-child .btn-success, #tabel_pembelian td:first-child .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
  #tabel td:first-child .btn-warning, #tabel_pembelian td:first-child .btn-warning { color: #b45309; border-color: #fbe3bd; background: #fef3e0; }
  #tabel td:first-child .btn-primary, #tabel_pembelian td:first-child .btn-primary { color: #2563eb; border-color: #cfdcff; background: #e8edff; }
  #tabel td:first-child .btn-danger,  #tabel_pembelian td:first-child .btn-danger  { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }
  #tabel td:first-child .btn-info,    #tabel_pembelian td:first-child .btn-info    { color: #0891b2; border-color: #a5f3fc; background: #ecfeff; }

  /* Tombol di kolom Actions baru muncul saat barisnya di-hover. */
  table.data-table.po-aksi-hover tbody td:first-child .btn {
    visibility: hidden;
    opacity: 0;
    transition: opacity .12s ease;
  }
  table.data-table.po-aksi-hover tbody tr:hover td:first-child .btn {
    visibility: visible;
    opacity: 1;
  }

  /* Dropdown "Tampilkan" (jumlah baris per halaman) di toolbar. */
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
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%231D2130' stroke-width='2.5'><polyline points='6 9 12 15 18 9'/></svg>");
    background-repeat: no-repeat;
    background-position: right center;
  }

  #tabel, #tabel_pembelian {
    min-width: 100%;
  }
</style>
@endsection
@section('buttons')
  <!-- <li class="nav-item">
    <button style="margin-right: 5px;" class="btn btn-primary btn-sm" type="button"   onclick="buttonAdd()" ><i class="bi bi-plus-lg"></i></button>
  </li> -->
  <!-- <li class="nav-item">
    <button style="margin-right: 5px;" class="btn btn-success btn-sm" type="button"  onclick="buttonEdit()" ><i class="bi bi-pencil"></i></button>
  </li>
  <li class="nav-item">
    <button style="margin-right: 5px;" class="btn btn-danger btn-sm " type="button"  onclick="buttonDelete()" ><i class="bi bi-trash"></i></button>
  </li> -->



@endsection



@section('content')
{{-- Judul "Penerimaan Barang" sekarang di bar atas layout (page-title). --}}



<!-- <div class="container"> -->

<div id="printContainer" style="display:none">

</div>

<div id="tempPrintContainer" style="display:none">

</div>
<div id="page1" class="container-fluid">
  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />

  <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
  <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS!!}" />
  <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
  <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />





  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <div class="card mb-3 tab-card">
    <div class="card-body">
      <div class="nav nav-tabs border-0 custom-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab"
           aria-controls="nav-home" aria-selected="true">
          Outstanding PO
        </a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab"
           aria-controls="nav-profile" aria-selected="false">
          Penerimaan Barang
        </a>
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
                  <input type="search" id="npoSearch1" class="po-search-inp" placeholder="Cari data">
                  <div class="po-len-wrap">
                    <label for="npoLen1">Tampilkan</label>
                    <select id="npoLen1" class="po-len-inp">
                      <option value="10">10</option>
                      <option value="25">25</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                      <option value="-1">Semua</option>
                    </select>
                  </div>
                </div>

                    <table id="tabel" class="data-table po-aksi-hover">
                      <thead class="text-center">
                        <tr>
                          <th scope="col">Actions</th>
                          <th scope="col">No. PO</th>
                          <th scope="col">Tanggal</th>
                          <!-- <th scope="col">Kode Supp</th> -->
                          <th scope="col">Nama Supplier</th>
                          <th scope="col">Gudang</th>
                          <th scope="col">Ekspedisi</th>
                        </tr>
                      </thead>
                      {{-- Blok lama di bawah dulu dibungkus komentar HTML, tapi Blade tetap mengeksekusinya
                           (±500 KB data PO terkirim ke browser tanpa tampil). Sekarang komentar Blade. --}}
                      {{-- <tbody id="tabel_data" class="text-right" >
                        @for ($i = 0; $i < count($po); $i++)
                        <tr id="tr{!! $i !!}" onclick="tesclick( {{ $i }} , {{ $po[$i]  }} )">
                            <td>{{ $po[$i]->NoBukti}}</td>
                            <td>{!! date("Y/m/d", strtotime($po[$i]->TANGGAL)) !!}</td>
                            <td>{{ $po[$i]->KODESUPP}}</td>
                            <td>{{ $po[$i]->NAMACUSTSUPP}}</td>
                            <td>-</td>
                            <td>-</td>
                            <td>{{ $po[$i]->KodeBrg }}</td>
                            <td>{{ $po[$i]->Urut }}</td>
                            <td>{{ $po[$i]->PartNumber }}</td>
                            <td>{{ $po[$i]->namaBrg }}</td>
                            <td>{{ number_format($po[$i]->QNTOS) }}</td>
                            <td>{{ number_format($po[$i]->QntPO) }}</td>
                            <td>{{ $po[$i]->QntPO }}</td>
                        </tr>
                        @endfor

                      </tbody> --}}

                      <tbody id="tabel_data" class="text-left" >
                        @for ($j = 0; $j < count($tempPo); $j++)
                        <tr id="tr{!! $j  !!}" onclick="">
                        {{-- <tr id="tr{!! $j  !!}" onclick="tesclick({{$j}} , {{$tempPo[$j]}})"> --}}
                        <td>
                          <button style="" class="btn btn-warning btn-sm" type="button" title="Detail" onclick="buttonDetail({{$j}}, {{$tempPo[$j]}})" ><i class="bi bi-info-lg"></i></button>
                          {{-- <button style="" class="btn btn-primary btn-sm" type="button" title="Terima" onclick="buttonAdd({{$j}}, {{$tempPo[$j]}})" ><i class="bi bi-plus"></i></button> --}}
                          <button style="width: 30px;" class="btn btn-primary btn-sm" type="button" title="Terima" onclick="buttonAdd({{$j}}, {{$tempPo[$j]}})" ><i class="bi bi-plus-lg" style="font-size: 16px;"></i></button>
                        </td>
                            <td>{{ $tempPo[$j][0]->NoBukti}}</td>
                            <td>{!! date("Y/m/d", strtotime($tempPo[$j][0]->TANGGAL)) !!}</td>
                            <!-- <td>{{ $tempPo[$j][0]->KODESUPP}}</td> -->
                            <td>{{ $tempPo[$j][0]->NAMACUSTSUPP}}</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                        @endfor

                      </tbody>


                    </table>
              </div>
            </div>
          </div>
        </div>
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
          <div class="row">
            <div class="col-md-12">
              <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                <div class="po-toolbar">
                  <input type="search" id="npoSearch2" class="po-search-inp" placeholder="Cari data">
                  <div class="po-len-wrap">
                    <label for="npoLen2">Tampilkan</label>
                    <select id="npoLen2" class="po-len-inp">
                      <option value="10">10</option>
                      <option value="25">25</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                      <option value="-1">Semua</option>
                    </select>
                  </div>
                </div>
              <table id="tabel_pembelian" class="data-table po-aksi-hover">
                <thead class="text-center">
                  <tr>
                    <th>Actions</th>
                    <th>No. Bukti</th>
                    <th>Tanggal</th>
                    <th>Nama Supplier</th>
                    <th>No. PO</th>
                    <th>Gudang</th>
                    <th>FakturSupp</th>
                  </tr>
                </thead>
                <tbody id="tabel_pembelian_data" class="text-left">
                    @for ($j = 0; $j < count($tempPembelian1); $j++)
                    <tr>
                      <td>
                        <button style="" class="btn btn-warning btn-sm" type="button" title="Detail" onclick="detailPembelian({{$j}},{{$tempPembelian1[$j]}})" ><i class="bi bi-info-lg"></i></button>
                        <button class="btn btn-success btn-sm" type="button" title="Edit" onclick="buttonEditPembelian('{{ $tempPembelian1[$j][0]->NoBukti}}')"><i class="bi bi-pen"></i></button>

                        <button style="" class="btn btn-primary btn-sm" type="button" title="Cetak" onclick="buttonPrintPembelian('{{$tempPembelian1[$j][0]->NoBukti}}')" ><i class="bi bi-printer"></i></button>
                      </td>
                      <!-- <td colspan="7">Tidak ada data po ditemukan.</td> -->
                      <td>{{ $tempPembelian1[$j][0]->NoBukti}}</td>
                      <td>{!! date("Y/m/d", strtotime($tempPembelian1[$j][0]->TANGGAL)) !!}</td>
                      <td>{{ $tempPembelian1[$j][0]->NamaSupplier}}</td>
                      <td>{{ $tempPembelian1[$j][0]->NoPO}}</td>
                      <td>{{ $tempPembelian1[$j][0]->NAMAGUDANG}}</td>
                      <td>{{ $tempPembelian1[$j][0]->FAKTURSUPP}}</td>
                    </tr>
                    @endfor
                </tbody>
                <tfoot>
                  <tr>
                    <!-- <th colspan="5">Total</th>
                    <th colspan="6"></th> -->
                  </tr>
                </tfoot>
              </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>







<!-- <div> -->



<!-- start modal add -->
<div class="modal fade wms-baru wms-baru-modal" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <!-- <h1>Tes Modal</h1> -->
        <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
        <input type="hidden" name="noUrut" id="input_add_noUrut" value="{!! csrf_token() !!}" />
        <div class="bs-form">
          <label for="input_add_nobukti">No Bukti</label>
          <input type="text" class="form-control" id="input_add_nobukti" placeholder="Surat Jln" disabled>

          <label for="input_add_tanggal">Tanggal</label>
          <input type="date" class="form-control" id="input_add_tanggal" value="{!! date('Y-m-d') !!}" >

          <label for="input_add_nomorpo">Nomor PO</label>
          <input type="text" class="form-control" id="input_add_nomorpo" placeholder="Nomor PO" required disabled>

          <label for="input_add_gudang">Gudang</label>
          <select id="input_add_gudang" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
            @foreach ($gudang as $g)
            <option value="{{ $g->KODEGDG }}">{{ $g->KODEGDG }} {{ $g->NAMA }}</option>
            @endforeach
          </select>

          <label for="input_add_suratjalansupp">Surat Jln Supp</label>
          <input type="text" autocomplete="off" class="form-control" id="input_add_suratjalansupp" placeholder="Surat Jln" required>

          <label for="input_add_nokend">No. Kend / Sopir</label>
          <input type="text" autocomplete="off" class="form-control" id="input_add_nokend" placeholder="No Kend / Sopir" required>
        </div>
      </div>
        <div class="container-fluid wms-scroll wms-modal-tabel px-4" style="overflow-x: auto;">

              <table id="addTable" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>
                    <th style="" scope="col">Terima</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col">Qty PO</th>
                    <th scope="col">Qty LPB</th>
                    <th scope="col">Qty OS</th>
                    <th scope="col">Satuan</th>
                    <th scope="col">Qty Terima</th>
                    <th scope="col">No. PO Cust</th>
                    <th scope="col">Customer</th>
                  </tr>
                </thead>


                <tbody id="addTableData" class="text-left" >
                  <tr >

                      <td class="text-center"><input class="" type="checkbox" value="" id="flexCheckDefault"></td>
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



      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
        <button type="button" class="btn btn-primary" onclick="submitAdd()">Simpan</button>
      </div>
    </div>
  </div>
</div>
</div>
<!-- End modal add-->

<!-- start modal detail -->
<div class="modal fade wms-baru wms-baru-modal" id="detail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel">Detail</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <div class="bs-form">
          <label for="detailDate">Tanggal</label>
          <input type="date" class="form-control" id="detailDate" value="{!! date('Y-m-d') !!}" disabled>

          <label for="detailSupp">Supplier</label>
          <input type="text" class="form-control" id="detailSupp" placeholder="" disabled>

          <label for="detailKodeSupp">Kode Supp</label>
          <input type="text" class="form-control" id="detailKodeSupp" placeholder="" disabled>
        </div>
      </div>
        <div class="container-fluid wms-scroll wms-modal-tabel px-4" style="overflow-x: auto;">

              <table id="detailTable" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>
                    <th scope="col">Nama Barang</th>
                    <th scope="col">Qty PO</th>
                    <th scope="col">Qty LPB</th>
                    <th scope="col">Qty OS</th>
                    <th scope="col">Satuan</th>
                    <th scope="col">No. PO Cust</th>
                    <th scope="col">Customer</th>
                  </tr>
                </thead>


                <tbody id="detailTableData" class="text-left" >
                  <tr >
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
<!-- End modal detail pembelian-->



<!-- start modal detail -->
<div class="modal fade wms-baru wms-baru-modal" id="detailPembelian" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailPembelianModalLabel">Detail</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <div class="bs-form">
          <label for="detailPembelianNoPO">No PO</label>
          <input type="text" class="form-control" id="detailPembelianNoPO" disabled>

          <label for="detailPembelianSupp">Supplier</label>
          <input type="text" class="form-control" id="detailPembelianSupp" disabled>

          <label for="detailPembelianDate">Tanggal</label>
          <input type="date" class="form-control" id="detailPembelianDate" value="{!! date('Y-m-d') !!}" disabled >

          <label for="detailPembelianFakturSupp">Keterangan</label>
          <input type="text" autocomplete="off" class="form-control" id="detailPembelianFakturSupp"   disabled >

          <label for="detailPembelianKeterangan">FakturSupp</label>
          <input type="text" autocomplete="off" class="form-control" id="detailPembelianKeterangan"   disabled >
        </div>
        <div class="container-fluid wms-scroll wms-modal-tabel px-0" style="overflow: auto">

              <table id="detailPembelianTable" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>
                    <th scope="col">Kode Barang</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Qty PO</th>
                    <th scope="col">Satuan</th>
                    <th scope="col">Qnt OUT</th>
                    <th scope="col">No. PO Customer</th>
                    <th scope="col">Customer</th>
                  </tr>
                </thead>


                <tbody id="detailPembelianTableData" class="text-left" >
                  <tr >
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
<!-- End modal detail-->

<!-- start modal editpembelian  -->
<div class="modal fade wms-baru wms-baru-modal" id="editPembelian" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                            <!-- tes max width -->
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editPembelianModalLabel">Detail</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <div class="bs-form">
          <label for="editPembelianNoPO">No PO</label>
          <input type="text" class="form-control" id="editPembelianNoPO" disabled>

          <label for="editPembelianSupp">Supplier</label>
          <input type="text" class="form-control" id="editPembelianSupp" disabled>

          <label for="editPembelianDate">Tanggal</label>
          <input type="date" class="form-control" id="editPembelianDate" value="{!! date('Y-m-d') !!}" disabled >

          <label for="editPembelianFakturSupp">Keterangan</label>
          <input type="text" autocomplete="off" class="form-control" id="editPembelianFakturSupp" >

          <label for="editPembelianKeterangan">FakturSupp</label>
          <input type="text" autocomplete="off" class="form-control" id="editPembelianKeterangan" >
        </div>

        <div class="d-flex justify-content-end mt-3" style="gap: 8px;">
          <button type="button" class="btn btn-primary" onclick="showPembelianAdd()">Add Item</button>
          <!-- <button type="button" class="btn btn-primary" onclick="resetDetailPembelian('SML/LPB/00197/0323','SML/PO/00302/0223')" class="btn btn-secondary"  >reset</button> -->
          <button type="button" class="btn btn-success" onclick="saveKetFaktur()">Save Faktur & Ket</button>
        </div>

        <div id="formPembelianAdd" class="form-card mt-3 showhide">
          <div class="form-card-title">Add Item</div>
          <div class="bs-form">
            <label for="editPembelianAddSelect">Pilih Barang</label>
            <select onchange="changeSelectBarang()" id="editPembelianAddSelect" class="form-control form-select-lg mb-3 bs-full" aria-label=".form-select-lg example">
            </select>

            <label for="editPembelianInputAddKode">Kode Barang</label>
            <input id="editPembelianInputAddKode" type="text" class="form-control" disabled>

            <label for="editPembelianInputAddNamaBarang">Nama Barang</label>
            <input id="editPembelianInputAddNamaBarang" type="text" class="form-control" disabled>

            <label for="editPembelianInputAddQty">Qty</label>
            <input id="editPembelianInputAddQty" type="number" value=0.00 class="form-control">

            <label for="editPembelianInputAddSatuan">Satuan</label>
            <input type="text" id="editPembelianInputAddSatuan" value="PCS" class="form-control" disabled>

            <label for="editPembelianInputAddQtyPO">Qty PO</label>
            <input id="editPembelianInputAddQtyPO" type="number" value=0.00 class="form-control" disabled>

            <label for="editPembelianInputAddQtyOS">Qty OS</label>
            <input id="editPembelianInputAddQtyOS" type="number" value=0.00 class="form-control" disabled>
          </div>
          <div class="d-flex justify-content-end mt-3" style="gap: 8px;">
            <button type="button" class="btn btn-secondary" onclick="buttonBatalShowHide()" >Batal</button>
            <button type="button" onclick="submitPembelianAdd()" class="btn btn-primary" >Add Item</button>
          </div>
        </div>

        <div id="formPembelianEdit" class="form-card mt-3 showhide">
          <div class="form-card-title">Edit Item</div>
          <div class="bs-form">
            <label for="editPembelianInputEditKode">Kode Barang</label>
            <input id="editPembelianInputEditKode" type="text" class="form-control" disabled>

            <label for="editPembelianInputEditNamaBarang">Nama Barang</label>
            <input id="editPembelianInputEditNamaBarang" type="text" class="form-control" disabled>

            <label for="editPembelianInputEditQty">Qty</label>
            <input id="editPembelianInputEditQty" type="number" value=0.00 class="form-control">

            <label for="editPembelianInputEditSatuan">Satuan</label>
            <input type="text" id="editPembelianInputEditSatuan" value="PCS" class="form-control" disabled>

            <label for="editPembelianInputEditQtyPO">Qty PO</label>
            <input id="editPembelianInputEditQtyPO" type="number" value=0.00 class="form-control" disabled>
          </div>
          <div class="d-flex justify-content-end mt-3" style="gap: 8px;">
            <button type="button" class="btn btn-secondary" onclick="buttonBatalShowHide()" >Batal</button>
            <button type="button" class="btn btn-success" onclick="submitPembelianEdit()" >Edit Item</button>
          </div>
        </div>

        <div class="container-fluid wms-scroll wms-modal-tabel px-0" style="overflow:auto;">

              <table id="editPembelianTable" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>
                    <th scope="col">Kode Barang</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Qty PO</th>
                    <th scope="col">Satuan</th>
                    <th scope="col">Qnt OUT</th>
                    <th scope="col">No. PO Customer</th>
                    <th scope="col">Customer</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>


                <tbody id="editPembelianTableData" class="text-left" >
                  <tr >
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
          <!-- <div class="modal-footer showhide">
            <button type="button" class="btn btn-secondary" data-dismiss="" >Batal</button>
            <button type="button" class="btn btn-primary" onclick="">Submit</button>
          </div>

          <div class="modal-footer showhide">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
            <button type="button" class="btn btn-primary" onclick="submitAdd()">Simpan</button>
          </div> -->
          <!-- <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="saveKetFaktur()">Save1</button>
          </div> -->
        </div>
    </div>
  </div>
</div>
</div>
<!-- End modal editpembelian-->

<!-- start modal editpembelian  -->
<div class="modal fade wms-baru wms-baru-modal" id="formPrint" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                            <!-- tes max width -->
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="printPembelianModalLabel">Detail</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <div class="bs-form">
          <label for="printPembelianNoPO">No PO</label>
          <input type="text" class="form-control" id="printPembelianNoPO" disabled>

          <label for="printPembelianSupp">Supplier</label>
          <input type="text" class="form-control" id="printPembelianSupp" disabled>

          <label for="printPembelianDate">Tanggal</label>
          <input type="date" class="form-control" id="printPembelianDate" value="{!! date('Y-m-d') !!}" disabled >

          <label for="printPembelianFakturSupp">Keterangan</label>
          <input type="text" class="form-control" id="printPembelianFakturSupp" autocomplete="off" disabled>

          <label for="printPembelianKeterangan">FakturSupp</label>
          <input type="text" class="form-control" id="printPembelianKeterangan" autocomplete="off" disabled>
        </div>

        <div class="container-fluid wms-scroll wms-modal-tabel px-0" style="overflow:auto;">

              <table id="printPembelianTable" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>
                    <th scope="col">v</th>
                    <th scope="col">Kode Barang</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Qty PO</th>
                    <th scope="col">Satuan</th>
                  </tr>
                </thead>


                <tbody id="printPembelianTableData" class="text-right" >
                  <tr >
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>
                </tr>

                </tbody>


              </table>
        </div>





        <div class="d-flex justify-content-end mt-3">
          <span class="wms-jumlah-print">
            <label for="input_print_jumlahprint" class="mb-0">Jumlah Print</label>
            <input type="number" value=1 class="text-right" id="input_print_jumlahprint" >
          </span>
        </div>
          </div>

          <!-- <div class="modal-footer showhide">
            <button type="button" class="btn btn-secondary" data-dismiss="" >Batal</button>
            <button type="button" class="btn btn-primary" onclick="">Submit</button>
          </div>

          <div class="modal-footer showhide">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
            <button type="button" class="btn btn-primary" onclick="submitAdd()">Simpan</button>
          </div> -->
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="submitPrint()">Print</button>
          </div>
        </div>
    </div>
  </div>
</div>
</div>
<!-- End modal editpembelian-->

<!-- start modal editpembelian  -->
<div class="modal fade" id="tes1234" tabindex="-2" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="tes1234Label">Detail</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <!-- <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" /> -->
        <h1>tes</h1>

    </div>
  </div>
</div>
</div>
<!-- End modal editpembelian-->


@endsection

@section('js')
  <script type="text/javascript">

    let row_id = "";
    let action = "";
    let row_data = {};


    // untuk edit pembelian
    let dataEditPembelianAddIndex = ""
    let dataEditPembelianAdd = [];
    let dataEditPembelianEdit = [];
    let indexEditPembelianEdit = 0;
    let dataLPB = {};
    //end untuk pembelian

    // loadall
    let dataRefreshPO = []
    let dataRefreshPembelian = []

    let dataPrint = []

    // ── Tampilan tabel daftar (disamakan dengan newSML purchasing/newpo) ──────────────
    // Urutan & columnDefs tetap seperti semula; yang ditambah hanya tampilan: kotak search +
    // "Tampilkan" di toolbar, kotak scroll .po-table-wrap, info + halaman di bawah.
    let npoPanjangHalaman = { 1: 10, 2: 10 }

    function npoOpsiTabel (urut) {
      return {
        'order': [[1, 'asc']],
        "lengthChange": false,
        "pageLength": npoPanjangHalaman[urut],
        "columnDefs": [
          { "type": "date", "targets": [2] },
          { "className": "text-center", "targets": [0] }, { "targets": [0], 'orderable': false }
        ],
        dom: "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        language: {
          emptyTable: 'Tidak ada data',
          zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
        },
        drawCallback: function () {
          // ditunda 0ms supaya DOM pagination sudah terpasang saat diukur
          setTimeout(npoAturTinggiTabel, 0)
        }
      }
    }

    // Kotak scroll tabel setinggi sisa ruang di #content, jadi yang discroll hanya isi tabel.
    function npoAturTinggiTabel () {
      let area = document.getElementById('content')
      let pane = document.querySelector('#myTabContent .tab-pane.active')
      if (!area || !pane) { return }
      let wrap = pane.querySelector('.po-table-wrap')
      if (!wrap) { return }

      wrap.style.maxHeight = 'none'

      let padBawah = parseFloat(getComputedStyle(area).paddingBottom) || 0
      let batasBawah = area.getBoundingClientRect().bottom - padBawah
      let kotak = wrap.getBoundingClientRect()
      let bawah = pane.getBoundingClientRect().bottom - kotak.bottom

      let sisa = batasBawah - kotak.top - bawah - 4
      wrap.style.maxHeight = Math.max(200, Math.floor(sisa)) + 'px'
    }

    // Kotak search & "Tampilkan" diikat sekali (tabel di-destroy + init ulang oleh loadAll()).
    function npoIkatToolbar (urut) {
      let tabelId = urut === 2 ? '#tabel_pembelian' : '#tabel'
      let input = document.getElementById('npoSearch' + urut)
      if (input && !input.dataset.rtBound) {
        input.dataset.rtBound = '1'
        input.addEventListener('input', function () {
          $(tabelId).DataTable().search(input.value).draw()
        })
      }
      let sel = document.getElementById('npoLen' + urut)
      if (sel && !sel.dataset.rtBound) {
        sel.dataset.rtBound = '1'
        sel.value = String(npoPanjangHalaman[urut])
        sel.addEventListener('change', function () {
          let n = Number(sel.value)
          npoPanjangHalaman[urut] = (n === -1 || n > 0) ? n : 10
          $(tabelId).DataTable().page.len(npoPanjangHalaman[urut]).draw()
        })
      }
      if (input && input.value) {
        $(tabelId).DataTable().search(input.value).draw()
      }
    }

    function npoInitTabel (urut) {
      let tabelId = urut === 2 ? '#tabel_pembelian' : '#tabel'
      $(tabelId).DataTable(npoOpsiTabel(urut))
      npoIkatToolbar(urut)
      npoAturTinggiTabel()
    }

    $('#nav-home-tab, #nav-profile-tab').on('shown.bs.tab', function () {
      $($.fn.dataTable.tables(true)).DataTable().columns.adjust()
      npoAturTinggiTabel()
    })

    let npoTimerResize = null
    $(window).on('resize', function () {
      if (npoTimerResize) { clearTimeout(npoTimerResize) }
      npoTimerResize = setTimeout(npoAturTinggiTabel, 150)
    })

    // Dinonaktifkan 2026-10-19: init lama tanpa halaman/toolbar - diganti npoInitTabel() di bawah
    // (urutan & columnDefs sama).
    // $(document).ready(function(){
    //   $("#tabel").DataTable({'order': [[1, 'asc']], "lengthChange": false, "paging": false , "columnDefs": [
    // { "type": "date", "targets": [2] },
    // {  "className": "text-center", "targets": [0] }, { "targets": [0] ,  'orderable' : false}
    //
    // ]});
    // });
    // $(document).ready(function(){
    //   $("#tabel_pembelian").DataTable({
    // 'order': [[1, 'asc']]
    // , "lengthChange": false, "paging": false , "columnDefs": [
    // { "type": "date", "targets": [2] },
    // {  "className": "text-center", "targets": [0] }, { "targets": [0] ,  'orderable' : false}
    // // {"order": [[ 1, 'desc' ]]},
    //
    // ]});
    // });
    $(document).ready(function(){
      npoInitTabel(1)
      npoInitTabel(2)
    });

    function detailPembelian1(index) {
      let tempDataDetailPembelian = dataRefreshPembelian[index]
      detailPembelian('x', tempDataDetailPembelian)
    }

    function detailPembelian(detail_pembelian_row_id, detail_pembelian_row_data) {


      let date = new Date(detail_pembelian_row_data[0].TANGGAL);
      // let date = new Date(detail_row_data[0].TANGGAL);
      var day = ("0" + date.getDate()).slice(-2);
      var month = ("0" + (date.getMonth() + 1)).slice(-2);
      var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
      let table_pembelian_row_detail = ""

      detail_pembelian_row_data.forEach((detail_pembelian_row, i) => {
        let satuan = ""
        if (detail_pembelian_row.Satuan) {
          satuan = detail_pembelian_row.Satuan
        }
        table_pembelian_row_detail += `<tr><td>${detail_pembelian_row.KodeBrg}</td><td>${detail_pembelian_row.namabrgx}</td><td class="text-right">${detail_pembelian_row.QntTerima}</td><td class="text-right">${detail_pembelian_row.QNTPO}</td><td>${satuan}</td><td class="text-right">${Number(detail_pembelian_row.QNTOUT )? detail_pembelian_row.QNTOUT : "0.00"}</td><td>-</td><td>-</td></tr>`
      });
      let fakturSupp = ""
      let keterangan = ""
      if (detail_pembelian_row_data[0].FAKTURSUPP) {
        fakturSupp = detail_pembelian_row_data[0].FAKTURSUPP
      }
      if (detail_pembelian_row_data[0].KETERANGAN) {
        keterangan = detail_pembelian_row_data[0].KETERANGAN
      }
      document.getElementById("detailPembelianModalLabel").innerHTML = detail_pembelian_row_data[0].NoBukti;
      document.getElementById("detailPembelianTableData").innerHTML = table_pembelian_row_detail
      document.getElementById("detailPembelianSupp").value = detail_pembelian_row_data[0].NamaSupplier
      document.getElementById("detailPembelianFakturSupp").value = fakturSupp
      document.getElementById("detailPembelianKeterangan").value =  keterangan
      document.getElementById("detailPembelianNoPO").value = detail_pembelian_row_data[0].NoPO
      // document.getElementById("detailPembelianDate").innerHTML = "Tanggal : " +  date.getFullYear() + "/" + (date.getMonth()+1) + "/" + date.getDate()
      $("#detailPembelianDate").val(date1);
      $("#detailPembelian").modal('toggle');

    }

    function tesModal() {
      //
      // $("#tes1234").modal('toggle');
      $('#formPembelianAdd').show();
    }

    function showPembelianAdd() {
      let akses = $("#akses_istambah").val();

      if (!Number(akses)) {
        alertify.warning('No access')
        return
      }
      $('.showhide').hide();
      console.log(dataEditPembelianAddIndex , "?")
      dataEditPembelianAddIndex = ""
      console.log(dataEditPembelianAddIndex , "!")

      document.getElementById("editPembelianAddSelect").value = ""
      document.getElementById("editPembelianInputAddKode").value = ""
      document.getElementById("editPembelianInputAddNamaBarang").value = ""
      document.getElementById("editPembelianInputAddQtyPO").value = "0.00"
      document.getElementById("editPembelianInputAddSatuan").value = ""
      document.getElementById("editPembelianInputAddQty").value = "0.00"
      $('#formPembelianAdd').show();
    }
    function showPembelianEdit(indexBarang) {
      let akses = $("#akses_iskoreksi").val();

      if (!Number(akses)) {
        alertify.warning('No access')
        return
      }
      console.log(indexBarang,'zxc')
      $('.showhide').hide();
      indexEditPembelianEdit = indexBarang
      console.log(dataEditPembelianEdit[indexBarang])
      console.log(dataEditPembelianEdit[indexBarang].pQC)
      if (dataEditPembelianEdit[indexBarang].pQC) {
        alertify.warning("data sudah di qc");

        return
      }
      document.getElementById("editPembelianInputEditKode").value = dataEditPembelianEdit[indexBarang].KodeBrg
      document.getElementById("editPembelianInputEditNamaBarang").value = dataEditPembelianEdit[indexBarang].namabrgx
      document.getElementById("editPembelianInputEditQtyPO").value = dataEditPembelianEdit[indexBarang].QNTPO
      document.getElementById("editPembelianInputEditSatuan").value = dataEditPembelianEdit[indexBarang].Satuan
      document.getElementById("editPembelianInputEditQty").value = dataEditPembelianEdit[indexBarang].Qnt
      $('#formPembelianEdit').show();
      document.getElementById('formPembelianEdit').scrollIntoView();
    }

    function buttonBatalShowHide() {
      $('.showhide').hide();
    }

    function loadAll1 () {
      console.log('tes123')
      $.ajax({
        url: "{!! url('tesnewpassword') !!}",
        type: "get",
        async: false,
        data: {password: 'asdtes'},
        success: function(res) {
          console.log(res)
          // console.log(res , "RESPOND PO");
          // console.log(res[0])

        }
      })
    }

    function loadAll () {
      // tabel_pembelian


      $.ajax({
        url: "{!! url('getAksesNewPOwms') !!}",
        type: "get",
        async: false,
        success: function(res) {
          console.log(res)
          document.getElementById("akses_istambah").value = res.ISTAMBAH
          document.getElementById("akses_iskoreksi").value = res.ISKOREKSI
          document.getElementById("akses_iscetak").value = res.ISCETAK
          document.getElementById("akses_ishapus").value = res.ISHAPUS
        }
      })

      //po
      $('#tabel').DataTable().destroy();
      console.log('tes')
      $.ajax({
        url: "{!! url('getAllPOwms') !!}",
        type: "get",
        async: false,
        success: function(res) {
          console.log(res , "RESPOND PO");
          // console.log(res[0])
          dataRefreshPO = res
          let rowTabel = ""
          res.forEach((item, i) => {
            let date1 = ""
            if (item[0].TANGGAL) {
              let date = new Date(item[0].TANGGAL);
              let day = ("0" + date.getDate()).slice(-2);
              let month = ("0" + (date.getMonth() + 1)).slice(-2);
              date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
            }
            let NoBukti = ""
            let KODESUPP = ""
            let NAMACUSTSUPP = ""
            if (item[0].NoBukti) {
              NoBukti = item[0].NoBukti
            }
            if (item[0].KODESUPP) {
              KODESUPP = item[0].KODESUPP
            }
            if (item[0].NAMACUSTSUPP) {
              NAMACUSTSUPP = item[0].NAMACUSTSUPP
            }
            // Dinonaktifkan 2026-10-20: tombol + terlalu kecil (bi-plus) - diganti ukuran seperti master Barang.
            // rowTabel += `<tr><td><button style="" class="btn btn-warning btn-sm" type="button" title="Detail" onclick="buttonDetail1(${i})" ><i class="bi bi-info-lg"></i></button> <button style="" class="btn btn-primary btn-sm" type="button" title="Terima" onclick="buttonAdd1(${i})" ><i class="bi bi-plus"></i></button></td><td>${NoBukti}</td><td>${date1}</td><td>${NAMACUSTSUPP}</td><td>-</td><td>-</td></tr>`
            rowTabel += `<tr><td><button style="" class="btn btn-warning btn-sm" type="button" title="Detail" onclick="buttonDetail1(${i})" ><i class="bi bi-info-lg"></i></button> <button style="width: 30px;" class="btn btn-primary btn-sm" type="button" title="Terima" onclick="buttonAdd1(${i})" ><i class="bi bi-plus-lg" style="font-size: 16px;"></i></button></td><td>${NoBukti}</td><td>${date1}</td><td>${NAMACUSTSUPP}</td><td>-</td><td>-</td></tr>`

          });
          document.getElementById("tabel_data").innerHTML = rowTabel
        }
      })






    //     $("#tabel").DataTable({'order': [[1, 'asc']], "lengthChange": false, "paging": false , "columnDefs": [
    //   { "type": "date", "targets": [2] },
    //   {  "className": "text-center", "targets": [0] ,  'orderable' : false},
    //
    // ]});
        npoInitTabel(1)

      //pembelian
      $('#tabel_pembelian').DataTable().destroy();

      $.ajax({
        url: "{!! url('getAllPembelianwms') !!}",
        type: "get",
        async: false,
        success: function(res) {
          console.log(res , "RESPOND PEM");
          // console.log(res[0])
          dataRefreshPembelian = res
          let rowTabel = ""
          res.forEach((item, i) => {



            let date1 = ""
            if (item[0].TANGGAL) {
              let date = new Date(item[0].TANGGAL);
              let day = ("0" + date.getDate()).slice(-2);
              let month = ("0" + (date.getMonth() + 1)).slice(-2);
              date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
            }
            let NoBukti = ""
            let NamaSupplier = ""
            let NoPO = ""
            let NAMAGUDANG = ""
            let FAKTURSUPP = ""
            if (item[0].NoBukti) {
              NoBukti = item[0].NoBukti
            }
            if (item[0].NamaSupplier) {
              NamaSupplier = item[0].NamaSupplier
            }
            if (item[0].NoPO) {
              NoPO = item[0].NoPO
            }
            if (item[0].NAMAGUDANG) {
              NAMAGUDANG = item[0].NAMAGUDANG
            }
            if (item[0].FAKTURSUPP) {
              FAKTURSUPP = item[0].FAKTURSUPP
            }
            rowTabel += `<tr>
            <td>
            <button style="" class="btn btn-warning btn-sm" type="button"   onclick="detailPembelian1(${i})" ><i class="bi bi-info-lg"></i></button>
            <button class="btn btn-success btn-sm" type="button" onclick="buttonEditPembelian1('${NoBukti}')"><i class="bi bi-pen"></i></button>
            <button style="" class="btn btn-primary btn-sm" type="button"   onclick="buttonPrintPembelian('${NoBukti}')" ><i class="bi bi-printer"></i></button>
            </td>
            <td>${NoBukti}</td><td>${date1}</td><td>${NamaSupplier}</td><td>${NoPO}</td><td>${NAMAGUDANG}</td><td>${FAKTURSUPP}</td></tr>`
          });

          document.getElementById("tabel_pembelian_data").innerHTML = rowTabel
        }
      })
      // @for ($j = 0; $j < count($tempPembelian1); $j++)
      // <tr>
      //   <!-- <td colspan="7">Tidak ada data po ditemukan.</td> -->
      //   <td>{{ $tempPembelian1[$j][0]->NoBukti}}</td>
      //   <td>{!! date("Y/m/d", strtotime($tempPembelian1[$j][0]->TANGGAL)) !!}</td>
      //   <td>{{ $tempPembelian1[$j][0]->NamaSupplier}}</td>
      //   <td>{{ $tempPembelian1[$j][0]->NoPO}}</td>
      //   <td>{{ $tempPembelian1[$j][0]->NAMAGUDANG}}</td>
      //   <td>{{ $tempPembelian1[$j][0]->FAKTURSUPP}}</td>
      //   <td>
          // <button style="" class="btn btn-warning btn-sm" type="button"   onclick="detailPembelian({{$j}},{{$tempPembelian1[$j]}})" ><i class="bi bi-info-lg"></i></button>
          // <button class="btn btn-success btn-sm" type="button" onclick="buttonEditPembelian({{$j}},{{$tempPembelian1[$j]}})"><i class="bi bi-pen"></i></button>
      //   </td>
      // </tr>
      // @endfor

    //   $("#tabel_pembelian").DataTable({
    // 'order': [[1, 'asc']]
    // , "lengthChange": false, "paging": false , "columnDefs": [
    // { "type": "date", "targets": [2] },
    // {  "className": "text-center", "targets": [0] ,  'orderable' : false},
    // // {"order": [[ 1, 'desc' ]]},
    //
    // ]});
      npoInitTabel(2)



    }

    function resetDetailPembelian (noBukti, noPO) {
      // SML/LPB/00197/0323
      // SML/PO/00302/0223
      let _token = $("#_token").val();
      console.log(noBukti, noPO)
      $('.showhide').hide();

      $.ajax({
        url: "{!! url('detailPembelianwms') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          NoBukti: noBukti
        },
        success: function(res) {
          console.log('=================1111===============!!!!!!')
          console.log(dataLPB, "breset")
          console.log(dataEditPembelianEdit)
          // console.log(res)
          // console.log(res[1])
          // console.log(res[0] ,'i0')

          console.log('=================22222===============!!!!!!')
          dataEditPembelianEdit = res[0]
          dataLPB = res[0][0]
          console.log(dataLPB,'areset')
          console.log(dataEditPembelianEdit)
          console.log('=================33333===============!!!!!!')
        }
      })

      if (!dataEditPembelianEdit) {
        console.log('=================44444===============!!!!!!')
        // window.location.href = "newpo";
        // editPembelian
        $("#editPembelian").modal('toggle');


      } else {
        let date = new Date(dataLPB.TANGGAL);
        var day = ("0" + date.getDate()).slice(-2);
        var month = ("0" + (date.getMonth() + 1)).slice(-2);
        var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;

        let table_row_edit_pembelian = ""
        dataEditPembelianEdit.forEach((r, i) => {
          console.log(r)
          if (r.Satuan) {
            satuan = r.Satuan
          }
          table_row_edit_pembelian += `<tr><td>${r.KodeBrg}</td><td>${r.namabrgx}</td><td class="text-right">${r.Qnt}</td><td class="text-right">${r.QNTPO}</td><td>${satuan}</td><td class="text-right">${r.QNTOUT}</td><td>-</td><td>-</td><td class="text-center"><button class="btn btn-success btn-sm" type="button" onclick="showPembelianEdit(${i})"><i class="bi bi-pen"></i></button><button style="" class="btn btn-danger btn-sm" type="button" onclick="submitPembelianDelete(${i})" ><i class="bi bi-trash"></i></button></td></tr>`
          });
        document.getElementById("editPembelianModalLabel").innerHTML = "Edit " +  dataLPB.NoBukti;
        document.getElementById("editPembelianNoPO").value = dataLPB.NoPO
        document.getElementById("editPembelianSupp").value = dataLPB.NamaSupplier
        document.getElementById("editPembelianFakturSupp").value = dataLPB.FAKTURSUPP
        document.getElementById("editPembelianKeterangan").value = dataLPB.KETERANGAN
        document.getElementById("editPembelianTableData").innerHTML = table_row_edit_pembelian

        $.ajax({
          url: "{!! url('detailPOwms') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            NoPO:  noPO,
            NoBukti: noBukti,
          },
          success: function(res) {
            console.log('======detailPO success====')
            console.log(res,'1')
            dataEditPembelianAdd = res
            console.log(dataEditPembelianAdd,'2')
          }
        })
        editPembelianSelect = ""
        console.log(dataEditPembelianAdd,'3')
        if (dataEditPembelianAdd.length) {
          editPembelianSelect += `<option value="" selected disabled>-- Pilih Barang --</option>`
          dataEditPembelianAdd.forEach((data, i) => {
            editPembelianSelect += `<option value="${i}">${data.KodeBrg} - ${data.namaBrg}</option>`
          });
        } else {
          editPembelianSelect += `<option value="" selected disabled>Tidak ada barang untuk ditambah</option>`
        }

        document.getElementById("editPembelianAddSelect").innerHTML = editPembelianSelect


        $('#editPembelianDate').val(date1)
      }



    }

    function submitPrint () {
      console.log('submitPrint')


        let printContent1 = ""
        let jumlahPrint = $(`#input_print_jumlahprint`).val();
        let z= 0
        dataPrint.forEach((item, i) => {
          console.log('item print', item)

          let date = new Date(item.TANGGAL);
          var day = ("0" + date.getDate()).slice(-2);
          var month = ("0" + (date.getMonth() + 1)).slice(-2);
          var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;

          if (document.getElementById(`printChecklist${i}`).checked) {
            // qrcodeprint
            let printContent = document.getElementById(`tempqrcodeprint${i}`).innerHTML;
            let printContent2 = document.getElementById(`tempqrcodeprintkodebrg${i}`).innerHTML;
            printContent1 += `<div style="margin: 0px; padding: 0px ; text-align: center">`

            for (let i=0; i < jumlahPrint ; i++ ) {

              if (z == 0) {
                printContent1 += `<div style="break-inside: avoid; width:7.5cm; height: 4.5cm; padding-right: 3px">`
              } else {
                printContent1 += `<div style="break-inside: avoid; width:7.5cm; height: 4.5cm; page-break-before: always; margin-top: 10px; padding-right: 3px">`
              }

              // printContent1 += `<div style="break-inside: avoid; width:7.5cm; height: 4.5cm; page-break-after: always">`
              printContent1 += `<div  style="display: flex; flex-direction: column; margin-top: -25px" >`
              printContent1 += `<div class="" style="display: flex; flex-direction: row; text-align: center,">`
              printContent1 += `<div style="width:70%; padding-left: 5px; text-align: left"><h6 style=""> ${item.NoBukti}</h6> </div>`
              // printContent1 += `<div style="width:40%" ></div>`
              printContent1 += `<div style=" width:30%; padding-right: 8px; margin-right: 10px" > <h6>${item.KodeBrg} </h6> </div>`
              printContent1 += `</div>`

              printContent1 += `<div class="" style="display: flex; flex-direction: row;margin-top: -20px">`
              printContent1 += `<div  style="width:70%; padding-left: 5px">` + printContent +`</div>`
              // printContent1 += `<div style="width:10%">` + `</div>`
              printContent1 += `<div style=" width:30%; padding-right: 18px">`+ printContent2 +`</div>`
              printContent1 += `</div>`

              printContent1 += `</div>`
              printContent1 += `<div style="width: 7.5cm; text-align: center ; margin-top: -15px">`
              printContent1 += `<h6 style="font-weight: bold; font-size: 10px;padding-right: 5px">${item.namabrgx} ${ item.NAMAMERK ? ' / ' + item.NAMAMERK : '' }</h6>`
              printContent1 += `</div>`



              printContent1 += `<div style="margin-top: -25px; text-align: center">`
              printContent1 += `<h6>${date1} / ${item.QntTerima} ${item.Satuan}</h6>`
              printContent1 += `</div>`
              printContent1 += `</div>`
              // printContent1 += `<div style=' page-break-before: always'></div>`
              z++
            }
            printContent1 += '</div>'
          }
        });

        // printContent1 += `<div ><div style="flex: 3" >${item.NoBukti}</div><div style="flex: 3"></div><div style="flex: 3" >${item.KodeBrg}</div></div>`
        // printContent1 += `<div  >`
        // printContent1 += `<div style="display: flex">`
        // printContent1 += printContent
        // printContent1 += `</div>`
        // printContent1 += `<div class="col-2">asd</div>`
        // printContent1 += `<div >`
        // printContent1 += printContent
        // printContent1 += `</div>`
        // printContent1 += `</div>`
        // printContent1 += `<div ><div class="mt-4">${item.NoBukti}.${item.Urut}.${item.KodeBrg}</div></div>`


        document.getElementById("printContainer").innerHTML = printContent1
        w=window.open(' ');

        w.document.write($(`#printContainer`).html());
        w.print();
        w.close();


        // console.log(idBarcode , idJumlah)
        // let printContent = document.getElementById(`${idBarcode}`).innerHTML;
        // let jumlahPrint = $(`#${idJumlah}`).val();
        // console.log(jumlahPrint)
        // let printContent1 = ""
        // for (let i=0; i < jumlahPrint ; i++ ) {
        //   printContent1 += `<div class="row">`
        //   printContent1 += printContent
        //   printContent1 += `</div>`
        // }
        // document.getElementById("printContainer").innerHTML = printContent1
        // w=window.open(' ');
        //
        // w.document.write($(`#printContainer`).html());
        // w.print();
        // w.close();

    }

    function buttonPrintPembelian (nobukti) {
      let akses = $("#akses_iscetak").val();

      if (!Number(akses)) {
        alertify.warning('No access')
        return
      }
      console.log('buttonPrintPembelian')
      console.log(nobukti)

      let _token = $("#_token").val();

      $.ajax({
        url: "{!! url('detailPembelianwms') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          NoBukti: nobukti
        },
        success: function(res) {
          console.log(res)

          dataPrint = res[0]
          console.log(res[0])
          console.log(res[0][0])
          console.log(res[0][0].pQC)

        }
      })

      console.log(dataPrint)
      console.log(dataPrint[0])
      if (dataPrint[0].pQC === '1') {



 



        let date = new Date(dataPrint[0].TANGGAL);
        var day = ("0" + date.getDate()).slice(-2);
        var month = ("0" + (date.getMonth() + 1)).slice(-2);
        var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;

        let rowTable = ""
        let rowTableTemp = ""
        dataPrint.forEach((r, i) => {
          console.log(r)
          let satuan = ""
          if (r.Satuan) {
            satuan = r.Satuan
          }
          rowTable += `<tr><td>
          <div class="form-check text-center">
              <input id="printChecklist${i}" class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
              </div></td><td>${r.KodeBrg}</td><td>${r.namabrgx}</td><td>${r.QntTerima}</td><td>${r.QNTPO}</td><td>${satuan}</td></tr>`
          rowTableTemp += `
          <div id="tempqrcodeprint${i}" style="width: 10px; height: 10px"></div>
          <div id="tempqrcodeprintkodebrg${i}" style="width: 10px; height: 10px"></div>
          `

          });
        document.getElementById("printPembelianModalLabel").innerHTML = "Print " +  dataPrint[0].NoBukti;
        document.getElementById("printPembelianNoPO").value = dataPrint[0].NoPO
        document.getElementById("printPembelianSupp").value = dataPrint[0].NamaSupplier
        document.getElementById("printPembelianFakturSupp").value = dataPrint[0].FAKTURSUPP
        document.getElementById("printPembelianKeterangan").value = dataPrint[0].KETERANGAN
        document.getElementById("printPembelianTableData").innerHTML = rowTable
        document.getElementById("tempPrintContainer").innerHTML = rowTableTemp
        dataPrint.forEach((item, i) => {
          // new QRCode(document.getElementById(`qrcodeprint${i}`), `${item.NOBUKTI}${item.KODEBRG}`);
          new QRCode(document.getElementById(`tempqrcodeprint${i}`), {text: `${item.NoBukti}.${item.Urut}.${item.KodeBrg}` , width: 90, height: 90});
          new QRCode(document.getElementById(`tempqrcodeprintkodebrg${i}`), {text: `${item.KodeBrg}` , width: 90, height: 90});
        });
        $("#formPrint").modal('toggle')

      } else {
        alertify.warning('Data belum di QC')
      }

    }

    function submitPembelianDelete(index) {
      let akses = $("#akses_ishapus").val();

      if (!Number(akses)) {
        alertify.warning('No access')
        return
      }


      alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + '?',
          function() {
            console.log(dataEditPembelianEdit[index])
            // console.log('1')
            // return
            let _token = $("#_token").val();
            let choice = "D"
            let dataLPBDelete = dataEditPembelianEdit[index]

            console.log(dataLPBDelete.pQC)
            if (dataLPBDelete.pQC) {
              alertify.warning("data sudah di qc");

              return
            }
            let reqNoBukti = dataLPBDelete.NoBukti
            let reqNoUrut = dataLPBDelete.NoUrut
            let reqTANGGAL = dataLPBDelete.TANGGAL
            let reqKodeSupp = dataLPBDelete.KODESUPP
            let reqKodeGudang = dataLPBDelete.KODEGDG
            let reqNoPO = dataLPBDelete.NoPO
            let reqKeterangan = dataLPBDelete.KETERANGAN
            let reqFakturSupp = dataLPBDelete.FAKTURSUPP
            //
            let reqUrut = dataLPBDelete.Urut
            let reqKodeBarang = dataLPBDelete.KodeBrg
            let reqUrutPO = dataLPBDelete.UrutPO
            let reqQtyTerima = dataLPBDelete.Qnt
            let reqNoSat = dataLPBDelete.NoSat
            let reqSatuan = dataLPBDelete.Satuan
            let reqIsi = dataLPBDelete.Isi
            let reqQtyTerima1 = 0
            let reqQtyTerima2 = 0
            if (dataLPBDelete.NoSat == 1) {
              reqQtyTerima1 = reqQtyTerima;
              reqQtyTerima2 = reqQtyTerima / dataLPBDelete.ISI2;
            } else if (dataLPBDelete.NoSat == 2) {
              reqQtyTerima1 = reqQtyTerima * dataLPBDelete.ISI2;
              reqQtyTerima2 = reqQtyTerima;
            }
            let reqNamaBarang = dataLPBDelete.namabrgx
            let reqNoBatch = ""
            let reqQtyReject = 0
            let reqQtyReject1 = 0
            let reqQtyReject2 = 0
            let reqPBeliJasa = 0
            let reqEd = null

            $.ajax({
              url: "{!! url('sp_beligudangwms') !!}",
              type: "post",
              async: false,
              data: {
                _token : _token,
                // data: data,
                // choice: choice,
                // qtyTerima: qtyTerima,
                // dataLPB: dataLPB
                choice,
                reqNoBukti,
                reqNoUrut,
                reqTANGGAL,
                reqKodeSupp,
                reqKodeGudang,
                reqNoPO,
                reqKeterangan,
                reqFakturSupp,
                reqUrut,
                reqKodeBarang,
                reqUrutPO,
                reqQtyTerima ,
                reqNoSat,
                reqSatuan,
                reqIsi,
                reqQtyTerima1,
                reqQtyTerima2,
                reqNamaBarang,
                reqNoBatch,
                reqQtyReject,
                reqQtyReject1,
                reqQtyReject2,
                reqPBeliJasa,
                reqEd,
              },
              success: function(res) {
                // console.log(res , "RESPOND");
                console.log(res)
                console.log("del successsssssssssssssssssssss")
                loadAll()
                resetDetailPembelian(reqNoBukti,reqNoPO)
                alertify.success('Item telah dihapus');
              }
            })

          }
        ,function(){
          console.log('no')
        });





    }

    function saveKetFaktur () {
      let akses = $("#akses_iskoreksi").val();

      if (!Number(akses)) {
        alertify.warning('No access')
        return
      }
      let _token = $("#_token").val();
      let choice = "F"
      console.log(dataEditPembelianEdit[0])
      // console.log(dataLPBEdit)
      dataLPBEdit = dataEditPembelianEdit[0]
      // editPembelianFakturSupp
      // editPembelianKeterangan
      let reqNoBukti = dataLPBEdit.NoBukti
      let reqNoUrut = dataLPBEdit.NoUrut
      let reqTANGGAL = dataLPBEdit.TANGGAL
      let reqKodeSupp = dataLPBEdit.KODESUPP
      let reqKodeGudang = dataLPBEdit.KODEGDG
      let reqNoPO = dataLPBEdit.NoPO
      let reqKeterangan = $("#editPembelianKeterangan").val()
      let reqFakturSupp = $("#editPembelianFakturSupp").val()
      //
      let reqUrut = dataLPBEdit.Urut
      let reqKodeBarang = dataLPBEdit.KodeBrg
      let reqUrutPO = dataLPBEdit.UrutPO
      let reqQtyTerima = dataLPBEdit.Qnt
      let reqNoSat = dataLPBEdit.NoSat
      let reqSatuan = dataLPBEdit.Satuan
      let reqIsi = dataLPBEdit.Isi
      let reqQtyTerima1 = 0
      let reqQtyTerima2 = 0
      if (dataLPBEdit.NoSat == 1) {
        reqQtyTerima1 = reqQtyTerima;
        reqQtyTerima2 = reqQtyTerima / dataLPBEdit.ISI2;
      } else if (dataLPBEdit.NoSat == 2) {
        reqQtyTerima1 = reqQtyTerima * dataLPBEdit.ISI2;
        reqQtyTerima2 = reqQtyTerima;
      }
      let reqNamaBarang = dataLPBEdit.namabrgx
      let reqNoBatch = ""
      let reqQtyReject = 0
      let reqQtyReject1 = 0
      let reqQtyReject2 = 0
      let reqPBeliJasa = 0
      let reqEd = null

      $.ajax({
        url: "{!! url('sp_beligudangwms') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          // data: data,
          // choice: choice,
          // qtyTerima: qtyTerima,
          // dataLPB: dataLPB
          choice,
          reqNoBukti,
          reqNoUrut,
          reqTANGGAL,
          reqKodeSupp,
          reqKodeGudang,
          reqNoPO,
          reqKeterangan,
          reqFakturSupp,
          reqUrut,
          reqKodeBarang,
          reqUrutPO,
          reqQtyTerima ,
          reqNoSat,
          reqSatuan,
          reqIsi,
          reqQtyTerima1,
          reqQtyTerima2,
          reqNamaBarang,
          reqNoBatch,
          reqQtyReject,
          reqQtyReject1,
          reqQtyReject2,
          reqPBeliJasa,
          reqEd,
        },
        success: function(res) {
          // console.log(res , "RESPOND");
          console.log(res)
          console.log("successsssssssssssssssssssss")
          loadAll()
          resetDetailPembelian(reqNoBukti,reqNoPO)
          alertify.success('Faktur dan keterangan telah diupdate');
        }
      })

      console.log(reqFakturSupp,reqKeterangan)

      // console.log(choice,
      // reqNoBukti,
      // reqNoUrut,
      // reqTANGGAL,
      // reqKodeSupp,
      // reqKodeGudang,
      // reqNoPO,
      // reqKeterangan,
      // reqFakturSupp,
      // reqUrut,
      // reqKodeBarang,
      // reqUrutPO,
      // reqQtyTerima ,
      // reqNoSat,
      // reqSatuan,
      // reqIsi,
      // reqQtyTerima1,
      // reqQtyTerima2,
      // reqNamaBarang,
      // reqNoBatch,
      // reqQtyReject,
      // reqQtyReject1,
      // reqQtyReject2,
      // reqPBeliJasa,
      // reqEd)

      // document.getElementById("editPembelianInputEditKode").value = dataEditPembelianEdit[indexBarang].KodeBrg
      // document.getElementById("editPembelianInputEditNamaBarang").value = dataEditPembelianEdit[indexBarang].NamaBrg
      // document.getElementById("editPembelianInputEditQtyPO").value = dataEditPembelianEdit[indexBarang].QNTPO
      // document.getElementById("editPembelianInputEditSatuan").value = dataEditPembelianEdit[indexBarang].Satuan
      // document.getElementById("editPembelianInputEditQty").value = dataEditPembelianEdit[indexBarang].Qnt
    }

    function submitPembelianEdit() {
      console.log(indexEditPembelianEdit, dataEditPembelianEdit[indexEditPembelianEdit])

      let _token = $("#_token").val();
      let choice = "U"
      let dataLPBEdit = dataEditPembelianEdit[indexEditPembelianEdit]
      let reqQtyTerima = $("#editPembelianInputEditQty").val()

      // console.log(dataLPBEdit.QNTOUT, dataLPBEdit.Qnt , reqQtyTerima)
      // console.log(Number(reqQtyTerima) , Number(dataLPBEdit.QNTOUT) , Number(dataLPBEdit.Qnt))
      // console.log(Number(reqQtyTerima) , (Number(dataLPBEdit.QNTOUT) + Number(dataLPBEdit.Qnt)))
      if (Number(reqQtyTerima) > (Number(dataLPBEdit.QNTOUT) + Number(dataLPBEdit.Qnt))) {
        alertify.warning("Qty melebihi Qty OUT");
        console.log('error')
        return
      }
      if (Number(reqQtyTerima) < 0) {
        alertify.warning("Qty tidak boleh negatif");
        return
      }
      // console.log(dataLPBEdit.QNTOUT, dataLPBEdit.Qnt , reqQtyTerima)
      // return
      let reqNoBukti = dataLPBEdit.NoBukti
      let reqNoUrut = dataLPBEdit.NoUrut
      let reqTANGGAL = dataLPBEdit.TANGGAL
      let reqKodeSupp = dataLPBEdit.KODESUPP
      let reqKodeGudang = dataLPBEdit.KODEGDG
      let reqNoPO = dataLPBEdit.NoPO
      let reqKeterangan = dataLPBEdit.KETERANGAN
      let reqFakturSupp = dataLPBEdit.FAKTURSUPP
      //
      let reqUrut = dataLPBEdit.Urut
      let reqKodeBarang = dataLPBEdit.KodeBrg
      let reqUrutPO = dataLPBEdit.UrutPO

      let reqNoSat = dataLPBEdit.NoSat
      let reqSatuan = dataLPBEdit.Satuan
      let reqIsi = dataLPBEdit.Isi
      let reqQtyTerima1 = 0
      let reqQtyTerima2 = 0
      if (dataLPBEdit.NoSat == 1) {
        reqQtyTerima1 = reqQtyTerima;
        reqQtyTerima2 = reqQtyTerima / dataLPBEdit.ISI2;
      } else if (dataLPBEdit.NoSat == 2) {
        reqQtyTerima1 = reqQtyTerima * dataLPBEdit.ISI2;
        reqQtyTerima2 = reqQtyTerima;
      }
      let reqNamaBarang = dataLPBEdit.namabrgx
      let reqNoBatch = ""
      let reqQtyReject = 0
      let reqQtyReject1 = 0
      let reqQtyReject2 = 0
      let reqPBeliJasa = 0
      let reqEd = null

      $.ajax({
        url: "{!! url('sp_beligudangwms') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          // data: data,
          // choice: choice,
          // qtyTerima: qtyTerima,
          // dataLPB: dataLPB
          choice,
          reqNoBukti,
          reqNoUrut,
          reqTANGGAL,
          reqKodeSupp,
          reqKodeGudang,
          reqNoPO,
          reqKeterangan,
          reqFakturSupp,
          reqUrut,
          reqKodeBarang,
          reqUrutPO,
          reqQtyTerima ,
          reqNoSat,
          reqSatuan,
          reqIsi,
          reqQtyTerima1,
          reqQtyTerima2,
          reqNamaBarang,
          reqNoBatch,
          reqQtyReject,
          reqQtyReject1,
          reqQtyReject2,
          reqPBeliJasa,
          reqEd,
        },
        success: function(res) {
          // console.log(res , "RESPOND");
          console.log(res)
          console.log("successsssssssssssssssssssss")
          loadAll()
          resetDetailPembelian(reqNoBukti,reqNoPO)
          alertify.success('Item telah diedit');
        }
      })


    }

    function submitPembelianAdd() {
      if (dataEditPembelianAddIndex === "") {
        console.log("no item")
        alertify.warning("Tidak ada item dipilih");
      } else {

        let _token = $("#_token").val();


        // console.log(dataEditPembelianAddIndex)
        let data = dataEditPembelianAdd[dataEditPembelianAddIndex]
        console.log(data)
        let reqQtyTerima = $("#editPembelianInputAddQty").val()
        if (Number(reqQtyTerima) > Number(data.OSPO)) {
          alertify.warning("Qty melebihi Qty OS");
          console.log('error')
          return
        }
        if (Number(reqQtyTerima) < 0) {
          alertify.warning("Qty tidak boleh negatif");
          return
        }

        let choice = "I"
        let reqNoBukti = dataLPB.NoBukti
        let reqNoUrut = dataLPB.NoUrut
        let reqTANGGAL = dataLPB.TANGGAL
        let reqKodeSupp = dataLPB.KODESUPP
        let reqKodeGudang = dataLPB.KODEGDG
        let reqNoPO = dataLPB.NoPO
        let reqKeterangan = dataLPB.KETERANGAN
        let reqFakturSupp = dataLPB.FAKTURSUPP
        let reqUrut = 0
        let reqKodeBarang = data.KodeBrg
        let reqUrutPO = data.Urut

        if (Number(reqQtyTerima) < 0) {
          alertify.warning("Qty tidak boleh negatif");
          return
        }
        let reqNoSat = data.nosat
        let reqSatuan = data.Satuan
        let reqIsi = data.ISI
        let reqQtyTerima1 = 0
        let reqQtyTerima2 = 0
        if (data.nosat == 1) {
          reqQtyTerima1 = reqQtyTerima;
          reqQtyTerima2 = reqQtyTerima / data.ISI2;
        } else if (data.nosat == 2) {
          reqQtyTerima1 = reqQtyTerima * data.ISI2;
          reqQtyTerima2 = reqQtyTerima;
        }
        let reqNamaBarang = ''
        let reqNoBatch = ""
        let reqQtyReject = 0
        let reqQtyReject1 = 0
        let reqQtyReject2 = 0
        let reqPBeliJasa = 0
        let reqEd = null


        console.log({
          _token : _token,
          // data: data,
          // choice: choice,
          // qtyTerima: qtyTerima,
          // dataLPB: dataLPB
          choice,
          reqNoBukti,
          reqNoUrut,
          reqTANGGAL,
          reqKodeSupp,
          reqKodeGudang,
          reqNoPO,
          reqKeterangan,
          reqFakturSupp,
          reqUrut,
          reqKodeBarang,
          reqUrutPO,
          reqQtyTerima ,
          reqNoSat,
          reqSatuan,
          reqIsi,
          reqQtyTerima1,
          reqQtyTerima2,
          reqNamaBarang,
          reqNoBatch,
          reqQtyReject,
          reqQtyReject1,
          reqQtyReject2,
          reqPBeliJasa,
          reqEd,
        })


        // return


          // console.log(editPembelianInputAddQtyPO)
          $.ajax({
            url: "{!! url('sp_beligudangwms') !!}",
            type: "post",
            async: false,
            data: {
              _token : _token,
              // data: data,
              // choice: choice,
              // qtyTerima: qtyTerima,
              // dataLPB: dataLPB
              choice,
              reqNoBukti,
              reqNoUrut,
              reqTANGGAL,
              reqKodeSupp,
              reqKodeGudang,
              reqNoPO,
              reqKeterangan,
              reqFakturSupp,
              reqUrut,
              reqKodeBarang,
              reqUrutPO,
              reqQtyTerima ,
              reqNoSat,
              reqSatuan,
              reqIsi,
              reqQtyTerima1,
              reqQtyTerima2,
              reqNamaBarang,
              reqNoBatch,
              reqQtyReject,
              reqQtyReject1,
              reqQtyReject2,
              reqPBeliJasa,
              reqEd,
            },
            success: function(res) {
              // console.log(res , "RESPOND");
              console.log(res)
              console.log("successsssssssssssssssssssss")
              loadAll()
              resetDetailPembelian(reqNoBukti,reqNoPO)
              alertify.success('Item telah diadd');
            }
          })

      }

    }

    function changeSelectBarang () {
      // sp_BeliGudang
      let indexBarang = document.getElementById("editPembelianAddSelect").value;
      dataEditPembelianAddIndex = indexBarang
      console.log(dataEditPembelianAdd[indexBarang])
      // editPembelianInputAddKode
      // editPembelianInputAddNamaBarang
      // editPembelianInputAddQtyPO
      // editPembelianInputAddQtyPO
      document.getElementById("editPembelianInputAddKode").value = dataEditPembelianAdd[indexBarang].KodeBrg
      document.getElementById("editPembelianInputAddNamaBarang").value = dataEditPembelianAdd[indexBarang].namaBrg

      document.getElementById("editPembelianInputAddQtyOS").value =  parseFloat(dataEditPembelianAdd[indexBarang].OSPO).toFixed(2)
      document.getElementById("editPembelianInputAddQtyPO").value =  parseFloat(dataEditPembelianAdd[indexBarang].QNT).toFixed(2)
      document.getElementById("editPembelianInputAddSatuan").value = dataEditPembelianAdd[indexBarang].Satuan
    }

    function buttonEditPembelian1 (nobukti) {
      // tempDataEditPembelian = dataRefreshPembelian[index]
      // buttonEditPembelian('x', tempDataEditPembelian)
      buttonEditPembelian(nobukti)
    }


        function buttonEditPembelian (nobukti) {
          $('.showhide').hide();
          console.log("qwert")
          console.log(nobukti)

          let _token = $("#_token").val();

          let edit_pembelian_row_data = []

          $.ajax({
            url: "{!! url('detailPembelianwms') !!}",
            type: "post",
            async: false,
            data: {
              _token : _token,
              NoBukti: nobukti
            },
            success: function(res) {
              console.log(res)

              edit_pembelian_row_data = res[0]
              console.log(res[0])
              console.log(res[0][0])
              console.log(res[0][0].pQC)

            }
          })

          if(!edit_pembelian_row_data.length) {
            alertify.warning('silahkan refresh browser')
            return
          }




          dataLPB =  edit_pembelian_row_data[0]
           // detailPembelianDate
          // document.getElementById("editPembelianDate").value = edit_pembelian_row_data[0].TANGGAL
          dataEditPembelianEdit = edit_pembelian_row_data
          // return

          // console.log(edit_pembelian_row_id, edit_pembelian_row_data , "<<<< edit pembelian")
          let date = new Date(edit_pembelian_row_data[0].TANGGAL);
          var day = ("0" + date.getDate()).slice(-2);
          var month = ("0" + (date.getMonth() + 1)).slice(-2);
          var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;


          if (Number(edit_pembelian_row_data[0].IsOtorisasi1) ) {
            alertify.warning('Data sudah di otorisasi')
            return
          }

          if (edit_pembelian_row_data[0].pQC) {
            alertify.warning('Data sudah di QC')
            return
          }

          let checkLockPeriode = [{
            BULAN: 0,
            TAHUN: 0
          }]
          let checkDateLockPeriode = new Date(edit_pembelian_row_data[0].TANGGAL)
          // console.log('2')
          $.ajax({
            url: "{!! url('lockperiodecheck') !!}",
            type: "post",
            async: false,
            data: {
              _token : _token,
              tahun: checkDateLockPeriode.getFullYear(),
              bulan: checkDateLockPeriode.getMonth() + 1
            },
            success: function(res) {
              console.log(res)
              checkLockPeriode = res
            }
          })

          if ( checkLockPeriode.length ) {

              alertify.warning("Periode sudah di kunci");
              return
          }


          let table_row_edit_pembelian = ""
          edit_pembelian_row_data.forEach((r, i) => {
            // console.log('r===' ,r ,'ASD')
            // console.log('SDW')
            // let QntPO_format = r.QntPO.toLocaleString();
            // console.log(r)
            // console.log(r.QntPO)
            // console.log(r.Satuan)
            let satuan = ""
            if (r.Satuan) {
              satuan = r.Satuan
            }
            table_row_edit_pembelian += `<tr><td>${r.KodeBrg}</td><td>${r.namabrgx}</td><td class="text-right">${r.Qnt}</td><td class="text-right">${r.QNTPO}</td><td>${satuan}</td><td class="text-right">${Number(r.QNTOUT) ? r.QNTOUT : "0.00"}</td><td>-</td><td>-</td><td class="text-center"><button class="btn btn-success btn-sm" type="button" onclick="showPembelianEdit(${i})"><i class="bi bi-pen"></i></button><button style="" class="btn btn-danger btn-sm" type="button" onclick="submitPembelianDelete(${i})" ><i class="bi bi-trash"></i></button></td></tr>`
            });
          document.getElementById("editPembelianModalLabel").innerHTML = "Edit " +  edit_pembelian_row_data[0].NoBukti;
          document.getElementById("editPembelianNoPO").value = edit_pembelian_row_data[0].NoPO
          document.getElementById("editPembelianSupp").value = edit_pembelian_row_data[0].NamaSupplier
          document.getElementById("editPembelianFakturSupp").value = edit_pembelian_row_data[0].FAKTURSUPP
          document.getElementById("editPembelianKeterangan").value = edit_pembelian_row_data[0].KETERANGAN
          document.getElementById("editPembelianTableData").innerHTML = table_row_edit_pembelian

          console.log(edit_pembelian_row_data[0].NoPO)
          console.log(edit_pembelian_row_data[0].NoBukti , "<<<<<<<<<<")

          $.ajax({
            url: "{!! url('detailPOwms') !!}",
            type: "post",
            async: false,
            data: {
              _token : _token,
              NoPO: edit_pembelian_row_data[0].NoPO,
              NoBukti: edit_pembelian_row_data[0].NoBukti
            },
            success: function(res) {
              console.log(res)
              dataEditPembelianAdd = res
              console.log(dataEditPembelianAdd)
            }
          })

          editPembelianSelect = ""
          if (dataEditPembelianAdd.length) {
            editPembelianSelect += `<option value="" selected disabled>-- Pilih Barang --</option>`
            dataEditPembelianAdd.forEach((data, i) => {
              editPembelianSelect += `<option value="${i}">${data.KodeBrg} - ${data.namaBrg}</option>`
            });
          } else {
            editPembelianSelect += `<option value="" selected disabled>Tidak ada barang untuk ditambah</option>`
          }

          document.getElementById("editPembelianAddSelect").innerHTML = editPembelianSelect


          console.log('date1', date1)
          $('#editPembelianDate').val(date1)
          $("#editPembelian").modal('toggle');
        }



    function buttonEditPembelianTemp (edit_pembelian_row_id, edit_pembelian_row_data) {
      $('.showhide').hide();
      console.log("qwert")
      dataLPB =  edit_pembelian_row_data[0]
       // detailPembelianDate
      // document.getElementById("editPembelianDate").value = edit_pembelian_row_data[0].TANGGAL
      dataEditPembelianEdit = edit_pembelian_row_data

      console.log(edit_pembelian_row_id, edit_pembelian_row_data , "<<<< edit pembelian")
      let date = new Date(edit_pembelian_row_data[0].TANGGAL);
      var day = ("0" + date.getDate()).slice(-2);
      var month = ("0" + (date.getMonth() + 1)).slice(-2);
      var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;

      if (edit_pembelian_row_data[0].pQC) {
        alertify.warning('Data sudah di QC')
        return
      }

      let table_row_edit_pembelian = ""
      edit_pembelian_row_data.forEach((r, i) => {
        console.log('r===' ,r ,'ASD')
        console.log('SDW')
        // let QntPO_format = r.QntPO.toLocaleString();
        // console.log(r)
        // console.log(r.QntPO)
        // console.log(r.Satuan)
        let satuan = ""
        if (r.Satuan) {
          satuan = r.Satuan
        }
        table_row_edit_pembelian += `<tr><td>${r.KodeBrg}</td><td>${r.namabrgx}</td><td>${r.Qnt}</td><td>${r.QNTPO}</td><td>${satuan}</td><td>${Number(r.QNTOUT) ? r.QNTOUT : "0.00"}</td><td>-</td><td>-</td><td class="text-center"><button class="btn btn-success btn-sm" type="button" onclick="showPembelianEdit(${i})"><i class="bi bi-pen"></i></button><button style="" class="btn btn-danger btn-sm" type="button" onclick="submitPembelianDelete(${i})" ><i class="bi bi-trash"></i></button></td></tr>`
        });
      document.getElementById("editPembelianModalLabel").innerHTML = "Edit " +  edit_pembelian_row_data[0].NoBukti;
      document.getElementById("editPembelianNoPO").value = edit_pembelian_row_data[0].NoPO
      document.getElementById("editPembelianSupp").value = edit_pembelian_row_data[0].NamaSupplier
      // let fakturSupp = ""
      // let keterangan = ""
      // if (edit_pembelian_row_data[0].FAKTURSUPP) {
      //   fakturSupp = edit_pembelian_row_data[0].FAKTURSUPP
      // }
      // if (edit_pembelian_row_data[0].KETERANGAN) {
      //   keterangan = edit_pembelian_row_data[0].KETERANGAN
      // }
      // document.getElementById("editPembelianFakturSupp").value = fakturSupp
      // document.getElementById("editPembelianKeterangan").value = keterangan
      document.getElementById("editPembelianFakturSupp").value = edit_pembelian_row_data[0].FAKTURSUPP
      document.getElementById("editPembelianKeterangan").value = edit_pembelian_row_data[0].KETERANGAN
      document.getElementById("editPembelianTableData").innerHTML = table_row_edit_pembelian

      console.log(edit_pembelian_row_data[0].NoPO)
      console.log(edit_pembelian_row_data[0].NoBukti , "<<<<<<<<<<")
      let _token = $("#_token").val();
      $.ajax({
        url: "{!! url('detailPOwms') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          NoPO: edit_pembelian_row_data[0].NoPO,
          NoBukti: edit_pembelian_row_data[0].NoBukti
        },
        success: function(res) {
          // console.log(res , "RESPOND");
          console.log(res)
          dataEditPembelianAdd = res
          console.log(dataEditPembelianAdd)
        }
      })
      // console.log(dataEditPembelianAdd , "+")
      // editPembelianAddKode
      editPembelianSelect = ""
      if (dataEditPembelianAdd.length) {
        editPembelianSelect += `<option value="" selected disabled>-- Pilih Barang --</option>`
        dataEditPembelianAdd.forEach((data, i) => {
          editPembelianSelect += `<option value="${i}">${data.KodeBrg} - ${data.namaBrg}</option>`
        });
      } else {
        editPembelianSelect += `<option value="" selected disabled>Tidak ada barang untuk ditambah</option>`
      }

      document.getElementById("editPembelianAddSelect").innerHTML = editPembelianSelect

      // $.ajax({
      //   url: "{!! url('deletenewmenu') !!}",
      //   type: "POST",
      //   async: false,
      //   data: {
      //     _token : _token,
      //     KODEMENU: kodemenu
      //   },
      //   success: function(res) {
      //     alertify.success('Menu telah dihapus.');
      //     window.location.href = "newmenu";
      //   }
      // })

      $('#editPembelianDate').val(date1)
      $("#editPembelian").modal('toggle');
    }
    function buttonDetail1(index) {
      let tempDataDetail = dataRefreshPO[index]
      buttonDetail('x' , tempDataDetail)
    }

    function buttonDetail(detail_row_id, detail_row_data ) {
      console.log(detail_row_id, detail_row_data ,  "<<< detail")
      console.log('================')
      // let date = new Date(detail_row_data[0].TANGGAL);
      let date = new Date(detail_row_data[0].TANGGAL);
      var day = ("0" + date.getDate()).slice(-2);
      var month = ("0" + (date.getMonth() + 1)).slice(-2);
      var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;

      let table_row_detail = ""
      detail_row_data.forEach((detail_row, i) => {
        // let QntPO_format = detail_row.QntPO.toLocaleString();
        // console.log(detail_row)
        // console.log(detail_row.QntPO)
        // console.log(detail_row.Satuan)
        table_row_detail += `<tr><td>${detail_row.namaBrg}</td><td class="text-right">${parseFloat(detail_row.QNT).toFixed(2)}</td><td class="text-right">${parseFloat(detail_row.QntBeli).toFixed(2)}</td><td class="text-right">${parseFloat(detail_row.OSPO).toFixed(2)}</td><td>${detail_row.Satuan}</td><td>-</td><td>-</td></tr>`
      });

      document.getElementById("detailModalLabel").innerHTML = "Detail " +  detail_row_data[0].NoBukti;
      document.getElementById("detailSupp").value = detail_row_data[0].NAMACUSTSUPP;
      document.getElementById("detailKodeSupp").value = detail_row_data[0].KODESUPP;
      // document.getElementById("detailDate").value = date.getFullYear() + "-" + (date.getMonth()+1) + "-" + date.getDate()

      // let date1= date.getFullYear() + "-" + (date.getMonth()+1) + "-" + date.getDate()
      console.log(date1)
      $('#detailDate').val(date1)
      document.getElementById("detailTableData").innerHTML = table_row_detail


      $("#detail").modal('toggle');
    }

    function buttonAdd1(index) {
      let tempDataAdd = dataRefreshPO[index]
      buttonAdd('x' , tempDataAdd)
      // console.log(tempDataAdd)

    }

    function setNewNoBukti () {
      $.ajax({
        url: "{!! url('getNoBuktiwms') !!}",
        type: "get",
        async: false,
        success: function(res) {
          console.log(res , "RESPOND");
          console.log(res[0])
          document.getElementById("input_add_nobukti").value = res[0].Nobukti
          document.getElementById("input_add_noUrut").value = res[0].Nourut
        }
      })
    }

    function buttonAdd(add_row_id, add_row_data) {
      document.getElementById("input_add_suratjalansupp").value ='';
      document.getElementById("input_add_nokend").value ='';

      let akses = $("#akses_istambah").val();

      if (!Number(akses)) {
        alertify.warning('No access')
        return
      }
      console.log('tes123')
      console.log(add_row_data)
      setNewNoBukti()



      console.log(add_row_id, add_row_data ,  "<<< add")
      console.log('================')
      row_data = add_row_data
      let table_row_add = ""
      add_row_data.forEach((add_row, i) => {
        table_row_add += `<tr><td class="text-center"><input id="add_checkbox${i}" class="" type="checkbox" ></td><td>${add_row.namaBrg}</td><td class="text-right">${parseFloat(add_row.QNT).toFixed(2)}</td><td class="text-right">${parseFloat(add_row.QntBeli).toFixed(2)}</td><td class="text-right">${parseFloat(add_row.OSPO).toFixed(2)}</td><td>${add_row.Satuan}</td><td><input id="input_add_qntTerima${i}" style="width: 100px;" class="text-right" type="number" min=0 value=0.00></td><td>-</td><td>-</td></tr>`
      });

      document.getElementById("addTableData").innerHTML = table_row_add
      document.getElementById("input_add_nomorpo").value = add_row_data[0].NoBukti;
      document.getElementById("exampleModalLabel").innerHTML = "Add " +  add_row_data[0].NoBukti;
      document.getElementById("input_add_gudang").value = add_row_data[0].KODEGDG;
      $("#form").modal('toggle');
    }

    // function buttonAdd() {
    //   console.log(row_id)
    //
    //   if (row_id === "") {
    //     console.log('tes')
    //     alertify.warning("Tidak ada baris dipilih");
    //     return
    //   }
    //   console.log('masok')
    //
    //   let table_row_add = ""
      // row_data.forEach((add_row, i) => {
      //   table_row_add += `<tr><td class="text-center"><input class="" type="checkbox" value="" id="flexCheckDefault"></td><td>${add_row.namaBrg}</td><td>${add_row.QntPO}</td><td>${add_row.QntBeli}</td><td>${add_row.QNTOS}</td><td>${add_row.Satuan}</td><td>${add_row.OS}</td><td>-</td><td>-</td></tr>`
      // });
    //
      // document.getElementById("addTableData").innerHTML = table_row_add
      // document.getElementById("input_add_nomorpo").value = row_data[0].NoBukti;
      // document.getElementById("exampleModalLabel").innerHTML = "Add " +  row_data[0].NoBukti;
      //
      // $("#form").modal('toggle');
    // }

    function buttonEdit() {
      console.log(row_id)

      if (row_id === "") {
        console.log('tes')
        alertify.warning("Tidak ada baris dipilih");
        return
      }
      console.log('tes1')
      document.getElementById("exampleModalLabel").innerHTML = "Edit Menu";

      document.getElementById("input_kodemenu").value = row_data.KODEMENU;
      document.getElementById("input_access").value = row_data.ACCESS;
      document.getElementById("input_l0").value = row_data.L0;
      document.getElementById("input_keterangan").value = row_data.Keterangan;
      document.getElementById("input_href").value = row_data.href;
      action = "Edit";
      $("#form").modal('toggle');
      console.log('masok' , row_data);
    }

    function buttonDelete() {
      if (row_id === "") {
        console.log('tes')
        alertify.warning("Tidak ada baris dipilih");
        return
      }
      alertify.confirm('Hapus Menu', 'Apakah yakin ingin menghapus menu ' + row_data.KODEMENU + row_data.Keterangan + ' ?',
      function(){
        let _token = $("#_token").val();
        let kodemenu = row_data.KODEMENU;

        $.ajax({
          url: "{!! url('deletenewmenu') !!}",
          type: "POST",
          async: false,
          data: {
            _token : _token,
            KODEMENU: kodemenu
          },
          success: function(res) {
            alertify.success('Menu telah dihapus.');
            window.location.href = "newmenu";
          }
        })

    }
      ,function(){});
    }

    function submitAdd() {
      console.log('submitAdd123')
        let tempData = []
        // console.log('tes')
        for (let i = 0; i < row_data.length; i++) {
          // console.log('masok')
          // let tes = document.getElementById(`add_checkbox${i}`)
          // console.log(tes)
          // let isChecked = document.getElementById(`add_checkbox${i}`)
          // console.log(isChecked)
          // console.log(document.getElementById(`add_checkbox${i}`).checked)
          if (document.getElementById(`add_checkbox${i}`).checked) {
            // console.log(row_data[i])
          // let id = "#add_checkbox" + i
          // console.log(id)
          // let tes = $(`#add_checkbox${i}`).val();
          // console.log(tes)
          row_data[i].inputQntTerima = $(`#input_add_qntTerima${i}`).val();
          tempData.push(row_data[i])
        }
      }

      if(!tempData.length) {
        alertify.warning("Tidak ada item dipilih");
        return
      }




      console.log('else')
      let flag = false
      tempData.forEach((item, i) => {
        console.log(Number(item.inputQntTerima), Number(item.OSPO))
        if (Number(item.inputQntTerima) > Number(item.OSPO)){
          flag = true
        }
        if (Number(item.inputQntTerima) < 0 ) {
          flag = true
        }
      });
      if (flag) {
        alertify.warning("QtyTerima lebih besar dari QNTOS ataupun negatif");
        return
      }

      // let gudang = $("input_add_gudang").val();
      let gudang = document.getElementById("input_add_gudang").value;
      let suratJalan = $("#input_add_suratjalansupp").val();
      let noKend = $("#input_add_nokend").val();
      let noPO = $("#input_add_nomorpo").val();
      let noBukti = $("#input_add_nobukti").val();
      let _token = $("#_token").val();
      let noUrut = $("#input_add_noUrut").val();
      let inputTanggal = $("#input_add_tanggal").val();


      let checkDate = new Date(inputTanggal)

      let periode_bulan = document.getElementById("periode_bulan").value
      let periode_tahun = document.getElementById("periode_tahun").value

      if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

          alertify.warning("Tanggal tidak sesuai periode");
          return
      }


      console.log(tempData)
      // console.log(suratJalan , noKend , noPO, gudang , noUrut, noBukti, inputTanggal)
      // console.log(tempData , "=======")
      // ajax
      console.log('====== starting create =======')
      // return
      $.ajax({
                url : "{!! url('addDBBeliwms') !!}",
                type : "POST",
                async : false,
                data : {
                  _token : _token,
                  data : tempData,
                  suratJalan : suratJalan,
                  noKend : noKend,
                  noPO : noPO,
                  gudang: gudang,
                  noBukti: noBukti,
                  noUrut: noUrut,
                  inputTanggal: inputTanggal
                },
                success: function (res) {
                  console.log(res , "RESPOND");
                  if (res == 1) {

                    console.log('create success')
                    alertify.success('Pembelian telah ditambah');
                    // window.location.href = "newpo";
                    loadAll()
                    $("#form").modal('toggle');
                  }
                  if (res == 2) {
                    setNewNoBukti()
                    alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
                  }


                  if (res == 22) {
                    setNewNoBukti()
                    alertify.warning('Tanggal atau periode tidak sesuai, silahkan cek periode dan tanggal');
                  }

                  
                },
                error: function (err) {
                  console.log(err)
                  alertify.warning('Terjadi kesalahan silahkan refresh browser')
                }
              })

  }



    function submitForm() {
      if (action == "Add") {
        console.log("Add Menu Submit")
        console.log(  $("#input_kodemenu").val() , $("#input_access").val() ,$("#input_l0").val() ,$("#input_keterangan").val()  )


        let _token = $("#_token").val();
        let kodemenu = $("#input_kodemenu").val();
        let keterangan = $("#input_keterangan").val();
        let l0 = $("#input_l0").val();
        let access = $("#input_access").val();
        let href = $("#input_href").val();

        console.log(_token, kodemenu, keterangan, l0, access)

        if (kodemenu && keterangan && access) {
          // alertify.alert('Gagal menambahkan menu jadi!', 'Semua kolom harus terisi.', function(){ });
          console.log('starting create')
          $.ajax({
            url : "{!! url('addnewmenu') !!}",
            type : "POST",
            async : false,
            data : {
              _token : _token,
              KODEMENU : kodemenu,
              Keterangan : keterangan,
              L0 : l0,
              ACCESS : access,
              href: href
            },
            success: function (res) {
              if (res == 1) {
                console.log('create success')

                window.location.href = "newmenu";
              } else {
                console.log('create fail')
              }
            }
          })
          $("#form").modal('toggle');
        } else {
          alertify.alert('Tes', 'Semua kolom harus terisi.', function(){ });

        }

      } else {
        console.log("Edit Menu Submit")
        console.log("Code lama :", row_data.KODEMENU)
        console.log(  $("#input_kodemenu").val() , $("#input_access").val() ,$("#input_l0").val() ,$("#input_keterangan").val()  )


        let _token = $("#_token").val();
        let kodemenu = $("#input_kodemenu").val();
        let keterangan = $("#input_keterangan").val();
        let l0 = $("#input_l0").val();
        let access = $("#input_access").val();
        let kodelama = row_data.KODEMENU
        let href = $("#input_href").val();


        if (kodemenu && keterangan && access) {
          // alertify.alert('Gagal menambahkan menu jadi!', 'Semua kolom harus terisi.', function(){ });
          console.log('starting edit')
          $.ajax({
            url : "{!! url('editnewmenu') !!}",
            type : "POST",
            async : false,
            data : {
              _token : _token,
              KODEMENU : kodemenu,
              Keterangan : keterangan,
              L0 : l0,
              ACCESS : access,
              kodelama: kodelama,
              href: href
            },
            success: function (res) {
              if (res == 1) {
                console.log('edit success')
                window.location.href = "newmenu";
              } else {
                console.log('edit fail')
              }
            }
          })
          $("#form").modal('toggle');
        } else {
          alertify.alert('Tes', 'Semua kolom harus terisi.', function(){ });

        }

      }
    }

    function tesclick(tesprint, tesprint1) {
      console.log('asd')
      row_id = tesprint;
      row_data = tesprint1;
      $('#tabel_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      $("#tr"+tesprint).css('background-color', '#FFF59E');
      console.log(tesprint, tesprint1 ,row_id , action)
    }



  </script>



@endsection
