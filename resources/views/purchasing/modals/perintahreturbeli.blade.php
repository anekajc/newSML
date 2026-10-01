@extends('newmaster')
@section('buttons')



@endsection
{{-- tampilan search bar 1 --}}
  @section('css')
  
  <style>
  .rodokNdukurTitik{
    margin-top:-12px;
  }
  </style>

  <style>
    .dataTables_wrapper {
    overflow-x: auto;
  }

  .dataTables_scrollBody {
      border: none !important;
  }

  /* Remove conflicting borders */
  .table-responsive {
      border: none !important;
  }

  #tabel td, #tabel th {
      border-left: 1px solid #dee2e6;
      border-top: 1px solid #dee2e6;
  }

  #tabel td:first-child, #tabel th:first-child {
      border-left: none;
  }

  #tabel thead tr:first-child th {
      border-top: none;
  }
    </style>

  <style>
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
{{-- end tampilan search bar 2 --}}

{{-- tampilan search bar 3 --}}
  <style>
  #tabel3_filter {
      display: flex;
      align-items: flex-end;
      margin-top: 8px;
      margin-right: 10px;
      margin-bottom: -10px;
    }

  #tabel3_filter label input {
      width: 150px;
      padding: 5px 10px; 
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-shadow: none; 
      font-size: 0.65rem; 
    }

  #tabel3_filter label {
      font-weight: 600; 
      font-size: 0.9rem; 
      color: #333;
    }

  #tabel3_filter input:focus {
      border-color: #007bff; 
      outline: none; 
    }
  </style>
{{-- end tampilan search bar 3 --}}

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

{{-- Header tabel interaktif (drag kolom + roda gigi + bar kolom tersembunyi + modal
     filter), disamakan dengan resources/views/purchasing/pembelianpermintaanagen.blade.php,
     dipakai untuk styling #tabel_add (tabel item form Add PRB) di bawah. --}}
<link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
<style>
/* ---------- #tabel_add (tabel item di form Add PRB) - header abu-abu uppercase + zebra
   + hover, disalin dari purchaseOrder.blade.php / pembelianpermintaanagen.blade.php.
   Kolom Actions di tabel ini ada di PALING KIRI (first-child), beda dari #tabel_add pada
   pembelianpermintaanagen.blade.php yang Actions-nya di paling kanan. ---------- */
#tabel_add thead th {
  background: #f8f9fb !important;
  color: #6b7280 !important;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: .04em;
  font-weight: 600;
  border-bottom: 1px solid #e7e9ee;
  border-top: none;
}

#tabel_add tbody tr:nth-of-type(odd) { background-color: #fbfbfc; }
#tabel_add tbody tr:hover { background-color: #f5f3ff; }

/* Qty & Sat rata tengah, header dan isi, supaya lurus segaris (kolomnya masing-masing
   ada di posisi ke-4 dan ke-5: Actions, Kode Barang, Nama Barang, Qty, Sat, ...). */
#tabel_add thead th:nth-child(4),
#tabel_add thead th:nth-child(5),
#tabel_add tbody td:nth-child(4):not([colspan]),
#tabel_add tbody td:nth-child(5):not([colspan]) {
  text-align: center;
}

/* Kolom Actions ada di paling kiri (first-child) - tombol bulat kecil pastel, disalin
   dari purchaseOrder.blade.php / #tabel2 di pembelianpermintaanagen.blade.php. */
#tabel_add td:first-child:not([colspan]) {
  display: flex;
  gap: 4px;
  justify-content: center;
  align-items: center;
}

#tabel_add td:first-child .btn {
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

#tabel_add td:first-child .btn:hover {
  filter: brightness(0.97);
  transform: translateY(-1px);
}

#tabel_add td:first-child .btn-success {
  color: #16a34a; border-color: #cdebd7; background: #e7f7ed;
}

#tabel_add td:first-child .btn-warning {
  color: #b45309; border-color: #fbe3bd; background: #fef3e0;
}

#tabel_add td:first-child .btn-primary {
  color: #2563eb; border-color: #cfdcff; background: #e8edff;
}

#tabel_add td:first-child .btn-danger {
  color: #dc2626; border-color: #f7cfcf; background: #fdeaea;
}

/* ---------- Tombol chip (latar tint muda + teks berwarna) untuk tombol + Tambah Item,
   Submit Add/Edit, dan Batal di form Add PRB - disalin dari purchaseOrder.blade.php. ---------- */
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

/* Batal = aksi sekunder, jadi abu-abu muda dengan teks gelap (bukan solid gelap). */
.btn-batal-add {
  background-color: #f1f3f5;
  border-color: #dee2e6;
  color: #495057;
}

.btn-batal-add:hover,
.btn-batal-add:focus {
  background-color: #e9ecef;
  border-color: #ced4da;
  color: #343a40;
}

.btn-batal-add:active {
  background-color: #dee2e6 !important;
  border-color: #ced4da !important;
  color: #343a40 !important;
}
</style>
{{-- end tampilan search sales --}}
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

