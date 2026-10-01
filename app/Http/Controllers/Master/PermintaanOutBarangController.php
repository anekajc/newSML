<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use App\Traits\ActivityTrait;
use App\Model\GudangSQL;
use App\Model\RakSQL;
use App\Model\LokasiSQL;
use App\Model\OutBrg;
use App\Model\OutBrgDet;
use App\Model\vwOutBarang;
use App\Model\vwShowOutBrg;
use App\Model\vwShowPilihBarang;
use App\Model\vwShowpilihNoTransaksi;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\NewPeriode;
use App\Model\NewUsers;

class PermintaanOutBarangController extends Controller {
  use ActivityTrait;

	public function index() {
    $tipe = 'permintaanpersiapan';
		// $getID = Menu::where('access', '/permintaanpersiapan')->first();
		// $check = AksesMenu::where('id_user', \Auth::id())->where('id_menu', $getID->id)->first();
		// if ($check->tampil == 1) {
		// 	User::where('id', \Auth::id())->update(['status' => 1, 'hostid' => gethostbyaddr(\Request::ip()), 'ipaddress' => \Request::ip()]);
    //   $checkUser = User::where('id', \Auth::id())->first();
  	// 	if ($checkUser->level == 3 && $checkUser->username == 'SA') {
    //     $menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('menu.show_acc', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
    //   }else{
    //     $menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('menu.grup','>', 0)->where('menu.show_acc', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
    //   }

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

      $periode = NewPeriode::where('user_id', \Auth::id())->first();
      $gudang = GudangSQL::All();
      $outtransaksi = vwOutBarang::
          where('Tipe', '<>', 'SO')->
          whereMonth('TANGGAL', '=', $periode->bulan)->whereYear('TANGGAL', '=', $periode->tahun)->
          orderBy('TANGGAL', 'DESC')->selectRaw('vwOutBarang.*')->get();
      $persiapanppbrg = vwShowOutBrg::whereMonth('TANGGAL', '=', $periode->bulan)->whereYear('TANGGAL', '=', $periode->tahun)->
          orderBy('NOBUKTI', 'DESC')->selectRaw('vwShowOutBrg.*')->get();
      return view("permintaanoutbarang")
      ->with('menul0', $menul0)
      ->with('periode', $periode)
      // ->with('akses', $check)
      ->with('outtransaksi', $outtransaksi)
      ->with('gudang', $gudang)
      ->with('tipe', $tipe)
      ->with('persiapanppbrg', $persiapanppbrg);
		// } else {
		// 	return redirect('/home');
		// }
	}

  public function indexso() {
    $tipe = 'permintaanpersiapanso';
    // $getID = Menu::where('access', '/permintaanpersiapan')->first();
    // $check = AksesMenu::where('id_user', \Auth::id())->where('id_menu', $getID->id)->first();
    // if ($check->tampil == 1) {
    // 	User::where('id', \Auth::id())->update(['status' => 1, 'hostid' => gethostbyaddr(\Request::ip()), 'ipaddress' => \Request::ip()]);
    //   $checkUser = User::where('id', \Auth::id())->first();
    // 	if ($checkUser->level == 3 && $checkUser->username == 'SA') {
    //     $menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('menu.show_acc', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
    //   }else{
    //     $menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('menu.grup','>', 0)->where('menu.show_acc', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
    //   }

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

      $periode = NewPeriode::where('user_id', \Auth::id())->first();
      $gudang = GudangSQL::All();
      $outtransaksi = vwOutBarang::
          where('Tipe', '=', 'SO')->
          whereMonth('TANGGAL', '=', $periode->bulan)->whereYear('TANGGAL', '=', $periode->tahun)->
          orderBy('TANGGAL', 'DESC')->selectRaw('vwOutBarang.*')->get();
      $persiapanppbrg = vwShowOutBrg::whereMonth('TANGGAL', '=', $periode->bulan)->whereYear('TANGGAL', '=', $periode->tahun)->
          orderBy('NOBUKTI', 'DESC')->selectRaw('vwShowOutBrg.*')->get();
      return view("permintaanoutbarang")
      ->with('menul0', $menul0)
      ->with('periode', $periode)
      // ->with('akses', $check)
      ->with('outtransaksi', $outtransaksi)
      ->with('gudang', $gudang)
      ->with('tipe', $tipe)
      ->with('persiapanppbrg', $persiapanppbrg);
    // } else {
    // 	return redirect('/home');
    // }
  }

