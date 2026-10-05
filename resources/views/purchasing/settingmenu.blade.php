@extends('master')

@section('css')
<style type="text/css">
  .modal-lg {
    max-width: 85%;
  }

  .btn-show-modal {
    width: 70%;
  }
  .card-footer {
      padding: .75rem 1.25rem;
      background-color: palegoldenrod;
      border-top: 1px solid black;
      text-align: right;
  }
  .fa, .fac {
      font-family: 'Font Awesome 5 Free';
      font-weight: 900;
      width: 25px;
      -webkit-font-smoothing: antialiased;
      display: inline-block;
      font-style: normal;
      font-variant: normal;
      text-rendering: auto;
      line-height: 1;
  }
  input[type=checkbox], input[type=radio] {
    box-sizing: border-box;
    padding: 0;
    height: 100%;
  }
</style>
@endsection

@section('breadcrumb')
<li class="nav-item">
  <a class="nav-link" href="#" data-toggle="tooltip" data-placement="bottom" title="Berkas / Setting Menu"><span class="blue" id="title_page">Setting Menu</span></a>
</li>
@endsection

@section('button-add-refresh')
<!-- <button type="button" class="btn btn-primary btn-sm btn-top" onclick="loadAll()" rel="tooltip" data-placement="bottom" title="Refresh" style="width: 30px;"><i class="fas fa-sync-alt"></i></button>&nbsp;&nbsp; -->
@if (\Auth::user()->level == 3)
  <!-- <button class="btn btn-success btn-sm btn-top" type="button" data-toggle="modal" data-target="#addMenu" rel="tooltip" data-placement="bottom" title="Tambah" style="width: 30px;"><i class="fas fa-plus-circle"></i></button>&nbsp;&nbsp;
  <button type="button" class="btn btn-warning btn-sm btn-top" onclick="show()" rel="tooltip" data-placement="bottom" title="Ubah" style="width: 30px;"><i class="fas fa-pencil-alt"></i></button>&nbsp;&nbsp;
  <button type="button" class="btn btn-danger btn-sm btn-top" onclick="erase()" rel="tooltip" data-placement="bottom" title="Hapus" style="width: 30px;"><i class="fas fa-trash"></i></button>&nbsp;&nbsp; -->
@endif
@if (\Auth::user()->level == 3)
  <!-- <button type="button" class="btn btn-secondary btn-sm btn-top" onclick="showAkses()" rel="tooltip" data-placement="bottom" title="Akses Menu Utama" style="width: 30px;"><i class="fas fa-bars"></i></button>&nbsp;&nbsp; -->
@endif
@endsection

@section('content')
<div class="container">
  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <div class="card">
    <div class="card-body" style="background: palegoldenrod;">
      <div class="row">
        <div class="col-4">
          <div class="input-group mb-2">
            <div class="col-10">
              <div class="input-group-text">
                <i class="fac fa-database"></i>&nbsp;&nbsp;&nbsp;Master Data
              </div>
            </div>
            <div class="col-1" >
              @if ($grup2->show_acc == 1)
                <input type="checkbox" class="form-control" id="{{ $grup2->grup }}" value="0" checked>
              @else
                <input type="checkbox" class="form-control" id="{{ $grup2->grup }}" value="1">
              @endif
            </div>
          </div>
        </div>
        <div class="col-4">
          <div class="input-group mb-2">
            <div class="col-10">
              <div class="input-group-text">
                <i class="fac fa-calculator"></i>&nbsp;&nbsp;&nbsp;Accounting
              </div>
            </div>
            <div class="col-1">
              @if ($grup3->show_acc == 1)
                <input type="checkbox" class="form-control" id="{{ $grup3->grup }}" value="0" checked>
              @else
                <input type="checkbox" class="form-control" id="{{ $grup3->grup }}" value="1">
              @endif
            </div>
          </div>
        </div>
        <div class="col-4">
          <div class="input-group mb-2">
            <div class="col-10">
              <div class="input-group-text">
                <i class="fac fa-shopping-bag"></i>&nbsp;&nbsp;&nbsp;Pengadaan
              </div>
            </div>
            <div class="col-1">
              @if ($grup4->show_acc == 1)
                <input type="checkbox" class="form-control" id="{{ $grup4->grup }}" value="0" checked>
              @else
                <input type="checkbox" class="form-control" id="{{ $grup4->grup }}" value="1">
              @endif
            </div>
          </div>
        </div>
        <div class="col-4">
          <div class="input-group mb-2">
            <div class="col-10">
              <div class="input-group-text">
                <i class="fac fa-shopping-cart"></i>&nbsp;&nbsp;&nbsp;Marketing
              </div>
            </div>
            <div class="col-1">
              @if ($grup5->show_acc == 1)
                <input type="checkbox" class="form-control" id="{{ $grup5->grup }}" value="0" checked>
              @else
                <input type="checkbox" class="form-control" id="{{ $grup5->grup }}" value="1">
              @endif
            </div>
          </div>
        </div>
        <div class="col-4">
          <div class="input-group mb-2">
            <div class="col-10">
              <div class="input-group-text">
                <i class="fac fa-receipt"></i>&nbsp;&nbsp;&nbsp;POS
              </div>
            </div>
            <div class="col-1">
              @if ($grup6->show_acc == 1)
                <input type="checkbox" class="form-control" id="{{ $grup6->grup }}" value="0" checked>
              @else
                <input type="checkbox" class="form-control" id="{{ $grup6->grup }}" value="1">
              @endif
            </div>
          </div>
        </div>
        <div class="col-4">
          <div class="input-group mb-2">
            <div class="col-10">
              <div class="input-group-text">
                <i class="fac fa-industry"></i>&nbsp;&nbsp;&nbsp;Produksi
              </div>
            </div>
            <div class="col-1">
              @if ($grup7->show_acc == 1)
                <input type="checkbox" class="form-control" id="{{ $grup7->grup }}" value="0" checked>
              @else
                <input type="checkbox" class="form-control" id="{{ $grup7->grup }}" value="1">
              @endif
            </div>
          </div>
        </div>
        <div class="col-4">
          <div class="input-group mb-2">
            <div class="col-10">
              <div class="input-group-text">
                <i class="fac fa-warehouse"></i>&nbsp;&nbsp;&nbsp;Gudang
              </div>
            </div>
            <div class="col-1">
              @if ($grup8->show_acc == 1)
                <input type="checkbox" class="form-control" id="{{ $grup8->grup }}" value="0" checked>
              @else
                <input type="checkbox" class="form-control" id="{{ $grup8->grup }}" value="1">
              @endif
            </div>
          </div>
        </div>
        <div class="col-4">
          <div class="input-group mb-2">
            <div class="col-10">
              <div class="input-group-text">
                <i class="fac fa-chart-line"></i>&nbsp;&nbsp;&nbsp;Laporan
              </div>
            </div>
            <div class="col-1">
              @if ($grup9->show_acc == 1)
                <input type="checkbox" class="form-control" id="{{ $grup9->grup }}" value="0" checked>
              @else
                <input type="checkbox" class="form-control" id="{{ $grup9->grup }}" value="1">
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="card-footer">
      <button type="button" class="btn btn-primary" onclick="editSetMenu()">Simpan</button>
    </div>
  </div>