<div id="page1" class="container-fluid">
    <!-- <div id="qrcode"></div> -->
    <div class="row">
      <div class="col-6 text-left">
        <h2 style="margin-top:-85px;">Perintah Retur Beli</h2>
      </div>
      <div class="col-6 text-right">
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
          Add PRB
        </button>
      </div>
      <div class="col-6 text-right">
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

    <div class="card">
      <div class="card-header" style="margin-top:-55px;">
        <div class="row">
          <div class="nav nav-tabs col-12" id="nav-tab" role="tablist" style="border-bottom: 0;">
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true" 
              style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
              Perintah Retur Beli
            </a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false" 
              style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
              Perintah Retur Beli (Sudah Otorisasi)
            </a>
            <a class="nav-item nav-link" id="nav-profile1-tab" data-toggle="tab" href="#profile1" role="tab" aria-controls="nav-profile1" aria-selected="false" 
              style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
              List Retur Jual
            </a>
          </div>
        </div>
      </div>

      <div class="card-body" style="padding:0;">
        <div class="tab-content" id="myTabContent">

          <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div class="row">
              <div class="col-md-12" style='overflow:auto;'>
                <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                  <table id="tabel" class="table table-bordered table-hover table-striped">
                    <thead class="text-center bg-primary text-white">
                      <tr>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Actions</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">No. Bukti</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Tanggal</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Oto</th>
                      </tr>
                    </thead>
                    <tbody id="tabel_data" class="text-left">
                      {{-- @foreach ($tempOutstanding1 as $OutPR)
                      <tr>
                        
                         <td class="text-center">
                            <button class="btn btn-success btn-sm" type="button" title="Details" onclick="buttonAdd('{{ $OutPR->Nobukti }}')">
                              <i class="bi bi-plus-lg"></i>
                            </button>
                        </td>
                        <td style='white-space:nowrap;'>{{ $OutPR->Nobukti }}</td>
                        <td style='white-space:nowrap;'>{!! date("Y/m/d", strtotime($OutPR->Tanggal)) !!}</td>
                        <td style='white-space:nowrap;'>{{ $OutPR->kodebrg }}</td>
                        <td style='white-space:nowrap;'>{{ $OutPR->NamaBrg }}</td>
                        <td style='white-space:nowrap;' class='text-center'>{{ $OutPR->sat }}</td>
                        <td style='white-space:nowrap;' class='text-right'>{{ number_format($OutPR->Qnt, 2) }}</td>
                        <td style='white-space:nowrap;' class='text-right'>{{ number_format($OutPR->QNTPO, 2) }}</td>
                        <td style='white-space:nowrap;' class='text-right'>{{ number_format($OutPR->SisaPPL, 2) }}</td>
                        <td style='white-space:nowrap;'>{{ $OutPR->Keterangan }}</td>
                        <td style='white-space:nowrap;' class='text-right'>{{ number_format($OutPR->QntoutSO, 2) }}</td>
                        <td style='white-space:nowrap;' class='text-right'>{{ number_format($OutPR->QntStock, 2) }}</td>
                      </tr>
                      @endforeach --}}
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div class="row">
              <div class="col-12" style="overflow:auto;">
                <div class="container-fluid" style="padding:0; margin:0; width:100%;">
                  
                  <table id="tabel2" class="table table-bordered table-hover table-striped table-responsive-lg">
                    <thead class="text-center bg-primary text-white">
                      <tr>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Actions</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">No. Bukti</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Tanggal</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Oto</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">User Oto</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Tanggal Oto</th>
                      </tr>
                    </thead>
                    <tbody id="tabel2_data" class="text-left">
                      {{-- @foreach( $tempOutstanding3 as $PurchaseOrderData)
                      <tr>
                        <td class="text-center"style='white-space:nowrap;'>
                            <button class="btn btn-warning btn-sm" type="button" title="Details" onclick="buttonDetail('{{ $PurchaseOrderData->NoBukti }}')">
                              <i class="bi bi-info-circle-fill"></i>
                            </button>
                            <button class="btn btn-success btn-sm" type="button" title="Edit" onclick="buttonEdit('{{ $PurchaseOrderData->NoBukti }}')">
                              <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button class="btn btn-primary btn-sm" type="button" title="Otorisasi" onclick="buttonOtorisasi('{{ $PurchaseOrderData->NoBukti }}')">
                              <i class="bi bi-key-fill"></i>
                            </button>
                        </td>
                        <td style='white-space:nowrap;'>{{ $PurchaseOrderData->NoBukti }}</td>
                        <td style='white-space:nowrap;'>{!! date("Y/m/d", strtotime($PurchaseOrderData->Tanggal)) !!}</td>
                        <td style='white-space:nowrap;'>{{ $PurchaseOrderData->NamaCustSupp }}</td>
                        <td style='white-space:nowrap;'>{!! date("Y/m/d", strtotime($PurchaseOrderData->tglKirim)) !!}</td>
                        <td style='white-space:nowrap;'>{{ $PurchaseOrderData->NOSO }}</td>
                        <td style='white-space:nowrap;'>{{ $PurchaseOrderData->NOPOCUST }}</td>
                        <td style='white-space:nowrap;' class='text-right'>{{ number_format($PurchaseOrderData->TotDPPRp, 2) }}</td>
                        <td style='white-space:nowrap;' class='text-right'>{{ number_format($PurchaseOrderData->TotSubTotalRp, 2) }}</td>
                        <td style='white-space:nowrap;' class='text-right'>{{ number_format($PurchaseOrderData->TotPPNRp, 2) }}</td>
                        <td style='white-space:nowrap;' class='text-right'>{{ number_format($PurchaseOrderData->TotNetRp, 2) }}</td>
                          @if($PurchaseOrderData->IsOtorisasi1 == 1)
                            <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>
                          @else
                            <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>
                          @endif
                        <td style='white-space:nowrap;'>{{ $PurchaseOrderData->OtoUser1 }}</td>
                        <td style='white-space:nowrap;'>
                          @if($PurchaseOrderData->TglOto1 === null)
                            -
                          @else
                            {{ \Carbon\Carbon::parse($PurchaseOrderData->TglOto1)->format('d/m/Y - H:i:s') }}
                          @endif
                        </td>
                          @if($PurchaseOrderData->Isbatal== 1)
                            <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>
                          @else
                            <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>
                          @endif
                        <td style='white-space:nowrap;'>{{ $PurchaseOrderData->UserBatal }}</td>
                        <td style='white-space:nowrap;'>
                          @if($PurchaseOrderData->TglBatal === null)
                            -
                          @else
                            {{ \Carbon\Carbon::parse($PurchaseOrderData->TglBatal)->format('d/m/Y - H:i:s') }}
                          @endif
                        </td>
                      </tr>
                      @endforeach --}}
                    </tbody>
                  </table>

                </div>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="profile1" role="tabpanel" aria-labelledby="profile-tab">
            <div class="row">
              <div class="col-12" style="overflow:auto;">
                <div class="container-fluid" style="padding:0; margin:0; width:100%;">
                  
                  <table id="tabel3" class="table table-bordered table-hover table-striped table-responsive-lg">
                    <thead class="text-center bg-primary text-white">
                      <tr>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Nomor Retur</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Kode Barang</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Nama Barang</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Satuan</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Qty</th>
                      </tr>
                    </thead>
                    <tbody id="tabel3_data" class="text-left">
                      {{-- @foreach ($tempOutstanding5 as $POOtorisasi)
                      <tr>
                        <td class="text-center">
                            <button class="btn btn-warning btn-sm" type="button" title="Details" onclick="buttonDetail('{{ $POOtorisasi->NoBukti }}')">
                              <i class="bi bi-info-circle-fill"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" type="button" title="Otorisasi" onclick="buttonBatalOtorisasi('{{ $POOtorisasi->NoBukti }}')">
                              <i class="bi bi-key-fill"></i>
                            </button>
                        </td>
                        <td style='white-space:nowrap;'>{{ $POOtorisasi->NoBukti }}</td>
                        <td style='white-space:nowrap;'>{!! date("Y/m/d", strtotime($POOtorisasi->Tanggal)) !!}</td>
                        <td style='white-space:nowrap;'>{{ $POOtorisasi->NamaCustSupp }}</td>
                        <td style='white-space:nowrap;'>{!! date("Y/m/d", strtotime($POOtorisasi->TglKirim)) !!}</td>
                        <td style='white-space:nowrap;'>{{ $POOtorisasi->NOSO }}</td>
                        <td style='white-space:nowrap;'>{{ $POOtorisasi->NOPOCUST }}</td>
                        <td style='white-space:nowrap;' class='text-right'>{{ number_format($POOtorisasi->TotDPPRp, 2) }}</td>
                        <td style='white-space:nowrap;' class='text-right'>{{ number_format($POOtorisasi->TotSubTotalRp, 2) }}</td>
                        <td style='white-space:nowrap;' class='text-right'>{{ number_format($POOtorisasi->TotPPNRp, 2) }}</td>
                        <td style='white-space:nowrap;' class='text-right'>{{ number_format($POOtorisasi->TotNetRp, 2) }}</td>
                          @if($POOtorisasi->IsOtorisasi1 == 1)
                            <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>
                          @else
                            <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>
                          @endif
                        <td style='white-space:nowrap;'>{{ $POOtorisasi->OtoUser1 }}</td>
                        <td style='white-space:nowrap;'>
                          @if($POOtorisasi->TglOto1 === null)
                            -
                          @else
                            {{ \Carbon\Carbon::parse($POOtorisasi->TglOto1)->format('d/m/Y - H:i:s') }}
                          @endif
                        </td>
                          @if($POOtorisasi->Isbatal == 1)
                            <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>
                          @else
                            <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>
                          @endif
                        <td style='white-space:nowrap;'>{{ $POOtorisasi->UserBatal }}</td>
                        <td style='white-space:nowrap;'>
                          @if($POOtorisasi->TglBatal === null)
                            -
                          @else
                            {{ \Carbon\Carbon::parse($POOtorisasi->TglBatal)->format('d/m/Y - H:i:s') }}
                          @endif
                        </td>
                      </tr>
                      @endforeach --}}
                    </tbody>
                  </table>

                </div>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="profile2" role="tabpanel" aria-labelledby="profile-tab">
            <div class="row">
              <div class="col-12" style="overflow:auto;">
                <div class="container-fluid">
                      <table id="tabelRetur" class="table table-bordered table-striped"  >
                        <thead class="text-center">
                          <tr>
                            <th scope="col">Profile 2</th>
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