	public function loadAll() {
		$periode = NewPeriode::where('user_id', \Auth::id())->first();
    return vwShowOutBrg::whereMonth('TANGGAL', '=', $periode->bulan)->whereYear('TANGGAL', '=', $periode->tahun)->
        orderBy('NOBUKTI', 'DESC')->selectRaw('vwShowOutBrg.*')->get();

	}

	public function loadAllOutTransaksi() {
		$periode = NewPeriode::where('user_id', \Auth::id())->first();
		return vwOutBarang::whereMonth('TANGGAL', '=', $periode->bulan)->whereYear('TANGGAL', '=', $periode->tahun)->
        orderBy('TANGGAL', 'DESC')->selectRaw('vwOutBarang.*')->get();
  }

	public function generateNomorBukti(Request $req) {
    $periode = NewPeriode::where('user_id', \Auth::id())->first();
    $getUser = NewUsers::where('id', \Auth::id())->first();
    $inisial = DB::connection("sqlsrv")->select('select * from DBNOMOR');
    if ($req->input('tipe') == 'permintaanpersiapanso'){
      $values = [
        $inisial[0]->OB,
        $periode->bulan,
        $periode->tahun,
        $getUser->username
      ];
      $nomor = DB::connection("sqlsrv")->select('exec SP_IsiNobukti ?,?,?,?',$values);

    }
    if ($req->input('tipe') == 'permintaanpersiapan'){
      $values = [
        $inisial[0]->OBN,
        $periode->bulan,
        $periode->tahun,
        $getUser->username
      ];
      $nomor = DB::connection("sqlsrv")->select('exec SP_IsiNobukti ?,?,?,?',$values);

    }
		return $nomor[0]->Nobukti;
	}

	public function generateNomorUrut(Request $req) {
    $periode = NewPeriode::where('user_id', \Auth::id())->first();
    $getUser = NewUsers::where('id', \Auth::id())->first();
    $inisial = DB::connection("sqlsrv")->select('select * from DBNOMOR');
    if ($req->input('tipe') == 'permintaanpersiapanso'){
      $values = [
        $inisial[0]->OB,
        $periode->bulan,
        $periode->tahun,
        $getUser->username
      ];
      $nomor = DB::connection("sqlsrv")->select('exec SP_IsiNobukti ?,?,?,?',$values);

    }
    if ($req->input('tipe') == 'permintaanpersiapan'){
      $values = [
        $inisial[0]->OBN,
        $periode->bulan,
        $periode->tahun,
        $getUser->username
      ];
      $nomor = DB::connection("sqlsrv")->select('exec SP_IsiNobukti ?,?,?,?',$values);

    }

		return $nomor[0]->Nourut;
	}

