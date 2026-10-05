@extends('master')

@section('css')
@endsection

@section('breadcrumb')
<li class="nav-item">
  <a class="nav-link" href="#" data-toggle="tooltip" data-placement="bottom" title="Berkas / Set Periode Kerja"><span class="blue" id="title_page">Set Periode Kerja</span></a>
</li>
@endsection

@section('content')
<div class="container">
  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <div class="col-sm-12 col-md-6 offset-md-3">
    <div class="card">
      <div class="card-body" style="background: palegoldenrod;">
        <div class="row">
          <div class="col-6">
            <div class="form-group">
              <label for="bulan">Bulan</label>
              @if ($akses->koreksi == 1)
                <select class="form-control" id="bulan" required>
              @else
                <select class="form-control" id="bulan" disabled>
              @endif
                <option value="">-- Pilih bulan --</option>
                @for ($i = 1; $i <= 12; $i++)
                  @if ($periode->bulan == $i)
                    <option value="{!! $i !!}" selected>{!! $i !!}</option>
                  @else
                    <option value="{!! $i !!}">{!! $i !!}</option>
                  @endif
                @endfor
              </select>
            </div>
          </div>
          <div class="col-6">
            <div class="form-group">
              <label for="tahun">Tahun</label>
              @if ($akses->koreksi == 1)
                <input type="number" class="form-control" id="tahun" value="{!! $periode->tahun !!}" required>
              @else
                <input type="number" class="form-control" id="tahun" value="{!! $periode->tahun !!}" disabled>
              @endif
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-10 offset-2">
            @if ($akses->koreksi == 1)
              <button type="button" class="btn btn-primary float-right" onclick="edit()">Simpan</button>
            @else
              <button type="button" class="btn btn-primary float-right" disabled>Simpan</button>
            @endif
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
	});
  // end document ready

  // start edit
  function edit() {
    var _token = $("#_token").val();
    var _bulan = $("#bulan").val();
    var _tahun = $("#tahun").val();
    if (_bulan != "" && _tahun != "") {
      $.ajax({
        url     : "{!! url('editPeriode') !!}",
        type    : "POST",
        async   : false,
        data    : {
          _token : _token,
          bulan : _bulan,
          tahun : _tahun
        },
        success : function(result) {
          location.reload();
        }
      });
    } else {
      alertify.alert('Gagal mengubah data periode kerja!', 'Semua kolom harus terisi.', function(){ });
    }
  }
  // end edit
</script>
@endsection
