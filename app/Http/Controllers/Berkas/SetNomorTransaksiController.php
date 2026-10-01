<?php

namespace App\Http\Controllers\Berkas;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// use App\Http\Controllers\NewMenuController;

class SetNomorTransaksiController extends Controller
{

  public function index(Request $req) {

    // Daftar user & menu samping tidak dipakai halaman ini (layout newmasterTest mengambil menu
    // lewat AJAX /getmenu) - query-nya dimatikan supaya halaman lebih ringan.
    // $users = DB::connection("SML")->select('select * from DBFLPASS');
    // $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(1);

    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();

    return view('berkas.setnomortransaksi' , [
      "menul0" => [],
      "periode" => $periode,
    ]);

  }

  public function loadAll () {
    $users = DB::connection("SML")->select('select * from DBFLPASS');
    return $users;

  }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select("SELECT
    KODEUSAHA,
    NAMA,
    ALAMAT1,
    ALAMAT2,
    KOTA,
    Telpon,
    Fax,
    NAMAPKP,
    ALAMATPKP1,
    ALAMATPKP2,
    KOTAPKP,
    NPWP,
    TGLPENGUKUHAN,
    NAMAPKP1,
    ALAMATPKP21,
    ALAMATPKP22,
    KOTAPKP1,
    NPWP1,
    TGLPENGUKUHAN1,
    Direksi,
    Jabatan,
    email,
    L_Update
        FROM DBPERUSAHAAN");

    // Gambar TTD & Logo per NPWP dikirim sebagai data URI (kosong = belum ada gambar).
    if (count($detail)) {
      $gambar = [];
      foreach (self::SLOT_GAMBAR as $kunci => $slot) {
        $file = $this->cariFileGambar($slot[0], $slot[1]);
        $gambar[$kunci] = $file
          ? 'data:' . (mime_content_type($file) ?: 'image/png') . ';base64,' . base64_encode(file_get_contents($file))
          : '';
      }
      $detail[0]->GAMBAR = $gambar;
    }

    return $detail;
  }

  // Gambar TTD & Logo disimpan sebagai file di resources/views/berkas/img/{ttd,logoperusahaan},
  // satu file per NPWP: npwp1.<ext> & npwp2.<ext>. Kunci = nama field upload dari form.
  const SLOT_GAMBAR = [
    'ttd_1'  => ['ttd', 'npwp1'],
    'logo_1' => ['logoperusahaan', 'npwp1'],
    'ttd_2'  => ['ttd', 'npwp2'],
    'logo_2' => ['logoperusahaan', 'npwp2'],
  ];
  const EKSTENSI_GAMBAR = ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp'];

  private function folderGambar($folder) {
    return resource_path('views/berkas/img/' . $folder);
  }

  private function cariFileGambar($folder, $nama) {
    foreach (self::EKSTENSI_GAMBAR as $ext) {
      $file = $this->folderGambar($folder) . DIRECTORY_SEPARATOR . $nama . '.' . $ext;
      if (is_file($file)) {
        return $file;
      }
    }
    return null;
  }

  // Logo NPWP 1 untuk ikon sidebar layout newmasterTest. Folder gambar ada di resources (tidak
  // bisa diakses langsung dari browser), jadi file dikirim lewat route ini. URL di layout memakai
  // ?v=<waktu ubah file>, jadi aman di-cache browser lama - ganti logo otomatis ganti URL.
  public function logo() {
    $file = $this->cariFileGambar('logoperusahaan', 'npwp1');
    if (!$file) {
      abort(404);
    }
    return response()->file($file, ['Cache-Control' => 'private, max-age=31536000']);
  }

    public function submitEdit(Request $req) {
    // Kolom teks DBPERUSAHAAN hampir semuanya NOT NULL, sedangkan Laravel mengubah isian kosong
    // jadi null - dulu mengosongkan Fax/Alamat 2/dst membuat simpan gagal (500). Semua teks
    // dikirim sebagai string (kosong = ''); tanggal kosong tetap null (kolomnya boleh null).
    $t = function ($nama) use ($req) { return trim((string) $req->input($nama)); };
    $tgl = function ($nama) use ($req) {
      $v = trim((string) $req->input($nama));
      return preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) ? $v : null;
    };

