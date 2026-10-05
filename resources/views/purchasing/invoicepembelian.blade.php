@extends('purchasing.newmaster')
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

@section('content')
<div class="container-fluid" style="margin-top:-80px;">
<h1>Kelengkapan Dokumen</h1>
<!-- <button onclick="loadAll()">tes</button> -->
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
          <div class="nav nav-tabs col-12" id="nav-tab" role="tablist" style="border-bottom: 0;">
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true"
            style="color: #fff; background-color: #007bff; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">Outstanding Pembelian</a>

            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false"
            style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">Transaksi Kelengkapan Dokumen(Belum Oto)</a>
            
            <a class="nav-item nav-link" id="nav-profile1-tab" data-toggle="tab" href="#profile1" role="tab" aria-controls="nav-profile1" aria-selected="false" 
              style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
              Transaksi Kelengkapan Dokumen(Oto)
            </a>
          </div>
      </div>
    </div>
    <div class="card-body" style="padding:0;">
      <div class="tab-content" id="myTabContent">

        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
          <div class="row">
            <div class="col-md-12">
                <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                    <table id="tabel" class="table table-bordered table-hover table-striped table-responsive-lg">                      
			              <thead class="text-center bg-primary text-white">
                        <tr style='white-space:nowrap;'>

                          <th scope="col">Actions</th>
                          <th scope="col">No. Bukti</th>
                          <th scope="col">Tanggal</th>
                          <th scope="col">Kode Supp</th>
                          <th scope="col">Nama Supplier</th>
                          <th scope="col">NO.PO</th>
                          <th scope="col">DPP</th>
                          <th scope="col">PPN</th>
                          <th scope="col">TOTAL</th>

                        </tr>
                      </thead>


                      <tbody id="tabel_data" class="text-left" >
                        @for ($j = 0; $j < count($tempoutstanding); $j++)
                        <tr id="tr{!! $j  !!}" onclick="" style='white-space:nowrap;'>
                        <td>
                          <button style="" class="btn btn-warning btn-sm" type="button" onclick="buttonDetailout('{{ $tempoutstanding[$j]->NoBukti}}')"><i class="bi bi-info"></i></button>
                          <button style="" class="btn btn-success btn-sm" type="button" onclick="buttonEditPembelian('{{ $tempoutstanding[$j]->NoPO}}')"><i class="bi bi-bag-plus-fill"></i></button>
                          <!-- <button style="" class="btn btn-primary btn-sm" type="button" onclick="buttonOtoPembelian('{{ $tempoutstanding[$j]->NoBukti}}')"><i class="bi bi-key"></i></button> -->
                        </td>
                            <td>{{ $tempoutstanding[$j]->NoBukti}}</td>
                            <td>{!! date("Y/m/d", strtotime($tempoutstanding[$j]->TANGGAL)) !!}</td>
                            <td>{{ $tempoutstanding[$j]->KODECUSTSUPP}}</td>
                            <td>{{ $tempoutstanding[$j]->NAMACUSTSUPP}}</td>

                            <td>{{ $tempoutstanding[$j]->NoPO}}</td>
                            <td class="text-right">  {{ number_format($tempoutstanding[$j]->TNDPP , 2 ,'.' , ',') }}</td>
                            <td class="text-right">  {{ number_format($tempoutstanding[$j]->TNPPN , 2 ,'.' , ',') }}</td>
                            <td class="text-right">  {{ number_format($tempoutstanding[$j]->TSUBTOTAL , 2 ,'.' , ',') }}</td>


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
            <div class="col-12" style="overflow:auto;">
              <table id="tabel_pembelian" class="table table-bordered table-striped"  >
                <thead class="text-center bg-primary text-white">
                  <tr style='white-space:nowrap;'>
                    <th scope="col">Actions</th>
                    <th scope="col">No. Bukti</th>
                    <th scope="col">Tanggal</th>
                    <th scope="col">Kode Supp</th>
                    <th scope="col" style='width : 1000px'>Nama Supplier</th>
                    <th scope="col">NO.PO</th>
                    <th scope="col">DPP</th>
                    <th scope="col">PPN</th>
                    <th scope="col">TOTAL</th>
                    
                  </tr>
                </thead>
                <tbody id="tabel_pembelian_data" class="text-left">
                    @for ($j = 0; $j < count($tampilinvoicebeli); $j++)
                    <tr style='white-space:nowrap;'>
                      <td>
                        <button style="" class="btn btn-warning btn-sm" type="button" onclick="detailPembelian('{{ $tampilinvoicebeli[$j]->NoBukti}}')"><i class="bi bi-info"></i></button>
                        <!-- <button style="" class="btn btn-primary btn-sm" type="button"   onclick="buttonPrintPembelian('{{$tampilinvoicebeli[$j]->NoBukti}}')" ><i class="bi bi-printer"></i></button> -->
                        <button style="" class="btn btn-primary btn-sm" type="button" onclick="submitOtorisasi1('{{ $tampilinvoicebeli[$j]->NoBukti}}')"><i class="bi bi-key"></i></button>
                        <button style="" class="btn btn-danger btn-sm" type="button" onclick="hapustransaksi('{{ $tampilinvoicebeli[$j]->NoBukti}}')"><i class="bi bi-trash"></i></button>
                      </td>
                      <td>{{ $tampilinvoicebeli[$j]->NoBukti}}</td>
                      <td>{!! date("Y/m/d", strtotime($tampilinvoicebeli[$j]->Tanggal)) !!}</td>
                      <td>{{ $tampilinvoicebeli[$j]->KODECUSTSUPP}}</td>
                      <td>{{ $tampilinvoicebeli[$j]->NamaCustSupp}}</td>

                      <td>{{ $tampilinvoicebeli[$j]->NoPO}}</td>

                      <td class="text-right">  {{ number_format($tampilinvoicebeli[$j]->TotDPPRp , 2 ,'.' , ',') }}</td>
                      <td class="text-right">  {{ number_format($tampilinvoicebeli[$j]->TotPPNRp , 2 ,'.' , ',') }}</td>
                      <td class="text-right">  {{ number_format($tampilinvoicebeli[$j]->TotNetRp , 2 ,'.' , ',') }}</td>
                     

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

        <div class="tab-pane fade" id="profile1" role="tabpanel" aria-labelledby="profile-tab">
          <div class="row">
            <div class="col-12" style="overflow:auto;">
              <table id="tabel_pembelian2" class="table table-bordered table-striped"  >
                <thead class="text-center bg-primary text-white">
                  <tr style='white-space:nowrap;'>
                    <th scope="col">Actions</th>
                    <th scope="col">No. Bukti</th>
                    <th scope="col">Tanggal</th>
                    <th scope="col">Kode Supp</th>
                    <th scope="col">Nama Supplier</th>
                    <th scope="col">NO.PO</th>
                    <th scope="col">DPP</th>
                    <th scope="col">PPN</th>
                    <th scope="col">TOTAL</th>
                    <th scope="col">User Oto 1</th>
                    <th scope="col">Tgl oto 1</th>
                  </tr>
                </thead>
                <tbody id="tabel_pembelian_data2" class="text-left">
                    @for ($j = 0; $j < count($tampilinvoicebeliOto); $j++)
                    <tr style='white-space:nowrap;'>
                      <td>
                        <button style="" class="btn btn-warning btn-sm" type="button" onclick="detailPembelian('{{ $tampilinvoicebeliOto[$j]->NoBukti}}')"><i class="bi bi-pen"></i></button>
                        <!-- <button style="" class="btn btn-primary btn-sm" type="button"   onclick="buttonPrintPembelian('{{$tampilinvoicebeliOto[$j]->NoBukti}}')" ><i class="bi bi-printer"></i></button> -->
                        <button style="" class="btn btn-danger btn-sm" type="button" onclick="submitUnOtorisasi1('{{ $tampilinvoicebeliOto[$j]->NoBukti}}')"><i class="bi bi-key"></i></button>
                      </td>
                      <td>{{ $tampilinvoicebeliOto[$j]->NoBukti}}</td>
                      <td>{!! date("Y/m/d", strtotime($tampilinvoicebeliOto[$j]->Tanggal)) !!}</td>
                      <td>{{ $tampilinvoicebeliOto[$j]->KODECUSTSUPP}}</td>
                      <td>{{ $tampilinvoicebeliOto[$j]->NamaCustSupp}}</td>

                      <td>{{ $tampilinvoicebeliOto[$j]->NoPO}}</td>

                      <td class="text-right">  {{ number_format($tampilinvoicebeliOto[$j]->TotDPPRp , 2 ,'.' , ',') }}</td>
                      <td class="text-right">  {{ number_format($tampilinvoicebeliOto[$j]->TotPPNRp , 2 ,'.' , ',') }}</td>
                      <td class="text-right">  {{ number_format($tampilinvoicebeliOto[$j]->TotNetRp , 2 ,'.' , ',') }}</td>
                     
                      <td>{{ $tampilinvoicebeliOto[$j]->OtoUser1}}</td>
                      <td>{{ $tampilinvoicebeliOto[$j]->TglOto1}}</td>
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







<!-- <div> -->



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
                <label>No Buktixxx</label>
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




