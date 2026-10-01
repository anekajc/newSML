@extends('newmaster')
@section('buttons')

@endsection
@section('content')

 <link rel="stylesheet" href="{{ asset('css/tableMaster2.css') }}?v={{ filemtime(public_path('css/tableMaster2.css')) }}">


  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Master</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Aktiva</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Master Aktiva</h1>
    </div>
    <button class="btn btn-action-primary" onclick="cleanFormAdd()">+ Add Aktiva</button>
  </div> --}}

<div id="contentContainer" class="container-fluid">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  @include('master.partials.headerTableMaster')

  <div class="table-outer">
    <div class="table-wrap">
      <table class="tb" id="tabel">
        <thead>
          <tr>
            <th scope="col">Actions</th>
            <th scope="col">Kode Aktiva</th>
            <th scope="col">Keterangan</th>
            <th scope="col">Tanggal</th>
            <th scope="col">Devisi</th>
            <th scope="col">Tipe Aktiva</th>
            <th scope="col">Kelompok</th>
            <th scope="col">Quantity</th>
            <th scope="col">Susut</th>
            <th scope="col">Metode</th>
            <th scope="col">Akumulasi</th>
            <th scope="col">Biaya Penyusutan 1</th>
            <th scope="col">Persen Biaya 1</th>
            <th scope="col">Biaya Penyusutan 2</th>
            <th scope="col">Persen Biaya 2</th>
          </tr>
        </thead>
        <tbody id="tabel_data" class="text-right">
      </tbody>
      </table>
    </div>
</div>

</div>
<!-- start modal add -->
<div class="modal fade" id="formAdd" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 900px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel">Form Add Aktiva</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form>
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="">

          <!-- Group Aktiva -->
          <div class="row mb-3 align-items-center">
            <div class="col-md-2">
              <label class="form-label">Group Aktiva</label>
            </div>
            <div class="col-md-4">
              <div class="input-group">
                <input type="text" class="form-control" id="input_add_GroupAktiva" placeholder="Group Aktiva">
                <button type="button" class="btn btn-primary btn-select" onclick="buttonGroupAktiva()">+</button>
              </div>
            </div>
            <div class="col-md-2">
              <label class="form-label">Devisi</label>
            </div>
            <div class="col-md-4">
              <div class="input-group">
                <input type="text" class="form-control" id="input_add_DaftarDevisi" placeholder="Daftar Devisi">
                <button type="button" class="btn btn-primary btn-select" onclick="buttonDaftarDevisi()">+</button>
              </div>
            </div>
          </div>

          <!-- No. Aktiva / No. Urut -->
          <div class="row mb-3 align-items-center">
            <div class="col-md-2">
              <label class="form-label">No. Aktiva</label>
            </div>
            <div class="col-md-4">
              <input type="text" class="form-control" id="input_add_NoAktiva" placeholder="No. Aktiva" disabled>
            </div>
            <div class="col-md-2">
              <label class="form-label">No. Urut</label>
            </div>
            <div class="col-md-4">
              <input type="text" class="form-control" id="input_add_NoUrut" placeholder="No. Urut" disabled>
            </div>
          </div>

          <!-- Tipe Aktiva / Tgl. Perolehan -->
          <div class="row mb-3 align-items-center">
            <div class="col-md-2">
              <label class="form-label">Tipe Aktiva</label>
            </div>
            <div class="col-md-4">
              <select class="form-control" id="input_add_TipeAktiva" disabled>
                <option value="0">Aktiva Tetap</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label date-label">Tgl. Perolehan</label>
            </div>
            <div class="col-md-4">
              <input type="date" class="form-control" id="input_add_TglPerolehan">
            </div>
          </div>

          <!-- Tgl. Pemakaian -->
          <div class="row mb-3 align-items-center">
            <div class="col-md-6"></div>
            <div class="col-md-2">
              <label class="form-label date-label">Tgl. Pemakaian</label>
            </div>
            <div class="col-md-4">
              <input type="date" class="form-control" id="input_add_TglPemakaian">
            </div>
          </div>

          <!-- Kuantum / Susut / Metode -->
          <div class="row mb-3 align-items-center">
            <div class="col-md-2">
              <label class="form-label date-label">Kuantum</label>
            </div>
            <div class="col-md-2">
              <input type="number" class="form-control text-right" id="input_add_Kuantum" value="1">
            </div>
            <div class="col-md-2">
              <label class="form-label date-label">Susut (%)</label>
            </div>
            <div class="col-md-2">
              <input type="number" class="form-control text-right" id="input_add_Susut" placeholder="Susut(%)">
            </div>
            <div class="col-md-2">
              <label class="form-label date-label">Metode Susut</label>
            </div>
            <div class="col-md-2">
              <select class="form-control" id="input_add_MetodePenyusutan">
                <option value="L">[L]urus</option>
                <option value="M">[M]enurun</option>
                <option value="P">[P]ajak</option>
              </select>
            </div>
          </div>

          <!-- Keterangan -->
          <div class="row mb-3">
            <div class="col-md-2">
              <label class="form-label">Keterangan</label>
            </div>
            <div class="col-md-10">
              <textarea class="form-control" id="input_add_Keterangan" rows="2" placeholder="Keterangan"></textarea>
            </div>
          </div>

          <!-- Akumulasi Penyusutan -->
          <div class="row mb-3 align-items-center">
            <div class="col-md-2">
              <label class="form-label">Akumulasi Penyusutan</label>
            </div>
            <div class="col-md-4">
              <div class="input-group">
                <input type="text" class="form-control" id="input_add_AkumulasiPenyusutan" placeholder="Akumulasi Penyusutan">
                <button type="button" class="btn btn-primary btn-select" onclick="buttonAkumulasiPenyusutan()">+</button>
              </div>
            </div>
          </div>

          <!-- Biaya Penyusutan 1 -->
          <div class="row mb-3 align-items-center">
            <div class="col-md-2">
              <label class="form-label">Biaya Penyusutan 1</label>
            </div>
            <div class="col-md-4">
              <div class="input-group">
                <input type="text" class="form-control" id="input_add_BiayaPenyusutan1" placeholder="Biaya Penyusutan 1">
                <button type="button" class="btn btn-primary btn-select" onclick="buttonBiayaPenyusutan('1')">+</button>
              </div>
            </div>
            <div class="col-md-3">
              <div class="input-group">
                <input type="number" class="form-control percentage-input text-right" id="input_add_PersenBiayaPenyusutan1" placeholder="%">
                <span class="input-group-text">%</span>
              </div>
            </div>
          </div>

          <!-- Biaya Penyusutan 2 -->
          <div class="row mb-3 align-items-center">
            <div class="col-md-2">
              <label class="form-label">Biaya Penyusutan 2</label>
            </div>
            <div class="col-md-4">
              <div class="input-group">
                <input type="text" class="form-control" id="input_add_BiayaPenyusutan2" placeholder="Biaya Penyusutan 2">
                <button type="button" class="btn btn-primary btn-select" onclick="buttonBiayaPenyusutan('2')">+</button>
              </div>
            </div>
            <div class="col-md-3">
              <div class="input-group">
                <input type="number" class="form-control percentage-input text-right" id="input_add_PersenBiayaPenyusutan2" placeholder="%">
                <span class="input-group-text">%</span>
              </div>
            </div>
          </div>

          <!-- Biaya Penyusutan 3 -->
          <div class="row mb-3 align-items-center">
            <div class="col-md-2">
              <label class="form-label">Biaya Penyusutan 3</label>
            </div>
            <div class="col-md-4">
              <div class="input-group">
                <input type="text" class="form-control" id="input_add_BiayaPenyusutan3" placeholder="Biaya Penyusutan 3">
                <button type="button" class="btn btn-primary btn-select" onclick="buttonBiayaPenyusutan('3')">+</button>
              </div>
            </div>
            <div class="col-md-3">
              <div class="input-group">
                <input type="number" class="form-control percentage-input text-right" id="input_add_PersenBiayaPenyusutan3" placeholder="%">
                <span class="input-group-text">%</span>
              </div>
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal add-->

