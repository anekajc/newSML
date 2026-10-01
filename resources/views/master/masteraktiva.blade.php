@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Master Aktiva')
@section('content')



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
<!-- start modal add -->
<div class="modal fade" id="formAdd" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 900px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel">Form Add Aktiva</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body" id="formBsGrid">
        <form>
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="">

          <!-- Group Aktiva -->
          <div class="bs-form">
          <label for="input_add_GroupAktiva">Group Aktiva</label>
          <div class="input-group"><input type="text" class="form-control" id="input_add_GroupAktiva" placeholder="Group Aktiva" disabled><div class="input-group-append"><button type="button" class="btn btn-chip-biru btn-select" onclick="buttonGroupAktiva()" title="Cari"><i class="bi bi-search"></i></button></div></div>
          <label for="input_add_DaftarDevisi">Devisi</label>
          <div class="input-group"><input type="text" class="form-control" id="input_add_DaftarDevisi" placeholder="Daftar Devisi" disabled><div class="input-group-append"><button type="button" class="btn btn-chip-biru btn-select" onclick="buttonDaftarDevisi()" title="Cari"><i class="bi bi-search"></i></button></div></div>
          <label for="input_add_NoAktiva">No. Aktiva</label>
          <input type="text" class="form-control" id="input_add_NoAktiva" placeholder="No. Aktiva" disabled>
          <label for="input_add_NoUrut">No. Urut</label>
          <input type="text" class="form-control" id="input_add_NoUrut" placeholder="No. Urut" disabled>
          <label for="input_add_TipeAktiva">Tipe Aktiva</label>
          <select class="form-control" id="input_add_TipeAktiva" disabled>
                <option value="0">Aktiva Tetap</option>
              </select>
          <label for="input_add_TglPerolehan">Tgl. Perolehan</label>
          <input type="date" class="form-control" id="input_add_TglPerolehan">
          <label for="input_add_Kuantum">Kuantum</label>
          <input type="number" class="form-control text-right" id="input_add_Kuantum" value="1">
          <label for="input_add_TglPemakaian">Tgl. Pemakaian</label>
          <input type="date" class="form-control" id="input_add_TglPemakaian">
          <label for="input_add_MetodePenyusutan">Metode Susut</label>
          <select class="form-control" id="input_add_MetodePenyusutan">
                <option value="L">[L]urus</option>
                <option value="M">[M]enurun</option>
                <option value="P">[P]ajak</option>
              </select>
          <label for="input_add_Susut">Susut (%)</label>
          <input type="number" class="form-control text-right" id="input_add_Susut" placeholder="Susut(%)">

          <!-- Keterangan -->
          <label for="input_add_Keterangan">Keterangan</label>
          <div class="bs-full"><textarea class="form-control" id="input_add_Keterangan" rows="2" placeholder="Keterangan"></textarea></div>

          <!-- Akumulasi Penyusutan -->
          <label for="input_add_AkumulasiPenyusutan">Akum. Penyusutan</label>
          <div class="input-group">
            <input type="text" class="form-control" id="input_add_AkumulasiPenyusutan" placeholder="Akumulasi Penyusutan" disabled>
            <div class="input-group-append">
              <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonAkumulasiPenyusutan()" title="Cari"><i class="bi bi-search"></i></button>
            </div>
          </div>
          <div style="grid-column: 3 / -1"></div>

          <!-- Biaya Penyusutan 1-3 -->
          <label for="input_add_BiayaPenyusutan1">Biaya Penyusutan 1</label>
          <div class="input-group"><input type="text" class="form-control" id="input_add_BiayaPenyusutan1" placeholder="Biaya Penyusutan 1" disabled><div class="input-group-append"><button type="button" class="btn btn-chip-biru btn-select" onclick="buttonBiayaPenyusutan('1')" title="Cari"><i class="bi bi-search"></i></button></div></div>
          <div class="input-group" style="grid-column: 3 / -1">
                <input type="number" class="form-control percentage-input text-right" id="input_add_PersenBiayaPenyusutan1" placeholder="%">
                <span class="input-group-text">%</span>
              </div>
          <label for="input_add_BiayaPenyusutan2">Biaya Penyusutan 2</label>
          <div class="input-group"><input type="text" class="form-control" id="input_add_BiayaPenyusutan2" placeholder="Biaya Penyusutan 2" disabled><div class="input-group-append"><button type="button" class="btn btn-chip-biru btn-select" onclick="buttonBiayaPenyusutan('2')" title="Cari"><i class="bi bi-search"></i></button></div></div>
          <div class="input-group" style="grid-column: 3 / -1">
                <input type="number" class="form-control percentage-input text-right" id="input_add_PersenBiayaPenyusutan2" placeholder="%">
                <span class="input-group-text">%</span>
              </div>
          <label for="input_add_BiayaPenyusutan3">Biaya Penyusutan 3</label>
          <div class="input-group"><input type="text" class="form-control" id="input_add_BiayaPenyusutan3" placeholder="Biaya Penyusutan 3" disabled><div class="input-group-append"><button type="button" class="btn btn-chip-biru btn-select" onclick="buttonBiayaPenyusutan('3')" title="Cari"><i class="bi bi-search"></i></button></div></div>
          <div class="input-group" style="grid-column: 3 / -1">
                <input type="number" class="form-control percentage-input text-right" id="input_add_PersenBiayaPenyusutan3" placeholder="%">
                <span class="input-group-text">%</span>
              </div>
        </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitAdd()">Simpan</button>
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
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body" id="formBsGrid">
        <form>
          <input type="hidden" name="noUrut" id="input_edit_noUrut" value="">

          <!-- Group Aktiva -->
          <div class="bs-form">
          <label for="input_edit_GroupAktiva">Group Aktiva</label>
          <div class="input-group"><input type="text" class="form-control" id="input_edit_GroupAktiva" placeholder="Group Aktiva" disabled><div class="input-group-append"><button type="button" class="btn btn-chip-biru btn-select" onclick="buttonGroupAktiva()" title="Cari"><i class="bi bi-search"></i></button></div></div>
          <label for="input_edit_DaftarDevisi">Devisi</label>
          <div class="input-group"><input type="text" class="form-control" id="input_edit_DaftarDevisi" placeholder="Daftar Devisi" disabled><div class="input-group-append"><button type="button" class="btn btn-chip-biru btn-select" onclick="buttonDaftarDevisi()" title="Cari"><i class="bi bi-search"></i></button></div></div>
          <label for="input_edit_NoAktiva">No. Aktiva</label>
          <input type="text" class="form-control" id="input_edit_NoAktiva" placeholder="No. Aktiva" disabled>
          <label for="input_edit_NoUrut">No. Urut</label>
          <input type="text" class="form-control" id="input_edit_NoUrut" placeholder="No. Urut" disabled>
          <label for="input_edit_TipeAktiva">Tipe Aktiva</label>
          <select class="form-control" id="input_edit_TipeAktiva" disabled>
                <option value="0">Aktiva Tetap</option>
              </select>
          <label for="input_edit_TglPerolehan">Tgl. Perolehan</label>
          <input type="date" class="form-control" id="input_edit_TglPerolehan">
          <label for="input_edit_Kuantum">Kuantum</label>
          <input type="number" class="form-control text-right" id="input_edit_Kuantum" value="1">
          <label for="input_edit_TglPemakaian">Tgl. Pemakaian</label>
          <input type="date" class="form-control" id="input_edit_TglPemakaian">
          <label for="input_edit_MetodePenyusutan">Metode Susut</label>
          <select class="form-control" id="input_edit_MetodePenyusutan">
                <option value="L">[L]urus</option>
                <option value="M">[M]enurun</option>
                <option value="P">[P]ajak</option>
              </select>
          <label for="input_edit_Susut">Susut (%)</label>
          <input type="number" class="form-control text-right" id="input_edit_Susut" placeholder="Susut(%)">

          <!-- Keterangan -->
          <label for="input_edit_Keterangan">Keterangan</label>
          <div class="bs-full"><textarea class="form-control" id="input_edit_Keterangan" rows="2" placeholder="Keterangan"></textarea></div>

          <!-- Akumulasi Penyusutan -->
          <label for="input_edit_AkumulasiPenyusutan">Akum. Penyusutan</label>
          <div class="input-group">
            <input type="text" class="form-control" id="input_edit_AkumulasiPenyusutan" placeholder="Akumulasi Penyusutan" disabled>
            <div class="input-group-append">
              <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonAkumulasiPenyusutan()" title="Cari"><i class="bi bi-search"></i></button>
            </div>
          </div>
          <div style="grid-column: 3 / -1"></div>

          <!-- Biaya Penyusutan 1-3 -->
          <label for="input_edit_BiayaPenyusutan1">Biaya Penyusutan 1</label>
          <div class="input-group"><input type="text" class="form-control" id="input_edit_BiayaPenyusutan1" placeholder="Biaya Penyusutan 1" disabled><div class="input-group-append"><button type="button" class="btn btn-chip-biru btn-select" onclick="buttonBiayaPenyusutan('1')" title="Cari"><i class="bi bi-search"></i></button></div></div>
          <div class="input-group" style="grid-column: 3 / -1">
                <input type="number" class="form-control percentage-input text-right" id="input_edit_PersenBiayaPenyusutan1" placeholder="%">
                <span class="input-group-text">%</span>
              </div>
          <label for="input_edit_BiayaPenyusutan2">Biaya Penyusutan 2</label>
          <div class="input-group"><input type="text" class="form-control" id="input_edit_BiayaPenyusutan2" placeholder="Biaya Penyusutan 2" disabled><div class="input-group-append"><button type="button" class="btn btn-chip-biru btn-select" onclick="buttonBiayaPenyusutan('2')" title="Cari"><i class="bi bi-search"></i></button></div></div>
          <div class="input-group" style="grid-column: 3 / -1">
                <input type="number" class="form-control percentage-input text-right" id="input_edit_PersenBiayaPenyusutan2" placeholder="%">
                <span class="input-group-text">%</span>
              </div>
          <label for="input_edit_BiayaPenyusutan3">Biaya Penyusutan 3</label>
          <div class="input-group"><input type="text" class="form-control" id="input_edit_BiayaPenyusutan3" placeholder="Biaya Penyusutan 3" disabled><div class="input-group-append"><button type="button" class="btn btn-chip-biru btn-select" onclick="buttonBiayaPenyusutan('3')" title="Cari"><i class="bi bi-search"></i></button></div></div>
          <div class="input-group" style="grid-column: 3 / -1">
                <input type="number" class="form-control percentage-input text-right" id="input_edit_PersenBiayaPenyusutan3" placeholder="%">
                <span class="input-group-text">%</span>
              </div>
        </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitEdit()">Simpan</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal add-->

