@extends('master.newmaster')
@section('buttons')

@endsection
@section('content')

<div class="container-fluid">

  <!-- <div id="qrcode"></div> -->
<div class="row mt-4">
      <div class="col-6 text-left">
        <h2 style="margin-top:-85px;">Master Barang</h2>
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
          Add Barang
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
                      <thead style='white-space:nowrap;'  id='theadCustom' class="text-center">
                        <tr>
                          <th scope="col">Actions</th>
                          <th scope="col">Kode Brg</th>
                          <th scope="col">Nama Brg</th>
                          <th scope="col">Agen</th>
                          <th scope="col">Group</th>
                          <th scope="col">Header Group</th>
                          <th scope="col">SubJenis</th>
                          <th scope="col">Jenis Barang</th>
                          <th scope="col">Nama Merk</th>
                          <th scope="col">SKU</th>
                          <th scope="col">Sat 1</th>
                          <th scope="col">Sat 2</th>
                          <th scope="col">Part Number</th>
                        </tr>
                      </thead>


                      <tbody style='white-space:nowrap;' id="tabel_data" class="text-left" >
                        
                      </tbody>


                    </table>
              </div>
            </div>
          </div>


</div>


<!-- start modal add -->
<div class="modal fade"  id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document">
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
            <div class="col-4">

            </div>

            <div class="col-3">
              <div class="form-group ">
              <input type="checkbox" id="input_add_isagen" name="" value="">
              <span class="text-left">Keagenan</span>
            </div>
            </div>

          </div>
            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Group</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_kodegroup" value='BJ' disabled>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <span>Barang Jadi</span>
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">HeadGroup</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <select id="input_add_kodeheadgroup" onchange="changeInputHeadGroup()" class="form-control" aria-label="Default select example">
                    <option selected value="" disabled>Pilih HeadGroup</option>
                  </select>
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">SubGroup</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <select id="input_add_kodesubgroup" onchange="changeInputSubGroup()"  class="form-control" aria-label="Default select example" >
                    <option selected disabled value="">Pilih SubGroup</option>
                  </select>
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">SubKategori</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <select id="input_add_kodesubkategori" onchange="changeInputSubKategori()" class="form-control" aria-label="Default select example" >
                    <option selected disabled value="">Pilih Subkategori</option>
                  </select>
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Merk</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <select id="input_add_kodemerk" onchange="changeInputMerk()" class="form-control" aria-label="Default select example">
                    <option selected disabled value="">Pilih Merk</option>
                  </select>
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kode Barang</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_kodebarang" disabled >
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Nama Barang</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_namabarang" >
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Nama Barang 2</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_namabarang2" >
                </div>
              </div>

            </div>

            <br/>

            <div class="row">
              <div class="col-2 text-left">
                <!-- <div class="form-group text-left">
                  <label class="text-left">Satuan</label>
                </div> -->
              </div>
              <div class="col-3">
                <div class="form-group">
                  <b>Satuan</b>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <b>Isi</b>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <b>Harga</b>
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Satuan</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_satuan" >
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="number" value=0 min=0 class="form-control text-right" id="input_add_isi" >
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="number" value=1000 min=0 class="form-control text-right" id="input_add_harga" >
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Satuan 2</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_satuan2" >
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="number" value=0 min=0 class="form-control text-right" id="input_add_isi2" >
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="number" value=0 min=0 class="form-control text-right" id="input_add_harga2" >
                </div>
              </div>

            </div>

            <!-- <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Satuan 3</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_satuan3" >
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="number" value=0 min=0 class="form-control text-right" id="input_add_isi3" >
                </div>
              </div>

            </div> -->

            <br/>

            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Qty Min</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="number" value=0 min=0 class="form-control text-right" id="input_add_qtymin" >
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Qty Max</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="number" value=0 min=0 class="form-control text-right" id="input_add_qtymax" >
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Toleransi</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="number" value=0 min=0 class="form-control text-right" id="input_add_toleransi" >
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Berat/Volume</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <select id="input_add_isberat" class="form-control" aria-label="Default select example">
                    <option value=0>Volume</option>
                    <option value=1>Berat</option>
                  </select>
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Status</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <select id="input_add_isaktif" class="form-control" aria-label="Default select example">
                    <option value=1>Aktif</option>
                    <option value=0>NonAktif</option>
                  </select>
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Berat/Volume</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="number" class="form-control text-right" id="input_add_beratvolume" value=0.00>
                </div>
              </div>

            </div>

            <div class="row">

              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Part Number</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control text-left" id="input_add_partnumber" >
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Lokasi</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group">
                  <input type="text" class="form-control text-left" id="input_add_lokasi" >
                </div>
              </div>


            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kode SKU</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control text-leftt" id="input_add_kodesku" >
                </div>
              </div>
              <div class="col-3">
                <div class="form-group ">
                <input type="checkbox" id="input_add_iskontrak" name="" value="">
                <span class="text-left">Kontrak</span>
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
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document">
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
            <div class="col-4">

            </div>

            <div class="col-3">
              <div class="form-group ">
              <input type="checkbox" id="input_edit_isagen" name="" value="">
              <span class="text-left">Keagenan</span>
            </div>
            </div>

          </div>
            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Group</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_kodegroup" value='BJ' disabled>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <span>Barang Jadi</span>
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">HeadGroup</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <select disabled id="input_edit_kodeheadgroup" onchange="changeInputHeadGroup()" class="form-control" aria-label="Default select example">
                    <option selected value="" disabled>Pilih HeadGroup</option>
                  </select>
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">SubGroup</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <select disabled id="input_edit_kodesubgroup" onchange="changeInputSubGroup()"  class="form-control" aria-label="Default select example" >
                    <option selected disabled value="">Pilih SubGroup</option>
                  </select>
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">SubKategori</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <select disabled id="input_edit_kodesubkategori" onchange="changeInputSubKategori()" class="form-control" aria-label="Default select example" >
                    <option selected disabled value="">Pilih Subkategori</option>
                  </select>
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Merk</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <select disabled id="input_edit_kodemerk" onchange="changeInputMerk()" class="form-control" aria-label="Default select example">
                    <option selected disabled value="">Pilih Merk</option>
                  </select>
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kode Barang</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_kodebarang" disabled >
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Nama Barang</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_namabarang" >
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Nama Barang 2</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_namabarang2" >
                </div>
              </div>

            </div>

            <br/>

            <div class="row">
              <div class="col-2 text-left">
                <!-- <div class="form-group text-left">
                  <label class="text-left">Satuan</label>
                </div> -->
              </div>
              <div class="col-3">
                <div class="form-group">
                  <b>Satuan</b>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <b>Isi</b>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <b>Harga</b>
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Satuan</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_satuan" >
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="number" value=0 min=0 class="form-control text-right" id="input_edit_isi" >
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="number" value=1000 min=0 class="form-control text-right" id="input_edit_harga" >
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Satuan 2</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_satuan2" >
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="number" value=0 min=0 class="form-control text-right" id="input_edit_isi2" >
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="number" value=0 min=0 class="form-control text-right" id="input_edit_harga2" >
                </div>
              </div>

            </div>

            <!-- <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Satuan 3</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_satuan3" >
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="number" value=0 min=0 class="form-control text-right" id="input_edit_isi3" >
                </div>
              </div>

            </div> -->

            <br/>

            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Qty Min</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="number" value=0 min=0 class="form-control text-right" id="input_edit_qtymin" >
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Qty Max</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="number" value=0 min=0 class="form-control text-right" id="input_edit_qtymax" >
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Toleransi</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="number" value=0 min=0 class="form-control text-right" id="input_edit_toleransi" >
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Berat/Volume</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <select id="input_edit_isberat" class="form-control" aria-label="Default select example">
                    <option value=0>Volume</option>
                    <option value=1>Berat</option>
                  </select>
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Status</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <select id="input_edit_isaktif" class="form-control" aria-label="Default select example">
                    <option value=1>Aktif</option>
                    <option value=0>NonAktif</option>
                  </select>
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Berat/Volume</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="number" class="form-control text-right" id="input_edit_beratvolume" value=0.00>
                </div>
              </div>

            </div>

            <div class="row">

              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Part Number</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control text-left" id="input_edit_partnumber" >
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Lokasi</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group">
                  <input type="text" class="form-control text-left" id="input_edit_lokasi" >
                </div>
              </div>


            </div>
            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kode SKU</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control text-left" id="input_edit_kodesku" >
                </div>
              </div>
              <div class="col-3">
                <div class="form-group ">
                <input type="checkbox" id="input_edit_iskontrak" name="" value="">
                <span class="text-left">Kontrak</span>
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




