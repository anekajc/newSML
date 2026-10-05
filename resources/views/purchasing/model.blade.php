@extends('master')

@section('css')
@endsection

@section('breadcrumb')
<li class="nav-item">
  <a class="nav-link" href="#" data-toggle="tooltip" data-placement="bottom" title="Master Data / Master Bahan, Barang & Jasa / Master Group Bahan Baku / Model"><span class="blue" id="title_page">Model</span></a>
</li>
@endsection

@section('button-add-refresh')
<button type="button" class="btn btn-primary btn-sm btn-top" onclick="loadAll()" rel="tooltip" data-placement="bottom" title="Refresh" style="width: 30px;"><i class="fas fa-sync-alt"></i></button>&nbsp;&nbsp;
@if ($akses->tambah == 1)
  <button class="btn btn-success btn-sm btn-top" type="button" data-toggle="modal" data-target="#addModel" rel="tooltip" data-placement="bottom" title="Tambah" style="width: 30px;"><i class="fas fa-plus-circle"></i></button>&nbsp;&nbsp;
@endif
@if ($akses->koreksi == 1)
  <button type="button" class="btn btn-warning btn-sm btn-top" onclick="show()" rel="tooltip" data-placement="bottom" title="Ubah" style="width: 30px;"><i class="fas fa-pencil-alt"></i></button>&nbsp;&nbsp;
@endif
@if ($akses->hapus == 1)
  <button type="button" class="btn btn-danger btn-sm btn-top" onclick="erase()" rel="tooltip" data-placement="bottom" title="Hapus" style="width: 30px;"><i class="fas fa-trash"></i></button>
@endif
@endsection

@section('content')
<div class="container">
  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <div class="card">
    <div class="card-body" style="background: palegoldenrod;">
      <div class="row">
        <div class="col-8 offset-md-2" style="padding-left: 20px; padding-right: 20px; overflow:auto;">
          <table class="table table-bordered table-striped" id="tabel_model" style=" background: white;">
            <thead>
              <tr>
                <th width="30%">Kode</th>
                <th>Nama</th>
              </tr>
            </thead>
            <tbody id="model_data">
              @if (count($model) > 0)
                @for ($i = 0; $i < count($model); $i++)
                  <tr id="{!! $i !!}-tr" onclick="select({!! $i !!}, {!! $model[$i]->id !!}, '{!! $model[$i]->kode !!}', '{!! $model[$i]->nama !!}')">
                    <td>{!! $model[$i]->kode !!}</td>
                    <td>{!! $model[$i]->nama !!}</td>
                  </tr>
                @endfor
              @else
                <tr>
                  <td colspan="2">Tidak ada data model ditemukan.</td>
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

<!-- start modal add model -->
<div class="modal fade" id="addModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Tambah Model</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="reset()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="kode">Kode Model</label>
          <input type="text" class="form-control" id="kode" placeholder="Masukkan Kode Model" required>
        </div>
        <div class="form-group">
          <label for="nama">Nama Model</label>
          <input type="text" class="form-control" id="nama" placeholder="Masukkan Nama Model" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="reset()">Batal</button>
        <button type="button" class="btn btn-primary" onclick="add()">Tambah</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal add model -->

<!-- start modal edit model -->
<div class="modal fade" id="editModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Ubah Model</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="reset('e')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="eid" required>
        <div class="form-group">
          <label for="ekode">Kode Model</label>
          <input type="text" class="form-control" id="ekode" placeholder="Masukkan Kode Model" required>
        </div>
        <div class="form-group">
          <label for="enama">Nama Model</label>
          <input type="text" class="form-control" id="enama" placeholder="Masukkan Nama Model" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="reset('e')">Batal</button>
        <button type="button" class="btn btn-primary" onclick="edit()">Edit</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal edit model -->
@endsection

