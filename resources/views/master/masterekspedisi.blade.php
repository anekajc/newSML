@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Master Ekspedisi')
@section('content')



  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Master</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Ekspedisi</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Master Ekspedisi</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add Ekspedisi</button>
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
  /* Pasangan label + input per baris (Pph21 | Pph23), pola mastersupplier/mastercustomer. */
  #formBsGrid .bs-form.bs-form-3 { grid-template-columns: 120px minmax(0, 1fr) 70px minmax(0, 1fr) 70px minmax(0, 1fr); margin-top: 14px; }
  @media (max-width: 575.98px) {
    #formBsGrid .bs-form.bs-form-3 { grid-template-columns: 1fr; }
  }
</style>

<!-- start modal add -->
<div class="modal fade"  id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 700px">
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
          {{-- Label & cara input disamakan dengan mastersupplier/mastercustomer (satu tabel DBCUSTSUPP).
               Alamat 2, Fax & Alamat Pajak 2 tidak tampil seperti di supplier, tetapi tetap dibawa
               lewat input hidden supaya nilai lama tidak terhapus saat Edit disimpan. --}}
          <input type="hidden" id="input_add_alamat2">
          <input type="hidden" id="input_add_noFax">
          <input type="hidden" id="input_add_alamatPajak2">

            <div class="bs-form">
          <label for="input_add_kode">Kode</label>
          <input type="text" class="form-control" id="input_add_kode" placeholder="Kode">
          <label for="input_add_bentukUsaha">Bentuk Usaha</label>
          <div class="d-flex align-items-center" style="gap:8px"><select id="input_add_bentukUsaha" class="form-control" aria-label="Default select example" onchange=changePph23()>
                    <option selected value="PT">PT</option>
                    <option value="CV">CV</option>
                    <option value="PD">PD</option>
                    <option value="UD">UD</option>
                    <option value="TOKO">TOKO</option>
                    <option value="BAPAK">BAPAK</option>
                    <option value="IBU">IBU</option>
                    <option value="EXP">EXP</option>
                    <option value="-">-</option>
                  </select>
            <input type="checkbox" id="input_add_isPpn" name="" value="">
                <label class="text-left">PKP</label></div>

          <label for="input_add_nama">Nama</label>
          <div class="bs-full"><input type="text" class="form-control" id="input_add_nama" placeholder="Nama"></div>

          <label for="input_add_alamat">Alamat</label>
          <div class="bs-full"><input type="text" class="form-control" id="input_add_alamat" placeholder="Alamat"></div>

          <label for="input_add_kota">Kota</label>
          <select id="input_add_kota" class="form-control" aria-label="Default select example">
                    <option selected value="0">Pilih Kota</option>
                  </select>
          <label for="input_add_kodePos">KodePos</label>
          <input type="text" class="form-control" id="input_add_kodePos" placeholder="Kode Pos">

          <label for="input_add_negara">Negara</label>
          <input type="text" class="form-control" id="input_add_negara" placeholder="Negara">
          <label for="input_add_telepon">Telp</label>
          <input type="text" class="form-control" id="input_add_telepon" placeholder="No Telp">

          <label for="input_add_email">Email</label>
          <input type="text" class="form-control" id="input_add_email" placeholder="Email">
          <label for="input_add_aktif">Status</label>
          <select id="input_add_aktif" class="form-control" aria-label="Default select example">
                    <option selected value=1>Aktif</option>
                    <option value=0>Tidak Aktif</option>
                  </select>
        </div>

            {{-- Ekspedisi tidak memakai TOP (hanya supplier & customer). --}}
            <div class="bs-form bs-form-3">
          <label for="input_add_pph21">Pph21</label>
          <input type="number" class="form-control text-right" id="input_add_pph21" value=0.00>
          <label for="input_add_pph23">Pph23</label>
          <input type="number" class="form-control text-right" id="input_add_pph23" value=2>
          <div style="grid-column: 5 / -1"></div>
        </div>

            <div class="bs-form">
          <label for="input_add_att">PIC</label>
          <div class="bs-full"><input type="text" class="form-control" id="input_add_att" placeholder="PIC"></div>

          <label for="input_add_attPhone">PIC Phone</label>
          <input type="text" class="form-control" id="input_add_attPhone" placeholder="PIC Phone">
          <label for="input_add_attDepart">PIC Depart</label>
          <input type="text" class="form-control" id="input_add_attDepart" placeholder="PIC Depart">

          <label for="input_add_bank">Bank</label>
          <input type="text" class="form-control" id="input_add_bank" placeholder="Nama Bank">
          <label for="input_add_accNo">Acc No.</label>
          <input type="text" class="form-control" id="input_add_accNo" placeholder="Acc No.">

          <label for="input_add_atasNama">a/n</label>
          <div class="bs-full"><input type="text" class="form-control" id="input_add_atasNama" placeholder="Atas Nama"></div>
        </div>
            <br/>

            <h5>Data Pajak</h5>
            <br/>
            <div class="bs-form bs-form-1">
          <label for="input_add_NPWP">NPWP</label>
          <input type="text" class="form-control" id="input_add_NPWP" placeholder="NPWP">

          <label for="input_add_namaPajak">Nama</label>
          <input type="text" class="form-control" id="input_add_namaPajak" placeholder="Nama PKP">

          <label for="input_add_alamatPajak">Alamat</label>
          <input type="text" class="form-control" id="input_add_alamatPajak" placeholder="Alamat PKP">

          <label for="input_add_pajakKota">Kota</label>
          <input type="text" class="form-control" id="input_add_pajakKota" placeholder="Kota PKP">
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
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 700px">
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
          <input type="hidden" name="noUrut" id="input_edit_noUrut" value="" />
          {{-- Label & cara input disamakan dengan mastersupplier/mastercustomer (satu tabel DBCUSTSUPP).
               Alamat 2, Fax & Alamat Pajak 2 tidak tampil seperti di supplier, tetapi tetap dibawa
               lewat input hidden supaya nilai lama tidak terhapus saat Edit disimpan. --}}
          <input type="hidden" id="input_edit_alamat2">
          <input type="hidden" id="input_edit_noFax">
          <input type="hidden" id="input_edit_alamatPajak2">

            <div class="bs-form">
          <label for="input_edit_kode">Kode</label>
          <input type="text" class="form-control" disabled id="input_edit_kode" placeholder="Kode">
          <label for="input_edit_bentukUsaha">Bentuk Usaha</label>
          <div class="d-flex align-items-center" style="gap:8px"><select id="input_edit_bentukUsaha" class="form-control" aria-label="Default select example">
                    <option selected value="PT">PT</option>
                    <option value="CV">CV</option>
                    <option value="PD">PD</option>
                    <option value="UD">UD</option>
                    <option value="TOKO">TOKO</option>
                    <option value="BAPAK">BAPAK</option>
                    <option value="IBU">IBU</option>
                    <option value="EXP">EXP</option>
                    <option value="-">-</option>
                  </select>
            <input type="checkbox" id="input_edit_isPpn" name="" value="">
                <label class="text-left">PKP</label></div>

          <label for="input_edit_nama">Nama</label>
          <div class="bs-full"><input type="text" class="form-control" id="input_edit_nama" placeholder="Nama"></div>

          <label for="input_edit_alamat">Alamat</label>
          <div class="bs-full"><input type="text" class="form-control" id="input_edit_alamat" placeholder="Alamat"></div>

          <label for="input_edit_kota">Kota</label>
          <select id="input_edit_kota" class="form-control" aria-label="Default select example">
                    <option selected value="0">Pilih Kota</option>
                  </select>
          <label for="input_edit_kodePos">KodePos</label>
          <input type="text" class="form-control" id="input_edit_kodePos" placeholder="Kode Pos">

          <label for="input_edit_negara">Negara</label>
          <input type="text" class="form-control" id="input_edit_negara" placeholder="Negara">
          <label for="input_edit_telepon">Telp</label>
          <input type="text" class="form-control" id="input_edit_telepon" placeholder="No Telp">

          <label for="input_edit_email">Email</label>
          <input type="text" class="form-control" id="input_edit_email" placeholder="Email">
          <label for="input_edit_aktif">Status</label>
          <select id="input_edit_aktif" class="form-control" aria-label="Default select example">
                    <option selected value=1>Aktif</option>
                    <option value=0>Tidak Aktif</option>
                  </select>
        </div>

            {{-- Ekspedisi tidak memakai TOP (hanya supplier & customer). --}}
            <div class="bs-form bs-form-3">
          <label for="input_edit_pph21">Pph21</label>
          <input type="number" class="form-control text-right" id="input_edit_pph21" value=0.00>
          <label for="input_edit_pph23">Pph23</label>
          <input type="number" class="form-control text-right" id="input_edit_pph23" value=0.00>
          <div style="grid-column: 5 / -1"></div>
        </div>

            <div class="bs-form">
          <label for="input_edit_att">PIC</label>
          <div class="bs-full"><input type="text" class="form-control" id="input_edit_att" placeholder="PIC"></div>

          <label for="input_edit_attPhone">PIC Phone</label>
          <input type="text" class="form-control" id="input_edit_attPhone" placeholder="PIC Phone">
          <label for="input_edit_attDepart">PIC Depart</label>
          <input type="text" class="form-control" id="input_edit_attDepart" placeholder="PIC Depart">

          <label for="input_edit_bank">Bank</label>
          <input type="text" class="form-control" id="input_edit_bank" placeholder="Nama Bank">
          <label for="input_edit_accNo">Acc No.</label>
          <input type="text" class="form-control" id="input_edit_accNo" placeholder="Acc No.">

          <label for="input_edit_atasNama">a/n</label>
          <div class="bs-full"><input type="text" class="form-control" id="input_edit_atasNama" placeholder="Atas Nama"></div>
        </div>
            <br/>

            <h5>Data Pajak</h5>
            <br/>
            <div class="bs-form bs-form-1">
          <label for="input_edit_NPWP">NPWP</label>
          <input type="text" class="form-control" id="input_edit_NPWP" placeholder="NPWP">

          <label for="input_edit_namaPajak">Nama</label>
          <input type="text" class="form-control" id="input_edit_namaPajak" placeholder="Nama PKP">

          <label for="input_edit_alamatPajak">Alamat</label>
          <input type="text" class="form-control" id="input_edit_alamatPajak" placeholder="Alamat PKP">

          <label for="input_edit_pajakKota">Kota</label>
          <input type="text" class="form-control" id="input_edit_pajakKota" placeholder="Kota PKP">
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

