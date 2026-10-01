@extends('newmaster')
@section('buttons')

@endsection
@section('content')
<div class="container-fluid">

  <!-- <div id="qrcode"></div> -->
  <div class="row">
    <div class="col-6 text-left">
      <h1>Purchase Order ( Stock )</h1>
    </div>
    <div class="col-6 text-right">
      <button type="button" class="btn btn-primary btn-lg " style="height: 60px; " onclick="buttonAdd()"  >Add PO ( Stock )</button>
    </div>
  </div>
<button onclick="loadAll()">tes</button>
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
          <div class="row mt-4">
              <!-- <div class="col-12 text-right">
                  <button type="button" class="btn btn-primary btn-lg " style="height: 60px; " onclick="buttonAdd()"  >Add Koreksi Stock Gudang</button>
              </div> -->
          </div>
          <div class="row mt-3">
            <div class="col-12" style="overflow:auto;">
              <div class="">

                    <table id="tabel" class="table table-bordered table-striped"  >
                      <thead class="text-center">
                        <tr>
                          <th scope="col">No Bukti</th>
                          <th scope="col">Tanggal</th>
                          <th scope="col">Authorized</th>
                          <th scope="col">Actions</th>

                        </tr>
                      </thead>


                      <tbody id="tabel_data" class="text-left" >
                        @for ($i = 0; $i < count($listData); $i++)
                        <tr >

                          <td>{{ $listData[$i][0]->NoBukti }}</td>
                          <td>{!! date("Y/m/d", strtotime($listData[$i][0]->Tanggal)) !!}</td>
                          @if ($listData[$i][0]->IsOtorisasi1 == 0)
                            <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>
                          @elseif ($listData[$i][0]->IsOtorisasi1 == 1)
                            <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>
                          @endif


                            <td class="text-center">
                              <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                              <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('{{ $listData[$i][0]->NoBukti }}')"><i class="bi bi-info"></i></button>
                              <button class="btn btn-success btn-sm" type="button" onclick="buttonEdit('{{ $listData[$i][0]->NoBukti }}')"><i class="bi bi-pen"></i></button>
                              <button data-toggle="tooltip" data-placement="top" title="Otorisasi" class="btn btn-info btn-sm" type="button" onclick="buttonOtorisasi('{{ $listData[$i][0]->NoBukti }}' , '{{ $listData[$i][0]->IsOtorisasi1 }}' )"><i class="bi bi-key"></i></button>
                              <button data-toggle="tooltip" data-placement="top" title="Batal Otorisasi" class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('{{ $listData[$i][0]->NoBukti }}' , '{{ $listData[$i][0]->IsOtorisasi1 }}' )"><i class="bi bi-key"></i></button>
                              <!-- <button class="btn btn-danger btn-sm" type="button" onclick="buttonDelete('{{ $listData[$i][0]->NoBukti }}')"><i class="bi bi-trash"></i></button> -->
                            </td>
                      </tr>
                      @endfor
                      </tbody>


                    </table>
              </div>
            </div>
          </div>


</div>


