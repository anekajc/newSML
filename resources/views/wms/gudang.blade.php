@extends('newmasterTest')
@section('page-title', 'Gudang')

@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/wms-tampilan-baru.css') !!}?v={{ @filemtime(public_path('css/wms-tampilan-baru.css')) ?: '1' }}">
@endsection


@section('buttons')
<button type="button" class="btn btn-primary btn-sm btn-top" onclick="loadAll()" rel="tooltip" data-placement="bottom" title="Refresh" style="width: 30px;"><i class="bi bi-arrow-repeat"></i></button>&nbsp;&nbsp;


<button type="button" class="btn btn-secondary btn-sm btn-top" data-toggle="modal" onclick="showRak()" rel="tooltip" data-placement="bottom" title="Rak/Lokasi" style="width: 30px;"><i class="bi bi-pin-map"></i></button>&nbsp;&nbsp;
@endsection

@section('content')
<div class="container-fluid">
{{-- <h1>Gudang</h1> - judul pindah ke header (page-title) --}}
</div>

<div id="printContainer" style="display:none">

</div>

<div id="tempPrintContainer" style="display:none">

</div>

<div class="container">


  <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
  <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS!!}" />
  <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
  <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  <div class="card wms-kartu">
    {{-- card-header lama (isi tab-nya sudah dikomentari) tidak ditampilkan:
    <div class="card-header">
      <div class="row">
        <!-- <nav style="width: 100%;">
          <div class="nav nav-tabs col-12" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true" style="color: black;">Outstanding PO</a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false" style="color: black;">Penerimaan Barang</a>
          </div>
        </nav> -->
      </div>
    </div> --}}
      <div class="card-body">
      <div class="row">
        <div class="col-12" style="padding-left: 20px; padding-right: 20px; overflow:auto;">
          <table class="data-table" id="tabel_gudang" style=" background: white;">
            <thead>
              <tr>
                <th width="15%">Kode</th>
                <th width="30%">Nama</th>
                <th>Alamat</th>
                <th width="10%">Produksi</th>
                <th width="10%">Rusak</th>
              </tr>
            </thead>
            <tbody id="gudang_data">
              @if (count($gudang) > 0)
                @for ($i = 0; $i < count($gudang); $i++)
                  <tr id="{!! $i !!}-tr" onclick="select({!! $i !!}, {!! $i !!}, '{!! $gudang[$i]->KODEGDG !!}', '{!! $gudang[$i]->NAMA !!}','{!! $gudang[$i]->Alamat !!}', {!! $gudang[$i]->IsProduksi !!}, {!! $gudang[$i]->IsRusak !!})">
                    <td>{!! $gudang[$i]->KODEGDG !!}</td>
                    <td>{!! $gudang[$i]->NAMA !!}</td>
                    <td>{!! $gudang[$i]->Alamat !!}</td>
                    @if ($gudang[$i]->IsProduksi == 1)
                      <td><i class="bi bi-check-circle text-success"></i></td>
                    @else
                      <td><i class="bi bi-x-circle text-danger"></i></td>
                    @endif
                    @if ($gudang[$i]->IsRusak == 1)
                      <td><i class="bi bi-check-circle text-success"></i></td>
                    @else
                      <td><i class="bi bi-x-circle text-danger"></i></td>
                    @endif
                  </tr>
                @endfor
              @else
                <tr>
                  <td colspan="5">Tidak ada data gudang ditemukan.</td>
                  <td style="display: none;"></td>
                  <td style="display: none;"></td>
                  <td style="display: none;"></td>
                  <td style="display: none;"></td>
                </tr>
              @endif
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- start modal add gudang -->
<!-- <div class="modal fade" id="addGudang" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Tambah Gudang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="reset()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="kode">Kode Gudang</label>
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <input type="text" class="form-control" id="kode" placeholder="Kode Gudang" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="nama">Nama Gudang</label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <input type="text" class="form-control" id="nama" placeholder="Nama Gudang" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="alamat">Alamat</label>
            </div>
          </div>
          <div class="col-8">
            <div class="form-group">
              <textarea class="form-control" id="alamat" rows="1" placeholder="Alamat" required></textarea>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="produksi">Produksi?</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <select class="form-control" id="produksi">
                <option value="0">Tidak</option>
                <option value="1">Ya</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="rusak">Rusak?</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <select class="form-control" id="rusak">
                <option value="0">Tidak</option>
                <option value="1">Ya</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="reset()">Batal</button>
        <button type="button" class="btn btn-primary" onclick="add()">Tambah</button>
      </div>
    </div>
  </div>
</div> -->
<!-- End modal add gudang -->

<!-- start modal edit gudang -->
<!-- <div class="modal fade" id="editGudang" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Koreksi Gudang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="reset('e')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="eid" required>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="ekode">Kode Gudang</label>
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <input type="text" class="form-control" id="ekode" placeholder="Kode Gudang" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="enama">Nama Gudang</label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <input type="text" class="form-control" id="enama" placeholder="Nama Gudang" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="alamat">Alamat</label>
            </div>
          </div>
          <div class="col-8">
            <div class="form-group">
              <textarea class="form-control" id="ealamat" rows="1" placeholder="Alamat" required></textarea>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="eproduksi">Produksi?</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <select class="form-control" id="eproduksi">
                <option value="0">Tidak</option>
                <option value="1">Ya</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="erusak">Rusak?</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <select class="form-control" id="erusak">
                <option value="0">Tidak</option>
                <option value="1">Ya</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="reset('e')">Batal</button>
        <button type="button" class="btn btn-primary" onclick="edit()">Edit</button>
      </div>
    </div>
  </div>
</div> -->
<!-- End modal edit gudang -->

