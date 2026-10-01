@extends('master.newmaster')
@section('buttons')

@endsection
@section('content')

<div class="container-fluid">

  <!-- <div id="qrcode"></div> -->
<div class="row mt-4">
      <div class="col-6 text-left">
        <h2 style="margin-top:-85px;">Master Customer</h2>
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
          Add Customer
        </button>
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
            onclick="loadAll()">
          tes load all
        </button>
      </div> --}}
    </div>
<!-- <button onclick="loadAll()">tes</button> -->
</div>

<div id="printContainer" style="display:none">

</div>
<div id="contentContainer" class="container-fluid" style='margin-top:-95px;' >
  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />
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
                      <thead id='theadCustom' class="text-center" style='white-space:nowrap;'>
                        <tr>
                          <th scope="col">Actions</th>
                          <th scope="col">Kode</th>
                          <th scope="col">Bentuk Usaha</th>
                          <th scope="col">Nama</th>
                          <th scope="col">Alamat</th>
                          <th scope="col">Kota</th>
                          <th scope="col">Kode Pos</th>
                          <th scope="col">Negara</th>
                          <th scope="col">Telpon</th>
                          <th scope="col">Fax</th>
                          <th scope="col">Email</th>

                        </tr>
                      </thead>


                      <tbody id="tabel_data" class="text-left" >
                      </tbody>


                    </table>
              </div>
            </div>
          </div>


</div>


<!-- start modal add -->
<div class="modal fade"  id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 800px">
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
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kode</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_kode" placeholder="Kode">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Bentuk Usaha</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_bentukusaha" placeholder="Bentuk Usaha">
                </div>
              </div>
              <div class="col-2">
                <div class="form-group text-right">
                <input type="checkbox" id="input_add_isppn" name="" value="">
                <label class="text-left">PPN</label>
              </div>
              </div>

            </div>

            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Nama</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_nama" placeholder="Nama">
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Alamat</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_alamat"  placeholder="Alamat">
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kota</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <select id="input_add_kota" class="form-control" aria-label="Default select example">
                    <option selected value="0">Pilih Kota</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                  </select>
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">KodePos</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_kodepos" placeholder="Kode Pos">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Negara</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_negara" placeholder="Negara">
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Telp</label>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_telp" placeholder="No Telp">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Pph23</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <input type="number" class="form-control text-right" id="input_add_pph23" value=0.00>
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Fax</label>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_fax" placeholder="No Fax">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Pph21</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <input type="number" class="form-control text-right" id="input_add_pph21" value=0.00>
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Email</label>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_email" placeholder="Email">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">TOP</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <input type="number" class="form-control text-right" id="input_add_top" value=0>
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Status</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <select id="input_add_isaktif" class="form-control" aria-label="Default select example">
                    <option selected value=1>Aktif</option>
                    <option value=0>Tidak Aktif</option>
                  </select>
                </div>
              </div>
              <div class="col-1 text-right">
                <div class="form-group text-right">
                  <label class="text-right">Plafon</label> 
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <input type="number" class="form-control text-right" id="input_add_plafon" value=0 >
                </div>
              </div>
              <div class="col-2 text-right">
                <div class="form-group text-left">
                  <label class="text-left">H Piutang</label> 
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <input type="number" class="form-control text-right" id="input_add_haripiutang" value=0 >
                </div>
              </div>


            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Att</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_att" placeholder="Att">
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Att Phone</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_attphone" placeholder="Att Phone">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Att Depart</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_attdepart" placeholder="Att Depart">
                </div>
              </div>

            </div>
            <!-- <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Bank</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_bank" placeholder="Nama Bank">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Acc No.</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_accno" placeholder="Acc No.">
                </div>
              </div>

            </div> -->
            <!-- <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">a/n</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_atn" placeholder="Atas Nama">
                </div>
              </div>
            </div> -->
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Jenis</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <select id="input_add_jeniscustomer" class="form-control" aria-label="Default select example">
                    <option selected value="0">Pilih Jenis Customer</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                  </select>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group text-right">
                <input type="checkbox" id="input_add_berikat" name="" value="">
                <label class="text-left">Berikat</label>
              </div>
              </div>
              <div class="col-3">
                <div class="form-group text-right">
                <input type="checkbox" id="input_add_blacklist" name="" value="">
                <label class="text-left">Blacklist</label>
              </div>
              </div>
            </div>

            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Int.Comp</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_intcomp" placeholder="">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Comp Code</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_compcode" placeholder="">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">CustCode</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_custcode" placeholder="">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Group</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <select id="input_add_groupcustomer" class="form-control" aria-label="Default select example">
                    <option selected value="0">Pilih Group</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                  </select>
                </div>
              </div>
            </div>
            <br/>

            <h5 class='text-primary'>Data Pajak</h5>
            <br/>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">NPWP</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_npwp" placeholder="NPWP">
                </div>
              </div>


            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Nama</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_namapkp" placeholder="Nama PKP">
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Alamat</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_alamatpkp" placeholder="Alamat PKP">
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kota</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_kotapkp" placeholder="Kota PKP">
                </div>
              </div>


            </div>








    </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button>
  </div>