<!-- start modal select add group aktiva -->
<div class="modal fade picker-kas"  id="formAddGroupAktiva" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Group Aktiva</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto;">
              <table id="tabelAddGroupAktiva">
                <thead id='theadCustom' class="text-center">
                  <tr>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>

            </tr>
                </thead>
                <tbody id="tabel_dataAddGroupAktiva" class="text-left"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn picker-kas-batal" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal select add group aktiva-->

<!-- start modal select add akumulasi penyusutan -->
<div class="modal fade picker-kas"  id="formAddAkumulasiPenyusutan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Akumulasi Penyusutan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto;">
              <table id="tabelAddAkumulasiPenyusutan">
                <thead id='theadCustom' class="text-center">
                  <tr>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>
            </tr>
                </thead>
                <tbody id="tabel_dataAddAkumulasiPenyusutan" class="text-left"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn picker-kas-batal" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal select add akumulasi penyusutan-->

<!-- start modal select add akumulasi penyusutan -->
<div class="modal fade picker-kas"  id="formAddBiayaPenyusutan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Biaya Penyusutan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto;">
              <table id="tabelAddBiayaPenyusutan">
                <thead id='theadCustom' class="text-center">
                  <tr>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>
            </tr>
                </thead>
                <tbody id="tabel_dataBiayaPenyusutan" class="text-left"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn picker-kas-batal" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal select add akumulasi penyusutan-->

