<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\NewPeriode;
// use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;

// use App\Http\Controllers\NewMenuController;

class NewSetupPeriodeKerjaController extends Controller
{

  public function index() {


    // $user = DB::connection("SML")->select('select * from DBGUDANG where KODEGDG <> :id', ['id' => 'GTC']);
    // $users = DB::connection("SML")->select('select * from new_users');


    // GlobalController::getPeriode() langsung membaca $periode[0] - user yang belum punya baris
    // DBPERIODE membuat halaman ini 500. Di sini dibaca sendiri dengan cadangan bulan/tahun
    // berjalan (GlobalController tidak diubah karena dipakai modul lain).
    // $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $baris = DB::connection('SML')->select('select top 1 bulan, tahun from dbperiode where user_id = :username', ['username' => \Auth::user()->username]);
    $periode = (object) [
      "bulan" => $baris ? (int) $baris[0]->bulan : (int) date('n'),
      "tahun" => $baris ? $baris[0]->tahun : (int) date('Y'),
    ];


    // $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0();

    return view('berkas.newsetupperiodekerja' , [
      "menul0" =>[],
      "periode" => $periode,
      // "users"=> $users
    ]);
    // $username = \Auth::user()->username;

  }

  public function updatePeriodeKerja (Request $req) {
    $username = \Auth::user()->username;

    if (!ctype_digit((string) $req->bulan) || (int) $req->bulan < 1 || (int) $req->bulan > 12) {
      return ["status" => 0, "pesan" => "Bulan harus 1 sampai 12"];
    }
    if (!preg_match('/^\d{4}$/', (string) $req->tahun)) {
      return ["status" => 0, "pesan" => "Tahun harus diisi 4 angka"];
    }

    $tes = DB::connection("SML")->update('update DBPERIODE set bulan= :bulan , tahun= :tahun where USER_ID= :username' , ['bulan' => $req->bulan , 'tahun' => $req->tahun , 'username' => $username ]);
    $tes2 = DB::connection("SML")->select('select * from DBPERIODE where USER_ID = :username ', ['username' => $username]);
    // return redirect('/home/');
    if (!$tes) {
      // User belum punya baris DBPERIODE (login hanya meng-UPDATE, tidak pernah INSERT) -
      // dibuatkan di sini, format sama dengan baris lain (userid & user_id = username).
      DB::connection("SML")->insert('insert into DBPERIODE (userid, bulan, tahun, user_id) values (:userid, :bulan, :tahun, :username)', ['userid' => $username, 'bulan' => (string) (int) $req->bulan, 'tahun' => (string) $req->tahun, 'username' => $username]);
      $tes = 1;
      $tes2 = DB::connection("SML")->select('select * from DBPERIODE where USER_ID = :username ', ['username' => $username]);
    }
    return ["status" => $tes, "data" => $tes2];
  }




}
