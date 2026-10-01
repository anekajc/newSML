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
use App\Model\AlokasiBrg;
use App\Model\AlokasiBrgDet;
use App\Model\VWOutPenerimaanBrg;
use App\Model\vwShowAlokasiBrg;
use App\Model\vwShowDetailAlokasiBrg;
use App\Model\vwShowLocBrg;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\NewPeriode;
use App\Model\NewUsers;

class AlokasiBarangController extends Controller {
  use ActivityTrait;

	public function index() {
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
      $penerimaan = VWOutPenerimaanBrg::whereMonth('TANGGAL', '=', $periode->bulan)->whereYear('TANGGAL', '=', $periode->tahun)->
          orderBy('NoBukti1','kodebrg', 'DESC')->selectRaw('VWOUTPENERIMAANBRG.*')->get();
      $alokasibrg = vwShowAlokasiBrg::whereMonth('TANGGAL', '=', $periode->bulan)->whereYear('TANGGAL', '=', $periode->tahun)->
          orderBy('NOBUKTI', 'DESC')->selectRaw('vwShowAlokasiBrg.*')->get();

      return view("alokasibarang")
      ->with('menul0', $menul0)
      ->with('periode', $periode)
      // ->with('akses', $check)
      ->with('penerimaan', $penerimaan)
      ->with('gudang', $gudang)
      ->with('alokasibrg', $alokasibrg);
		// } else {
		// 	return redirect('/home');
		// }
	}

	public function loadAll() {
		$periode = NewPeriode::where('user_id', \Auth::id())->first();
    return vwShowAlokasiBrg::whereMonth('TANGGAL', '=', $periode->bulan)->whereYear('TANGGAL', '=', $periode->tahun)->
        orderBy('NOBUKTI', 'DESC')->selectRaw('vwShowAlokasiBrg.*')->get();

	}

	public function loadAllOutPpb() {
		$periode = NewPeriode::where('user_id', \Auth::id())->first();
    return VWOutPenerimaanBrg::whereMonth('TANGGAL', '=', $periode->bulan)->whereYear('TANGGAL', '=', $periode->tahun)->
        orderBy('NoBukti1','kodebrg', 'DESC')->selectRaw('VWOUTPENERIMAANBRG.*')->get();
  }

	public function generateNomorBukti() {
    $periode = NewPeriode::where('user_id', \Auth::id())->first();
    $getUser = NewUsers::where('id', \Auth::id())->first();
    $inisial = DB::connection("sqlsrv")->select('select * from DBNOMOR');
    $values = [
      $inisial[0]->IB,
      $periode->bulan,
      $periode->tahun,
      $getUser->username
    ];
    $nomor = DB::connection("sqlsrv")->select('exec SP_IsiNobukti ?,?,?,?',$values);

		return $nomor[0]->Nobukti;
	}

	public function generateNomorUrut() {
    $periode = NewPeriode::where('user_id', \Auth::id())->first();
    $getUser = NewUsers::where('id', \Auth::id())->first();
    $inisial = DB::connection("sqlsrv")->select('select * from DBNOMOR');
    $values = [
      $inisial[0]->IB,
      $periode->bulan,
      $periode->tahun,
      $getUser->username
    ];
    $nomor = DB::connection("sqlsrv")->select('exec SP_IsiNobukti ?,?,?,?',$values);

		return $nomor[0]->Nourut;
	}