</div>

<!-- Start modal akses -->
<div class="modal fade" id="editAksesMenu" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Akses Menu Utama</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="resetShowAkses()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-onehalf">
            <div class="form-group">
              <label for="aksesUsername">Nama Pengguna </label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <select class="form-control" id="aksesUsername" name="aksesUsername" disabled>
                <option value="">-- Pilih nama pengguna --</option>
                @for ($i = 0; $i < count($user); $i++)
                  <option value="{!! $user[$i]->id !!}">{!! $user[$i]->name !!}</option>
                @endfor
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <!-- <div class="col-2">
            <div class="form-group">
              <label for="salinAksesUsername">Salin akses menu dari pengguna lain: </label>
            </div>
          </div> -->
          <div class="col-sm-11 col-md-6">
            <div class="form-group">
              <input type="hidden" class="form-control" id="salinAksesUsername" onchange="salinAkses()" disabled>
            </div>
          </div>
        </div>
        <hr style="margin-top: 7px; margin-bottom: 7px;" />
        <div class="row">
          <div class="col-2" style="padding-left: 10px; padding-right: 10px; height: 500px; overflow-y:auto;">
            <div class="input-group mb-2">
              <div class="input-group-text">
                <i class="fac fa-database"></i>
              </div>
              <input type="button" class="btn btn-primary btn-show-modal text-left" onclick="pilihcek(this.value, '2')" value="Master Data">
            </div>
            <div class="input-group mb-2">
              <div class="input-group-text">
                <i class="fac fa-calculator"></i>
              </div>
              <input type="button" class="btn btn-primary btn-show-modal text-left" onclick="pilihcek(this.value, '3')" value="Accounting">
            </div>
            <div class="input-group mb-2">
              <div class="input-group-text">
                <i class="fac fa-shopping-bag"></i>
              </div>
              <input type="button" class="btn btn-primary btn-show-modal text-left" onclick="pilihcek(this.value, '4')" value="Pengadaan">
            </div>
            <div class="input-group mb-2">
              <div class="input-group-text">
                <i class="fac fa-shopping-cart"></i>
              </div>
              <input type="button" class="btn btn-primary btn-show-modal text-left" onclick="pilihcek(this.value, '5')" value="Marketing">
            </div>
            <div class="input-group mb-2">
              <div class="input-group-text">
                <i class="fac fa-industry"></i>
              </div>
              <input type="button" class="btn btn-primary btn-show-modal text-left" onclick="pilihcek(this.value, '7')" value="Produksi">
            </div>
            <div class="input-group mb-2">
              <div class="input-group-text">
                <i class="fac fa-warehouse"></i>
              </div>
              <input type="button" class="btn btn-primary btn-show-modal text-left" onclick="pilihcek(this.value, '8')" value="Gudang">
            </div>
            <div class="input-group mb-2">
              <div class="input-group-text">
                <i class="fac fa-chart-line"></i>
              </div>
              <input type="button" class="btn btn-primary btn-show-modal text-left" onclick="pilihcek(this.value, '9')" value="Laporan">
            </div>
          </div>
          <div class="col-10" style="padding-left: 20px; padding-right: 20px; height: 500px; overflow-y:auto;">
            <div class="form-group tableFixHead" style="max-height: 70vh !important;">
              <table class="table table-bordered align-middle table-hover" id="tabelAksesMenu">
                <thead>
                  <tr>
                    <th class="align-middle">Menu</th>
                    <th class="align-middle">Tampil</th>
                    <th class="align-middle">Tambah</th>
                    <th class="align-middle">Koreksi</th>
                    <th class="align-middle">Hapus</th>
                    <th class="align-middle">Cetak</th>
                    <th class="align-middle">Eksport</th>
                    <th class="align-middle">Otorisasi 1</th>
                    <th class="align-middle">Otorisasi 2</th>
                    <th class="align-middle">Otorisasi 3</th>
                    <th class="align-middle">Otorisasi 4</th>
                    <th class="align-middle">Otorisasi 5</th>
                    <th class="align-middle">Batal Otorisasi</th>
                    <th class="align-middle">Batal Transaksi</th>
                  </tr>
                </thead>
                <tbody>
                  @for ($i = 0; $i < count($allmenu); $i++)
                    @if ($i + 1 < count($allmenu))
                      @if ($allmenu[$i + 1]->l0 > $allmenu[$i]->l0)
                        <tr style="background-color: yellow;">
                      @else
                        <tr>
                      @endif
                    @else
                      <tr>
                    @endif
                      <td class="align-middle">{!! $allmenu[$i]->keterangan !!}</td>
                      <td class="align-middle text-center"><input type="checkbox" id="{!! $allmenu[$i]->id !!}-tampil" class="aksesMenuCheckbox lvl-{!! $allmenu[$i]->l0 !!}"></td>
                      <td class="align-middle text-center"><input type="checkbox" id="{!! $allmenu[$i]->id !!}-tambah" class="aksesMenuCheckbox lvl-{!! $allmenu[$i]->l0 !!}"></td>
                      <td class="align-middle text-center"><input type="checkbox" id="{!! $allmenu[$i]->id !!}-koreksi" class="aksesMenuCheckbox lvl-{!! $allmenu[$i]->l0 !!}"></td>
                      <td class="align-middle text-center"><input type="checkbox" id="{!! $allmenu[$i]->id !!}-hapus" class="aksesMenuCheckbox lvl-{!! $allmenu[$i]->l0 !!}"></td>
                      <td class="align-middle text-center"><input type="checkbox" id="{!! $allmenu[$i]->id !!}-cetak" class="aksesMenuCheckbox lvl-{!! $allmenu[$i]->l0 !!}"></td>
                      <td class="align-middle text-center"><input type="checkbox" id="{!! $allmenu[$i]->id !!}-export" class="aksesMenuCheckbox lvl-{!! $allmenu[$i]->l0 !!}"></td>
                      <td class="align-middle text-center"><input type="checkbox" id="{!! $allmenu[$i]->id !!}-otorisasi1" class="aksesMenuCheckbox lvl-{!! $allmenu[$i]->l0 !!}"></td>
                      <td class="align-middle text-center"><input type="checkbox" id="{!! $allmenu[$i]->id !!}-otorisasi2" class="aksesMenuCheckbox lvl-{!! $allmenu[$i]->l0 !!}"></td>
                      <td class="align-middle text-center"><input type="checkbox" id="{!! $allmenu[$i]->id !!}-otorisasi3" class="aksesMenuCheckbox lvl-{!! $allmenu[$i]->l0 !!}"></td>
                      <td class="align-middle text-center"><input type="checkbox" id="{!! $allmenu[$i]->id !!}-otorisasi4" class="aksesMenuCheckbox lvl-{!! $allmenu[$i]->l0 !!}"></td>
                      <td class="align-middle text-center"><input type="checkbox" id="{!! $allmenu[$i]->id !!}-otorisasi5" class="aksesMenuCheckbox lvl-{!! $allmenu[$i]->l0 !!}"></td>
                      <td class="align-middle text-center"><input type="checkbox" id="{!! $allmenu[$i]->id !!}-batalotorisasi" class="aksesMenuCheckbox lvl-{!! $allmenu[$i]->l0 !!}"></td>
                      <td class="align-middle text-center"><input type="checkbox" id="{!! $allmenu[$i]->id !!}-bataltransaksi" class="aksesMenuCheckbox lvl-{!! $allmenu[$i]->l0 !!}"></td>
                    </tr>
                  @endfor
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="resetShowAkses()">Batal</button>
        <button type="button" class="btn btn-primary" id="buttonEditAkses" onclick="editAkses()">Simpan</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal akses -->

