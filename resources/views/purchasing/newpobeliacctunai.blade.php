  @extends('purchasing.newmaster')
  @section('buttons')

  {{-- tampilan search bar 1 --}}
    @section('css')
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

        <style>
        .modal-xxl {
            max-width: 95%;
        }
        .table-responsive {
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
  {{-- end tampilan search bar 1 --}}

  {{-- tampilan search bar 2 --}}
    <style>
    #tabel_pembelian_filter {
        display: flex;
        align-items: flex-end;
        margin-top: 8px;
        margin-right: 10px;
        margin-bottom: -10px;
      }

    #tabel_pembelian_filter label input {
        width: 150px;
        padding: 5px 10px;
        border-radius: 10px;
        border: 1px solid #ccc;
        box-shadow: none;
        font-size: 0.65rem;
      }

    #tabel_pembelian_filter label {
        font-weight: 600;
        font-size: 0.9rem;
        color: #333;
      }

    #tabel_pembelian_filter input:focus {
        border-color: #007bff;
        outline: none;
      }
    </style>
  {{-- end tampilan search bar 2 --}}

  @endsection

  @section('content')

  
<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>


  <div class="container-fluid" style="margin-top:-80px;">
  <h1>Invoice Pembelian (Tunai)</h1>
  {{-- <button onclick="loadAll()">tes</button>  --}}
  </div>

  <!-- <div class="container"> -->

  <div id="printContainer" style="display:none">

  </div>

  <div id="tempPrintContainer" style="display:none">

  </div>
  <div class="container-fluid">
    <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
    <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />

    <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
    <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS!!}" />
    <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
    <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />

    <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

    <div class="card">
      <div class="card-header">
        <div class="row">
          <nav style="width: 100%;">
            <div class="nav nav-tabs col-12" id="nav-tab" role="tablist" style="border-bottom: 0;">
              <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab"
              aria-controls="nav-home" aria-selected="true"
              style="color: #fff; background-color: #007bff; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff;">
              Penerimaan Acc Tunai (Belum oto)
            </a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab"
              aria-controls="nav-profile" aria-selected="false"
              style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff;">
              Penerimaan Acc Tunai (Otorisasi)
            </a>
            </div>
          </nav>
        </div>
      </div>
      <div class="card-body" >
        <div class="tab-content" id="myTabContent">

          <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div class="row">
              <div class="col-12" style="overflow:auto; padding:0; margin:0; width:100%;">
                <div class="container-fluid">
                    <table id="tabel" class="table table-bordered table-striped">
                      <thead class="text-center bg-primary text-white">
                          <tr style='white-space:nowrap;'>
                            <th scope="col">Actions</th>
                            <th scope="col">No. Bukti</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Nama Supplier</th>
                            <th scope="col">Keterangan</th>
                            <th scope="col">NO.PO</th>
                            <th scope="col">DPP</th>
                            <th scope="col">PPN</th>
                            <th scope="col">TOTAL</th>
                          </tr>
                        </thead>

                        <tbody id="tabel_data" class="text-left" style='white-space:nowrap;' >
                          @foreach($tempPo as $tempPoData)
                            <tr>
                            <td>
                                <button style="" class="btn btn-warning btn-sm" type="button" title="Details" onclick="buttonDetail('{{ $tempPoData->NoBukti}}')"><i class="bi bi-info"></i></button>
                                <button style="" class="btn btn-success btn-sm" type="button" title="Edit" onclick="buttonEditPembelian('{{ $tempPoData->NoBukti}}')"><i class="bi bi-pen"></i></button>
                                <button style="" class="btn btn-primary btn-sm" type="button" title="Otorisasi" onclick="buttonOtoPembelian('{{ $tempPoData->NoBukti}}')"><i class="bi bi-key"></i></button>
                            </td>
                            <td>{{ $tempPoData->NoBukti}}</td>
                            <td>{!! date("Y/m/d", strtotime($tempPoData->TglBeli)) !!}</td>
                            <td>{{ $tempPoData->NAMACUSTSUPP}}</td>
                            <td>{{ $tempPoData->KETERANGAN ?? '' }}</td>
                            <td>{{ $tempPoData->NoPO ?? '' }}</td>

                            <td class="text-right">{{ number_format($tempPoData->TotDPP , 2 ,'.' , ',') }}</td>
                            <td class="text-right">{{ number_format($tempPoData->TotPPN , 2 ,'.' , ',') }}</td>
                            <td class="text-right">{{ number_format($tempPoData->TotNet , 2 ,'.' , ',') }}</td>
                          </tr>
                          @endforeach

                        </tbody>

                      </table>
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div class="row">
              <div class="col-12" style="overflow:auto; padding:0; margin:0; width:100%;">
                <div class="container-fluid">
                  <table id="tabel_pembelian" class="table table-bordered table-striped"  >
                    <thead class="text-center bg-primary text-white">
                      <tr style='white-space:nowrap;'>
                        <th>Actions</th>
                        <th>No. Bukti</th>
                        <th>Tanggal</th>
                        <th>Nama Supplier</th>
                        <th scope="col">Keterangan</th>
                        <th>No. PO</th>
                        <th>Gudang</th>
                        <th>FakturSupp</th>
                        <th scope="col">DPP</th>
                        <th scope="col">PPN</th>
                        <th scope="col">TOTAL</th>
                        <th scope="col">User Oto1</th>
                        <th scope="col">Tgl oto1</th>
                      </tr>
                    </thead>
                    <tbody id="tabel_pembelian_data" class="text-left">
                        @foreach($tempPembelian1 as $tempPembelianData)
                        <tr style='white-space:nowrap;'>
                          <td>

                            <button style="" class="btn btn-warning btn-sm" type="button" title="Details" onclick="detailPembelian('{{ $tempPembelianData->NoBukti}}')"><i class="bi bi-info"></i></button>
                            <!-- <button style="" class="btn btn-primary btn-sm" type="button" title="Print"  onclick="buttonPrintPembelian('{{$tempPembelianData->NoBukti}}')" ><i class="bi bi-printer"></i></button> -->
                            <button style="" class="btn btn-danger btn-sm" type="button" title="Otorisasi" onclick="buttonUnOtoPembelian('{{ $tempPembelianData->NoBukti}}')"><i class="bi bi-key"></i></button>
                            <button style="" class="btn btn-primary btn-sm" type="button" title="Print" onclick="submitPrint('{{ $tempPembelianData->NoBukti}}')"><i class="bi bi-printer"></i></button>
                          </td>
                          <!-- <td colspan="7">Tidak ada data po ditemukan.</td> -->
                          <td>{{ $tempPembelianData->NoBukti}}</td>
                          <td>{!! date("Y/m/d", strtotime($tempPembelianData->TglBeli)) !!}</td>
                          <td>{{ $tempPembelianData->NAMACUSTSUPP}}</td>
                          <td>{{ $tempPembelianData->KETERANGAN}}</td>
                          <td>{{ $tempPembelianData->NoPO}}</td>
                          <td>{{ $tempPembelianData->NAMAGUDANG}}</td>
                          <td>{{ $tempPembelianData->FAKTURSUPP}}</td>

                          <td  class="text-right">{{ number_format($tempPembelianData->TotDPP , 2 ,'.' , ',') }}</td>
                          <td  class="text-right">{{ number_format($tempPembelianData->TotPPN , 2 ,'.' , ',') }}</td>
                          <td  class="text-right">{{ number_format($tempPembelianData->TotNet , 2 ,'.' , ',') }}</td>
                          <td>{{ $tempPembelianData->OtoUser1}}</td>
                          <td>{{ $tempPembelianData->TglOto1}}</td>
                        </tr>
                        @endforeach
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





    <!-- start modal add -->
   <div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content rounded-4">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Add</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <!-- <h1>Tes Modal</h1> -->
            <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
            <input type="hidden" name="noUrut" id="input_add_noUrut" value="{!! csrf_token() !!}" />
            <div class="container-fluid">
              <div class="row">


                <div class="col-12">
                  <div class="form-group">
                    <label>No Bukti</label>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_add_nobukti" placeholder="Surat Jln" disabled>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group">
                    <label>Tanggal</label>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group">
                    <input type="date" class="form-control" id="input_add_tanggal" value="{!! date('Y-m-d') !!}" >
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <div class="form-group">
                    <label>Nomor PO</label>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_add_nomorpo" placeholder="Nomor PO" required disabled>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group">
                    <label>Gudang</label>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group">
                  <select id="input_add_gudang" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                    @foreach ($gudang as $g)
                    <option value="{{ $g->KODEGDG }}">{{ $g->KODEGDG }} {{ $g->NAMA }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <label>Surat Jln Supp</label>
                </div>
              </div>
              <div class="col-12">
                <div class="form-group">
                  <input type="text" autocomplete="off" class="form-control" id="input_add_suratjalansupp" placeholder="Surat Jln" required>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <label>No. Kend / Sopir</label>
                </div>
              </div>
              <div class="col-12">
                <div class="form-group">
                  <input type="text" autocomplete="off" class="form-control" id="input_add_nokend" placeholder="No Kend / Sopir" required>
                </div>
              </div>
            </div>
            </div>



          </div>
            <div class="container-fluid" style="overflow-x: auto;">

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
            <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button>
          </div>
        </div>
      </div>
     </div>
    </div>


  <!-- End modal add-->

  <!-- start modal edit pembelian  -->
<div class="modal fade" id="editPembelian" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xxl modal-dialog-centered" role="document" style="max-width: 95%;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editPembelianModalLabel">Detail</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="row">
          <input type="hidden" class="form-control" id="EditNourut">
          
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>No Bukti</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control text-left" id="EditNobukti" placeholder="" disabled>
                </div>
              </div>
              <div class="col-md-4" style='margin-top:-15px;'>
                <div class="form-group">
                  <label>Tanggal</label>
                </div>
              </div>
              <div class="col-md-8" style='margin-top:-15px;'>
                <div class="form-group">
                  <input type="date" class="form-control text-left" id="editPembelianDate" value="{!! date('Y-m-d') !!}" disabled>
                </div>
              </div>

              <div class="col-md-4" style='margin-top:-15px;'>
                <div class="form-group">
                  <label>Supplier</label>
                </div>
              </div>

              <div class="col-md-8" style='margin-top:-15px;'>
                <div class="input-group mb-3">
                  <input type="text" class="form-control text-left" placeholder="Kode Pelanggan" id="editPembelianKodeSupp" disabled>
                </div>
              </div>

              <div class="col-md-4" style='margin-top:-15px;'>
                <div class="form-group">
                  <label>No PO</label>
                </div>
              </div>

              <div class="col-md-8" style='margin-top:-15px;'>
                <div class="input-group mb-3">
                  <input type="text" class="form-control text-left" placeholder="No PO" id="editPembelianNoPO" disabled>
                </div>
              </div>

              <div class="col-md-4" style='margin-top:-15px;'>
                <div class="form-group">
                  <label>Surat Jln Supp</label>
                </div>
              </div>

              <div class="col-md-8" style='margin-top:-15px;'>
                <div class="input-group mb-3">
                  <input type="text" class="form-control text-left" placeholder="Surat Jalan Supplier" id="editPembelianFakturSupp" disabled>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <input type="text" class="form-control text-center" placeholder="Nama Pelanggan" id="editPembelianSupp" disabled>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <textarea style="width: 100%; resize: none" rows=3 placeholder="Alamat Pelanggan" class="form-control text-center" id="editPembelianAlamatSupp" disabled></textarea>
                </div>
              </div>

              <div class="col-md-12">
                <div class="row">
                  <div class="form-group col-4">
                    <label>Gudang</label>
                  </div>
                  <div class="col-md-8" style='margin-left:-32px;'>
                    <div class="form-group">
                      <input type="text" style="width: 220px;resize: none" class="form-control text-left" placeholder="Gudang" id="editPembeliangudang" disabled>
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
                  <div class="col-9">
                    <div class="form-group">
                      <label>Valas</label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="row">
                  <div class="col-md-12">
                    <div class="input-group form-group">
                      <input type="text" class="form-control" id="editPembelianvalas" disabled>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="row">
                  <div class="col-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <label>Kurs</label>
                    </div>
                  </div>
                  <div class="col-md-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <input type="text" class="form-control" id="editPembeliankurs" onchange="onchangekurs()">
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-12">
                <div class="row">
                  <div class="col-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <label>TOP</label>
                    </div>
                  </div>
                  <div class="col-md-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <input type="number" class="form-control text-left" id="editPembelianHari" value=0 min=0 disabled>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-12">
                <div class="row">
                  <div class="col-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <label>No/Sopir</label>
                    </div>
                  </div>
                  <div class="col-md-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <input type="text" class="form-control text-left" id="editPembelianNoSopir" disabled>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-12">
                <div class="row">
                  <div class="col-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <label>SO Cust</label>
                    </div>
                  </div>
                  <div class="col-md-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <input type="text" class="form-control text-left" id="editPembelianSoCustomer" disabled>
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
                  <div class="col-6">
                    <div class="form-group">
                      <label>Pembayaran</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <select onchange="onChangeInputAddPembayaran()" id="editPembeliantipebayar" class="form-control form-select-lg mb-3 text-center" aria-label=".form-select-lg example" disabled>
                        <option value=0 selected>Tunai</option>
                        <option value=1>Kredit</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-12">
                <div class="row">
                  <div class="col-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <label>Jth Tempo</label>
                    </div>
                  </div>
                  <div class="col-md-6" style='margin-top:-15px;'>
                    <input type="date" class="form-control text-center" id="editPembelianJthTempo" value="{!! date('Y-m-d') !!}" onblur="onChangeTgglKirim()" disabled>
                  </div>
                </div>
              </div> 

              <div class="col-md-12">
                <div class="row">
                  <div class="col-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <label>PPN</label>
                    </div>
                  </div>
                  <div class="col-md-6" style='margin-top:-15px;'>
                    <select onchange="onChangeTipePPN()" id="editPembeliantipeppn" class="form-control text-center form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
                      <option value=0 selected>None</option>
                      <option value=1>Exclude</option>
                      <option value=2>Include</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="col-md-12" hidden>
                <div class="row">
                  <div class="col-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <label>No Uang Muka</label>
                    </div>
                  </div>
                  <div class="col-md-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <input type="text" class="form-control text-left" id="editPembelianNoUangMuka" disabled>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-12">
                <div class="row">
                  <div class="col-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <label type="number">Uang Muka</label>
                    </div>
                  </div>
                  <div class="col-md-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <input type="number" class="form-control text-left" id="editPembelianNuangmuka" disabled>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="container-fluid mt-2">
          <div class="row">
            <div class="col-md-12 text-right">
              <button type="button" class="btn btn-success" style="height: 30px; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; transition: background-color 0.3s, box-shadow 0.3s; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="saveKetFaktur()">Save Faktur & Ket</button>
            </div>
          </div>
        </div>

        <!-- ADD DETAIL-->
        <div id="formPembelianAdd" class="container-fluid showhide">
          <div class="row">
            <div class="col-12">
              <h4>Add Item</h4>
            </div>
          </div>

          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label>Pilih Barang</label>
                <select onchange="changeSelectBarang()" id="editPembelianAddSelect" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                </select>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Kode Barang</label>
                <input id="editPembelianInputAddKode" type="text" class="form-control" disabled>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Nama Barang</label>
                <input id="editPembelianInputAddNamaBarang" type="text" class="form-control" disabled>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Qty</label>
                <input id="editPembelianInputAddQty" type="number" value="0.00" class="form-control">
              </div>
            </div>
          </div>

          <div class="row mt-2">
            <div class="col-md-3">
              <div class="form-group">
                <label>Satuan</label>
                <input type="text" id="editPembelianInputAddSatuan" value="PCS" class="form-control" disabled>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Qty POx</label>
                <input id="editPembelianInputAddQtyPO" type="number" value="0.00" class="form-control" disabled>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Qty OS</label>
                <input id="editPembelianInputAddQtyOS" type="number" value="0.00" class="form-control" disabled>
              </div>
            </div>
          </div>

          <div class="container-fluid mt-2">
            <div class="row">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" style="height: 30px; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; transition: background-color 0.3s, box-shadow 0.3s; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="buttonBatalShowHide()">Batal</button>
                <button type="button" style="height: 30px; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; transition: background-color 0.3s, box-shadow 0.3s; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="submitPembelianAdd()" class="btn btn-primary">Add Item</button>
              </div>
            </div>
          </div>
          <div class="line"></div>
        </div>

        <!-- EDIT DETAIL -->
        <div id="formPembelianEdit" class="container-fluid showhide">
          <div class="row">
            <div class="col-12">
              <label>Edit Item</label>
            </div>
          </div>

          <div class="row">
            <div class="col-md-3">
              <div class="row">
                <div class="col-md-5">
                  <div class="form-group">
                    <label>Kode Barang</label>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <input id="editPembelianInputEditKode" type="text" class="form-control" disabled>
                  </div>
                </div>
                <div class="col-md-5" style='margin-top:-15px;'>
                  <div class="form-group">
                    <label>Nama Barang</label>
                  </div>
                </div>
                <div class="col-md-12" style='margin-top:-15px;'>
                  <div class="form-group">
                    <input id="editPembelianInputEditNamaBarang" type="text" class="form-control" disabled>
                  </div>
                </div>

                <div class="col-md-5" style='margin-top:-15px;'>
                  <div class="form-group">
                    <label>Satuan</label>
                  </div>
                </div>
                <div class="col-md-12" style='margin-top:-15px;'>
                  <div class="input-group mb-3">
                    <input type="text" id="editPembelianInputAddSatuan2" value="PCS" class="form-control" disabled>
                  </div>
                </div>
                <div class="col-md-5" style='margin-top:-15px;'>
                  <div class="form-group">
                    <label>Qty</label>
                  </div>
                </div>
                <div class="col-md-12" style='margin-top:-15px;'>
                  <div class="input-group mb-3">
                    <input id="editPembelianInputEditQty" type="number" value="0.00" class="form-control">
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="row">
                <div class="col-md-5">
                  <div class="form-group">
                    <label>Harga</label>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="input-group mb-3">
                    <input id="editPembelianInputEditHarga" type="number" value="0.00" class="form-control">
                  </div>
                </div>

                <div class="col-md-5" style='margin-top:-15px;'>
                  <div class="form-group">
                    <label>Disc %</label>
                  </div>
                </div>
                <div class="col-md-12" style='margin-top:-15px;'>
                  <div class="input-group mb-3">
                    <input type="number" class="form-control text-right" id="editPembelianInputEditDisc" onChange="onChangeInputAddAddDisc()" value="0.00">
                  </div>
                </div>

                <div class="col-md-5" style='margin-top:-15px;'>
                  <div class="form-group">
                    <label>Disc Rp</label>
                  </div>
                </div>
                <div class="col-md-12" style='margin-top:-15px;'>
                  <div class="input-group mb-3">
                    <input type="number" class="form-control text-right" id="editPembelianInputEditDiscRp" onChange="onChangeInputAddAddDiscRp()" value="0.00">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="container-fluid mt-2">
            <div class="row">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" style="height: 30px; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; transition: background-color 0.3s, box-shadow 0.3s; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="buttonBatalShowHide()">Batal</button>
                <button type="button" class="btn btn-primary" style="height: 30px; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; transition: background-color 0.3s, box-shadow 0.3s; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="submitPembelianEdit()">Edit Item</button>
              </div>
            </div>
          </div>
        </div>

        <div class="container-fluid" style="overflow:auto;">
          <table id="editPembelianTable" class="table table-bordered table-striped mt-5">
            <thead class="text-center bg-primary text-white">
              <tr>
                <th scope="col">Kode Barang</th>
                <th scope="col">Nama Barang</th>
                <th scope="col">Qty</th>
                <th scope="col">Qty PO</th>
                <th scope="col">Satuan</th>
                <th scope="col">Harga</th>
                <th scope="col">Disc</th>
                <th scope="col">Subtotal</th>
                <th scope="col">Actions</th>
              </tr>
            </thead>
            <tbody id="editPembelianTableData" class="text-left">
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

        <!-- PPN GRANDTOTAL -->
        <div class="row mt-2">
          <div class="col">
            <div class="row">
              <div class="col-3 d-flex align-items-center">
                <label>Disc %</label>
              </div>
              <div class="col-8">
                <input type="number" class="form-control text-right" id="input_edit_disc" onblur="onChangeInputAddDisc()" value="0.00" disabled>
              </div>
            </div>
          </div>

          <div class="col">
            <div class="row">
              <div class="col-3 d-flex align-items-center">
                <label>DiscRp</label>
              </div>
              <div class="col-8">
                <input type="number" class="form-control text-right" id="input_edit_discrp" onblur="onChangeInputAddDiscRp()" value="0.00" disabled>
              </div>
            </div>
          </div>

          <div class="col">
            <div class="row">
              <div class="col-3 d-flex align-items-center">
                <label>DPP</label>
              </div>
              <div class="col-8">
                <input type="text" class="form-control text-right" id="input_edit_dpp" value="0.00" disabled>
              </div>
            </div>
          </div>

          <div class="col">
            <div class="row">
              <div class="col-3 d-flex align-items-center">
                <label>PPN</label>
              </div>
              <div class="col-8">
                <input type="text" class="form-control text-right" id="input_edit_ppn" value="0.00" disabled>
              </div>
            </div>
          </div>

          <div class="col">
            <div class="row">
              <div class="col-5 d-flex align-items-center">
                <label>GrandTotal</label>
              </div>
              <div class="col-7">
                <input type="text" class="form-control text-right" id="input_edit_grandtotal" value="0.00" disabled>
              </div>
            </div>
          </div>
        </div>
      <!-- PPN GRANDTOTAL -->

      <div class="modal-footer">
        <button type="button" id="btnotokananedit" class="btn btn-primary" style="height: 30px; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; transition: background-color 0.3s, box-shadow 0.3s; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="submitUnOtorisasi1()">Batal Oto</button>
        <!-- submitUnOtorisasi1   -->
        <!-- submitOtorisasi1 -->
      </div>
    </div>
  </div>
</div>
    </div>
    </div>




  <!-- End modal editpembelian-->

  <!-- //TAB KIRI -->

  <!-- start modal tab kiri detail INFORMASI-->
<div class="modal fade" id="detail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <input type="hidden" class="form-control" id="input_add_nourut">
                    
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>No Bukti</label>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <input type="text" class="form-control text-left" id="detailPembelianNobukti" placeholder="" disabled>
                                </div>
                            </div>
                            <div class="col-md-4" style='margin-top:-15px;'>
                                <div class="form-group">
                                    <label>Tanggal</label>
                                </div>
                            </div>
                            <div class="col-md-8" style='margin-top:-15px;'>
                                <div class="form-group">
                                    <input type="date" class="form-control text-left" id="detailDate" value="" disabled>
                                </div>
                            </div>

                            <div class="col-md-4" style='margin-top:-15px;'>
                                <div class="form-group">
                                    <label>Supplier</label>
                                </div>
                            </div>

                            <div class="col-md-8" style='margin-top:-15px;'>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control text-left" placeholder="Kode Pelanggan" id="detailPembelianKodeSupp" disabled>
                                </div>
                            </div>

                            <div class="col-md-4" style='margin-top:-15px;'>
                                <div class="form-group">
                                    <label>No PO</label>
                                </div>
                            </div>

                            <div class="col-md-8" style='margin-top:-15px;'>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control text-left" placeholder="No PO" id="detailNoPO" disabled>
                                </div>
                            </div>

                            <div class="col-md-4" style='margin-top:-15px;'>
                                <div class="form-group">
                                    <label>Surat Jln Supp</label>
                                </div>
                            </div>

                            <div class="col-md-8" style='margin-top:-15px;'>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control text-left" placeholder="Surat Jalan Supplier" id="detailFakturSupp" disabled>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="text" class="form-control text-center" placeholder="Nama Pelanggan" id="detailPembelianSupp" disabled>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <textarea style="width: 100%; resize: none" rows=3 placeholder="Alamat Pelanggan" class="form-control text-center" id="detailPembelianAlamatSupp" disabled></textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="row">
                                    <div class="form-group col-4">
                                        <label>Gudang</label>
                                    </div>

                                    <div class="col-md-8" style='margin-left:-32px;'>
                                        <div class="form-group">
                                            <input type="text" style="width: 220px;resize: none" class="form-control text-left" placeholder="Gudang" id="detailgudang" disabled>
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
                                    <div class="col-9">
                                        <div class="form-group">
                                            <label>Valas</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="input-group form-group">
                                            <input type="text" class="form-control" id="detailPembelianvalas" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-6" style='margin-top:-15px;'>
                                        <div class="form-group">
                                            <label>Kurs</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6" style='margin-top:-15px;'>
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="detailPembeliankurs" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-6" style='margin-top:-15px;'>
                                        <div class="form-group">
                                            <label>TOP</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style='margin-top:-15px;'>
                                        <div class="form-group">
                                            <input type="number" class="form-control text-left" id="detailPembelianhari" value=0 min=0 disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-6" style='margin-top:-15px;'>
                                        <div class="form-group">
                                            <label>No/Sopir</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style='margin-top:-15px;'>
                                        <div class="form-group">
                                            <input type="text" class="form-control text-left" id="detailNoSopir" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-6" style='margin-top:-15px;'>
                                        <div class="form-group">
                                            <label>SO Cust</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style='margin-top:-15px;'>
                                        <div class="form-group">
                                            <input type="text" class="form-control text-left" id="detailSoCustomer" disabled>
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
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>Pembayaran</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <select onchange="onChangeInputAddPembayaran()" id="detailPembeliantipebayar" class="form-control form-select-lg mb-3 text-center" aria-label=".form-select-lg example" disabled>
                                                <option value=0 selected>Tunai</option>
                                                <option value=1>Kredit</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-6" style='margin-top:-15px;'>
                                        <div class="form-group">
                                            <label>Jth Tempo</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style='margin-top:-15px;'>
                                        <input type="date" class="form-control text-center" id="detailPembelianJthTempo" value="{!! date('Y-m-d') !!}" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-6" style='margin-top:-15px;'>
                                        <div class="form-group">
                                            <label>PPN</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style='margin-top:-15px;'>
                                        <select onchange="onChangeTipePPN()" id="detailPembeliantipeppn" class="form-control text-center form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
                                            <option value=0 selected>None</option>
                                            <option value=1>Exclude</option>
                                            <option value=2>Include</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12" hidden>
                                <div class="row">
                                    <div class="col-6" style='margin-top:-15px;'>
                                        <div class="form-group">
                                            <label>No Uang Muka</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style='margin-top:-15px;'>
                                        <div class="form-group">
                                            <input type="text" class="form-control text-left" id="detailNoUangMuka" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-6" style='margin-top:-15px;'>
                                        <div class="form-group">
                                            <label type="number">Uang Muka</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style='margin-top:-15px;'>
                                        <div class="form-group">
                                            <input type="number" class="form-control text-left" id="detailNuangmuka" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="detailTable" class="table table-bordered table-striped">
                                <thead class="text-center bg-primary text-white">
                                    <tr>
                                        <th scope="col">Kode Barang</th>
                                        <th scope="col">Nama Barang</th>
                                        <th scope="col">Qty</th>
                                        <th scope="col">Qty PO</th>
                                        <th scope="col">Satuan</th>
                                        <th scope="col">Harga</th>
                                        <th scope="col">Disc</th>
                                        <th scope="col">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="detailTableData" class="text-left">
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

                <div class="row mt-2">
                    <div class="col">
                        <div class="row">
                            <div class="col-3 d-flex align-items-center">
                                <label>Disc %</label>
                            </div>
                            <div class="col-8">
                                <input type="number" class="form-control text-right" id="input_det_disc" onblur="onChangeInputAddDisc()" value="0.00" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="row">
                            <div class="col-3 d-flex align-items-center">
                                <label>DiscRp</label>
                            </div>
                            <div class="col-8">
                                <input type="number" class="form-control text-right" id="input_det_discrp" onblur="onChangeInputAddDiscRp()" value="0.00" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="row">
                            <div class="col-3 d-flex align-items-center">
                                <label>DPP</label>
                            </div>
                            <div class="col-8">
                                <input type="text" class="form-control text-right" id="input_det_dpp" value="0.00" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="row">
                            <div class="col-3 d-flex align-items-center">
                                <label>PPN</label>
                            </div>
                            <div class="col-8">
                                <input type="text" class="form-control text-right" id="input_det_ppn" value="0.00" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="row">
                            <div class="col-5 d-flex align-items-center">
                                <label>GrandTotal</label>
                            </div>
                            <div class="col-7">
                                <input type="text" class="form-control text-right" id="input_det_grandtotal" value="0.00" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" id="btnotokiri" class="btn btn-primary" style="height: 30px; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; transition: background-color 0.3s, box-shadow 0.3s; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="submitOtorisasi1()">Approve</button>
            </div>
        </div>
    </div>
</div>
    <!-- End modal detail Informasi-->







  
  
  
    <!-- TAB KANAN detail INFORMASI-->
    
<div class="modal fade" id="IdetailPembelian" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 95%;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="IdetailPembelianModalLabel">Detail </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="row">
          <input type="hidden" class="form-control" id="Iinput_add_nourut">
          
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>No Bukti</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control text-left" id="IdetailPembelianNobukti" placeholder="" disabled>
                </div>
              </div>
              <div class="col-md-4" style='margin-top:-15px;'>
                <div class="form-group">
                  <label>Tanggal</label>
                </div>
              </div>
              <div class="col-md-8" style='margin-top:-15px;'>
                <div class="form-group">
                  <input type="date" class="form-control text-left" id="IdetailDate" value="{!! date('Y-m-d') !!}" disabled>
                </div>
              </div>

              <div class="col-md-4" style='margin-top:-15px;'>
                <div class="form-group">
                  <label>Supplier</label>
                </div>
              </div>

              <div class="col-md-8" style='margin-top:-15px;'>
                <div class="input-group mb-3">
                  <input type="text" class="form-control text-left" placeholder="Kode Pelanggan" id="IdetailPembelianKodeSupp" disabled>
                </div>
              </div>

              <div class="col-md-4" style='margin-top:-15px;'>
                <div class="form-group">
                  <label>No PO</label>
                </div>
              </div>

              <div class="col-md-8" style='margin-top:-15px;'>
                <div class="input-group mb-3">
                  <input type="text" class="form-control text-left" placeholder="No PO" id="IdetailNoPO" disabled>
                </div>
              </div>

              <div class="col-md-4" style='margin-top:-15px;'>
                <div class="form-group">
                  <label>Surat Jln Supp</label>
                </div>
              </div>

              <div class="col-md-8" style='margin-top:-15px;'>
                <div class="input-group mb-3">
                  <input type="text" class="form-control text-left" placeholder="Surat Jalan Supplier" id="IdetailFakturSupp" disabled>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <input type="text" class="form-control text-center" placeholder="Nama Pelanggan" id="IdetailPembelianSupp" disabled>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <textarea style="width: 100%; resize: none" rows=3 placeholder="Alamat Pelanggan" class="form-control text-center" id="IdetailPembelianAlamatSupp" disabled></textarea>
                </div>
              </div>

              <div class="col-md-12">
                <div class="row">
                  <div class="form-group col-4">
                    <label>Gudang</label>
                  </div>
                  <div class="col-md-8" style='margin-left:-32px;'>
                    <div class="form-group">
                      <input type="text" style="width: 220px;resize: none" class="form-control text-left" placeholder="Gudang" id="Idetailgudang" disabled>
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
                  <div class="col-9">
                    <div class="form-group">
                      <label>Valas</label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="row">
                  <div class="col-md-12">
                    <div class="input-group form-group">
                      <input type="text" class="form-control" id="IdetailPembelianvalas" disabled>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="row">
                  <div class="col-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <label>Kurs</label>
                    </div>
                  </div>
                  <div class="col-md-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <input type="text" class="form-control" id="IdetailPembeliankurs" disabled>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-12">
                <div class="row">
                  <div class="col-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <label>TOP</label>
                    </div>
                  </div>
                  <div class="col-md-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <input type="number" class="form-control text-left" id="IdetailPembelianhari" value=0 min=0 disabled>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-12">
                <div class="row">
                  <div class="col-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <label>No/Sopir</label>
                    </div>
                  </div>
                  <div class="col-md-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <input type="text" class="form-control text-left" id="IdetailNoSopir" disabled>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-12">
                <div class="row">
                  <div class="col-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <label>SO Cust</label>
                    </div>
                  </div>
                  <div class="col-md-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <input type="text" class="form-control text-left" id="IdetailSoCustomer" disabled>
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
                  <div class="col-6">
                    <div class="form-group">
                      <label>Pembayaran</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <select onchange="onChangeInputAddPembayaran()" id="IdetailPembeliantipebayar" class="form-control form-select-lg mb-3 text-center" aria-label=".form-select-lg example" disabled>
                        <option value=0 selected>Tunai</option>
                        <option value=1>Kredit</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-12">
                <div class="row">
                  <div class="col-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <label>Jth Tempo</label>
                    </div>
                  </div>
                  <div class="col-md-6" style='margin-top:-15px;'>
                    <input type="date" class="form-control text-center" id="IdetailPembelianJthTempo" value="{!! date('Y-m-d') !!}" onblur="onChangeTgglKirim()" disabled>
                  </div>
                </div>
              </div>

              <div class="col-md-12">
                <div class="row">
                  <div class="col-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <label>PPN</label>
                    </div>
                  </div>
                  <div class="col-md-6" style='margin-top:-15px;'>
                    <select onchange="onChangeTipePPN()" id="IdetailPembeliantipeppn" class="form-control text-center form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
                      <option value=0 selected>None</option>
                      <option value=1>Exclude</option>
                      <option value=2>Include</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="col-md-12" hidden>
                <div class="row">
                  <div class="col-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <label>No Uang Muka</label>
                    </div>
                  </div>
                  <div class="col-md-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <input type="text" class="form-control text-left" id="IdetailNoUangMuka" disabled>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-12">
                <div class="row">
                  <div class="col-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <label type="number">Uang Muka</label>
                    </div>
                  </div>
                  <div class="col-md-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <input type="number" class="form-control text-left" id="IdetailNuangmuka" disabled>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row mt-3">
          <div class="col-12">
            <div style="overflow:auto; max-height: 300px;">
              <table id="detailPembelianTable" class="table table-bordered table-striped">
                <thead class="text-center bg-primary text-white">
                  <tr>
                    <th scope="col">Kode Barang</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Qty PO</th>
                    <th scope="col">Satuan</th>
                    <th scope="col">Harga</th>
                    <th scope="col">Disc</th>
                    <th scope="col">Subtotal</th>
                  </tr>
                </thead>
                <tbody id="detailPembelianTableData" class="text-left">
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

        <!-- PPN GRANDTOTAL -->
        <div class="row mt-2">
          <div class="col">
            <div class="row">
              <div class="col-3 d-flex align-items-center">
                <label>Disc %</label>
              </div>
              <div class="col-8">
                <input type="number" class="form-control text-right" id="Iinput_det_disc" onblur="onChangeInputAddDisc()" value="0.00" disabled>
              </div>
            </div>
          </div>

          <div class="col">
            <div class="row">
              <div class="col-3 d-flex align-items-center">
                <label>DiscRp</label>
              </div>
              <div class="col-8">
                <input type="number" class="form-control text-right" id="Iinput_det_discrp" onblur="onChangeInputAddDiscRp()" value="0.00" disabled>
              </div>
            </div>
          </div>

          <div class="col">
            <div class="row">
              <div class="col-3 d-flex align-items-center">
                <label>DPP</label>
              </div>
              <div class="col-8">
                <input type="text" class="form-control text-right" id="Iinput_det_dpp" value="0.00" disabled>
              </div>
            </div>
          </div>

          <div class="col">
            <div class="row">
              <div class="col-3 d-flex align-items-center">
                <label>PPN</label>
              </div>
              <div class="col-8">
                <input type="text" class="form-control text-right" id="Iinput_det_ppn" value="0.00" disabled>
              </div>
            </div>
          </div>

          <div class="col">
            <div class="row">
              <div class="col-5 d-flex align-items-center">
                <label>GrandTotal</label>
              </div>
              <div class="col-7">
                <input type="text" class="form-control text-right" id="Iinput_det_grandtotal" value="0.00" disabled>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- PPN GRANDTOTAL -->

      <div class="modal-footer">
        <button type="button" id="btnotokanan" class="btn btn-primary" 
        style=
        "height: 30px; 
        padding: 4px 12px; 
        border-radius: 20px; 
        font-size: 0.75rem; 
        font-weight: 600; 
        text-transform: uppercase; 
        transition: background-color 0.3s, box-shadow 0.3s; 
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" 
        onclick="submitUnOtorisasi1()">Batal Oto</button>
        <!-- submitUnOtorisasi1   -->
        <!-- submitOtorisasi1 -->
      </div>
    </div>
  </div>
</div>




 <!-- End modal detail-->


<!-- start modal edit pembelian  -->
<div class="modal fade" id="formPrint" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                            <!-- tes max width -->
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="printPembelianModalLabel">Detail</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <!-- <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" /> -->
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">

                <h5 id="">No PO</h5>
            </div>
            <div class="col-12">

                <input type="text" class="form-control" id="printPembelianNoPO" disabled>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <h5 id="">Supplier</h5>
            </div>
            <div class="col-12">
              <input type="text" class="form-control" id="printPembelianSupp" disabled>
            </div>
            <div class="col-12">

              <h5 id="">Tanggal</h5>
            </div>
            <div class="col-12">

                <input type="date" class="form-control" id="printPembelianDate" value="{!! date('Y-m-d') !!}" disabled >
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <h5 id="">Keterangan</h5>
            </div>
            <div class="col-12">
              <input type="text" class="form-control" id="printPembelianFakturSupp" autocomplete="off" disabled>
            </div>
            <div class="col-12">
              <h5 id="">FakturSupp</h5>
            </div>
            <div class="col-12">
              <input type="text" class="form-control" id="printPembelianKeterangan" autocomplete="off" disabled>
            </div>

          </div>
        </div>
        <div class="container-fluid mt-2">

      </div>

        <div class="container-fluid" style="overflow:auto;">

              <table id="printPembelianTable" class="table table-bordered table-striped mt-5"  >
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





        <div class="container-fluid">
          <div class="row">
            <div class="col-12 text-right">
              <label>Jumlah Print</label>
              <input type="number" style="max-width: 50px" value=1 class="text-right" id="input_print_jumlahprint" >
            </div>
           

          </div>

        </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="submitPrint()">Print</button>
          </div>
        </div>
    </div>
  </div>
</div>
  </div>
  <!-- End modal editpembelian-->

  <!-- start modal edit pembelian  -->
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
      let DataNobukti = "";


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
      let dataSO = []

      $(document).ready(function(){
        if ($.fn.modal && $.fn.modal.Constructor) {
            $.fn.modal.Constructor.prototype._enforceFocus = function() {}; // Untuk Bootstrap 4 & 5
            $.fn.modal.Constructor.prototype.enforceFocus = function() {};  // Untuk Bootstrap 3
        }
        $("#tabel").DataTable({'order': [[1, 'asc']], "lengthChange": false, "paging": false , "columnDefs": [
      { "type": "date", "targets": [2] },
      {  "className": "text-center", "targets": [0] }, { "targets": [0] ,  'orderable' : false}

    ]});
      });
      $(document).ready(function(){
        $("#tabel_pembelian").DataTable({
      'order': [[1, 'asc']]
    , "lengthChange": false, "paging": false , "columnDefs": [
      { "type": "date", "targets": [2] },
      {  "className": "text-center", "targets": [0] }, { "targets": [0] ,  'orderable' : false}
      // {"order": [[ 1, 'desc' ]]},

    ]});
      });



     


function detailPembelian1(index) {
  let tempDataDetailPembelian = dataRefreshPembelian[index]
  detailPembelian('x', tempDataDetailPembelian)
}

  //TAB KANAN detail INFORMASI

      function detailPembelian (nobukti) {
        console.log(nobukti,  "<<< detail")
        console.log('================')
        console.log(nobukti)


        let _token = $("#_token").val();
        let table_pembelian_row_detail =[]



        $.ajax({
          url: "{!! url('detailPembelianACCtunai') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            NoBukti: nobukti
          },
          success: function(res) {
              console.log('======xxxxx==========')
            console.log(res)

            table_pembelian_row_detail = res
            console.log(res)
            // console.log(res)
            // console.log(res.pQC)

          }
        })


        dataLPB =  table_pembelian_row_detail
        dataEditPembelianEdit = table_pembelian_row_detail

        let date = new Date(table_pembelian_row_detail[0].TANGGAL);
        var day = ("0" + date.getDate()).slice(-2);
        var month = ("0" + (date.getMonth() + 1)).slice(-2);
        var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;

        let dateJ = new Date(table_pembelian_row_detail.JthTempo);
        var dayJ = ("0" + dateJ.getDate()).slice(-2);
        var monthJ = ("0" + (dateJ.getMonth() + 1)).slice(-2);
        var dateJthTempo = dateJ.getFullYear()+"-"+(monthJ)+"-"+(dayJ) ;

        let table_row_detail_inf = ""

        table_pembelian_row_detail.forEach((detail_row, i) => {
          console.log(detail_row.KodeBrg)
          table_row_detail_inf += `<tr>
          <td>${detail_row.KodeBrg}</td>
          <td>${detail_row.namabrgx}</td>
          <td class="text-right">${detail_row.Qnt ? formatAngkaX(detail_row.Qnt) : '0.00' }</td>
          <td class="text-right">${detail_row.QNTPO ? formatAngkaX(detail_row.QNTPO) : '0.00' }</td>
          <td>${detail_row.Satuan}</td>
          <td class="text-right">${detail_row.Harga ? formatAngkaX(detail_row.Harga) : '0.00' }</td>
          <td class="text-right">${detail_row.DiscRp1 ? formatAngkaX(detail_row.DiscRp1) : '0.00' }</td>
          <td class="text-right">${detail_row.NDPP ? formatAngkaX(detail_row.NDPP) : '0.00' }</td></tr>` 

        });


        let fakturSupp = ""
        let keterangan = ""
        if (table_pembelian_row_detail[0].FAKTURSUPP) {
          fakturSupp = table_pembelian_row_detail[0].FAKTURSUPP
        }
        if (table_pembelian_row_detail[0].KETERANGAN) {
          keterangan = table_pembelian_row_detail[0].KETERANGAN
        }
        // document.getElementById("IdetailModalLabel").innerHTML = "Detail " +  table_pembelian_row_detail[0].NoBukti;

        document.getElementById("IdetailPembelianNobukti").value = table_pembelian_row_detail[0].NoBukti;
        document.getElementById("IdetailPembelianKodeSupp").value = table_pembelian_row_detail[0].KODESUPP;
        document.getElementById("IdetailPembelianAlamatSupp").value = table_pembelian_row_detail[0].ALAMAT1;
        document.getElementById("IdetailPembelianhari").value = table_pembelian_row_detail[0].HARI;

        document.getElementById("IdetailPembelianSupp").value = table_pembelian_row_detail[0].NamaSupplier;

        document.getElementById("IdetailPembelianvalas").value = table_pembelian_row_detail[0].KODEVLS;


        document.getElementById("IdetailNoPO").value = table_pembelian_row_detail[0].NoPO
        document.getElementById("IdetailFakturSupp").value = table_pembelian_row_detail[0].FAKTURSUPP
        
        document.getElementById("IdetailPembeliankurs").value = table_pembelian_row_detail[0].KURS

        document.getElementById("IdetailPembeliantipeppn").value = table_pembelian_row_detail[0].PPN
        document.getElementById("IdetailPembeliantipebayar").value = table_pembelian_row_detail[0].TIPEBAYAR

        document.getElementById("IdetailSoCustomer").value = table_pembelian_row_detail[0].NOSO
        console.log('mmm')
        console.log(table_pembelian_row_detail[0].NOUMK)
        document.getElementById("IdetailNoUangMuka").value = table_pembelian_row_detail[0].NOUMK

        document.getElementById("IdetailNuangmuka").value = table_pembelian_row_detail[0].NuangMuka
      

        document.getElementById("Idetailgudang").value = table_pembelian_row_detail[0].NAMAGUDANG
        document.getElementById("IdetailFakturSupp").value = table_pembelian_row_detail[0].FAKTURSUPP
        document.getElementById("IdetailPembelianJthTempo").value = formatDate(table_pembelian_row_detail[0].JthTempo)

        document.getElementById("IdetailNoSopir").value = table_pembelian_row_detail[0].SOPIR

        document.getElementById("Iinput_det_disc").value = formatAngkaX(table_pembelian_row_detail[0].disc)
        document.getElementById("Iinput_det_discrp").value = formatAngkaX(table_pembelian_row_detail[0].DISCRP)
        document.getElementById("Iinput_det_dpp").value = formatAngkaX(table_pembelian_row_detail[0].TotDPP)
        document.getElementById("Iinput_det_ppn").value = formatAngkaX(table_pembelian_row_detail[0].TotPPN)
        document.getElementById("Iinput_det_grandtotal").value = formatAngkaX(table_pembelian_row_detail[0].TotNet)



        
        $("#IdetailDate").val(date1);
        $("#btnotokanan").hide();
        $("#IdetailPembelian").modal('toggle');

         document.getElementById("detailPembelianTableData").innerHTML = table_row_detail_inf

        


      }

      function tesModal() {
        //
        // $("#tes1234").modal('toggle');
        $('#formPembelianAdd').show();
      }



        function submitOtorisasi1 () {
    console.log('otooo')
          let nobukti = DataNobukti;
          let otorisasi = 1
          console.log(nobukti, otorisasi)
          let _token = $("#_token").val();


          $.ajax({
            url: "{!! url('spotorisasiBeliAcctunai') !!}",
            type: "post",
            async: false,
            data: {
              _token,
              nobukti,
              otorisasi
            },
            success: function(res) {
              if (res > 0) {
                console.log(nobukti, otorisasi)
                loadAll()
                // $("#formOtorisasi1").modal('toggle')
                console.log('xxxx');
                $("#detail").modal('toggle')
                alertify.success('Berhasil update otorisasi')

              }


          },
          error: function (err) {
            alertify.warning('Terjadi kesalahan pada server, silahkan refresh ulang')
          }
        })
        }


        function submitUnOtorisasi1 () {

          let nobukti = DataNobukti;
          console.log(nobukti)
          let otorisasi = 1
          console.log(nobukti, otorisasi)

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
            url: "{!! url('spUnotorisasiBeliAcctunai') !!}",
            type: "post",
            async: false,
            data: {
              _token,
              nobukti,
              otorisasi,
		pket :value

            },
            success: function(res) {
              if (res > 0) {
                console.log(nobukti, otorisasi)
                loadAll()

                alertify.success('Berhasil update otorisasi')
                $("#IdetailPembelian").modal('toggle')
              }


          },
          error: function (err) {
            alertify.warning('Terjadi kesalahan pada server, silahkan refresh ulang')
          }
        })

},
  function() {
    alertify.error("Action cancelled");
  }
);

        }






        function buttonUnOtoPembelian (nobukti) {
          console.log(nobukti,  "<<< detail")
          console.log('================')
          console.log(nobukti)

          let _token = $("#_token").val();
          let table_pembelian_row_detail =[]
          DataNobukti = nobukti

          $.ajax({
            url: "{!! url('detailPembelianACCtunai') !!}",
            type: "post",
            async: false,
            data: {
              _token : _token,
              NoBukti: nobukti
            },
            success: function(res) {
                console.log('======xxxxx==========')
              console.log(res)

              table_pembelian_row_detail = res
              // console.log(res[0][0])
              // console.log(res[0][0].pQC)

            }
          })


          dataLPB =  table_pembelian_row_detail
          dataEditPembelianEdit = table_pembelian_row_detail

          let date = new Date(table_pembelian_row_detail[0].TANGGAL);
          var day = ("0" + date.getDate()).slice(-2);
          var month = ("0" + (date.getMonth() + 1)).slice(-2);
          var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;

          let table_row_detail_inf = ""

          table_pembelian_row_detail.forEach((detail_row, i) => {
            console.log(detail_row.KodeBrg)
            table_row_detail_inf += `<tr>
            <td>${detail_row.KodeBrg}</td>
            <td>${detail_row.namabrgx}</td>
            <td class="text-right">${detail_row.Qnt ? formatAngkaX(detail_row.Qnt) : '0.00' }</td>
          <td class="text-right">${detail_row.QNTPO ? formatAngkaX(detail_row.QNTPO) : '0.00' }</td>
            <td>${detail_row.Satuan}</td>
            <td class="text-right">${detail_row.Harga ? formatAngkaX(detail_row.Harga) : '0.00' }</td>
          <td class="text-right">${detail_row.DiscRp1 ? formatAngkaX(detail_row.DiscRp1) : '0.00' }</td>
          <td class="text-right">${detail_row.NDPP ? formatAngkaX(detail_row.NDPP) : '0.00' }</td></tr>` 

          });


          let fakturSupp = ""
          let keterangan = ""
          if (table_pembelian_row_detail[0].FAKTURSUPP) {
            fakturSupp = table_pembelian_row_detail[0].FAKTURSUPP
          }
          if (table_pembelian_row_detail[0].KETERANGAN) {
            keterangan = table_pembelian_row_detail[0].KETERANGAN
          }
          

             document.getElementById("IdetailPembelianNobukti").value = table_pembelian_row_detail[0].NoBukti;
        document.getElementById("IdetailPembelianKodeSupp").value = table_pembelian_row_detail[0].KODESUPP;
        document.getElementById("IdetailPembelianAlamatSupp").value = table_pembelian_row_detail[0].ALAMAT1;
        document.getElementById("IdetailPembelianhari").value = table_pembelian_row_detail[0].HARI;

        document.getElementById("IdetailPembelianSupp").value = table_pembelian_row_detail[0].NamaSupplier;

        document.getElementById("IdetailPembelianvalas").value = table_pembelian_row_detail[0].KODEVLS;


        document.getElementById("IdetailNoPO").value = table_pembelian_row_detail[0].NoPO
        document.getElementById("IdetailFakturSupp").value = table_pembelian_row_detail[0].FAKTURSUPP
        
        document.getElementById("IdetailPembeliankurs").value = table_pembelian_row_detail[0].KURS

        document.getElementById("IdetailPembeliantipeppn").value = table_pembelian_row_detail[0].PPN
        document.getElementById("IdetailPembeliantipebayar").value = table_pembelian_row_detail[0].TIPEBAYAR

        document.getElementById("IdetailSoCustomer").value = table_pembelian_row_detail[0].NOSO
        console.log('mmm')
        console.log(table_pembelian_row_detail[0].NOUMK)
        document.getElementById("IdetailNoUangMuka").value = table_pembelian_row_detail[0].NOUMK

        document.getElementById("IdetailNuangmuka").value = table_pembelian_row_detail[0].NuangMuka
      

        document.getElementById("Idetailgudang").value = table_pembelian_row_detail[0].NAMAGUDANG
        document.getElementById("IdetailFakturSupp").value = table_pembelian_row_detail[0].FAKTURSUPP
        document.getElementById("IdetailPembelianJthTempo").value = formatDate(table_pembelian_row_detail[0].JthTempo)

        document.getElementById("IdetailNoSopir").value = table_pembelian_row_detail[0].SOPIR

        document.getElementById("Iinput_det_disc").value = formatAngkaX(table_pembelian_row_detail[0].disc)
        document.getElementById("Iinput_det_discrp").value = formatAngkaX(table_pembelian_row_detail[0].DISCRP)
        document.getElementById("Iinput_det_dpp").value = formatAngkaX(table_pembelian_row_detail[0].TotDPP)
        document.getElementById("Iinput_det_ppn").value = formatAngkaX(table_pembelian_row_detail[0].TotPPN)
        document.getElementById("Iinput_det_grandtotal").value = formatAngkaX(table_pembelian_row_detail[0].TotNet)

          $("#IdetailDate").val(date1);
          $("#btnotokanan").show();
          $("#IdetailPembelian").modal('toggle');

          document.getElementById("detailPembelianTableData").innerHTML = table_row_detail_inf


        }

        function tesModal() {
          //
          // $("#tes1234").modal('toggle');
          $('#formPembelianAdd').show();
        }



          function submitOtorisasi1 () {

            let nobukti = DataNobukti;
            let otorisasi = 1
            console.log(nobukti, otorisasi)
            let _token = $("#_token").val();


            $.ajax({
              url: "{!! url('spotorisasiBeliAcctunai') !!}",
              type: "post",
              async: false,
              data: {
                _token,
                nobukti,
                otorisasi
              },
              success: function(res) {
                if (res > 0) {
                  console.log('ini')
                  loadAll()

                  alertify.success('Berhasil update otorisasi')
                  $("#detail").modal('toggle')
                }


            },
            error: function (err) {
              alertify.warning('Terjadi kesalahan pada server, silahkan refresh ulang')
            }
          })
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
        // if (dataEditPembelianEdit[indexBarang].pQC) {
        //   alertify.warning("data sudah di qc");
        //
        //   return
        // }
        document.getElementById("editPembelianInputEditKode").value = dataEditPembelianEdit[indexBarang].KodeBrg
        document.getElementById("editPembelianInputEditNamaBarang").value = dataEditPembelianEdit[indexBarang].namabrgx
        // document.getElementById("editPembelianInputEditQtyPO").value = dataEditPembelianEdit[indexBarang].QNTPO
        // document.getElementById("editPembelianInputEditSatuan").value = dataEditPembelianEdit[indexBarang].Satuan
        document.getElementById("editPembelianInputEditQty").value = dataEditPembelianEdit[indexBarang].Qnt
        document.getElementById("editPembelianInputEditDisc").value = dataEditPembelianEdit[indexBarang].DiscP1
        document.getElementById("editPembelianInputEditDiscRp").value = dataEditPembelianEdit[indexBarang].DiscTot
        document.getElementById("editPembelianInputEditHarga").value = dataEditPembelianEdit[indexBarang].Harga

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


function formatAngkaX (angka) {
      if (!Number(angka)) {
        return '0.00'
      } else {
        return formatAngka(parseFloat(angka).toFixed(2))

      }

    }

    function formatAngkaRound(angkaString) {
      angkaString = Math.ceil(Number(angkaString) * 100) / 100;
      return formatAngka(angkaString);
    }

    function formatAngka (angkaString) {

    if (angkaString === null || angkaString === undefined || angkaString === '') {
        return '0.00';
    }

    if (isNaN(Number(angkaString))) {
        return '0.00';
    }

    // Pakai hasil pembulatan dan ubah menjadi string
    angkaString = parseFloat(angkaString).toFixed(2).toString();

    let tempAngka = angkaString.split('.');

    if (tempAngka[0][0] == '-') {

        let temp2 = '';

        let tempAngka1 = tempAngka[0].split('-');

        for (let i = 0; i < tempAngka1[1].length; i++) {

            if (i != 0 && i % 3 == 0) {
                temp2 = ',' + temp2;
            }

            temp2 = tempAngka1[1][tempAngka1[1].length - i - 1] + temp2;
        }

        temp2 += '.' + tempAngka[1];

        return '-' + temp2;
    }

    let temp1 = '';

    for (let i = 0; i < tempAngka[0].length; i++) {

        if (i != 0 && i % 3 == 0) {
            temp1 = ',' + temp1;
        }

        temp1 = tempAngka[0][tempAngka[0].length - i - 1] + temp1;
    }

    temp1 += '.' + tempAngka[1];

    return temp1;
    }

      function loadAll () {
        // tabel_pembelian

        $.ajax({
          url: "{!! url('getAksesNewPOAcctunai') !!}",
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

        $('#tabel').DataTable().destroy();

        $.ajax({
          url: "{!! url('getAllPOBeliAcctunai') !!}",
          type: "get",
          async: false,
          success: function(res) {

            dataRefreshPO = res
            console.log(res) 
            let rowTabel = ""
            res.forEach((item, i) => {
              let date1 = ""

              if (item.TglBeli) {
                let date = new Date(item.TglBeli);
                let day = ("0" + date.getDate()).slice(-2);
                let month = ("0" + (date.getMonth() + 1)).slice(-2);
                date1 = date.getFullYear()+"/"+(month)+"/"+(day);
              }

              let NoBukti = ""
              let KODESUPP = ""
              let NAMACUSTSUPP = ""
              let VALAS = ""
              if (item.NoBukti) {
                NoBukti = item.NoBukti
              }
              if (item.KODESUPP) {
                KODESUPP = item.KODESUPP
              }
              if (item.NAMACUSTSUPP) {
                NAMACUSTSUPP = item.NAMACUSTSUPP
              }

              if (item.KODEVLS) {
                VALAS = item.KODEVLS
              }
              rowTabel += `<tr style='white-space:nowrap;'>
              <td>

                <button style="" class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('${NoBukti}')" ><i class="bi bi-info-lg"></i></button>
                <button class="btn btn-success btn-sm" type="button" onclick="buttonEditPembelian('${NoBukti}')"><i class="bi bi-pen"></i></button>
                <button style="" class="btn btn-primary btn-sm" type="button" onclick="buttonOtoPembelian('${NoBukti}')" ><i class="bi bi-key"></i></button>

              </td>

              <td>${NoBukti}</td>
              <td>${date1}</td>
              <td>${item.NAMACUSTSUPP ? item.NAMACUSTSUPP : '' }</td>
              <td>${item.Keterangan ? item.Keterangan : '' }</td>
              <td>${item.NoPO ? item.NoPO : '' }</td>
              <td class="text-right">${item.TotDPP ? formatAngkaX(item.TotDPP) : '0.00' }</td>
              <td class="text-right">${item.TotPPN ? formatAngkaX(item.TotPPN) : '0.00' }</td>
              <td class="text-right">${item.TotNet ? formatAngkaX(item.TotNet) : '0.00' }</td>

              </tr>`

            });
            document.getElementById("tabel_data").innerHTML = rowTabel
            $("#tabel").DataTable({'order': [[1, 'asc']], "lengthChange": false, "paging": false , "columnDefs": [
            { "type": "date", "targets": [2] },
            {  "className": "text-center", "targets": [0] }, { "targets": [0] ,  'orderable' : false}

    ]});
          }
        })

        //pembelian
        $('#tabel_pembelian').DataTable().destroy();

        $.ajax({
          url: "{!! url('getAllPembelianAcctunai') !!}",
          type: "get",
          async: false,
          success: function(res) {
            console.log(res , "RESPOND PEM");
            // console.log(res[0])
            dataRefreshPembelian = res
            let rowTabel = ""
            res.forEach((item, i) => {

              let date1 = ""
              if (item.TANGGAL) {
                let date = new Date(item.TANGGAL);
                let day = ("0" + date.getDate()).slice(-2);
                let month = ("0" + (date.getMonth() + 1)).slice(-2);
                date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
              }
              let NoBukti = ""
              let NamaSupplier = ""
              let NoPO = ""
              let NAMAGUDANG = ""
              let FAKTURSUPP = ""
              if (item.NoBukti) {
                NoBukti = item.NoBukti
              }
              if (item.NamaSupplier) {
                NamaSupplier = item.NamaSupplier
              }
              if (item.NoPO) {
                NoPO = item.NoPO
              }
              if (item.NAMAGUDANG) {
                NAMAGUDANG = item.NAMAGUDANG
              }
              if (item.FAKTURSUPP) {
                FAKTURSUPP = item.FAKTURSUPP
              }
              rowTabel += `<tr style='white-space:nowrap;'>
              <td>
              <button style="" class="btn btn-warning btn-sm" type="button"   onclick="detailPembelian('${NoBukti}')" ><i class="bi bi-info"></i></button>
              <button style="" class="btn btn-danger btn-sm" type="button"   onclick="buttonUnOtoPembelian('${NoBukti}')" ><i class="bi bi-key"></i></button>
              <button style="" class="btn btn-primary btn-sm" type="button"   onclick="submitPrint('${NoBukti}')" ><i class="bi bi-printer"></i></button>

              </td>
              <td>${NoBukti}</td>
              <td>${date1}</td>
              <td>${item.NAMACUSTSUPP ? item.NAMACUSTSUPP : '' }</td>
              <td>${item.Keterangan ? item.Keterangan : '' }</td>
              <td>${item.NoPO ? item.NoPO : '' }</td>
              <td>${item.NAMAGUDANG ? item.NAMAGUDANG : '' }</td>
              <td>${item.FAKTURSUPP ? item.FAKTURSUPP : '' }</td>
              <td class="text-right">${item.TotDPP ? item.TotDPP : '0.00' }</td>
              <td class="text-right">${item.TotPPN ? item.TotPPN : '0.00' }</td>
              <td class="text-right">${item.TotNet ? item.TotNet : '0.00' }</td>
              <td>${item.OtoUser1 ? item.OtoUser1 : '' }</td>
              <td>${item.TglOto1 ? item.TglOto1 : '' }</td>
              </tr>`
            });

            // <button style="" class="btn btn-primary btn-sm" type="button" onclick="buttonEditPembelian('${NoBukti}')"><i class="bi bi-printer"></i></button> button edit buat di atas
            document.getElementById("tabel_pembelian_data").innerHTML = rowTabel
            $("#tabel_pembelian").DataTable({
      'order': [[1, 'asc']]
    , "lengthChange": false, "paging": false , "columnDefs": [
      { "type": "date", "targets": [2] },
      {  "className": "text-center", "targets": [0] }, { "targets": [0] ,  'orderable' : false}
      // {"order": [[ 1, 'desc' ]]},

    ]});


          }
        })

      }

      function resetDetailPembelian (noBukti, noPO) {
        // SML/LPB/00197/0323
        // SML/PO/00302/0223
        let _token = $("#_token").val();
        console.log(noBukti, noPO)
        $('.showhide').hide();

        $.ajax({
          url: "{!! url('detailPembelian') !!}",
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
            table_row_edit_pembelian += `<tr>
            <td>${r.KodeBrg}</td>
            <td>${r.namabrgx}</td>
            <td class="text-right">${r.Qnt}</td>
            <td class="text-right">${r.QNTPO}</td>
            <td>${satuan}</td>


            <td class="text-right">${Number(r.Harga) ? r.Harga : "0.00"}</td>
            <td class="text-right">${Number(r.DiscRp1) ? r.DiscRp1 : "0.00"}</td>
            <td class="text-right">${Number(r.NDPP) ? r.NDPP : "0.00"}</td>


            <td class="text-center"><button class="btn btn-success btn-sm" type="button" onclick="showPembelianEdit(${i})"><i class="bi bi-pen"></i></button><button style="" class="btn btn-danger btn-sm" type="button" onclick="submitPembelianDelete(${i})" ><i class="bi bi-trash"></i></button></td></tr>`
            });
          document.getElementById("editPembelianModalLabel").innerHTML = "Edit " +  dataLPB.NoBukti;
          document.getElementById("editPembelianNoPO").value = dataLPB.NoPO
          document.getElementById("editPembelianSupp").value = dataLPB.NamaSupplier
          document.getElementById("editPembelianFakturSupp").value = dataLPB.FAKTURSUPP
          // document.getElementById("editPembelianKeterangan").value = dataLPB.KETERANGAN
          document.getElementById("editPembelianTableData").innerHTML = table_row_edit_pembelian

          $.ajax({
            url: "{!! url('detailPO') !!}",
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
              editPembelianSelect += `<option value="${i}">${data.KodeBrg} - ${data.namabrgx}</option>`
            });
          } else {
            editPembelianSelect += `<option value="" selected disabled>Tidak ada barang untuk ditambah</option>`
          }

          document.getElementById("editPembelianAddSelect").innerHTML = editPembelianSelect


          $('#editPembelianDate').val(date1)
        }



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
          url: "{!! url('detailPembelian') !!}",
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

              // console.log(dataLPBDelete.pQC)
              // if (dataLPBDelete.pQC) {
              //   alertify.warning("data sudah di qc");
              //
              //   return
              // }
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
                url: "{!! url('sp_beligudang') !!}",
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
        // let reqKeterangan = $("#editPembelianKeterangan").val()
        let reqKeterangan =''
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
          url: "{!! url('sp_beligudang') !!}",
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
              $("#editPembelian").modal('toggle');
          }
        })

        console.log(reqFakturSupp,reqKeterangan)

      }

      function submitPembelianEdit() {
        console.log(indexEditPembelianEdit, dataEditPembelianEdit[indexEditPembelianEdit])

        let _token = $("#_token").val();
        let choice = "U"
        let dataLPBEdit = dataEditPembelianEdit[indexEditPembelianEdit]
        let reqQtyTerima = $("#editPembelianInputEditQty").val();
        let reqDisc = $("#editPembelianInputEditDisc").val();
        let reqHarga = $("#editPembelianInputEditHarga").val();
        let reqDiscRp = $("#editPembelianInputEditDiscRp").val();


        // let reqDisc = $("#editPembelianInputEditDisc").val()
        // let reqDiscRp = $("#editPembelianInputEditDiscRp").val()
        // let reqHarga = $("#editPembelianInputEditHarga").val()

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
        // let reqDisc = dataLPBEdit.DiscP1
        // let reqDiscRp = dataLPBEdit.reqDiscRp
        // let reqHarga = dataLPBEdit.harga


        // ('reqDiscP1'),
        // ('reqDiscRp'),
        // ('reqHarga')
        console.log("===========================")

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
          reqDisc,
          reqDiscRp,
          reqHarga,
        })



        $.ajax({
          url: "{!! url('sp_beligudangACCtunai') !!}",
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
            reqDisc,
            reqDiscRp,
            reqHarga,
          },
          success: function(res) {
            // console.log(res , "RESPOND");
            console.log(res)
            console.log("successsssssssssssssssssssssx")
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
          let reqNoSat = data.NOSAT
          let reqSatuan = data.Satuan
          let reqIsi = data.ISI
          let reqQtyTerima1 = 0
          let reqQtyTerima2 = 0
          if (data.NOSAT == 1) {
            reqQtyTerima1 = reqQtyTerima;
            reqQtyTerima2 = reqQtyTerima / data.ISI2;
          } else if (data.NOSAT == 2) {
            reqQtyTerima1 = reqQtyTerima * data.ISI2;
            reqQtyTerima2 = reqQtyTerima;
          }
          let reqNamaBarang = data.namabrgx
          let reqNoBatch = ""
          let reqQtyReject = 0
          let reqQtyReject1 = 0
          let reqQtyReject2 = 0
          let reqPBeliJasa = 0
          let reqEd = null


            // console.log(editPembelianInputAddQtyPO)
            $.ajax({
              url: "{!! url('sp_beligudang') !!}",
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
                reqDiscRp,
                reqHarga
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


function onChangeHeader (field, value) {

  let _token  = $("#_token").val()
  let nobukti = $("#EditNobukti").val()
  $.ajax({
    url: "{!! url('acctunaionchangeheader') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      field,
      value,
      nobukti
    },
    success: function(res) {
       console.log('CH');
      console.log(value);
      alertify.success(`update ${field} berhasil`)

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}


function onChangeInputAddDisc () {
    // document.getElementById("input_add_discrp").value = '0.00'
    console.log('onChangeDisc')
   // if (tipeform == 'edit') {
      let value = $("#input_edit_disc").val()
      console.log(value)
      onChangeHeader('DISC' , value)
      refreshUpdateHeader()
      let nobukti = $("#EditNobukti").val()
      refreshDataTableAdd(nobukti)
   // }
}


function refreshUpdateHeader () {
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  $.ajax({
    url: "{!! url('acctunaispupdateso') !!}",
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





function refreshDataTableAdd (NOBUKTI = "") {


            console.log("KOREKSI BELI")
            console.log(NOBUKTI)

            let _token = $("#_token").val();

            let edit_pembelian_row_data = []

            $.ajax({
              url: "{!! url('detailPembelian') !!}",
              type: "post",
              async: false,
              data: {
                _token : _token,
                NoBukti: NOBUKTI
              },
              success: function(res) {
                console.log(res)

                edit_pembelian_row_data = res[0]
                console.log(res[0])
                console.log(res[0][0])
                // console.log(res[0][0].pQC)

              }
            })

            if(!edit_pembelian_row_data.length) {
              // alertify.warning('silahkan refresh browser')
              return
            }




            dataLPB =  edit_pembelian_row_data[0]
           
            dataEditPembelianEdit = edit_pembelian_row_data
           
            let date = new Date(edit_pembelian_row_data[0].TANGGAL);
            var day = ("0" + date.getDate()).slice(-2);
            var month = ("0" + (date.getMonth() + 1)).slice(-2);
            var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;


            let table_row_edit_pembelian = ""
            edit_pembelian_row_data.forEach((r, i) => {
              console.log('r===' ,r ,'ASD')
              console.log('SDW')
             
              let satuan = ""
              if (r.Satuan) {
                satuan = r.Satuan
              }
              table_row_edit_pembelian += `<tr>
              <td>${r.KodeBrg}</td>
              <td>${r.namabrgx}</td>
              
              <td class="text-right">${r.Qnt ? formatAngkaX(r.Qnt) : '0.00' }</td>
              <td class="text-right">${r.QNTPO ? formatAngkaX(r.QNTPO) : '0.00' }</td>
              <td>${satuan}</td>
              <td class="text-right">${r.Harga ? formatAngkaX(r.Harga) : '0.00' }</td>
            <td class="text-right">${r.DiscRp1 ? formatAngkaX(r.DiscRp1) : '0.00' }</td>
            <td class="text-right">${r.NDPP ? formatAngkaX(r.NDPP) : '0.00' }</td>
              <td class="text-center"><button class="btn btn-success btn-sm" type="button" onclick="showPembelianEdit(${i})"><i class="bi bi-pen"></i></button><button style="" class="btn btn-danger btn-sm" type="button" onclick="submitPembelianDelete(${i})" ><i class="bi bi-trash"></i></button></td></tr>`
                  
            });
            document.getElementById("editPembelianModalLabel").innerHTML = "Edit " +  edit_pembelian_row_data[0].NoBukti;

            document.getElementById("EditNobukti").value = edit_pembelian_row_data[0].NoBukti
            document.getElementById("editPembelianNoPO").value = edit_pembelian_row_data[0].NoPO
            document.getElementById("editPembelianKodeSupp").value = edit_pembelian_row_data[0].KODESUPP
            document.getElementById("editPembelianSupp").value = edit_pembelian_row_data[0].NamaSupplier
            document.getElementById("editPembelianAlamatSupp").value = edit_pembelian_row_data[0].ALAMAT1
            document.getElementById("editPembelianHari").value = edit_pembelian_row_data[0].HARI


            document.getElementById("editPembelianFakturSupp").value = edit_pembelian_row_data[0].FAKTURSUPP
            // document.getElementById("editPembelianKeterangan").value = edit_pembelian_row_data[0].KETERANGAN
            document.getElementById("editPembelianTableData").innerHTML = table_row_edit_pembelian



            document.getElementById("editPembelianvalas").value = edit_pembelian_row_data[0].KODEVLS
            document.getElementById("editPembeliankurs").value = edit_pembelian_row_data[0].KURS

            document.getElementById("editPembeliantipeppn").value = edit_pembelian_row_data[0].PPN
            document.getElementById("editPembeliantipebayar").value = edit_pembelian_row_data[0].TIPEBAYAR

            document.getElementById("editPembelianSoCustomer").value = edit_pembelian_row_data[0].NOSO
            document.getElementById("editPembelianNoUangMuka").value = edit_pembelian_row_data[0].NOUMK

            document.getElementById("editPembelianNuangmuka").value = edit_pembelian_row_data[0].NuangMuka


            document.getElementById("editPembeliangudang").value = edit_pembelian_row_data[0].NAMAGUDANG
            document.getElementById("editPembelianFakturSupp").value = edit_pembelian_row_data[0].FAKTURSUPP

            document.getElementById("editPembelianNoSopir").value = edit_pembelian_row_data[0].SOPIR


            document.getElementById("input_edit_disc").value = formatAngkaX(edit_pembelian_row_data[0].disc)
            document.getElementById("input_edit_discrp").value = formatAngkaX(edit_pembelian_row_data[0].DISCRP)
            document.getElementById("input_edit_dpp").value = formatAngkaX(edit_pembelian_row_data[0].TotDPP)
            document.getElementById("input_edit_ppn").value = formatAngkaX(edit_pembelian_row_data[0].TotPPN)
            document.getElementById("input_edit_grandtotal").value = formatAngkaX(edit_pembelian_row_data[0].TotNet)




            console.log(edit_pembelian_row_data[0].NoPO)
            console.log(edit_pembelian_row_data[0].NoBukti , "<<<<<<<<<<")

            $.ajax({
              url: "{!! url('detailPOBeli') !!}",
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
            // $("#editPembelian").modal('toggle');


}

function onchangekurs () {
  console.log('onChangekurs')
  // if (tipeform == 'edit') {
    let value = $("#editPembeliankurs").val()
    console.log(value)
    onChangeHeader('kurs' , value)
   
    // refreshUpdateHeader()
    let nobukti = $("#EditNobukti").val()
    refreshDataTableAdd(nobukti)
  // }


}



function onChangeInputAddDiscRp () {
    // document.getElementById("input_add_disc").value = '0.00'
    console.log('onChangeDiscRp')
      //if (tipeform == 'edit') {
        let value = $("#input_edit_discrp").val()
        // console.log(edit_pembelian_row_data[0].TotNet)
        let x = Number(value) / Number(dataLPB.TotNet) * 100
        console.log('RP Disss',Number(value),dataLPB.TotNet,x)
        console.log(value)
        onChangeHeader('DISC' , x)
        refreshUpdateHeader()
        let nobukti = $("#EditNobukti").val()
        refreshDataTableAdd(nobukti)
      //}
}


function onChangeHeader (field, value) {

  let _token  = $("#_token").val()
  let nobukti = $("#EditNobukti").val()
  $.ajax({
    url: "{!! url('acctunaionchangeheader') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      field,
      value,
      nobukti
    },
    success: function(res) {
       console.log('CH');
      console.log(value);
      alertify.success(`update ${field} berhasil`)

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}



// function refreshDataTableAdd (nobukti = "") {
//  console.log(nobukti,  "<<< detail")
//   console.log('================')
//   console.log(nobukti)


//   let _token = $("#_token").val();
//   let table_pembelian_row_detail =[]



//   $.ajax({
//     url: "{!! url('detailPembelianACCtunai') !!}",
//     type: "post",
//     async: false,
//     data: {
//       _token : _token,
//       NoBukti: nobukti
//     },
//       success: function(res) {
//         console.log('======xxxxx==========')
//       console.log(res)

//       table_pembelian_row_detail = res[0]
//       dataLPB =  table_pembelian_row_detail[0]
//         dataEditPembelianEdit = table_pembelian_row_detail

//         let date = new Date(table_pembelian_row_detail[0].TANGGAL);
//         var day = ("0" + date.getDate()).slice(-2);
//         var month = ("0" + (date.getMonth() + 1)).slice(-2);
//         var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;

//         let dateJ = new Date(table_pembelian_row_detail[0].JthTempo);
//         var dayJ = ("0" + dateJ.getDate()).slice(-2);
//         var monthJ = ("0" + (dateJ.getMonth() + 1)).slice(-2);
//         var dateJthTempo = dateJ.getFullYear()+"-"+(monthJ)+"-"+(dayJ) ;

//         let table_row_detail_inf = ""

//         table_pembelian_row_detail.forEach((detail_row, i) => {
//           console.log(detail_row.KodeBrg)
//           table_row_detail_inf += `<tr>
//           <td>${detail_row.KodeBrg}</td>
//           <td>${detail_row.namabrgx}</td>
//           <td class="text-right">${detail_row.Qnt ? formatAngkaX(detail_row.Qnt) : '0.00' }</td>
//           <td class="text-right">${detail_row.QNTPO ? formatAngkaX(detail_row.QNTPO) : '0.00' }</td>
//           <td>${detail_row.Satuan}</td>
//           <td class="text-right">${detail_row.Harga ? formatAngkaX(detail_row.Harga) : '0.00' }</td>
//           <td class="text-right">${detail_row.DiscRp1 ? formatAngkaX(detail_row.DiscRp1) : '0.00' }</td>
//           <td class="text-right">${detail_row.NNET ? formatAngkaX(detail_row.NNET) : '0.00' }</td></tr>` 

//         });


//         let fakturSupp = ""
//         let keterangan = ""
//         if (table_pembelian_row_detail[0].FAKTURSUPP) {
//           fakturSupp = table_pembelian_row_detail[0].FAKTURSUPP
//         }
//         if (table_pembelian_row_detail[0].KETERANGAN) {
//           keterangan = table_pembelian_row_detail[0].KETERANGAN
//         }
//         // document.getElementById("IdetailModalLabel").innerHTML = "Detail " +  table_pembelian_row_detail[0].NoBukti;

//         document.getElementById("IdetailPembelianNobukti").value = table_pembelian_row_detail[0].NoBukti;
//         document.getElementById("IdetailPembelianKodeSupp").value = table_pembelian_row_detail[0].KODESUPP;
//         document.getElementById("IdetailPembelianAlamatSupp").value = table_pembelian_row_detail[0].ALAMAT1;
//         document.getElementById("IdetailPembelianhari").value = table_pembelian_row_detail[0].HARI;

//         document.getElementById("IdetailPembelianSupp").value = table_pembelian_row_detail[0].NamaSupplier;

//         document.getElementById("IdetailPembelianvalas").value = table_pembelian_row_detail[0].KODEVLS;


//         document.getElementById("IdetailNoPO").value = table_pembelian_row_detail[0].NoPO
//         document.getElementById("IdetailFakturSupp").value = table_pembelian_row_detail[0].FAKTURSUPP
        
//         document.getElementById("IdetailPembeliankurs").value = table_pembelian_row_detail[0].KURS

//         document.getElementById("IdetailPembeliantipeppn").value = table_pembelian_row_detail[0].PPN
//         document.getElementById("IdetailPembeliantipebayar").value = table_pembelian_row_detail[0].TIPEBAYAR

//         document.getElementById("IdetailSoCustomer").value = table_pembelian_row_detail[0].NOSO
//         console.log('mmm')
//         console.log(table_pembelian_row_detail[0].NOUMK)
//         document.getElementById("IdetailNoUangMuka").value = table_pembelian_row_detail[0].NOUMK

//         document.getElementById("IdetailNuangmuka").value = table_pembelian_row_detail[0].NuangMuka
      

//         document.getElementById("Idetailgudang").value = table_pembelian_row_detail[0].NAMAGUDANG
//         document.getElementById("IdetailFakturSupp").value = table_pembelian_row_detail[0].FAKTURSUPP

//         document.getElementById("IdetailNoSopir").value = table_pembelian_row_detail[0].SOPIR

//         document.getElementById("Iinput_det_disc").value = table_pembelian_row_detail[0].disc
//         document.getElementById("Iinput_det_discrp").value = table_pembelian_row_detail[0].DISCRP
//         document.getElementById("Iinput_det_dpp").value = table_pembelian_row_detail[0].TotDPP
//         document.getElementById("Iinput_det_ppn").value = table_pembelian_row_detail[0].TotPPN
//         document.getElementById("Iinput_det_grandtotal").value = table_pembelian_row_detail[0].TotNet
      

//     },
//        error: function (err) {
//         console.log(err)
//         console.log(err.status)
//         console.log(err.statusText)
//         alertify.warning('Terjadi kesalahan silahkan refresh browser')
//       }
//   })


        



       

// }  

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
            $('.showhide').hide();

            let _token = $("#_token").val();

            let edit_pembelian_row_data = []

            $.ajax({
              url: "{!! url('detailPembelian') !!}",
              type: "post",
              async: false,
              data: {
                _token : _token,
                NoBukti: nobukti
              },
              success: function(res) {
                console.log(res)

                edit_pembelian_row_data = res
                console.log(res[0])

              }
            })

            if(!edit_pembelian_row_data.length) {
              alertify.warning('silahkan refresh browser')
              return
            }

            dataLPB =  edit_pembelian_row_data
            // detailPembelianDate
            // document.getElementById("editPembelianDate").value = edit_pembelian_row_data[0].TANGGAL
            dataEditPembelianEdit = edit_pembelian_row_data
            // return

            // console.log(edit_pembelian_row_id, edit_pembelian_row_data , "<<<< edit pembelian")
            let date = new Date(edit_pembelian_row_data[0].TANGGAL);
            var day = ("0" + date.getDate()).slice(-2);
            var month = ("0" + (date.getMonth() + 1)).slice(-2);
            var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;

            // if (edit_pembelian_row_data[0].pQC) {
            //   alertify.warning('Data sudah di QC')
            //   return
            // }

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
              table_row_edit_pembelian += `<tr>
              <td>${r.KodeBrg}</td>
              <td>${r.namabrgx}</td>
              
              <td class="text-right">${r.Qnt ? formatAngkaX(r.Qnt) : '0.00' }</td>
              <td class="text-right">${r.QNTPO ? formatAngkaX(r.QNTPO) : '0.00' }</td>
              <td>${satuan}</td>
              <td class="text-right">${r.Harga ? formatAngkaX(r.Harga) : '0.00' }</td>
            <td class="text-right">${r.DiscRp1 ? formatAngkaX(r.DiscRp1) : '0.00' }</td>
            <td class="text-right">${r.NDPP ? formatAngkaX(r.NDPP) : '0.00' }</td>
              <td class="text-center">
              <button class="btn btn-success btn-sm" type="button" onclick="showPembelianEdit(${i})"><i class="bi bi-pen"></i></button>
             
              </td>
            </tr>`
                  
            });
            //  <button style="" class="btn btn-danger btn-sm" type="button" onclick="submitPembelianDelete(${i})" ><i class="bi bi-trash"></i></button> ini button edit buat di atas
            document.getElementById("editPembelianModalLabel").innerHTML = "Edit " +  edit_pembelian_row_data[0].NoBukti;

            document.getElementById("EditNobukti").value = edit_pembelian_row_data[0].NoBukti
            document.getElementById("editPembelianNoPO").value = edit_pembelian_row_data[0].NoPO
            document.getElementById("editPembelianKodeSupp").value = edit_pembelian_row_data[0].KODESUPP + " (" + edit_pembelian_row_data[0].NamaSupplier + ")";
            document.getElementById("editPembelianSupp").value = edit_pembelian_row_data[0].NamaSupplier
            document.getElementById("editPembelianAlamatSupp").value = edit_pembelian_row_data[0].ALAMAT1
            document.getElementById("editPembelianHari").value = edit_pembelian_row_data[0].HARI

            document.getElementById("editPembelianJthTempo").value = formatDate(edit_pembelian_row_data[0].JthTempo);

            document.getElementById("editPembelianFakturSupp").value = edit_pembelian_row_data[0].FAKTURSUPP
            // document.getElementById("editPembelianKeterangan").value = edit_pembelian_row_data[0].KETERANGAN
            document.getElementById("editPembelianTableData").innerHTML = table_row_edit_pembelian



            document.getElementById("editPembelianvalas").value = edit_pembelian_row_data[0].KODEVLS
            document.getElementById("editPembeliankurs").value = edit_pembelian_row_data[0].KURS

            document.getElementById("editPembeliantipeppn").value = edit_pembelian_row_data[0].PPN
            document.getElementById("editPembeliantipebayar").value = edit_pembelian_row_data[0].TIPEBAYAR

            document.getElementById("editPembelianSoCustomer").value = edit_pembelian_row_data[0].NOSO
            document.getElementById("editPembelianNoUangMuka").value = edit_pembelian_row_data[0].NOUMK

            document.getElementById("editPembelianNuangmuka").value = edit_pembelian_row_data[0].NuangMuka

            document.getElementById("editPembeliangudang").value = edit_pembelian_row_data[0].NAMAGUDANG
            document.getElementById("editPembelianFakturSupp").value = edit_pembelian_row_data[0].FAKTURSUPP

            document.getElementById("editPembelianNoSopir").value = edit_pembelian_row_data[0].SOPIR

            document.getElementById("input_edit_disc").value = formatAngkaX(edit_pembelian_row_data[0].disc)
            document.getElementById("input_edit_discrp").value = formatAngkaX(edit_pembelian_row_data[0].DISCRP)
            document.getElementById("input_edit_dpp").value = formatAngkaX(edit_pembelian_row_data[0].TotDPP)
            document.getElementById("input_edit_ppn").value = formatAngkaX(edit_pembelian_row_data[0].TotPPN)
            document.getElementById("input_edit_grandtotal").value = formatAngkaX(edit_pembelian_row_data[0].TotNet)

             console.log('VXVX',dataLPB.TotNet) 


            console.log(edit_pembelian_row_data[0].NoPO)
            console.log(edit_pembelian_row_data[0].NoBukti , "<<<<<<<<<<")

            $.ajax({
              url: "{!! url('detailPOBeli') !!}",
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

            $("#btnotokananedit").hide();
            console.log('date1', date1)
            $('#editPembelianDate').val(date1)
            $("#editPembelian").modal('toggle');
          }

          function buttonOtoPembelian (nobukti) {
            console.log(nobukti,  "<<< detail")
            console.log('================')
            console.log(nobukti)
            
            pNObukti = nobukti 
            DataNobukti = nobukti
            // let date = new Date(detail_row_data[0].TANGGAL);

            let _token = $("#_token").val();

            let detail_row_data = []

            $.ajax({
              url: "{!! url('detailPembelianACCtunai') !!}",
              type: "post",
              async: false,
              data: {
                _token : _token,
                NoBukti: nobukti
              },
              success: function(res) {
                  console.log('======xxxxx==========')
                console.log(res)

                detail_row_data = res

              }
            })

            dataLPB =  detail_row_data

            // detailPembelianDate
            // document.getElementById("editPembelianDate").value = edit_pembelian_row_data[0].TANGGAL
            dataEditPembelianEdit = detail_row_data

            let date = new Date(detail_row_data[0].TANGGAL);

            var day = ("0" + date.getDate()).slice(-2);
            var month = ("0" + (date.getMonth() + 1)).slice(-2);
            var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;

            let table_row_detail = ""

            detail_row_data.forEach((detail_row, i) => {
            console.log(detail_row.KodeBrg)
            table_row_detail += `<tr><td>${detail_row.KodeBrg}</td>
            <td>${detail_row.namabrgx}</td>
            <td class="text-right">${detail_row.Qnt ? formatAngkaX(detail_row.Qnt) : '0.00' }</td>
            <td class="text-right">${detail_row.QNTPO ? formatAngkaX(detail_row.QNTPO) : '0.00' }</td>    
            <td>${detail_row.Satuan}</td>
            <td class="text-right">${detail_row.Harga ? formatAngkaX(detail_row.Harga) : '0.00' }</td>
            <td class="text-right">${detail_row.DiscRp1 ? formatAngkaX(detail_row.DiscRp1) : '0.00' }</td>
            <td class="text-right">${detail_row.NDPP ? formatAngkaX(detail_row.NDPP) : '0.00' }</td></tr>`   
            });
            

            document.getElementById("detailModalLabel").innerHTML = "Detail " +  detail_row_data[0].NoBukti;

        document.getElementById("detailPembelianNobukti").value = detail_row_data[0].NoBukti;
        document.getElementById("detailPembelianKodeSupp").value = detail_row_data[0].KODESUPP;
        document.getElementById("detailPembelianAlamatSupp").value = detail_row_data[0].ALAMAT1;
        document.getElementById("detailPembelianhari").value = detail_row_data[0].HARI;
        
      
        document.getElementById("detailPembelianSupp").value = detail_row_data[0].NamaSupplier;

        document.getElementById("detailPembelianvalas").value = detail_row_data[0].KODEVLS;

        document.getElementById("detailNoPO").value = detail_row_data[0].NoPO
        document.getElementById("detailFakturSupp").value = detail_row_data[0].FAKTURSUPP
       
        document.getElementById("detailPembeliankurs").value = detail_row_data[0].KURS

        document.getElementById("detailPembeliantipeppn").value = detail_row_data[0].PPN
        document.getElementById("detailPembeliantipebayar").value = detail_row_data[0].TIPEBAYAR

        document.getElementById("detailSoCustomer").value = detail_row_data[0].NOSO
        console.log('mmm')
        console.log(detail_row_data[0].NOUMK)
        document.getElementById("detailNoUangMuka").value = detail_row_data[0].NOUMK

        document.getElementById("detailNuangmuka").value = detail_row_data[0].NuangMuka
      
        document.getElementById("detailgudang").value = detail_row_data[0].NAMAGUDANG

        document.getElementById("input_det_disc").value = formatAngkaX(detail_row_data[0].disc)
        document.getElementById("input_det_discrp").value = formatAngkaX(detail_row_data[0].DISCRP)
        document.getElementById("input_det_dpp").value = formatAngkaX(detail_row_data[0].TotDPP)
        document.getElementById("input_det_ppn").value = formatAngkaX(detail_row_data[0].TotPPN)
        document.getElementById("input_det_grandtotal").value = formatAngkaX(detail_row_data[0].TotNet)
        document.getElementById("detailPembelianJthTempo").value = formatDate(detail_row_data[0].JthTempo);
        console.log ('jatuh tempo=========================',formatDate(detail_row_data[0].JthTempo),document.getElementById("detailPembelianJthTempo").value);


            $('#detailDate').val(date1)
            $("#btnotokiri").show();

            document.getElementById("detailTableData").innerHTML = table_row_detail

            $("#detail").modal('toggle');
          }

          function buttonAdd1(index) {
            let tempDataAdd = dataRefreshPO[index]
            buttonAdd('x' , tempDataAdd)

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

        // if (edit_pembelian_row_data[0].pQC) {
        //   alertify.warning('Data sudah di QC')
        //   return
        // }

        let table_row_edit_pembelian = ""
        edit_pembelian_row_data.forEach((r, i) => {
          console.log('r===' ,r ,'ASD')
          console.log('SDW')
          // let QntPO_format = r.QntPO.toLocaleString();
          // console.log(r)
          console.log(r.Harga)
          // console.log(r.Satuan)
          let satuan = ""
          if (r.Satuan) {
            satuan = r.Satuan
          }
          table_row_edit_pembelian += `<tr><td>${r.KodeBrg}</td><td>${r.namabrgx}</td><td>${r.Qnt}</td><td>${r.QNTPO}</td><td>${satuan}</td><td>${Number(r.Qnt) ? r.Qnt : "0.00"}</td><td>-</td><td>-</td><td class="text-center"><button class="btn btn-success btn-sm" type="button" onclick="showPembelianEdit(${i})"><i class="bi bi-pen"></i></button><button style="" class="btn btn-danger btn-sm" type="button" onclick="submitPembelianDelete(${i})" ><i class="bi bi-trash"></i></button></td></tr>`
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
        // document.getElementById("editPembelianKeterangan").value = edit_pembelian_row_data[0].KETERANGAN
        document.getElementById("editPembelianTableData").innerHTML = table_row_edit_pembelian

        console.log(edit_pembelian_row_data[0].NoPO)
        console.log(edit_pembelian_row_data[0].NoBukti , "<<<<<<<<<<")
        let _token = $("#_token").val();
        $.ajax({
          url: "{!! url('detailPO') !!}",
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




// Kiri Detail informasi 

      function buttonDetail (nobukti) {
        console.log(nobukti,  "<<< detail")
        console.log('================')
        console.log(nobukti)
        // let date = new Date(detail_row_data[0].TANGGAL);

        let _token = $("#_token").val();

        let detail_row_data = []

        $.ajax({
          url: "{!! url('detailPembelianACCtunai') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            NoBukti: nobukti
          },
          success: function(res) {
              console.log('======xxxxx==========')
            console.log(res)

            detail_row_data = res
            console.log(res)
            // console.log(res)
            // console.log(res.pQC)

          }
        })



        // if(!detail_row_datax.length) {
        //   alertify.warning('silahkan refresh browser')
        //   return
        // }




        dataLPB =  detail_row_data
        // detailPembelianDate
        // document.getElementById("editPembelianDate").value = edit_pembelian_row_data.TANGGAL
        dataEditPembelianEdit = detail_row_data


        let date = new Date(detail_row_data[0].TANGGAL);
        var day = ("0" + date.getDate()).slice(-2);
        var month = ("0" + (date.getMonth() + 1)).slice(-2);
        var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;

        let table_row_detail = ""


         detail_row_data.forEach((detail_row, i) => {
      
        
        table_row_detail += `<tr><td>${detail_row.KodeBrg}</td>
        <td>${detail_row.namabrgx}</td>
        <td class="text-right">${detail_row.Qnt ? formatAngkaX(detail_row.Qnt) : '0.00' }</td>
        <td class="text-right">${detail_row.QNTPO ? formatAngkaX(detail_row.QNTPO) : '0.00' }</td>
        <td>${detail_row.Satuan}</td>
        <td class="text-right">${detail_row.Harga ? formatAngkaX(detail_row.Harga) : '0.00' }</td>
        <td class="text-right">${detail_row.DiscRp1 ? formatAngkaX(detail_row.DiscRp1) : '0.00' }</td>
        <td class="text-right">${detail_row.NDPP ? formatAngkaX(detail_row.NDPP) : '0.00' }</td></tr>`


         });

        document.getElementById("detailModalLabel").innerHTML = "Detail " +  detail_row_data[0].NoBukti;

        document.getElementById("detailPembelianNobukti").value = detail_row_data[0].NoBukti;
        document.getElementById("detailPembelianKodeSupp").value = detail_row_data[0].KODESUPP;
        document.getElementById("detailPembelianAlamatSupp").value = detail_row_data[0].ALAMAT1;
        document.getElementById("detailPembelianhari").value = detail_row_data[0].HARI;

        document.getElementById("detailPembelianSupp").value = detail_row_data[0].NamaSupplier;

        document.getElementById("detailPembelianvalas").value = detail_row_data[0].KODEVLS;


        document.getElementById("detailNoPO").value = detail_row_data[0].NoPO
        document.getElementById("detailFakturSupp").value = detail_row_data[0].FAKTURSUPP
       
        document.getElementById("detailPembeliankurs").value = detail_row_data[0].KURS

        document.getElementById("detailPembeliantipeppn").value = detail_row_data[0].PPN
        document.getElementById("detailPembeliantipebayar").value = detail_row_data[0].TIPEBAYAR

        document.getElementById("detailSoCustomer").value = detail_row_data[0].NOSO
        console.log('mmm')
        console.log(detail_row_data[0].NOUMK)
        document.getElementById("detailNoUangMuka").value = detail_row_data[0].NOUMK

        document.getElementById("detailNuangmuka").value = detail_row_data[0].NuangMuka
       

        document.getElementById("detailgudang").value = detail_row_data[0].NAMAGUDANG
        document.getElementById("detailFakturSupp").value = detail_row_data[0].FAKTURSUPP

        document.getElementById("detailNoSopir").value = detail_row_data[0].SOPIR

        document.getElementById("input_det_disc").value = formatAngkaX(detail_row_data[0].disc)
        document.getElementById("input_det_discrp").value = formatAngkaX(detail_row_data[0].DISCRP)
        document.getElementById("input_det_dpp").value = formatAngkaX(detail_row_data[0].TotDPP)
        document.getElementById("input_det_ppn").value = formatAngkaX(detail_row_data[0].TotPPN)
        document.getElementById("input_det_grandtotal").value = formatAngkaX(detail_row_data[0].TotNet)
      console.log(date1)
        $('#detailDate').val(date1)
        $("#btnotokiri").hide();

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
          url: "{!! url('getNoBukti') !!}",
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
                  url : "{!! url('addDBBeli') !!}",
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

    
function submitPrint (nobukti) {

let _token = $('#_token').val()

  $.ajax({
    url: "{!! url('invoicepembelianprint') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      NOBUKTI: nobukti
    },
    success: function(res) {

      dataPrint = res.detail;
      dataSO = res.dbumjual;

      console.log(dataPrint);
      console.log(dataSO);
      
    }
  })

  let arrayDataPrint = []

  for (let i = 0; i < dataPrint.length; i+=7) 
  {
    let tempArray = dataPrint.slice(i,i+7)
    arrayDataPrint.push(tempArray)
  }
  let xsubtotal = 0
  for (let i = 0; i < dataPrint.length; i++) {
    // const element = array[i];
    console.log("===========!!============")
    // console.log(dataPrint[i])
     xsubtotal += Number(dataPrint[i].subtotal)
     console.log(xsubtotal)
  }

  let printContent = ''
  let imageContent = document.getElementById(`imagecontainer`).innerHTML;
  let css = ''
  let hdr = ''
  let str= ''
  let ftr= ''
  let tanggalOnly = dataPrint[0].Tanggal.split(' ')[0].split('-').reverse().join('/');
  let userLogin = "{{ auth()->user()->username }}"

  const now = new Date()
  const tanggalCetak = now.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric'});
  const jamCetak = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

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
      height: 18px;
      padding: 0px 4px;
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
      height: 14cm;
      position: relative;
    }

    .footer-sign {
      padding-top: 5px;
      position: absolute;
      width: 100%;
      bottom: 70px;
    }

    .footer-print-date {
      position: absolute;
      width: 95%;
      bottom: 120px;
    }

     .solid{
      border-left: 0px red solid;
      height: 225px;
      width: 0px;
      display: inline-block;
      padding-left: 0px;
      }

    </style>`;
  hdr = `<div style="width: 100%;">

                  <div style="display: flex; width: 100%;">
                    <div class="pb-1" style="width: 15%; margin-top: 15px">
                      `+ imageContent +`
                    </div>
                    <div class="pb-1 ps-3" style="width: 100%; margin-top: 10px; font-size:12px;">
                      <h2 class="m-0 pb-2">CV. SINAR MAHAKAM LESTARI</h2>
                      <div class="pb-1" style="width: 100%">JL. AMPERA PERGUDANGAN MANGKUPALAS BISNIS CENTRE BLOK D NO.18 RT.022 SIMPANG PASIR PALARAN SAMARINDA-KALIMANTAN TIMUR</div>
                      <div class="pb-1" style="width: 100%">Telp (0541) 4104142, Fax (0541) 4104195</div>
                      <div class="pb-1" style="width: 100%">E-Mail : sml@indo.net.id</div>
                    </div>
                    <div style="width:80%; padding-top:45px; display:flex; justify-content:flex-start;">
                      <h2 style="margin-left:-10px; font-size:24px;">
                        FAKTUR PEMBELIAN
                      </h2>
                    </div>
                  </div>

                  <div style="display:flex; width:100%;">
                    <div style="width:60%;">
                      <div style="display: flex; font-size: 12px;">
                        <div class="pb-1" style="width: 30%">Tanggal</div>
                        <div class="pb-1" style="width: 5%">:</div>
                        <div class="pb-1" style="width: 65%">`+tanggalOnly+`</div>
                      </div>
                      <div style="display: flex; font-size: 12px;">
                        <div class="pb-1" style="width: 30%">No. SJ</div>
                        <div class="pb-1" style="width: 5%">:</div>
                        <div class="pb-1" style="width: 65%">${dataPrint[0].FakturSupp ?? ''}</div>
                      </div>
                      <div style="display: flex; font-size: 12px;">
                        <div class="pb-1" style="width: 30%">Diterima Dari</div>
                        <div class="pb-1" style="width: 5%">:</div>
                        <div class="pb-1" style="width: 65%">${dataPrint[0].NamaCustSupp}</div>
                      </div>
                      <div style="display: flex; font-size: 12px;">
                        <div class="pb-1" style="width: 30%">Expedisi</div>
                        <div class="pb-1" style="width: 5%">:</div>
                        <div class="pb-1" style="width: 65%">${dataPrint[0].namaexp}</div>
                      </div>
                    </div>

                  <div style="width:40%;">
                  <div style="display: flex; font-size: 12px;">
                    <div class="pb-1" style="width: 30%">No. FB</div>
                    <div class="pb-1" style="width: 5%">:</div>
                    <div class="pb-1" style="width: 65%">${dataPrint[0].NoBukti}</div>
                  </div>
                  <div style="display: flex; font-size: 12px;">
                    <div class="pb-1" style="width: 30%">No. PO</div>
                    <div class="pb-1" style="width: 5%">:</div>
                    <div class="pb-1" style="width: 65%">${dataPrint[0].NoPO}</div>
                  </div>
                  <div style="display: flex; font-size: 12px;">
                    <div class="pb-1" style="width: 30%">NO. PR/SO</div>
                    <div class="pb-1" style="width: 5%">:</div>
                    <div class="pb-1" style="width: 65%">${dataPrint[0].NOSO ?? ''}</div>
                  </div>
                  <div style="display: flex; font-size: 12px;">
                    <div class="pb-1" style="width: 30%">No. PO Cust</div>
                    <div class="pb-1" style="width: 5%">:</div>
                    <div class="pb-1" style="width: 65%">${dataPrint[0].NOPOCUST}</div>
                  </div>
                </div>

              </div>
          <table
          class="detail-spb-table"
          style="width: 95%; font-family: sans-serif; display: table; font-size: 10px; border: 1px solid #3c3c3c;">
              <thead>
                <tr>
                  <td class="text-center" style="width: 1%">No.</td>
                  <td class="text-center" style="width: 30%">NAMA BARANG</td>
                  <td class="text-center" style="width: 15%">KODE BRG</td>
                  <td class="text-center" style="width: 5%">SAT</td>
                  <td class="text-center" style="width: 5%">QTY</td>
                  <td class="text-center" style="width: 15%">HARGA</td>
                  <td class="text-center" style="width: 10%">DISKON</td>
                  <td class="text-center" style="width: 15%">JUMLAH</td>
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
    <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; text-align: center; width: 1%;">${z+1}</td>
    <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 30%;">${itemSub.NamaBrg}</td>
    <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; text-align: center; width: 15%;">${itemSub.KodeBrg}</td>
    <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; text-align: center; width: 5%;">${itemSub.Satuan}</td>
    <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; text-align: center; width: 5%;">${itemSub.Qnt ? parseFloat(itemSub.Qnt).toFixed(2) : ''}</td>
    <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; text-align: right; width: 15%;">${formatAngkaRound(itemSub.harga)}</td>
    <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; text-align: center; width: 10%;">${itemSub.DISC ?? ''}</td>
    <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; text-align: right; width: 15%;">${formatAngka(Math.round(Number(itemSub.subtotal))).split('.')[0]}</td>
  </tr>`;
z++;
});

// Fill remaining empty rows   table is 225px, each row ~24px, header ~24px = ~8 total slots
// const maxRows = 7;
// const fillerCount = Math.max(0, maxRows - item.length);
// for (let f = 0; f < fillerCount; f++) {
//   tempPrintStr += `
//     <tr style="height: 24px;">
//       <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
//       <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
//       <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
//       <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
//       <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
//       <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
//       <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
//       <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
//     </tr>`;
// }

tempPrintStr += `</tbody>`;
tempPrintStr += `</table>`;

if (i == arrayDataPrint.length - 1) {

let tableSO = '';
if (dataSO && dataSO.length > 0) {

    tableSO = `
    <table style="width:100%; border-collapse:collapse; font-size:10px;">
        <tr>
            <th style="border:1px solid black;width:2%;">No</th>
            <th style="border:1px solid black;">No Bukti</th>
            <th style="border:1px solid black;">DPP</th>
        </tr>
    `;

    dataSO.forEach(function(item, index) {
        tableSO += `
        <tr>
            <td style="border:1px solid black;text-align:center;">
                ${index + 1}
            </td>
            <td style="border:1px solid black;">
                ${item.NOBUKTI}
            </td>
            <td style="border:1px solid black;text-align:right;">
                ${formatAngka(parseFloat(item.DPP).toFixed(2))}
            </td>
        </tr>`;
    });

    tableSO += `</table>`;
  }

  tempPrintStr += `<div style="display: flex; width: 98%; margin-top: 10px;">

    <div style="width:70%;">
      ${dataSO && dataSO.length > 0 ? tableSO : ''}
    </div>

<div style="width: 90%; font-family: sans-serif; font-size: 10px;" class=''>
  <table style="width: 100%; table-layout: fixed; margin-left: 125px; border-collapse: collapse; margin-top: -6px;">
    <tr>
      <td class="no-border text-left" style="width: 34%; font-size:10px;">Dibuat Oleh,</td>
    </tr>
    <tr style="height: 3.5rem;">
      <td class="no-border" colspan="3">&nbsp;</td>
    </tr>

    <tr>
      <td class="no-border px-2" style="padding:0;">
        <p class="m-0" style="font-size:10px; line-height:1.2;">
          Nama : ${dataPrint[0].OtoUser1}
        </p>
        <div style="border-bottom:1px solid #000; margin:2px 0;"></div>
        <p class="m-0" style="font-size:10px; line-height:1.2;">
          Tgl :
        </p>
      </td>
    </tr>

  </table>
</div>`;}

if(i == arrayDataPrint.length - 1){
tempPrintStr += `
<div style="width: 95%; font-family: sans-serif; font-size: 10px;">

  <div style="display: flex; font-size:10px; justify-content: flex-end; width: 91%; padding-bottom: 2px;">
    <div style="width: 40%; text-align:left;"> SUB TOTAL </div>
    <div style="width: 20%; text-align: right">${formatAngka(parseFloat(xsubtotal).toFixed(2)).split('.')[0]}</div>
  </div>
  <div style="display: flex; font-size:10px; justify-content: flex-end; width: 91%; padding-bottom: 2px;">
    <div style="width: 40%; text-align:left;"> DISKON </div>
    <div style="width: 20%; text-align: right">${formatAngka(parseFloat(dataPrint[0].POTONGAN).toFixed(2)).split('.')[0]}</div>
  </div>
  <div style="display: flex; font-size:10px; justify-content: flex-end; width: 91%; padding-bottom: 4px; position: relative;">
    <div style="width: 40%; text-align:left;"> UANG MUKA </div>
    <div style="width: 20%; text-align: right">${formatAngka(parseFloat(dataPrint[0].NuangMuka).toFixed(2)).split('.')[0]}</div>
    <div style="
    position: absolute;
    right: 0;
    bottom: 0;
    width: 60%;
    border-bottom: 1px solid #000;"></div>
  </div>
  <div style="display: flex; font-size:10px; justify-content: flex-end; width: 91%; padding-bottom: 2px;">
    <div style="width: 40%; text-align:left;"> DPP </div>
    <div style="width: 20%; text-align: right">${formatAngka(parseFloat(dataPrint[0].NDPPRP).toFixed(2)).split('.')[0]}</div>
  </div>
  <div style="display: flex; font-size:10px; justify-content: flex-end; width: 91%; padding-bottom: 2px;">
    <div style="width: 40%; text-align:left;"> PPN </div>
    <div style="width: 20%; text-align: right">${formatAngka(parseFloat(dataPrint[0].NPPNRP).toFixed(2)).split('.')[0]}</div>
  </div>
  <div style="display: flex; font-size:10px; justify-content: flex-end; width: 91%; padding-bottom: 6px; position: relative; font-weight: bold;">
    <div style="width: 40%; text-align:left;"> TOTAL </div>
    <div style="width: 20%; text-align: right">${formatAngka(parseFloat(dataPrint[0].NNETRP).toFixed(2)).split('.')[0]}</div>
    <div style="
      position: absolute;
      right: 0;
      bottom: 3px;
      width: 60%;
      border-bottom: 1px solid #000;">
    </div>

    <!-- garis bawah 2 -->
    <div style="
      position: absolute;
      right: 0;
      bottom: 0;
      width: 60%;
      border-bottom: 1px solid #000;">
    </div>
  </div>
</div>`};

tempPrintStr += `
<div class="footer-print-date" style="margin-bottom:-100px;">
      <table class="m-0" style="width: 100%; font-family: sans-serif;
      font-size: 10px;">
        <tr>
          <td class="no-border">${i+1}/${arrayDataPrint.length} `+userLogin+` `+tanggalCetak+`      `+jamCetak+`</td>
        </tr>
      </table>
    </div>

  </div>
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

  {{-- script buat hover so belum otorisasi dan sudah otorisasi --}}
    <script>
      const tabHome = document.getElementById('nav-home-tab');
      const tabProfile = document.getElementById('nav-profile-tab');

      function setActiveTab(homeActive) {
        if (homeActive) {
          tabHome.style.backgroundColor = '#007bff';
          tabHome.style.color = '#fff';
          tabProfile.style.backgroundColor = '#f8f9fa';
          tabProfile.style.color = '#007bff';
        } else {
          tabProfile.style.backgroundColor = '#007bff';
          tabProfile.style.color = '#fff';
          tabHome.style.backgroundColor = '#f8f9fa';
          tabHome.style.color = '#007bff';
        }
      }

      // Default warna tab
      setActiveTab(true);

      // buat ganti tab
      tabHome.addEventListener('click', function () {
        setActiveTab(true);
      });

      tabProfile.addEventListener('click', function () {
        setActiveTab(false);
      });
    </script>
  {{-- script buat hover so belum otorisasi dan sudah otorisasi --}}


@endsection
