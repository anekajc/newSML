<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\DBFLMENU;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Models\vwOUtPOWMS;
use App\Models\VWtampilbeli;
use App\Models\vwMasterBeli;
use App\Models\NEWDBBELI;






class NewPOBeliAccController extends Controller
{


  public function index () {
    $kodemenu = '030402';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu);
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');

    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0();


    $pembelian = vwMasterBeli::all()->where('pJasa',0)->where('TipeBayar' , 1)->where('IsOtorisasi1', 1)->where('BULAN',$periode->bulan )->where('TAHUN', $periode->tahun)->sortBy('NoBukti')->groupBy('NoBukti');
    $tempPembelian1 = [];
    foreach ($pembelian as $p) {
      array_push($tempPembelian1, $p);
    }
       // $po = vwOUtPOWMS::all();
     $po = vwtampilbeli::all()->where('pJasa',0);
     $poGroup = vwMasterBeli::all()->where('pJasa',0)->where('TipeBayar' , 1)->where('IsOtorisasi1', 0)->where('BULAN', $periode->bulan)-> where('TAHUN', $periode->tahun)-> where('NeedOtorisasi', 1)->sortBy('NoBukti')->sortBy('Urut')->groupBy('NoBukti');


    $tempPo = [];
    foreach ($poGroup as $pG) {
      // code...
      array_push($tempPo, $pG);
    }



    $gudang = DB::connection("SML")->select('select * from DBGUDANG where KODEGDG <> :id', ['id' => 'GTC']);

    return view('newpobeliacc' , [
      // "title" => "Home",
      // "menu" => $menu,
      "periode" => $periode,
      "menul0" => $menul0,
      "po" => $po,
      "poGroup" => $poGroup,
      "tempPo" => $tempPo,
      // "tempPembelian" => $tempPembelian,
      // "tesMax" => $tesMax
      "gudang" => $gudang,
      // "pembelian" => $pembelian,
      "tempPembelian1" => $tempPembelian1,
      "akses" => $akses
    ]);


  }

  public function getAkses () {
    $kodemenu = '030061';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu);
    return $akses;
  }

  public function getAllPO () {
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $poGroup = vwMasterBeli::all()->where('pJasa',0)->where('TipeBayar' , 1)-> where('BULAN', $periode->bulan)-> where('TAHUN', $periode->tahun)-> where('NeedOtorisasi', 1)->sortBy('NoBukti')->sortBy('Urut')->groupBy('NoBukti');


   $tempPo = [];
   foreach ($poGroup as $pG) {
     // code...
     array_push($tempPo, $pG);
   }
    return $tempPo;
  }

  public function getAllPembelian () {

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $pembelian = vwMasterBeli::all()->where('pJasa',0)->where('TipeBayar' , 1)->where('IsOtorisasi1', 1)->where('BULAN',$periode->bulan )->where('TAHUN', $periode->tahun)->sortBy('NoBukti')->groupBy('NoBukti');
    $tempPembelian1 = [];
    foreach ($pembelian as $p) {
      array_push($tempPembelian1, $p);
    }

    return $tempPembelian1;
  }



  public function spOtorisasi1 (Request $req) {
      $username = \Auth::user()->username;
      $nobukti =  $req->nobukti;
      $otorisasi = $req->otorisasi;

      if ($otorisasi == 0 ) {
        $username = '';
        $tanggal = null;
      }


      $update = DB::connection('SML')->update("update dbbeli set IsOtorisasi1 = :otorisasi , OtoUser1 = :username , TglOto1 = getDate() , MaxOL = 1 where nobukti = :nobukti", ['otorisasi' => $otorisasi, 'username' => $username,  'nobukti' => $nobukti ] );
      return $update;

    }


    public function spUnOtorisasi1 (Request $req) {
        $username = \Auth::user()->username;
        $nobukti =  $req->nobukti;
        $otorisasi = $req->otorisasi;

        if ($otorisasi == 0 ) {
          $username = '';
          $tanggal = null;
        }


        $update = DB::connection('SML')->update("update dbbeli set IsOtorisasi1 = 0 , OtoUser1 = '' , TglOto1 = NULL, MaxOL = -1 where nobukti = :nobukti", [  'nobukti' => $nobukti ] );
        return $update;

      }

public function getDetailPembelian (Request $req) {
  $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
  $pembelian = VWtampilbeli::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->where('NoBukti', $req->input('NoBukti'))->sortBy('NoBukti')->groupBy('NoBukti');
  $tempPembelian1 = [];
  foreach ($pembelian as $p) {
    array_push($tempPembelian1, $p);
  }
  return $tempPembelian1;
}


public function getDetailPO (Request $req) {
  $tempBeli = VWtampilbeli::where('NoBukti' , $req->input('NoBukti'))->select('KodeBrg')->get()->toArray();
  // $tempBeli1 = [];
  // foreach ($tempBeli as $t) {
  //   // code...
  //   array_push($tempBeli1, $t->KodeBrg);
  // }
  $tempDetail = vwOUtPOWMS::select()->where('NoBukti', $req->input('NoPO'))->whereNotIn('KodeBrg', $tempBeli)->get();
  $poDetail = [];
  foreach ($tempDetail as $t) {
    // code...
    array_push($poDetail,$t);
  }
  return $tempDetail;
}

