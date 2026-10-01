@extends('master')

@section('css')

@endsection

@section('breadcrumb')
<li class="nav-item">
  <a class="nav-link" href="#" data-toggle="tooltip" data-placement="bottom" title="{!! $breadcrumb !!}"><span class="blue" id="title_page">{!! $halaman !!}</span></a>
</li>
@endsection

@section('button-add-refresh')
<button type="button" class="btn btn-primary btn-sm btn-top" onclick="loadAll()" data-toggle="tooltip" data-placement="bottom" title="Refresh" style="width: 30px;"><i class="fas fa-sync-alt"></i></button>&nbsp;&nbsp;
<button type="button" class="btn btn-secondary btn-sm btn-top" data-toggle="modal" data-target="#setting" rel="tooltip" data-placement="bottom" title="Setting" style="width: 30px;"><i class="fas fa-cogs"></i></button>&nbsp;&nbsp;
@if ($akses->cetak == 1)
  <form class="" action="{!! url('cetakLaporan') !!}" target="_blank" method="POST">
    <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
    <input type="hidden" name="title" value="{!! $title !!}">
    <input type="hidden" name="table" id="tabel-cetak">
    <input type="hidden" name="paper" value="A4">
    <input type="hidden" name="periode" id="periode-cetak">
    <input type="hidden" name="orientation" value="landscape">
    <button type="submit" class="btn btn-info btn-sm btn-top" rel="tooltip" data-placement="bottom" title="Cetak" style="width: 30px;"><i class="fas fa-print"></i></button>&nbsp;&nbsp;
  </form>
@endif
@if ($akses->export == 1)
  <button type="button" class="btn btn-dark btn-sm btn-top" data-toggle="modal" data-target="#export" rel="tooltip" data-placement="bottom" title="Export" style="width: 30px;"><i class="fas fa-file"></i></button>&nbsp;&nbsp;
@endif
@endsection

@section('content')
<div class="container-fluid">
  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <div class="card">
    <div class="card-body" style="background: palegoldenrod;">
      <div class="row">
        <div class="col-12" id="info"></div>
        <div class="col-12" id="data" style="padding-left: 1%; padding-right: 1%; overflow:auto;">
          {!! $table !!}
        </div>
      </div>
    </div>
  </div>
</div>

<!-- start modal setting -->
<div class="modal fade" id="setting" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Setting</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        @if ($settingtgl == 1)
          <div class="form-group">
            <div class="row" style="padding-left: 9px; padding-right: 9px;">
              <div class="col-4">
                <label for="">Set Range Tanggal</label>
                <input type="date" class="form-control" id="min_tgl" onchange="setMin()">
              </div>
              <div class="col-4">
                <label for="">&nbsp;&nbsp;</label>
                <input type="date" class="form-control" id="max_tgl" onchange="setMin()">
              </div>
              <div class="col-4">
                <label for="filter">Tipe</label>
                @if ($halaman == "PR" || $halaman == "PO" || $halaman == "Uang Muka Beli" || $halaman == "Perintah Retur Pembelian" || $halaman == "SO" || $halaman == "Uang Muka Jual" || $halaman == "Perintah Retur Penjualan"
                   || $halaman == "Permintaan Pemakaian" || $halaman == "Perintah Opname" || $halaman == "Permintaan Transfer Barang" || $halaman == "Transfer Barang")
                  <select class="form control" id="filter" onchange="disableTanggal()">
                @else
                  <select class="form control" id="filter" onchange="disableTanggal()" disabled>
                @endif
                  <option value="semua">Semua</option>
                  <option value="outstanding">Outstanding</option>
                </select>
              </div>
            </div>
          </div>
        @endif
        <div class="form-group">
          <div class="row" style="padding-left: 9px; padding-right: 9px;">
            <div class="col-6">
              <label for="">Urutkan berdasarkan</label>
              <select class="form control" id="kolom">
                @for ($i = 0; $i < count($col); $i++)
                  <option value="{!! $col[$i] !!}">{!! $header[$i] !!}</option>
                @endfor
              </select>
            </div>
            <div class="col-6">
              <label for="">&nbsp;&nbsp;</label>
              <select class="form control" id="urut">
                <option value="ASC">Terkecil ke terbesar</option>
                <option value="DESC">Terbesar ke terkecil</option>
              </select>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label for="">Tampilkan Kolom:</label>
          <div class="row" style="padding-left: 9px; padding-right: 9px;">
            @for ($i = 0; $i < count($kolom); $i++)
              @if ($kolom[$i] != "Total")
                <div class="col-4">
                  @if ($fetch[$i] == 1)
                    <input type="checkbox" id="{!! $i !!}-cek" onchange="updateColumn({!! $i !!})" checked> {!! $kolom[$i] !!}
                  @else
                    <input type="checkbox" id="{!! $i !!}-cek" onchange="updateColumn({!! $i !!})"> {!! $kolom[$i] !!}
                  @endif
                </div>
              @else
            </div><div class="row" style="padding-left: 9px; padding-right: 9px; padding-top: 12px;">
                <div class="col-6">
                  @if ($fetch[$i] == 1)
                    <input type="checkbox" id="{!! $i !!}-cek" onchange="updateColumn({!! $i !!})" checked> Tampilkan Subtotal & Total
                  @else
                    <input type="checkbox" id="{!! $i !!}-cek" onchange="updateColumn({!! $i !!})"> Tampilkan Subtotal & Total
                  @endif
                </div>
              @endif
            @endfor
          </div>
        </div>
        <div class="form-group">
          <center><button type="button" class="btn btn-danger btn-sm" onclick="reset()">Reset Filter</button></center>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" onclick="update()">OK</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal setting -->

