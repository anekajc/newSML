@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Master Barang')
@section('content')



  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Master</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Barang</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Master Barang</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add Barang</button>
  </div> --}}

<div id="contentContainer" class="container-fluid po-list-page">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  <div class="card">
    <div class="card-body" style="padding:0;">

  @include('master.partials.toolbarMaster')

  <table id="tabel" class="data-table po-aksi-hover">
        <thead id="tabel_header" class="text-center">
          <tr>
            <th style="padding: 4px 12px;" scope="col">Actions</th>
          </tr>
        </thead>
        <tbody id="tabel_data" class="text-left"></tbody>
      </table>

      <div class="po-rt-hint">
        <i class="bi bi-info-circle"></i>
        Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom untuk menyembunyikan kolom.
      </div>

    </div>
  </div>

</div>


<style>
  /* Tiga pasang label + input per baris (Qty Min | Qty Max | Toleransi, dst). */
  #formBsGrid .bs-form.bs-form-3 { grid-template-columns: 120px minmax(0, 1fr) 100px minmax(0, 1fr) 100px minmax(0, 1fr); }
  #formBsGrid .bs-form.bs-form-3 .bs-full-3 { grid-column: 4 / -1; }
  @media (max-width: 575.98px) {
    #formBsGrid .bs-form.bs-form-3 { grid-template-columns: 1fr; }
    #formBsGrid .bs-form.bs-form-3 .bs-full-3 { grid-column: 1 / -1; }
  }
</style>

