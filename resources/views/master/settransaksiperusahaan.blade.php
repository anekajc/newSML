@extends('master')

@section('css')
  <style>
    body {
      overflow-x: hidden;
    }
  </style>
@endsection

@section('breadcrumb')
<li class="nav-item">
  <a class="nav-link" href="#" data-toggle="tooltip" data-placement="bottom" title="Berkas / Set Nomor Transaksi dan Perusahaan"><span class="blue" id="title_page">Set Nomor Transaksi dan Perusahaan</span></a>
</li>
@endsection

@section('button-add-refresh')
@endsection

@section('content')
<div class="container-fluid" style="padding-left: 10px; padding-right : 10px;">
  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <div class="card">
    <div class="card-header" style="background: light;">
      <div class="row">
        <nav style="width: 100%;">
          <div class="nav nav-pills col-12" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Perusahaan</a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Set Nomor Transaksi</a>
          </div>
        </nav>
      </div>
    </div>
    <div class="card-body" style="background: palegoldenrod;">
      <div class="row">
        <div class="col-12 tab-content" id="nav-tabContent">
          <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" style="padding-left: 20px; padding-right : 20px;">
            <div class="row">
              <div class="col-3">
                <div class="form-group">
                  <label for="nama">Nama</label>
                  <input type="text" class="form-control" id="nama" placeholder="Nama" value="{!! $perusahaan->nama !!}">
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <label for="alamat">Alamat</label>
                  <textarea class="form-control" id="alamat" rows="2" placeholder="Alamat">{!! $perusahaan->alamat !!}</textarea>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <label for="kota">Kota</label>
                  <input type="text" class="form-control" id="kota" placeholder="Kota" value="{!! $perusahaan->kota !!}">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-3">
                <div class="form-group">
                  <label for="telepon">Telepon</label>
                  <input type="text" class="form-control" id="telepon" placeholder="Telepon" value="{!! $perusahaan->telepon !!}">
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <label for="fax">Fax</label>
                  <input type="text" class="form-control" id="fax" placeholder="Fax" value="{!! $perusahaan->fax !!}">
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" class="form-control" id="email" placeholder="Email" value="{!! $perusahaan->email !!}">
                </div>
              </div>
            </div>
            <hr/>
            <div class="row">
              <div class="col-3">
                <div class="form-group">
                  <label for="nama_pajak">Nama PKP</label>
                  <input type="text" class="form-control" id="nama_pajak" placeholder="Nama PKP" value="{!! $perusahaan->nama_pajak !!}">
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <label for="alamat_pajak">Alamat PKP</label>
                  <textarea class="form-control" id="alamat_pajak" rows="2" placeholder="Alamat PKP">{!! $perusahaan->alamat_pajak !!}</textarea>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <label for="kota_pkp">Kota PKP</label>
                  <input type="text" class="form-control" id="kota_pajak" placeholder="Kota PKP" value="{!! $perusahaan->kota_pajak !!}">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-3">
                <div class="form-group">
                  <label for="npwp">NPWP</label>
                  <input type="text" class="form-control" id="npwp" placeholder="NPWP" value="{!! $perusahaan->npwp !!}">
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <label for="tanggal_pengukuhan">Tanggal Pengukuhan</label>
                  <input type="date" id="tanggal_pengukuhan" class="form-control" value="{!! $perusahaan->tanggal_pengukuhan !!}">
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <label for="fpj">Penandatanganan FPJ</label>
                  <input type="text" class="form-control" id="penandatanganan_fpj" placeholder="Penandatanganan FPJ" value="{!! $perusahaan->penandatanganan_fpj !!}">
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <label for="jabatan">Jabatan</label>
                  <input type="text" class="form-control" id="jabatan" placeholder="Jabatan" value="{!! $perusahaan->jabatan !!}">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <button type="button" class="btn btn-primary float-right" onclick="editPerusahaan()">Simpan</button>
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" style="padding-left: 30px; padding-right : 30px;">
            <div class="row">
              <div class="col-10">
                <div class="row">
                  <div class="col-12"><h6>Accounting</h6></div>
                </div>
                <div class="row">
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="bon">Bon Sementara</label>
                      <input type="text" class="form-control" id="bon" maxLength="3" required value="{!! $nomor->bon !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="kas_masuk">Kas Masuk</label>
                      <input type="text" class="form-control" id="kas_masuk" maxLength="3" required value="{!! $nomor->kas_masuk !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="kas_keluar">Kas Keluar</label>
                      <input type="text" class="form-control" id="kas_keluar" maxLength="3" required value="{!! $nomor->kas_keluar !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="bank_masuk">Bank Masuk</label>
                      <input type="text" class="form-control" id="bank_masuk" maxLength="3" required value="{!! $nomor->bank_masuk !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="bank_keluar">Bank Keluar</label>
                      <input type="text" class="form-control" id="bank_keluar" maxLength="3" required value="{!! $nomor->bank_keluar !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="bukti_memorial">Bukti Memorial</label>
                      <input type="text" class="form-control" id="bukti_memorial" maxLength="3" required value="{!! $nomor->bukti_memorial !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="jurnal_koreksi">Jurnal Koreksi</label>
                      <input type="text" class="form-control" id="jurnal_koreksi" maxLength="3" required value="{!! $nomor->jurnal_koreksi !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="bukti_giro_terima">Bukti Giro Terima</label>
                      <input type="text" class="form-control" id="bukti_giro_terima" maxLength="3" required value="{!! $nomor->bukti_giro_terima !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="bukti_giro_cair">Bukti Giro Cair</label>
                      <input type="text" class="form-control" id="bukti_giro_cair" maxLength="3" required value="{!! $nomor->bukti_giro_cair !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="bukti_tolak_giro">Bukti Tolak Giro</label>
                      <input type="text" class="form-control" id="bukti_tolak_giro" maxLength="3" required value="{!! $nomor->bukti_tolak_giro !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="bukti_buka_giro">Bukti Buka Giro</label>
                      <input type="text" class="form-control" id="bukti_buka_giro" maxLength="3" required value="{!! $nomor->bukti_buka_giro !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="bukti_cair_giro">Bukti Cair Giro</label>
                      <input type="text" class="form-control" id="bukti_cair_giro" maxLength="3" required value="{!! $nomor->bukti_cair_giro !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="dph">DPH</label>
                      <input type="text" class="form-control" id="dph" maxLength="3" required value="{!! $nomor->dph !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="pelunasan_dpp">Pelunasan DPP</label>
                      <input type="text" class="form-control" id="pelunasan_dpp" maxLength="3" required value="{!! $nomor->pelunasan_dpp !!}">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12"><h6>Pengadaan</h6></div>
                </div>
                <div class="row">
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="pr">PR</label>
                      <input type="text" class="form-control" id="pr" maxLength="3" required value="{!! $nomor->pr !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="po">PO</label>
                      <input type="text" class="form-control" id="po" maxLength="3" required value="{!! $nomor->po !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="penerimaan">Penerimaan Gudang</label>
                      <input type="text" class="form-control" id="penerimaan" maxLength="3" required value="{!! $nomor->penerimaan !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="invoice_pembelian">Invoice Pembelian</label>
                      <input type="text" class="form-control" id="invoice_pembelian" maxLength="3" required value="{!! $nomor->invoice_pembelian !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="perintah_retur_pembelian">Perintah Ret. Beli</label>
                      <input type="text" class="form-control" id="perintah_retur_pembelian" maxLength="3" required value="{!! $nomor->perintah_retur_pembelian !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="retur_pembelian">Retur Beli</label>
                      <input type="text" class="form-control" id="retur_pembelian" maxLength="3" required value="{!! $nomor->retur_pembelian !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="debet_note">Debet Note</label>
                      <input type="text" class="form-control" id="debet_note" maxLength="3" required value="{!! $nomor->debet_note !!}">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-tenhalf"><h6>Marketing</h6></div>
                  <div class="col-onehalf"><h6>POS</h6></div>
                </div>
                <div class="row">
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="so">SO</label>
                      <input type="text" class="form-control" id="so" maxLength="3" required value="{!! $nomor->so !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="surat_jalan">Surat Jalan</label>
                      <input type="text" class="form-control" id="surat_jalan" maxLength="3" required value="{!! $nomor->surat_jalan !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="invoice_penjualan">Invoice Penjualan</label>
                      <input type="text" class="form-control" id="invoice_penjualan" maxLength="3" required value="{!! $nomor->invoice_penjualan !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="invoice_jasa">Invoice Jasa</label>
                      <input type="text" class="form-control" id="invoice_jasa" maxLength="3" required value="{!! $nomor->invoice_jasa !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="perintah_retur_penjualan">Perintah Ret. Jual</label>
                      <input type="text" class="form-control" id="perintah_retur_penjualan" maxLength="3" required value="{!! $nomor->perintah_retur_penjualan !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="retur_penjualan">Retur Jual</label>
                      <input type="text" class="form-control" id="retur_penjualan" maxLength="3" required value="{!! $nomor->retur_penjualan !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="kredit_note">Kredit Note</label>
                      <input type="text" class="form-control" id="kredit_note" maxLength="3" required value="{!! $nomor->kredit_note !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="pos">POS</label>
                      <input type="text" class="form-control" id="pos" maxLength="3" required value="{!! $nomor->pos !!}">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12"><h6>Gudang</h6></div>
                </div>
                <div class="row">
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="permintaan_pemakaian">Per. Pemakaian</label>
                      <input type="text" class="form-control" id="permintaan_pemakaian" maxLength="3" required value="{!! $nomor->permintaan_pemakaian !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="pemakaian_barang">Pemakaian Barang</label>
                      <input type="text" class="form-control" id="pemakaian_barang" maxLength="3" required value="{!! $nomor->pemakaian_barang !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="ubah_kemasan">Ubah Kemasan</label>
                      <input type="text" class="form-control" id="ubah_kemasan" maxLength="3" required value="{!! $nomor->ubah_kemasan !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="perintah_opname">Perintah Opname</label>
                      <input type="text" class="form-control" id="perintah_opname" maxLength="3" required value="{!! $nomor->perintah_opname !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="berita_acara_opname">Berita Acara Opname</label>
                      <input type="text" class="form-control" id="berita_acara_opname" maxLength="3" required value="{!! $nomor->berita_acara_opname !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="opname_barang">Opname Barang</label>
                      <input type="text" class="form-control" id="opname_barang" maxLength="3" required value="{!! $nomor->opname_barang !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="koreksi_stock">Koreksi Stock</label>
                      <input type="text" class="form-control" id="koreksi_stock" maxLength="3" required value="{!! $nomor->koreksi_stock !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="permintaan_transfer_barang">Per. Trf. Barang</label>
                      <input type="text" class="form-control" id="permintaan_transfer_barang" maxLength="3" required value="{!! $nomor->permintaan_transfer_barang !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="transfer_barang">Transfer Barang</label>
                      <input type="text" class="form-control" id="transfer_barang" maxLength="3" required value="{!! $nomor->transfer_barang !!}">
                    </div>
                  </div>
                  <div class="col-onehalf">
                    <div class="form-group">
                      <label for="penerimaan_transfer_barang">Pen. Trf. Barang</label>
                      <input type="text" class="form-control" id="penerimaan_transfer_barang" maxLength="3" required value="{!! $nomor->penerimaan_transfer_barang !!}">
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-2" style="padding-left: 20px;">
                <div class="row">
                  <div class="col-12"><h6>Format</h6></div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <div class="form-group">
                      <label for="inisial_perusahaan">Inisial Perusahaan</label>
                      <input type="text" class="form-control" id="inisial_perusahaan" maxLength="3" required value="{!! $nomor->inisial_perusahaan !!}">
                    </div>
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-12">
                    <div class="form-group">
                      <label for="pemisah">Pemisah</label>
                      <select class="form-control" id="pemisah">
                        @for ($i = 0; $i < count($pemisah); $i++)
                          @if ($pemisah[$i] == $nomor->pemisah)
                            <option value="{!! $pemisah[$i] !!}" selected>{!! $pemisah[$i] !!}</option>
                          @else
                            <option value="{!! $pemisah[$i] !!}">{!! $pemisah[$i] !!}</option>
                          @endif
                        @endfor
                      </select>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group">
                      <br>
                      <label for="">Format Nomor</label>
                      <select class="form-control" id="format1">
                        @for ($i = 0; $i < count($format); $i++)
                          @if ($i + 1 == $nomor->format1)
                            <option value="{!! $i + 1 !!}" selected>{!! $format[$i] !!}</option>
                          @else
                            <option value="{!! $i + 1 !!}">{!! $format[$i] !!}</option>
                          @endif
                        @endfor
                      </select>
                      <select class="form-control" id="format2" style="margin-top: 5px;">
                        @for ($i = 0; $i < count($format); $i++)
                          @if ($i + 1 == $nomor->format2)
                            <option value="{!! $i + 1 !!}" selected>{!! $format[$i] !!}</option>
                          @else
                            <option value="{!! $i + 1 !!}">{!! $format[$i] !!}</option>
                          @endif
                        @endfor
                      </select>
                      <select class="form-control" id="format3" style="margin-top: 5px;">
                        @for ($i = 0; $i < count($format); $i++)
                          @if ($i + 1 == $nomor->format3)
                            <option value="{!! $i + 1 !!}" selected>{!! $format[$i] !!}</option>
                          @else
                            <option value="{!! $i + 1 !!}">{!! $format[$i] !!}</option>
                          @endif
                        @endfor
                      </select>
                      <select class="form-control" id="format4" style="margin-top: 5px;">
                        @for ($i = 0; $i < count($format); $i++)
                          @if ($i + 1 == $nomor->format4)
                            <option value="{!! $i + 1 !!}" selected>{!! $format[$i] !!}</option>
                          @else
                            <option value="{!! $i + 1 !!}">{!! $format[$i] !!}</option>
                          @endif
                        @endfor
                      </select>
                    </div>
                    <br>
                    <div class="form-group">
                      <label for="reset">Reset setiap</label>
                      <select class="form-control" id="reset">
                        @for ($i = 0; $i < count($reset); $i++)
                          @if ($i + 1 == $nomor->reset)
                            <option value="{!! $i + 1 !!}" selected>{!! $reset[$i] !!}</option>
                          @else
                            <option value="{!! $i + 1 !!}">{!! $reset[$i] !!}</option>
                          @endif
                        @endfor
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12" style="padding-top: 40px;">
                    <button type="button" class="btn btn-primary float-right" onclick="editNomor()">Simpan</button>
                  </div>
                </div>
              </div>
            </div>
            <br>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('js')
