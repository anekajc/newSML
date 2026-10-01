<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\User;
use App\Models\Periode;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\NewPeriode;
use Illuminate\Support\Facades\DB;

use App\Traits\AksesTrait;

class HomeController extends Controller
{

  use AksesTrait;
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

      // $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0();

      return view('newmaster' , [
        "menul0" => [],
        "periode" => $periode,
      ]);

    }

      public function accountingIndex() {
      $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();

      $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(5);

      return view('accounting.newmaster' , [
        "menul0" => $menul0,
        "periode" => $periode,
      ]);

    }

    public function marketingIndex() {
      $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();

      $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(4);

      return view('marketing.newmaster' , [
        "menul0" => $menul0,
        "periode" => $periode,
      ]);

    }

    public function gudangIndex() {
      $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();

      $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(6);

      return view('gudang.newmaster' , [
        "menul0" => $menul0,
        "periode" => $periode,
      ]);

    }

    public function purchasingIndex() {
      $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();

      $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(3);

      return view('purchasing.newmaster' , [
        "menul0" => $menul0,
        "periode" => $periode,
      ]);


    }

     public function masterIndex() {
      $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();

      $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

      return view('master.newmaster' , [
        "menul0" => $menul0,
        "periode" => $periode,
      ]);

    }

     public function reportIndex() {
      $akses = $this->cekAkses("Home");
      if ($akses['userLoggedOut']) { return redirect('/'); }

      $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();

      $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(5);

      return view('report.mastercore' , [
        "menul0" => $menul0,
        "periode" => $periode,
        "akses" => $akses
      ]);

    }

     public function berkasIndex() {
      $akses = $this->cekAkses("Home");
      if ($akses['userLoggedOut']) { return redirect('/'); }

      $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();

      $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(1);

      return view('berkas.newmaster' , [
        "menul0" => $menul0,
        "periode" => $periode,
        "akses" => $akses
      ]);

    }

 public function GetMenu($headermenu)
{
    $tempAngka = 0;
    if ($headermenu == 2) {
        $tempAngka += 1;
    }

    $menul0 = NewMenu::where('L0', $tempAngka)
        ->join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')
        ->where('DBFLMENUWEB.USERID', \Auth::user()->username)
        ->where('DBFLMENUWEB.HASACCESS', 1)
        ->whereNotNull('DBMENUWEB.HeaderMenu')
        ->orderBy('KODEMENU')
        ->get();
    $tempAngka += 1;

    $menul1 = NewMenu::where('L0', $tempAngka)
        ->join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')
        ->where('DBFLMENUWEB.USERID', \Auth::user()->username)
        ->where('DBFLMENUWEB.HASACCESS', 1)
        ->whereNotNull('DBMENUWEB.HeaderMenu')
        ->orderBy('KODEMENU')
        ->get();
    $tempAngka += 1;

    $menul2 = NewMenu::where('L0', 2)
        ->join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')
        ->where('DBFLMENUWEB.USERID', \Auth::user()->username)
        ->where('DBFLMENUWEB.HASACCESS', 1)
        ->whereNotNull('DBMENUWEB.HeaderMenu')
        ->orderBy('KODEMENU')
        ->get();

    foreach ($menul1 as $menu1) {
        $array1 = [];
        $kodecheck = $menu1['KODEMENU'];
        foreach ($menul2 as $menu2) {
            if (substr($menu2['KODEMENU'], 0, strlen($kodecheck)) == $kodecheck) {
                array_push($array1, $menu2);
            }
        }
        $menu1->child = $array1;
    }

    foreach ($menul0 as $menu0) {
        $array = [];
        $kodecheck = $menu0['KODEMENU'];
        foreach ($menul1 as $menu1) {
            if (substr($menu1['KODEMENU'], 0, strlen($kodecheck)) == $kodecheck) {
                array_push($array, $menu1);
            }
        }
        $menu0->child = $array;
    }

    return response()->json($menul0);
}

public function getMenuReport($headermenu = 1)
{
    $rows = DB::connection('SML')->table('dbmenureportweb')
        ->orderBy('KODEMENU')
        ->get();

    return response()->json($this->buildReportTree($rows, null, 0));
}

// Same tree as getMenuReport(), minus the HTTP JSON wrapper -- for
// server-side consumers (AksesTrait::cekAkses()) that need the plain
// array/recursion result directly instead of an endpoint response.
public function getReportMenuTreeArray()
{
    $rows = DB::connection('SML')->table('dbmenureportweb')
        ->orderBy('KODEMENU')
        ->get();

    return $this->buildReportTree($rows, null, 0);
}

private function buildReportTree($rows, $parentKode, $depth)
{
    // Hard depth guard — same safety net you already need after the
    // circular-recursion / memory-exhaustion issue on the main menu tree.
    if ($depth > 10) {
        return [];
    }

    $candidates = $rows->filter(function ($row) use ($parentKode) {
        if ($parentKode === null) {
            return $row->L0 == 1;
        }
        return $row->KODEMENU !== $parentKode
            && str_starts_with($row->KODEMENU, $parentKode);
    });

    // Keep only the closest descendants — drop a candidate if another
    // candidate is a longer/closer ancestor of it, so we don't attach
    // grandchildren directly under this node.
    $result = [];
    foreach ($candidates as $row) {
        $hasCloserParent = $candidates->contains(function ($other) use ($row, $parentKode) {
            return $other->KODEMENU !== $row->KODEMENU
                && str_starts_with($row->KODEMENU, $other->KODEMENU)
                && strlen($other->KODEMENU) > strlen($parentKode ?? '');
        });

        if ($hasCloserParent) continue;

        $result[] = [
            'KODEMENU'   => $row->KODEMENU,
            'Keterangan' => $row->Keterangan,
            'href'       => $row->href,
            'ACCESS'     => $row->ACCESS,
            'icon'       => $row->icon ?? null,
            'color'      => $row->color ?? null,
            'child'      => $this->buildReportTree($rows, $row->KODEMENU, $depth + 1),
        ];
    }

    return $result;
}

}
