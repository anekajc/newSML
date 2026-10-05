@extends('newmaster')

@section('css')
@endsection

@section('buttons')
<button type="button" class="btn btn-primary btn-sm btn-top" onclick="loadAll()" rel="tooltip" data-placement="bottom" title="Refresh" style="width: 30px;"><i class="bi bi-arrow-repeat"></i></button>&nbsp;&nbsp;

<button type="button" class="btn btn-secondary btn-sm btn-top" data-toggle="modal" onclick="showRak()" rel="tooltip" data-placement="bottom" title="Rak/Lokasi" style="width: 30px;"><i class="bi bi-pin-map"></i></button>&nbsp;&nbsp;
@endsection

@section('content')
<div class="container-fluid">
<h1>Gudang</h1>
</div>

<div class="container">
  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  <div class="card">
    <div class="card-header">
      <div class="row">
        <!-- <nav style="width: 100%;">
          <div class="nav nav-tabs col-12" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true" style="color: black;">Outstanding PO</a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false" style="color: black;">Penerimaan Barang</a>
          </div>
        </nav> -->
      </div>
    </div>
      <div class="card-body">
      <div class="row">
        <div class="col-12" style="padding-left: 20px; padding-right: 20px; overflow:auto;">
          <table class="table table-bordered table-striped" id="tabel_gudang" style=" background: white;">
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
                  <th width='10%'>Tingkat</th>
                  <th width='10%'>Kolom</th>
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
              <div class="col-2">
                <div class="form-group">
                  <input type="text" class="form-control" id="kodeRak" maxlength="2" onkeypress="return hanyaAngka(event)" required>
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
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                  <label for="tingkat">Tingkat</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <input type="text" class="form-control" id="tingkat" maxlength="1" onkeypress="return hanyaAngka(event)" required>
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
            </div>
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
              <div class="col-2">
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
            <div class="row">
              <div class="col-2">
                <div class="form-group text-center">
                  <label for="etingkat">Tingkat</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <input type="text" class="form-control" id="etingkat" maxlength="1" required>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <label for="ekolom">Kolom</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <input type="text" class="form-control" id="ekolom" maxlength="2" required>
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
  <div class="modal-dialog modal-md" role="document">
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
        <div id="contentDetailLokasi">
          <div class="form-group">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th width='30%'>Kode Lokasi</th>
                  <th>Nama Lokasi</th>
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
          </div>
          <hr style="margin-top: 0.2rem; margin-bottom: 0.5rem;">
          <div id="divTambahPostingLokasi">
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                  <label for="kodeLokasi">Kode Lokasi</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <input type="text" class="form-control" id="kodeLokasi" maxlength="2" onkeypress="return hanyaAngka(event)" required>
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
              <div class="col-2">
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
<script type="text/javascript">
  var g_id = "", g_kode = "", g_nama = "", g_produksi = "", g_rusak = "", g_alamat = "";
  var g_koderak = "", g_namarak = "", g_kodegdgrak = "", g_tingkat = "", g_kolom = "";
  var g_kodelokasi = "", g_namalokasi = "", g_kodegdgraklokasi = "", g_koderaklokasi = "";
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
	});

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
    $("#detaillokasi").modal('toggle');
    $("#detailRak").modal('toggle');
    showRak();
  }

  function resetTambahDetailRak() {
    $("#kodeRak").val("");
    $("#namaRak").val("");
    $("#tingkat").val("");
    $("#kolom").val("");
    $("#divTambahPostingRak").hide();
  }

  function resetEditDetailRak() {
    $("#kodeRak").val("");
    $("#namaRak").val("");
    $("#tingkat").val("");
    $("#kolom").val("");
    $("#divUbahPostingRak").hide();
  }

  function resetTambahDetailL() {
    $("#kodeLokasi").val("");
    $("#namaLokasi").val("");
    $("#divTambahPostingLokasi").hide();
  }

  function reseteditDetailL() {
    $("#kodeLokasi").val("");
    $("#namaLokasi").val("");
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
              str = str + '<td><i class="fas fa-check-circle green"></i></td>';
            } else {
              str = str + '<td><i class="fas fa-times-circle red"></i></td>';
            }
            if (result[i].IsRusak == 1) {
              str = str + '<td><i class="fas fa-check-circle green"></i></td>';
            } else {
              str = str + '<td><i class="fas fa-times-circle red"></i></td>';
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
  }
  // end refresh table

  function select(_row, _id, _kode, _nama, _produksi, _rusak, _alamat) {
    $('#gudang_data > tr').each(function() {
      $(this).css('background-color', '');
    });
    $("#"+_row+"-tr").css('background-color', 'gold');
    g_id = _id; g_kode = _kode; g_nama = _nama; g_produksi = _produksi; g_rusak = _rusak; g_alamat = _alamat;
  }

  function selectrak(_row, _koderak, _namarak, _kodegdgrak, _tingkat, _kolom) {
    $('#rakgudang > tr').each(function() {
      $(this).css('background-color', '');
    });
    $("#r"+_row+"-tr").css('background-color', 'gold');
    g_koderak = _koderak; g_namarak = _namarak; g_kodegdgrak = _kodegdgrak; g_tingkat = _tingkat; g_kolom = _kolom;
  }

  function selectlokasi(_row, _kodelokasi, _namalokasi, _kodegdgraklokasi, _koderaklokasi) {
    $('#lokasirak > tr').each(function() {
      $(this).css('background-color', '');
    });
    $("#l"+_row+"-tr").css('background-color', 'gold');
    g_kodelokasi = _kodelokasi; g_namalokasi = _namalokasi; g_kodegdgraklokasi = _kodegdgraklokasi; g_koderaklokasi = _koderaklokasi;
  }

  function showDivTambahPostingRak() {
    if ($("#divTambahPostingRak").is(":hidden")) {
      $("#divTambahPostingRak").show();
      $("#divUbahPostingRak").hide();
    } else {
      $("#divTambahPostingRak").hide();
    }
  }

  function showDivTambahPostingLokasi() {
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
    var _token = $("#_token").val();
    var _kode = $("#kodeRak").val();
    var _nama = $("#namaRak").val();
    var _tingkat = $("#tingkat").val();
    var _kolom = $("#kolom").val();
    var _kodegdg = $("#lkdgudang").html();
    var _Choice = 'I';
    if (_kode != "" && _nama != "" && _tingkat != "" && _kolom != "") {
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
          kodegdg : _kodegdg
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
    var _kodegdg = $("#lkdgudangl").html();
    var _koderak = $("#lkdrak").html();
    var _Choice = 'I';
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
          koderak : _koderak
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
    g_koderak = ""; g_namarak = ""; g_kodegdgrak = ""; g_tingkat = ""; g_kolom = "";
    $("#lkdgudang").html(g_kode);
    $("#lnmgudang").html(g_nama);
    $("#detailRak").modal('toggle');
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
            str = str + '<tr id="r'+i+'-tr" onclick="selectrak(' + i + ', \'' + result[i].KodeRak + '\', \'' + result[i].NamaRak + '\', \'' + _kode + '\', \'' + result[i].Tingkat + '\', \'' + result[i].Kolom + '\')">\
              <td>' + result[i].KodeRak + '</td>\
              <td>' + result[i].NamaRak + '</td>\
              <td>' + result[i].Tingkat + '</td>\
              <td>' + result[i].Kolom + '</td>\</tr>';
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
    if (g_koderak === "" || g_namarak === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    $("#divTambahPostingRak").hide();
    $("#ekodeRak").val(g_koderak);
    $("#enamaRak").val(g_namarak);
    $("#etingkat").val(g_tingkat);
    $("#ekolom").val(g_kolom);
    $("#divUbahPostingRak").show();
  }

  // start tampilkan Menu Rak gudang
  function showRakLokasi() {
    if (g_koderak === "" || g_namarak === "" || g_kodegdgrak === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    g_kodelokasi = ""; g_namalokasi = ""; g_kodegdgraklokasi = ""; g_koderaklokasi = "";
    $("#lkdgudangl").html(g_kodegdgrak);
    $("#lnmgudangl").html($("#lnmgudang").html());
    $("#lkdrak").html(g_koderak);
    $("#lnmrak").html(g_namarak);
    $("#detaillokasi").modal('toggle');
    $("#detailRak").modal('toggle');
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
        if (result.length > 0) {
          for (var i = 0; i < result.length; i++) {
            str = str + '<tr id="l'+i+'-tr" onclick="selectlokasi(' + i + ', ' + result[i].KodeLokasi + ', \'' + result[i].NamaLokasi + '\', \'' + _kodegdg + '\', \'' + _koderak + '\')">\
              <td>' + result[i].KodeLokasi + "</td>\
              <td>" + result[i].NamaLokasi + '</td></tr>';
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
    if (g_kodelokasi === "" || g_namalokasi === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    $("#divTambahPostingLokasi").hide();
    $("#ekodeLokasi").val(g_kodelokasi);
    $("#enamaLokasi").val(g_namalokasi);
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
    var _tingkat = $("#etingkat").val();
    var _kolom = $("#ekolom").val();
    var _kodegdg = $("#lkdgudang").html();
    var _Choice = 'U';
    if (_kode != "" && _nama != "" && _tingkat != "" && _kolom != "") {
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
          kodegdg : _kodegdg
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
    var _kodegdg = $("#lkdgudangl").html();
    var _koderak = $("#lkdrak").html();
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
          koderak : _koderak
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
        var _tingkat = g_tingkat;
        var _kolom = g_kolom;
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
            kodegdg : _kodegdg
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
        var _kodegdg = $("#lkdrak").html();
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