<!-- start modal add -->
<div class="modal fade"  id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" style="max-width: 1800px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <div class="container-fluid">
          <div class="row">

            <!-- <input type="hidden" id="input_kodegroup" value="" /> -->
            <div class="col-2">
              <div class="form-group">
                <label>Supplier</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_supplier" placeholder="Supplier" disabled>
              </div>
            </div>
            <!-- <div class="col-2">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-2">
              <div class="form-group">
                <input type="text" class="form-control" id="input_kodehdgroup" placeholder="No Bukti" disabled>
              </div>
            </div> -->
            <div class="col-0">
            </div>
            <div class="col-4">
              <div class="form-group">
                <textarea style:"resize:none;" class="form-group"id="dataSupplier" rows="4" cols="50"></textarea>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-2">
              <div class="form-group">
                <label>No. Urut</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_noUrut" placeholder="No Bukti" disabled>
              </div>
            </div>
            <div class="col-1">
              <div class="form-group">
                <label>No. Bukti</label>
              </div>
            </div>
            <div class="col-4">
              <input type="text" class="form-control" id="input_add_noUrut" placeholder="No Bukti" disabled>
            </div>
          </div>
          <div class="row">
            <div class="col-2 text-left">
              <div class="form-group text-left">
                <label class="text-left">Tanggal: </label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="date" class="form-control" id="input_add_tanggal">
              </div>
            </div>
          </div>
          <hr style="border: 1px solid #512;">
          <div class="row">
            <div class="col-2 text-left">
              <div class="form-group text-left">
                <label class="text-left">Dikirim Ke: </label>
              </div>
            </div>
            <div class="col-2">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_kirimKe">
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_kirimKe" disabled>
              </div>
            </div>
            <div class="col-0 text-left">
              <div class="form-group text-left">
                <label class="text-left" onClick="buttonAddValas()">Valas: </label>
              </div>
            </div>
            <div class="col-1">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_valas">
              </div>
            </div>
            <div class="col-0 text-left">
              <div class="form-group text-left">
                <label class="text-left" >Kurs: </label>
              </div>
            </div>
            <div class="col-1">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_kurs">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-2 text-left">
              <div class="form-group text-left">
                <label class="text-left">Alamat: </label>
              </div>
            </div>
            <div class="col-6">
              <div class="form-group">
                  <textarea style:"resize:none;" class="form-group"id="dataSupplier" rows="4" cols="50"></textarea>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-1 text-left">
              <div class="form-group text-left">
                <label class="text-left">Tipe PPN: </label>
              </div>
            </div>
            <div class="col-1">
              <div class="form-select">
                  <select class="form-select" id="input_add_tipePPN">
                      <option value='1'>tes</option>
                  </select>
              </div>
            </div>
            <div class="col-1 text-left">
              <div class="form-group text-left">
                <label class="text-left">Pembayaran: </label>
              </div>
            </div>
            <div class="col-2">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_pembayaran">
              </div>
            </div>
            <div class="col-0 text-left">
              <div class="form-group text-left">
                <label class="text-left">Hari: </label>
              </div>
            </div>
            <div class="col-2">
              <div class="form-select">
                <input type="text" class="form-control" id="input_add_hari">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-2 text-left">
              <div class="form-group text-left">
                <label class="text-left">Ekspedisi: </label>
              </div>
            </div>
            <div class="col-2">
              <div class="form-select">
                <input type="text" class="form-control" id="input_add_ekspedisi">
              </div>
            </div>
            <div class="col-4">
              <div class="form-select">
                <input type="text" class="form-control" id="namaEkspedisi" disabled>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-2 text-left">
              <div class="form-group text-left">
                <label class="text-left">Keterangan: </label>
              </div>
            </div>
            <div class="col-6">
              <div class="form-select">
                <input type="text" class="form-control" id="input_add_keteranganEkspedisi">
              </div>
            </div>
          </div>

          <hr style="border: 1px solid #512;">

          <div class="row ">
            <div class="col-md-12 text-right">
            <button type="button" class="btn btn-primary" onclick="buttonAddAddItem()" class="btn btn-secondary"  >Add Barang</button>
            <button type="button" class="btn btn-primary" onclick="buttonAddAddItem()" class="btn btn-secondary"  >Add Biaya</button>
        </div>
      </div>
    </div>

    <!-- ADD SUBGROUP -->

    <div id="addAddItem" class="container-fluid showhide">
            <!-- <div class="line"></div> -->
            <div class="row">
              <div class="col-4">
                <h4>Add Barang</h4>
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Tipe</label>
              </div>
              </div>
              <div class="col-3">
                <input id="input_add_add_Tipe" type="text" class="form-control" disabled>
              </div>
              <div class="col-1 text-right">

              </div>

            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>No. SO</label>
              </div>
              </div>
              <div class="col-3">
                <input id="input_add_add_noSo" type="text" class="form-control">
              </div>
              <div class="col-2">
                <div class="form-group">
                <label>No. PO Cust</label>
              </div>
              </div>
              <div class="col-3">
                <input id="input_add_add_noPoCust" type="text" class="form-control">
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Kode Barang</label>
              </div>
              </div>
              <div class="col-4">
                <input id="input_add_add_kodeBarang" type="text" class="form-control" disabled>
              </div>
              <div class="col-4">
                <button class="btn btn-primary btn-lg" onClick="kodeBarangSearch()">Select</button>
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Ket. Nama</label>
              </div>
              </div>
              <div class="col-8">
                <input id="input_add_add_ketNama" type="text" class="form-control">
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Quantity</label>
              </div>
              </div>
              <div class="col-4">
                <input id="input_add_add_qnt" type="number" value=0.00 class="form-control text-right">
              </div>
              <div class="col-2">
                <label for="">Satuan</label>
              </div>
              <div id="input_add_add_satuan" class="col-4">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1" disabled>
                  <label class="form-check-label" for="inlineRadio1">-</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2" disabled>
                  <label class="form-check-label" for="inlineRadio2">-</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="option3" disabled>
                  <label class="form-check-label" for="inlineRadio3">-</label>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Harga</label>
              </div>
              </div>
              <div class="col-8">
                <input id="input_add_add_harga" type="text" class="form-control" value="0.00">
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Disc(%)</label>
              </div>
              </div>
              <div class="col-2">
                <input id="input_add_add_diskon1" type="text" class="form-control">
              </div> -
              <div class="col-2">
                <input id="input_add_add_diskon2" type="text" class="form-control">
              </div> -
              <div class="col-2">
                <input id="input_add_add_diskon3" type="text" class="form-control">
              </div>
              <div class="col-1">
                <div class="form-group">
                <label>Disc Rp</label>
              </div>
              </div>
              <div class="col-2">
                <input id="input_add_add_totalDiskon" type="text" class="form-control">
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Keterangan</label>
              </div>
              </div>
              <div class="col-8">
                <input id="input_add_add_keterangan" type="text" class="form-control">
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="closeShowHideAdd()" >Batal</button>
                <button type="button" onclick="submitAddAdd()" class="btn btn-primary" >Add</button>
              </div>

            </div>
          </div>

    <!-- END ADD SUBGROUP -->

    <!-- EDIT SUBGROUP -->

    <div id="addEditItem" class="container-fluid showhide">
            <!-- <div class="line"></div> -->
            <div class="row">
              <div class="col-4">
                <h4>Edit Item</h4>
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Ref SO</label>
              </div>
              </div>
              <div class="col-3">
                <input id="input_add_edit_refso" type="text" class="form-control" value="-" disabled>
              </div>
              <div class="col-1 text-right">

                <button type="button" disabled onclick="" disabled class="btn btn-primary" >+</button>
              </div>
              <div class="col-2">
                <div class="form-group">
                <label>No PO Cust</label>
              </div>
              </div>
              <div class="col-4">

                <input id="input_add_edit_nopocust" type="text" class="form-control" disabled>
              </div>

            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Kode Barang</label>
              </div>
              </div>
              <div class="col-3">
                <input id="input_add_edit_kodebarang" type="text" class="form-control" disabled>
              </div>
              <div class="col-1 text-right">

                <button type="button" disabled onclick="" class="btn btn-primary" >+</button>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Ket. Barang</label>
              </div>
              </div>
              <div class="col-4">
                <input id="input_add_edit_keterangannama" type="text" class="form-control" disabled>
              </div>

            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Quantity</label>
              </div>
              </div>
              <div class="col-4">
                <input id="input_add_edit_qnt" type="number" value=0.00 class="form-control text-right">
              </div>
              <div class="col-2">
                <label for="">Satuan</label>
              </div>
              <div id="input_add_edit_satuan" class="col-4">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1" disabled>
                  <label class="form-check-label" for="inlineRadio1">-</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2" disabled>
                  <label class="form-check-label" for="inlineRadio2">-</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="option3" disabled>
                  <label class="form-check-label" for="inlineRadio3">-</label>
                </div>
              </div>

            </div>
            <div class="row">


            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Keterangan</label>
              </div>
              </div>
              <div class="col-10">
                <input id="input_add_edit_keterangan" type="text" class="form-control">
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="closeShowHideAdd()" >Batal</button>
                <button type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button>
              </div>

            </div>
          </div>

    <!-- END EDIT SUBGROUP -->




        <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_add" class="table table-bordered table-striped"  >
              <thead class="text-center">
                <tr>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Qty</th>

                  <th scope="col">Sat</th>
                  <th scope="col">Harga</th>
                  <th scope="col">Diskon</th>
                  <th scope="col">Sub. Total</th>
                  <th scope="col">No. PR</th>
                  <th scope="col">Ket. Det</th>
                  <th scope="col">Actions</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add" class="text-left" >

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
  </div>
  <div class="modal-footer">
    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
    <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button>
  </div>
</div>
</div>
</div>
<!-- End modal add-->

<!-- start modal list item add -->
<div class="modal fade"  id="formAddListItem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add List Item</h5>
        <button type="button" class="close" onclick="closeListItemAdd()" >
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->







        <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_add_list_item" class="table table-bordered table-striped"  >
              <thead class="text-center">
                <tr>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Merk</th>

                  <th scope="col">Part Number</th>
                  <th scope="col">Actions</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_item" class="text-left" >

                <tr >

                  <td>-</td>
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
          </div>
            <!-- <button onclick="buttonSubKategori()">tes</button> -->


    </div>
  </div>
  <!-- <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
    <button type="button" class="btn btn-primary" onclick="">Submit</button>
  </div> -->
</div>
</div>
</div>
<!-- End modal list item add-->


<!-- start modal detail -->
<div class="modal fade"  id="formDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detail</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <div class="container-fluid">
          <div class="row">
            <div class="col-2">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_detail_nobukti" placeholder="No Bukti" disabled>
              </div>
            </div>

            <div class="col-2">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="date" class="form-control text-center" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>

          </div>
          <div class="row">
            <div class="col-2">
              <div class="form-group">
                <label>Departemen</label>
              </div>
            </div>
            <div class="col-4">
              <select disabled id="input_detail_kodedepartemen" class="form-control" aria-label="Default select example">
                  <!-- <option selected value="" disabled>Pilih Dept</option> -->
              </select>
            </div>
          </div>
          <!-- <div class="row ">
            <div class="col-md-12 text-right">
            <button type="button" class="btn btn-primary" onclick="buttonAddAddItem()" class="btn btn-secondary"  >Add Item</button>
        </div>
      </div> -->
    </div>




        <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_detail_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_detail" class="table table-bordered table-striped"  >
              <thead class="text-center">
                <tr>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Qty</th>

                  <th scope="col">Sat</th>

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
  </div>
  <div class="modal-footer">
    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
    <!-- <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button> -->
  </div>
</div>
</div>
</div>
<!-- End modal detail-->

