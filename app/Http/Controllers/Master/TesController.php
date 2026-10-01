<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;

class TesController extends Controller
{
  public function index () {
    // $menul0 = [];
    $menul0 = NewMenu::where('l0' , 0)->orderBy('KODEMENU')->get();
    $menul1 = NewMenu::where('l0' , 1)->orderBy('KODEMENU')->get();
    $menul2 = NewMenu::where('l0' , 2)->orderBy('KODEMENU')->get();

    // $menu = NewMenu::orderBy('KODEMENU')->get();
    // $menuarray = array();
    // $maxlevel = NewMenu::max('l0');
    // for ($i = 0 ; $i < $maxlevel ; $i++) {
    //   $temparr = [];
    //   foreach ($menu as $m) {
    //     // code...
    //     if ($m['l0'] == $i) {
    //       array_push($temparr, $m);
    //     }
    //   }
    //   array_push($menuarray, $temparr);
    // }
    //
    // $temp1 = [];
    // $temp2 = [];
    // for ($j = count($menuarray) ; $j > 0 ; $j--) {
    //   $temp = [];
    //   if (count($menuarray) - $j - 1 == count($menuarray)) {
    //     $temp1 = $menuarray[$j - 1];
    //   } else {
    //     $temp2 = [];
    //     for ($k = 0 ; $k < count($menuarray[$j - 1]) ; $k++) {
    //       // code...
    //       $tempkodemenu = $menuarray[$j - 1][$k]['KODEMENU'];
    //       $temp = $menuarray[$j - 1][$k];
    //       $array = [];
    //       $asdadasdas = $temp['KODEMENU'];
    //       $tempcount = strlen($asdadasdas);
    //       for ($l = 0 ; $l < count($temp1) ; $l++) {
    //         $testestes = substr($temp1['KODEMENU'] , 0 , $tempcount);
    //         if ($tempkodemenu == $testestes ) {
    //           array_push($array, $temp1[$l]);
    //         }
    //       }
    //       $temp->child = $array;
    //       array_push($temp2,$temp);
    //     }
    //     $temp1 = $temp2;
    //   }
    //
    // }
    // $menul0 = $temp1;
    //
    // ======================

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

    return view('partials.tesnavbar' , [
      "title" => "Home",
      "menul0" => $menul0,
      "menul1" => $menul1,
      "menul2" => $menul2,
    ]);
  }

  public function menu () {
    $menul0 = NewMenu::where('l0' , 0)->orderBy('KODEMENU')->get();
    $menul1 = NewMenu::where('l0' , 1)->orderBy('KODEMENU')->get();
    $menul2 = NewMenu::where('l0' , 2)->orderBy('KODEMENU')->get();

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

    $menu = NewMenu::orderBy('KODEMENU')->get();

    return view('newmenu1' , [
      "title" => "Home",
      "menu" => $menu,
      "menul0" => $menul0,
      "menul1" => $menul1,
      "menul2" => $menul2,
    ]);
  }

  // public function about () {
  //   return view('tesAbout' , [
  //     "title" => "About"
  //   ]);
  // }
  //
  // public function detail($id) {
  //   return view('tesDetail' , [
  //      "title" => "Detail",
  //      "data" => Tes::detail($id),
  //    ]);
  // }
  //
  // public function table() {
  //   $barangjadi = BahanBarang::where('grup', 'BJ')->where('deleted', 0)->paginate(100000);
  //   return view('tesTable' , [
  //      "title" => "Table",
  //      "data" => $barangjadi,
  //    ]);
  // }


  // ISTAMBAH, ISKOREKSI, ISHAPUS, ISCETAK , ISEXPORT , IsOtorisasi1, IsOtorisasi2 , IsOtorisasi3 , IsOtorisasi4, IsOtorisasi5 ,
  // TIPE, IsBatal, pembatalan

  public function addMenu (Request $req) {
      DB::beginTransaction();

      try{
        $check = NewMenu::where('KODEMENU', $req->input('KODEMENU'))->count();
        if($check == 0) {
          NewMenu::create([
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

      $check = NewMenu::where('KODEMENU', $req->input('KODEMENU'))->count();
      if($check == 0 ) {
        NewMenu::where('KODEMENU' , $req->input('kodelama'))->update([
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
        NewMenu::where('KODEMENU' , $req->input('kodelama'))->update([
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
      NewMenu::where('KODEMENU' , $req->input('KODEMENU'))->delete();

      NewAksesMenu::where('L1' , $req->input('KODEMENU'))->delete();

    } catch (\Exception $e) {
      DB::rollback();
    }
  }
}
