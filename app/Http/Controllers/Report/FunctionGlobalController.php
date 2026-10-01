<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FunctionGlobalController extends Controller {

	/* BERKAS FUNCTION */

	public function doUpdateDBMENUREPORT(Request $req) {
		// $href = "laporanstockmutasistockpermerk";
		// $kodemenu = "0501031";
		// DB::connection('SML')->update('update DBMENUREPORT set href = :href where KODEMENU = :kodemenu', ['href' => $href, 'kodemenu' => $kodemenu]);

		$menuUpdates = [
		    '050101' => 'laporanstockmutasistockqty',
		    '050102' => 'laporanstockmutasistockrp',
		    '050103' => 'laporanstockmutasistockqtyrp',
		    '0501031' => 'laporanstockmutasistockpermerk',
		    '0501032' => '#',
		    '0501033' => 'laporanstockmutasistockharian',
		    '050104' => 'laporanstocksaldostock',
		    '050105' => 'laporanstockstockfisikgudang',
		    '050108' => 'laporanstockstockkartudanopname',
		    '050109' => 'laporanstockfastslowdeadmoving',
		    '050201' => 'laporanstockkartustockqty',
		    '050202' => 'laporanstockkartustockqtyrp',
		];

		foreach ($menuUpdates as $kodemenu => $href) {
		    DB::connection('SML')->update(
		        'update DBMENUREPORTweb set href = :href where KODEMENU = :kodemenu',
		        ['href' => $href, 'kodemenu' => $kodemenu]
		    );
		}

		DB::connection('SML')->update('alter table DBSIMPANHEADER alter column header varchar(2000) null');


		return "1";
	}

	/* END OF BERKAS FUNCTION */



	/* MASTER FUNCTION */

	/* END OF BERKAS FUNCTION */



	/* TRANSAKSI FUNCTION */

	/* END OF TRANSAKSI FUNCTION */




	/* REPORT FUNCTION */

	public function doLoadHeader(Request $req) {
		$header = DB::connection('MGL')->select('select * from DBSIMPANHEADER where username = :user and href = :href and reportmode = :mode', ['user' => \Auth::User()->username, 'href' => $req->href, 'mode' => $req->mode]);

		return $header;
	}

	public function doSimpanHeader(Request $req) {
		// sementara pakai ini sampai sistem login databasenya benar

		DB::connection('SML')->update('delete from DBSIMPANHEADER where username = :user and href = :href and reportmode = :mode' , ['user' => \Auth::User()->username, 'href' => $req->href, 'mode' => $req->mode]);

		DB::connection('MGL')->update('insert into DBSIMPANHEADER (username, href, reportmode, header, issubtotal, isgrandtotal) values (:user, :href, :mode, :header, :issubtotal, :isgrandtotal)' , ['user' => \Auth::User()->username, 'href' => $req->href, 'mode' => $req->mode, 'header' => $req->header, 'issubtotal' => $req->issubtotal, 'isgrandtotal' => $req->isgrandtotal]);

		return;
	}

	/* END OF REPORT FUNCTION */


	public function doBrowseGudang(Request $req) {
		$res = DB::connection('MGL')->select('select KODEGDG, NAMA from DBGUDANG');

		return $res;
	}

}