<!-- start modal export -->
<div class="modal fade" id="export" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Export</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="" action="{!! url('exportPDF') !!}" method="POST">
          <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
          <input type="hidden" name="title" value="{!! $title !!}">
          <input type="hidden" name="table" id="tabel-pdf">
          <input type="hidden" name="periode" id="periode-pdf">
          <div class="row">
            <div class="col-fourhalf">
              <select class="form-control" name="orientation">
                <option value="portrait">Portrait</option>
                <option value="landscape" selected>Landscape</option>
              </select>
            </div>
            <div class="col-fourhalf">
              <select class="form-control" name="paper">
                <option value="A0">A0</option>
                <option value="A1">A1</option>
                <option value="A2">A2</option>
                <option value="A3">A3</option>
                <option value="A4" selected>A4</option>
                <option value="A5">A5</option>
                <option value="A6">A6</option>
                <option value="A7">A7</option>
                <option value="A8">A8</option>
                <option value="A9">A9</option>
                <option value="B0">B0</option>
                <option value="B1">B1</option>
                <option value="B2">B2</option>
                <option value="B3">B3</option>
                <option value="B4">B4</option>
                <option value="B5">B5</option>
                <option value="B6">B6</option>
                <option value="B7">B7</option>
                <option value="B8">B8</option>
                <option value="B9">B9</option>
                <option value="B10">B10</option>
                <option value="C5E">C5E</option>
                <option value="Comm10E">Comm10E</option>
                <option value="DLE">DLE</option>
                <option value="Executive">Executive</option>
                <option value="Folio">Folio</option>
                <option value="Ledger">Ledger</option>
                <option value="Legal">Legal</option>
                <option value="Letter">Letter</option>
                <option value="Tabloid">Tabloid</option>
              </select>
            </div>
            <div class="col-3">
              <button type="submit" class="btn btn-warning"><i class="fas fa-file-pdf"></i> PDF</button>
            </div>
          </div>
        </form>
        <hr style="margin-top: 5px; margin-bottom: 5px;">
        <form class="" action="{!! url('exportEXCEL') !!}" method="POST">
          <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
          <input type="hidden" name="title" value="{!! $title !!}">
          <input type="hidden" name="table" id="tabel-excel">
          <input type="hidden" name="periode" id="periode-excel">
          <div class="row">
            <div class="col-12">
              <center><button type="submit" class="btn btn-success"><i class="fas fa-file-excel"></i> Excel</button></center>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal export -->
@endsection