<!-- start modal select add devisi -->
<div class="modal fade picker-kas"  id="formAddDevisi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Devisi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto;">
              <table id="tabelAddDevisi">
                <thead id='theadCustom' class="text-center">
                  <tr>
              <th scope="col">Kode Devisi</th>
              <th scope="col">Nama Devisi</th>

            </tr>
                </thead>
                <tbody id="tabel_dataAddDevisi" class="text-left"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn picker-kas-batal" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal select add devisi-->


<!-- start modal select add group aktiva -->
<div class="modal fade picker-kas"  id="formAddGroupAktiva" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Group Aktiva</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto;">
              <table id="tabelAddGroupAktiva">
                <thead id='theadCustom' class="text-center">
                  <tr>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>

            </tr>
                </thead>
                <tbody id="tabel_dataAddGroupAktiva" class="text-left"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn picker-kas-batal" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal edit-->

<!-- start modal select  edit group aktiva -->
<div class="modal fade picker-kas"  id="formEditGroupAktiva" style='z-index:1060;' tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Group Aktiva</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto;">
              <table id="tabelEditGroupAktiva">
                <thead class="text-center">
                  <tr>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>

            </tr>
                </thead>
                <tbody id="tabel_dataEditGroupAktiva" class="text-left"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn picker-kas-batal" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal select edit group aktiva-->

