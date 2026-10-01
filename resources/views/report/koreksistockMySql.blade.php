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
  <a class="nav-link" href="#" data-toggle="tooltip" data-placement="bottom" title="Gudang / Koreksi Stock"><span class="blue" id="title_page">Koreksi Stock</span></a>
</li>
@endsection

@section('button-add-refresh')
<button type="button" class="btn btn-primary btn-sm btn-top" onclick="loadAll()" rel="tooltip" data-placement="bottom" title="Refresh" style="width: 30px;"><i class="fas fa-sync-alt"></i></button>&nbsp;&nbsp;
@if ($akses->tambah == 1)
  <button class="btn btn-success btn-sm btn-top" type="button" onclick="showAdd()" rel="tooltip" data-placement="bottom" title="Tambah" style="width: 30px;"><i class="fas fa-plus-circle"></i></button>&nbsp;&nbsp;
@endif
@if ($akses->koreksi == 1)
  <button type="button" class="btn btn-warning btn-sm btn-top" onclick="show()" rel="tooltip" data-placement="bottom" title="Ubah" style="width: 30px;"><i class="fas fa-pencil-alt"></i></button>&nbsp;&nbsp;
@endif
@if ($akses->hapus == 1)
  <button type="button" class="btn btn-danger btn-sm btn-top" onclick="erase()" rel="tooltip" data-placement="bottom" title="Hapus" style="width: 30px;"><i class="fas fa-trash"></i></button>
@endif
@endsection

@section('content')
<div class="container-fluid">
  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <div class="card">
    <div class="card-body" style="background: palegoldenrod;">
      <div class="row">
        <div class="col-12" style="padding-left: 20px; padding-right: 20px; overflow:auto;">
          <table class="table table-bordered table-striped" id="tabel_koreksi" style="background:white;">
            <thead>
              <tr>
                <th width="10px"></th>
                <th>No. Bukti</th>
                <th>Tanggal</th>
                <th>Gudang</th>
                <th>OT</th>
                <th>User OT</th>
                <th>Tanggal OT</th>
                <th>BT</th>
                <th>User BT</th>
                <th>Tanggal BT</th>
              </tr>
            </thead>
            <tbody id="koreksi_data">
              @if (count($koreksi) > 0)
                @for ($i = 0; $i < count($koreksi); $i++)
                  <tr id="{!! $koreksi[$i]->id !!}-tr" onclick="select({!! $koreksi[$i]->id !!}, '{!! $koreksi[$i]->no_bukti !!}')">
                    <td><button type="button" class="btn btn-secondary btn-sm btn-opsi" onclick="showDet({!! $koreksi[$i]->id !!})"><i class="fas fa-info-circle"></i></button></td>
                    <td>{!! $koreksi[$i]->no_bukti !!}</td>
                    <td>{!! date("d/m/Y", strtotime($koreksi[$i]->tanggal)) !!}</td>
                    <td>{!! $koreksi[$i]->gudang !!} - {!! $koreksi[$i]->nama !!}</td>
                    @if ($koreksi[$i]->auth_1 == 1)
                    <td><input type="checkbox" class="form-control check_auth" disabled checked></td>
                    @else
                    <td><input type="checkbox" class="form-control check_auth" id="{!! $koreksi[$i]->id !!}-auth" onchange="changeAuth({!! $koreksi[$i]->id !!}, '{!! $koreksi[$i]->no_bukti !!}')"></td>
                    @endif
                    <td>{!! $koreksi[$i]->auth_user_1 !!}</td>
                    @if (isset($koreksi[$i]->auth_date_1))
                      <td>{!! date("d/m/Y", strtotime($koreksi[$i]->auth_date_1)) !!}</td>
                    @else
                      <td></td>
                    @endif
                    @if ($koreksi[$i]->batal == 1)
                    <td><input type="checkbox" class="form-control check_batal" disabled checked></td>
                    @else
                    <td><input type="checkbox" class="form-control check_batal" id="{!! $koreksi[$i]->id !!}-batal" onchange="changeBatal({!! $koreksi[$i]->id !!}, '{!! $koreksi[$i]->no_bukti !!}')"></td>
                    @endif
                    <td>{!! $koreksi[$i]->batal_user !!}</td>
                    @if (isset($koreksi[$i]->batal_date))
                      <td>{!! date("d/m/Y", strtotime($koreksi[$i]->batal_date)) !!}</td>
                    @else
                      <td></td>
                    @endif
                  </tr>
                @endfor
              @else
                <tr>
                  <td colspan="10">Tidak ada data koreksi stock ditemukan.</td>
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

<!-- start modal add -->
<div class="modal fade" id="addKoreksiStock" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Tambah Koreksi Stock</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="reset()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" id="no_urut">
          <div class="col-onequarter">
            <label for="no_urut">No. Bukti</label>
            <input type="text" class="form-control" id="no_bukti" disabled>
          </div>
          <div class="col-onequarter">
            <label for="tanggal">Tanggal</label>
            <input type="date" class="form-control" id="tanggal" value="{!! date('Y-m-d') !!}">
          </div>
          <div class="col-2">
            <label for="gudang">Gudang</label>
            <select class="form-control" id="gudang" onchange="loadBarang()">
              <option value="">-- Pilih Gudang --</option>
              @for ($i = 0; $i < count($gudang); $i++)
                <option value="{!! $gudang[$i]->kode !!}">{!! $gudang[$i]->kode !!} - {!! $gudang[$i]->nama !!}</option>
              @endfor
            </select>
          </div>
          <div class="col-4">
            <label for="keterangan">Keterangan</label>
            <textarea id="keterangan" class="form-control" rows="1"></textarea>
          </div>
        </div>
        <hr style="margin-top: 7px; margin-bottom: 7px;" />
        <div class="row">
          <div class="col-12 tableFixHead" style="height: 57vh;">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th style="width: 15%;">Kode Barang</th>
                  <th>Nama Barang</th>
                  <th style="width: 7%;">Qty Masuk</th>
                  <th style="width: 7%;">Qty Keluar</th>
                  <th style="width: 5%;">Satuan</th>
                  <th style="width: 7%;">Isi</th>
                  <th style="width: 7%;">Harga</th>
                  <th style="width: 7%;">Batch</th>
                </tr>
              </thead>
              <tbody id="cart">

              </tbody>
            </table>
          </div>
        </div>
        <hr style="margin-top: 7px; margin-bottom: 7px;" />
        <div class="row form-group">
          <div class="col-12" id="tombolAddBarang">
            <div class="float-right">
              <button type="button" class="btn btn-sm btn-success" onclick="showDivAdd()" rel="tooltip" data-placement="bottom" title="Tambah" style="width: 30px;"><i class="fas fa-plus-circle"></i></button>
              <button type='button' class='btn btn-warning btn-sm' onclick='editCart()' rel="tooltip" data-placement="bottom" title="Ubah" style="width: 30px;"><i class='fas fa-pencil-alt'></i></button>
              <button type='button' class='btn btn-danger btn-sm' onclick='deleteCart()' rel="tooltip" data-placement="bottom" title="Hapus" style="width: 30px;"><i class='fas fa-trash'></i></button>
              <button type='button' class='btn btn-secondary btn-sm' onclick='showBatch()' rel="tooltip" data-placement="bottom" title="Batch" style="width: 33px;"><i class="fas fa-archive"></i></button>
            </div>
          </div>
        </div>
        <div id="divAdd" style="margin-top: -7px;">
          <div class="row form-group">
            <div class="col-1">
              <label for="mk">M/K</label>
              <select class="form-control" id="mk" onchange="disableHPP()">
                <option value="0">Masuk</option>
                <option value="1">Keluar</option>
              </select>
            </div>
            <div class="col-2">
              <label for="pilihjenis">Pilih Jenis </label>
              <select class="form-control" id="pilihjenis" onchange="loadBarangGdg()">
                <option value="-">Semua</option>
                @for ($i = 0; $i < count($grup); $i++)
                  <option value="{!! $grup[$i]->kode !!}">{!! $grup[$i]->kode !!} - {!! $grup[$i]->nama !!}</option>
                @endfor
              </select>
            </div>
            <div class="col-4">
              <label for="barang">Barang</label>
              <select class="form-control" id="barang" onchange="loadSatuan()">

              </select>
              <!-- <input type="text" id="barang" class="form-control"> -->
              <input type="hidden" id="kbarang">
              <input type="hidden" id="hbarang">
            </div>
            <input type="hidden" id="nama_brg" class="form-control" disabled>
            <div class="col-1">
              <label for="qty">Qty</label>
              <input type="text" id="qty" class="form-control format-number text-right" placeholder="Kuantitas" value="0.00" required>
            </div>
            <div class="col-1">
              <label for="satuan">Satuan</label>
              <select class="form-control" id="satuan">

              </select>
            </div>
            <div class="col-onehalf">
              <label for="hpp">Harga</label>
              <input type="text" id="hpp" class="form-control format-number text-right" placeholder="Harga" value="0">
            </div>
            <div class="col-onehalf">
              <label for="" style="width: 100%;">&nbsp;&nbsp;</label>
              <button type="button" class="btn btn-primary float-right" style="margin-left: 5px;" id="addBarang" onclick="addBarang()">Tambah</button>
              <button type="button" class="btn btn-secondary float-right" onclick="batalAdd()">Batal</button>
            </div>
          </div>
        </div>
        <div id="divEdit">
          <div class="row form-group">
            <input type="hidden" id="editrowcart">
            <div class="col-1">
              <label for="editmk">M/K</label>
              <select class="form-control" id="editmk" onchange="disableHPP('edit')">
                <option value="0">Masuk</option>
                <option value="1">Keluar</option>
              </select>
            </div>
            <div class="col-5">
              <label for="editbarang">Barang</label>
              <input class="form-control" id="editbarang" disabled>
            </div>
            <!-- <div class="col-2">
              <label for="editnama_brg">Nama</label> -->
              <input type="hidden" id="editnama_brg" class="form-control" disabled>
            <!-- </div> -->
            <div class="col-1">
              <label for="editqty">Qty</label>
              <input type="text" id="editqty" class="form-control format-number text-right" placeholder="Kuantitas" required>
            </div>
            <div class="col-1">
              <label for="editsatuan">Satuan</label>
              <select class="form-control" id="editsatuan">

              </select>
            </div>
            <div class="col-onehalf">
              <label for="edithpp">Harga</label>
              <input type="text" id="edithpp" class="form-control format-number text-right" placeholder="Harga" value="0">
            </div>
            <div class="col-twohalf">
              <label for="" style="width: 100%;">&nbsp;&nbsp;</label>
              <button type="button" class="btn btn-primary float-right" style="margin-left: 5px;" id="editBarang" onclick="editBarang()">Ubah</button>
              <button type="button" class="btn btn-secondary float-right" onclick="batalEdit()">Batal</button>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="reset()">Batal</button>
        <button type="button" class="btn btn-primary" onclick="add()">Simpan</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal add -->

