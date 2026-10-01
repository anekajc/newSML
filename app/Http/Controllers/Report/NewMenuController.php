<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\NewMenuReport;
use App\Model\NewAksesMenu;
use App\Model\NewPeriode;
use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;

class NewMenuController extends Controller
{

  public function index () {
    $periode = NewPeriode::where('user_id' , \Auth::id())->first();
    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(5);
    $menul1 = NewMenuReport::where('l0' , 1)->orderBy('KODEMENU')->get();
    $menul2 = NewMenuReport::where('l0' , 2)->orderBy('KODEMENU')->get();
    $menul3 = NewMenuReport::where('l0' , 3)->orderBy('KODEMENU')->get();

    foreach ($menul2 as $menu2) {
      $array = [];
      $kodecheck = $menu2['KODEMENU'];
      foreach ($menul3 as $menu3) {
          // array_push($array, $kodecheck);
          if (substr($menu3['KODEMENU'],0, strlen($kodecheck)) == $kodecheck) {
            array_push($array, $menu3);
          }
      }
      $menu2->child = $array;

    }

    foreach ($menul1 as $menu1) {
      $array = [];
      $kodecheck = $menu1['KODEMENU'];
      foreach ($menul2 as $menu2) {
          // array_push($array, $kodecheck);
          if (substr($menu2['KODEMENU'],0, strlen($kodecheck)) == $kodecheck) {
            array_push($array, $menu2);
          }
      }
      $menu1->child = $array;

    }

    foreach ($menul0 as $menu0) {

      $array = [];
      $kodecheck = $menu0['KODEMENU'];
      foreach ($menul1 as $menu1) {
        if (substr($menu1['KODEMENU'],0,strlen($kodecheck)) == $kodecheck) {
          array_push($array, $menu1);
        }
      }
        $menu0->child = $array;
    }

    $menu = NewMenuReport::orderBy('KODEMENU')->get();

    return view('newmenu' , [
      // "title" => "Home",
      "periode" => $periode,
      "menu" => $menu,
      "menul0" => $menul0,
      // "menul1" => $menul1,
      // "menul2" => $menul2,
    ]);
  }

  public function getMenuL0 () {
    // $menul0 = NewMenuReport::where('HeaderMenu', 1)->where('l0' , 0)->orderBy('KODEMENU')->get();
    // $menul0 = NewMenuReport::where([['HeaderMenu','=', 3],['L0','=', 0]])->orderBy('KODEMENU')->get();
    // $menul1 = NewMenuReport::where([['HeaderMenu','=', 3],['L0','=',1]])->orderBy('KODEMENU')->get();

    // $menul0 = NewMenuReport::where('L0' , 0)->join('DBFLMENUREPORT', 'DBFLMENUREPORT.L1' ,'=' , 'DBMENUREPORT.KODEMENU')->where('DBFLMENUREPORT.USERID' , \Auth::user()->username)->where('DBFLMENUREPORT.ACCESS' , 1)->orderBy('KODEMENU')->get();
    // $menul1 = NewMenuReport::where('L0' , 1)->join('DBFLMENUREPORT', 'DBFLMENUREPORT.L1' ,'=' , 'DBMENUREPORT.KODEMENU')->where('DBFLMENUREPORT.USERID' , \Auth::user()->username)->where('DBFLMENUREPORT.ACCESS' , 1)->orderBy('KODEMENU')->get();
    // return $menul0;

    $menul0 = NewMenuReport::where('L0' , 1)->join('DBFLMENUREPORT', 'DBFLMENUREPORT.L1' ,'=' , 'DBMENUREPORT.KODEMENU')->where('DBFLMENUREPORT.USERID' , \Auth::user()->username)->where('DBFLMENUREPORT.ACCESS' , 1)->where('DBMENUREPORT.HeaderMenu' , 1)->orderBy('KODEMENU')->get();
    $menul1 = NewMenuReport::where('L0' , 2)->join('DBFLMENUREPORT', 'DBFLMENUREPORT.L1' ,'=' , 'DBMENUREPORT.KODEMENU')->where('DBFLMENUREPORT.USERID' , \Auth::user()->username)->where('DBFLMENUREPORT.ACCESS' , 1)->where('DBMENUREPORT.HeaderMenu' , 1)->orderBy('KODEMENU')->get();
    $menul2 = NewMenuReport::where('L0' , 3)->join('DBFLMENUREPORT', 'DBFLMENUREPORT.L1' ,'=' , 'DBMENUREPORT.KODEMENU')->where('DBFLMENUREPORT.USERID' , \Auth::user()->username)->where('DBFLMENUREPORT.ACCESS' , 1)->where('DBMENUREPORT.HeaderMenu' , 1)->orderBy('KODEMENU')->get();
    $menul3 = NewMenuReport::where('L0' , 4)->join('DBFLMENUREPORT', 'DBFLMENUREPORT.L1' ,'=' , 'DBMENUREPORT.KODEMENU')->where('DBFLMENUREPORT.USERID' , \Auth::user()->username)->where('DBFLMENUREPORT.ACCESS' , 1)->where('DBMENUREPORT.HeaderMenu' , 1)->orderBy('KODEMENU')->get();

    // $menul0 = DB::connection("SML")->select("select a.* , b.HASACCESS from DBMENU a join dbflmenu b on a.KODEMENU = b.L1  where b.USERID = 'SA' and b.HASACCESS = 1  and a.L0 =0 and a.HeaderMenu = 3 order by a.KODEMENU");
    // $menul1 = DB::connection("SML")->select("select a.* , b.HASACCESS from DBMENU a join dbflmenu b on a.KODEMENU = b.L1  where b.USERID = 'SA' and b.HASACCESS = 1  and a.L0 =1 and a.HeaderMenu = 3 order by a.KODEMENU");
    //
    // $menul0 = collect($menul0)->map(function($x){ return (array) $x; })->toArray();
    // $menul1 = collect($menul1)->map(function($x){ return (array) $x; })->toArray();

    // foreach ($menul1 as $menu1) {
    //   $array = [];
    //   $kodecheck = $menu1['KODEMENU'];
    //   foreach ($menul2 as $menu2) {
    //       // array_push($array, $kodecheck);
    //       if (substr($menu2['KODEMENU'],0, strlen($kodecheck)) == $kodecheck) {
    //         array_push($array, $menu2);
    //       }
    //   }
    //   $menu1->child = $array;
    //
    // }

    foreach ($menul2 as $menu2) {

      $array2 = [];
      $kodecheck = $menu2['KODEMENU'];
      foreach ($menul3 as $menu3) {
        if (substr($menu3['KODEMENU'],0,strlen($kodecheck)) == $kodecheck) {
          array_push($array2, $menu3);
        }
      }
        $menu2->child = $array2;
    }

    foreach ($menul1 as $menu1) {

      $array1 = [];
      $kodecheck = $menu1['KODEMENU'];
      foreach ($menul2 as $menu2) {
        if (substr($menu2['KODEMENU'],0,strlen($kodecheck)) == $kodecheck) {
          array_push($array1, $menu2);
        }
      }
        $menu1->child = $array1;
    }

    foreach ($menul0 as $menu0) {

      $array = [];
      $kodecheck = $menu0['KODEMENU'];
      foreach ($menul1 as $menu1) {
        if (substr($menu1['KODEMENU'],0,strlen($kodecheck)) == $kodecheck) {
          array_push($array, $menu1);
        }
      }
        $menu0->child = $array;
    }

    return $menul0;
  }