<!-- start modal add detail Rak -->
<div class="modal fade" id="detailRak" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Rak Gudang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="resetDetail()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12">
            <h7 class="modal-title">Gudang : </h7><h7 class="modal-title" id="lkdgudang"></h7><h7> ( </h7><h7 class="modal-title" id="lnmgudang"></h7><h7> )</h7>
          </div>
        </div>
        <hr style="margin-top: 0.5rem; margin-bottom: 0.5rem;">
        <div id="contentDetailRak">
          <div class="form-group">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th width='25%'>Kode Rak</th>
                  <th>Nama Rak</th>
                  <!-- <th width='10%'>Tingkat</th>
                  <th width='10%'>Kolom</th>
                  <th width='10%'>Baris</th> -->
                </tr>
              </thead>
              <tbody id="rakgudang">
              </tbody>
            </table>
          </div>
          <hr style="margin-top: 0.5rem; margin-bottom: 0.2rem;">
          <div class="form-group">
            <button type="button" class="btn btn-success btn-sm" onclick="showDivTambahPostingRak()" rel="tooltip" data-placement="bottom" title="Tambah" style="width: 30px;"><i class="bi bi-plus-circle"></i></button>
            <button type="button" class="btn btn-warning btn-sm" onclick="showEditDetailRak()" rel="tooltip" data-placement="bottom" title="Koreksi" style="width: 30px;"><i class="bi bi-pencil-square"></i></button>
            <button type="button" class="btn btn-danger btn-sm" onclick="eraseDetailRak()" rel="tooltip" data-placement="bottom" title="Hapus" style="width: 30px;"><i class="bi bi-trash"></i></button>
            <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" onclick="showRakLokasi()" rel="tooltip" data-placement="bottom" title="Lokasi Rak" style="width: 30px;"><i class="bi bi-pin-map"></i></button>&nbsp;&nbsp;
          </div>
          <hr style="margin-top: 0.2rem; margin-bottom: 0.5rem;">
          <div id="divTambahPostingRak">
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                  <label for="kodeRak">Kode Rak</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="text" class="form-control" id="kodeRak" maxlength="4" onkeypress="" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                  <label for="namaRak">Nama Rak</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="namaRak" placeholder="Nama Rak" required>
                </div>
              </div>
            </div>
            <!-- <div class="row">
              <div class="col-2">
                <div class="form-group">
                  <label for="tingkat">Tingkat</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="text" class="form-control" id="tingkat" maxlength="4" onkeypress="" required>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group text-center">
                  <label for="kolom">Kolom</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <input type="text" class="form-control" id="kolom" maxlength="2" onkeypress="return hanyaAngka(event)" required>
                </div>
              </div>
            </div> -->
            <!-- <div class="row">
              <div class="col-2">
                <div class="form-group text-center">
                  <label for="baris">Baris</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="text" class="form-control" id="baris" maxlength="4"  required>
                </div>
              </div>
            </div> -->
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <button type="button" class="btn btn-primary" id="tombolTambahRak" onclick="addDetailRak()">Tambah</button>
                  <button type="button" class="btn btn-primary" id="resetTambahRak" onclick="resetTambahDetailRak()">Batal</button>
                </div>
              </div>
            </div>
          </div>
          <div id="divUbahPostingRak">
            <div class="row">
              <input type="hidden" id="eid_detailrak">
              <div class="col-2">
                <div class="form-group">
                  <label for="ekodeRak">Kode Rak</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="text" class="form-control" id="ekodeRak" placeholder="Kode Rak" disabled required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                  <label for="enamaRak">Nama Rak</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="enamaRak" placeholder="Nama Rak" required>
                </div>
              </div>
            </div>
            <!-- <div class="row">
              <div class="col-2">
                <div class="form-group text-center">
                  <label for="etingkat">Tingkat</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="text" class="form-control" id="etingkat" maxlength="4" required>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <label for="ekolom">Kolom</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <input type="text" class="form-control" id="ekolom" maxlength="2" onkeypress="return hanyaAngka(event)"  required>
                </div>
              </div>
            </div> -->
            <!-- <div class="row">
              <div class="col-2">
                <div class="form-group">
                  <label for="ebaris">Baris</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="text" class="form-control" id="ebaris" maxlength="4"  required>
                </div>
              </div>
            </div> -->
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <button type="button" class="btn btn-primary" id="etombolUbahRak" onclick="editDetailRak()">Ubah</button>
                  <button type="button" class="btn btn-primary" id="resetEditRak" onclick="resetEditDetailRak()">Batal</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="resetDetail()">Tutup</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal add detail Rak -->

