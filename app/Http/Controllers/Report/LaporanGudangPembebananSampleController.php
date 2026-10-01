<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanGudangPembebananSampleController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporangudangpembebanansample");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('reportgudangpembebanansample' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    $tglAwal  = $req->get('date1');
    $tglAkhir = $req->get('date2');
    $Ordr     = $req->get('inputOrd');

    $values  = [$tglAwal, $tglAkhir, $Ordr];
    
    $res = DB::connection('MGL')->select('exec SP_REPORTBEBANSAMPLE ?,?,?',
      $values);

    return $res;
  }


}
