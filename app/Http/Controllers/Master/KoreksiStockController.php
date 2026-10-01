<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use App\Model\Menu;
use App\Model\AksesMenu;
use App\Model\User;
use App\Model\Periode;
use App\Model\Koreksi;
use App\Model\KoreksiDet;
use App\Model\Nomor;
use App\Model\BahanBarang;
use App\Model\Gudang;
use App\Model\AksesGudang;
use App\Model\StockBarang;
use App\Model\Batch;
use App\Model\Grup;
use App\Model\PostJurnalOto;
use App\Model\Trans;

class KoreksiStockController extends Controller {

	public function index() {
		$getID = Menu::where('access', '/koreksistock')->first();
		$check = AksesMenu::where('id_user', \Auth::id())->where('id_menu', $getID->id)->first();
		if ($check->tampil == 1) {
			User::where('id', \Auth::id())->update(['status' => 1, 'hostid' => gethostbyaddr(\Request::ip()), 'ipaddress' => \Request::ip()]);
			$checkUser = User::where('id', \Auth::id())->first();
  		if ($checkUser->level == 3 && $checkUser->username == 'SA') {
        $menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('menu.show_acc', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
      }else{
        $menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('menu.grup','>', 0)->where('menu.show_acc', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
      }
			$periode = Periode::where('id_user', \Auth::id())->first();
			$koreksi = Koreksi::join('gudang', 'gudang.kode', '=', 'koreksi_stock.gudang')->whereMonth('koreksi_stock.tanggal', '=', $periode->bulan)->whereYear('koreksi_stock.tanggal', '=', $periode->tahun)->where('koreksi_stock.deleted', 0)->
				orderBy('koreksi_stock.no_bukti', 'DESC')->select('koreksi_stock.*', 'gudang.nama')->get();
			$gudang = AksesGudang::join('gudang', 'gudang.id', '=', 'akses_gudang.id_gudang')->where('akses_gudang.id_user', \Auth::id())->where('akses_gudang.akses', 1)->where('gudang.deleted', 0)->select('gudang.*')->get();
			$grup = Grup::where('deleted', 0)->get();
			return view("koreksistock")->with('menu', $menu)->with('periode', $periode)->with('koreksi', $koreksi)->with('gudang', $gudang)->with('akses', $check)->with('grup', $grup);
		} else {
			return redirect('/home');
		}
	}

	public function loadAll() {
		$periode = Periode::where('id_user', \Auth::id())->first();
		return Koreksi::join('gudang', 'gudang.kode', '=', 'koreksi_stock.gudang')->whereMonth('koreksi_stock.tanggal', '=', $periode->bulan)->whereYear('koreksi_stock.tanggal', '=', $periode->tahun)->where('koreksi_stock.deleted', 0)->
			orderBy('koreksi_stock.no_bukti', 'DESC')->select('koreksi_stock.*', 'gudang.nama')->get();
	}

	public function generateNomorBukti() {
		$periode = Periode::where('id_user', \Auth::id())->first();
		$nomor = Nomor::where('id', 1)->first();
		$str = "";
		$nourut = "";

		if ($nomor->reset == 1) {
			$getCount = Koreksi::whereMonth('tanggal', '=', $periode->bulan)->whereYear('tanggal', '=', $periode->tahun)->count();
			$nourut = (string)($getCount + 1);
		} else {
			$getCount = Koreksi::whereYear('tanggal', '=', $periode->tahun)->count();
			$nourut = (string)($getCount + 1);
		}

		$nourut = str_pad($nourut, 4, "0", STR_PAD_LEFT);

		if ($nomor->format1 == 1) { $str .= $nomor->inisial_perusahaan; }
		else if ($nomor->format1 == 2) { $str .= $nomor->koreksi_stock; }
		else if ($nomor->format1 == 3) { $str .= $nourut; }
		else if ($nomor->format1 == 4) { $str .= str_pad($periode->bulan, 2, "0", STR_PAD_LEFT).substr($periode->tahun, -2); }
		else if ($nomor->format1 == 5) { $str .= str_pad($periode->bulan, 2, "0", STR_PAD_LEFT).$periode->tahun; }
		else if ($nomor->format1 == 6) { $str .= substr($periode->tahun, -2).str_pad($periode->bulan, 2, "0", STR_PAD_LEFT); }
		else if ($nomor->format1 == 7) { $str .= $periode->tahun.str_pad($periode->bulan, 2, "0", STR_PAD_LEFT); }
		$str .= $nomor->pemisah;
		if ($nomor->format2 == 1) { $str .= $nomor->inisial_perusahaan; }
		else if ($nomor->format2 == 2) { $str .= $nomor->koreksi_stock; }
		else if ($nomor->format2 == 3) { $str .= $nourut; }
		else if ($nomor->format2 == 4) { $str .= str_pad($periode->bulan, 2, "0", STR_PAD_LEFT).substr($periode->tahun, -2); }
		else if ($nomor->format2 == 5) { $str .= str_pad($periode->bulan, 2, "0", STR_PAD_LEFT).$periode->tahun; }
		else if ($nomor->format2 == 6) { $str .= substr($periode->tahun, -2).str_pad($periode->bulan, 2, "0", STR_PAD_LEFT); }
		else if ($nomor->format2 == 7) { $str .= $periode->tahun.str_pad($periode->bulan, 2, "0", STR_PAD_LEFT); }
		$str .= $nomor->pemisah;
		if ($nomor->format3 == 1) { $str .= $nomor->inisial_perusahaan; }
		else if ($nomor->format3 == 2) { $str .= $nomor->koreksi_stock; }
		else if ($nomor->format3 == 3) { $str .= $nourut; }
		else if ($nomor->format3 == 4) { $str .= str_pad($periode->bulan, 2, "0", STR_PAD_LEFT).substr($periode->tahun, -2); }
		else if ($nomor->format3 == 5) { $str .= str_pad($periode->bulan, 2, "0", STR_PAD_LEFT).$periode->tahun; }
		else if ($nomor->format3 == 6) { $str .= substr($periode->tahun, -2).str_pad($periode->bulan, 2, "0", STR_PAD_LEFT); }
		else if ($nomor->format3 == 7) { $str .= $periode->tahun.str_pad($periode->bulan, 2, "0", STR_PAD_LEFT); }
		$str .= $nomor->pemisah;
		if ($nomor->format4 == 1) { $str .= $nomor->inisial_perusahaan; }
		else if ($nomor->format4 == 2) { $str .= $nomor->koreksi_stock; }
		else if ($nomor->format4 == 3) { $str .= $nourut; }
		else if ($nomor->format4 == 4) { $str .= str_pad($periode->bulan, 2, "0", STR_PAD_LEFT).substr($periode->tahun, -2); }
		else if ($nomor->format4 == 5) { $str .= str_pad($periode->bulan, 2, "0", STR_PAD_LEFT).$periode->tahun; }
		else if ($nomor->format4 == 6) { $str .= substr($periode->tahun, -2).str_pad($periode->bulan, 2, "0", STR_PAD_LEFT); }
		else if ($nomor->format4 == 7) { $str .= $periode->tahun.str_pad($periode->bulan, 2, "0", STR_PAD_LEFT); }

		return $str;
	}

	public function generateNomorUrut() {
		$periode = Periode::where('id_user', \Auth::id())->first();
		$nomor = Nomor::where('id', 1)->first();
		$nourut = "";

		if ($nomor->reset == 1) {
			$getCount = Koreksi::whereMonth('tanggal', '=', $periode->bulan)->whereYear('tanggal', '=', $periode->tahun)->count();
			$nourut = (string)($getCount + 1);
		} else {
			$getCount = Koreksi::whereYear('tanggal', '=', $periode->tahun)->count();
			$nourut = (string)($getCount + 1);
		}

		$nourut = str_pad($nourut, 4, "0", STR_PAD_LEFT);

		return $nourut;
	}

	public function add(Request $req) {
		$count = Koreksi::where('no_bukti', $req->input('no_bukti'))->count();
		if ($count == 0) {
			$newKoreksiStock = Koreksi::create([
				'no_urut' => $req->input('no_urut'),
				'no_bukti' => $req->input('no_bukti'),
				'tanggal' => $req->input('tanggal'),
				'gudang' => $req->input('gudang'),
				'keterangan' => $req->input('keterangan')
			]);

			$batch = $req->input('batch');
			if (!is_array($batch)) {
			    $batch = array();
			}
			for ($i = 0; $i < count($batch); $i++) {
				if ($batch[$i][4] != 0) {
					Batch::create([
						'no_bukti' => $req->input('no_bukti'),
						'no_batch' => $batch[$i][0],
						'urut' => $batch[$i][5],
						'gudang' => $req->input('gudang'),
						'kode_barang' => $batch[$i][1],
						'tanggal' => $batch[$i][2],
						'qty' => $batch[$i][4] * $batch[$i][6] * $batch[$i][7],
						'flag' => $batch[$i][7]
					]);
				}
			}

			for ($i = 0; $i < count($req->input('cart')); $i++) {
				KoreksiDet::create([
					'id_koreksi' => $newKoreksiStock->id,
					'kode_barang' => $req->input('cart')[$i][0],
					'nama_barang' => $req->input('cart')[$i][1],
					'qty_masuk' => $req->input('cart')[$i][2],
					'qty_keluar' => $req->input('cart')[$i][3],
					'satuan' => $req->input('cart')[$i][4],
					'isi' => $req->input('cart')[$i][5],
					'mk' => $req->input('cart')[$i][6],
					'harga' => $req->input('cart')[$i][8]
				]);

				// update stok barang
				$cek_jml = StockBarang::where('bulan', (int)date('m', strtotime($req->input('tanggal'))))->where('tahun', (int)date('Y', strtotime($req->input('tanggal'))))->
					where('kode_barang', $req->input('cart')[$i][0])->where('gudang', $req->input('gudang'))->count();
				if ($cek_jml == 0) {
					$bulanlalu = (int)date('m', strtotime($req->input('tanggal'))) - 1;
					$taun = (int)date('Y', strtotime($req->input('tanggal')));
					if ($bulanlalu == 0) {
						$bulanlalu = 12;
						$taun = $taun - 1;
					}
					$cekstock = StockBarang::where('bulan', $bulanlalu)->where('tahun', $taun)->where('kode_barang', $req->input('cart')[$i][0])->where('gudang', $req->input('gudang'))->count();
					if ($cekstock != 0) {
						$saldoAwal = StockBarang::where('bulan', $bulanlalu)->where('tahun', $taun)->where('kode_barang', $req->input('cart')[$i][0])->where('gudang', $req->input('gudang'))->first();
						StockBarang::create([
							'bulan' => (int)date('m', strtotime($req->input('tanggal'))),
							'tahun' => (int)date('Y', strtotime($req->input('tanggal'))),
							'kode_barang' => $req->input('cart')[$i][0],
							'gudang' => $req->input('gudang'),
							'awal_qty' => $saldoAwal->akhir_qty,
							'awal_harga' => $saldoAwal->akhir_harga,
							'qty_adi' => $req->input('cart')[$i][2] * $req->input('cart')[$i][5],
							'qty_ado' => $req->input('cart')[$i][3] * $req->input('cart')[$i][5],
							'harga_adi' => (string)($req->input('cart')[$i][2] * $req->input('cart')[$i][5] * $req->input('cart')[$i][8])
						]);
					} else {
						StockBarang::create([
							'bulan' => (int)date('m', strtotime($req->input('tanggal'))),
							'tahun' => (int)date('Y', strtotime($req->input('tanggal'))),
							'kode_barang' => $req->input('cart')[$i][0],
							'gudang' => $req->input('gudang'),
							'qty_adi' => $req->input('cart')[$i][2] * $req->input('cart')[$i][5],
							'qty_ado' => $req->input('cart')[$i][3] * $req->input('cart')[$i][5],
							'harga_adi' => (string)($req->input('cart')[$i][2] * $req->input('cart')[$i][5] * $req->input('cart')[$i][8])
						]);
					}
				}
				else {
					$oldStock = StockBarang::where('bulan', (int)date('m', strtotime($req->input('tanggal'))))->where('tahun', (int)date('Y', strtotime($req->input('tanggal'))))->
						where('kode_barang', $req->input('cart')[$i][0])->where('gudang', $req->input('gudang'))->first();
					StockBarang::where('bulan', (int)date('m', strtotime($req->input('tanggal'))))->where('tahun', (int)date('Y', strtotime($req->input('tanggal'))))->
						where('kode_barang', $req->input('cart')[$i][0])->where('gudang', $req->input('gudang'))->update([
						'qty_adi' => $oldStock->qty_adi + ($req->input('cart')[$i][2] * $req->input('cart')[$i][5]),
						'qty_ado' => $oldStock->qty_ado + ($req->input('cart')[$i][3] * $req->input('cart')[$i][5]),
						'harga_adi' => (string)($oldStock->harga_adi + ($req->input('cart')[$i][2] * $req->input('cart')[$i][5] * $req->input('cart')[$i][8]))
					]);
				}
			}
			return 1;
		}
		else {
			return 0;
		}
	}

	public function show(Request $req) {
		return Koreksi::where('id', $req->input('id'))->first();
	}

	public function showDet(Request $req) {
		return KoreksiDet::where('id_koreksi', $req->input('id'))->get();
	}

	public function edit(Request $req) {
		$getPrev = Koreksi::where('id', $req->input('id'))->first();
		$getPrevDet = KoreksiDet::where('id_koreksi', $req->input('id'))->get();
		for ($i = 0; $i < count($getPrevDet); $i++) {
			// update stok barang
			$oldStock = StockBarang::where('bulan', (int)date('m', strtotime($getPrev->tanggal)))->where('tahun', (int)date('Y', strtotime($getPrev->tanggal)))->
				where('kode_barang', $getPrevDet[$i]->kode_barang)->where('gudang', $getPrev->gudang)->first();
			StockBarang::where('bulan', (int)date('m', strtotime($getPrev->tanggal)))->where('tahun', (int)date('Y', strtotime($getPrev->tanggal)))->
				where('kode_barang', $getPrevDet[$i]->kode_barang)->where('gudang', $getPrev->gudang)->update([
					'qty_adi' => $oldStock->qty_adi - ($getPrevDet[$i]->qty_masuk * $getPrevDet[$i]->isi),
					'qty_ado' => $oldStock->qty_ado - ($getPrevDet[$i]->qty_keluar * $getPrevDet[$i]->isi),
					'harga_adi' => (string)($oldStock->harga_adi - ($getPrevDet[$i]->qty_masuk * $getPrevDet[$i]->isi * $getPrevDet[$i]->harga))
			]);
		}
		Batch::where('no_bukti', $getPrev->no_bukti)->delete();
		Koreksi::where('id', $req->input('id'))->update([
			'tanggal' => $req->input('tanggal'),
			'gudang' => $req->input('gudang'),
			'keterangan' => $req->input('keterangan')
		]);

		$batch = $req->input('batch');
		if (!is_array($batch)) {
				$batch = array();
		}
		for ($i = 0; $i < count($batch); $i++) {
			if ($batch[$i][4] != 0) {
				Batch::create([
					'no_bukti' => $getPrev->no_bukti,
					'no_batch' => $batch[$i][0],
					'urut' => $batch[$i][5],
					'gudang' => $getPrev->gudang,
					'kode_barang' => $batch[$i][1],
					'tanggal' => $batch[$i][2],
					'qty' => $batch[$i][4] * $batch[$i][6] * $batch[$i][7],
					'flag' => $batch[$i][7]
				]);
			}
		}

		KoreksiDet::where('id_koreksi', $req->input('id'))->delete();
		for ($i = 0; $i < count($req->input('cart')); $i++) {
			KoreksiDet::create([
				'id_koreksi' => $req->input('id'),
				'kode_barang' => $req->input('cart')[$i][0],
				'nama_barang' => $req->input('cart')[$i][1],
				'qty_masuk' => $req->input('cart')[$i][2],
				'qty_keluar' => $req->input('cart')[$i][3],
				'satuan' => $req->input('cart')[$i][4],
				'isi' => $req->input('cart')[$i][5],
				'mk' => $req->input('cart')[$i][6],
				'harga' => $req->input('cart')[$i][8]
			]);

			// update stok barang
			$cek_jml = StockBarang::where('bulan', (int)date('m', strtotime($req->input('tanggal'))))->where('tahun', (int)date('Y', strtotime($req->input('tanggal'))))->
				where('kode_barang', $req->input('cart')[$i][0])->where('gudang', $req->input('gudang'))->count();
			if ($cek_jml == 0) {
				$bulanlalu = (int)date('m', strtotime($req->input('tanggal'))) - 1;
				$taun = (int)date('Y', strtotime($req->input('tanggal')));
				if ($bulanlalu == 0) {
					$bulanlalu = 12;
					$taun = $taun - 1;
				}
				$cekstock = StockBarang::where('bulan', $bulanlalu)->where('tahun', $taun)->where('kode_barang', $req->input('cart')[$i][0])->where('gudang', $req->input('gudang'))->count();
				if ($cekstock != 0) {
					$saldoAwal = StockBarang::where('bulan', $bulanlalu)->where('tahun', $taun)->where('kode_barang', $req->input('cart')[$i][0])->where('gudang', $req->input('gudang'))->first();
					StockBarang::create([
						'bulan' => (int)date('m', strtotime($req->input('tanggal'))),
						'tahun' => (int)date('Y', strtotime($req->input('tanggal'))),
						'kode_barang' => $req->input('cart')[$i][0],
						'gudang' => $req->input('gudang'),
						'awal_qty' => $saldoAwal->akhir_qty,
						'awal_harga' => $saldoAwal->akhir_harga,
						'qty_adi' => $req->input('cart')[$i][2] * $req->input('cart')[$i][5],
						'qty_ado' => $req->input('cart')[$i][3] * $req->input('cart')[$i][5],
						'harga_adi' => (string)($req->input('cart')[$i][2] * $req->input('cart')[$i][5] * $req->input('cart')[$i][8])
					]);
				} else {
					StockBarang::create([
						'bulan' => (int)date('m', strtotime($req->input('tanggal'))),
						'tahun' => (int)date('Y', strtotime($req->input('tanggal'))),
						'kode_barang' => $req->input('cart')[$i][0],
						'gudang' => $req->input('gudang'),
						'qty_adi' => $req->input('cart')[$i][2] * $req->input('cart')[$i][5],
						'qty_ado' => $req->input('cart')[$i][3] * $req->input('cart')[$i][5],
						'harga_adi' => (string)($req->input('cart')[$i][2] * $req->input('cart')[$i][5] * $req->input('cart')[$i][8])
					]);
				}
			}
			else {
				$oldStock = StockBarang::where('bulan', (int)date('m', strtotime($req->input('tanggal'))))->where('tahun', (int)date('Y', strtotime($req->input('tanggal'))))->
					where('kode_barang', $req->input('cart')[$i][0])->where('gudang', $req->input('gudang'))->first();
				StockBarang::where('bulan', (int)date('m', strtotime($req->input('tanggal'))))->where('tahun', (int)date('Y', strtotime($req->input('tanggal'))))->
					where('kode_barang', $req->input('cart')[$i][0])->where('gudang', $req->input('gudang'))->update([
					'qty_adi' => $oldStock->qty_adi + ($req->input('cart')[$i][2] * $req->input('cart')[$i][5]),
					'qty_ado' => $oldStock->qty_ado + ($req->input('cart')[$i][3] * $req->input('cart')[$i][5]),
					'harga_adi' => (string)($oldStock->harga_adi + ($req->input('cart')[$i][2] * $req->input('cart')[$i][5] * $req->input('cart')[$i][8]))
				]);
			}
		}
	}

	public function changeAuth(Request $req) {
		$getUser = User::where('id', \Auth::id())->first();
		Koreksi::where('id', $req->input('id'))->update([
			'auth_1' => 1,
			'auth_user_1' => $getUser->name,
			'auth_date_1' => now(),
			'batal' => 0,
			'batal_user' => '',
			'batal_date' => null
		]);

		$getKoreksi = Koreksi::where('id', $req->input('id'))->first();
		$getJurnal = PostJurnalOto::where('no_bukti', $getKoreksi->no_bukti)->get();
		for ($i = 0; $i < count($getJurnal); $i++) {
			Trans::create([
				'no_bukti' => $getJurnal[$i]->no_bukti,
				'tanggal' => $getJurnal[$i]->tanggal,
				'devisi' => $getJurnal[$i]->devisi,
				'custsupp' => $getJurnal[$i]->kode_custsupp,
				'departemen' => "",
				'note' => $getJurnal[$i]->note,
				'lampiran' => 0,
				'perkiraan' => $getJurnal[$i]->perkiraan,
				'lawan' => $getJurnal[$i]->lawan,
				'keterangan' => $getJurnal[$i]->keterangan,
				'valas' => $getJurnal[$i]->valas,
				'kurs' => $getJurnal[$i]->kurs,
				'debet' => $getJurnal[$i]->debet,
				'kredit' => $getJurnal[$i]->kredit,
				'debet_rp' => $getJurnal[$i]->debet_rp,
				'kredit_rp' => $getJurnal[$i]->kredit_rp,
				'tipe_trans' => $getJurnal[$i]->tipetrans,
				'jenis' => $getJurnal[$i]->jenis,
				'urut' => $i
			]);
		}
    $this->logActivity('OTO', \Auth::id(), $getKoreksi->no_bukti);
	}

	public function changeBatal(Request $req) {
		$getUser = User::where('id', \Auth::id())->first();
		Koreksi::where('id', $req->input('id'))->update([
			'auth_1' => 0,
			'auth_user_1' => '',
			'auth_date_1' => null,
			'batal' => 1,
			'batal_user' => $getUser->name,
			'batal_date' => now()
		]);
		$getKoreksi = Koreksi::where('id', $req->input('id'))->first();
		Trans::where('no_bukti', $getKoreksi->no_bukti)->delete();
    $this->logActivity('BTL', \Auth::id(), $getKoreksi->no_bukti);
		return 1;
	}

	public function erase(Request $req) {
		Koreksi::where('id', $req->input('id'))->update(['deleted' => 1]);
		$getPrev = Koreksi::where('id', $req->input('id'))->first();
		$getPrevDet = KoreksiDet::where('id_koreksi', $req->input('id'))->get();
		for ($i = 0; $i < count($getPrevDet); $i++) {
			// update stok barang
			$oldStock = StockBarang::where('bulan', (int)date('m', strtotime($getPrev->tanggal)))->where('tahun', (int)date('Y', strtotime($getPrev->tanggal)))->
				where('kode_barang', $getPrevDet[$i]->kode_barang)->where('gudang', $getPrev->gudang)->first();
			StockBarang::where('bulan', (int)date('m', strtotime($getPrev->tanggal)))->where('tahun', (int)date('Y', strtotime($getPrev->tanggal)))->
				where('kode_barang', $getPrevDet[$i]->kode_barang)->where('gudang', $getPrev->gudang)->update([
					'qty_adi' => $oldStock->qty_adi - ($getPrevDet[$i]->qty_masuk * $getPrevDet[$i]->isi),
					'qty_ado' => $oldStock->qty_ado - ($getPrevDet[$i]->qty_keluar * $getPrevDet[$i]->isi),
					'harga_adi' => (string)($oldStock->harga_adi - ($getPrevDet[$i]->qty_masuk * $getPrevDet[$i]->isi * $getPrevDet[$i]->harga))
			]);
		}
		KoreksiDet::where('id_koreksi', $req->input('id'))->delete();
		Batch::where('no_bukti', $getPrev->no_bukti)->delete();
	}

}

?>