<!-- start modal editpembelian  ADD -->
<div class="modal fade" id="editPembelian" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xxl modal-dialog-centered" role="document" style="max-width: 95%;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editPembelianModalLabel"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <div class="modal-body">
        <input type="hidden" name="noUrut" id="editnoUrut" value="{!! csrf_token() !!}" />
        
        <div class="container-fluid">
          <div class="row">
            <!-- TAMBAHAN UNTUK ACC -->
            <div class="col-md-3">
              <div class="row">
                <div class="col-4">
                  <div class="form-group">
                    <label>No Bukti</label>
                  </div>
                </div>
                <div class="col-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="editnobukti" placeholder="Nomor Bukti" disabled>
                  </div>
                </div>

                <div class="col-4" style='margin-top:-15px;'>
                  <div class="form-group">
                    <label>Tanggal</label>
                  </div>
                </div>
                <div class="col-8" style='margin-top:-15px;'>
                  <div class="form-group">
                    <input type="date" class="form-control text-center" id="editPembelianDate" value="{!! date('Y-m-d') !!}">
                  </div>
                </div>

                <div class="col-4" style='margin-top:-15px;'>
                  <div class="form-group">
                    <label>Supplier</label>
                  </div>
                </div>
                <div class="col-8" style='margin-top:-15px;'>
                  <div class="form-group">
                    <input type="text" class="form-control" id="editPembelianSupp" placeholder="Supplier" disabled>
                  </div>
                </div>

                <div class="col-4" style='margin-top:-15px;'>
                  <div class="form-group">
                    <label>PPN</label>
                  </div>
                </div>
                <div class="col-8" style='margin-top:-15px;'>
                  <div class="form-group">
                    <input type="text" class="form-control" id="editPembeliantipeppn" placeholder="Tipe PPN" disabled>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="row">
                <div class="col-4">
                  <div class="form-group">
                    <label>Tgl Invoice</label>
                  </div>
                </div>
                <div class="col-8">
                  <div class="form-group">
                    <input type="date" class="form-control text-center" id="edittglinvoice" value="{!! date('Y-m-d') !!}">
                  </div>
                </div>

                <div class="col-4" style='margin-top:-15px;'>
                  <div class="form-group">
                    <label>Tgl Pajak</label>
                  </div>
                </div>
                <div class="col-8" style='margin-top:-15px;'>
                  <div class="form-group">
                    <input type="date" class="form-control text-center" id="edittglfpajak" value="{!! date('Y-m-d') !!}">
                  </div>
                </div>

                <div class="col-4" style='margin-top:-15px;'>
                  <div class="form-group">
                    <label>Tgl B.Potong</label>
                  </div>
                </div>
                <div class="col-8" style='margin-top:-15px;'>
                  <div class="form-group">
                    <input type="date" class="form-control text-center" id="edittglbpotong" value="{!! date('Y-m-d') !!}">
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="row">
                <div class="col-4">
                  <div class="form-group">
                    <label>No. Invoice</label>
                  </div>
                </div>
                <div class="col-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="editnoinvoice" placeholder="No. Invoice">
                  </div>
                </div>

                <div class="col-4" style='margin-top:-15px;'>
                  <div class="form-group">
                    <label>No. Pajak</label>
                  </div>
                </div>
                <div class="col-8" style='margin-top:-15px;'>
                  <div class="form-group">
                    <input type="text" class="form-control" id="editnopajak" placeholder="No. Pajak">
                  </div>
                </div>

                <div class="col-4" style='margin-top:-15px;'>
                  <div class="form-group">
                    <label>No B.Potong</label>
                  </div>
                </div>
                <div class="col-8" style='margin-top:-15px;'>
                  <div class="form-group">
                    <input type="text" class="form-control" id="editnobpotong" placeholder="No. Bukti Potong">
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <!-- Empty column for spacing -->
            </div>
          </div>
        </div>


        <!-- ADD ITEM FORM -->
        <div id="formPembelianAdd" class="container-fluid showhide">
          <div class="line"></div>
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

          <div class="row">
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
                <button type="button" class="btn btn-secondary" onclick="buttonBatalShowHide()">Batal</button>
                <button type="button" onclick="submitPembelianAdd()" class="btn btn-primary">Add Item</button>
              </div>
            </div>
          </div>
          <div class="line"></div>
        </div>

        <!-- EDIT ITEM FORM -->
        <div id="formPembelianEdit" class="container-fluid showhide">
          <div class="line"></div>
          <div class="row">
            <div class="col-12">
              <h4>Edit Item</h4>
            </div>
          </div>

          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label>Kode Barang</label>
                <input id="editPembelianInputEditKode" type="text" class="form-control" disabled>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Nama Barang</label>
                <input id="editPembelianInputEditNamaBarang" type="text" class="form-control" disabled>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Qty</label>
                <input id="editPembelianInputEditQty" type="number" value="0.00" class="form-control">
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Satuan</label>
                <input type="text" id="editPembelianInputEditSatuan" value="PCS" class="form-control" disabled>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label>Harga</label>
                <input id="editPembelianInputEditHarga" type="number" value="0.00" class="form-control">
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>DISC %</label>
                <input id="editPembelianInputEditDisc" type="number" value="0.00" class="form-control">
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Disc Rp</label>
                <input id="editPembelianInputEditDiscRp" type="number" value="0.00" class="form-control">
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Qty POz</label>
                <input id="editPembelianInputEditQtyPO" type="number" value="0.00" class="form-control" disabled>
              </div>
            </div>
          </div>

          <div class="container-fluid mt-2">
            <div class="row">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="buttonBatalShowHide()">Batal</button>
                <button type="button" class="btn btn-success" onclick="submitPembelianEdit()">Edit Item</button>
              </div>
            </div>
          </div>
          <div class="line"></div>
        </div>

        <!-- TABLE -->
        <div class="container-fluid" style="overflow:auto;">
          <table id="editPembelianTable" class="table table-bordered table-striped mt-5">
            <thead class="text-center bg-primary text-white">
              <tr>
                <th scope="col">Buat Invoice</th>
                <th scope="col">No Pembelian</th>
                <th scope="col">Tgl Pembelian</th>
                <th scope="col">NOPO</th>
                <th scope="col">Tgl PO</th>
              </tr>
            </thead>
            <tbody id="editPembelianTableData" class="text-left">
              <tr>
                <td class="text-center"><input class="" type="checkbox" value="" id="flexCheckDefault"></td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <div class="container-fluid mt-2">
          <div class="row">
            <div class="col-md-12 text-right">
              <button type="button" class="btn btn-primary" onclick="saveKetFaktur()">Submit</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>
<!-- End modal editpembelian-->




<!-- //TAB KIRI -->


<!-- start modal detail INFORMASI-->
<div class="modal fade" id="detail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document"  style="min-width: 1500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel">Detail</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>




      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">




          <div class="col-4">
            <div class="form-group">
              <!-- <label>Tanggal</label> -->
              <input type="date" class="form-control text-center" id="detailDate" value="{!! date('Y-m-d') !!}"  >
              <input type="text" class="form-control" id="detailSupp" placeholder="Supplier" disabled>
            </div>
          </div>

