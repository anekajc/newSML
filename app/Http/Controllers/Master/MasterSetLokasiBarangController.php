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

class MasterSetLokasiBarangController extends Controller
{

  public function index(Request $req) {



    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select('SELECT * FROM dbVALAS');

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.mastersetlokasibarang' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

    
  }
  public function loadAll () {
    $listData = DB::connection('SML')->select("Declare @IsAktif tinyint
                    set @IsAktif=2

                    Select TOP 100 0 urut,a.NAMABRG, a.KODEBRG, isnull(m1.NAMAMERK,'')NAMAMERK, isnull(a.Mlokasi,'')Mlokasi, isnull(x.KETERANGAN,'')as KetLokasi, a.SAT1, a.SAT2, a.ISI2, a.SAT3, a.ISI3, isnull(m1.namaMerk,'')namaMerk

                    from dbBarang a
                    Left Outer join dbGroup b on b.Kodegrp=a.kodegrp

                    Left Outer Join DbMLokasi x on x.KodeLokasi=a.MLokasi
                    left outer join dbmerk m1 on a.kodemerk=m1.kodemerk
                    where (a.IsAktif=@IsAktif and @IsAktif<>2) or (@IsAktif=2 and a.KodeBrg like '%')and a.IsBarang=3
                    and ISAKTIF=1  
                    Order by Urut,a.Kodebrg
                                                ");
    return $listData;
  }

  public function spAdd (Request $req) {

    $Choice = "I";

    $values = [
      $Choice,
      $req->kode,
      $req->nama,
      '',
      ''
    ];
    $res = DB::connection('SML')->select('exec sp_MLokasi ?,?,?,?,?',$values);
    return response()->json(['success' => true, 'message' => 'Data inserted successfully']);
  }

  public function spDelete (Request $req) {
    $Choice = "D";

    $values = [
      $Choice,
      $req->kode,
      $req->nama,
      $req->kode,
      ''
    ];
    $res = DB::connection('SML')->select('exec sp_MLokasi ?,?,?,?,?',$values);
    return $res;
    }

  public function spEdit (Request $req) {
    $edit = DB::connection('SML')->update("
    update dbbarang set Mlokasi= :lokasiBarang where kodebrg= :kodeBarang", ['kodeBarang' => $req->kodeBarang , 'lokasiBarang' => $req->lokasiBarang]);

    $edit2 = DB::connection('SML')->update("insert into DBHISLOKASIBRG (KODEBRG,LOKASI,TGL,IDUSER) values (:kodeBarang,:lokasiBarang,GetDate(),:user)", ['kodeBarang' => $req->kodeBarang , 'lokasiBarang' => $req->lokasiBarang, 'user'=>\Auth::User()->username]);

    return $edit;
  }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('SELECT * FROM dbMlokasi where KODELOKASI = :kode' , ['kode' => $req->kode]);
    return $detail;
  }


public function loadHistory (Request $req) {
    $listData = DB::connection('SML')->select("select lokasi,tgl,iduser   from DBHISLOKASIBRG where KODEBRG= :kodeBarang",[ 'kodeBarang'=> $req->kodeBarang]);
    return $listData;
  }

  public function loadLokasiBarang (Request $req) {
    $listData = DB::connection('SML')->select("select KODELOKASI From DbMLokasi");
    return $listData;
  }

}