<!-- start modal edit -->
<div class="modal fade"  id="formEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <div class="container-fluid">
          <div class="row">
            <!-- <div class="col-2">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_edit_nobukti" placeholder="No Bukti" disabled>
              </div>
            </div> -->

            <!-- <input type="hidden" id="input_kodegroup" value="" /> -->
            <div class="col-2">
              <div class="form-group">
                <label>No Urut</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_edit_nourut" placeholder="No Urut" disabled>
              </div>
            </div>

            <!-- <div class="col-2">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-2">
              <div class="form-group">
                <input type="text" class="form-control" id="input_kodehdgroup" placeholder="No Bukti" disabled>
              </div>
            </div> -->
            <div class="col-2">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="date" class="form-control text-center" id="input_edit_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>

          </div>
          <div class="row">
            <div class="col-2">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_edit_nobukti" placeholder="No Bukti" disabled>
              </div>
            </div>
            <div class="col-2">
              <div class="form-group">
                <label>Kode Departemen</label>
              </div>
            </div>
            <div class="col-4">
              <select disabled id="input_edit_kodedepartemen" class="form-control" aria-label="Default select example">
                  <!-- <option selected value="" disabled>Pilih Dept</option> -->
              </select>
            </div>
          </div>
          <div class="row ">
            <div class="col-md-12 text-right">
            <button type="button" class="btn btn-primary" onclick="buttonEditAddItem()" class="btn btn-secondary"  >Add Item</button>
        </div>
      </div>
    </div>

    <!-- ADD SUBGROUP -->

    <div id="editAddItem" class="container-fluid showhideedit">
            <!-- <div class="line"></div> -->
            <div class="row">
              <div class="col-4">
                <h4>Add Item</h4>
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Ref SO</label>
              </div>
              </div>
              <div class="col-3">
                <input id="input_edit_add_refso" type="text" class="form-control" value="-" disabled>
              </div>
              <div class="col-1 text-right">

                <button type="button" disabled onclick="" disabled class="btn btn-primary" >+</button>
              </div>
              <div class="col-2">
                <div class="form-group">
                <label>No PO Cust</label>
              </div>
              </div>
              <div class="col-4">

                <input id="input_edit_add_nopocust" type="text" class="form-control" disabled>
              </div>

            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Kode Barang</label>
              </div>
              </div>
              <div class="col-3">
                <input id="input_edit_add_kodebarang" type="text" class="form-control" disabled>
              </div>
              <div class="col-1 text-right">

                <button type="button" onclick="buttonEditListKodeBarang()" class="btn btn-primary" >+</button>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Ket. Barang</label>
              </div>
              </div>
              <div class="col-4">
                <input id="input_edit_add_keterangannama" type="text" class="form-control" disabled>
              </div>

            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Quantity</label>
              </div>
              </div>
              <div class="col-4">
                <input id="input_edit_add_qnt" type="number" value=0.00 class="form-control text-right">
              </div>
              <div class="col-2">
                <label for="">Satuan</label>
              </div>
              <div id="input_edit_add_satuan" class="col-4">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioEditAdd1" value="option1" disabled>
                  <label class="form-check-label" for="inlineRadio1">-</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioEditAdd2" value="option2" disabled>
                  <label class="form-check-label" for="inlineRadio2">-</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioEditAdd3" value="option3" disabled>
                  <label class="form-check-label" for="inlineRadio3">-</label>
                </div>
              </div>

            </div>
            <div class="row">


            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Keterangan</label>
              </div>
              </div>
              <div class="col-10">
                <input id="input_edit_add_keterangan" type="text" class="form-control">
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="closeShowHideEdit()" >Batal</button>
                <button type="button" onclick="submitEditAdd()" class="btn btn-primary" >Add</button>
              </div>

            </div>
          </div>

    <!-- END ADD SUBGROUP -->


    <!-- ADD SUBGROUP -->

    <div id="editEditItem" class="container-fluid showhideedit">
            <!-- <div class="line"></div> -->
            <div class="row">
              <div class="col-4">
                <h4>Edit Item</h4>
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Ref SO</label>
              </div>
              </div>
              <div class="col-3">
                <input id="input_edit_edit_refso" type="text" class="form-control" value="-" disabled>
              </div>
              <div class="col-1 text-right">

                <button type="button" disabled onclick="" disabled class="btn btn-primary" >+</button>
              </div>
              <div class="col-2">
                <div class="form-group">
                <label>No PO Cust</label>
              </div>
              </div>
              <div class="col-4">

                <input id="input_edit_edit_nopocust" type="text" class="form-control" disabled>
              </div>

            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Kode Barang</label>
              </div>
              </div>
              <div class="col-3">
                <input id="input_edit_edit_kodebarang" type="text" class="form-control" disabled>
              </div>
              <div class="col-1 text-right">

                <button disabled type="button" class="btn btn-primary" >+</button>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Ket. Barang</label>
              </div>
              </div>
              <div class="col-4">
                <input id="input_edit_edit_keterangannama" type="text" class="form-control" disabled>
              </div>

            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Quantity</label>
              </div>
              </div>
              <div class="col-4">
                <input id="input_edit_edit_qnt" type="number" value=0.00 class="form-control text-right">
              </div>
              <div class="col-2">
                <label for="">Satuan</label>
              </div>
              <div id="input_edit_edit_satuan" class="col-4">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioEditEdit1" value="option1" disabled>
                  <label class="form-check-label" for="inlineRadio1">-</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioEditEdit2" value="option2" disabled>
                  <label class="form-check-label" for="inlineRadio2">-</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioEditEdit3" value="option3" disabled>
                  <label class="form-check-label" for="inlineRadio3">-</label>
                </div>
              </div>

            </div>
            <div class="row">


            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Keterangan</label>
              </div>
              </div>
              <div class="col-10">
                <input id="input_edit_edit_keterangan" type="text" class="form-control">
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="closeShowHideEdit()" >Batal</button>
                <button type="button" onclick="submitEditEdit()" class="btn btn-primary" >Edit</button>
              </div>

            </div>
          </div>

    <!-- END ADD SUBGROUP -->




        <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_detail_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_edit" class="table table-bordered table-striped"  >
              <thead class="text-center">
                <tr>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Qty</th>

                  <th scope="col">Sat</th>
                  <th scope="col">Actions</th>

                </tr>
              </thead>


              <tbody id="tabel_data_edit" class="text-left" >

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
  </div>
  <div class="modal-footer">
    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
    <!-- <button type="button" class="btn btn-primary" onclick="submitEdit()">Submit</button> -->
  </div>
</div>
</div>
</div>
<!-- End modal edit-->


<!-- start modal list item edit -->
<div class="modal fade"  id="formEditListItem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit List Item</h5>
        <button type="button" class="close" onclick="closeListItemEdit()" >
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->







        <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_edit_list_item" class="table table-bordered table-striped"  >
              <thead class="text-center">
                <tr>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Merk</th>

                  <th scope="col">Part Number</th>
                  <th scope="col">Actions</th>

                </tr>
              </thead>


              <tbody id="tabel_data_edit_list_item" class="text-left" >

                <tr >

                  <td>-</td>
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
          </div>
            <!-- <button onclick="buttonSubKategori()">tes</button> -->


    </div>
  </div>
  <!-- <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
    <button type="button" class="btn btn-primary" onclick="">Submit</button>
  </div> -->
</div>
</div>
</div>
<!-- End modal list item edit-->




@endsection

@section('js')
<script type="text/javascript">

let dataRefresh = []

let dataTableAdd = []
let dataTableEdit = []

let dataAddListItem = []

let dataEditListItem = []

let tempAdd = {}
let tempEdit = {}
let tempIndexEdit = 0
let tempEditAdd = {}
let tempEditEdit = {}

$(document).ready(function(){
      $("#tabel").DataTable({
        "lengthChange": false,
          "paging": false ,
        //    "columnDefs": [
        // { "type": "date", "targets": [1] },
        // {  "className": "text-center", "targets": [3] },
      // ]
    });


  //   $("#tabel_add_list_item").DataTable({
  //     "lengthChange": false,
  //       "paging": false ,
  // });
    // formAddListItem
});