<!-- start modal edit -->
<div class="modal fade" id="editKoreksiStock" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding-bottom: 5px; padding-top: 5px;">
        <h5 class="modal-title" id="exampleModalLabel">Ubah Koreksi Stock</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="reset('e')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="eid" required>
        <div class="row">
          <input type="hidden" id="eno_urut">
          <div class="col-onequarter">
            <label for="eno_bukti">No. Bukti</label>
            <input type="text" class="form-control" id="eno_bukti" disabled>
          </div>
          <div class="col-onequarter">
            <label for="etanggal">Tanggal</label>
            <input type="date" class="form-control" id="etanggal">
          </div>
          <div class="col-2">
            <label for="egudang">Gudang</label>
            <select class="form-control" id="egudang" onchange="loadBarang('e')">
              <option value="">-- Pilih Gudang --</option>
              @for ($i = 0; $i < count($gudang); $i++)
                <option value="{!! $gudang[$i]->kode !!}">{!! $gudang[$i]->kode !!} - {!! $gudang[$i]->nama !!}</option>
              @endfor
            </select>
          </div>
          <div class="col-4">
            <label for="eketerangan">Keterangan</label>
            <textarea id="eketerangan" class="form-control" rows="1"></textarea>
          </div>
        </div>
        <hr style="margin-top: 7px; margin-bottom: 7px;" />
        <div class="row">
          <div class="col-12 tableFixHead" style="height: 57vh;">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th style="width: 15%;">Kode Barang</th>
                  <th>Nama Barang</th>
                  <th style="width: 7%;">Qty Masuk</th>
                  <th style="width: 7%;">Qty Keluar</th>
                  <th style="width: 5%;">Satuan</th>
                  <th style="width: 7%;">Isi</th>
                  <th style="width: 7%;">Harga</th>
                  <th style="width: 7%;">Batch</th>
                </tr>
              </thead>
              <tbody id="ecart">

              </tbody>
            </table>
          </div>
        </div>
        <hr style="margin-top: 7px; margin-bottom: 7px;" />
        <div class="row form-group">
          <div class="col-12" id="etombolAddBarang">
            <div class="float-right">
              <button type="button" class="btn btn-sm btn-success" onclick="showDivAdd('e')" rel="tooltip" data-placement="bottom" title="Tambah" style="width: 30px;"><i class="fas fa-plus-circle"></i></button>
              <button type='button' class='btn btn-warning btn-sm' onclick='eeditCart()' rel="tooltip" data-placement="bottom" title="Ubah" style="width: 30px;"><i class='fas fa-pencil-alt'></i></button>
              <button type='button' class='btn btn-danger btn-sm' onclick='edeleteCart()' rel="tooltip" data-placement="bottom" title="Hapus" style="width: 30px;"><i class='fas fa-trash'></i></button>
              <button type='button' class='btn btn-secondary btn-sm' onclick='eshowBatch()' rel="tooltip" data-placement="bottom" title="Batch" style="width: 33px;"><i class="fas fa-archive"></i></button>
            </div>
          </div>
        </div>
        <div id="edivAdd" style="margin-top: -7px;">
          <div class="row form-group">
            <div class="col-1">
              <label for="emk">M/K</label>
              <select class="form-control" id="emk" onchange="disableHPP('e')">
                <option value="0">Masuk</option>
                <option value="1">Keluar</option>
              </select>
            </div>
            <div class="col-2">
              <label for="epilihjenis">Pilih Jenis </label>
              <select class="form-control" id="epilihjenis" onchange="loadBarangGdg('e')">
                <option value="-">Semua</option>
                @for ($i = 0; $i < count($grup); $i++)
                  <option value="{!! $grup[$i]->kode !!}">{!! $grup[$i]->kode !!} - {!! $grup[$i]->nama !!}</option>
                @endfor
              </select>
            </div>
            <div class="col-4">
              <label for="ebarang">Barang</label>
              <select class="form-control" id="ebarang" onchange="loadSatuan('e')">

              </select>
              <!-- <input type="text" id="ebarang" class="form-control"> -->
              <input type="hidden" id="ekbarang">
              <input type="hidden" id="ehbarang">
            </div>
            <input type="hidden" id="enama_brg" class="form-control" disabled required>
            <div class="col-1">
              <label for="eqty">Qty</label>
              <input type="text" id="eqty" class="form-control format-number text-right" placeholder="Kuantitas" value="0.00" required>
            </div>
            <div class="col-1">
              <label for="esatuan">Satuan</label>
              <select class="form-control" id="esatuan">

              </select>
            </div>
            <div class="col-onehalf">
              <label for="ehpp">Harga</label>
              <input type="text" id="ehpp" class="form-control format-number text-right" placeholder="Harga" value="0">
            </div>
            <div class="col-onehalf">
              <label for="" style="width: 100%;">&nbsp;&nbsp;</label>
              <button type="button" class="btn btn-primary float-right" style="margin-left: 5px;" id="eaddBarang" onclick="addBarang('e')">Tambah</button>
              <button type="button" class="btn btn-secondary float-right" onclick="batalAdd('e')">Batal</button>
            </div>
          </div>
        </div>
        <div id="edivEdit">
          <div class="row form-group">
            <input type="hidden" id="eeditrowcart">
            <div class="col-1">
              <label for="eeditmk">M/K</label>
              <select class="form-control" id="eeditmk" onchange="disableHPP('eedit')">
                <option value="0">Masuk</option>
                <option value="1">Keluar</option>
              </select>
            </div>
            <div class="col-5">
              <label for="eeditbarang">Barang</label>
              <input class="form-control" id="eeditbarang" disabled>
            </div>
            <!-- <div class="col-2">
              <label for="eeditnama_brg">Nama</label> -->
              <input type="hidden" id="eeditnama_brg" class="form-control" disabled required>
            <!-- </div> -->
            <div class="col-1">
              <label for="eeditqty">Qty</label>
              <input type="text" id="eeditqty" class="form-control format-number text-right" placeholder="Kuantitas" required>
            </div>
            <div class="col-1">
              <label for="eeditsatuan">Satuan</label>
              <select class="form-control" id="eeditsatuan">

              </select>
            </div>
            <div class="col-onehalf">
              <label for="eedithpp">Harga</label>
              <input type="text" id="eedithpp" class="form-control format-number text-right" placeholder="Harga" value="0">
            </div>
            <div class="col-twohalf">
              <label for="" style="width: 100%;">&nbsp;&nbsp;</label>
              <button type="button" class="btn btn-primary float-right" style="margin-left: 5px;" id="eeditBarang" onclick="editBarang('e')">Ubah</button>
              <button type="button" class="btn btn-secondary float-right" onclick="batalEdit('e')">Batal</button>
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
<!-- End modal edit -->

<!-- start modal detail -->
<div class="modal fade" id="detailKoreksiStock" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding-bottom: 5px; padding-top: 5px;">
        <h5 class="modal-title" id="exampleModalLabel">Detail Koreksi Stock</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" class="form-control" id="dno_urut">
          <div class="col-onequarter">
            <label for="dno_bukti">No. Bukti</label>
            <input type="text" class="form-control" id="dno_bukti" disabled>
          </div>
          <div class="col-onequarter">
            <label for="dtanggal">Tanggal</label>
            <input type="date" class="form-control" id="dtanggal" disabled>
          </div>
          <div class="col-2">
            <label for="dgudang">Gudang</label>
            <select class="form-control" id="dgudang" disabled>
              @for ($i = 0; $i < count($gudang); $i++)
                <option value="{!! $gudang[$i]->kode !!}">{!! $gudang[$i]->kode !!} - {!! $gudang[$i]->nama !!}</option>
              @endfor
            </select>
          </div>
          <div class="col-4">
            <label for="dketerangan">Keterangan</label>
            <textarea id="dketerangan" class="form-control" rows="1" disabled></textarea>
          </div>
        </div>
        <hr style="margin-top: 7px; margin-bottom: 7px;" />
        <div class="row">
          <div class="col-12 tableFixHead" style="height: 60vh;">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th style="width: 15%;">Kode Barang</th>
                  <th>Nama Barang</th>
                  <th style="width: 7%;">Qty Masuk</th>
                  <th style="width: 7%;">Qty Keluar</th>
                  <th style="width: 5%;">Satuan</th>
                  <th style="width: 7%;">Isi</th>
                  <th style="width: 7%;">Harga</th>
                  <th style="width: 7%;">Batch</th>
                </tr>
              </thead>
              <tbody id="dcart">

              </tbody>
            </table>
          </div>
        </div>
        <hr style="margin-top: 7px; margin-bottom: 7px;" />
        <div class="row form-group">
          <div class="col-12" id="tombolAddBarang">
            <div class="float-right">
              <button type='button' class='btn btn-secondary btn-sm' onclick='showDetBatch()' rel="tooltip" data-placement="bottom" title="Batch" style="width: 33px;"><i class="fas fa-archive"></i></button>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal detail -->

<!-- start modal detail batch -->
<div class="modal fade" id="detailBatch" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" style="min-width: 80% !important;" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding-bottom: 5px; padding-top: 5px;">
        <h5 class="modal-title" id="exampleModalLabel">Detail Batch</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Kode Barang</th>
                  <th>No. Batch</th>
                  <th>Expired Date</th>
                  <th>Qty Batch</th>
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

<!-- start modal add batch -->
<div class="modal fade" id="addBatch1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" style="min-width: 80% !important;" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding-bottom: 5px; padding-top: 5px;">
        <h5 class="modal-title" id="exampleModalLabel">Tambah Batch</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row form-group">
          <div class="col-12" id="tombolAddBatch">
            <div class="float-right">
              <button type="button" class="btn btn-sm btn-success" onclick="showAddBatch()" rel="tooltip" data-placement="bottom" title="Tambah" style="width: 30px;"><i class="fas fa-plus-circle"></i></button>
              <button type='button' class='btn btn-warning btn-sm' onclick='showeditBatch()' rel="tooltip" data-placement="bottom" title="Edit" style="width: 30px;"><i class='fas fa-pencil-alt'></i></button>
              <button type='button' class='btn btn-danger btn-sm' onclick='deleteBatch()' rel="tooltip" data-placement="bottom" title="Delete" style="width: 30px;"><i class='fas fa-trash'></i></button>
            </div>
          </div>
        </div>
        <div class="row form-group" id="divAddBatch">
          <input type="hidden" id="index_terima" disabled>
          <input type="hidden" id="isi_batch" disabled>
          <div class="col-threehalf">
            <label for="kode_brg_batch">Kode Barang</label>
            <input type="text" class="form-control" id="kode_brg_batch" disabled>
          </div>
          <div class="col-3">
            <label for="no_batch">No. Batch</label>
            <input type="text" class="form-control" id="no_batch">
          </div>
          <div class="col-2">
            <label for="exp">Expired Date</label>
            <input type="date" id="exp" class="form-control">
          </div>
          <div class="col-onehalf">
            <label for="qty_batch">Qty</label>
            <input type="text" id="qty_batch" class="form-control format-number text-right" value="0">
          </div>
          <div class="col-2">
            <label for="" style="width: 100%;">&nbsp;&nbsp;</label>
            <button type="button" class="btn btn-primary float-right" style="margin-left: 5px;" onclick="addBatch()">Tambah</button>
            <button type="button" class="btn btn-secondary float-right" onclick="batalAddBatch()">Batal</button>
          </div>
        </div>
        <div class="row form-group" id="divEditBatch">
          <input type="hidden" id="editrow" disabled>
          <div class="col-threehalf">
            <label for="editkode_brg_batch">Kode Barang</label>
            <input type="text" class="form-control" id="editkode_brg_batch" disabled>
          </div>
          <div class="col-3">
            <label for="editno_batch">No. Batch</label>
            <input type="text" class="form-control" id="editno_batch">
          </div>
          <div class="col-2">
            <label for="editexp">Expired Date</label>
            <input type="date" id="editexp" class="form-control">
          </div>
          <div class="col-onehalf">
            <label for="editqty_batch">Qty</label>
            <input type="text" id="editqty_batch" class="form-control format-number text-right" value="0">
          </div>
          <div class="col-2">
            <label for="" style="width: 100%;">&nbsp;&nbsp;</label>
            <button type="button" class="btn btn-primary float-right" style="margin-left: 5px;" onclick="editBatch()">Ubah</button>
            <button type="button" class="btn btn-secondary float-right" onclick="batalEditBatch()">Batal</button>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Kode Barang</th>
                  <th>No. Batch</th>
                  <th>Expired Date</th>
                  <th>Qty Batch</th>
                </tr>
              </thead>
              <tbody id="data_batch1">
                <tr>
                  <td colspan="4">Tidak ada batch.</td>
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