  // ISTAMBAH, ISKOREKSI, ISHAPUS, ISCETAK , ISEXPORT , IsOtorisasi1, IsOtorisasi2 , IsOtorisasi3 , IsOtorisasi4, IsOtorisasi5 ,
  // TIPE, IsBatal, pembatalan

  public function addMenu (Request $req) {
    // return 12345;
      DB::beginTransaction();

      try{
        // return "balik";
        $check = NewMenuReport::where('KODEMENU', $req->input('KODEMENU'))->count();
        // return $check;
        if($check == 0) {
          NewMenuReport::create([
            'KODEMENU'=> $req->input('KODEMENU'),
            'Keterangan'=> $req->input('Keterangan'),
            'L0'=> $req->input('L0') ,
            'ACCESS' => $req->input('ACCESS'),
            'href' => $req->input('href')
          ]);
          $users = NewUsers::all();
          foreach ($users as $u) {
            $access = False;
            if ($u['username'] == 'admin') {
              $access = True;
            }
            NewAksesMenu::create([
              'USERID' => $u->id,
              'L1' => $req->input('KODEMENU'),
              'HASACCESS' => $access
            ]);
          }
          DB::commit();
          return 1;
        } else {
          return 0;
        }

      } catch (\Exception $e) {
        DB::rollback();
      }
  }

  public function editMenu (Request $req) {
    DB::beginTransaction();

    try{

      $check = NewMenuReport::where('KODEMENU', $req->input('KODEMENU'))->count();
      if($check == 0 ) {
        NewMenuReport::where('KODEMENU' , $req->input('kodelama'))->update([
          'KODEMENU'=> $req->input('KODEMENU'),
          'Keterangan'=> $req->input('Keterangan'),
          'L0'=> $req->input('L0') ,
          'ACCESS' => $req->input('ACCESS'),
          'href' => $req->input('href')
        ]);

        NewAksesMenu::where('L1' , $req->input('kodelama'))->update([
        'L1' => $req->input('KODEMENU')
        ]);

        DB::commit();
        return 1;
      } else if (  $req->input('KODEMENU') ==  $req->input('kodelama')) {
        NewMenuReport::where('KODEMENU' , $req->input('kodelama'))->update([
          'Keterangan'=> $req->input('Keterangan'),
          'L0'=> $req->input('L0') ,
          'ACCESS' => $req->input('ACCESS'),
          'href' => $req->input('href')
        ]);
        return 1;
      } else {
        return 0;
      }



    } catch (\Exception $e) {
      DB::rollback();
    }
  }

  public function deleteMenu (Request $req) {
    DB::beginTransaction();

    try {
      NewMenuReport::where('KODEMENU' , $req->input('KODEMENU'))->delete();

      NewAksesMenu::where('L1' , $req->input('KODEMENU'))->delete();

    } catch (\Exception $e) {
      DB::rollback();
    }
  }
}