    if ($t('nama') === '') {
      return 'Nama perusahaan harus diisi';
    }
    // Batas panjang kolom DBPERUSAHAAN.
    $batas = ['nama' => 40, 'alamat1' => 100, 'alamat2' => 100, 'kota' => 40, 'telpon' => 30, 'fax' => 30, 'email' => 100,
      'namapkp' => 40, 'alamatpkp1' => 100, 'alamatpkp2' => 100, 'kotapkp' => 40, 'npwp' => 40,
      'namapkp1' => 40, 'alamatpkp21' => 100, 'alamatpkp22' => 100, 'kotapkp1' => 40, 'npwp1' => 40,
      'direksi' => 50, 'jabatan' => 50];
    foreach ($batas as $nama => $maks) {
      if (strlen($t($nama)) > $maks) {
        return 'Isian ' . $nama . ' maksimal ' . $maks . ' karakter';
      }
    }

    // Gambar TTD & Logo: dicek dulu semuanya sebelum ada yang disimpan.
    $labelGambar = ['ttd_1' => 'Ttd NPWP 1', 'logo_1' => 'Logo NPWP 1', 'ttd_2' => 'Ttd NPWP 2', 'logo_2' => 'Logo NPWP 2'];
    $unggahan = [];
    foreach (self::SLOT_GAMBAR as $kunci => $slot) {
      if (!$req->hasFile($kunci)) {
        continue;
      }
      $file = $req->file($kunci);
      $ext = strtolower($file->getClientOriginalExtension());
      if (!$file->isValid()) {
        return 'Gambar ' . $labelGambar[$kunci] . ' gagal diunggah';
      }
      if (!in_array($ext, self::EKSTENSI_GAMBAR) || @getimagesize($file->getRealPath()) === false) {
        return 'Gambar ' . $labelGambar[$kunci] . ' harus berupa file gambar (png, jpg, gif, bmp, webp)';
      }
      if ($file->getSize() > 2 * 1024 * 1024) {
        return 'Gambar ' . $labelGambar[$kunci] . ' maksimal 2 MB';
      }
      $unggahan[$kunci] = [$file, $ext];
    }

    DB::connection('SML')->update(
        "UPDATE DBPERUSAHAAN SET
            NAMA = :nama,
            ALAMAT1 = :alamat1,
            ALAMAT2 = :alamat2,
            KOTA = :kota,
            Telpon = :telpon,
            Fax = :fax,
            email = :email,
            NAMAPKP = :namapkp,
            ALAMATPKP1 = :alamatpkp1,
            ALAMATPKP2 = :alamatpkp2,
            KOTAPKP = :kotapkp,
            NPWP = :npwp,
            TGLPENGUKUHAN = :tglpengukuhan,
            NAMAPKP1 = :namapkp1,
            ALAMATPKP21 = :alamatpkp21,
            ALAMATPKP22 = :alamatpkp22,
            KOTAPKP1 = :kotapkp1,
            NPWP1 = :npwp1,
            TGLPENGUKUHAN1 = :tglpengukuhan1,
            Direksi = :direksi,
            Jabatan = :jabatan,
            L_Update = GETDATE()
        ",
        [
            "nama"           => $t('nama'),
            "alamat1"        => $t('alamat1'),
            "alamat2"        => $t('alamat2'),
            "kota"           => $t('kota'),
            "telpon"         => $t('telpon'),
            "fax"            => $t('fax'),
            "email"          => $t('email'),
            "namapkp"        => $t('namapkp'),
            "alamatpkp1"     => $t('alamatpkp1'),
            "alamatpkp2"     => $t('alamatpkp2'),
            "kotapkp"        => $t('kotapkp'),
            "npwp"           => $t('npwp'),
            "tglpengukuhan"  => $tgl('tglpengukuhan'),
            "namapkp1"       => $t('namapkp1'),
            "alamatpkp21"    => $t('alamatpkp21'),
            "alamatpkp22"    => $t('alamatpkp22'),
            "kotapkp1"       => $t('kotapkp1'),
            "npwp1"          => $t('npwp1'),
            "tglpengukuhan1" => $tgl('tglpengukuhan1'),
            "direksi"        => $t('direksi'),
            "jabatan"        => $t('jabatan'),
        ]
    );

    // Simpan gambar baru; file lama di slot yang sama (ekstensi apa pun) dihapus dulu.
    foreach ($unggahan as $kunci => [$file, $ext]) {
      [$folder, $nama] = self::SLOT_GAMBAR[$kunci];
      while ($lama = $this->cariFileGambar($folder, $nama)) {
        @unlink($lama);
        if (is_file($lama)) {
          return 'Data tersimpan, tapi gambar lama ' . $labelGambar[$kunci] . ' tidak bisa diganti';
        }
      }
      $file->move($this->folderGambar($folder), $nama . '.' . $ext);
    }

    return 1;
    }

}
