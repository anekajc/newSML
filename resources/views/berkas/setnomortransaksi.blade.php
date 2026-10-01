@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Atur Nomor Transaksi dan Perusahaan')
@section('content')

<link rel="stylesheet" href="{!! URL::asset('css/master-list.css') !!}?v={{ @filemtime(base_path('public/css/master-list.css')) ?: '1' }}">

  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Master</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Seting Perusahaan</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Atur Nomor Transaksi dan Perusahaan</h1>
    </div>
  </div> --}}

<div id="contentContainer" class="container-fluid">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  <div class="stp-card">

    <div class="stp-body">

      <div class="stp-side">
        <div class="stp-side-badge">
          <i class="bi bi-buildings"></i>
        </div>
      </div>

      <div class="stp-main" id="formBsGrid">

        <ul class="nav nav-tabs stp-main-tabs" id="stpMainTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-perusahaan-btn" data-toggle="tab" data-target="#tab-perusahaan" type="button" role="tab">Perusahaan</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-nomortransaksi-btn" data-toggle="tab" data-target="#tab-nomortransaksi" type="button" role="tab">Set Nomor Transaksi</button>
          </li>
        </ul>

        <div class="tab-content stp-tab-content">

          <!-- ===================== TAB: PERUSAHAAN ===================== -->
          <div class="tab-pane fade show active" id="tab-perusahaan" role="tabpanel">

            <div class="bs-form bs-form-lebar">
              <label for="input_nama">Nama</label>
              <input type="text" class="form-control bs-full" id="input_nama" maxlength="40">

              <label for="input_alamat1">Alamat</label>
              <input type="text" class="form-control bs-full" id="input_alamat1" maxlength="100">

              <span></span>
              <input type="text" class="form-control bs-full" id="input_alamat2" maxlength="100" aria-label="Alamat baris 2">

              <label for="input_kota">Kota</label>
              <input type="text" class="form-control" id="input_kota" maxlength="40">

              <label for="input_telpon">Telpon</label>
              <input type="text" class="form-control" id="input_telpon" maxlength="30">

              <label for="input_fax">Fax</label>
              <input type="text" class="form-control" id="input_fax" maxlength="30">

              <label for="input_email">E-Mail</label>
              <input type="text" class="form-control" id="input_email" maxlength="100">
            </div>

            <ul class="nav nav-tabs stp-sub-tabs mt-3" id="stpNpwpTab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-npwp1-btn" data-toggle="tab" data-target="#tab-npwp1" type="button" role="tab">NPWP 1</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-npwp2-btn" data-toggle="tab" data-target="#tab-npwp2" type="button" role="tab">NPWP 2</button>
              </li>
            </ul>

            <div class="tab-content stp-npwp-content">

              <!-- ---------- NPWP 1 ---------- -->
              <div class="tab-pane fade show active" id="tab-npwp1" role="tabpanel">

                <div class="bs-form bs-form-lebar">
                  <label for="input_namapkp_1">Nama PKP</label>
                  <input type="text" class="form-control bs-full" id="input_namapkp_1" maxlength="40">

                  <label for="input_alamatpkp1_1">Alamat PKP</label>
                  <input type="text" class="form-control bs-full" id="input_alamatpkp1_1" maxlength="100">

                  <span></span>
                  <input type="text" class="form-control bs-full" id="input_alamatpkp2_1" maxlength="100" aria-label="Alamat PKP baris 2">

                  <label for="input_kotapkp_1">Kota PKP</label>
                  <input type="text" class="form-control" id="input_kotapkp_1" maxlength="40">

                  <label for="input_npwp_1">NPWP</label>
                  <input type="text" class="form-control" id="input_npwp_1" maxlength="40">

                  <label for="input_tglpengukuhan_1" class="bs-wrap">Tanggal Pengukuhan</label>
                  <input type="date" class="form-control" id="input_tglpengukuhan_1">

                  <span></span>
                  <span></span>

                  <label for="input_penandatangan_1" class="bs-wrap">Penandatanganan FPJ</label>
                  <input type="text" class="form-control" id="input_penandatangan_1" maxlength="50">

                  <label for="input_jabatan_1">Jabatan</label>
                  <input type="text" class="form-control" id="input_jabatan_1" maxlength="50">

                  <span></span>
                  <div class="d-flex bs-full stp-upload-cell" style="gap: 32px;">

                    <div>
                      <div class="mb-1 stp-upload-label">Ttd Trading Director</div>
                      <div class="d-flex align-items-start" style="gap: 10px;">
                        <label class="btn btn-sm btn-outline-secondary mb-0">
                          <i class="bi bi-folder2-open"></i> Cari
                          <input type="file" id="input_ttd_1" accept="image/png,image/jpeg,image/gif,image/bmp,image/webp" hidden onchange="previewUploadImage(this, 'stp_ttd_box_1')">
                        </label>
                        <div class="stp-upload-box" id="stp_ttd_box_1">
                          <i class="bi bi-image stp-upload-placeholder"></i>
                        </div>
                      </div>
                    </div>

                    <div>
                      <div class="mb-1 stp-upload-label">Logo</div>
                      <div class="d-flex align-items-start" style="gap: 10px;">
                        <label class="btn btn-sm btn-outline-secondary mb-0">
                          <i class="bi bi-folder2-open"></i> Cari
                          <input type="file" id="input_logo_1" accept="image/png,image/jpeg,image/gif,image/bmp,image/webp" hidden onchange="previewUploadImage(this, 'stp_logo_box_1')">
                        </label>
                        <div class="stp-upload-box" id="stp_logo_box_1">
                          <i class="bi bi-image stp-upload-placeholder"></i>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>

              </div>

              <!-- ---------- NPWP 2 ---------- -->
              <div class="tab-pane fade" id="tab-npwp2" role="tabpanel">

                <div class="bs-form bs-form-lebar">
                  <label for="input_namapkp_2">Nama PKP</label>
                  <input type="text" class="form-control bs-full" id="input_namapkp_2" maxlength="40">

                  <label for="input_alamatpkp1_2">Alamat PKP</label>
                  <input type="text" class="form-control bs-full" id="input_alamatpkp1_2" maxlength="100">

                  <span></span>
                  <input type="text" class="form-control bs-full" id="input_alamatpkp2_2" maxlength="100" aria-label="Alamat PKP baris 2">

                  <label for="input_kotapkp_2">Kota PKP</label>
                  <input type="text" class="form-control" id="input_kotapkp_2" maxlength="40">

                  <label for="input_npwp_2">NPWP</label>
                  <input type="text" class="form-control" id="input_npwp_2" maxlength="40">

                  <label for="input_tglpengukuhan_2" class="bs-wrap">Tanggal Pengukuhan</label>
                  <input type="date" class="form-control" id="input_tglpengukuhan_2">

                  <span></span>
                  <span></span>

                  <label for="input_penandatangan_2" class="bs-wrap">Penandatanganan FPJ</label>
                  <input type="text" class="form-control" id="input_penandatangan_2" maxlength="50">

                  <label for="input_jabatan_2">Jabatan</label>
                  <input type="text" class="form-control" id="input_jabatan_2" maxlength="50">

                  <span></span>
                  <div class="d-flex bs-full stp-upload-cell" style="gap: 32px;">

                    <div>
                      <div class="mb-1 stp-upload-label">Ttd Trading Director</div>
                      <div class="d-flex align-items-start" style="gap: 10px;">
                        <label class="btn btn-sm btn-outline-secondary mb-0">
                          <i class="bi bi-folder2-open"></i> Cari
                          <input type="file" id="input_ttd_2" accept="image/png,image/jpeg,image/gif,image/bmp,image/webp" hidden onchange="previewUploadImage(this, 'stp_ttd_box_2')">
                        </label>
                        <div class="stp-upload-box" id="stp_ttd_box_2">
                          <i class="bi bi-image stp-upload-placeholder"></i>
                        </div>
                      </div>
                    </div>

                    <div>
                      <div class="mb-1 stp-upload-label">Logo</div>
                      <div class="d-flex align-items-start" style="gap: 10px;">
                        <label class="btn btn-sm btn-outline-secondary mb-0">
                          <i class="bi bi-folder2-open"></i> Cari
                          <input type="file" id="input_logo_2" accept="image/png,image/jpeg,image/gif,image/bmp,image/webp" hidden onchange="previewUploadImage(this, 'stp_logo_box_2')">
                        </label>
                        <div class="stp-upload-box" id="stp_logo_box_2">
                          <i class="bi bi-image stp-upload-placeholder"></i>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>

              </div>

            </div>

          </div>

          <!-- ===================== TAB: SET NOMOR TRANSAKSI ===================== -->
          <div class="tab-pane fade" id="tab-nomortransaksi" role="tabpanel">

            <div class="stn-wrap">

              <!-- ---------- LEFT: KODE TRANSAKSI ---------- -->
              <div class="stn-left">
                <div class="stn-section-title">Kode Transaksi</div>

                {{-- Tiap kode transaksi = label | [kode][nomor] dalam grid .bs-form (dua isian dibagi
                     lewat .d-flex, aturannya ada di master-list.css). --}}
                <div class="bs-form bs-form-lebar">

                  <label for="input_kode_kasmasuk">Kas Masuk</label>
                  <div class="d-flex stn-pair">
                    <input type="text" class="form-control stn-code" id="input_kode_kasmasuk" value="BKM">
                    <input type="text" class="form-control" id="input_nomor_kasmasuk">
                  </div>
                  <label for="input_kode_kaskeluar">Kas Keluar</label>
                  <div class="d-flex stn-pair">
                    <input type="text" class="form-control stn-code" id="input_kode_kaskeluar" value="BKK">
                    <input type="text" class="form-control" id="input_nomor_kaskeluar">
                  </div>

                  <label for="input_kode_bankmasuk">Bank Masuk</label>
                  <div class="d-flex stn-pair">
                    <input type="text" class="form-control stn-code" id="input_kode_bankmasuk" value="BBM">
                    <input type="text" class="form-control" id="input_nomor_bankmasuk">
                  </div>
                  <label for="input_kode_bankkeluar">Bank Keluar</label>
                  <div class="d-flex stn-pair">
                    <input type="text" class="form-control stn-code" id="input_kode_bankkeluar" value="BBK">
                    <input type="text" class="form-control" id="input_nomor_bankkeluar">
                  </div>

                  <label for="input_kode_buktimemorial">Bukti Memorial</label>
                  <div class="d-flex stn-pair">
                    <input type="text" class="form-control stn-code" id="input_kode_buktimemorial" value="BMM">
                    <input type="text" class="form-control" id="input_nomor_buktimemorial">
                  </div>
                  <span></span>
                  <span></span>

                  <label for="input_kode_jurnalkoreksi">Jurnal koreksi</label>
                  <div class="d-flex stn-pair">
                    <input type="text" class="form-control stn-code" id="input_kode_jurnalkoreksi" value="BJK">
                    <input type="text" class="form-control" id="input_nomor_jurnalkoreksi">
                  </div>
                  <span></span>
                  <span></span>

                </div>

                <div class="bs-form bs-form-lebar mt-4">

                  <label for="input_kode_so">SO</label>
                  <div class="d-flex stn-pair">
                    <input type="text" class="form-control stn-code" id="input_kode_so" value="SO">
                    <input type="text" class="form-control" id="input_nomor_so">
                  </div>
                  <label for="input_kode_perintahkirim" class="bs-wrap">Perintah Pengiriman</label>
                  <div class="d-flex stn-pair">
                    <input type="text" class="form-control stn-code" id="input_kode_perintahkirim" value="DO">
                    <input type="text" class="form-control" id="input_nomor_perintahkirim">
                  </div>

                  <label for="input_kode_pengirimanbarang" class="bs-wrap">Pengiriman Barang</label>
                  <div class="d-flex stn-pair">
                    <input type="text" class="form-control stn-code" id="input_kode_pengirimanbarang" value="SJ">
                    <input type="text" class="form-control" id="input_nomor_pengirimanbarang">
                  </div>
                  <label for="input_kode_invoicepenjualan" class="bs-wrap">Invoice Penjualan</label>
                  <div class="d-flex stn-pair">
                    <input type="text" class="form-control stn-code" id="input_kode_invoicepenjualan" value="INVC">
                    <input type="text" class="form-control" id="input_nomor_invoicepenjualan">
                  </div>

                  <span></span>
                  <span></span>
                  <label for="input_kode_returpenjualan" class="bs-wrap">Retur Penjualan</label>
                  <div class="d-flex stn-pair">
                    <input type="text" class="form-control stn-code" id="input_kode_returpenjualan" value="RINVC">
                    <input type="text" class="form-control" id="input_nomor_returpenjualan">
                  </div>

                </div>

                <div class="bs-form bs-form-lebar mt-4">
                  <label for="input_kode_inisialperusahaan" class="bs-wrap">Inisial Perusahaan</label>
                  <input type="text" class="form-control stn-code" id="input_kode_inisialperusahaan" value="SML">
                  <label for="input_kode_tag">Tag</label>
                  <input type="text" class="form-control stn-code" id="input_kode_tag" value="SMX">
                </div>

              </div>

              <!-- ---------- RIGHT: KONFIGURASI ---------- -->
              {{-- Panel sempit: .bs-form satu kolom (label di atas isian). --}}
              <div class="stn-right">
                <div class="stn-section-title">Konfigurasi</div>

                <div class="bs-form stn-config">
                  <label for="input_pemisah">Pemisah</label>
                  <select class="form-control" id="input_pemisah" onchange="updateContohFormat()">
                    <option value="/">/</option>
                    <option value="-">-</option>
                    <option value=".">.</option>
                    <option value="">(tanpa pemisah)</option>
                  </select>

                  <label for="input_format1">Format Nomor Transaksi</label>
                  <div>

                  <select class="form-control mb-2" id="input_format1" onchange="updateContohFormat()">
                    <option>Inisial Perusahaan</option>
                    <option>Kode Transaksi</option>
                    <option>Nomor Urut</option>
                    <option>MMYY</option>
                    <option>YYMM</option>
                    <option>Tag</option>
                  </select>

                  <select class="form-control mb-2" id="input_format2" onchange="updateContohFormat()">
                    <option>Kode Transaksi</option>
                    <option>Inisial Perusahaan</option>
                    <option>Nomor Urut</option>
                    <option>MMYY</option>
                    <option>YYMM</option>
                    <option>Tag</option>
                  </select>

                  <select class="form-control mb-2" id="input_format3" onchange="updateContohFormat()">
                    <option>Nomor Urut</option>
                    <option>Inisial Perusahaan</option>
                    <option>Kode Transaksi</option>
                    <option>MMYY</option>
                    <option>YYMM</option>
                    <option>Tag</option>
                  </select>

                  <select class="form-control" id="input_format4" onchange="updateContohFormat()">
                    <option>MMYY</option>
                    <option>YYMM</option>
                    <option>Inisial Perusahaan</option>
                    <option>Kode Transaksi</option>
                    <option>Nomor Urut</option>
                    <option>Tag</option>
                  </select>
                  </div>

                  <label for="input_resetnomor">Reset Nomor Per</label>
                  <select class="form-control" id="input_resetnomor" onchange="updateContohFormat()">
                    <option>Bulan</option>
                    <option>Tahun</option>
                    <option>Tidak Pernah</option>
                  </select>

                  <label for="input_contohformat">Contoh Format</label>
                  <input type="text" class="form-control" id="input_contohformat" disabled>

                  {{-- No. Seri Faktur Pajak dihilangkan dari panel Konfigurasi (permintaan user).
                  <label for="input_noserifakturpajak">No. Seri Faktur Pajak</label>
                  <textarea class="form-control" id="input_noserifakturpajak" rows="3"></textarea>
                  --}}
                </div>

              </div>

            </div>

          </div>

        </div>

      </div>

    </div>

    <div class="stp-footer">
      <button type="button" class="btn btn-sm btn-batal-add" onclick="batalSetingPerusahaan()">Batal</button>
      <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitSetingPerusahaan()">Simpan</button>
    </div>

  </div>