<!-- start modal edit batch -->
<div class="modal fade" id="editBatch1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" style="min-width: 80% !important;" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding-bottom: 5px; padding-top: 5px;">
        <h5 class="modal-title" id="exampleModalLabel">Edit Batch</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row form-group">
          <div class="col-12" id="etombolAddBatch">
            <div class="float-right">
              <button type="button" class="btn btn-sm btn-success" onclick="showAddBatch('e')" rel="tooltip" data-placement="bottom" title="Tambah" style="width: 30px;"><i class="fas fa-plus-circle"></i></button>
              <button type='button' class='btn btn-warning btn-sm' onclick='eshoweditBatch()' rel="tooltip" data-placement="bottom" title="Edit" style="width: 30px;"><i class='fas fa-pencil-alt'></i></button>
              <button type='button' class='btn btn-danger btn-sm' onclick='edeleteBatch()' rel="tooltip" data-placement="bottom" title="Delete" style="width: 30px;"><i class='fas fa-trash'></i></button>
            </div>
          </div>
        </div>
        <div class="row form-group" id="edivAddBatch">
          <input type="hidden" id="eindex_terima" disabled>
          <input type="hidden" id="eisi_batch" disabled>
          <div class="col-threehalf">
            <label for="ekode_brg_batch">Kode Barang</label>
            <input type="text" class="form-control" id="ekode_brg_batch" disabled>
          </div>
          <div class="col-3">
            <label for="eno_batch">No. Batch</label>
            <input type="text" class="form-control" id="eno_batch">
          </div>
          <div class="col-2">
            <label for="eexp">Expired Date</label>
            <input type="date" id="eexp" class="form-control">
          </div>
          <div class="col-onehalf">
            <label for="eqty_batch">Qty</label>
            <input type="text" id="eqty_batch" class="form-control format-number text-right" value="0">
          </div>
          <div class="col-2">
            <label for="" style="width: 100%;">&nbsp;&nbsp;</label>
            <button type="button" class="btn btn-primary float-right" style="margin-left: 5px;" onclick="addBatch('e')">Tambah</button>
            <button type="button" class="btn btn-secondary float-right" onclick="batalAddBatch('e')">Batal</button>
          </div>
        </div>
        <div class="row form-group" id="edivEditBatch">
          <input type="hidden" id="eeditrow" disabled>
          <div class="col-threehalf">
            <label for="eeditkode_brg_batch">Kode Barang</label>
            <input type="text" class="form-control" id="eeditkode_brg_batch" disabled>
          </div>
          <div class="col-3">
            <label for="eeditno_batch">No. Batch</label>
            <input type="text" class="form-control" id="eeditno_batch">
          </div>
          <div class="col-2">
            <label for="eeditexp">Expired Date</label>
            <input type="date" id="eeditexp" class="form-control">
          </div>
          <div class="col-onehalf">
            <label for="eeditqty_batch">Qty</label>
            <input type="text" id="eeditqty_batch" class="form-control format-number text-right" value="0">
          </div>
          <div class="col-2">
            <label for="" style="width: 100%;">&nbsp;&nbsp;</label>
            <button type="button" class="btn btn-primary float-right" style="margin-left: 5px;" onclick="editBatch('e')">Ubah</button>
            <button type="button" class="btn btn-secondary float-right" onclick="batalEditBatch('e')">Batal</button>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Kode Barang</th>
                  <th>No. Batch</th>
                  <th>Expired Date</th>
                  <th>Qty Batch</th>
                </tr>
              </thead>
              <tbody id="edata_batch1">
                <tr>
                  <td colspan="4">Tidak ada batch.</td>
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

<!-- start modal add batch -->
<div class="modal fade" id="addBatch2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                  <th>Koreksi</th>
                </tr>
              </thead>
              <tbody id="data_batch2">
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

