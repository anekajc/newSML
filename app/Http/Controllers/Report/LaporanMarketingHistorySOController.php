<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanMarketingHistorySOController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporanmarketinghistoryso");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportmarketinghistoryso' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    $tgl1    = $req->get('date1');
    $tgl2    = $req->get('date2');
    $KodeCust = $req->get('inputCustomer');
    $lokasi = $req->get('inputLokasi');
    $IDUser = '';
    $TipeTrans = '';
    $penuh = $req->get('inputOto');

    $values  = [$tgl1, $tgl2, $KodeCust, $lokasi, $IDUser, $TipeTrans, $penuh];

    $res = DB::connection('SML')->select('exec SP_REPORTHISSO ?,?,?,?,?,?,?',
      $values);

    return $res;
  }

  public function doFilter(Request $req) {
    $kolom = ($req->get('inputOrd') == "N") ? 'NoBukti, Tanggal' : 'KodeBrg, NamaBrg';
    $listData = DB::connection('MGL')->select('select ' . $kolom . ' from Vwreportpurchasingreqdetclose where tanggal between :tgl1 and :tgl2 group by ' . $kolom , ['tgl1' => $req->date1, 'tgl2' => $req->date2]);
    return $listData;
  }

  public function doReportFilter(Request $req) {
    $kolom = ($req->get('inputOrd') == "N") ? 'NoBukti' : 'KodeBrg';
    $res = [];

    for ($i=0; $i < count($req->listdata); $i++) {
      $row = DB::connection('MGL')->select('select * from Vwreportpurchasingreqdetclose where ' . $kolom . ' = :list' , ['list' => $req->listdata[$i]]);

      for ($j=0; $j < count($row); $j++) {
        $res = array_add($res, $i+$j, $row[$j]);
      }
    }

    return $res;
  }

  public function loadCustSupp () {
    $listData = DB::connection('SML')->select('select distinct A.KodeCustSupp, A.NamaCustSupp, A.AlamatKota, A.NamaKota, A.PPN, A.Hari, A.HariHutPiut
           from vwBrowsCustSupp A
           where iscustomer=1
          order by A.KodeCustSupp');
    return $listData;
  }

  public function loadLokasi () {
    $listData = DB::connection('SML')->select('Select KodeKebun,nama namaKebun from DbKebunCustSupp
 Group By KodeKebun,nama ');
    return $listData;
  }



}
