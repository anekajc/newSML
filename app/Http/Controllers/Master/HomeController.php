<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Menu;
use App\Model\User;
use App\Model\Periode;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\NewPeriode;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      User::where('id', \Auth::id())->update(['status' => 1, 'hostid' => gethostbyaddr(\Request::ip()), 'ipaddress' => \Request::ip()]);
      $checkUser = User::where('id', \Auth::id())->first();
  		if ($checkUser->level == 3 && $checkUser->username == 'SA') {
        $menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('menu.show_acc', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
      }else{
        $menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('menu.grup','>', 0)->where('menu.show_acc', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
      }
      $periode = Periode::where('id_user', \Auth::id())->first();
      return view("dashboard")->with('menu', $menu)->with('periode', $periode);

    }

    public function newIndex() {
      $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
      // $menul0 = NewMenu::where('l0' , 0)->orderBy('KODEMENU')->get();
      // $menul1 = NewMenu::where('l0' , 1)->orderBy('KODEMENU')->get();
      // $menul2 = NewMenu::where('l0' , 2)->orderBy('KODEMENU')->get();
      //
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
      //
      // foreach ($menul0 as $menu0) {
      //
      //   $array = [];
      //   $kodecheck = $menu0['KODEMENU'];
      //   foreach ($menul1 as $menu1) {
      //     if (substr($menu1['KODEMENU'],0,strlen($kodecheck)) == $kodecheck) {
      //       array_push($array, $menu1);
      //     }
      //   }
      //     $menu0->child = $array;
      // }

      $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0();

      return view('newmaster' , [
        "menul0" => $menul0,
        "periode" => $periode,
      ]);


      // return view('')
    }
}
