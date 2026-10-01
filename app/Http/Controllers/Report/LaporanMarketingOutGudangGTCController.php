<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanMarketingOutGudangGTCController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporanmarketingoutgudanggtc");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportmarketingoutgudanggtc' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    // $SReport = "T";
    // $Ordr    = $req->get('inputOrd');
    // $tgl1    = $req->get('date1');
    // $tgl2    = $req->get('date2');
    // $isiList = "";
    // $NeedOto = $req->get('inputOto');
    // $idUser = "";
    // $tipe = "";
    // $KodeCust = $req->get('inputCustomer');
    // $group = $req->get('inputGroup');
    // $PIC = $req->get('inputPIC');
    // $Kategori = $req->get('inputKategori');
    // $Subkategori = $req->get('inputSubKategori');
    // $KodeMerk = $req->get('inputMerk');
    // $KodeAgen = "";
    // $Jenis = "";
    // $pagen = $req->get('inputAgen');
    //
    // $values  = [$SReport, $Ordr, $tgl1, $tgl2, $isiList, $NeedOto, $idUser, $tipe, $KodeCust, $group, $PIC, $Kategori, $Subkategori, $KodeMerk, $KodeAgen, $Jenis, $pagen];
    //
    // $res = DB::connection('SML')->select('exec Sp_ReportInvoicePenjualanDet ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?',
    //   $values);
    $SReport = "T";
    $Ordr    = "Y";
    $tgl1    = $req->get('date1');
    $tgl2    = $req->get('date2');
    $isiList = "";
    // $xx = $req->get('inputOto');
    $xx = 1;
    $idUser = "";
    $tipe = "";
    $KodeAgen = "";

    $values  = [$SReport,$Ordr, $tgl1, $tgl2, $isiList, $xx, $idUser, $tipe, $KodeAgen];


    $res = DB::connection('SML')->select('exec Sp_ReportOutSpbDet ?,?,?,?,?,?,?,?,?',
      $values);
    return $res;
  }

  public function doFilter(Request $req) {
    $kolom = ($req->get('inputOrd') == "N") ? 'NoBukti, Tanggal' : 'KodeBrg, NamaBrg';
    $listData = DB::connection('MGL')->select('select ' . $kolom . ' from Vwreportinvoicepenjualandet where tanggal between :tgl1 and :tgl2 group by ' . $kolom , ['tgl1' => $req->date1, 'tgl2' => $req->date2]);
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