</div>
</div>
</div>
<!-- End modal add-->








<!-- start modal edit -->
<div class="modal fade"  id="formEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 800px">
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
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->

            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kode</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="text" class="form-control" disabled id="input_edit_kode" placeholder="Kode">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Bentuk Usaha</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_bentukusaha" placeholder="Bentuk Usaha">
                </div>
              </div>
              <div class="col-2">
                <div class="form-group text-right">
                <input type="checkbox" id="input_edit_isppn" name="" value="">
                <label class="text-left">PPN</label>
              </div>
              </div>

            </div>

            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Nama</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_nama" placeholder="Nama">
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Alamat</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_alamat"  placeholder="Alamat">
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kota</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <select id="input_edit_kota" class="form-control" aria-label="Default select example">
                    <option selected value="0">Pilih Kota</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                  </select>
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">KodePos</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_kodepos" placeholder="Kode Pos">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Negara</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_negara" placeholder="Negara">
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Telp</label>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_telp" placeholder="No Telp">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Pph23</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <input type="number" class="form-control text-right" id="input_edit_pph23" value=0.00>
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Fax</label>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_fax" placeholder="No Fax">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Pph21</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <input type="number" class="form-control text-right" id="input_edit_pph21" value=0.00>
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Email</label>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_email" placeholder="Email">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">TOP</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <input type="number" class="form-control text-right" id="input_edit_top" value=0>
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Status</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <select id="input_edit_isaktif" class="form-control" aria-label="Default select example">
                    <option selected value=1>Aktif</option>
                    <option value=0>Tidak Aktif</option>
                  </select>
                </div>
              </div>
              <div class="col-1 text-right">
                <div class="form-group text-right">
                  <label class="text-right">Plafon</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <input type="number" class="form-control text-right" id="input_edit_plafon" value=0 >
                </div>
              </div>
              <div class="col-2 text-right">
                <div class="form-group text-left">
                  <label class="text-left">H Piutang</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <input type="number" class="form-control text-right" id="input_edit_haripiutang" value=0 >
                </div>
              </div>


            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Att</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_att" placeholder="Att">
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Att Phone</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_attphone" placeholder="Att Phone">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Att Depart</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_attdepart" placeholder="Att Depart">
                </div>
              </div>

            </div>
            <!-- <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Bank</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_bank" placeholder="Nama Bank">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Acc No.</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_accno" placeholder="Acc No.">
                </div>
              </div>

            </div> -->
            <!-- <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">a/n</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_atn" placeholder="Atas Nama">
                </div>
              </div>
            </div> -->
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Jenis</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <select id="input_edit_jeniscustomer" class="form-control" aria-label="Default select example">
                    <option selected value="0">Pilih Jenis Customer</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                  </select>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group text-right">
                <input type="checkbox" id="input_edit_berikat" name="" value="">
                <label class="text-left">Berikat</label>
              </div>
              </div>
              <div class="col-3">
                <div class="form-group text-right">
                <input type="checkbox" id="input_edit_blacklist" name="" value="">
                <label class="text-left">Blacklist</label>
              </div>
              </div>
            </div>

            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Int.Comp</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_intcomp" placeholder="">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Comp Code</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_compcode" placeholder="">
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">CustCode</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_custcode" placeholder="">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Group</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <select id="input_edit_groupcustomer" class="form-control" aria-label="Default select example">
                    <option selected value="0">Pilih Group</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                  </select>
                </div>
              </div>
            </div>
            <br/>

            <h5 class='text-primary'>Data Pajak</h5>
            <br/>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">NPWP</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_npwp" placeholder="NPWP">
                </div>
              </div>


            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Nama</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_namapkp" placeholder="Nama PKP">
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Alamat</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_alamatpkp" placeholder="Alamat PKP">
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kota</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_kotapkp" placeholder="Kota PKP">
                </div>
              </div>


            </div>








    </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-primary" onclick="submitEdit()">Submit</button>
  </div>