<!-- start modal add -->
<div class="modal fade" id="formEdit" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 900px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel">Form Edit Aktiva</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form>
          <input type="hidden" name="noUrut" id="input_edit_noUrut" value="">

          <!-- Group Aktiva -->
          <div class="row mb-3 align-items-center">
            <div class="col-md-2">
              <label class="form-label">Group Aktiva</label>
            </div>
            <div class="col-md-4">
              <div class="input-group">
                <input type="text" class="form-control" id="input_edit_GroupAktiva" placeholder="Group Aktiva">
                <button type="button" class="btn btn-primary btn-select" onclick="buttonGroupAktiva()">+</button>
              </div>
            </div>
            <div class="col-md-2">
              <label class="form-label">Devisi</label>
            </div>
            <div class="col-md-4">
              <div class="input-group">
                <input type="text" class="form-control" id="input_edit_DaftarDevisi" placeholder="Daftar Devisi">
                <button type="button" class="btn btn-primary btn-select" onclick="buttonDaftarDevisi()">+</button>
              </div>
            </div>
          </div>

          <!-- No. Aktiva / No. Urut -->
          <div class="row mb-3 align-items-center">
            <div class="col-md-2">
              <label class="form-label">No. Aktiva</label>
            </div>
            <div class="col-md-4">
              <input type="text" class="form-control" id="input_edit_NoAktiva" placeholder="No. Aktiva" disabled>
            </div>
            <div class="col-md-2">
              <label class="form-label">No. Urut</label>
            </div>
            <div class="col-md-4">
              <input type="text" class="form-control" id="input_edit_NoUrut" placeholder="No. Urut" disabled>
            </div>
          </div>

          <!-- Tipe Aktiva / Tgl. Perolehan -->
          <div class="row mb-3 align-items-center">
            <div class="col-md-2">
              <label class="form-label">Tipe Aktiva</label>
            </div>
            <div class="col-md-4">
              <select class="form-control" id="input_edit_TipeAktiva" disabled>
                <option value="0">Aktiva Tetap</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label date-label">Tgl. Perolehan</label>
            </div>
            <div class="col-md-4">
              <input type="date" class="form-control" id="input_edit_TglPerolehan">
            </div>
          </div>

          <!-- Tgl. Pemakaian -->
          <div class="row mb-3 align-items-center">
            <div class="col-md-6"></div>
            <div class="col-md-2">
              <label class="form-label date-label">Tgl. Pemakaian</label>
            </div>
            <div class="col-md-4">
              <input type="date" class="form-control" id="input_edit_TglPemakaian">
            </div>
          </div>

          <!-- Kuantum / Susut / Metode -->
          <div class="row mb-3 align-items-center">
            <div class="col-md-2">
              <label class="form-label date-label">Kuantum</label>
            </div>
            <div class="col-md-2">
              <input type="number" class="form-control text-right" id="input_edit_Kuantum" value="1">
            </div>
            <div class="col-md-2">
              <label class="form-label date-label">Susut (%)</label>
            </div>
            <div class="col-md-2">
              <input type="number" class="form-control text-right" id="input_edit_Susut" placeholder="Susut(%)">
            </div>
            <div class="col-md-2">
              <label class="form-label date-label">Metode Susut</label>
            </div>
            <div class="col-md-2">
              <select class="form-control" id="input_edit_MetodePenyusutan">
                <option value="L">[L]urus</option>
                <option value="M">[M]enurun</option>
                <option value="P">[P]ajak</option>
              </select>
            </div>
          </div>

          <!-- Keterangan -->
          <div class="row mb-3">
            <div class="col-md-2">
              <label class="form-label">Keterangan</label>
            </div>
            <div class="col-md-10">
              <textarea class="form-control" id="input_edit_Keterangan" rows="2" placeholder="Keterangan"></textarea>
            </div>
          </div>

          <!-- Akumulasi Penyusutan -->
          <div class="row mb-3 align-items-center">
            <div class="col-md-2">
              <label class="form-label">Akumulasi Penyusutan</label>
            </div>
            <div class="col-md-4">
              <div class="input-group">
                <input type="text" class="form-control" id="input_edit_AkumulasiPenyusutan" placeholder="Akumulasi Penyusutan">
                <button type="button" class="btn btn-primary btn-select" onclick="buttonAkumulasiPenyusutan()">+</button>
              </div>
            </div>
          </div>

          <!-- Biaya Penyusutan 1 -->
          <div class="row mb-3 align-items-center">
            <div class="col-md-2">
              <label class="form-label">Biaya Penyusutan 1</label>
            </div>
            <div class="col-md-4">
              <div class="input-group">
                <input type="text" class="form-control" id="input_edit_BiayaPenyusutan1" placeholder="Biaya Penyusutan 1">
                <button type="button" class="btn btn-primary btn-select" onclick="buttonBiayaPenyusutan('1')">+</button>
              </div>
            </div>
            <div class="col-md-3">
              <div class="input-group">
                <input type="number" class="form-control percentage-input text-right" id="input_edit_PersenBiayaPenyusutan1" placeholder="%">
                <span class="input-group-text">%</span>
              </div>
            </div>
          </div>

          <!-- Biaya Penyusutan 2 -->
          <div class="row mb-3 align-items-center">
            <div class="col-md-2">
              <label class="form-label">Biaya Penyusutan 2</label>
            </div>
            <div class="col-md-4">
              <div class="input-group">
                <input type="text" class="form-control" id="input_edit_BiayaPenyusutan2" placeholder="Biaya Penyusutan 2">
                <button type="button" class="btn btn-primary btn-select" onclick="buttonBiayaPenyusutan('2')">+</button>
              </div>
            </div>
            <div class="col-md-3">
              <div class="input-group">
                <input type="number" class="form-control percentage-input text-right" id="input_edit_PersenBiayaPenyusutan2" placeholder="%">
                <span class="input-group-text">%</span>
              </div>
            </div>
          </div>

          <!-- Biaya Penyusutan 3 -->
          <div class="row mb-3 align-items-center">
            <div class="col-md-2">
              <label class="form-label">Biaya Penyusutan 3</label>
            </div>
            <div class="col-md-4">
              <div class="input-group">
                <input type="text" class="form-control" id="input_edit_BiayaPenyusutan3" placeholder="Biaya Penyusutan 3">
                <button type="button" class="btn btn-primary btn-select" onclick="buttonBiayaPenyusutan('3')">+</button>
              </div>
            </div>
            <div class="col-md-3">
              <div class="input-group">
                <input type="number" class="form-control percentage-input text-right" id="input_edit_PersenBiayaPenyusutan3" placeholder="%">
                <span class="input-group-text">%</span>
              </div>
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" onclick="submitEdit()">Submit</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal add-->

