@extends('master')

@section('css')
<style>
  .modal-lg {
    min-width: 98%;
  }
</style>
@endsection

@section('breadcrumb')
<li class="nav-item">
  <a class="nav-link" href="#" data-toggle="tooltip" data-placement="bottom" title="Gudang / Opname Barang"><span class="blue" id="title_page">Opname Barang</span></a>
</li>
@endsection

@section('button-add-refresh')
<button type="button" class="btn btn-primary btn-sm btn-top" onclick="loadAll()" rel="tooltip" data-placement="bottom" title="Refresh" style="width: 30px;"><i class="fas fa-sync-alt"></i></button>&nbsp;&nbsp;
@if ($akses->tambah == 1)
  <button class="btn btn-success btn-sm btn-top" type="button" id="tambah" onclick="showPilih()" rel="tooltip" data-placement="bottom" title="Tambah" style="width: 30px;"><i class="fas fa-plus-circle"></i></button>&nbsp;&nbsp;
@endif
@if ($akses->koreksi == 1)
  <button type="button" class="btn btn-warning btn-sm" id="koreksi" onclick="show()" rel="tooltip" data-placement="bottom" title="Koreksi" style="width: 30px;"><i class="fas fa-pencil-alt"></i></button>&nbsp;&nbsp;
@endif
@if ($akses->hapus == 1)
  <button type="button" class="btn btn-danger btn-sm" onclick="erase()" rel="tooltip" data-placement="bottom" title="Hapus" style="width: 30px;"><i class="fas fa-trash"></i></button>
@endif
@endsection

@section('content')
<div class="container-fluid">
  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <div class="card">
    <div class="card-header" style="background: palegoldenrod;">
      <div class="row">
        <nav style="width: 100%;">
          <div class="nav nav-pills col-12" id="nav-tab" role="tablist">
            <!-- <a class="nav-item nav-link " id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="false">Outstanding Penerimaan</a> -->
            <a class="nav-item nav-link active" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="true">Opname Barang</a>
          </div>
        </nav>
      </div>
    </div>
    <div class="card-body" style="background: palegoldenrod;">
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade " id="home" role="tabpanel" aria-labelledby="home-tab">
          <div class="card-body" style="background: palegoldenrod;">
            <div class="row">
              <div class="col-12" style="padding-left: 20px; padding-right: 20px; overflow:auto;">
                <table class="table table-bordered table-striped" id="tabel_penerimaan" style="background:white;">
                  <thead>
                    <tr>
                      <th width="10%">No. Penerimaan</th>
                      <th width="10%">Kode Barang</th>
                      <th width="45%">Nama Barang</th>
                      <th width="5%">Gudang</th>
                      <th width="10%">Qty Penerimaan</th>
                      <th width="10%">Qty Alokasi</th>
                      <th width="10%">Qty Sisa</th>
                    </tr>
                  </thead>
                  <tbody id="penerimaan_data">
                    @if (count($penerimaan) > 0)
                      @for ($i = 0; $i < count($penerimaan); $i++)
                        <tr id="{!! $i !!}-penerimaan" onclick="select2({!! $i !!},'{!! $penerimaan[$i]->NoBukti1 !!}','{!! $penerimaan[$i]->Kodebrg !!}','{!! $penerimaan[$i]->QntSaldo !!}','{!! $penerimaan[$i]->QNTAMBIL !!}','{!! $penerimaan[$i]->QNTSISA !!}','{!! $penerimaan[$i]->Kodegdg !!}','{!! $penerimaan[$i]->NAMAGUDANG !!}')">
                          <td>{!! $penerimaan[$i]->NoBukti1 !!}</td>
                          <td>{!! $penerimaan[$i]->Kodebrg !!}</td>
                          <td>{!! $penerimaan[$i]->NAMABRG !!}</td>
                          <td>{!! $penerimaan[$i]->Kodegdg !!}</td>
                          <td class="text-right">{!! number_format($penerimaan[$i]->QntSaldo) !!}</td>
                          <td class="text-right">{!! number_format($penerimaan[$i]->QNTAMBIL) !!}</td>
                          <td class="text-right">{!! number_format($penerimaan[$i]->QNTSISA) !!}</td>
                        </tr>
                      @endfor
                    @else
                      <tr>
                        <td colspan="7">Tidak ada data penerimaan ditemukan.</td>
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
        <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
          <div class="row">
            <div class="col-12" style="padding-left: 20px; padding-right: 20px; overflow:auto;">
              <table class="table table-bordered table-striped" id="tabel_opnamebrg" style="background:white;">
                <thead>
                  <tr>
                    <th width="10px"></th>
                    <th width="10%">No. Bukti</th>
                    <th width="7%">Tanggal</th>
                    <th width="7%">Lokasi</th>
                    <th>Keterangan</th>
                  </tr>
                </thead>
                <tbody id="opnamebrg_data">
                  @if (count($opnamebrg) > 0)
                    @for ($i = 0; $i < count($opnamebrg); $i++)
                      <tr id="{!! $i !!}-tr" onclick="select({!! $i !!}, '{!! $opnamebrg[$i]->NOBUKTI !!}',{!! $opnamebrg[$i]->NOURUT !!},'{!! $opnamebrg[$i]->Barcode !!}')">
                        <td><button type="button" class="btn btn-secondary btn-sm btn-opsi" onclick="showDetail('{!! $opnamebrg[$i]->NOBUKTI !!}')"><i class="fas fa-info-circle"></i></button></td>
                        <td>{!! $opnamebrg[$i]->NOBUKTI !!}</td>
                        <td>{!! date("d/m/Y", strtotime($opnamebrg[$i]->TANGGAL)) !!}</td>
                        <td>{!! $opnamebrg[$i]->Barcode !!}</td>
                        <td>{!! $opnamebrg[$i]->NOTE !!}</td>
                      </tr>
                    @endfor
                  @else
                    <tr>
                      <td colspan="5">Tidak ada Opname Barang.</td>
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

<!-- start modal pilih barang -->
<div class="modal fade" id="pilihpenerimaan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding-bottom: 5px; padding-top: 5px;">
        <h5 class="modal-title" id="exampleModalLabel">Pilih Barang</h5>
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
              <label id='lkodebrg' for="kodebrg">Lokasi</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <input type="text" class="form-control" id="kodebrg" onkeypress="showPenerimaan(event)" autofocus>
            </div>
          </div>
          <div class="col-1">
          </div>
          <div class="col-1">
            <div class="form-group text-right">
              <label for="barcodel">Barcode Lokasi</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <input type="text" class="form-control" id="barcodel" disabled>
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
        <input type="hidden" id="kodegdg">
        <input type="hidden" id="koderak">
        <input type="hidden" id="kodelokasi">
        <hr style="margin-top: 7px; margin-bottom: 7px;" />
        <div class="row">
          <div class="col-12">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Kode Barang</th>
                  <th>Nama Barang</th>
                  <th>Qty Computer</th>
                  <th>Qty Stock</th>
                  <th>Selisih</th>
                  <th>Pilih</th>
                </tr>
              </thead>
              <tbody id="opname_detail">
                <tr>
                  <td colspan="6">Tidak ada barang yang dipilih.</td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <th colspan="5" style="text-align: right;">Pilih Semua</th>
                  <th id="psemua" style="text-align: center; width:5%;"><input type='checkbox' id='ps-check' onclick='updateDataAll()'></th>
                </tr>
              </tfoot>
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