<!-- Start modal add -->
<div class="modal fade" id="addMenu" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Tambah Menu</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="reset()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="keterangan">Keterangan</label>
            </div>
          </div>
          <div class="col-8">
            <div class="form-group">
              <input type="text" class="form-control" id="keterangan" placeholder="Masukan Nama Menu" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="l0">l0</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <input type="text" class="form-control" id="l0" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="access">Access</label>
            </div>
          </div>
          <div class="col-5">
            <div class="form-group">
              <input type="text" class="form-control" id="access" placeholder="Masukkan link Access" value="/" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="ol">ol</label>
            </div>
          </div>
          <div class="col-1">
            <div class="form-group">
              <input type="text" class="form-control" id="ol" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="parent">Parent</label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <input type="text" class="form-control" id="parent" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="show_acc">Show Acc</label>
            </div>
          </div>
          <div class="col-1">
            <div class="form-group">
              <input type="text" class="form-control" id="show_acc" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="grup">Grup</label>
            </div>
          </div>
          <div class="col-5">
            <div class="form-group">
              <select class="form-control" id="grup" required>
                <option value="0">-- Pilih Grup --</option>
                <option value="8">Master Data</option>
                <option value="55">Accounting</option>
                <option value="69">Pengadaan</option>
                <option value="82">Marketing</option>
                <option value="95">POS</option>
                <option value="97">Produksi</option>
                <option value="102">Gudang</option>
                <option value="114">Laporan</option>
                <option value="227">Utilitas</option>
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
</div>
<!-- End modal add -->

