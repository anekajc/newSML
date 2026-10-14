@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Posting')

{{-- kp-* rules copied verbatim from berkas/newsetupperiodekerja.blade.php so this
     page matches Setup Periode Kerja. Progress bar uses Bootstrap 4.5 classes
     (progress/progress-bar-striped/progress-bar-animated) already loaded by
     newmasterTest -- no extra css file. --}}
@section('css')
{{-- report-table.css dimuat untuk gaya tombol dialog alertify (.ajs-app-buttons
     .is-success). po-table-header.css dimuat ULANG setelahnya supaya versi
     po-table-header.css menang saat spesifisitas seri, sama seperti
     gudang/pembebananpemakaian.blade.php. --}}
<link rel="stylesheet"
    href="{!! URL::asset('css/report-table.css') !!}?v={{ @filemtime(base_path('public/css/report-table.css')) ?: '1' }}">
<link rel="stylesheet"
    href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
<style>

  .kp-wrap {
    display: flex;
    justify-content: center;
    padding: 30px 0;
  }

  .kp-card {
    width: 100%;
    max-width: 420px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.08);
    overflow: hidden;
    border: 1px solid rgba(0,0,0,0.06);
  }

  .kp-header {
    display: flex;
    align-items: center;
    gap: 14px;
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    color: #fff;
    padding: 18px 20px;
  }

  .kp-header-icon {
    width: 42px;
    height: 42px;
    flex-shrink: 0;
    border-radius: 50%;
    background: rgba(255,255,255,0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
  }

  .kp-header-title {
    font-size: 17px;
    font-weight: 700;
  }

  .kp-header-subtitle {
    font-size: 12.5px;
    color: rgba(255,255,255,0.85);
    margin-top: 2px;
  }

  .kp-body {
    padding: 24px 22px 22px;
  }

  .kp-periode-row {
    display: flex;
    gap: 16px;
    margin-bottom: 24px;
  }

  .kp-periode-field {
    flex: 1;
  }

  .kp-periode-field label {
    display: block;
    font-weight: 600;
    font-size: 13.5px;
    color: #333;
    margin-bottom: 6px;
  }

  .kp-periode-select,
  .kp-tahun-input {
    width: 100%;
    height: 2.5rem;
    padding: 8px 10px;
    border-radius: 6px;
    border: 1px solid #ced4da;
    font-weight: 600;
    color: #0d6efd;
    background: #eef4ff;
    text-align: center;
  }

  .kp-periode-select:focus,
  .kp-tahun-input:focus {
    outline: none;
    border-color: #0d6efd;
    box-shadow: 0 0 0 2px rgba(13,110,253,0.15);
  }

  .kp-footer {
    display: flex;
    justify-content: flex-end;
  }

  .kp-ok-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #2563eb;
    color: #fff;
    border: none;
    padding: 9px 22px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    box-shadow: 0 3px 8px rgba(37,99,235,0.25);
    transition: background 0.12s ease;
  }

  .kp-ok-btn:hover {
    background: #1d4ed8;
  }

</style>
@endsection

@section('content')

<div id="contentContainer" class="container-fluid">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />

  <div class="kp-wrap">
    <div class="kp-card">

      <div class="kp-header">
        <div class="kp-header-icon">
          <i class="bi bi-journal-check"></i>
        </div>
        <div>
          <div class="kp-header-title">Posting</div>
          <div class="kp-header-subtitle">Periode transaksi yang akan diposting ulang</div>
        </div>
      </div>

      <div class="kp-body">

        <div class="kp-periode-row">
          <div class="kp-periode-field">
            <label for="input_posting_bulan">Bulan</label>
            <select id="input_posting_bulan" class="form-select kp-periode-select">
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
              <option value="6">6</option>
              <option value="7">7</option>
              <option value="8">8</option>
              <option value="9">9</option>
              <option value="10">10</option>
              <option value="11">11</option>
              <option value="12">12</option>
            </select>
          </div>

          <div class="kp-periode-field">
            <label for="input_posting_tahun">Tahun</label>
            <input type="number" class="form-control kp-tahun-input" id="input_posting_tahun" placeholder="Tahun">
          </div>
        </div>

        <div id="posting_progress_wrap" class="mb-4" style="display:none;">
          <div class="progress" style="height: 20px;">
            <div id="posting_progress" class="progress-bar progress-bar-striped progress-bar-animated"
              role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
          <small id="posting_status" class="d-block mt-2 text-muted"></small>
        </div>

        <div class="kp-footer">
          <button type="button" id="btn_posting_proses" class="kp-ok-btn" onclick="prosesPosting()">
            <i class="bi bi-gear-fill"></i> Proses
          </button>
        </div>

      </div>

    </div>
  </div>