function loadAll () {

  console.log('asd')
  let _token = $("#_token").val();


  $('#tabel').DataTable().destroy();

  $.ajax({
    url: "{!! url('pembelianpermintaanagenloadall') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      console.log(res)
      dataRefresh = res.listData


    }})
    console.log(dataRefresh)
    let rowTable = ""
    dataRefresh.forEach((item, i) => {
      let temp = ""
      if (Number(item[0].IsOtorisasi1) == 0) {
        temp = '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>'
      } else {
        temp = '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>'
      }
      let date1 = ""
      if (item[0].Tanggal) {
          let date = new Date(item[0].Tanggal);
          let day = ("0" + date.getDate()).slice(-2);
          let month = ("0" + (date.getMonth() + 1)).slice(-2);
          date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
        }

      rowTable += `
      <tr>
      <td>${item[0].NoBukti}</td>
      <td>${date1}</td>
      ${temp}
      <td class="text-center">

        <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('${item[0].NoBukti}')"><i class="bi bi-info"></i></button>
        <button class="btn btn-success btn-sm" type="button" onclick="buttonEdit('${item[0].NoBukti}')"><i class="bi bi-pen"></i></button>
        <button data-toggle="tooltip" data-placement="top" title="Otorisasi" class="btn btn-info btn-sm" type="button" onclick="buttonOtorisasi('${item[0].NoBukti}' , '${item[0].IsOtorisasi1}' )"><i class="bi bi-key"></i></button>
        <button data-toggle="tooltip" data-placement="top" title="Batal Otorisasi" class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('${item[0].NoBukti}' , '${item[0].IsOtorisasi1}' )"><i class="bi bi-key"></i></button>
      </td>

      </tr>
      `



    });



    document.getElementById("tabel_data").innerHTML = rowTable
    $("#tabel").DataTable({
      "lengthChange": false,
        "paging": false ,
      });

}

function buttonOtorisasi ( nobukti , isOtorisasi ) {

  console.log(nobukti , isOtorisasi )

  let akses = $("#akses_isotorisasi1").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  if(isOtorisasi > 0) {
    alertify.warning('Sudah diotorisasi')
    return
  }

  let _token = $("#_token").val();

  // pembelianpermintaannonagenupdateotorisasi
  $.ajax({
    url: "{!! url('pembelianpermintaannonagenupdateotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti,
      otorisasi :1
    },
    success: function(res) {
      if (res > 0) {
        loadAll()
        alertify.success('Berhasil update otorisasi')
      }


  }})
}

function buttonBatalOtorisasi ( nobukti , isOtorisasi ) {

  console.log(nobukti , isOtorisasi )

  let akses = $("#akses_isbatal").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  if(isOtorisasi == 0) {
    alertify.warning('Belum diotorisasi')
    return
  }

  let _token = $("#_token").val();

  // pembelianpermintaannonagenupdateotorisasi
  $.ajax({
    url: "{!! url('pembelianpermintaannonagenupdateotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti,
      otorisasi :0
    },
    success: function(res) {
      if (res > 0) {
        loadAll()
        alertify.success('Berhasil batal otorisasi')
      }


  }})
}


function refreshDataTableEdit (nobukti) {
  $.ajax({
    url: "{!! url('pembelianpermintaannonagenspdetail') !!}",
    type: "get",
    async: false,
    data: {
      nobukti
    },
    success: function(res) {
      console.log(res)
      dataTableEdit = res


  }})


  if (!dataTableEdit.length) {
    $("#formEdit").modal('toggle')

    return
  }

  let rowTable = ``
  dataTableEdit.forEach((item, i) => {
    rowTable += `<tr >
    <td>${item.KodeBrg}</td>
    <td>${item.NamaBrg}</td>

    <td>${item.Qnt}</td>
    <td>${item.Satuan}</td>
    <td class="text-center">
    <button class="btn btn-success btn-sm" type="button" onclick="buttonEditEditItem('${i}')"><i class="bi bi-pen"></i></button>
    <button class="btn btn-danger btn-sm" type="button" onclick="buttonEditDeleteItem('${i}')"><i class="bi bi-trash"></i></button>
    </td>
    </tr>`
  });
  // <td class="d-flex justify-content-center"><input id="input_edit_qnt${i}" style="width:100px" type="number" value="${item.Qnt}" class="form-control text-right"></td>
  document.getElementById("tabel_data_edit").innerHTML  = rowTable
}


function submitAddEdit () {
  console.log('submitAddEdit')
  // console.log()
  console.log(tempEdit)
  console.log(tempIndexEdit)
  console.log(dataTableAdd[tempIndexEdit])

  let qnt = $("#input_add_edit_qnt").val();
  let keterangan = $("#input_add_edit_keterangan").val();
  let satuan = 0
  let isi = 0
  let nosat = 0

  if (document.getElementById("inlineRadioAddEdit1").checked) {
    satuan = tempEdit.SAT1
    isi = tempEdit.ISI1
    nosat = 1
  }
  if (document.getElementById("inlineRadioAddEdit2").checked) {
    satuan = tempEdit.SAT2
    isi = tempEdit.ISI2
    nosat = 2
  }
  if (document.getElementById("inlineRadioAddEdit3").checked) {
    satuan = tempEdit.SAT3
    isi = tempEdit.ISI3
    nosat =3
  }

  dataTableAdd[tempIndexEdit].nosat = nosat
  dataTableAdd[tempIndexEdit].satuan = satuan
  dataTableAdd[tempIndexEdit].isi = isi
  dataTableAdd[tempIndexEdit].keterangan = keterangan
  dataTableAdd[tempIndexEdit].qnt = qnt
  refreshDataTableAdd()
  $('.showhide').hide();
  alertify.success("Item sudah di edit");



}

function buttonAddEditItem (index) {
  console.log(dataTableAdd[index])
  tempEdit = dataTableAdd[index]
  tempIndexEdit = index
  $('.showhide').hide();
  $('#addEditItem').show();

  document.getElementById("input_add_edit_keterangan").value  = tempEdit.keterangan
  document.getElementById("input_add_edit_kodebarang").value  = tempEdit.kodebarang
  document.getElementById("input_add_edit_refso").value  = tempEdit.refso
  document.getElementById("input_add_edit_keterangannama").value  = tempEdit.keterangannama
  document.getElementById("input_add_edit_qnt").value  = tempEdit.qnt
  document.getElementById("input_add_edit_nopocust").value  = tempEdit.nopocust

  let radioButton = ``
  if (tempEdit.SAT1) {
    radioButton += ` <div class="form-check form-check-inline">
       <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioAddEdit1" value="option1" >
      <label class="form-check-label" for="inlineRadio1">[1] ${tempEdit.SAT1}</label>
     </div>`
  } else {
    radioButton += `<div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioAddEdit1" value="option1" disabled>
      <label class="form-check-label" for="inlineRadio1">-</label>
    </div>`
  }
  if (tempEdit.SAT2) {
    radioButton += ` <div class="form-check form-check-inline">
       <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioAddEdit2" value="option2" >
      <label class="form-check-label" for="inlineRadio2">[2] ${tempEdit.SAT2}</label>
     </div>`
  } else {
    radioButton += `<div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioAddEdit2" value="option2" disabled>
      <label class="form-check-label" for="inlineRadio2">-</label>
    </div>`
  }
  if (tempEdit.SAT3) {
    radioButton += ` <div class="form-check form-check-inline">
       <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioAddEdit3" value="option1" >
      <label class="form-check-label" for="inlineRadio3">[3] ${tempEdit.SAT3}</label>
     </div>`
  } else {
    radioButton += `<div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioAddEdit3" value="option1" disabled>
      <label class="form-check-label" for="inlineRadio3">-</label>
    </div>`
  }
  console.log(tempEdit.nosat)
  document.getElementById(`input_add_edit_satuan`).innerHTML = radioButton
  document.getElementById(`inlineRadioAddEdit${tempEdit.nosat}`).checked = true

}