<!-- start modal add detail Lokasi -->
<div class="modal fade" id="detaillokasi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document" style="min-width: 600px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Lokasi Rak Gudang</h5>
        <button type="button" class="close" aria-label="Close" onclick="resetDetailLokasi()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <h7 class="modal-title">Gudang : </h7><h7 class="modal-title" id="lkdgudangl"></h7><h7> ( </h7><h7 class="modal-title" id="lnmgudangl"></h7><h7> )</h7>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <h7 class="modal-title">Rak : </h7><h7 class="modal-title" id="lkdrak"></h7><h7> ( </h7><h7 class="modal-title" id="lnmrak"></h7><h7> )</h7>
            </div>
          </div>
        </div>
        <hr style="margin-top: 0.5rem; margin-bottom: 0.2rem;">
        <div id="contentDetailLokasi" >
          <div class="form-group"  style="overflow-y: auto; max-height: 300px">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th width='20%'>Kode Lokasi</th>
                  <th>Nama Lokasi</th>
                  <th width='10%'>Tingkat</th>
                  <th width='10%'>Kolom</th>
                  <th width='10%'>Baris</th>
                </tr>
              </thead>
              <tbody id="lokasirak">
              </tbody>
            </table>
          </div>
          <hr style="margin-top: 0.5rem; margin-bottom: 0.2rem;">
          <div class="form-group">
            <button type="button" class="btn btn-success btn-sm" onclick="showDivTambahPostingLokasi()" rel="tooltip" data-placement="bottom" title="Tambah" style="width: 30px;"><i class="bi bi-plus-circle"></i></button>
            <button type="button" class="btn btn-warning btn-sm" onclick="showEditDetailLokasi()" rel="tooltip" data-placement="bottom" title="Ubah" style="width: 30px;"><i class="bi bi-pencil-square"></i></button>
            <button type="button" class="btn btn-danger btn-sm" onclick="eraseDetailLokasi()" rel="tooltip" data-placement="bottom" title="Hapus" style="width: 30px;"><i class="bi bi-trash"></i></button>
            <button style="" class="btn btn-primary btn-sm" type="button"   onclick="buttonPrintPenerimaan()" ><i class="bi bi-printer"></i></button>
          </div>
          <hr style="margin-top: 0.2rem; margin-bottom: 0.5rem;">
          <div id="divTambahPostingLokasi">
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                  <label for="kodeLokasi">Kode Lokasi</label>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <!-- <input type="text" class="form-control" id="kodeLokasi" maxlength="3" onkeypress="return hanyaAngka(event)" required> -->
                  <input type="text" class="form-control" id="kodeLokasi" maxlength="30" >
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                  <label for="namaLokasi">Nama Lokasi</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="namaLokasi" placeholder="Nama Lokasi" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                  <label for="urutLokasi">Urut</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="string" class="form-control" id="urutLokasi" placeholder="Urut" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                  <label for="qntMax">Qnt Max</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="number" class="form-control" id="qntMax" min=0 value=0 required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                  <label for="tingkat">Tingkat</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="text" class="form-control" id="lokasitingkat" maxlength="4" onkeypress="" >
                </div>
              </div>
              <div class="col-2">
                <div class="form-group text-center">
                  <label for="kolom">Kolom</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="text" class="form-control" id="lokasikolom" maxlength="4"  >
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group text-center">
                  <label for="baris">Baris</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="text" class="form-control" id="lokasibaris" maxlength="4"  >
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <button type="button" class="btn btn-primary" id="tombolTambahL" onclick="addDetailL()">Tambah</button>
                  <button type="button" class="btn btn-primary" id="resetTambahL" onclick="resetTambahDetailL()">Batal</button>
                </div>
              </div>
            </div>
          </div>
          <div id="divUbahPostingLokasi">
            <div class="row">
              <input type="hidden" id="eid_detaillokasi">
              <div class="col-2">
                <div class="form-group">
                  <label for="ekodeLokasi">Kode Lokasi</label>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <input type="text" class="form-control" id="ekodeLokasi" placeholder="Kode Lokasi" disabled required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                  <label for="enamaLokasi">Nama Lokasi</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="enamaLokasi" placeholder="Nama Lokasi" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                  <label for="eurutLokasi">Urut</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="string" class="form-control" id="eurutLokasi" placeholder="Urut" required>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                  <label for="eqntMax">Qnt Max</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="number" class="form-control" id="eqntMax" value=0 min=0 required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                  <label for="tingkat">Tingkat</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="text" class="form-control" id="elokasitingkat" maxlength="4" onkeypress="" >
                </div>
              </div>
              <div class="col-2">
                <div class="form-group text-center">
                  <label for="kolom">Kolom</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="text" class="form-control" id="elokasikolom" maxlength="4"  >
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group text-center">
                  <label for="baris">Baris</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="text" class="form-control" id="elokasibaris" maxlength="4"  >
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <button type="button" class="btn btn-primary" id="etombolUbahL" onclick="editDetailL()">Ubah</button>
                  <button type="button" class="btn btn-primary" id="eresetUbahL" onclick="reseteditDetailL()">Batal</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="resetDetailLokasi()">Tutup</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal add detail Lokasi -->

@endsection