<!-- DARI SINI -->

          <div class="col-md-3">
          <div class="row">

                  <div class="col-4">
                    <div class="form-group">
                      <label>Valas</label>
                    </div>
                  </div>

                  <div class="col-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="detailvalas" placeholder="valas" disabled>
                    </div>
                  </div>


                  <div class="col-4">
                    <div class="form-group">
                      <label>Tipe PPN</label>
                    </div>
                  </div>
                  <div class="col-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="detailtipeppn" placeholder="tipe ppn" disabled>
                    </div>
                  </div>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="row">
                    <div class="col-4">
                      <div class="form-group">
                        <label>Kurs</label>
                      </div>
                    </div>
                    <div class="col-8">
                      <div class="form-group">
                        <input type="text" class="form-control" id="detailkurs" placeholder="kurs" disabled>
                      </div>
                    </div>

                    <div class="col-4">
                      <div class="form-group">
                        <label>Tipe Bayar</label>
                      </div>
                    </div>
                    <div class="col-8">
                      <div class="form-group">
                          <input type="text" class="form-control" id="detailtipebayar" placeholder="tipe bayar" disabled>
                      </div>
                    </div>
                  </div>
                </div>


                <div class="col-md-3">
                  <div class="row">
                    <div class="col-4">
                      <div class="form-group">
                        <label>NO SO</label>
                      </div>
                    </div>
                    <div class="col-8">
                      <div class="form-group">
                        <input type="text" class="form-control" id="detailSoCustomer" placeholder="NO SO" disabled>
                      </div>
                    </div>

                    <div class="col-4">
                      <div class="form-group">
                        <label>UMB</label>
                      </div>
                    </div>
                    <div class="col-8">
                      <div class="form-group">
                        <!-- <input type="date" class="form-control text-center" id="input_add_tanggal" value="{!! date('Y-m-d') !!}"  > -->
                          <input type="text" class="form-control" id="detailNoUangMuka" placeholder="No Uang Muka" disabled>
                      </div>
                    </div>
                  </div>
                </div>



                <div class="col-md-3">
                  <div class="row">
                    <div class="col-4">
                      <div class="form-group">
                        <label>NUM</label>
                      </div>
                    </div>
                    <div class="col-8">
                      <div class="form-group">
                        <!-- <input type="date" class="form-control text-center" id="input_add_tanggalso" value="{!! date('yyyy-mm-dd') !!}" disabled > -->
                        <input type="text" class="form-control" id="detailNuangmuka" placeholder="N UM" disabled>
                      </div>
                    </div>

                    <div class="col-4">
                      <div class="form-group">
                        <label>PPN %</label>
                      </div>
                    </div>
                    <div class="col-8">
                      <div class="form-group">
                        <!-- <input type="date" class="form-control text-center" id="input_add_tanggal" value="{!! date('Y-m-d') !!}"  > -->
                          <input type="text" class="form-control" id="detailNilaiPPN" placeholder="11/12" disabled>
                      </div>
                    </div>
                  </div>
                </div>




                  <!-- SAMPAI SINI -->

                  <div class="col-md-3">
                  <div class="row">

                    <div class="col-4">
                      <div class="form-group">
                        <label>NO PO</label>
                      </div>
                    </div>

                    <div class="col-8">
                      <div class="form-group">
                        <input type="text" class="form-control" id="detailNoPO" placeholder="NOPO" disabled>
                      </div>
                    </div>


                    <div class="col-4">
                      <div class="form-group">
                        <label>Gudang</label>
                      </div>
                    </div>
                    <div class="col-8">
                      <div class="form-group">
                        <input type="text" class="form-control" id="detailgudang" placeholder="Gudang" disabled>
                      </div>
                    </div>

                </div>
                </div>


                <div class="col-md-3">
                <div class="row">

                  <div class="col-4">
                    <div class="form-group">
                      <label>Sopir</label>
                    </div>
                  </div>

                  <div class="col-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="detailNoSopir" placeholder="Nopol/Sopir" disabled>
                    </div>
                  </div>
              </div>
              </div>



              <div class="row">
                <div class="col-12">
                  <h5 id="">Keterangan</h5>
                </div>
                <div class="col-12">
                  <input type="text" autocomplete="off" class="form-control" id="detailKeterangan" >
                </div>
                <div class="col-12">
                  <h5 id="">FakturSupp</h5>
                </div>
                <div class="col-12">
                  <input type="text" autocomplete="off" class="form-control" id="detailFakturSupp" >
                </div>
              </div>



          </div>
          </div>
        </div>






        <div class="container-fluid" style="overflow-x: auto;">

              <table id="detailTable" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>

                    <th scope="col">Kode Barang</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Qty PO</th>
                    <th scope="col">Satuan</th>
                    <th scope="col">Harga</th>
                    <th scope="col">Disc</th>
                    <th scope="col">Subtotal</th>f


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
        <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="submitOtorisasi1()"  >Approve</button>

                  </div>
    </div>
  </div>
</div>
</div>
<!-- End modal detail pembelian-->

<!-- //TAB KANAN -->

<!-- start modal detail -->
<div class="modal fade" id="detailPembelian" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xxl modal-dialog-centered" role="document" style="max-width: 95%;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailPembelianModalLabel">Detail</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">
            <!-- TAMBAHAN UNTUK ACC -->
            <div class="col-md-3">
              <div class="row">
                <div class="col-4">
                  <div class="form-group">
                    <label>No Bukti</label>
                  </div>
                </div>
                <div class="col-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="editnobukti2" placeholder="Nomor Bukti" disabled>
                  </div>
                </div>

                <div class="col-4" style='margin-top:-15px;'>
                  <div class="form-group">
                    <label>Tanggal</label>
                  </div>
                </div>
                <div class="col-8" style='margin-top:-15px;'>
                  <div class="form-group">
                    <input type="date" class="form-control text-center" id="editPembelianDate2" value="{!! date('Y-m-d') !!}">
                  </div>
                </div>

                <div class="col-4" style='margin-top:-15px;'>
                  <div class="form-group">
                    <label>Supplier</label>
                  </div>
                </div>
                <div class="col-8" style='margin-top:-15px;'>
                  <div class="form-group">
                    <input type="text" class="form-control" id="editPembelianSupp2" placeholder="Supplier" disabled>
                  </div>
                </div>

                <div class="col-4" style='margin-top:-15px;'>
                  <div class="form-group">
                    <label>PPN</label>
                  </div>
                </div>
                <div class="col-8" style='margin-top:-15px;'>
                  <div class="form-group">
                    <input type="text" class="form-control" id="editPembeliantipeppn2" placeholder="Tipe PPN" disabled>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="row">
                <div class="col-4">
                  <div class="form-group">
                    <label>Tgl Invoice</label>
                  </div>
                </div>
                <div class="col-8">
                  <div class="form-group">
                    <input type="date" class="form-control text-center" id="edittglinvoice2" value="{!! date('Y-m-d') !!}">
                  </div>
                </div>

                <div class="col-4" style='margin-top:-15px;'>
                  <div class="form-group">
                    <label>Tgl Pajak</label>
                  </div>
                </div>
                <div class="col-8" style='margin-top:-15px;'>
                  <div class="form-group">
                    <input type="date" class="form-control text-center" id="edittglfpajak2" value="{!! date('Y-m-d') !!}">
                  </div>
                </div>

                <div class="col-4" style='margin-top:-15px;'>
                  <div class="form-group">
                    <label>Tgl B.Potong</label>
                  </div>
                </div>
                <div class="col-8" style='margin-top:-15px;'>
                  <div class="form-group">
                    <input type="date" class="form-control text-center" id="edittglbpotong2" value="{!! date('Y-m-d') !!}">
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="row">
                <div class="col-4">
                  <div class="form-group">
                    <label>No. Invoice</label>
                  </div>
                </div>
                <div class="col-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="editnoinvoice2" placeholder="No. Invoice">
                  </div>
                </div>

                <div class="col-4" style='margin-top:-15px;'>
                  <div class="form-group">
                    <label>No. Pajak</label>
                  </div>
                </div>
                <div class="col-8" style='margin-top:-15px;'>
                  <div class="form-group">
                    <input type="text" class="form-control" id="editnopajak2" placeholder="No. Pajak">
                  </div>
                </div>

                <div class="col-4" style='margin-top:-15px;'>
                  <div class="form-group">
                    <label>No B.Potong</label>
                  </div>
                </div>
                <div class="col-8" style='margin-top:-15px;'>
                  <div class="form-group">
                    <input type="text" class="form-control" id="editnobpotong2" placeholder="No. Bukti Potong">
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <!-- Empty column for spacing -->
            </div>
          </div>
        </div>

        <!-- TABLE -->
        <div class="container-fluid mt-4" style="overflow: auto">
          <table id="editPembelianTable2" class="table table-bordered table-striped">
            <thead class="text-center bg-primary text-white">
              <tr>
                <th scope="col">Action</th>
                <th scope="col">No Pembelian</th>
                <th scope="col">Gudang</th>
                <th scope="col">Kode Barang</th>
                <th scope="col">Nama Barang</th>
                <th scope="col">Qty</th>
                <th scope="col">Sat</th>
                <th scope="col">Harga</th>
                <th scope="col">Total</th>
              </tr>
            </thead>
            <tbody id="editPembelianTableData2" class="text-left">
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

        <!-- Commented out footer buttons
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" onclick="submitUnOtorisasi1()">Buka Oto</button>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" onclick="edittransaksi()">simpan edit</button>
        </div>
        -->
      </div>
    </div>
  </div>
</div>
<!-- End modal detail-->


<!-- start modal editpembelian  -->
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
            <!-- <div class="col-2">


            </div> -->
            <!-- <div class="col-1">
                <input type="number" value=1 class="form-control text-right" id="input_print_jumlahprint" >
            </div> -->

          </div>

        </div>
          </div>

          <!-- <div class="modal-footer showhide">
            <button type="button" class="btn btn-secondary" data-dismiss="" >Batal</button>
            <button type="button" class="btn btn-primary" onclick="">Submit</button>
          </div>

          <div class="modal-footer showhide">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
            <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button>
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



 <!-- detail informasi tab kiri/out beli -->
  <!-- start modal tab kiri detail INFORMASI-->