<!-- start modal select add group aktiva -->
<div class="modal fade"  id="formAddGroupAktiva" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Group Aktiva</h5>
        
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table id="tabelAddGroupAktiva" class="table table-bordered table-striped"  >
          <thead id='theadCustom' class="text-center">
            <tr>
              <th scope="col">Actions</th>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>

            </tr>
          </thead>

          <tbody id="tabel_dataAddGroupAktiva" class="text-left" >
            <tr>

              <td class="text-center">
                <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                <button type="button" onclick="buttonPilihAkumulasiPerkiraan()"><i class="bi bi-plus">Select</i></button>
              </td>
              <td></td>
              <td></td>

          </tr>
          </tbody>


        </table>


    </div>
        <div class="modal-footer">
           
        </div>
  </div>
</div>
</div>
<!-- End modal select add group aktiva-->

<!-- start modal select add akumulasi penyusutan -->
<div class="modal fade"  id="formAddAkumulasiPenyusutan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Akumulasi Penyusutan</h5>
        
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table id="tabelAddAkumulasiPenyusutan" class="table table-bordered table-striped"  >
          <thead id='theadCustom' class="text-center">
            <tr>
              <th scope="col">Actions</th>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>
            </tr>
          </thead>

          <tbody id="tabel_dataAddAkumulasiPenyusutan" class="text-left" >
            <tr>

              <td></td>
              <td></td>

                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button type="button" onclick="buttonPilihAkumulasiPenyusutan()"><i class="bi bi-pen">Select</i></button>
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
<!-- End modal select add akumulasi penyusutan-->

<!-- start modal select add akumulasi penyusutan -->
<div class="modal fade"  id="formAddBiayaPenyusutan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Biaya Penyusutan</h5>
        
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table id="tabelAddBiayaPenyusutan" class="table table-bordered table-striped"  >
          <thead id='theadCustom' class="text-center">
            <tr>
              <th scope="col">Actions</th>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>
            </tr>
          </thead>

          <tbody id="tabel_dataBiayaPenyusutan" class="text-left" >
            <tr>
              <td>-</td>
              <td>-</td>
              <td>-</td>
          </tr>
          </tbody>


        </table>


    </div>
        <div class="modal-footer">
           
        </div>
  </div>
</div>
</div>
<!-- End modal select add akumulasi penyusutan-->

<!-- start modal select add devisi -->
<div class="modal fade"  id="formAddDevisi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Devisi</h5>
        
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table id="tabelAddDevisi" class="table table-bordered table-striped"  >
          <thead id='theadCustom' class="text-center">
            <tr>
              <th scope="col">Kode Devisi</th>
              <th scope="col">Nama Devisi</th>
              <th scope="col">Actions</th>

            </tr>
          </thead>

          <tbody id="tabel_dataAddDevisi" class="text-left" >
            <tr>

              <td></td>
              <td></td>

                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button type="button" onclick="buttonPilihDevisi()"><i class="bi bi-pen">Select</i></button>
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
<!-- End modal select add devisi-->


<!-- start modal select add group aktiva -->
<div class="modal fade"  id="formAddGroupAktiva" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Group Aktiva</h5>
        
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table id="tabelAddGroupAktiva" class="table table-bordered table-striped"  >
          <thead id='theadCustom' class="text-center">
            <tr>
              <th scope="col">Actions</th>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>

            </tr>
          </thead>

          <tbody id="tabel_dataAddGroupAktiva" class="text-left" >
            <tr>

              <td class="text-center">
                <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                <button type="button" onclick="buttonPilihAkumulasiPerkiraan()"><i class="bi bi-plus">Select</i></button>
              </td>
              <td></td>
              <td></td>

          </tr>
          </tbody>


        </table>


    </div>
        <div class="modal-footer">
           
        </div>
  </div>
</div>
</div>
<!-- End modal edit-->

<!-- start modal select  edit group aktiva -->
<div class="modal fade"  id="formEditGroupAktiva" style='z-index:1060;' tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Group Aktiva</h5>
        
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table id="tabelEditGroupAktiva" class="table table-bordered table-striped"  >
          <thead class="text-center">
            <tr>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>
              <th scope="col">Actions</th>

            </tr>
          </thead>

          <tbody id="tabel_dataEditGroupAktiva" class="text-left" >
            <tr>

              <td></td>
              <td></td>

                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button type="button" onclick="buttonPilihAkumulasiPerkiraan()"><i class="bi bi-pen">Select</i></button>
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
<!-- End modal select edit group aktiva-->