<!-- start modal detail akun -->
<div class="modal fade"  id="formDetailAkun" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detail Akun</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <!-- <h1>Tes Modal</h1> -->
        <div class="container-fluid">
          <input type="hidden" id="input_harga_kodegroup" value="" /><div class="bs-form bs-form-1">
          <label for="input_target_kodesales">Kode Cust Supp</label>
          <div class="bs-full"><div class="d-flex align-items-center" style="gap:8px"><input type="text" class="form-control" id="input_target_kodesales" placeholder="Kode Cust Supp" disabled>
            <button type="button" class="btn btn-sm btn-chip-biru" onclick="buttonAddDetailAkun()">Tambah</button></div></div>
        </div>
    </div>

    <!-- ADD SUBGROUP -->

    <div id="addDetailAkun" class="showhide form-card mt-3">

            <div class="form-card-title">Tambah Detail Akun</div>

            <div class="bs-form bs-form-1">
          <label for="input_detailAkun_add_hutPiut">Hutang / Piutang</label>
          <div class="input-group">
                    <input type="text" class="form-control" id="input_detailAkun_add_hutPiut" placeholder="Hutang Piutang" disabled>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonHutangPiutang()" title="Cari"><i class="bi bi-search"></i></button>
                    </div>
                </div>
        </div>

            <div class="text-right mt-3">
              <button type="button" class="btn btn-sm btn-batal-add" onclick="closeShowSales()">Batal</button>
              <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitAddDetailAkun()">Simpan</button>
            </div>
      </div>

    <!-- END ADD SUBGROUP -->

    <!-- EDIT SUBGROUP -->

    <div id="editDetailAkun" class="showhide form-card mt-3">

            <div class="form-card-title">Edit Detail Akun</div>

            <div class="bs-form bs-form-1">
          <label for="input_detailAkun_edit_hutPiut">Hutang / Piutang</label>
          <div class="input-group">
                    <input type="text" class="form-control" id="input_detailAkun_edit_hutPiut" placeholder="Hutang Piutang" disabled>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonHutangPiutang()" title="Cari"><i class="bi bi-search"></i></button>
                    </div>
                </div>
        </div>

            <div class="text-right mt-3">
              <button type="button" class="btn btn-sm btn-batal-add" onclick="closeShowSales()">Batal</button>
              <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitEditDetailAkun()">Simpan</button>
            </div>
      </div>

    <!-- END EDIT SUBGROUP -->

        <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_detailAkun" class="table table-bordered table-striped"  >
              <thead id='theadCustom' class="text-center">
                <tr>
                  <th scope="col">Actions</th>
                  <th scope="col">Kode</th>
                  <th scope="col">Nama</th>
                  <th scope="col">Perkiraan</th>

                </tr>
              </thead>

              <tbody id="tabel_data_detailAkun" class="text-left" >
              </tbody>

            </table>
          </div>

    </div>
  </div>

