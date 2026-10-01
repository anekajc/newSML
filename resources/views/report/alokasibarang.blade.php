@extends('newmaster')

@section('css')
<style>
  .modal-lg {
    min-width: 98%;
  }
</style>
@endsection

@section('buttons')
  <button type="button" class="btn btn-primary btn-sm btn-top" onclick="loadAll()" rel="tooltip" data-placement="bottom" title="Refresh" style="width: 30px;"><i class="bi bi-arrow-repeat"></i></button>&nbsp;&nbsp;
  <!-- <button class="btn btn-success btn-sm btn-top" type="button" id="tambah" onclick="showPilih()" rel="tooltip" data-placement="bottom" title="Tambah" style="width: 30px;"><i class="bi bi-plus-circle"></i></button>&nbsp;&nbsp; -->
  <!-- <button type="button" class="btn btn-danger btn-sm" onclick="erase()" rel="tooltip" data-placement="bottom" title="Hapus" style="width: 30px;"><i class="bi bi-trash"></i></button> -->
@endsection

@section('content')
<div class="container-fluid">
<h1>Alokasi Barang</h1>
</div>

<div class="container-fluid">
  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <div class="card">
    <div class="card-header" >
      <div class="row">
        <nav style="width: 100%;">
          <div class="nav nav-pills col-12" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">Outstanding Penerimaan Barang</a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">Alokasi Barang</a>
          </div>
        </nav>
      </div>
    </div>
    <div class="card-body" >
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
          <div class="card-body">
            <div class="row">
              <div class="col-12" style="padding-left: 20px; padding-right: 20px; overflow:auto;">
                <table class="table table-bordered table-striped" id="tabel_penerimaan" style="background:white;">
                  <thead>
                    <tr>
                      <th width="10%">Actions</th>
                      <th width="10%">No. Penerimaan</th>
                      <th width="5%">Tanggal</th>
                      <th width="10%">Kode Barang</th>
                      <th width="25%">Nama Barang</th>
                      <th width="5%">Gudang</th>
                      <th width="5%">Satuan</th>
                      <th width="10%">Qty Penerimaan</th>
                      <th width="10%">Qty Alokasi</th>
                      <th width="10%">Qty Sisa</th>
                    </tr>
                  </thead>
                  <tbody id="penerimaan_data">
                    @if (count($penerimaan) > 0)
                      @for ($i = 0; $i < count($penerimaan); $i++)
                        <!-- <tr id="{!! $i !!}-penerimaan" onclick="select2({!! $i !!},'{!! $penerimaan[$i]->NoBukti1 !!}','{!! $penerimaan[$i]->NOURUT !!}','{!! $penerimaan[$i]->TANGGAL !!}','{!! $penerimaan[$i]->Kodebrg !!}','{!! $penerimaan[$i]->Qntsaldo !!}','{!! $penerimaan[$i]->QNTAMBIL !!}','{!! $penerimaan[$i]->SisaOut !!}','{!! $penerimaan[$i]->Kodegdg !!}','{!! $penerimaan[$i]->NAMAGUDANG !!}')"> -->
                        <tr id="{!! $i !!}-penerimaan">
                          <td class="text-center">
                            <button type="button" class="btn btn-warning btn-sm btn-opsi" onclick="showDetailStk('{!! $penerimaan[$i]->NoBukti1 !!}')">
                              <i class="bi bi-info-lg"></i>
                            </button>
                            <button class="btn btn-success btn-sm btn-top" type="button" id="tambah" onclick="select2({!! $i !!},'{!! $penerimaan[$i]->NoBukti1 !!}','{!! $penerimaan[$i]->NOURUT !!}','{!! $penerimaan[$i]->TANGGAL !!}','{!! $penerimaan[$i]->Kodebrg !!}','{!! $penerimaan[$i]->QntSaldo !!}','{!! $penerimaan[$i]->QNTAMBIL !!}','{!! $penerimaan[$i]->QNTSISA !!}','{!! $penerimaan[$i]->Kodegdg !!}','{!! $penerimaan[$i]->NAMAGUDANG !!}')"
                              rel="tooltip" data-placement="bottom" title="Tambah" style="width: 30px;">
                              <i class="bi bi-plus-circle"></i>
                            </button>
                          </td>
                          <td>{!! $penerimaan[$i]->NoBukti1 !!}</td>
                          <td>{!! date("d/m/Y", strtotime($penerimaan[$i]->TANGGAL)) !!}</td>
                          <td>{!! $penerimaan[$i]->Kodebrg !!}</td>
                          <td>{!! $penerimaan[$i]->NAMABRG !!}</td>
                          <td>{!! $penerimaan[$i]->Kodegdg !!}</td>
                          <td>{!! $penerimaan[$i]->Satuan !!}</td>
                          <td class="text-right">{!! number_format($penerimaan[$i]->QntSaldo) !!}</td>
                          <td class="text-right">{!! number_format($penerimaan[$i]->QNTAMBIL) !!}</td>
                          <td class="text-right">{!! number_format($penerimaan[$i]->QNTSISA) !!}</td>
                        </tr>
                      @endfor
                    @else
                      <tr>
                        <td colspan="10">Tidak ada data Outstanding Penerimaan Barang ditemukan.</td>
                        <td style="display: none;"></td>
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
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
          <div class="row">
            <div class="col-12" style="padding-left: 20px; padding-right: 20px; overflow:auto;">
              <table class="table table-bordered table-striped" id="tabel_alokasibrg" style="background:white;">
                <thead>
                  <tr>
                    <th width="10%">Actions</th>
                    <th width="10%">No. Bukti</th>
                    <th width="5%">Tanggal</th>
                    <th width="10%">No. Penerimaan</th>
                    <th width="10%">Kode Barang</th>
                    <th width="15%">Nama Barang</th>
                    <th width="5%">Qty</th>
                    <th width="5%">Satuan</th>
                    <th width="5%">Gudang</th>
                    <th width="5%">Rak</th>
                    <th width="5%">Lokasi</th>
                    <th width="7%">Barcode</th>
                    <th width="13%">Keterangan</th>
                  </tr>
                </thead>
                <tbody id="alokasibrg_data">
                  @if (count($alokasibrg) > 0)
                    @for ($i = 0; $i < count($alokasibrg); $i++)
                      <tr id="{!! $i !!}-tr">
                        <td class="text-center">
                          <button type="button" class="btn btn-warning btn-sm btn-opsi" onclick="showDetailStk('{!! $penerimaan[$i]->NoBukti1 !!}')">
                            <i class="bi bi-info-lg"></i>
                          </button>
                          <button type="button" class="btn btn-danger btn-sm" onclick="select({!! $i !!}, '{!! $alokasibrg[$i]->NOBUKTI !!}',{!! $alokasibrg[$i]->URUT !!},'{!! $alokasibrg[$i]->KODEBRG !!}')"
                            rel="tooltip" data-placement="bottom" title="Hapus" style="width: 30px;">
                            <i class="bi bi-trash"></i>
                          </button>
                        </td>
                        <td>{!! $alokasibrg[$i]->NOBUKTI !!}</td>
                        <td>{!! date("d/m/Y", strtotime($alokasibrg[$i]->TANGGAL)) !!}</td>
                        <td>{!! $alokasibrg[$i]->noso !!}</td>
                        <td>{!! $alokasibrg[$i]->KODEBRG !!}</td>
                        <td>{!! $alokasibrg[$i]->NamaBrg !!}</td>
                        <td class="text-right">{!! number_format($alokasibrg[$i]->QNT) !!}</td>
                        <td>{!! $alokasibrg[$i]->SATUAN !!}</td>
                        <td>{!! $alokasibrg[$i]->KodeGdg !!}</td>
                        <td>{!! $alokasibrg[$i]->KodeRak !!}</td>
                        <td>{!! $alokasibrg[$i]->KodeLokasi !!}</td>
                        <td>{!! $alokasibrg[$i]->BarcodeLoc !!}</td>
                        <td>{!! $alokasibrg[$i]->KETERANGAN !!}</td>
                      </tr>
                    @endfor
                  @else
                    <tr>
                      <td colspan="13">Tidak ada Alokasi Barang.</td>
                      <td style="display: none;"></td>
                      <td style="display: none;"></td>
                      <td style="display: none;"></td>
                      <td style="display: none;"></td>
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

<!-- start modal detail stock  -->
<div class="modal fade" id="detailStockBrg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding-bottom: 5px; padding-top: 5px;">
        <h5 class="modal-title" id="exampleModalLabel">Detail Barang Lokasi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12 tableFixHead" style="height: 40vh; overflow:auto;">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th width="20%">Kode Barang</th>
                  <th width="35%">Nama Barang</th>
                  <th width="10%">Gudang</th>
                  <th width="15%">Lokasi</th>
                  <th width="10%">Qty Penerimaan</th>
                  <th width="10%">Qty Alokasi</th>
                </tr>
              </thead>
              <tbody id="detdetail_stkbrg">
                <tr>
                  <td colspan="6">Tidak ada Barang Lokasi.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal detail stock -->

