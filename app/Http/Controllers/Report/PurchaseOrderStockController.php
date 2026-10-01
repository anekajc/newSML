<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\DBFLMENU;
use App\Model\NewPeriode;
use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Model\VwPPL;



// use App\Http\Controllers\NewMenuController;

class PurchaseOrderStockController extends Controller
{

  public function index() {
    $kodemenu = '030011';
    $akses = DBFLMENU::where('USERID', \Auth::user()->username)-> where('L1', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }


    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    // $listData = DB::connection('SML')->select('SELECT * FROM DBMERK');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0();


    $outstanding = VwPPL::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->where('IsJasa', 0)->where('pAgen', 1)->groupBy('NoBukti');
    $tempOutstanding = [];
    foreach ($outstanding as $p) {
      // code...
      array_push($tempOutstanding, $p);
    }

    return view('purchaseorderstock' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $tempOutstanding,
      "akses" => $akses
    ]);

  }

  public function loadAll () {

    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();

    $outstanding = VwPPL::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->where('IsJasa', 0)->where('pAgen', 1)->groupBy('NoBukti');
    $tempOutstanding = [];
    foreach ($outstanding as $p) {
      // code...
      array_push($tempOutstanding, $p);
    }

    return [
      "listData" => $tempOutstanding
    ];
  }

  public function spDetail (Request $req) {
    $detailOutstanding = VwPPL::all()->where('NoBukti', $req->nobukti )->sortBy('Urut');
    $tempOutstanding = [];
    foreach ($detailOutstanding as $do) {
      // code...
      array_push($tempOutstanding,$do);
    }
    return $tempOutstanding;
  }

  public function temp () {
    // $penerimaan = VWRSPBWEB::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NOBUKTI');
    // $tempPenerimaan = [];
    // foreach ($penerimaan as $p) {
    //   // code...
    //   array_push($tempPenerimaan, $p);
    // }
    //
    // $detailOutstanding = VwOutSPBRSPB::all()->where('NOBUKTI', $req->input('NOBUKTI'))->sortBy('URUT');
    // $tempOutstanding = [];
    // foreach ($detailOutstanding as $do) {
    //   // code...
    //   array_push($tempOutstanding,$do);
    // }
    // return $tempOutstanding;
    //
    // $tempPenerimaan = VWRSPBWEB::where('NOBUKTI' , $req->input('norspb'))->select('KODEBRG')->get()->toArray();
    //
    // $tempAddList = VwOutSPBRSPB::select()->where('NOBUKTI', $req->input('nosj'))->whereNotIn('KODEBRG', $tempPenerimaan)->get();
    //
    // return $tempAddList;
  }

  // public function spNobukti (Request $req) {
  //   $inisial = DB::connection('SML')->select("SELECT PPL FROM DBNOMOR");
  // }

  public function getNoBukti (Request $req) {
    // $values = [
    //   'a'
    // ];
    // return 'tes';
    // $po = DB::connection("SML")->select('exec sp_outstanding_po ?',$values);
    // $periode = NewPeriode::where('user_id' , \Auth::id())->first();
    $username = \Auth::user()->username;
    $periode = DB::connection("SML")->select('select TOP 1 * from DBPERIODE where user_id = :username ' , ["username" => $username]);
    $inisial = DB::connection("SML")->select('select PPLA from DBNOMOR');
    // $inisial = DB::connection("SML")->select('select SPR from DBNOMOR');
    // return [$periode->bulan,$inisial[0]->PBL,$username];
    $values = [
        $inisial[0]->PPLA,
        $periode[0]->bulan,
        $periode[0]->tahun,
        $username,
        // $periode
        // $periode
    ];
    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
    return $noBukti;
  }

  public function listBarang (Request $req) {
    // $harga = DB::connection('SML')->select("select * from dbHARGAJUAL where KODEBRG = :kodebarang" , ['kodebarang' => $req->kodebarang]);
//     select b.NAMAMERK ,  a.* from dbbarang a
// join DBMERK b on a.KodeMerk = b.KODEMERK
//  where a.KODEGRP = 'BJ' and a.pAgen = 1
    $listData = DB::connection('SML')->select("select b.NAMAMERK ,  a.* from dbbarang a join DBMERK b on a.KodeMerk = b.KODEMERK where a.KODEGRP = 'BJ' and a.pAgen = :isagen" , ["isagen" => $req->isagen]);
    return $listData;
  }

  public function spAdd (Request $req) {

    $check = DB::connection('SML')->select('select * from dbppl where Nobukti = :nobukti',["nobukti" => $req->nobukti]);
      if ($check) {
        return 2;
      }

    $listData = $req->listData;
    foreach ($listData as $d) {
      // code...
      $values = [
        $req->choice,
        $req->nobukti,
        $req->nourut,
        $req->tanggal,
        $d['urut'],
        $d['kodebarang'],
        $d['qnt'],
        $d['nosat'],
        $d['satuan'],
        $d['isi'],
        $d['keterangan'],
        $d['isclose'],
        $d['isclosed'],
        $d['kddep'],
        $d['keterangannama'],
        $req->isjasa,
        $d['noso'],
        $d['urutso'],
        $req->pagen,
        $d['nopocust'],
        $d['jmlrecord'],
        $req->pjasa

      ];
      DB::connection('SML')->statement('exec sp_PPL ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);
    }
    return 1;
    // foreach ($penerimaan as $p) {
    //   // code...
    //   array_push($tempPenerimaan, $p);
    // }
    //
    // DB::connection('SML')->statement('exec sp_RSPB ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?' ,$values);
    // return 1;
  }


  public function spDelete (Request $req) {

    // $listData = $req->listData;
    // foreach ($listData as $d) {
      // code...
      $values = [
        $req->choice,
        $req->nobukti,
        $req->nourut,
        $req->tanggal,
        $req->urut,
        $req->kodebarang,
        $req->qnt,
        $req->nosat,
        $req->satuan,
        $req->isi,
        $req->keterangan,
        $req->isclose,
        $req->isclosed,
        $req->kddep,
        $req->keterangannama,
        $req->isjasa,
        $req->noso,
        $req->urutso,
        $req->pagen,
        $req->nopocust,
        $req->jmlrecord,
        $req->pjasa

      ];
      DB::connection('SML')->statement('exec sp_PPL ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);
    // }
    return 1;
    // foreach ($penerimaan as $p) {
    //   // code...
    //   array_push($tempPenerimaan, $p);
    // }
    //
    // DB::connection('SML')->statement('exec sp_RSPB ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?' ,$values);
    // return 1;
  }

}
