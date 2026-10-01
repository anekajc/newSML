<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use App\Traits\ActivityTrait;
use App\Model\User;
use App\Model\Periode;
use App\Model\Nomor;
use App\Model\GudangSQL;
use App\Model\RakSQL;
use App\Model\LokasiSQL;
use App\Model\OutBrg;
use App\Model\OutBrgDet;
use App\Model\OutBrgDet2;
use App\Model\vwPermintaanOutBarang;
use App\Model\vwShowPersiapanOutBrg;
use App\Model\vwShowStkBrg;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\NewPeriode;
use App\Model\NewUsers;

class OutBarangController extends Controller {
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
			$outtransaksi = vwPermintaanOutBarang::whereMonth('TANGGAL', '=', $periode->bulan)->whereYear('TANGGAL', '=', $periode->tahun)->
            groupBy('NoBukti','NOURUT','TANGGAL')->orderBy('NoBukti', 'DESC')->selectRaw('vwPermintaanOutBarang.NoBukti,vwPermintaanOutBarang.NOURUT,vwPermintaanOutBarang.TANGGAL')->distinct()->get();

      $persiapanoutbrg = vwShowPersiapanOutBrg::whereMonth('TANGGAL', '=', $periode->bulan)->whereYear('TANGGAL', '=', $periode->tahun)->
          orderBy('NOBUKTI', 'DESC')->selectRaw('vwShowPersiapanOutBrg.*')->get();