<!-- start modal harga -->
<div class="modal fade"  id="formHarga" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Harga</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <div class="container-fluid">
          <div class="row">

            <input type="hidden" id="input_harga_kodegroup" value="" />
            <div class="col-2">
              <div class="form-group">
                <label>Kode Barang</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_harga_kodebarang" placeholder="Kode Barang" disabled>
              </div>
            </div>
            
            
            <div class="col-2 ml-auto text-right">
              <button type="button" class="btn btn-primary" onclick="buttonAddHarga()" class="btn btn-secondary"  >Add Harga</button>
            </div>

          </div>
    </div>

    <!-- ADD SUBGROUP -->

    <div id="addHarga" class="container-fluid showhide">
            <!-- <div class="line"></div> -->
            <div class="row">
              <div class="col-4">
                <h4>Add Harga</h4>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Supplier</label>
              </div>
              </div>
              <div class="col-4">

                <select id="input_harga_add_kodesupplier" class="form-control" aria-label="Default select example">
                </select>
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Harga1</label>
              </div>
              </div>
              <div class="col-4">
                <input id="input_harga_add_harga" value=0.00 min=0 type="number" class="form-control text-right">
              </div>
              <!-- <div class="col-2">
                <div class="form-group">
                <label>Perkiraan Persiapan</label>
              </div>
              </div>
              <div class="col-4">

                <select id="input_harga_add_perkpers" class="form-control" aria-label="Default select example">
                  <option selected>Open this select menu</option>
                  <option value="1">One</option>
                  <option value="2">Two</option>
                  <option value="3">Three</option>
                </select>
              </div> -->

            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Harga2</label>
              </div>
              </div>
              <div class="col-4">
                <input id="input_harga_add_harga2" value=0.00 min=0  type="number" class="form-control text-right">
              </div>
              <!-- <div class="col-2">
                <div class="form-group">
                <label>Perkiraan Jual</label>
              </div>
              </div>
              <div class="col-4">

                <select id="input_harga_add_perkjual" class="form-control" aria-label="Default select example">
                  <option selected>Open this select menu</option>
                  <option value="1">One</option>
                  <option value="2">Two</option>
                  <option value="3">Three</option>
                </select>
              </div> -->
            </div>
            <!-- <div class="row">
              <div class="col-1">
                <div class="form-group">
                <label>Perkiraan Persiapan</label>
              </div>
              </div>
              <div class="col-3">
                <select id="input_harga_add_perkpers" class="form-control" aria-label="Default select example">
                  <option selected>Open this select menu</option>
                  <option value="1">One</option>
                  <option value="2">Two</option>
                  <option value="3">Three</option>
                </select>
              </div>
              <div class="col-1">
                <div class="form-group">
                <label>Perkiraan Jual</label>
              </div>
              </div>
              <div class="col-3">

                <select id="input_harga_add_perkjual" class="form-control" aria-label="Default select example">
                  <option selected>Open this select menu</option>
                  <option value="1">One</option>
                  <option value="2">Two</option>
                  <option value="3">Three</option>
                </select>
              </div>
            </div> -->

            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="closeShowHarga()" >Batal</button>
                <button type="button" onclick="submitAddHarga()" class="btn btn-primary" >Add</button>
              </div>

            </div>
          </div>

    <!-- END ADD SUBGROUP -->

    <!-- EDIT SUBGROUP -->

    <div id="editHarga" class="container-fluid showhide">
            <!-- <div class="line"></div> -->
            <div class="row">
              <div class="col-4">
                <h4>Edit Harga</h4>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Supplier</label>
              </div>
              </div>
              <div class="col-4">

                <select id="input_harga_edit_kodesupplier" disabled class="form-control" aria-label="Default select example">
                </select>
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Harga1</label>
              </div>
              </div>
              <div class="col-4">
                <input id="input_harga_edit_harga" value=0.00 min=0 type="number" class="form-control text-right">
              </div>
              <!-- <div class="col-2">
                <div class="form-group">
                <label>Perkiraan Persiapan</label>
              </div>
              </div>
              <div class="col-4">

                <select id="input_harga_edit_perkpers" class="form-control" aria-label="Default select example">
                  <option selected>Open this select menu</option>
                  <option value="1">One</option>
                  <option value="2">Two</option>
                  <option value="3">Three</option>
                </select>
              </div> -->

            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Harga2</label>
              </div>
              </div>
              <div class="col-4">
                <input id="input_harga_edit_harga2" value=0.00 min=0  type="number" class="form-control text-right">
              </div>
              <!-- <div class="col-2">
                <div class="form-group">
                <label>Perkiraan Jual</label>
              </div>
              </div>
              <div class="col-4">

                <select id="input_harga_add_perkjual" class="form-control" aria-label="Default select example">
                  <option selected>Open this select menu</option>
                  <option value="1">One</option>
                  <option value="2">Two</option>
                  <option value="3">Three</option>
                </select>
              </div> -->
            </div>
            <!-- <div class="row">
              <div class="col-1">
                <div class="form-group">
                <label>Perkiraan Persiapan</label>
              </div>
              </div>
              <div class="col-3">
                <select id="input_harga_add_perkpers" class="form-control" aria-label="Default select example">
                  <option selected>Open this select menu</option>
                  <option value="1">One</option>
                  <option value="2">Two</option>
                  <option value="3">Three</option>
                </select>
              </div>
              <div class="col-1">
                <div class="form-group">
                <label>Perkiraan Jual</label>
              </div>
              </div>
              <div class="col-3">

                <select id="input_harga_add_perkjual" class="form-control" aria-label="Default select example">
                  <option selected>Open this select menu</option>
                  <option value="1">One</option>
                  <option value="2">Two</option>
                  <option value="3">Three</option>
                </select>
              </div>
            </div> -->

            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="closeShowHarga()" >Batal</button>
                <button type="button" onclick="submitEditHarga()" class="btn btn-primary" >Edit</button>
              </div>

            </div>
          </div>

    <!-- END EDIT SUBGROUP -->




        <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_harga" class="table table-bordered table-striped"  >
              <thead id='theadCustom' class="text-center">
                <tr>
                  <th scope="col">Actions</th>
                  <th scope="col">Supplier</th>
                  <th scope="col">Harga 1</th>
                  <th scope="col">Harga 2</th>

                </tr>
              </thead>


              <tbody id="tabel_data_harga" class="text-left" >

                <tr >

                  <td colspan=3></td>


                    <td class="text-center">
                      <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                      <button class="btn btn-success btn-sm" type="button" ><i class="bi bi-pen"></i></button>
                      <button class="btn btn-danger btn-sm" type="button" ><i class="bi bi-trash"></i></button>
                    </td>
              </tr>
              </tbody>


            </table>
          </div>
            <!-- <button onclick="buttonSubKategori()">tes</button> -->


    </div>
  </div>
  <!-- <div class="modal-footer">
     
    <button type="button" class="btn btn-primary" onclick="">Submit</button>
  </div> -->