public function spBeliGudang (Request $req) {
  // return $req;
  // $data = $req->input('data');
  // $dataLPB = $req->input('dataLPB');
  // $choice = $req->input('choice');
  // $qtyTerima = $req->input('qtyTerima');
  // $qntTerima1 = 0;
  // $qntTerima2 = 0;
  // if ($data['NOSAT'] == 1) {
  //   $qntTerima1 = $qtyTerima;
  //   $qntTerima2 = $qtyTerima / $data['ISI2'];
  // } else if ($data['NOSAT'] == 2) {
  //   $qntTerima1 = $qtyTerima * $data['ISI2'];
  //   $qntTerima2 = $qtyTerima;
  // }

  //=======================

  $values = [
    $req->input('choice'),
    $req->input('reqNoBukti'),
    $req->input('reqNoUrut'),
    $req->input('reqTANGGAL'),
    $req->input('reqKodeSupp'),
    $req->input('reqKodeGudang'),
    $req->input('reqNoPO'),
    $req->input('reqKeterangan'),
    $req->input('reqFakturSupp'),
    $req->input('reqUrut'),
    $req->input('reqKodeBarang'),
    $req->input('reqUrutPO'),
    $req->input('reqQtyTerima'),
    $req->input('reqNoSat'),
    $req->input('reqSatuan'),
    $req->input('reqIsi'),
    $req->input('reqQtyTerima1'),
    $req->input('reqQtyTerima2'),
    $req->input('reqNamaBarang'),
    "",
    $req->input('reqQtyReject'),
    $req->input('reqQtyReject1'),
    $req->input('reqQtyReject2'),
    $req->input('reqPBeliJasa'),
    $req->input('reqEd'),
    $req->input('reqDisc'),
    $req->input('reqDiscRp'),
    $req->input('reqHarga')

  ];

  DB::connection('SML')->statement('exec sp_BeliGudangACC ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);

  return 1;

  //=============================

//   @Choice varchar(1),
// @NoBukti varchar(20),
// @NoUrut varchar(10),
// @Tanggal datetime,
// @KodeSupp varchar(15),
// @KodeGdg varchar(15),
// @NoPO varchar(20),
// @Keterangan varchar(200),
// @FakturSupp Varchar(20),
// @Urut int,
// @KodeBrg varchar(25),
// @UrutPO int,
// @QntTerima numeric(18,2),
// @NoSat tinyint,
// @Satuan varchar(5),
// @Isi numeric(18,2),
// @Qnt1Terima numeric(18,2),
// @Qnt2Terima numeric(18,2),
// @NamaBrg varchar(100),
// @NoBatch Varchar(50),
// @QntReject Numeric(18,2)=0,
// @QntReject1 Numeric(18,2)=0,
// @QntReject2 Numeric(18,2)=0,
// @pBeliJasa Bit=0,
// @Ed datetime=null
  // DB::connection('SML')->statement('exec sp_InsertOutstandingPO ?,?,?,?,?,?,?,?,?,?', $tempValues);
}


  // ISTAMBAH, ISKOREKSI, ISHAPUS, ISCETAK , ISEXPORT , IsOtorisasi1, IsOtorisasi2 , IsOtorisasi3 , IsOtorisasi4, IsOtorisasi5 ,
  // TIPE, IsBatal, pembatalan

public function getNoBukti (Request $req) {
  // $values = [
  //   'a'
  // ];
  // return 'tes';
  // $po = DB::connection("SML")->select('exec sp_outstanding_po ?',$values);
  $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
  $inisial = DB::connection("SML")->select('select PBL from DBNOMOR');
  // $inisial = DB::connection("SML")->select('select SPR from DBNOMOR');
  $username = \Auth::user()->username;
  // return [$periode->bulan,$inisial[0]->PBL,$username];
  $values = [
      $inisial[0]->PBL,
      $periode->bulan,
      $periode->tahun,
      $username
  ];
  $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
  return $noBukti;
}

public function addDBBeli (Request $req) {
  // try {

        $data = $req->input('data');
        $suratJalan = $req->input('suratJalan');
        $noKend = $req->input('noKend');
        $noPO = $req->input('noPO');
        // $date = date("Y-m-d H:i:s");
        $date = $req->input('inputTanggal');
        $gudang = $req->input('gudang');
        $noBukti = $req->input('noBukti');
        $noUrut = $req->input('noUrut');
        $username = \Auth::user()->username;
        $periode = app('App\Http\Controllers\GlobalController')->getPeriode();


        $check = DB::connection('SML')->select('select * from dbBeli where NOBUKTI = :nobukti',["nobukti" => $noBukti]);
        if ($check) {
          return 2;
        }
        // delete	TempOutstandingPO where IDUser=@IDUser
        DB::connection('SML')->statement('delete	TempOutstandingPO where IDUser = :idUser',['idUser' => $username ]);
        foreach ($data as $d) {
          $values = [$username,$noPO,$periode->tahun,$periode->bulan, 0,$d['inputQntTerima'], 1, $d['Urut']];
          DB::connection("SML")->statement('exec sp_RefreshTempOutstandingPOweb ?,?,?,?,?,?,?,?',$values);

        }
        // $check = DB::connection('SML')->select('select * from dbBeli where NOBUKTI = :id' , ['id' => $noBukti]);
        // if (count($check) == 0) {

          $tempValues = [$noBukti,$noUrut,$date,$gudang, $noPO, $suratJalan, $noKend, 0, 0, $username];
        // } else {

          // $tempValues = [$noBukti,$noUrut,$date,$gudang, $noPO, $suratJalan, $noKend, 0, 1, $username];
        // }
        DB::connection('SML')->statement('exec sp_InsertOutstandingPO ?,?,?,?,?,?,?,?,?,?', $tempValues);
        return 1;
//         @NoBukti varchar(20),
// @NoUrut varchar(10),
// @Tanggal datetime,
// @KodeGdg varchar(20),
// @NoPO varchar(20),
// @FakturSupp varchar(200),
// @Keterangan Varchar(5000),
// @pjasa Bit=0,
// @JmlRecord Integer,
// @iduser varchar(20)
  // } catch (\Exception $e) {
  //
  // }
}

// ADD LAMA


  // public function addDBBeli (Request $req) {
  //   DB::beginTransaction();
  //   try {
  //     $tesMaxNumber = NEWDBBELI::all()->count();
  //     if ($tesMaxNumber) {
  //       $tesMaxNumber = NEWDBBELI::max('NoBukti');
  //       $tesMaxNumber = $tesMaxNumber + 1;
  //     } else {
  //       $tesMaxNumber = 1;
  //       // return 0;
  //
  //     }
  //     // $tesMax = vwOUtPOWMS::max('NoBukti');
  //     // if ($tesMaxNumber) {
  //     //   $tesMaxNumber = $tesMaxNumber + 1;
  //     // } else {
  //     //   $tesMaxNumber = 1;
  //     // }
  //     //
  //     $data = $req->input('data');
  //     $suratJalan = $req->input('suratJalan');
  //     $noKend = $req->input('noKend');
  //     $noPO = $req->input('noPO');
  //     $date = date("Y-m-d H:i:s");
  //     // $dateStr = strtotime($date);
  //     // $monthName = date('F',$dateStr);
  //     // $monthNo = date('m',$dateStr);
  //     // $year = date('Y',$dateStr);
  //     // $noBukti = "LPB/" . $monthNo . substr($year , -2) . "/" . "0002";
  //     foreach($data as $d) {
  //       NEWDBBELI::create([
  //         'Tanggal' => $date,
  //         'NamaSupplier' => $d['NAMACUSTSUPP'],
  //         'NoPo'=> $noPO,
  //         'Gudang' => "-",
  // 				'FakturSupp' => "-",
  //         'KodeBarang' => $d['KodeBrg'] ,
  //         'NamaBarang' => $d['namaBrg'],
  //         'Qty' => $d['QNT'],
  //         'QtyPO'=> $d['QntPO'],
  //         'QtyOS' =>$d['QNTOS'],
  //         'Satuan' => $d['Satuan'],
  //         'NoSO'=> "-",
  //         'NoPOCust' => "-",
  //         'Customer' => "-",
  //         'SuratJalan' => $suratJalan,
  //         'NoKendSopir' => $noKend,
  // 				'NoBukti' => $tesMaxNumber
  //       ]);
  //     }
  //     DB::commit();
  //
  //     return 1;
  //     // $check = NewMenu::where('KODEMENU', $req->input('KODEMENU'))->count();
  //     // if($check == 0) {
  //     //   NewMenu::create([
  //     //     'KODEMENU'=> $req->input('KODEMENU'),
  //     //     'Keterangan'=> $req->input('Keterangan'),
  //     //     'L0'=> $req->input('L0') ,
  //     //     'ACCESS' => $req->input('ACCESS'),
  //     //     'href' => $req->input('href')
  //     //   ]);
  //     //   $users = NewUsers::all();
  //     //   foreach ($users as $u) {
  //     //     $access = False;
  //     //     if ($u['username'] == 'admin') {
  //     //       $access = True;
  //     //     }
  //     //     NewAksesMenu::create([
  //     //       'USERID' => $u->id,
  //     //       'L1' => $req->input('KODEMENU'),
  //     //       'HASACCESS' => $access
  //     //     ]);
  //     //   }
  //     //   DB::commit();
  //     //   return 1;
  //     // } else {
  //     //   return 0;
  //     // }
  //   } catch (\Exception $e) {
  //     DB::rollback();
  //   }
  // }


// END ADD LAMA



}
