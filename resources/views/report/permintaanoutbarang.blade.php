@extends('newmaster')

@section('css')
<style>
  .modal-lg {
    min-width: 98%;
  }
</style>
@endsection

@section('breadcrumb')
<li class="nav-item">
  <a class="nav-link" href="#" data-toggle="tooltip" data-placement="bottom" title="Out Bound / Persiapan Barang"><span class="blue" id="title_page">Persiapan Barang</span></a>
</li>
@endsection

@section('buttons')
<button type="button" class="btn btn-primary btn-sm btn-top" onclick="loadAll()" rel="tooltip" data-placement="bottom" title="Refresh" style="width: 30px;"><i class="bi bi-arrow-repeat"></i></button>&nbsp;&nbsp;
@endsection

@section('content')
<div class="container-fluid">
<h1>Persiapan Barang</h1>
</div>

<div class="container-fluid">
  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <input type="hidden" id="tipe_trans" value="{!! $tipe !!}">
  <div class="card">
    <div class="card-header">
      <div class="row">
        <nav style="width: 100%;">
          <div class="nav nav-pills col-12" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">Outstanding Transaksi Barang</a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">Persiapan Barang</a>
          </div>
        </nav>
      </div>
    </div>
    <div class="card-body">
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
          <div class="card-body">
            <div class="row">
              <div class="col-12" style="padding-left: 20px; padding-right: 20px; overflow:auto;">
                <table class="table table-bordered table-striped" id="tabel_outtransaksi" style="background:white;">
                  <thead>
                    <tr>
                      <th width="10%">Actions</th>
                      <th width="10%">No. Transaksi</th>
                      <th width="5%">Tanggal</th>
                      <th width="10%">Kode Barang</th>
                      <th width="40%">Nama Barang</th>
                      <th width="5%">Gudang</th>
                      <th width="10%">Qty</th>
                    </tr>
                  </thead>
                  <tbody id="outtransaksi_data">
                    @if (count($outtransaksi) > 0)
                      @for ($i = 0; $i < count($outtransaksi); $i++)
                        <tr id="{!! $i !!}-outtransaksi">
                          <td class="text-center">
                            <button type="button" class="btn btn-success btn-sm btn-top" id="tambah" onclick="select2({!! $i !!},'{!! $outtransaksi[$i]->NoBukti !!}','{!! $outtransaksi[$i]->Kodebrg !!}','{!! $outtransaksi[$i]->Qntsaldo !!}','{!! $outtransaksi[$i]->QNTAMBIL !!}','{!! $outtransaksi[$i]->SisaOut !!}','{!! $outtransaksi[$i]->Kodegdg !!}','{!! $outtransaksi[$i]->NAMAGUDANG !!}','I')" rel="tooltip" data-placement="bottom" title="Tambah" style="width: 30px;">
                              <i class="bi bi-plus-circle"></i>
                            </button>
                          </td>
                          <td>{!! $outtransaksi[$i]->NoBukti !!}</td>
                          <td>{!! date("d/m/Y", strtotime($outtransaksi[$i]->TANGGAL)) !!}</td>
                          <td>{!! $outtransaksi[$i]->Kodebrg !!}</td>
                          <td>{!! $outtransaksi[$i]->namabrg !!}</td>
                          <td>{!! $outtransaksi[$i]->Kodegdg !!}</td>
                          <td class="text-right">{!! number_format($outtransaksi[$i]->Qntsaldo) !!}</td>
                        </tr>
                      @endfor
                    @else
                      <tr>
                        <td colspan="7">Tidak ada data Outstanding Transaksi ditemukan.</td>
                        <td style="display: none;"></td>
                        <td style="display: none;"></td>
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
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
          <div class="row">
            <div class="col-12" style="padding-left: 20px; padding-right: 20px; overflow:auto;">
              <table class="table table-bordered table-striped" id="tabel_ppbrg" style="background:white;">
                <thead>
                  <tr>
                    <th width="10%">Actions</th>
                    <th width="10%">No. Bukti</th>
                    <th width="7%">Tanggal</th>
                    <th width="10%">No. Transaksi</th>
                    <th width="10%">Kode Barang</th>
                    <th width="16%">Nama Barang</th>
                    <th width="5%">Qty</th>
                    <th width="5%">Satuan</th>
                    <th width="12%">Keterangan</th>
                  </tr>
                </thead>
                <tbody id="ppbrg_data">
                  @if (count($persiapanppbrg) > 0)
                    @for ($i = 0; $i < count($persiapanppbrg); $i++)
                      <tr id="{!! $i !!}-tr">
                        <td class="text-center">
                          <button type="button" class="btn btn-warning btn-sm btn-top" id="koreksi" onclick="select({!! $i !!}, '{!! $persiapanppbrg[$i]->NOBUKTI !!}',{!! $persiapanppbrg[$i]->URUT !!},'{!! $persiapanppbrg[$i]->KODEBRG !!}','U')" rel="tooltip" data-placement="bottom" title="Koreksi" style="width: 30px;">
                            <i class="bi bi-pencil-square"></i>
                          </button>
                          <button type="button" class="btn btn-danger btn-sm btn-top" onclick="select({!! $i !!}, '{!! $persiapanppbrg[$i]->NOBUKTI !!}',{!! $persiapanppbrg[$i]->URUT !!},'{!! $persiapanppbrg[$i]->KODEBRG !!}','D')" rel="tooltip" data-placement="bottom" title="Hapus" style="width: 30px;">
                            <i class="bi bi-trash"></i>
                          </button>
                        </td>
                        <td>{!! $persiapanppbrg[$i]->NOBUKTI !!}</td>
                        <td>{!! date("d/m/Y", strtotime($persiapanppbrg[$i]->TANGGAL)) !!}</td>
                        <td>{!! $persiapanppbrg[$i]->NoBeli !!}</td>
                        <td>{!! $persiapanppbrg[$i]->KODEBRG !!}</td>
                        <td>{!! $persiapanppbrg[$i]->NamaBrg !!}</td>
                        <td class="text-right">{!! number_format($persiapanppbrg[$i]->QNT) !!}</td>
                        <td>{!! $persiapanppbrg[$i]->SATUAN !!}</td>
                        <td>{!! $persiapanppbrg[$i]->KETERANGAN !!}</td>
                      </tr>
                    @endfor
                  @else
                    <tr>
                      <td colspan="9">Tidak ada Persiapan Barang.</td>
                      <td style="display: none;"></td>
                      <td style="display: none;"></td>
                      <td style="display: none;"></td>
                      <td style="display: none;"></td>
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
  </div>
</div>