<!-- start modal select edit devisi -->
<div class="modal fade"  id="formEditDevisi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Devisi</h5>
        
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table id="tabelEditDevisi" class="table table-bordered table-striped"  >
          <thead id='theadCustom' class="text-center">
            <tr>
              <th scope="col">Actions</th>
              <th scope="col">Kode Devisi</th>
              <th scope="col">Nama Devisi</th>

            </tr>
          </thead>

          <tbody id="tabel_dataEditDevisi" class="text-left" >
            <tr>

              <td></td>
              <td></td>

                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button type="button" onclick="buttonPilihDevisi()"><i class="bi bi-pen">Select</i></button>
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
<!-- End modal select edit devisi-->

<!-- start modal select edit akumulasi penyusutan -->
<div class="modal fade"  id="formEditAkumulasiPenyusutan" style='z-index:1200;' tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Akumulasi Penyusutan</h5>
        
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table id="tabelEditAkumulasiPenyusutan" class="table table-bordered table-striped"  >
          <thead class="text-center">
            <tr>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>
              <th scope="col">Actions</th>

            </tr>
          </thead>

          <tbody id="tabel_dataEditAkumulasiPenyusutan" class="text-left" >
            <tr>

              <td></td>
              <td></td>

                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button type="button" onclick="buttonPilihAkumulasiPenyusutan()"><i class="bi bi-pen">Select</i></button>
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
<!-- End modal select edit akumulasi penyusutan-->

<!-- start saldo awal -->
<div class="modal fade"  id="formSaldoAwal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 550px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Saldo Awal</h5>
        
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label>Kode Aktiva</label>
              </div>
            </div>
            <div class="col-8">
              <div class="form-group">
                <input type="text" class="form-control text-left" id="nomorPerkiraan" disabled>
              </div>
            </div>
          </div>

          <div class="row mt-2">
            <div class="col-4">
              <div class="form-group">
                <label>Valas</label>
              </div>
            </div>
            <div class="col-8">
              <div class="row">
                <div class="col-5">
                  <div class="form-group">
                      <div class="input-group">
                        <input type="text" class="form-control" id="input_add_SaldoValas" value="IDR">
                        <button type="button" class="btn btn-primary btn-select" onclick="buttonAddListValas()">+</button>
                      </div>
                  </div>
                </div>
                <div class="col-3">
                  <div class="form-group">
                    <label>Kurs</label>
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <input type="number" class="form-control text-right" id="input_add_SaldoKurs" value="1.00">
                    <input type="text" class="form-control text-right" id="input_saldoAwal_devisi" placeholder="devisi" hidden>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row mt-2">
            <div class="col-4">
              <div class="form-group">
                <label>Nilai Awal</label>
              </div>
            </div>
            <div class="col-8">
              <div class="form-group">
                <input type="text" class="form-control text-right" id="input_add_SaldoNilaiAwal" 
                  value="0.00" 
                  style="font-variant-numeric: tabular-nums;" 
                  oninput="formatNumber(this)">
              </div>
            </div>
          </div>

          <div class="row mt-2">
            <div class="col-4">
              <div class="form-group">
                <label>Nilai Penyusutan</label>
              </div>
            </div>
            <div class="col-8">
              <div class="form-group">
                
                <input type="text" class="form-control text-right" id="input_add_SaldoNilaiPenyusutan" 
                  value="0.00" 
                  style="font-variant-numeric: tabular-nums;" 
                  oninput="formatNumber(this)">
              </div>
            </div>
          </div>
        </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-primary" onclick="submitSaldoAwal()">Submit</button>
  </div>
</div>
</div>
</div>

<!-- start modal select valas -->
<div class="modal fade" id="formSelectValas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Devisi</h5>
        
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table id="tabelSelectValas" class="table table-bordered table-striped"  >
          <thead id='theadCustom' class="text-center">
            <tr>
              <th scope="col">Actions</th>
              <th scope="col">Kode Valas</th>
              <th scope="col">Nama Valas</th>

            </tr>
          </thead>

          <tbody id="tabel_dataSelectValas" class="text-left" >
            <tr>

              <td></td>
              <td></td>

                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button type="button" onclick="buttonPilihValas()"><i class="bi bi-pen">Select</i></button>
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
<!-- End modal select valas-->
<!-- End modal saldo awal-->

@endsection

@section('js')

<script src="{{ asset('js/masterTable.js') }}"></script>

<script type="text/javascript">

let dataRefresh = []

function loadAll () {
  let _token = $("#_token").val();

  document.getElementById('breadcrumb').innerHTML = "Master Aktiva"

  $('#tabel').DataTable().destroy();

  $.ajax({
    url: "{!! url('masteraktivaloadall') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
    },
    success: function(res) {
      console.log(res)
      dataRefresh = res
  }})

let rowTable = ""
dataRefresh.forEach((item, i) => {
  let temp = ""
  
  // Format date to DD-MM-YYYY
  let formattedDate = '';
  if (item.Tanggal) {
    const date = new Date(item.Tanggal);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    formattedDate = `${day}-${month}-${year}`;
  }

  rowTable += `<tr>
    <td class='text-center'>
      <div class="action-buttons-wrap">
          <button data-toggle="tooltip" data-placement="top" title="Saldo Awal" class="btn-action-sm btn-action-primary" type="button" onclick="buttonSaldoAwal('${item.KodeAktiva}')"><i class="bi bi-currency-dollar"></i></button>
          <button data-toggle="tooltip" data-placement="top" title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.KodeAktiva}')"><i class="bi bi-pen"></i></button>
          <button data-toggle="tooltip" data-placement="top" title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${item.KodeAktiva}')"><i class="bi bi-trash"></i></button>
      </div>
    </td>
  <td>${item.KodeAktiva ?? ''}</td>
  <td>${item.Keterangan ?? ''}</td>
  <td>${formattedDate}</td>
  <td>${item.NamaDevisi ?? ''}</td>
  <td>${item.MyTipe ?? ''}</td>
  <td>${item.NamaPerkiraan ?? ''}</td>
  <td class='text-right'>${item.Quantity ?? ''}</td>
  <td class='text-right'>${item.Susut ?? ''}</td>
  <td>${item.Metode ?? ''}</td>
  <td>${item.akumulasi ?? ''}</td>
  <td>${item.Biaya ?? ''}</td>
  <td class='text-right'>${item.PersenBiaya1 ?? ''}</td>
  <td>${item.Biaya2 ?? ''}</td>
  <td class='text-right'>${item.PersenBiaya2 ?? ''}</td>
  </tr>`
});

   let currentLength = $("#tabel_length_visual").val() ? Number($("#tabel_length_visual").val()) : 10;
      document.getElementById("tabel_data").innerHTML = rowTable
      $("#tabel").DataTable({
        "lengthChange": false,
        "paging": true,
        "searching": true,
        "dom": 'tip',
        "pageLength": currentLength
      });

}