</div>
</div>
</div>
<!-- End modal edit-->

<!-- start detail akun -->
<div class="modal fade"  id="formDetailAkun" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detail Akun</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <button class="btn btn-primary btn-sm" type="button" onclick="buttonDetailAkunAdd()">Add Data</i></button>
        <table id="tabelDetailAkun" class="table table-bordered table-striped"  >
          <thead id='theadCustom' class="text-center">
            <tr>
              <th scope="col">Actions</th>
              <th scope="col">Kode</th>
              <th scope="col">Perkiraan</th>

            </tr>
          </thead>

          <tbody id="tabel_dataDetailAkun" class="text-left" >
            <tr>

              <td></td>
              <td></td>

                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button type="button" onclick="buttonDetailAkunEdit()"><i class="bi bi-pen">Select</i></button>
                  <button type="button" onclick="buttonDetailAkunDelete()"><i class="bi bi-trash">Select</i></button>
                </td>
          </tr>
          </tbody>


        </table>


    </div>
        <div class="modal-footer">
           
        </div>
  </div>
</div>
</div>
<!-- End modal detail akun-->


<!-- start modal add detail akun-->
<div class="modal fade"  id="formDetailAkunAdd" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Detail Akun</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kode</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_kodedetail" placeholder="Kode" disabled>
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Hutang/Piutang</label>
                </div>
              </div>
              <div class="col-5">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_perkiraandetail" placeholder="Perkiraan">
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <button class="btn btn-primary btn-sm" type="button" onclick="buttonDetailAkunSelect()">Select</i></button>
                </div>
              </div>

            </div>


    </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-primary" onclick="submitAddDetailAkun()">Submit</button>
  </div>
</div>
</div>
</div>
<!-- End modal add detail akun-->

{{-- start modal edit akun --}}
<div class="modal fade"  id="formDetailAkunEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Detail Akun</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kode</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_kodedetail" placeholder="Kode" disabled>
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Hutang/Piutang</label>
                </div>
              </div>
              <div class="col-5">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_perkiraandetail" placeholder="Perkiraan">
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <button class="btn btn-primary btn-sm" type="button" onclick="buttonDetailAkunSelect()">Select</i></button>
                </div>
              </div>

            </div>

    </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-primary" onclick="submitAddDetailAkunEdit()">Submit</button>
  </div>
</div>
</div>
</div>

<!-- start detail akun perkiraan -->
<div class="modal fade"  id="formDetailAkunAddPerkiraan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detail Akun</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      {{-- <button class="btn btn-primary btn-sm" type="button" onclick="buttonDetailAkunAdd('${item.Perkiraan}')">Add Data</i></button> --}}
        <table id="tabelDetailAkunAddPerkiraan" class="table table-bordered table-striped"  >
          <thead class="text-center bg-primary text-white">
            <tr>
              <th scope="col">Actions</th>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>

            </tr>
          </thead>

          <tbody id="tabel_dataDetailAkunAddPerkiraan" class="text-left" >
            <tr>

              <td></td>
              <td></td>

                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button type="button" onclick="buttonDetailAkunEditSelect()"><i class="bi bi-pen">Select</i></button>
                </td>
          </tr>
          </tbody>


        </table>


    </div>
        <div class="modal-footer">
           
        </div>
  </div>
</div>
</div>
<!-- End modal detail akun perkiraan-->

@include('master.modalSupplierCustomer')

@endsection

@section('js')
<script type="text/javascript">

let dataRefresh = []
let kodeDetailPerkiraan = []

