<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Fluent;
use App\Models\NewPeriode;

use Carbon\Carbon;use Illuminate\Support\Arr;

trait AksesTrait {
	public function cekAkses($href) {
		// Sesi habis / user sudah logout: jangan sentuh Auth::user() (null),
		// cukup kembalikan flag supaya controller me-redirect ke halaman login.
		if (!\Auth::check()) {
			return array('userLoggedOut' => true);
		}

		$akses = array('userLoggedOut' => false);

    	// $periode = NewPeriode::where('USERID' , \Auth::User()->username)->first();
		$periode = collect(
		    DB::connection('SML')->select(
		        'select top 1 bulan, tahun from DBPERIODE where user_id = ?',
		        [\Auth::user()->username]
		    )
		)->first();

		// dbmenureportweb is the live report-menu table now; DBMENUREPORT and its
		// per-user permission table DBFLMENUREPORT are retired (DBFLMENUREPORT.L1
		// still uses DBMENUREPORT's old numbering). Hak akses report web per user
		// sekarang dibaca dari DBFLMENUREPORTWEB (diisi lewat Berkas > Set Pemakai >
		// Akses Report) - lihat blok $href != "Home" di bawah.
		$menul0 = $this->toFluentMenuTree(
			app('App\Http\Controllers\HomeController')->getReportMenuTreeArray()
		);

		$program = DB::connection('SML')->select('select * from DBPERUSAHAAN');
		$akses = Arr::add($akses, 'program', $program[0]->NAMA);
		$akses = Arr::add($akses, 'href', $href);
		$akses = Arr::add($akses, 'user', \Auth::User()->username);

		if ($href != "Home") {
			$menu = DB::connection('SML')->selectOne(
				'select KODEMENU, Keterangan from dbmenureportweb where href = ?',
				[$href]
			);
			// $akses = Arr::add($akses, 'akses', new Fluent(['Access' => true, 'IsDesign' => true, 'Isexport' => true]));

			// Report yang terdaftar di dbmenureportweb: hak akses diambil dari
			// DBFLMENUREPORTWEB milik user. Belum ada baris = belum diberi akses.
			// Report yang href-nya belum terdaftar di dbmenureportweb tidak punya kode
			// menu, jadi tidak bisa dicentang di Set Pemakai - tetap dibuka untuk semua
			// user seperti sebelumnya, supaya halamannya tidak tertutup untuk siapa pun.
			$hak = ['Access' => true, 'IsDesign' => true, 'Isexport' => true];
			if ($menu) {
				$fl = DB::connection('SML')->selectOne(
					'select Access, IsDesign, Isexport from DBFLMENUREPORTWEB where UserID = ? and L1 = ?',
					[\Auth::user()->username, $menu->KODEMENU]
				);
				$hak = [
					'Access' => $fl ? (bool) $fl->Access : false,
					'IsDesign' => $fl ? (bool) $fl->IsDesign : false,
					'Isexport' => $fl ? (bool) $fl->Isexport : false,
				];
			}
			$akses = Arr::add($akses, 'akses', new Fluent($hak));
			// Falls back to the raw href when it's not in dbmenureportweb yet
			// (some older report hrefs still aren't) instead of fataling on
			// $menu->Keterangan against a null row.
			$akses = Arr::add($akses, 'namamenu', $menu->Keterangan ?? $href);

			// Dikirim bersama halaman supaya doLoadHeader() di masterreport2/2x/Gudang
			// tidak perlu lagi AJAX sinkron ke globalfunctions_doLoadHeader saat page
			// load (dulu terjadi 2x per halaman -- sekali dari ready halaman, sekali
			// lagi dari ready master layout -- masing-masing mengunci main thread).
			$simpanheader = DB::connection('SML')->select(
				'select reportmode, header, issubtotal, isgrandtotal from DBSIMPANHEADER where username = ? and href = ?',
				[\Auth::user()->username, $href]
			);
		} else {
			$akses = Arr::add($akses, 'namamenu', "Home");
			$simpanheader = [];
		}

		$akses = Arr::add($akses, 'simpanheader', $simpanheader);
		$akses = Arr::add($akses, 'periode', $periode);
		$akses = Arr::add($akses, 'menul0' , $menul0);

		/* // Tidak jadi dipakai karena timestamp tidak akurat
		$datetime = Carbon::now()->toDateTimeString();            // yyyy-mmm-dd hh:mm
		$datetime = Str::replaceArray('-', ['', ''], $datetime);  // yyyymmmdd hh:mm:ss
		$datetime = Str::replaceArray(':', ['', ''], $datetime);  // yyyymmmdd hhmmss
		$datetime = Str::replaceArray(' ', ['_'], $datetime);     // yyyymmmdd_hhmmss
		$akses = Arr::add($akses, 'xlsfilename', strtoupper($href) . "_" . $datetime);
		*/
		$akses = Arr::add($akses, 'xlsfilename', strtoupper($href));

		return $akses;
	}

