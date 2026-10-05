<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\NewPeriode;
// use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;



// use App\Http\Controllers\NewMenuController;

class NewSetupPeriodeKerjaController extends Controller
{

  public function index() {


    // $user = DB::connection("SML")->select('select * from DBGUDANG where KODEGDG <> :id', ['id' => 'GTC']);
    // $users = DB::connection("SML")->select('select * from new_users');


    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();


    // $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0();

    return view('newsetupperiodekerja' , [
      "menul0" =>[],
      "periode" => $periode,
      // "users"=> $users
    ]);
    // $username = \Auth::user()->username;

  }

  public function updatePeriodeKerja (Request $req) {
    $username = \Auth::user()->username;

    $tes = DB::connection("SML")->update('update DBPERIODE set bulan= :bulan , tahun= :tahun where USER_ID= :username' , ['bulan' => $req->bulan , 'tahun' => $req->tahun , 'username' => $username ]);
    $tes2 = DB::connection("SML")->select('select * from DBPERIODE where USER_ID = :username ', ['username' => $username]);
    // return redirect('/home/');
    return ["status" => $tes, "data" => $tes2];
  }




}
