<?php

namespace App\Http\Controllers\Wms;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Models\vwOUtPOWMS;
use App\Models\VWtampilbeli;
use App\Models\NEWDBBELI;

class NewPOWmsController extends Controller
{





  public function index () {
    // return redirect('/home');

    $kodemenu = '210200';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
 
    // $menu = NewMenu::orderBy('KODEMENU')->get();

    // $values = [
    //   'a'
    // ];
    // $po = DB::connection("SML")->select('exec sp_outstanding_po ?',$values);

    // $testesbeli = DB::connection("SML")->select('select * from VWtampilbeli where Bulan = :bulan and Tahun = :tahun' ,['bulan' => $periode->bulan , 'tahun' => $periode->tahun ]);
    // $testesbeli1 = $testesbeli->groupBy('NoBukti');
    // Dinonaktifkan 2026-10-19: memuat seluruh view lalu menyaring periode di PHP (lambat).
    // $pembelian = VWtampilbeli::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('NoBukti')->groupBy('NoBukti');
    // Pengganti: saring periode yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $pembelian = VWtampilbeli::where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('NoBukti')->groupBy('NoBukti');
    $tempPembelian1 = [];
    foreach ($pembelian as $p) {
      array_push($tempPembelian1, $p);
    }
    $po = vwOUtPOWMS::all();
    // Dinonaktifkan 2026-10-20: mengambil ulang view yang sama dengan $po di atas (query kedua, lambat).
    // $poGroup = vwOUtPOWMS::all()->sortBy('NoBukti')->sortBy('Urut')->groupBy('NoBukti');
    // Pengganti: pakai data $po yang sudah diambil (isi sama, satu query lebih sedikit).
    $poGroup = $po->sortBy('NoBukti')->sortBy('Urut')->groupBy('NoBukti');

    $tempPo = [];
    foreach ($poGroup as $pG) {
      // code...
      array_push($tempPo, $pG);
    }

    // $tesMax = vwOUtPOWMS::max('NoBukti');
    // $pembelianGroup = NEWDBBELI::all()->sortBy('Tanggal')->groupBy('NoBukti');
    // $tempPembelian = [];
    // foreach ($pembelianGroup as $pembG) {
    //   array_push($tempPembelian, $pembG);
    // }

    $gudang = DB::connection("SML")->select('select * from DBGUDANG where KODEGDG <> :id', ['id' => 'GTC']);

    return view('wms.newpowms' , [
      // "title" => "Home",
      // "menu" => $menu,
      "periode" => $periode,
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
    $kodemenu = '210200';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    return $akses;
  }

  public function getAllPO () {
    $poGroup = vwOUtPOWMS::all()->sortBy('NoBukti')->sortBy('Urut')->groupBy('NoBukti');

    $tempPo = [];
    foreach ($poGroup as $pG) {
      // code...
      array_push($tempPo, $pG);
    }
    return $tempPo;
  }

  public function getAllPembelian () {
    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    // Dinonaktifkan 2026-10-19: memuat seluruh view lalu menyaring periode di PHP (lambat).
    // $pembelian = VWtampilbeli::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('NoBukti')->groupBy('NoBukti');
    // Pengganti: saring periode yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $pembelian = VWtampilbeli::where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('NoBukti')->groupBy('NoBukti');
    $tempPembelian = [];
    foreach ($pembelian as $p) {
      array_push($tempPembelian, $p);
    }

    return $tempPembelian;
  }

public function getDetailPembelian (Request $req) {
  $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
  // Dinonaktifkan 2026-10-19: memuat seluruh view lalu menyaring periode di PHP (lambat).
  // $pembelian = VWtampilbeli::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->where('NoBukti', $req->input('NoBukti'))->sortBy('NoBukti')->groupBy('NoBukti');
  // Pengganti: saring periode yang sama langsung di query database (hasil sama, jauh lebih cepat).
  $pembelian = VWtampilbeli::where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->where('NoBukti', $req->input('NoBukti'))->sortBy('NoBukti')->groupBy('NoBukti');
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
    $req->input('reqEd')
  ];

  DB::connection('SML')->statement('exec sp_BeliGudang ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);

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
  $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
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
        $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();




          $checkp = DB::connection('SML')->select("select month(:tglbulan) bulan,year(:tgltahun) tahun,
        month(getdate()) as pbulan, year(getdate()) as ptahun",["tglbulan" => $date, "tgltahun" => $date]);

          // return[$checkp[0]->bulan , $periode->bulan , $checkp[0]->tahun , $periode->tahun ];
         if ($checkp[0]->bulan != $checkp[0]->pbulan || $checkp[0]->tahun != $checkp[0]->ptahun) {
          return 22;
        }


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
