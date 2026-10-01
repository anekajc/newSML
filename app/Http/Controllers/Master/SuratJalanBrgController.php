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
use App\Model\Nomor;
use App\Model\Gudang;
use App\Model\SO;
use App\Model\DetailSO;
use App\Model\BahanBarang;
use App\Model\StockBarang;
use App\Model\SuratJalan;
use App\Model\DetailSuratJalan;
use App\Model\Batch;
use App\Model\AksesGudang;
use App\Model\OutstandingSO;
use App\Model\HutangPiutang;
use App\Model\PostHutPiut;
use App\Model\PostJurnalOto;
use App\Model\Trans;
use App\Traits\ActivityTrait;

class SuratJalanBrgController extends Controller {
  use ActivityTrait;

	public function index() {
		$getID = Menu::where('access', '/suratjalanbrg')->first();
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
			$getGudang = AksesGudang::join('gudang', 'gudang.id', '=', 'akses_gudang.id_gudang')->where('akses_gudang.id_user', \Auth::id())->where('akses_gudang.akses', 1)->where('gudang.deleted', 0)->select('gudang.kode')->get()->toArray();
			$so = SO::where('deleted', 0)->where('done', 0)->where('auth_1', 1)->whereMonth('tanggal', '=', $periode->bulan)->whereYear('tanggal', '=', $periode->tahun)->whereIn('gudang', $getGudang)->orderBy('no_bukti', 'DESC')->get();
			$gudang = AksesGudang::join('gudang', 'gudang.id', '=', 'akses_gudang.id_gudang')->where('akses_gudang.id_user', \Auth::id())->where('akses_gudang.akses', 1)->where('gudang.deleted', 0)->select('gudang.*')->get();
			$outstandingSO1 = OutstandingSO::whereIn('gudang', $getGudang)->whereMonth('tanggal', '=', $periode->bulan)->whereYear('tanggal', '=', $periode->tahun)->where('selisih', '<', 0)->orderBy('no_bukti', 'DESC')->get();
			$outstandingSO2 = OutstandingSO::whereIn('gudang', $getGudang)->whereMonth('tanggal', '=', $periode->bulan)->whereYear('tanggal', '=', $periode->tahun)->where('selisih', '>=', 0)->orderBy('no_bukti', 'DESC')->get();
			$sj = SuratJalan::join('custsupp', 'custsupp.kode', '=', 'surat_jalan.customer')->where('surat_jalan.deleted', 0)->whereIn('surat_jalan.gudang', $getGudang)->
				whereMonth('surat_jalan.tanggal', '=', $periode->bulan)->whereYear('surat_jalan.tanggal', '=', $periode->tahun)->select('surat_jalan.*', 'custsupp.nama')->orderBy('surat_jalan.no_bukti', 'DESC')->get();
			return view("suratjalanbrg")->with('menu', $menu)->with('periode', $periode)->with('akses', $check)->with('so', $so)->with('gudang', $gudang)->with('so1', $outstandingSO1)->with('so2', $outstandingSO2)->with('sj', $sj);
		} else {
			return redirect('/home');
		}
	}

	public function getSO() {
		return SO::where('deleted', 0)->where('done', 0)->where('auth_1', 1)->whereMonth('tanggal', '=', $periode->bulan)->whereYear('tanggal', '=', $periode->tahun)->whereIn('gudang', $getGudang)->orderBy('no_bukti', 'DESC')->get();
	}

	public function loadAllSO() {
		$periode = Periode::where('id_user', \Auth::id())->first();
		$getGudang = AksesGudang::join('gudang', 'gudang.id', '=', 'akses_gudang.id_gudang')->where('akses_gudang.id_user', \Auth::id())->where('akses_gudang.akses', 1)->where('gudang.deleted', 0)->select('gudang.kode')->get()->toArray();
		return OutstandingSO::whereIn('gudang', $getGudang)->whereMonth('tanggal', '=', $periode->bulan)->whereYear('tanggal', '=', $periode->tahun)->orderBy('no_bukti', 'DESC')->get();
	}

	public function loadAllSOByNoBukti(Request $req) {
		$periode = Periode::where('id_user', \Auth::id())->first();
		$getGudang = AksesGudang::join('gudang', 'gudang.id', '=', 'akses_gudang.id_gudang')->where('akses_gudang.id_user', \Auth::id())->where('akses_gudang.akses', 1)->where('gudang.deleted', 0)->select('gudang.kode')->get()->toArray();
		// $getSO = SO::whereIn('gudang', $getGudang)->whereMonth('tanggal', '=', $periode->bulan)->whereYear('tanggal', '=', $periode->tahun)->where('no_bukti', $req->input('no_bukti'))->orderBy('no_bukti', 'DESC')->first();
		return OutstandingSO::where('no_bukti', $req->input('no_bukti'))->whereIn('gudang', $getGudang)->get();
	}

	public function loadAll() {
		$periode = Periode::where('id_user', \Auth::id())->first();
		$getGudang = AksesGudang::join('gudang', 'gudang.id', '=', 'akses_gudang.id_gudang')->where('akses_gudang.id_user', \Auth::id())->where('akses_gudang.akses', 1)->where('gudang.deleted', 0)->select('gudang.kode')->get()->toArray();
		return SuratJalan::join('custsupp', 'custsupp.kode', '=', 'surat_jalan.customer')->where('surat_jalan.deleted', 0)->whereIn('surat_jalan.gudang', $getGudang)->
			whereMonth('surat_jalan.tanggal', '=', $periode->bulan)->whereYear('surat_jalan.tanggal', '=', $periode->tahun)->select('surat_jalan.*', 'custsupp.nama')->orderBy('surat_jalan.no_bukti', 'DESC')->get();
	}

	public function getSumQty(Request $req) {
		$periode = Periode::where('id_user', \Auth::id())->first();
		return StockBarang::where('kode_barang', $req->input('kode_barang'))->where('gudang', $req->input('gudang'))->where('bulan', $periode->bulan)->where('tahun', $periode->tahun)->selectRaw('akhir_qty')->first();
	}

	public function generateNomorBukti() {
		$periode = Periode::where('id_user', \Auth::id())->first();
		$nomor = Nomor::where('id', 1)->first();
		$str = "";
		$nourut = "";

		if ($nomor->reset == 1) {
			$getCount = SuratJalan::whereMonth('tanggal', '=', $periode->bulan)->whereYear('tanggal', '=', $periode->tahun)->count();
			$nourut = (string)($getCount + 1);
		} else {
			$getCount = SuratJalan::whereYear('tanggal', '=', $periode->tahun)->count();
			$nourut = (string)($getCount + 1);
		}

		$nourut = str_pad($nourut, 4, "0", STR_PAD_LEFT);

		if ($nomor->format1 == 1) { $str .= $nomor->inisial_perusahaan; }
		else if ($nomor->format1 == 2) { $str .= $nomor->surat_jalan; }
		else if ($nomor->format1 == 3) { $str .= $nourut; }
		else if ($nomor->format1 == 4) { $str .= str_pad($periode->bulan, 2, "0", STR_PAD_LEFT).substr($periode->tahun, -2); }
		else if ($nomor->format1 == 5) { $str .= str_pad($periode->bulan, 2, "0", STR_PAD_LEFT).$periode->tahun; }
		else if ($nomor->format1 == 6) { $str .= substr($periode->tahun, -2).str_pad($periode->bulan, 2, "0", STR_PAD_LEFT); }
		else if ($nomor->format1 == 7) { $str .= $periode->tahun.str_pad($periode->bulan, 2, "0", STR_PAD_LEFT); }
		$str .= $nomor->pemisah;
		if ($nomor->format2 == 1) { $str .= $nomor->inisial_perusahaan; }
		else if ($nomor->format2 == 2) { $str .= $nomor->surat_jalan; }
		else if ($nomor->format2 == 3) { $str .= $nourut; }
		else if ($nomor->format2 == 4) { $str .= str_pad($periode->bulan, 2, "0", STR_PAD_LEFT).substr($periode->tahun, -2); }
		else if ($nomor->format2 == 5) { $str .= str_pad($periode->bulan, 2, "0", STR_PAD_LEFT).$periode->tahun; }
		else if ($nomor->format2 == 6) { $str .= substr($periode->tahun, -2).str_pad($periode->bulan, 2, "0", STR_PAD_LEFT); }
		else if ($nomor->format2 == 7) { $str .= $periode->tahun.str_pad($periode->bulan, 2, "0", STR_PAD_LEFT); }
		$str .= $nomor->pemisah;
		if ($nomor->format3 == 1) { $str .= $nomor->inisial_perusahaan; }
		else if ($nomor->format3 == 2) { $str .= $nomor->surat_jalan; }
		else if ($nomor->format3 == 3) { $str .= $nourut; }
		else if ($nomor->format3 == 4) { $str .= str_pad($periode->bulan, 2, "0", STR_PAD_LEFT).substr($periode->tahun, -2); }
		else if ($nomor->format3 == 5) { $str .= str_pad($periode->bulan, 2, "0", STR_PAD_LEFT).$periode->tahun; }
		else if ($nomor->format3 == 6) { $str .= substr($periode->tahun, -2).str_pad($periode->bulan, 2, "0", STR_PAD_LEFT); }
		else if ($nomor->format3 == 7) { $str .= $periode->tahun.str_pad($periode->bulan, 2, "0", STR_PAD_LEFT); }
		$str .= $nomor->pemisah;
		if ($nomor->format4 == 1) { $str .= $nomor->inisial_perusahaan; }
		else if ($nomor->format4 == 2) { $str .= $nomor->surat_jalan; }
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
			$getCount = SuratJalan::whereMonth('tanggal', '=', $periode->bulan)->whereYear('tanggal', '=', $periode->tahun)->count();
			$nourut = (string)($getCount + 1);
		} else {
			$getCount = SuratJalan::whereYear('tanggal', '=', $periode->tahun)->count();
			$nourut = (string)($getCount + 1);
		}

		$nourut = str_pad($nourut, 4, "0", STR_PAD_LEFT);
		return $nourut;
	}

	public function add(Request $req) {
		$count = SuratJalan::where('no_bukti', $req->input('no_bukti'))->count();
		$getGudangTransit = Gudang::where('transit', 1)->where('deleted', 0)->first();
		if ($count == 0) {
			$detail = $req->input('barang');
			$getSO = SO::where('no_bukti', $req->input('no_so'))->first();
			for ($i = 0; $i < count($detail); $i++) {
				$prevDetSO = DetailSO::where('id_so', $getSO->id)->where('kode_barang', $detail[$i][0])->where('satuan', $detail[$i][2])->where('foc', $detail[$i][6])->first();
				if ($prevDetSO->qty_kirim - $prevDetSO->qty_retur + $detail[$i][5] > $prevDetSO->qty) {
					return $detail[$i][0];
				}
			}
			$newSJ = SuratJalan::create([
				'no_urut' => $req->input('no_urut'),
				'no_bukti' => $req->input('no_bukti'),
				'tanggal' => $req->input('tanggal'),
				'tanggal_kirim' => $req->input('tanggal_kirim'),
				'keterangan' => $req->input('keterangan'),
				'no_so' => $req->input('no_so'),
				'customer' => $req->input('customer'),
				'alamat' => $req->input('alamat'),
				'gudang' => $req->input('gudang')
			]);
			for ($i = 0; $i < count($detail); $i++) {
				$prevDetSO = DetailSO::where('id_so', $getSO->id)->where('kode_barang', $detail[$i][0])->where('satuan', $detail[$i][2])->where('foc', $detail[$i][6])->first();
				DetailSO::where('id_so', $getSO->id)->where('kode_barang', $detail[$i][0])->where('satuan', $detail[$i][2])->where('foc', $detail[$i][6])->update([
					'qty_kirim' => $prevDetSO->qty_kirim + $detail[$i][5]
				]);

				$gettipebrg = BahanBarang::where('kode', $detail[$i][0])->first();
				if ($gettipebrg->grup != "JS") {
					$oldStock = StockBarang::where('bulan', (int)date('m', strtotime($getSO->tanggal)))->where('tahun', (int)date('Y', strtotime($getSO->tanggal)))->
						where('kode_barang', $detail[$i][0])->where('gudang', $getSO->gudang)->first();
						StockBarang::where('bulan', (int)date('m', strtotime($getSO->tanggal)))->where('tahun', (int)date('Y', strtotime($getSO->tanggal)))->
							where('kode_barang', $detail[$i][0])->where('gudang', $getSO->gudang)->update([
						'so' => $oldStock->so - ($detail[$i][5] * $detail[$i][3])
					]);
				}

				$dpp = ($detail[$i][5] * ($prevDetSO->harga - $prevDetSO->diskon_rp)) * (100 - $getSO->diskon_persen) / 100;
				$ppn = 0;
				$nnet = $dpp;
				if ($getSO->tipe_ppn == 1) {
					$dpp = $dpp * 10 / 11;
					$ppn = $dpp * 0.1;
				} else if ($getSO->tipe_ppn == 2) {
					$ppn = $dpp * 0.1;
					$nnet = $dpp + $ppn;
				}
				DetailSuratJalan::create([
					'id_sj' => $newSJ->id,
					'kode_barang' => $detail[$i][0],
					'nama_barang' => $detail[$i][1],
					'qty_kirim' => $detail[$i][5],
					'satuan' => $detail[$i][2],
					'isi' => $detail[$i][3],
					'foc' => $detail[$i][6],
					'dpp' => $dpp,
					'dpp_rp' => $dpp * $getSO->kurs,
					'ppn' => $ppn,
					'ppn_rp' => $ppn * $getSO->kurs,
					'nnet' => $nnet,
					'nnet_rp' => $nnet * $getSO->kurs
				]);

				$gettipebrg = BahanBarang::where('kode', $detail[$i][0])->first();
				if ($gettipebrg->grup != "JS") {
					// update gudang transit
					$cek_jml = StockBarang::where('bulan', (int)date('m', strtotime($req->input('tanggal'))))->where('tahun', (int)date('Y', strtotime($req->input('tanggal'))))->
						where('kode_barang', $detail[$i][0])->where('gudang', $getGudangTransit->kode)->count();
					if ($cek_jml == 0) {
						$bulanlalu = (int)date('m', strtotime($req->input('tanggal'))) - 1;
						$taun = (int)date('Y', strtotime($req->input('tanggal')));
						if ($bulanlalu == 0) {
							$bulanlalu = 12;
							$taun = $taun - 1;
						}
						$cekstock = StockBarang::where('bulan', $bulanlalu)->where('tahun', $taun)->where('kode_barang', $detail[$i][0])->where('gudang', $getGudangTransit->kode)->count();
						if ($cekstock != 0) {
							$saldoAwal = StockBarang::where('bulan', $bulanlalu)->where('tahun', $taun)->where('kode_barang', $detail[$i][0])->where('gudang', $getGudangTransit->kode)->first();
							StockBarang::create([
								'bulan' => (int)date('m', strtotime($req->input('tanggal'))),
								'tahun' => (int)date('Y', strtotime($req->input('tanggal'))),
								'kode_barang' => $detail[$i][0],
								'gudang' => $getGudangTransit->kode,
								'awal_qty' => $saldoAwal->akhir_qty,
								'awal_harga' => $saldoAwal->akhir_harga,
								'qty' => $detail[$i][5] * $detail[$i][3]
							]);
						} else {
							StockBarang::create([
								'bulan' => (int)date('m', strtotime($req->input('tanggal'))),
								'tahun' => (int)date('Y', strtotime($req->input('tanggal'))),
								'kode_barang' => $detail[$i][0],
								'gudang' => $getGudangTransit->kode,
								'qty' => $detail[$i][5] * $detail[$i][3]
							]);
						}
					}
					else {
						$oldStock = StockBarang::where('bulan', (int)date('m', strtotime($req->input('tanggal'))))->where('tahun', (int)date('Y', strtotime($req->input('tanggal'))))->
							where('kode_barang', $detail[$i][0])->where('gudang', $getGudangTransit->kode)->first();
						StockBarang::where('bulan', (int)date('m', strtotime($req->input('tanggal'))))->where('tahun', (int)date('Y', strtotime($req->input('tanggal'))))->
							where('kode_barang', $detail[$i][0])->where('gudang', $getGudangTransit->kode)->update([
							'qty' => $oldStock->qty + ($detail[$i][5] * $detail[$i][3])
						]);
					}

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
								'qty_jual' => $detail[$i][5] * $detail[$i][3]
								// 'harga_jual' => ($detail[$i][5] * ($prevDetSO->harga - $prevDetSO->diskon_rp) * $getSO->kurs)
							]);
						} else {
							StockBarang::create([
								'bulan' => (int)date('m', strtotime($req->input('tanggal'))),
								'tahun' => (int)date('Y', strtotime($req->input('tanggal'))),
								'kode_barang' => $detail[$i][0],
								'gudang' => $req->input('gudang'),
								'qty_jual' => $detail[$i][5] * $detail[$i][3]
								// 'harga_jual' => ($detail[$i][5] * ($prevDetSO->harga - $prevDetSO->diskon_rp) * $getSO->kurs)
							]);
						}
					}
					else {
						$oldStock = StockBarang::where('bulan', (int)date('m', strtotime($req->input('tanggal'))))->where('tahun', (int)date('Y', strtotime($req->input('tanggal'))))->
							where('kode_barang', $detail[$i][0])->where('gudang', $req->input('gudang'))->first();
						StockBarang::where('bulan', (int)date('m', strtotime($req->input('tanggal'))))->where('tahun', (int)date('Y', strtotime($req->input('tanggal'))))->
							where('kode_barang', $detail[$i][0])->where('gudang', $req->input('gudang'))->update([
							'qty_jual' => $oldStock->qty_jual + ($detail[$i][5] * $detail[$i][3])
							// 'harga_jual' => $oldStock->harga_jual + ($detail[$i][5] * ($prevDetSO->harga - $prevDetSO->diskon_rp) * $getSO->kurs)
						]);
					}
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
					Batch::create([
						'no_bukti' => $req->input('no_bukti'),
						'no_batch' => $batch[$i][0],
						'urut' => $batch[$i][5],
						'gudang' => $getGudangTransit->kode,
						'kode_barang' => $batch[$i][1],
						'tanggal' => $batch[$i][2],
						'qty' => $batch[$i][4] * $batch[$i][6]
					]);
				}
			}

			$sisa = 0;
			$getDetSO = DetailSO::where('id_so', $getSO->id)->get();
			for ($i = 0; $i < count($getDetSO); $i++) {
				$sisa += $getDetSO[$i]->qty - $getDetSO[$i]->qty_kirim + $getDetSO[$i]->qty_retur;
			}
			if ($sisa <= 0) {
				SO::where('no_bukti', $req->input('no_so'))->update(['done' => 1]);
			} else {
				SO::where('no_bukti', $req->input('no_so'))->update(['done' => 0]);
			}
			$this->logActivity('ADD', \Auth::id(), $newSJ->no_bukti);
			return "1;;".$newSJ->id.";;".$newSJ->no_bukti;
		} else {
			$detail = $req->input('barang');
			for ($i = 0; $i < count($detail); $i++) {
				$prevDetSO = DetailSO::where('id_so', $getSO->id)->where('kode_barang', $detail[$i][0])->where('satuan', $detail[$i][2])->where('foc', $detail[$i][6])->first();
				if ($prevDetSO->qty_kirim - $prevDetSO->qty_retur + $detail[$i][5] > $prevDetSO->qty) {
					return $detail[$i][0];
				}
			}
			return "0";
		}
	}

	public function changeAuth(Request $req) {
		$getUser = User::where('id', \Auth::id())->first();
		SuratJalan::where('id', $req->input('id'))->update([
			'auth_1' => 1,
			'auth_user_1' => $getUser->name,
			'auth_date_1' => now(),
			'batal' => 0,
			'batal_user' => "",
			'batal_date' => null
		]);
    $getNoBukti = SuratJalan::where('id', $req->input('id'))->first();
    $this->logActivity('OTO', \Auth::id(), $getNoBukti->no_bukti);
	}

	public function changeBatal(Request $req) {
		$getUser = User::where('id', \Auth::id())->first();
		SuratJalan::where('id', $req->input('id'))->update([
			'auth_1' => 0,
			'auth_user_1' => '',
			'auth_date_1' => null,
			'batal' => 1,
			'batal_user' => $getUser->name,
			'batal_date' => now(),
		]);
    $getNoBukti = SuratJalan::where('id', $req->input('id'))->first();
    $this->logActivity('BTL', \Auth::id(), $getNoBukti->no_bukti);
	}

	public function show(Request $req) {
		return SuratJalan::where('id', $req->input('id'))->first();
	}

	public function showByNoBukti(Request $req) {
		return SuratJalan::where('no_bukti', $req->input('no_bukti'))->first();
	}

	public function showDet(Request $req) {
		return DetailSuratJalan::where('id_sj', $req->input('id'))->get();
	}

	public function showDetByNoBukti(Request $req) {
		$getSJ = SuratJalan::where('no_bukti', $req->input('no_bukti'))->first();
		return DetailSuratJalan::where('id_sj', $getSJ->id)->get();
	}

	public function edit(Request $req) {
		$getSO = SO::where('no_bukti', $req->input('no_so'))->first();
		$getSJ = SuratJalan::where('id', $req->input('id'))->first();
		$getDetSJ = DetailSuratJalan::where('id_sj', $req->input('id'))->get();
		$getGudangTransit = Gudang::where('transit', 1)->where('deleted', 0)->first();
		for ($i = 0; $i < count($getDetSJ); $i++) {
			$getPrevDetSO = DetailSO::where('id_so', $getSO->id)->where('kode_barang', $getDetSJ[$i]->kode_barang)->where('satuan', $getDetSJ[$i]->satuan)->where('foc', $getDetSJ[$i]->foc)->first();
			DetailSO::where('id_so', $getSO->id)->where('kode_barang', $getDetSJ[$i]->kode_barang)->where('satuan', $getDetSJ[$i]->satuan)->where('foc', $getDetSJ[$i]->foc)->update([
					'qty_kirim' => $getPrevDetSO->qty_kirim - $getDetSJ[$i]->qty_kirim,
					'qty_retur' => $getPrevDetSO->qty_retur - $getDetSJ[$i]->qty_retur
			]);

			$gettipebrg = BahanBarang::where('kode', $getDetSJ[$i]->kode_barang)->first();
			if ($gettipebrg->grup != "JS") {
				// update gudang transit
				$oldStock = StockBarang::where('bulan', (int)date('m', strtotime($getSJ->tanggal)))->where('tahun', (int)date('Y', strtotime($getSJ->tanggal)))->
					where('kode_barang', $getDetSJ[$i]->kode_barang)->where('gudang', $getGudangTransit->kode)->first();
				StockBarang::where('bulan', (int)date('m', strtotime($getSJ->tanggal)))->where('tahun', (int)date('Y', strtotime($getSJ->tanggal)))->
					where('kode_barang', $getDetSJ[$i]->kode_barang)->where('gudang', $getGudangTransit->kode)->update([
					'qty' => $oldStock->qty - (($getDetSJ[$i]->qty_kirim - $getDetSJ[$i]->qty_retur) * $getDetSJ[$i]->isi)
				]);

				// update stok barang
				$oldStock = StockBarang::where('bulan', (int)date('m', strtotime($getSJ->tanggal)))->where('tahun', (int)date('Y', strtotime($getSJ->tanggal)))->
					where('kode_barang', $getDetSJ[$i]->kode_barang)->where('gudang', $getSJ->gudang)->first();
				StockBarang::where('bulan', (int)date('m', strtotime($getSJ->tanggal)))->where('tahun', (int)date('Y', strtotime($getSJ->tanggal)))->
					where('kode_barang', $getDetSJ[$i]->kode_barang)->where('gudang', $getSJ->gudang)->update([
					'qty_jual' => $oldStock->qty_jual - (($getDetSJ[$i]->qty_kirim - $getDetSJ[$i]->qty_retur) * $getDetSJ[$i]->isi)
					// 'harga_jual' => $oldStock->harga_jual - (($getDetSJ[$i]->qty_kirim - $getDetSJ[$i]->qty_retur) * ($getPrevDetSO->harga - $getPrevDetSO->diskon_rp)) * $getSO->kurs
				]);

				$oldStock = StockBarang::where('bulan', (int)date('m', strtotime($getSO->tanggal)))->where('tahun', (int)date('Y', strtotime($getSO->tanggal)))->
					where('kode_barang', $getDetSJ[$i]->kode_barang)->where('gudang', $getSO->gudang)->first();
					StockBarang::where('bulan', (int)date('m', strtotime($getSO->tanggal)))->where('tahun', (int)date('Y', strtotime($getSO->tanggal)))->
						where('kode_barang', $getDetSJ[$i]->kode_barang)->where('gudang', $getSO->gudang)->update([
					'so' => $oldStock->so + (($getDetSJ[$i]->qty_kirim - $getDetSJ[$i]->qty_retur) * $getDetSJ[$i]->isi)
				]);
			}
		}
		$detail = $req->input('barang');
		for ($a = 0; $a < count($detail); $a++) {
			$prevDetSO = DetailSO::where('id_so', $getSO->id)->where('kode_barang', $detail[$a][0])->where('satuan', $detail[$a][2])->where('foc', $detail[$a][7])->first();
			if ($prevDetSO->qty_kirim - $prevDetSO->qty_retur + $detail[$a][5] - $detail[$a][6] > $prevDetSO->qty) {
				for ($i = 0; $i < count($getDetSJ); $i++) {
					$getPrevDetSO = DetailSO::where('id_so', $getSO->id)->where('kode_barang', $getDetSJ[$i]->kode_barang)->where('satuan', $getDetSJ[$i]->satuan)->where('foc', $getDetSJ[$i]->foc)->first();
					DetailSO::where('id_so', $getSO->id)->where('kode_barang', $getDetSJ[$i]->kode_barang)->where('satuan', $getDetSJ[$i]->satuan)->where('foc', $getDetSJ[$i]->foc)->update([
							'qty_kirim' => $getPrevDetSO->qty_kirim + $getDetSJ[$i]->qty_kirim,
							'qty_retur' => $getPrevDetSO->qty_retur + $getDetSJ[$i]->qty_retur
					]);

					$gettipebrg = BahanBarang::where('kode', $getDetSJ[$i]->kode_barang)->first();
					if ($gettipebrg->grup != "JS") {
						// update gudang transit
						$oldStock = StockBarang::where('bulan', (int)date('m', strtotime($getSJ->tanggal)))->where('tahun', (int)date('Y', strtotime($getSJ->tanggal)))->
							where('kode_barang', $getDetSJ[$i]->kode_barang)->where('gudang', $getGudangTransit->kode)->first();
						StockBarang::where('bulan', (int)date('m', strtotime($getSJ->tanggal)))->where('tahun', (int)date('Y', strtotime($getSJ->tanggal)))->
							where('kode_barang', $getDetSJ[$i]->kode_barang)->where('gudang', $getGudangTransit->kode)->update([
							'qty' => $oldStock->qty + (($getDetSJ[$i]->qty_kirim - $getDetSJ[$i]->qty_retur) * $getDetSJ[$i]->isi)
						]);

						// update stok barang
						$oldStock = StockBarang::where('bulan', (int)date('m', strtotime($getSJ->tanggal)))->where('tahun', (int)date('Y', strtotime($getSJ->tanggal)))->
							where('kode_barang', $getDetSJ[$i]->kode_barang)->where('gudang', $getSJ->gudang)->first();
						StockBarang::where('bulan', (int)date('m', strtotime($getSJ->tanggal)))->where('tahun', (int)date('Y', strtotime($getSJ->tanggal)))->
							where('kode_barang', $getDetSJ[$i]->kode_barang)->where('gudang', $getSJ->gudang)->update([
							'qty_jual' => $oldStock->qty_jual + (($getDetSJ[$i]->qty_kirim - $getDetSJ[$i]->qty_retur) * $getDetSJ[$i]->isi)
							// 'harga_jual' => $oldStock->harga_jual - (($getDetSJ[$i]->qty_kirim - $getDetSJ[$i]->qty_retur) * ($getPrevDetSO->harga - $getPrevDetSO->diskon_rp)) * $getSO->kurs
						]);

						$oldStock = StockBarang::where('bulan', (int)date('m', strtotime($getSO->tanggal)))->where('tahun', (int)date('Y', strtotime($getSO->tanggal)))->
							where('kode_barang', $getDetSJ[$i]->kode_barang)->where('gudang', $getSO->gudang)->first();
							StockBarang::where('bulan', (int)date('m', strtotime($getSO->tanggal)))->where('tahun', (int)date('Y', strtotime($getSO->tanggal)))->
								where('kode_barang', $getDetSJ[$i]->kode_barang)->where('gudang', $getSO->gudang)->update([
							'so' => $oldStock->so - (($getDetSJ[$i]->qty_kirim - $getDetSJ[$i]->qty_retur) * $getDetSJ[$i]->isi)
						]);
					}
				}
				return $detail[$a][0];
			}
		}
		DetailSuratJalan::where('id_sj', $req->input('id'))->delete();
		Batch::where('no_bukti', $req->input('no_bukti'))->delete();

		SuratJalan::where('id', $req->input('id'))->update([
			'tanggal' => $req->input('tanggal'),
			'tanggal_kirim' => $req->input('tanggal_kirim'),
			'keterangan' => $req->input('keterangan'),
			'alamat' => $req->input('alamat'),
			'gudang' => $req->input('gudang')
		]);
		$detail = $req->input('barang');
		for ($i = 0; $i < count($detail); $i++) {
			$prevDetSO = DetailSO::where('id_so', $getSO->id)->where('kode_barang', $detail[$i][0])->where('satuan', $detail[$i][2])->where('foc', $detail[$i][7])->first();
			DetailSO::where('id_so', $getSO->id)->where('kode_barang', $detail[$i][0])->where('satuan', $detail[$i][2])->where('foc', $detail[$i][7])->update([
				'qty_kirim' => $prevDetSO->qty_kirim + $detail[$i][5],
				'qty_retur' => $prevDetSO->qty_retur + $detail[$i][6],
			]);

			$gettipebrg = BahanBarang::where('kode', $detail[$i][0])->first();
			if ($gettipebrg->grup != "JS") {
				$oldStock = StockBarang::where('bulan', (int)date('m', strtotime($getSO->tanggal)))->where('tahun', (int)date('Y', strtotime($getSO->tanggal)))->
					where('kode_barang', $detail[$i][0])->where('gudang', $getSO->gudang)->first();
					StockBarang::where('bulan', (int)date('m', strtotime($getSO->tanggal)))->where('tahun', (int)date('Y', strtotime($getSO->tanggal)))->
						where('kode_barang', $detail[$i][0])->where('gudang', $getSO->gudang)->update([
					'so' => $oldStock->so - (($detail[$i][5] - $detail[$i][6]) * $detail[$i][3])
				]);
			}

			$dpp = (($detail[$i][5] - $detail[$i][6]) * ($prevDetSO->harga - $prevDetSO->diskon_rp)) * (100 - $getSO->diskon_persen) / 100;
			$ppn = 0;
			$nnet = $dpp;
			if ($getSO->tipe_ppn == 1) {
				$dpp = $dpp * 10 / 11;
				$ppn = $dpp * 0.1;
			} else if ($getSO->tipe_ppn == 2) {
				$ppn = $dpp * 0.1;
				$nnet = $dpp + $ppn;
			}
			DetailSuratJalan::create([
				'id_sj' => $req->input('id'),
				'kode_barang' => $detail[$i][0],
				'nama_barang' => $detail[$i][1],
				'qty_kirim' => $detail[$i][5],
				'qty_retur' => $detail[$i][6],
				'satuan' => $detail[$i][2],
				'isi' => $detail[$i][3],
				'foc' => $detail[$i][7],
				'dpp' => $dpp,
				'dpp_rp' => $dpp * $getSO->kurs,
				'ppn' => $ppn,
				'ppn_rp' => $ppn * $getSO->kurs,
				'nnet' => $nnet,
				'nnet_rp' => $nnet * $getSO->kurs
			]);

			$gettipebrg = BahanBarang::where('kode', $detail[$i][0])->first();
			if ($gettipebrg->grup != "JS") {
				// update gudang transit
				$cek_jml = StockBarang::where('bulan', (int)date('m', strtotime($req->input('tanggal'))))->where('tahun', (int)date('Y', strtotime($req->input('tanggal'))))->
					where('kode_barang', $detail[$i][0])->where('gudang', $getGudangTransit->kode)->count();
				if ($cek_jml == 0) {
					$bulanlalu = (int)date('m', strtotime($req->input('tanggal'))) - 1;
					$taun = (int)date('Y', strtotime($req->input('tanggal')));
					if ($bulanlalu == 0) {
						$bulanlalu = 12;
						$taun = $taun - 1;
					}
					$cekstock = StockBarang::where('bulan', $bulanlalu)->where('tahun', $taun)->where('kode_barang', $detail[$i][0])->where('gudang', $getGudangTransit->kode)->count();
					if ($cekstock != 0) {
						$saldoAwal = StockBarang::where('bulan', $bulanlalu)->where('tahun', $taun)->where('kode_barang', $detail[$i][0])->where('gudang', $getGudangTransit->kode)->first();
						StockBarang::create([
							'bulan' => (int)date('m', strtotime($req->input('tanggal'))),
							'tahun' => (int)date('Y', strtotime($req->input('tanggal'))),
							'kode_barang' => $detail[$i][0],
							'gudang' => $getGudangTransit->kode,
							'awal_qty' => $saldoAwal->akhir_qty,
							'awal_harga' => $saldoAwal->akhir_harga,
							'qty' => ($detail[$i][5] - $detail[$i][6]) * $detail[$i][3]
						]);
					} else {
						StockBarang::create([
							'bulan' => (int)date('m', strtotime($req->input('tanggal'))),
							'tahun' => (int)date('Y', strtotime($req->input('tanggal'))),
							'kode_barang' => $detail[$i][0],
							'gudang' => $getGudangTransit->kode,
							'qty' => ($detail[$i][5] - $detail[$i][6]) * $detail[$i][3]
						]);
					}
				}
				else {
					$oldStock = StockBarang::where('bulan', (int)date('m', strtotime($req->input('tanggal'))))->where('tahun', (int)date('Y', strtotime($req->input('tanggal'))))->
						where('kode_barang', $detail[$i][0])->where('gudang', $getGudangTransit->kode)->first();
					StockBarang::where('bulan', (int)date('m', strtotime($req->input('tanggal'))))->where('tahun', (int)date('Y', strtotime($req->input('tanggal'))))->
						where('kode_barang', $detail[$i][0])->where('gudang', $getGudangTransit->kode)->update([
						'qty' => $oldStock->qty + ($detail[$i][5] - $detail[$i][6]) * $detail[$i][3]
					]);
				}

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
							'qty_jual' => ($detail[$i][5] - $detail[$i][6]) * $detail[$i][3]
							// 'harga_jual' => (($detail[$i][5] - $detail[$i][6]) * ($prevDetSO->harga - $prevDetSO->diskon_rp) * $getSO->kurs)
						]);
					} else {
						StockBarang::create([
							'bulan' => (int)date('m', strtotime($req->input('tanggal'))),
							'tahun' => (int)date('Y', strtotime($req->input('tanggal'))),
							'kode_barang' => $detail[$i][0],
							'gudang' => $req->input('gudang'),
							'qty_jual' => ($detail[$i][5] - $detail[$i][6]) * $detail[$i][3]
							// 'harga_jual' => (($detail[$i][5] - $detail[$i][6]) * ($prevDetSO->harga - $prevDetSO->diskon_rp) * $getSO->kurs)
						]);
					}
				}
				else {
					$oldStock = StockBarang::where('bulan', (int)date('m', strtotime($req->input('tanggal'))))->where('tahun', (int)date('Y', strtotime($req->input('tanggal'))))->
						where('kode_barang', $detail[$i][0])->where('gudang', $req->input('gudang'))->first();
					StockBarang::where('bulan', (int)date('m', strtotime($req->input('tanggal'))))->where('tahun', (int)date('Y', strtotime($req->input('tanggal'))))->
						where('kode_barang', $detail[$i][0])->where('gudang', $req->input('gudang'))->update([
						'qty_jual' => $oldStock->qty_jual + ($detail[$i][5] - $detail[$i][6]) * $detail[$i][3]
						// 'harga_jual' => $oldStock->harga_jual + (($detail[$i][5] - $detail[$i][6]) * ($prevDetSO->harga - $prevDetSO->diskon_rp) * $getSO->kurs)
					]);
				}
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

				Batch::create([
					'no_bukti' => $req->input('no_bukti'),
					'no_batch' => $batch[$i][0],
					'urut' => $batch[$i][5],
					'gudang' => $getGudangTransit->kode,
					'kode_barang' => $batch[$i][1],
					'tanggal' => $batch[$i][2],
					'qty' => $batch[$i][4] * $batch[$i][6]
				]);
			}
		}

		$sisa = 0;
		$getDetSO = DetailSO::where('id_so', $getSO->id)->get();
		for ($i = 0; $i < count($getDetSO); $i++) {
			$sisa += $getDetSO[$i]->qty - $getDetSO[$i]->qty_kirim + $getDetSO[$i]->qty_retur;
		}
		if ($sisa <= 0) {
			SO::where('no_bukti', $req->input('no_so'))->update(['done' => 1]);
		} else {
			SO::where('no_bukti', $req->input('no_so'))->update(['done' => 0]);
		}

		$this->logActivity('UPD', \Auth::id(), $getSJ->no_bukti);
		return 1;
	}

	public function erase(Request $req) {
		$getSJ = SuratJalan::where('id', $req->input('id'))->first();
		$getSO = SO::where('no_bukti', $getSJ->no_so)->first();
		$getGudangTransit = Gudang::where('transit', 1)->where('deleted', 0)->first();

		$getDetSJ = DetailSuratJalan::where('id_sj', $req->input('id'))->get();
		for ($i = 0; $i < count($getDetSJ); $i++) {
			$getPrevDetSO = DetailSO::where('id_so', $getSO->id)->where('kode_barang', $getDetSJ[$i]->kode_barang)->where('satuan', $getDetSJ[$i]->satuan)->where('foc', $getDetSJ[$i]->foc)->first();
			DetailSO::where('id_so', $getSO->id)->where('kode_barang', $getDetSJ[$i]->kode_barang)->where('satuan', $getDetSJ[$i]->satuan)->where('foc', $getDetSJ[$i]->foc)->update([
					'qty_kirim' => $getPrevDetSO->qty_kirim - $getDetSJ[$i]->qty_kirim,
					'qty_retur' => $getPrevDetSO->qty_retur - $getDetSJ[$i]->qty_retur
			]);

			$gettipebrg = BahanBarang::where('kode', $getDetSJ[$i]->kode_barang)->first();
			if ($gettipebrg->grup != "JS") {
				// update gudang transit
				$oldStock = StockBarang::where('bulan', (int)date('m', strtotime($getSJ->tanggal)))->where('tahun', (int)date('Y', strtotime($getSJ->tanggal)))->
					where('kode_barang', $getDetSJ[$i]->kode_barang)->where('gudang', $getGudangTransit->kode)->first();
				StockBarang::where('bulan', (int)date('m', strtotime($getSJ->tanggal)))->where('tahun', (int)date('Y', strtotime($getSJ->tanggal)))->
					where('kode_barang', $getDetSJ[$i]->kode_barang)->where('gudang', $getGudangTransit->kode)->update([
					'qty' => $oldStock->qty - (($getDetSJ[$i]->qty_kirim - $getDetSJ[$i]->qty_retur) * $getDetSJ[$i]->isi)
				]);

				// update stok barang
				$oldStock = StockBarang::where('bulan', (int)date('m', strtotime($getSJ->tanggal)))->where('tahun', (int)date('Y', strtotime($getSJ->tanggal)))->
					where('kode_barang', $getDetSJ[$i]->kode_barang)->where('gudang', $getSJ->gudang)->first();
				StockBarang::where('bulan', (int)date('m', strtotime($getSJ->tanggal)))->where('tahun', (int)date('Y', strtotime($getSJ->tanggal)))->
					where('kode_barang', $getDetSJ[$i]->kode_barang)->where('gudang', $getSJ->gudang)->update([
					'qty_jual' => $oldStock->qty_jual - (($getDetSJ[$i]->qty_kirim - $getDetSJ[$i]->qty_retur) * $getDetSJ[$i]->isi)
					// 'harga_jual' => $oldStock->harga_jual - (($getDetSJ[$i]->qty_kirim - $getDetSJ[$i]->qty_retur) * ($getPrevDetSO->harga - $getPrevDetSO->diskon_rp) * $getSO->kurs)
				]);

				$oldStock = StockBarang::where('bulan', (int)date('m', strtotime($getSO->tanggal)))->where('tahun', (int)date('Y', strtotime($getSO->tanggal)))->
					where('kode_barang', $getDetSJ[$i]->kode_barang)->where('gudang', $getSO->gudang)->first();
					StockBarang::where('bulan', (int)date('m', strtotime($getSO->tanggal)))->where('tahun', (int)date('Y', strtotime($getSO->tanggal)))->
						where('kode_barang', $getDetSJ[$i]->kode_barang)->where('gudang', $getSO->gudang)->update([
					'so' => $oldStock->so + (($getDetSJ[$i]->qty_kirim - $getDetSJ[$i]->qty_retur) * $getDetSJ[$i]->isi)
				]);
			}
		}
		DetailSuratJalan::where('id_sj', $req->input('id'))->delete();
		Batch::where('no_bukti', $getSJ->no_bukti)->delete();

		$sisa = 0;
		$getDetSO = DetailSO::where('id_so', $getSO->id)->get();
		for ($i = 0; $i < count($getDetSO); $i++) {
			$sisa += $getDetSO[$i]->qty - $getDetSO[$i]->qty_kirim + $getDetSO[$i]->qty_retur;
		}
		if ($sisa <= 0) {
			SO::where('no_bukti', $getSJ->no_so)->update(['done' => 1]);
		} else {
			SO::where('no_bukti', $getSJ->no_so)->update(['done' => 0]);
		}

    SuratJalan::where('id', $req->input('id'))->delete();
		$this->logActivity('DEL', \Auth::id(), $getSJ->no_bukti);
	}

}

?>
