@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Setup Periode Kerja')
@section('content')

  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Utilitas</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Setup Periode Kerja</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Setup Periode Kerja</h1>
    </div>
  </div> --}}

<div id="printContainer" style="display:none">

</div>

<div id="contentContainer" class="container-fluid">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <input type="hidden" id="periode_tahun" value="{{ $periode->tahun }}" />
  <input type="hidden" id="periode_bulan" value="{{ (int) $periode->bulan }}" />

  <div class="kp-wrap">
    <div class="kp-card">

      <div class="kp-header">
        <div class="kp-header-icon">
          <i class="bi bi-calendar-week-fill"></i>
        </div>
        <div>
          <div class="kp-header-title">Setup Periode Kerja</div>
          <div class="kp-header-subtitle">Pilih Bulan dan Tahun, Klik Simpan</div>
        </div>
      </div>

      <div class="kp-body" id="formBsGrid">

        <div class="bs-form bs-form-1">
          <label for="input_periodekerja_bulan">Bulan</label>
          <select id="input_periodekerja_bulan" class="form-control">
            <option value="1">Januari</option>
            <option value="2">Februari</option>
            <option value="3">Maret</option>
            <option value="4">April</option>
            <option value="5">Mei</option>
            <option value="6">Juni</option>
            <option value="7">Juli</option>
            <option value="8">Agustus</option>
            <option value="9">September</option>
            <option value="10">Oktober</option>
            <option value="11">November</option>
            <option value="12">Desember</option>
          </select>

          <label for="input_periodekerja_tahun">Tahun</label>
          <input type="text" class="form-control" id="input_periodekerja_tahun" placeholder="Tahun" maxlength="4" inputmode="numeric">
        </div>

        <div class="kp-footer">
          <button type="button" class="kp-ok-btn" onclick="submitPeriodeKerja()">
            <i class="bi bi-check-lg"></i> Simpan
          </button>
        </div>

      </div>

    </div>
  </div>

</div>

@endsection

@section('css')
{{-- Isian memakai #formBsGrid .bs-form seperti form menu master/purchasing (bs-form-1 ada di master-list.css). --}}
<link rel="stylesheet" href="{!! URL::asset('css/master-list.css') !!}?v={{ @filemtime(base_path('public/css/master-list.css')) ?: '1' }}">
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

  .kp-footer {
    display: flex;
    justify-content: flex-end;
    margin-top: 22px;
  }

  /* Tombol soft seperti tombol utama memorialkoreksi (.btn-dpp-utama). */
  .kp-ok-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #e8edff;
    color: #2563eb;
    border: 1px solid #cfdcff;
    padding: 7px 18px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: background 0.12s ease;
  }

  .kp-ok-btn:hover {
    background: #dce6ff;
    color: #1d4ed8;
  }

</style>
@endsection

@section('js')
<script type="text/javascript">

const namaBulanPeriode = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']

$(document).ready(function () {
  let tahun = $("#periode_tahun").val();
  let bulan = $("#periode_bulan").val();

  document.getElementById("input_periodekerja_tahun").value = tahun;
  document.getElementById("input_periodekerja_bulan").value = bulan;

  // document.getElementById('breadcrumb').innerHTML = "Setup Periode Kerja" // dimatikan: judul sekarang di bar atas (page-title)
});

function submitPeriodeKerja () {
  let tahunTeks = String($("#input_periodekerja_tahun").val()).trim();
  let tahun = Number(tahunTeks);
  let bulan = Number($("#input_periodekerja_bulan").val());
  let _token = $("#_token").val();

  if (!/^\d{4}$/.test(tahunTeks) || tahun < 1900 || tahun > 2999) {
    alertify.warning('Tahun harus diisi 4 angka, mis. ' + new Date().getFullYear());
    return
  }
  if (!(bulan >= 1 && bulan <= 12)) {
    alertify.warning('Bulan harus dipilih');
    return
  }

  $.ajax({
    url: "{!! url('newsetupperiodekerjaupdate') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      bulan,
      tahun
    },
    success: function (res) {
      if (!res || typeof res !== 'object') {
        alertify.error('Sesi login habis atau server tidak merespons dengan benar. Silakan muat ulang halaman.')
        return
      }
      if (!Number(res.status)) {
        alertify.warning(res.pesan || 'Periode kerja gagal disimpan')
        return
      }

      // Badge periode di bar atas layout newmasterTest (class .period-badge, tanpa id).
      let el = document.querySelector(".period-badge");
      if (el) {
        el.innerHTML = `Username: {{ Auth::user()->username }} &nbsp;–&nbsp; Periode: ${namaBulanPeriode[bulan]} ${tahun}`;
      }

      // Also keep the hidden periode_bulan/periode_tahun inputs on THIS page
      // in sync, so re-submitting on the same page uses the fresh values.
      document.getElementById("periode_bulan").value = bulan;
      document.getElementById("periode_tahun").value = tahun;

      alertify.success('Periode kerja telah disimpan');
    },
    error: function (err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}

</script>
@endsection