</div>
</div>
</div>
<!-- End modal harga-->



@endsection

@section('js')
<script type="text/javascript">

let dataRefresh = []

let listSelectHeadGroup = []
let listSelectSubGroup = []
let listSelectSubKategori = []

function loadAll () {
  $('#tabel').DataTable().destroy();

  $('#tabel').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "{!! url('masterbarangloadall') !!}",
      type: "GET",
    },
    columns: [
      {
        data: "KODEBRG",
        render: function(data, type, row) {
          return `
            <div class="action-buttons text-center" style="position: relative;">
              <button class="btn btn-success btn-sm hover-tooltip" type="button" onclick="buttonEdit('${row.KODEBRG}')" data-tooltip="Edit Barang">
                <i class="bi bi-pen"></i>
              </button>
              <button class="btn btn-danger btn-sm hover-tooltip" type="button" onclick="buttonDelete('${row.KODEBRG}')" data-tooltip="Delete Barang">
                <i class="bi bi-trash"></i>
              </button>
              <button class="btn btn-primary btn-sm hover-tooltip" type="button" onclick="buttonHarga('${row.KODEBRG}')" data-tooltip="Harga Barang">
                <i class="bi bi-list"></i>
              </button>
            </div>
          `;
        },
        orderable: false,
        searchable: false,
        className: "text-center"
      },
      { data: "KODEBRG" },
      { data: "NAMABRG" },
      {
        data: "pAgen",
        render: function(data) {
          if (data == 0) {
            return '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>';
          } else {
            return '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>';
          }
        },
        orderable: false,
        searchable: false,
        className: "text-center"
      },
      { data: "nNAMAGROUP" },
      { data: "nNAMAHDGROUP" },
      { data: "nNAMASUBGROUP" },
      { data: "nNAMASUBKATEGORI" },
      { data: "nNAMAMERK" },
      { data: "SKU" },
      { data: "SAT1" },
      { data: "SAT2" },
      { data: "PartNumber" },
    ],
    lengthChange: true,
    paging: true
  });
}