<!-- Start modal edit -->
<div class="modal fade" id="editPemakai" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Ubah Pemakai</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="reset('e')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="eid" required>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="eketerangan">Keterangan</label>
            </div>
          </div>
          <div class="col-8">
            <div class="form-group">
              <input type="text" class="form-control" id="eketerangan" placeholder="Masukan Nama Menu" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="el0">l0</label>
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <input type="text" class="form-control" id="el0" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="eaccess">Access</label>
            </div>
          </div>
          <div class="col-5">
            <div class="form-group">
              <input type="text" class="form-control" id="eaccess" placeholder="Masukkan link Access" value="/" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="eol">ol</label>
            </div>
          </div>
          <div class="col-1">
            <div class="form-group">
              <input type="text" class="form-control" id="eol" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="eparent">Parent</label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <input type="text" class="form-control" id="eparent" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="eshow_acc">Show Acc</label>
            </div>
          </div>
          <div class="col-1">
            <div class="form-group">
              <input type="text" class="form-control" id="eshow_acc" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label for="egrup">Grup</label>
            </div>
          </div>
          <div class="col-5">
            <div class="form-group">
              <select class="form-control" id="egrup" required>
                <option value="0">-- Pilih Grup --</option>
                <option value="8">Master Data</option>
                <option value="55">Accounting</option>
                <option value="69">Pengadaan</option>
                <option value="82">Marketing</option>
                <option value="95">POS</option>
                <option value="97">Produksi</option>
                <option value="102">Gudang</option>
                <option value="114">Laporan</option>
                <option value="227">Utilitas</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="reset('e')">Batal</button>
        <button type="button" class="btn btn-primary" onclick="edit()">Ubah</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal edit -->

@endsection