<!-- start modal pilih outtransaksi -->
<div class="modal fade" id="pilihouttransaksi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding-bottom: 5px; padding-top: 5px;">
        <h5 class="modal-title" id="exampleModalLabel">Pilih Transaksi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="reset()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" id="no_urut">
          <div class="col-1">
            <div class="form-group">
              <label for="no_bukti">No. Bukti</label>
            </div>
          </div>
          <div class="col-onehalf">
            <div class="form-group">
              <input type="text" class="form-control" id="no_bukti" disabled>
            </div>
          </div>
          <div class="col-6">
          </div>
          <div class="col-1">
            <div class="form-group text-right">
              <label for="tanggal">Tanggal</label>
            </div>
          </div>
          <div class="col-onehalf">
            <div class="form-group">
              <input type="date" class="form-control" id="tanggal" value="{!! date('Y-m-d') !!}">
            </div>
          </div>
        </div>
        <hr style="margin-top: 0.2rem; margin-bottom: 0.3rem;" />
        <div class="row">
          <div class="col-1">
            <div class="form-group">
              <label for="keterangan">Keterangan</label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <textarea id="keterangan" class="form-control" rows="1"></textarea>
            </div>
          </div>
        </div>

        <input type="hidden" id="kodebrg">
        <input type="hidden" id="barcodel">
        <input type="hidden" id="kodegdg">
        <input type="hidden" id="koderak">
        <input type="hidden" id="kodelokasi">
        <hr style="margin-top: 7px; margin-bottom: 7px;" />
        <div class="row">
          <div class="col-12" style="height: 60vh; overflow:auto;">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th style='width:10%;'>No. Transaksi</th>
                  <th style='width:20%;'>Kode Barang</th>
                  <th style='width:35%;'>Nama Barang</th>
                  <th style='width:10%;'>Qty</th>
                  <th style='width:5%;'>Pilih</th>
                </tr>
              </thead>
              <tbody id="outtransaksi_detail">
                <tr>
                  <td colspan="5">Tidak ada Outstanding Transaksi yang dipilih.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="reset()">Batal</button>
        <button type="button" class="btn btn-primary" onclick="showAdd()">OK</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal pilih outtransaksi -->

<!-- start modal add  -->
<div class="modal fade" id="addPpBrg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding-bottom: 5px; padding-top: 5px;">
        <h5 class="modal-title" id="exampleModalLabel">Tambah  Persiapan Barang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="reset()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" id="no_outtransaksi">
        </div>
        <hr style="margin-top: 0.2rem; margin-bottom: 0.3rem;" />
        <div class="row">
          <input type="hidden" id="gudang">
          <input type="hidden" id="barcode">
          <input type="hidden" id="rak">
          <input type="hidden" id="lokasi">
        </div>
        <hr style="margin-top: 7px; margin-bottom: 7px;" />
        <div class="row">
          <div class="col-12 tableFixHead" style="height: 60vh;">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>No. Transaksi</th>
                  <th>Kode Barang</th>
                  <th>Nama Barang</th>
                  <th>Qty Transaksi</th>
                  <th>Qty</th>
                  <th>Satuan</th>
                  <th>Isi</th>
                </tr>
              </thead>
              <tbody id="detail_ppbrg">
                <tr>
                  <td colspan="7">Tidak ada barang.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <!-- <hr style="margin-top: 7px; margin-bottom: 7px;" />
        <div class="row form-group" id="tombolAddBarang">
          <div class="col-12">
            <div class="float-right">
              <button type='button' class='btn btn-secondary btn-sm' onclick='showBatch()' rel="tooltip" data-placement="bottom" title="Batch" style="width: 33px;"><i class="fas fa-archive"></i></button>
            </div>
          </div>
        </div> -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="reset()">Batal</button>
        <button type="button" class="btn btn-primary" onclick="add()">Simpan</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal add  -->

<!-- start modal detail  -->
<div class="modal fade" id="detailPpBrg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding-bottom: 5px; padding-top: 5px;">
        <h5 class="modal-title" id="exampleModalLabel">Detail  Persiapan Barang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" id="detno_urut">
          <div class="col-1">
            <div class="form-group">
              <label for="detno_bukti">No. Bukti</label>
            </div>
          </div>
          <div class="col-onehalf">
            <div class="form-group">
              <input type="text" class="form-control" id="detno_bukti" disabled>
            </div>
          </div>
          <div class="col-6">
          </div>
          <div class="col-1">
            <div class="form-group text-right">
              <label for="dettanggal">Tanggal</label>
            </div>
          </div>
          <div class="col-onehalf">
            <div class="form-group">
              <input type="date" class="form-control" id="dettanggal" disabled>
            </div>
          </div>
        </div>
        <hr style="margin-top: 0.2rem; margin-bottom: 0.3rem;" />
        <div class="row">
          <div class="col-1">
            <div class="form-group">
              <label for="detgudang">Gudang</label>
            </div>
          </div>
          <div class="col-1">
            <div class="form-group">
              <input type="text" class="form-control" id="detgudang" disabled>
            </div>
          </div>
          <div class="col-5">
          </div>
          <div class="col-1">
            <div class="form-group">
              <label for="detbarcode">Barcode Lokasi</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <input type="text" class="form-control" id="detbarcode" disabled>
            </div>
          </div>
          <input type="hidden" id="rak">
          <input type="hidden" id="lokasi">
        </div>
        <div class="row">
          <div class="col-1">
            <div class="form-group">
              <label for="detketerangan">Keterangan</label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <textarea id="detketerangan" class="form-control" rows="1" disabled></textarea>
            </div>
          </div>
        </div>
        <hr style="margin-top: 7px; margin-bottom: 7px;" />
        <div class="row">
          <div class="col-12 tableFixHead" style="height: 60vh;">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>No. Transaksi</th>
                  <th>Kode Barang</th>
                  <th>Nama Barang</th>
                  <th>Qty Sisa</th>
                  <th>Qty</th>
                  <th>Satuan</th>
                  <th>Isi</th>
                </tr>
              </thead>
              <tbody id="detdetail_ppbrg">
                <tr>
                  <td colspan="7">Tidak ada barang.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <!-- <hr style="margin-top: 7px; margin-bottom: 7px;" />
        <div class="row form-group" id="tombolAddBarang">
          <div class="col-12">
            <div class="float-right">
              <button type='button' class='btn btn-secondary btn-sm' onclick='showDetBatch()' rel="tooltip" data-placement="bottom" title="Batch" style="width: 33px;"><i class="fas fa-archive"></i></button>
            </div>
          </div>
        </div> -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal detail  -->