function loadAll () {
  $('#tabel').DataTable().destroy();

  $('#tabel').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "{!! url('mastercustomerloadall') !!}",
      type: "get",
      data: {
        _token: $("#_token").val(),
      }
    },
    columns: [
      {
        data: 'KODECUSTSUPP',
        render: function(data, type, row) {
          return `
          <button class="btn btn-success btn-sm hover-tooltip" type="button" onclick="buttonEdit('${data}')" data-tooltip="Edit Customer">
            <i class="bi bi-pen"></i>
          </button>
          <button class="btn btn-primary btn-sm hover-tooltip" type="button" onclick="buttonDetailAkun('${data}')" data-tooltip="Detail Akun">
            <i class="bi bi-card-text"></i>
          </button>
          <button class="btn btn-primary btn-sm hover-tooltip" type="button" onclick="buttonDetailAkun('${data}')" data-tooltip="PIC">
            <i class="bi bi-file-earmark-person"></i>
          </button>
          <button class="btn btn-primary btn-sm hover-tooltip" type="button" onclick="buttonAlamat('${data}')" data-tooltip="Alamat">
            <i class="bi bi-house-door"></i>
          </button>
          <button class="btn btn-danger btn-sm hover-tooltip" type="button" onclick="buttonDelete('${data}')" data-tooltip="Delete Customer">
            <i class="bi bi-trash"></i>
          </button>
          `;
        },
        orderable: false,
        searchable: false,
        className: "text-center"
      },
      { data: 'KODECUSTSUPP' },
      { data: 'USAHA', defaultContent: '' },
      { data: 'NAMACUSTSUPP', defaultContent: '' },
      { data: 'ALAMAT1', defaultContent: '' },
      { data: 'Kota', defaultContent: '' },
      { data: 'KODEPOS', defaultContent: '' },
      { data: 'NEGARA', defaultContent: '' },
      { data: 'TELPON', defaultContent: '' },
      { data: 'FAX', defaultContent: '' },
      { data: 'EMAIL', defaultContent: '' }
    ],
    lengthChange: true,
    pageLength: 10
  });
}

function buttonAdd () {
  $.ajax({
    url: "{!! url('mastercustomerlistselect') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {

      console.log(res)
      console.log(res.listDataKota)
      let rowTable = `<option selected disabled value=0>Pilih Kota</option>`
      res.listDataKota.forEach((item, i) => {
        rowTable += `
          <option value="${item.KodeKota}">${item.NamaKota}</option>
        `
      });
      let rowTableJenis = `<option selected disabled value="">Pilih Jenis</option>`
      res.listDataJenis.forEach((item, i) => {
        rowTableJenis += `
          <option value="${item.KodeJenis}">${item.KodeJenis} - ${item.NamaJenis}</option>
        `
      });
      let rowTableGroup = `<option selected disabled value="">Pilih Group</option>`
      res.listDataGroup.forEach((item, i) => {
        rowTableGroup += `
          <option value="${item.KODEGROUPCUSTSUPP}">${item.KODEGROUPCUSTSUPP} - ${item.NAMAGROUPCUSTSUPP}</option>
        `
      });


      document.getElementById("input_add_groupcustomer").innerHTML = rowTableGroup
      document.getElementById("input_add_jeniscustomer").innerHTML = rowTableJenis
      document.getElementById("input_add_kota").innerHTML = rowTable
    }})


  $("#form").modal('toggle')

}