<!-- start modal edit batch -->
<div class="modal fade" id="editBatch2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                  <th>Koreksi</th>
                </tr>
              </thead>
              <tbody id="edata_batch2">
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
  var cart = [], ecart = [], batch = [], ebatch = [], detbatch = [];
  var g_id = "", g_nb = "", g_iddet = "", g_idb = "";
  // start document ready
	$(document).ready(function(){
    loadBarang();
    loadAkses();
    restrictedDate();
    $(".format-number").autoNumeric('init', {mDec: '0'});
    $("#divAdd").hide();
    $("#divEdit").hide();
    $("#edivAdd").hide();
    $("#edivEdit").hide();
    $("#divAddBatch").hide();
    $("#divEditBatch").hide();
    $("#edivAddBatch").hide();
    $("#edivEditBatch").hide();
    resetDisable();
    resetDisable('e');
    loadCart();
    var table = $('#tabel_koreksi').DataTable({"lengthMenu": [[-1, 10, 25, 50, 100], ["All", 10, 25, 50, 100]]});
    table.on('search', function () {
      $('#koreksi_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_nb = "", g_iddet = "", g_idb = "";
    } );
    table.on('order', function () {
      $('#koreksi_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_nb = "", g_iddet = "", g_idb = "";
    } );

    var canPass = false;
    $("#barang").autocomplete({
      minLength: 0,
      delay: 0,
      appendTo: '#addKoreksiStock',
      position: { collision: "flip" },
      source: function(request, response) {
        $.getJSON("{!! url('searchBarangGudang') !!}", {
          term: request.term,
          grup: $("#pilihjenis").val(),
          gudang: $("#gudang").val()
        }, function(data) {
          response(data);
        });
      },
      search: function( event, ui ) {
        if (!canPass) { event.preventDefault(); } else { canPass = false; }
      },
      focus: function(event, ui) {
        // prevent autocomplete from updating the textbox
        event.preventDefault();
      },
      select: function(event, ui) {
        // prevent autocomplete from updating the textbox
        event.preventDefault(); $('#barang').val(ui.item.kode); $('#kbarang').val(ui.item.kode); $('#hbarang').val(ui.item.nama); loadSatuan();
      },
      response: function(event,ui) {
        if (ui.content.length == 1) {
          ui.item = ui.content[0]; $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui); $(this).autocomplete('close');
        } else {
          $('#kbarang').val(""); $('#hbarang').val(""); loadSatuan();
        }
      }
    }).keypress(function (e) {
      if (e.keyCode === 13) { canPass = true; $("#barang").autocomplete("search", $("#barang").val()); }
    });

    var ecanPass = false;
    $("#ebarang").autocomplete({
      minLength: 0,
      delay: 0,
      appendTo: '#editKoreksiStock',
      position: { collision: "flip" },
      source: function(request, response) {
        $.getJSON("{!! url('searchBarangGudang') !!}", {
          term: request.term,
          grup: $("#epilihjenis").val(),
          gudang: $("#egudang").val()
        }, function(data) {
          response(data);
        });
      },
      search: function( event, ui ) {
        if (!ecanPass) { event.preventDefault(); } else { ecanPass = false; }
      },
      focus: function(event, ui) {
        // prevent autocomplete from updating the textbox
        event.preventDefault();
      },
      select: function(event, ui) {
        // prevent autocomplete from updating the textbox
        event.preventDefault(); $('#ebarang').val(ui.item.kode); $('#ekbarang').val(ui.item.kode); $('#ehbarang').val(ui.item.nama); loadSatuan('e');
      },
      response: function(event,ui) {
        if (ui.content.length == 1) {
          ui.item = ui.content[0]; $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui); $(this).autocomplete('close');
        } else {
          $('#ekbarang').val(""); $('#ehbarang').val(""); loadSatuan('e');
        }
      }
    }).keypress(function (e) {
      if (e.keyCode === 13) { ecanPass = true; $("#ebarang").autocomplete("search", $("#ebarang").val()); }
    });
	});
  // end document ready

  // start refresh tabel
  function loadAll() {
    $('#tabel_koreksi').DataTable().destroy();
    $('#koreksi_data > tr').each(function() {
      $(this).css('background-color', '');
    });
    g_id = "", g_nb = "", g_iddet = "", g_idb = "";
    var _token = $("#_token").val();
    var akses = "";
    $.ajax({
      url     : "{!! url('getAksesByMenu') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        menu : '/koreksistock'
      },
      success : function(result) {
        akses = result;
      }
    });
    $.ajax({
      url     : "{!! url('loadAllKoreksiStock') !!}",
      type    : "GET",
      async   : false,
      success : function(result) {
        if (result.length > 0) {
          var str = "";
          for (var i = 0; i < result.length; i++) {
            str = str + '<tr id="' + result[i].id + '-tr" onclick="select(' + result[i].id + ', \'' + result[i].no_bukti + '\')">\
              <td><button type="button" class="btn btn-secondary btn-sm btn-opsi" onclick="showDet(' + result[i].id + ')"><i class="fas fa-info-circle"></i></button></td>\
              <td>' + result[i].no_bukti + '</td>\
              <td>' + format_date(result[i].tanggal) + '</td>\
              <td>' + result[i].gudang + ' - ' + result[i].nama + '</td>';
            if (result[i].auth_1 == 1) {
              str = str + '<td><input type="checkbox" class="form-control check_auth" disabled checked></td>';
            } else {
              str = str + '<td><input type="checkbox" class="form-control check_auth" id="'+ result[i].id +'-auth" onchange="changeAuth('+ result[i].id +', \''+result[i].no_bukti+'\')"></td>'
            }
              str = str + '<td>' + result[i].auth_user_1 + '</td>';
            if (result[i].auth_date_1 != null) {
              str = str + '<td>' + format_date(result[i].auth_date_1) + '</td>';
            } else {
              str = str + "<td></td>";
            }
            if (result[i].batal == 1) {
              str = str + '<td><input type="checkbox" class="form-control check_batal" disabled checked></td>';
            } else {
              str = str + '<td><input type="checkbox" class="form-control check_batal" id="'+ result[i].id +'-batal" onchange="changeBatal('+ result[i].id +', \''+result[i].no_bukti+'\')"></td>'
            }
            str = str + '<td>' + result[i].batal_user + '</td>';
            if (result[i].batal_date != null) {
              str = str + '<td>' + format_date(result[i].batal_date) + '</td>';
            } else {
              str = str + "<td></td>";
            }
            str = str + '</tr>';
          }
          $('#koreksi_data').html(str);
        }
        else {
          $('#koreksi_data').html('<tr>\
            <td colspan="10">Tidak ada data koreksi stock ditemukan.</td>\
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
        loadAkses();
      }
    });
    var table = $('#tabel_koreksi').DataTable({"lengthMenu": [[-1, 10, 25, 50, 100], ["All", 10, 25, 50, 100]]});
    table.on('search', function () {
      $('#koreksi_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_nb = "", g_iddet = "", g_idb = "";
    } );
    table.on('order', function () {
      $('#koreksi_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_nb = "", g_iddet = "", g_idb = "";
    } );
  }
  // end refresh table

  function select(_id, _nb) {
    $('#koreksi_data > tr').each(function() {
      $(this).css('background-color', '');
    });
    $("#"+_id+"-tr").css('background-color', 'gold');
    g_id = _id; g_nb = _nb;
  }

  function selectdet(_row, name = "") {
    $('#'+name+'cart > tr').each(function() {
      $(this).css('background-color', '');
    });
    $("#"+name+_row+"-trdet").css('background-color', 'gold');
    g_iddet = _row;
  }

  function selectdetbatch(_row, name = "") {
    $('#'+name+'data_batch1 > tr').each(function() {
      $(this).css('background-color', '');
    });
    $("#"+name+_row+"-trb").css('background-color', 'gold');
    g_idb = _row;
  }

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
  }

  function showAdd() {
    var no_urut = generateNoUrut();
    var no_bukti = generateNoBukti();
    $("#no_urut").val(no_urut);
    $("#no_bukti").val(no_bukti);
    cart = [];
    loadCart();
    $("#addKoreksiStock").modal('toggle');
  }

  function loadAkses() {
    var _token = $("#_token").val();
    $.ajax({
      url     : "{!! url('getAksesByMenu') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        menu : '/koreksistock'
      },
      success : function(result) {
        if (result.otorisasi1 == 0) {
          $(".check_auth").prop("disabled", true);
          $(".check_batal").prop("disabled", true);
        }
      }
    });
  }

  function changeAuth(id, nomor_bukti) {
    alertify.confirm('Otorisasi Koreksi Stock', 'Apakah yakin ingin mengotorisasi Koreksi Stock dengan nomor bukti ' + nomor_bukti + ' ?',
      function(){
        var _token = $("#_token").val();
        $.ajax({
          url     : "{!! url('authKoreksiStock') !!}",
          type    : "POST",
          async   : false,
          data    : {
            _token : _token,
            id : id
          },
          success : function(result) {
            loadAll();
            alertify.success('Koreksi Stock dengan nomor bukti '+nomor_bukti+' telah diotorisasi.');
          }
        });
    },function(){
      $("#"+id+"-auth").prop('checked', false);
    });
  }

  function changeBatal(id, nomor_bukti) {
    alertify.confirm('Pembatalan Koreksi Stock', 'Apakah yakin ingin membatalkan Koreksi Stock dengan nomor bukti ' + nomor_bukti + ' ?',
      function(){
        var _token = $("#_token").val();
        $.ajax({
          url     : "{!! url('batalKoreksiStock') !!}",
          type    : "POST",
          async   : false,
          data    : {
            _token : _token,
            id : id
          },
          success : function(result) {
            if (result == 1) {
              loadAll();
              alertify.success('Koreksi Stock dengan nomor bukti '+nomor_bukti+' telah dibatalkan.');
            }
          }
        });
    },function(){
      $("#"+id+"-batal").prop('checked', false);
    });
  }

  function generateNoBukti() {
    var res = "";
    $.ajax({
      url     : "{!! url('generateNomorBuktiKoreksiStock') !!}",
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
      url     : "{!! url('generateNomorUrutKoreksiStock') !!}",
      type    : "GET",
      async   : false,
      success : function(result) {
        res = result;
      }
    });
    return res;
  }

  function loadBarang(name = "") {
    var _token = $("#_token").val();
    var _gudang = $("#"+name+"gudang").val();
    if (_gudang != "") {
      if (name == "") { cart = []; loadCart(); batch = []; }
      else { ecart = []; loadCart('e'); ebatch = [];  }
      $("#"+name+"barang").prop("disabled", false);
      $("#"+name+"nama_brg").val('');
      $("#"+name+"editnama_brg").val('');
      $("#"+name+"satuan").html('');
      $("#"+name+"satuan").prop('disabled', true);
      $("#"+name+"qty").val('0');
      $("#"+name+"qty").prop('disabled', true);
    } else {
      if (name == "") { cart = []; loadCart(); }
      else { ecart = []; loadCart('e'); }
      $("#"+name+"barang").prop("disabled", true);
      $("#"+name+"editbarang").prop("disabled", true);
      $("#"+name+"nama_brg").val('');
      $("#"+name+"editnama_brg").val('');
      $("#"+name+"satuan").html('');
      $("#"+name+"satuan").prop('disabled', true);
      $("#"+name+"qty").val('0');
      $("#"+name+"qty").prop('disabled', true);
    }
    loadBarangGdg(name);
  }

  function showDivAdd(name = "") {
    batalEdit(name); loadBarangGdg(name);
    $("#"+ name + "divAdd").show();
    $("#"+ name + "tombolAddBarang").hide();
    $("#"+ name + "barang").focus();
  }

  function batalAdd(name = "") {
    $("#" + name + "divAdd").hide();
    $("#" + name + "tombolAddBarang").show();
  }

  function resetDisable(name = "") {
    $("#" + name + "qty").val('0');
    $("#" + name + "qty").prop('disabled', true);
    $("#" + name + "satuan").html('');
    $("#" + name + "satuan").prop('disabled', true);
    $("#" + name + "addBarang").prop('disabled', true);
  }

  function reset(name = "") {
    restrictedDate();
    if (name == "") {
      $("#gudang").val("");
      $("#keterangan").val("");
      $("#qty").val('0');
      $("#qty").prop('disabled', true);
      $("#satuan").html('');
      $("#satuan").prop('disabled', true);
      $("#addBarang").prop('disabled', true);
      cart = [];
      loadCart();
      batalAdd();
    } else {
      release(g_nb);
      ecart = [];
      loadCart(name);
      resetDisable(name);
      batalAdd(name);
    }
    batalEdit(name);
  }

  function loadBarangGdg(name = "") {
    resetDisable(name);
    var _token = $("#_token").val();
    var _gudang = $("#"+name+"gudang").val();
    var pilihjenis = $("#"+name+"pilihjenis").val();
    var str = '<option value="">-- Pilih Barang --</option>';
    $.ajax({
      url     : "{!! url('getBarangByGrupGdgPB') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        kode : pilihjenis,
        gudang : _gudang
      },
      success : function(result) {
        for (var i = 0; i < result.length; i++) {
          str += "<option value='" + result[i].kode + "'>" + result[i].kode + " - " + result[i].nama + "</option>";
        }
      }
    });
    $("#" + name + "barang").html(str);
    $("#" + name + "barang").prop('disabled', false);
    $("#" + name + "nama_brg").prop('disabled', true);
    $("#" + name + "nama_brg").val("");
    // $("#qty").prop('disabled', false);
    // $("#satuan").prop('disabled', false);
    // $("#keterangan").prop('disabled', false);
    // $("#addBarang").prop('disabled', false);
  }

  function loadSatuan(name = "") {
    var str = "";
    var _token = $("#_token").val();
    var kodebrg = $("#" + name + "barang").val();
    $("#" + name + "kbarang").val(kodebrg);
    var _kode = $("#" + name + "kbarang").val();
    if (_kode != ""){
      $.ajax({
        url     : "{!! url('getBarangByKode') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          kode : _kode
        },
        success : function(result) {
          str += "<option value='" + result.satuan1 + " - " + result.isi1 + "'>" + result.satuan1 + " - " + result.isi1 + "</option>";
          if (result.satuan2 != null && result.satuan2 != "-" && result.satuan2 != "") {
            str += "<option value='" + result.satuan2 + " - " + result.isi2 + "'>" + result.satuan2 + " - " + result.isi2 + "</option>";
          }
          if (result.satuan3 != null && result.satuan3 != "-" && result.satuan3 != "") {
            str += "<option value='" + result.satuan3 + " - " + result.isi3 + "'>" + result.satuan3 + " - " + result.isi3 + "</option>";
          }
          $("#" + name + "satuan").html(str);
          $("#" + name + "qty").val('0');
          $("#" + name + "qty").prop('disabled', false);
          $("#" + name + "satuan").prop('disabled', false);
          $("#" + name + "addBarang").prop('disabled', false);
          $("#" + name + "nama_brg").val(result.nama);
        }
      });
    } else {
      $("#" + name + "qty").val('0');
      $("#" + name + "qty").prop('disabled', true);
      $("#" + name + "satuan").html('');
      $("#" + name + "satuan").prop('disabled', true);
      $("#" + name + "addBarang").prop('disabled', true);
      $("#" + name + "nama_brg").val("");
      $("#" + name + "nama_brg").prop('disabled', true);
    }
    var nmbrg = $("#" + name + "nama_brg").val();
    $("#" + name + "hbarang").val(nmbrg);
  }

  function loadEditSatuan(_kode, name = "") {
    var str = "";
    var _token = $("#_token").val();
    $.ajax({
      url     : "{!! url('getBarangByKode') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        kode : _kode
      },
      success : function(result) {
        str += "<option value='" + result.satuan1 + " - " + result.isi1 + "'>" + result.satuan1 + " - " + result.isi1 + "</option>";
        if (result.satuan2 != null && result.satuan2 != "-" && result.satuan2 != "") {
          str += "<option value='" + result.satuan2 + " - " + result.isi2 + "'>" + result.satuan2 + " - " + result.isi2 + "</option>";
        }
        if (result.satuan3 != null && result.satuan3 != "-" && result.satuan3 != "") {
          str += "<option value='" + result.satuan3 + " - " + result.isi3 + "'>" + result.satuan3 + " - " + result.isi3 + "</option>";
        }
        $("#" + name + "editsatuan").html(str);
        if (result.grup == "JS") {
          $("#" + name + "editnama_brg").prop('disabled', false);
        } else {
          $("#" + name + "editnama_brg").prop('disabled', true);
        }
      }
    });

  }

  function disableHPP(name = "") {
    $("#" + name + "hpp").val("0");
    if ($("#" + name + "mk").val() == 0) {
      $("#" + name + "hpp").prop('disabled', false);
    } else {
      $("#" + name + "hpp").prop('disabled', true);
    }
  }

  function addBarang(name = "") {
    if (name == "") {
      if ($("#qty").val() != "" && $("#kbarang").val() != "" && $("#qty").val().split(".")[0].replace(/,/g, "") != 0) {
        var cek = 0;
        for (var i = 0; i < cart.length; i++) {
          // if (cart[i][0] == $("#barang").val().split(" - ")[0] && cart[i][4] == $("#satuan").val().split(" - ")[0] && cart[i][6] == $("#mk").val()) {
          //   if ($("#mk").val() == 0) {
          //     cart[i][2] = numberWithCommas(parseInt(cart[i][2].replace(/,/g, "")) + parseInt($("#qty").val().split(".")[0].replace(/,/g, "")));
          //   } else {
          //     cart[i][3] = numberWithCommas(parseInt(cart[i][3].replace(/,/g, "")) + parseInt($("#qty").val().split(".")[0].replace(/,/g, "")));
          //   }
          //   cek = 1;
          //   break;
          // }
          if (cart[i][0] == $("#kbarang").val()) {
            cek = 2;
            alertify.warning("Barang sudah ada");
            return;
          }
        }
        if (cek == 0) {
          var temp = [];
          var qty = 0;
          // kode
          temp.push($("#kbarang").val());
          // nama
          temp.push($("#hbarang").val());
          if ($("#mk").val() == 0) {
            temp.push(numberWithCommas($("#qty").val().split(".")[0].replace(/,/g, "")));
            temp.push(numberWithCommas(0));
            qty = toInteger($("#qty").val());
          } else {
            temp.push(numberWithCommas(0));
            temp.push(numberWithCommas($("#qty").val().split(".")[0].replace(/,/g, "")));
            qty = toInteger($("#qty").val());

            var _token = $("#_token").val();
            $.ajax({
              url     : "{!! url('getBatchBarang') !!}",
              type    : "POST",
              async   : false,
              data    : {
                _token : _token,
                kode_barang : $("#barang").val().split(" - ")[0],
                gudang : $("#gudang").val()
              },
              success : function(result) {
                for (var j = 0; j < result.length; j++) {
                  var dummy = [];
                  var qty = result[j].qty / toInteger($("#satuan").val().split(" - ")[1]);
                  if (qty > 0) {
                    dummy.push(result[j].no_batch); //0
                    dummy.push(result[j].kode_barang); //1
                    dummy.push(result[j].tanggal); //2
                    dummy.push(numberWithCommas(round(qty, 1))); //3
                    dummy.push(numberWithCommas(0)); //4
                    dummy.push(i); //5
                    dummy.push(toInteger($("#satuan").val().split(" - ")[1])); //6
                    dummy.push(-1); //7
                    batch.push(dummy);
                  }
                }
              }
            });
          }
          // satuan
          temp.push($("#satuan").val().split(" - ")[0]);
          // isi
          temp.push($("#satuan").val().split(" - ")[1]);
          //masuk keluar
          temp.push($("#mk").val());
          temp.push(qty);
          temp.push($("#hpp").val());
          cart.push(temp);
        }
        $("#barang").val("");
        $("#kbarang").val("");
        $("#hbarang").val("");
        $("#hpp").val("0");
        loadCart();
        loadSatuan();
        $("#"+ name + "barang").focus();
      }
      else {
        alertify.warning('Kuantitas tidak boleh 0.');
      }
    } else {
      if ($("#eqty").val() != "" && $("#ekbarang").val() != "" && $("#eqty").val().split(".")[0].replace(/,/g, "") != 0) {
        var cek = 0;
        for (var i = 0; i < ecart.length; i++) {
          // if (ecart[i][0] == $("#ebarang").val().split(" - ")[0] && ecart[i][4] == $("#esatuan").val().split(" - ")[0] && ecart[i][6] == $("#emk").val()) {
          //   if ($("#emk").val() == 0) {
          //     ecart[i][2] = numberWithCommas(parseInt(ecart[i][2].replace(/,/g, "")) + parseInt($("#eqty").val().split(".")[0].replace(/,/g, "")));
          //   } else {
          //     ecart[i][3] = numberWithCommas(parseInt(ecart[i][3].replace(/,/g, "")) + parseInt($("#eqty").val().split(".")[0].replace(/,/g, "")));
          //   }
          //   cek = 1;
          //   break;
          // }
          if (ecart[i][0] == $("#ekbarang").val()) {
            cek = 2;
            alertify.warning("Barang sudah ada");
            return;
          }
        }
        if (cek == 0) {
          var temp = [];
          // kode
          temp.push($("#ekbarang").val());
          // nama
          temp.push($("#ehbarang").val());
          if ($("#emk").val() == 0) {
            temp.push(numberWithCommas($("#eqty").val().split(".")[0].replace(/,/g, "")));
            temp.push(numberWithCommas(0));
          } else {
            temp.push(numberWithCommas(0));
            temp.push(numberWithCommas($("#eqty").val().split(".")[0].replace(/,/g, "")));
          }
          // satuan
          temp.push($("#esatuan").val().split(" - ")[0]);
          // isi
          temp.push($("#esatuan").val().split(" - ")[1]);
          //masuk keluar
          temp.push($("#emk").val());
          temp.push(toInteger($("#eqty").val()));
          temp.push($("#ehpp").val());
          ecart.push(temp);
        }
        $("#ebarang").val("");
        $("#ekbarang").val("");
        $("#ehbarang").val("");
        $("#ehpp").val("0");
        loadCart('e');
        loadSatuan('e');
        $("#"+ name + "barang").focus();
      }
      else {
        alertify.warning('Kuantitas tidak boleh 0.');
      }
    }
  }

  function loadCart(name = "") {
    g_iddet = "";
    $('#'+name+'cart > tr').each(function() {
      $(this).css('background-color', '');
    });
    $("#" + name + "cart").html("");
    var str = "";
    if (name == ""){
      if (cart.length == 0) {
        str = "<tr><td colspan='8'>Belum ada barang</td></tr>";
      }
      else {
        for (var i = 0; i < cart.length; i++) {
          str += "<tr id='" + i + "-trdet' onclick='selectdet(" + i + ")'>\
            <td style='padding: 3px;'>" + cart[i][0] + "</td>\
            <td style='padding: 3px;'>" + cart[i][1] + "</td>\
            <td style='padding: 3px; text-align: right;'>" + cart[i][2] + "</td>\
            <td style='padding: 3px; text-align: right;' >" + cart[i][3] + "</td>\
            <td style='padding: 3px;'>" + cart[i][4] + "</td>\
            <td style='padding: 3px; text-align: right;'>" + cart[i][5] + "</td>\
            <td style='padding: 3px; text-align: right;'>" + cart[i][8] + "</td>\
            <td style='padding: 3px; text-align: right;'><span id='" + i + "-jmlbatch'></span></td></tr>";
        }
      }
    } else {
      if (ecart.length == 0) {
        str = "<tr><td colspan='8'>Belum ada barang</td></tr>";
      }
      else {
        for (var i = 0; i < ecart.length; i++) {
          str += "<tr id='e" + i + "-trdet' onclick='selectdet(" + i + ", \"e\")'>\
            <td style='padding: 3px;'>" + ecart[i][0] + "</td>\
            <td style='padding: 3px;'>" + ecart[i][1] + "</td>\
            <td style='padding: 3px; text-align: right;'>" + ecart[i][2] + "</td>\
            <td style='padding: 3px; text-align: right;'>" + ecart[i][3] + "</td>\
            <td style='padding: 3px;'>" + ecart[i][4] + "</td>\
            <td style='padding: 3px; text-align: right;'>" + ecart[i][5] + "</td>\
            <td style='padding: 3px; text-align: right;'>" + ecart[i][8] + "</td>\
            <td style='padding: 3px; text-align: right;'><span id='e" + i + "-jmlbatch'></span></td></tr>";
        }
      }
    }
    $("#" + name + "cart").html(str);
    calculateBatch(name);
  }

  function calculateBatch(name = "") {
    if (name == "") {
      var temp = [];
      for (var i = 0; i < cart.length; i++) {
        temp.push(0);
      }
      for (var i = 0; i < batch.length; i++) {
        temp[batch[i][5]] += parseInt(batch[i][4].replace(/,/g, ""));
      }
      for (var i = 0; i < temp.length; i++) {
        $("#"+name+i+"-jmlbatch").html(numberWithCommas(temp[i]));
      }
    } else {
      var temp = [];
      for (var i = 0; i < ecart.length; i++) {
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

  function editCart() {
    if (g_iddet === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    var i = g_iddet;
    batalAdd();
    $("#divEdit").show();
    $("#editrowcart").val(i);
    $("#editbarang").val(cart[i][0] + " - " + cart[i][1]);
    $("#editnama_brg").val(cart[i][1]);
    if (cart[i][6] == 0) {
      $("#editqty").val(cart[i][2]);
    } else {
      $("#editqty").val(cart[i][3]);
    }
    loadEditSatuan(cart[i][0]);
    $("#editsatuan").val(cart[i][4] + " - " + cart[i][5]);
    $("#editmk").val(cart[i][6]);
    $("#edithpp").val(cart[i][8]);
    $("#tombolAddBarang").hide();
  }

  function eeditCart() {
    if (g_iddet === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    var i = g_iddet;
    batalAdd('e');
    $("#edivEdit").show();
    $("#eeditrowcart").val(i);
    $("#eeditbarang").val(ecart[i][0] + " - " + ecart[i][1]);
    $("#eeditnama_brg").val(ecart[i][1]);
    if (ecart[i][6] == 0) {
      $("#eeditqty").val(ecart[i][2]);
    } else {
      $("#eeditqty").val(ecart[i][3]);
    }
    loadEditSatuan(ecart[i][0], 'e');
    $("#eeditsatuan").val(ecart[i][4] + " - " + ecart[i][5]);
    $("#eeditmk").val(ecart[i][6]);
    $("#eedithpp").val(ecart[i][8]);
    $("#etombolAddBarang").hide();
  }

  function batalEdit(name = "") {
    $("#" + name + "divEdit").hide();
    $("#" + name + "tombolAddBarang").show();
  }

  function editBarang(name = "") {
    var i = $("#" + name + "editrowcart").val();
    if (name == "") {
      var barang = cart[i][0];
      var gudang = $("#gudang").val();
      var qty = 0;

      for (var j = 0; j < batch.length; j++) {
        if (batch[j][5] == i) {
          batch.splice(j, 1);
          j--;
        }
      }
      // for (var j = 0; j < cart.length; j++) {
      //   if (j != i) {
      //     if (barang == cart[j][0] && $("#" + name + "editsatuan").val().split(" - ")[0] == cart[j][4] && $("#" + name + "editmk").val() == cart[j][6]) {
      //       if ($("#" + name + "editmk").val() == 0) {
      //         cart[j][2] = numberWithCommas(toInteger(cart[j][2]) + toInteger($("#" + name + "editqty").val()));
      //       } else {
      //         cart[j][3] = numberWithCommas(toInteger(cart[j][3]) + toInteger($("#" + name + "editqty").val()));
      //       }
      //       cart.splice(i, 1);
      //       cek = 1; break;
      //     }
      //   }
      // }
      // if (cek == 0) {
      if ($("#" + name + "editmk").val() == 0) {
        cart[i][2] = $("#" + name + "editqty").val();
        cart[i][3] = numberWithCommas(0);
        qty = toInteger($("#" + name + "editqty").val());
      } else {
        cart[i][2] = numberWithCommas(0);
        cart[i][3] = $("#" + name + "editqty").val();
        qty = toInteger($("#" + name + "editqty").val());

        var _token = $("#_token").val();
        $.ajax({
          url     : "{!! url('getBatchBarang') !!}",
          type    : "POST",
          async   : false,
          data    : {
            _token : _token,
            kode_barang : barang,
            gudang : gudang
          },
          success : function(result) {
            for (var j = 0; j < result.length; j++) {
              var dummy = [];
              var qty = result[j].qty / toInteger($("#" + name + "editsatuan").val().split(" - ")[1]);
              if (qty > 0) {
                dummy.push(result[j].no_batch); //0
                dummy.push(result[j].kode_barang); //1
                dummy.push(result[j].tanggal); //2
                dummy.push(numberWithCommas(round(qty, 1))); //3
                dummy.push(numberWithCommas(0)); //4
                dummy.push(i); //5
                dummy.push(toInteger($("#" + name + "editsatuan").val().split(" - ")[1])); //6
                dummy.push(-1); //7
                batch.push(dummy);
              }
            }
          }
        });
      }
      cart[i][4] = $("#" + name + "editsatuan").val().split(" - ")[0];
      cart[i][5] = $("#" + name + "editsatuan").val().split(" - ")[1];
      cart[i][6] = $("#" + name + "editmk").val();
      cart[i][7] = qty;
      cart[i][8] = $("#" + name + "edithpp").val();
      // }
    } else {
      var no_bukti = $("#eno_bukti").val();
      var barang = ecart[i][0];
      var gudang = $("#egudang").val();
      var qty = 0;

      for (var j = 0; j < ebatch.length; j++) {
        if (ebatch[j][5] == i) {
          ebatch.splice(j, 1);
          j--;
        }
      }
      // for (var j = 0; j < ecart.length; j++) {
      //   if (j != i) {
      //     if (barang == ecart[j][0] && $("#" + name + "editsatuan").val().split(" - ")[0] == ecart[j][4] && $("#" + name + "editmk").val() == ecart[j][6]) {
      //       if ($("#" + name + "editmk").val() == 0) {
      //         ecart[j][2] = numberWithCommas(toInteger(ecart[j][2]) + toInteger($("#" + name + "editqty").val()));
      //       } else {
      //         ecart[j][3] = numberWithCommas(toInteger(ecart[j][3]) + toInteger($("#" + name + "editqty").val()));
      //       }
      //       ecart.splice(i, 1);
      //       cek = 1; break;
      //     }
      //   }
      // }
      // if (cek == 0) {
      if ($("#" + name + "editmk").val() == 0) {
        ecart[i][2] = $("#" + name + "editqty").val();
        ecart[i][3] = numberWithCommas(0);
        qty = toInteger($("#" + name + "editqty").val());
      } else {
        ecart[i][2] = numberWithCommas(0);
        ecart[i][3] = $("#" + name + "editqty").val();
        qty = toInteger($("#" + name + "editqty").val());

        var _token = $("#_token").val();
        $.ajax({
          url     : "{!! url('getBatchBarangBAP') !!}",
          type    : "POST",
          async   : false,
          data    : {
            _token : _token,
            kode_barang : barang,
            no_bukti : no_bukti,
            gudang : gudang
          },
          success : function(result) {
            for (var j = 0; j < result.length; j++) {
              var dummy = [];
              var qty = result[j].qty / toInteger($("#" + name + "editsatuan").val().split(" - ")[1]);
              dummy.push(result[j].no_batch); //0
              dummy.push(result[j].kode_barang); //1
              dummy.push(result[j].tanggal); //2
              dummy.push(numberWithCommas(round(qty, 1))); //3
              dummy.push(numberWithCommas(0)); //4
              dummy.push(i); //5
              dummy.push(toInteger($("#" + name + "editsatuan").val().split(" - ")[1])); //6
              dummy.push(-1); //7
              ebatch.push(dummy);
            }
          }
        });
      }
      ecart[i][4] = $("#" + name + "editsatuan").val().split(" - ")[0];
      ecart[i][5] = $("#" + name + "editsatuan").val().split(" - ")[1];
      ecart[i][6] = $("#" + name + "editmk").val();
      ecart[i][7] = qty;
      ecart[i][8] = $("#" + name + "edithpp").val();
      // }
    }
    loadCart(name);
    batalEdit(name);
  }

  function deleteCart() {
    if (g_iddet === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    var i = g_iddet;
    for (var j = 0; j < batch.length; j++) {
      if (batch[j][5] == i) { batch.splice(j, 1); j--; }
    }
    for (var j = 0; j < batch.length; j++) {
      if (batch[j][5] > i) { batch[j][5] = batch[j][5] - 1; }
    }
    cart.splice(i, 1);
    loadCart();
  }

  function edeleteCart() {
    if (g_iddet === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    var i = g_iddet;
    for (var j = 0; j < ebatch.length; j++) {
      if (ebatch[j][5] == i) { ebatch.splice(j, 1); j--; }
    }
    for (var j = 0; j < ebatch.length; j++) {
      if (ebatch[j][5] > i) { ebatch[j][5] = ebatch[j][5] - 1; }
    }
    ecart.splice(i, 1);
    loadCart('e');
  }

  function showBatch() {
    if (g_iddet === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    var i = g_iddet;
    if (cart[i][6] == 0) {
      $("#index_terima").val(i);
      $("#isi_batch").val(toInteger(cart[i][5]));
      $("#kode_brg_batch").val(cart[i][0]);
      $("#editkode_brg_batch").val(cart[i][0]);
      loadBatch1(i);
      $("#addBatch1").modal('toggle');
    } else {
      loadBatch2(i);
      $("#index_retur").val(i);
      $("#addBatch2").modal('toggle');
    }
  }

  function eshowBatch() {
    if (g_iddet === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    var i = g_iddet;
    if (ecart[i][6] == 0) {
      $("#eindex_terima").val(i);
      $("#eisi_batch").val(toInteger(ecart[i][5]));
      $("#ekode_brg_batch").val(ecart[i][0]);
      $("#eeditkode_brg_batch").val(ecart[i][0]);
      loadBatch1(i, 'e');
      $("#editBatch1").modal('toggle');
    } else {
      loadBatch2(i, 'e');
      $("#eindex_retur").val(i);
      $("#editBatch2").modal('toggle');
    }
  }

  function showAddBatch(name = "") {
    if (name == "") {
      $("#tombolAddBatch").hide();
      $("#divAddBatch").show();
    } else {
      $("#etombolAddBatch").hide();
      $("#edivAddBatch").show();
    }
  }

  function loadBatch1(index_terima, name = "") {
    $('#'+name+'data_batch > tr').each(function() {
      $(this).css('background-color', '');
    });
    g_idb= "";
    if (name == "") {
      var cek = 0;
      var str = "";
      for (var i = 0; i < batch.length; i++) {
        if (batch[i][5] == index_terima) {
          cek += 1;
          str += '<tr id="' + i + '-trb" onclick="selectdetbatch(' + i + ')"><td>' + batch[i][1] + "</td>\
            <td>" + batch[i][0] + "</td>\
            <td>" + format_date(batch[i][2]) + "</td>\
            <td style='text-align: right;'>" + batch[i][4] + "</td></tr>";
        }
      }
      if (cek == 0) {
        $("#data_batch1").html('<tr><td colspan="5">Tidak ada batch.</td></tr>');
      } else {
        $("#data_batch1").html(str);
      }
    } else {
      var cek = 0;
      var str = "";
      for (var i = 0; i < ebatch.length; i++) {
        if (ebatch[i][5] == index_terima) {
          cek += 1;
          str += '<tr id="e' + i + '-trb" onclick="selectdetbatch(' + i + ', \'e\')"><td>' + ebatch[i][1] + "</td>\
            <td>" + ebatch[i][0] + "</td>\
            <td>" + format_date(ebatch[i][2]) + "</td>\
            <td style='text-align: right;'>" + ebatch[i][4] + "</td></tr>";
        }
      }
      if (cek == 0) {
        $("#edata_batch1").html('<tr><td colspan="5">Tidak ada batch.</td></tr>');
      } else {
        $("#edata_batch1").html(str);
      }
    }
  }

  function addBatch(name = "") {
    if (name == "") {
      if ($("#kode_brg_batch").val() != "" && $("#no_batch").val() != "" && $("#exp").val() != "" &&
        $("#qty_batch").val() != "" && parseInt($("#qty_batch").val().replace(/,/g, "")) > 0) {
        var cek = 0;
        for (var i = 0; i < batch.length; i++) {
          if (batch[i][5] == $("#index_terima").val() && batch[i][0] == $("#no_batch").val() && batch[i][2] == $("#exp").val()) {
            batch[i][4] = numberWithCommas(parseInt(batch[i][4].replace(/,/g, "")) + parseInt($("#qty_batch").val().replace(/,/g, "")));
            cek = 1;
            break;
          }
        }
        if (cek == 0) {
          var temp = [];
          temp.push($("#no_batch").val()); //0
          temp.push($("#kode_brg_batch").val()); //1
          temp.push($("#exp").val()); //2
          temp.push($("#qty_batch").val()); //3
          temp.push($("#qty_batch").val()); //4
          temp.push($("#index_terima").val()); //5
          temp.push(toInteger($("#isi_batch").val())); //6
          temp.push(1); //7
          batch.push(temp);
        }
        $("#no_batch").val("");
        $("#exp").val("");
        $("#qty_batch").val("0");
        loadBatch1($("#index_terima").val());
        calculateBatch();
      }
      else if (parseInt($("#qty_batch").val().replace(/,/g, "")) == 0) {
        alertify.warning("Kuantitas batch tidak boleh 0");
      }
      else {
        alertify.warning("Semua kolom harus terisi");
      }
    } else {
      if ($("#ekode_brg_batch").val() != "" && $("#eno_batch").val() != "" && $("#eexp").val() != "" &&
        $("#eqty_batch").val() != "" && parseInt($("#eqty_batch").val().replace(/,/g, "")) > 0) {
        var cek = 0;
        for (var i = 0; i < ebatch.length; i++) {
          if (ebatch[i][5] == $("#eindex_terima").val() && ebatch[i][0] == $("#eno_batch").val() && ebatch[i][2] == $("#eexp").val()) {
            ebatch[i][4] = numberWithCommas(parseInt(ebatch[i][4].replace(/,/g, "")) + parseInt($("#eqty_batch").val().replace(/,/g, "")));
            cek = 1;
            break;
          }
        }
        if (cek == 0) {
          var temp = [];
          temp.push($("#eno_batch").val());
          temp.push($("#ekode_brg_batch").val());
          temp.push($("#eexp").val());
          temp.push($("#eqty_batch").val());
          temp.push($("#eqty_batch").val());
          temp.push($("#eindex_terima").val());
          temp.push(toInteger($("#eisi_batch").val()));
          temp.push(1);
          ebatch.push(temp);
        }
        $("#eno_batch").val("");
        $("#eexp").val("");
        $("#eqty_batch").val("0");
        loadBatch1($("#eindex_terima").val(), 'e');
        calculateBatch('e');
      }
      else if (parseInt($("#eqty_batch").val().replace(/,/g, "")) == 0) {
        alertify.warning("Kuantitas batch tidak boleh 0");
      }
      else {
        alertify.warning("Semua kolom harus terisi");
      }
    }
  }

  function showeditBatch() {
    if (g_idb === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    var i = g_idb;
    batalAddBatch();
    $("#editrow").val(i);
    $("#editno_batch").val(batch[i][1]);
    $("#editexp").val(batch[i][2]);
    $("#editqty_batch").val(batch[i][3]);
    $("#tombolAddBatch").hide();
    $("#divEditBatch").show();
  }

  function eshoweditBatch() {
    if (g_idb === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    var i = g_idb;
    batalAddBatch('e');
    $("#eeditrow").val(i);
    $("#eeditno_batch").val(ebatch[i][1]);
    $("#eeditexp").val(ebatch[i][2]);
    $("#eeditqty_batch").val(ebatch[i][4]);
    $("#etombolAddBatch").hide();
    $("#edivEditBatch").show();
  }

  function batalAddBatch(name = "") {
    $("#"+name+"no_batch").val("");
    $("#"+name+"exp").val("");
    $("#"+name+"qty_batch").val("0");
    $("#"+name+"tombolAddBatch").show();
    $("#"+name+"divAddBatch").hide();
  }

  function editBatch(name = "") {
    var i = $("#"+name+"editrow").val();
    if (name == "") {
      batch[i][1] = $("#"+name+"editno_batch").val();
      batch[i][2] = $("#"+name+"editexp").val();
      batch[i][3] = $("#"+name+"editqty_batch").val();
      batch[i][4] = $("#"+name+"editqty_batch").val();
      batalEditBatch(name);
      loadBatch1(batch[i][5], name);
      calculateBatch(name);
    } else {
      ebatch[i][1] = $("#"+name+"editno_batch").val();
      ebatch[i][2] = $("#"+name+"editexp").val();
      ebatch[i][3] = $("#"+name+"editqty_batch").val();
      ebatch[i][4] = $("#"+name+"editqty_batch").val();
      batalEditBatch(name);
      loadBatch1(ebatch[i][5], name);
      calculateBatch(name);
    }
  }

  function batalEditBatch(name = "") {
    $("#"+name+"divEditBatch").hide();
    $("#"+name+"tombolAddBatch").show();
  }

  function deleteBatch() {
    if (g_idb === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    var i = g_idb;
    var index = batch[i][5];
    batch.splice(i, 1);
    loadBatch1(index);
    calculateBatch();
  }

  function edeleteBatch() {
    if (g_idb === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    var i = g_idb;
    var index = ebatch[i][5];
    ebatch.splice(i, 1);
    loadBatch1(index, 'e');
    calculateBatch('e');
  }

  // function cekQtyBatch(name = "") {
  //   if (name == "") {
  //     var maks = parseInt(cart[$("#index_terima").val()][4].replace(/,/g, ""));
  //     var qty_batch = 0;
  //     for (var i = 0; i < batch.length; i++) {
  //       if (batch[i][5] == $("#index_terima").val()) {
  //         qty_batch += parseInt(batch[i][4].replace(/,/g, ""));
  //       }
  //     }
  //     if (maks < qty_batch + parseInt($("#"+name+"qty_batch").val().replace(/,/g, ""))) {
  //       $("#"+name+"qty_batch").val(numberWithCommas(maks - qty_batch));
  //     }
  //   } else {
  //     var maks = parseInt(ecart[$("#eindex_terima").val()][4].replace(/,/g, ""));
  //     var qty_batch = 0;
  //     for (var i = 0; i < ebatch.length; i++) {
  //       if (ebatch[i][5] == $("#eindex_terima").val()) {
  //         qty_batch += parseInt(ebatch[i][4].replace(/,/g, ""));
  //       }
  //     }
  //     if (maks < qty_batch + parseInt($("#"+name+"qty_batch").val().replace(/,/g, ""))) {
  //       $("#"+name+"qty_batch").val(numberWithCommas(maks - qty_batch));
  //     }
  //   }
  // }

  function eshowBatch2(index) {
    loadBatch2(index, 'e');
    $("#eindex_retur").val(index);
    $("#editBatch2").modal('toggle');
  }

  function loadBatch2(index, name = "") {
    var str = "";
    if (name == "") {
      if (batch.length > 0) {
          var count = 0;
          for (var i = 0; i < batch.length; i++) {
            if (batch[i][5] == index) {
              str += "<tr><td>" + batch[i][1] + "</td>\
                <td>" + batch[i][0] + "</td>\
                <td>" + format_date(batch[i][2]) + "</td>\
                <td style='text-align: right;'>" + batch[i][3] + "</td>\
                <td style='text-align: right;'><input type='text' class='format-number text-right' value='" + batch[i][4] + "' id='"+i+"-inputbatch' onkeyup='editReturBatch("+i+")'></td>\
                </tr>";
              count += 1;
            }
          }
          if (count == 0) {
            $("#data_batch2").html("<tr><td colspan='5'>Tidak ada batch.</td></tr>");
          } else {
            $("#data_batch2").html(str);
          }
      } else {
        $("#data_batch2").html("<tr><td colspan='5'>Tidak ada batch.</td></tr>");
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
                <td style='text-align: right;'>" + ebatch[i][3] + "</td>\
                <td style='text-align: right;'><input type='text' class='format-number text-center' value='" + ebatch[i][4] + "' id='e"+i+"-inputbatch' onkeyup='editReturBatch("+i+", \"e\")'></td>\
                </tr>";
              count += 1;
            }
          }
          if (count == 0) {
            $("#edata_batch2").html("<tr><td colspan='5'>Tidak ada batch.</td></tr>");
          } else {
            $("#edata_batch2").html(str);
          }
      } else {
        $("#edata_batch2").html("<tr><td colspan='5'>Tidak ada batch.</td></tr>");
      }
    }
    $(".format-number").autoNumeric('init', {mDec: '0'});
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

  function showDetBatch() {
    if (g_iddet === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    var index_terima = g_iddet;
    var cek = 0;
    var str = "";
    for (var i = 0; i < detbatch.length; i++) {
      if (detbatch[i][4] == index_terima) {
        cek += 1;
        str += "<tr><td>" + detbatch[i][0] + "</td>\
          <td>" + detbatch[i][1] + "</td>\
          <td>" + format_date(detbatch[i][2]) + "</td>\
          <td style='text-align: right;'>" + detbatch[i][3] + "</td>\
          </tr>";
      }
    }
    if (cek == 0) {
      $("#detdata_batch").html('<tr><td colspan="4">Tidak ada batch.</td></tr>');
    } else {
      $("#detdata_batch").html(str);
    }
    $("#detailBatch").modal('toggle');
  }

  function cekBatch(name = "") {
    var check = 0;
    if (name == "") {
      if (batch.length != 0) {
        var temp = [], temp2 = [], count_b = [], join_retur = [];
        for (var i = 0; i < cart.length; i++) {
          temp.push(0);
          count_b.push(0);
          temp2.push(parseFloat(cart[i][7]));
        }
        for (var i = 0; i < batch.length; i++) {
          temp[batch[i][5]] += parseFloat(batch[i][4].replace(/,/g, ""));
          count_b[batch[i][5]] += 1;
        }
        for (var i = 0; i < temp.length; i++) {
          if (temp[i] != temp2[i] && count_b[i] > 0) {
            return '0&'+cart[i][0];
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
        for (var i = 0; i < ecart.length; i++) {
          temp.push(0);
          count_b.push(0);
          temp2.push(parseFloat(ecart[i][7]));
        }
        for (var i = 0; i < ebatch.length; i++) {
          temp[ebatch[i][5]] += parseFloat(ebatch[i][4].replace(/,/g, ""));
          count_b[ebatch[i][5]] += 1;
        }
        for (var i = 0; i < temp.length; i++) {
          if (temp[i] != temp2[i] && count_b[i] > 0) {
            return '0&'+ecart[i][0];
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
      for (var i = 0; i < cart.length; i++) {
        cart[i][2] = cart[i][2].replace(/,/g, "");
        cart[i][3] = cart[i][3].replace(/,/g, "");
        cart[i][5] = cart[i][5].replace(/,/g, "");
        cart[i][8] = cart[i][8].replace(/,/g, "");
      }
      for (var i = 0; i < batch.length; i++) {
        batch[i][4] = toInteger(batch[i][4]);
      }
    } else {
      for (var i = 0; i < ecart.length; i++) {
        ecart[i][2] = ecart[i][2].replace(/,/g, "");
        ecart[i][3] = ecart[i][3].replace(/,/g, "");
        ecart[i][5] = ecart[i][5].replace(/,/g, "");
        ecart[i][8] = ecart[i][8].replace(/,/g, "");
      }
      for (var i = 0; i < ebatch.length; i++) {
        ebatch[i][4] = toInteger(ebatch[i][4]);
      }
    }
  }

  function returnCommas(name = "") {
    if (name == "") {
      for (var i = 0; i < cart.length; i++) {
        cart[i][2] = numberWithCommas(cart[i][2]);
        cart[i][3] = numberWithCommas(cart[i][3]);
        cart[i][5] = numberWithCommas(cart[i][5]);
        cart[i][8] = numberWithCommas(cart[i][8]);
      }
      for (var i = 0; i < batch.length; i++) {
        batch[i][4] = numberWithCommas(batch[i][4]);
      }
    } else {
      for (var i = 0; i < ecart.length; i++) {
        ecart[i][2] = numberWithCommas(ecart[i][2]);
        ecart[i][3] = numberWithCommas(ecart[i][3]);
        ecart[i][5] = numberWithCommas(ecart[i][5]);
        ecart[i][8] = numberWithCommas(ecart[i][8]);
      }
      for (var i = 0; i < ebatch.length; i++) {
        ebatch[i][4] = numberWithCommas(ebatch[i][4]);
      }
    }
  }

  // start add pr
  function add() {
    var _token = $("#_token").val();
    var _no_urut = $("#no_urut").val();
    var _no_bukti = $("#no_bukti").val();
    var _tanggal = $("#tanggal").val();
    var _gudang = $("#gudang").val();
    var _keterangan = $("#keterangan").val();
    var cek = cekBatch();
    if (_no_urut != "" && _no_bukti != "" && _tanggal != "" && _gudang != "" && cart.length != 0 && cek == 1) {
      removeCommas();
      $.ajax({
        url     : "{!! url('addKoreksiStock') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          no_urut : _no_urut,
          no_bukti : _no_bukti,
          tanggal : _tanggal,
          gudang : _gudang,
          keterangan : _keterangan,
          cart : cart,
          batch : batch
        },
        success : function(result) {
          $("#no_urut").val(generateNoUrut());
          $("#no_bukti").val(generateNoBukti());
          if (result.split(";;")[0] == "1") {
            cart = []; batch = [];
            resetDisable(); loadCart(); loadAll(); reset(); select(result.split(";;")[1], result.split(";;")[2]);
            alertify.success('Data koreksi stock telah ditambahkan.');
          } else {
            returnCommas();
            alertify.alert('Gagal menambahkan data koreksi stock!', 'Nomor Bukti sudah dipakai. Silahkan tekan tombol tambah kembali.', function(){ });
          }
        }
      });
    }
    else {
      if (cek != "1") {
        if (cek.split("&")[0] == '0') {
          alertify.alert('Gagal menambahkan data koreksi stock!', 'Jumlah batch kode barang '+cek.split("&")[1]+' tidak sama.', function(){ });
        } else {
          alertify.alert('Gagal menambahkan data koreksi stock!', 'Jumlah batch kode barang '+cek.split("&")[2]+' pada no. batch '+cek.split("&")[1]+' dengan expired date '+cek.split("&")[3]+' melebihi jumlah qty batch.', function(){ });
        }
      }
      else {
        alertify.alert('Gagal menambahkan data koreksi stock!', 'Semua kolom harus terisi.', function(){ });
      }
    }
  }
  // end add pr

  // start tampilkan pr
  function show() {
    if (g_id === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    var _id = g_id, cekauth = 0; ebatch = [], ecart = [];
    var _token = $("#_token").val(); var no_bukti = ""; var gudang = "";
    $("#eid").val(_id);
    ecart = [];
    $.ajax({
      url     : "{!! url('showKoreksiStock') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        id : _id
      },
      success : function(result) {
        cekauth = result.auth_1;
        if (cekauth == 1) {
          return;
        }
        $("#eno_urut").val(result.no_urut);
        $("#eno_bukti").val(result.no_bukti);
        no_bukti = result.no_bukti;
        $("#etanggal").val(result.tanggal);
        $("#egudang").val(result.gudang);
        gudang = result.gudang;
        if (result.keterangan == null) {
          $("#eketerangan").val("");
        } else {
          $("#eketerangan").val(result.keterangan);
        }
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
      url     : "{!! url('showDetKoreksiStock') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        id : _id
      },
      success : function(result) {
        for (var i = 0; i < result.length; i++) {
          var temp = [];
          temp.push(result[i].kode_barang);
          temp.push(result[i].nama_barang);
          temp.push(numberWithCommas(result[i].qty_masuk));
          temp.push(numberWithCommas(result[i].qty_keluar));
          temp.push(result[i].satuan);
          temp.push(numberWithCommas(result[i].isi));
          temp.push(result[i].mk);
          temp.push(result[i].qty_masuk + result[i].qty_keluar);
          temp.push(numberWithCommas(result[i].harga));
          ecart.push(temp);

          if (ecart[i][6] == 0) {
            $.ajax({
              url     : "{!! url('getBatchNoBukti') !!}",
              type    : "POST",
              async   : false,
              data    : {
                _token : _token,
                no_bukti : no_bukti,
                barang : ecart[i][0]
              },
              success : function(result) {
                for (var j = 0; j < result.length; j++) {
                  var temp = [];
                  temp.push(result[j].no_batch);
                  temp.push(result[j].kode_barang);
                  temp.push(result[j].tanggal);
                  temp.push(numberWithCommas((result[j].qty * result[j].flag) / toInteger(ecart[i][5])));
                  temp.push(numberWithCommas((result[j].qty * result[j].flag) / toInteger(ecart[i][5])));
                  temp.push(i);
                  temp.push(toInteger(ecart[i][5]));
                  temp.push(result[j].flag);
                  ebatch.push(temp);
                }
              }
            });
          } else {
            $.ajax({
              url     : "{!! url('getBatchBarang') !!}",
              type    : "POST",
              async   : false,
              data    : {
                _token : _token,
                kode_barang : ecart[i][0],
                gudang : gudang
              },
              success : function(result) {
                for (var j = 0; j < result.length; j++) {
                  var dummy = [];
                  var qty = result[j].qty / toInteger(ecart[i][5]);
                  dummy.push(result[j].no_batch);
                  dummy.push(result[j].kode_barang);
                  dummy.push(result[j].tanggal);
                  dummy.push(numberWithCommas(round(qty, 1)));
                  dummy.push(numberWithCommas(0));
                  dummy.push(i);
                  dummy.push(toInteger(ecart[i][5]));
                  dummy.push(-1);
                  ebatch.push(dummy);
                }
              }
            });
            $.ajax({
              url     : "{!! url('getBatchNoBuktiBarang') !!}",
              type    : "POST",
              async   : false,
              data    : {
                _token : _token,
                no_bukti : no_bukti,
                barang : ecart[i][0]
              },
              success : function(result) {
                for (var j = 0; j < result.length; j++) {
                  for (var k = 0; k < ebatch.length; k++) {
                    if (result[j].no_batch == ebatch[k][0] && result[j].kode_barang == ebatch[k][1] && result[j].tanggal == ebatch[k][2]) {
                      ebatch[k][3] = numberWithCommas(toInteger(ebatch[k][3]) + (result[j].qty * result[j].flag) / toInteger(ecart[i][5]));
                      ebatch[k][4] = numberWithCommas(toInteger(ebatch[k][4]) + (result[j].qty * result[j].flag) / toInteger(ecart[i][5]));
                    }
                  }
                }
              }
            });
          }
        }
      }
    });
    for (var i = 0; i < ebatch.length; i++) {
      if (ebatch[i][3] <= 0) {
        ebatch.splice(i, 1);
        i--;
      }
    }
    loadCart('e');
    $("#editKoreksiStock").modal('toggle');
  }
  // end tampilkan pr

  // start tampilkan pr
  function showDet(_id) {
    var isi_detail = [], qty_batch = [], no_bukti = "";
    detbatch = [];
    var _token = $("#_token").val();
    $.ajax({
      url     : "{!! url('showKoreksiStock') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        id : _id
      },
      success : function(result) {
        $("#dno_urut").val(result.no_urut);
        $("#dno_bukti").val(result.no_bukti);
        no_bukti = result.no_bukti;
        $("#dtanggal").val(result.tanggal);
        var ket = "";
        if (result.keterangan != null) {
          ket = result.keterangan;
        }
        $("#dgudang").val(result.gudang);
        $("#dketerangan").val(ket);
      }
    });
    $.ajax({
      url     : "{!! url('showDetKoreksiStock') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        id : _id
      },
      success : function(result) {
        var str = "";
        $("#dcart").html("");
        for (var i = 0; i < result.length; i++) {
          str += "<tr id='d" + i + "-trdet' onclick='selectdet(" + i + ", \"d\")'>\
            <td style='padding: 3px;'>" + result[i].kode_barang + "</td>\
            <td style='padding: 3px;'>" + result[i].nama_barang + "</td>\
            <td style='padding: 3px; text-align: right;'>" + result[i].qty_masuk + "</td>\
            <td style='padding: 3px; text-align: right;'>" + result[i].qty_keluar + "</td>\
            <td style='padding: 3px;'>" + result[i].satuan + "</td>\
            <td style='padding: 3px; text-align: right;'>" + numberWithCommas(result[i].isi) + "</td>\
            <td style='padding: 3px; text-align: right;'>" + numberWithCommas(result[i].harga) + "</td>\
            <td style='padding: 3px; text-align: right;'><span id='det" + i + "-jmlbatch'></span></td>\
            </tr>";
          qty_batch.push(0);
          isi_detail.push(result[i].isi);
        }
        $("#dcart").html(str);
      }
    });
    $.ajax({
      url     : "{!! url('getBatchNoBukti') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        no_bukti : no_bukti
      },
      success : function(result) {
        for (var i = 0; i < result.length; i++) {
          var temp = [];
          temp.push(result[i].kode_barang);
          temp.push(result[i].no_batch);
          temp.push(result[i].tanggal);
          temp.push(numberWithCommas((result[i].qty * result[i].flag) / isi_detail[result[i].urut]));
          temp.push(result[i].urut);
          temp.push(isi_detail[result[i].urut]);
          detbatch.push(temp);
          qty_batch[result[i].urut] += (result[i].qty * result[i].flag) / isi_detail[result[i].urut];
        }
      }
    });
    for (var i = 0; i < qty_batch.length; i++) {
      $("#det"+i+"-jmlbatch").html(qty_batch[i]);
    }
    $("#detailKoreksiStock").modal('toggle');
  }
  // end tampilkan pr

  // start edit pr
  function edit() {
    var _token = $("#_token").val();
    var _id = $("#eid").val();
    var _no_bukti = $("#eno_bukti").val();
    var _tanggal = $("#etanggal").val();
    var _gudang = $("#egudang").val();
    var _keterangan = $("#eketerangan").val();
    var cek = cekBatch('e');
    if (_tanggal != "" && _gudang != "" && ecart.length != 0 && cek == 1) {
      removeCommas('e');
      $.ajax({
        url     : "{!! url('editKoreksiStock') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          id : _id,
          tanggal : _tanggal,
          gudang : _gudang,
          keterangan : _keterangan,
          cart : ecart,
          batch : ebatch
        },
        success : function(result) {
          ecart = []; ebatch = [];
          loadCart('e'); reset('e'); loadAll(); select(_id, _no_bukti);
          $("#editKoreksiStock").modal('toggle');
          alertify.success('Data koreksi stock telah diubah.');
        }
      });
    }
    else {
      if (cek != "1") {
        if (cek.split("&")[0] == '0') {
          alertify.alert('Gagal menambahkan data koreksi stock!', 'Jumlah batch kode barang '+cek.split("&")[1]+' tidak sama.', function(){ });
        } else {
          alertify.alert('Gagal menambahkan data koreksi stock!', 'Jumlah batch kode barang '+cek.split("&")[2]+' pada no. batch '+cek.split("&")[1]+' dengan expired date '+cek.split("&")[3]+' melebihi jumlah qty batch.', function(){ });
        }
      }
      else {
        alertify.alert('Gagal mengubah data koreksi stock!', 'Semua kolom harus terisi.', function(){ });
      }
    }
  }
  // end edit pr

  // start hapus pr
  function erase() {
    if (g_id === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    var _token = $("#_token").val();
    var cekauth = 0;
    $.ajax({
      url     : "{!! url('showKoreksiStock') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        id :g_id
      },
      success : function(result) {
        cekauth = result.auth_1;
      }
    });
    if (cekauth == 1) {
      alertify.warning("Transaksi sudah diotorisasi");
      return;
    }
    alertify.confirm('Hapus Koreksi Stock', 'Apakah yakin ingin menghapus Koreksi Stock dengan nomor bukti ' + g_nb + ' ?',
      function(){
        var _token = $("#_token").val();
        $.ajax({
          url     : "{!! url('eraseKoreksiStock') !!}",
          type    : "POST",
          async   : false,
          data    : {
            _token : _token,
            id : g_id
          },
          success : function(result) {
            loadAll();
            alertify.success('Data Koreksi Stock telah dihapus.');
          }
        });
    },function(){});
  }
  // end hapus pr
</script>
@endsection