</div>

@endsection

@section('js')
<script type="text/javascript">

let psTimer = null;
let psMulai = 0;

$(document).ready(function () {
  document.getElementById("input_posting_tahun").value = $("#periode_tahun").val();
  document.getElementById("input_posting_bulan").value = $("#periode_bulan").val();
});

function psDurasi () {
  let detik = Math.floor((Date.now() - psMulai) / 1000);
  let mm = String(Math.floor(detik / 60)).padStart(2, '0');
  let ss = String(detik % 60).padStart(2, '0');
  return mm + ':' + ss;
}

function psCegahTutup (e) {
  e.preventDefault();
  e.returnValue = '';
}

function psSetProses (jalan) {
  $('#btn_posting_proses, #input_posting_bulan, #input_posting_tahun').prop('disabled', jalan);
  if (jalan) {
    window.addEventListener('beforeunload', psCegahTutup);
  } else {
    window.removeEventListener('beforeunload', psCegahTutup);
  }
}

function psSelesai (berhasil, pesan) {
  clearInterval(psTimer);
  psTimer = null;

  $('#posting_progress')
    .removeClass('progress-bar-animated progress-bar-striped')
    .addClass(berhasil ? 'bg-success' : 'bg-danger');
  $('#posting_status')
    .removeClass('text-muted')
    .addClass(berhasil ? 'text-success' : 'text-danger')
    .text(pesan);

  psSetProses(false);
}

function prosesPosting () {
  let bulan = Number($("#input_posting_bulan").val());
  let tahun = Number($("#input_posting_tahun").val());

  if (!bulan || bulan < 1 || bulan > 12) {
    alertify.warning('Bulan tidak valid');
    return;
  }
  if (!tahun || tahun < 2000 || tahun > 2100) {
    alertify.warning('Tahun tidak valid');
    return;
  }

  var dlgPosting = alertify.confirm('Posting', `Posting ulang transaksi periode ${bulan} / ${tahun}?`, function () {
    psSetProses(true);

    psMulai = Date.now();
    $('#posting_progress')
      .removeClass('bg-success bg-danger')
      .addClass('progress-bar-striped progress-bar-animated');
    $('#posting_status')
      .removeClass('text-success text-danger')
      .addClass('text-muted')
      .text('Memproses... 00:00');
    $('#posting_progress_wrap').show();

    psTimer = setInterval(function () {
      $('#posting_status').text('Memproses... ' + psDurasi());
    }, 1000);

    $.ajax({
      url: "{!! url('postingproses') !!}",
      type: "post",
      timeout: 0,
      data: {
        _token: $("#_token").val(),
        bulan,
        tahun
      },
      success: function (res) {
        if (res.success) {
          psSelesai(true, 'Selesai (' + psDurasi() + ')');
          alertify.success('Posting selesai');
        } else {
          psSelesai(false, 'Gagal: ' + (res.message || 'Terjadi kesalahan'));
          alertify.error(res.message || 'Gagal memposting');
        }
      },
      error: function (xhr) {
        let pesan = xhr.responseJSON?.message || 'Terjadi kesalahan, silakan refresh browser';
        psSelesai(false, 'Gagal: ' + pesan);
        alertify.error(pesan);
      }
    });
  }, function () {}).set('labels', { ok: 'Proses', cancel: 'Batal' });
  // 'is-success' bikin tombol OK hijau (aksi proses), Cancel tetap netral --
  // gaya bersama di public/css/report-table.css.
  dlgPosting.elements.root.classList.add('ajs-app-buttons', 'is-success');
}

</script>
@endsection