@section('js')
<script type="text/javascript">

  var g_id = "", g_kode = "", g_nama = "";
  // start document ready
	$(document).ready(function(){
    var table = $('#tabel_model').DataTable({"lengthMenu": [[-1, 10, 25, 50, 100], ["All", 10, 25, 50, 100]]});
    table.on('search', function () {
      $('#model_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_kode = "", g_nama = "";
    } );
    table.on('order', function () {
      $('#model_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_kode = "", g_nama = "";
    } );
	});
  // end document ready

  // start reset input
  function reset(name = "") {
    $("#" + name + "kode").val("");
    $("#" + name + "nama").val("");
  }
  // end reset input

  // start refresh tabel
  function loadAll() {
    $('#tabel_model').DataTable().destroy();
    $('#model_data > tr').each(function() {
      $(this).css('background-color', '');
    });
    g_id = "", g_kode = "", g_nama = "";
    $.ajax({
      url     : "{!! url('loadAllModel') !!}",
      type    : "GET",
      async   : false,
      success : function(result) {
        if (result.length > 0) {
          $('#model_data').html("");
          var str = "";
          for (var i = 0; i < result.length; i++) {
            str = str + '<tr id="'+i+'-tr" onclick="select(' + i + ', ' + result[i].id + ', \'' + result[i].kode + '\', \'' + result[i].nama + '\')">\
            <td>' + result[i].kode + '</td>\
              <td>' + result[i].nama + '</td></tr>';
          }
          $('#model_data').html(str);
        }
        else {
          $('#model_data').html('<tr>\
            <td colspan="2">Tidak ada data model ditemukan.</td>\
            <td style="display: none;"></td>\
          </tr>');
        }
        middleTD();
      }
    });
    var table = $('#tabel_model').DataTable({"lengthMenu": [[-1, 10, 25, 50, 100], ["All", 10, 25, 50, 100]]});
    table.on('search', function () {
      $('#model_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_kode = "", g_nama = "";
    } );
    table.on('order', function () {
      $('#model_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_kode = "", g_nama = "";
    } );
  }
  // end refresh table

  function select(_row, _id, _kode, _nama) {
    $('#model_data > tr').each(function() {
      $(this).css('background-color', '');
    });
    $("#"+_row+"-tr").css('background-color', 'gold');
    g_id = _id; g_kode = _kode; g_nama = _nama;
  }

  // start add model
  function add() {
    var _token = $("#_token").val();
    var _kode = $("#kode").val();
    var _nama = $("#nama").val();
    if (_kode != "" && _nama != "") {
      $.ajax({
        url     : "{!! url('addModel') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          kode : _kode,
          nama : _nama
        },
        success : function(result) {
          if (result == 1) {
            reset();
            loadAll();
            alertify.success('Data model telah ditambahkan.');
          } else {
            alertify.alert('Gagal menambahkan data model!', 'Kode model sudah ada.', function(){ });
          }
        }
      });
    }
    else {
      alertify.alert('Gagal menambahkan data model!', 'Semua kolom harus terisi.', function(){ });
    }
  }
  // end add model

  // start tampilkan model
  function show() {
    if (g_id === "" || g_kode === "" || g_nama === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    $("#eid").val(g_id);
    $("#ekode").val(g_kode);
    $("#enama").val(g_nama);
    $("#editModel").modal('toggle');
  }
  // end tampilkan model

  // start edit model
  function edit() {
    var _token = $("#_token").val();
    var _id = $("#eid").val();
    var _kode = $("#ekode").val();
    var _nama = $("#enama").val();
    if (_id != "" && _kode != "" && _nama != "") {
      $.ajax({
        url     : "{!! url('editModel') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          id : _id,
          kode : _kode,
          nama : _nama
        },
        success : function(result) {
          if (result == 1) {
            $("#editModel").modal('toggle');
            reset("e");
            loadAll();
            alertify.success('Data model telah diubah.');
          } else {
            alertify.alert('Gagal mengubah data model!', 'Kode model sudah ada.', function(){ });
          }
        }
      });
    }
    else {
      alertify.alert('Gagal mengubah data model!', 'Semua kolom harus terisi.', function(){ });
    }
  }
  // end edit model

  // start hapus model
  function erase() {
    if (g_id === "" || g_nama === "") {
      alertify.warning("Tidak ada baris dipilih");
      return;
    }
    alertify.confirm('Hapus Model', 'Apakah yakin ingin menghapus model dengan nama ' + g_nama + ' ?',
      function(){
        var _token = $("#_token").val();
        $.ajax({
          url     : "{!! url('eraseModel') !!}",
          type    : "POST",
          async   : false,
          data    : {
            _token : _token,
            id : g_id
          },
          success : function(result) {
            loadAll();
            alertify.success('Data model telah dihapus.');
          }
        });
    },function(){});
  }
  // end hapus model
</script>
@endsection
