<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanMarketingSOController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporanmarketingso");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportmarketingso' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    $SReport = "T";
    $tgl1    = $req->query('date1');
    $tgl2    = $req->query('date2');
    $isiList = "";
    $idUser = "";
    $tipe = "";
    $Ordr    = $req->query('inputOrd');
    $KodeCust = $req->query('inputCustomer');
    $group = $req->query('inputGroup');
    $NeedOto = $req->query('inputOto');
    $PIC = $req->query('inputPIC');
    $Kategori = $req->query('inputKategori');
    $Subkategori = $req->query('inputSubKategori');
    $KodeMerk = $req->query('inputMerk');
    $pagen = $req->query('inputAgen');

    $values  = [$SReport, $Ordr, $tgl1, $tgl2, $isiList, $NeedOto, $idUser, $tipe, $KodeCust, $group, $PIC, $Kategori, $Subkategori, $KodeMerk, $pagen];

    $res = DB::connection('SML')->select('exec Sp_ReportSODet ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?',
      $values);

    return $res;
  }

  public function loadCustomer () {
    $listData = DB::connection('SML')->select('select distinct A.KodeCustSupp, A.NamaCustSupp, A.AlamatKota, A.NamaKota, A.PPN, A.Hari, A.HariHutPiut
           from vwBrowsCustSupp A
           where iscustomer=1
          order by A.KodeCustSupp');
    return $listData;
  }

  public function loadGroup () {
    $listData = DB::connection('SML')->select('select NamaHDGRP+KodeHDGrp KodeHDGRP,NamaHDGRP from  dbHdGroup');
    return $listData;
  }

  public function loadPIC () {
    $listData = DB::connection('SML')->select('Select Nama+KodePIC KODEPIC,Nama NamaPIC from DBPICCUSTSUPP');
    return $listData;
  }

  public function loadKategori () {
    $listData = DB::connection('SML')->select('select NamaSubGrp+KOdeSubGrp KOdeSubGrp,NamaSubGrp from DbSubGroup order by KodeSubGrp');
    return $listData;
  }

  public function loadSubKategori () {
    $listData = DB::connection('SML')->select('select Keterangan+Urut Urut,Keterangan
                      from DBSubGroupJnsTambah');
    return $listData;
  }

  public function loadMerk () {
    $listData = DB::connection('SML')->select('select NamaMerk+KodeMerk KodeMerk,NamaMerk  from DBmerk
                        order by KodeMerk');
    return $listData;
  }


}
