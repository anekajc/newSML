@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Set Pemakai')

@section('css')
<style>
  /* Kartu ringkasan di atas tabel - nilai disalin dari tableMaster2.css (tidak dimuat lagi di
     layout newmasterTest karena menimpa gaya tabel daftar .data-table). */
  .sp-stats-row { display: flex; gap: 16px; margin-bottom: 14px; flex-wrap: wrap; }
  .sp-stat-card {
    background: #fff;
    border: 1px solid #e7e9ee;
    border-radius: 10px;
    box-shadow: 0 1px 2px rgba(20,20,43,.04), 0 1px 1px rgba(20,20,43,.03);
    padding: 14px 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 200px;
    flex: 1 1 200px;
  }
  .sp-stat-icon {
    width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex: none;
  }
  .sp-stat-icon.is-purple { background: #efe9ff; color: #6f42f3; }
  .sp-stat-icon.is-green  { background: #e7f7ed; color: #16a34a; }
  .sp-stat-icon.is-blue   { background: #e8edff; color: #2563eb; }
  .sp-stat-label { font-size: 13px; color: #6b7280; margin-bottom: 2px; }
  .sp-stat-value { font-size: 22px; font-weight: 700; line-height: 1.1; color: #1f2430; }
  .sp-stat-sub   { font-size: 12px; color: #6b7280; margin-top: 2px; }

  /* Tabel hak akses di modal Akses Menu / Akses Report / Akses COA. */
  .akses-user { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; font-size: 13.5px; color: #6b7280; }
  .akses-user b { color: #1f2430; font-size: 15px; }
  .akses-wrap { max-height: 62vh; overflow: auto; border: 1px solid #e7e9ee; border-radius: 8px; }
  .akses-tabel { width: 100%; margin: 0; border-collapse: separate; border-spacing: 0; font-size: 13px; }
  .akses-tabel thead th {
    position: sticky; top: 0; z-index: 2;
    background: #f7f8fa; color: #6b7280; font-weight: 700; font-size: 11.5px;
    text-transform: uppercase; letter-spacing: .03em; text-align: center;
    padding: 8px 6px; border-bottom: 1px solid #e7e9ee; white-space: nowrap;
  }
  .akses-tabel tbody td { padding: 5px 8px; border-bottom: 1px solid #f0f1f4; vertical-align: middle; white-space: nowrap; }
  .akses-tabel tbody td.cek { text-align: center; }
  .akses-tabel tbody tr:hover td { background: #f7f9ff; }
  .akses-tabel tbody tr.akses-induk td { background: #fff8e1; font-weight: 600; }
  .akses-tabel input[type="checkbox"] { width: 15px; height: 15px; cursor: pointer; accent-color: #2563eb; vertical-align: middle; }
  .akses-kosong { text-align: center; color: #9ca3af; padding: 18px !important; }

  /* Modal Akses COA: dua daftar berdampingan + tombol pindah di tengah. */
  .coa-grid { display: grid; grid-template-columns: minmax(0, 1fr) 56px minmax(0, 1fr); gap: 14px; align-items: stretch; }
  .coa-panel { display: flex; flex-direction: column; min-width: 0; }
  .coa-judul { font-size: 12.5px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 6px; display: flex; justify-content: space-between; }
  .coa-judul span { font-weight: 600; text-transform: none; letter-spacing: 0; }
  .coa-panel .akses-wrap { height: 55vh; max-height: none; }
  .coa-tombol { display: flex; flex-direction: column; justify-content: center; gap: 12px; }
  .coa-tombol .btn { width: 44px; height: 36px; padding: 0; border-radius: 8px; }
  @media (max-width: 767.98px) {
    .coa-grid { grid-template-columns: 1fr; }
    .coa-tombol { flex-direction: row; justify-content: center; }
  }
</style>
@endsection

@section('content')

  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Berkas</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Set Pemakai</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Set Pemakai</h1>
      <p>Master — manajemen pengguna &amp; akses sistem</p>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add User</button>
  </div> --}}

<div id="contentContainer" class="container-fluid po-list-page">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  <!-- Kartu ringkasan - angkanya diisi loadAll() -->
  <div class="sp-stats-row">
    <div class="sp-stat-card">
      <div class="sp-stat-icon is-purple"><i class="bi bi-people"></i></div>
      <div>
        <div class="sp-stat-label">Total User</div>
        <div class="sp-stat-value" id="stat_total">-</div>
      </div>
    </div>
    <div class="sp-stat-card">
      <div class="sp-stat-icon is-green"><i class="bi bi-check-circle"></i></div>
      <div>
        <div class="sp-stat-label">User Aktif</div>
        <div class="sp-stat-value" id="stat_aktif">-</div>
        <div class="sp-stat-sub"><span id="stat_nonaktif">-</span> Tidak Aktif</div>
      </div>
    </div>
    <div class="sp-stat-card">
      <div class="sp-stat-icon is-blue"><i class="bi bi-shield-check"></i></div>
      <div>
        <div class="sp-stat-label">Administrator</div>
        <div class="sp-stat-value" id="stat_admin">-</div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body" style="padding:0;">

  @include('master.partials.toolbarMaster')

      <table id="tabel" class="data-table po-aksi-hover">
        <thead>
          <tr>
            <th style="padding: 4px 12px;" scope="col">Actions</th>
            <th style="padding: 4px 12px;" scope="col">User ID</th>
            <th style="padding: 4px 12px;" scope="col">Nama</th>
            <th style="padding: 4px 12px;" scope="col">Level</th>
          </tr>
        </thead>
        <tbody id="tabel_data" class="text-left">
        </tbody>
      </table>

    </div>
  </div>

</div>

<!-- start modal akses menu -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="judulAksesMenu" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style="width: 95%; max-width:1400px;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="judulAksesMenu">Akses Menu</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="input_add_username" value="" />
        <div class="akses-user">User <b id="label_akses_username">-</b> <span>· perubahan langsung tersimpan saat kotak dicentang</span></div>

        <div class="akses-wrap">
          <table id="addTable" class="akses-tabel">
            <thead>
              <tr>
                <th scope="col" style="text-align:left;">Kode Menu</th>
                <th scope="col" style="text-align:left;">Keterangan</th>
                <th scope="col">Akses</th>
                <th scope="col">Tambah</th>
                <th scope="col">Koreksi</th>
                <th scope="col">Hapus</th>
                <th scope="col">Cetak</th>
                <th scope="col">Export</th>
                <th scope="col">Oto1</th>
                <th scope="col">Oto2</th>
                <th scope="col">Oto3</th>
                <th scope="col">Oto4</th>
                <th scope="col">Oto5</th>
                <th scope="col">Batal</th>
              </tr>
            </thead>
            <tbody id="addTableData">
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Tutup</button>
        {{-- <button type="button" class="btn btn-primary" onclick="submitAkses()">Submit</button> --}}
      </div>
    </div>
  </div>
</div>
<!-- End modal akses menu-->

<!-- start modal akses menu report -->
<div class="modal fade" id="formReport" tabindex="-1" role="dialog" aria-labelledby="judulAksesReport" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width:900px;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="judulAksesReport">Akses Menu Report</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="input_report_username" value="" />
        <div class="akses-user">User <b id="label_report_username">-</b> <span>· perubahan langsung tersimpan saat kotak dicentang</span></div>

        <div class="akses-wrap">
          <table id="reportTable" class="akses-tabel">
            <thead>
              <tr>
                <th scope="col" style="text-align:left;">Kode Menu</th>
                <th scope="col" style="text-align:left;">Keterangan</th>
                <th scope="col">Akses</th>
                <th scope="col">Design</th>
                <th scope="col">Export</th>
              </tr>
            </thead>
            <tbody id="reportTableData">
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Tutup</button>
        {{-- <button type="button" class="btn btn-primary" onclick="submitAksesReport()">Submit</button> --}}
      </div>
    </div>
  </div>
</div>
<!-- End modal akses menu report-->


<!-- start modal akses coa -->
<div class="modal fade" id="formCOA" tabindex="-1" role="dialog" aria-labelledby="judulAksesCOA" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style="width: 95%; max-width:1300px;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="judulAksesCOA">Akses COA</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="input_coa_username" value="" />
        <div class="akses-user">User <b id="label_coa_username">-</b> <span>· centang perkiraan, lalu pakai tombol di tengah untuk memindahkan</span></div>

        <div class="coa-grid">
          <div class="coa-panel">
            <div class="coa-judul">Perkiraan yang tersedia <span id="jumlahCOA">0</span></div>
            <div class="akses-wrap">
              <table id="tableCOA" class="akses-tabel">
                <thead>
                  <tr>
                    <th scope="col" style="width:44px;">Pilih</th>
                    <th scope="col" style="text-align:left;">Perkiraan</th>
                    <th scope="col" style="text-align:left;">Keterangan</th>
                  </tr>
                </thead>
                <tbody id="tableDataCOA">
                </tbody>
              </table>
            </div>
          </div>

          <div class="coa-tombol">
            <button class="btn btn-chip-biru" type="button" title="Beri akses semua perkiraan" onclick="buttonAddAllCOA()"><i class="bi bi-chevron-double-right"></i></button>
            <button class="btn btn-chip-biru" type="button" title="Beri akses perkiraan yang dicentang" onclick="buttonAddCOA()"><i class="bi bi-chevron-right"></i></button>
            <button class="btn btn-batal-add" type="button" title="Cabut akses perkiraan yang dicentang" onclick="buttonDeleteCOA()"><i class="bi bi-chevron-left"></i></button>
            <button class="btn btn-batal-add" type="button" title="Cabut semua akses perkiraan" onclick="buttonDeleteAllCOA()"><i class="bi bi-chevron-double-left"></i></button>
          </div>

          <div class="coa-panel">
            <div class="coa-judul">Akses perkiraan yang diberikan <span id="jumlahAksesCOA">0</span></div>
            <div class="akses-wrap">
              <table id="tableAksesCOA" class="akses-tabel">
                <thead>
                  <tr>
                    <th scope="col" style="width:44px;">Pilih</th>
                    <th scope="col" style="text-align:left;">Perkiraan</th>
                    <th scope="col" style="text-align:left;">Keterangan</th>
                  </tr>
                </thead>
                <tbody id="tableDataAksesCOA">
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal akses coa-->

<!-- start modal add -->
<div class="modal fade"  id="formAddUser" tabindex="-1" role="dialog" aria-labelledby="judulAddUser" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 540px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="judulAddUser">Tambah User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">

        <div class="container-fluid">
          <div class="bs-form bs-form-1">
            <label for="input_add_NIK">NIK</label>
            <div class="input-group">
              {{-- Yang tampil NIK karyawan; yang dikirim ke server KeyNIK (input hidden) - lihat buttonSelectKaryawan. --}}
              <input type="text" class="form-control" id="input_add_NIK" placeholder="NIK" disabled>
              <input type="hidden" id="input_add_keynik">
              <div class="input-group-append">
                <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonNIK('add')" title="Cari"><i class="bi bi-search"></i></button>
              </div>
            </div>

            <label for="input_add_user">User</label>
            <input type="text" class="form-control" id="input_add_user" placeholder="User" maxlength="10" autocomplete="off">

            <label for="input_add_password">Password</label>
            <input type="password" class="form-control" id="input_add_password" maxlength="12" placeholder="Password" autocomplete="new-password">

            <label for="input_add_passwordConfirm">Konfirmasi PW</label>
            <input type="password" class="form-control" id="input_add_passwordConfirm" onchange="checkPassword()" maxlength="12" placeholder="Ulangi password" autocomplete="new-password">

            <label for="input_add_namaLengkap">Nama Lengkap</label>
            <input type="text" class="form-control" id="input_add_namaLengkap" placeholder="Nama Lengkap" maxlength="50">

            <label for="input_add_departemen">Departemen</label>
            <select class="form-control" id="input_add_departemen">
              <option value="" selected disabled>Pilih Departemen</option>
            </select>

            <label for="input_add_jabatan">Jabatan</label>
            <select class="form-control" id="input_add_jabatan">
              <option value="" selected disabled>Pilih Jabatan</option>
            </select>

            <label for="input_add_level">Level</label>
            <select class="form-control" id="input_add_level">
              <option value="0">User</option>
              <option value="1">Supervisor</option>
              <option value="2">Administrator</option>
            </select>

            <label for="input_add_status">Status</label>
            <select class="form-control" id="input_add_status">
              <option value="0">Offline</option>
              <option value="1">Online</option>
            </select>

            <label for="input_add_kodeKasir">Kode Kasir</label>
            <input type="text" class="form-control" id="input_add_kodeKasir" placeholder="Kode Kasir" maxlength="3">

            <label for="input_add_limit">Limit</label>
            <input type="text" inputmode="decimal" class="form-control text-right format-number" id="input_add_limit" value="0" step="any">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitAddUserData()">Simpan</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal add-->


<!-- start modal edit -->
<div class="modal fade" id="formEditUser" tabindex="-1" role="dialog" aria-labelledby="judulEditUser" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 540px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="judulEditUser">Edit User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">

        <div class="container-fluid">
          <div class="bs-form bs-form-1">
            <label for="input_edit_NIK">NIK</label>
            <div class="input-group">
              {{-- NIK hanya ditampilkan: Sp_FLpassWEB mode U tidak mengubah keynik (baris update-nya di-comment
                   di SP), jadi mengganti NIK di form Edit dulu tidak pernah tersimpan. --}}
              <input type="text" class="form-control" id="input_edit_NIK" placeholder="NIK" disabled>
              <input type="hidden" id="input_edit_keynik">
              {{-- <div class="input-group-append">
                <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonNIK('edit')" title="Cari"><i class="bi bi-search"></i></button>
              </div> --}}
            </div>

            <label for="input_edit_user">User</label>
            <input type="text" class="form-control" id="input_edit_user" disabled>

            <label for="input_edit_namaLengkap">Nama Lengkap</label>
            <input type="text" class="form-control" id="input_edit_namaLengkap" placeholder="Nama Lengkap" maxlength="50">

            <label for="input_edit_departemen">Departemen</label>
            <select class="form-control" id="input_edit_departemen">
              <option value="" selected disabled>Pilih Departemen</option>
            </select>

            <label for="input_edit_jabatan">Jabatan</label>
            <select class="form-control" id="input_edit_jabatan">
              <option value="" selected disabled>Pilih Jabatan</option>
            </select>

            <label for="input_edit_level">Level</label>
            <select class="form-control" id="input_edit_level">
              <option value="0">User</option>
              <option value="1">Supervisor</option>
              <option value="2">Administrator</option>
            </select>

            <label for="input_edit_status">Status</label>
            <select class="form-control" id="input_edit_status">
              <option value="0">Offline</option>
              <option value="1">Online</option>
            </select>

            <label for="input_edit_kodeKasir">Kode Kasir</label>
            <input type="text" class="form-control" id="input_edit_kodeKasir" placeholder="Kode Kasir" maxlength="3">

            <label for="input_edit_limit">Limit</label>
            <input type="text" inputmode="decimal" class="form-control text-right format-number" id="input_edit_limit" value="0" step="any">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitAddUserEdit()">Simpan</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal edit-->

@include('master.partials.modalOpenMaster')

@endsection

@section('js')
<script src="{!! URL::asset('js/master-list.js') !!}?v={{ @filemtime(base_path('public/js/master-list.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>
<script type="text/javascript">

// addEventListener("beforeunload", (event) => {
//   window.location.href = "{{ url('logout')}}";
// });

  let dataRefresh = []
  let listUser = []
  let listAkses = []
  let listAksesReport = []
  let listCOA = []
  let listAksesCOA = []

  // Modal pemilih (NIK / Departemen / Jabatan) dipakai form Tambah dan Edit - hasil pilihan
  // diisikan ke form yang membukanya. Dulu selalu ke form Tambah, jadi pemilih di form Edit
  // tidak mengubah apa-apa.
  let pickerTarget = 'add'

  // Nilai dari database ditulis ke HTML lewat fungsi ini: nama/keterangan yang mengandung
  // kutip atau < > tidak lagi merusak baris tabel & onclick.
  function spEsc (v) {
    if (v === null || v === undefined) { return '' }
    return String(v)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;')
  }

  // Respons AJAX yang bukan data (mis. halaman login karena sesi habis) ditangani di sini.
  function spBukanData (res, harap) {
    let ok = harap === 'array' ? Array.isArray(res) : (res && typeof res === 'object')
    if (!ok) {
      alertify.error('Sesi login habis atau server tidak merespons dengan benar. Silakan muat ulang halaman.')
      return true
    }
    return false
  }

  function spGagal (err) {
    console.log(err)
    alertify.error('Gagal terhubung ke server, silakan coba lagi')
  }

  function badgeLevel (tingkat) {
    if (Number(tingkat) === 0) { return '<span class="sp-badge is-user">User</span>' }
    if (Number(tingkat) === 1) { return '<span class="sp-badge is-supervisor">Supervisor</span>' }
    if (Number(tingkat) === 2) { return '<span class="sp-badge is-admin">Administrator</span>' }
    return spEsc(tingkat)
  }

  function isiRingkasan (res) {
    let aktif = res.filter(u => Number(u.STATUS) === 1).length
    document.getElementById('stat_total').textContent = res.length
    document.getElementById('stat_aktif').textContent = aktif
    document.getElementById('stat_nonaktif').textContent = res.length - aktif
    document.getElementById('stat_admin').textContent = res.filter(u => Number(u.TINGKAT) === 2).length
  }

  function loadAll () {

    let berhasil = false
    $.ajax({
      url: "{!! url('newsetpemakailoadall') !!}",
      type: "get",
      async: false,
      success: function(res) {
        if (spBukanData(res, 'array')) { return }
        listUser = res
        berhasil = true
      },
      error: spGagal
    })
    if (!berhasil) { return }

    if ($.fn.DataTable.isDataTable('#tabel')) {
      $('#tabel').DataTable().destroy()
    }

    // Urutan sel HARUS sama dengan <thead>: Actions, User ID, Nama, Level. Dulu baris hasil
    // loadAll() menaruh tombol aksi di kolom terakhir, jadi setelah edit/hapus tombolnya
    // pindah ke bawah kolom Level.
    let rowTable = ''
    listUser.forEach((item, i) => {
      rowTable += `<tr>
        <td style="white-space:nowrap;" class="text-center">
          <div class="action-buttons-wrap">
            <button title="Edit User" class="btn-action-sm btn-action-success" type="button" onclick="editUser(${i})"><i class="bi bi-pen"></i></button>
            <button title="Akses Menu" class="btn-action-sm btn-action-primary" type="button" onclick="editAkses(${i})"><i class="bi bi-card-checklist"></i></button>
            <button title="Akses Report" class="btn-action-sm btn-action-primary" type="button" onclick="editAksesReport(${i})"><i class="bi bi-card-list"></i></button>
            <button title="Akses COA" class="btn-action-sm btn-action-primary" type="button" onclick="editCOA(${i})"><i class="bi bi-card-heading"></i></button>
            <button title="Hapus User" class="btn-action-sm btn-action-danger" type="button" onclick="deleteUser(${i})"><i class="bi bi-trash"></i></button>
          </div>
        </td>
        <td>${spEsc(item.USERID)}</td>
        <td>${spEsc(item.FullName)}</td>
        <td>${badgeLevel(item.TINGKAT)}</td>
      </tr>`
    })
    {{-- Dinonaktifkan: tiga tombol ini belum punya fungsi sendiri - "Akses Gudang" membuka
         modal Akses Menu, "COA Report" & "Akses COA BS" membuka modal Akses COA biasa.
         Aktifkan lagi setelah modal & route-nya dibuat.
      <button title="Akses Gudang" class="btn-action-sm btn-action-primary" type="button" onclick="editAkses(${i})"><i class="bi bi-box2"></i></button>
      <button title="COA Report" class="btn-action-sm btn-action-primary" type="button" onclick="editCOA(${i})"><i class="bi bi-postcard"></i></button>
      <button title="Akses COA BS" class="btn-action-sm btn-action-primary" type="button" onclick="editCOA(${i})"><i class="bi bi-postcard"></i></button>
    --}}

    document.getElementById("tabel_data").innerHTML = rowTable
    isiRingkasan(listUser)

    $("#tabel").DataTable({
      "lengthChange": false,
      "paging": true,
      "searching": true,
      "ordering": false,
      "dom": MasterList.dom, "order": [], "language": MasterList.bahasa,
      "pageLength": MasterList.panjang()
    })
    MasterList.selesai('#tabel')

  }

  function userDari (idx) {
    return listUser[idx] ? listUser[idx].username : ''
  }

  function deleteUser (idx) {
    let username = userDari(idx)
    if (!username) { return }

    alertify.confirm('Hapus User', 'Apakah yakin ingin menghapus user ' + spEsc(username) + ' ?',
        function() {
          let _token = $("#_token").val()

          $.ajax({
            url: "{!! url('newsetpemakaideleteuser') !!}",
            type: "post",
            async: false,
            data: {
              _token : _token,
              username: username
            },
            success: function(res) {
              if (res != 1) {
                alertify.warning(res)
              } else {
                alertify.success("User " + spEsc(username) + " telah dihapus")
                loadAll()
              }
            },
            error: spGagal
          })
        }
      ,function(){
      })

  }

  /* ================= Akses Menu ================= */

  // [id checkbox, kolom DBFLMENUWEB, nama field untuk sp_updatesetmenuweb1]
  const kolomAkses = [
    ['hasaccess', 'HASACCESS'],
    ['istambah', 'ISTAMBAH'],
    ['isKoreksi', 'ISKOREKSI'],
    ['isHapus', 'ISHAPUS'],
    ['isCetak', 'ISCETAK'],
    ['isExport', 'ISEXPORT'],
    ['isOtorisasi1', 'IsOtorisasi1'],
    ['isOtorisasi2', 'IsOtorisasi2'],
    ['isOtorisasi3', 'IsOtorisasi3'],
    ['isOtorisasi4', 'IsOtorisasi4'],
    ['isOtorisasi5', 'IsOtorisasi5'],
    ['isBatal', 'IsBatal']
  ]

  function muatAkses (username) {
    let _token = $("#_token").val()
    let berhasil = false
    $.ajax({
      url: "{!! url('newsetpemakailistakses') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        userid: username
      },
      success: function(res) {
        if (spBukanData(res, 'array')) { return }
        listAkses = res
        berhasil = true
      },
      error: spGagal
    })
    if (!berhasil) { return false }

    let rowTable = ""
    listAkses.forEach((item, i) => {
      let induk = Number(item.L0) === 0
      rowTable += `<tr${induk ? ' class="akses-induk"' : ''}>
        <td>${spEsc(item.KODEMENU)}</td>
        <td>${spEsc(item.Keterangan)}</td>`
      kolomAkses.forEach(([field, kolom]) => {
        let klik = induk
          ? `clickUpdateAksesMenuHeader(${i}, '${field}')`
          : `clickUpdateAksesMenu(${i})`
        rowTable += `<td class="cek"><input type="checkbox" id="akses_checkbox_${field}${i}" onclick="${klik}"${Number(item[kolom]) === 1 ? ' checked' : ''}></td>`
      })
      rowTable += `</tr>`
    })

    if (!listAkses.length) {
      rowTable = '<tr><td colspan="14" class="akses-kosong">Tidak ada data menu</td></tr>'
    }

    document.getElementById("input_add_username").value = username
    document.getElementById("label_akses_username").textContent = username
    document.getElementById("addTableData").innerHTML = rowTable
    return true
  }

  function editAkses (idx) {
    let username = userDari(idx)
    if (!username) { return }
    if (muatAkses(username)) {
      $("#form").modal('show')
    }
  }

  function clickUpdateAksesMenuHeader (index, field) {
    let _token = $("#_token").val()
    let username = $("#input_add_username").val()
    let kodemenu = listAkses[index].KODEMENU
    let checkBox = document.getElementById(`akses_checkbox_${field}${index}`).checked
    let nilai = checkBox ? 1 : 0

    // Centang menu induk ikut mencentang semua sub menu (kode diawali kode induk) - aturan yang
    // sama dengan update di server, jadi tampilan selalu sesuai isi database.
    let awal = String(kodemenu).trim()
    listAkses.forEach((item, i) => {
      if (String(item.KODEMENU).startsWith(awal)) {
        document.getElementById(`akses_checkbox_${field}${i}`).checked = checkBox
      }
    })

    $.ajax({
      url: "{!! url('newsetpemakaispupdateaksesheader') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        username: username,
        nilai,
        field,
        headermenu: kodemenu
      },
      success: function(res) {
        if (res != 1) {
          alertify.warning('Akses menu ' + spEsc(kodemenu) + ' gagal disimpan')
          muatAkses(username)
        }
      },
      error: function (err) {
        spGagal(err)
        // Tampilan dikembalikan ke isi database supaya tidak menyesatkan.
        muatAkses(username)
      }
    })
  }

  function clickUpdateAksesMenu (index) {
    let _token = $("#_token").val()
    let username = $("#input_add_username").val()

    let tempData = {
      KODEMENU: listAkses[index].KODEMENU,
      Keterangan: listAkses[index].Keterangan,
      HASACCESS: 0,
      ISTAMBAH: 0,
      ISKOREKSI: 0,
      ISHAPUS: 0,
      ISCETAK: 0,
      ISBATAL: 0,
      ISEXPORT: 0,
      ISOTO1: 0,
      ISOTO2: 0,
      ISOTO3: 0,
      ISOTO4: 0,
      ISOTO5: 0,
      // Tidak tampil di layar - nilai lamanya dikirim balik supaya tidak ter-reset.
      PEMBATALAN: Number(listAkses[index].pembatalan) === 1 ? 1 : 0,
    }
    const peta = {
      hasaccess: 'HASACCESS', istambah: 'ISTAMBAH', isKoreksi: 'ISKOREKSI', isHapus: 'ISHAPUS',
      isCetak: 'ISCETAK', isBatal: 'ISBATAL', isExport: 'ISEXPORT',
      isOtorisasi1: 'ISOTO1', isOtorisasi2: 'ISOTO2', isOtorisasi3: 'ISOTO3', isOtorisasi4: 'ISOTO4', isOtorisasi5: 'ISOTO5'
    }
    Object.keys(peta).forEach(field => {
      if (document.getElementById(`akses_checkbox_${field}${index}`).checked) {
        tempData[peta[field]] = 1
      }
    })

    $.ajax({
      url: "{!! url('newsetpemakaispupdateakses') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        username: username,
        tempData: tempData
      },
      success: function(res) {
        if (res != 1) {
          alertify.warning('Akses menu ' + spEsc(tempData.KODEMENU) + ' gagal disimpan')
          muatAkses(username)
        }
      },
      error: function (err) {
        spGagal(err)
        muatAkses(username)
      }
    })

  }

  /* ================= Akses Report ================= */

  function muatAksesReport (username) {
    let _token = $("#_token").val()
    let berhasil = false
    $.ajax({
      url: "{!! url('newsetpemakailistaksesreport') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        userid: username
      },
      success: function(res) {
        if (spBukanData(res, 'array')) { return }
        listAksesReport = res
        berhasil = true
      },
      error: spGagal
    })
    if (!berhasil) { return false }

    let rowTable = ""
    listAksesReport.forEach((item, i) => {
        rowTable += `<tr>
        <td>${spEsc(item.KODEMENU)}</td>
        <td>${spEsc(item.Keterangan)}</td>
        <td class="cek"><input onclick="clickUpdateAksesMenuReport(${i})" type="checkbox" id="akses_report_checkbox_hasaccess${i}"${Number(item.Access) === 1 ? ' checked' : ''}></td>
        <td class="cek"><input onclick="clickUpdateAksesMenuReport(${i})" type="checkbox" id="akses_report_checkbox_isdesign${i}"${Number(item.IsDesign) === 1 ? ' checked' : ''}></td>
        <td class="cek"><input onclick="clickUpdateAksesMenuReport(${i})" type="checkbox" id="akses_report_checkbox_isexport${i}"${Number(item.Isexport) === 1 ? ' checked' : ''}></td>
        </tr>`
    })

    if (!listAksesReport.length) {
      rowTable = '<tr><td colspan="5" class="akses-kosong">Tidak ada data report</td></tr>'
    }

    document.getElementById("input_report_username").value = username
    document.getElementById("label_report_username").textContent = username
    document.getElementById("reportTableData").innerHTML = rowTable
    return true
  }

  function editAksesReport (idx) {
    let username = userDari(idx)
    if (!username) { return }
    if (muatAksesReport(username)) {
      $("#formReport").modal('show')
    }
  }

  function clickUpdateAksesMenuReport (index) {
    let _token = $("#_token").val()
    let username = $("#input_report_username").val()

    let tempData = {
      KODEMENU: listAksesReport[index].KODEMENU,
      Keterangan: listAksesReport[index].Keterangan,
      HASACCESS: document.getElementById(`akses_report_checkbox_hasaccess${index}`).checked ? 1 : 0,
      ISDESIGN: document.getElementById(`akses_report_checkbox_isdesign${index}`).checked ? 1 : 0,
      ISEXPORT: document.getElementById(`akses_report_checkbox_isexport${index}`).checked ? 1 : 0
    }

    $.ajax({
      url: "{!! url('newsetpemakaispupdateaksesreport') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        username: username,
        tempData: tempData
      },
      success: function(res){
        if (res != 1) {
          alertify.warning('Akses report ' + spEsc(tempData.KODEMENU) + ' gagal disimpan')
          muatAksesReport(username)
        }
      },
      error: function (err) {
        spGagal(err)
        muatAksesReport(username)
      }
    })

  }

  /* ================= Akses COA ================= */

  function refreshCOA (username) {
    let _token = $("#_token").val()
    let berhasil = false

    $.ajax({
      url: "{!! url('newsetpemakailistcoa') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        username: username
      },
      success: function(res) {
        if (spBukanData(res, 'object') || !Array.isArray(res.listCoa)) { return }
        listCOA = res.listCoa
        listAksesCOA = res.listAksesCoa || []
        berhasil = true
      },
      error: spGagal
    })
    if (!berhasil) { return false }

    let rowTable = ""
    listCOA.forEach((item, i) => {
      rowTable += `
      <tr>
      <td class="cek"><input type="checkbox" onclick="clickCheckboxCOA(${i})" id="COA_checkbox${i}"></td>
      <td>${spEsc(item.Perkiraan)}</td>
      <td>${spEsc(item.Keterangan)}</td>
      </tr>
      `
    })
    if (!listCOA.length) {
      rowTable = '<tr><td colspan="3" class="akses-kosong">Semua perkiraan sudah diberikan</td></tr>'
    }
    document.getElementById("tableDataCOA").innerHTML = rowTable

    let rowTableAkses = ""
    listAksesCOA.forEach((item, i) => {
      rowTableAkses += `
      <tr>
      <td class="cek"><input type="checkbox" id="aksesCOA_checkbox${i}"></td>
      <td>${spEsc(item.Perkiraan)}</td>
      <td>${item.Keterangan === null || item.Keterangan === undefined ? '<i style="color:#dc2626;">(perkiraan sudah tidak ada)</i>' : spEsc(item.Keterangan)}</td>
      </tr>
      `
    })
    if (!listAksesCOA.length) {
      rowTableAkses = '<tr><td colspan="3" class="akses-kosong">Belum ada akses perkiraan</td></tr>'
    }
    document.getElementById("tableDataAksesCOA").innerHTML = rowTableAkses

    document.getElementById("jumlahCOA").textContent = listCOA.length
    document.getElementById("jumlahAksesCOA").textContent = listAksesCOA.length
    return true
  }

  function editCOA (idx) {
    let username = userDari(idx)
    if (!username) { return }

    document.getElementById("input_coa_username").value = username
    document.getElementById("label_coa_username").textContent = username

    if (refreshCOA(username)) {
      $("#formCOA").modal('show')
    }
  }

  function buttonDeleteAllCOA () {
    let _token = $("#_token").val()
    let username = $("#input_coa_username").val()

    if (!listAksesCOA.length) {
      alertify.warning('Belum ada akses perkiraan yang bisa dicabut')
      return
    }

    alertify.confirm('Cabut Semua Akses COA', 'Cabut semua akses perkiraan milik user ' + spEsc(username) + ' ?',
      function () {
        $.ajax({
          url: "{!! url('newsetpemakaideleteallaksescoa') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            username,
          },
          success: function(res) {
            refreshCOA(username)
            alertify.success('Semua akses perkiraan telah dicabut')
          },
          error: spGagal
        })
      },
      function () {})

  }

  function buttonDeleteCOA () {
    let deleteAksesCOA = []

    let _token = $("#_token").val()
    let username = $("#input_coa_username").val()
    listAksesCOA.forEach((item, i) => {
      let cb = document.getElementById(`aksesCOA_checkbox${i}`)
      if (cb && cb.checked) {
        deleteAksesCOA.push({ Perkiraan: item.Perkiraan })
      }
    })

    if (!deleteAksesCOA.length) {
      alertify.warning('Centang dulu perkiraan di daftar kanan yang akan dicabut aksesnya')
      return
    }

    $.ajax({
      url: "{!! url('newsetpemakaideleteaksescoa') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        username,
        perkiraan: deleteAksesCOA
      },
      success: function(res) {
        if (res != 1) {
          alertify.warning(res)
        }
        refreshCOA(username)
      },
      error: spGagal
    })
  }

  function buttonAddAllCOA () {
    let username = $("#input_coa_username").val()
    let _token = $("#_token").val()

    if (!listCOA.length) {
      alertify.warning('Semua perkiraan sudah diberikan')
      return
    }

    $.ajax({
      url: "{!! url('newsetpemakaiupdateaddallcoa') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        username
      },
      success: function(res) {
        refreshCOA(username)
        alertify.success('Semua perkiraan telah diberikan')
      },
      error: spGagal
    })
  }

  function buttonAddCOA () {
    let username = $("#input_coa_username").val()
    let _token = $("#_token").val()

    let adaPilihan = listCOA.some((item, i) => {
      let cb = document.getElementById(`COA_checkbox${i}`)
      return cb && cb.checked
    })
    if (!adaPilihan) {
      alertify.warning('Centang dulu perkiraan di daftar kiri yang akan diberikan')
      return
    }

    $.ajax({
      url: "{!! url('newsetpemakaiupdateaddcoa') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        username
      },
      success: function(res) {
        refreshCOA(username)
      },
      error: spGagal
    })
  }

  // Centang di daftar kiri langsung menandai DBPERKIRAAN.iskirim; tombol ">" lalu memberi
  // akses semua perkiraan yang bertanda iskirim=1 (alur lama, tidak diubah).
  function clickCheckboxCOA (index) {
    let _token = $("#_token").val()
    let cb = document.getElementById(`COA_checkbox${index}`)
    let nilai = cb.checked ? 1 : 0
    let perkiraan = listCOA[index].Perkiraan

    $.ajax({
      url: "{!! url('newsetpemakaiupdateiskirimcoa') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        perkiraan,
        nilai
      },
      error: function (err) {
        spGagal(err)
        cb.checked = !cb.checked
      }
    })

  }

  /* ================= Tambah / Edit user ================= */

  function buttonAdd () {
    ['NIK', 'keynik', 'user', 'password', 'passwordConfirm', 'namaLengkap', 'kodeKasir']
      .forEach(f => { document.getElementById('input_add_' + f).value = '' })
    isiDropdown('departemen', 'add', '')
    isiDropdown('jabatan', 'add', '')
    document.getElementById('input_add_level').value = '0'
    document.getElementById('input_add_status').value = '0'
    setNum("input_add_limit", '0')
    passwordCheckState = 0

    $("#formAddUser").modal('show')
  }

  function editUser (idx) {
    let username = userDari(idx)
    if (!username) { return }
    let _token = $("#_token").val()

    $.ajax({
      url: "{!! url('newsetpemakaidetailuser') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        username: username
      },
      success: function(res) {
        if (spBukanData(res, 'array')) { return }
        if (!res.length) {
          alertify.warning('User ' + spEsc(username) + ' tidak ditemukan')
          return
        }

        document.getElementById('input_edit_keynik').value = res[0].keynik ?? ''
        document.getElementById('input_edit_NIK').value = nikDariKeynik(res[0].keynik)
        document.getElementById('input_edit_user').value = res[0].USERID ?? ''
        document.getElementById('input_edit_namaLengkap').value = res[0].FullName ?? ''
        isiDropdown('departemen', 'edit', res[0].kodeBag)
        isiDropdown('jabatan', 'edit', res[0].KodeJab)
        document.getElementById('input_edit_level').value = String(Number(res[0].TINGKAT) || 0)
        document.getElementById('input_edit_status').value = String(Number(res[0].STATUS) || 0)
        document.getElementById('input_edit_kodeKasir').value = res[0].KodeKasir ?? ''
        setNum("input_edit_limit", Number(res[0].limit) || 0)

        $("#formEditUser").modal('show')
      },
      error: spGagal
    })

  }

  /* ---------- Modal pemilih (NIK / Jabatan / Departemen) ---------- */

  function bukaPemilih (url, judul, kolom, render) {
    let hasil = null
    $.ajax({
      url: url,
      type: "get",
      async: false,
      success: function (res) {
        if (spBukanData(res, 'array')) { return }
        hasil = res
      },
      error: spGagal
    })
    if (!hasil) { return }
    dataRefresh = hasil

    // Tabel pemilih dilepas dari DataTables dulu sebelum header & isinya diganti (NIK /
    // Departemen / Jabatan memakai tabel yang sama).
    if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
      $('#tabelModalOpen').DataTable().destroy()
    }

    document.querySelector("#theadOpen").innerHTML = '<tr>' +
      kolom.map(k => `<th scope="col">${k}</th>`).join('') + '</tr>'
    document.getElementById("tabel_dataModalOpen").innerHTML = hasil.map(render).join('')
    document.getElementById("namaModalOpen").innerHTML = judul

    pickerKasInit('tabelModalOpen')
    $("#formModalOpen").modal('show')
  }

  function buttonNIK (mode) {
    pickerTarget = mode === 'edit' ? 'edit' : 'add'
    // Dipilih dengan klik baris (class pick-row, lihat public/js/picker-kas.js) - tanpa tombol Actions.
    bukaPemilih("{!! url('newsetpemakaiLoadKaryawan') !!}", 'NIK Karyawan', ['NIK', 'Nama'], (item, i) => `<tr class="pick-row" style="cursor:pointer" onclick="buttonSelectKaryawan(${i})">
      <td>${spEsc(item.NIK)}</td>
      <td>${spEsc(item.Nama)}</td>
    </tr>`)
  }

  function buttonSelectKaryawan (i) {
    let item = dataRefresh[i]
    // Yang disimpan ke DBFLPASS.keynik adalah KeyNIK (int), bukan NIK KTP 16 digit -
    // NIK KTP melebihi batas int @Keynik dan membuat Sp_FLpassWEB gagal.
    document.getElementById('input_' + pickerTarget + '_keynik').value = item.KeyNIK
    // Yang tampil di form NIK karyawan.
    document.getElementById('input_' + pickerTarget + '_NIK').value = item.NIK ?? ''
    // Nama Lengkap yang masih kosong diisi nama karyawan yang dipilih.
    let nama = document.getElementById('input_' + pickerTarget + '_namaLengkap')
    if (!nama.value.trim()) { nama.value = item.Nama ?? '' }

    $("#formModalOpen").modal("hide")
  }

  /* ---------- Dropdown Departemen / Jabatan ---------- */

  // Daftar diambil sekali saat halaman dibuka, lalu dipakai form Tambah dan Edit.
  let listDepartemen = []
  let listJabatan = []
  let listKaryawan = null

  function ambilList (url) {
    let hasil = []
    $.ajax({
      url: url,
      type: "get",
      async: false,
      success: function (res) {
        if (spBukanData(res, 'array')) { return }
        hasil = res
      },
      error: spGagal
    })
    return hasil
  }

  // Isi <select> Departemen/Jabatan di form (add/edit) lalu pilih kode yang diberikan.
  // Kode user yang sudah tidak ada di master tetap ditampilkan supaya tidak hilang saat disimpan.
  function isiDropdown (jenis, mode, kode) {
    let list = jenis === 'departemen' ? listDepartemen : listJabatan
    let ambilKode = jenis === 'departemen' ? (r => r.KDDEP) : (r => r.KODEJAB)
    let ambilNama = jenis === 'departemen' ? (r => r.NMDEP) : (r => r.NamaJab)
    let judul = jenis === 'departemen' ? 'Pilih Departemen' : 'Pilih Jabatan'
    kode = String(kode ?? '').trim()

    let opsi = `<option value="" disabled>${judul}</option>` + list.map(r => {
      let k = String(ambilKode(r) ?? '').trim()
      return `<option value="${spEsc(k)}">${spEsc(k)} - ${spEsc(ambilNama(r))}</option>`
    }).join('')
    if (kode && !list.some(r => String(ambilKode(r) ?? '').trim() === kode)) {
      opsi += `<option value="${spEsc(kode)}">${spEsc(kode)}</option>`
    }

    let el = document.getElementById('input_' + mode + '_' + jenis)
    el.innerHTML = opsi
    el.value = kode
  }

  // Form Edit hanya menerima keynik dari DBFLPASS - NIK karyawan dicari dari daftar karyawan.
  function nikDariKeynik (keynik) {
    if (keynik === null || keynik === undefined || keynik === '') { return '' }
    if (listKaryawan === null) { listKaryawan = ambilList("{!! url('newsetpemakaiLoadKaryawan') !!}") }
    let k = listKaryawan.find(r => String(r.KeyNIK) === String(keynik).trim())
    return k ? (k.NIK ?? '') : ''
  }

  $(function () {
    listDepartemen = ambilList("{!! url('newsetpemakaiLoadDepartemen') !!}")
    listJabatan = ambilList("{!! url('newsetpemakaiLoadJabatan') !!}")
  })

  let passwordCheckState = 0

  function checkPassword () {

    let password = document.getElementById("input_add_password").value
    let passwordConfirm = document.getElementById("input_add_passwordConfirm").value

    if (!passwordConfirm) {
      passwordCheckState = 0
      return
    }

    if (password == passwordConfirm){
      passwordCheckState = 1
      alertify.success('Konfirmasi Password Sama')
    }
    else {
      passwordCheckState = 0
      alertify.error('Konfirmasi Password Salah')
    }

  }

  // Validasi yang sama untuk Tambah dan Edit.
  function cekFormUser (d, mode) {
    // NIK hanya dicek saat tambah: di form Edit NIK tidak bisa diubah (lihat komentar di form).
    if (mode === 'add' && !d.nik) { return "NIK harus diisi" }
    if (mode === 'add' && !/^\d+$/.test(d.nik)) { return "NIK harus berupa angka" }
    if (!d.user) { return "User harus diisi" }
    if (!d.namaLengkap) { return "Nama Lengkap harus diisi" }
    if (!d.departemen) { return "Departemen harus diisi" }
    if (!d.jabatan) { return "Jabatan harus diisi" }
    if (d.limit !== '' && (isNaN(Number(d.limit)) || Number(d.limit) < 0)) { return "Limit harus angka 0 atau lebih" }
    return ''
  }

  function ambilFormUser (mode) {
    let v = id => String($('#input_' + mode + '_' + id).val() ?? '').trim()
    return {
      // Field "nik" berisi KeyNIK (int) - nilai yang disimpan ke DBFLPASS.keynik.
      nik: v('keynik'),
      user: v('user'),
      namaLengkap: v('namaLengkap'),
      departemen: v('departemen'),
      jabatan: v('jabatan'),
      level: v('level'),
      status: v('status'),
      kodeKasir: v('kodeKasir'),
      // Limit memakai separator ribuan (.format-number) - koma dibuang sebelum dikirim.
      limit: v('limit').replace(/,/g, '') === '' ? '0' : v('limit').replace(/,/g, '')
    }
  }

  function submitAddUserEdit () {
    let _token = $("#_token").val()
    let data = ambilFormUser('edit')

    let pesan = cekFormUser(data, 'edit')
    if (pesan) {
      alertify.warning(pesan)
      return
    }

    $.ajax({
      url: "{!! url('newsetpemakaiAddUser') !!}",
      type: "post",
      async: false,
      data: Object.assign({ _token: _token, choice: 'U' }, data),
      success: function(res) {
        if (res != 1) {
          alertify.warning(res)
        }  else {
          alertify.success("Data User telah diedit")
          $("#formEditUser").modal('hide')
          loadAll()
        }
      },
      error: spGagal
    })

  }


  function submitAddUserData () {
    let _token = $("#_token").val()
    let data = ambilFormUser('add')
    let password = $("#input_add_password").val()
    let passwordConfirm = $("#input_add_passwordConfirm").val()

    let pesan = cekFormUser(data, 'add')
    if (pesan) {
      alertify.warning(pesan)
      return
    }

    if (!password) {
      alertify.warning("Password harus diisi")
      return
    }

    // Dulu hanya memunculkan pesan lalu tetap menyimpan walau konfirmasi password salah.
    if (password !== passwordConfirm) {
      passwordCheckState = 0
      alertify.error('Password tidak cocok, silahkan konfirmasi ulang.')
      return
    }

    $.ajax({
      url: "{!! url('newsetpemakaiAddUser') !!}",
      type: "post",
      async: false,
      data: Object.assign({ _token: _token, choice: 'I', password: password }, data),
      success: function(res) {
        if (res != 1) {
          alertify.warning(res)
        }  else {
          alertify.success("Data User telah ditambah")
          $("#formAddUser").modal('hide')
          loadAll()
        }
      },
      error: spGagal
    })

  }

  // Separator ribuan pada input angka (.format-number) - pola accounting/pengajuandpp.blade.php:
  // autoNumeric memformat tampilan, nilai yang diisi program lewat setNum(), dan pembacaan nilai
  // membuang koma dengan .replace(/,/g, '') sebelum dikirim.
  function toNum (v) {
    let n = parseFloat(String(v == null ? '' : v).replace(/,/g, ''))
    return isNaN(n) ? 0 : n
  }
  function setNum (id, v) {
    let el = document.getElementById(id)
    if (!el) return
    // Nilai kosong tetap kosong (validasi "harus diisi" bergantung pada ini).
    if (v === '' || v == null) {
      el.value = ''
    } else if ($(el).data('autoNumeric')) {
      $(el).autoNumeric('set', toNum(v))
    } else {
      el.value = v
    }
  }
  $(function () {
    $('.format-number').autoNumeric('init', { mDec: '2', vMin: '-9999999999999.99' })
  })

  window.onload = function(){
    loadAll()
  }

</script>

@endsection