</div>

@endsection

@section('css')
<style>

  .stp-card {
    background: #fff;
    border: 1px solid #d9d9d9;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    overflow: hidden;
  }

  .stp-body {
    display: flex;
  }

  .stp-side {
    width: 170px;
    flex-shrink: 0;
    background: linear-gradient(160deg, #0d1b3d 0%, #1c3f8f 55%, #4f83e0 100%);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .stp-side-badge {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: rgba(255,255,255,0.12);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
    color: #fff;
  }

  .stp-main {
    flex: 1;
    min-width: 0;
    padding: 14px 22px 0 22px;
  }

  /* Tab berupa <button>: Bootstrap 4 (layout newmasterTest) tidak mereset gaya bawaan tombol
     seperti Bootstrap 5, jadi latar abu & garis fokusnya dihapus di sini. */
  .stp-main-tabs button.nav-link,
  .stp-sub-tabs button.nav-link {
    background: transparent;
    cursor: pointer;
    outline: none;
  }

  .stp-main-tabs .nav-link {
    font-weight: 600;
    font-size: 14px;
    color: #555;
    border: none;
    border-bottom: 2px solid transparent;
    padding: 8px 14px;
  }

  .stp-main-tabs .nav-link.active {
    color: #0d1b3d;
    border-bottom: 2px solid #0d1b3d;
    background: transparent;
  }

  .stp-tab-content {
    padding-top: 12px;
  }

  /* Isian tab Perusahaan / NPWP / Set Nomor Transaksi memakai #formBsGrid .bs-form seperti
     form menu master/purchasing (bs-form-lebar, bs-full, bs-wrap ada di master-list.css). */

  /* Tombol "Cari" gambar TTD/Logo berupa <label class="btn">, jadi gaya label grid
     (huruf kecil abu, cursor default) dikembalikan ke gaya tombol. */
  #formBsGrid .bs-form .stp-upload-cell label.btn {
    cursor: pointer;
    font-size: 12px;
    letter-spacing: normal;
  }

  #formBsGrid .bs-form .stp-upload-cell label.btn:not(:hover) {
    color: #6c757d;
  }

  .stp-sub-tabs .nav-link {
    font-size: 13px;
    padding: 5px 14px;
    color: #555;
    border: 1px solid transparent;
  }

  .stp-sub-tabs .nav-link.active {
    background: #f4f6fb;
    border: 1px solid #d9d9d9;
    border-bottom-color: #f4f6fb;
    color: #0d1b3d;
    font-weight: 600;
  }

  .stp-npwp-content {
    background: #f4f6fb;
    border: 1px solid #d9d9d9;
    border-top: none;
    border-radius: 0 0 6px 6px;
    padding: 14px 16px 16px;
  }

  .stp-upload-label {
    font-size: 12px;
    color: #333;
  }

  .stp-upload-box {
    width: 110px;
    height: 90px;
    border: 1px solid #c9c9c9;
    border-radius: 6px;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
  }

  .stp-upload-box img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
  }

  .stp-upload-placeholder {
    color: #bbb;
    font-size: 22px;
  }

  .stp-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 12px 22px;
    border-top: 1px solid #e5e5e5;
    background: #fafafa;
  }

  .stp-btn-ok {
    background: #f1f1f1;
    border: 1px solid #b9b9b9;
    color: #1a7a1a;
    font-weight: 600;
    padding: 5px 20px;
    font-size: 13px;
  }

  .stp-btn-ok:hover {
    background: #e6f4e6;
  }

  .stp-btn-cancel {
    background: #f1f1f1;
    border: 1px solid #b9b9b9;
    color: #b02a2a;
    font-weight: 600;
    padding: 5px 20px;
    font-size: 13px;
  }

  .stp-btn-cancel:hover {
    background: #fbe9e9;
  }

  /* ===================== SET NOMOR TRANSAKSI ===================== */

  .stn-wrap {
    display: flex;
    gap: 24px;
    padding-bottom: 20px;
  }

  .stn-left {
    flex: 1;
    min-width: 0;
  }

  .stn-right {
    width: 260px;
    flex-shrink: 0;
    border-left: 1px solid #e2e2e2;
    padding-left: 22px;
  }

  .stn-section-title {
    font-size: 13px;
    font-weight: 700;
    color: #333;
    text-decoration: underline;
    margin-bottom: 14px;
  }

  .stn-code {
    text-align: center;
    font-weight: 600;
  }

  /* Pasangan [kode][nomor]: kode dibuat sempit tetap, nomor mengisi sisa sel. */
  #formBsGrid .bs-form .stn-pair { gap: 6px; }
  #formBsGrid .bs-form .stn-pair > .stn-code { flex: 0 0 72px; }

  /* Inisial Perusahaan & Tag berdiri sendiri di sel grid - tidak perlu selebar sel. */
  #formBsGrid .bs-form > .stn-code {
    width: 90px;
    justify-self: start;
  }

  /* Panel Konfigurasi sempit: .bs-form satu kolom, label di atas isian. */
  #formBsGrid .bs-form.stn-config {
    grid-template-columns: minmax(0, 1fr);
    gap: 6px 0;
  }

  #formBsGrid .bs-form.stn-config label:not(:first-child) {
    margin-top: 8px;
  }