<!-- start modal select edit devisi -->
<div class="modal fade picker-kas"  id="formEditDevisi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Devisi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto;">
              <table id="tabelEditDevisi">
                <thead id='theadCustom' class="text-center">
                  <tr>
              <th scope="col">Kode Devisi</th>
              <th scope="col">Nama Devisi</th>

            </tr>
                </thead>
                <tbody id="tabel_dataEditDevisi" class="text-left"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn picker-kas-batal" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal select edit devisi-->

<!-- start modal select edit akumulasi penyusutan -->
<div class="modal fade picker-kas"  id="formEditAkumulasiPenyusutan" style='z-index:1200;' tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Akumulasi Penyusutan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto;">
              <table id="tabelEditAkumulasiPenyusutan">
                <thead class="text-center">
                  <tr>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>

            </tr>
                </thead>
                <tbody id="tabel_dataEditAkumulasiPenyusutan" class="text-left"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn picker-kas-batal" data-dismiss="modal">Batal</button>
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
        
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

          <div class="bs-form bs-form-1">
          <label for="nomorPerkiraan">Kode Aktiva</label>
          <input type="text" class="form-control text-left" id="nomorPerkiraan" disabled>
        </div>

          <div class="bs-form bs-form-1">
          <label for="input_add_SaldoValas">Valas</label>
          <select class="form-control" id="input_add_SaldoValas" onchange="onChangeSaldoValas()"></select>
          <label for="input_add_SaldoKurs">Kurs</label>
          <input type="text" inputmode="decimal" class="form-control text-right format-number" id="input_add_SaldoKurs" value="1.00">
          <input type="text" class="form-control text-right" id="input_saldoAwal_devisi" placeholder="devisi" hidden>
        </div>

          <div class="bs-form bs-form-1">
          <label for="input_add_SaldoNilaiAwal">Nilai Awal</label>
          <input type="text" class="form-control text-right" id="input_add_SaldoNilaiAwal" 
                  value="0.00" 
                  style="font-variant-numeric: tabular-nums;" 
                  oninput="formatNumber(this)">

          <label for="input_add_SaldoNilaiPenyusutan">Nilai Penyusutan</label>
          <input type="text" class="form-control text-right" id="input_add_SaldoNilaiPenyusutan" 
                  value="0.00" 
                  style="font-variant-numeric: tabular-nums;" 
                  oninput="formatNumber(this)">
        </div>
        </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitSaldoAwal()">Simpan</button>
  </div>