<script type="text/javascript">

  // start document ready
	$(document).ready(function(){
    middleTD();
    loadAkses();
	});
  // end document ready

  function loadAkses() {
    var _token = $("#_token").val();
    var akses = "";
    $.ajax({
      url     : "{!! url('getAksesByMenu') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        menu : '/settransaksiperusahaan'
      },
      success : function(result) {
        if (result.koreksi == 1) {
          $("input").prop("disabled", false);
          $("button").prop("disabled", false);
          $("select").prop("disabled", false);
          $("textarea").prop("disabled", false);
        } else {
          $("input").prop("disabled", true);
          $("button").prop("disabled", true);
          $("select").prop("disabled", true);
          $("textarea").prop("disabled", true);
        }
      }
    });
  }

  // start edit perusahaan
  function editPerusahaan() {
    var _token = $("#_token").val();
    var _nama = $("#nama").val();
    var _alamat = $("#alamat").val();
    var _kota = $("#kota").val();
    var _telepon = $("#telepon").val();
    var _fax = $("#fax").val();
    var _email = $("#email").val();
    var _nama_pajak = $("#nama_pajak").val();
    var _alamat_pajak = $("#alamat_pajak").val();
    var _kota_pajak = $("#kota_pajak").val();
    var _npwp = $("#npwp").val();
    var _tanggal_pengukuhan = $("#tanggal_pengukuhan").val();
    var _penandatanganan_fpj = $("#penandatanganan_fpj").val();
    var _jabatan = $("#jabatan").val();
    if (_nama != "" && _alamat != "" && _kota != "" && _telepon != "" && _fax != "" && _email != "" && _jabatan != ""
      && _nama_pajak != "" && _alamat_pajak != "" && _npwp != "" && _tanggal_pengukuhan != "" && _penandatanganan_fpj != "") {
      $.ajax({
        url     : "{!! url('editPerusahaan') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          nama : _nama,
          alamat : _alamat,
          kota : _kota,
          telepon : _telepon,
          fax : _fax,
          email : _email,
          nama_pajak : _nama_pajak,
          alamat_pajak : _alamat_pajak,
          kota_pajak : _kota_pajak,
          npwp : _npwp,
          tanggal_pengukuhan : _tanggal_pengukuhan,
          penandatanganan_fpj : _penandatanganan_fpj,
          jabatan : _jabatan
        },
        success : function(result) {
          alertify.success('Data perusahaan telah diubah.');
        }
      });
    }
    else {
      alertify.alert('Gagal mengubah data perusahaan!', 'Semua kolom harus terisi.', function(){ });
    }
  }
  // end edit perusahaan

  function editNomor() {
    var _token = $("#_token").val();
    var _bon = $("#bon").val();
    var _kas_masuk = $("#kas_masuk").val();
    var _kas_keluar = $("#kas_keluar").val();
    var _bank_masuk = $("#bank_masuk").val();
    var _bank_keluar = $("#bank_keluar").val();
    var _bukti_memorial = $("#bukti_memorial").val();
    var _jurnal_koreksi = $("#jurnal_koreksi").val();
    var _bukti_giro_terima = $("#bukti_giro_terima").val();
    var _bukti_giro_cair = $("#bukti_giro_cair").val();
    var _bukti_tolak_giro = $("#bukti_tolak_giro").val();
    var _bukti_buka_giro = $("#bukti_buka_giro").val();
    var _bukti_cair_giro = $("#bukti_cair_giro").val();
    var _dph = $("#dph").val();
    var _pelunasan_dpp = $("#pelunasan_dpp").val();
    var _pr = $("#pr").val();
    var _po = $("#po").val();
    var _penerimaan = $("#penerimaan").val();
    var _invoice_pembelian = $("#invoice_pembelian").val();
    var _perintah_retur_pembelian = $("#perintah_retur_pembelian").val();
    var _retur_pembelian = $("#retur_pembelian").val();
    var _debet_note = $("#debet_note").val();
    var _so = $("#so").val();
    var _surat_jalan = $("#surat_jalan").val();
    var _invoice_penjualan = $("#invoice_penjualan").val();
    var _invoice_jasa = $("#invoice_jasa").val();
    var _perintah_retur_penjualan = $("#perintah_retur_penjualan").val();
    var _retur_penjualan = $("#retur_penjualan").val();
    var _kredit_note = $("#kredit_note").val();
    var _permintaan_pemakaian = $("#permintaan_pemakaian").val();
    var _pemakaian_barang = $("#pemakaian_barang").val();
    var _ubah_kemasan = $("#ubah_kemasan").val();
    var _perintah_opname = $("#perintah_opname").val();
    var _berita_acara_opname = $("#berita_acara_opname").val();
    var _opname_barang = $("#opname_barang").val();
    var _koreksi_stock = $("#koreksi_stock").val();
    var _permintaan_transfer_barang = $("#permintaan_transfer_barang").val();
    var _transfer_barang = $("#transfer_barang").val();
    var _penerimaan_transfer_barang = $("#penerimaan_transfer_barang").val();
    var _inisial_perusahaan = $("#inisial_perusahaan").val();
    var _pos = $("#pos").val();
    var _pemisah = $("#pemisah").val();
    var _format1 = $("#format1").val();
    var _format2 = $("#format2").val();
    var _format3 = $("#format3").val();
    var _format4 = $("#format4").val();
    var _reset = $("#reset").val();
    if (_bon != "" && _kas_masuk != "" && _kas_keluar != "" && _bank_masuk != "" && _bank_keluar != "" && _bukti_memorial != "" && _jurnal_koreksi != "" && _bukti_giro_terima != "" && _bukti_giro_cair != "" && _bukti_tolak_giro != "" && _bukti_buka_giro != "" && _bukti_cair_giro != "" && _dph != "" &&
        _pelunasan_dpp != "" && _pr != "" && _po != "" && _penerimaan != "" && _invoice_pembelian != "" && _perintah_retur_pembelian != "" && _retur_pembelian != "" && _debet_note != "" && _so != "" && _surat_jalan != "" && _invoice_penjualan != "" && _invoice_jasa != "" && _perintah_retur_penjualan != "" &&
        _retur_penjualan != "" && _kredit_note != "" && _permintaan_pemakaian != "" && _pemakaian_barang != "" && _ubah_kemasan != "" && _perintah_opname != "" && _berita_acara_opname != "" && _opname_barang != "" &&_koreksi_stock != "" && _permintaan_transfer_barang != "" && _transfer_barang != "" &&
        _penerimaan_transfer_barang != "" && _pos != "" && _inisial_perusahaan != "") {
      $.ajax({
        url     : "{!! url('editNomor') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          bon : _bon,
          kas_masuk : _kas_masuk,
          kas_keluar : _kas_keluar,
          bank_masuk : _bank_masuk,
          bank_keluar : _bank_keluar,
          bukti_memorial : _bukti_memorial,
          jurnal_koreksi : _jurnal_koreksi,
          bukti_giro_terima : _bukti_giro_terima,
          bukti_giro_cair : _bukti_giro_cair,
          bukti_tolak_giro : _bukti_tolak_giro,
          bukti_buka_giro : _bukti_buka_giro,
          bukti_cair_giro : _bukti_cair_giro,
          dph : _dph,
          pelunasan_dpp : _pelunasan_dpp,
          pr : _pr,
          po : _po,
          penerimaan : _penerimaan,
          invoice_pembelian : _invoice_pembelian,
          perintah_retur_pembelian : _perintah_retur_pembelian,
          retur_pembelian : _retur_pembelian,
          debet_note : _debet_note,
          so : _so,
          surat_jalan : _surat_jalan,
          invoice_penjualan : _invoice_penjualan,
          invoice_jasa : _invoice_jasa,
          perintah_retur_penjualan : _perintah_retur_penjualan,
          retur_penjualan : _retur_penjualan,
          kredit_note : _kredit_note,
          permintaan_pemakaian : _permintaan_pemakaian,
          pemakaian_barang : _pemakaian_barang,
          ubah_kemasan : _ubah_kemasan,
          perintah_opname : _perintah_opname,
          berita_acara_opname : _berita_acara_opname,
          opname_barang : _opname_barang,
          koreksi_stock : _koreksi_stock,
          permintaan_transfer_barang : _permintaan_transfer_barang,
          transfer_barang : _transfer_barang,
          penerimaan_transfer_barang : _penerimaan_transfer_barang,
          pos : _pos,
          inisial_perusahaan : _inisial_perusahaan,
          pemisah : _pemisah,
          format1 : _format1,
          format2 : _format2,
          format3 : _format3,
          format4 : _format4,
          reset : _reset
        },
        success : function(result) {
          alertify.success('Data nomor transaksi telah diubah.');
        }
      });
    }
    else {
      alertify.alert('Gagal mengubah data nomor transaksi!', 'Semua kolom harus terisi.', function(){ });
    }
  }

</script>
@endsection