</style>
@endsection

@section('js')
<script type="text/javascript">

// $(document).ready(function () {
//   document.getElementById('breadcrumb').innerHTML = "Atur Nomor Transaksi dan Perusahaan" // dimatikan: judul sekarang di bar atas (page-title)
// });

function previewUploadImage(inputEl, targetBoxId) {
  let file = inputEl.files && inputEl.files[0];
  if (!file) return;

  let reader = new FileReader();
  reader.onload = function (e) {
    document.getElementById(targetBoxId).innerHTML = `<img src="${e.target.result}" alt="preview">`;
  };
  reader.readAsDataURL(file);
}

// Batal = buang perubahan yang belum disimpan, isi form dikembalikan ke data di database.
// (Dulu tombol ini belum berfungsi sama sekali.)
function batalSetingPerusahaan() {
  alertify.confirm('Batal', 'Buang perubahan yang belum disimpan?',
    function () {
      if (loadAll()) { alertify.message('Perubahan dibatalkan') }
    },
    function () {})
}

// DBPERUSAHAAN hanya punya satu Direksi & satu Jabatan - kolom Penandatangan FPJ dan Jabatan
// di tab NPWP 1 & NPWP 2 menunjuk ke data yang sama, jadi isinya dibuat selalu kembar.
// Dulu yang disimpan hanya isian NPWP 1; ketikan di NPWP 2 hilang diam-diam.
function kembarkan (a, b) {
  let elA = document.getElementById(a)
  let elB = document.getElementById(b)
  elA.addEventListener('input', () => { elB.value = elA.value })
  elB.addEventListener('input', () => { elA.value = elB.value })
}
kembarkan('input_penandatangan_1', 'input_penandatangan_2')
kembarkan('input_jabatan_1', 'input_jabatan_2')