@section('js')
<script src="{!! URL::asset('js/wms-tampilan-baru.js') !!}?v={{ @filemtime(public_path('js/wms-tampilan-baru.js')) ?: '1' }}"></script>
<script type="text/javascript">
  var g_id = "", g_kode = "", g_nama = "", g_produksi = "", g_rusak = "", g_alamat = "";
  var g_koderak = "", g_namarak = "", g_kodegdgrak = "", g_tingkat = "", g_kolom = "";
  var g_kodelokasi = "", g_namalokasi = "", g_kodegdgraklokasi = "", g_koderaklokasi = "" , g_urutLokasi = "" , g_qntMax= 0, g_barcodeloc = '', g_lokasitingkat = '', g_lokasibaris = '', g_lokasikolom = '' ;
  // start document ready
	$(document).ready(function(){
    var table = $('#tabel_gudang').DataTable({"lengthMenu": [[-1, 10, 25, 50, 100], ["All", 10, 25, 50, 100]]});
    table.on('search', function () {
      $('#gudang_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_kode = "", g_nama = "", g_produksi = "", g_rusak = "", g_alamat = "";
    } );
    table.on('order', function () {
      $('#gudang_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_kode = "", g_nama = "", g_produksi = "", g_rusak = "", g_alamat = "";
    } );
    // Ditambahkan 2026-10-19: tabel sekarang berhalaman - pindah halaman juga mereset pilihan baris.
    table.on('page', function () {
      $('#gudang_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_kode = "", g_nama = "", g_produksi = "", g_rusak = "", g_alamat = "";
    } );
	});

  function buttonPrintPenerimaan() {
    let akses = $("#akses_iscetak").val();

    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }
    if (g_kodelokasi === "" || g_namalokasi === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    console.log(g_barcodeloc)


    // return
    let printContent1 = ''

    let printContent2 = document.getElementById(`tempqrcodeprint`).innerHTML;
    printContent1 += `<div style="break-inside: avoid">`
    printContent1 += `<div style="width: 125px; text-align: center">`
    printContent1 += printContent2
    printContent1 += `</div>`

    printContent1 += `<div style="margin-top: -20px;width: 125px; text-align: center">`
    printContent1 += `<h6>${g_barcodeloc}</h6>`
    printContent1 += `</div>`
    printContent1 += `</div>`

    document.getElementById("printContainer").innerHTML = printContent1



    w=window.open(' ');
    // w.opener = null;
    // w.refferer = null;
    // w.rel="noopener , noreferrer"

    w.document.write($(`#printContainer`).html());
    // console.log('a')
    w.print();
    // console.log('b')
    w.close();



  }

  function hanyaAngka(event) {
    var angka = (event.which) ? event.which : event.keyCode
    if (angka != 46 && angka > 31 && (angka < 48 || angka > 57))
        return false;
    return true;
  }

  $("#divTambahPostingRak").hide();
  $("#divUbahPostingRak").hide();
  $("#divTambahPostingLokasi").hide();
  $("#divUbahPostingLokasi").hide();
  // end document ready

  // start reset input
  // function reset(name = "") {
  //   $("#" + name + "kode").val("");
  //   $("#" + name + "nama").val("");
  //   $("#" + name + "alamat").val("");
  //   $("#" + name + "produksi").val("0");
  //   $("#" + name + "rusak").val("0");
  // }
  // end reset input

  function resetDetail() {
    $("#lkdgudang").html("");
    $("#lnmgudang").html("");
    $("#kodeRak").val("");
    $("#namaRak").val("");
    $("#tingkat").val("");
    $("#kolom").val("");
    $("#baris").val("");
  }

  function resetDetailLokasi() {
    g_kode =$("#lkdgudangl").html();
    g_nama =$("#lnmgudangl").html();

    $("#lkdgudangl").html("");
    $("#lnmgudangl").html("");
    $("#lkdrak").html("");
    $("#lnmrak").html("");
    $("#kodeLokasi").val("");
    $("#namaLokasi").val("");
    $("#urutLokasi").val("");
    document.getElementById("qntMax").value = 0


    $("#detaillokasi").modal('toggle');
    $("#detailRak").modal('toggle');
    showRak();
  }

  function resetTambahDetailRak() {
    $("#kodeRak").val("");
    $("#namaRak").val("");
    $("#tingkat").val("");
    $("#kolom").val("");
    $("#baris").val("");
    $("#divTambahPostingRak").hide();
  }

  function resetEditDetailRak() {
    $("#kodeRak").val("");
    $("#namaRak").val("");
    $("#tingkat").val("");
    $("#kolom").val("");
    $("#baris").val("");
    $("#divUbahPostingRak").hide();
  }

  function resetTambahDetailL() {
    $("#kodeLokasi").val("");
    $("#namaLokasi").val("");
    $("#urutLokasi").val("");
    document.getElementById("qntMax").value = 0
    $("#divTambahPostingLokasi").hide();
  }

  function reseteditDetailL() {
    $("#kodeLokasi").val("");
    $("#namaLokasi").val("");
    $("#urutLokasi").val("");
    // $("#urutLokasi").val("");
    document.getElementById("qntMax").value = 0
    // $("#qntMax").val(0);
    $("#divUbahPostingLokasi").hide();
  }

  // start refresh tabel
  function loadAll() {
    $('#tabel_gudang').DataTable().destroy();
    $('#gudang_data > tr').each(function() {
      $(this).css('background-color', '');
    });
    g_id = "", g_kode = "", g_nama = "", g_produksi = "", g_rusak = "", g_alamat = "";
    $.ajax({
      url     : "{!! url('loadAllGudang') !!}",
      type    : "GET",
      async   : false,
      success : function(result) {
        if (result.length > 0) {
          $('#gudang_data').html("");
          var str = "";
          for (var i = 0; i < result.length; i++) {
            str = str + '<tr id="'+i+'-tr" onclick="select('+i+', '+i+', \''+result[i].KODEGDG+'\', \''+result[i].NAMA+'\',  \''+result[i].Alamat+'\', '+result[i].IsProduksi+', '+result[i].IsRusak+')">\
              <td>' + result[i].KODEGDG + '</td>\
              <td>' + result[i].NAMA + '</td>\
              <td>' + result[i].Alamat + '</td>';
            if (result[i].IsProduksi == 1) {
              str = str + '<td><i class="bi bi-check-circle text-success"></i></td>'; // 2026-10-20: ikon sama dengan tampilan awal (fas tidak dimuat)
            } else {
              str = str + '<td><i class="bi bi-x-circle text-danger"></i></td>'; // 2026-10-20: ikon sama dengan tampilan awal (fas tidak dimuat)
            }
            if (result[i].IsRusak == 1) {
              str = str + '<td><i class="bi bi-check-circle text-success"></i></td>'; // 2026-10-20: ikon sama dengan tampilan awal (fas tidak dimuat)
            } else {
              str = str + '<td><i class="bi bi-x-circle text-danger"></i></td>'; // 2026-10-20: ikon sama dengan tampilan awal (fas tidak dimuat)
            }
            str = str + '</tr>';
          }
          $('#gudang_data').html(str);
        }
        else {
          $('#gudang_data').html('<tr>\
            <td colspan="5">Tidak ada data gudang ditemukan.</td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
          </tr>');
        }
        middleTD();
      }
    });
    var table = $('#tabel_gudang').DataTable({"lengthMenu": [[-1, 10, 25, 50, 100], ["All", 10, 25, 50, 100]]});
    table.on('search', function () {
      $('#gudang_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_kode = "", g_nama = "", g_produksi = "", g_rusak = "", g_alamat = "";
    } );
    table.on('order', function () {
      $('#gudang_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_kode = "", g_nama = "", g_produksi = "", g_rusak = "", g_alamat = "";
    } );
    // Ditambahkan 2026-10-19: tabel sekarang berhalaman - pindah halaman juga mereset pilihan baris.
    table.on('page', function () {
      $('#gudang_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_kode = "", g_nama = "", g_produksi = "", g_rusak = "", g_alamat = "";
    } );
  }
  // end refresh table

  function select(_row, _id, _kode, _nama, _produksi, _rusak, _alamat) {
    $('#gudang_data > tr').each(function() {
      $(this).css('background-color', '');
    });
    $("#"+_row+"-tr").css('background-color', 'gold');
    g_id = _id; g_kode = _kode; g_nama = _nama; g_produksi = _produksi; g_rusak = _rusak; g_alamat = _alamat;
  }

  function selectrak(_row, _koderak, _namarak, _kodegdgrak, _tingkat, _kolom, _baris) {
    console.log(_row, _koderak, _namarak, _kodegdgrak, _tingkat, _kolom, _baris)
    $('#rakgudang > tr').each(function() {
      $(this).css('background-color', '');
    });
    $("#r"+_row+"-tr").css('background-color', 'gold');
    g_koderak = _koderak; g_namarak = _namarak; g_kodegdgrak = _kodegdgrak; g_tingkat = _tingkat; g_kolom = _kolom; g_baris= _baris
    // console.log(g_baris)
  }

  function selectlokasi(_row, _kodelokasi, _namalokasi, _kodegdgraklokasi, _koderaklokasi , _qntMax , _urutLokasi , _barcodeloc , _lokasitingkat , _lokasibaris , _lokasikolom) {
    console.log('selectlokasi')
    console.log(_row, _kodelokasi, _namalokasi, _kodegdgraklokasi, _koderaklokasi, _qntMax , _urutLokasi, _barcodeloc )
    console.log( _lokasitingkat , _lokasibaris , _lokasikolom)
    $('#lokasirak > tr').each(function() {
      $(this).css('background-color', '');
    });
    $("#l"+_row+"-tr").css('background-color', 'gold');
    g_kodelokasi = _kodelokasi; g_namalokasi = _namalokasi; g_kodegdgraklokasi = _kodegdgraklokasi; g_koderaklokasi = _koderaklokasi; g_urutLokasi = _urutLokasi ; g_qntMax = _qntMax ; g_barcodeloc = _barcodeloc ; g_lokasitingkat = _lokasitingkat ; g_lokasibaris = _lokasibaris ; g_lokasikolom = _lokasikolom ;

    document.getElementById("tempPrintContainer").innerHTML = '<div id="tempqrcodeprint" style="width: 10px; height: 10px"></div>'

    new QRCode(document.getElementById(`tempqrcodeprint`), {text: `${g_barcodeloc}` , width: 125, height: 125});

  }

  function showDivTambahPostingRak() {
    console.log('showDivTambahPostingRak')
    let akses = $("#akses_istambah").val();

    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }



    // var _kode = $("#kodeRak").val();
    // var _nama = $("#namaRak").val();
    $("#kodeRak").val('');
    $("#namaRak").val('');
    if ($("#divTambahPostingRak").is(":hidden")) {
      $("#divTambahPostingRak").show();
      $("#divUbahPostingRak").hide();
    } else {
      $("#divTambahPostingRak").hide();
    }
  }

  function showDivTambahPostingLokasi() {
    console.log('showDivTambahPostingLokasi')
    let akses = $("#akses_istambah").val();



    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }

    $("#divTambahPostingLokasi").hide();
    $("#kodeLokasi").val('');
    $("#namaLokasi").val('');
    $("#lokasitingkat").val('');
    $("#lokasibaris").val('');
    $("#lokasikolom").val('');
    $("#qntMax").val(0);
    $("#urutLokasi").val('');

    if ($("#divTambahPostingLokasi").is(":hidden")) {
      $("#divTambahPostingLokasi").show();
      $("#divUbahPostingLokasi").hide();
    } else {
      $("#divTambahPostingLokasi").hide();
    }
  }

  // start add gudang
  // function add() {
  //   var _token = $("#_token").val();
  //   var _kode = $("#kode").val();
  //   var _nama = $("#nama").val();
  //   var _alamat = $("#alamat").val();
  //   var _produksi = $("#produksi").val();
  //   var _rusak = $("#rusak").val();
  //   if (_kode != "" && _nama != "") {
  //     $.ajax({
  //       url     : "{!! url('addGudang') !!}",
  //       type    : "POST",
  //       async   : false,
  //       data    : {
  //         _token : _token,
  //         kode : _kode,
  //         nama : _nama,
  //         alamat : _alamat,
  //         produksi : _produksi,
  //         rusak : _rusak
  //       },
  //       success : function(result) {
  //         if (result == 1) {
  //           reset();
  //           loadAll();
  //           alertify.success('Data gudang telah ditambahkan.');
  //         } else {
  //           alertify.alert('Gagal menambahkan data gudang!', 'Kode gudang sudah ada.', function(){ });
  //         }
  //       }
  //     });
  //   }
  //   else {
  //     alertify.alert('Gagal menambahkan data gudang!', 'Semua kolom harus terisi.', function(){ });
  //   }
  // }
  // end add gudang

  // start add Rak gudang
  function addDetailRak(name) {
    // console.log('addDetailRak')
    var _token = $("#_token").val();
    var _kode = $("#kodeRak").val();
    var _nama = $("#namaRak").val();
    var _tingkat = '';
    var _kolom = 0;
    var _baris = '';
    var _kodegdg = $("#lkdgudang").html();
    var _Choice = 'I';
    if (_kode != "" && _nama != "" ) {
      $.ajax({
        url     : "{!! url('addRak') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          choice : _Choice,
          kode : _kode,
          nama : _nama,
          tingkat : _tingkat,
          kolom : _kolom,
          kodegdg : _kodegdg,
          baris: _baris
        },
        success : function(result) {
          if (result == 1) {
            resetTambahDetailRak();
            showloadRak();
            alertify.success('Data Rak telah ditambahkan.');
          } else {
            alertify.alert('Gagal menambahkan data Rak!', 'Kode Rak sudah ada.', function(){ });
          }
        }
      });
    }
    else {
      alertify.alert('Gagal menambahkan data Rak!', 'Semua kolom harus terisi.', function(){ });
    }
  }
  // end add Rak gudang

  // start add Rak gudang
  function addDetailL() {
    var _token = $("#_token").val();
    var _kode = $("#kodeLokasi").val();
    var _nama = $("#namaLokasi").val();
    var _qntMax = $("#qntMax").val();
    var _urutLokasi = $("#urutLokasi").val();
    var _kodegdg = $("#lkdgudangl").html();
    var _koderak = $("#lkdrak").html();
    var _Choice = 'I';
    var _tingkat = $("#lokasitingkat").val();
    var _baris = $("#lokasibaris").val();
    var _kolom = $("#lokasikolom").val();
    // console.log('addLokasi')
    // console.log(_qntMax, _urutLokasi)
    // return
    if (_kode != "" && _nama != "") {
      $.ajax({
        url     : "{!! url('addLokasi') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          choice : _Choice,
          kode : _kode,
          nama : _nama,
          kodegdg : _kodegdg,
          koderak : _koderak,
          qntMax: _qntMax,
          urutLokasi: _urutLokasi,
          tingkat: _tingkat,
          baris: _baris,
          kolom: _kolom,
        },
        success : function(result) {
          if (result == 1) {
            resetTambahDetailL();
            showloadRakLokasi();
            alertify.success('Data Lokasi telah ditambahkan.');
          } else {
            alertify.alert('Gagal menambahkan data Lokasi!', 'Kode Lokasi sudah ada.', function(){ });
          }
        }
      });
    }
    else {
      alertify.alert('Gagal menambahkan data Lokasi!', 'Semua kolom harus terisi.', function(){ });
    }
  }
  // end add Rak gudang Lokasi

  // start tampilkan gudang
  // function show() {
  //   if (g_id === "" || g_kode === "" || g_nama === "" || g_produksi === "" || g_rusak === "") {
  //     alertify.warning("Tidak ada baris dipilih");
  //     return;
  //   }
  //   $("#eid").val(g_id);
  //   $("#ekode").val(g_kode);
  //   $("#enama").val(g_nama);
  //   $("#enama").val(g_alamat);
  //   $("#eproduksi").val(g_produksi);
  //   $("#erusak").val(g_rusak);
  //   $("#editGudang").modal('toggle');
  // }
  // end tampilkan gudang

  // start tampilkan Menu Rak gudang
  function showRak() {
    if (g_kode === "" || g_nama === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    console.log('showRak')
    g_koderak = ""; g_namarak = ""; g_kodegdgrak = ""; g_tingkat = ""; g_kolom = "";
    $("#lkdgudang").html(g_kode);
    $("#lnmgudang").html(g_nama);
    $("#detailRak").modal('toggle');
    $("#divTambahPostingRak").hide();
    $("#divUbahPostingRak").hide();
    showloadRak();

  }
  // end tampilkan Menu Rak gudang

  // start tampilkan Rak gudang
  function showloadRak() {
    $("#rakgudang").html("");
    var _token = $("#_token").val();
    var _kode = g_kode;
    str = "";
    $.ajax({
      url     : "{!! url('showRak') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        KodeGdg : _kode
      },
      success : function(result) {
        if (result.length > 0) {
          for (var i = 0; i < result.length; i++) {
            str = str + '<tr id="r'+i+'-tr" onclick="selectrak(' + `${i} , '${result[i].KodeRak}' ,  '${result[i].NamaRak }' , '${_kode}' , '${result[i].Tingkat}' , '${result[i].Kolom}', ${result[i].Baris}`+ ')">\
              <td>' + result[i].KodeRak + '</td>\
              <td>' + result[i].NamaRak + '</td>\</tr>'
              ;
          }
        } else {
          str = str + "<td colspan='4'>Data Rak tidak ditemukan</td>";
        }
        $("#rakgudang").html(str);
      }
    });
  }
  // end tampilkan Rak gudang

  function showEditDetailRak() {
    console.log('showEditDetailRak')
    let akses = $("#akses_iskoreksi").val();

    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }
    if (g_koderak === "" || g_namarak === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    $("#divTambahPostingRak").hide();
    $("#ekodeRak").val(g_koderak);
    $("#enamaRak").val(g_namarak);
    $("#etingkat").val(g_tingkat);
    $("#ekolom").val(g_kolom);
    $("#ebaris").val(g_baris);
    $("#divUbahPostingRak").show();
  }

  // start tampilkan Menu Rak gudang
  function showRakLokasi() {
    if (g_koderak === "" || g_namarak === "" || g_kodegdgrak === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    console.log('showRakLokasi')
    g_kodelokasi = ""; g_namalokasi = ""; g_kodegdgraklokasi = ""; g_koderaklokasi = ""; g_lokasitingkat = ""; g_lokasibaris = ""; g_lokasikolom = "";
    $("#lkdgudangl").html(g_kodegdgrak);
    $("#lnmgudangl").html($("#lnmgudang").html());
    $("#lkdrak").html(g_koderak);
    $("#lnmrak").html(g_namarak);
    $("#divTambahPostingLokasi").hide();
    $("#divUbahPostingLokasi").hide();

    // $("#formAddListItem").modal('toggle')
    // document.getElementById("input_add_add_kodebarang").value = dataAddListItem[i].KODEBRG
    // document.getElementById("input_add_add_keterangannama").value = dataAddListItem[i].NAMABRG
    var modal = document.getElementById("detailRak");
    modal.style.display = "block";

    $("#detaillokasi").modal('toggle');
    // $("#detailRak").modal('toggle');
    showloadRakLokasi();
  }
  // end tampilkan Menu Rak gudang

  // start tampilkan Rak Lokasi
  function showloadRakLokasi() {
    $("#lokasirak").html("");
    var _token = $("#_token").val();
    var _kodegdg = $("#lkdgudangl").html();
    var _koderak = $("#lkdrak").html();
    str = "";
    $.ajax({
      url     : "{!! url('showLokasi') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        Kodegdg : _kodegdg,
        Koderak : _koderak
      },
      success : function(result) {

        console.log('Result show Lokasi' , result)
        if (result.length > 0) {
          for (var i = 0; i < result.length; i++) {

            str = str + '<tr id="l'+i+'-tr" onclick="selectlokasi('+ `${i} , '${result[i].KodeLokasi}', '${result[i].NamaLokasi}'  , '${_kodegdg}' , '${_koderak}' , ${result[i].QntMax} , '${result[i].Urut}' , '${result[i].BarcodeLoc}' ,
            '${result[i].TINGKAT}' , '${result[i].BARIS}' , '${result[i].KOLOM}'  ` +')">\
              <td>' +` ${result[i].KodeLokasi} ` + "</td>\
              <td>" + `${result[i].NamaLokasi} ` + "</td>\
              <td>" + `${result[i].TINGKAT ? result[i].TINGKAT : ''} ` + "</td>\
              <td>" + `${result[i].KOLOM ? result[i].KOLOM : ''} ` + "</td>\
              <td>" + `${result[i].BARIS ? result[i].BARIS : ''} ` + '</td>\</tr>';
          }
        } else {
          str = str + "<td colspan='2'>Data Lokasi tidak ditemukan</td>";
        }
        $("#lokasirak").html(str);
      }
    });
  }
  // end tampilkan Rak Lokasi

  function showEditDetailLokasi() {
    console.log('showEditDetailLokasi')
    let akses = $("#akses_iskoreksi").val();

    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }
    if (g_kodelokasi === "" || g_namalokasi === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    $("#divTambahPostingLokasi").hide();
    $("#ekodeLokasi").val(g_kodelokasi);
    $("#enamaLokasi").val(g_namalokasi);
    console.log(typeof g_lokasitingkat)
    console.log(typeof g_lokasibaris)
    console.log(typeof g_lokasibaris.toString())
    console.log(typeof g_lokasikolom)
    g_lokasitingkat ? $("#elokasitingkat").val(g_lokasitingkat) : $("#elokasitingkat").val('');
    g_lokasibaris ? $("#elokasibaris").val(g_lokasibaris) :  $("#elokasibaris").val('');
    g_lokasikolom ? $("#elokasikolom").val(g_lokasikolom) : $("#elokasikolom").val('');
    $("#eqntMax").val(g_qntMax);
    $("#eurutLokasi").val(g_urutLokasi);
    $("#divUbahPostingLokasi").show();
  }

  // start edit gudang
  // function edit() {
  //   var _token = $("#_token").val();
  //   var _id = $("#eid").val();
  //   var _kode = $("#ekode").val();
  //   var _nama = $("#enama").val();
  //   var _alamat = $("#ealamat").val();
  //   var _produksi = $("#eproduksi").val();
  //   var _rusak = $("#erusak").val();
  //   if (_id != "" && _kode != "" && _nama != "") {
  //     $.ajax({
  //       url     : "{!! url('editGudang') !!}",
  //       type    : "POST",
  //       async   : false,
  //       data    : {
  //         _token : _token,
  //         id : _id,
  //         kode : _kode,
  //         nama : _nama,
  //         alamat : _alamat,
  //         produksi : _produksi,
  //         rusak : _rusak
  //       },
  //       success : function(result) {
  //         if (result == 1) {
  //           $("#editGudang").modal('toggle');
  //           reset("e");
  //           loadAll();
  //           alertify.success('Data gudang telah diubah.');
  //         } else {
  //           alertify.alert('Gagal mengubah data gudang!', 'Kode gudang sudah ada.', function(){ });
  //         }
  //       }
  //     });
  //   }
  //   else {
  //     alertify.alert('Gagal mengubah data gudang!', 'Semua kolom harus terisi.', function(){ });
  //   }
  // }
  // end edit gudang

  // start edit Rak gudang
  function editDetailRak(name) {
    var _token = $("#_token").val();
    var _kode = $("#ekodeRak").val();
    var _nama = $("#enamaRak").val();
    var _tingkat = '';
    var _kolom = 0;
    var _baris = '';
    var _kodegdg = $("#lkdgudang").html();
    var _Choice = 'U';
    if (_kode != "" && _nama != "" ) {
      $.ajax({
        url     : "{!! url('addRak') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          choice : _Choice,
          kode : _kode,
          nama : _nama,
          tingkat : _tingkat,
          kolom : _kolom,
          kodegdg : _kodegdg,
          baris: _baris
        },
        success : function(result) {
          if (result == 1) {
            resetEditDetailRak();
            showloadRak();
            alertify.success('Data Rak telah diubah.');
          } else {
            alertify.alert('Gagal mengubah data Rak!', 'Kode Rak sudah ada.', function(){ });
          }
        }
      });
    }
    else {
      alertify.alert('Gagal mengubah data Rak!', 'Semua kolom harus terisi.', function(){ });
    }
  }
  // end edit Rak gudang

  // start edit Rak lokasi
  function editDetailL(name) {
    var _token = $("#_token").val();
    var _kode = $("#ekodeLokasi").val();
    var _nama = $("#enamaLokasi").val();
    var _qntMax = $("#eqntMax").val();
    var _urutLokasi = $("#eurutLokasi").val();
    var _kodegdg = $("#lkdgudangl").html();
    var _koderak = $("#lkdrak").html();
    var _tingkat = $("#elokasitingkat").val();
    var _baris = $("#elokasibaris").val();
    var _kolom = $("#elokasikolom").val();
    var _Choice = 'U';
    if (_kode != "" && _nama != "") {
      $.ajax({
        url     : "{!! url('addLokasi') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          choice : _Choice,
          kode : _kode,
          nama : _nama,
          kodegdg : _kodegdg,
          koderak : _koderak,
          qntMax: _qntMax,
          urutLokasi: _urutLokasi,
          tingkat: _tingkat,
          baris: _baris,
          kolom: _kolom,
        },
        success : function(result) {
          if (result == 1) {
            reseteditDetailL();
            showloadRakLokasi();
            alertify.success('Data Lokasi telah diubah.');
          } else {
            alertify.alert('Gagal mengubah data Lokasi!', 'Kode Lokasi sudah ada.', function(){ });
          }
        }
      });
    }
    else {
      alertify.alert('Gagal mengubah data Lokasi!', 'Semua kolom harus terisi.', function(){ });
    }
  }
  // end edit Rak Lokasi

  // start hapus gudang
  // function erase() {
  //   if (g_id === "" || g_nama === "") {
  //     alertify.warning("Tidak ada baris dipilih");
  //     return;
  //   }
  //   alertify.confirm('Hapus Gudang', 'Apakah yakin ingin menghapus gudang dengan nama ' + g_nama + ' ?',
  //     function(){
  //       var _token = $("#_token").val();
  //       $.ajax({
  //         url     : "{!! url('eraseGudang') !!}",
  //         type    : "POST",
  //         async   : false,
  //         data    : {
  //           _token : _token,
  //           id : g_id
  //         },
  //         success : function(result) {
  //           loadAll();
  //           alertify.success('Data gudang telah dihapus.');
  //         }
  //       });
  //   },function(){});
  // }
  // end hapus gudang

  // start hapus Rak gudang
  function eraseDetailRak() {
    console.log('eraseDetailRak')
    let akses = $("#akses_ishapus").val();

    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }
    if (g_koderak === "" || g_namarak === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    alertify.confirm('Hapus Rak', 'Apakah yakin ingin menghapus Rak dengan nama ' + g_namarak + ' ?',
      function(){
        var _token = $("#_token").val();
        var _kode = g_koderak;
        var _nama = g_namarak;
        var _kodegdg = $("#lkdgudang").html();
        var _tingkat = '';
        var _kolom = 0;
        var _baris = '';
        var _Choice = 'D';
        $.ajax({
          url     : "{!! url('addRak') !!}",
          type    : "POST",
          async   : false,
          data    : {
            _token : _token,
            choice : _Choice,
            kode : _kode,
            nama : _nama,
            tingkat : _tingkat,
            kolom : _kolom,
            kodegdg : _kodegdg,
            baris: _baris
          },
          success : function(result) {
            if (result == 1) {
              showloadRak();
              alertify.success('Data Rak telah dihapus.');
            } else {
              alertify.alert('Gagal menghapus data Rak!', 'Kode Rak sudah ada transaksi.', function(){ });
            }
          }
        });
    },function(){});
  }
  // end hapus Rak gudang

  // start hapus Rak Lokasi
  function eraseDetailLokasi() {
    console.log('eraseDetailLokasi')
    let akses = $("#akses_ishapus").val();

    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }
    if (g_kodelokasi === "" || g_namalokasi === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    alertify.confirm('Hapus Lokasi', 'Apakah yakin ingin menghapus Lokasi dengan nama ' + g_namalokasi + ' ?',
      function(){
        var _token = $("#_token").val();
        var _kode = g_kodelokasi;
        var _nama = g_namalokasi;
        var _kodegdg = $("#lkdgudangl").html();
        var _koderak = $("#lkdrak").html();
        var _Choice = 'D';
        $.ajax({
          url     : "{!! url('addLokasi') !!}",
          type    : "POST",
          async   : false,
          data    : {
            _token : _token,
            choice : _Choice,
            kode : _kode,
            nama : _nama,
            kodegdg : _kodegdg,
            koderak : _koderak
          },
          success : function(result) {
            if (result == 1) {
              showloadRakLokasi();
              alertify.success('Data Lokasi telah dihapus.');
            } else {
              alertify.alert('Gagal menghapus data Lokasi!', 'Kode Lokasi sudah ada transaksi.', function(){ });
            }

          }
        });
    },function(){});
  }
  // end hapus Rak Lokasi
</script>
@endsection