<!-- start modal pilih penerimaan -->
<div class="modal fade" id="pilihpenerimaan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding-bottom: 5px; padding-top: 5px;">
        <h5 class="modal-title" id="exampleModalLabel">Pilih Penerimaan Barang</h5>
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
          <div class="col-7">
          </div>
          <div class="col-1">
            <div class="form-group text-right">
              <label for="tanggal">Tanggal</label>
            </div>
          </div>
          <div class="col-onehalf">
            <div class="form-group">
              <input type="date" class="form-control" id="tanggal" value="{!! date('Y-m-d') !!}" >
            </div>
          </div>
        </div>
        <hr style="margin-top: 0.2rem; margin-bottom: 0.3rem;" />
        <div class="row">
          <div class="col-1">
            <div class="form-group">
              <label for="no_bukti">No. Penerimaan</label>
            </div>
          </div>
          <div class="col-onehalf">
            <div class="form-group">
              <input type="text" class="form-control" id="no_Penerimaan" disabled>
            </div>
          </div>
        </div>
        <hr style="margin-top: 7px; margin-bottom: 7px;" />
        <div class="row">
          <div class="col-12" style="height: 60vh; overflow:auto;">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th style='width:20%;'>Kode Barang</th>
                  <th style='width:45%;'>Nama Barang</th>
                  <th style='width:10%;'>Qty Penerimaan</th>
                  <th style='width:10%;'>Qty Alokasi</th>
                  <th style='width:10%;'>Qty Sisa</th>
                  <th style='width:5%;'>Pilih</th>
                </tr>
              </thead>
              <tbody id="penerimaan_detail">
                <tr>
                  <td colspan="6">Tidak ada Outstanding Penerimaan Barang yang dipilih.</td>
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
<!-- End modal pilih penerimaan -->

<!-- start modal scan barcode -->
<div class="modal fade" id="scanbarcode" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding-bottom: 5px; padding-top: 5px;">
        <h5 class="modal-title" id="exampleModalLabel">Scan Barcode</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="resetscan()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="kodebrg">Kode Barang</label>
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <input type="text" class="form-control" id="kodebrg" disabled>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="namabrg">Nama Barang</label>
            </div>
          </div>
          <div class="col-6">
            <div class="form-group">
              <input type="text" class="form-control" id="namabrg" disabled>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group text-center">
              <label for="barcode" id="lbarcode" >Scan Barang</label>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="qtypenerimaan">Qty Penerimaan</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <input type="text" class="form-control format-number text-right" id="qtypenerimaan" disabled>
            </div>
          </div>
          <div class="col-4">
          </div>
          <div class="col-4">
            <div class="form-group">
              <input type="text" class="form-control" id="barcode" onkeypress="showOuttransaksi(event)" autofocus>
            </div>
          </div>
        </div>
        <div class="row">
          <input type="hidden" id="qtyalokasi">
          <div class="col-2">
            <div class="form-group">
              <!-- <label for="qtyalokasi">Qty Alokasi</label> -->
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <!-- <input type="text" class="form-control format-number text-right" id="qtyalokasi" disabled> -->
            </div>
          </div>
          <div class="col-4">
          </div>
          <div class="col-1">
            <div class="form-group">
              <label for="cbbarang">Barang</label>
            </div>
          </div>
          <div class="col-1">
            <div class="form-group">
              <i class="bi bi-check-circle text-success" id='cbbarang'></i>
              <i class="bi bi-x-circle text-danger" id='cbbarang1'></i>
              <!-- <input type='checkbox' id='cbbarang'> -->
            </div>
          </div>
          <div class="col-1">
            <div class="form-group">
              <label for="cblokasi">Lokasi</label>
            </div>
          </div>
          <div class="col-1">
            <div class="form-group">
              <i class="bi bi-check-circle text-success" id='cblokasi'></i>
              <i class="bi bi-x-circle text-danger" id='cblokasi1'></i>
              <!-- <input type='checkbox' id='cblokasi'> -->
            </div>
          </div>
        </div>
        <hr style="margin-top: 7px; margin-bottom: 7px;" />
        <div class="row">
          <div class="col-12" style="height: 40vh; overflow:auto;">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th style='width:20%;'>Lokasi</th>
                  <th style='width:10%;'>Stock</th>
                  <th style='width:10%;'>Qty</th>
                  <th style='width:5%;'>Pilih</th>
                </tr>
              </thead>
              <tbody id="scanbarcode_detail">
                <tr>
                  <td colspan="4">Stock Barang Kosong.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="resetscan()">Batal</button>
        <button type="button" class="btn btn-primary" onclick="Addscan()">OK</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal scan barcode -->

