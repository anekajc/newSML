@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Kunci Periode')
@section('content')



  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Utilitas</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Kunci Periode</span>
  </div> --}}

  {{-- Judul sekarang di bar atas (page-title) layout newmasterTest.
  <div class="sp-page-head">
    <div>
      <h1>Kunci Periode</h1>
    </div>
  </div> --}}

<div id="contentContainer" class="container-fluid">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  <div class="kp-wrap">
    <div class="kp-card">

      <div class="kp-header">
        <div class="kp-header-icon">
          <i class="bi bi-lock-fill"></i>
        </div>
        <div>
          <div class="kp-header-title">Buka/Tutup Periode</div>
          <div class="kp-header-subtitle">Centang bulan untuk mengunci, hapus centang untuk membuka</div>
        </div>
      </div>

      <div class="kp-body" id="formBsGrid">

        <div class="bs-form bs-form-1 kp-tahun-row">
          <label for="input_tahun">Tahun</label>
          <div class="input-group kp-tahun-group">
            <div class="input-group-prepend">
              <button type="button" class="btn btn-chip-biru" title="Tahun sebelumnya" onclick="geserTahun(-1)"><i class="bi bi-chevron-left"></i></button>
            </div>
            <input type="text" id="input_tahun" class="form-control text-center" value="{{ date('Y') }}" maxlength="4" inputmode="numeric" onchange="loadKunciPeriode()">
            <div class="input-group-append">
              <button type="button" class="btn btn-chip-biru" title="Tahun berikutnya" onclick="geserTahun(1)"><i class="bi bi-chevron-right"></i></button>
            </div>
          </div>
        </div>

        <div class="kp-month-list kp-month-list-cols">
          <div class="kp-month-col">
            <label class="kp-month-item">
              <input type="checkbox" id="bulan_1" value="1" onchange="toggleBulan(this)">
              <span>Januari</span>
            </label>
            <label class="kp-month-item">
              <input type="checkbox" id="bulan_2" value="2" onchange="toggleBulan(this)">
              <span>Februari</span>
            </label>
            <label class="kp-month-item">
              <input type="checkbox" id="bulan_3" value="3" onchange="toggleBulan(this)">
              <span>Maret</span>
            </label>
            <label class="kp-month-item">
              <input type="checkbox" id="bulan_4" value="4" onchange="toggleBulan(this)">
              <span>April</span>
            </label>
            <label class="kp-month-item">
              <input type="checkbox" id="bulan_5" value="5" onchange="toggleBulan(this)">
              <span>Mei</span>
            </label>
            <label class="kp-month-item">
              <input type="checkbox" id="bulan_6" value="6" onchange="toggleBulan(this)">
              <span>Juni</span>
            </label>
          </div>

          <div class="kp-month-col">
            <label class="kp-month-item">
              <input type="checkbox" id="bulan_7" value="7" onchange="toggleBulan(this)">
              <span>Juli</span>
            </label>
            <label class="kp-month-item">
              <input type="checkbox" id="bulan_8" value="8" onchange="toggleBulan(this)">
              <span>Agustus</span>
            </label>
            <label class="kp-month-item">
              <input type="checkbox" id="bulan_9" value="9" onchange="toggleBulan(this)">
              <span>September</span>
            </label>
            <label class="kp-month-item">
              <input type="checkbox" id="bulan_10" value="10" onchange="toggleBulan(this)">
              <span>Oktober</span>
            </label>
            <label class="kp-month-item">
              <input type="checkbox" id="bulan_11" value="11" onchange="toggleBulan(this)">
              <span>November</span>
            </label>
            <label class="kp-month-item">
              <input type="checkbox" id="bulan_12" value="12" onchange="toggleBulan(this)">
              <span>Desember</span>
            </label>
          </div>
        </div>

        <div class="kp-footer">
          <button type="button" class="kp-ok-btn" onclick="loadKunciPeriode()">
            <i class="bi bi-arrow-clockwise"></i> Muat Ulang
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
    max-width: 520px;
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
    padding: 20px 22px 22px;
  }

  /* Baris Tahun memakai #formBsGrid .bs-form (master-list.css); tombol geser tahun
     menempel di kiri-kanan input seperti tombol browse di form master. */
  .kp-tahun-row {
    margin-bottom: 16px;
  }

  .kp-tahun-group {
    max-width: 200px;
  }

  .kp-month-list.is-memuat {
    opacity: .55;
    pointer-events: none;
  }

  .kp-month-list {
    background: #fbfbfb;
    border: 1px solid #e9e9e9;
    border-radius: 8px;
    padding: 6px 4px;
    margin-bottom: 20px;
  }

  .kp-month-list-cols {
    display: flex;
    gap: 4px;
  }

  .kp-month-col {
    flex: 1;
    min-width: 0;
  }

  .kp-month-col:first-child {
    border-right: 1px solid #e9e9e9;
  }

  .kp-month-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 7px 12px;
    cursor: pointer;
    font-size: 14px;
    color: #333;
    border-radius: 6px;
    transition: background 0.12s ease;
  }

  .kp-month-item:hover {
    background: #eef4ff;
  }

  .kp-month-item input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: #2563eb;
    cursor: pointer;
  }

  .kp-footer {
    display: flex;
    justify-content: flex-end;
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

// Tahun yang daftar bulannya sedang tampil. Centang/hapus centang selalu memakai tahun ini,
// bukan isi kotak Tahun yang mungkin sudah diketik ulang tapi belum dimuat.
let tahunTampil = ''

function tahunValid (tahun) {
  return /^\d{4}$/.test(tahun) && Number(tahun) >= 1900 && Number(tahun) <= 2999
}

function geserTahun (arah) {
  let tahun = String($("#input_tahun").val()).trim()
  if (!tahunValid(tahun)) { tahun = tahunTampil || String(new Date().getFullYear()) }
  $("#input_tahun").val(Number(tahun) + arah)
  loadKunciPeriode()
}

function loadKunciPeriode () {
  let tahun = String($("#input_tahun").val()).trim();

  if (!tahunValid(tahun)) {
    alertify.warning("Tahun harus diisi 4 angka, mis. " + new Date().getFullYear());
    if (tahunTampil) { $("#input_tahun").val(tahunTampil) }
    return;
  }
  $("#input_tahun").val(tahun)

  // Uncheck everything first so a switch to a different year doesn't
  // carry over checks from the previous year while the request is in flight.
  for (let i = 1; i <= 12; i++) {
    document.getElementById(`bulan_${i}`).checked = false;
  }

  let daftar = document.querySelector('.kp-month-list')
  daftar.classList.add('is-memuat')

  $.ajax({
    url: "{!! url('kunciperiodeload') !!}",
    type: "get",
    async: false,
    data: { tahun },
    success: function(res) {
      if (!Array.isArray(res)) {
        alertify.error('Sesi login habis atau server tidak merespons dengan benar. Silakan muat ulang halaman.')
        return
      }
      // res = array of locked BULAN numbers for this TAHUN, e.g. [1, 2, 5]
      res.forEach((bulan) => {
        let cb = document.getElementById(`bulan_${Number(bulan)}`);
        if (cb) cb.checked = true;
      });
      tahunTampil = tahun
    },
    error: function(err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });

  // async:false - permintaan sudah selesai di sini, berhasil atau gagal.
  daftar.classList.remove('is-memuat')
}

function toggleBulan (checkbox) {
  let bulan   = checkbox.value;
  let tahun   = tahunTampil;
  let checked = checkbox.checked ? 1 : 0;
  let _token  = $("#_token").val();

  if (!tahun) {
    alertify.warning("Muat dulu daftar bulan untuk tahun yang dipilih");
    checkbox.checked = !checkbox.checked; // revert
    return;
  }

  // Kotak Tahun sudah diganti tapi belum dimuat - jangan kunci/buka bulan di tahun yang salah.
  if (String($("#input_tahun").val()).trim() !== tahun) {
    checkbox.checked = !checkbox.checked; // revert
    loadKunciPeriode();
    alertify.warning("Tahun diganti - daftar bulan dimuat ulang, silakan centang lagi");
    return;
  }

  $.ajax({
    url: "{!! url('kunciperiodetoggle') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      bulan,
      tahun,
      checked
    },
    success: function(res) {
      if (res != 1) {
        alertify.warning(res);
        checkbox.checked = !checkbox.checked; // revert on failure
      } else {
        alertify.success(checked ? 'Periode dikunci' : 'Periode dibuka');
      }
    },
    error: function(err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
      checkbox.checked = !checkbox.checked; // revert on failure
    }
  });
}

window.onload = function () {
  loadKunciPeriode();
}

</script>
@endsection