function buttonPilihGroupAktiva(selectedPerkiraan, selectedKeterangan, selectedPersen, selectedAkumulasi, selectedBiaya1, selectedBiaya2) {
  $("#input_add_GroupAktiva").val(selectedPerkiraan);
  $("#input_add_Susut").val(selectedPersen);
  $("#input_add_AkumulasiPenyusutan").val(selectedAkumulasi);
  $("#input_add_BiayaPenyusutan1").val(selectedBiaya1);
  $("#input_add_BiayaPenyusutan2").val(selectedBiaya2);

  $("#formAddGroupAktiva").modal("hide");

  setNewNoAktiva(selectedPerkiraan)

}

function setNewNoAktiva (Perkiraan) {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('spnoaktiva') !!}",
    type: "post",
    async: false,
    data: {
      Perkiraan,
      _token
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_add_NoAktiva").value = res[0].noAktiva
      document.getElementById("input_add_NoUrut").value = res[0].noUrut

    }})
}


function buttonGroupAktiva () {
  $("#formAddGroupAktiva").modal('toggle')
  let _token = $("#_token").val();

  $('#tabelAddGroupAktiva').DataTable().destroy();

  $.ajax({
    url: "{!! url('masteraktivaloadgroupaktiva') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
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
        <button class="btn-action-sm btn-action-primary" type="button" onclick="buttonPilihGroupAktiva('${item.Perkiraan}', '${item.keterangan}', '${item.Persen}', '${item.Akumulasi}', '${item.Biaya1}', '${item.Biaya2}')"><i class="bi bi-plus"></i></button>
      </td>
      <td>${item.Perkiraan}</td>
      <td>${item.keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataAddGroupAktiva").innerHTML = rowTable;
  $("#tabelAddGroupAktiva").DataTable({
    "lengthChange": false,
    "paging": false,
  });
}

function buttonPilihAkumulasiPenyusutan (selectedPerkiraan, selectedKeterangan) {
  // Set the selected values in the second modal
  $("#input_add_AkumulasiPenyusutan").val(selectedPerkiraan);
  $("#input_edit_AkumulasiPenyusutan").val(selectedPerkiraan);
  // You can set other fields here if needed

  // Close the first modal
  $("#formAddAkumulasiPenyusutan").modal("hide");

}

function buttonBiayaPenyusutan (kodeBiaya) {

  $("#formAddBiayaPenyusutan").modal('toggle')
  
  console.log('asd');
  let _token = $("#_token").val();

  $('#tabelAddBiayaPenyusutan').DataTable().destroy();

  $.ajax({
    url: "{!! url('masteraktivaloadbiayapenyusutan') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
    },
    success: function (res) {
      console.log(res);
      dataRefresh = res;
    },
  });

  if (kodeBiaya == '1'){

  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr>
      <td class="text-center">
        <button class="btn-action-sm btn-action-primary" type="button" onclick="buttonPilihBiayaPenyusutan('${item.Perkiraan}', '${item.Keterangan}', '1')"><i class="bi bi-plus"></i></button>
      </td>
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
  });
  document.getElementById("tabel_dataBiayaPenyusutan").innerHTML = rowTable;
} else if (kodeBiaya == '2'){
  
  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr>
      <td class="text-center">
        <button class="btn-action-sm btn-action-primary" type="button" onclick="buttonPilihBiayaPenyusutan('${item.Perkiraan}', '${item.Keterangan}', '2')"><i class="bi bi-plus"></i></button>
      </td>
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataBiayaPenyusutan").innerHTML = rowTable;
} else if (kodeBiaya == '3'){
  
  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr>
      <td class="text-center">
        <button class="btn-action-sm btn-action-primary" type="button" onclick="buttonPilihBiayaPenyusutan('${item.Perkiraan}', '${item.Keterangan}', '3')"><i class="bi bi-plus"></i></button>
      </td>
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataBiayaPenyusutan").innerHTML = rowTable;
}

  $("#tabelAddBiayaPenyusutan").DataTable({
    "lengthChange": true,
    "paging": true
  });
}

function buttonPilihBiayaPenyusutan (selectedPerkiraan, selectedKeterangan, selectorBiaya) {
  // Set the selected values in the second modal
  if (selectorBiaya == '1'){
  $("#input_add_BiayaPenyusutan1").val(selectedPerkiraan);
  $("#input_edit_BiayaPenyusutan1").val(selectedPerkiraan);
  } else if (selectorBiaya == '2'){
  $("#input_add_BiayaPenyusutan2").val(selectedPerkiraan);
  $("#input_edit_BiayaPenyusutan2").val(selectedPerkiraan);
  } else if (selectorBiaya == '3'){
  $("#input_add_BiayaPenyusutan3").val(selectedPerkiraan);
  $("#input_edit_BiayaPenyusutan3").val(selectedPerkiraan);
  }
  // You can set other fields here if needed

  // Close the first modal
  $("#formAddBiayaPenyusutan").modal("hide");

}


function buttonAkumulasiPenyusutan () {
  $("#formAddAkumulasiPenyusutan").modal('toggle')
  loadAkumulasiPenyusutan()
}