<!-- start modal add -->
<div class="modal fade"  id="form"tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />
          <div class="bs-form">
          <label for="input_add_kodegroup">Group</label>
          <div class="d-flex align-items-center" style="gap:8px">
            <input type="text" class="form-control" id="input_add_kodegroup" value='BJ' disabled style="flex:0 0 70px">
            <input type="text" class="form-control" value='Barang Jadi' disabled>
          </div>
          <label for="input_add_isagen">Keagenan</label>
          <div class="bs-check"><input type="checkbox" id="input_add_isagen" name="" value=""></div>

          <label for="input_add_kodeheadgroup">HeadGroup</label>
          <select id="input_add_kodeheadgroup" onchange="changeInputHeadGroup()" class="form-control" aria-label="Default select example">
                    <option selected value="" disabled>Pilih HeadGroup</option>
                  </select>

          <label for="input_add_kodesubkategori">SubKategori</label>
          <select id="input_add_kodesubkategori" onchange="changeInputSubKategori()" class="form-control" aria-label="Default select example" >
                    <option selected disabled value="">Pilih Subkategori</option>
                  </select>

          <label for="input_add_kodesubgroup">SubGroup</label>
          <select id="input_add_kodesubgroup" onchange="changeInputSubGroup()"  class="form-control" aria-label="Default select example" >
                    <option selected disabled value="">Pilih SubGroup</option>
                  </select>

          <label for="input_add_kodemerk">Merk</label>
          <select id="input_add_kodemerk" onchange="changeInputMerk()" class="form-control" aria-label="Default select example">
                    <option selected disabled value="">Pilih Merk</option>
                  </select>

          <label for="input_add_kodebarang">Kode Barang</label>
          <input type="text" class="form-control" id="input_add_kodebarang" disabled >

          <label for="input_add_kodesku">Kode SKU</label>
          <div class="d-flex align-items-center" style="gap:8px"><input type="text" class="form-control text-leftt" id="input_add_kodesku" >
            <div class="bs-check"><input type="checkbox" id="input_add_iskontrak" name="" value="">
                <span class="text-left">Kontrak</span></div></div>

          <label for="input_add_namabarang">Nama Barang</label>
          <div class="bs-full"><input type="text" class="form-control" id="input_add_namabarang" ></div>

          <label for="input_add_namabarang2">Nama Barang 2</label>
          <div class="bs-full"><input type="text" class="form-control" id="input_add_namabarang2" ></div>
        </div>

            <br/>

            <div class="bs-form bs-form-1">
          <label></label>
          <div class="d-flex" style="gap:8px"><b class="form-control border-0 bg-transparent px-0">Satuan</b><b class="form-control border-0 bg-transparent px-0">Isi</b><b class="form-control border-0 bg-transparent px-0">Harga</b></div>
        </div>

            <div class="bs-form bs-form-1">
          <label for="input_add_satuan">Satuan</label>
          <div class="bs-full"><div class="d-flex align-items-center" style="gap:8px"><!-- <div class="form-group">
                  <input type="text" class="form-control" id="input_add_satuan" >
                </div> -->

                <div class="form-group">
                  <!-- <input type="text" class="form-control" id="input_add_namaarea" placeholder="Nama Area"> -->
                  <select id="input_add_satuan" class="form-control" aria-label="Default select example">
                    <option selected value="0">Pilih Satuan</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                  </select>
                </div>
            <input type="text" inputmode="decimal" value=0 min=0 class="form-control text-right format-number" id="input_add_isi" >
            <input type="text" inputmode="decimal" value=1000 min=0 class="form-control text-right format-number" id="input_add_harga" ></div></div>


            
          <label for="input_add_satuan2">Satuan 2</label>
          <div class="bs-full"><div class="d-flex align-items-center" style="gap:8px"><!-- <div class="form-group">
                  <input type="text" class="form-control" id="input_add_satuan2" >
                </div> -->


                <div class="form-group">
                  <!-- <input type="text" class="form-control" id="input_add_namaarea" placeholder="Nama Area"> -->
                  <select id="input_add_satuan2" class="form-control" aria-label="Default select example">
                    <option selected value="0">Pilih Satuan</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                  </select>
                </div>
            <input type="text" inputmode="decimal" value=0 min=0 class="form-control text-right format-number" id="input_add_isi2" >
            <input type="text" inputmode="decimal" value=0 min=0 class="form-control text-right format-number" id="input_add_harga2" ></div></div>


   

            
          <label for="input_add_satuan3">Satuan 3</label>
          <div class="bs-full"><div class="d-flex align-items-center" style="gap:8px"><!-- <div class="form-group">
                  <input type="text" class="form-control" id="input_add_satuan3" >
                </div> -->

                <div class="form-group">
                  <select id="input_add_satuan3" class="form-control" aria-label="Default select example">
                    <option selected value="0">Pilih Satuan</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                  </select>
                </div>
            <input type="text" inputmode="decimal" value=0 min=0 class="form-control text-right format-number" id="input_add_isi3" >
            <input type="text" inputmode="decimal" value=0 min=0 class="form-control text-right format-number" id="input_add_harga3" ></div></div>
        </div>










            <br/>

            <div class="bs-form bs-form-3">
          <label for="input_add_qtymin">Qty Min</label>
          <input type="text" inputmode="decimal" value=0 min=0 class="form-control text-right format-number" id="input_add_qtymin" >
          <label for="input_add_qtymax">Qty Max</label>
          <input type="text" inputmode="decimal" value=0 min=0 class="form-control text-right format-number" id="input_add_qtymax" >
          <label for="input_add_toleransi">Toleransi</label>
          <input type="number" value=0 min=0 class="form-control text-right" id="input_add_toleransi" >

          <label for="input_add_isaktif">Status</label>
          <select id="input_add_isaktif" class="form-control" aria-label="Default select example">
                    <option value=1>Aktif</option>
                    <option value=0>NonAktif</option>
                  </select>
          <label for="input_add_isberat">Berat/Volume</label>
          <select id="input_add_isberat" class="form-control" aria-label="Default select example">
                    <option value=0>Volume</option>
                    <option value=1>Berat</option>
                  </select>
          <label for="input_add_beratvolume">Berat/Volume</label>
          <input type="number" class="form-control text-right" id="input_add_beratvolume" value=0.00>

          <label for="input_add_partnumber">Part Number</label>
          <input type="text" class="form-control text-left" id="input_add_partnumber" >
          <label for="input_add_lokasi">Lokasi</label>
          <div class="bs-full-3"><input type="text" class="form-control text-left" id="input_add_lokasi" ></div>
        </div>




    </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitAdd()">Simpan</button>
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
      <div class="modal-body" id="formBsGrid">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <div class="bs-form">
          <label for="input_edit_kodegroup">Group</label>
          <div class="d-flex align-items-center" style="gap:8px">
            <input type="text" class="form-control" id="input_edit_kodegroup" value='BJ' disabled style="flex:0 0 70px">
            <input type="text" class="form-control" value='Barang Jadi' disabled>
          </div>
          <label for="input_edit_isagen">Keagenan</label>
          <div class="bs-check"><input type="checkbox" id="input_edit_isagen" name="" value=""></div>

          <label for="input_edit_kodeheadgroup">HeadGroup</label>
          <select disabled id="input_edit_kodeheadgroup" onchange="changeInputHeadGroup()" class="form-control" aria-label="Default select example">
                    <option selected value="" disabled>Pilih HeadGroup</option>
                  </select>

          <label for="input_edit_kodesubkategori">SubKategori</label>
          <select disabled id="input_edit_kodesubkategori" onchange="changeInputSubKategori()" class="form-control" aria-label="Default select example" >
                    <option selected disabled value="">Pilih Subkategori</option>
                  </select>

          <label for="input_edit_kodesubgroup">SubGroup</label>
          <select disabled id="input_edit_kodesubgroup" onchange="changeInputSubGroup()"  class="form-control" aria-label="Default select example" >
                    <option selected disabled value="">Pilih SubGroup</option>
                  </select>

          <label for="input_edit_kodemerk">Merk</label>
          <select disabled id="input_edit_kodemerk" onchange="changeInputMerk()" class="form-control" aria-label="Default select example">
                    <option selected disabled value="">Pilih Merk</option>
                  </select>

          <label for="input_edit_kodebarang">Kode Barang</label>
          <input type="text" class="form-control" id="input_edit_kodebarang" disabled >

          <label for="input_edit_kodesku">Kode SKU</label>
          <div class="d-flex align-items-center" style="gap:8px"><input type="text" class="form-control text-left" id="input_edit_kodesku" >
            <div class="bs-check"><input type="checkbox" id="input_edit_iskontrak" name="" value="">
                <span class="text-left">Kontrak</span></div></div>

          <label for="input_edit_namabarang">Nama Barang</label>
          <div class="bs-full"><input type="text" class="form-control" id="input_edit_namabarang" ></div>

          <label for="input_edit_namabarang2">Nama Barang 2</label>
          <div class="bs-full"><input type="text" class="form-control" id="input_edit_namabarang2" ></div>
        </div>

            <br/>

            <div class="bs-form bs-form-1">
          <label></label>
          <div class="d-flex" style="gap:8px"><b class="form-control border-0 bg-transparent px-0">Satuan</b><b class="form-control border-0 bg-transparent px-0">Isi</b><b class="form-control border-0 bg-transparent px-0">Harga</b></div>
        </div>

            <div class="bs-form bs-form-1">
          <label for="input_edit_satuan">Satuan</label>
          <div class="bs-full"><div class="d-flex align-items-center" style="gap:8px"><!-- <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_satuan" >
                </div> -->

                <div class="form-group">
                  <!-- <input type="text" class="form-control" id="input_add_namaarea" placeholder="Nama Area"> -->
                  <select id="input_edit_satuan" class="form-control" aria-label="Default select example">
                    <option selected value="0">Pilih Satuan</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                  </select>
              </div>
            <input type="text" inputmode="decimal" value=0 min=0 class="form-control text-right format-number" id="input_edit_isi" >
            <input type="text" inputmode="decimal" value=1000 min=0 class="form-control text-right format-number" id="input_edit_harga" ></div></div>


            
          <label for="input_edit_satuan2">Satuan 2</label>
          <div class="bs-full"><div class="d-flex align-items-center" style="gap:8px"><!-- <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_satuan2" >
                </div> -->

                <div class="form-group">
                  <!-- <input type="text" class="form-control" id="input_add_namaarea" placeholder="Nama Area"> -->
                  <select id="input_edit_satuan2" class="form-control" aria-label="Default select example">
                    <option selected value="0">Pilih Satuan</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                  </select>
              
                </div>
            <input type="text" inputmode="decimal" value=0 min=0 class="form-control text-right format-number" id="input_edit_isi2" >
            <input type="text" inputmode="decimal" value=0 min=0 class="form-control text-right format-number" id="input_edit_harga2" ></div></div>


              
          <label for="input_edit_satuan3">Satuan 3</label>
          <div class="bs-full"><div class="d-flex align-items-center" style="gap:8px"><!-- <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_satuan3" >
                </div> -->

                <div class="form-group">
                  <!-- <input type="text" class="form-control" id="input_add_namaarea" placeholder="Nama Area"> -->
                  <select id="input_edit_satuan3" class="form-control" aria-label="Default select example">
                    <option selected value="0">Pilih Satuan</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                  </select>
              
                </div>
            <input type="text" inputmode="decimal" value=0 min=0 class="form-control text-right format-number" id="input_edit_isi3" >
            <input type="text" inputmode="decimal" value=0 min=0 class="form-control text-right format-number" id="input_edit_harga3" ></div></div>
        </div>

            <br/>

            <div class="bs-form bs-form-3">
          <label for="input_edit_qtymin">Qty Min</label>
          <input type="text" inputmode="decimal" value=0 min=0 class="form-control text-right format-number" id="input_edit_qtymin" >
          <label for="input_edit_qtymax">Qty Max</label>
          <input type="text" inputmode="decimal" value=0 min=0 class="form-control text-right format-number" id="input_edit_qtymax" >
          <label for="input_edit_toleransi">Toleransi</label>
          <input type="number" value=0 min=0 class="form-control text-right" id="input_edit_toleransi" >

          <label for="input_edit_isaktif">Status</label>
          <select id="input_edit_isaktif" class="form-control" aria-label="Default select example">
                    <option value=1>Aktif</option>
                    <option value=0>NonAktif</option>
                  </select>
          <label for="input_edit_isberat">Berat/Volume</label>
          <select id="input_edit_isberat" class="form-control" aria-label="Default select example">
                    <option value=0>Volume</option>
                    <option value=1>Berat</option>
                  </select>
          <label for="input_edit_beratvolume">Berat/Volume</label>
          <input type="number" class="form-control text-right" id="input_edit_beratvolume" value=0.00>

          <label for="input_edit_partnumber">Part Number</label>
          <input type="text" class="form-control text-left" id="input_edit_partnumber" >
          <label for="input_edit_lokasi">Lokasi</label>
          <div class="bs-full-3"><input type="text" class="form-control text-left" id="input_edit_lokasi" ></div>
        </div>

    </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitEdit()">Simpan</button>
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
      <div class="modal-body" id="formBsGrid">
        <!-- <h1>Tes Modal</h1> -->
        <div class="container-fluid">
          <input type="hidden" id="input_harga_kodegroup" value="" /><div class="bs-form bs-form-1">
          <label for="input_harga_kodebarang">Kode Barang</label>
          <div class="bs-full"><div class="d-flex align-items-center" style="gap:8px"><input type="text" class="form-control" id="input_harga_kodebarang" placeholder="Kode Barang" disabled>
            <button type="button" class="btn btn-sm btn-chip-biru" onclick="buttonAddHarga()">Tambah</button></div></div>
        </div>
    </div>

    <!-- ADD SUBGROUP -->

    <div id="addHarga" class="container-fluid showhide">
        <div class="form-card mt-3">
          <div class="form-card-title">Tambah Harga</div>
          <div class="bs-form bs-form-1">
            <label for="input_harga_add_kodesupplier">Supplier</label>
            <select id="input_harga_add_kodesupplier" class="form-control">
            </select>

            <label for="input_harga_add_harga">Harga1</label>
            <input id="input_harga_add_harga" value=0.00 min=0 type="text" inputmode="decimal" class="form-control text-right format-number">

            <label for="input_harga_add_harga2">Harga2</label>
            <input id="input_harga_add_harga2" value=0.00 min=0 type="text" inputmode="decimal" class="form-control text-right format-number">
          </div>
          <div class="text-right mt-3">
            <button type="button" class="btn btn-sm btn-batal-add" onclick="closeShowHarga()">Batal</button>
            <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitAddHarga()">Simpan</button>
          </div>
        </div>
      </div>

    <!-- END ADD SUBGROUP -->

    <!-- EDIT SUBGROUP -->

    <div id="editHarga" class="container-fluid showhide">
        <div class="form-card mt-3">
          <div class="form-card-title">Edit Harga</div>
          <div class="bs-form bs-form-1">
            <label for="input_harga_edit_kodesupplier">Supplier</label>
            <select id="input_harga_edit_kodesupplier" disabled class="form-control">
            </select>

            <label for="input_harga_edit_harga">Harga1</label>
            <input id="input_harga_edit_harga" value=0.00 min=0 type="text" inputmode="decimal" class="form-control text-right format-number">

            <label for="input_harga_edit_harga2">Harga2</label>
            <input id="input_harga_edit_harga2" value=0.00 min=0 type="text" inputmode="decimal" class="form-control text-right format-number">
          </div>
          <div class="text-right mt-3">
            <button type="button" class="btn btn-sm btn-batal-add" onclick="closeShowHarga()">Batal</button>
            <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitEditHarga()">Simpan</button>
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

