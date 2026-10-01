<?php

namespace App\Http\Controllers\Master;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
// use App\Model\VWPerkiraan;

// use App\Http\Controllers\NewMenuController;

class MasterLabaRugiController extends Controller
{

  public function index(Request $req) {



    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select("Select a.*,b.Keterangan NamaPerkiraan
from dbLRHPP a
     left outer join dbPerkiraan b on b.Perkiraan=a.perkiraan
     left outer join dbdevisi c on c.devisi=a.devisi
where a.Devisi='01' and a.IsLRHpp='0' and
      Bulan='08' and Tahun='2025'");

    
    $listDataDevisi = DB::connection('SML')->select('SELECT Devisi, NamaDevisi FROM DBDEVISI');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.masterlabarugi' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData,
      "listDataDevisi" => $listDataDevisi
    ]);

  }

  public function loadAll (Request $req) {
    
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();

    $listData = DB::connection('SML')->select("Select a.*,b.Keterangan NamaPerkiraan
      from dbLRHPP a
      left outer join dbPerkiraan b on b.Perkiraan=a.perkiraan
      left outer join dbdevisi c on c.devisi=a.devisi
      where a.Devisi= :filterDevisi and a.IsLRHpp=:filterLaporan and
      Bulan= :bulan and Tahun= :tahun",['filterDevisi'=> $req->filterDevisi, 'filterLaporan'=>$req->filterLaporan, 'bulan'=>$periode->bulan, 'tahun' => $periode->tahun]);
    return $listData;
  }

  public function spAdd (Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM DBLRHPP where Nomor = :nomor' , ['nomor' => $req->nomor]);

    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();

    if ($check) {
      return 'Nomor Laba Rugi sudah ada di database';
    }

    $listData = DB::connection('SML')->update("
    insert into dbLRHPP (Devisi, Nomor, Perkiraan, Keterangan, Grup, Tipe, Tanda, Jumlah, Persen, Tampil,bulan, Tahun,IsLRHPP)
    values (:devisi, :nomor, :perkiraan, :keterangan, :group, :tipe, :tanda, :jumlah, :persentasi, :tampil, :bulan, :tahun,:islrhpp)" , 
      [
      'devisi' => $req->devisi, 
      'nomor' => $req->nomor,
      'perkiraan' => $req->perkiraan, 
      'keterangan' => $req->keterangan, 
      'group' => $req->group, 
      'tipe' => $req->tipe, 
      'tanda' => $req->tanda, 
      'jumlah' => $req->jumlah, 
      'persentasi' => $req->persentasi, 
      'tampil' => $req->tampil,
      'bulan' => $periode->bulan, 
      'tahun' => $periode->tahun, 
      'islrhpp' => '0',  
      ]);
    return $listData;

  }

  public function spDelete (Request $req) {

    $delete = DB::connection('SML')->update('delete from DBLRHPP where Nomor = :nomor' , ['nomor' => $req->nomor ]);
    return $delete;
  }

  public function spEdit (Request $req) {

    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();


    $listData = DB::connection('SML')->update("
     update dbLRHPP 
     set Perkiraan= :perkiraan, Keterangan= :keterangan, Grup=:group, Tipe=:tipe, Tanda=:tanda, Jumlah= :jumlah, 
      Persen=:persentasi, Tampil= :tampil
      where Devisi= :devisi and Nomor= :nomor and bulan= :bulan and tahun= :tahun and IsLRHPP= :islrhpp" , 
      [
      'devisi' => $req->devisi, 
      'nomor' => $req->nomor,
      'perkiraan' => $req->perkiraan, 
      'keterangan' => $req->keterangan, 
      'group' => $req->group, 
      'tipe' => $req->tipe, 
      'tanda' => $req->tanda, 
      'jumlah' => $req->jumlah, 
      'persentasi' => $req->persentasi, 
      'tampil' => $req->tampil,
      'bulan' => $periode->bulan, 
      'tahun' => $periode->tahun, 
      'islrhpp' => '0',  
      ]);
    return $listData;

  }

  public function loadEdit (Request $req) {
    
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();

    $listData = DB::connection('SML')->select("Select a.*,b.Keterangan NamaPerkiraan
      from dbLRHPP a
      left outer join dbPerkiraan b on b.Perkiraan=a.perkiraan
      left outer join dbdevisi c on c.devisi=a.devisi
      where a.Devisi= :filterDevisi and a.IsLRHpp=:filterLaporan and
      Bulan= :bulan and Tahun= :tahun and Nomor = :nomor",['filterDevisi'=> $req->filterDevisi, 'filterLaporan'=>$req->filterLaporan, 'bulan'=>$periode->bulan, 'tahun' => $periode->tahun, 'nomor' => $req->nomor]);
    return $listData;
  }

  public function loadPerkiraan (Request $req) {
  
  $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();

  $listData = DB::connection('SML')->select("
  SELECT Perkiraan,Keterangan,Tipe from dbPerkiraan where Kelompok >= 3  
  and Perkiraan not in (select Perkiraan from DBLRHPP where Bulan = :bulan and Tahun = :tahun) 
  order by Perkiraan",
  ['bulan'=>$periode->bulan, 'tahun' => $periode->tahun]);
  return $listData;
  }


}
