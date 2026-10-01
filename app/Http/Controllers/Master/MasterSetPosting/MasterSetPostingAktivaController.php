<?php

namespace App\Http\Controllers\Master\MasterSetPosting;


use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
// use App\Model\VWPerkiraan;

// use App\Http\Controllers\NewMenuController;

class MasterSetPostingAktivaController extends Controller
{

  public function index(Request $req) {
    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select('SELECT a.Perkiraan, b.keterangan from dbposthutpiut a
                       left outer join dbperkiraan b on b.perkiraan=a.perkiraan
                       where a.Kode=\'AKV\' order by a.Perkiraan');

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master/masterSetPosting/mastersetpostingaktiva' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "listData" => $listData
    ]);

  }

  public function loadAll () {
    $listData = DB::connection('SML')->select("SELECT a.*,b.Keterangan, b.Kelompok, b.Tipe, b.Valas, b.DK, b.Neraca, b.Simbol, b.IsPPN, b.Lokasi, b.iskirim,a.Persen, b.IsAktif  from dbposthutpiut a
                           left outer join dbperkiraan b on b.perkiraan=a.perkiraan
                           WHERE a.Kode='AKV' order by a.Perkiraan");
    return $listData;
  }

  public function spAdd(Request $req)
  {

      $listData = DB::connection('SML')->update("INSERT INTO DBPOSTHUTPIUT (Perkiraan, Kode, Tipe, Persen, IsBeliJual, IsLokalorExim, IsUM, Akumulasi, Biaya1, Biaya2, PersenBiaya1, PersenBiaya2) VALUES (:perkiraan,  'AKV', :metodePenyusutan, :persenSusut, 0, 0, :uangMuka, :akm, :bp1, :bp2, :persenbp1,:persenbp2)", 
      ['perkiraan' => $req->perkiraan, 'uangMuka' => $req->uangMuka,'akm' => $req->akm, 'bp1' => $req->bp1, 'persenbp1' => $req->persenbp1, 'bp2' => $req->bp2, 'persenbp2' => $req->persenbp2, 'persenSusut' => $req->persenSusut, 'metodePenyusutan' => $req->metodePenyusutan ]);

      return 1;
  }

  public function spEdit(Request $req)
  {
      $listData = DB::connection('SML')->update("UPDATE DBPOSTHUTPIUT SET 
      Perkiraan = :perkiraan, 
      IsUm = :uangMuka, 
      Persen = :persenSusut, 
      Tipe = :metodePenyusutan, 
      Akumulasi = :akm, 
      Biaya1 = :bp1, 
      Biaya2 = :bp2,
      PersenBiaya1 = :persenbp1, 
      PersenBiaya2 = :persenbp2  where Perkiraan = :kodeLama and Kode = 'AKV' ", 
      ['perkiraan' => $req->perkiraan, 
      'kodeLama' => $req->kodeLama, 
      'uangMuka' => $req->uangMuka,
      'akm' => $req->akm,
      'bp1' => $req->bp1, 
      'persenbp1' => $req->persenbp1, 
      'bp2' => $req->bp2, 
      'persenbp2' => $req->persenbp2, 
      'persenSusut' => $req->persenSusut, 
      'metodePenyusutan' => $req->metodePenyusutan ]);

      return 1;
  }

  public function spDelete (Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM DBPPL where KDDep = :kode' , ['kode' => $req->kode]);

    if ($check) {
      return 'Dept digunakkan di Pembelian';
    }

    //$check = DB::connection('SML')->select('SELECT * FROM DBDEPARTEMEN where KodeDepartemen = :kode' , ['kode' => $req->kode]);
    //if ($check) {
      //return 'ga bisa hapus';
    //}
    $delete = DB::connection('SML')->update('delete from DBPOSTHUTPIUT where Perkiraan = :kode' , ['kode' => $req->kode ]);
    return $delete;
  }
  
  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select("SELECT a.*,b.Keterangan, b.Kelompok, b.Tipe, b.Valas, b.DK, b.Neraca, b.Simbol, b.IsPPN, b.Lokasi, b.iskirim, a.Persen, b.IsAktif  from dbposthutpiut a
                           left outer join dbperkiraan b on b.perkiraan=a.perkiraan
                           WHERE a.Kode='AKV' and a.Perkiraan = :kode order by a.Perkiraan " , ['kode' => $req->kode]);
    return $detail;
  }

  public function loadPerkiraanPenyusutan () {
    $listData = DB::connection('SML')->select('SELECT Perkiraan, Keterangan FROM dbperkiraan');
    return $listData;
  }

    public function loadPerkiraan () {
    $listData = DB::connection('SML')->select('SELECT Perkiraan,Keterangan FROM dbPerkiraan WHERE tipe=1 AND
                       perkiraan not in (select Perkiraan FROM dbposthutpiut) order by Perkiraan');
    return $listData;
  }

    public function loadAkumulasi () {
    $listData = DB::connection('SML')->select("SELECT a.Perkiraan,b.keterangan from dbposthutpiut a
                           left outer join dbperkiraan b on b.perkiraan=a.perkiraan
                           WHERE a.Kode='AKM' order by a.Perkiraan");
    return $listData;
  }

}