<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/master-list.js') !!}?v={{ @filemtime(base_path('public/js/master-list.js')) ?: '1' }}"></script>

<script type="text/javascript">

let dataRefresh = []

let listSelectHeadGroup = []
let listSelectSubGroup = []
let listSelectSubKategori = []

// Kolom tabel daftar (lebih dari 5 kolom -> bisa digeser & disembunyikan, lihat MasterList.kolom()).
// [field, label, tampil, tipe, total, desimal]
const MB_KOLOM = [
  ['KODEBRG',          'Kode Brg',     1, 'varchar', 0, 0],
  ['NAMABRG',          'Nama Brg',     1, 'varchar', 0, 0],
  ['pAgen',            'Agen',         1, 'varchar', 0, 0],
  ['nNAMASUBGROUP',    'SubJenis',     1, 'varchar', 0, 0],
  ['nNAMASUBKATEGORI', 'Jenis Barang', 1, 'varchar', 0, 0],
  ['nNAMAMERK',        'Nama Merk',    1, 'varchar', 0, 0],
  ['SKU',              'SKU',          1, 'varchar', 0, 0],
  ['SAT1',             'Sat 1',        1, 'varchar', 0, 0],
  ['SAT2',             'Sat 2',        1, 'varchar', 0, 0],
  ['PartNumber',       'Part Number',  1, 'varchar', 0, 0],
]