<!-- start modal edit  -->
<div class="modal fade" id="editPpBrg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding-bottom: 5px; padding-top: 5px;">
        <h5 class="modal-title" id="exampleModalLabel">Koreksi  Persiapan Barang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="reset('e')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" id="eid">
          <input type="hidden" id="eno_outtransaksi">
          <input type="hidden" id="eno_urut">
          <div class="col-1">
            <div class="form-group">
              <label for="eno_bukti">No. Bukti</label>
            </div>
          </div>
          <div class="col-onehalf">
            <div class="form-group">
              <input type="text" class="form-control" id="eno_bukti" disabled>
            </div>
          </div>
          <div class="col-6">
          </div>
          <div class="col-1">
            <div class="form-group text-right">
              <label for="etanggal">Tanggal</label>
            </div>
          </div>
          <div class="col-onehalf">
            <div class="form-group">
              <input type="date" class="form-control" id="etanggal" value="{!! date('Y-m-d') !!}">
            </div>
          </div>
        </div>
        <hr style="margin-top: 0.2rem; margin-bottom: 0.3rem;" />
        <div class="row">
          <input type="hidden" id="egudang">
          <input type="hidden" id="ebarcode">
          <input type="hidden" id="erak">
          <input type="hidden" id="elokasi">
        </div>
        <div class="row">
          <div class="col-1">
            <div class="form-group">
              <label for="eketerangan">Keterangan</label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <textarea id="eketerangan" class="form-control" rows="1"></textarea>
            </div>
          </div>
        </div>
        <hr style="margin-top: 7px; margin-bottom: 7px;" />
        <!-- <hr style="margin-top: 7px; margin-bottom: 7px;" />
        <div class="row form-group" id="tombolAddBarang">
          <div class="col-12">
            <div class="float-right">
              <button type='button' class='btn btn-secondary btn-sm' onclick='eshowBatch()' rel="tooltip" data-placement="bottom" title="Batch" style="width: 33px;"><i class="fas fa-archive"></i></button>
            </div>
          </div>
        </div> -->
        <div class="row">
          <div class="col-12">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>No. Transaksi</th>
                  <th>Kode Barang</th>
                  <th>Nama Barang</th>
                  <th>Qty Transaksi</th>
                  <th>Qty</th>
                  <th>Satuan</th>
                  <th>Isi</th>
                  <th>Barcode</th>
                  <th>Jumlah Print</th>
                  <th>Print</th>
                </tr>
              </thead>
              <tbody id="edetail_ppbrg">
                <tr>
                  <td colspan="7">Tidak ada barang.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="reset('e')">Batal</button>
        <button type="button" class="btn btn-primary" onclick="edit()">Simpan</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal edit  -->

@endsection

