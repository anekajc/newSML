<?php

namespace App\Http\Controllers\Purchasing;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use Illuminate\Support\Facades\DB;
use App\Models\vwOUtPOWMSNONSTOCK;
use App\Models\VWtampilbeli;

class NewPOJasaController extends Controller
{

  // Rentang tanggal default = satu bulan penuh periode kerja user (sama seperti Purchase Order).
  private function periodeRange ($periode) {
    $stamp = mktime(0, 0, 0, (int) $periode->bulan, 1, (int) $periode->tahun);
    return [ date('Y-m-01', $stamp), date('Y-m-t', $stamp) ];
  }

  public function index (Request $req) {

    // $kodemenu = '030302';
    // $akses = NewAksesMenu::where('USERID', \Auth::id())-> where('L1', $kodemenu)->first();
    // if(!$akses || !$akses->HASACCESS) {
    //    return redirect('/home');

    $kodemenu = '030302';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu, $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');

    }

    // $periode = NewPeriode::where('user_id' , \Auth::id())->first();
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(3);
    list($npoTglAwal, $npoTglAkhir) = $this->periodeRange($periode);

    // $menu = NewMenu::orderBy('KODEMENU')->get();

    // $values = [
    //   'a'
    // ];
    // $po = DB::connection("SML")->select('exec sp_outstanding_po ?',$values);

    // $testesbeli = DB::connection("SML")->select('select * from VWtampilbeli where Bulan = :bulan and Tahun = :tahun' ,['bulan' => $periode->bulan , 'tahun' => $periode->tahun ]);
    // $testesbeli1 = $testesbeli->groupBy('NoBukti');

    $pembelian = DB::connection("SML")->select("select * from dbo.fnc_masterbeli ( :bulan , :tahun, :pjasa)" , ["bulan" => $periode->bulan, "tahun" => $periode->tahun , "pjasa" => 1]);

    // Blade sekarang menggambar kedua tabel lewat AJAX (getAllPOjasa/getAllPembelianjasa) saat
    // load - $po/$poGroup/$tempPo tidak lagi dipakai untuk merender tabel awal, sama seperti
    // NewPOController@index. Dikirim kosong supaya vwOUtPOWMSNONSTOCK::all() (seluruh tabel)
    // tidak dijalankan dua kali percuma di setiap page load.
    $gudang = DB::connection("SML")->select('select * from DBGUDANG where KODEGDG <> :id', ['id' => 'GTC']);

    return view('purchasing.newpojasa' , [
      // "title" => "Home",
      // "menu" => $menu,
      "periode" => $periode,
      "menul0" => $menul0,
      "po" => [],
      "poGroup" => [],
      "tempPo" => [],
      // "tempPembelian" => $tempPembelian,
      // "tesMax" => $tesMax
      "gudang" => $gudang,
      // "pembelian" => $pembelian,
      "tempPembelian1" => $pembelian,
      "npoTglAwal" => $npoTglAwal,
      "npoTglAkhir" => $npoTglAkhir,
      "akses" => $akses
    ]);


  }

  public function getAkses () {
    $kodemenu = '03004';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu);
    return $akses;
  }

  public function getAllPO (Request $req) {
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    list($tglawal, $tglakhir) = $this->periodeRange($periode);
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglawal')))  { $tglawal  = $req->input('tglawal'); }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglakhir'))) { $tglakhir = $req->input('tglakhir'); }
    if ($tglawal > $tglakhir) { $tglakhir = $tglawal; }

    $tglakhirPlus = date('Y-m-d', strtotime($tglakhir . ' +1 day'));
    $poGroup = vwOUtPOWMSNONSTOCK::where('TANGGAL', '>=', $tglawal)->where('TANGGAL', '<', $tglakhirPlus)->get()->sortBy('NoBukti')->sortBy('Urut')->groupBy('NoBukti');

    $tempPo = [];
    foreach ($poGroup as $pG) {
      // code...
      array_push($tempPo, $pG->values());
    }
    return $tempPo;
  }

  public function getAllPembelian (Request $req) {
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    list($tglawal, $tglakhir) = $this->periodeRange($periode);
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglawal')))  { $tglawal  = $req->input('tglawal'); }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglakhir'))) { $tglakhir = $req->input('tglakhir'); }
    if ($tglawal > $tglakhir) { $tglakhir = $tglawal; }
    $tglakhirplus = date('Y-m-d', strtotime($tglakhir . ' +1 day'));

    // Sama dengan NewPOController@getAllPembelian, beda hanya pjasa = 1. JANGAN memakai
    // VWtampilbeli::all() - itu memuat seluruh view (semua bulan/tahun) ke memori PHP baru
    // disaring lewat Collection, dan di produksi ini habis memori/timeout sehingga halaman
    // gagal memuat data begitu tabel ini digambar lewat AJAX saat load.
    //
    // fnc_masterbeli hanya menerima bulan/tahun, jadi rentang bebas dilayani dengan
    // memanggilnya per bulan yang tersentuh rentang lalu disaring per tanggal (TANGGAL).
    $pembelian = [];
    $cursor = date('Y-m-01', strtotime($tglawal));
    $batas  = date('Y-m-01', strtotime($tglakhir));
    while ($cursor <= $batas) {
      $rows = DB::connection("SML")->select(
        "select * from dbo.fnc_masterbeli ( :bulan , :tahun, :pjasa)
         where TANGGAL >= :tglawal and TANGGAL < :tglakhirplus",
        ["bulan" => (int) date('n', strtotime($cursor)), "tahun" => (int) date('Y', strtotime($cursor)),
         "pjasa" => 1, "tglawal" => $tglawal, "tglakhirplus" => $tglakhirplus]);
      $pembelian = array_merge($pembelian, $rows);
      $cursor = date('Y-m-01', strtotime($cursor . ' +1 month'));
    }
    return $pembelian;
  }

public function getDetailPembelian (Request $req) {
  $nobukti = $req->NoBukti;
  // Bulan/tahun HARUS diambil dari tanggal dokumennya sendiri, bukan periode kerja user
  // yang sedang aktif - kalau tidak, dokumen dari bulan lain selalu kembali kosong.
  $header = DB::connection('SML')->select('select TANGGAL from dbBeli where NOBUKTI = :nobukti', ['nobukti' => $nobukti]);
  if ($header) {
    $bulan = (int) date('n', strtotime($header[0]->TANGGAL));
    $tahun = (int) date('Y', strtotime($header[0]->TANGGAL));
  } else {
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $bulan = $periode->bulan;
    $tahun = $periode->tahun;
  }

  $pembelian = DB::connection("SML")->select("select * from dbo.fnc_Tampilbeli ( :bulan , :tahun, :pjasa) where NoBukti = :NoBukti" , ["bulan" => $bulan, "tahun" => $tahun , "pjasa" => 1, "NoBukti" => $nobukti]);
   
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
  $tempDetail = vwOUtPOWMSNONSTOCK::select()->where('NoBukti', $req->input('NoPO'))->whereNotIn('KodeBrg', $tempBeli)->get();
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
  // $periode = NewPeriode::where('user_id' , \Auth::id())->first();
  $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
  $inisial = DB::connection("SML")->select('select PBJ from DBNOMOR');
  // $inisial = DB::connection("SML")->select('select SPR from DBNOMOR');
  $username = \Auth::user()->username;
  // return [$periode->bulan,$inisial[0]->PBJ,$username];
  $values = [
      $inisial[0]->PBJ,
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

          $tempValues = [$noBukti,$noUrut,$date,$gudang, $noPO, $suratJalan, $noKend, 1, 0 , $username];
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