// Tabel server-side (masterbarangloadall): kolom DataTables disusun ulang dari kolom yang tampil
// setiap kali kolom digeser/disembunyikan (ReportTable memanggil loadAll lewat onChange).
function loadAll () {
  if ($.fn.DataTable.isDataTable('#tabel')) {
    $('#tabel').DataTable().destroy();
  }
  document.getElementById('tabel_data').innerHTML = ''

  let cols = MasterList.kolomTampil()
  document.getElementById('tabel_header').innerHTML = MasterList.headHtml(cols)

  $('#tabel').DataTable({
    processing: true,
    serverSide: true,

    paging: true,
    searching: true,
    lengthChange: false,
    pageLength: MasterList.panjang(),
    // Server (masterbarangloadall) tidak memproses urutan kolom - klik judul hanya memuat
    // ulang data yang sama, jadi pengurutan dimatikan.
    ordering: false,

    dom: MasterList.dom,
    language: MasterList.bahasa,

    autoWidth: false,
    responsive: false,

    ajax: {
      url: "{!! url('masterbarangloadall') !!}",
      type: "GET",
    },
    columns: MasterList.kolomServer(cols, function (data, type, row) {
      return `
        <div class="action-buttons-wrap text-center" style="position: relative;">
          <button title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${row.KODEBRG}')"><i class="bi bi-pen"></i></button>
          <button title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${row.KODEBRG}')"><i class="bi bi-trash"></i></button>
        </div>
      `;
    }, {
      pAgen: {
        render: function (data) {
          if (data == 0) {
            return '<span class="text-danger"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></span>';
          }
          return '<span class="text-success"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></span>';
        },
        searchable: false,
        className: "text-center"
      }
    })
  });

  MasterList.selesai('#tabel')
}