function buttonAdd () {
  document.getElementById("input_add_kodebarang").value= ''
  document.getElementById("input_add_namabarang").value= ''
  document.getElementById("input_add_namabarang2").value= ''
  document.getElementById("input_add_satuan").value = ''
  document.getElementById("input_add_satuan2").value= ''
  // let satuan3 document.getElementById("input_add_satuan3").value=
  document.getElementById("input_add_isi").value= '1.00'
  document.getElementById("input_add_isi2").value= '1.00'
  document.getElementById("input_add_harga").value= '0.00'
  document.getElementById("input_add_harga2").value= '0.00'
  // let isi3 document.getElementById("input_add_isi3").value=
  document.getElementById("input_add_qtymax").value= '0.00'
  document.getElementById("input_add_qtymin").value= '0.00'
  document.getElementById("input_add_toleransi").value= '0.00'
  document.getElementById("input_add_isberat").value= '0.00'
  document.getElementById("input_add_isaktif").value= 1
  document.getElementById("input_add_beratvolume").value= '0.00'
  document.getElementById("input_add_partnumber").value= ''
  document.getElementById("input_add_lokasi").value= ''
  document.getElementById("input_add_kodesku").value= ''

    $.ajax({
      url: "{!! url('masterbaranglistselect') !!}",
      type: "get",
      async: false,
      data: {
      },
      success: function(res) {

        console.log(res)
        // console.log(res.listHeadGroup)
        // console.log(res.listSubGroup)
        // console.log(res.listSubKategori);

        listSelectHeadGroup = res.listHeadGroup
        listSelectSubGroup = res.listSubGroup
        listSelectSubKategori = res.listSubKategori
        let rowSelectMerk = `<option selected disabled value="">Pilih Merk</option>`
        res.listMerk.forEach((item, i) => {
          rowSelectMerk += `
            <option value="${item.KODEMERK}">${item.NAMAMERK}</option>
          `
        });
        document.getElementById("input_add_kodemerk").innerHTML = rowSelectMerk

      }})

      console.log('================')
      console.log(listSelectHeadGroup)
      console.log(listSelectSubGroup)
      console.log(listSelectSubKategori)

      // <option selected value="0">Pilih Merk</option>
      // <option value="1">One</option>
      // <option value="2">Two</option>
      // <option value="3">Three</option>

      let rowSelect = `<option selected disabled value="">Pilih HeadGroup</option>`
      listSelectHeadGroup.forEach((item, i) => {
        rowSelect += `
          <option value="${item.KODEHDGRP}">${item.NAMAHDGRP}</option>
        `
      });
      document.getElementById("input_add_kodeheadgroup").innerHTML = rowSelect
      document.getElementById("input_add_kodesubgroup").innerHTML =  `<option selected disabled value="">Pilih SubGroup</option>`
      document.getElementById("input_add_kodesubkategori").innerHTML =  `<option selected disabled value="">Pilih SubKategori</option>`

    $("#form").modal('toggle')
}

function changeInputHeadGroup () {
  // console.log('tes')
  let valueGroup = $("#input_add_kodegroup").val();
  let valueHeadGroup = $("#input_add_kodeheadgroup").val();
  console.log(valueGroup , valueHeadGroup)

  let temp = listSelectSubGroup.filter (function (el) {
    return el.KodeGrp == valueGroup && el.KodeHDGrp == valueHeadGroup
  })

  let rowSelect = `<option selected disabled value="">Pilih SubGroup</option>`
  temp.forEach((item, i) => {
    rowSelect += `
    <option value="${item.KodeSubGrp}">${item.NamaSubGrp}</option>
    `
  });

  document.getElementById("input_add_kodesubgroup").innerHTML = rowSelect
  document.getElementById("input_add_kodesubkategori").innerHTML =  `<option selected disabled value="">Pilih SubKategori</option>`
  document.getElementById("input_add_kodebarang").value = ''


  console.log(temp)
}

function changeInputSubGroup () {
  let valueGroup = $("#input_add_kodegroup").val();
  let valueHeadGroup = $("#input_add_kodeheadgroup").val();
  let valueSubGroup = $("#input_add_kodesubgroup").val()

  let temp = listSelectSubKategori.filter(function (el){
    return el.KodeGrp == valueGroup && el.HDGROUP == valueHeadGroup && el.KodeSubGrp == valueSubGroup
  })
  let rowSelect = `<option selected disabled value="">Pilih SubKategori</option>`
  temp.forEach((item, i) => {
    rowSelect += `
    <option value="${item.Urut}">${item.Keterangan}</option>
    `
  });

  document.getElementById("input_add_kodesubkategori").innerHTML = rowSelect
  document.getElementById("input_add_kodebarang").value = ''

}

function changeInputSubKategori () {
  let valueMerk = $("#input_add_kodemerk").val();
  if (valueMerk) {
    let valueGroup = $("#input_add_kodegroup").val();
    let valueHeadGroup = $("#input_add_kodeheadgroup").val();
    let valueSubGroup = $("#input_add_kodesubgroup").val()
    let valueSubKategori = $("#input_add_kodesubkategori").val()

    console.log(valueGroup)
    console.log(valueHeadGroup);
    // console.log(Number(valueHeadGroup))
    console.log(valueSubGroup);
    // console.log(Number(valueSubGroup));
    console.log(valueSubKategori);
    // console.log(Number(valueSubKategori));

    let kodebarang = `2` +valueHeadGroup +valueSubGroup +valueSubKategori + valueMerk
    console.log(kodebarang)



    let _token = $("#_token").val();
    $.ajax({
      url: "{!! url('masterbarangspcheckdbbarang') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        kodebarang: kodebarang + '%'
      },
      success: function(res) {

        console.log(res)

        if(!res.length) {
          console.log('+ 0001')
          kodebarang += '0001'
        } else {
          console.log(res[0].KODEBRG)
          let tes = res[0].KODEBRG
          // let temp = tes.slice(-4)
          let temp = tes.slice(kodebarang.length , kodebarang.length + 4)
          console.log(temp)
          console.log(Number(temp))
          let tempUrut = '0000' + (Number(temp) + 1)
          console.log(tempUrut.slice(-4))
          kodebarang += tempUrut.slice(-4)
        }

      }})

    document.getElementById("input_add_kodebarang").value = kodebarang
  }

}