function loadAkumulasiPenyusutan() {
  console.log('asd');
  let _token = $("#_token").val();

  $('#tabelAddAkumulasiPenyusutan').DataTable().destroy();

  $.ajax({
    url: "{!! url('masteraktivaloadakumulasipenyusutan') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
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
        <button class="btn-action-sm btn-action-primary" type="button" onclick="buttonPilihAkumulasiPenyusutan('${item.Perkiraan}', '${item.keterangan}')"><i class="bi bi-plus"></i></button>
      </td>
      <td>${item.Perkiraan}</td>
      <td>${item.keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataAddAkumulasiPenyusutan").innerHTML = rowTable;
  $("#tabelAddAkumulasiPenyusutan").DataTable({
    "lengthChange": false,
    "paging": false,
  });
}

function buttonPilihDevisi(selectedPerkiraan, selectedKeterangan) {
  // Set the selected values in the second modal
  $("#input_add_DaftarDevisi").val(selectedPerkiraan);
  // You can set other fields here if needed

  // Close the first modal
  $("#formAddDevisi").modal("hide");

}

function buttonDaftarDevisi () {
  $("#formAddDevisi").modal('toggle')
  loadDevisi()
}

function buttonAddListValas () {
  $("#formSelectValas").modal('toggle')
  
  console.log('asd');
  let _token = $("#_token").val();

  $('#tabelSelectValas').DataTable().destroy();

  $.ajax({
    url: "{!! url('masteraktivaloadvalas') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
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
        <button class="btn-action-sm btn-action-primary" type="button" onclick="buttonAddPickValas('${item.KODEVLS}', '${item.KURS}')"><i class='bi bi-plus'></i></button>
      </td>
      <td>${item.KODEVLS}</td>
      <td>${item.KURS}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataSelectValas").innerHTML = rowTable;
  $("#tabelSelectValas").DataTable({
    "lengthChange": false,
    "paging": false,
  });
  
}

function buttonAddPickValas (selectedValas, selectedKurs) {
  // Set the selected values in the second modal
  $("#input_add_SaldoValas").val(selectedValas);
  $("#input_add_SaldoKurs").val(selectedKurs);
  // You can set other fields here if needed

  // Close the first modal
  $("#formSelectValas").modal("hide");

}

function loadDevisi () {
  console.log('asd');
  let _token = $("#_token").val();

  $('#tabelAddDevisi').DataTable().destroy();

  $.ajax({
    url: "{!! url('masteraktivaloaddevisi') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
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
        <button class="btn-action-sm btn-action-primary" type="button" onclick="buttonPilihDevisi('${item.Devisi}', '${item.NamaDevisi}')"><i class="bi bi-plus"></i></button>
      </td>
      <td>${item.Devisi}</td>
      <td>${item.NamaDevisi}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataAddDevisi").innerHTML = rowTable;
  $("#tabelAddDevisi").DataTable({
    "lengthChange": false,
    "paging": false,
  });
}

function buttonEdit (kode) {
  console.log(kode)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('masteraktivaspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_edit_GroupAktiva").value = res[0].NoMuka
      document.getElementById("input_edit_DaftarDevisi").value = res[0].Devisi
      document.getElementById("input_edit_NoUrut").value = res[0].NoBelakang
      document.getElementById("input_edit_NoAktiva").value = res[0].Perkiraan
      document.getElementById("input_edit_TglPerolehan").value = new Date(res[0].TglPeroleh).toLocaleDateString('en-CA');
      document.getElementById("input_edit_TglPemakaian").value = new Date(res[0].Tanggal).toLocaleDateString('en-CA');
      document.getElementById("input_edit_TipeAktiva").value = res[0].TipeAktiva
      document.getElementById("input_edit_Keterangan").value = res[0].Keterangan
      document.getElementById("input_edit_Kuantum").value = res[0].Quantity
      document.getElementById("input_edit_Susut").value = res[0].Persen
      document.getElementById("input_edit_MetodePenyusutan").value = res[0].Tipe
      document.getElementById("input_edit_AkumulasiPenyusutan").value = res[0].Akumulasi
      document.getElementById("input_edit_BiayaPenyusutan1").value = res[0].Biaya
      document.getElementById("input_edit_PersenBiayaPenyusutan1").value = res[0].PersenBiaya1
      document.getElementById("input_edit_BiayaPenyusutan2").value = res[0].Biaya2
      document.getElementById("input_edit_PersenBiayaPenyusutan2").value = res[0].PersenBiaya2
      document.getElementById("input_edit_BiayaPenyusutan3").value = res[0].biaya3
      document.getElementById("input_edit_PersenBiayaPenyusutan3").value = res[0].persenbiaya3

    }})
    $("#formEdit").modal('toggle')
}

function buttonAdd () {
      document.getElementById("input_add_GroupAktiva").value = ''
      document.getElementById("input_add_DaftarDevisi").value =''
      document.getElementById("input_add_NoUrut").value = ''
      document.getElementById("input_add_NoAktiva").value = ''
      document.getElementById("input_add_TglPerolehan").value = new Date().toISOString().split('T')[0];
      document.getElementById("input_add_TglPemakaian").value = new Date().toISOString().split('T')[0];
      document.getElementById("input_add_Keterangan").value = ''
      document.getElementById("input_add_Kuantum").value = 1
      document.getElementById("input_add_Susut").value = ''
      document.getElementById("input_add_AkumulasiPenyusutan").value = ''
      document.getElementById("input_add_BiayaPenyusutan1").value = ''
      document.getElementById("input_add_PersenBiayaPenyusutan1").value = ''
      document.getElementById("input_add_BiayaPenyusutan2").value = ''
      document.getElementById("input_add_PersenBiayaPenyusutan2").value = ''
      document.getElementById("input_add_BiayaPenyusutan3").value = ''
      document.getElementById("input_add_PersenBiayaPenyusutan3").value = ''
  
    $("#formAdd").modal('toggle')

}

function buttonDelete (kode) {
  console.log(kode)
  let _token = $("#_token").val();

  alertify.confirm('Hapus Area', 'Apakah yakin ingin menghapus Aktiva ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masteraktivaspdelete') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            kode
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
              console.log('hehe')
            } else {
              console.log(res)
              loadAll()
              alertify.success("Aktiva telah dihapus");

            }
          }})
      }
    ,function(){
      console.log('no')
    });


}