<!-- start modal add  -->
<div class="modal fade" id="addOpnameBrg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding-bottom: 5px; padding-top: 5px;">
        <h5 class="modal-title" id="exampleModalLabel">Tambah Opname Barang</h5>
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
              <label for="kdbrg">Barang</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <input type="text" class="form-control" id="kdbrg" disabled>
            </div>
          </div>
          <div class="col-1">
          </div>
          <div class="col-1">
            <div class="form-group">
              <label for="barcode">Barcode Lokasi</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <input type="text" class="form-control" id="barcode" disabled>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-1">
            <div class="form-group">
              <label for="ket">Keterangan</label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <textarea id="ket" class="form-control" rows="1"></textarea>
            </div>
          </div>
        </div>
        <input type="hidden" id="gudang">
        <input type="hidden" id="rak">
        <input type="hidden" id="lokasi">
        <hr style="margin-top: 7px; margin-bottom: 7px;" />
        <div class="row">
          <div class="col-12 tableFixHead" style="height: 60vh;">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Kode Barang</th>
                  <th>Nama Barang</th>
                  <th>Qty Computer</th>
                  <th>Qty Stock</th>
                  <th>Selisih</th>
                </tr>
              </thead>
              <tbody id="detail_opnamebrg">
                <tr>
                  <td colspan="5">Tidak ada barang.</td>
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
<div class="modal fade" id="detailOpnameBrg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding-bottom: 5px; padding-top: 5px;">
        <h5 class="modal-title" id="exampleModalLabel">Detail Opname Barang</h5>
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
              <label for="detkodebrg">Barang</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <input type="text" class="form-control" id="detkodebrg" disabled>
            </div>
          </div>
          <div class="col-1">
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
          <input type="hidden" id="detgudang">
          <input type="hidden" id="detrak">
          <input type="hidden" id="detlokasi">
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
                  <th>Kode Barang</th>
                  <th>Nama Barang</th>
                  <th>Qty Computer</th>
                  <th>Qty Stock</th>
                  <th>Selisih</th>
                </tr>
              </thead>
              <tbody id="detdetail_opnamebrg">
                <tr>
                  <td colspan="5">Tidak ada barang.</td>
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
<div class="modal fade" id="editOpnameBrg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding-bottom: 5px; padding-top: 5px;">
        <h5 class="modal-title" id="exampleModalLabel">Koreksi Opname Barang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="reset('e')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" id="eid">
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
              <label for="ekodebrg">Barang</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <input type="text" class="form-control" id="ekodebrg" onkeypress="showEditPenerimaan(event)" autofocus>
            </div>
          </div>
          <div class="col-1">
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
        <input type="hidden" id="egudang">
        <input type="hidden" id="erak">
        <input type="hidden" id="elokasi">
        <hr style="margin-top: 7px; margin-bottom: 7px;" />
        <div class="row">
          <div class="col-12">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Kode Barang</th>
                  <th>Nama Barang</th>
                  <th>Qty Computer</th>
                  <th>Qty Stock</th>
                  <th>Selisih</th>
                </tr>
              </thead>
              <tbody id="edetail_opnamebrg">
                <tr>
                  <td colspan="5">Tidak ada barang.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <hr style="margin-top: 7px; margin-bottom: 7px;" />
        <div class="row form-group" id="tombolAddBarang">
          <div class="col-12">
            <div class="float-right">
              <button type='button' class='btn btn-secondary btn-sm' onclick='eshowBatch()' rel="tooltip" data-placement="bottom" title="Batch" style="width: 33px;"><i class="fas fa-archive"></i></button>
            </div>
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

<!-- start modal add batch -->
<div class="modal fade" id="addBatch" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" style="min-width: 60% !important;" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding-bottom: 5px; padding-top: 5px;">
        <h5 class="modal-title" id="exampleModalLabel">Batch</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="index_retur">
        <div class="row">
          <div class="col-12">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Kode Barang</th>
                  <th>No. Batch</th>
                  <th>Expired Date</th>
                  <th>Qty Batch</th>
                  <th>Pakai</th>
                </tr>
              </thead>
              <tbody id="data_batch">
                <tr>
                  <td colspan="5">Tidak ada batch.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal add batch -->

<!-- start modal detail batch -->
<div class="modal fade" id="detailBatch" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" style="min-width: 60% !important;" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding-bottom: 5px; padding-top: 5px;">
        <h5 class="modal-title" id="exampleModalLabel">Batch</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="index_retur">
        <div class="row">
          <div class="col-12">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Kode Barang</th>
                  <th>No. Batch</th>
                  <th>Expired Date</th>
                  <th>Pakai</th>
                </tr>
              </thead>
              <tbody id="detdata_batch">
                <tr>
                  <td colspan="4">Tidak ada batch.</td>
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
<!-- End modal detail batch -->

<!-- start modal edit batch -->
<div class="modal fade" id="editBatch" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" style="min-width: 60% !important;" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding-bottom: 5px; padding-top: 5px;">
        <h5 class="modal-title" id="exampleModalLabel">Batch</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="eindex_retur">
        <div class="row">
          <div class="col-12">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Kode Barang</th>
                  <th>No. Batch</th>
                  <th>Expired Date</th>
                  <th>Qty Batch</th>
                  <th>Pakai</th>
                </tr>
              </thead>
              <tbody id="edata_batch">
                <tr>
                  <td colspan="5">Tidak ada batch.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal edit batch -->
@endsection