@section('js')
<script type="text/javascript">

  var arrayAksesMenu = [], g_id = "", g_user = "", g_name = "", g_level = "";
  // start document ready
	$(document).ready(function(){
    $("#buttonEditAkses").hide();
    $("#tabelAksesMenu").hide();
    // $("#editAksesMenu").modal('toggle');
    // showAkses();
    // start checkbox onchange
    $('.aksesMenuCheckbox').change(function () {
      var isChecked = false;
      var idCheckbox = $(this).attr('id').split("-");
      if ($(this).is(":checked")) {
        isChecked = true;
        arrayAksesMenu[parseInt(idCheckbox[0]) - 1][idCheckbox[1]] = 1;
      } else {
        arrayAksesMenu[parseInt(idCheckbox[0]) - 1][idCheckbox[1]] = 0;
      }
      var classCheckbox = $(this).attr('class').split(' ');
      var classLevel = classCheckbox[1].split('-');
      var level = parseInt(classLevel[1]);
      var currentTD = $(this).parent('td');
      var cellIndex = currentTD.index();
      var nextCheckbox = currentTD.closest('tr').next().children().eq(cellIndex).children().eq(0);
      var previousCheckbox = currentTD.closest('tr').prev().children().eq(cellIndex).children().eq(0);
      while (nextCheckbox.length) {
        classCheckbox = nextCheckbox.attr('class').split(' ');
        classLevel = classCheckbox[1].split('-');
        nextLevel = parseInt(classLevel[1]);
        if (level < nextLevel) {
          nextCheckbox.prop("checked", isChecked);
          var idCheckbox = nextCheckbox.attr('id').split("-");
          if (isChecked) {
            arrayAksesMenu[parseInt(idCheckbox[0]) - 1][idCheckbox[1]] = 1;
          } else {
            arrayAksesMenu[parseInt(idCheckbox[0]) - 1][idCheckbox[1]] = 0;
          }
          if (nextCheckbox.parent('td').closest('tr').next().children().eq(cellIndex).children().length > 0) {
            nextCheckbox = nextCheckbox.parent('td').closest('tr').next().children().eq(cellIndex).children().eq(0);
          }
          else {
            break;
          }
        }
        else {
          break;
        }
      }
      while (previousCheckbox.length) {
        classCheckbox = previousCheckbox.attr('class').split(' ');
        classLevel = classCheckbox[1].split('-');
        prevLevel = parseInt(classLevel[1]);
        if (level == prevLevel) {
          if (previousCheckbox.parent('td').closest('tr').prev().children().eq(cellIndex).children().length > 0) {
            previousCheckbox = previousCheckbox.parent('td').closest('tr').prev().children().eq(cellIndex).children().eq(0);
          }
          else {
            break;
          }
        }
        else if (level > prevLevel) {
          var idCheckbox = previousCheckbox.attr('id').split("-");
          if (isChecked) {
            arrayAksesMenu[parseInt(idCheckbox[0]) - 1][idCheckbox[1]] = 1;
            previousCheckbox.prop("checked", isChecked);
          }
          if (previousCheckbox.parent('td').closest('tr').prev().children().eq(cellIndex).children().length > 0) {
            previousCheckbox = previousCheckbox.parent('td').closest('tr').prev().children().eq(cellIndex).children().eq(0);
          }
          else {
            break;
          }
        }
        else {
          break;
        }
        if (prevLevel == 0) { break; }
      }
    });
    // end checkbox onchange

	});
  // end document ready

  function select(_id, user, name, level) {
    $('#users_data > tr').each(function() {
      $(this).css('background-color', '');
    });
    $("#"+_id+"-tr").css('background-color', 'gold');
    g_id = _id; g_user = user; g_name = name; g_level = level;
  }

  // start refresh tabel
  function loadAll() {
    $.ajax({
      url     : "{!! url('loadAllUsersUtama') !!}",
      type    : "GET",
      async   : false,
      success : function(result) {
        if (result.length > 0) {
          $('#users_data').html("");
          var str = ""; var str2 = '<option value="">-- Pilih nama pengguna --</option>';
          for (var i = 0; i < result.length; i++) {
            if (result[i].id != {!! \Auth::id() !!}) { str += "<tr id='" + result[i].id + "-tr' onclick='select(" + result[i].id + ", \"" + result[i].username + "\", \"" + result[i].name + "\", \"" + result[i].level + "\")'>"; }
            else { str += "<tr id='" + result[i].id + "-tr' onclick='select(" + result[i].id + ", \"" + result[i].username + "\", \"" + result[i].name + "\", \"" + result[i].level + "\")'>"; }
            str += "<td>" + result[i].username + "</td><td>" + result[i].name + "</td>";
            if (result[i].level == 0) { str += "<td>User</td>"; } else if (result[i].level == 1) { str += "<td>Supervisor</td>"; } else { str += "<td>Administrator</td>"; }
            if (result[i].status == 0) { str += "<td style='color: red;'>Offline</td>"; } else { str += "<td style='color: green;'>Online</td>"; }
            str += "<td>" + result[i].hostid + "</td><td class='align-middle'>" + result[i].ipaddress + "</td></tr>";

            str2 += '<option value="'+result[i].id+'">'+result[i].name+'</option>';
          }
          $('#users_data').html(str);
          $('#aksesUsername').html(str2);
          $('#salinAksesUsername').html(str2);
        }
        else {
          $('#users_data').html("<tr><td colspan='6'>Tidak ada data ditemukan</td></tr>");
        }
      }
    });
  }
  // end refresh table

  // start reset input
  function reset(name = "") {
    $("#" + name + "username").val("");
    $("#" + name + "password").val("");
    $("#" + name + "cpassword").val("");
    $("#" + name + "fullname").val("");
    $("#" + name + "level").val("");
  }
  // end reset input

  // start validasi input
  function validate(token, username, password, cpassword, fullname, level) {
    if (token != "" && username != "" && password != "" && cpassword != "" && fullname != "" && level != "") {
      if (password == cpassword){ return 1; }
      else { return 2; }
    }
    else { return 3; }
  }
  // end validasi input

  // start insert pemakai
  function add() {
    var _token = $("#_token").val();
    var _username = $("#username").val();
    var _password = $("#password").val();
    var _cpassword = $("#cpassword").val();
    var _fullname = $("#fullname").val();
    var _level = $("#level").val();
    var check = validate(_token, _username, _password, _cpassword, _fullname, _level);
    if (check == 1) {
      $.ajax({
        url     : "{!! url('addUserUtama') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          username : _username,
          password : _password,
          fullname : _fullname,
          level : _level
        },
        success : function(result) {
          if (result == 1) {
            reset();
            loadAll();
            loadAllDropdown();
            alertify.success('Data pemakai telah ditambahkan.');
          } else {
            alertify.success('Nama pengguna sudah ada.');
          }
        }
      });
    }
    else if (check == 2) {
      alertify.alert('Gagal menambahkan data pemakai!', 'Kata Sandi dan Konfirmasi Kata Sandi harus sama.', function(){ });
    }
    else {
      alertify.alert('Gagal menambahkan data pemakai!', 'Semua kolom harus terisi.', function(){ });
    }
  }
  // end insert pemakai

  // start tampilkan pemakai
  function show() {
    if (g_id === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    if (g_id === {!! \Auth::id() !!}) {
      alertify.warning("Tidak dapat mengubah user");
      return;
    }
    $("#eid").val(g_id);
    $("#eusername").val(g_user);
    $("#efullname").val(g_name);
    $("#elevel").val(g_level);
    $("#editPemakai").modal('toggle');
  }
  // end tampilkan pemakai

  // start edit pemakai
  function edit() {
    var _token = $("#_token").val();
    var _id = $("#eid").val();
    var _username = $("#eusername").val();
    var _fullname = $("#efullname").val();
    var _level = $("#elevel").val();
    var check = validate(_token, _username, "-", "-", _fullname, _level);
    if (check == 1 && _id != "") {
      $.ajax({
        url     : "{!! url('editUserUtama') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          id : _id,
          username : _username,
          fullname : _fullname,
          level : _level
        },
        success : function(result) {
          $("#editPemakai").modal('toggle');
          reset("e");
          loadAll();
          alertify.success('Data pemakai telah diubah.');
        }
      });
    }
    else if (check == 2) {
      alertify.alert('Gagal mengubah data pemakai!', 'Kata Sandi dan Konfirmasi Kata Sandi harus sama.', function(){ });
    }
    else {
      alertify.alert('Gagal mengubah data pemakai!', 'Semua kolom harus terisi.', function(){ });
    }
  }
  // end edit pemakai

  // start hapus pemakai
  function erase() {
    if (g_id === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    if (g_id === {!! \Auth::id() !!}) {
      alertify.warning("Tidak dapat menghapus pemakai");
      return;
    }
    alertify.confirm('Hapus Pemakai', 'Apakah yakin ingin menghapus pemakai dengan nama pengguna ' + g_user + ' ?',
      function(){
        var _token = $("#_token").val();
        $.ajax({
          url     : "{!! url('eraseUserUtama') !!}",
          type    : "POST",
          async   : false,
          data    : {
            _token : _token,
            id : g_id
          },
          success : function(result) {
            loadAll();
            alertify.success('Data pemakai telah dihapus.');
          }
        });
    },function(){});
  }
  // end hapus pemakai

  // start showAkses
  function showAkses() {
    g_id = 9;
    if (g_id === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    var _token = $("#_token").val();
    $("#aksesUsername").val(g_id);
    $("#editAksesMenu").modal('toggle');
    $.ajax({
      url     : "{!! url('getAksesMenuUtama') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        id : g_id
      },
      success : function(result) {
        $(".aksesMenuCheckbox").prop("checked",false);
        arrayAksesMenu = [];
        for (var i = 0; i < result.length; i++) {
          arrayAksesMenu[i] = {};
          arrayAksesMenu[i]['tampil'] = result[i].tampil;
          arrayAksesMenu[i]['tambah'] = result[i].tambah;
          arrayAksesMenu[i]['koreksi'] = result[i].koreksi;
          arrayAksesMenu[i]['hapus'] = result[i].hapus;
          arrayAksesMenu[i]['cetak'] = result[i].cetak;
          arrayAksesMenu[i]['export'] = result[i].export;
          arrayAksesMenu[i]['otorisasi1'] = result[i].otorisasi1;
          arrayAksesMenu[i]['otorisasi2'] = result[i].otorisasi2;
          arrayAksesMenu[i]['otorisasi3'] = result[i].otorisasi3;
          arrayAksesMenu[i]['otorisasi4'] = result[i].otorisasi4;
          arrayAksesMenu[i]['otorisasi5'] = result[i].otorisasi5;
          arrayAksesMenu[i]['batalotorisasi'] = result[i].batalotorisasi;
          arrayAksesMenu[i]['bataltransaksi'] = result[i].bataltransaksi;
          if (result[i].tampil == 1) { $("#" + result[i].id_menu + "-tampil").prop("checked", true); }
          if (result[i].tambah == 1) { $("#" + result[i].id_menu + "-tambah").prop("checked", true); }
          if (result[i].koreksi == 1) { $("#" + result[i].id_menu + "-koreksi").prop("checked", true); }
          if (result[i].hapus == 1) { $("#" + result[i].id_menu + "-hapus").prop("checked", true); }
          if (result[i].cetak == 1) { $("#" + result[i].id_menu + "-cetak").prop("checked", true); }
          if (result[i].export == 1) { $("#" + result[i].id_menu + "-export").prop("checked", true); }
          if (result[i].otorisasi1 == 1) { $("#" + result[i].id_menu + "-otorisasi1").prop("checked", true); }
          if (result[i].otorisasi2 == 1) { $("#" + result[i].id_menu + "-otorisasi2").prop("checked", true); }
          if (result[i].otorisasi3 == 1) { $("#" + result[i].id_menu + "-otorisasi3").prop("checked", true); }
          if (result[i].orotisasi4 == 1) { $("#" + result[i].id_menu + "-orotisasi4").prop("checked", true); }
          if (result[i].otorisasi5 == 1) { $("#" + result[i].id_menu + "-otorisasi5").prop("checked", true); }
          if (result[i].batalotorisasi == 1) { $("#" + result[i].id_menu + "-batalotorisasi").prop("checked", true); }
          if (result[i].bataltransaksi == 1) { $("#" + result[i].id_menu + "-bataltransaksi").prop("checked", true); }
        }
        $("#buttonEditAkses").show();
        $("#tabelAksesMenu").show();
        $("#salinAksesUsername").prop("disabled", false);
        // $("#salinAksesUsername").html($("#aksesUsername").html());
        // $("#salinAksesUsername option[value='" + $("#aksesUsername").val() + "']").remove();
      }
    });
  }
  // end showAkses
  function editSetMenu() {
    var _token = $("#_token").val();
    // var _grup1 = $("#1").val();
    var gr2 = $("#2").prop('checked');
    var gr3 = $("#3").prop('checked');
    var gr4 = $("#4").prop('checked');
    var gr5 = $("#5").prop('checked');
    var gr6 = $("#6").prop('checked');
    var gr7 = $("#7").prop('checked');
    var gr8 = $("#8").prop('checked');
    var gr9 = $("#9").prop('checked');
    // var gr10 = $("#10").prop('checked');

    if(gr2 == true){
      var _grup2 = 1;
    }else{
      var _grup2 = 0;
    }
    if(gr3 == true){
      var _grup3 = 1;
    }else{
      var _grup3 = 0;
    }
    if(gr4 == true){
      var _grup4 = 1;
    }else{
      var _grup4 = 0;
    }
    if(gr5 == true){
      var _grup5 = 1;
    }else{
      var _grup5 = 0;
    }
    if(gr6 == true){
      var _grup6 = 1;
    }else{
      var _grup6 = 0;
    }
    if(gr7 == true){
      var _grup7 = 1;
    }else{
      var _grup7 = 0;
    }
    if(gr8 == true){
      var _grup8 = 1;
    }else{
      var _grup8 = 0;
    }
    if(gr9 == true){
      var _grup9 = 1;
    }else{
      var _grup9 = 0;
    }
    // if(gr10 == true){
    //   var _grup10 = 1;
    // }else{
    //   var _grup10 = 0;
    // }
    $.ajax({
      url     : "{!! url('editSetMenuUtama') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        // grup1 : _grup1,
        grup2 : _grup2,
        grup3 : _grup3,
        grup4 : _grup4,
        grup5 : _grup5,
        grup6 : _grup6,
        grup7 : _grup7,
        grup8 : _grup8,
        grup9 : _grup9
        // grup10 : _grup10
      },
      success : function(result) {
        alertify.success('Akses Menu Utama berhasil diubah.');
      }
    });
  }
  // start edit akses
  function editAkses() {
    var _token = $("#_token").val();
    var _id = $("#aksesUsername").val();
    $.ajax({
      url     : "{!! url('editAksesMenuUtama') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        id : _id,
        data : arrayAksesMenu
      },
      success : function(result) {
        $("#editAksesMenu").modal('toggle');
        alertify.success('Akses menu pemakai berhasil diubah.');
        resetShowAkses();
      }
    });
  }
  // end edit akses

  // start reset akses
  function resetShowAkses() {
    arrayAksesMenu = [];
    $("#aksesUsername").val("");
    $("#buttonEditAkses").hide();
    $("#tabelAksesMenu").hide();
    $("#salinAksesUsername").prop("disabled", true);
    $("#salinAksesUsername").val("");
    // $("#salinAksesUsername").html($("#aksesUsername").html());
  }
  // end reset akses

  function pilihcek(_ket,_id_ket) {
    var _token = $("#_token").val();
    var _id = _id_ket;
    var _username = _ket;
    if (_id != "") {
      alertify.confirm('Salin Akses Menu Utama', 'Apakah yakin ingin menyalin akses menu ' + _username + ' ?',
        function(){
          $.ajax({
            url     : "{!! url('getAksesMenuUtamaPilih') !!}",
            type    : "POST",
            async   : false,
            data    : {
              _token : _token,
              id : _id
            },
            success : function(result) {
              $(".aksesMenuCheckbox").prop("checked",false);
              arrayAksesMenu = [];
              for (var i = 0; i < result.length; i++) {
                arrayAksesMenu[i] = {};
                arrayAksesMenu[i]['tampil'] = 1;
                arrayAksesMenu[i]['tambah'] = 1;
                arrayAksesMenu[i]['koreksi'] = 1;
                arrayAksesMenu[i]['hapus'] = 1;
                arrayAksesMenu[i]['cetak'] = 1;
                arrayAksesMenu[i]['export'] = 1;
                arrayAksesMenu[i]['otorisasi1'] = 1;
                arrayAksesMenu[i]['otorisasi2'] = 0;
                arrayAksesMenu[i]['otorisasi3'] = 0;
                arrayAksesMenu[i]['otorisasi4'] = 0;
                arrayAksesMenu[i]['otorisasi5'] = 0;
                arrayAksesMenu[i]['batalotorisasi'] = 1;
                arrayAksesMenu[i]['bataltransaksi'] = 1;
                if (1 == 1) { $("#" + result[i].id + "-tampil").prop("checked", true); }
                if (1 == 1) { $("#" + result[i].id + "-tambah").prop("checked", true); }
                if (1 == 1) { $("#" + result[i].id + "-koreksi").prop("checked", true); }
                if (1 == 1) { $("#" + result[i].id + "-hapus").prop("checked", true); }
                if (1 == 1) { $("#" + result[i].id + "-cetak").prop("checked", true); }
                if (1 == 1) { $("#" + result[i].id + "-export").prop("checked", true); }
                if (1 == 1) { $("#" + result[i].id + "-otorisasi1").prop("checked", true); }
                if (0 == 1) { $("#" + result[i].id + "-otorisasi2").prop("checked", true); }
                if (0 == 1) { $("#" + result[i].id + "-otorisasi3").prop("checked", true); }
                if (0 == 1) { $("#" + result[i].id + "-otorisasi4").prop("checked", true); }
                if (0 == 1) { $("#" + result[i].id + "-otorisasi5").prop("checked", true); }
                if (1 == 1) { $("#" + result[i].id + "-batalotorisasi").prop("checked", true); }
                if (1 == 1) { $("#" + result[i].id + "-bataltransaksi").prop("checked", true); }
              }
            }
          });
      },function(){
        $("#salinAksesUsername").val("");
      });
    }
  }

  // start copy akses
  function salinAkses() {
    var _token = $("#_token").val();
    var _id = $("#salinAksesUsername").val();
    var _username = $("#salinAksesUsername option:selected").text();
    if (_id != "") {
      alertify.confirm('Salin akses menu pemakai', 'Apakah yakin ingin menyalin akses menu dari user dengan nama pengguna ' + _username + ' ?',
        function(){
          $.ajax({
            url     : "{!! url('getAksesMenuUtama') !!}",
            type    : "POST",
            async   : false,
            data    : {
              _token : _token,
              id : _id
            },
            success : function(result) {
              $(".aksesMenuCheckbox").prop("checked",false);
              arrayAksesMenu = [];
              for (var i = 0; i < result.length; i++) {
                arrayAksesMenu[i] = {};
                arrayAksesMenu[i]['tampil'] = result[i].tampil;
                arrayAksesMenu[i]['tambah'] = result[i].tambah;
                arrayAksesMenu[i]['koreksi'] = result[i].koreksi;
                arrayAksesMenu[i]['hapus'] = result[i].hapus;
                arrayAksesMenu[i]['cetak'] = result[i].cetak;
                arrayAksesMenu[i]['export'] = result[i].export;
                arrayAksesMenu[i]['otorisasi1'] = result[i].otorisasi1;
                arrayAksesMenu[i]['otorisasi2'] = result[i].otorisasi2;
                arrayAksesMenu[i]['otorisasi3'] = result[i].otorisasi3;
                arrayAksesMenu[i]['otorisasi4'] = result[i].otorisasi4;
                arrayAksesMenu[i]['otorisasi5'] = result[i].otorisasi5;
                arrayAksesMenu[i]['batalotorisasi'] = result[i].batalotorisasi;
                arrayAksesMenu[i]['bataltransaksi'] = result[i].bataltransaksi;
                if (result[i].tampil == 1) { $("#" + result[i].id_menu + "-tampil").prop("checked", true); }
                if (result[i].tambah == 1) { $("#" + result[i].id_menu + "-tambah").prop("checked", true); }
                if (result[i].koreksi == 1) { $("#" + result[i].id_menu + "-koreksi").prop("checked", true); }
                if (result[i].hapus == 1) { $("#" + result[i].id_menu + "-hapus").prop("checked", true); }
                if (result[i].cetak == 1) { $("#" + result[i].id_menu + "-cetak").prop("checked", true); }
                if (result[i].export == 1) { $("#" + result[i].id_menu + "-export").prop("checked", true); }
                if (result[i].otorisasi1 == 1) { $("#" + result[i].id_menu + "-otorisasi1").prop("checked", true); }
                if (result[i].otorisasi2 == 1) { $("#" + result[i].id_menu + "-otorisasi2").prop("checked", true); }
                if (result[i].otorisasi3 == 1) { $("#" + result[i].id_menu + "-otorisasi3").prop("checked", true); }
                if (result[i].orotisasi4 == 1) { $("#" + result[i].id_menu + "-otorisasi4").prop("checked", true); }
                if (result[i].otorisasi5 == 1) { $("#" + result[i].id_menu + "-otorisasi5").prop("checked", true); }
                if (result[i].batalotorisasi == 1) { $("#" + result[i].id_menu + "-batalotorisasi").prop("checked", true); }
                if (result[i].bataltransaksi == 1) { $("#" + result[i].id_menu + "-bataltransaksi").prop("checked", true); }
              }
            }
          });
      },function(){
        $("#salinAksesUsername").val("");
      });
    }
  }
  // end copy akses

  // start refresh dropdown
  function loadAllDropdown() {
    $.ajax({
      url     : "{!! url('loadAllUsersUtama') !!}",
      type    : "GET",
      async   : false,
      success : function(result) {
        str = '<option value="">-- Pilih nama pengguna --</option>';
        if (result.length > 0) {
          for (var i = 0; i < result.length; i++) {
            str = str + '<option value="' + result[i].id + '">' + result[i].name + '</option>';
          }
        }
        $("#aksesUsername").html(str);
      }
    });
  }
  // end refresh dropdown

</script>
@endsection