// Tanggal dari SQL Server ("2020-01-31 00:00:00.000") -> "2020-01-31" untuk <input type=date>.
// Tanggal kosong tetap kosong (dulu jadi 1970-01-01 karena new Date(null)).
function tanggalInput (v) {
  if (!v) { return '' }
  let m = String(v).match(/^(\d{4}-\d{2}-\d{2})/)
  return m ? m[1] : ''
}

// ── Set Nomor Transaksi: live "Contoh Format" preview ─────────────────
const formatSampleMap = {
  'Inisial Perusahaan': 'SML',
  'Kode Transaksi':     'LPB',
  'Nomor Urut':         '00000',
  'MMYY':               '0116',
  'YYMM':               '1601',
  'Tag':                'SMX'
};

function updateContohFormat() {
  let pemisah = document.getElementById('input_pemisah').value;
  let parts = ['input_format1', 'input_format2', 'input_format3', 'input_format4']
    .map(id => formatSampleMap[document.getElementById(id).value] || '');

  document.getElementById('input_contohformat').value = parts.join(pemisah);
}

function loadAll(){

  let _token = $("#_token").val();
  let berhasil = false
  $.ajax({
    url: "{!! url('dbnomorspdetail') !!}",
    type: "get",
    async: false,
    success: function(res) {
      if (!Array.isArray(res)) {
        alertify.error('Sesi login habis atau server tidak merespons dengan benar. Silakan muat ulang halaman.')
        return
      }
      if (!res.length) {
        alertify.warning('Data perusahaan belum ada di database')
        return
      }

      document.getElementById("input_nama").value = res[0].NAMA
      document.getElementById("input_alamat1").value = res[0].ALAMAT1
      document.getElementById("input_alamat2").value = res[0].ALAMAT2
      document.getElementById("input_kota").value = res[0].KOTA
      document.getElementById("input_telpon").value = res[0].Telpon
      document.getElementById("input_fax").value = res[0].Fax
      document.getElementById("input_email").value = res[0].email

      document.getElementById("input_namapkp_1").value = res[0].NAMAPKP
      document.getElementById("input_alamatpkp1_1").value = res[0].ALAMATPKP1
      document.getElementById("input_alamatpkp2_1").value = res[0].ALAMATPKP2
      document.getElementById("input_kotapkp_1").value = res[0].KOTAPKP
      document.getElementById("input_npwp_1").value = res[0].NPWP
      document.getElementById("input_tglpengukuhan_1").value = tanggalInput(res[0].TGLPENGUKUHAN);
      document.getElementById("input_penandatangan_1").value = res[0].Direksi
      document.getElementById("input_jabatan_1").value = res[0].Jabatan

      document.getElementById("input_namapkp_2").value = res[0].NAMAPKP1
      document.getElementById("input_alamatpkp1_2").value = res[0].ALAMATPKP21
      document.getElementById("input_alamatpkp2_2").value = res[0].ALAMATPKP22
      document.getElementById("input_kotapkp_2").value = res[0].KOTAPKP1
      document.getElementById("input_npwp_2").value = res[0].NPWP1
      document.getElementById("input_tglpengukuhan_2").value = tanggalInput(res[0].TGLPENGUKUHAN1);
      document.getElementById("input_penandatangan_2").value = res[0].Direksi
      document.getElementById("input_jabatan_2").value = res[0].Jabatan

      // Gambar TTD & Logo tersimpan sebagai file di folder berkas/img/ttd & berkas/img/logoperusahaan;
      // controller mengirimnya sebagai data URI (kosong = belum ada gambar). Pilihan file yang
      // belum disimpan ikut dibuang supaya Batal benar-benar kembali ke data tersimpan.
      let gambar = res[0].GAMBAR || {}
      ;['ttd_1', 'logo_1', 'ttd_2', 'logo_2'].forEach(k => {
        document.getElementById('input_' + k).value = ''
        let jenis = k.split('_')[0], slot = k.split('_')[1]
        document.getElementById('stp_' + jenis + '_box_' + slot).innerHTML = gambar[k]
          ? `<img src="${gambar[k]}" alt="${jenis}">`
          : '<i class="bi bi-image stp-upload-placeholder"></i>'
      })

      berhasil = true
    },
    error: function (err) {
      console.log(err);
      alertify.warning('Gagal memuat data perusahaan, silakan refresh browser');
    }
  })

  return berhasil
}

