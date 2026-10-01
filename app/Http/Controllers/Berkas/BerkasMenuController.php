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

class BerkasMenuController extends Controller
{

  public function index(Request $req) {

    // Daftar user & menu samping tidak dipakai halaman ini (layout newmasterTest mengambil menu
    // lewat AJAX /getmenu) - query-nya dimatikan supaya halaman lebih ringan.
    // $users = DB::connection("SML")->select('select * from DBFLPASS');
    // $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(1);

    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();

    return view('berkas.berkasmenu' , [
      "menul0" => [],
      "periode" => $periode,
    ]);

  }

  // L0 & OL tinyint, ACCESS int, ketiganya NOT NULL - isian kosong/bukan angka dulu membuat
  // simpan gagal (500).
  private function cekAngka (Request $req) {
    foreach (['L0' => 255, 'ACCESS' => 2147483647, 'OL' => 255] as $kolom => $maks) {
      $v = (string) $req->input($kolom);
      if (!ctype_digit($v) || (int) $v > $maks) {
        return $kolom . ' harus diisi angka 0-' . $maks;
      }
    }
    return '';
  }

  public function loadAll () {
    $users = DB::connection("SML")->select('select * from DBMENUWEB order by KODEMENU');
    return $users;

  }

    public function spAdd (Request $req)
    {
        $kode = trim((string) $req->KODEMENU);
        if ($kode === '') {
            return 'Kode menu harus diisi';
        }
        if (trim((string) $req->Keterangan) === '') {
            return 'Keterangan harus diisi';
        }
        if ($pesan = $this->cekAngka($req)) {
            return $pesan;
        }
        // Panjang kolom DBMENUWEB: KODEMENU varchar(25), Keterangan varchar(500).
        if (strlen($kode) > 25 || strpos($kode, ' ') !== false) {
            return 'Kode menu maksimal 25 karakter dan tanpa spasi';
        }
        if (strlen(trim((string) $req->Keterangan)) > 500) {
            return 'Keterangan maksimal 500 karakter';
        }

        $check = DB::connection('SML')->select(
            'SELECT * FROM DBMENUWEB where KODEMENU = :KODEMENU',
            ['KODEMENU' => $kode]
        );

        if ($check) {
            return 'Kode menu ' . $kode . ' sudah ada di database';
        }

        DB::connection('SML')->insert(
            'insert into DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon)
            values (:KODEMENU, :Keterangan, :L0, :ACCESS, :OL, :TipeTrans, :HeaderMenu, :href, :icon)',
            [
                'KODEMENU'   => $kode,
                'Keterangan' => trim((string) $req->Keterangan),
                'L0'         => $req->L0,
                'ACCESS'     => $req->ACCESS,
                'OL'         => $req->OL,
                'TipeTrans'  => '',
                'HeaderMenu' => 1,
                'href'       => '#',
                'icon'       => null,
            ]
        );

        return 1;
    }

  public function spDelete (Request $req) {
    // $check = DB::connection('SML')->select('SELECT * FROM DBPPL where KDDep = :kode' , ['kode' => $req->kode]);

    // if ($check) {
    //   return 'Dept digunakkan di Pembelian';
    // }

    //$check = DB::connection('SML')->select('SELECT * FROM DBDEPARTEMEN where KodeDepartemen = :kode' , ['kode' => $req->kode]);
    //if ($check) {
      //return 'ga bisa hapus';
    //}
    $kode = (string) $req->kode;
    if (trim($kode) === '') {
      return 'Kode menu belum dipilih';
    }

    // Menu yang masih punya sub menu (kode diawali kode ini) tidak boleh dihapus - sub menunya
    // akan tertinggal tanpa induk di pohon menu.
    $anak = DB::connection('SML')->select(
      'SELECT top 1 KODEMENU FROM DBMENUWEB where KODEMENU like :awal and KODEMENU <> :kode',
      ['awal' => str_replace(['[', '%', '_'], ['[[]', '[%]', '[_]'], rtrim($kode)) . '%', 'kode' => $kode]
    );
    if ($anak) {
      return 'Menu ' . $kode . ' masih punya sub menu (' . $anak[0]->KODEMENU . '), hapus sub menunya dulu';
    }

    $delete = DB::connection('SML')->update('delete from DBMENUWEB where KODEMENU = :kode' , ['kode' => $kode ]);

    // Hak akses web untuk menu ini ikut dibuang - kalau tertinggal, menu baru yang kelak memakai
    // kode yang sama langsung "mewarisi" akses user lama.
    if ($delete) {
      DB::connection('SML')->update('delete from DBFLMENUWEB where L1 = :kode' , ['kode' => $kode ]);
    }
    // Dulu jumlah baris dikembalikan apa adanya: 0 (kode tidak ada) tampil sebagai peringatan "0".
    return $delete ? 1 : 'Menu ' . $kode . ' tidak ditemukan';
  }

  public function spEdit (Request $req) {
    if (trim((string) $req->Keterangan) === '') {
      return 'Keterangan harus diisi';
    }
    if ($pesan = $this->cekAngka($req)) {
      return $pesan;
    }
    if (strlen(trim((string) $req->Keterangan)) > 500) {
      return 'Keterangan maksimal 500 karakter';
    }

    $edit = DB::connection('SML')->update('update DBMENUWEB set Keterangan = :Keterangan, L0 = :L0, ACCESS = :ACCESS, OL = :OL where KODEMENU = :KODEMENU' , ['KODEMENU' => $req->KODEMENU , 'Keterangan' => trim((string) $req->Keterangan), 'L0' => $req->L0, 'ACCESS' => $req->ACCESS, 'OL' => $req->OL]);

    // Kode menu dikunci di form edit, jadi 0 baris berarti menunya sudah dihapus orang lain.
    return $edit ? 1 : 'Menu ' . $req->KODEMENU . ' tidak ditemukan';
  }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('SELECT * FROM DBMENUWEB where KODEMENU = :KODEMENU' , ['KODEMENU' => $req->KODEMENU]);
    return $detail;
  }

}