<div id="page2" class="container-fluid" style="display: none" >
  <div class="row">
    <div class="col-6 text-left">
      <h2 style="margin-top: -80px;">Form Perintah Retur Beli</h2>
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
          onclick="buttonCloseForm()">
        Close
      </button>
    </div>
  </div>

  <div id="modalBodyAddMain" class="">
    <div class="modal-body" style="margin-top:-60px;">
      <div class="row">

            <input type="hidden" class="form-control" id="input_nourut">

            <div class="col-md-12">
              <div class="row">
                <div class="col-md-1">
                  <div class="form-group">
                    <label>No Bukti</label>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <input type="text" class="form-control text-center" id="input_add_nobukti" placeholder="" readonly>
                    <input type="text" class="form-control text-center" id="input_add_nourut" placeholder="nourut" hidden>
                  </div>
                </div>

                <div class="col-md-1">
                  <div class="form-group">
                    <label>Tanggal</label>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <input type="date" class="form-control text-center" id="input_add_tanggal" value="{!! date('Y-m-d') !!}" readonly>
                  </div>
                </div>

                <div class="col-md-1">
                  <div class="form-group">
                    <label>Pembayaran</label>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <select id="input_add_tipebayar" class="form-control form-select-lg mb-3 text-center" aria-label=".form-select-lg example">
                      <option value=0 selected >Tunai</option>
                      <option value=1>Kredit</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-12" style='margin-top:-12px;'>
              <div class="row">
                <div class="col-md-1">
                  <div class="form-group">
                    <label>Keterangan</label>
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="form-group">
                    <textarea style="width: 100%; resize: none" rows="2" placeholder="Keterangan" class="form-control text-left " id="input_add_keterangan"></textarea>
                  </div>
                </div>
              </div>
            </div>

          </div>

          {{-- <div class="row" style='margin-top:5px'>
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
          </div> --}}
            <div class="showhidemodalbodyaddmain mt-4" id="modalBodyAddMainHeader" style="display: none;">
              <div class="row" style='margin-top:-30px'>

                <div class="col-md-3">
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Dikirim Ke</label>
                      </div>
                    </div>
                    <div class="form-group row">
                      <input class="form-control col-8" id="input_add_kodeAlamatKirim" readonly >
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
                      <input class="form-control col-8" id="input_add_kodeEkspedisi" value ='-'readonly>
                      <button onclick="buttonAddListLokasiPenerima()" id="buttonAddListLokasiPenerima" style="height:32px;" value = '-' class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
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
                          <div class="input-group form-group">
                            <input type="text" class="form-control" id="input_add_noso" value='-' readonly>
                            <button onclick="buttonAddListNoSO()" id="buttonAddListNoSo" style="height:32px;" class="btn btn-primary btn-sm text-right">
                              <i class="bi bi-plus"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-4">
                          <div class="form-group">
                            <label>No. PO Cust</label>
                          </div>
                        </div>
                        <div class="col-8">
                          <div class="form-group">
                            <input type="text" class="form-control" id="input_add_nopocust" value ='-' readonly>
                          </div>
                        </div>
                      </div>
                    </div>
  
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-4">
                          <div class="form-group">
                            <label>Tgl Kirim</label>
                          </div>
                        </div>
                        <div class="col-8">
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
            <table id="tabel_add" class="data-table">
              <thead class="text-center">
                <tr>
                  <th scope="col">Actions</th>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Qty</th>
                  <th scope="col">Sat</th>
                  <th scope="col">No. Retur Jual</th>
                  <th scope="col">No. Beli</th>
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
                  <td class="text-center">
                    <div class="btn-group" role="group">
                      <button class="btn btn-warning btn-sm" type="button" title="Details" onclick="">
                        <i class="bi bi-info-circle-fill"></i>
                      </button>
                      <button class="btn btn-primary btn-sm" type="button" title="Otorisasi" onclick="">
                        <i class="bi bi-key-fill"></i>
                      </button>
                      <button class="btn btn-success btn-sm" type="button" title="Edit" onclick="">
                        <i class="bi bi-pencil-fill"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 mt-2 text-right">
            <button type="button" id='buttonTambahItem' class="btn btn-lg btn-chip-biru" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="buttonAddAddItem()"><b>+ Tambah Item</b></button>
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

          <div class="row" style='margin-top:-25px;'>
            <div class="col-md-12">
              <div class="row">

                <!-- START OF ITEM No. R. Jual, No Beli, & Supplier -->
                <div class="col-md-4">
                  <div class="row">
                    <div class="col-md-12">

                      <input type="text" class="form-control" id="inputitem_urut" hidden>

                      <div class="row">
                        <div class="col-4">
                          <div class="form-group">
                            <label>No. R. Jual</label>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="input-group form-group">
                            <input type="text" class="form-control" id="input_add_add_norjual" value='-'>
                            <input type="text" class="form-control" id="input_add_add_urutRJual" hidden>
                            <button class="btn btn-primary btn-sm text-right" onclick='buttonNoRJual()'>
                              <i class="bi bi-plus"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                      
                      <div class="row" style='margin-top:-12px;'>
                        <div class="col-4">
                          <div class="form-group">
                            <label>No. Beli</label>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="input-group form-group">
                            <input type="text" class="form-control" id="input_add_add_nobeli" value='-'>
                            <input type="text" class="form-control" id="input_add_add_urutPbl" hidden>
                            <button class="btn btn-primary btn-sm text-right" onclick='buttonNoBeli()'>
                              <i class="bi bi-plus"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                      
                      <div class="row" style='margin-top:-12px;'>
                        <div class="col-4">
                          <div class="form-group">
                            <label>Supplier</label>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="input-group form-group">
                            <input type="text" class="form-control" id="input_add_add_supplier">
                            <button class="btn btn-primary btn-sm text-right" onclick='buttonSupplier()'>
                              <i class="bi bi-plus"></i>
                            </button>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
                <!-- END OF ITEM No. R. Jual, No Beli, & Supplier -->


                <!-- START OF ITEM Gudang, Kode Barang, Nama Barang -->
                <div class="col-md-4">
                  <div class="row">
                    <div class="col-md-12">

                      <div class="row">
                        <div class="col-4">
                          <div class="form-group">
                            <label>Kode Barang</label>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="input-group form-group">
                            <input type="text" class="form-control" id="input_add_add_kodebrg" >
                            <button class="btn btn-primary btn-sm text-right" onclick='buttonBarang()'>
                              <i class="bi bi-plus"></i>
                            </button>
                          </div>
                        </div>
                      </div>

                      <div class="row" style='margin-top:-12px;'>
                        <div class="col-4">
                          <div class="form-group">
                            <label>Nama Barang</label>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="input-group form-group">
                            <input type="text" class="form-control" id="input_add_add_namabrg" readonly>
                          </div>
                        </div>
                      </div>

                      <div class="row" style='margin-top:-12px;'>
                        <div class="col-4">
                          <div class="form-group">
                            <label>Quantity</label>
                          </div>
                        </div>
                        <div class="col-3">
                          <div class="input-group form-group">
                            <input type="number" class="form-control" id="input_add_add_quantity">
                          </div>
                        </div>

                        <div class="col-2">
                          <div class="form-group">
                            <label>Satuan</label>
                          </div>
                        </div>
                        <div class="col-3">
                          <div class="input-group form-group">
                            <select id="input_add_add_satuan" class="form-control form-select-lg text-center" aria-label=".form-select-lg example" readonly>
                            </select>
                          </div>
                          
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
                <!-- END OF ITEM Gudang, Kode Barang, Nama Barang -->


                <!-- START OF ITEM Quantity, Satuan, Keterangan -->
                <div class="col-md-4">
                  <div class="row">
                    <div class="col-md-12">

                      <div class="row">
                        <div class="col-3">
                          <div class="form-group">
                            <label>Gudang</label>
                          </div>
                        </div>
                        <div class="col-md-9">
                          <div class="input-group form-group">
                            <input type="text" class="form-control" id="input_add_add_gudang">
                            <button id="buttonBrowseGudang" class="btn btn-primary btn-sm text-right" onclick='buttonGudang()'>
                              <i class="bi bi-plus"></i>
                            </button>
                          </div>
                        </div>
                      </div>

                      <div class="row" style='margin-top:-12px;'>
                        <div class="col-md-3">
                          <div class="form-group">
                            <label>Keterangan</label>
                          </div>
                        </div>
                        <div class="col-md-9">
                          <div class="form-group">
                            <textarea style="width: 100%; resize: none" rows="3" placeholder="Keterangan" class="form-control text-center" id="input_add_add_keterangan"></textarea>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
                <!-- END OF ITEM Quantity, Satuan, Keterangan -->

              </div>
            </div>
          </div>

          <div class="row mt-2">
            <div class="col-md-12 text-right">
              <button type="button" class="btn btn-lg btn-batal-add" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="closeShowHideAdd()">Batal</button>

              <button type="button" id="submitAddAdd" class="btn btn-lg btn-chip-biru" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="submitAddAdd()">Submit Add</button>

              <button type="button" id="submitAddEdit" class="btn btn-lg btn-chip-biru" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="submitAddEdit()">Submit Edit</button>
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
              <th scope="col">Qty</th>
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
              <select id="input_add_edit_nosat" onchange="onChangeInputAddAddNosat()" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" tabindex='2'>
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

  <div class="container-fluid" style="margin-top: -10px;">
  {{-- <div class="row">
    
    <!-- Disc % -->
    <div class="col">
      <div class="form-group">
        <label>Disc %</label>
        <input type="number" class="form-control text-right" id="input_add_disc" onblur="onChangeInputAddDisc()" value="0.00">
      </div>
    </div>

    <!-- DiscRp -->
    <div class="col">
      <div class="form-group">
        <label>DiscRp</label>
        <input type="number" class="form-control text-right" id="input_add_discrp" onblur="onChangeInputAddDiscRp()" value ="0.00" >
      </div>
    </div>

    <!-- DPP -->
    <div class="col">
      <div class="form-group">
        <label>DPP</label>
        <input type="text" class="form-control text-right" id="input_add_dpp" value="0.00" disabled>
      </div>
    </div>

    <!-- PPN -->
    <div class="col">
      <div class="form-group">
        <label>PPN</label>
        <input type="text" class="form-control text-right" id="input_add_ppn" value="0.00" disabled>
      </div>
    </div>

    <!-- Grand Total -->
    <div class="col">
      <div class="form-group">
        <label>Grand Total</label>
        <input type="text" class="form-control text-right" id="input_add_grandtotal" value="0.00" disabled>
      </div>
    </div>

  </div> --}}
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
          <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_alamatlokasipenerima"  value ='-'disabled></textarea>
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

@include('modals/modalPRBAdd')

@endsection

@section('js')
<script type="text/javascript">

    window.onload = function(){
      loadAll();
    };
    
let dataTableAdd = []
let dataTableEdit = []

let dataRefresh = []

let dataAddAddListItem = []

let dataRefreshOutstanding = []
let dataRefreshOutstanding2 = []
let dataRefreshOutstanding3 = []

let dataRefreshPenerimaan = []

let listAlamatKirim = []

let tempAddAdd = {}
let tempAddEdit = {}
let tempIndexEdit = 0
let tempEditAdd = {}
let tempEditEdit = {}

let nosatTemp = 0
let isi1Temp = 0
let isi2Temp = 0

let tipeform = ''
let tipeformitem = ''