<div class="modal fade" id="detailout" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xxl modal-dialog-centered" role="document" style="max-width: 95%;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabelout">Detail</h5>
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
                  <input type="text" class="form-control text-left" id="detailPembelianNobuktiout" placeholder="" disabled>
                </div>
              </div>
              <div class="col-md-4" style='margin-top:-15px;'>
                <div class="form-group">
                  <label>Tanggal</label>
                </div>
              </div>
              <div class="col-md-8" style='margin-top:-15px;'>
                <div class="form-group">
                  <input type="date" class="form-control text-left" id="detailDateout" value="{!! date('Y-m-d') !!}" disabled>
                </div>
              </div>

              <div class="col-md-4" style='margin-top:-15px;'>
                <div class="form-group">
                  <label>Supplier</label>
                </div>
              </div>

              <div class="col-md-8" style='margin-top:-15px;'>
                <div class="input-group mb-3">
                  <input type="text" class="form-control text-left" placeholder="Kode Pelanggan" id="detailPembelianKodeSuppout" disabled>
                </div>
              </div>

              <div class="col-md-4" style='margin-top:-15px;'>
                <div class="form-group">
                  <label>No PO</label>
                </div>
              </div>

              <div class="col-md-8" style='margin-top:-15px;'>
                <div class="input-group mb-3">
                  <input type="text" class="form-control text-left" placeholder="No PO" id="detailNoPOout" disabled>
                </div>
              </div>

              <div class="col-md-4" style='margin-top:-15px;'>
                <div class="form-group">
                  <label>Surat Jln Supp</label>
                </div>
              </div>

              <div class="col-md-8" style='margin-top:-15px;'>
                <div class="input-group mb-3">
                  <input type="text" class="form-control text-left" placeholder="Surat Jalan Supplier" id="detailFakturSuppout" disabled>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <input type="text" class="form-control text-center" placeholder="Nama Pelanggan" id="detailPembelianSuppout" disabled>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <textarea style="width: 100%; resize: none" rows=3 placeholder="Alamat Pelanggan" class="form-control text-center" id="detailPembelianAlamatSuppout" disabled></textarea>
                </div>
              </div>

              <div class="col-md-12">
                <div class="row">
                  <div class="form-group col-4">
                    <label>Gudang</label>
                  </div>
                  <div class="col-md-8" style='margin-left:-32px;'>
                    <div class="form-group">
                      <input type="text" style="width: 220px;resize: none" class="form-control text-left" placeholder="Gudang" id="detailgudangout" disabled>
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
                      <input type="text" class="form-control" id="detailPembelianvalasout" disabled>
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
                      <input type="text" class="form-control" id="detailPembeliankursout" disabled>
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
                      <input type="number" class="form-control text-left" id="detailPembelianhariout" value=0 min=0 disabled>
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
                      <input type="text" class="form-control text-left" id="detailNoSopirout" disabled>
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
                      <input type="text" class="form-control text-left" id="detailSoCustomerout" disabled>
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
                      <select onchange="onChangeInputAddPembayaran()" id="detailPembeliantipebayarout" class="form-control form-select-lg mb-3 text-center" aria-label=".form-select-lg example" disabled>
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
                    <input type="date" class="form-control text-center" id="detailPembelianJthTempoout" value="{!! date('Y-m-d') !!}" onblur="onChangeTgglKirim()" disabled>
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
                    <select onchange="onChangeTipePPN()" id="detailPembeliantipeppnout" class="form-control text-center form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
                      <option value=0 selected>None</option>
                      <option value=1>Exclude</option>
                      <option value=2>Include</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="col-md-12">
                <div class="row">
                  <div class="col-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <label>No Uang Muka</label>
                    </div>
                  </div>
                  <div class="col-md-6" style='margin-top:-15px;'>
                    <div class="form-group">
                      <input type="text" class="form-control text-left" id="detailNoUangMukaout" disabled>
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
                      <input type="number" class="form-control text-left" id="detailNuangmukaout" disabled>
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
              <table id="detailTableout" class="table table-bordered table-striped">
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
                <tbody id="detailTableDataout" class="text-left">
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
                <input type="number" class="form-control text-right" id="input_det_discout" onblur="onChangeInputAddDisc()" value="0.00" disabled>
              </div>
            </div>
          </div>

          <div class="col">
            <div class="row">
              <div class="col-3 d-flex align-items-center">
                <label>DiscRp</label>
              </div>
              <div class="col-8">
                <input type="number" class="form-control text-right" id="input_det_discrpout" onblur="onChangeInputAddDiscRp()" value="0.00" disabled>
              </div>
            </div>
          </div>

          <div class="col">
            <div class="row">
              <div class="col-3 d-flex align-items-center">
                <label>DPP</label>
              </div>
              <div class="col-8">
                <input type="text" class="form-control text-right" id="input_det_dppout" value="0.00" disabled>
              </div>
            </div>
          </div>

          <div class="col">
            <div class="row">
              <div class="col-3 d-flex align-items-center">
                <label>PPN</label>
              </div>
              <div class="col-8">
                <input type="text" class="form-control text-right" id="input_det_ppnout" value="0.00" disabled>
              </div>
            </div>
          </div>

          <div class="col">
            <div class="row">
              <div class="col-5 d-flex align-items-center">
                <label>GrandTotal</label>
              </div>
              <div class="col-7">
                <input type="text" class="form-control text-right" id="input_det_grandtotalout" value="0.00" disabled>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Commented out footer button
      <div class="modal-footer">
        <button type="button" id="btnotokiri" class="btn btn-primary" style="height: 30px; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; transition: background-color 0.3s, box-shadow 0.3s; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="submitOtorisasi1()">Approve</button>
      </div>
      -->
    </div>
  </div>
</div>
    <!-- End modal detail Informasi-->




@endsection

