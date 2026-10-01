<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\NewPeriode;
use App\Model\NewUsers;
use App\Model\VwPPL;
use App\Model\DBFLMENU;

// use App\Model\NewMenu;
// use App\Model\NewAksesMenu;
// use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;

class ReportTesController extends Controller
{


  public function index() {
    $kodemenu = '03001';
    $akses = DBFLMENU::where('USERID', \Auth::user()->username)-> where('L1', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    // $listData = DB::connection('SML')->select('SELECT * FROM DBMERK');

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0();

    $outstanding = VwPPL::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->where('IsJasa', 0)->where('pAgen', 0)->groupBy('NoBukti');
    $tempOutstanding = [];
    foreach ($outstanding as $p) {
      // code...
      array_push($tempOutstanding, $p);
    }

    return view('reportTes' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $tempOutstanding,
      "akses" => $akses
    ]);
  }

    public function spReport(Request $req) {
      // sp_ReportBC4B11315 = all
      // sp_ReportBC4B11315GDG = notall
      // $req->input('input3')
      // return 1;

      if ($req->get('input1') == "All") {
          $values = [
            'T',
            'N',
            $req->get('date1'),
            $req->get('date2'),
            '',
            2,
            '',
            ''
          ];
          $res = DB::connection('MGL')->select('exec Sp_ReportPurchasingReqDetClose ?,?,?,?,?,?,?,?',$values);
          return $res;
      } else {
          $values = [
            $req->get('date1'),
            $req->get('date2'),
            0,
            $req->get('input1')
          ];
          $res = DB::connection('MGL')->select('exec sp_ReportBC4B11345GDG ?,?,?,?',$values);
          return $res;
      }


    }

    public function takeDataFormCustomizeTable(Request $req) {
    $headers = []; // Initialize an empty array for headers

    if ($req->get('input1') == "All") {
        $values = [
            'T',
            'N',
            $req->get('date1'),
            $req->get('date2'),
            '',
            2,
            '',
            ''
        ];
        $res = DB::connection('MGL')->select('exec Sp_ReportPurchasingReqDetClose ?,?,?,?,?,?,?,?', $values);
        // Assuming the first row of the result contains column names
        if (!empty($res)) {
            $headers = array_keys((array) $res[0]);
        }
    } else {
        $values = [
            $req->get('date1'),
            $req->get('date2'),
            0,
            $req->get('input1')
        ];
        $res = DB::connection('MGL')->select('exec sp_ReportBC4B11345GDG ?,?,?,?', $values);
        // Assuming the first row of the result contains column names
        if (!empty($res)) {
            $headers = array_keys((array) $res[0]);
        }
    }

    return $headers;
}



}