function changeInputMerk () {

  let valueSubKategori = $("#input_add_kodesubkategori").val()
  if (valueSubKategori) {
    let valueGroup = $("#input_add_kodegroup").val();
    let valueHeadGroup = $("#input_add_kodeheadgroup").val();
    let valueSubGroup = $("#input_add_kodesubgroup").val()
    // let valueSubKategori = $("#input_add_kodesubkategori").val()
    let valueMerk = $("#input_add_kodemerk").val();

    let kodebarang = `2` +valueHeadGroup +valueSubGroup +valueSubKategori + valueMerk
    console.log(kodebarang)

    let _token = $("#_token").val();
    $.ajax({
      url: "{!! url('masterbarangspcheckdbbarang') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        kodebarang: kodebarang + '%'
      },
      success: function(res) {

        console.log(res)
        if(!res.length) {
          console.log('+ 0001')
          kodebarang += '0001'
        } else {
          console.log(res[0].KODEBRG)
          let tes = res[0].KODEBRG
          // let temp = tes.slice(-4)
          let temp = tes.slice(kodebarang.length , kodebarang.length + 4)
          console.log(temp)
          console.log(Number(temp))
          let tempUrut = '0000' + (Number(temp) + 1)
          console.log(tempUrut.slice(-4))
          kodebarang += tempUrut.slice(-4)
        }

      }})


    document.getElementById("input_add_kodebarang").value = kodebarang



  }
}

function buttonEdit (kodebarang) {
  console.log(kodebarang)

  $.ajax({
    url: "{!! url('masterbaranglistselect') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {

      console.log(res)
      // console.log(res.listHeadGroup)
      // console.log(res.listSubGroup)
      // console.log(res.listSubKategori);

      listSelectHeadGroup = res.listHeadGroup
      listSelectSubGroup = res.listSubGroup
      listSelectSubKategori = res.listSubKategori
      let rowSelectMerk = `<option selected disabled value="">Pilih Merk</option>`
      res.listMerk.forEach((item, i) => {
        rowSelectMerk += `
          <option value="${item.KODEMERK}">${item.NAMAMERK}</option>
        `
      });
      document.getElementById("input_edit_kodemerk").innerHTML = rowSelectMerk

    }})

  $.ajax({
    url: "{!! url('masterbarangspdetail') !!}",
    type: "get",
    async: false,
    data: {
      // _token : _token,
      kodebarang

    },
    success: function(res) {

    console.log(res)

    document.getElementById("input_edit_kodebarang").value = res[0].KODEBRG
    document.getElementById("input_edit_namabarang").value = res[0].NAMABRG
    document.getElementById("input_edit_namabarang2").value = res[0].NamaBrg2
    document.getElementById("input_edit_satuan").value = res[0].SAT1
    document.getElementById("input_edit_satuan2").value = res[0].SAT2
    document.getElementById("input_edit_isi").value = res[0].ISI1
    document.getElementById("input_edit_isi2").value = res[0].ISI2
    document.getElementById("input_edit_harga").value = res[0].Hrg1_1
    document.getElementById("input_edit_harga2").value = res[0].Hrg2_1
    document.getElementById("input_edit_qtymin").value = res[0].QntMin
    document.getElementById("input_edit_qtymax").value = res[0].QntMax
    document.getElementById("input_edit_toleransi").value = res[0].Tolerate
    document.getElementById("input_edit_isberat").value = res[0].pBerat
    document.getElementById("input_edit_beratvolume").value = res[0].Berat
    document.getElementById("input_edit_isaktif").value = res[0].ISAKTIF
    document.getElementById("input_edit_partnumber").value = res[0].PartNumber
    document.getElementById("input_edit_lokasi").value = res[0].Mlokasi
    document.getElementById("input_edit_kodesku").value = res[0].SKU
    document.getElementById("input_edit_kodemerk").value = res[0].KodeMerk

    if (Number(res[0].pAgen)) {
      document.getElementById("input_edit_isagen").checked = true
    } else {
      document.getElementById("input_edit_isagen").checked = false
    }

    if (Number(res[0].pKontrak)) {
      document.getElementById("input_edit_iskontrak").checked = true
    } else {
      document.getElementById("input_edit_iskontrak").checked = false
    }

    console.log('================')
    // console.log(listSelectHeadGroup)
    // console.log(listSelectSubGroup)
    console.log(listSelectSubKategori)

    let tempHeadGroup = listSelectHeadGroup.filter (function (el) {
      return el.KODEGRP == res[0].KODEGRP  && el.KODEHDGRP == res[0].KodeHdGrp
    })
    let tempSubGroup = listSelectSubGroup.filter (function (el) {
      return el.KodeGrp == res[0].KODEGRP  && el.KodeHDGrp == res[0].KodeHdGrp && el.KodeSubGrp == res[0].KODESUBGRP
    })
    let tempSubKategori = listSelectSubKategori.filter (function (el) {
      return el.KodeGrp == res[0].KODEGRP  && el.HDGROUP == res[0].KodeHdGrp && el.KodeSubGrp == res[0].KODESUBGRP && el.Urut == res[0].KODESUBKATEGORI
    })

    console.log(tempHeadGroup)
    console.log(tempSubGroup);
    console.log('********'
    )
    console.log(tempSubKategori)

    let selectHeadGroup = `<option selected value="${tempHeadGroup[0].KODEHDGRP}">${tempHeadGroup[0].NAMAHDGRP}</option>`

    document.getElementById("input_edit_kodeheadgroup").innerHTML = selectHeadGroup

    let selectSubGroup = `<option selected value="${tempSubGroup[0].KodeSubGrp}">${tempSubGroup[0].NamaSubGrp}</option>`

    document.getElementById("input_edit_kodesubgroup").innerHTML = selectSubGroup

    let selectSubKategori = `<option selected value="${tempSubKategori[0].Urut}">${tempSubKategori[0].Keterangan}</option>`

    document.getElementById("input_edit_kodesubkategori").innerHTML = selectSubKategori

    }})


    // let selectKodeHeadGroup = ``
    // let selectKodeSubGroup = ``
    // let selectKodeSubKategori = ``
    // let selectKodeMerk = ``
    // document.getElementById("input_edit_kodeheadgroup").innerHTML = `<option selected disabled value="">Pilih SubGroup</option>`
    // document.getElementById("input_edit_kodesubgroup").innerHTML =  `<option selected disabled value="">Pilih SubGroup</option>`
    // document.getElementById("input_edit_kodesubkategori").innerHTML =  `<option selected disabled value="">Pilih SubKategori</option>`

    $("#formEdit").modal('toggle')
}