function buttonOtorisasi (nobukti) {
  console.log(nobukti)

  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

    let _token = $("#_token").val();

    $.ajax({
      url: "{!! url('prbupdateotorisasi') !!}",
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
        alertify.warning('Gagal Otorisasi')
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

  alertify.confirm('Batal Otorisasi', 'Batal Otorisasi PRB ' + nobukti + ' ?',
      function() {
        let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('prbupdatebatalotorisasi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti

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

function onChangeInputAddDiscRp () {
    // document.getElementById("input_add_disc").value = '0.00'
    console.log('onChangeDiscRp')
      if (tipeform == 'edit') {
        let value = $("#input_add_discrp").val()
        console.log(dataHeaderAdd)
        let x = Number(value) / Number(dataHeaderAdd.TOtSubtotalRP) * 100
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

function submitAddAdd () {

  console.log('submitAddAdd')

  let checkDate = new Date($("#input_add_tanggal").val())
  
  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if (checkDate.getFullYear() !== Number(periode_tahun) || (checkDate.getMonth() +1) !== Number(periode_bulan)) {
      alertify.warning("Tanggal tidak sesuai periode");
      return
  }

  let Jmlrecord = 0 
  if (dataTableAdd.length) {
    Jmlrecord = 1
  }

  let _token  = $("#_token").val()
  let Choice = "I"

  let NoBukti = $("#input_add_nobukti").val()
  let NoUrut = $("#input_add_nourut").val() 
  let Tanggal = $("#input_add_tanggal").val()
  let KodeSupp = $("#input_add_add_supplier").val()
  let KodeGdg = $("#input_add_add_gudang").val()

  let NoBeli = $("#input_add_add_nobeli").val()
  let Keterangan = $("#input_add_keterangan").val()
  // let FakturSupp = $("#input_add_lier").val() isi di controller
  // let Urut = $("#input_add_lier").val() isi di controller
  let KodeBrg = $("#input_add_add_kodebrg").val()
  let UrutPBL = $("#input_add_add_urutPbl").val()
  let Qnt = $("#input_add_add_quantity").val()
  let NoSat = nosatTemp
  let Satuan = $("#input_add_add_satuan").val()
  let Qnt1 = $("#input_add_add_quantity").val()
  let Qnt2 = $("#input_add_lier").val()
  let NORJual = $("#input_add_add_norjual").val()
  let UrutRJual = $("#input_add_add_urutRJual").val()
  let KETDET = $("#input_add_add_keterangan").val()

  console.log(tempAddAdd)

  let Isi = 0

  if (NoSat == 1) {
    Qnt2 = Qnt * isi1Temp
    Isi = isi1Temp
  }
  if (NoSat == 2) {
    Qnt2 = Qnt * isi2Temp
    Isi = isi2Temp
  }

  if (!Keterangan) {
    Keterangan = '-'
  }

  console.log(
    "I",
    NoBukti,
    NoUrut,
    Tanggal,
    KodeSupp,
    KodeGdg,
    NoBeli,
    Keterangan,
    '',
    0,
    KodeBrg,
    UrutPBL,
    Qnt,
    NoSat,
    Satuan,
    Isi,
    Qnt1,
    Qnt2,
    0,
    '',
    NORJual,
    UrutRJual,
    Jmlrecord,
    KETDET,
    0
  )

  console.log('==========' , Number(NoSat))
  if (!KodeBrg || !KodeGdg) {
    alertify.warning("Data belum lengkap")
    return
  }

  // if (Number(Hari) < 0 || Number(Qnt) < 0 || Number(Harga) < 0 || Number(DiscTot) < 0)  {
  //   alertify.warning("Angka negatif")
  //   return
  // }

  $.ajax({
    url: "{!! url('prbspadd') !!}",
    type: "post",
    async: false,
    data: {

    _token,
    Choice,
    NoBukti,
    NoUrut,
    Tanggal,
    KodeSupp,
    KodeGdg,
    NoBeli,
    Keterangan,
    KodeBrg,
    UrutPBL,
    Qnt,
    NoSat,
    Satuan,
    Isi,
    Qnt1,
    Qnt2,
    NORJual,
    UrutRJual,
    Jmlrecord,
    KETDET

    },
    success: function(res) {
      
      if (res == 1) {

        loadAll()
        tipeform = 'edit'
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

  let _token  = $("#_token").val()
  let Choice = "U"

  let NoBukti = $("#input_add_nobukti").val()
  let NoUrut = $("#input_add_nourut").val() 
  let Tanggal = $("#input_add_tanggal").val()
  let KodeSupp = $("#input_add_add_supplier").val()
  let KodeGdg = $("#input_add_add_gudang").val()

  let NoBeli = $("#input_add_add_nobeli").val()
  let Keterangan = $("#input_add_keterangan").val()
  // let FakturSupp = $("#input_add_lier").val() isi di controller
  // let Urut = $("#input_add_lier").val() isi di controller
  let KodeBrg = $("#input_add_add_kodebrg").val()
  let UrutPBL = $("#input_add_add_urutPbl").val()
  let Qnt = $("#input_add_add_quantity").val()
  let NoSat = nosatTemp
  let Satuan = $("#input_add_add_satuan").val()
  let Qnt1 = $("#input_add_add_quantity").val()
  let Qnt2 = $("#input_add_lier").val()
  let NORJual = $("#input_add_add_norjual").val()
  let UrutRJual = $("#input_add_add_urutRJual").val()
  let KETDET = $("#input_add_add_keterangan").val()

  console.log(tempAddAdd)

  let Isi = 0

  if (NoSat == 1) {
    Qnt2 = Qnt * isi1Temp
    Isi = isi1Temp
  }
  if (NoSat == 2) {
    Qnt2 = Qnt * isi2Temp
    Isi = isi2Temp
  }

  if (!Keterangan) {
    Keterangan = '-'
  }

  console.log(
    "U",
    NoBukti,
    NoUrut,
    Tanggal,
    KodeSupp,
    KodeGdg,
    NoBeli,
    Keterangan,
    '',
    0,
    KodeBrg,
    UrutPBL,
    Qnt,
    NoSat,
    Satuan,
    Isi,
    Qnt1,
    Qnt2,
    0,
    '',
    NORJual,
    UrutRJual,
    Jmlrecord,
    KETDET,
    0
  )

  console.log('==========' , Number(NoSat))
  if (!KodeBrg || !KodeGdg) {
    alertify.warning("Data belum lengkap")
    return
  }

  // if (Number(Hari) < 0 || Number(Qnt) < 0 || Number(Harga) < 0 || Number(DiscTot) < 0)  {
  //   alertify.warning("Angka negatif")
  //   return
  // }

  $.ajax({
    url: "{!! url('prbspadd') !!}",
    type: "post",
    async: false,
    data: {

    _token,
    Choice,
    NoBukti,
    NoUrut,
    Tanggal,
    KodeSupp,
    KodeGdg,
    NoBeli,
    Keterangan,
    KodeBrg,
    UrutPBL,
    Qnt,
    NoSat,
    Satuan,
    Isi,
    Qnt1,
    Qnt2,
    NORJual,
    UrutRJual,
    Jmlrecord,
    KETDET

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

  // cleanFormAddAdd()
  // document.getElementById("buttonAddAddListBarang").disabled = false
  $('#h4AddAddItem').show();
  $('#h4AddEditItem').hide();
  $('#submitAddAdd').show();
  $('#submitAddEdit').hide();
  $('#addAddItem').show();
  // document.getElementById("input_add_add_namabarang").scrollIntoView();
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

  let selectOption = ''
  if (tempAddEdit.SATUAN) {
    selectOption += `<option value=1 selected>${tempAddEdit.SATUAN}</option>`
  }

  if (tempAddEdit.NoPNW == ''){
    tempAddEdit.NoPNW = '-' 
  }

  document.getElementById("input_add_add_norjual").value = tempAddEdit.NORJual
  document.getElementById("input_add_add_nobeli").value = tempAddEdit.nopbl
  document.getElementById("input_add_add_supplier").value = tempAddEdit.KODESUPP
  document.getElementById("input_add_add_kodebrg").value = tempAddEdit.KODEBRG
  document.getElementById("input_add_add_namabrg").value = tempAddEdit.NamaBrg
  document.getElementById("input_add_add_quantity").value = tempAddEdit.QNT
  document.getElementById("input_add_add_satuan").value = tempAddEdit.SATUAN
  document.getElementById("input_add_add_gudang").value = tempAddEdit.KodeGdg
  document.getElementById("input_add_add_keterangan").value = tempAddEdit.KETERANGAN

  $('#divhargaterakhir').hide();
  $('#divStockProyeksi').hide();
  $('#h4AddAddItem').hide();
  $('#h4AddEditItem').show();
  $('#submitAddAdd').hide();
  $('#submitAddEdit').show();
  $('#addAddItem').show();

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
      kode:'PRB',
      _token
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

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
          <td>${formatAngka(item.qty)}</td>
          <td>${item.satuan}</td>
          <td>${item.harga}</td>
          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickPWO('${item.no_bukti}' , '${item.tanggal}')" type="button" ><i class="bi bi-plus"></i></button></td>
        </tr>`
      });

      document.getElementById("tabel_data_add_list_pwo").innerHTML = rowTable

      document.getElementById("namaHeaderTable").textContent = 'Nomor Penawaran PO'

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

  let _token = $("#_token").val();
  let foc = $("#input_add_add_foc").val();
  let noSo = $("#input_add_noso").val();
  
  if (!noSo) {
    alertify.warning("Isi Nomor SO terlebih dahulu")
    return
  }

  if (foc == 0 & noSo == '-') {
    if ( noBuktiUntukAdd != 0){

    $('#tabel_add_list_barang_nonfoc').DataTable().destroy();

    $.ajax({
      url: "{!! url('polistbarangnosominus') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        noBukti : noBuktiUntukAdd
      },
      success: function(res) {
        let rowTable = ``
        dataAddAddListItem = res
        dataAddAddListItem.forEach((item, i) => {
          rowTable += `
          <tr>
            <td style="white-space:nowrap;" class="text-center">
              <button class="btn btn-primary btn-sm" onclick="buttonAddAddPickBarangNonFOC(${i})" type="button" ><i class="bi bi-plus"></i>
              </button>
            </td>
            <td style="white-space:nowrap;">${item.KodeBrg}</td>
            <td style="white-space:nowrap;">${item.NamaBrg}</td>
            <td style="white-space:nowrap;">${item.PartNumber}</td>
            <td style="white-space:nowrap;">${item.NAMAMERK ? item.NAMAMERK : ''}</td>
            <td style="white-space:nowrap;">${item.Sat}</td>
            <td style="white-space:nowrap;">${formatAngka(item.Qnt)}</td>
            <td style="white-space:nowrap;">${formatAngka(item.QntPO)}</td>
            <td style="white-space:nowrap;">${item.SisaPPL}</td>
            <td style="white-space:nowrap;">${item.NoBukti}</td>
            <td style="white-space:nowrap;">${item.NosoCust}</td>
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
        document.getElementById("namaHeaderTable").textContent = 'Barang Tanpa FOC'
        $('.showhidemodalbodyadd').hide();
        $('#modalBodyAddAddListBarangNonFOC').show();

        $("#form").modal('toggle')

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    }) }
    else if ( noBuktiUntukAdd == 0){
      
    $('#tabel_add_list_barang_nonfoc').DataTable().destroy();

    $.ajax({
      url: "{!! url('polistbarangnosominusallso') !!}",
      type: "post",
      async: false,
      data: {
        _token
      },
      success: function(res) {
        let rowTable = ``
        dataAddAddListItem = res
        dataAddAddListItem.forEach((item, i) => {
          rowTable += `
          <tr>
            <td style="white-space:nowrap;" class="text-center">
              <button class="btn btn-primary btn-sm" onclick="buttonAddAddPickBarangNonFOC(${i})" type="button" ><i class="bi bi-plus"></i>
              </button>
            </td>
            <td style="white-space:nowrap;">${item.KodeBrg}</td>
            <td style="white-space:nowrap;">${item.NamaBrg}</td>
            <td style="white-space:nowrap;">${item.PartNumber}</td>
            <td style="white-space:nowrap;">${item.NAMAMERK ? item.NAMAMERK : ''}</td>
            <td style="white-space:nowrap;">${item.Sat}</td>
            <td style="white-space:nowrap;">${formatAngka(item.Qnt)}</td>
            <td style="white-space:nowrap;">${formatAngka(item.QntPO)}</td>
            <td style="white-space:nowrap;">${item.SisaPPL}</td>
            <td style="white-space:nowrap;">${item.NoBukti}</td>
            <td style="white-space:nowrap;">${item.NosoCust}</td>
          </tr>`
        });

        if(!res.length) {
          rowTable= ``
        }
        document.getElementById("tabel_data_add_list_barang_nonfoc").innerHTML = rowTable
        document.getElementById("namaHeaderTable").textContent = 'Barang Tanpa FOC'
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

    }
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
            <td style="white-space:nowrap;" class="text-center">
              <button class="btn btn-primary btn-sm" onclick="buttonAddAddPickBarangFOCPlus(${i})" type="button" ><i class="bi bi-plus"></i></button>
            </td>
            <td style="white-space:nowrap;">${item.Kodebrg}</td>
            <td style="white-space:nowrap;">${item.NamaBrg}</td>
            <td style="white-space:nowrap;">${item.partNumber}</td>
            <td style="white-space:nowrap;">${item.NamaMerk}</td>
          </tr>`
        });

        document.getElementById("tabel_data_add_list_barang_foc").innerHTML = rowTable

        $("#tabel_add_list_barang_foc").DataTable({
          "lengthChange": false,
            "paging": true ,
        });
        document.getElementById("namaHeaderTable").textContent = 'Barang (FOC)'
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
            <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddAddPickBarangNonFOCPlus(${i})" type="button" ><i class="bi bi-plus"></i></button></td>
            <td>${item.KodeBrg}</td>
            <td style="white-space:nowrap;">${item.NamaBrg}</td>
            <td>${formatAngka(item.Qnt)}</td>
            <td>${formatAngka(item.Qnt2)}</td>
            <td>${item.Sat}</td>
            <td>${item.SisaPPL}</td>
            <td>${item.Sisa2PPL}</td>
            <td>${item.NoBukti}</td>
            <td>${item.PartNumber}</td>
            
          </tr>`
        });

        document.getElementById("tabel_data_add_list_barang_nonfocplus").innerHTML = rowTable

        $("#tabel_add_list_barang_nonfocplus").DataTable({
          "lengthChange": false,
            "paging": true ,
        });
        document.getElementById("namaHeaderTable").textContent = 'Barang'
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
          <td class="text-center"><button class="btn btn-primary btn-sm" style="margin-top:5px; margin-bottom:5px;" onclick="buttonAddPickNoSO('-' , '-')" type="button" ><i class="bi bi-plus"></i></button></td> 
          <td>-</td>
          <td>-</td>
          <td>-</td>
          </tr>`

      listNoSo = res

      listNoSo.forEach((item, i) => {
        rowTable += `
        <tr>
          <td class="text-center"><button class="btn btn-primary btn-sm" style="margin-top:5px; margin-bottom:5px;" onclick="buttonAddPickNoSO('${item.NOBUKTI}' , '${item.NoPesanan}')" type="button" ><i class="bi bi-plus"></i></button></td>
        
          <td>${item.NOBUKTI}</td>
          <td>${item.Tanggal}</td>
          <td>${item.NoPesanan}</td></tr>`
      });

      document.getElementById("tabel_data_add_list_noSo").innerHTML = rowTable
      $("#tabel_add_list_noSo").DataTable({
        "lengthChange": false,
        "paging": true,
      });
      document.getElementById("namaHeaderTable").textContent = 'Nomor SO'
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
        <td class="text-center"><button class="btn btn-primary btn-sm" style="margin-top:5px; margin-bottom:5px;" onclick="buttonAddPickAlamatKirim(${i} )" type="button" ><i class="bi bi-plus"></i></button></td>
        <td>${item.KODEGDG}</td>
        <td>${item.NAMA}</td>
        <td>${item.Alamat}</td>
        
        </tr>`
      });

      document.getElementById("tabel_data_add_list_alamatkirim").innerHTML = rowTable
      $("#tabel_add_list_alamatkirim").DataTable({
        "lengthChange": false,
        "paging": true,
      });

      document.getElementById("namaHeaderTable").textContent = 'Dikirim Ke'

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
      let rowTable = `<tr>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickLokasiPenerima('-' , '-' )" type="button" ><i class="bi bi-plus"></i></button></td>

        <td>-</td>
        <td>-</td>
        
        </tr>`
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickLokasiPenerima('${item.KodeCustsupp}' , '${item.NamaCust}' )" type="button" ><i class="bi bi-plus"></i></button></td>
        <td>${item.KodeCustsupp}</td>
        <td>${item.NamaCust}</td>
        </tr>`
      });

      document.getElementById("tabel_data_add_list_lokasipenerima").innerHTML = rowTable
      $("#tabel_add_list_lokasipenerima").DataTable({
        "lengthChange": false,
        "paging": true,
      });

      document.getElementById("namaHeaderTable").textContent = 'Ekspedisi'

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
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickValas('${item.kodevls}' , '${item.kurs ? parseFloat(item.kurs).toFixed(2) : '0.00'}' )" type="button" ><i class="bi bi-plus"></i></button></td>
        <td>${item.kodevls}</td>
        <td>${item.namavls}</td>
        <td>${item.kurs ? parseFloat(item.kurs).toFixed(2) : '0.00'}</td>
       
        </tr>`
      });

      document.getElementById("tabel_data_add_list_valas").innerHTML = rowTable
      $("#tabel_add_list_valas").DataTable({
        "lengthChange": false,
        "paging": true,
      });

      document.getElementById("namaHeaderTable").textContent = 'Valas'

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
        <td class="text-center"><button class="btn btn-primary btn-sm" style="margin-top:10px;" onclick="buttonAddPickPelanggan('${item.KodeCustSupp}' , '${item.NamaCustSupp}' , '${item.Alamat}','${item.HARI}', '${item.PPN}')" type="button" ><i class="bi bi-plus"></i></button></td>

        <td>${item.KodeCustSupp}</td>
        <td>${item.NamaCustSupp}</td>
        <td>${item.Alamat}</td>
        
        </tr>`
      });


      document.getElementById("tabel_data_add_list_pelanggan").innerHTML = rowTable
      $("#tabel_add_list_pelanggan").DataTable({
        "lengthChange": false,
          "paging": true ,
      });

      document.getElementById("namaHeaderTable").textContent = 'Supplier'

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
      
function loadAll() {
  console.log("loadAll test");

  // optional: if you’re not using CSRF token here, you can remove this
  const _token = $("#_token").val();

  // Safely destroy DataTables if they exist
  if ($.fn.DataTable.isDataTable('#tabel')) $('#tabel').DataTable().destroy();
  if ($.fn.DataTable.isDataTable('#tabel2')) $('#tabel2').DataTable().destroy();
  if ($.fn.DataTable.isDataTable('#tabel3')) $('#tabel3').DataTable().destroy();

  // Fetch data asynchronously
  $.ajax({
    url: "{!! url('prbloadall') !!}",
    type: "GET",
    success: function(res) {
      console.log(res);

      const dataRefreshOutstanding = res.listPRB || [];
      const dataRefreshOutstanding2 = res.listSdhOto || [];
      const dataRefreshOutstanding3 = res.listRJual || [];

      // ========== TABLE 1 ==========
      let rowTable = "";
      if (dataRefreshOutstanding.length > 0) {
        // If it's a 2D array (like [ [ {...}, {...} ] ]), use [0]
        const list1 = Array.isArray(dataRefreshOutstanding[0]) ? dataRefreshOutstanding[0] : dataRefreshOutstanding;
        list1.forEach((item) => {
          const date1 = item.TANGGAL ? formatDate(item.TANGGAL) : "";
          rowTable += `
            <tr>
              <td class="text-center" style="white-space:nowrap;">
                <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('${item.NOBUKTI}')">
                  <i class="bi bi-info"></i>
                </button>
                <button class="btn btn-success btn-sm" type="button" onclick="buttonEdit('${item.NOBUKTI}')">
                  <i class="bi bi-pen"></i>
                </button>
                <button class="btn btn-primary btn-sm" type="button" onclick="buttonOtorisasi('${item.NOBUKTI}')">
                  <i class="bi bi-key"></i>
                </button>
                <button class="btn btn-danger btn-sm" type="button" onclick="buttonDelete('${item.NOBUKTI}')">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
              <td style="white-space:nowrap;">${item.NOBUKTI}</td>
              <td style="white-space:nowrap;">${date1}</td>
              ${
                item.IsOtorisasi1 == 1
                  ? '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display:none">1</div></i></td>'
                  : '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display:none">0</div></i></td>'
              }
            </tr>`;
        });
      }
      $("#tabel_data").html(rowTable);
      $("#tabel").DataTable({ lengthChange: false, paging: false });


      // ========== TABLE 2 ==========
      let rowTable2 = "";
      if (dataRefreshOutstanding2.length > 0) {
        const list2 = Array.isArray(dataRefreshOutstanding2[0]) ? dataRefreshOutstanding2[0] : dataRefreshOutstanding2;
        list2.forEach((item) => {
          const date1 = item.Tanggal ? formatDate(item.Tanggal) : "";
          const date2 = item.TglOto1 ? formatDate(item.TglOto1) : "";

          rowTable2 += `
            <tr>
              <td class="text-center" style="white-space:nowrap;">
                <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('${item.NOBUKTI}')">
                  <i class="bi bi-info"></i>
                </button>
                <button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('${item.NOBUKTI}')">
                  <i class="bi bi-key"></i>
                </button>
              </td>
              <td style="white-space:nowrap;">${item.NOBUKTI}</td>
              <td style="white-space:nowrap;">${date1}</td>
              ${
                item.IsOtorisasi1 == 1
                  ? '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display:none">1</div></i></td>'
                  : '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display:none">0</div></i></td>'
              }
              <td style="white-space:nowrap;">${item.OtoUser1 || ""}</td>
              <td style="white-space:nowrap;">${date2}</td>
            </tr>`;
        });
      }
      $("#tabel2_data").html(rowTable2);
      $("#tabel2").DataTable({ lengthChange: false, paging: false });


      // ========== TABLE 3 ==========
      let rowTable3 = "";
      if (dataRefreshOutstanding3.length > 0) {
        const list3 = Array.isArray(dataRefreshOutstanding3[0]) ? dataRefreshOutstanding3[0] : dataRefreshOutstanding3;
        list3.forEach((item) => {
          rowTable3 += `
            <tr>
              <td style="white-space:nowrap;">${item.Nobukti}</td>
              <td style="white-space:nowrap;">${item.Kodebrg}</td>
              <td style="white-space:nowrap;">${item.NAMABRG}</td>
              <td style="white-space:nowrap;">${item.Satuan}</td>
              <td class="text-right">${formatAngka(parseFloat(item.Qnt || 0).toFixed(2))}</td>
            </tr>`;
        });
      }
      $("#tabel3_data").html(rowTable3);
      $("#tabel3").DataTable({ lengthChange: false, paging: false });

      console.log("Finished rendering all tables");
    },
    error: function(err) {
      console.error("Error loading data:", err);
    }
  });
}


// ========== Helper: Date Formatter ==========
function formatDate(dateString) {
  const date = new Date(dateString);
  if (isNaN(date)) return "";
  const day = ("0" + date.getDate()).slice(-2);
  const month = ("0" + (date.getMonth() + 1)).slice(-2);
  return `${date.getFullYear()}/${month}/${day}`;
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
      document.getElementById("input_add_add_namabarangasli").value = tempAddAdd.NamaBrg
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
  document.getElementById("input_add_add_namabarangasli").value = tempAddAdd.NamaBrg
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


let cekQntPR = 0
let cekQntPO = 0
let cekQntSisa = 0

function cekQntStock () {
  
  tempStockAdd = dataAddAddListItem[0]

  let currentQntPO = 0

  cekQntPR = tempStockAdd.Qnt
  
  cekQntPO = tempStockAdd.QntPO
  cekQntSisa = tempStockAdd.SisaPPL

  currentQntPO = parseInt(document.getElementById("input_add_add_qty").value) || 0

  console.log(currentQntPO + ' current qnt PO')

  if (currentQntPO > cekQntSisa) {
    alertify.warning('Qty PO Tidak boleh melebihi Qty Sisa')
    document.getElementById("input_add_add_qty").value = '0.00'
  }
  
}

function buttonAddAddPickBarangNonFOC (index , pEdit = 0) {

  let _token  = $("#_token").val()

  console.log(dataAddAddListItem[index])
  tempAddAdd = dataAddAddListItem[index]
  
  cekSatuanBarang(tempAddAdd.KodeBrg)

  document.getElementById("input_add_add_kodebarang").value = tempAddAdd.KodeBrg
  document.getElementById("input_add_add_namabarang").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_namabarangasli").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_qty").value = tempAddAdd.SisaPPL
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
          <td>${formatAngka(item.QNT)}</td>
          <td>${item.SATUAN}</td>
          <td>${item.KODEVLS}</td>
          <td>${item.KURS}</td>
          <td class="text-right">${Number(item.HARGA) ? parseFloat(item.HARGA).toFixed(2) : '0.00'}</td>
          <td>${item.DISCRP}</td>
          <td class="text-right">${Number(item.HrgNetto) ? parseFloat(item.HrgNetto).toFixed(2) : '0.00'}</td>
        </tr>`
      });

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
  document.getElementById("input_add_add_namabarangasli").value = tempAddAdd.NamaBrg
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
          <td>${formatAngka(item.QNT)}</td>
          <td>${item.SATUAN}</td>
          <td>${item.KODEVLS}</td>
          <td>${item.KURS}</td>
          <td class="text-right">${Number(item.HARGA) ? parseFloat(item.HARGA).toFixed(2) : '0.00'}</td>
          <td>${item.DISCRP}</td>
          <td class="text-right">${Number(item.HrgNetto) ? parseFloat(item.HrgNetto).toFixed(2) : '0.00'}</td>
        </tr>`
      });

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

let tempSatuanBarang = []

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

function buttonAddPickPelanggan (kode, nama , alamat, hari, ppn) {
  console.log('buttonAddPickPelanggan')

  setNewNoBukti(ppn)

  document.getElementById("input_add_kodesupplier").value = kode
  document.getElementById("input_add_namasupplier").value = nama
  document.getElementById("input_add_alamatsupplier").value = alamat
  document.getElementById("input_add_pembayaran").value = 0
  document.getElementById("input_add_hari").value = hari
  document.getElementById("input_add_kodealamatkirim").value = ''
  document.getElementById("input_add_alamatkirim").value = ''
  document.getElementById("input_add_kodepic").value = ''
  document.getElementById("input_add_namapic").value = ''
  document.getElementById("input_add_kodeekspedisi").value = '-'
  document.getElementById("input_add_ekspedisi").value = '-'

  if (hari == 0 ){
    selectTipeBayar = `<option value=0 selected>Non-Kredit</option>
    <option value=1>Kredit</option>`
  }
  else if (hari != 0){
    selectTipeBayar = `
    <option value=0 >Non-Kredit</option>
    <option value=1 selected>Kredit</option>`
  }

  if (ppn == 1){
    selectTipePPN = `
    <option value=1>Exclude</option>
    <option value=2>Include</option>`
  } else if (ppn == 0) {
    selectTipePPN = `
    <option value=0>None</option>`
  }

  document.getElementById("input_add_pembayaran").innerHTML = selectTipeBayar
  document.getElementById("input_add_tipeppn").innerHTML = selectTipePPN

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
  document.getElementById("input_add_add_norjual").value = '-'
  document.getElementById("input_add_add_nobeli").value = '-'
  document.getElementById("input_add_add_supplier").value = ''
  document.getElementById("input_add_add_kodebrg").value = ''
  document.getElementById("input_add_add_namabrg").value = ''
  document.getElementById("input_add_add_quantity").value = ''
  document.getElementById("input_add_add_satuan").value = ''
  document.getElementById("input_add_add_gudang").value = ''
  document.getElementById("input_add_add_keterangan").value = '' 

}

function lockFormAdd () {
  document.getElementById("input_add_tipeppn").disabled = true
  document.getElementById("input_add_pembayaran").disabled = true
  document.getElementById("input_add_nopocust").disabled = true
  document.getElementById("input_add_noso").disabled = true
  document.getElementById("input_add_keterangan").disabled = true
  document.getElementById("input_add_tanggalkirim").disabled = true
  document.getElementById("input_add_tanggalkirim").disabled = true
  document.getElementById("input_add_hari").disabled = true
  document.getElementById("input_add_draftpo").disabled = true
  document.getElementById("input_add_add_discpersen1").disabled = true
  document.getElementById("input_add_add_discpersen2").disabled = true
  document.getElementById("input_add_add_discpersen3").disabled = true
  document.getElementById("input_add_add_foc").disabled = true

  document.getElementById("buttonAddListPelanggan").hidden = true
  document.getElementById("buttonAddListGudang").hidden = true
  document.getElementById("buttonAddListSales").hidden = true
  document.getElementById("buttonAddListValas").hidden = true
  document.getElementById("buttonAddListPIC").hidden = true
  document.getElementById("buttonAddListLokasiPenerima").hidden = true
  document.getElementById("buttonAddListBackOffice").hidden = true
  document.getElementById("buttonAddListBackOffice").hidden = true
  document.getElementById("buttonAddListNoSo").hidden = true
  document.getElementById("buttonTambahItem").hidden = true

  document.getElementById("input_add_disc").disabled = true
  document.getElementById("input_add_discrp").disabled = true
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

}

function cleanFormAdd () { 
  
  document.getElementById("input_add_tipebayar").value = ''
  document.getElementById("input_add_keterangan").value = ''
}

function buttonEdit (NOBUKTI) {
  tipeform = 'edit'
  console.log('buttonEdit' , NOBUKTI)

  $('.showhide').hide();
  // $('.showhidemodalbodyaddmain').hide();
  $('#buttonSubmitSaveHeader').show();
  // unlockFormAdd()

  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  let _token  = $("#_token").val()
  let oto = 1

  $.ajax({
    url: "{!! url('prbcekotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: NOBUKTI
    },
    success: function(res) {
      console.log(res)
      oto = res[0].isOtorisasi
    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

  if (oto == 1) {
    alertify.warning("Sudah diotorisasi")
    return
  }

  $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddListPelanggan').show();
  $('#modalBodyAddMain').show();
  refreshDataTableAdd(NOBUKTI)
  // $("#form").modal('toggle')
  $('#page1').hide();
  $('#page2').show();
}

 let noBuktiUntukAdd = 0

function buttonAdd (noBukti) {
  tipeform = 'add'

  if (noBukti != null){
  noBuktiUntukAdd = noBukti
  }
  if ( noBukti == null){
    noBuktiUntukAdd = 0
  }

  $('.showhide').hide();
  // $('.showhidemodalbodyaddmain').hide();
  $('#buttonSubmitSaveHeader').hide();
  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  dataTableAdd = []
  // cleanFormAdd()
  // cleanFormAddAdd()
  // unlockFormAdd()
  setNewNoBukti();

  refreshDataTableAdd()

  $('#page1').hide();
  $('#page2').show();

}

function buttonCloseForm () {
  $('#page3').hide();
  $('#page2').hide();
  $('#page1').show();

}

function buttonCloseFormDetail () {
  $('#page3').hide();
  $('#page1').show();

}

function submitAdd () 
{

  let alamatpelanggan = $("#input_add_alamatsupplier").val();
  console.log(alamatpelanggan)
  let catatan = $("#input_add_keterangan").val();
  console.log(catatan)

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
  console.log('button Detail' , NOBUKTI)

  $('.showhide').hide();
  // $('.showhidemodalbodyaddmain').hide();
  $('#buttonSubmitSaveHeader').show();
  lockFormAdd()

  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddListPelanggan').show();
  $('#modalBodyAddMain').show();
  refreshDataTableAdd(NOBUKTI)
  // $("#form").modal('toggle')
  $('#page1').hide();
  $('#page2').show();
}

function refreshDataTableAdd (NOBUKTI) {

  console.log('refreshDataTableAdd' , NOBUKTI)
  if (!NOBUKTI) {
    
    // if(!dataTableAdd.length) {
      let rowTable = `<tr>
      <td class="text-center" colspan="9">Belum ada barang</td>
      </tr>`
    // }
    document.getElementById("tabel_data_add").innerHTML = rowTable
  } else {

    let _token  = $("#_token").val()

    $.ajax({
      url: "{!! url('prbgetdetail') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti: NOBUKTI
      },
      success: function(res) {
        console.log('aaa')
        console.log('res' , res)

        if (!res.list.length) {
          alertify.warning("Data habis")
          //  $("#form").modal('toggle')
          $('#page3').hide();
          $('#page2').hide();
          $('#page1').show();
        } else {
          dataHeaderAdd = res.list[0]
          dataTableAdd = res.list

          let rowTable = ""
          dataTableAdd.forEach((item, i) => {

            rowTable += 
            `<tr>
              <td class="text-center">
                ${tipeform == 'edit' ? 
                `<button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
                <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDeleteItem(${i})"><i class="bi bi-trash"></i></button>`
                : `-`
                }
              </td>
              <td>${item.KODEBRG}</td>
              <td>${item.NamaBrg}</td>
              <td class="text-right">${item.Qnt ? formatAngka(item.Qnt) : '0.00'}</td>
              <td class="text-center">${item.Satuan}</td>
              <td>${item.NORJual}</td>
              <td>${item.nopbl}</td>
            </tr>`
          });

          if(!dataTableAdd.length) {
            rowTable = `<tr>
            <td class="text-center" colspan="9">Belum ada barang</td>
            </tr>`
          }
          document.getElementById("tabel_data_add").innerHTML = rowTable

          document.getElementById("input_add_nobukti").value = dataHeaderAdd.NOBUKTI
          document.getElementById("input_add_tanggal").value = formatDate(dataHeaderAdd.TANGGAL)
          document.getElementById("input_add_tipebayar").value = dataHeaderAdd.TIPEBAYAR
          document.getElementById("input_add_keterangan").value = dataHeaderAdd.KETERANGAN

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

function formatDate(date) {
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

function LockFreeOfCharge(){
  let focState = document.getElementById('input_add_add_foc').value

  if (focState == 1){
    document.getElementById('input_add_add_harga').disabled = true;
    document.getElementById('input_add_add_discrp').disabled = true;
    document.getElementById('input_add_add_discpersen1').disabled = true;
    document.getElementById('input_add_add_discpersen2').disabled = true;
    document.getElementById('input_add_add_discpersen3').disabled = true;
    
    document.getElementById('input_add_add_harga').value = 0,00 ;
    document.getElementById('input_add_add_discrp').value = 0,00 ;
    document.getElementById('input_add_add_discpersen1').value = 0 ;
    document.getElementById('input_add_add_discpersen2').value = 0 ;
    document.getElementById('input_add_add_discpersen3').value = 0 ;
  } else {
    document.getElementById('input_add_add_harga').disabled = false;
    document.getElementById('input_add_add_discrp').disabled = false;
    document.getElementById('input_add_add_discpersen1').disabled = false;
    document.getElementById('input_add_add_discpersen2').disabled = false;
    document.getElementById('input_add_add_discpersen3').disabled = false;
  }

}

</script>
{{-- script buat hover po belum otorisasi dan sudah otorisasi --}}
  <script>
    const tabHome = document.getElementById('nav-home-tab');
    const tabProfile = document.getElementById('nav-profile-tab');
    const tabProfile1 = document.getElementById('nav-profile1-tab');
  
    function setActiveTab(homeActive) {
      if (homeActive == 0) {
        tabHome.style.backgroundColor = '#007bff';
        tabHome.style.color = '#fff';
        tabProfile.style.backgroundColor = '#f8f9fa';
        tabProfile.style.color = '#007bff';

        tabProfile1.style.backgroundColor = '#f8f9fa';
        tabProfile1.style.color = '#007bff';

      } else if (homeActive == 1){
        tabHome.style.backgroundColor = '#f8f9fa';
        tabHome.style.color = '#007bff';

        tabProfile.style.backgroundColor = '#007bff';
        tabProfile.style.color = '#fff';

        tabProfile1.style.backgroundColor = '#f8f9fa';
        tabProfile1.style.color = '#007bff';
      }
      else if (homeActive == 2){
        tabProfile.style.backgroundColor = '#f8f9fa';
        tabProfile.style.color = '#007bff';

        tabHome.style.backgroundColor = '#f8f9fa';
        tabHome.style.color = '#007bff';

        tabProfile1.style.backgroundColor = '#007bff';
        tabProfile1.style.color = '#fff';
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

    function performSearch () {
      const searchValue = document.getElementById('input_add_add_kodebarang').value.trim();
    
      buttonAddAddListBarang();
    
      // Apply search to all DataTables
      $('#tabel_add_list_barang_nonfoc').DataTable().search(searchValue).draw();
      $('#tabel_add_list_barang_nonfocplus').DataTable().search(searchValue).draw();
      $('#tabel_add_list_barang_foc').DataTable().search(searchValue).draw();
    }

    // Keyboard event
    document.getElementById('input_add_add_kodebarang').addEventListener('keypress', function(event) {
      if (event.key === 'Enter') {
          event.preventDefault();
          performSearch();
      }
    });


  function buttonNoRJual () {
  console.log('asd');

  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('prblistnorjual') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token
    },
    success: function (res) {
      console.log(res);
      dataRefresh = res;
    },
  });

  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr>
      <td class="text-center">
        <button class="btn btn-primary btn-sm" type="button" onclick="buttonSelectNoRJual('${item.NoBukti}','${item.Urut}')"><i class="bi bi-plus"></i></button>
      </td>
      <td>${item.NoBukti}</td>
      <td>${item.KodeCustSupp}</td>
      <td>${item.NamaCustSupp}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Actions</th>
    <th scope="col">No. Bukti</th>
    <th scope="col">Kode Customer</th>
    <th scope="col">Nama Customer</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'No. Retur Jual'

  $("#tabelModalOpen").DataTable({
    "lengthChange": true,
    "paging": true,
  });
  
  $("#formModalOpen").modal('toggle')
}

function buttonSelectNoRJual(NoBukti, urut){
  document.getElementById('input_add_add_norjual').value = NoBukti;
  document.getElementById('input_add_add_urutRJual').value = urut;
  // document.getElementById('input_detailAkun_edit_hutPiut').value = perkiraan;

  $("#formModalOpen").modal("hide");
}

function buttonNoBeli () {
  console.log('asd');

  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('prblistnobeli') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token
    },
    success: function (res) {
      dataRefresh = res;
    },
  });

  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr>
      <td class="text-center">
        <button class="btn btn-primary btn-sm" type="button" onclick="buttonSelectNoBeli('${item.NoBukti}','${item.urut}')"><i class="bi bi-plus"></i></button>
      </td>
      <td>${item.NoBukti}</td>
      <td>${item.Tanggal}</td>
      <td>${item.KodeSupp}</td>
      <td>${item.namaCustSupp}</td>
      <td>${item.Kodegdg}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Actions</th>
    <th scope="col">No. Bukti</th>
    <th scope="col">Tanggal</th>
    <th scope="col">Kode Supp</th>
    <th scope="col">Nama Supp</th>
    <th scope="col">Kode Gudang</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'No. Beli'

  $("#tabelModalOpen").DataTable({
    "lengthChange": true,
    "paging": true,
  });
  
  $("#formModalOpen").modal('toggle')
}

function buttonSelectNoBeli(NoBukti, urut){
  document.getElementById('input_add_add_nobeli').value = NoBukti;
  document.getElementById('input_add_add_urutPbl').value = urut;
  // document.getElementById('input_detailAkun_edit_hutPiut').value = perkiraan;

  $("#formModalOpen").modal("hide");
}

function buttonSupplier () {
  console.log('asd');

  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('prblistsupplier') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token
    },
    success: function (res) {
      dataRefresh = res;
    },
  });

  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr>
      <td class="text-center">
        <button class="btn btn-primary btn-sm" type="button" onclick="buttonSelectSupplier('${item.KodeCustSupp}')"><i class="bi bi-plus"></i></button>
      </td>
      <td>${item.KodeCustSupp}</td>
      <td>${item.NamaCustSupp}</td>
      <td>${item.Alamat}</td>
      <td>${item.namaKota}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Actions</th>
    <th scope="col">Kode</th>
    <th scope="col">Nama</th>
    <th scope="col">Alamat</th>
    <th scope="col">Kota</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'No. Beli'

  $("#tabelModalOpen").DataTable({
    "lengthChange": true,
    "paging": true,
  });
  
  $("#formModalOpen").modal('toggle')
}

function buttonSelectSupplier (NoBukti){
  document.getElementById('input_add_add_supplier').value = NoBukti;
  // document.getElementById('input_detailAkun_edit_hutPiut').value = perkiraan;

  $("#formModalOpen").modal("hide");
}

function buttonGudang () {
  console.log('asd');

  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('prblistgudang') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token
    },
    success: function (res) {
      dataRefresh = res;
    },
  });

  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr>
      <td class="text-center">
        <button class="btn btn-primary btn-sm" type="button" onclick="buttonSelectGudang('${item.KodeGdg}')"><i class="bi bi-plus"></i></button>
      </td>
      <td>${item.KodeGdg}</td>
      <td>${item.Nama}</td>
      <td>${item.Alamat}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Actions</th>
    <th scope="col">Kode</th>
    <th scope="col">Nama</th>
    <th scope="col">Alamat</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'No. Beli'

  $("#tabelModalOpen").DataTable({
    "lengthChange": true,
    "paging": true,
  });
  
  $("#formModalOpen").modal('toggle')
}

function buttonSelectGudang (NoBukti){
  document.getElementById('input_add_add_gudang').value = NoBukti;
  // document.getElementById('input_detailAkun_edit_hutPiut').value = perkiraan;

  $("#formModalOpen").modal("hide");
}


function buttonBarang () {
  console.log('asd');

  let _token = $("#_token").val();

  const isEmpty = (value) => !value || value === '-';

  let kodeJual = document.getElementById('input_add_add_norjual').value
  let kodeBeli = document.getElementById('input_add_add_nobeli').value

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

if(isEmpty(kodeBeli)&&isEmpty(kodeJual)){
  alertify.warning('No Jual dan No Beli Kosong')
  return;
}

if (isEmpty(kodeBeli)){
  $.ajax({
    url: "{!! url('prblistbarangJualTanpaBeli') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
      kodeJual
    },
    success: function (res) {
      dataRefresh = res;

      let rowTable = "";
      dataRefresh.forEach((item, i) => {
        let temp = "";

        rowTable += `<tr>
          <td class="text-center">
            <button class="btn btn-primary btn-sm" type="button" onclick="buttonSelectBarang('${item.Kodebrg}','${item.NamaBrg}', ${item.Qnt}, '${item.satuan}', '${item.Nosat}', '${item.ISI1}', '${item.ISI2}' )"><i class="bi bi-plus"></i></button>
          </td>
          <td>${item.Kodebrg}</td>
          <td>${item.NamaBrg}</td>
        </tr>`;
      });

      document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

      let headerTable = `
      <tr>
        <th scope="col">Actions</th>
        <th scope="col">Kode Barang</th>
        <th scope="col">Nama Barang</th>
      </tr>
      `
      document.querySelector("#theadOpen").innerHTML = headerTable;

    },
  });
} else if (isEmpty(kodeJual)){
  $.ajax({
    url: "{!! url('prblistbarangBeliTanpaJual') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
      noBeli : kodeBeli
    },
    success: function (res) {
      dataRefresh = res;
      
      let rowTable = "";
      dataRefresh.forEach((item, i) => {
        let temp = "";

        rowTable += `<tr>
          <td class="text-center">
            <button class="btn btn-primary btn-sm" type="button" onclick="buttonSelectBarang('${item.KODEBRG}','${item.NAMABRG}',${item.Qnt}, '${item.SATUAN}', '${item.NOSAT}', '${item.ISI1}', '${item.ISI2}')"><i class="bi bi-plus"></i></button>
          </td>
          <td>${item.KODEBRG}</td>
          <td>${item.NAMABRG}</td>
        </tr>`;
      });

      document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

      let headerTable = `
      <tr>
        <th scope="col">Actions</th>
        <th scope="col">Kode Barang</th>
        <th scope="col">Nama Barang</th>
      </tr>
      `
      document.querySelector("#theadOpen").innerHTML = headerTable;
    },
  });

} else {
  $.ajax({
    url: "{!! url('prblistbarangJualDanBeli') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
      noBeli : kodeBeli,
      kodeJual
    },
    success: function (res) {
      dataRefresh = res;

      let rowTable = "";
      dataRefresh.forEach((item, i) => {
        let temp = "";

        rowTable += `<tr>
          <td class="text-center">
            <button class="btn btn-primary btn-sm" type="button" onclick="buttonSelectBarang('${item.KODEBRG}','${item.NAMABRG}',${item.QntTerima}, '${item.SATUAN}', '${item.NOSAT}', '${item.ISI1}', '${item.ISI2}')"><i class="bi bi-plus"></i></button>
          </td>
          <td>${item.KODEBRG}</td>
          <td>${item.NAMABRG}</td>
        </tr>`;
      });

      document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

      let headerTable = `
      <tr>
        <th scope="col">Actions</th>
        <th scope="col">Kode Barang</th>
        <th scope="col">Nama Barang</th>
      </tr>
      `
      document.querySelector("#theadOpen").innerHTML = headerTable;
    },
  });

}
  
  document.getElementById("namaModalOpen").innerHTML = 'Barang'

  $("#tabelModalOpen").DataTable({
    "lengthChange": true,
    "paging": true,
  });
  
  $("#formModalOpen").modal('toggle')
}

function buttonSelectBarang (kodebrg, namabrg, qnt, satuan, nosat, isi1, isi2){
  document.getElementById('input_add_add_kodebrg').value = kodebrg;
  document.getElementById('input_add_add_namabrg').value = namabrg;
  document.getElementById('input_add_add_quantity').value = qnt;

  let satuanTemp = `
    <option value="${satuan}" selected>${satuan}</option>
  `

  document.getElementById('input_add_add_satuan').innerHTML = satuanTemp

      nosatTemp = nosat ?? nosatTemp;
      isi1Temp = isi1 ?? isi1Temp;
      isi2Temp = isi2 ?? isi2Temp;

      console.log(nosatTemp, isi1Temp, isi2Temp)

  // document.getElementById('input_detailAkun_edit_hutPiut').value = perkiraan;

  $("#formModalOpen").modal("hide");
}

  </script>

@endsection