</div>
</div>
</div>

<!-- End modal saldo awal-->

@endsection

{{-- Modal pemilih mengikuti picker purchasing (picker-kas.css / pickerKasInit()). --}}
<link rel="stylesheet" href="{!! URL::asset('css/picker-kas.css') !!}?v={{ @filemtime(base_path('public/css/picker-kas.css')) ?: '1' }}">

@section('js')

<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/master-list.js') !!}?v={{ @filemtime(base_path('public/js/master-list.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>

<script type="text/javascript">

let dataRefresh = []

// Kolom tabel daftar (lebih dari 5 kolom -> bisa digeser & disembunyikan, lihat MasterList.kolom()).
// [field, label, tampil, tipe, total, desimal]
const MAK_KOLOM = [
  ['KodeAktiva',    'Kode Aktiva',        1, 'varchar', 0, 0],
  ['Keterangan',    'Keterangan',         1, 'varchar', 0, 0],
  ['Tanggal',       'Tanggal',            1, 'varchar', 0, 0],
  ['NamaDevisi',    'Devisi',             1, 'varchar', 0, 0],
  ['MyTipe',        'Tipe Aktiva',        1, 'varchar', 0, 0],
  ['NamaPerkiraan', 'Kelompok',           1, 'varchar', 0, 0],
  ['Quantity',      'Quantity',           1, 'varchar', 0, 0],
  ['Susut',         'Susut',              1, 'varchar', 0, 0],
  ['Metode',        'Metode',             1, 'varchar', 0, 0],
  ['akumulasi',     'Akumulasi',          1, 'varchar', 0, 0],
  ['Biaya',         'Biaya Penyusutan 1', 1, 'varchar', 0, 0],
  ['PersenBiaya1',  'Persen Biaya 1',     1, 'varchar', 0, 0],
  ['Biaya2',        'Biaya Penyusutan 2', 1, 'varchar', 0, 0],
  ['PersenBiaya2',  'Persen Biaya 2',     1, 'varchar', 0, 0],
]

// Data tabel utama disimpan terpisah dari dataRefresh - pemilih (group, akumulasi, devisi, dsb)
// memakai dataRefresh untuk daftarnya sendiri, dan renderTabel() dipanggil ulang saat kolom digeser.
let dataTabel = []

function renderTabel () {
  if ($.fn.DataTable.isDataTable('#tabel')) {
    $('#tabel').DataTable().destroy();
  }

  let cols = MasterList.kolomTampil()
  document.getElementById('tabel_header').innerHTML = MasterList.headHtml(cols)

  // Tampilan sel sama seperti sebelumnya: tanggal DD-MM-YYYY, angka rata kanan.
  let tanggal = function (item) {
    if (!item.Tanggal) { return '<td></td>' }
    const date = new Date(item.Tanggal);
    return '<td>' + String(date.getDate()).padStart(2, '0') + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + date.getFullYear() + '</td>'
  }
  // Desimal < 1 dari SQL Server datang tanpa nol di depan (".00") - tambahkan jadi "0.00".
  let kanan = function (field) { return function (item) { return "<td class='text-right'>" + String(item[field] ?? '').replace(/^(-?)\./, (m, minus) => minus + '0.') + '</td>' } }
  let khusus = { Tanggal: tanggal, Quantity: kanan('Quantity'), Susut: kanan('Susut'), PersenBiaya1: kanan('PersenBiaya1'), PersenBiaya2: kanan('PersenBiaya2') }

  let rowTable = ""
  dataTabel.forEach((item, i) => {
    let aksi = `
      <div class="action-buttons-wrap">
          <button title="Saldo Awal" class="btn-action-sm btn-action-primary" type="button" onclick="buttonSaldoAwal('${item.KodeAktiva}')"><i class="bi bi-currency-dollar"></i></button>
          <button title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.KodeAktiva}')"><i class="bi bi-pen"></i></button>
          <button title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${item.KodeAktiva}')"><i class="bi bi-trash"></i></button>
      </div>`
    rowTable += MasterList.baris(item, cols, aksi, khusus)
  });

  document.getElementById("tabel_data").innerHTML = rowTable
  $("#tabel").DataTable(MasterList.opsi())
  MasterList.selesai('#tabel')
}