function buttonEditEditItem (index) {
  // console.log(dataTableEdit[index])
  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  console.log(index)
  $('.showhideedit').hide();

  tempEditEdit = dataTableEdit[index]
  console.log(tempEditEdit)

  document.getElementById("input_edit_edit_kodebarang").value  = tempEditEdit.KodeBrg
  document.getElementById("input_edit_edit_refso").value  = tempEditEdit.NOSO
  document.getElementById("input_edit_edit_nopocust").value  = tempEditEdit.NoSOCust
  document.getElementById("input_edit_edit_keterangan").value  = tempEditEdit.Keterangan
  document.getElementById("input_edit_edit_qnt").value  = tempEditEdit.Qnt
  document.getElementById("input_edit_edit_keterangannama").value  = tempEditEdit.NamaBrg

  let radioButton = ``

  if (tempEditEdit.SAT1) {
    radioButton += ` <div class="form-check form-check-inline">
       <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioEditEdit1" value="option1" >
      <label class="form-check-label" for="inlineRadio1">[1] ${tempEditEdit.SAT1}</label>
     </div>`
  } else {
    radioButton += `<div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioEditEdit1" value="option1" disabled>
      <label class="form-check-label" for="inlineRadio1">-</label>
    </div>`
  }
  if (tempEditEdit.SAT2) {
    radioButton += ` <div class="form-check form-check-inline">
       <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioEditEdit2" value="option2" >
      <label class="form-check-label" for="inlineRadio2">[2] ${tempEditEdit.SAT2}</label>
     </div>`
  } else {
    radioButton += `<div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioEditEdit2" value="option2" disabled>
      <label class="form-check-label" for="inlineRadio2">-</label>
    </div>`
  }
  if (tempEditEdit.SAT3) {
    radioButton += ` <div class="form-check form-check-inline">
       <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioEditEdit3" value="option1" >
      <label class="form-check-label" for="inlineRadio3">[3] ${tempEditEdit.SAT3}</label>
     </div>`
  } else {
    radioButton += `<div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioEditEdit3" value="option1" disabled>
      <label class="form-check-label" for="inlineRadio3">-</label>
    </div>`
  }
  document.getElementById(`input_edit_edit_satuan`).innerHTML = radioButton
  document.getElementById(`inlineRadioEditEdit${tempEditEdit.NoSat}`).checked = true



  $('#editEditItem').show();
}

function buttonEdit (nobukti) {
  console.log('buttonEdit')

  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  console.log(nobukti)

  $('.showhideedit').hide();

  $.ajax({
    url: "{!! url('pembelianpermintaannonagenlistdepartemen') !!}",
    type: "get",
    async: false,
    data: {
      // isagen: 0
    },
    success: function(res) {
      console.log('dept' , res)
      let selectDept = ``
      res.forEach((item, i) => {
        selectDept += `<option value="${item.KDDEP}">${item.KDDEP} - ${item.NMDEP}</option>`
      });

      document.getElementById("input_edit_kodedepartemen").innerHTML = selectDept

    }})

  $.ajax({
    url: "{!! url('pembelianpermintaannonagenspdetail') !!}",
    type: "get",
    async: false,
    data: {
      nobukti
    },
    success: function(res) {
      console.log(res)
      dataTableEdit = res


  }})
  let rowTable = ``
  dataTableEdit.forEach((item, i) => {
    rowTable += `<tr >
    <td>${item.KodeBrg}</td>
    <td>${item.NamaBrg}</td>
    <td>${item.Qnt}</td>
    <td>${item.Satuan}</td>
    <td class="text-center">
    <button class="btn btn-success btn-sm" type="button" onclick="buttonEditEditItem('${i}')"><i class="bi bi-pen"></i></button>
    <button class="btn btn-danger btn-sm" type="button" onclick="buttonEditDeleteItem('${i}')"><i class="bi bi-trash"></i></button></td>
    </tr>`
  });

  // <td class="d-flex justify-content-center"><input id="input_edit_qnt${i}" style="width:100px" type="number" value="${item.Qnt}" class="form-control text-right"></td>


  let date = new Date(dataTableEdit[0].Tanggal);
  let day = ("0" + date.getDate()).slice(-2);
  let month = ("0" + (date.getMonth() + 1)).slice(-2);
  date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
  $('#input_edit_tanggal').val(date1)

  document.getElementById("tabel_data_edit").innerHTML  = rowTable
  document.getElementById("input_edit_nobukti").value  = dataTableEdit[0].NoBukti
  document.getElementById("input_edit_nourut").value  = dataTableEdit[0].Nourut
  document.getElementById("input_edit_kodedepartemen").value  = dataTableEdit[0].KDDep

  // document.getElementById("input_detail_tanggal").value  = res[0].Tanggal
  $("#formEdit").modal('toggle')
}

function buttonEditDeleteItem (index) {
  console.log('buttonEditDeleteItem')



  console.log(dataTableEdit[index])

  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  let data = dataTableEdit[index]

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + data.KodeBrg + ' ?',
      function() {
        let _token = $("#_token").val();
        let choice = "D"
        let nourut = $("#input_edit_nourut").val();
        let nobukti = $("#input_edit_nobukti").val();
        let tanggal = $("#input_edit_tanggal").val();
        let isjasa = 0
        let pagen = 0
        let pjasa = 0
        let urut = data.Urut
        let kodebarang = data.KodeBrg
        let qnt = data.Qnt
        let nosat = data.NoSat
        let satuan = data.Satuan
        let isi = data.Isi
        let keterangan = data.Keterangan
        let isclose = 0
        let isclosed =0
        let kddep = "TEMP"
        let keterangannama = data.NamaBrg
        let noso = data.NOSO
        let urutso = data.URUTSO
        let nopocust = data.NoSOCust
        let jmlrecord = 0

        $.ajax({
          url: "{!! url('pembelianpermintaannonagenspdelete') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            choice,
            nourut,
            nobukti,
            tanggal,
            isjasa,
            pagen,
            pjasa,
            urut,
            kodebarang,
            qnt,
            nosat,
            satuan,
            isi,
            keterangan,
            isclose,
            isclosed,
            kddep,
            keterangannama,
            noso,
            urutso,
            nopocust,
            jmlrecord,
          },
          success: function(res) {
            console.log(res)
            alertify.success("Item sudah di delete");
            refreshDataTableEdit(nobukti)
            loadAll()


        }})
      }
    ,function(){
      console.log('no')
    });





}




function buttonDetail (nobukti) {
  console.log('buttonDetail')
  console.log(nobukti)

  $.ajax({
    url: "{!! url('pembelianpermintaannonagenlistdepartemen') !!}",
    type: "get",
    async: false,
    data: {
      // isagen: 0
    },
    success: function(res) {
      console.log('dept' , res)
      let selectDept = ``
      res.forEach((item, i) => {
        selectDept += `<option value="${item.KDDEP}">${item.KDDEP} - ${item.NMDEP}</option>`
      });

      document.getElementById("input_detail_kodedepartemen").innerHTML = selectDept

    }})

  $.ajax({
    url: "{!! url('pembelianpermintaannonagenspdetail') !!}",
    type: "get",
    async: false,
    data: {
      nobukti
    },
    success: function(res) {
      console.log(res)

      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `<tr>
        <td>${item.KodeBrg}</td>
        <td>${item.NamaBrg}</td>
        <td>${item.Qnt}</td>
        <td>${item.Satuan}</td>
        </tr>`
      });

      let date = new Date(res[0].Tanggal);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
      $('#input_detail_tanggal').val(date1)

      document.getElementById("tabel_data_detail").innerHTML  = rowTable
      document.getElementById("input_detail_nobukti").value  = res[0].NoBukti
      document.getElementById("input_detail_kodedepartemen").value = res[0].KDDep
      // document.getElementById("input_detail_tanggal").value  = res[0].Tanggal

  }})
  $("#formDetail").modal('toggle')



}