</div>
</div>
</div>
<!-- End modal detail akun-->

<!-- start modal alamat kirim -->
<div class="modal fade"  id="formAlamatKirim" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Alamat Kirim</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <!-- <h1>Tes Modal</h1> -->
        <div class="container-fluid">
          <input type="hidden" id="input_harga_kodegroup" value="" /><div class="bs-form bs-form-1">
          <label for="input_alamatKirim_kodeCustSupp">Kode Cust Supp</label>
          <div class="bs-full"><div class="d-flex align-items-center" style="gap:8px"><input type="text" class="form-control" id="input_alamatKirim_kodeCustSupp" placeholder="Kode Cust Supp" disabled>
            <button type="button" class="btn btn-sm btn-chip-biru" onclick="buttonAddAlamatKirim()">Tambah</button></div></div>
        </div>
    </div>

    <!-- ADD SUBGROUP -->

    <div id="addAlamatKirim" class="showhide form-card mt-3">

            <div class="form-card-title">Tambah Alamat Kirim</div>

            <div class="bs-form">
          <label for="input_alamatKirim_nama">Nama</label>
          <div class="input-group">
                    <input type="text" class="form-control" id="input_alamatKirim_nama" placeholder="Nama">
                </div>
          <label for="input_alamatKirim_up">Up</label>
          <div class="input-group">
                    <input type="text" class="form-control" id="input_alamatKirim_up">
                </div>


            
          <label for="input_alamatKirim_alamat">Alamat</label>
          <div class="bs-full"><div class="input-group">
                    <input type="text" class="form-control" id="input_alamatKirim_alamat" placeholder="Alamat">
                </div></div>


            
          <label for="input_alamatKirim_telepon">Telepon</label>
          <div class="input-group">
                    <input type="text" class="form-control" id="input_alamatKirim_telepon" placeholder="Telepon">
                </div>
          <label for="input_alamatKirim_fax">Fax</label>
          <div class="input-group">
                    <input type="text" class="form-control" id="input_alamatKirim_fax" placeholder="Fax">
                </div>
        </div>
            

            <div class="text-right mt-3">
              <button type="button" class="btn btn-sm btn-batal-add" onclick="closeShowSales()">Batal</button>
              <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitAddAlamatKirim()">Simpan</button>
            </div>
      </div>

    <!-- END ADD SUBGROUP -->

    <!-- EDIT SUBGROUP -->

    <div id="editAlamatKirim" class="showhide form-card mt-3">

            <div class="form-card-title">Edit Alamat Kirim</div>

            <div class="bs-form">
          <label for="input_alamatKirim_edit_nama">Nama</label>
          <div class="input-group">
                    <input type="text" class="form-control" id="input_alamatKirim_edit_nama" placeholder="Nama">
                </div>
          <label for="input_alamatKirim_edit_up">Up</label>
          <div class="input-group">
                    <input type="text" class="form-control" id="input_alamatKirim_edit_up">
                </div>


            
          <label for="input_alamatKirim_edit_alamat">Alamat</label>
          <div class="bs-full"><div class="input-group">
                    <input type="text" class="form-control" id="input_alamatKirim_edit_alamat" placeholder="Alamat">
                </div></div>


            
          <label for="input_alamatKirim_edit_telepon">Telepon</label>
          <div class="input-group">
                    <input type="text" class="form-control" id="input_alamatKirim_edit_telepon" placeholder="Telepon">
                </div>
          <label for="input_alamatKirim_edit_fax">Fax</label>
          <div class="input-group">
                    <input type="text" class="form-control" id="input_alamatKirim_edit_fax" placeholder="Fax">
                </div>
        </div>

            <div class="text-right mt-3">
              <button type="button" class="btn btn-sm btn-batal-add" onclick="closeShowSales()">Batal</button>
              <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitEditAlamatKirim()">Simpan</button>
            </div>
      </div>

    <!-- END EDIT SUBGROUP -->

        <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_alamatKirim" class="table table-bordered table-striped"  >
              <thead id='theadCustom' class="text-center">
                <tr>
                  <th scope="col">Actions</th>
                  <th scope="col">Nama</th>
                  <th scope="col">Alamat</th>
                  <th scope="col">Telepon</th>
                  <th scope="col">Fax</th>

                </tr>
              </thead>

              <tbody id="tabel_data_alamatKirim" class="text-left" >
              </tbody>

            </table>
          </div>

    </div>
  </div>

</div>
</div>
</div>
<!-- End modal alamat kirim-->

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
const MEK_KOLOM = [
  ['KODECUSTSUPP', 'Kode',         1, 'varchar', 0, 0],
  ['USAHA',        'Bentuk Usaha', 1, 'varchar', 0, 0],
  ['NAMACUSTSUPP', 'Nama',         1, 'varchar', 0, 0],
  ['ALAMAT1',      'Alamat',       1, 'varchar', 0, 0],
  ['namaKota',     'Kota',         1, 'varchar', 0, 0],
  ['KODEPOS',      'Kode Pos',     1, 'varchar', 0, 0],
  ['NEGARA',       'Negara',       1, 'varchar', 0, 0],
  ['TELPON',       'Telepon',      1, 'varchar', 0, 0],
  ['EMAIL',        'Email',        1, 'varchar', 0, 0],
  ['NPPH23',       'Pph 23',       1, 'varchar', 0, 0],
  ['NPPH22',       'Pph 21',       1, 'varchar', 0, 0],
  ['IsPpn',        'PPN',          1, 'varchar', 0, 0],
]