@section('js')
<script type="text/javascript">
  var data_outtransaksi = [], temp = [];
  var ppbrg = [], eppbrg = [], batch = [], ebatch = [], detbatch = [];
  var g_idoutp="",g_nobuktioutp="",g_kodebrgoutp="",g_namabrgoutp="",g_qntsaldooutp="",g_qntambiloutp="",g_qntsisaoutp="",g_kodegdgoutp="",g_namagudangoutp="";

  var g_id = "", g_nb = "", g_nu = "", g_kd = "", g_iddet = "", g_id_2 = "";
  var str = "";
  var j = 0;
  // start document ready
	$(document).ready(function(){
    restrictedDate();
    setTipe();
    $(".format-number").autoNumeric('init', {mDec: '2'});
    var table = $('#tabel_outtransaksi').DataTable({"lengthMenu": [[-1, 10, 25, 50, 100], ["All", 10, 25, 50, 100]]});
    table.on('search', function () {
      $('#outtransaksi_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_idoutp=""; g_nobuktioutp=""; g_kodebrgoutp=""; g_namabrgoutp=""; g_qntsaldooutp=""; g_qntambiloutp=""; g_qntsisaoutp=""; g_kodegdgoutp=""; g_namagudangoutp="";
    } );
    table.on('order', function () {
      $('#outtransaksi_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_idoutp=""; g_nobuktioutp=""; g_kodebrgoutp=""; g_namabrgoutp=""; g_qntsaldooutp=""; g_qntambiloutp=""; g_qntsisaoutp=""; g_kodegdgoutp=""; g_namagudangoutp="";
    } );
    var table = $('#tabel_ppbrg').DataTable({"lengthMenu": [[-1, 10, 25, 50, 100], ["All", 10, 25, 50, 100]]});
    table.on('search', function () {
      $('#ppbrg_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_nb = "", g_nu = "", g_iddet = "";
    } );
    table.on('order', function () {
      $('#ppbrg_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_nb = "", g_nu = "", g_iddet = "";
    } );
	});

  // end document ready

  function restrictedDate() {
    var bln_periode = ("0"+{!! $periode->bulan !!}).slice(-2);
    var thn_periode = {!! $periode->tahun !!};
    var temp = new Date(thn_periode, bln_periode, 0);
    var minDate = temp.getFullYear()+"-"+("0" + (temp.getMonth() + 1)).slice(-2)+"-01";
    var maxDate = temp.getFullYear()+"-"+("0" + (temp.getMonth() + 1)).slice(-2)+"-"+("0" + temp.getDate()).slice(-2);
    var now = new Date();
    $("#tanggal").prop('max', maxDate);
    $("#tanggal").prop('min', minDate);
    $("#etanggal").prop('max', maxDate);
    $("#etanggal").prop('min', minDate);
    if (temp.getFullYear() == now.getFullYear() && temp.getMonth() == now.getMonth()) {
      $("#tanggal").val(now.getFullYear()+"-"+(("0" + (now.getMonth() + 1)).slice(-2))+"-"+(("0" + now.getDate()).slice(-2)));
    } else {
      $("#tanggal").val(minDate);
    }

    $("#tanggal").val(temp.getFullYear()+"-"+(("0" + (temp.getMonth() + 1)).slice(-2))+"-"+(("0" + now.getDate()).slice(-2)));
  }

  function setTipe() {
    var _token = $("#_token").val();
    var _tipe = $("#tipe").val();
    if ($("#tipe").val()=="0") {

    } else {

    }
  }

  // start refresh tabel
  function loadAll() {
    $('#tabel_outtransaksi').DataTable().destroy();
    $('#tabel_ppbrg').DataTable().destroy();
    $('#outtransaksi_data > tr').each(function() {
      $(this).css('background-color', '');
    });
    g_idoutp=""; g_nobuktioutp=""; g_kodebrgoutp=""; g_namabrgoutp=""; g_qntsaldooutp=""; g_qntambiloutp=""; g_qntsisaoutp=""; g_kodegdgoutp=""; g_namagudangoutp="";
    $('#ppbrg_data > tr').each(function() {
      $(this).css('background-color', '');
    });
    g_id = "", g_nb = "", g_nu = "", g_iddet = "";
    var _token = $("#_token").val();
    var akses = "";
    $.ajax({
      url     : "{!! url('getAksesByMenu') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        menu : '/permintaanpersiapan'
      },
      success : function(result) {
        akses = result;
      }
    });
    $.ajax({
      url     : "{!! url('loadAllOutTransaksi') !!}",
      type    : "GET",
      async   : false,
      success : function(result) {
        if (result.length > 0) {
          str = "";
          actions = "I";
          $('#outtransaksi_data').html("");
          for (var i = 0; i < result.length; i++) {
            str = str + '<tr id="'+i+'-outtransaksi">\
              <td class="text-center">\
                <button type="button" class="btn btn-success btn-sm btn-top" id="tambah" onclick="select2('+i+',\''+ result[i].NoBukti +'\',\''+ result[i].Kodebrg +'\',\''+ result[i].Qntsaldo +'\',\''+ result[i].QntOut +'\',\''+ result[i].SisaOut +'\',\
                \''+ result[i].Kodegdg +'\',\''+ result[i].NAMAGUDANG +'\',\''+ actions +'\')"rel="tooltip" data-placement="bottom" title="Tambah" style="width: 30px;">\
                  <i class="bi bi-plus-circle"></i>\
                </button>\
              </td>\
              <td>' + result[i].NoBukti + '</td>\
              <td>' + format_date(result[i].TANGGAL) + '</td>\
              <td>' + result[i].Kodebrg + '</td>\
              <td>' + result[i].namabrg + '</td>\
              <td>' + result[i].Kodegdg + '</td>\
              <td class="text-right">' + numberWithCommas(parseInt(result[i].Qntsaldo)) + '</td>\
              </tr>';
          }
          $('#outtransaksi_data').html(str);
        }
        else {
          $('#outtransaksi_data').html('<tr>\
            <td colspan="7">Tidak ada data Outstanding Transaksi ditemukan.</td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
          </tr>');
        }
        middleTD();
      }
    });
    $.ajax({
      url     : "{!! url('loadAllPpBrg') !!}",
      type    : "GET",
      async   : false,
      success : function(result) {
        if (result.length > 0) {
          str = "";
          var auth = "";
          var actionsU = "U";
          var actionsD = "D";
          $('#ppbrg_data').html("");
          for (var i = 0; i < result.length; i++) {
            str = str + '<tr id="' + i + '-tr">\
              <td class="text-center">\
                <button type="button" class="btn btn-warning btn-sm btn-top" id="koreksi" onclick="select(' + i + ', \'' + result[i].NOBUKTI + '\', \'' + result[i].URUT + '\', \'' + result[i].KODEBRG + '\', \'' + actionsU + '\')" rel="tooltip" data-placement="bottom" title="Koreksi" style="width: 30px;">\
                  <i class="bi bi-pencil-square"></i>\
                </button>\
                <button type="button" class="btn btn-danger btn-sm btn-top" onclick="select(' + i + ', \'' + result[i].NOBUKTI + '\', \'' + result[i].URUT + '\', \'' + result[i].KODEBRG + '\', \'' + actionsD + '\')" rel="tooltip" data-placement="bottom" title="Hapus" style="width: 30px;">\
                  <i class="bi bi-trash"></i>\
                </button>\
              </td>\
              <td>' + result[i].NOBUKTI + '</td>\
              <td>' + format_date(result[i].TANGGAL) + '</td>\
              <td>' + result[i].NoBeli + '</td>\
              <td>' + result[i].KODEBRG + '</td>\
              <td>' + result[i].NamaBrg + '</td>\
              <td class="text-right">' + numberWithCommas(parseInt(result[i].QNT)) + '</td>\
              <td>' + result[i].SATUAN + '</td>\
              <td>' + result[i].KETERANGAN + '</td>\
              </tr>';
          }
          $('#ppbrg_data').html(str);
        }
        else {
          $('#ppbrg_data').html('<tr>\
            <td colspan="9">Tidak ada  Persiapan Barang.</td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
          </tr>');
        }
        middleTD();
      }
    });
    var table = $('#tabel_outtransaksi').DataTable({"lengthMenu": [[-1, 10, 25, 50, 100], ["All", 10, 25, 50, 100]]});
    table.on('search', function () {
      $('#outtransaksi_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_idoutp=""; g_nobuktioutp=""; g_kodebrgoutp=""; g_namabrgoutp=""; g_qntsaldooutp=""; g_qntambiloutp=""; g_qntsisaoutp=""; g_kodegdgoutp=""; g_namagudangoutp="";
    } );
    table.on('order', function () {
      $('#outtransaksi_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_idoutp=""; g_nobuktioutp=""; g_kodebrgoutp=""; g_namabrgoutp=""; g_qntsaldooutp=""; g_qntambiloutp=""; g_qntsisaoutp=""; g_kodegdgoutp=""; g_namagudangoutp="";
    } );
    var table = $('#tabel_ppbrg').DataTable({"lengthMenu": [[-1, 10, 25, 50, 100], ["All", 10, 25, 50, 100]]});
    table.on('search', function () {
      $('#ppbrg_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_nb = "", g_nu = "", g_iddet = "";
    } );
    table.on('order', function () {
      $('#ppbrg_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_nb = "", g_nu = "", g_iddet = "";
    } );
  }
  // end refresh table

  function select2(_row,_nobuktioutp,_kodebrgoutp,_qntsaldooutp,_qntambiloutp,_qntsisaoutp,_kodegdgoutp,_namagudangoutp,_actions) {
    $('#outtransaksi_data > tr').each(function() {
      $(this).css('background-color', '');
    });
    $("#"+_row+"-outtransaksi").css('background-color', 'gold');
    g_idoutp=_row; g_nobuktioutp=_nobuktioutp; g_kodebrgoutp=_kodebrgoutp;
    // g_namabrgoutp=_namabrgoutp;
    g_qntsaldooutp=_qntsaldooutp; g_qntambiloutp=_qntambiloutp; g_qntsisaoutp=_qntsisaoutp; g_kodegdgoutp=_kodegdgoutp; g_namagudangoutp=_namagudangoutp;
    if(_actions=='I') {
      showPilih();
    }
  }

  function select(_id, _nb, _nu, _kd, _actions) {
    $('#ppbrg_data > tr').each(function() {
      $(this).css('background-color', '');
    });
    $("#"+_id+"-tr").css('background-color', 'gold');
    g_id = _id; g_nb = _nb; g_nu = _nu; g_kd = _kd;
    if(_actions=='U') {
      show();
    }
    if(_actions=='D') {
      erase();
    }
  }

  function selectdet(_row, name = "") {
    $('#'+name+'detail_ppbrg > tr').each(function() {
      $(this).css('background-color', '');
    });
    $("#"+name+_row+"-trdet").css('background-color', 'gold');
    g_iddet = _row;
  }

  function loadDetailouttransaksi() {
    var _token = $("#_token").val();
    str="";
    $.ajax({
      url     : "{!! url('showDetOutTransaksi') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token
      },
      success : function(result) {
        if (result.length > 0) {
          for (var i = 0; i < result.length; i++) {
            temp = [];
            if (result[i].SisaOut > 0) {
              temp.push(result[i].NoBukti);//0
              temp.push(result[i].Kodebrg);//1
              temp.push(result[i].namabrg);//2
              temp.push(numberWithCommas(result[i].Qntsaldo));//3
              if (result[i].QntOut == 0){
                temp.push(0);//4
              } else {
                temp.push(numberWithCommas(result[i].QntOut));//4
              }
              temp.push(numberWithCommas(result[i].SisaOut));//5
              temp.push(0);//6
              temp.push(numberWithCommas(result[i].SisaOut));//7
              temp.push(result[i].Kodegdg);//8
              temp.push(result[i].NAMAGUDANG);//9
              temp.push(result[i].satuan);//10
              temp.push(result[i].isi);//11
              temp.push(result[i].Urut);//12
              temp.push(result[i].TANGGAL);//13
              temp.push(result[i].nosat);//14
              data_outtransaksi.push(temp);
              str = str + "<tr>\
                <td style='width:10%'>" + result[i].NoBukti + "</td>\
                <td style='width:20%'>" + result[i].Kodebrg + "</td>\
                <td style='width:35%'>" + result[i].namabrg + "</td>\
                <td style='width:10%' class='text-right'>" + numberWithCommas(parseInt(result[i].Qntsaldo)) + "</td>\
                <td style='width:5%' class='text-center'><input type='checkbox' id='"+j+"-check' onclick='updateData("+j+")'></td></tr>";
              j++;
            }
          }
          $("#outtransaksi_detail").html(str);
          $(".format-number").autoNumeric('init', {mDec: '2'});
        } else {
          $("#outtransaksi_detail").html('<tr><td colspan="5">Tidak ada Outstanding Transaksi yang dipilih.</td></tr>');
        }
      }
    });
  }

  function updateData(i) {
    if ($('#' + i + '-check').is(":checked")) {
      data_outtransaksi[i][6] = 1;
      data_outtransaksi[i][5] = numberWithCommas(data_outtransaksi[i][7]);
    } else {
      data_outtransaksi[i][6] = 0;
      data_outtransaksi[i][5] = "0";
    }
  }

  function generateNoBukti() {
    var _token = $("#_token").val();
    var tipe = $("#tipe_trans").val();
    var res = "";
    $.ajax({
      url     : "{!! url('generateNomorBuktiPpBrg') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        tipe : tipe
      },
      success : function(result) {
        res = result;
      }
    });
    return res;
  }

  function generateNoUrut() {
    var _token = $("#_token").val();
    var tipe = $("#tipe_trans").val();
    var res = "";
    $.ajax({
      url     : "{!! url('generateNomorUrutPpBrg') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        tipe : tipe
      },
      success : function(result) {
        res = result;
      }
    });
    return res;
  }

  function showPilih() {
    if($("#tipe").val()=="0") {
      $("#lkdbrg").html("Kode Barang");
    }else if ($("#tipe").val()=="1") {
      $("#lkdbrg").html("No. Transaksi");
    }
    $("#pilihouttransaksi").modal('toggle');
    $("#outtransaksi_detail").html("");
    $("#no_urut").val(generateNoUrut());
    $("#no_bukti").val(generateNoBukti());

    j = 0;
    data_outtransaksi = [];
    str = "";
    loadDetailouttransaksi();
  }

  function showAdd() {
    if (data_outtransaksi.length > 0) {
      var count = 0;
      var barcode = "";
      var gudang = "";
      var rak = "";
      var lokasi = "";
      for (var i = 0; i < data_outtransaksi.length; i++) {
        count = count + data_outtransaksi[i][6];
      }
      if (count > 0) {
        barcode = $("#barcodel").val();
        gudang = $("#kodegdg").val();
        rak = $("#koderak").val();
        lokasi = $("#kodelokasi").val();
        $("#barcode").val(barcode);
        $("#gudang").val(gudang);
        $("#rak").val(rak);
        $("#lokasi").val(lokasi);

        ppbrg = [];
        batch = [];
        for (var i = 0; i < data_outtransaksi.length; i++) {
          if (data_outtransaksi[i][6] == 1 && parseInt(data_outtransaksi[i][3]) > 0) {
            temp = [];
            temp.push(data_outtransaksi[i][0]);//0 nobeli
            temp.push(data_outtransaksi[i][1]);//1 kdbrg
            temp.push(data_outtransaksi[i][2]);//2 nmbrg
            temp.push(data_outtransaksi[i][3]);//3 qntsaldo
            temp.push(data_outtransaksi[i][4]);//4 qntambil
            temp.push(data_outtransaksi[i][5]);//5 qnt
            temp.push(data_outtransaksi[i][7]);//6 kdgdg
            temp.push(data_outtransaksi[i][8]);//7 nmgdg
            temp.push(data_outtransaksi[i][10]);//8 satuan
            temp.push(data_outtransaksi[i][11]);//9 isi
            temp.push(data_outtransaksi[i][12]);//10 urutbeli
            temp.push(data_outtransaksi[i][13]);//11 tanggal transaksi
            temp.push(data_outtransaksi[i][14]);//12 nosat
            ppbrg.push(temp);
          }
        }
        data_outtransaksi = [];
        $("#no_outtransaksi").val("");
        loadppbrg();
        // $("#addPpBrg").modal("toggle");
        add();
        $("#pilihouttransaksi").modal('toggle');
      }
    }
  }

  function loadppbrg(name = "") {
    $('#'+name+'detail_ppbrg > tr').each(function() {
      $(this).css('background-color', '');
    });
    g_iddet = "";
    str = "";
    if (name == "") {
      if (ppbrg.length > 0) {
        for (var i = 0; i < ppbrg.length; i++) {
          str += "<tr id='" + i + "-trdet' onclick='selectdet(" + i + ")'>\
            <td style='width: 10%;'>" + ppbrg[i][0] + '</td>\
            <td style="width: 10%;">' + ppbrg[i][1] + '</td>\
            <td style="width: 35%;">' + ppbrg[i][2] + '</td>\
            <td style="width: 10%; text-align: right;">' + numberWithCommas(parseInt(ppbrg[i][3])) + '</td>\
            <td style="width: 10%; text-align: right;"><input type="text" id="'+i+'-qtypakai" class="format-number text-right" onkeyup="updateBarang('+i+')" value="'+ toInteger(ppbrg[i][5]) +'"></td>\
            <td style="width: 10%;">' + ppbrg[i][8] + '</td>\
            <td style="width: 10%; text-align: right;">' + numberWithCommas(parseInt(ppbrg[i][9])) + '</td></tr>';
        }
        $("#detail_ppbrg").html(str);

      }else {
        $("#detail_ppbrg").html('<tr><td colspan="7">Tidak ada barang.</td></tr>');
      }
    } else {
      if (eppbrg.length > 0) {
        for (var i = 0; i < eppbrg.length; i++) {
          str += "<tr id='e" + i + "-trdet' onclick='selectdet(" + i + ", \"e\")'>\
            <td style='width: 10%; '>" + eppbrg[i][0] + '</td>\
            <td style="width: 10%; ">' + eppbrg[i][1] + '</td>\
            <td style="width: 30%;">' + eppbrg[i][2] + '</td>\
            <td style="width: 10%; text-align: right;">' + numberWithCommas(parseInt(eppbrg[i][3])) + '</td>\
            <td style="width: 10%; text-align: right;"><input type="text" id="e'+i+'-qtypakai" class="format-number text-right" onkeyup="updateBarang('+i+', \'e\')" value="'+ toInteger(eppbrg[i][6]) +'"></td>\
            <td style="width: 10%; ">' + eppbrg[i][4] + '</td>\
            <td style="width: 10%; text-align: right;">' + numberWithCommas(parseInt(eppbrg[i][5])) + '</td>\
            ' + `<td class="text-center"><div id="containerbarcode${i}"><svg id="barcode${i}"></svg></td>
            <td><input id="input_add_qntPrint${i}" style="width: 100px;" class="text-right" type="number" min=0 value=${eppbrg[i][3]}></td>
            <td><button class="btn btn-success btn-sm" type="button" onclick="printBarcode('containerbarcode${i}','input_add_qntPrint${i}' )"><i class="bi bi-printer"></i></button></td>
            </tr>`;
        }
        $("#edetail_ppbrg").html(str);

        for (var i = 0; i < eppbrg.length; i++) {
          JsBarcode(`#barcode${i}`, eppbrg[i][1] ,{width: 2, height: 25,
            // displayValue: false
          });
        }
        $(".format-number").autoNumeric('init', {mDec: '2'});
      } else {
        $("#edetail_ppbrg").html('<tr><td colspan="7">Tidak ada barang.</td></tr>');
      }
    }

  }

  function updateBarang(index, name = "") {
    if (name == "") {
      if (ppbrg[index][5] < toInteger($("#"+index+"-qtykirim").val())) {
        $("#"+index+"-qtykirim").val(ppbrg[index][5]);
        barang[index][2] = numberWithCommas(barang[index][5]);
      }
      else {
        barang[index][2] = $("#"+index+"-qtykirim").val();
      }
    } else {
      if (eppbrg[index][6] < toInteger($("#e"+index+"-qtykirim").val())) {
        $("#e"+index+"-qtykirim").val(eppbrg[index][6]);
        eppbrg[index][3] = eppbrg[index][6];
      }
      else {
        eppbrg[index][3] = $("#e"+index+"-qtykirim").val();
      }
    }
  }

  function removeCommas(name = "") {
    if (name == "") {
      for (var i = 0; i < ppbrg.length; i++) {
        ppbrg[i][3] = toInteger(ppbrg[i][3]);
        // ppbrg[i][4] = toInteger(ppbrg[i][4]);
        ppbrg[i][5] = toInteger(ppbrg[i][5]);
        ppbrg[i][9] = toInteger(ppbrg[i][9]);
      }
    } else {
      for (var i = 0; i < eppbrg.length; i++) {
        eppbrg[i][3] = toInteger(eppbrg[i][3]);
        // eppbrg[i][4] = toInteger(eppbrg[i][4]);
        eppbrg[i][5] = toInteger(eppbrg[i][5]);
        eppbrg[i][9] = toInteger(eppbrg[i][9]);
      }
    }
  }

  function returnCommas(name = "") {
    if (name == "") {
      for (var i = 0; i < ppbrg.length; i++) {
        ppbrg[i][3] = numberWithCommas(ppbrg[i][3]);
        // ppbrg[i][4] = numberWithCommas(ppbrg[i][4]);
        ppbrg[i][5] = numberWithCommas(ppbrg[i][5]);
        ppbrg[i][9] = numberWithCommas(ppbrg[i][9]);
      }
    } else {
      for (var i = 0; i < eppbrg.length; i++) {
        eppbrg[i][3] = numberWithCommas(eppbrg[i][3]);
        // eppbrg[i][4] = numberWithCommas(eppbrg[i][4]);
        eppbrg[i][5] = numberWithCommas(eppbrg[i][5]);
        eppbrg[i][9] = numberWithCommas(eppbrg[i][9]);
      }
    }
  }

  function reset(name = "") {
    ppbrg = []; batch = [], data_outtransaksi = [];
    $("#outtransaksi_detail").html("");
    $("#kodebrg").val("");
    $("#kodegdg").val("");
    $("#koderak").val("");
    $("#kodelokasi").val("");
    restrictedDate();
    if (name == "e") release(g_nb);
  }

  // start add
  function add() {
    var _token = $("#_token").val();
    var _no_urut = $("#no_urut").val();
    var _no_bukti = $("#no_bukti").val();
    var _tanggal = $("#tanggal").val();
    var _no_outtransaksi = $("#no_outtransaksi").val();
    var _barcode = $("#barcode").val();
    var _gudang = $("#gudang").val();
    var _rak = $("#rak").val();
    var _lokasi = $("#lokasi").val();
    var _keterangan = $("#keterangan").val();
    var _choice = "I";
    // var cek = cekBatch();
    if (_no_urut != "" && _no_bukti != "" && _tanggal != "" &&
      ppbrg.length > 0
      // && cek == '1' && _no_outtransaksi != ""
        ) {
      removeCommas();
      $.ajax({
        url     : "{!! url('addPpBrg') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          choice : _choice,
          no_urut : _no_urut,
          no_bukti : _no_bukti,
          tanggal : _tanggal,
          no_outtransaksi : _no_outtransaksi,
          gudang : _gudang,
          rak : _rak,
          lokasi : _lokasi,
          barcode : _barcode,
          keterangan : _keterangan,
          ppbrg : ppbrg,
          batch : batch
        },
        success : function(result) {
          $("#no_urut").val(generateNoUrut());
          $("#no_bukti").val(generateNoBukti());
          if (result.split(";;")[0] == "1") {
            reset(); loadAll();
            // showPilih();
            // select(0, result.split(";;")[2],result.split(";;")[1],result.split(";;")[3]);
            // $("#addPpBrg").modal("toggle");
            alertify.success('Persiapan Barang telah ditambahkan.');
          } else if (result.split(";;")[0] == "0") {
            returnCommas();
            $("#no_urut").val(generateNoUrut());
            $("#no_bukti").val(generateNoBukti());
            add();
            // alertify.alert('Gagal menambahkan  Persiapan Barang!', 'Nomor Bukti sudah dipakai. Silahkan tekan tombol simpan kembali.', function(){ });
          } else {
            returnCommas();
            alertify.warning('Barang '+result+' melebihi batas kuantitas Outstanding Transaksi.');
          }
        }
      });
    } else {

        alertify.alert('Gagal menambahkan  Persiapan Barang!', 'Semua kolom harus terisi.', function(){ });

    }
  }
  // end add

  function changeAuth(id, no_bukti) {
    alertify.confirm('Otorisasi  Persiapan Barang', 'Apakah yakin ingin mengotorisasi  Persiapan Barang dengan nomor bukti ' + no_bukti + ' ?',
      function(){
        var _token = $("#_token").val(); var err = 0;
        $.ajax({
          url     : "{!! url('showDetPpBrg') !!}",
          type    : "POST",
          async   : false,
          data    : {
            _token : _token,
            nobukti : no_bukti
          },
          success : function(result) {
            for (var i = 0; i < result.length; i++) {
              if (result[i].perkiraan == "") {
                err = i; break;
              }
            }
          }
        });
        if (err != 0) {
          $("#"+id+"-auth").prop('checked', false); alertify.warning("Detail dengan nomor urut " + (err+1) + " pada nomor bukti " + nomor_bukti + " tidak memiliki perkiraan."); return;
        }
        $.ajax({
          url     : "{!! url('authPpBrg') !!}",
          type    : "POST",
          async   : false,
          data    : {
            _token : _token,
            nobukti : no_bukti
          },
          success : function(result) {
            loadAll();
            alertify.success(' Persiapan Barang dengan nomor bukti '+no_bukti+' telah diotorisasi.');
          }
        });
    },function(){
      $("#"+id+"-auth").prop('checked', false);
    });
  }

  function changeBatal(id, no_bukti) {
    alertify.confirm('Pembatalan  Persiapan Barang', 'Apakah yakin ingin membatalkan  Persiapan Barang dengan nomor bukti ' + no_bukti + ' ?',
      function(){
        var _token = $("#_token").val();
        $.ajax({
          url     : "{!! url('batalPpBrg') !!}",
          type    : "POST",
          async   : false,
          data    : {
            _token : _token,
            nobukti : no_bukti
          },
          success : function(result) {
            loadAll();
            alertify.success(' Persiapan Barang dengan nomor bukti '+no_bukti+' telah dibatalkan.');
          }
        });
    },function(){
      $("#"+id+"-batal").prop('checked', false);
    });
  }

  // start tampilkan detail
  function showDetail(_nobukti) {
    var no_bukti = "";
    temp = [];
    var isi = [];
    detbatch = [];
    var date = "";
    var _token = $("#_token").val();
    $.ajax({
      url     : "{!! url('showPpBrg') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        nobukti : _nobukti
      },
      success : function(result) {
        $("#detno_urut").val(result.NOURUT);
        $("#detno_bukti").val(result.NOBUKTI);
        date = format_date(result.TANGGAL);
        $("#dettanggal").val(date);
        $("#detgudang").val(result.KodeGdg);
        $("#detbarcode").val(result.BarcodeLoc);
        $("#detrak").val(result.KodeRak);
        $("#detlokasi").val(result.KodeLokasi);
        $("#detketerangan").val(result.KETERANGAN);
        no_bukti = result.NOBUKTI;
      }
    });
    $.ajax({
      url     : "{!! url('showDetPpBrg') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        id : no_bukti
      },
      success : function(result) {
        str = "";
        $("#detdetail_ppbrg").html("");
        for (var i = 0; i < result.length; i++) {
          str = str + "<tr id='det" + i + "-trdet' onclick='selectdet(" + i + ", \"det\")'>\
            <td>" + result[i].NoBeli + "</td>\
            <td>" + result[i].KODEBRG + "</td>\
            <td>" + result[i].NamaBrg + "</td>\
            <td class='text-right'>" + numberWithCommas(parseInt(result[i].QNTSISA)) + "</td>\
            <td class='text-right'>" + numberWithCommas(parseInt(result[i].QNT)) + "</td>\
            <td>" + result[i].SATUAN + "</td>\
            <td class='text-right'>" + numberWithCommas(parseInt(result[i].ISI)) + "</td>";
          temp.push(0);
          isi.push(result[i].isi);
        }
        $("#detdetail_ppbrg").html(str);
      }
    });
    // $.ajax({
    //   url     : "{!! url('getBatchNoBukti') !!}",
    //   type    : "POST",
    //   async   : false,
    //   data    : {
    //     _token : _token,
    //     no_bukti : no_bukti
    //   },
    //   success : function(result) {
    //     for (var i = 0; i < result.length; i++) {
    //       var dum = [];
    //       dum.push(result[i].no_batch);
    //       dum.push(result[i].kode_barang);
    //       dum.push(result[i].tanggal);
    //       dum.push(numberWithCommas(round(result[i].qty / isi[result[i].urut] * -1, 1)));
    //       dum.push(result[i].urut);
    //       dum.push(isi[result[i].urut]);
    //       detbatch.push(dum);
    //     }
    //   }
    // });
    // for (var i = 0; i < detbatch.length; i++) {
    //   temp[detbatch[i][4]] += parseInt(detbatch[i][3].replace(/,/g, ""));
    // }
    // for (var i = 0; i < temp.length; i++) {
    //   $("#det"+i+"-jmlbatch").html(numberWithCommas(temp[i]));
    // }
    $("#detailPpBrg").modal('toggle');
  }
  // end tampilkan detail

  function showDetBatch() {
    if (g_iddet === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    var index = g_iddet;
    str = "";
    if (detbatch.length > 0) {
        var count = 0;
        for (var i = 0; i < detbatch.length; i++) {
          if (detbatch[i][4] == index) {
            str += "<tr><td>" + detbatch[i][1] + "</td>\
              <td>" + detbatch[i][0] + "</td>\
              <td>" + format_date(detbatch[i][2]) + "</td>\
              <td>" + detbatch[i][3] + "</td>\
              </tr>";
            count += 1;
          }
        }
        if (count == 0) {
          $("#detdata_batch").html("<tr><td colspan='4'>Tidak ada batch.</td></tr>");
        } else {
          $("#detdata_batch").html(str);
        }
    } else {
      $("#detdata_batch").html("<tr><td colspan='4'>Tidak ada batch.</td></tr>");
    }
    $("#detailBatch").modal('toggle');
  }

  // start tampilkan
  function show() {
    if (g_id === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    var _id = g_id; var cekauth = 0; var gudang = "";
    $("#eid").val(_id);
    var id_outtransaksi = 0;
    var no_bukti = g_nb;
    var _token = $("#_token").val();
    var isi = [];
    ebatch = [];
    $.ajax({
      url     : "{!! url('showPpBrg') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        nobukti : no_bukti
      },
      success : function(result) {
        cekauth = result.IsOtorisasi1;
        $("#eno_urut").val(result.NOURUT);
        $("#eno_bukti").val(result.NOBUKTI);
        $("#etanggal").val(result.TANGGAL);
        $("#egudang").val(result.KodeGdg);
        $("#egudang").val(result.KodeRak);
        $("#egudang").val(result.KodeLokasi);
        $("#ebarcode").val(result.BarcodeLoc);
        gudang = result.KodeGdg;
        $("#eketerangan").val(result.KETERANGAN);
        // $("#eid_outtransaksi").val(result.id_outtransaksi);
        // id_outtransaksi = result.id_outtransaksi;
        no_bukti = result.NOBUKTI;
      }
    });
    if (cekauth == 1) {
      alertify.warning("Transaksi sudah diotorisasi");
      return;
    }
    // var checkhold = checkHold(g_nb);
    // if (checkhold) {
    //   alertify.warning("Transaksi sedang diedit.");
    //   return;
    // }
    // hold(g_nb);
    $.ajax({
      url     : "{!! url('showDetPpBrg') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        id : g_nb
      },
      success : function(result) {
        eppbrg = [];
        for (var i = 0; i < result.length; i++) {
          temp = [];
          temp.push(result[i].NoBeli);//0
          temp.push(result[i].KODEBRG);//1
          temp.push(result[i].NamaBrg);//2
          temp.push(numberWithCommas(result[i].QNT));//3
          temp.push(result[i].SATUAN);//4
          temp.push(numberWithCommas(result[i].ISI));//5
          temp.push(result[i].QNT);//6
          temp.push(result[i].URUT);//7
          temp.push(result[i].NOBUKTI);//8
          temp.push(result[i].UrutBeli);//9
          eppbrg.push(temp);
          isi.push(result[i].ISI);
        }
      }
    });
    $.ajax({
      url     : "{!! url('showDetOutTransaksiPpBrg') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        id : g_nb
      },
      success : function(result) {
        for (var i = 0; i < eppbrg.length; i++) {
          for (var j = 0; j < result.length; j++) {
            if (eppbrg[i][0] == result[j].NoBeli && eppbrg[i][1] == result[j].KODEBRG && eppbrg[i][9] == result[j].UrutBeli) {
              eppbrg[i][6] += result[j].QNTSISA;
              break;
            }
          }
        }
      }
    });
    loadppbrg('e');
    $("#editPpBrg").modal('toggle');
  }
  // end tampilkan invoice

  // start edit invoice
  function edit() {
    var _token = $("#_token").val();
    var _id = $("#eid").val();
    var _no_urut = $("#eno_urut").val();
    var _no_bukti = $("#eno_bukti").val();
    var _tanggal = $("#etanggal").val();
    var _id_outtransaksi = $("#eno_outtransaksi").val();
    var _gudang = $("#egudang").val();
    var _rak = $("#erak").val();
    var _lokasi = $("#elokasi").val();
    var _barcode = $("#ebarcode").val();
    var _keterangan = $("#eketerangan").val();
    var cek = cekBatch('e');
    if (_id != "" && _no_urut != "" && _no_bukti != "" && _tanggal != "" &&
        _gudang != "" && _rak != "" &&  _lokasi != "" &&  _barcode != "" &&
      eppbrg.length > 0
      // && cek == '1' && _id_outtransaksi != ""
    ) {
      removeCommas('e');
      $.ajax({
        url     : "{!! url('addPpBrg') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          id : _id,
          no_urut : _no_urut,
          no_bukti : _no_bukti,
          tanggal : _tanggal,
          gudang : _gudang,
          rak : _rak,
          lokasi : _lokasi,
          barcode : _barcode,
          id_outtransaksi : _id_outtransaksi,
          keterangan : _keterangan,
          ppbrg : eppbrg,
          batch : ebatch
        },
        success : function(result) {
          if (result == 1) {
            loadAll(); select(_id, _no_bukti, _no_urut,'');
            $("#editPpBrg").modal('toggle');
            alertify.success('Data  Persiapan Barang telah diubah.');
          } else {
            returnCommas('e');
            alertify.warning('Barang '+result+' melebihi batas kuantitas Outstanding Transaksi.');
          }
        }
      });
    }
    else {
      if (cek != '1') {
        if (cek.split("&")[0] == '0') {
          alertify.alert('Gagal mengubah  Persiapan barang!', 'Jumlah batch kode barang '+cek.split("&")[1]+' tidak sama.', function(){ });
        } else {
          alertify.alert('Gagal mengubah  Persiapan barang!', 'Jumlah batch kode barang '+cek.split("&")[2]+' pada no. batch '+cek.split("&")[1]+' dengan expired date '+cek.split("&")[3]+' melebihi jumlah qty batch.', function(){ });
        }
      } else {
        alertify.alert('Gagal mengubah  Persiapan barang!', 'Semua kolom harus terisi.', function(){ });
      }
    }
  }
  // end edit invoice

  // start hapus invoice
  function erase() {
    if (g_nb === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    var _token = $("#_token").val();
    var _choice = "D";
    var cekauth = 0;
    $.ajax({
      url     : "{!! url('showPpBrg') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        nobukti :g_nb
      },
      success : function(result) {
        cekauth = result.IsOtorisasi1;
      }
    });
    if (cekauth == 1) {
      alertify.warning("Transaksi sudah diotorisasi");
      return;
    }
    alertify.confirm('Hapus  Persiapan Barang', 'Apakah yakin ingin menghapus  Persiapan Barang dengan nomor bukti ' + g_nb + ' dan Kode Barang ' + g_kd + ' ?',
      function(){
        var _token = $("#_token").val();
        $.ajax({
          url     : "{!! url('erasePpBrg') !!}",
          type    : "POST",
          async   : false,
          data    : {
            _token : _token,
            choice : _choice,
            no_bukti : g_nb,
            urut : g_nu
          },
          success : function(result) {
            if (result.split(";;")[0] == "1") {
              loadAll();
              alertify.success('Data  Persiapan Barang telah dihapus.');
            } else {
              alertify.warning('Data  Persiapan Barang gagal telah dihapus.');
            }
          }
        });
    },function(){});
  }
  // end hapus invoice
</script>
@endsection