function setNewNoBukti () {
  $.ajax({
    url: "{!! url('pembelianpermintaanagenspnobukti') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    }})
}


function buttonAdd () {
  $('.showhide').hide();

  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  // pembelianpermintaannonagenspnobukti
  $.ajax({
    url: "{!! url('pembelianpermintaanagenspnobukti') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    }})
    dataTableAdd = []

    $.ajax({
      url: "{!! url('pembelianpermintaannonagenlistdepartemen') !!}",
      type: "get",
      async: false,
      data: {
        // isagen: 0
      },
      success: function(res) {
        console.log('dept' , res)
        let selectDept = `<option selected value="" disabled>Pilih Dept</option>`
        res.forEach((item, i) => {
          selectDept += `<option value="${item.KDDEP}">${item.KDDEP} - ${item.NMDEP}</option>`
        });

        document.getElementById("input_add_kodedepartemen").innerHTML = selectDept

      }})

  refreshDataTableAdd()
  $("#form").modal('toggle')
}

function closeListItemAdd () {
  $("#formAddListItem").modal('toggle')
  // document.getElementById("input_add_add_kodebarang").value = dataAddListItem[i].KODEBRG
  // document.getElementById("input_add_add_keterangannama").value = dataAddListItem[i].NAMABRG
  var modal = document.getElementById("form");
  modal.style.display = "block";

}

function closeListItemEdit () {
  $("#formEditListItem").modal('toggle')
  // document.getElementById("input_add_add_kodebarang").value = dataAddListItem[i].KODEBRG
  // document.getElementById("input_add_add_keterangannama").value = dataAddListItem[i].NAMABRG
  var modal = document.getElementById("formEdit");
  modal.style.display = "block";

}

function buttonEditListKodeBarang () {
  var modal = document.getElementById("formEdit");
  modal.style.display = "none";


  $.ajax({
    url: "{!! url('pembelianpermintaanagenlistbarang') !!}",
    type: "get",
    async: false,
    data: {
      isagen: 1
    },
    success: function(res) {
      console.log(res)
      dataEditListItem = res



    }})

    // tabel_add_list_item
    // $('#tabel_add_list_item').DataTable().destroy();


    console.log(dataEditListItem)

    let rowTable = ``
    dataEditListItem.forEach((item, i) => {
      rowTable += `
      <tr>
      <td>${item.KODEBRG}</td>
      <td>${item.NAMABRG}</td>
      <td>${item.NAMAMERK}</td>
      <td>${item.PartNumber}</td>
      <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonEditAddInsertItem(${i})" type="button" ><i class="bi bi-plus"></i></button></td>

      </tr>`
    });


    if(!dataEditListItem.length) {
      rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
    }
    document.getElementById("tabel_data_edit_list_item").innerHTML = rowTable


  $("#formEditListItem").modal('toggle')
}

function buttonAddListKodeBarang () {

  var modal = document.getElementById("form");
  // $('#form').css('opacity', .8);
  modal.style.display = "none";
  // $("#formAddListItem").modal({backdrop: true})
  // pembelianpermintaannonagenlistbarang
  $.ajax({
    url: "{!! url('pembelianpermintaanagenlistbarang') !!}",
    type: "get",
    async: false,
    data: {
      isagen: 1
    },
    success: function(res) {
      console.log(res)
      dataAddListItem = res



    }})

    // tabel_add_list_item
    // $('#tabel_add_list_item').DataTable().destroy();


    console.log(dataAddListItem)
    let rowTable = ``
    dataAddListItem.forEach((item, i) => {
      rowTable += `
      <tr>
      <td>${item.KODEBRG}</td>
      <td>${item.NAMABRG}</td>
      <td>${item.NAMAMERK}</td>
      <td>${item.PartNumber}</td>
      <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddAddInsertItem(${i})" type="button" ><i class="bi bi-plus"></i></button></td>

      </tr>`
    });


    if(!dataAddListItem.length) {
      rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
    }
    document.getElementById("tabel_data_add_list_item").innerHTML = rowTable

    // $("#tabel_add_list_item").DataTable({
    //   "lengthChange": false,
    //     "paging": false ,
    //   });

  $("#formAddListItem").modal('toggle')

}

function buttonAddDeleteItem (i) {
  dataTableAdd.splice(i,1)
  refreshDataTableAdd()
}
function buttonEditAddInsertItem (i) {
  console.log('buttonEditAddInsertItem')
  // console.log(tempEdit)
  tempEditAdd = dataEditListItem[i]

  console.log(dataTableEdit)
  console.log(tempEditAdd)
  let check = dataTableEdit.filter(function (el) {
    // console.log(el.kodebarang)
    return el.KodeBrg == tempEditAdd.KODEBRG
  })
  if (check.length) {
    alertify.warning('Barang sudah ada di list')
    return
  }

  let radioButton = ``

  if (tempEditAdd.SAT1) {
    radioButton += ` <div class="form-check form-check-inline">
       <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioEditAdd1" value="option1" >
      <label class="form-check-label" for="inlineRadio1">[1] ${tempEditAdd.SAT1}</label>
     </div>`
  } else {
    radioButton += `<div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioEditAdd1" value="option1" disabled>
      <label class="form-check-label" for="inlineRadio1">-</label>
    </div>`
  }
  if (tempEditAdd.SAT2) {
    radioButton += ` <div class="form-check form-check-inline">
       <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioEditAdd2" value="option2" >
      <label class="form-check-label" for="inlineRadio2">[2] ${tempEditAdd.SAT2}</label>
     </div>`
  } else {
    radioButton += `<div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioEditAdd2" value="option2" disabled>
      <label class="form-check-label" for="inlineRadio2">-</label>
    </div>`
  }
  if (tempEditAdd.SAT3) {
    radioButton += ` <div class="form-check form-check-inline">
       <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioEditAdd3" value="option1" >
      <label class="form-check-label" for="inlineRadio3">[3] ${tempEditAdd.SAT3}</label>
     </div>`
  } else {
    radioButton += `<div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioEditAdd3" value="option1" disabled>
      <label class="form-check-label" for="inlineRadio3">-</label>
    </div>`
  }
  document.getElementById("input_edit_add_satuan").innerHTML = radioButton

  $("#formEditListItem").modal('toggle')
  document.getElementById("input_edit_add_kodebarang").value = tempEditAdd.KODEBRG
  document.getElementById("input_edit_add_keterangannama").value = tempEditAdd.NAMABRG
  var modal = document.getElementById("formEdit");
  modal.style.display = "block";

}

function buttonAddAddInsertItem (i) {
  console.log(dataAddListItem[i])
  tempAdd = dataAddListItem[i]

  let check = dataTableAdd.filter(function (el) {
    return el.kodebarang == tempAdd.KODEBRG
  })
  console.log(dataTableAdd)
  console.log(check)

  if (check.length) {
    alertify.warning('Barang sudah ada di list')
    return
  }

  let radioButton = ``

  if (tempAdd.SAT1) {
    radioButton += ` <div class="form-check form-check-inline">
       <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1" >
      <label class="form-check-label" for="inlineRadio1">[1] ${tempAdd.SAT1}</label>
     </div>`
  } else {
    radioButton += `<div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1" disabled>
      <label class="form-check-label" for="inlineRadio1">-</label>
    </div>`
  }
  if (tempAdd.SAT2) {
    radioButton += ` <div class="form-check form-check-inline">
       <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2" >
      <label class="form-check-label" for="inlineRadio2">[2] ${tempAdd.SAT2}</label>
     </div>`
  } else {
    radioButton += `<div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2" disabled>
      <label class="form-check-label" for="inlineRadio2">-</label>
    </div>`
  }
  if (tempAdd.SAT3) {
    radioButton += ` <div class="form-check form-check-inline">
       <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="option1" >
      <label class="form-check-label" for="inlineRadio3">[3] ${tempAdd.SAT3}</label>
     </div>`
  } else {
    radioButton += `<div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="option1" disabled>
      <label class="form-check-label" for="inlineRadio3">-</label>
    </div>`
  }
  document.getElementById("input_add_add_satuan").innerHTML = radioButton

  // <div class="form-check form-check-inline">
  //   <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1" disabled>
  //   <label class="form-check-label" for="inlineRadio1">-</label>
  // </div>
  // <div class="form-check form-check-inline">
  //   <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2" disabled>
  //   <label class="form-check-label" for="inlineRadio2">-</label>
  // </div>
  // <div class="form-check form-check-inline">
  //   <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="option3" disabled>
  //   <label class="form-check-label" for="inlineRadio3">-</label>
  // </div>

  $("#formAddListItem").modal('toggle')
  document.getElementById("input_add_add_kodebarang").value = dataAddListItem[i].KODEBRG
  document.getElementById("input_add_add_keterangannama").value = dataAddListItem[i].NAMABRG
  var modal = document.getElementById("form");
  modal.style.display = "block";
}

function submitEditEdit () {
  let _token = $("#_token").val();
  let choice = "U"
  let nobukti = $("#input_edit_nobukti").val();
  let nourut = $("#input_edit_nourut").val();
  let tanggal = $("#input_edit_tanggal").val();
  let urut = tempEditEdit.Urut
  let kodebarang = $("#input_edit_edit_kodebarang").val();
  let qnt = $("#input_edit_edit_qnt").val();
  let nosat = 0
  let satuan = 0
  let isi = 0
  let keterangan = $("#input_edit_edit_keterangan").val();
  let isclose = tempEditEdit.IsClose
  let isclosed = tempEditEdit.IsClosed
  let kddep = tempEditEdit.KDDep
  let keterangannama = $("#input_edit_edit_keterangannama").val();
  let isjasa = 0
  let noso = $("#input_edit_edit_refso").val();
  let urutso = tempEditEdit.URUTSO
  let pagen = 1
  let nopocust = $("#input_edit_edit_nopocust").val();
  let jmlrecord = 0
  let pjasa = 0



  if (document.getElementById("inlineRadioEditEdit1").checked) {
    satuan = tempEditEdit.SAT1
    isi = tempEditEdit.ISI1
    nosat = 1
  }
  if (document.getElementById("inlineRadioEditEdit2").checked) {
    satuan = tempEditEdit.SAT2
    isi = tempEditEdit.ISI2
    nosat = 2
  }
  if (document.getElementById("inlineRadioEditEdit3").checked) {
    satuan = tempEditEdit.SAT3
    isi = tempEditEdit.ISI3
    nosat =3
  }


  console.log('choice',choice)
  console.log('nobukti',nobukti)
  console.log('nourut',nourut)
  console.log('tanggal',tanggal)
  console.log('urut',urut)
  console.log('kodebarang',kodebarang)
  console.log('qnt',qnt)
  console.log('nosat',nosat)
  console.log('satuan',satuan)
  console.log('isi',isi)
  console.log('keterangan',keterangan)
  console.log('isclose',isclose)
  console.log('isclosed',isclosed)
  console.log('kddep',kddep)
  console.log('keterangannama',keterangannama)
  console.log('isjasa',isjasa)
  console.log('noso',noso)
  console.log('urutso',urutso)
  console.log('pagen',pagen)
  console.log('nopocust',nopocust)
  console.log('jmlrecord',jmlrecord)
  console.log('pjasa',pjasa)


  $.ajax({
    url: "{!! url('pembelianpermintaannonagenspdelete') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      choice,
      nourut,
      nobukti,
      tanggal,
      isjasa,
      pagen,
      pjasa,
      urut,
      kodebarang,
      qnt,
      nosat,
      satuan,
      isi,
      keterangan,
      isclose,
      isclosed,
      kddep,
      keterangannama,
      noso,
      urutso,
      nopocust,
      jmlrecord,
    },
    success: function(res) {
      console.log(res)
      alertify.success("Item sudah di edit");
      refreshDataTableEdit(nobukti)
      $('.showhideedit').hide();
      loadAll()


  }})




}

function submitAdd () {
  console.log(dataTableAdd)
  // return
  if (!dataTableAdd.length) {
    alertify.warning("Belum ada data");
    return
  }





  let _token = $("#_token").val();
  let nourut = $("#input_add_nourut").val();
  let nobukti = $("#input_add_nobukti").val();
  let tanggal = $("#input_add_tanggal").val();
  let kodedepartemen = $("#input_add_kodedepartemen").val();
  console.log('kddep',kodedepartemen)

  if (!kodedepartemen) {
    alertify.warning("Departemen harus diisi");
    return
  }
  // return

  let checkDate = new Date(tanggal)

  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  console.log(checkDate.getFullYear(), checkDate.getMonth() +1 )
  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {
      // console.log('ga sama')
      alertify.warning("Tanggal tidak sesuai periode");
      return
  }

  // return



  let choice = "I"
  let isjasa = 0
  let pagen = 1
  let pjasa = 0
  console.log(_token)
  console.log('choice' , choice)
  console.log('nourut' , nourut)
  console.log('nobukti' , nobukti)
  console.log('tanggal' , tanggal)
  console.log('isjasa' , isjasa)
  console.log('pagen' , pagen)
  dataTableAdd[0].jmlrecord = 0
  console.log(dataTableAdd)

  $.ajax({
    url: "{!! url('pembelianpermintaannonagenspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nourut,
      nobukti,
      tanggal,
      choice,
      isjasa,
      pagen,
      pjasa,
      kodedepartemen,
      listData: dataTableAdd

    },
    success: function(res) {

      if (res == 2) {
        setNewNoBukti()
        alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
      } else if (res == 1) {
        console.log(res)
        loadAll()
        $("#form").modal('toggle')
      } else {

        alertify.warning(res);

      }


    }})


}

function submitEditAdd () {
  console.log(tempEditAdd)
  let satuan = 0
  let isi = 0
  let nosat = 0
  console.log('0 ===========')
  if (document.getElementById("inlineRadioEditAdd1").checked) {
    satuan = tempEditAdd.SAT1
    isi = tempEditAdd.ISI1
    nosat = 1
  }
  console.log('1 ===========')
  if (document.getElementById("inlineRadioEditAdd2").checked) {
    satuan = tempEditAdd.SAT2
    isi = tempEditAdd.ISI2
    nosat = 2
  }
  console.log('2 ===========')
  if (document.getElementById("inlineRadioEditAdd3").checked) {
    satuan = tempEditAdd.SAT3
    isi = tempEditAdd.ISI3
    nosat =3
  }
  console.log('3 ============')

  let _token = $("#_token").val();
  let choice = "I"
  let nobukti = $("#input_edit_nobukti").val();
  let nourut = $("#input_edit_nourut").val();
  let kodebarang = $("#input_edit_add_kodebarang").val();
  let keterangannama = $("#input_edit_add_keterangannama").val();
  let qnt = $("#input_edit_add_qnt").val();
  let keterangan = $("#input_edit_add_keterangan").val();
  let nopocust = $("#input_edit_add_nopocust").val();
  let refso = $("#input_edit_add_refso").val();
  let tanggal = $("#input_edit_tanggal").val();
  let isjasa = 0
  let pagen = 0
  let pjasa = 0

  let isclose = 0
  let isclosed =0
  let kddep = "TEMP"

  let jmlrecord = 1
  let urut = 0
  let urutso = 0

  // noso: refso
  // nopocust: nopocust
  // urutso: 0


  refso = "-"
  nopocust = "-"

  console.log('4 ==============')

  if (!kodebarang) {
    alertify.warning("KodeBarang  harus diisi");
    return
  }
  if (!nosat) {
    alertify.warning("Satuan  harus diisi");
    return
  }

  console.log('5 =================')
  console.log('choice' , choice)
  console.log('nobukti' , nobukti)
  console.log('nourut' , nourut)
  console.log('tanggal' , tanggal)
  console.log('urut' , urut)
  console.log('kodebarang' , kodebarang)
  console.log('qnt' , qnt)
  console.log('nosat' , nosat)
  console.log('satuan' , satuan)
  console.log('isi' , isi)
  console.log('keterangan' , keterangan)
  console.log('isclose' , isclose)
  console.log('isclosed' , isclosed)
  console.log('kddep' , kddep)
  console.log('keterangannama' , keterangannama)
  console.log('isjasa' , isjasa)
  console.log('noso' , refso)
  console.log('urutso' , urutso)
  console.log('pagen' , pagen)
  console.log('nopocust' , nopocust)
  console.log('jmlrecord' , jmlrecord)
  console.log('pjasa' , pjasa)


  // let _token = $("#_token").val();
  // let choice = "D"
  // let nourut = $("#input_edit_nourut").val();
  // let nobukti = $("#input_edit_nobukti").val();
  // let tanggal = $("#input_edit_tanggal").val();
  // let isjasa = 0
  // let pagen = 0
  // let pjasa = 0
  // let urut = data.Urut
  // let kodebarang = data.KodeBrg
  // let qnt = data.Qnt
  // let nosat = data.NoSat
  // let satuan = data.Satuan
  // let isi = data.Isi
  // let keterangan = data.Keterangan
  // let isclose = 0
  // let isclosed =0
  // let kddep = "TEMP"
  // let keterangannama = data.NamaBrg
  // let noso = data.NOSO
  // let urutso = data.URUTSO
  // let nopocust = data.NoSOCust
  // let jmlrecord = 0

  console.log('6====================')

  $.ajax({
    url: "{!! url('pembelianpermintaannonagenspdelete') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      choice,
      nourut,
      nobukti,
      tanggal,
      isjasa,
      pagen,
      pjasa,
      urut,
      kodebarang,
      qnt,
      nosat,
      satuan,
      isi,
      keterangan,
      isclose,
      isclosed,
      kddep,
      keterangannama,
      noso:refso,
      urutso,
      nopocust,
      jmlrecord,
    },
    success: function(res) {
      console.log(res)
      alertify.success("Item sudah di add");

      console.log('7 ======================')
      refreshDataTableEdit(nobukti)
      loadAll()


  }})




  $('.showhideedit').hide();
}

function submitAddAdd () {
  // let satuan = $("#input_add_add_satuan").val();
  // console.log(satuan)
  console.log(tempAdd)
  let satuan = 0
  let isi = 0
  let nosat = 0

  if (document.getElementById("inlineRadio1").checked) {
    satuan = tempAdd.SAT1
    isi = tempAdd.ISI1
    nosat = 1
  }
  if (document.getElementById("inlineRadio2").checked) {
    satuan = tempAdd.SAT2
    isi = tempAdd.ISI2
    nosat = 2
  }
  if (document.getElementById("inlineRadio3").checked) {
    satuan = tempAdd.SAT3
    isi = tempAdd.ISI3
    nosat =3
  }

  let kodebarang = $("#input_add_add_kodebarang").val();
  let keterangannama = $("#input_add_add_keterangannama").val();
  let qnt = $("#input_add_add_qnt").val();
  let keterangan = $("#input_add_add_keterangan").val();
  let nopocust = $("#input_add_add_nopocust").val();
  let refso = $("#input_add_add_refso").val();

  refso = "-"
  nopocust = "-"

  if (!kodebarang) {
    alertify.warning("KodeBarang  harus diisi");
    return
  }
  if (!nosat) {
    alertify.warning("Satuan  harus diisi");
    return
  }

  console.log('kodebarang' , kodebarang)
  console.log('keterangannama' , keterangannama)
  console.log('qnt' , qnt)
  console.log('keterangan' , keterangan)
  console.log('nopocust' , nopocust)
  console.log('refso' , refso)
  console.log('nosat' , nosat)
  console.log('satuan' , satuan)
  console.log('isi' , isi)

  $('.showhide').hide();

  dataTableAdd.push({
    kodebarang,
    keterangannama,
    qnt,
    keterangan,
    nopocust,
    refso,
    nosat,
    satuan,
    isi,
    jmlrecord: 1,
    kddep: "TEMP",
    nosocust: "-",
    noso: "-",
    urutso: 0,
    isclose: false,
    isclosed: false,
    urut: 0,
    SAT1: tempAdd.SAT1,
    SAT2: tempAdd.SAT2,
    SAT3: tempAdd.SAT3,
    ISI1: tempAdd.ISI1,
    ISI2: tempAdd.ISI2,
    ISI3: tempAdd.ISI3,

  })
  refreshDataTableAdd()

  // console.log(document.getElementById("inlineRadio1").checked)
  // console.log(document.getElementById("inlineRadio2").checked)
  // console.log(document.getElementById("inlineRadio3").checked)
  // document.getElementById("inlineRadio1").checked = false
}

function buttonEditAddItem () {
  console.log('buttonEditAddItem')
  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  $('.showhideedit').hide();

  tempEditAdd = {}
  // document.getElementById("inlineRadioEditAdd1").checked = false
  document.getElementById("input_edit_add_refso").value = "-"
  document.getElementById("input_edit_add_nopocust").value = ""
  document.getElementById("input_edit_add_kodebarang").value = ""
  document.getElementById("input_edit_add_keterangannama").value = ""
  document.getElementById("input_edit_add_qnt").value = "0.00"
  document.getElementById("input_edit_add_keterangan").value = ""
  document.getElementById("input_edit_add_satuan").innerHTML = `<div class="form-check form-check-inline">
    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioEditAdd1" value="option1" disabled>
    <label class="form-check-label" for="inlineRadio1">-</label>
  </div>
  <div class="form-check form-check-inline">
    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioEditAdd2" value="option2" disabled>
    <label class="form-check-label" for="inlineRadio2">-</label>
  </div>
  <div class="form-check form-check-inline">
    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioEditAdd3" value="option3" disabled>
    <label class="form-check-label" for="inlineRadio3">-</label>
  </div>`



  $('#editAddItem').show();
  // editAddItem
  // inlineRadioEditAdd
}

function buttonAddAddItem () {
  console.log('buttonAddAddItem')

  $('.showhide').hide();
  tempAdd = {}
  // document.getElementById("inlineRadio1").checked = false
  // document.getElementById("input_add_add_refso").value = "-"
  // document.getElementById("input_add_add_nopocust").value = ""
  // document.getElementById("input_add_add_kodebarang").value = ""
  // document.getElementById("input_add_add_keterangannama").value = ""
  // document.getElementById("input_add_add_qnt").value = "0.00"
  // document.getElementById("input_add_add_keterangan").value = ""
  // document.getElementById("input_add_add_satuan").innerHTML = `<div class="form-check form-check-inline">
  //   <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1" disabled>
  //   <label class="form-check-label" for="inlineRadio1">-</label>
  // </div>
  // <div class="form-check form-check-inline">
  //   <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2" disabled>
  //   <label class="form-check-label" for="inlineRadio2">-</label>
  // </div>
  // <div class="form-check form-check-inline">
  //   <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="option3" disabled>
  //   <label class="form-check-label" for="inlineRadio3">-</label>
  // </div>`

  $('#addAddItem').show();
}

function closeShowHideAdd () {
  $('.showhide').hide();
}

function closeShowHideEdit () {
  $('.showhideedit').hide();
}

function refreshDataTableAdd () {

  let rowTable = ""
  dataTableAdd.forEach((item, i) => {
    rowTable += `<tr>
    <td>${item.kodebarang}</td>
    <td>${item.keterangannama}</td>
    <td>${item.qnt}</td>
    <td>${item.satuan}</td>
    <td class="text-center">
    <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
     <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDeleteItem(${i})"><i class="bi bi-trash"></i></button></td>
    </tr>`
  });

  if(!dataTableAdd.length) {
    rowTable = `<tr>
    <td class="text-center" colspan="5">Belum ada barang</td>
    </tr>`
  }
  document.getElementById("tabel_data_add").innerHTML = rowTable

}

</script>




@endsection