	// Recursively wraps HomeController::getReportMenuTreeArray()'s plain-array
	// nodes in Illuminate\Support\Fluent, which supports both ->prop and
	// ['prop'] access. report/newmaster2.blade.php and newmaster2x.blade.php
	// use both styles on the same node (e.g. $menu0->href alongside
	// $menu0['Keterangan']) -- that only worked before because the old
	// NewMenuReport rows were Eloquent models (dual-access natively). Plain
	// arrays only support ['prop'], so this bridges the gap without touching
	// every consuming Blade view.
	private function toFluentMenuTree(array $nodes): array {
		return array_map(function ($node) {
			$node['child'] = $this->toFluentMenuTree($node['child'] ?? []);
			return new Fluent($node);
		}, $nodes);
	}


	/*
	// 2 function di bawah adalah function dari program Project
	// ditaruh sini sebgai refrensi
	public function checkMenu($access, $otherColumn = '') {
		$checkStatusUser = User::where('id', \Auth::id())->first();
		if ($checkStatusUser->status == 0) {
			User::where('id', Auth::id())->update(['status' => 0, 'hostid' => '', 'ipaddress' => '']);
			Auth::logout();
			$akses = array('userLoggedOut' => true);
			return $akses;
		}

		$columnName = ($otherColumn != '') ? $otherColumn : 'access';

		$getKode = Menu::where($columnName, $access)->first();
		$check = AksesMenu::where('id_user', \Auth::id())->where('kode_menu', $getKode->kode)->first();

		$akses = array('userLoggedOut' => false);
		$akses = Arr::add($akses, 'akses', $check);

		if ($check->tampil == 1) {
			User::where('id', \Auth::id())
				->update(['status' => 1, 'hostid' => gethostbyaddr(\Request::ip()), 'ipaddress' => \Request::ip()]);

			$checkUser = User::where('id', \Auth::id())->first();
			if ($checkUser->level == 3 && $checkUser->username == 'SA') {
	    		$menu = Menu::join('akses_menu', 'menu.kode', '=', 'akses_menu.kode_menu')
	    			->where('akses_menu.tampil', 1)
	    			->where('menu.show_acc', 1)
	    			->where('akses_menu.id_user', \Auth::id())
	    			->select('menu.*')->orderBy('kode', 'ASC')
	    			->get();
	  		} else {
	    		$menu = Menu::join('akses_menu', 'menu.kode', '=', 'akses_menu.kode_menu')
	    			->where('akses_menu.tampil', 1)
	    			->where('menu.grup','>', 0)
	    			->where('menu.show_acc', 1)
	    			->where('akses_menu.id_user', \Auth::id())
	    			->select('menu.*')->orderBy('kode', 'ASC')
	    			->get();
	  		}

			$periode = Periode::where('id_user', \Auth::id())->first();

			$akses = Arr::add($akses, 'allowTransEdit', $this->allowTransEdit($checkUser->level));
		} else {
			return $akses;
		}

		$akses = Arr::add($akses, 'menu', $menu);
		$akses = Arr::add($akses, 'periode', $periode);
		return $akses;
	}

	public function allowTransEdit($level) {
		if ($level == 3) {
			$getKode = Menu::where('access', '/transaksidiedit')->first();
			$check = AksesMenu::where('id_user', \Auth::id())->where('kode_menu', $getKode->kode)->first();

			if ($check->tampil == 1) { return true; }
		}

		return false;
	}
	/* */

}