@section('js')
<script type="text/javascript">
  var data_penerimaan = [], temp = [];
  var opnamebrg = [], eopnamebrg = [], batch = [], ebatch = [], detbatch = [];
  var g_idoutp="",g_nobuktioutp="",g_kodebrgoutp="",g_namabrgoutp="",g_qntsaldooutp="",g_qntambiloutp="",g_qntsisaoutp="",g_kodegdgoutp="",g_namagudangoutp="";

  var g_id = "", g_nb = "", g_nu = "", g_kd = "", g_iddet = "", g_id_2 = "", xlokasi = "", xkodebrg = "";
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
      g_idoutp=""; g_nobuktioutp=""; g_kodebrgoutp=""; g_namabrgoutp=""; g_qntsaldooutp=""; g_qntambiloutp=""; g_qntsisaoutp=""; g_kodegdgoutp=""; g_namagudangoutp="";
    } );
    table.on('order', function () {
      $('#penerimaan_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_idoutp=""; g_nobuktioutp=""; g_kodebrgoutp=""; g_namabrgoutp=""; g_qntsaldooutp=""; g_qntambiloutp=""; g_qntsisaoutp=""; g_kodegdgoutp=""; g_namagudangoutp="";
    } );
    var table = $('#tabel_opnamebrg').DataTable({"lengthMenu": [[-1, 10, 25, 50, 100], ["All", 10, 25, 50, 100]]});
    table.on('search', function () {
      $('#opnamebrg_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_nb = "", g_nu = "", g_iddet = "";
    } );
    table.on('order', function () {
      $('#opnamebrg_data > tr').each(function() {
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

  // start refresh tabel
  function loadAll() {
    $('#tabel_penerimaan').DataTable().destroy();
    $('#tabel_opnamebrg').DataTable().destroy();
    $('#penerimaan_data > tr').each(function() {
      $(this).css('background-color', '');
    });
    g_idoutp=""; g_nobuktioutp=""; g_kodebrgoutp=""; g_namabrgoutp=""; g_qntsaldooutp=""; g_qntambiloutp=""; g_qntsisaoutp=""; g_kodegdgoutp=""; g_namagudangoutp="";
    $('#opnamebrg_data > tr').each(function() {
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
        menu : '/opnamebrg'
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
            str = str + '<tr id="'+i+'-penerimaan" onclick="select2('+i+',\''+ result[i].NoBukti1 +'\',\''+ result[i].Kodebrg +'\',\''+ result[i].QntSaldo +'\',\''+ result[i].QNTAMBIL +'\',\''+ result[i].QNTSISA +'\',\
            \''+ result[i].Kodegdg +'\',\''+ result[i].NAMAGUDANG +'\')">\
              <td>' + result[i].NoBukti1 + '</td>\
              <td>' + result[i].Kodebrg + '</td>\
              <td>' + result[i].NAMABRG + '</td>\
              <td>' + result[i].Kodegdg + '</td>\
              <td class="text-right">' + numberWithCommas(parseInt(result[i].QntSaldo)) + '</td>';
              if (result[i].QNTAMBIL == null){
                str = str + '<td class="text-right">' + numberWithCommas(0) + '</td>';
              } else {
                str = str + '<td class="text-right">' + numberWithCommas(parseInt(result[i].QNTAMBIL)) + '</td>';
              }
              str = str + '<td class="text-right">' + numberWithCommas(parseInt(result[i].QNTSISA)) + '</td>\
              </tr>';
          }
          $('#penerimaan_data').html(str);
        }
        else {
          $('#penerimaan_data').html('<tr>\
            <td colspan="7">Tidak ada data penerimaan ditemukan.</td>\
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
      url     : "{!! url('loadAllopnamebrg') !!}",
      type    : "GET",
      async   : false,
      success : function(result) {
        if (result.length > 0) {
          str = "";
          var auth = "";
          $('#opnamebrg_data').html("");
          for (var i = 0; i < result.length; i++) {
            str = str + '<tr id="' + i + '-tr" onclick="select(' + i + ', \'' + result[i].NOBUKTI + '\', \'' + result[i].NOURUT + '\', \'' + result[i].Barcode + '\')">\
              <td><button type="button" class="btn btn-secondary btn-sm btn-opsi" onclick="showDetail(\''+ result[i].NOBUKTI +'\')"><i class="fas fa-info-circle"></i></button></td>\
              <td>' + result[i].NOBUKTI + '</td>\
              <td>' + format_date(result[i].TANGGAL) + '</td>\
              <td>' + result[i].Barcode + '</td>\
              <td>' + result[i].NOTE + '</td>\
              </tr>';
          }
          $('#opnamebrg_data').html(str);
        }
        else {
          $('#opnamebrg_data').html('<tr>\
            <td colspan="6">Tidak ada Opname Barang.</td>\
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
      g_idoutp=""; g_nobuktioutp=""; g_kodebrgoutp=""; g_namabrgoutp=""; g_qntsaldooutp=""; g_qntambiloutp=""; g_qntsisaoutp=""; g_kodegdgoutp=""; g_namagudangoutp="";
    } );
    table.on('order', function () {
      $('#penerimaan_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_idoutp=""; g_nobuktioutp=""; g_kodebrgoutp=""; g_namabrgoutp=""; g_qntsaldooutp=""; g_qntambiloutp=""; g_qntsisaoutp=""; g_kodegdgoutp=""; g_namagudangoutp="";
    } );
    var table = $('#tabel_opnamebrg').DataTable({"lengthMenu": [[-1, 10, 25, 50, 100], ["All", 10, 25, 50, 100]]});
    table.on('search', function () {
      $('#opnamebrg_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_nb = "", g_nu = "", g_iddet = "";
    } );
    table.on('order', function () {
      $('#opnamebrg_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_nb = "", g_nu = "", g_iddet = "";
    } );
  }
  // end refresh table

  function select2(_row,_nobuktioutp,_kodebrgoutp,_qntsaldooutp,_qntambiloutp,_qntsisaoutp,_kodegdgoutp,_namagudangoutp) {
    $('#penerimaan_data > tr').each(function() {
      $(this).css('background-color', '');
    });
    $("#"+_row+"-penerimaan").css('background-color', 'gold');
    g_idoutp=_row; g_nobuktioutp=_nobuktioutp; g_kodebrgoutp=_kodebrgoutp;
    // g_namabrgoutp=_namabrgoutp;
    g_qntsaldooutp=_qntsaldooutp; g_qntambiloutp=_qntambiloutp; g_qntsisaoutp=_qntsisaoutp; g_kodegdgoutp=_kodegdgoutp; g_namagudangoutp=_namagudangoutp;
  }

  function select(_id, _nb, _nu, _kd) {
    $('#opnamebrg_data > tr').each(function() {
      $(this).css('background-color', '');
    });
    $("#"+_id+"-tr").css('background-color', 'gold');
    g_id = _id; g_nb = _nb; g_nu = _nu; g_kd = _kd;
  }

  function selectdet(_row, name = "") {
    $('#'+name+'detail_opnamebrg > tr').each(function() {
      $(this).css('background-color', '');
    });
    $("#"+name+_row+"-trdet").css('background-color', 'gold');
    g_iddet = _row;
  }

  function loadDetailEditpenerimaan(name) {
    var _token = $("#_token").val();
    if ($("#ekodebrg").val() != "" || $("#ebarcode").val() != "") {
      var _kodebrg = $("#ekodebrg").val();
      var _kodegdg = $("#ebarcode").val();
      var _tipe = "";
      if (name == ""){
        _tipe = "0";
        $.ajax({
          url     : "{!! url('showStkBrg') !!}",
          type    : "POST",
          async   : false,
          data    : {
            _token : _token,
            kodebrg : _kodebrg,
            kodegdg : _kodegdg,
            tipe : _tipe
          },
          success : function(result) {
            if (result.length > 0) {
              for (var i = 0; i < result.length; i++) {
                temp = [];
                temp.push(result[i].KODEBRG);//0
                temp.push(result[i].NAMABRG);//1
                temp.push(result[i].KODEGDG);//2
                temp.push(result[i].BARCODE);//3
                temp.push(numberWithCommas(result[i].SALDOQNT));//4 saldo komputer
                temp.push(numberWithCommas(result[i].SALDOQNT));//5 qty stock
                temp.push(1);//6
                temp.push(numberWithCommas(result[i].SALDOQNT-result[i].SALDOQNT));//7 selisih
                temp.push(1);//8 nosat
                temp.push(result[i].SAT1);//9 satuan
                temp.push(numberWithCommas(result[i].ISI1));//10 isi
                eopnamebrg.push(temp);
                str = str + "<tr>\
                  <td style='width:20%'>" + result[i].KODEBRG + "</td>\
                  <td style='width:35%'>" + result[i].NAMABRG + "</td>";
                  if (result[i].SALDOQNT==0){
                    str = str + "<td style='width:10%' class='text-right'>" + numberWithCommas(0) + "</td>";
                  }else{
                    str = str + "<td style='width:10%' class='text-right'>" + numberWithCommas(parseInt(result[i].SALDOQNT)) + "</td>";
                  }
                  str = str + "<td style='width:10%' class='text-right'><input type='text' class='format-number text-right' onkeyup='updateData("+i+")' id='"+i+"-qty' value='" + result[i].SALDOQNT + "'></td>\
                  <td style='width:10%' class='text-right' id='"+i+"-qtyselisih'>" + numberWithCommas(0) + "</td></tr>";
              }
              $("#edetail_opnamebrg").html(str);
              $(".format-number").autoNumeric('init', {mDec: '2'});
            } else {
              $("#edetail_opnamebrg").html('<tr><td colspan="5">Tidak ada barang yang dipilih.</td></tr>');
            }
          }
        });
      }else{
        for (var i = 0; i < eopnamebrg.length; i++) {
          if (eopnamebrg[i][0] == _kodebrg){
            alertify.warning("Barang sudah ada!");
            return;
          }
        }
        _tipe = "1";
        $.ajax({
          url     : "{!! url('showStkBrg') !!}",
          type    : "POST",
          async   : false,
          data    : {
            _token : _token,
            kodebrg : _kodebrg,
            kodegdg : _kodegdg,
            tipe : _tipe
          },
          success : function(result) {
            if (result.length > 0) {
              for (var i = 0; i < result.length; i++) {
                temp = [];
                temp.push(result[i].KodeBarcode);//0
                temp.push(result[i].NAMABRG);//1
                temp.push($("#egudang").val());//2
                temp.push($("#ebarcode").val());//3
                temp.push(numberWithCommas(0));//4 saldo komputer
                temp.push(numberWithCommas(0));//5 qty stock
                temp.push(1);//6
                temp.push(numberWithCommas(0));//7 selisih
                temp.push(1);//8 nosat
                temp.push(result[i].SAT1);//9 satuan
                temp.push(numberWithCommas(result[i].ISI1));//10 isi
                temp.push(data_penerimaan.length+1);//11
                temp.push($("#eno_bukti").val());//12
                eopnamebrg.push(temp);
                isi.push(result[i].ISI);
                str = str + "<tr>\
                  <td style='width:20%'>" + _kodebrg + "</td>\
                  <td style='width:35%'>" + result[i].NAMABRG + "</td>\
                  <td style='width:10%' class='text-right'>" + numberWithCommas(0) + "</td>\
                  <td style='width:10%' class='text-right'><input type='text' class='format-number text-right' onkeyup='updateData("+i+")' id='"+i+"-qty' value='" + 0 + "'></td>\
                  <td style='width:10%' class='text-right' id='"+i+"-qtyselisih'>" + numberWithCommas(0) + "</td></tr>";
              }
              $("#edetail_opnamebrg").html(str);
              $(".format-number").autoNumeric('init', {mDec: '2'});
            } else {
              $("#edetail_opnamebrg").html('<tr><td colspan="5">Tidak ada barang yang dipilih.</td></tr>');
            }
          }
        });
      }
    } else {
      $("#edetail_opnamebrg").html('<tr><td colspan="5">Tidak ada barang yang dipilih.</td></tr>');
    }
  }

  function loadDetailpenerimaan(name) {
    var _token = $("#_token").val();
    if ($("#kodebrg").val() != "" || $("#barcodel").val() != "") {
      var _kodebrg = $("#kodebrg").val();
      var _kodegdg = $("#barcodel").val();
      var _tipe = "";
      if (name == ""){
        _tipe = "0";
        $.ajax({
          url     : "{!! url('showStkBrg') !!}",
          type    : "POST",
          async   : false,
          data    : {
            _token : _token,
            kodebrg : _kodebrg,
            kodegdg : _kodegdg,
            tipe : _tipe
          },
          success : function(result) {
            if (result.length > 0) {
              for (var i = 0; i < result.length; i++) {
                temp = [];
                temp.push(result[i].KODEBRG);//0
                temp.push(result[i].NAMABRG);//1
                temp.push(result[i].KODEGDG);//2
                temp.push(result[i].BARCODE);//3
                temp.push(numberWithCommas(result[i].SALDOQNT));//4 saldo komputer
                temp.push(numberWithCommas(result[i].SALDOQNT));//5 qty stock
                temp.push(1);//6
                temp.push(numberWithCommas(result[i].SALDOQNT-result[i].SALDOQNT));//7 selisih
                temp.push(1);//8 nosat
                temp.push(result[i].SAT1);//9 satuan
                temp.push(numberWithCommas(result[i].ISI1));//10 isi
                data_penerimaan.push(temp);
                str = str + "<tr>\
                  <td style='width:20%'>" + result[i].KODEBRG + "</td>\
                  <td style='width:35%'>" + result[i].NAMABRG + "</td>";
                  if (result[i].SALDOQNT==0){
                    str = str + "<td style='width:10%' class='text-right'>" + numberWithCommas(0) + "</td>";
                  }else{
                    str = str + "<td style='width:10%' class='text-right'>" + numberWithCommas(parseInt(result[i].SALDOQNT)) + "</td>";
                  }
                  str = str + "<td style='width:10%' class='text-right'><input type='text' class='format-number text-right' onkeyup='updateData("+j+")' id='"+j+"-qty' value='" + result[i].SALDOQNT + "'></td>\
                  <td style='width:10%' class='text-right' id='"+j+"-qtyselisih'>" + numberWithCommas(0) + "</td>\
                  <td style='width:5%' class='text-center'><input type='checkbox' id='"+j+"-check' onclick='updateData("+j+")' checked></td></tr>";
                j++;
              }
              $("#opname_detail").html(str);
              $(".format-number").autoNumeric('init', {mDec: '2'});
            } else {
              $("#opname_detail").html('<tr><td colspan="6">Tidak ada barang yang dipilih.</td></tr>');
            }
          }
        });
      }else{
        for (var i = 0; i < data_penerimaan.length; i++) {
          if (data_penerimaan[i][0] == _kodebrg){
            alertify.warning("Barang sudah ada!");
            return;
          }
        }
        _tipe = "1";
        $.ajax({
          url     : "{!! url('showStkBrg') !!}",
          type    : "POST",
          async   : false,
          data    : {
            _token : _token,
            kodebrg : _kodebrg,
            kodegdg : _kodegdg,
            tipe : _tipe
          },
          success : function(result) {
            if (result.length > 0) {
              for (var i = 0; i < result.length; i++) {
                temp = [];
                temp.push(result[i].KodeBarcode);//0
                temp.push(result[i].NAMABRG);//1
                temp.push($("#kodegdg").val());//2
                temp.push($("#barcodel").val());//3
                temp.push(numberWithCommas(0));//4 saldo komputer
                temp.push(numberWithCommas(0));//5 qty stock
                temp.push(1);//6
                temp.push(numberWithCommas(0));//7 selisih
                temp.push(1);//8 nosat
                temp.push(result[i].SAT1);//9 satuan
                temp.push(numberWithCommas(result[i].ISI1));//10 isi
                data_penerimaan.push(temp);
                str = str + "<tr>\
                  <td style='width:20%'>" + _kodebrg + "</td>\
                  <td style='width:35%'>" + result[i].NAMABRG + "</td>\
                  <td style='width:10%' class='text-right'>" + numberWithCommas(0) + "</td>\
                  <td style='width:10%' class='text-right'><input type='text' class='format-number text-right' onkeyup='updateData("+j+")' id='"+j+"-qty' value='" + 0 + "'></td>\
                  <td style='width:10%' class='text-right' id='"+j+"-qtyselisih'>" + numberWithCommas(0) + "</td>\
                  <td style='width:5%' class='text-center'><input type='checkbox' id='"+j+"-check' onclick='updateData("+j+")' checked></td></tr>";
                j++;
              }
              $("#opname_detail").html(str);
              $(".format-number").autoNumeric('init', {mDec: '2'});
            } else {
              $("#opname_detail").html('<tr><td colspan="6">Tidak ada barang yang dipilih.</td></tr>');
            }
          }
        });
      }
    } else {
      $("#opname_detail").html('<tr><td colspan="6">Tidak ada barang yang dipilih.</td></tr>');
    }
  }

  function updateData(i) {
    if ($('#' + i + '-check').is(":checked")) {
      data_penerimaan[i][6] = 1;
    } else {
      data_penerimaan[i][6] = 0;
    }
    if ($('#' + i + '-qty').val() != "") {
      var update_qty = toInteger($('#' + i + '-qty').val());
      var comp_qty = toInteger(data_penerimaan[i][4]);
      // if (comp_qty < 0){
      //   update_qty = -1*update_qty;
      // }
      data_penerimaan[i][7] = numberWithCommas(update_qty-comp_qty);
      data_penerimaan[i][5] = $('#' + i + '-qty').val();
      $('#' + i + '-qtyselisih').html(data_penerimaan[i][7])
    } else {
      data_penerimaan[i][7] = "0";
      data_penerimaan[i][5] = "0";
      $('#' + i + '-qtyselisih').html(data_penerimaan[i][7])
    }
  }

  function updateDataAll() {
    if ($('#ps-check').is(":checked")) {
      document.getElementById("ps-check").checked = true;
      for (var i = 0; i < data_penerimaan.length; i++) {
        document.getElementById(""+i+"-check").checked = true;
        if ($('#' + i + '-check').is(":checked")) {
          data_penerimaan[i][6] = 1;
        } else {
          data_penerimaan[i][6] = 0;
        }
        // j++
      }
    } else {
      document.getElementById("ps-check").checked = false;
      for (var i = 0; i < data_penerimaan.length; i++) {
        document.getElementById(""+i+"-check").checked = false;
        if ($('#' + i + '-check').is(":checked")) {
          data_penerimaan[i][6] = 1;
        } else {
          data_penerimaan[i][6] = 0;
        }
        // j++
      }
    }
  }

  function generateNoBukti() {
    var res = "";
    $.ajax({
      url     : "{!! url('generateNomorBuktiopnamebrg') !!}",
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
      url     : "{!! url('generateNomorUrutopnamebrg') !!}",
      type    : "GET",
      async   : false,
      success : function(result) {
        res = result;
      }
    });
    return res;
  }

  function showPilih() {
    $("#barcodel").val("");
    $("#pilihpenerimaan").modal('toggle');
    $("#opname_detail").html("");
    $("#no_urut").val(generateNoUrut());
    $("#no_bukti").val(generateNoBukti());
    j = 0;
    data_penerimaan = [];
    str = "";
  }

  function showEditPenerimaan(event) {
    var barcode=$("#ekodebrg").val();
    let unicode= event.which;
    document.getElementById("ekodebrg").innerHTML = unicode;
    if (unicode === 13){
      if (barcode.length===6){
        if ($("#ebarcode").val() == "") {
          alertify.warning("Scan Lokasi Terlebih dulu!");
          return;
        }
        xkodebrg=$("#ekodebrg").val();
        loadDetailEditpenerimaan("e");
      }else{
        if ($("#ebarcode").val() != "") {
          alertify.warning("Tidak bisa Scan Lokasi lebih dari sekali!");
          return;
        }
        xlokasi=$("#ekodebrg").val();
        $("#ebarcode").val(xlokasi);
        loadbarcode("U");
        loadDetailEditpenerimaan("");
      }
      $("#ekodebrg").val("");
    }
  }

  function showPenerimaan(event) {
    var barcode=$("#kodebrg").val();
    let unicode= event.which;
    document.getElementById("kodebrg").innerHTML = unicode;
    if (unicode === 13){
      if (barcode.length===6){
        if ($("#barcodel").val() == "") {
          alertify.warning("Scan Lokasi Terlebih dulu!");
          return;
        }
        xkodebrg=$("#kodebrg").val();
        loadDetailpenerimaan("e");
      }else{
        if ($("#barcodel").val() != "") {
          alertify.warning("Tidak bisa Scan Lokasi lebih dari sekali!");
          return;
        }
        xlokasi=$("#kodebrg").val();
        $("#barcodel").val(xlokasi);
        loadbarcode("");
        $("#lkodebrg").html("Barang");
        loadDetailpenerimaan("");
      }
      $("#kodebrg").val("");
    }
  }

  function loadbarcode(name) {
    var barcode="";
    if (name==""){
      barcode=$("#barcodel").val();
    } else if (name=="U"){
      barcode=$("#ebarcode").val();
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
          } else if (name=="U"){
            $("#egudang").val(result.KodeGdg);
            $("#erak").val(result.KodeRak);
            $("#elokasi").val(result.KodeLokasi);
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
      var count = 0;
      var barcode = "";
      var gudang = "";
      var rak = "";
      var lokasi = "";
      for (var i = 0; i < data_penerimaan.length; i++) {
        count = count + data_penerimaan[i][6];
      }
      if (count > 0) {
        barcode = $("#barcodel").val();
        gudang = $("#kodegdg").val();
        rak = $("#koderak").val();
        lokasi = $("#kodelokasi").val();
        // $("#pilihpenerimaan").modal('toggle');
        $("#barcode").val(barcode);
        $("#gudang").val(gudang);
        $("#rak").val(rak);
        $("#lokasi").val(lokasi);

        opnamebrg = [];
        batch = [];
        for (var i = 0; i < data_penerimaan.length; i++) {
          // alert(data_penerimaan[i][6]+"(=1)"+data_penerimaan[i][3] +"(>0)");
          if (data_penerimaan[i][6] == 1) {
            temp = [];
            temp.push(data_penerimaan[i][0]);//0 kdbrg
            temp.push(data_penerimaan[i][1]);//1 nmbrg
            temp.push(data_penerimaan[i][2]);//2 kdgdg
            temp.push(data_penerimaan[i][3]);//3 barcode
            temp.push(data_penerimaan[i][4]);//4 qntkomputer
            temp.push(data_penerimaan[i][5]);//5 qntstock
            temp.push(data_penerimaan[i][6]);//6 ispilih
            temp.push(data_penerimaan[i][7]);//7 qntselisih
            temp.push(data_penerimaan[i][8]);//8 nosat
            temp.push(data_penerimaan[i][9]);//9 sat
            temp.push(data_penerimaan[i][10]);//10 isi
            opnamebrg.push(temp);
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
            //       var qty = result[j].qty / toInteger(data_penerimaan[i][4]);
            //       if (qty > 0) {
            //         dummy.push(result[j].no_batch);
            //         dummy.push(result[j].kode_barang);
            //         dummy.push(result[j].tanggal);
            //         dummy.push(numberWithCommas(round(qty, 1)));
            //         dummy.push(numberWithCommas(0));
            //         dummy.push(i);
            //         dummy.push(toInteger(data_penerimaan[i][4]));
            //         batch.push(dummy);
            //       }
            //     }
            //   }
            // });
          }
        }
        data_penerimaan = [];
        $("#no_penerimaan").val("");
        loadopnamebrg();
        // $("#addOpnameBrg").modal("toggle");
        add();
      }
    }
  }

  function loadopnamebrg(name = "") {
    $('#'+name+'detail_opnamebrg > tr').each(function() {
      $(this).css('background-color', '');
    });
    g_iddet = "";
    str = "";
    if (name == "") {
      if (opnamebrg.length > 0) {
        for (var i = 0; i < opnamebrg.length; i++) {
          str += "<tr id='" + i + "-trdet' onclick='selectdet(" + i + ")'>\
            <td style='width: 15%;'>" + opnamebrg[i][0] + '</td>\
            <td style="width: 55%;">' + opnamebrg[i][1] + '</td>\
            <td style="width: 10%; text-align: right;">' + numberWithCommas(toInteger(opnamebrg[i][4])) + '</td>\
            <td style="width: 10%; text-align: right;"><input type="text" id="'+i+'-qtypakai" class="format-number text-right" onkeyup="updateBarang('+i+')" value="'+ toInteger(opnamebrg[i][5]) +'"></td>\
            <td style="width: 10%; text-align: right;" id="'+i+'-qtyselisih">' + numberWithCommas(toInteger(opnamebrg[i][7])) + '</td></tr>';
        }
        $("#detail_opnamebrg").html(str);
        $(".format-number").autoNumeric('init', {mDec: '2'});
      }
      else {
        $("#detail_opnamebrg").html('<tr><td colspan="5">Tidak ada barang.</td></tr>');
      }
    } else {
      if (eopnamebrg.length > 0) {
        for (var i = 0; i < eopnamebrg.length; i++) {
          str += "<tr id='e" + i + "-trdet' onclick='selectdet(" + i + ", \"e\")'>\
            <td style='width: 15%; '>" + eopnamebrg[i][0] + '</td>\
            <td style="width: 55%; ">' + eopnamebrg[i][1] + '</td>\
            <td style="width: 10%; text-align: right;">' + numberWithCommas(toInteger(eopnamebrg[i][4])) + '</td>\
            <td style="width: 10%; text-align: right;"><input type="text" id="e'+i+'-qtypakai" class="format-number text-right" onkeyup="updateBarang('+i+', \'e\')" value="'+ toInteger(eopnamebrg[i][5]) +'"></td>\
            <td style="width: 10%; text-align: right;" id="e'+i+'-qtydselisih">' + numberWithCommas(toInteger(eopnamebrg[i][7])) + '</td></tr>';
        }
        $("#edetail_opnamebrg").html(str);
        $(".format-number").autoNumeric('init', {mDec: '2'});
      }
      else {
        $("#edetail_opnamebrg").html('<tr><td colspan="5">Tidak ada barang.</td></tr>');
      }
    }
    // calculateBatch(name);
  }

  function updateBarang(index, name = "") {
    if (name == "") {
      if ($('#' + index + '-qtypakai').val() != "") {
        var update_qty = toInteger($('#' + index + '-qtypakai').val());
        var comp_qty = toInteger(opnamebrg[index][4]);
        // if (comp_qty < 0){
        //   update_qty = -1*update_qty;
        // }
        // alert(update_qty+"-"+comp_qty);
        opnamebrg[index][7] = numberWithCommas(update_qty-comp_qty);
        opnamebrg[index][5] = $('#' + index + '-qtypakai').val();
        $('#' + index + '-qtydselisih').html(opnamebrg[index][7])
      } else {
        opnamebrg[index][7] = "0";
        opnamebrg[index][5] = "0";
        $('#' + index + '-qtydselisih').html(opnamebrg[index][7])
      }
    } else {
      if ($('#e' + index + '-qtypakai').val() != "") {
        var update_qty = toInteger($('#e' + index + '-qtypakai').val());
        var comp_qty = toInteger(eopnamebrg[index][4]);
        // if (comp_qty < 0){
        //   update_qty = -1*update_qty;
        // }
        // alert(update_qty+"-"+comp_qty);
        eopnamebrg[index][7] = numberWithCommas(update_qty-comp_qty);
        eopnamebrg[index][5] = $('#e' + index + '-qtypakai').val();
        $('#e' + index + '-qtydselisih').html(eopnamebrg[index][7])
      } else {
        eopnamebrg[index][7] = "0";
        eopnamebrg[index][5] = "0";
        $('#e' + index + '-qtydselisih').html(eopnamebrg[index][7])
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
      for (var i = 0; i < opnamebrg.length; i++) {
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
      for (var i = 0; i < eopnamebrg.length; i++) {
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
        for (var i = 0; i < opnamebrg.length; i++) {
          temp.push(0);
          count_b.push(0);
          temp2.push(parseFloat(opnamebrg[i][2].replace(/,/g, "")));
        }
        for (var i = 0; i < batch.length; i++) {
          temp[batch[i][5]] += parseFloat(batch[i][4].replace(/,/g, ""));
          count_b[batch[i][5]] += 1;
        }
        for (var i = 0; i < temp.length; i++) {
          if (temp[i] != temp2[i] && count_b[i] > 0) {
            return '0&'+opnamebrg[i][0];
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
        for (var i = 0; i < eopnamebrg.length; i++) {
          temp.push(0);
          count_b.push(0);
          temp2.push(parseFloat(eopnamebrg[i][2].replace(/,/g, "")));
        }
        for (var i = 0; i < ebatch.length; i++) {
          temp[ebatch[i][5]] += parseFloat(ebatch[i][4].replace(/,/g, ""));
          count_b[ebatch[i][5]] += 1;
        }
        for (var i = 0; i < temp.length; i++) {
          if (temp[i] != temp2[i] && count_b[i] > 0) {
            return '0&'+eopnamebrg[i][0];
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
      for (var i = 0; i < opnamebrg.length; i++) {
        opnamebrg[i][4] = toInteger(opnamebrg[i][4]);
        opnamebrg[i][5] = toInteger(opnamebrg[i][5]);
        opnamebrg[i][7] = toInteger(opnamebrg[i][7]);
        opnamebrg[i][10] = toInteger(opnamebrg[i][10]);
      }
      for (var i = 0; i < batch.length; i++) {
        batch[i][4] = toInteger(batch[i][4]);
      }
    } else {
      for (var i = 0; i < eopnamebrg.length; i++) {
        eopnamebrg[i][4] = toInteger(eopnamebrg[i][4]);
        eopnamebrg[i][5] = toInteger(eopnamebrg[i][5]);
        eopnamebrg[i][7] = toInteger(eopnamebrg[i][7]);
        eopnamebrg[i][10] = toInteger(eopnamebrg[i][10]);
      }
      for (var i = 0; i < ebatch.length; i++) {
        ebatch[i][4] = toInteger(ebatch[i][4]);
      }
    }
  }

  function returnCommas(name = "") {
    if (name == "") {
      for (var i = 0; i < opnamebrg.length; i++) {
        opnamebrg[i][4] = numberWithCommas(opnamebrg[i][4]);
        opnamebrg[i][5] = numberWithCommas(opnamebrg[i][5]);
        opnamebrg[i][7] = numberWithCommas(opnamebrg[i][7]);
        opnamebrg[i][10] = numberWithCommas(opnamebrg[i][10]);
      }
      for (var i = 0; i < batch.length; i++) {
        batch[i][4] = numberWithCommas(batch[i][4]);
      }
    } else {
      for (var i = 0; i < eopnamebrg.length; i++) {
        eopnamebrg[i][4] = numberWithCommas(eopnamebrg[i][4]);
        eopnamebrg[i][5] = numberWithCommas(eopnamebrg[i][5]);
        eopnamebrg[i][7] = numberWithCommas(eopnamebrg[i][7]);
        eopnamebrg[i][10] = numberWithCommas(eopnamebrg[i][10]);
      }
      for (var i = 0; i < ebatch.length; i++) {
        ebatch[i][4] = numberWithCommas(ebatch[i][4]);
      }
    }
  }

  function reset(name = "") {
    opnamebrg = []; batch = [], data_penerimaan = [];
    xlokasi = "", xkodebrg = "";
    $("#opname_detail").html("");
    $("#kodebrg").val("");
    $("#barcodel").val("");
    $("#keterangan").val("");
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
    var _barcode = $("#barcodel").val();
    var _keterangan = $("#keterangan").val();
    var _gudang = $("#kodegdg").val();
    var _rak = $("#koderak").val();
    var _lokasi = $("#kodelokasi").val();
    var _choice = "I";
    var cek = cekBatch();
    if (_no_urut != "" && _no_bukti != "" && _tanggal != "" &&
       _barcode != "" &&
      opnamebrg.length > 0
      // && cek == '1' && _no_penerimaan != ""
        ) {
      removeCommas();
      $.ajax({
        url     : "{!! url('addopnamebrg') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          choice : _choice,
          no_urut : _no_urut,
          no_bukti : _no_bukti,
          tanggal : _tanggal,
          barcode : _barcode,
          keterangan : _keterangan,
          gudang : _gudang,
          rak : _rak,
          lokasi : _lokasi,
          opnamebrg : opnamebrg,
          batch : batch
        },
        success : function(result) {
          $("#no_urut").val(generateNoUrut());
          $("#no_bukti").val(generateNoBukti());
          if (result.split(";;")[0] == "1") {
            reset(); loadAll();
            showPilih();
            // select(0, result.split(";;")[2],result.split(";;")[1],result.split(";;")[3]);
            // $("#addOpnameBrg").modal("toggle");
            alertify.success('Opname Barang telah ditambahkan.');
          } else if (result.split(";;")[0] == "0") {
            returnCommas();
            alertify.alert('Gagal menambahkan Opname Barang!', 'Nomor Bukti sudah dipakai. Silahkan tekan tombol simpan kembali.', function(){ });
          } else {
            returnCommas();
            alertify.warning('Barang '+result+' melebihi batas kuantitas barang.');
          }
        }
      });
    } else {
      if (cek != '1') {
        if (cek.split("&")[0] == '0') {
          alertify.alert('Gagal menambahkan Opname Barang!', 'Jumlah batch kode barang '+cek.split("&")[1]+' tidak sama.', function(){ });
        } else {
          alertify.alert('Gagal menambahkan Opname Barang!', 'Jumlah batch kode barang '+cek.split("&")[2]+' pada no. batch '+cek.split("&")[1]+' dengan expired date '+cek.split("&")[3]+' melebihi jumlah qty batch.', function(){ });
        }
      } else {
        alertify.alert('Gagal menambahkan Opname Barang!', 'Semua kolom harus terisi.', function(){ });
      }
    }
  }
  // end add

  function changeAuth(id, no_bukti) {
    alertify.confirm('Otorisasi alokasi Barang', 'Apakah yakin ingin mengotorisasi alokasi Barang dengan nomor bukti ' + no_bukti + ' ?',
      function(){
        var _token = $("#_token").val(); var err = 0;
        $.ajax({
          url     : "{!! url('showDetOpnameBrg') !!}",
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
          url     : "{!! url('authOpnameBrg') !!}",
          type    : "POST",
          async   : false,
          data    : {
            _token : _token,
            nobukti : no_bukti
          },
          success : function(result) {
            loadAll();
            alertify.success('alokasi Barang dengan nomor bukti '+no_bukti+' telah diotorisasi.');
          }
        });
    },function(){
      $("#"+id+"-auth").prop('checked', false);
    });
  }

  function changeBatal(id, no_bukti) {
    alertify.confirm('Pembatalan alokasi Barang', 'Apakah yakin ingin membatalkan alokasi Barang dengan nomor bukti ' + no_bukti + ' ?',
      function(){
        var _token = $("#_token").val();
        $.ajax({
          url     : "{!! url('batalOpnameBrg') !!}",
          type    : "POST",
          async   : false,
          data    : {
            _token : _token,
            nobukti : no_bukti
          },
          success : function(result) {
            loadAll();
            alertify.success('alokasi Barang dengan nomor bukti '+no_bukti+' telah dibatalkan.');
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
    var date = new Date();
    // var date = "";
    var _token = $("#_token").val();
    $.ajax({
      url     : "{!! url('showOpnameBrg') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        nobukti : _nobukti
      },
      success : function(result) {
        var d="", m="", y="";
        $("#detno_urut").val(result.NOURUT);
        $("#detno_bukti").val(result.NOBUKTI);
        d=("0"+result.Hari).slice(-2);
        m=("0"+result.Bln).slice(-2);
        y=("0"+result.Thn).slice(-4);
        $("#dettanggal").val(y+"-"+m+"-"+d);
        // date = format_date(result.TANGGAL);
        // $("#dettanggal").val(date);
        $("#detbarcode").val(result.Barcode);
        $("#detketerangan").val(result.KETERANGAN);
        $("#detgudang").val(result.KodeGdg);
        $("#detrak").val(result.KodeRak);
        $("#detlokasi").val(result.KodeLokasi);
        no_bukti = result.NOBUKTI;
      }
    });
    $.ajax({
      url     : "{!! url('showDetOpnameBrg') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        id : no_bukti
      },
      success : function(result) {
        str = "";
        $("#detdetail_opnamebrg").html("");
        for (var i = 0; i < result.length; i++) {
          str = str + "<tr id='det" + i + "-trdet' onclick='selectdet(" + i + ", \"det\")'>\
            <td style='width: 15%;'>" + result[i].KODEBRG + "</td>\
            <td style='width: 55%;'>" + result[i].NAMABRG + "</td>";
            if (result[i].SaldoComp==0){
              str = str + "<td style='width: 10%;' class='text-right'>" + numberWithCommas(0) + "</td>";
            } else
            {
              str = str + "<td style='width: 10%;' class='text-right'>" + numberWithCommas(parseInt(result[i].SaldoComp)) + "</td>";
            }
            if (result[i].QntOpname==0){
              str = str + "<td style='width: 10%;' class='text-right'>" + numberWithCommas(0) + "</td>";
            } else
            {
              str = str + "<td style='width: 10%;' class='text-right'>" + numberWithCommas(parseInt(result[i].QntOpname)) + "</td>";
            }
            if (result[i].Selisih==0){
              str = str + "<td style='width: 10%;' class='text-right'>" + numberWithCommas(0) + "</td>";
            } else
            {
              str = str + "<td style='width: 10%;' class='text-right'>" + numberWithCommas(parseInt(result[i].Selisih)) + "</td>";
            }
          temp.push(0);
          isi.push(result[i].ISI);
        }
        $("#detdetail_opnamebrg").html(str);
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
    $("#detailOpnameBrg").modal('toggle');
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
      url     : "{!! url('showOpnameBrg') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        nobukti : no_bukti
      },
      success : function(result) {
        // cekauth = result.IsOtorisasi1;
        var d="",m="",y="";
        cekauth = 0;
        $("#eno_urut").val(result.NOURUT);
        $("#eno_bukti").val(result.NOBUKTI);
        d=("0"+result.Hari).slice(-2);
        m=("0"+result.Bln).slice(-2);
        y=("0"+result.Thn).slice(-4);
        $("#etanggal").val(y+"-"+m+"-"+d);
        // $("#etanggal").val(format_date(result.TANGGAL));
        $("#ebarcode").val(result.Barcode);
        $("#eketerangan").val(result.KETERANGAN);
        loadbarcode("U");
        gudang = result.KodeGdg;
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
      url     : "{!! url('showDetOpnameBrg') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        id : g_nb
      },
      success : function(result) {
        eopnamebrg = [];
        for (var i = 0; i < result.length; i++) {
          temp = [];
          temp.push(result[i].KODEBRG);//0
          temp.push(result[i].NAMABRG);//1
          temp.push(result[i].KODEGDG);//2
          temp.push(result[i].Barcode);//3
          if (result[i].SaldoComp==0){
            temp.push(numberWithCommas(0));//4
          }else{
            temp.push(numberWithCommas(result[i].SaldoComp));//4
          }
          if (result[i].QntOpname==0){
            temp.push(numberWithCommas(0));//5
          }else{
            temp.push(numberWithCommas(result[i].QntOpname));//5
          }
          temp.push(0);//6
          if (result[i].Selisih==0){
            temp.push(numberWithCommas(0));//7
          }else{
            temp.push(numberWithCommas(result[i].Selisih));//7
          }
          temp.push(result[i].NOSAT);//8
          temp.push(result[i].SATUAN);//9
          temp.push(numberWithCommas(result[i].ISI));//10
          temp.push(result[i].URUT);//11
          temp.push(result[i].NOBUKTI);//12
          eopnamebrg.push(temp);
          isi.push(result[i].ISI);
        }
      }
    });
    // $.ajax({
    //   url     : "{!! url('showDetPenerimaanOpnameBrg') !!}",
    //   type    : "POST",
    //   async   : false,
    //   data    : {
    //     _token : _token,
    //     id : g_nb
    //   },
    //   success : function(result) {
    //     for (var i = 0; i < eopnamebrg.length; i++) {
    //       for (var j = 0; j < result.length; j++) {
    //         if (eopnamebrg[i][0] == result[j].NoBeli && eopnamebrg[i][1] == result[j].KODEBRG && eopnamebrg[i][9] == result[j].UrutBeli) {
    //           eopnamebrg[i][6] += result[j].QNTSISA;
    //           break;
    //         }
    //       }
    //     }
    //   }
    // });
    // for (var a = 0; a < eopnamebrg.length; a++) {
    //   $.ajax({
    //     url     : "{!! url('getBatchBarang') !!}",
    //     type    : "POST",
    //     async   : false,
    //     data    : {
    //       _token : _token,
    //       kode_barang : eopnamebrg[a][0],
    //       gudang : gudang
    //     },
    //     success : function(result) {
    //       for (var i = 0; i < result.length; i++) {
    //         var dummy = [];
    //         var qty = result[i].qty / parseInt(eopnamebrg[a][4].replace(/,/g, ""));
    //         dummy.push(result[i].no_batch);
    //         dummy.push(result[i].kode_barang);
    //         dummy.push(result[i].tanggal);
    //         dummy.push(numberWithCommas(round(qty, 1)));
    //         dummy.push(numberWithCommas(0));
    //         dummy.push(a);
    //         dummy.push(eopnamebrg[a][4].replace(/,/g, ""));
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
    loadopnamebrg('e');
    $("#editOpnameBrg").modal('toggle');
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
    var _choice = "U";
    var cek = '0';
    // var cek = cekBatch('e');
    if (_no_urut != "" && _no_bukti != "" && _tanggal != "" &&
        _barcode != "" &&
      eopnamebrg.length > 0
      // && cek == '1' && _id_penerimaan != ""
    ) {
      removeCommas('e');
      $.ajax({
        url     : "{!! url('addopnamebrg') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          id : _id,
          choice : _choice,
          no_urut : _no_urut,
          no_bukti : _no_bukti,
          tanggal : _tanggal,
          gudang : _gudang,
          rak : _rak,
          lokasi : _lokasi,
          barcode : _barcode,
          id_penerimaan : _id_penerimaan,
          keterangan : _keterangan,
          opnamebrg : eopnamebrg,
          batch : ebatch
        },
        success : function(result) {
          if (result == 1) {
            loadAll(); select(_id, _no_bukti, _no_urut,''); reset('e');
            $("#editOpnameBrg").modal('toggle');
            alertify.success('Data Opname Barang telah diubah.');
          } else {
            returnCommas('e');
            alertify.warning('Barang '+result+' melebihi batas kuantitas Opname Barang.');
          }
        }
      });
    }
    else {
      if (cek != '1') {
        if (cek.split("&")[0] == '0') {
          alertify.alert('Gagal mengubah Opname Barang!', 'Jumlah batch kode barang '+cek.split("&")[1]+' tidak sama.', function(){ });
        } else {
          alertify.alert('Gagal mengubah Opname Barang!', 'Jumlah batch kode barang '+cek.split("&")[2]+' pada no. batch '+cek.split("&")[1]+' dengan expired date '+cek.split("&")[3]+' melebihi jumlah qty batch.', function(){ });
        }
      } else {
        alertify.alert('Gagal mengubah Opname Barang!', 'Semua kolom harus terisi.', function(){ });
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
      url     : "{!! url('showOpnameBrg') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        nobukti :g_nb
      },
      success : function(result) {
        // cekauth = result.IsOtorisasi1;
        cekauth = 0;
      }
    });
    if (cekauth == 1) {
      alertify.warning("Transaksi sudah diotorisasi");
      return;
    }
    alertify.confirm('Hapus Opname Barang', 'Apakah yakin ingin menghapus Opname Barang dengan nomor bukti ' + g_nb + ' dan Kode Barang ' + g_kd + ' ?',
      function(){
        var _token = $("#_token").val();
        $.ajax({
          url     : "{!! url('addopnamebrg') !!}",
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
              alertify.success('Data Opname Barang telah dihapus.');
            } else {
              alertify.warning('Data Opname Barang gagal dihapus.');
            }
          }
        });
    },function(){});
  }
  // end hapus invoice
</script>
@endsection