function submitEdit () {

  let _token = $("#_token").val();
  let isppn = 0
  if (document.getElementById("input_edit_isppn").checked) {
    isppn= 1
  }
  console.log(isppn)
  let kode = $("#input_edit_kode").val();
  console.log('kode' , kode)
  let bentukusaha = $("#input_edit_bentukusaha").val();
  console.log('bentukusaha' , bentukusaha)
  let nama = $("#input_edit_nama").val();
  console.log('nama' , nama)
  let alamat = $("#input_edit_alamat").val();
  console.log('alamat' , alamat)
  let kota = $("#input_edit_kota").val();
  console.log('kota' , kota)
  let kodepos = $("#input_edit_kodepos").val();
  console.log('kodepos' , kodepos)
  let negara = $("#input_edit_negara").val();
  console.log('negara' , negara)
  let telp = $("#input_edit_telp").val();
  console.log('telp' , telp)
  let fax = $("#input_edit_fax").val();
  console.log('fax' , fax)
  let email = $("#input_edit_email").val();
  console.log('email' , email)
  let pph23 = $("#input_edit_pph23").val();
  console.log('pph23' , pph23)
  let pph21 = $("#input_edit_pph21").val();
  console.log('pph21' , pph21)
  let haripiutang = $("#input_edit_haripiutang").val();
  console.log('haripiutang' , haripiutang)
  let isaktif = $("#input_edit_isaktif").val();
  console.log('isaktif' , isaktif)
  let att = $("#input_edit_att").val();
  console.log('att' , att)
  let attphone = $("#input_edit_attphone").val();
  console.log('attphone' , attphone)
  
  if(!attphone){
    alertify.warning('Att Phone wajib diisi.')
  }

  let attdepart = $("#input_edit_attdepart").val();
  console.log('attdepart' , attdepart)
  // let bank = $("#input_edit_bank").val();
  // console.log('bank' , bank)
  // let accno = $("#input_edit_accno").val();
  // console.log('accno' , accno)
  let npwp = $("#input_edit_npwp").val();
  console.log('npwp' , npwp)
  let namapkp = $("#input_edit_namapkp").val();
  console.log('namapkp' , namapkp)
  let alamatpkp = $("#input_edit_alamatpkp").val();
  console.log('alamatpkp' , alamatpkp)
  let kotapkp = $("#input_edit_kotapkp").val();
  console.log('kotapkp' , kotapkp)
  // let atn = $("#input_edit_atn").val();
  // console.log('atn' , atn)

  console.log('==============================')

  let top = $("#input_edit_top").val();
  console.log('top' , top)
  let plafon = $("#input_edit_plafon").val();
  console.log('plafon' , plafon)
  let jeniscustomer = $("#input_edit_jeniscustomer").val();
  console.log('jeniscustomer' , jeniscustomer)
  let groupcustomer = $("#input_edit_groupcustomer").val();
  console.log('groupcustomer' , groupcustomer)
  let intcomp = $("#input_edit_intcomp").val();
  console.log('intcomp' , intcomp)
  let compcode = $("#input_edit_compcode").val();
  console.log('compcode' , compcode)
  let custcode = $("#input_edit_custcode").val();
  console.log('custcode' , custcode)

  let berikat = 0
  if (document.getElementById("input_edit_berikat").checked) {
    berikat= 1
  }
  console.log('berikat' , berikat)
  let blacklist = 0
  if (document.getElementById("input_edit_blacklist").checked) {
    blacklist= 1
  }
  console.log('blacklist' , blacklist)

  let jenis = 1

  // jenis,kode,bentukusaha, nama, alamat, kota, kodepos, negara, telp, fax, email, pph23,pph21,haripiutang, isaktif,att,attphone,attdepart, bank,accno,npwp,namapkp,alamatpkp,kotapkp,atn

  if (!kode) {
    alertify.warning("Kode harus diisi");
    return
  }
  if (!nama) {
    alertify.warning("Nama harus diisi");
    return
  }

  if (kota == 0 || !kota) {
    alertify.warning("Kota harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastercustomerspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      isppn, top, plafon, jeniscustomer, groupcustomer, intcomp , compcode , custcode, berikat, blacklist,
      jenis,kode,bentukusaha, nama, alamat, kota, kodepos, negara, telp, fax, email, pph23,pph21,haripiutang, isaktif,att,attphone,attdepart, npwp,namapkp,alamatpkp,kotapkp
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Customer telah diedit");
        loadAll()
        $("#formEdit").modal('toggle')
      }

    }})

}