	public function add(Request $req) {
    $getUser = NewUsers::where('id', \Auth::id())->first();
    if ($req->input('choice')=='I'){
  		$count = OutBrg::where('NOBUKTI', $req->input('no_bukti'))->count();
  		if ($count == 0) {
  			$detail = $req->input('ppbrg');
  			for ($i = 0; $i < count($detail); $i++) {
          $getTransaksi = vwOutBarang::where('NoBukti', $detail[$i][0])->where('Kodebrg', $detail[$i][1])->where('satuan', $detail[$i][8])->first();
          if ($getTransaksi->SisaOut < $detail[$i][5]) {
            return $detail[$i][1];
          }
          $values = [
            $req->input('choice'),
            $req->input('no_bukti'),
            $req->input('no_urut'),
            $req->input('tanggal'),
            $req->input('gudang'),
            $req->input('rak'),
            $req->input('lokasi'),
            $req->input('keterangan'),
            $i,//urutdet
            $detail[$i][1],
            $detail[$i][2],
            $detail[$i][5],
            $detail[$i][12],
            $detail[$i][8],
            $detail[$i][9],
            $detail[$i][0],
            $detail[$i][10],
            $getUser->name,
            now(),
            $req->input('barcode')
          ];
          DB::connection("sqlsrv")->statement('exec Sp_OutBrg ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?',$values);
  			}

  			// $this->logActivity('ADD', \Auth::id(), $req->input('no_bukti'));
  			return "1;;1;;".$req->input('no_bukti');
  		} else {
  			$detail = $req->input('PpBrg');
  			for ($i = 0; $i < count($detail); $i++) {
  				$getTransaksi = vwOutBarang::where('NoBukti', $detail[$i][0])->where('Kodebrg', $detail[$i][1])->where('satuan', $detail[$i][8])->first();
  				if ($getTransaksi->SisaOut < $detail[$i][5]) {
  					return $detail[$i][1];
  				}
  			}
  			return "0";
  		}
    } elseif ($req->input('choice')=='U') {

    } else {
      $count = OutBrgDet::where('NOBUKTI', $req->input('no_bukti'))->count();
  		if ($count != 0) {
        $values = [
          $req->input('choice'),
          $req->input('no_bukti'),
          "",
          now(),
          "",
          "",
          "",
          "",
          $req->input('urut'),//urutdet
          "",
          "",
          0,
          1,
          "",
          0,
          "",
          0,
          $getUser->name,
          now(),
          ""
        ];
        DB::connection("sqlsrv")->statement('exec Sp_OutBrg ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?',$values);

        // $this->logActivity('DEL', \Auth::id(), $req->input('no_bukti'));
  			return "1;;1;;".$req->input('no_bukti');
  		} else {
  			return "0";
  		}
    }
	}

	public function changeAuth(Request $req) {
		$getUser = NewUsers::where('id', \Auth::id())->first();
		OutBrg::where('NOBUKTI', $req->input('nobukti'))->update([
			'IsOtorisasi1' => 1,
			'OtoUser1' => $getUser->name,
			'TglOto1' => now(),
			'IsBatal' => 0,
			'UserBatal' => '',
			'TglBatal' => null
		]);

    $this->logActivity('OTO', \Auth::id(), $req->input('nobukti'));
	}

	public function changeBatal(Request $req) {
		$getUser = NewUsers::where('id', \Auth::id())->first();
		OutBrg::where('NOBUKTI', $req->input('nobukti'))->update([
      'IsOtorisasi1' => 0,
			'OtoUser1' => '',
			'TglOto1' => null,
			'IsBatal' => 1,
			'UserBatal' => $getUser->name,
			'TglBatal' => now()
		]);
    $this->logActivity('BTL', \Auth::id(), $req->input('nobukti'));
	}

	public function show(Request $req) {
		return OutBrg::where('NOBUKTI', $req->input('nobukti'))->orderBy('NOBUKTI', 'DESC')->first();
	}

  public function showDetPpBrg(Request $req) {
    $periode = NewPeriode::where('user_id', \Auth::id())->first();
		return vwShowOutBrg::
      whereMonth('TANGGAL', '=', $periode->bulan)->whereYear('TANGGAL', '=', $periode->tahun)->
      where('NOBUKTI', $req->input('id'))->orderBy('URUT','NOBUKTI', 'ASC')->get();
	}

  public function showDetOutTransaksiPpBrg(Request $req) {
    $periode = NewPeriode::where('user_id', \Auth::id())->first();
		return vwShowOutBrg::
      whereMonth('TANGGAL', '=', $periode->bulan)->whereYear('TANGGAL', '=', $periode->tahun)->
      where('NOBUKTI', $req->input('id'))->orderBy('URUT','NOBUKTI', 'ASC')->get();
	}

	public function showDet(Request $req) {
		$periode = NewPeriode::where('user_id', \Auth::id())->first();
		return vwOutBarang::
    // whereMonth('TANGGAL', '=', $periode->bulan)->whereYear('TANGGAL', '=', $periode->tahun)->
        orderBy('NoBukti', 'DESC')->selectRaw('vwOutBarang.*')->get();
	}

}

?>