      return view("outbarang")
      ->with('menul0', $menul0)
      ->with('periode', $periode)
      // ->with('akses', $check)
      ->with('outtransaksi', $outtransaksi)
      ->with('gudang', $gudang)
      ->with('persiapanoutbrg', $persiapanoutbrg);
		// } else {
		// 	return redirect('/home');
		// }
	}

	public function loadAll() {
		$periode = NewPeriode::where('user_id', \Auth::id())->first();
    return vwShowPersiapanOutBrg::whereMonth('TANGGAL', '=', $periode->bulan)->whereYear('TANGGAL', '=', $periode->tahun)->
        orderBy('NOBUKTI', 'DESC')->selectRaw('vwShowPersiapanOutBrg.*')->get();

	}

	public function loadAllOutPpb() {
		$periode = NewPeriode::where('user_id', \Auth::id())->first();
		return vwPermintaanOutBarang::whereMonth('TANGGAL', '=', $periode->bulan)->whereYear('TANGGAL', '=', $periode->tahun)->
        groupBy('NoBukti','NOURUT','TANGGAL')->orderBy('NoBukti', 'DESC')->selectRaw('vwPermintaanOutBarang.NoBukti,vwPermintaanOutBarang.NOURUT,vwPermintaanOutBarang.TANGGAL')->distinct()->get();
  }

	public function generateNomorBukti() {
    $periode = NewPeriode::where('user_id', \Auth::id())->first();
    $getUser = NewUsers::where('id', \Auth::id())->first();
    $inisial = DB::connection("sqlsrv")->select('select * from DBNOMOR');
    $values = [
      $inisial[0]->OB,
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
      $inisial[0]->OB,
      $periode->bulan,
      $periode->tahun,
      $getUser->username
    ];
    $nomor = DB::connection("sqlsrv")->select('exec SP_IsiNobukti ?,?,?,?',$values);

		return $nomor[0]->Nourut;
	}

	public function add(Request $req) {
    $getUser = User::where('id', \Auth::id())->first();
    if ($req->input('choice')=='I'){
  		$count = OutBrgDet::where('NOBUKTI', $req->input('no_bukti'))->count();
  		if ($count =! 0) {
  			$detail = $req->input('outbrg');
  			for ($i = 0; $i < count($detail); $i++) {
          if ($detail[$i][16] == "1"){
            $getTransaksi = vwPermintaanOutBarang::where('NoBukti', $detail[$i][0])->where('Kodebrg', $detail[$i][1])->where('satuan', $detail[$i][8])->first();
            if ($getTransaksi->SisaOut < $detail[$i][5]) {
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
            DB::connection("SPLSIG")->statement('exec Sp_OutBrg2 ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?',$values);
          }
  			}
  			$this->logActivity('ADD', \Auth::id(), $req->input('no_bukti'));
  			return "1;;1;;".$req->input('no_bukti');
  		} else {
  			$detail = $req->input('outbrg');
  			for ($i = 0; $i < count($detail); $i++) {
  				$getTransaksi = vwPermintaanOutBarang::where('NoBukti', $detail[$i][0])->where('Kodebrg', $detail[$i][1])->where('satuan', $detail[$i][8])->first();
  				if ($getTransaksi->SisaOut < $detail[$i][5]) {
  					return $detail[$i][1];
  				}
  			}
  			return "0";
  		}
    } elseif ($req->input('choice')=='U') {
      $count = OutBrgDet2::where('NOBUKTI', $req->input('no_bukti'))->count();
      if ($count =! 0) {
        $detail = $req->input('outbrg');
        for ($i = 0; $i < count($detail); $i++) {
          $getTransaksi = vwPermintaanOutBarang::where('NoBukti', $detail[$i][0])->where('Kodebrg', $detail[$i][1])->where('satuan', $detail[$i][8])->first();
          if ($getTransaksi->SisaOut < $detail[$i][5]) {
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
          DB::connection("SPLSIG")->statement('exec Sp_OutBrg2 ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?',$values);
        }
        $this->logActivity('ADD', \Auth::id(), $req->input('no_bukti'));
        return "1;;1;;".$req->input('no_bukti');
      } else {
        $detail = $req->input('outbrg');
        for ($i = 0; $i < count($detail); $i++) {
          $getTransaksi = vwPermintaanOutBarang::where('NoBukti', $detail[$i][0])->where('Kodebrg', $detail[$i][1])->where('satuan', $detail[$i][8])->first();
          if ($getTransaksi->SisaOut < $detail[$i][5]) {
            return $detail[$i][1];
          }
        }
        return "0";
      }
    } else {
      $count = OutBrgDet2::where('NOBUKTI', $req->input('no_bukti'))->count();
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
        DB::connection("SPLSIG")->statement('exec Sp_OutBrg2 ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?',$values);

        $this->logActivity('DEL', \Auth::id(), $req->input('no_bukti'));
  			return "1;;1;;".$req->input('no_bukti');
  		} else {
  			return "0";
  		}
    }
	}

	public function changeAuth(Request $req) {
		$getUser = User::where('id', \Auth::id())->first();
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
		$getUser = User::where('id', \Auth::id())->first();
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

  public function showDetOutBrg(Request $req) {
		return vwShowPersiapanOutBrg::where('NOBUKTI', $req->input('id'))->orderBy('URUT','NOBUKTI', 'ASC')->get();
	}

  public function showDetStkBrg(Request $req) {
    $periode = NewPeriode::where('user_id', \Auth::id())->first();
    if ($req->input('tipe') == "0"){
      return vwShowStkBrg::whereMonth('TANGGAL', '=', $periode->bulan)->whereYear('TANGGAL', '=', $periode->tahun)->
      where('NoBukti', $req->input('id'))->orderBy('Kodebrg','Kodegdg','Barcode', 'ASC')->get();
    }else{
      return vwShowStkBrg::whereMonth('TANGGAL', '=', $periode->bulan)->whereYear('TANGGAL', '=', $periode->tahun)->
      where('Kodebrg', $req->input('id'))->orderBy('Kodebrg','Kodegdg','Barcode', 'ASC')->get();
    }

	}

  public function showDetOutTransaksiOutBrg(Request $req) {
		return vwShowPersiapanOutBrg::where('NOBUKTI', $req->input('id'))->orderBy('URUT','NOBUKTI', 'ASC')->get();
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
    return vwPermintaanOutBarang::whereMonth('TANGGAL', '=', $periode->bulan)->whereYear('TANGGAL', '=', $periode->tahun)->
        orderBy('NoBukti', 'DESC')->selectRaw('vwPermintaanOutBarang.*')->get();
	}

	public function edit(Request $req) {
		$getPrev = PemakaianBarang::where('id', $req->input('id'))->first();
		$getPrevDet = DetailPemakaianBarang::where('id_pemakaian', $req->input('id'))->get();
		for ($i = 0; $i < count($getPrevDet); $i++) {
			$getDetPermintaan = DetailPermintaanPemakaian::where('id_permintaan', $getPrev->id_permintaan)->where('kode_barang', $getPrevDet[$i]->kode_barang)->where('satuan', $getPrevDet[$i]->satuan)->first();
			DetailPermintaanPemakaian::where('id_permintaan', $getPrev->id_permintaan)->where('kode_barang', $getPrevDet[$i]->kode_barang)->where('satuan', $getPrevDet[$i]->satuan)->update([
				'qty_done' => $getDetPermintaan->qty_done - $getPrevDet[$i]->qty
			]);

			// update stok barang
			$oldStock = StockBarang::where('bulan', (int)date('m', strtotime($getPrev->tanggal)))->where('tahun', (int)date('Y', strtotime($getPrev->tanggal)))->
				where('kode_barang', $getPrevDet[$i]->kode_barang)->where('gudang', $getPrev->gudang)->first();
			StockBarang::where('bulan', (int)date('m', strtotime($getPrev->tanggal)))->where('tahun', (int)date('Y', strtotime($getPrev->tanggal)))->
				where('kode_barang', $getPrevDet[$i]->kode_barang)->where('gudang', $getPrev->gudang)->update([
				'qty_pakai' => $oldStock->qty_pakai - ($getPrevDet[$i]->qty * $getPrevDet[$i]->isi)
			]);
		}
		$detail = $req->input('pemakaian');
		for ($a = 0; $a < count($detail); $a++) {
			$getPermintaan = DetailPermintaanPemakaian::where('id_permintaan', $req->input('id_permintaan'))->where('kode_barang', $detail[$a][0])->where('satuan', $detail[$a][3])->first();
			if ($getPermintaan->selisih < $detail[$a][2]) {
				for ($i = 0; $i < count($getPrevDet); $i++) {
					$getDetPermintaan = DetailPermintaanPemakaian::where('id_permintaan', $getPrev->id_permintaan)->where('kode_barang', $getPrevDet[$i]->kode_barang)->where('satuan', $getPrevDet[$i]->satuan)->first();
					DetailPermintaanPemakaian::where('id_permintaan', $getPrev->id_permintaan)->where('kode_barang', $getPrevDet[$i]->kode_barang)->where('satuan', $getPrevDet[$i]->satuan)->update([
						'qty_done' => $getDetPermintaan->qty_done + $getPrevDet[$i]->qty
					]);

					// update stok barang
					$oldStock = StockBarang::where('bulan', (int)date('m', strtotime($getPrev->tanggal)))->where('tahun', (int)date('Y', strtotime($getPrev->tanggal)))->
						where('kode_barang', $getPrevDet[$i]->kode_barang)->where('gudang', $getPrev->gudang)->first();
					StockBarang::where('bulan', (int)date('m', strtotime($getPrev->tanggal)))->where('tahun', (int)date('Y', strtotime($getPrev->tanggal)))->
						where('kode_barang', $getPrevDet[$i]->kode_barang)->where('gudang', $getPrev->gudang)->update([
						'qty_pakai' => $oldStock->qty_pakai + ($getPrevDet[$i]->qty * $getPrevDet[$i]->isi)
					]);
				}
				return $detail[$a][0];
			}
		}
		DetailPemakaianBarang::where('id_pemakaian', $req->input('id'))->delete();
		Batch::where('no_bukti', $req->input('no_bukti'))->delete();
		PemakaianBarang::where('id', $req->input('id'))->update([
			'tanggal' => $req->input('tanggal'),
			'departemen' => $req->input('departemen'),
			'gudang' => $req->input('gudang'),
			'keterangan' => $req->input('keterangan')
		]);
		for ($i = 0; $i < count($detail); $i++) {
			DetailPemakaianBarang::create([
				'id_pemakaian' => $detail[$i][6],
				'no_pemakaian' => $detail[$i][7],
				'perkiraan' => $detail[$i][8],
				'kode_barang' => $detail[$i][0],
				'nama_barang' => $detail[$i][1],
				'qty' => $detail[$i][2],
				'satuan' => $detail[$i][3],
				'isi' => $detail[$i][4]
			]);

			$getPermintaan = DetailPermintaanPemakaian::where('id_permintaan', $req->input('id_permintaan'))->where('kode_barang', $detail[$i][0])->where('satuan', $detail[$i][3])->first();
			DetailPermintaanPemakaian::where('id_permintaan', $req->input('id_permintaan'))->where('kode_barang', $detail[$i][0])->where('satuan', $detail[$i][3])->update([
				'qty_done' => $getPermintaan->qty_done + $detail[$i][2]
			]);

			// update stok barang
			$cek_jml = StockBarang::where('bulan', (int)date('m', strtotime($req->input('tanggal'))))->where('tahun', (int)date('Y', strtotime($req->input('tanggal'))))->
				where('kode_barang', $detail[$i][0])->where('gudang', $req->input('gudang'))->count();
			if ($cek_jml == 0) {
				$bulanlalu = (int)date('m', strtotime($req->input('tanggal'))) - 1;
				$taun = (int)date('Y', strtotime($req->input('tanggal')));
				if ($bulanlalu == 0) {
					$bulanlalu = 12;
					$taun = $taun - 1;
				}
				$cekstock = StockBarang::where('bulan', $bulanlalu)->where('tahun', $taun)->where('kode_barang', $detail[$i][0])->where('gudang', $req->input('gudang'))->count();
				if ($cekstock != 0) {
					$saldoAwal = StockBarang::where('bulan', $bulanlalu)->where('tahun', $taun)->where('kode_barang', $detail[$i][0])->where('gudang', $req->input('gudang'))->first();
					StockBarang::create([
						'bulan' => (int)date('m', strtotime($req->input('tanggal'))),
						'tahun' => (int)date('Y', strtotime($req->input('tanggal'))),
						'kode_barang' => $detail[$i][0],
						'gudang' => $req->input('gudang'),
						'awal_qty' => $saldoAwal->akhir_qty,
						'awal_harga' => $saldoAwal->akhir_harga,
						'qty_pakai' => $detail[$i][2] * $detail[$i][4]
					]);
				} else {
					StockBarang::create([
						'bulan' => (int)date('m', strtotime($req->input('tanggal'))),
						'tahun' => (int)date('Y', strtotime($req->input('tanggal'))),
						'kode_barang' => $detail[$i][0],
						'gudang' => $req->input('gudang'),
						'qty_pakai' => $detail[$i][2] * $detail[$i][4]
					]);
				}
			}
			else {
				$oldStock = StockBarang::where('bulan', (int)date('m', strtotime($req->input('tanggal'))))->where('tahun', (int)date('Y', strtotime($req->input('tanggal'))))->
					where('kode_barang', $detail[$i][0])->where('gudang', $req->input('gudang'))->first();
				StockBarang::where('bulan', (int)date('m', strtotime($req->input('tanggal'))))->where('tahun', (int)date('Y', strtotime($req->input('tanggal'))))->
					where('kode_barang', $detail[$i][0])->where('gudang', $req->input('gudang'))->update([
					'qty_pakai' => $oldStock->qty_pakai + ($detail[$i][2] * $detail[$i][4])
				]);
			}
		}

		$batch = $req->input("batch");
		if (!is_array($batch)) {
				$batch = array();
		}
		for ($i = 0; $i < count($batch); $i++) {
			if ($batch[$i][4] > 0) {
				Batch::create([
					'no_bukti' => $req->input('no_bukti'),
					'no_batch' => $batch[$i][0],
					'urut' => $batch[$i][5],
					'gudang' => $req->input('gudang'),
					'kode_barang' => $batch[$i][1],
					'tanggal' => $batch[$i][2],
					'qty' => $batch[$i][4] * $batch[$i][6] * -1
				]);
			}
		}
		$this->logActivity('UPD', \Auth::id(), $getPrev->no_bukti);
		return 1;
	}

	public function erase(Request $req) {
		$getPrev = PemakaianBarang::where('id', $req->input('id'))->first();
		$getPrevDet = DetailPemakaianBarang::where('id_pemakaian', $req->input('id'))->get();
		for ($i = 0; $i < count($getPrevDet); $i++) {
			$getDetPermintaan = DetailPermintaanPemakaian::where('id_permintaan', $getPrev->id_permintaan)->where('kode_barang', $getPrevDet[$i]->kode_barang)->where('satuan', $getPrevDet[$i]->satuan)->first();
			DetailPermintaanPemakaian::where('id_permintaan', $getPrev->id_permintaan)->where('kode_barang', $getPrevDet[$i]->kode_barang)->where('satuan', $getPrevDet[$i]->satuan)->update([
				'qty_done' => $getDetPermintaan->qty_done - $getPrevDet[$i]->qty
			]);

			// update stok barang
			$oldStock = StockBarang::where('bulan', (int)date('m', strtotime($getPrev->tanggal)))->where('tahun', (int)date('Y', strtotime($getPrev->tanggal)))->
				where('kode_barang', $getPrevDet[$i]->kode_barang)->where('gudang', $getPrev->gudang)->first();
			StockBarang::where('bulan', (int)date('m', strtotime($getPrev->tanggal)))->where('tahun', (int)date('Y', strtotime($getPrev->tanggal)))->
				where('kode_barang', $getPrevDet[$i]->kode_barang)->where('gudang', $getPrev->gudang)->update([
				'qty_pakai' => $oldStock->qty_pakai - ($getPrevDet[$i]->qty * $getPrevDet[$i]->isi)
			]);
		}
		DetailPemakaianBarang::where('id_pemakaian', $req->input('id'))->delete();
		Batch::where('no_bukti', $getPrev->no_bukti)->delete();
		PemakaianBarang::where('id', $req->input('id'))->update(['deleted' => 1]);
		$this->logActivity('DEL', \Auth::id(), $getPrev->no_bukti);
	}

}

?>