function loadAll () {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('masteraktivaloadall') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
    },
    success: function(res) {
      dataTabel = res
  }})

  renderTabel()
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

    rowTable += `<tr class="pick-row" onclick="buttonPilihGroupAktiva('${item.Perkiraan}', '${item.keterangan}', '${item.Persen}', '${item.Akumulasi}', '${item.Biaya1}', '${item.Biaya2}')">
      <td>${item.Perkiraan}</td>
      <td>${item.keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataAddGroupAktiva").innerHTML = rowTable;
  pickerKasInit('tabelAddGroupAktiva')
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

    rowTable += `<tr class="pick-row" onclick="buttonPilihBiayaPenyusutan('${item.Perkiraan}', '${item.Keterangan}', '1')">
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
  });
  document.getElementById("tabel_dataBiayaPenyusutan").innerHTML = rowTable;
} else if (kodeBiaya == '2'){
  
  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr class="pick-row" onclick="buttonPilihBiayaPenyusutan('${item.Perkiraan}', '${item.Keterangan}', '2')">
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataBiayaPenyusutan").innerHTML = rowTable;
} else if (kodeBiaya == '3'){
  
  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr class="pick-row" onclick="buttonPilihBiayaPenyusutan('${item.Perkiraan}', '${item.Keterangan}', '3')">
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataBiayaPenyusutan").innerHTML = rowTable;
}

  pickerKasInit('tabelAddBiayaPenyusutan')
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

    rowTable += `<tr class="pick-row" onclick="buttonPilihAkumulasiPenyusutan('${item.Perkiraan}', '${item.keterangan}')">
      <td>${item.Perkiraan}</td>
      <td>${item.keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataAddAkumulasiPenyusutan").innerHTML = rowTable;
  pickerKasInit('tabelAddAkumulasiPenyusutan')
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

// Valas Saldo Awal dipilih lewat dropdown seperti purchasing/purchaseOrder (muatDropdownValas /
// onChangeValas). Isinya dari masteraktivaloadvalas, sumber yang dulu dipakai modal browse.
let listValas = []

function muatDropdownValas () {
  $.ajax({
    url: "{!! url('masteraktivaloadvalas') !!}",
    type: "get",
    async: false,
    data: {
      _token: $("#_token").val(),
    },
    success: function (res) {
      listValas = res
    },
  });

  let selectEl = document.getElementById("input_add_SaldoValas")
  selectEl.innerHTML = ''
  listValas.forEach((item) => {
    let opt = document.createElement('option')
    opt.value = item.KODEVLS
    opt.textContent = item.KODEVLS
    selectEl.appendChild(opt)
  });

  selectEl.value = 'IDR'
}

// Valas tersimpan yang tidak ada di daftar tetap ditampilkan apa adanya.
function pilihSaldoValas (kode) {
  let selectEl = document.getElementById("input_add_SaldoValas")
  if (kode && !listValas.some(item => item.KODEVLS === kode)) {
    let opt = document.createElement('option')
    opt.value = kode
    opt.textContent = kode
    selectEl.appendChild(opt)
  }
  selectEl.value = kode
}

function onChangeSaldoValas () {
  let kode = $("#input_add_SaldoValas").val()
  let itemX = listValas.find(item => item.KODEVLS === kode)
  setNum("input_add_SaldoKurs", itemX ? itemX.KURS : '')
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

    rowTable += `<tr class="pick-row" onclick="buttonPilihDevisi('${item.Devisi}', '${item.NamaDevisi}')">
      <td>${item.Devisi}</td>
      <td>${item.NamaDevisi}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataAddDevisi").innerHTML = rowTable;
  pickerKasInit('tabelAddDevisi')
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
      document.getElementById("input_edit_Kuantum").value = MasterList.nolDepan(res[0].Quantity)
      document.getElementById("input_edit_Susut").value = (parseFloat(res[0].Persen) || 0).toFixed(2)
      document.getElementById("input_edit_MetodePenyusutan").value = res[0].Tipe
      document.getElementById("input_edit_AkumulasiPenyusutan").value = res[0].Akumulasi
      document.getElementById("input_edit_BiayaPenyusutan1").value = res[0].Biaya
      document.getElementById("input_edit_PersenBiayaPenyusutan1").value = (parseFloat(res[0].PersenBiaya1) || 0).toFixed(2)
      document.getElementById("input_edit_BiayaPenyusutan2").value = res[0].Biaya2
      document.getElementById("input_edit_PersenBiayaPenyusutan2").value = (parseFloat(res[0].PersenBiaya2) || 0).toFixed(2)
      document.getElementById("input_edit_BiayaPenyusutan3").value = res[0].biaya3
      document.getElementById("input_edit_PersenBiayaPenyusutan3").value = (parseFloat(res[0].persenbiaya3) || 0).toFixed(2)

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

  alertify.confirm('Hapus Aktiva', 'Apakah yakin ingin menghapus Aktiva ' + kode + ' ?',
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
  let kurs = $("#input_add_SaldoKurs").val().replace(/,/g, '');
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
        // Dulu cabang gagal pun menampilkan "telah ditambah" lalu menutup modal.
        alertify.warning(res);
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

    rowTable += `<tr class="pick-row" onclick="buttonPilihEditGroupAktiva('${item.Perkiraan}', '${item.keterangan}', '${item.Persen}', '${item.Akumulasi}', '${item.Biaya1}', '${item.Biaya2}')">
      <td>${item.Perkiraan}</td>
      <td>${item.keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataEditGroupAktiva").innerHTML = rowTable;
  pickerKasInit('tabelEditGroupAktiva')
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

    rowTable += `<tr class="pick-row" onclick="buttonEditPilihDevisi('${item.Devisi}', '${item.NamaDevisi}')">
      <td>${item.Devisi}</td>
      <td>${item.NamaDevisi}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataEditDevisi").innerHTML = rowTable;
  pickerKasInit('tabelEditDevisi')
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

    rowTable += `<tr class="pick-row" onclick="buttonEditPilihAkumulasiPenyusutan('${item.Perkiraan}', '${item.keterangan}')">
      <td>${item.Perkiraan}</td>
      <td>${item.keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataEditAkumulasiPenyusutan").innerHTML = rowTable;
  pickerKasInit('tabelEditAkumulasiPenyusutan')
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
      pilihSaldoValas(res[0].Valas)
      setNum("input_add_SaldoKurs", res[0].Kurs)

  formatNumber(document.getElementById("input_add_SaldoNilaiAwal"))
  formatNumber(document.getElementById("input_add_SaldoNilaiPenyusutan"))
    }})

    $("#formSaldoAwal").modal('toggle')
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
  MasterList.kolom({ href: 'masteraktiva', kolom: MAK_KOLOM, onChange: renderTabel })
  muatDropdownValas()
  loadAll();
};

</script>


@endsection