function submitAdd () {

  let _token = $("#_token").val();
  let isppn = 0
  if (document.getElementById("input_add_isppn").checked) {
    isppn= 1
  }
  // console.log(document.getElementById("input_add_isppn").checked)
  // return
  console.log(isppn)
  let kode = $("#input_add_kode").val();
  console.log('kode' , kode)
  let bentukusaha = $("#input_add_bentukusaha").val();
  console.log('bentukusaha' , bentukusaha)
  let nama = $("#input_add_nama").val();
  console.log('nama' , nama)
  let alamat = $("#input_add_alamat").val();
  console.log('alamat' , alamat)
  let kota = $("#input_add_kota").val();
  console.log('kota' , kota)
  let kodepos = $("#input_add_kodepos").val();
  console.log('kodepos' , kodepos)
  let negara = $("#input_add_negara").val();
  console.log('negara' , negara)
  let telp = $("#input_add_telp").val();
  console.log('telp' , telp)
  let fax = $("#input_add_fax").val();
  console.log('fax' , fax)
  let email = $("#input_add_email").val();
  console.log('email' , email)
  let pph23 = $("#input_add_pph23").val();
  console.log('pph23' , pph23)
  let pph21 = $("#input_add_pph21").val();
  console.log('pph21' , pph21)
  let haripiutang = $("#input_add_haripiutang").val();
  console.log('haripiutang' , haripiutang)
  let isaktif = $("#input_add_isaktif").val();
  console.log('isaktif' , isaktif)
  let att = $("#input_add_att").val();
  console.log('att' , att)
  let attphone = $("#input_add_attphone").val();
  console.log('attphone' , attphone)
  if(!attphone){
    alertify.warning('Att Phone wajib diisi.')
  }

  let attdepart = $("#input_add_attdepart").val();
  console.log('attdepart' , attdepart)
  // let bank = $("#input_add_bank").val();
  // console.log('bank' , bank)
  // let accno = $("#input_add_accno").val();
  // console.log('accno' , accno)
  let npwp = $("#input_add_npwp").val();
  console.log('npwp' , npwp)
  let namapkp = $("#input_add_namapkp").val();
  console.log('namapkp' , namapkp)
  let alamatpkp = $("#input_add_alamatpkp").val();
  console.log('alamatpkp' , alamatpkp)
  let kotapkp = $("#input_add_kotapkp").val();
  console.log('kotapkp' , kotapkp)
  // let atn = $("#input_add_atn").val();
  // console.log('atn' , atn)

  console.log('==============================')

  let top = $("#input_add_top").val();
  console.log('top' , top)
  let plafon = $("#input_add_plafon").val();
  console.log('plafon' , plafon)
  let jeniscustomer = $("#input_add_jeniscustomer").val();
  console.log('jeniscustomer' , jeniscustomer)
  let groupcustomer = $("#input_add_groupcustomer").val();
  console.log('groupcustomer' , groupcustomer)
  let intcomp = $("#input_add_intcomp").val();
  console.log('intcomp' , intcomp)
  let compcode = $("#input_add_compcode").val();
  console.log('compcode' , compcode)
  let custcode = $("#input_add_custcode").val();
  console.log('custcode' , custcode)

  let berikat = 0
  if (document.getElementById("input_add_berikat").checked) {
    berikat= 1
  }
  console.log('berikat' , berikat)
  let blacklist = 0
  if (document.getElementById("input_add_blacklist").checked) {
    blacklist= 1
  }
  console.log('blacklist' , blacklist)

  let jenis = 1

  // jenis,kode,bentukusaha, nama, alamat, kota, kodepos, negara, telp, fax, email, pph23,pph21,haripiutang, isaktif,att,attphone,attdepart, bank,accno,npwp,namapkp,alamatpkp,kotapkp,atn

  if (!kode) {
    alertify.warning("Kode harus diisi");
    return
  }
  if (!nama) {
    alertify.warning("Nama harus diisi");
    return
  }

  if (kota == 0 || !kota) {
    alertify.warning("Kota harus diisi");
    return
  }
  //
  // if (!namaarea) {
  //   alertify.warning("Nama area harus diisi");
  //   return
  // }
  //
  $.ajax({
    url: "{!! url('mastercustomerspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      isppn, top, plafon, jeniscustomer, groupcustomer, intcomp , compcode , custcode, berikat, blacklist,
      jenis,kode,bentukusaha, nama, alamat, kota, kodepos, negara, telp, fax, email, pph23,pph21,haripiutang, isaktif,att,attphone,attdepart, npwp,namapkp,alamatpkp,kotapkp
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Customer telah ditambah");
        loadAll()
        $("#form").modal('toggle')
      }

    }})

  // console.log(kodearea, namaarea)
}

