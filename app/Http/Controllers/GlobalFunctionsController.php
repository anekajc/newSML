<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GlobalFunctionsController extends Controller {
	public function doLoadHeader(Request $req) {
		$header = DB::connection('MGL')->select('select * from DBSIMPANHEADER where username = :user and href = :href and reportmode = :mode', ['user' => \Auth::User()->username, 'href' => $req->href, 'mode' => $req->mode]);

		return $header;
	}

	public function doSimpanHeader(Request $req) {
		// sementara pakai ini sampai sistem login databasenya benar
		
		DB::connection('SML')->update('delete from DBSIMPANHEADER where username = :user and href = :href and reportmode = :mode' , ['user' => \Auth::User()->username, 'href' => $req->href, 'mode' => $req->mode]);
		
		DB::connection('MGL')->update('insert into DBSIMPANHEADER (username, href, reportmode, header, issubtotal, isgrandtotal) values (:user, :href, :mode, :header, :issubtotal, :isgrandtotal)' , ['user' => \Auth::User()->username, 'href' => $req->href, 'mode' => $req->mode, 'header' => $req->header, 'issubtotal' => $req->issubtotal, 'isgrandtotal' => $req->isgrandtotal]);

		return;

	    // DB::beginTransaction();

	    // try {
	    // 	$flagQuery = 'delete kembalian';
     //  		DB::connection('SML')->update('delete from DBSIMPANHEADER where username = :user and href = :href and reportmode = :mode' , ['user' => \Auth::User()->username, 'href' => $req->href, 'mode' => $req->mode]);

     //  		$flagQuery = 'insert simpan header';
	    //  	DB::connection('MGL')->update('insert into DBSIMPANHEADER (username, href, reportmode, header, issubtotal, isgrandtotal) values (:user, :href, :mode, :header, :issubtotal, :isgrandtotal)' , ['user' => \Auth::User()->username, 'href' => $req->href, 'mode' => $req->mode, 'header' => $req->header, 'issubtotal' => $req->issubtotal, 'isgrandtotal' => $req->isgrandtotal]);

	    //  	DB::commit();
	    //  	return "H";
	    // } catch (\Exception $e) {
	    //     DB::rollback();
	    //     return "E".";;".$flagQuery.";;".$e;
	    // }
	}
  
}