function submitEdit () {

  let _token = $("#_token").val();
  let groupAktiva = $("#input_edit_GroupAktiva").val();
  let daftarDevisi = $("#input_edit_DaftarDevisi").val();
  let noUrut = $("#input_edit_NoUrut").val();
  let noAktiva = $("#input_edit_NoAktiva").val();
  let tglPerolehan = $("#input_edit_TglPerolehan").val();
  let tglPemakaian = $("#input_edit_TglPemakaian").val();
  let tipeAktiva = $("#input_edit_TipeAktiva").val();
  let keterangan = $("#input_edit_Keterangan").val();
  let kuantum = $("#input_edit_Kuantum").val();
  let susut = $("#input_edit_Susut").val();
  let metodePenyusutan = $("#input_edit_MetodePenyusutan").val();
  let akumulasiPenyusutan = $("#input_edit_AkumulasiPenyusutan").val();
  let BP1 = $("#input_edit_BiayaPenyusutan1").val();
  let PersenBP1 = $("#input_edit_PersenBiayaPenyusutan1").val();
  let BP2 = $("#input_edit_BiayaPenyusutan2").val();
  let PersenBP2 = $("#input_edit_PersenBiayaPenyusutan2").val();
  let BP3 = $("#input_edit_BiayaPenyusutan3").val();
  let PersenBP3 = $("#input_edit_PersenBiayaPenyusutan3").val();

  if (!groupAktiva) {
    alertify.warning("Group Aktiva harus diisi");
    return;
  }

  if (!daftarDevisi) {
    alertify.warning("Daftar Devisi harus diisi");
    return;
  }

  if (!noUrut) {
    alertify.warning("No Urut harus diisi");
    return;
  }

  if (!noAktiva) {
    alertify.warning("No Aktiva harus diisi");
    return;
  }

  if (!tglPerolehan) {
    alertify.warning("Tanggal Perolehan harus diisi");
    return;
  }

  if (!tglPemakaian) {
    alertify.warning("Tanggal Pemakaian harus diisi");
    return;
  }

  if (!tipeAktiva) {
    alertify.warning("Tipe Aktiva harus diisi");
    return;
  }

  if (!keterangan) {
    alertify.warning("Keterangan harus diisi");
    return;
  }

  if (!kuantum) {
    alertify.warning("Kuantum harus diisi");
    return;
  }

  if (!susut) {
    alertify.warning("Susut harus diisi");
    return;
  }

  if (!metodePenyusutan) {
    alertify.warning("Metode Penyusutan harus diisi");
    return;
  }

  if (!akumulasiPenyusutan) {
    alertify.warning("Akumulasi Penyusutan harus diisi");
    return;
  }

  // BP1/BP2/BP3 must never be sent as a true empty string — the backend
  // expects at least a single space when the field is blank.
  const emptyToSpace = (val) => (val === null || val === undefined || val.trim() === '') ? '-' : val;

  BP1 = emptyToSpace(BP1);
  BP2 = emptyToSpace(BP2);
  BP3 = emptyToSpace(BP3);

  $.ajax({
    url: "{!! url('masteraktivaspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      groupAktiva,
      daftarDevisi,
      noUrut,
      noAktiva,
      tglPerolehan,
      tglPemakaian,
      tipeAktiva,
      keterangan,
      kuantum,
      susut,
      metodePenyusutan,
      akumulasiPenyusutan,
      BP1,
      PersenBP1,
      BP2,
      PersenBP2,
      BP3,
      PersenBP3,
    },
    success: function(res) {

      if (res != 1) {
        alertify.success("Data Aktiva telah diedit");
        loadAll()
        $("#formEdit").modal('hide')
      }  else {
        console.log(res ,'!')
        alertify.success("Data Aktiva telah diedit");
        loadAll()
        $("#formEdit").modal('hide')
      }

    }})

}

function submitAdd () {

  let _token = $("#_token").val();
  let groupAktiva = $("#input_add_GroupAktiva").val();
  let daftarDevisi = $("#input_add_DaftarDevisi").val();
  let noUrut = $("#input_add_NoUrut").val();
  let noAktiva = $("#input_add_NoAktiva").val();
  let tglPerolehan = $("#input_add_TglPerolehan").val();
  let tglPemakaian = $("#input_add_TglPemakaian").val();
  let tipeAktiva = $("#input_add_TipeAktiva").val();
  let keterangan = $("#input_add_Keterangan").val();
  let kuantum = $("#input_add_Kuantum").val();
  let susut = $("#input_add_Susut").val();
  let metodePenyusutan = $("#input_add_MetodePenyusutan").val();
  let akumulasiPenyusutan = $("#input_add_AkumulasiPenyusutan").val();
  let BP1 = $("#input_add_BiayaPenyusutan1").val() || '';
  let PersenBP1 = $("#input_add_PersenBiayaPenyusutan1").val() || 0;
  let BP2 = $("#input_add_BiayaPenyusutan2").val() || '-';
  let PersenBP2 = $("#input_add_PersenBiayaPenyusutan2").val() || 0;
  let BP3 = $("#input_add_BiayaPenyusutan3").val() || '-';
  let PersenBP3 = $("#input_add_PersenBiayaPenyusutan3").val() || 0;

  if (!groupAktiva) {
    alertify.warning("Group Aktiva harus diisi");
    return;
  }

  if (!daftarDevisi) {
    alertify.warning("Daftar Devisi harus diisi");
    return;
  }

  if (!noUrut) {
    alertify.warning("No Urut harus diisi");
    return;
  }

  if (!noAktiva) {
    alertify.warning("No Aktiva harus diisi");
    return;
  }

  if (!tglPerolehan) {
    alertify.warning("Tanggal Perolehan harus diisi");
    return;
  }

  if (!tglPemakaian) {
    alertify.warning("Tanggal Pemakaian harus diisi");
    return;
  }

  if (!tipeAktiva) {
    alertify.warning("Tipe Aktiva harus diisi");
    return;
  }

  if (!keterangan) {
    alertify.warning("Keterangan harus diisi");
    return;
  }

  if (!kuantum) {
    alertify.warning("Kuantum harus diisi");
    return;
  }

  if (!susut) {
    alertify.warning("Susut harus diisi");
    return;
  }

  if (!metodePenyusutan) {
    alertify.warning("Metode Penyusutan harus diisi");
    return;
  }

  if (!akumulasiPenyusutan) {
    alertify.warning("Akumulasi Penyusutan harus diisi");
    return;
  }

  $.ajax({
    url: "{!! url('masteraktivaspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      groupAktiva,
      daftarDevisi,
      noUrut,
      noAktiva,
      tglPerolehan,
      tglPemakaian,
      tipeAktiva,
      keterangan,
      kuantum,
      susut,
      metodePenyusutan,
      akumulasiPenyusutan,
      BP1,
      PersenBP1,
      BP2,
      PersenBP2,
      BP3,
      PersenBP3,
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning("Data Aktiva telah ditambah");
        loadAll()
        $("#formAdd").modal("hide");
      }  else {
        console.log(res ,'!')
        alertify.success("Data Aktiva telah ditambah");
        loadAll()
        $("#formAdd").modal("hide");
      }

    }})

}