<!-- start modal add  -->
<div class="modal fade" id="addAlokasiBrg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding-bottom: 5px; padding-top: 5px;">
        <h5 class="modal-title" id="exampleModalLabel">Tambah Alokasi Barang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="reset()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" id="no_penerimaan">
        </div>
        <hr style="margin-top: 0.2rem; margin-bottom: 0.3rem;" />
        <div class="row">
          <div class="col-1">
            <div class="form-group">
              <label for="gudang">Gudang</label>
            </div>
          </div>
          <div class="col-1">
            <div class="form-group">
              <input type="text" class="form-control" id="gudang" disabled>
            </div>
          </div>
        </div>
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
        <hr style="margin-top: 7px; margin-bottom: 7px;" />
        <div class="row">
          <div class="col-12 tableFixHead" style="height: 60vh;">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>No. Penerimaan</th>
                  <th>Kode Barang</th>
                  <th>Nama Barang</th>
                  <th>Qty Sisa</th>
                  <th>Qty</th>
                  <th>Satuan</th>
                  <th>Isi</th>
                </tr>
              </thead>
              <tbody id="detail_alokasibrg">
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
<div class="modal fade" id="detailAlokasiBrg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding-bottom: 5px; padding-top: 5px;">
        <h5 class="modal-title" id="exampleModalLabel">Detail Alokasi Barang</h5>
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
              <tbody id="detdetail_alokasibrg">
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
<div class="modal fade" id="editAlokasiBrg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding-bottom: 5px; padding-top: 5px;">
        <h5 class="modal-title" id="exampleModalLabel">Koreksi Alokasi Barang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="reset('e')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" id="eid">
          <input type="hidden" id="eno_penerimaan">
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
          <div class="col-1">
            <div class="form-group">
              <label for="egudang">Gudang</label>
            </div>
          </div>
          <div class="col-1">
            <div class="form-group">
              <input type="text" class="form-control" id="egudang" disabled>
            </div>
          </div>
          <div class="col-5">
          </div>
          <div class="col-1">
            <div class="form-group">
              <label for="ebarcode">Barcode Lokasi</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <input type="text" class="form-control" id="ebarcode" disabled>
            </div>
          </div>
          <input type="hidden" id="erak">
          <input type="hidden" id="elokasi">
          <!-- <div class="col-1">
            <div class="form-group">
              <label for="rak">Rak</label>
            </div>
          </div> -->
          <!-- <div class="col-2">
            <div class="form-group">
              <select class="form-control" id="rak" onchange="loadlokasi('I')" >
                <option value="">-- Pilih Rak --</option>

              </select>
            </div>
          </div> -->
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
          <!-- <div class="col-2">
          </div>
          <div class="col-1">
            <div class="form-group">
              <label for="lokasi">Lokasi</label>
            </div>
          </div> -->
          <!-- <div class="col-2">
            <div class="form-group">
              <select class="form-control" id="lokasi">
                <option value="">-- Pilih Lokasi --</option>

              </select>
            </div>
          </div> -->
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
                  <th>Qty Sisa</th>
                  <th>Qty</th>
                  <th>Satuan</th>
                  <th>Isi</th>
                </tr>
              </thead>
              <tbody id="edetail_alokasibrg">
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
  var data_penerimaan = [], data_scanbarcode = [], temp = [];
  var alokasibrg = [], ealokasibrg = [], batch = [], ebatch = [], detbatch = [];
  var xkodebrg = "",xlokasi = "",xbarcode = "", xkodegdg = "", xkoderak = "", xkodelokasi = "";
  var g_idoutp="",g_nobuktioutp="",g_nourutoutp="",g_tgloutp="",g_kodebrgoutp="",g_namabrgoutp="",g_qntsaldooutp="",g_qntambiloutp="",g_qntsisaoutp="",g_kodegdgoutp="",g_namagudangoutp="";

  var g_id = "", g_nb = "", g_nu = "", g_kd = "", g_iddet = "", g_id_2 = "";
  var str = "";
  var j = 0;
  // start document ready
	$(document).ready(function(){
    restrictedDate();
    $(".format-number").autoNumeric('init', {mDec: '2'});
    var table = $('#tabel_penerimaan').DataTable({"lengthMenu": [[-1, 10, 25, 50, 100], ["All", 10, 25, 50, 100]]});
    table.on('search', function () {
      $('#penerimaan_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_idoutp=""; g_nobuktioutp=""; g_nourutoutp=""; g_tgloutp=""; g_kodebrgoutp=""; g_namabrgoutp=""; g_qntsaldooutp=""; g_qntambiloutp=""; g_qntsisaoutp=""; g_kodegdgoutp=""; g_namagudangoutp="";
    } );
    table.on('order', function () {
      $('#penerimaan_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_idoutp=""; g_nobuktioutp=""; g_nourutoutp=""; g_tgloutp=""; g_kodebrgoutp=""; g_namabrgoutp=""; g_qntsaldooutp=""; g_qntambiloutp=""; g_qntsisaoutp=""; g_kodegdgoutp=""; g_namagudangoutp="";
    } );
    var table = $('#tabel_alokasibrg').DataTable({"lengthMenu": [[-1, 10, 25, 50, 100], ["All", 10, 25, 50, 100]]});
    table.on('search', function () {
      $('#alokasibrg_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_nb = "", g_nu = "", g_iddet = "";
    } );
    table.on('order', function () {
      $('#alokasibrg_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_nb = "", g_nu = "", g_iddet = "";
    } );
	});

  $("#cbbarang").hide();
  $("#cblokasi").hide();
  $("#cbbarang1").show();
  $("#cblokasi1").show();
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
    if ($("#tipe").val()==0) {
      $("#lbarcode").html("Barcode Lokasi");
    } else {
      $("#lbarcode").html("Barcode Lokasi");
    }
  }

  // start refresh tabel
  function loadAll() {
    $('#tabel_penerimaan').DataTable().destroy();
    $('#tabel_alokasibrg').DataTable().destroy();
    $('#penerimaan_data > tr').each(function() {
      $(this).css('background-color', '');
    });
    g_idoutp=""; g_nobuktioutp=""; g_nourutoutp=""; g_tgloutp=""; g_kodebrgoutp=""; g_namabrgoutp=""; g_qntsaldooutp=""; g_qntambiloutp=""; g_qntsisaoutp=""; g_kodegdgoutp=""; g_namagudangoutp="";
    $('#alokasibrg_data > tr').each(function() {
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
        menu : '/alokasibrg'
      },
      success : function(result) {
        akses = result;
      }
    });
    $.ajax({
      url     : "{!! url('loadAllOutPenerimaan') !!}",
      type    : "GET",
      async   : false,
      success : function(result) {
        if (result.length > 0) {
          str = "";
          $('#penerimaan_data').html("");
          for (var i = 0; i < result.length; i++) {
            str = str + '<tr id="'+i+'-penerimaan">\
              <td width="10%" class="text-center">\
                <button type="button" class="btn btn-warning btn-sm btn-opsi" onclick="showDetailStk(\''+ result[i].NoBukti1 +'\')">\
                  <i class="bi bi-info-lg"></i>\
                </button>\
                <button type="button" class="btn btn-success btn-sm btn-top"  id="tambah" onclick="select2('+i+',\''+ result[i].NoBukti1 +'\',\''+ result[i].NOURUT +'\',\''+ result[i].TANGGAL +'\',\''+ result[i].Kodebrg +'\',\''+ result[i].QntSaldo +'\', \''+ result[i].QNTAMBIL +'\',\
                  \''+ result[i].QNTSISA +'\',\''+ result[i].Kodegdg +'\',\''+ result[i].NAMAGUDANG +'\')" rel="tooltip" data-placement="bottom" title="Tambah" style="width: 30px;">\
                  <i class="bi bi-plus-circle"></i>\
                </button>\
              </td>\
              <td width="10%">' + result[i].NoBukti1 + '</td>\
              <td width="5%">' + format_date(result[i].TANGGAL) + '</td>\
              <td width="10%">' + result[i].Kodebrg + '</td>\
              <td width="25%">' + result[i].NAMABRG + '</td>\
              <td width="5%">' + result[i].Kodegdg + '</td>\
              <td width="5%">' + result[i].Satuan + '</td>\
              <td width="10%" class="text-right">' + numberWithCommas(parseFloat(result[i].QntSaldo)) + '</td>\
              <td width="10%" class="text-right">' + numberWithCommas(parseFloat(result[i].QNTAMBIL)) + '</td>\
              <td width="10%" class="text-right">' + numberWithCommas(parseFloat(result[i].QNTSISA)) + '</td>\
              </tr>';
          }
          $('#penerimaan_data').html(str);
        }
        else {
          $('#penerimaan_data').html('<tr>\
            <td colspan="10">Tidak ada data Outstanding Penerimaan Barang ditemukan.</td>\
            <td style="display: none;"></td>\
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
    $.ajax({
      url     : "{!! url('loadAllAlokasiBrg') !!}",
      type    : "GET",
      async   : false,
      success : function(result) {
        if (result.length > 0) {
          str = "";
          var auth = "";
          $('#alokasibrg_data').html("");
          for (var i = 0; i < result.length; i++) {
            str = str + '<tr id="' + i + '-tr">\
              <td width="10%" class="text-center">\
                <button type="button" class="btn btn-warning btn-sm btn-opsi" onclick="showDetailStk(\'' + result[i].NOBUKTI + '\')">\
                  <i class="bi bi-info-lg"></i>\
                </button>\
                <button type="button" class="btn btn-danger btn-sm" onclick="select(' + i + ', \'' + result[i].NOBUKTI + '\', \'' + result[i].URUT + '\', \'' + result[i].KODEBRG + '\')" rel="tooltip" data-placement="bottom" title="Hapus" style="width: 30px;">\
                  <i class="bi bi-trash"></i>\
                </button>\
              </td>\
              <td width="10%">' + result[i].NOBUKTI + '</td>\
              <td width="7%">' + format_date(result[i].TANGGAL) + '</td>\
              <td width="10%">' + result[i].noso + '</td>\
              <td width="10%">' + result[i].KODEBRG + '</td>\
              <td width="16%">' + result[i].NamaBrg + '</td>\
              <td width="5%" class="text-right">' + numberWithCommas(parseInt(result[i].QNT)) + '</td>\
              <td width="5%">' + result[i].SATUAN + '</td>\
              <td width="5%">' + result[i].KodeGdg + '</td>\
              <td width="5%">' + result[i].KodeRak + '</td>\
              <td width="5%">' + result[i].KodeLokasi + '</td>\
              <td width="10%">' + result[i].BarcodeLoc + '</td>\
              <td width="12%">' + result[i].KETERANGAN + '</td>\
              </tr>';
          }
          $('#alokasibrg_data').html(str);
        }
        else {
          $('#alokasibrg_data').html('<tr>\
            <td colspan="13">Tidak ada Alokasi Barang.</td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
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
    var table = $('#tabel_penerimaan').DataTable({"lengthMenu": [[-1, 10, 25, 50, 100], ["All", 10, 25, 50, 100]]});
    table.on('search', function () {
      $('#penerimaan_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_idoutp=""; g_nobuktioutp=""; g_nourutoutp=""; g_tgloutp=""; g_kodebrgoutp=""; g_namabrgoutp=""; g_qntsaldooutp=""; g_qntambiloutp=""; g_qntsisaoutp=""; g_kodegdgoutp=""; g_namagudangoutp="";
    } );
    table.on('order', function () {
      $('#penerimaan_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_idoutp=""; g_nobuktioutp=""; g_nourutoutp=""; g_tgloutp=""; g_kodebrgoutp=""; g_namabrgoutp=""; g_qntsaldooutp=""; g_qntambiloutp=""; g_qntsisaoutp=""; g_kodegdgoutp=""; g_namagudangoutp="";
    } );
    var table = $('#tabel_alokasibrg').DataTable({"lengthMenu": [[-1, 10, 25, 50, 100], ["All", 10, 25, 50, 100]]});
    table.on('search', function () {
      $('#alokasibrg_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_nb = "", g_nu = "", g_iddet = "";
    } );
    table.on('order', function () {
      $('#alokasibrg_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_nb = "", g_nu = "", g_iddet = "";
    } );
  }
  // end refresh table

  function select2(_row,_nobuktioutp,_nourutoutp,_tgloutp,_kodebrgoutp,_qntsaldooutp,_qntambiloutp,_qntsisaoutp,_kodegdgoutp,_namagudangoutp) {
    $('#penerimaan_data > tr').each(function() {
      $(this).css('background-color', '');
    });
    $("#"+_row+"-penerimaan").css('background-color', 'gold');
    g_idoutp=_row; g_nobuktioutp=_nobuktioutp; g_nourutoutp=_nourutoutp; g_tgloutp=_tgloutp; g_kodebrgoutp=_kodebrgoutp;
    // g_namabrgoutp=_namabrgoutp;
    g_qntsaldooutp=_qntsaldooutp; g_qntambiloutp=_qntambiloutp; g_qntsisaoutp=_qntsisaoutp; g_kodegdgoutp=_kodegdgoutp; g_namagudangoutp=_namagudangoutp;
    showPilih();
  }

  function select(_id, _nb, _nu, _kd) {
    $('#alokasibrg_data > tr').each(function() {
      $(this).css('background-color', '');
    });
    $("#"+_id+"-tr").css('background-color', 'gold');
    g_id = _id; g_nb = _nb; g_nu = _nu; g_kd = _kd;
    erase();
  }

  function selectdet(_row, name = "") {
    $('#'+name+'detail_alokasibrg > tr').each(function() {
      $(this).css('background-color', '');
    });
    $("#"+name+_row+"-trdet").css('background-color', 'gold');
    g_iddet = _row;
  }

  function loadDetailpenerimaan() {
    var _token = $("#_token").val();
    if ($("#no_Penerimaan").val() != "") {
      var _nobukti = $("#no_Penerimaan").val();
      $.ajax({
        url     : "{!! url('showDetOutPenerimaan') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          nobukti : _nobukti
        },
        success : function(result) {
          if (result.length > 0) {
            for (var i = 0; i < result.length; i++) {
              temp = [];
              if (result[i].QNTSISA > 0) {
                temp.push(result[i].NoBukti1);//0
                temp.push(result[i].Kodebrg);//1
                temp.push(result[i].NAMABRG);//2
                temp.push(numberWithCommas(result[i].QntSaldo));//3
                if (result[i].QNTAMBIL == 0){
                  temp.push(0);//4
                } else {
                  temp.push(numberWithCommas(result[i].QNTAMBIL));//4
                }
                temp.push(numberWithCommas(result[i].QNTSISA));//5
                temp.push(0);//6
                temp.push(numberWithCommas(result[i].QNTSISA));//7
                temp.push(result[i].Kodegdg);//8
                temp.push(result[i].NAMAGUDANG);//9
                temp.push(result[i].Satuan);//10
                temp.push(result[i].isi);//11
                temp.push(result[i].Urut);//12
                temp.push(result[i].TANGGAL);//13
                temp.push(result[i].nosat);//14
                data_penerimaan.push(temp);
                str = str + "<tr>\
                  <td style='width:20%'>" + result[i].Kodebrg + "</td>\
                  <td style='width:50%'>" + result[i].NAMABRG + "</td>\
                  <td style='width:10%' class='text-right'>" + numberWithCommas(parseInt(result[i].QntSaldo)) + "</td>";
                  if (result[i].QNTAMBIL == 0){
                    str = str + "<td style='width:10%' class='text-right' id='"+ i +"-QntOut'>" + numberWithCommas(0) + "</td>";
                  } else {
                    str = str + "<td style='width:10%' class='text-right' id='"+ i +"-QntOut'>" + numberWithCommas(parseInt(result[i].QNTAMBIL)) + "</td>";
                  }
                  str = str + "<td style='width:10%' class='text-right' id='"+ i +"-QntSisa'>"+ numberWithCommas(parseInt(result[i].QNTSISA)) +"</td>\
                  <td style='width:5%' class='text-center'><button type='button' class='btn btn-warning btn-sm btn-opsi' onclick='showScanBracode("+ i +",\""+ result[i].NoBukti1 +"\","+ result[i].Kodebrg +","+ result[i].Urut +")'><i class='bi bi-info-lg'></i></button></td></tr>";
              }
            }
            $("#penerimaan_detail").html(str);
            $(".format-number").autoNumeric('init', {mDec: '2'});
          } else {
            $("#penerimaan_detail").html('<tr><td colspan="6">Tidak ada Outstanding Penerimaan yang dipilih.</td></tr>');
          }
        }
      });
    }
    else {
      $("#penerimaan_detail").html('<tr><td colspan="6">Tidak ada Outstanding Penerimaan yang dipilih.</td></tr>');
    }
  }

  function showScanBracode(i,_nobukti,_kdbrg,_urut) {
    var no_bukti = _nobukti;
    var kodebrg = _kdbrg;
    var urut = _urut;

    $("#pilihpenerimaan").css({ opacity: 0.5 });
    $("#scanbarcode").modal('toggle');
    $("#kodebrg").val(data_penerimaan[i][1]);
    $("#namabrg").val(data_penerimaan[i][2]);
    $("#qtypenerimaan").val(data_penerimaan[i][3]);
    $("#qtyalokasi").val(data_penerimaan[i][4]);
    str="";
    for (var x = 0; x < alokasibrg.length; x++) {
      if (alokasibrg[x][1] == $("#kodebrg").val()){
        temp = [];
        temp.push(alokasibrg[x][0]);//0 nobeli
        temp.push(alokasibrg[x][1]);//1 kdbrg
        temp.push(alokasibrg[x][2]);//2 nmbrg
        temp.push(alokasibrg[x][3]);//3 qntstock
        temp.push(alokasibrg[x][4]);//4 qntambil
        temp.push(alokasibrg[x][5]);//5 qnt
        temp.push(alokasibrg[x][16]);//6 chackbox
        temp.push(alokasibrg[x][17]);//7 SisaOut
        temp.push(alokasibrg[x][6]);//8 kdgdg
        temp.push(alokasibrg[x][7]);//9 nmgdg
        temp.push(alokasibrg[x][8]);//10 satuan
        temp.push(alokasibrg[x][9]);//11 isi
        temp.push(alokasibrg[x][10]);//12 urutbeli
        temp.push(alokasibrg[x][11]);//13 tgl
        temp.push(alokasibrg[x][12]);//14 nosat
        temp.push(alokasibrg[x][13]);//15 barcode
        temp.push(alokasibrg[x][14]);//16 rak
        temp.push(alokasibrg[x][15]);//17 lokasi
        temp.push("U");//18 choice
        data_scanbarcode.push(temp);
      }
    }
    if(data_scanbarcode.length==0){
      loadDetailScanBarcode(no_bukti,kodebrg,urut);
    }else{
      for (var x = 0; x < data_scanbarcode.length; x++) {
        str = str + "<tr><td style='width:20%'>" + data_scanbarcode[x][15] + "</td>";
        if (data_scanbarcode[x][3]==0){
          str = str + "<td style='width:10%' class='text-right'>" + 0 + "</td>";
        }else{
          str = str + "<td style='width:10%' class='text-right'>" + numberWithCommas(parseFloat(data_scanbarcode[x][3])) + "</td>";
        }
        str = str + "<td style='width:10%' class='text-right'><input type='text' class='format-number text-right' onkeyup='updateDataScan("+x+")' id='"+x+"-qty' value='" + data_scanbarcode[x][5] + "' disabled></td>";
        if (data_scanbarcode[x][6]==0){
          str = str + "<td style='width:5%' class='text-center'><input type='checkbox' id='"+x+"-check' onclick='updateDataScan("+x+")'></td></tr>";
        }else{
          str = str + "<td style='width:5%' class='text-center'><input type='checkbox' id='"+x+"-check' onclick='updateDataScan("+x+")' checked></td></tr>";
        }
      }
      $("#scanbarcode_detail").html(str);
      $(".format-number").autoNumeric('init', {mDec: '2'});
    }
  }

  function loadDetailScanBarcode(_nobukti,_kodebrg,_urut) {
    var _token = $("#_token").val();
    if (_nobukti != "" && _kodebrg != "" && _urut != "") {
      var _tipe = "1";
      $.ajax({
        url     : "{!! url('showDetailBrgLoc') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          id : _nobukti,
          kodebrg : _kodebrg,
          tipe : _tipe,
          urut : _urut
        },
        success : function(result) {
          if (result.length > 0) {
            for (var i = 0; i < result.length; i++) {
              temp = [];
              if (result[i].SisaOut > 0) {
                temp.push(result[i].NoBukti);//0 nobeli
                temp.push(result[i].Kodebrg);//1 kdbrg
                temp.push(result[i].namabrg);//2 nmbrg
                if (result[i].Qntsaldo == 0){
                  temp.push(0);//3 qntstock
                } else {
                  temp.push(numberWithCommas(result[i].Qntsaldo));//3 qntstock
                }
                if (result[i].QntOut == 0){
                  temp.push(0);//4 qntambil
                } else {
                  temp.push(numberWithCommas(result[i].QntOut));//4 qntambil
                }
                temp.push(numberWithCommas(result[i].SisaOut));//5 qnt
                temp.push(0);//6 chackbox
                temp.push(numberWithCommas(result[i].SisaOut));//7 SisaOut
                temp.push(result[i].Kodegdg);//8 kdgdg
                temp.push(result[i].NAMAGUDANG);//9 nmgdg
                temp.push(result[i].satuan);//10 satuan
                temp.push(result[i].isi);//11 isi
                temp.push(result[i].URUT);//12 urutbeli
                temp.push(result[i].TANGGAL);//13 tgl
                temp.push(result[i].nosat);//14 nosat
                temp.push(result[i].Barcode);//15 barcode
                temp.push(result[i].koderak);//16 rak
                temp.push(result[i].kodelokasi);//17 lokasi
                temp.push("I");//18 choice
                data_scanbarcode.push(temp);
                str = str + "<tr>\
                  <td style='width:20%'>" + result[i].Barcode + "</td>";
                  if (result[i].Qntsaldo==0){
                    str = str + "<td style='width:10%' class='text-right'>" + numberWithCommas(0) + "</td>";
                  }else{
                    str = str + "<td style='width:10%' class='text-right'>" + numberWithCommas(parseInt(result[i].Qntsaldo)) + "</td>";
                  }
                  str = str + "<td style='width:10%' class='text-right'><input type='text' class='format-number text-right' onkeyup='updateDataScan("+i+")' id='"+i+"-qty' value='" + result[i].SisaOut + "' disabled></td>\
                  <td style='width:5%' class='text-center'><input type='checkbox' id='"+i+"-check' onclick='updateDataScan("+i+")'></td></tr>";
              }
            }
            $("#scanbarcode_detail").html(str);
            $(".format-number").autoNumeric('init', {mDec: '2'});
          } else {
            $("#scanbarcode_detail").html('<tr><td colspan="4">Stock Barang Kosong.</td></tr>');
          }
        }
      });
    }
    else {
      $("#scanbarcode_detail").html('<tr><td colspan="4">Stock Barang Kosong.</td></tr>');
    }
  }

  function updateDataScan(i) {
    if ($('#' + i + '-check').is(":checked")) {
      data_scanbarcode[i][6] = 1;
    } else {
      data_scanbarcode[i][6] = 0;
    }
    if ($('#' + i + '-qty').val() != "") {
      var update_qty = parseFloat($('#' + i + '-qty').val());
      if (update_qty > data_scanbarcode[i][7]){
        $('#' + i + '-qty').val(numberWithCommas(data_scanbarcode[i][7]));
        data_scanbarcode[i][5] = numberWithCommas(data_scanbarcode[i][7]);
      } else {
        data_scanbarcode[i][5] = $('#' + i + '-qty').val();
      }
    } else {
      data_scanbarcode[i][5] = "0";
    }
  }

  function generateNoBukti() {
    var res = "";
    $.ajax({
      url     : "{!! url('generateNomorBuktiAlokasiBrg') !!}",
      type    : "GET",
      async   : false,
      success : function(result) {
        res = result;
      }
    });
    return res;
  }

  function generateNoUrut() {
    var res = "";
    $.ajax({
      url     : "{!! url('generateNomorUrutAlokasiBrg') !!}",
      type    : "GET",
      async   : false,
      success : function(result) {
        res = result;
      }
    });
    return res;
  }

  function showPilih() {
    if (g_idoutp === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    $("#no_urut").val(generateNoUrut());
    $("#no_bukti").val(generateNoBukti());
    $("#no_Penerimaan").val(g_nobuktioutp);
    restrictedDate();
    // $("#tanggal").val(g_tgloutp);
    $("#pilihpenerimaan").modal('toggle');
    $("#penerimaan_detail").html('');
    j = 0;
    data_penerimaan = [];
    data_scanbarcode = [];
    str = "";
    xbarcode = "", xkodegdg = "", xkoderak = "", xkodelokasi = "";
    loadDetailpenerimaan();
  }

  function showOuttransaksi(event) {
    var barcode=$("#barcode").val();
    let unicode= event.which;
    document.getElementById("barcode").innerHTML = unicode;
    if (unicode === 13){
      if (barcode.length===14){
        xkodebrg=$("#barcode").val();
        $("#lbarcode").html("Scan Lokasi");
      }else{
        xlokasi=$("#barcode").val();
        $("#lbarcode").html("Scan Barang");
      }
      $("#barcode").val("");
    }
    for (var i = 0; i < data_scanbarcode.length; i++) {
      if (data_scanbarcode[i][1] == xkodebrg) {
        // document.getElementById("cbbarang").checked = true;
        $("#cbbarang1").hide();
        $("#cbbarang").show();
      }
      if (data_scanbarcode[i][15] == xlokasi) {
        // document.getElementById("cblokasi").checked = true;
        $("#cblokasi1").hide();
        $("#cblokasi").show();
      }
      if (data_scanbarcode[i][1] == xkodebrg && data_scanbarcode[i][15] == xlokasi) {
        document.getElementById(""+i+"-check").checked = true;
        document.getElementById(""+i+"-qty").disabled = false;
        data_scanbarcode[i][6] = 1;
        if ($('#' + i + '-qty').val() != "") {
          var update_qty = parseFloat($('#' + i + '-qty').val());
          if (update_qty > data_scanbarcode[i][7]){
            $('#' + i + '-qty').val(numberWithCommas(data_scanbarcode[i][7]));
            data_scanbarcode[i][5] = numberWithCommas(data_scanbarcode[i][7]);
          } else {
            data_scanbarcode[i][5] = $('#' + i + '-qty').val();
          }
        } else {
          data_scanbarcode[i][5] = "0";
        }
      }
    }
  }

  function loadbarcode(name) {
    var barcode="";
    if (name==""){
      barcode=$("#barcodeloc").val();
    } else if (name=="I"){
      barcode=$("#barcodel").val();
    }
    if (barcode != "") {
      var _token = $("#_token").val();
      var _barcode = barcode
      $.ajax({
        url     : "{!! url('showPilihBarcode') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          barcode : _barcode
        },
        success : function(result) {
          if (name==""){
            $("#kodegdg").val(result.KodeGdg);
            $("#koderak").val(result.KodeRak);
            $("#kodelokasi").val(result.KodeLokasi);
          } else if (name=="I"){
            $("#gudang").val(result.KodeGdg);
            $("#rak").val(result.KodeRak);
            $("#lokasi").val(result.KodeLokasi);
          }
        }
      });
    } else {

    }
  }

  function loadrak(name) {
    var kodegdg="";
    if (name==""){
      kodegdg=$("#kodegdg").val();
    } else if (name=="I"){
      kodegdg=$("#gudang").val();
    }
    if (kodegdg != "") {
      var _kodegdg = kodegdg;
      var _token = $("#_token").val();
      $.ajax({
        url     : "{!! url('showPilihRak') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          kodegdg : _kodegdg
        },
        success : function(result) {
          str = "<option value=''>-- Pilih Rak --</option>";
          for (var i = 0; i < result.length; i++) {
            str += "<option value='"+ result[i].KodeRak +"'>" + result[i].KodeRak +" - "+ result[i].NamaRak +"</option>";
          }
          if (name==""){
            $("#koderak").html(str);
          } else if (name=="I"){
            $("#rak").html(str);
          }
        }
      });
    } else {
      // alertify.warning("Gudang harus terisi.");
    }
  }

  function loadlokasi(name) {
    var kodegdg="";
    var koderak="";
    if (name==""){
      kodegdg=$("#kodegdg").val();
      koderak=$("#koderak").val();
    } else if (name=="I"){
      kodegdg=$("#gudang").val();
      koderak=$("#rak").val();
    }

    if (kodegdg != "" || koderak != "") {
      var _koderak = koderak;
      var _kodegdg = kodegdg;
      var _token = $("#_token").val();
      $.ajax({
        url     : "{!! url('showPilihLokasi') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          koderak : _koderak,
          kodegdg : _kodegdg
        },
        success : function(result) {
          str = "<option value=''>-- Pilih Lokasi --</option>";
          for (var i = 0; i < result.length; i++) {
            str += "<option value='"+ result[i].KodeLokasi +"'>" + result[i].KodeLokasi +" - "+ result[i].NamaLokasi +"</option>";
          }
          if (name==""){
            $("#kodelokasi").html(str);
          } else if (name=="I"){
            $("#lokasi").html(str);
          }
        }
      });
    } else {
      // alertify.warning("Gudang dan Rak harus terisi.");
    }
  }

  function showAdd() {
    if (data_penerimaan.length > 0) {
      // var count = 0;
      // var barcode = "";
      // var gudang = "";
      // var rak = "";
      // var lokasi = "";
      // for (var i = 0; i < data_penerimaan.length; i++) {
      //   count = count + data_penerimaan[i][6];
      // }
      // if (count > 0) {
        $("#pilihpenerimaan").modal('toggle');

        // batch = [];
        // for (var i = 0; i < data_penerimaan.length; i++) {
        //   if (data_penerimaan[i][6] == 1 && parseInt(data_penerimaan[i][3]) > 0) {
        //     temp = [];
        //     temp.push(data_penerimaan[i][0]);//0 nobeli
        //     temp.push(data_penerimaan[i][1]);//1 kdbrg
        //     temp.push(data_penerimaan[i][2]);//2 nmbrg
        //     temp.push(data_penerimaan[i][3]);//3 qntsaldo
        //     temp.push(data_penerimaan[i][4]);//4 qntambil
        //     temp.push(data_penerimaan[i][5]);//5 qnt
        //     temp.push(data_penerimaan[i][7]);//6 kdgdg
        //     temp.push(data_penerimaan[i][8]);//7 nmgdg
        //     temp.push(data_penerimaan[i][10]);//8 satuan
        //     temp.push(data_penerimaan[i][11]);//9 isi
        //     temp.push(data_penerimaan[i][12]);//10 urutbeli
        //     temp.push(data_penerimaan[i][13]);//11 tanggal transaksi
        //     temp.push(data_penerimaan[i][14]);//12 nosat
        //     alokasibrg.push(temp);
            // $.ajax({
            //   url     : "{!! url('getBatchBarang') !!}",
            //   type    : "POST",
            //   async   : false,
            //   data    : {
            //     _token : _token,
            //     kode_barang : data_penerimaan[i][1],
            //     gudang : gudang
            //   },
            //   success : function(result) {
            //     for (var j = 0; j < result.length; j++) {
            //       var dummy = [];
            //       var qty = result[j].qty / parseFloat(data_penerimaan[i][4]);
            //       if (qty > 0) {
            //         dummy.push(result[j].no_batch);
            //         dummy.push(result[j].kode_barang);
            //         dummy.push(result[j].tanggal);
            //         dummy.push(numberWithCommas(round(qty, 1)));
            //         dummy.push(numberWithCommas(0));
            //         dummy.push(i);
            //         dummy.push(parseFloat(data_penerimaan[i][4]));
            //         batch.push(dummy);
            //       }
            //     }
            //   }
            // });
          // }
        // }
        data_penerimaan = [];
        $("#no_penerimaan").val("");
        // $("#addAlokasiBrg").modal("toggle");
        add();
      // }
    }
  }

  function Addscan() {
    if (data_scanbarcode.length > 0) {
      var count = 0;
      var siap = parseInt($("#qtyalokasi").val());
      var penerimaan = parseInt($("#qtypenerimaan").val());
      var barcode = "";
      var gudang = "";
      var rak = "";
      var lokasi = "";
      for (var i = 0; i < data_scanbarcode.length; i++) {
        count = count + data_scanbarcode[i][6];
        if (data_scanbarcode[i][6]==1){
          siap = siap + parseInt(data_scanbarcode[i][5]);
        }
        penerimaan = parseInt(data_scanbarcode[i][3]);
      }
      if (siap > penerimaan) {
        alertify.warning("Alokasi lebih besar dari Penerimaan!");
        return;
      }
      if (count > 0) {
        $("#scanbarcode").modal('toggle');
        $("#pilihpenerimaan").css({ opacity: 1 });
        batch = [];
        // if (alokasibrg.length > 0){
        for (var j = 0; j < data_scanbarcode.length; j++) {
          if (data_scanbarcode[j][18] == "U") {
            for (var x = 0; x < alokasibrg.length; x++) {
              if (data_scanbarcode[j][1] == alokasibrg[x][1]) {
                if (data_scanbarcode[j][1] == alokasibrg[x][1] && data_scanbarcode[j][15] == alokasibrg[x][13]) {
                  alokasibrg[x][0]=data_scanbarcode[j][0];//0 nobeli
                  alokasibrg[x][1]=data_scanbarcode[j][1];//1 kdbrg
                  alokasibrg[x][2]=data_scanbarcode[j][2];//2 nmbrg
                  alokasibrg[x][3]=data_scanbarcode[j][3];//3 qntsaldo
                  alokasibrg[x][4]=data_scanbarcode[j][4];//4 qntambil
                  alokasibrg[x][5]=data_scanbarcode[j][5];//5 qnt
                  alokasibrg[x][6]=data_scanbarcode[j][8];//6 kdgdg
                  alokasibrg[x][7]=data_scanbarcode[j][9];//7 nmgdg
                  alokasibrg[x][8]=data_scanbarcode[j][10];//8 satuan
                  alokasibrg[x][9]=data_scanbarcode[j][11];//9 isi
                  alokasibrg[x][10]=data_scanbarcode[j][12];//10 urutbeli
                  alokasibrg[x][11]=data_scanbarcode[j][13];//11 tanggal transaksi
                  alokasibrg[x][12]=data_scanbarcode[j][14];//12 nosat
                  alokasibrg[x][13]=data_scanbarcode[j][15];//13 barcode
                  alokasibrg[x][14]=data_scanbarcode[j][16];//14 koderak
                  alokasibrg[x][15]=data_scanbarcode[j][17];//15 kodelokasi
                  alokasibrg[x][16]=data_scanbarcode[j][6];//16 chackbox
                  alokasibrg[x][17]=data_scanbarcode[j][7];//17 SisaOut
                  alokasibrg[x][18]=data_scanbarcode[j][18];//18 choice
                }
              }
            }
          }else{
            temp = [];
            temp.push(data_scanbarcode[j][0]);//0 nobeli
            temp.push(data_scanbarcode[j][1]);//1 kdbrg
            temp.push(data_scanbarcode[j][2]);//2 nmbrg
            temp.push(data_scanbarcode[j][3]);//3 qntsaldo
            temp.push(data_scanbarcode[j][4]);//4 qntambil
            temp.push(data_scanbarcode[j][5]);//5 qnt
            temp.push(data_scanbarcode[j][8]);//6 kdgdg
            temp.push(data_scanbarcode[j][9]);//7 nmgdg
            temp.push(data_scanbarcode[j][10]);//8 satuan
            temp.push(data_scanbarcode[j][11]);//9 isi
            temp.push(data_scanbarcode[j][12]);//10 urutbeli
            temp.push(data_scanbarcode[j][13]);//11 tanggal transaksi
            temp.push(data_scanbarcode[j][14]);//12 nosat
            temp.push(data_scanbarcode[j][15]);//13 barcode
            temp.push(data_scanbarcode[j][16]);//14 koderak
            temp.push(data_scanbarcode[j][17]);//15 kodelokasi
            temp.push(data_scanbarcode[j][6]);//16 chackbox
            temp.push(data_scanbarcode[j][7]);//17 SisaOut
            temp.push(data_scanbarcode[j][18]);//18 choice
            alokasibrg.push(temp);
          }
        }
        for (var i = 0; i < data_penerimaan.length; i++) {
          if (data_penerimaan[i][1] == $("#kodebrg").val()) {
            $("#"+ i +"-QntOut").html(numberWithCommas(siap));
            $("#"+ i +"-QntSisa").html(numberWithCommas(penerimaan-siap));
          }
        }
        data_scanbarcode = [];
        resetscan();
      }else{
        alertify.warning("Tidak ada Alokasi yang di pilih!");
      }
    }
  }

  function loadalokasibrg(name = "") {
    $('#'+name+'detail_alokasibrg > tr').each(function() {
      $(this).css('background-color', '');
    });
    g_iddet = "";
    str = "";
    if (name == "") {
      if (alokasibrg.length > 0) {
        for (var i = 0; i < alokasibrg.length; i++) {
          str += "<tr id='" + i + "-trdet' onclick='selectdet(" + i + ")'>\
            <td style='width: 10%;'>" + alokasibrg[i][0] + '</td>\
            <td style="width: 10%;">' + alokasibrg[i][1] + '</td>\
            <td style="width: 35%;">' + alokasibrg[i][2] + '</td>\
            <td style="width: 10%; text-align: right;">' + numberWithCommas(parseInt(alokasibrg[i][3])) + '</td>\
            <td style="width: 10%; text-align: right;"><input type="text" id="'+i+'-qtypakai" class="format-number text-right" onkeyup="updateBarang('+i+')" value="'+ parseFloat(alokasibrg[i][5]) +'"></td>\
            <td style="width: 10%;">' + alokasibrg[i][8] + '</td>\
            <td style="width: 10%; text-align: right;">' + numberWithCommas(parseInt(alokasibrg[i][9])) + '</td></tr>';
        }
        $("#detail_alokasibrg").html(str);
        $(".format-number").autoNumeric('init', {mDec: '2'});
      }
      else {
        $("#detail_alokasibrg").html('<tr><td colspan="7">Tidak ada barang.</td></tr>');
      }
    } else {
      if (ealokasibrg.length > 0) {
        for (var i = 0; i < ealokasibrg.length; i++) {
          str += "<tr id='e" + i + "-trdet' onclick='selectdet(" + i + ", \"e\")'>\
            <td style='width: 10%; '>" + ealokasibrg[i][0] + '</td>\
            <td style="width: 10%; ">' + ealokasibrg[i][1] + '</td>\
            <td style="width: 40%;">' + ealokasibrg[i][2] + '</td>\
            <td style="width: 10%; text-align: right;">' + numberWithCommas(parseInt(ealokasibrg[i][3])) + '</td>\
            <td style="width: 10%; text-align: right;"><input type="text" id="e'+i+'-qtypakai" class="format-number text-right" onkeyup="updateBarang('+i+', \'e\')" value="'+ parseFloat(ealokasibrg[i][6]) +'"></td>\
            <td style="width: 10%; ">' + ealokasibrg[i][4] + '</td>\
            <td style="width: 10%; text-align: right;">' + numberWithCommas(parseInt(ealokasibrg[i][5])) + '</td></tr>';
        }
        $("#edetail_alokasibrg").html(str);
        $(".format-number").autoNumeric('init', {mDec: '2'});
      }
      else {
        $("#edetail_alokasibrg").html('<tr><td colspan="7">Tidak ada barang.</td></tr>');
      }
    }
    calculateBatch(name);
  }

  function updateBarang(index, name = "") {
    if (name == "") {
      if (alokasibrg[index][5] < parseFloat($("#"+index+"-qtykirim").val())) {
        $("#"+index+"-qtykirim").val(alokasibrg[index][5]);
        barang[index][2] = numberWithCommas(barang[index][5]);
      }
      else {
        barang[index][2] = $("#"+index+"-qtykirim").val();
      }
    } else {
      if (ealokasibrg[index][6] < parseFloat($("#e"+index+"-qtykirim").val())) {
        $("#e"+index+"-qtykirim").val(ealokasibrg[index][6]);
        ealokasibrg[index][3] = ealokasibrg[index][6];
      }
      else {
        ealokasibrg[index][3] = $("#e"+index+"-qtykirim").val();
      }
    }
  }

  function showBatch() {
    if (g_iddet === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    var index = g_iddet;
    loadBatch(index);
    $("#index_retur").val(index);
    $("#addBatch").modal('toggle');
  }

  function eshowBatch() {
    if (g_iddet === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    var index = g_iddet;
    loadBatch(index, 'e');
    $("#eindex_retur").val(index);
    $("#editBatch").modal('toggle');
  }

  function loadBatch(index, name = "") {
    str = "";
    if (name == "") {
      if (batch.length > 0) {
          var count = 0;
          for (var i = 0; i < batch.length; i++) {
            if (batch[i][5] == index) {
              str += "<tr><td>" + batch[i][1] + "</td>\
                <td>" + batch[i][0] + "</td>\
                <td>" + format_date(batch[i][2]) + "</td>\
                <td>" + batch[i][3] + "</td>\
                <td><input type='text' class='format-number' value='" + batch[i][4] + "' id='"+i+"-inputbatch' onkeyup='editReturBatch("+i+")'></td>\
                </tr>";
              count += 1;
            }
          }
          if (count == 0) {
            $("#data_batch").html("<tr><td colspan='5'>Tidak ada batch.</td></tr>");
          } else {
            $("#data_batch").html(str);
          }
      } else {
        $("#data_batch").html("<tr><td colspan='5'>Tidak ada batch.</td></tr>");
      }
    }
    else {
      if (ebatch.length > 0) {
          var count = 0;
          for (var i = 0; i < ebatch.length; i++) {
            if (ebatch[i][5] == index) {
              str += "<tr><td>" + ebatch[i][1] + "</td>\
                <td>" + ebatch[i][0] + "</td>\
                <td>" + format_date(ebatch[i][2]) + "</td>\
                <td>" + ebatch[i][3] + "</td>\
                <td><input type='text' class='format-number' value='" + ebatch[i][4] + "' id='e"+i+"-inputbatch' onkeyup='editReturBatch("+i+", \"e\")'></td>\
                </tr>";
              count += 1;
            }
          }
          if (count == 0) {
            $("#edata_batch").html("<tr><td colspan='5'>Tidak ada batch.</td></tr>");
          } else {
            $("#edata_batch").html(str);
          }
      } else {
        $("#edata_batch").html("<tr><td colspan='5'>Tidak ada batch.</td></tr>");
      }
    }
    $(".format-number").autoNumeric('init', {mDec: '2'});
  }

  function editReturBatch(index_batch, name = "") {
    var input = $("#" + name + index_batch + "-inputbatch").val();
    if (input != "") {
      if (name == "") {
        batch[index_batch][4] = input;
      }
      else {
        ebatch[index_batch][4] = input;
      }
    }
    else {
      if (name == "") {
        batch[index_batch][4] = numberWithCommas(0);
      }
      else {
        ebatch[index_batch][4] = numberWithCommas(0);
      }
    }
    calculateBatch(name);
  }

  function calculateBatch(name = "") {
    if (name == "") {
      temp = [];
      for (var i = 0; i < alokasibrg.length; i++) {
        temp.push(0);
      }
      for (var i = 0; i < batch.length; i++) {
        temp[batch[i][5]] += parseInt(batch[i][4].replace(/,/g, ""));
      }
      for (var i = 0; i < temp.length; i++) {
        $("#"+name+i+"-jmlbatch").html(numberWithCommas(temp[i]));
      }
    } else {
      temp = [];
      for (var i = 0; i < ealokasibrg.length; i++) {
        temp.push(0);
      }
      for (var i = 0; i < ebatch.length; i++) {
        temp[ebatch[i][5]] += parseInt(ebatch[i][4].replace(/,/g, ""));
      }
      for (var i = 0; i < temp.length; i++) {
        $("#"+name+i+"-jmlbatch").html(numberWithCommas(temp[i]));
      }
    }
  }

  function cekBatch(name = "") {
    var check = 0;
    if (name == "") {
      if (batch.length != 0) {
        var temp = [], temp2 = [], count_b = [], join_retur = [];
        for (var i = 0; i < alokasibrg.length; i++) {
          temp.push(0);
          count_b.push(0);
          temp2.push(parseFloat(alokasibrg[i][2].replace(/,/g, "")));
        }
        for (var i = 0; i < batch.length; i++) {
          temp[batch[i][5]] += parseFloat(batch[i][4].replace(/,/g, ""));
          count_b[batch[i][5]] += 1;
        }
        for (var i = 0; i < temp.length; i++) {
          if (temp[i] != temp2[i] && count_b[i] > 0) {
            return '0&'+alokasibrg[i][0];
          }
        }
        for (var i = 0; i < batch.length; i++) {
          var cek_kembar = 0;
          for (var j = 0; j < join_retur.length; j++) {
            if (batch[i][0] == join_retur[j][0] && batch[i][1] == join_retur[j][1] && batch[i][2] == join_retur[j][2]) {
              join_retur[j][4] += parseFloat(batch[i][4].replace(/,/g, ""));
              cek_kembar = 1;
            }
          }
          if (cek_kembar == 0) {
            var dum = [];
            dum.push(batch[i][0]);
            dum.push(batch[i][1]);
            dum.push(batch[i][2]);
            dum.push(parseFloat(batch[i][3].replace(/,/g, "")));
            dum.push(parseFloat(batch[i][4].replace(/,/g, "")));
            join_retur.push(dum);
          }
        }
        for (var i = 0; i < join_retur.length; i++) {
          if (join_retur[i][3] < join_retur[i][4]) {
            return '2&'+join_retur[i][0]+'&'+join_retur[i][1]+'&'+join_retur[i][2];
          }
        }
        return '1';
      }
      else {
        return '1';
      }
    }
    else {
      if (ebatch.length != 0) {
        var temp = [], temp2 = [], count_b = [], join_retur = [];
        for (var i = 0; i < ealokasibrg.length; i++) {
          temp.push(0);
          count_b.push(0);
          temp2.push(parseFloat(ealokasibrg[i][2].replace(/,/g, "")));
        }
        for (var i = 0; i < ebatch.length; i++) {
          temp[ebatch[i][5]] += parseFloat(ebatch[i][4].replace(/,/g, ""));
          count_b[ebatch[i][5]] += 1;
        }
        for (var i = 0; i < temp.length; i++) {
          if (temp[i] != temp2[i] && count_b[i] > 0) {
            return '0&'+ealokasibrg[i][0];
          }
        }
        for (var i = 0; i < ebatch.length; i++) {
          var cek_kembar = 0;
          for (var j = 0; j < join_retur.length; j++) {
            if (ebatch[i][0] == join_retur[j][0] && ebatch[i][1] == join_retur[j][1] && ebatch[i][2] == join_retur[j][2]) {
              join_retur[j][4] += parseFloat(ebatch[i][4].replace(/,/g, ""));
              cek_kembar = 1;
            }
          }
          if (cek_kembar == 0) {
            var dum = [];
            dum.push(ebatch[i][0]);
            dum.push(ebatch[i][1]);
            dum.push(ebatch[i][2]);
            dum.push(parseFloat(ebatch[i][3].replace(/,/g, "")));
            dum.push(parseFloat(ebatch[i][4].replace(/,/g, "")));
            join_retur.push(dum);
          }
        }
        for (var i = 0; i < join_retur.length; i++) {
          if (join_retur[i][3] < join_retur[i][4]) {
            return '2&'+join_retur[i][0]+'&'+join_retur[i][1]+'&'+join_retur[i][2];
          }
        }
        return '1';
      }
      else {
        return '1';
      }
    }
  }

  function removeCommas(name = "") {
    if (name == "") {
      for (var i = 0; i < alokasibrg.length; i++) {
        alokasibrg[i][3] = parseFloat(alokasibrg[i][3]);
        alokasibrg[i][4] = parseFloat(alokasibrg[i][4]);
        alokasibrg[i][5] = parseFloat(alokasibrg[i][5]);
        alokasibrg[i][9] = parseFloat(alokasibrg[i][9]);
      }
      for (var i = 0; i < batch.length; i++) {
        batch[i][4] = parseFloat(batch[i][4]);
      }
    } else {
      for (var i = 0; i < ealokasibrg.length; i++) {
        ealokasibrg[i][3] = parseFloat(ealokasibrg[i][3]);
        ealokasibrg[i][4] = parseFloat(ealokasibrg[i][4]);
        ealokasibrg[i][5] = parseFloat(ealokasibrg[i][5]);
        ealokasibrg[i][9] = parseFloat(ealokasibrg[i][9]);
      }
      for (var i = 0; i < ebatch.length; i++) {
        ebatch[i][4] = parseFloat(ebatch[i][4]);
      }
    }
  }

  function returnCommas(name = "") {
    if (name == "") {
      for (var i = 0; i < alokasibrg.length; i++) {
        alokasibrg[i][3] = numberWithCommas(alokasibrg[i][3]);
        alokasibrg[i][4] = numberWithCommas(alokasibrg[i][4]);
        alokasibrg[i][5] = numberWithCommas(alokasibrg[i][5]);
        alokasibrg[i][9] = numberWithCommas(alokasibrg[i][9]);
      }
      for (var i = 0; i < batch.length; i++) {
        batch[i][4] = numberWithCommas(batch[i][4]);
      }
    } else {
      for (var i = 0; i < ealokasibrg.length; i++) {
        ealokasibrg[i][3] = numberWithCommas(ealokasibrg[i][3]);
        ealokasibrg[i][4] = numberWithCommas(ealokasibrg[i][4]);
        ealokasibrg[i][5] = numberWithCommas(ealokasibrg[i][5]);
        ealokasibrg[i][9] = numberWithCommas(ealokasibrg[i][9]);
      }
      for (var i = 0; i < ebatch.length; i++) {
        ebatch[i][4] = numberWithCommas(ebatch[i][4]);
      }
    }
  }

  function reset(name = "") {
    alokasibrg = []; batch = [], data_penerimaan = [], data_scanbarcode = [];
    xbarcode = "", xkodegdg = "", xkoderak = "", xkodelokasi = "", xlokasi = "", xkodebrg = "";
    $("#penerimaan_detail").html("");
    $("#scanbarcode_detail").html("");
    $("#kodebrg").val("");
    $("#kodegdg").val("");
    $("#koderak").val("");
    $("#kodelokasi").val("");
    restrictedDate();
    if (name == "e") release(g_nb);
  }

  function resetscan(name = "") {
    data_scanbarcode = [];
    xbarcode = "", xkodegdg = "", xkoderak = "", xkodelokasi = "", xkodebrg = "", xlokasi = "";
    $("#pilihpenerimaan").css({ opacity: 1 });
    $("#scanbarcode_detail").html("");
    $("#lbarcode").html("Scan Barang");
    $("#kodebrg").val("");
    $("#namabrg").val("");
    $("#qtypenerimaan").val("");
    $("#qtyalokasi").val("");
    // document.getElementById("cbbarang").checked = false;
    // document.getElementById("cblokasi").checked = false;
  }

  // start add
  function add() {
    var _token = $("#_token").val();
    var _no_urut = $("#no_urut").val();
    var _no_bukti = $("#no_bukti").val();
    var _tanggal = $("#tanggal").val();
    var _no_penerimaan = $("#no_penerimaan").val();
    var _barcode = xbarcode;
    var _gudang = $("#gudang").val();
    var _rak = xkoderak;
    var _lokasi = xkodelokasi;
    var _keterangan = $("#keterangan").val();
    var _choice = "I";
    var cek = cekBatch();
    if (_no_urut != "" && _no_bukti != "" && _tanggal != "" &&
      // _gudang != "" && _rak != "" && _lokasi != "" && _barcode != "" &&
      alokasibrg.length > 0
      // && cek == '1' && _no_penerimaan != ""
        ) {
      removeCommas();
      $.ajax({
        url     : "{!! url('addAlokasiBrg') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          choice : _choice,
          no_urut : _no_urut,
          no_bukti : _no_bukti,
          tanggal : _tanggal,
          no_penerimaan : _no_penerimaan,
          gudang : _gudang,
          rak : _rak,
          lokasi : _lokasi,
          barcode : _barcode,
          keterangan : _keterangan,
          alokasibrg : alokasibrg,
          batch : batch
        },
        success : function(result) {
          if (result.split(";;")[0] == "1") {
            reset(); loadAll();
            // showPilih();
            select(0, result.split(";;")[2],result.split(";;")[1],result.split(";;")[3]);
            // $("#addAlokasiBrg").modal("toggle");
            alertify.success('Alokasi Barang telah ditambahkan.');
          } else if (result.split(";;")[0] == "0") {
            returnCommas();
            $("#no_urut").val(generateNoUrut());
            $("#no_bukti").val(generateNoBukti());
            _no_bukti = $("#no_bukti").val();
            alertify.alert('Nomor Bukti sudah dipakai. NoBukti diganti menjadi '+_no_bukti+'.', function(){ });
          } else {
            returnCommas();
            alertify.warning('Barang '+result+' melebihi batas kuantitas Outstanding Transaksi.');
          }
        }
      });
    } else {
      if (cek != '1') {
        if (cek.split("&")[0] == '0') {
          alertify.alert('Gagal menambahkan Alokasi barang!', 'Jumlah batch kode barang '+cek.split("&")[1]+' tidak sama.', function(){ });
        } else {
          alertify.alert('Gagal menambahkan Alokasi barang!', 'Jumlah batch kode barang '+cek.split("&")[2]+' pada no. batch '+cek.split("&")[1]+' dengan expired date '+cek.split("&")[3]+' melebihi jumlah qty batch.', function(){ });
        }
      } else {
        alertify.alert('Gagal menambahkan Alokasi barang!', 'Semua kolom harus terisi.', function(){ });
      }
    }
  }
  // end add

  function changeAuth(id, no_bukti) {
    alertify.confirm('Otorisasi Alokasi Barang', 'Apakah yakin ingin mengotorisasi Alokasi Barang dengan nomor bukti ' + no_bukti + ' ?',
      function(){
        var _token = $("#_token").val(); var err = 0;
        $.ajax({
          url     : "{!! url('showDetAlokasiBrg') !!}",
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
          url     : "{!! url('authAlokasiBrg') !!}",
          type    : "POST",
          async   : false,
          data    : {
            _token : _token,
            nobukti : no_bukti
          },
          success : function(result) {
            loadAll();
            alertify.success('Alokasi Barang dengan nomor bukti '+no_bukti+' telah diotorisasi.');
          }
        });
    },function(){
      $("#"+id+"-auth").prop('checked', false);
    });
  }

  function changeBatal(id, no_bukti) {
    alertify.confirm('Pembatalan Alokasi Barang', 'Apakah yakin ingin membatalkan Alokasi Barang dengan nomor bukti ' + no_bukti + ' ?',
      function(){
        var _token = $("#_token").val();
        $.ajax({
          url     : "{!! url('batalAlokasiBrg') !!}",
          type    : "POST",
          async   : false,
          data    : {
            _token : _token,
            nobukti : no_bukti
          },
          success : function(result) {
            loadAll();
            alertify.success('Alokasi Barang dengan nomor bukti '+no_bukti+' telah dibatalkan.');
          }
        });
    },function(){
      $("#"+id+"-batal").prop('checked', false);
    });
  }

  // start tampilkan detail stockbrg
  function showDetailStk(_nobukti) {
    var no_bukti = _nobukti;
    var _tipe = "0";
    temp = [];
    var isi = [];
    var _token = $("#_token").val();
    $.ajax({
      url     : "{!! url('showDetailBrgLoc') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        id : no_bukti,
        tipe : _tipe
      },
      success : function(result) {
        str = "";
        $("#detdetail_stkbrg").html("");
        for (var i = 0; i < result.length; i++) {
          str = str + "<tr id='det" + i + "-trdet' onclick='selectdet(" + i + ", \"det\")'>\
            <td>" + result[i].Kodebrg + "</td>\
            <td>" + result[i].namabrg + "</td>\
            <td>" + result[i].Kodegdg + "</td>\
            <td>" + result[i].Barcode + "</td>";
            if (result[i].SisaOut == 0){
              str = str + "<td class='text-right'>" + numberWithCommas(0) + "</td>";
            }else{
              str = str + "<td class='text-right'>" + numberWithCommas(parseInt(result[i].SisaOut)) + "</td>";
            }
            if (result[i].QntOut == 0){
              str = str + "<td class='text-right'>" + numberWithCommas(0) + "</td>";
            }else{
              str = str + "<td class='text-right'>" + numberWithCommas(parseInt(result[i].QntOut)) + "</td>";
            }
          temp.push(0);
        }
        $("#detdetail_stkbrg").html(str);
      }
    });
    $("#detailStockBrg").modal('toggle');
  }
  // end tampilkan detail stockbrg

  // start tampilkan detail
  function showDetail(_nobukti) {
    var no_bukti = "";
    temp = [];
    var isi = [];
    detbatch = [];
    var date = "";
    var _token = $("#_token").val();
    $.ajax({
      url     : "{!! url('showAlokasiBrg') !!}",
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
      url     : "{!! url('showDetAlokasiBrg') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        id : no_bukti
      },
      success : function(result) {
        str = "";
        $("#detdetail_alokasibrg").html("");
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
        $("#detdetail_alokasibrg").html(str);
      }
    });
    $("#detailAlokasiBrg").modal('toggle');
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
    var id_penerimaan = 0;
    var no_bukti = g_nb;
    var _token = $("#_token").val();
    var isi = [];
    ebatch = [];
    $.ajax({
      url     : "{!! url('showAlokasiBrg') !!}",
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
        // $("#eid_penerimaan").val(result.id_penerimaan);
        // id_penerimaan = result.id_penerimaan;
        no_bukti = result.NOBUKTI;
      }
    });
    if (cekauth == 1) {
      alertify.warning("Transaksi sudah diotorisasi");
      return;
    }
    var checkhold = checkHold(g_nb);
    if (checkhold) {
      alertify.warning("Transaksi sedang diedit.");
      return;
    }
    hold(g_nb);
    $.ajax({
      url     : "{!! url('showDetAlokasiBrg') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        id : g_nb
      },
      success : function(result) {
        ealokasibrg = [];
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
          ealokasibrg.push(temp);
          isi.push(result[i].ISI);
        }
      }
    });
    $.ajax({
      url     : "{!! url('showDetOuttransaksiAlokasiBrg') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        id : g_nb
      },
      success : function(result) {
        for (var i = 0; i < ealokasibrg.length; i++) {
          for (var j = 0; j < result.length; j++) {
            if (ealokasibrg[i][0] == result[j].NoBeli && ealokasibrg[i][1] == result[j].KODEBRG && ealokasibrg[i][9] == result[j].UrutBeli) {
              ealokasibrg[i][6] += result[j].QNTSISA;
              break;
            }
          }
        }
      }
    });
    // for (var a = 0; a < ealokasibrg.length; a++) {
    //   $.ajax({
    //     url     : "{!! url('getBatchBarang') !!}",
    //     type    : "POST",
    //     async   : false,
    //     data    : {
    //       _token : _token,
    //       kode_barang : ealokasibrg[a][0],
    //       gudang : gudang
    //     },
    //     success : function(result) {
    //       for (var i = 0; i < result.length; i++) {
    //         var dummy = [];
    //         var qty = result[i].qty / parseInt(ealokasibrg[a][4].replace(/,/g, ""));
    //         dummy.push(result[i].no_batch);
    //         dummy.push(result[i].kode_barang);
    //         dummy.push(result[i].tanggal);
    //         dummy.push(numberWithCommas(round(qty, 1)));
    //         dummy.push(numberWithCommas(0));
    //         dummy.push(a);
    //         dummy.push(ealokasibrg[a][4].replace(/,/g, ""));
    //         ebatch.push(dummy);
    //       }
    //     }
    //   });
    // }
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
    //       for (var j = 0; j < ebatch.length; j++) {
    //         if (result[i].no_batch == ebatch[j][0] && result[i].kode_barang == ebatch[j][1] && result[i].tanggal == ebatch[j][2]) {
    //           ebatch[j][3] = numberWithCommas(parseFloat(ebatch[j][3].replace(/,/g, "")) + round(result[i].qty / isi[result[i].urut] * -1, 1));
    //           ebatch[j][4] = numberWithCommas(parseFloat(ebatch[j][4].replace(/,/g, "")) + round(result[i].qty / isi[result[i].urut] * -1, 1));
    //           break;
    //         }
    //       }
    //     }
    //   }
    // });
    // for (var i = 0; i < ebatch.length; i++) {
    //   if (ebatch[i][3] <= 0) {
    //     ebatch.splice(i, 1);
    //     i--;
    //   }
    // }
    loadalokasibrg('e');
    $("#editAlokasiBrg").modal('toggle');
  }
  // end tampilkan invoice

  // start edit invoice
  function edit() {
    var _token = $("#_token").val();
    var _id = $("#eid").val();
    var _no_urut = $("#eno_urut").val();
    var _no_bukti = $("#eno_bukti").val();
    var _tanggal = $("#etanggal").val();
    var _id_penerimaan = $("#eno_penerimaan").val();
    var _gudang = $("#egudang").val();
    var _rak = $("#erak").val();
    var _lokasi = $("#elokasi").val();
    var _barcode = $("#ebarcode").val();
    var _keterangan = $("#eketerangan").val();
    var cek = cekBatch('e');
    if (_id != "" && _no_urut != "" && _no_bukti != "" && _tanggal != "" &&
        _gudang != "" && _rak != "" &&  _lokasi != "" &&  _barcode != "" &&
      ealokasibrg.length > 0
      // && cek == '1' && _id_penerimaan != ""
    ) {
      removeCommas('e');
      $.ajax({
        url     : "{!! url('addAlokasiBrg') !!}",
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
          id_penerimaan : _id_penerimaan,
          keterangan : _keterangan,
          alokasibrg : ealokasibrg,
          batch : ebatch
        },
        success : function(result) {
          if (result == 1) {
            loadAll(); select(_id, _no_bukti, _no_urut,'');
            $("#editAlokasiBrg").modal('toggle');
            alertify.success('Data Alokasi Barang telah diubah.');
          } else {
            returnCommas('e');
            alertify.warning('Barang '+result+' melebihi batas kuantitas Outstanding Transaksi Alokasi.');
          }
        }
      });
    }
    else {
      if (cek != '1') {
        if (cek.split("&")[0] == '0') {
          alertify.alert('Gagal mengubah Alokasi barang!', 'Jumlah batch kode barang '+cek.split("&")[1]+' tidak sama.', function(){ });
        } else {
          alertify.alert('Gagal mengubah Alokasi barang!', 'Jumlah batch kode barang '+cek.split("&")[2]+' pada no. batch '+cek.split("&")[1]+' dengan expired date '+cek.split("&")[3]+' melebihi jumlah qty batch.', function(){ });
        }
      } else {
        alertify.alert('Gagal mengubah Alokasi barang!', 'Semua kolom harus terisi.', function(){ });
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
      url     : "{!! url('showAlokasiBrg') !!}",
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
    alertify.confirm('Hapus Alokasi Barang', 'Apakah yakin ingin menghapus Alokasi Barang dengan nomor bukti ' + g_nb + ' dan Kode Barang ' + g_kd + ' ?',
      function(){
        var _token = $("#_token").val();
        $.ajax({
          url     : "{!! url('eraseAlokasiBrg') !!}",
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
              alertify.success('Data Alokasi Barang telah dihapus.');
            } else {
              alertify.warning('Data Alokasi Barang gagal telah dihapus.');
            }
          }
        });
    },function(){});
  }
  // end hapus invoice
</script>
@endsection