// Data tabel utama disimpan terpisah dari dataRefresh - pemilih (valas, gudang, dsb) memakai
// dataRefresh untuk daftarnya sendiri, dan renderTabel() dipanggil ulang saat kolom digeser.
let dataTabel = []

function renderTabel () {
  if ($.fn.DataTable.isDataTable('#tabel')) {
    $('#tabel').DataTable().destroy();
  }

  let cols = MasterList.kolomTampil()
  document.getElementById('tabel_header').innerHTML = MasterList.headHtml(cols)

  // Tampilan sel sama seperti sebelumnya: nilai kosong/0 ditulis kosong, PPN berupa ikon.
  let kosong = function (field) { return function (item) { return '<td>' + (parseFloat(item[field]) ? MasterList.nolDepan(item[field]) : '') + '</td>' } }
  let khusus = {
    NPPH23: kosong('NPPH23'),
    NPPH22: kosong('NPPH22'),
    IsPpn: function (item) {
      return item.IsPpn == 0
        ? '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>'
        : '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>'
    }
  }

  let rowTable = ""
  dataTabel.forEach((item, i) => {
    let aksi = `
      <div class="action-buttons-wrap">
        <button class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.KODECUSTSUPP}')" title="Edit Ekspedisi">
              <i class="bi bi-pen"></i>
            </button>
        <button class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${item.KODECUSTSUPP}')" title="Delete Ekspedisi">
              <i class="bi bi-trash"></i>
            </button>
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
    url: "{!! url('masterekspedisiloadall') !!}",
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


// Kota berupa dropdown DBKOTA seperti mastersupplier/mastercustomer (dulu input kode + modal pemilih).
function isiKota (id) {
  $.ajax({
    url: "{!! url('masterEkspedisiLoadKota') !!}",
    type: "get",
    async: false,
    success: function(res) {
      let rowTable = `<option selected disabled value=0>Pilih Kota</option>`
      res.forEach((item, i) => {
        rowTable += `
          <option value="${item.KodeKota}">${item.NamaKota}</option>
        `
      });

      document.getElementById(id).innerHTML = rowTable
    }})
}

function buttonAdd () {

  // Form Add dikembalikan ke nilai bawaan HTML-nya (mis. Pph23 = 2), pola mastersupplier.
  $('#form .modal-body input[type="text"], #form .modal-body input[type="number"]').each(function () { this.value = this.defaultValue })
  $('#form .modal-body input[type="hidden"]').val('')
  $('#form .modal-body input[type="checkbox"]').prop('checked', false)
  $('#form .modal-body select').prop('selectedIndex', 0)

  isiKota('input_add_kota')

  $("#form").modal('toggle')

}

function buttonEdit (kode) {
  let _token = $("#_token").val();

  isiKota('input_edit_kota')

  $.ajax({
    url: "{!! url('masterEkspedisiLoadDetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      document.getElementById("input_edit_kode").value = res[0].KODECUSTSUPP
      document.getElementById("input_edit_nama").value = res[0].NAMACUSTSUPP
      document.getElementById("input_edit_bentukUsaha").value = res[0].USAHA
      document.getElementById("input_edit_alamat").value = res[0].ALAMAT1
      document.getElementById("input_edit_alamat2").value = res[0].ALAMAT2
      document.getElementById("input_edit_kota").value = res[0].Kota
      document.getElementById("input_edit_isPpn").checked = Number(res[0].IsPpn) == 1
      document.getElementById("input_edit_kodePos").value = res[0].KODEPOS
      document.getElementById("input_edit_negara").value = res[0].NEGARA
      document.getElementById("input_edit_telepon").value = res[0].TELPON
      document.getElementById("input_edit_pph23").value = (parseFloat(res[0].NPPH23) || 0).toFixed(2)
      document.getElementById("input_edit_noFax").value = res[0].FAX
      document.getElementById("input_edit_pph21").value = (parseFloat(res[0].NPPH22) || 0).toFixed(2)
      document.getElementById("input_edit_email").value = res[0].EMAIL
      document.getElementById("input_edit_aktif").value = res[0].IsAktif
      document.getElementById("input_edit_att").value = res[0].Att
      document.getElementById("input_edit_attPhone").value = res[0].AttPhone
      document.getElementById("input_edit_attDepart").value = res[0].AttDepart
      document.getElementById("input_edit_bank").value = res[0].bank
      document.getElementById("input_edit_accNo").value = res[0].NoAcc
      document.getElementById("input_edit_atasNama").value = res[0].ATN
      document.getElementById("input_edit_NPWP").value = res[0].NPWP
      document.getElementById("input_edit_pajakKota").value = res[0].KOTAPKP
      document.getElementById("input_edit_namaPajak").value = res[0].NAMAPKP
      document.getElementById("input_edit_alamatPajak").value = res[0].ALAMATPKP1
      document.getElementById("input_edit_alamatPajak2").value = res[0].ALAMATPKP2

      // document.getElementById("input_edit_kurs").value = parseFloat(res[0].KURS).toFixed(2);
    }})

    $("#formEdit").modal('toggle')
}

function buttonDelete (kode) {
  console.log(kode)
  let _token = $("#_token").val();

  alertify.confirm('Hapus Valas', 'Apakah yakin ingin menghapus Data Ekspedisi ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masterEkspedisiSubmitDelete') !!}",
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
              alertify.success("Data Ekspedisi telah dihapus");

            }
          }})
      }
    ,function(){
      console.log('no')
    });

}

function submitEdit () {

  let _token = $("#_token").val();
  
  let kodeCustSupp = $("#input_edit_kode").val(); //KodeCustSupp
  let bentukUsaha = $("#input_edit_bentukUsaha").val(); //Usaha
  let namaCustSupp = $("#input_edit_nama").val(); //NamaCustSupp
  let alamat1 = $("#input_edit_alamat").val(); //Alamat1
  let alamat2 = $("#input_edit_alamat2").val(); //Alamat2
  let kodeKota = $("#input_edit_kota").val(); //KodeKota
  let pCounter = $("#input_edit_counter").val(); //pCounter
  let isPpn = document.getElementById("input_edit_isPpn").checked ? 1 : 0; //IsPpn
  let kodePos = $("#input_edit_kodePos").val(); //KodePos
  let negara = $("#input_edit_negara").val(); //Negara

  let telepon = $("#input_edit_telepon").val(); //Telpon
  let npph23 = $("#input_edit_pph23").val(); //NPPH23
  let noFax = $("#input_edit_noFax").val(); //Fax
  let npph21 = $("#input_edit_pph21").val(); //NPPH22
  let email = $("#input_edit_email").val(); //Email
  let aktif = $("#input_edit_aktif").val(); //IsAktif

  let att = $("#input_edit_att").val(); //Att
  let attPhone = $("#input_edit_attPhone").val(); //AttPhone
  let attDepart = $("#input_edit_attDepart").val(); //AttDepart
  let bank = $("#input_edit_bank").val(); //Bank
  let accNo = $("#input_edit_accNo").val(); //NoACC
  let atasNama = $("#input_edit_atasNama").val(); //ATN

  let npwp = $("#input_edit_NPWP").val(); //NPWP
  let kotaPkp = $("#input_edit_pajakKota").val(); //KotaPKP
  let namaPkp = $("#input_edit_namaPajak").val(); //NamaPKP
  let alamatPkp = $("#input_edit_alamatPajak").val(); //AlamatPkp1
  let alamatPkp2 = $("#input_edit_alamatPajak2").val() //AlamatPkp2

  // Validasi sama dengan mastersupplier.
  if (!kodeCustSupp) {
    alertify.warning("Kode harus diisi");
    return
  }
  if (!namaCustSupp) {
    alertify.warning("Nama harus diisi");
    return
  }
  if (kodeKota == 0 || !kodeKota) {
    alertify.warning("Kota harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('masterEkspedisiSubmitEdit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kodeCustSupp,
      bentukUsaha,
      namaCustSupp,
      alamat1,
      alamat2,
      
      kodeKota,
      counter: pCounter,
      isPpn,
      kodePos,
      negara,
      telepon,
      npph23,
      
      fax: noFax,
      npph21,
      email,
      isAktif : aktif,
      att,
      attPhone,
      attDepart,
      bank,
      accNo,
      atasNama,
      npwp,
      kotaPkp,
      
      namaPkp,
      alamatPkp,
      alamatPkp2
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        alertify.success("Data Ekspedisi berhasil dikoreksi.");
        loadAll()
        $("#formEdit").modal('toggle')
      }

    }})

}

function submitAdd () {

  let _token = $("#_token").val();
  
  let kodeCustSupp = $("#input_add_kode").val(); //KodeCustSupp
  let bentukUsaha = $("#input_add_bentukUsaha").val(); //Usaha
  let namaCustSupp = $("#input_add_nama").val(); //NamaCustSupp
  let alamat1 = $("#input_add_alamat").val(); //Alamat1
  let alamat2 = $("#input_add_alamat2").val(); //Alamat2
  let kodeKota = $("#input_add_kota").val(); //KodeKota
  let IsPpn = document.getElementById("input_add_isPpn").checked ? 1 : 0; //IsPpn

  let kodePos = $("#input_add_kodePos").val(); //KodePos
  let negara = $("#input_add_negara").val(); //Negara

  let telepon = $("#input_add_telepon").val(); //Telpon
  let npph23 = $("#input_add_pph23").val(); //NPPH23
  let noFax = $("#input_add_noFax").val(); //Fax
  let npph21 = $("#input_add_pph21").val(); //NPPH22
  let email = $("#input_add_email").val(); //Email
  let aktif = $("#input_add_aktif").val(); //IsAktif

  let att = $("#input_add_att").val(); //Att
  let attPhone = $("#input_add_attPhone").val(); //AttPhone
  let attDepart = $("#input_add_attDepart").val(); //AttDepart
  let bank = $("#input_add_bank").val(); //Bank
  let accNo = $("#input_add_accNo").val(); //NoACC
  let atasNama = $("#input_add_atasNama").val(); //An

  let npwp = $("#input_add_NPWP").val(); //NPWP
  let kotaPkp = $("#input_add_pajakKota").val(); //KotaPKP
  let namaPkp = $("#input_add_namaPajak").val(); //NamaPKP
  let alamatPkp = $("#input_add_alamatPajak").val(); //AlamatPkp1
  let alamatPkp2 = $("#input_add_alamatPajak2").val() //AlamatPkp2

  if(!attPhone){
    alertify.warning('PIC Phone Wajib Diisi')
    // dulu tanpa return - pesannya "wajib" tetapi data tetap tersimpan
    return
  }

  // Validasi sama dengan mastersupplier.
  if (!kodeCustSupp) {
    alertify.warning("Kode harus diisi");
    return
  }
  if (!namaCustSupp) {
    alertify.warning("Nama harus diisi");
    return
  }
  if (kodeKota == 0 || !kodeKota) {
    alertify.warning("Kota harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('masterEkspedisiSubmitAdd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kodeCustSupp,
      bentukUsaha,
      namaCustSupp,
      alamat1,
      alamat2,
      
      kodeKota,
      IsPpn,
      kodePos,
      negara,
      telepon,
      npph23,
      
      fax: noFax,
      npph21,
      email,
      isAktif : aktif,
      att,
      attPhone,
      
      attDepart,
      bank,
      accNo,
      atasNama,
      npwp,
      kotaPkp,
      
      namaPkp,
      alamatPkp,
      alamatPkp2
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        alertify.success("Data Ekspedisi berhasil ditambahkan.");
        loadAll()
        $("#form").modal('toggle')
      }

    }})

}

let kodeCustSuppTemp = ''

function buttonEditDetailAkun (kodeCustSupp, urutData) {

urutDetailAkunTemp = urutData;

  $('.showhide').hide();
  $.ajax({
    url: "{!! url('masterEkspedisiLoadDetailAkunEdit') !!}",
    type: "get",
    async: false,
    data: {
      kodeCustSupp,
      urutData,
    },
    success: function(res) {
      document.getElementById("input_detailAkun_edit_hutPiut").value = res[0].Perkiraan
    }})

  $('#editDetailAkun').show()
}

function buttonDetailAkun (kodeCustSupp) {
  
  kodeCustSuppTemp = kodeCustSupp;
  
  $('.showhide').hide();

    $.ajax({
      url: "{!! url('masterEkspedisiLoadDetailAkun') !!}",
      type: "get",
      async: false,
      data: {
        kodeCustSupp
      },
      success: function(res) {
        let rowTable = ''

        res.forEach((item, i) => {
          rowTable += `
          <tr>
          <td class="text-center">
            <button class="btn btn-success btn-sm hover-tooltip" onclick="buttonEditDetailAkun('${item.KodeCustSupp}', '${item.Urut}')" type="button" data-tooltip="Edit Detail Akun"><i class="bi bi-pen"></i></button>
            <button class="btn btn-danger btn-sm hover-tooltip" onclick="buttonDeleteDetailAkun('${item.KodeCustSupp}', '${item.Urut}')" type="button" data-tooltip="Delete Detail Akun" ><i class="bi bi-trash"></i></button>
          </td>
          <td>${item.KodeCustSupp}</td>
          <td>${item.NamaPerkiraan}</td>
          <td>${item.Perk_Perkiraan}</td>
          </tr>
          `
        });
        
        document.getElementById("tabel_data_detailAkun").innerHTML = rowTable
        document.getElementById("input_target_kodesales").value = kodeCustSupp

      }})


  $("#formDetailAkun").modal('toggle')
}

function buttonAddDetailAkun () {

  document.getElementById('input_detailAkun_add_hutPiut').value = ''

  $('.showhide').hide();
  $('#addDetailAkun').show()
}

function submitAddDetailAkun () {

  let _token = $("#_token").val();

  let kodeCustSupp = $("#input_target_kodesales").val();
  let Perkiraan = $("#input_detailAkun_add_hutPiut").val();
  let Choice = "I"

  $.ajax({
    url: "{!! url('masterEkspedisiAddDetailAkun') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodeCustSupp,
      Perkiraan,
      choice: Choice
    },
    success: function(res) {
      console.log(res)
      $('.showhide').hide();
      refreshTableDetailAkun(kodeCustSuppTemp)

    }})

}

function refreshTableDetailAkun (kodeCustSupp){

$.ajax({
      url: "{!! url('masterEkspedisiLoadDetailAkun') !!}",
      type: "get",
      async: false,
      data: {
        kodeCustSupp
      },
      success: function(res) {
        let rowTable = ''

        res.forEach((item, i) => {
          rowTable += `
          <tr>
          <td class="text-center">
            <button class="btn btn-success btn-sm hover-tooltip" onclick="buttonEditDetailAkun('${item.KodeCustSupp}', '${item.Urut}')" type="button" data-tooltip="Edit Detail Akun"><i class="bi bi-pen"></i></button>
            <button class="btn btn-danger btn-sm hover-tooltip" onclick="buttonDeleteDetailAkun('${item.KodeCustSupp}', '${item.Urut}')" type="button" data-tooltip="Delete Detail Akun" ><i class="bi bi-trash"></i></button>
          </td>
          <td>${item.KodeCustSupp}</td>
          <td>${item.NamaPerkiraan}</td>
          <td>${item.Perk_Perkiraan}</td>
          </tr>
          `
        });
        
        document.getElementById("tabel_data_detailAkun").innerHTML = rowTable
        document.getElementById("input_target_kodesales").value = kodeCustSupp

      }})

}

function buttonDeleteDetailAkun (kodeCustSupp, urutData) {
  
  let _token = $("#_token").val();
  let Choice = 'D';

  alertify.confirm('Hapus Harga', 'Apakah yakin ingin menghapus Sales ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masterEkspedisiDeleteDetailAkun') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            kodeCustSupp,
            urutData,
            choice : Choice
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              refreshTableDetailAkun(kodeCustSupp)
              alertify.success("Data Detail Akun telah dihapus");

          }
          }})
      }
    ,function(){
      console.log('no')
    });

}

urutDetailAkunTemp = 0

function submitEditDetailAkun () {

  let _token = $("#_token").val();

  let kodeCustSupp = $("#input_target_kodesales").val();
  let OldKodeCustSupp = $("#input_target_kodesales").val();
  let Perkiraan = $("#input_detailAkun_edit_hutPiut").val();
  let Choice = 'U'

$.ajax({
  url: "{!! url('masterEkspedisiSubmitEditDetailAkun') !!}",
  type: "post",
  async: false,
  data: {
    _token,
    choice : Choice,
    kodeCustSupp,
    OldKodeCustSupp,
    Perkiraan,
    urutData : urutDetailAkunTemp
  },
  success: function(res) {
    console.log(res)
    $('.showhide').hide();
    refreshTableDetailAkun(kodeCustSupp)
    alertify.success("Data Detail Akun telah dikoreksi");
  }})

}

function refreshTableAlamatKirim (kodeCustSupp){

$.ajax({
      url: "{!! url('masterEkspedisiLoadAlamatKirim') !!}",
      type: "get",
      async: false,
      data: {
        kodeCustSupp
      },
      success: function(res) {
        let rowTable = ''

        res.forEach((item, i) => {
          rowTable += `
          <tr>
          <td class="text-center">
            <button class="btn btn-success btn-sm hover-tooltip" onclick="buttonEditAlamatKirim('${item.KodeCustSupp}', '${item.Nomor}')" type="button" data-tooltip="Edit Detail Akun"><i class="bi bi-pen"></i></button>
            <button class="btn btn-danger btn-sm hover-tooltip" onclick="buttonDeleteAlamatKirim('${item.KodeCustSupp}', '${item.Nomor}')" type="button" data-tooltip="Delete Detail Akun" ><i class="bi bi-trash"></i></button>
          </td>
          <td>${item.Nama}</td>
          <td>${item.AlamatTxt}</td>
          <td>${item.Telp}</td>
          <td>${item.Fax}</td>
          </tr>
          `
        });
        
        document.getElementById("tabel_data_alamatKirim").innerHTML = rowTable
        document.getElementById("input_alamatKirim_kodeCustSupp").value = kodeCustSupp

      }})

}

function buttonAlamatKirim (kodeCustSupp) {
  
  kodeCustSuppTemp = kodeCustSupp;
  
  $('.showhide').hide();

    $.ajax({
      url: "{!! url('masterEkspedisiLoadAlamatKirim') !!}",
      type: "get",
      async: false,
      data: {
        kodeCustSupp
      },
      success: function(res) {
        let rowTable = ''

        res.forEach((item, i) => {
          rowTable += `
          <tr>
          <td class="text-center">
            <button class="btn btn-success btn-sm hover-tooltip" onclick="buttonEditAlamatKirim('${item.KodeCustSupp}', '${item.Nomor}')" type="button" data-tooltip="Edit Detail Akun"><i class="bi bi-pen"></i></button>
            <button class="btn btn-danger btn-sm hover-tooltip" onclick="buttonDeleteAlamatKirim('${item.KodeCustSupp}', '${item.Nomor}')" type="button" data-tooltip="Delete Detail Akun" ><i class="bi bi-trash"></i></button>
          </td>
          <td>${item.Nama}</td>
          <td>${item.AlamatTxt}</td>
          <td>${item.Telp}</td>
          <td>${item.Fax}</td>
          </tr>
          `
        });
        
        document.getElementById("tabel_data_alamatKirim").innerHTML = rowTable
        document.getElementById("input_alamatKirim_kodeCustSupp").value = kodeCustSupp

      }})


  $("#formAlamatKirim").modal('toggle')
}

function buttonAddAlamatKirim () {

  $('.showhide').hide();
  $('#addAlamatKirim').show()
}

function buttonEditAlamatKirim (kodeCustSupp, urutData) {

urutDetailAkunTemp = urutData;

  $('.showhide').hide();
  $.ajax({
    url: "{!! url('masterEkspedisiLoadDetailAkunEdit') !!}",
    type: "get",
    async: false,
    data: {
      kodeCustSupp,
      urutData,
    },
    success: function(res) {
      document.getElementById("input_detailAkun_edit_hutPiut").value = res[0].Perkiraan
    }})

  $('#editDetailAkun').show()
}

function submitAddAlamatKirim () {

  let _token = $("#_token").val();

  let kodeCustSupp = $("#input_alamatKirim_kodeCustSupp").val();

  let nomorUrutTemp = 0

  $.ajax({
      url: "{!! url('masterEkspedisiGetNomorAlamatKirim') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        kodeCustSupp
      },
      success: function(res) {
        nomorUrutTemp = parseInt(res[0].Nomor) + 1
      }})

  let Alamat = $("#input_alamatKirim_alamat").val();
  let Telp = $("#input_alamatKirim_telepon").val();
  let Fax = $("#input_alamatKirim_fax").val();
  let up = $("#input_alamatKirim_up").val();
  let Nama = $("#input_alamatKirim_nama").val();

  $.ajax({
    url: "{!! url('masterEkspedisiAddAlamatKirim') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodeCustSupp,
      Nama,
      Alamat,
      Telp,
      Fax,
      Nomor : nomorUrutTemp
    },
    success: function(res) {
      console.log(res)
      $('.showhide').hide();
      refreshTableAlamatKirim(kodeCustSuppTemp)

    }})

}

function buttonDeleteAlamatKirim (kodeCustSupp, nomor) {
  
  let _token = $("#_token").val();

  alertify.confirm('Hapus Data Alamat Kirim', 'Apakah yakin ingin menghapus Alamat Kirim ini?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masterEkspedisiDeleteAlamatKirim') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            kodeCustSupp,
            Nomor : nomor
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              refreshTableAlamatKirim(kodeCustSupp)
              alertify.success("Data Alamat Kirim telah dihapus");

          }
          }})
      }
    ,function(){
      console.log('no')
    });

}

function buttonEditAlamatKirim (kodeCustSupp, nomor) {

  nomorUrutTemp = nomor

  $('.showhide').hide();
  $.ajax({
    url: "{!! url('masterEkspedisiLoadAlamatKirimEdit') !!}",
    type: "get",
    async: false,
    data: {
      kodeCustSupp,
      Nomor: nomor,
    },
    success: function(res) {
      document.getElementById("input_alamatKirim_edit_nama").value = res[0].Nama
      document.getElementById("input_alamatKirim_edit_up").value = res[0].UP
      document.getElementById("input_alamatKirim_edit_alamat").value = res[0].Alamat
      document.getElementById("input_alamatKirim_edit_telepon").value = res[0].Telp
      document.getElementById("input_alamatKirim_edit_fax").value = res[0].Fax
    }})

  $('#editAlamatKirim').show()
}

function submitEditAlamatKirim () {

  let _token = $("#_token").val();

  let kodeCustSupp = $("#input_alamatKirim_kodeCustSupp").val();
  let nama = $("#input_alamatKirim_edit_nama").val();
  let up = $("#input_alamatKirim_edit_up").val();
  let alamat = $("#input_alamatKirim_edit_alamat").val();
  let telp = $("#input_alamatKirim_edit_telepon").val();
  let fax = $("#input_alamatKirim_edit_fax").val();
  
$.ajax({
  url: "{!! url('masterEkspedisiSubmitEditAlamatKirim') !!}",
  type: "post",
  async: false,
  data: {
    _token,
    kodeCustSupp,
    nama,
    up,
    alamat,
    telp,
    fax,
    nomor : nomorUrutTemp
  },
  success: function(res) {
    console.log(res)
    $('.showhide').hide();
    refreshTableAlamatKirim(kodeCustSupp)
    alertify.success("Data Alamat Kirim telah dikoreksi");
  }})

}

window.onload = function(){
  MasterList.kolom({ href: 'masterekspedisi', kolom: MEK_KOLOM, onChange: renderTabel })
  loadAll();
};

function closeShowSales () {
  $('.showhide').hide();
}

function buttonHutangPiutang () {
  console.log('asd');

  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('masterEkspedisiLoadHutangPiutang') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
      kodeCustSuppTemp
    },
    success: function (res) {
      console.log(res);
      dataRefresh = res;
    },
  });

  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr class="pick-row" onclick="buttonSelectHutangPiutang('${item.Perkiraan}')">
      <td>${item.Perkiraan}</td>
      <td>${item.keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Perkiraan</th>
    <th scope="col">Keterangan</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'Merk'

  pickerKasInit('tabelModalOpen')
  
  $("#formModalOpen").modal('toggle')
}

function buttonSelectHutangPiutang(perkiraan){
  document.getElementById('input_detailAkun_add_hutPiut').value = perkiraan;
  document.getElementById('input_detailAkun_edit_hutPiut').value = perkiraan;

  $("#formModalOpen").modal("hide");
}

function buttonKota(searchValue = '') {

    if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
        $('#tabelModalOpen').DataTable().destroy();
    }

    $.ajax({
    url: "{!! url('masterEkspedisiLoadKota') !!}",
    type: "get",
    async: false,
    data: {
      kodeCustSuppTemp
    },
    success: function (res) {
      console.log(res);
      dataRefresh = res;
    },
  });

    let rowTable = "";
    dataRefresh.forEach((item, i) => {
        rowTable += `<tr class="pick-row" onclick="buttonSelectKota('${item.KodeKota}', '${item.NamaKota}', '${item.KodeArea}')">
            <td>${item.KodeKota}</td>
            <td>${item.NamaKota}</td>
            <td>${item.KodeArea}</td>
        </tr>`;
    });

    document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

    let headerTable = `
        <tr>
            <th scope="col">Kode Kota</th>
            <th scope="col">Nama Kota</th>
            <th scope="col">Kode Area</th>
        </tr>
    `;
    document.querySelector("#theadOpen").innerHTML = headerTable;
    document.getElementById("namaModalOpen").innerHTML = 'Kota';

    // Initialize DataTable
    currentDataTable = pickerKasInit('tabelModalOpen')

    // If search value is provided, search and auto-select
    if (searchValue) {
        handleSearchAndAutoSelect(searchValue);
    }

    if (!searchValue || testModalOpener !== 0) {
        $("#formModalOpen").modal('toggle');
    }

}

function buttonSelectKota(kode, nama, area){
  document.getElementById('input_add_kota').value = kode;
  document.getElementById('input_add_namaArea').value = nama;
  document.getElementById('input_add_kodeArea').value = area;

  document.getElementById('input_edit_kota').value = kode;
  document.getElementById('input_edit_namaArea').value = nama;
  document.getElementById('input_edit_kodeArea').value = area;

  $("#formModalOpen").modal("hide");
}

let testModalOpener = 0

function handleSearchAndAutoSelect(searchValue) {
    if (!currentDataTable) return;

    // Search in DataTable
    currentDataTable.search(searchValue).draw();

    // Get filtered data immediately (no timeout)
    const filteredData = currentDataTable.rows({ search: 'applied' }).data().toArray();
    
    // If exactly one match found, auto-select it
    if (filteredData.length === 1) {
        // Get the matched row data
        const rowData = currentDataTable.row({ search: 'applied' }).data();
        if (rowData && rowData.length >= 3) {
            // Baris pemilih tidak punya kolom tombol lagi (dipilih dengan klik baris),
            // jadi kolom data mulai dari indeks 0.
            const kodeKota = $(rowData[0]).text() || rowData[0];
            const namaKota = $(rowData[1]).text() || rowData[1];
            const kodeArea = $(rowData[2]).text() || rowData[2];
            
            // Auto-select the matched item
            buttonSelectKota(kodeKota, namaKota, kodeArea);
            testModalOpener = 0
            console.log(testModalOpener)
        }
    } else if (filteredData.length !== 1){
        testModalOpener = 1
        console.log(testModalOpener)
    }
}

// Reusable function to setup Enter key functionality for any input
function setupEnterKeySearch(inputId, buttonFunction, options = {}) {
    const input = document.getElementById(inputId);
    
    if (!input) {
        console.error(`Input with ID '${inputId}' not found`);
        return;
    }

    // Enter key functionality
    input.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            
            const searchValue = this.value.trim();
            
            // Only proceed if there's a value (unless allowEmpty is true)
            if (searchValue || options.allowEmpty) {
                if (typeof buttonFunction === 'function') {
                    buttonFunction(searchValue);
                } else if (typeof window[buttonFunction] === 'function') {
                    window[buttonFunction](searchValue);
                } else {
                    console.error(`Function '${buttonFunction}' not found`);
                }
            }
        }
    });

    // Find the button and add click functionality
    const button = input.parentElement.querySelector('button');
    
    if (button) {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            
            const searchValue = input.value.trim(); // Get value from the input, not this
            
            // Only proceed if there's a value (unless allowEmpty is true)
            if (searchValue || options.allowEmpty) {
                if (typeof buttonFunction === 'function') {
                    buttonFunction(searchValue);
                } else if (typeof window[buttonFunction] === 'function') {
                    window[buttonFunction](searchValue);
                } else {
                    console.error(`Function '${buttonFunction}' not found`);
                }
            }
        });
    }
}

// Dinonaktifkan: Kota sekarang dropdown seperti mastersupplier, jadi pencarian kode kota lewat
// Enter / modal pemilih (buttonKota) tidak dipakai lagi di form Add/Edit.
// document.addEventListener('DOMContentLoaded', function() {
//     setupEnterKeySearch('input_add_kota', 'buttonKota', { allowEmpty: true });
//     setupEnterKeySearch('input_edit_kota', 'buttonKota', { allowEmpty: true });
// });

// Pph23 bawaan mengikuti Bentuk Usaha, sama dengan mastersupplier.
function changePph23() {
  let valueUsaha = document.getElementById('input_add_bentukUsaha').value

  if (valueUsaha == 'PT' || valueUsaha == 'CV') {
    document.getElementById('input_add_pph23').value = 2
  } else {
    document.getElementById('input_add_pph23').value = 2.5
  }
}


</script>

<!-- start modal select modal open ( 1 modal buat beberapa fungsi, jadi tinggal inject data ) -->
<div class="modal fade picker-kas" id="formModalOpen" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="namaModalOpen"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto;">
              <table id="tabelModalOpen">
                <thead id='theadOpen' class="text-center">
                  <tr></tr>
                </thead>
                <tbody id="tabel_dataModalOpen" class="text-left"></tbody>
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
<!-- End modal select modal open ( 1 modal buat beberapa fungsi, jadi tinggal inject data )-->


@endsection
