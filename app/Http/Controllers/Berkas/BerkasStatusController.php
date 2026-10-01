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

class BerkasStatusController extends Controller
{

  public function index(Request $req) {

    // Daftar user diisi lewat AJAX loadAll() dan menu samping layout newmasterTest diambil lewat
    // AJAX /getmenu, jadi dua query ini tidak dipakai halaman - dimatikan supaya lebih ringan.
    // $users = DB::connection("SML")->select('select * from DBFLPASS');
    // $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(1);

    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();

    return view('berkas.berkasstatus' , [
      "menul0" => [],
      "periode" => $periode,
    ]);

  }

  public function loadAll () {
    $users = DB::connection("SML")->select('select USERID, FullName, STATUS from DBFLPASS order by USERID');
    return $users;

  }

  public function spEdit (Request $req) {
    if (!$req->kode) {
      return 'User ID kosong';
    }
    if ((string) $req->status !== '0' && (string) $req->status !== '1') {
      return 'Status tidak dikenal';
    }

    $edit = DB::connection('SML')->update('update DBFLPASS set STATUS = :status where USERID = :kode' , ['kode' => $req->kode , 'status' => $req->status]);

    // Dulu jumlah baris dikembalikan apa adanya, jadi 0 (user tidak ada) tampil sebagai "0".
    return $edit ? 1 : 'User ' . $req->kode . ' tidak ditemukan';
  }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('SELECT USERID, FullName, STATUS FROM DBFLPASS where USERID = :kode' , ['kode' => $req->kode]);
    return $detail;
  }

}