@section('js')
  <script type="text/javascript">

    let row_id = "";
    let action = "";
    let row_data = {};
    let DataNobukti = "";
  let listDataEdit = [];

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
    let dataRefreshPembelianOto = []

    let dataPrint = []

    $(document).ready(function(){
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

        $(document).ready(function(){
      $("#tabel_pembelian2").DataTable({
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

// informasi pembelian KANAN

      function detailPembelian (nobukti) {


      let _token = $("#_token").val();
      let table_pembelian_row_detail =[]



      $.ajax({
        url: "{!! url('detailInvoiceBeli') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          NoBukti: nobukti
        },
        success: function(res) {

          console.log(res,'res')

          table_pembelian_row_detail = res
          console.log(res[0])
          // console.log(res[0][0])
          // console.log(res[0][0].pQC)

        }
      })

      dataLPB =  table_pembelian_row_detail[0]
      dataEditPembelianEdit = table_pembelian_row_detail


       let date = new Date(dataLPB.Tanggal);
      var day = ("0" + date.getDate()).slice(-2);
      var month = ("0" + (date.getMonth() + 1)).slice(-2);
       var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;


       let dateinvoice = new Date(dataLPB.TglInvoice);
      var dayinvoice = ("0" + dateinvoice.getDate()).slice(-2);
      var monthinvoice = ("0" + (dateinvoice.getMonth() + 1)).slice(-2);
       var date1invoice = dateinvoice.getFullYear()+"-"+(monthinvoice)+"-"+(dayinvoice) ;

       let datefpajak = new Date(dataLPB.TglFakturPajak);
      var dayfpajak = ("0" + datefpajak.getDate()).slice(-2);
      var monthfpajak = ("0" + (datefpajak.getMonth() + 1)).slice(-2);
       var date1fpajak = datefpajak.getFullYear()+"-"+(monthfpajak)+"-"+(dayfpajak) ;



       let datebpotong = new Date(dataLPB.TglBuktiPotong);
      var daybpotong = ("0" + datebpotong.getDate()).slice(-2);
      var monthbpotong = ("0" + (datebpotong.getMonth() + 1)).slice(-2);
       var date1bpotong = datefpajak.getFullYear()+"-"+(monthbpotong)+"-"+(daybpotong) ;



       let table_row_detail_inf = ""


console.log(table_pembelian_row_detail)

       table_pembelian_row_detail.forEach((detail_row, i) => {
         console.log('DATADETAIL',detail_row)
   console.log(detail_row.KODEBARANG,'xb',i)
         table_row_detail_inf += `<tr>
         <td>
         
         <button class="btn btn-success btn-sm" type="button" onclick="hapustransaksi(${detail_row.KODEBARANG})"><i class="bi bi-trash"></i></button>
         </td>
         
         <td>${detail_row.NoBeli}</td>
         <td>${detail_row.KodeGdg}</td>
         <td>${detail_row.KODEBARANG}</td>
         <td>${detail_row.NAMABARANG}</td>
         <td class="text-right">${detail_row.QNT ? formatAngkaX(detail_row.QNT) : '0.00' }</td>
 
         <td>${detail_row.SATUAN}</td>
         <td class="text-right">${detail_row.HARGA ? formatAngkaX(detail_row.HARGA) : '0.00' }</td>
          <td class="text-right">${detail_row.NNET ? formatAngkaX(detail_row.NNET) : '0.00' }</td>
        
         
         </tr>`


      });





      let fakturSupp = ""
      let keterangan = ""
      if (dataLPB.NoPO) {
        fakturSupp = dataLPB.NoPO
      }
      if (dataLPB.KETERANGAN) {
        keterangan = dataLPB.KETERANGAN
      }
      document.getElementById("detailPembelianModalLabel").innerHTML = dataLPB.NoBukti;




      document.getElementById("editnobukti2").value = dataLPB.NoBukti;
      document.getElementById("editPembelianSupp2").value = dataLPB.NamaCustSupp;
      document.getElementById("editPembelianDate2").value = date1;

      document.getElementById("editPembeliantipeppn2").value = dataLPB.myppn;
      document.getElementById("edittglinvoice2").value = date1invoice;
      document.getElementById("edittglfpajak2").value = date1fpajak;
      document.getElementById("edittglbpotong2").value = date1bpotong;
      document.getElementById("editnoinvoice2").value = dataLPB.NoInvoice;
      document.getElementById("editnopajak2").value = dataLPB.NoFakturPajak;
      document.getElementById("editnobpotong2").value = dataLPB.NoBuktiPotong;











      $("#detailPembelianDate").val(date1);
      $("#detailPembelian").modal('toggle');
console.log('vvvvvvvvvvvvvvvvvv')
      document.getElementById("editPembelianTableData2").innerHTML = table_row_detail_inf


    }





    function edittransaksi () {

      let nobukti = document.getElementById("editnobukti2").value ;;
      let otorisasi = 1;
      let tanggal=document.getElementById("edittglinvoice2").value ;
      let tglfpajak=document.getElementById("edittglfpajak2").value ;
      let tglinvoice=document.getElementById("edittglinvoice2").value ;
      let tglbpotong=document.getElementById("edittglbpotong2").value ;
      let nopajak=document.getElementById("editnopajak2").value ;
      let noinvoice=document.getElementById("editnoinvoice2").value ;
      let nobuktipotong=document.getElementById("editnobpotong2").value ;

      console.log(nobukti, otorisasi)
      let _token = $("#_token").val();


      $.ajax({
        url: "{!! url('sp_edittransaksi') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          nobukti,
          tanggal,
          tglfpajak,
          tglbpotong,
          tglinvoice,
          nopajak,
          nobuktipotong,
          noinvoice



        },
        success: function(res) {
          if (res > 0) {
            console.log(nobukti, otorisasi)
            loadAll()

            alertify.success('Berhasil update otorisasi')
            $("#detailPembelian").modal('toggle')
          }


      },
      error: function (err) {
        alertify.warning('Terjadi kesalahan pada server, silahkan refresh ulang')
      }
    })
    }


    function hapustransaksi () {

     


        alertify.confirm('Hapus Menu', 'Apakah yakin ingin menghapus data ?',
      function(){


                     let nobukti = document.getElementById("editnobukti2").value ;;
                    console.log(nobukti)
                    let _token = $("#_token").val();

                    $.ajax({
                      url: "{!! url('sp_hapusinvoice') !!}",
                      type: "post",
                      async: false,
                      data: {
                        _token,
                        nobukti

                      },
                      success: function(res) {
                        if (res > 0) {
                          console.log(nobukti)
                          loadAll()

                          alertify.success('Berhasil Hapus Data')
                          $("#detailPembelian").modal('toggle')
                        }


                    },
                    error: function (err) {
                      alertify.warning('Terjadi kesalahan pada server, silahkan refresh ulang')
                    }
                    })

         }
      ,function(){});            

    }







    function tesModal() {
      //
      // $("#tes1234").modal('toggle');
      $('#formPembelianAdd').show();
    }



      function submitOtorisasi1 (nobukti) {
  console.log('=============================')
        // let nobukti = DataNobukti;
        let otorisasi = 1
        console.log(nobukti, otorisasi,'----t')
        let _token = $("#_token").val();


        $.ajax({
          url: "{!! url('spotorisasiInvoiceBeli') !!}",
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

              alertify.success('Berhasil update otorisasi')
              // $("#formOtorisasi1").modal('toggle')
            }


        },
        error: function (err) {
          alertify.warning('Terjadi kesalahan pada server, silahkan refresh ulang')
        }
      })
      }


      function submitUnOtorisasi1 (nobukti) {

        // let nobukti = DataNobukti;
        console.log(nobukti)
        let otorisasi = 1
        console.log(nobukti, otorisasi)
        let _token = $("#_token").val();


        $.ajax({
          url: "{!! url('spUnotorisasiInvoiceBeli') !!}",
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

              alertify.success('Berhasil update otorisasi')
              $("#formOtorisasi1").modal('toggle')
            }


        },
        error: function (err) {
          alertify.warning('Terjadi kesalahan pada server, silahkan refresh ulang')
        }
      })
      }






      function buttonUnOtoPembelian(nobukti) {
        console.log(nobukti,  "<<< detail")
        console.log('================')
        console.log(nobukti)


        let _token = $("#_token").val();
        let table_pembelian_row_detail =[]
        DataNobukti = nobukti


        $.ajax({
          url: "{!! url('detailPembelianACC') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            NoBukti: nobukti
          },
          success: function(res) {
              console.log('======xxxxx==========')
            console.log(res)

            table_pembelian_row_detail = res[0]
            console.log(res[0])
            // console.log(res[0][0])
            // console.log(res[0][0].pQC)

          }
        })


        dataLPB =  table_pembelian_row_detail[0]
        dataEditPembelianEdit = table_pembelian_row_detail

         let date = new Date(table_pembelian_row_detail[0].TANGGAL);
        var day = ("0" + date.getDate()).slice(-2);
        var month = ("0" + (date.getMonth() + 1)).slice(-2);
         var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;

         let table_row_detail_inf = ""

        table_pembelian_row_detail.forEach((detail_row, i) => {
    console.log(detail_row.KodeBrg)
           table_row_detail_inf += `<tr><td>${detail_row.KodeBrg}</td><td>${detail_row.namabrgx}</td><td class="text-right">${detail_row.Qnt}</td><td class="text-right">${detail_row.QNTPO}</td><td>${detail_row.Satuan}</td><td class="text-right">${Number(detail_row.Harga) ? detail_row.Harga : "0.00"}</td><td class="text-right">${detail_row.DiscRp1}</td><td class="text-right">${detail_row.NNET}</td></tr>`

        });


        let fakturSupp = ""
        let keterangan = ""
        if (table_pembelian_row_detail[0].FAKTURSUPP) {
          fakturSupp = table_pembelian_row_detail[0].FAKTURSUPP
        }
        if (table_pembelian_row_detail[0].KETERANGAN) {
          keterangan = table_pembelian_row_detail[0].KETERANGAN
        }
        document.getElementById("detailPembelianModalLabel").innerHTML = table_pembelian_row_detail[0].NoBukti;


        // document.getElementById("detailPembelianSupp").value = table_pembelian_row_detail[0].NamaSupplier
        // document.getElementById("detailPembelianFakturSupp").value = fakturSupp
        // document.getElementById("detailPembelianKeterangan").value =  keterangan
        // document.getElementById("detailPembelianNoPO").value = table_pembelian_row_detail[0].NoPO
        // document.getElementById("detailPembeliangudang").value = table_pembelian_row_detail[0].NAMAGUDANG



        document.getElementById("detailPembelianSupp").value = table_pembelian_row_detail[0].NamaSupplier;

        document.getElementById("detailPembelianvalas").value = table_pembelian_row_detail[0].KODEVLS;


        document.getElementById("detailPembelianNoPO").value = table_pembelian_row_detail[0].NoPO
        document.getElementById("detailPembelianFakturSupp").value = table_pembelian_row_detail[0].FAKTURSUPP
        document.getElementById("detailPembelianKeterangan").value = table_pembelian_row_detail[0].KETERANGAN
        ///document.getElementById("detailTableData").innerHTML = table_row_detail



        document.getElementById("detailPembelianvalas").value = table_pembelian_row_detail[0].KODEVLS
        document.getElementById("detailPembeliankurs").value = table_pembelian_row_detail[0].KURS

        document.getElementById("detailPembeliantipeppn").value = table_pembelian_row_detail[0].MyPPN
        document.getElementById("detailPembeliantipebayar").value = table_pembelian_row_detail[0].MyTipeBayar

        document.getElementById("detailPembelianSoCustomer").value = table_pembelian_row_detail[0].NOSO
        document.getElementById("detailPembelianNoUangMuka").value = table_pembelian_row_detail[0].NOUMK

        document.getElementById("detailPembelianNuangmuka").value = table_pembelian_row_detail[0].NuangMuka
        document.getElementById("detailPembelianNilaiPPN").value = table_pembelian_row_detail[0].NilaiPPN

        document.getElementById("detailPembeliangudang").value = table_pembelian_row_detail[0].NAMAGUDANG
        // document.getElementById("detailFakturSupp").value = detail_row_data[0].FAKTURSUPP

        document.getElementById("detailPembelianNoSopir").value = table_pembelian_row_detail[0].SOPIR










        $("#detailPembelianDate").val(date1);
        $("#detailPembelian").modal('toggle');
    console.log('vvvvvvvvvvvvvvvvvv')
        document.getElementById("detailPembelianTableData").innerHTML = table_row_detail_inf


      }

      function tesModal() {
        //
        // $("#tes1234").modal('toggle');
        $('#formPembelianAdd').show();
      }



        function submitOtorisasi1 (nobukti) {
  console.log(nobukti)
          // let nobukti = DataNobukti;
          let otorisasi = 1
          console.log(nobukti, otorisasi)
          let _token = $("#_token").val();


          $.ajax({
            url: "{!! url('spotorisasiInvoiceBeli') !!}",
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

                alertify.success('Berhasil update otorisasi')
                // $("#formOtorisasi1").modal('toggle')
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
       console.log('koreksi')
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
     
      document.getElementById("editPembelianInputEditKode").value = dataEditPembelianEdit[indexBarang].KodeBrg
      document.getElementById("editPembelianInputEditNamaBarang").value = dataEditPembelianEdit[indexBarang].namabrgx
      document.getElementById("editPembelianInputEditQtyPO").value = dataEditPembelianEdit[indexBarang].QNTPO
      document.getElementById("editPembelianInputEditSatuan").value = dataEditPembelianEdit[indexBarang].Satuan
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

    function loadAll () {

      $.ajax({
        url: "{!! url('getAksesInvoiceBeli') !!}",
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
        url: "{!! url('getAlloutbeliinv') !!}",
        type: "get",
        async: false,
        success: function(res) {
          let rowTabel = ""
          res.forEach((item, i) => {
            console.log(item)
            console.log(item.TotDPP)
            let date1 = ""
            if (item.TANGGAL) {
              let date = new Date(item.TANGGAL);
              let day = ("0" + date.getDate()).slice(-2);
              let month = ("0" + (date.getMonth() + 1)).slice(-2);
              date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
            }

            rowTabel += `
            <tr style='white-space:nowrap;'>
              <td>
            <button style="" class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('${item.NoBukti}')"><i class="bi bi-pen"></i></button>
            <button style="" class="btn btn-success btn-sm" type="button" onclick="buttonEditPembelian('${item.NoPO}')"><i class="bi bi-bag-plus-fill"></i></button>

            <td>${item.NoBukti ? item.NoBukti : '' }</td>
            <td>${date1}</td>
            <td>${item.KODECUSTSUPP ? item.KODECUSTSUPP : '' }</td>
            <td>${item.NAMACUSTSUPP ? item.NAMACUSTSUPP : '' }</td>
            <td>${item.NoPO ? item.NoPO : '' }</td>

            <td class="text-right">${item.TNDPP ? formatAngkaX(item.TNDPP) : '0.00' }</td>
            <td class="text-right">${item.TNPPN ? formatAngkaX(item.TNPPN) : '0.00' }</td>
            <td class="text-right">${item.TSUBTOTAL ? formatAngkaX(item.TSUBTOTAL) : '0.00' }</td>

            </tr>`

          });
          document.getElementById("tabel_data").innerHTML = rowTabel
        }
      })

      $('#tabel_pembelian').DataTable().destroy();

      $.ajax({
        url: "{!! url('getAllInvoiceBeli') !!}",
        type: "get",
        async: false,
        success: function(res) {
          // console.log(res[0])
          dataRefreshPembelian = res.tampilinvoicebeli;
          dataRefreshPembelianOto = res.tampilinvoicebeliOto;

          let rowTabel2 = ""
          dataRefreshPembelian.forEach((item, i) => {

            let date1 = ""
            if (item.Tanggal) {
              let date = new Date(item.Tanggal);
              let day = ("0" + date.getDate()).slice(-2);
              let month = ("0" + (date.getMonth() + 1)).slice(-2);
              date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
            }
            console.log('===============>',item.IsOtorisasi1);
            rowTabel2 += `<tr style='white-space:nowrap;'>
            <td>
            <button style="" class="btn btn-warning btn-sm" type="button" onclick="detailPembelian('${item.NoBukti}')"><i class="bi bi-info"></i></button>
            <button style="" class="btn btn-primary btn-sm" type="button" onclick="submitOtorisasi1('${item.NoBukti}')"><i class="bi bi-key"></i></button>
            <button style="" class="btn btn-danger btn-sm" type="button" onclick="hapustransaksi('${item.NoBukti}')"><i class="bi bi-trash"></i></button>
            </td>

            <td>${item.NoBukti ? item.NoBukti : '' }</td>
            <td>${date1}</td>
            <td>${item.KODECUSTSUPP ? item.KODECUSTSUPP : '' }</td>
            <td style='width:1000px'> ${item.NamaCustSupp ? item.NamaCustSupp : '' }</td>

            <td>${item.NoPO ? item.NoPO : '' }</td>

            <td>${item.TotDPPRp ?  formatAngka(parseFloat(item.TotDPPRp).toFixed(2)) : '0.00' }</td>
            <td>${item.TotPPNRp ?  formatAngka(parseFloat(item.TotPPNRp).toFixed(2)) : '0.00' }</td>
            <td>${item.TotNetRp ?  formatAngka(parseFloat(item.TotNetRp).toFixed(2)) : '0.00' }</td>

            </tr>`
          });

          document.getElementById("tabel_pembelian_data").innerHTML = rowTabel2

          let rowTabel3 = ""
          dataRefreshPembelianOto.forEach((item, i) => {

            let date1 = ""
            if (item.Tanggal) {
              let date = new Date(item.Tanggal);
              let day = ("0" + date.getDate()).slice(-2);
              let month = ("0" + (date.getMonth() + 1)).slice(-2);
              date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
            }
            console.log('===============>',item.IsOtorisasi1);
            rowTabel3 += `<tr style='white-space:nowrap;'>
            <td>

            <button style="" class="btn btn-warning btn-sm" type="button" onclick="detailPembelian('${item.NoBukti}')"><i class="bi bi-pen"></i></button>
            <button style="" class="btn btn-danger btn-sm" type="button" onclick="submitUnOtorisasi1('${item.NoBukti}')"><i class="bi bi-key"></i></button>

            </td>

            <td>${item.NoBukti ? item.NoBukti : '' }</td>
            <td>${date1}</td>
            <td>${item.KODECUSTSUPP ? item.KODECUSTSUPP : '' }</td>
            <td style='width:1000px'> ${item.NamaCustSupp ? item.NamaCustSupp : '' }</td>

            <td>${item.NoPO ? item.NoPO : '' }</td>

            <td>${item.TotDPPRp ?  formatAngka(parseFloat(item.TotDPPRp).toFixed(2)) : '0.00' }</td>
            <td>${item.TotPPNRp ?  formatAngka(parseFloat(item.TotPPNRp).toFixed(2)) : '0.00' }</td>
            <td>${item.TotNetRp ?  formatAngka(parseFloat(item.TotNetRp).toFixed(2)) : '0.00' }</td>

            <td>${item.OtoUser1 ? item.OtoUser1 : '' }</td>
            <td>${item.TglOto1 ? item.TglOto1 : '' }</td>
            </tr>`
          });

          document.getElementById("tabel_pembelian_data2").innerHTML = rowTabel3
        }
      })


            // <button style="" class="btn btn-primary btn-sm" type="button"   onclick="buttonPrintPembelian('${item.NoBukti}')" ><i class="bi bi-printer"></i></button> tombol print buat loadAll


      function formatAngka (angkaString) {
        // console.log('formatAngka' , angkaString);
        let tempAngka = angkaString.split('.')

        if (tempAngka[0][0] == '-') {
          let temp2=''

          let tempAngka1 = tempAngka[0].split('-')
          for (let i = 0; i < tempAngka1[1].length; i++) {
            if (i != 0 && i % 3 == 0) {
              temp2 = ',' + temp2
            }
            temp2 = tempAngka1[1][tempAngka1[1].length - i -1] + temp2
            // console.log(i, temp2)
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
          // console.log(i, temp1)
        }
        temp1 += '.' + tempAngka[1]
        return temp1
      }


    }

    function resetDetailPembelian (noBukti, noPO) {
      // SML/LPB/00197/0323
      // SML/PO/00302/0223
      let _token = $("#_token").val();
      console.log(noBukti, noPO)
      $('.showhide').hide();

      $.ajax({
        url: "{!! url('detailInvoiceBeli') !!}",
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
        let date = new Date(dataLPB.Tanggal);
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
        // document.getElementById("editPembelianNoPO").value = dataLPB.NoPO
        document.getElementById("editPembelianSupp").value = dataLPB.NamaSupplier
        document.getElementById("editPembelianFakturSupp").value = dataLPB.FAKTURSUPP
        document.getElementById("editPembelianKeterangan").value = dataLPB.KETERANGAN
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

            let reqNoBukti = dataLPBDelete.NoBukti
            let reqNoUrut = dataLPBDelete.NoUrut
            let reqTANGGAL = dataLPBDelete.TANGGAL
            let reqKodeSupp = dataLPBDelete.KODESUPP
            let reqKodeGudang = dataLPBDelete.KODEGDG
            let reqNoPO = dataLPBDelete.NoPO
            let reqKeterangan = dataLPBDelete.KETERANGAN
            let reqFakturSupp = dataLPBDelete.FAKTURSUPP

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

      dataLPBEdit = dataEditPembelianEdit[0]




      let reqNobukti = $("#editnobukti").val()
      let reqNourut =  $("#editnoUrut").val()
      let reqTanggal = $("#editPembelianDate").val()
      let reqNopo = dataLPBEdit.NoPO

      let reqTglpajak = $("#edittglfpajak").val()
      let reqTglbpotong = $("#edittglbpotong").val()
      let reqTglinvoice = $("#edittglinvoice").val()
      let reqNopajak = $("#editnopajak").val()
      let reqNobpotong = $("#editnobpotong").val()
      let reqNoinvoice = $("#editnoinvoice").val()
      let tempData = []
      for (let i = 0; i < listDataEdit.length; i++) {
          if (document.getElementById(`add_checkbox${i}`).checked) {
          listDataEdit[i].inputQntTerima = $(`#input_add_qntTerima${i}`).val();
          tempData.push(listDataEdit[i])
        }
      }

      if(!tempData.length) {
        alertify.warning("Tidak ada item dipilih");
        return
      }


  console.log(reqNobukti)
  console.log(reqNourut)
  console.log(tempData)


      $.ajax({
        url: "{!! url('sp_beligudangInvoiceBeli') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          reqNobukti,
          reqNourut,
          reqTanggal,
          reqNopo,
          reqTglpajak,
          reqTglbpotong,
          reqTglinvoice,
          reqNopajak,
          reqNobpotong,
          reqNoinvoice,
          tempData

        },
        success: function(res) {
          // console.log(res , "RESPOND");
          console.log(res)
          console.log("successsssssssssssssssssssss")
          loadAll()
          // resetDetailPembelian(reqNobukti,reqNopo)
            $("#editPembelian").modal('toggle');
          alertify.success('Faktur dan keterangan telah diupdate');
        }
      })

      console.log(reqNourut,reqNourut)

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
        url: "{!! url('sp_beligudangACC') !!}",
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
      console.log("qwerthhh")
      console.log(nobukti)
     setNewNoBukti()
      let _token = $("#_token").val();

      let edit_pembelian_row_data = []

      $.ajax({
        url: "{!! url('detailbeli') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          // NoPO: edit_pembelian_row_data[0].NoPO,
          NoBukti: nobukti
        },
        success: function(res) {
            console.log("xxxxcc")
          console.log(res)
          listDataEdit = res
          edit_pembelian_row_data = res
          // console.log(res[0])
          // console.log(res[0][0])
          // console.log(res[0][0].KodeBrg)

        }
      })

          if(!edit_pembelian_row_data.length) {
            console.log('------------')
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

            let satuan = ""
            if (r.Satuan) {
              satuan = r.Satuan
            }
            table_row_edit_pembelian += `<tr><td class="text-center"><input id="add_checkbox${i}" class="" type="checkbox" ></td><td>${r.NoBukti}</td><td class="text-right">${r.Tanggal}</td><td class="text-right">${r.NoPO}</td><td class="text-right">${r.TglPO}</td></tr>`
            });
          // document.getElementById("editPembelianModalLabel").innerHTML = "Edit " +  edit_pembelian_row_data[0].NoBukti;
          document.getElementById("editPembelianSupp").value = edit_pembelian_row_data[0].NAMACUSTSUPP
          document.getElementById("editPembelianTableData").innerHTML = table_row_edit_pembelian
          document.getElementById("editPembeliantipeppn").value = edit_pembelian_row_data[0].MyPPN




          console.log(edit_pembelian_row_data[0].NoBukti , "<<<<<<<<<<")

          $.ajax({
            url: "{!! url('detailPOBeli') !!}",
            type: "post",
            async: false,
            data: {
              _token : _token,

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
          // $('#editPembelianDate').val(date1)
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
            url: "{!! url('detailPembelianACC') !!}",
            type: "post",
            async: false,
            data: {
              _token : _token,
              NoBukti: nobukti
            },
            success: function(res) {
                console.log('======xxxxx==========')
              console.log(res)

              detail_row_data = res[0]
              console.log(res[0])
              // console.log(res[0][0])
              // console.log(res[0][0].pQC)

            }
          })



          // if(!detail_row_datax.length) {
          //   alertify.warning('silahkan refresh browser')
          //   return
          // }




           dataLPB =  detail_row_data[0]
           // detailPembelianDate
          // document.getElementById("editPembelianDate").value = edit_pembelian_row_data[0].TANGGAL
           dataEditPembelianEdit = detail_row_data


          let date = new Date(detail_row_data[0].TANGGAL);
          var day = ("0" + date.getDate()).slice(-2);
          var month = ("0" + (date.getMonth() + 1)).slice(-2);
           var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;

           let table_row_detail = ""

      //    detail_row_data.forEach((detail_row, i) => {
               detail_row_data.forEach((detail_row, i) => {
            // let QntPO_format = detail_row.QntPO.toLocaleString();
             console.log(detail_row.KodeBrg)
            // console.log(detail_row.QntPO)
            // console.log(detail_row.Satuan)


          // table_row_detail += `<tr><td>${detail_row.KodeBrg}</td><td class="text-right">${parseFloat(detail_row.QNT).toFixed(2)}</td><td class="text-right">${parseFloat(detail_row.QntBeli).toFixed(2)}</td><td class="text-right">${parseFloat(detail_row.OSPO).toFixed(2)}</td><td>${detail_row.Satuan}</td><td>-</td><td>-</td></tr>`
          table_row_detail += `<tr><td>${detail_row.KodeBrg}</td><td>${detail_row.namabrgx}</td><td class="text-right">${detail_row.Qnt}</td><td class="text-right">${detail_row.QNTPO}</td><td>${detail_row.Satuan}</td><td class="text-right">${Number(detail_row.Harga) ? detail_row.Harga : "0.00"}</td><td class="text-right">${detail_row.DiscRp1}</td><td class="text-right">${detail_row.NNET}</td></tr>`
            // detail_row_data += `<tr><td>${detail_row.KodeBrg}</td><td>${detail_row.namabrgx}</td><td class="text-right">${detail_row.Qnt}</td><td class="text-right">${detail_row.QNTPO}</td><td>${detail_row.Satuan}</td><td class="text-right">${Number(detail_row.Harga) ? detail_row.Harga : "0.00"}</td><td class="text-right">${detail_row.DiscRp1}</td><td class="text-right">${detail_row.NNET}</td><td class="text-center"><button class="btn btn-success btn-sm" type="button" //////onclick="showPembelianEdit(${i})"><i class="bi bi-pen"></i></button><button style="" class="btn btn-danger btn-sm" type="button" onclick="submitPembelianDelete(${i})" ><i class="bi bi-trash"></i></button></td></tr>`


          });

          document.getElementById("detailModalLabel").innerHTML = "Detail " +  detail_row_data[0].NoBukti;
          document.getElementById("detailSupp").value = detail_row_data[0].NamaSupplier;

          document.getElementById("detailvalas").value = detail_row_data[0].KODEVLS;


          document.getElementById("detailNoPO").value = detail_row_data[0].NoPO
          document.getElementById("detailFakturSupp").value = detail_row_data[0].FAKTURSUPP
          document.getElementById("detailKeterangan").value = detail_row_data[0].KETERANGAN
          ///document.getElementById("detailTableData").innerHTML = table_row_detail



          document.getElementById("detailvalas").value = detail_row_data[0].KODEVLS
          document.getElementById("detailkurs").value = detail_row_data[0].KURS

          document.getElementById("detailtipeppn").value = detail_row_data[0].MyPPN
          document.getElementById("detailtipebayar").value = detail_row_data[0].MyTipeBayar

          document.getElementById("detailSoCustomer").value = detail_row_data[0].NOSO
          document.getElementById("detailNoUangMuka").value = detail_row_data[0].NOUMK

          document.getElementById("detailNuangmuka").value = detail_row_data[0].NuangMuka
          document.getElementById("detailNilaiPPN").value = detail_row_data[0].NilaiPPN

          document.getElementById("detailgudang").value = detail_row_data[0].NAMAGUDANG
          // document.getElementById("detailFakturSupp").value = detail_row_data[0].FAKTURSUPP

          document.getElementById("detailNoSopir").value = detail_row_data[0].SOPIR






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


      console.log(edit_pembelian_row_data[0].NoBukti , "<<<<<<<<<<")
      let _token = $("#_token").val();
      $.ajax({
        url: "{!! url('detailPO') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          NoPO: edit_pembelian_row_data[0].NoBukti,
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






    function buttonDetail (nobukti) {
      console.log(nobukti,  "<<< detail")
      console.log('================')
      console.log(nobukti)

      let _token = $("#_token").val();

      let detail_row_data = []

console.log(nobukti,"sebelum url")
      $.ajax({
     url: "{!! url('detailBeliDet') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          NoBukti: nobukti
        },
        success: function(res) {

            console.log('======xxxxx==========')
          console.log(res)

          detail_row_data = res[0]
          console.log(res[0])
          // console.log(res[0][0])
          // console.log(res[0][0].pQC)

        }
      })



      // if(!detail_row_datax.length) {
      //   alertify.warning('silahkan refresh browser')
      //   return
      // }




       dataLPB =  detail_row_data[0]
       // detailPembelianDate
      // document.getElementById("editPembelianDate").value = edit_pembelian_row_data[0].TANGGAL
       dataEditPembelianEdit = detail_row_data


      let date = new Date(detail_row_data[0].TANGGAL);
      var day = ("0" + date.getDate()).slice(-2);
      var month = ("0" + (date.getMonth() + 1)).slice(-2);
       var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;

       let table_row_detail = ""

  //    detail_row_data.forEach((detail_row, i) => {
           detail_row_data.forEach((detail_row, i) => {
        // let QntPO_format = detail_row.QntPO.toLocaleString();
         console.log(detail_row.KodeBrg)
        // console.log(detail_row.QntPO)
        // console.log(detail_row.Satuan)


      // table_row_detail += `<tr><td>${detail_row.KodeBrg}</td><td class="text-right">${parseFloat(detail_row.QNT).toFixed(2)}</td><td class="text-right">${parseFloat(detail_row.QntBeli).toFixed(2)}</td><td class="text-right">${parseFloat(detail_row.OSPO).toFixed(2)}</td><td>${detail_row.Satuan}</td><td>-</td><td>-</td></tr>`
      table_row_detail += `<tr><td>${detail_row.KodeBrg}</td><td>${detail_row.namabrgx}</td><td class="text-right">${detail_row.Qnt}</td><td class="text-right">${detail_row.QNTPO}</td><td>${detail_row.Satuan}</td><td class="text-right">${Number(detail_row.Harga) ? detail_row.Harga : "0.00"}</td><td class="text-right">${detail_row.DiscRp1}</td><td class="text-right">${detail_row.NNET}</td></tr>`
        // detail_row_data += `<tr><td>${detail_row.KodeBrg}</td><td>${detail_row.namabrgx}</td><td class="text-right">${detail_row.Qnt}</td><td class="text-right">${detail_row.QNTPO}</td><td>${detail_row.Satuan}</td><td class="text-right">${Number(detail_row.Harga) ? detail_row.Harga : "0.00"}</td><td class="text-right">${detail_row.DiscRp1}</td><td class="text-right">${detail_row.NNET}</td><td class="text-center"><button class="btn btn-success btn-sm" type="button" //////onclick="showPembelianEdit(${i})"><i class="bi bi-pen"></i></button><button style="" class="btn btn-danger btn-sm" type="button" onclick="submitPembelianDelete(${i})" ><i class="bi bi-trash"></i></button></td></tr>`


      });

      document.getElementById("detailModalLabel").innerHTML = "Detail " +  detail_row_data[0].NoBukti;
      document.getElementById("detailSupp").value = detail_row_data[0].NamaSupplier;

      document.getElementById("detailvalas").value = detail_row_data[0].KODEVLS;


      document.getElementById("detailNoPO").value = detail_row_data[0].NoPO
      document.getElementById("detailFakturSupp").value = detail_row_data[0].FAKTURSUPP
      document.getElementById("detailKeterangan").value = detail_row_data[0].KETERANGAN
      ///document.getElementById("detailTableData").innerHTML = table_row_detail



      document.getElementById("detailvalas").value = detail_row_data[0].KODEVLS
      document.getElementById("detailkurs").value = detail_row_data[0].KURS

      document.getElementById("detailtipeppn").value = detail_row_data[0].MyPPN
      document.getElementById("detailtipebayar").value = detail_row_data[0].MyTipeBayar

      document.getElementById("detailSoCustomer").value = detail_row_data[0].NOSO
      document.getElementById("detailNoUangMuka").value = detail_row_data[0].NOUMK

      document.getElementById("detailNuangmuka").value = detail_row_data[0].NuangMuka
      document.getElementById("detailNilaiPPN").value = detail_row_data[0].NilaiPPN

      document.getElementById("detailgudang").value = detail_row_data[0].NAMAGUDANG
      // document.getElementById("detailFakturSupp").value = detail_row_data[0].FAKTURSUPP

      document.getElementById("detailNoSopir").value = detail_row_data[0].SOPIR






      // document.getElementById("detailDate").value = date.getFullYear() + "-" + (date.getMonth()+1) + "-" + date.getDate()

      // let date1= date.getFullYear() + "-" + (date.getMonth()+1) + "-" + date.getDate()
     console.log(date1)
      $('#detailDate').val(date1)


      document.getElementById("detailTableData").innerHTML = table_row_detail


      $("#detailout").modal('toggle');
    }

    function buttonAdd1(index) {
      let tempDataAdd = dataRefreshPO[index]
      buttonAdd('x' , tempDataAdd)
      // console.log(tempDataAdd)

    }

    // function setNewNoBukti () {
    //   let _token = $('#_token').val()
    //   let kode ='BPL'
    //   $.ajax({
    //     url: "{!! url('spnobukti') !!}",
    //     type: "post",
    //     data:{_token,kode},
    //     async: false,
    //     success: function(res) {
    //       console.log(res , "RESPOND");
    //       console.log(res[0].Nobukti)
    //       document.getElementById("editnobukti").value = res[0].Nobukti
    //       document.getElementById("editnoUrut").value = res[0].Nourut
    //     }
    //   })
    // }

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

    function submitAdd () {
      console.log('submitAdd123')
        let tempData = []
        // console.log('tes')
        for (let i = 0; i < edit_pembelian_row_data.length; i++) {
          if (document.getElementById(`add_checkbox${i}`).checked) {
          edit_pembelian_row_data[i].inputQntTerima = $(`#input_add_qntTerima${i}`).val();
          tempData.push(edit_pembelian_row_data[i])
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
    
    function formatAngkaX (angka) {
      if (!Number(angka)) {
        return '0.00'
      } else {
        return formatAngka(parseFloat(angka).toFixed(2))

      }

    }


    function formatAngka (angkaString) {
      if (!Number(angkaString)) {
        return '0.00'
      }
      angkastring = parseFloat(angkaString).toFixed(2)

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


    function buttonDetailout (nobukti) {
        // let date = new Date(detail_row_data[0].TANGGAL);

        let _token = $("#_token").val();

        let detail_row_data = []

        $.ajax({
          url: "{!! url('detailBeliDet') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            NoBukti: nobukti
          },
          success: function(res) {
              console.log('======xxxxx==========')
            console.log(res)

            detail_row_data = res[0]
            console.log(res[0])
            // console.log(res[0][0])
            // console.log(res[0][0].pQC)

          }
        })



        // if(!detail_row_datax.length) {
        //   alertify.warning('silahkan refresh browser')
        //   return
        // }




        dataLPB =  detail_row_data[0]
        // detailPembelianDate
        // document.getElementById("editPembelianDate").value = edit_pembelian_row_data[0].TANGGAL
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
        <td class="text-right">${detail_row.NNET ? formatAngkaX(detail_row.NNET) : '0.00' }</td></tr>`


         
          


         });

        document.getElementById("detailModalLabelout").innerHTML = "Detail " +  detail_row_data[0].NoBukti;

        document.getElementById("detailPembelianNobuktiout").value = detail_row_data[0].NoBukti;
        document.getElementById("detailPembelianKodeSuppout").value = detail_row_data[0].KODESUPP;
        document.getElementById("detailPembelianAlamatSuppout").value = detail_row_data[0].ALAMAT1;
        document.getElementById("detailPembelianhariout").value = detail_row_data[0].HARI;

        document.getElementById("detailPembelianSuppout").value = detail_row_data[0].NamaSupplier;

        document.getElementById("detailPembelianvalasout").value = detail_row_data[0].KODEVLS;


        document.getElementById("detailNoPOout").value = detail_row_data[0].NoPO
        document.getElementById("detailFakturSuppout").value = detail_row_data[0].FAKTURSUPP
       
        document.getElementById("detailPembeliankursout").value = detail_row_data[0].KURS

        document.getElementById("detailPembeliantipeppnout").value = detail_row_data[0].PPN
        document.getElementById("detailPembeliantipebayarout").value = detail_row_data[0].TIPEBAYAR

        document.getElementById("detailSoCustomerout").value = detail_row_data[0].NOSO
        console.log('mmm')
        console.log(detail_row_data[0].NOUMK)
        document.getElementById("detailNoUangMukaout").value = detail_row_data[0].NOUMK

        document.getElementById("detailNuangmukaout").value = detail_row_data[0].NuangMuka
       

        document.getElementById("detailgudangout").value = detail_row_data[0].NAMAGUDANG
        document.getElementById("detailFakturSuppout").value = detail_row_data[0].FAKTURSUPP

        document.getElementById("detailNoSopirout").value = detail_row_data[0].SOPIR

        document.getElementById("input_det_discout").value = detail_row_data[0].disc
        document.getElementById("input_det_discrpout").value = detail_row_data[0].DISCRP
        document.getElementById("input_det_dppout").value = detail_row_data[0].TotDPP
        document.getElementById("input_det_ppnout").value = detail_row_data[0].TotPPN
        document.getElementById("input_det_grandtotalout").value = detail_row_data[0].TotNet
      console.log(date1)
        $('#detailDateout').val(date1)
        // $("#btnotokiri").hide();

        document.getElementById("detailTableDataout").innerHTML = table_row_detail


        $("#detailout").modal('toggle');
      }

      function buttonAdd1(index) {
        let tempDataAdd = dataRefreshPO[index]
        buttonAdd('x' , tempDataAdd)
        // console.log(tempDataAdd)

      }

      function setNewNoBukti () {
        $.ajax({
          url: "{!! url('getNoBuktiInvoiceBeli') !!}",
          type: "get",
          async: false,
          success: function(res) {
            console.log(res , "RESPONDxg");
            console.log(res[0].Nobukti)
            document.getElementById("editnobukti").value = res[0].Nobukti
            document.getElementById("editnoUrut").value = res[0].Nourut
          }
        })
      }





  </script>

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
  </script>

@endsection