@section('js')
<script type="text/javascript">
  var def_kolom = "{!! $col[0] !!}", def_urut = "ASC", kolom = "{!! $col[0] !!}", urut = "ASC";
  var fetch = {!! json_encode($fetch) !!};
  // start document ready
	$(document).ready(function(){
    restrictedDate();
    $("#tabel-cetak").val($("#tabel").html());
    $("#tabel-pdf").val($("#tabel").html());
    $("#tabel-excel").val($("#tabel").html());
    $("form").submit(function() { loadAll(); });
	});
  // end document ready

  function restrictedDate() {
    if ({!! $settingtgl !!} == 1) {
      var bln_periode = ("0"+{!! $periode->bulan !!}).slice(-2);
      var thn_periode = {!! $periode->tahun !!};
      var temp = new Date(thn_periode, bln_periode, 0);
      var minDate = temp.getFullYear()+"-"+("0" + (temp.getMonth() + 1)).slice(-2)+"-01";
      var maxDate = temp.getFullYear()+"-"+("0" + (temp.getMonth() + 1)).slice(-2)+"-"+("0" + temp.getDate()).slice(-2);
      $("#min_tgl").val(minDate); $("#max_tgl").val(maxDate);
      $("#info").html("<h6>Periode: " + format_date(minDate) + " s/d " + format_date(maxDate) + "</h6>");
      $("#periode-cetak").val("Periode: " + format_date(minDate) + " s/d " + format_date(maxDate));
      $("#periode-pdf").val("Periode: " + format_date(minDate) + " s/d " + format_date(maxDate));
      $("#periode-excel").val("Periode: " + format_date(minDate) + " s/d " + format_date(maxDate));
    }
  }

  function updateColumn(index) {
    if ($("#"+index+"-cek").prop("checked")) {
      fetch[index] = 1;
    } else {
      fetch[index] = 0;
    }
  }

  function setMin() {
    $("#max_tgl").prop('min', $("#min_tgl").val());
    if ($("#max_tgl").val() < $("#min_tgl").val()) {
      $("#max_tgl").val($("#min_tgl").val());
    }
  }

  function disableTanggal() {
    if ($("#filter").val() == "semua") {
      $("#min_tgl").prop('disabled', false);
    } else {
      $("#min_tgl").prop('disabled', true);
    }
  }

  // start refresh tabel
  function loadAll() {
    var _token = $("#_token").val();
    var minDate = $("#min_tgl").val();
    var maxDate = $("#max_tgl").val();
    var filter = $("#filter").val();
    $.ajax({
      url     : "{!! url('loadAllL'.str_replace(" ", "", $halaman)) !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
        kolom : kolom,
        urut : urut,
        fetch : fetch,
        min : minDate,
        max : maxDate,
        filter : filter
      },
      success : function(result) {
        $('#data').html(result);
        $("#tabel-cetak").val($("#tabel").html());
        $("#tabel-pdf").val($("#tabel").html());
        $("#tabel-excel").val($("#tabel").html());
        if ({!! $settingtgl !!} == 1) {
          if (filter == "semua") {
            $("#info").html("<h6>Periode: " + format_date(minDate) + " s/d " + format_date(maxDate) + "</h6>");
            $("#periode-cetak").val("Periode: " + format_date(minDate) + " s/d " + format_date(maxDate));
            $("#periode-pdf").val("Periode: " + format_date(minDate) + " s/d " + format_date(maxDate));
            $("#periode-excel").val("Periode: " + format_date(minDate) + " s/d " + format_date(maxDate));
          } else {
            $("#info").html("<h6>Periode: s/d " + format_date(maxDate) + "</h6>");
            $("#periode-cetak").val("Periode: s/d " + format_date(maxDate));
            $("#periode-pdf").val("Periode: s/d " + format_date(maxDate));
            $("#periode-excel").val("Periode: s/d " + format_date(maxDate));
          }
        }
      }
    });
  }
  // end refresh table

  function update() {
    $("#setting").modal("toggle");
    kolom = $("#kolom").val();
    urut = $("#urut").val();
    loadAll();
  }

  function reset() {
    kolom = def_kolom;
    urut = def_urut;
    $("#kolom").val(kolom);
    $("#urut").val(urut);
    for (var i = 0; i < fetch.length; i++) { fetch[i] = 1; $("#"+i+"-cek").prop("checked", true); }
    loadAll();
    $("#setting").modal("toggle");
  }
</script>
@endsection