function buttonAdd () {
  document.getElementById("input_add_kodebarang").value= ''
  document.getElementById("input_add_namabarang").value= ''
  document.getElementById("input_add_namabarang2").value= ''
  document.getElementById("input_add_satuan").value = ''
  document.getElementById("input_add_satuan2").value= ''
  // let satuan3 document.getElementById("input_add_satuan3").value=
  setNum("input_add_isi", '1.00')
  setNum("input_add_isi2", '1.00')
  setNum("input_add_harga", '0.00')
  setNum("input_add_harga2", '0.00')
  // let isi3 document.getElementById("input_add_isi3").value=
  setNum("input_add_qtymax", '0.00')
  setNum("input_add_qtymin", '0.00')
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



      $.ajax({
    url: "{!! url('masterbaranglistsatuan') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {

      console.log(res)
      let rowTable = `<option selected value=0>Pilih Satuan</option>`
      res.forEach((item, i) => {
        rowTable += `
          <option value="${item.kodesatuan}">${item.kodesatuan}</option>
        `
      });

      document.getElementById("input_add_satuan").innerHTML = rowTable
      document.getElementById("input_add_satuan2").innerHTML = rowTable
      document.getElementById("input_add_satuan3").innerHTML = rowTable
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
  let tempsat = ""
  let tempsat2 = ""
  let tempsat3 = ""

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
    tempsat = res[0].SAT1
    document.getElementById("input_edit_satuan2").value = res[0].SAT2
    tempsat2 = res[0].SAT2
    document.getElementById("input_edit_satuan3").value = res[0].SAT3
    tempsat3 = res[0].SAT3

    setNum("input_edit_isi", res[0].ISI1)
    setNum("input_edit_isi2", res[0].ISI2)
    setNum("input_edit_isi3", res[0].ISI3)
    setNum("input_edit_harga", res[0].Hrg1_1)
    setNum("input_edit_harga2", res[0].Hrg2_1)
    setNum("input_edit_harga3", res[0].Hrg3_1)
    setNum("input_edit_qtymin", res[0].QntMin)
    setNum("input_edit_qtymax", res[0].QntMax)
    document.getElementById("input_edit_toleransi").value = (parseFloat(res[0].Tolerate) || 0).toFixed(2)
    document.getElementById("input_edit_isberat").value = res[0].pBerat
    document.getElementById("input_edit_beratvolume").value = (parseFloat(res[0].Berat) || 0).toFixed(2)
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


    $.ajax({
   url: "{!! url('masterbaranglistsatuan') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {

      console.log(res)
      let rowTable = `<option selected value=0>Pilih Sat Tax</option>`
      res.forEach((item, i) => {
        rowTable += `
          <option value="${item.kodesatuan}">${item.kodesatuan}</option>
        `
      });

      document.getElementById("input_edit_satuan").innerHTML = rowTable
      document.getElementById("input_edit_satuan2").innerHTML = rowTable
      document.getElementById("input_edit_satuan3").innerHTML = rowTable
      
     document.getElementById("input_edit_satuan").value = tempsat
     document.getElementById("input_edit_satuan2").value = tempsat2
     document.getElementById("input_edit_satuan3").value = tempsat3
    }})
    console.log(tempsat)  



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
      setNum("input_harga_edit_harga", res[0].HARGA1)
      setNum("input_harga_edit_harga2", res[0].HARGA2)

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
let harga = $("#input_harga_edit_harga").val().replace(/,/g, '');
let harga2 = $("#input_harga_edit_harga2").val().replace(/,/g, '');

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
  let harga = $("#input_harga_add_harga").val().replace(/,/g, '');
  let harga2 = $("#input_harga_add_harga2").val().replace(/,/g, '');

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
  let satuan3 = $("#input_add_satuan3").val();
  let isi = $("#input_add_isi").val().replace(/,/g, '');
  let isi2 = $("#input_add_isi2").val().replace(/,/g, '');
  let isi3 = $("#input_add_isi3").val().replace(/,/g, '');
  let harga = $("#input_add_harga").val().replace(/,/g, '');
  let harga2 = $("#input_add_harga2").val().replace(/,/g, '');
  let harga3 = $("#input_add_harga3").val().replace(/,/g, '');
  let qtymax = $("#input_add_qtymax").val().replace(/,/g, '');
  let qtymin = $("#input_add_qtymin").val().replace(/,/g, '');
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
  console.log('satuan3' , satuan3)
  console.log('isi' , Number(isi))
  console.log('isi2' , Number(isi2))
  console.log('harga', harga)
  console.log('harga2', harga2)
  console.log('harga3', harga3)
  console.log('isi3' , Number(isi3))
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
  if (!satuan || satuan == 0) {
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
      harga3,
      satuan,
      satuan2,
      satuan3,
      isi: Number(isi),
      isi2: Number(isi2),
      harga,
      harga2,
      isi3: Number(isi3),
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
  let satuan3 = $("#input_edit_satuan3").val();
  let isi = $("#input_edit_isi").val().replace(/,/g, '');
  let isi2 = $("#input_edit_isi2").val().replace(/,/g, '');
  let isi3 = $("#input_edit_isi3").val().replace(/,/g, '');
  let harga = $("#input_edit_harga").val().replace(/,/g, '');
  let harga2 = $("#input_edit_harga2").val().replace(/,/g, '');
  let harga3 = $("#input_edit_harga3").val().replace(/,/g, '');
  
  let qtymax = $("#input_edit_qtymax").val().replace(/,/g, '');
  let qtymin = $("#input_edit_qtymin").val().replace(/,/g, '');
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
  if (!satuan || satuan == 0) {
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
      satuan3,
      isi: Number(isi),
      isi2: Number(isi2),
      harga,
      harga2,
      harga3,
      isi3: Number(isi3),
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

// Separator ribuan pada input angka (.format-number) - pola accounting/pengajuandpp.blade.php:
// autoNumeric memformat tampilan, nilai yang diisi program lewat setNum(), dan pembacaan nilai
// membuang koma dengan .replace(/,/g, '') sebelum dikirim / dihitung.
function toNum (v) {
  let n = parseFloat(String(v == null ? '' : v).replace(/,/g, ''))
  return isNaN(n) ? 0 : n
}
function setNum (id, v) {
  let el = document.getElementById(id)
  if (!el) return
  // Nilai kosong tetap kosong (validasi "harus diisi" bergantung pada ini).
  if (v === '' || v == null) {
    el.value = ''
  } else if ($(el).data('autoNumeric')) {
    $(el).autoNumeric('set', toNum(v))
  } else {
    el.value = v
  }
}
$(function () {
  $('.format-number').autoNumeric('init', { mDec: '2', vMin: '-9999999999999.99' })
})

window.onload = function(){
    MasterList.kolom({ href: 'masterbarang', kolom: MB_KOLOM, onChange: loadAll })
    loadAll();
};
</script>




@endsection