function buttonAddHarga () {
  $('.showhide').hide();
  $('#addHarga').show()
}

function buttonEditHarga (kodebarang , kodesupplier) {
  console.log('buttonEditHarga')
  console.log(kodebarang,kodesupplier)
  $('.showhide').hide();
  $.ajax({
    url: "{!! url('masterbarangspdetailhargadetail') !!}",
    type: "get",
    async: false,
    data: {
      kodebarang,
      kodesupplier,
    },
    success: function(res) {
      console.log(res)
      document.getElementById("input_harga_edit_kodesupplier").value = res[0].KODEJENISCUSTSUPP
      document.getElementById("input_harga_edit_harga").value = res[0].HARGA1
      document.getElementById("input_harga_edit_harga2").value = res[0].HARGA2

    }})

  $('#editHarga').show()
}

function closeShowHarga () {
  $('.showhide').hide();
}

function submitEditHarga () {
  let _token = $("#_token").val();
console.log('submitEditHarga')
let kodebarang = $("#input_harga_kodebarang").val();
let kodesupplier = $("#input_harga_edit_kodesupplier").val();
let harga = $("#input_harga_edit_harga").val();
let harga2 = $("#input_harga_edit_harga2").val();

console.log('kodebarang' , kodebarang)
console.log('kodesupplier' , kodesupplier)
console.log('harga' , harga)
console.log('harga2' , harga2)

$.ajax({
  url: "{!! url('masterbarangspeditharga') !!}",
  type: "post",
  async: false,
  data: {
    _token,
    kodebarang,
    kodesupplier,
    harga,
    harga2
  },
  success: function(res) {
    console.log(res)
    $('.showhide').hide();
    refreshTableHarga(kodebarang)

  }})

}


function submitAddHarga () {

    let _token = $("#_token").val();
  console.log('submitAddHarga')
  let kodebarang = $("#input_harga_kodebarang").val();
  let kodesupplier = $("#input_harga_add_kodesupplier").val();
  let harga = $("#input_harga_add_harga").val();
  let harga2 = $("#input_harga_add_harga2").val();

  console.log('kodebarang' , kodebarang)
  console.log('kodesupplier' , kodesupplier)
  console.log('harga' , harga)
  console.log('harga2' , harga2)


  $.ajax({
    url: "{!! url('masterbarangspaddharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang,
      kodesupplier,
      harga,
      harga2
    },
    success: function(res) {
      console.log(res)
      $('.showhide').hide();
      refreshTableHarga(kodebarang)


    }})

}

function buttonDelete (kodebarang) {
  console.log('buttonDeleteHarga')
  console.log(kodebarang)

  let _token = $("#_token").val();

  alertify.confirm('Hapus Harga', 'Apakah yakin ingin menghapus Barang ' + kodebarang + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masterbarangspdelete') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            kodebarang,
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              loadAll()
              alertify.success("Barang telah dihapus");
          }
          }})
      }
    ,function(){
      console.log('no')
    });
}


function buttonDeleteHarga (kodebarang, kodesupplier) {
  console.log('buttonDeleteHarga')
  console.log(kodebarang, kodesupplier)

  let _token = $("#_token").val();


  alertify.confirm('Hapus Harga', 'Apakah yakin ingin menghapus Harga ' + kodebarang + ' - ' + kodesupplier + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masterbarangspdeleteharga') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            kodebarang,
            kodesupplier
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              refreshTableHarga(kodebarang)
              alertify.success("Harga telah dihapus");




          }
          }})
      }
    ,function(){
      console.log('no')
    });

}

function refreshTableHarga (kodebarang) {
  $.ajax({
    url: "{!! url('masterbaranglistsupplier') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {

      console.log(res)

      // input_harga_add_kodesupplier
      let rowSelect = `<option selected disabled value="">Pilih Supplier</option>`
      res.forEach((item, i) => {
        rowSelect += `
        <option value="${item.KodeJenis}">${item.KodeJenis} - ${item.NamaJenis}</option>
        `
      });

      document.getElementById("input_harga_add_kodesupplier").innerHTML = rowSelect
      document.getElementById("input_harga_edit_kodesupplier").innerHTML = rowSelect

    }})

    $.ajax({
      url: "{!! url('masterbarangspdetailharga') !!}",
      type: "get",
      async: false,
      data: {
        kodebarang
      },
      success: function(res) {
        console.log('res detail harga')
        console.log(res)

        let rowTable = ''

        res.forEach((item, i) => {
          rowTable += `
          <tr>
          <td class="text-center">
            <button class="btn btn-success btn-sm hover-tooltip" onclick="buttonEditHarga('${kodebarang}' , '${item.KODEJENISCUSTSUPP}')" type="button" data-tooltip="Edit Harga" ><i class="bi bi-pen"></i></button> 
            <button class="btn btn-danger btn-sm hover-tooltip" onclick="buttonDeleteHarga('${kodebarang}', '${item.KODEJENISCUSTSUPP}')" type="button" data-tooltip="Delete Harga" ><i class="bi bi-trash"></i></button> 
          </td>
          <td>${item.KODEJENISCUSTSUPP}</td>
          <td>${item.HARGA1}</td>
          <td>${item.HARGA2}</td>
          </tr>
          `
        });

        if (!res.length) {
          rowTable = `<td colspan=4 class="text-center">Belum ada data harga</td>`
        }

        document.getElementById("tabel_data_harga").innerHTML = rowTable
        document.getElementById("input_harga_kodebarang").value = kodebarang


        // input_harga_add_kodesupplier


      }})
}