function submitAddDetailAkun () {

  let _token = $("#_token").val();
  let kode = $("#input_add_kodedetail").val();
  let nama = $("#input_add_perkiraandetail").val();

  if (!kode) {
    alertify.warning("Kode harus diisi");
    return
  }

  if (!nama) {
    alertify.warning("Perkiraan harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastercustomerspadddetailakun') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      nama
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Detail Akun telah ditambah");
        loadDetailAkun()
        $("#formDetailAkunAdd").modal('hide')
      }

    }})

  // console.log(kodearea, namaarea)
}

function buttonEdit (kode) {
  console.log(kode)
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('mastercustomerlistselect') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {

      console.log(res)
      console.log(res.listDataKota)
      let rowTable = `<option selected disabled value=0>Pilih Kota</option>`
      res.listDataKota.forEach((item, i) => {
        rowTable += `
          <option value="${item.KodeKota}">${item.NamaKota}</option>
        `
      });
      let rowTableJenis = `<option selected disabled value="">Pilih Jenis</option>`
      res.listDataJenis.forEach((item, i) => {
        rowTableJenis += `
          <option value="${item.KodeJenis}">${item.KodeJenis} - ${item.NamaJenis}</option>
        `
      });
      let rowTableGroup = `<option selected disabled value="">Pilih Group</option>`
      res.listDataGroup.forEach((item, i) => {
        rowTableGroup += `
          <option value="${item.KODEGROUPCUSTSUPP}">${item.KODEGROUPCUSTSUPP} - ${item.NAMAGROUPCUSTSUPP}</option>
        `
      });


      document.getElementById("input_edit_groupcustomer").innerHTML = rowTableGroup
      document.getElementById("input_edit_jeniscustomer").innerHTML = rowTableJenis
      document.getElementById("input_edit_kota").innerHTML = rowTable
    }})

  $.ajax({
    url: "{!! url('mastercustomerspdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_edit_kode").value = res[0].KODECUSTSUPP
      document.getElementById("input_edit_nama").value = res[0].NAMACUSTSUPP
      document.getElementById("input_edit_bentukusaha").value = res[0].USAHA
      if (Number(res[0].IsPpn)) {
        document.getElementById("input_edit_isppn").checked = true
      } else {
        document.getElementById("input_edit_isppn").checked = false
      }
      if (Number(res[0].BERIKAT)) {
        document.getElementById("input_edit_berikat").checked = true
      } else {
        document.getElementById("input_edit_berikat").checked = false
      }
      if (Number(res[0].pBlackList)) {
        document.getElementById("input_edit_blacklist").checked = true
      } else {
        document.getElementById("input_edit_blacklist").checked = false
      }
      document.getElementById("input_edit_alamat").value = res[0].ALAMAT1
      document.getElementById("input_edit_kota").value = res[0].Kota
      document.getElementById("input_edit_groupcustomer").value = res[0].Agent
      document.getElementById("input_edit_jeniscustomer").value = res[0].JenisCustSupp
      document.getElementById("input_edit_kodepos").value = res[0].KODEPOS
      document.getElementById("input_edit_telp").value = res[0].TELPON
      document.getElementById("input_edit_negara").value = res[0].NEGARA
      document.getElementById("input_edit_fax").value = res[0].FAX
      document.getElementById("input_edit_email").value = res[0].EMAIL
      document.getElementById("input_edit_pph23").value = res[0].NPPH23
      document.getElementById("input_edit_pph21").value = res[0].NPPH22
      document.getElementById("input_edit_haripiutang").value = res[0].HARIHUTPIUT
      document.getElementById("input_edit_isaktif").value = res[0].IsAktif
      document.getElementById("input_edit_att").value = res[0].Att
      document.getElementById("input_edit_attphone").value = res[0].AttPhone
      document.getElementById("input_edit_attdepart").value = res[0].AttDepart
      // document.getElementById("input_edit_accno").value = res[0].NoAcc
      // document.getElementById("input_edit_bank").value = res[0].bank
      // document.getElementById("input_edit_atn").value = res[0].ATN
      document.getElementById("input_edit_npwp").value = res[0].NPWP
      document.getElementById("input_edit_namapkp").value = res[0].NAMAPKP
      document.getElementById("input_edit_alamatpkp").value = res[0].ALAMATPKP1
      document.getElementById("input_edit_kotapkp").value = res[0].KOTAPKP

      document.getElementById("input_edit_compcode").value = res[0].CompCode
      document.getElementById("input_edit_custcode").value = res[0].CustCode
      document.getElementById("input_edit_intcomp").value = res[0].IntCode


    }})
    $("#formEdit").modal('toggle')
}