function submitSetingPerusahaan() {
  let _token = $("#_token").val();

  if (!String($("#input_nama").val()).trim()) {
    alertify.warning("Nama perusahaan harus diisi");
    return
  }

  let isian = {
      _token,
      nama:           $("#input_nama").val(),
      alamat1:        $("#input_alamat1").val(),
      alamat2:        $("#input_alamat2").val(),
      kota:           $("#input_kota").val(),
      telpon:         $("#input_telpon").val(),
      fax:            $("#input_fax").val(),
      email:          $("#input_email").val(),

      namapkp:        $("#input_namapkp_1").val(),
      alamatpkp1:     $("#input_alamatpkp1_1").val(),
      alamatpkp2:     $("#input_alamatpkp2_1").val(),
      kotapkp:        $("#input_kotapkp_1").val(),
      npwp:           $("#input_npwp_1").val(),
      tglpengukuhan:  $("#input_tglpengukuhan_1").val(),

      namapkp1:        $("#input_namapkp_2").val(),
      alamatpkp21:     $("#input_alamatpkp1_2").val(),
      alamatpkp22:     $("#input_alamatpkp2_2").val(),
      kotapkp1:        $("#input_kotapkp_2").val(),
      npwp1:           $("#input_npwp_2").val(),
      tglpengukuhan1:  $("#input_tglpengukuhan_2").val(),

      direksi: $("#input_penandatangan_1").val(),
      jabatan: $("#input_jabatan_1").val(),
  }

  // Dikirim sebagai FormData supaya gambar TTD & Logo yang dipilih ikut terunggah.
  // Gambar yang tidak dipilih ulang tidak dikirim, jadi file lamanya tetap dipakai.
  let data = new FormData()
  Object.keys(isian).forEach(k => data.append(k, isian[k] == null ? '' : isian[k]))
  ;['ttd_1', 'logo_1', 'ttd_2', 'logo_2'].forEach(k => {
    let file = document.getElementById('input_' + k).files[0]
    if (file) { data.append(k, file) }
  })

  $.ajax({
    url: "{!! url('setnomortransaksispedit') !!}",
    type: "post",
    async: false,
    data: data,
    processData: false,
    contentType: false,
    success: function (res) {
      if (res != 1) {
        alertify.warning(res);
      } else {
        alertify.success("Data Perusahaan telah disimpan");
        loadAll();
      }
    },
    error: function (err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}

window.onload = function(){
  loadAll();
  updateContohFormat();
};

</script>
@endsection