function buttonHarga (kodebarang) {
  $('.showhide').hide();

  // masterbaranglistsupplier

  $.ajax({
    url: "{!! url('masterbaranglistsupplier') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {

      console.log(res)

      // input_harga_add_kodesupplier
      let rowSelect = `<option selected disabled value="">Pilih Supplier</option>`
      res.forEach((item, i) => {
        rowSelect += `
        <option value="${item.KodeJenis}">${item.KodeJenis} - ${item.NamaJenis}</option>
        `
      });

      document.getElementById("input_harga_add_kodesupplier").innerHTML = rowSelect
      document.getElementById("input_harga_edit_kodesupplier").innerHTML = rowSelect

    }})

    $.ajax({
      url: "{!! url('masterbarangspdetailharga') !!}",
      type: "get",
      async: false,
      data: {
        kodebarang
      },
      success: function(res) {
        console.log('res detail harga')
        console.log(res)

        let rowTable = ''

        res.forEach((item, i) => {
          rowTable += `
          <tr>
          <td class="text-center">
            <button class="btn btn-success btn-sm hover-tooltip" onclick="buttonEditHarga('${kodebarang}' , '${item.KODEJENISCUSTSUPP}')" type="button" data-tooltip="Edit Harga"><i class="bi bi-pen"></i></button>
            <button class="btn btn-danger btn-sm hover-tooltip" onclick="buttonDeleteHarga('${kodebarang}', '${item.KODEJENISCUSTSUPP}')" type="button" data-tooltip="Delete Harga" ><i class="bi bi-trash"></i></button>
          </td>
          <td>${item.KODEJENISCUSTSUPP}</td>
          <td>${item.HARGA1}</td>
          <td>${item.HARGA2}</td>
          </tr>
          `
        });

        if (!res.length) {
          rowTable = `<td colspan=4 class="text-center">Belum ada data harga</td>`
        }

        document.getElementById("tabel_data_harga").innerHTML = rowTable
        document.getElementById("input_harga_kodebarang").value = kodebarang


        // input_harga_add_kodesupplier


      }})


  $("#formHarga").modal('toggle')
}

function submitAdd () {

  console.log('SUBMIT ADD')

  let _token = $("#_token").val();
  let isagen = 0
  if (document.getElementById("input_add_isagen").checked) {
    isagen= 1
  }
  let iskontrak = 0
  if (document.getElementById("input_add_iskontrak").checked) {
    iskontrak= 1
  }
  let kodegroup = $("#input_add_kodegroup").val();
  let kodeheadgroup = $("#input_add_kodeheadgroup").val();
  let kodesubgroup = $("#input_add_kodesubgroup").val();
  let kodesubkategori = $("#input_add_kodesubkategori").val();
  let kodemerk = $("#input_add_kodemerk").val();
  let kodebarang = $("#input_add_kodebarang").val();
  let namabarang = $("#input_add_namabarang").val();
  let namabarang2 = $("#input_add_namabarang2").val();
  let satuan = $("#input_add_satuan").val();
  let satuan2 = $("#input_add_satuan2").val();
  // let satuan3 = $("#input_add_satuan3").val();
  let isi = $("#input_add_isi").val();
  let isi2 = $("#input_add_isi2").val();
  let harga = $("#input_add_harga").val();
  let harga2 = $("#input_add_harga2").val();
  // let isi3 = $("#input_add_isi3").val();
  let qtymax = $("#input_add_qtymax").val();
  let qtymin = $("#input_add_qtymin").val();
  let toleransi = $("#input_add_toleransi").val();
  let isberat = $("#input_add_isberat").val();
  let isaktif = $("#input_add_isaktif").val();
  let beratvolume = $("#input_add_beratvolume").val();
  let partnumber = $("#input_add_partnumber").val();
  let lokasi = $("#input_add_lokasi").val();
  let kodesku = $("#input_add_kodesku").val();

  console.log('isagen' , isagen)
  console.log('kodegroup' , kodegroup)
  console.log('kodeheadgroup' , kodeheadgroup)
  console.log('kodesubgroup' , kodesubgroup)
  console.log('kodesubkategori' , kodesubkategori)
  console.log('kodemerk' , kodemerk)
  console.log('kodebarang' , kodebarang)
  console.log('namabarang' , namabarang)
  console.log('namabarang2' , namabarang2)
  console.log('satuan' , satuan)
  console.log('satuan2' , satuan2)
  // console.log('satuan3' , satuan3)
  console.log('isi' , Number(isi))
  console.log('isi2' , Number(isi2))
  console.log('harga', harga)
  console.log('harga2', harga2)
  // console.log('isi3' , Number(isi3))
  console.log('qtymax' , Number(qtymax))
  console.log('qtymin' , Number(qtymin))
  console.log('toleransi' , Number(toleransi))
  console.log('isberat' , isberat)
  console.log('isaktif' , isaktif)
  console.log('beratvolume' , Number(beratvolume))
  console.log('partnumber' , partnumber)
  console.log('lokasi' , lokasi)
  console.log('kodesku' , kodesku)
  console.log('iskontrak' , iskontrak)

  // return
  if (!kodegroup) {
    alertify.warning("KodeGroup harus diisi");
    return
  }
  if (!kodeheadgroup) {
    alertify.warning("KodeHeadGroup  harus diisi");
    return
  }
  if (!kodesubgroup) {
    alertify.warning("KodeSubGroup harus diisi");
    return
  }
  if (!kodesubkategori) {
    alertify.warning("KodeSubKategori harus diisi");
    return
  }
  if (!kodemerk) {
    alertify.warning("KodeMerk harus diisi");
    return
  }
  if (!kodebarang) {
    alertify.warning("KodeBarang harus diisi");
    return
  }
  if (!namabarang) {
    alertify.warning("NamaBarang harus diisi");
    return
  }
  if (!satuan) {
    alertify.warning("Satuan 1 harus diisi");
    return
  }
  if (!isi) {
    alertify.warning("Isi 1 harus diisi");
    return
  }


  $.ajax({
    url: "{!! url('masterbarangspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      isagen: Number(isagen),
      kodegroup,
      kodeheadgroup,
      kodesubgroup,
      kodesubkategori,
      kodemerk,
      kodebarang,
      namabarang,
      namabarang2,
      harga,
      harga2,
      satuan,
      satuan2,
      // satuan3,
      isi: Number(isi),
      isi2: Number(isi2),
      harga,
      harga2,
      // isi3: Number(isi3),
      qtymax: Number(qtymax),
      qtymin: Number(qtymin),
      toleransi: Number(toleransi),
      isberat: Number(isberat),
      isaktif: Number(isaktif),
      beratvolume: Number(beratvolume),
      partnumber,
      lokasi,
      kodesku,
      proses: 0,
      istakein: 0,
      iskontrak
    },
    success: function(res) {

    console.log(res)

    if (res != 1) {
      alertify.warning(res);
    }  else {
      alertify.success('Barang telah ditambah');
      loadAll()
      $("#form").modal('toggle')
    }

    }})


}