function submitSaldoAwal () {

  let _token = $("#_token").val();
  let Choice = 'U'
  let Divisi = $("#input_saldoAwal_devisi").val();
  let Perkiraan = $("#nomorPerkiraan").val();
  // let Bulan = $("#input_add_DaftarDevisi").val();
  // let Tahun = $("#input_add_DaftarDevisi").val();
  let Valas = $("#input_add_SaldoValas").val();
  let kurs = $("#input_add_SaldoKurs").val();
  let Awal = parseFloat(($("#input_add_SaldoNilaiAwal").val() || 0).toString().replace(/,/g, '')) || 0;
  // let Awal = $("#input_add_SaldoNilaiAwal").val() || 0;
  let AwalSusut = parseFloat(($("#input_add_SaldoNilaiPenyusutan").val() || 0).toString().replace(/,/g, '')) || 0;

  $.ajax({
    url: "{!! url('masteraktivaspaddsaldoawal') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      Choice,
      Divisi,
      Perkiraan,
      Valas,
      kurs,
      Awal,
      AwalSusut
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning("Data Saldo Awal Perkiraan " + Perkiraan + " telah ditambah");
        loadAll()
        $("#formSaldoAwal").modal("hide");
      }  else {
        console.log(res ,'!')
        alertify.success("Data Saldo Awal Perkiraan " + Perkiraan + " telah ditambah");
        loadAll()
        $("#formSaldoAwal").modal("hide");
      }

    }})

}

function buttonPilihEditGroupAktiva(selectedPerkiraan, selectedKeterangan, selectedPersen, selectedAkumulasi, selectedBiaya1, selectedBiaya2) {
  // Set the selected values in the second modal
  $("#input_edit_GroupAktiva").val(selectedPerkiraan);
  $("#input_edit_Susut").val(selectedPersen);
  $("#input_edit_AkumulasiPenyusutan").val(selectedAkumulasi);
  $("#input_edit_BiayaPenyusutan1").val(selectedBiaya1);
  $("#input_edit_BiayaPenyusutan2").val(selectedBiaya2);
  $("#formEditGroupAktiva").modal("hide");

}

function buttonEditGroupAktiva () {
  $("#formEditGroupAktiva").modal('toggle')
  loadEditGroupAktiva()
}

function loadEditGroupAktiva() {
  console.log('asd');
  let _token = $("#_token").val();

  $('#tabelEditGroupAktiva').DataTable().destroy();

  $.ajax({
    url: "{!! url('masteraktivaloadeditgroupaktiva') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
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
      <td>${item.Perkiraan}</td>
      <td>${item.keterangan}</td>
      <td class="text-center">
        <button class="btn btn-success btn-sm" type="button" onclick="buttonPilihEditGroupAktiva('${item.Perkiraan}', '${item.keterangan}', '${item.Persen}', '${item.Akumulasi}', '${item.Biaya1}', '${item.Biaya2}')">Select</button>
      </td>
    </tr>`;
  });

  document.getElementById("tabel_dataEditGroupAktiva").innerHTML = rowTable;
  $("#tabelEditGroupAktiva").DataTable({
    "lengthChange": false,
    "paging": false,
  });
}

function buttonEditPilihDevisi(selectedPerkiraan, selectedKeterangan) {
  // Set the selected values in the second modal
  $("#input_edit_DaftarDevisi").val(selectedPerkiraan);
  // You can set other fields here if needed

  // Close the first modal
  $("#formEditDevisi").modal("hide");

}

function buttonEditDaftarDevisi () {
  $("#formEditDevisi").modal('toggle')
  loadEditDevisi()
}

function loadEditDevisi() {
  console.log('asd');
  let _token = $("#_token").val();

  $('#tabelEditDevisi').DataTable().destroy();

  $.ajax({
    url: "{!! url('masteraktivaloaddevisi') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
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
      <td>${item.Devisi}</td>
      <td>${item.NamaDevisi}</td>
      <td class="text-center">
        <button class="btn btn-success btn-sm" type="button" onclick="buttonEditPilihDevisi('${item.Devisi}', '${item.NamaDevisi}')">Select</button>
      </td>
    </tr>`;
  });

  document.getElementById("tabel_dataEditDevisi").innerHTML = rowTable;
  $("#tabelEditDevisi").DataTable({
    "lengthChange": false,
    "paging": false,
  });
}

function buttonEditPilihAkumulasiPenyusutan(selectedPerkiraan, selectedKeterangan) {
  $("#input_edit_AkumulasiPenyusutan").val(selectedPerkiraan);
  $("#formEditAkumulasiPenyusutan").modal("hide");

}

function buttonEditAkumulasiPenyusutan () {
  $("#formEditAkumulasiPenyusutan").modal('toggle')
  loadEditAkumulasiPenyusutan()
}

function loadEditAkumulasiPenyusutan() {
  console.log('asd');
  let _token = $("#_token").val();

  $('#tabelEditAkumulasiPenyusutan').DataTable().destroy();

  $.ajax({
    url: "{!! url('masteraktivaloadakumulasipenyusutan') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
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
      <td>${item.Perkiraan}</td>
      <td>${item.keterangan}</td>
      <td class="text-center">
        <button class="btn btn-success btn-sm" type="button" onclick="buttonEditPilihAkumulasiPenyusutan('${item.Perkiraan}', '${item.keterangan}')">Select</button>
      </td>
    </tr>`;
  });

  document.getElementById("tabel_dataEditAkumulasiPenyusutan").innerHTML = rowTable;
  $("#tabelEditAkumulasiPenyusutan").DataTable({
    "lengthChange": false,
    "paging": false,
  });
}

function buttonSaldoAwal (kode) {
  console.log(kode)
  let perkiraanSaldoAwalTemp = ''

      document.getElementById("input_add_SaldoNilaiAwal").value = '0,00'
      document.getElementById("input_add_SaldoNilaiPenyusutan").value = '0,00'

  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('masteraktivaspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      document.getElementById("nomorPerkiraan").value = res[0].Perkiraan
      perkiraanSaldoAwalTemp = res[0].Perkiraan
      document.getElementById("input_saldoAwal_devisi").value = res[0].Devisi

    }})

$.ajax({
    url: "{!! url('masteraktivaspdetailsaldoawal') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      perkiraan : perkiraanSaldoAwalTemp
    },
    success: function(res) {
      console.log(res)
      document.getElementById("input_add_SaldoNilaiAwal").value = res[0].Awal
      document.getElementById("input_add_SaldoNilaiPenyusutan").value = res[0].AwalSusut
      document.getElementById("input_add_SaldoValas").value = res[0].Valas
      document.getElementById("input_add_SaldoKurs").value = res[0].Kurs

  formatNumber(document.getElementById("input_add_SaldoNilaiAwal"))
  formatNumber(document.getElementById("input_add_SaldoNilaiPenyusutan"))
    }})

    $("#formSaldoAwal").modal('toggle')
}

window.onload = function(){
  loadAll();
};

</script>


@endsection