function buttonDelete (kode) {
  console.log(kode)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Supplier', 'Apakah yakin ingin menghapus Customer ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('mastercustomerspdelete') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            kode
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              loadAll()
              alertify.success("Customer telah dihapus");

            }
          }})
      }
    ,function(){
      console.log('no')
    });


}

function buttonDetailAkunDelete (kode, kodecust) {
  console.log(kode, kodecust)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Akun', 'Apakah yakin ingin menghapus Akun ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('mastercustomerspdeletedetailakun') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            kode,
            kodecust
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              loadDetailAkun();
              alertify.success("Customer telah dihapus");

            }
          }})
      }
    ,function(){
      console.log('no')
    });


}

function buttonDetailAkunAdd (){
  document.getElementById("input_add_kodedetail").value = noBuktiDetailTemp;
  document.getElementById("input_add_perkiraandetail").value = '';
  $("#formDetailAkunAdd").modal('toggle')
}

let perkiraanOldTemp = ''

function buttonDetailAkunEdit (perkiraan, kodecust){
  document.getElementById("input_edit_kodedetail").value = noBuktiDetailTemp;
  perkiraanOldTemp = perkiraan
  loadPerkiraanEdit(perkiraan, kodecust);
  $("#formDetailAkunEdit").modal('toggle')
}

function buttonDetailAkunSelectAdd (selectedPerkiraan) {
  $("#input_add_perkiraandetail").val(selectedPerkiraan);
  $("#input_edit_perkiraandetail").val(selectedPerkiraan);
  $("#formDetailAkunAddPerkiraan").modal("hide");

}

function buttonDetailAkunSelect (){
  loadPerkiraanDetail()
  $("#formDetailAkunAddPerkiraan").modal('toggle')
}

function loadPerkiraanEdit (perkiraan, kodecust){

    $.ajax({
    url: "{!! url('mastercustomerloadperkiraanedit') !!}",
    type: "get",
    async: false,
    data: {
      perkiraan,
      kodecust
    },
    success: function (res) {
      console.log(res);
      document.getElementById('input_edit_perkiraandetail').value = res[0].Perkiraan
    },
  });


}

function loadPerkiraanDetail () {
  let _token = $("#_token").val();

  $('#tabelDetailAkunAddPerkiraan').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastercustomerloaddetailperkiraan') !!}",
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
        <button class="btn btn-primary btn-sm" type="button" onclick="buttonDetailAkunSelectAdd('${item.Perkiraan}')"> + </i></button>
      </td>
      <td>${item.Perkiraan}</td>
      <td>${item.keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataDetailAkunAddPerkiraan").innerHTML = rowTable;
  $("#tabelDetailAkunAddPerkiraan").DataTable({
    "lengthChange": false,
    "paging": false,
  });
}

let noBuktiDetailTemp = ''

function buttonDetailAkun (kodeDetail){
  noBuktiDetailTemp = kodeDetail
  loadDetailAkun(kodeDetail)
  $("#formDetailAkun").modal('toggle')
}

function loadDetailAkun (kodeDetail) {

  console.log(kodeDetail);
  let _token = $("#_token").val();
  kodeDetailPerkiraan = kodeDetail;

  console.log(kodeDetailPerkiraan + '55555')

  $('#tabelDetailAkun').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastercustomerloaddetailakun') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
      kodeDetail: noBuktiDetailTemp
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
        <button class="btn btn-success btn-sm" type="button" onclick="buttonDetailAkunEdit('${item.Perkiraan}', '${item.KodeCustSupp}' )"><i class="bi bi-pen"></i></button>
        <button class="btn btn-danger btn-sm" type="button" onclick="buttonDetailAkunDelete('${item.Perkiraan}', '${item.KodeCustSupp}')"><i class="bi bi-trash"></i></button>
      </td>
      <td>${item.KodeCustSupp}</td>
      <td>${item.Perkiraan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataDetailAkun").innerHTML = rowTable;
  $("#tabelDetailAkun").DataTable({
    "lengthChange": false,
    "paging": false,
  });
}

window.onload = function(){
  loadAll();
};

</script>




@endsection