function submitEdit () {

  console.log('SUBMIT Edit')

  let _token = $("#_token").val();
  let isagen = 0
  if (document.getElementById("input_edit_isagen").checked) {
    isagen= 1
  }
  let iskontrak = 0
  if (document.getElementById("input_edit_iskontrak").checked) {
    iskontrak= 1
  }
  let kodegroup = $("#input_edit_kodegroup").val();
  let kodeheadgroup = $("#input_edit_kodeheadgroup").val();
  let kodesubgroup = $("#input_edit_kodesubgroup").val();
  let kodesubkategori = $("#input_edit_kodesubkategori").val();
  let kodemerk = $("#input_edit_kodemerk").val();
  let kodebarang = $("#input_edit_kodebarang").val();
  let namabarang = $("#input_edit_namabarang").val();
  let namabarang2 = $("#input_edit_namabarang2").val();
  let satuan = $("#input_edit_satuan").val();
  let satuan2 = $("#input_edit_satuan2").val();
  // let satuan3 = $("#input_edit_satuan3").val();
  let isi = $("#input_edit_isi").val();
  let isi2 = $("#input_edit_isi2").val();
  let harga = $("#input_edit_harga").val();
  let harga2 = $("#input_edit_harga2").val();
  // let isi3 = $("#input_edit_isi3").val();
  let qtymax = $("#input_edit_qtymax").val();
  let qtymin = $("#input_edit_qtymin").val();
  let toleransi = $("#input_edit_toleransi").val();
  let isberat = $("#input_edit_isberat").val();
  let isaktif = $("#input_edit_isaktif").val();
  let beratvolume = $("#input_edit_beratvolume").val();
  let partnumber = $("#input_edit_partnumber").val();
  let lokasi = $("#input_edit_lokasi").val();
  let kodesku = $("#input_edit_kodesku").val();

  console.log('isagen' , isagen)
  console.log('kodegroup' , kodegroup)
  console.log('kodeheadgroup' , kodeheadgroup)
  console.log('kodesubgroup' , kodesubgroup)
  console.log('kodesubkategori' , kodesubkategori)
  console.log('kodemerk' , kodemerk)
  console.log('kodebarang' , kodebarang)
  console.log('namabarang' , namabarang)
  console.log('namabarang2' , namabarang2)
  console.log('satuan' , satuan)
  console.log('satuan2' , satuan2)
  // console.log('satuan3' , satuan3)
  console.log('isi' , Number(isi))
  console.log('isi2' , Number(isi2))
  console.log('harga', harga)
  console.log('harga2', harga2)
  // console.log('isi3' , Number(isi3))
  console.log('qtymax' , Number(qtymax))
  console.log('qtymin' , Number(qtymin))
  console.log('toleransi' , Number(toleransi))
  console.log('isberat' , isberat)
  console.log('isaktif' , isaktif)
  console.log('beratvolume' , Number(beratvolume))
  console.log('partnumber' , partnumber)
  console.log('lokasi' , lokasi)
  console.log('kodesku' , kodesku)
  console.log('iskontrak' , iskontrak)

  // return
  if (!kodegroup) {
    alertify.warning("KodeGroup harus diisi");
    return
  }
  if (!kodeheadgroup) {
    alertify.warning("KodeHeadGroup  harus diisi");
    return
  }
  if (!kodesubgroup) {
    alertify.warning("KodeSubGroup harus diisi");
    return
  }
  if (!kodesubkategori) {
    alertify.warning("KodeSubKategori harus diisi");
    return
  }
  if (!kodemerk) {
    alertify.warning("KodeMerk harus diisi");
    return
  }
  if (!kodebarang) {
    alertify.warning("KodeBarang harus diisi");
    return
  }
  if (!namabarang) {
    alertify.warning("NamaBarang harus diisi");
    return
  }
  if (!satuan) {
    alertify.warning("Satuan 1 harus diisi");
    return
  }
  if (!isi) {
    alertify.warning("Isi 1 harus diisi");
    return
  }


  $.ajax({
    url: "{!! url('masterbarangspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      isagen: Number(isagen),
      kodegroup,
      kodeheadgroup,
      kodesubgroup,
      kodesubkategori,
      kodemerk,
      kodebarang,
      namabarang,
      namabarang2,
      harga,
      harga2,
      satuan,
      satuan2,
      // satuan3,
      isi: Number(isi),
      isi2: Number(isi2),
      harga,
      harga2,
      // isi3: Number(isi3),
      qtymax: Number(qtymax),
      qtymin: Number(qtymin),
      toleransi: Number(toleransi),
      isberat: Number(isberat),
      isaktif: Number(isaktif),
      beratvolume: Number(beratvolume),
      partnumber,
      lokasi,
      kodesku,
      proses: 0,
      istakein: 0,
      iskontrak
    },
    success: function(res) {

    console.log(res)
    if (res != 1) {
      alertify.warning(res);
    }  else {
      alertify.success('Barang telah diedit');
      loadAll()
      $("#formEdit").modal('toggle')
    }
    }})


}

window.onload = function() {
    loadAll();
};
</script>




@endsection