	public function add(Request $req) {
    $getUser = NewUsers::where('id', \Auth::id())->first();
    if ($req->input('choice')=='I'){
  		$count = AlokasiBrgDet::where('NOBUKTI', $req->input('no_bukti'))->count();
  		if ($count =! 0) {
  			$detail = $req->input('alokasibrg');
  			for ($i = 0; $i < count($detail); $i++) {
          if ($detail[$i][16] == "1"){
            $getTransaksi = VWOutPenerimaanBrg::where('NoBukti1', $detail[$i][0])->where('Kodebrg', $detail[$i][1])->where('URUT', $detail[$i][10])->first();
            if ($getTransaksi->QNTSISA < $detail[$i][5]) {
              return $detail[$i][1];
            }
            $values = [
              $req->input('choice'),
              $req->input('no_bukti'),
              $req->input('no_urut'),
              $req->input('tanggal'),
              $detail[$i][6],
              $detail[$i][14],
              $detail[$i][15],
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
              $detail[$i][13]
            ];
            DB::connection("sqlsrv")->statement('exec Sp_AlokasiBrg ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?',$values);
          }
  			}
  			// $this->logActivity('ADD', \Auth::id(), $req->input('no_bukti'));
  			return "1;;1;;".$req->input('no_bukti');
  		} else {
  			$detail = $req->input('alokasibrg');
  			for ($i = 0; $i < count($detail); $i++) {
  				$getTransaksi = VWOutPenerimaanBrg::where('NoBukti1', $detail[$i][0])->where('Kodebrg', $detail[$i][1])->where('URUT', $detail[$i][10])->first();
  				if ($getTransaksi->QNTSISA < $detail[$i][5]) {
  					return $detail[$i][1];
  				}
  			}
  			return "0";
  		}
    } elseif ($req->input('choice')=='U') {
      $count = AlokasiBrgDet::where('NOBUKTI', $req->input('no_bukti'))->count();
      if ($count =! 0) {
        $detail = $req->input('alokasibrg');
        for ($i = 0; $i < count($detail); $i++) {
          $getTransaksi = VWOutPenerimaanBrg::where('NoBukti1', $detail[$i][0])->where('Kodebrg', $detail[$i][1])->where('URUT', $detail[$i][10])->first();
          if ($getTransaksi->QNTSISA < $detail[$i][5]) {
            return $detail[$i][1];
          }
          $values = [
            $req->input('choice'),
            $req->input('no_bukti'),
            $req->input('no_urut'),
            $req->input('tanggal'),
            $detail[$i][6],
            $detail[$i][14],
            $detail[$i][15],
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
            $detail[$i][13]
          ];
          DB::connection("sqlsrv")->statement('exec Sp_AlokasiBrg ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?',$values);
        }
        // $this->logActivity('ADD', \Auth::id(), $req->input('no_bukti'));
        return "1;;1;;".$req->input('no_bukti');
      } else {
        $detail = $req->input('alokasibrg');
        for ($i = 0; $i < count($detail); $i++) {
          $getTransaksi = VWOutPenerimaanBrg::where('NoBukti1', $detail[$i][0])->where('Kodebrg', $detail[$i][1])->where('URUT', $detail[$i][10])->first();
          if ($getTransaksi->QNTSISA < $detail[$i][5]) {
            return $detail[$i][1];
          }
        }
        return "0";
      }
    } else {
      $count = AlokasiBrgDet::where('NOBUKTI', $req->input('no_bukti'))->count();
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
        DB::connection("sqlsrv")->statement('exec Sp_AlokasiBrg ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?',$values);

        // $this->logActivity('DEL', \Auth::id(), $req->input('no_bukti'));
  			return "1;;1;;".$req->input('no_bukti');
  		} else {
  			return "0";
  		}
    }
	}

	public function changeAuth(Request $req) {
		$getUser = NewUsers::where('id', \Auth::id())->first();
		AlokasiBrg::where('NOBUKTI', $req->input('nobukti'))->update([
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
		AlokasiBrg::where('NOBUKTI', $req->input('nobukti'))->update([
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
		return AlokasiBrg::where('NOBUKTI', $req->input('nobukti'))->orderBy('NOBUKTI', 'DESC')->first();
	}

  public function showDetOutBrg(Request $req) {
		return vwShowDetailAlokasiBrg::where('NOBUKTI', $req->input('id'))->orderBy('URUT','NOBUKTI', 'ASC')->get();
	}

  public function showDetailBrgLoc(Request $req) {
    $periode = NewPeriode::where('user_id', \Auth::id())->first();
    if ($req->input('tipe') == "0"){
      return vwShowLocBrg::whereMonth('TANGGAL', '=', $periode->bulan)->whereYear('TANGGAL', '=', $periode->tahun)->
      where('NoBukti', $req->input('id'))->orderBy('urutloc','Kodebrg','Kodegdg','Barcode', 'ASC')->get();
    }else{
      return vwShowLocBrg::whereMonth('TANGGAL', '=', $periode->bulan)->whereYear('TANGGAL', '=', $periode->tahun)->
      where('NoBukti', $req->input('id'))->where('Kodebrg', $req->input('kodebrg'))->where('URUT', $req->input('urut'))->
      orderBy('urutloc','Kodebrg','Kodegdg','Barcode', 'ASC')->get();
    }
	}

  public function showDetOutTransaksiOutBrg(Request $req) {
		return vwShowDetailAlokasiBrg::where('NOBUKTI', $req->input('id'))->orderBy('URUT','NOBUKTI', 'ASC')->get();
	}

  public function showPilihRak(Request $req) {
		return RakSQL::where('KodeGdg', $req->input('kodegdg'))->orderBy('KodeRak', 'ASC')->get();
	}

  public function showPilihLokasi(Request $req) {
		return LokasiSQL::where('KodeRak', $req->input('koderak'))->where('KodeGdg', $req->input('kodegdg'))->orderBy('KodeLokasi', 'ASC')->get();
	}

  public function showPilihBarcode(Request $req) {
		return LokasiSQL::where('BarcodeLoc', $req->input('barcode'))->first();
	}

	public function showDet(Request $req) {
    $periode = NewPeriode::where('user_id', \Auth::id())->first();
		return VWOutPenerimaanBrg::whereMonth('TANGGAL', '=', $periode->bulan)->whereYear('TANGGAL', '=', $periode->tahun)->
    where('NoBukti1', $req->input('nobukti'))->
    orderBy('NoBukti1','kodebrg', 'ASC')->get();
	}

}

?>
