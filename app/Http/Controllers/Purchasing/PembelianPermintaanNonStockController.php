<?php

namespace App\Http\Controllers\Purchasing;
use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\NewPeriode;
use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Models\VwPPL;
use App\Model\DBFLMENU;



// use App\Http\Controllers\NewMenuController;

class PembelianPermintaanNonStockController extends Controller
{

 public function index(Request $req)
{
    $kodemenu = '030103';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu, $req->path());
    if (!$akses || !$akses->HASACCESS) {
        return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(3);

    // Data tabel (listData1/listData2, header kolom, dsb) TIDAK disiapkan di sini karena
    // blade memuatnya sendiri lewat AJAX loadAll() saat halaman ready - menyiapkannya di
    // sini dobel kerja dan bikin buka halaman lemot. Sama seperti pola Agen/Non-Agen.
    return view('purchasing.pembelianpermintaannonstock', [
        "menul0" => $menul0,
        "periode" => $periode,
        "akses" => $akses,
    ]);
}


  public function loadAll(Request $req)
{
    $queryOtorisasi = '';

    if ($req->isoto != 2) {
        $queryOtorisasi = ' AND IsOtorisasi1 = ' . $req->isoto;
    }

    // =========================
    // Header Table
    // =========================
    $reqHeader = new Request([
        'href' => 'pembelianpermintaannonstock'
    ]);

    $header = app('App\Http\Controllers\HeaderTableController')
        ->getHeaderTable($reqHeader);

    // =========================
    // Data Pembelian Non-Stock
    // =========================
    // Qnt/QntBatal/QntPO ikut diambil (dengan alias eksplisit, supaya key JSON-nya pasti
    // "Qnt"/"QntBatal"/"QntPO") untuk menghitung badge Status "Sudah/Belum/Batal" di
    // pembelianpermintaannonstock.blade.php (prStatusPR()), sama seperti pola Agen.
    $outstanding = DB::connection("SML")->select(
        "SELECT NoBukti, Tanggal, IsOtorisasi1, TglOto1, OtoUser1,
                Qnt AS Qnt, QntBatal AS QntBatal, QntPO AS QntPO
         FROM vwPPL
         WHERE Tanggal BETWEEN :tglawal AND :tglakhir
           AND IsJasa = 1
           AND pAgen = 0
           $queryOtorisasi
         ORDER BY Tanggal DESC, NoBukti DESC",
        [
            "tglawal"  => $req->tglawal,
            "tglakhir" => $req->tglakhir
        ]
    );

    $collection = collect($outstanding)->groupBy('NoBukti');

    $tempOutstanding = [];
    foreach ($collection as $groupedData) {
        $tempOutstanding[] = $groupedData;
    }

    // Mengikuti pola Agen
    $tempOtorisasi = [];

    return [
        "listData1" => $tempOutstanding,
        "listData2" => $tempOtorisasi,

        "aliasordered"      => $header['aliasordered'],
        "headertableheader" => $header['headertableheader'],
        "isnumeric"         => $header['isnumeric'],
        "headertablevalue"  => $header['headertablevalue'],
        "isparsed"          => $header['isparsed'],
        "isshown"           => $header['isshown'],
        "desimal"           => $header['desimal'],
    ];
}

  public function updateOtorisasi(Request $req) {
    $tanggal = now();
    $res = DB::connection('SML')->update(
        "UPDATE DBPPL SET isOtorisasi1 = 1, maxol = 1, OtoUser1 = :username, TglOto1 = :tanggal WHERE NoBukti = :nobukti",
        [
            "username" => \Auth::user()->username,
            "tanggal" => $tanggal,
            "nobukti" => $req->nobukti
        ]
    );

      $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','PR',$req->nobukti,'',0,'DBPPL');
    return $res;
}
  public function updateBatalOtorisasi(Request $req) {
    $res = DB::connection('SML')->update(
        "UPDATE DBPPL SET isOtorisasi1 = 0, maxol = -1, OtoUser1 = '', TglOto1 = NULL WHERE NoBukti = :nobukti",
        [
            "nobukti" => $req->nobukti
        ]
    );
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','PR',$req->nobukti,$req->pket,0,'DBPPL');
    return $res;
}
  public function cekOtorisasi (Request $req) {
    $res = DB::connection('SML')->select("select isOtorisasi1 from DBPPL where nobukti = :nobukti", ["nobukti" => $req->nobukti ]);
    return $res;
  }

  public function spDetail (Request $req) {
    // Sebelumnya VwPPL::all()->where(...) - itu menarik SELURUH isi VwPPL (semua NoBukti,
    // semua periode) ke memori PHP baru difilter di sana. Diganti query terarah ke DB
    // (WHERE didorong ke server SQL), sama seperti pola Agen/Non-Agen.
    return DB::connection('SML')->select(
      "select * from VwPPL where NoBukti = :nobukti order by Urut",
      ["nobukti" => $req->nobukti]
    );
  }



  // public function spNobukti (Request $req) {
  //   $inisial = DB::connection('SML')->select("SELECT PPL FROM DBNOMOR");
  // }

  public function listDepartemen (Request $req) {
    $listDepartemen = DB::connection("SML")->select('select * from DBDEPART');
    return $listDepartemen;
  }

  public function getNoBukti (Request $req) {
    // $values = [
    //   'a'
    // ];
    // return 'tes';
    // $po = DB::connection("SML")->select('exec sp_outstanding_po ?',$values);
    // $periode = NewPeriode::where('user_id' , \Auth::id())->first();
    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $inisial = DB::connection("SML")->select('select PNS from DBNOMOR');
    // $inisial = DB::connection("SML")->select('select SPR from DBNOMOR');
    // return [$periode->bulan,$inisial[0]->PBL,$username];
    $values = [
        $inisial[0]->PNS,
        $periode->bulan,
        $periode->tahun,
        $username,
        // $periode
        // $periode
    ];
    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
    return $noBukti;
  }

  public function listBarang(Request $req)
{
    if (!$req->filled('search')) {
        return response()->json([]);
    }

    $search = "%" . $req->input('search') . "%";

    $listData = DB::connection('SML')->select("
        SELECT
            b.NAMAMERK,
            a.KODEBRG,
            a.NAMABRG,
            a.KODEMERK,
            a.PartNumber,
            a.SAT1,
            a.SAT2,
            a.SAT3,
            a.ISI1,
            a.ISI2,
            a.ISI3
        FROM dbbarang a
        LEFT JOIN DBMERK b ON a.KodeMerk = b.KODEMERK
        WHERE a.KODEGRP = 'JS'
        AND a.IsJasa = 1
        AND a.IsAktif = 1
        AND (a.KODEBRG LIKE ? OR a.NAMABRG LIKE ?)
        ORDER BY a.KODEBRG ASC
    ", [
        $search,
        $search
    ]);

    return response()->json($listData);
}

  public function spAdd (Request $req) {
    $choice = $req->choice;
    $jmlrecord = $req->jmlrecord;
    $nobukti = $req->nobukti;


      $purut = DB::connection('SML')->select('select * from DBPPLdet where Nobukti = :nobukti', ['nobukti' => $nobukti]);
    if ($purut){

        if ($choice=='I' ){

        $purut = DB::connection('SML')->select('select max(urut)+1 xurut from DBPPLdet where Nobukti = :nobukti', ['nobukti' => $nobukti]);
            // return 'uuu';
        $xurut= $purut[0]->xurut;
        }else {
            // return 'mmm';
            $xurut = $req->urut;
        }

    }else{
        // return 'ttt';
        $xurut=1;
    }
    if ($choice == "I" && $jmlrecord == 0) {
        $check = DB::connection('SML')->select('select * from DBPPL where Nobukti = :nobukti', ['nobukti' => $nobukti]);
        if ($check) {
            return 2;
        }
    }
    $values = [
        $choice,
        $nobukti,
        strval($req->nourut),
        date('Y-m-d H:i:s', strtotime($req->tanggal)),
        (int) $req->urut,
        $req->kodebarang,
         $req->qnt,
        (int) $req->nosat,
        $req->satuan,
         $req->isi,
        $req->keterangan ?? '',
        (int) $req->isclose,
        (int) $req->isclosed,
        $req->kodedepartemen,
        $req->keterangannama ?? '',
        (int) $req->isjasa,
        $req->noso ?? '',
        (int) $req->urutso,
        (int) $req->pagen,
        $req->nopocust ?? '',
        (int) $req->jmlrecord,
        (int) $req->pjasa
    ];
    DB::connection('SML')->statement('exec sp_PPL ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);
      $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $req->choice,'PR',$nobukti,'',$xurut,'DBPPLDET');
    return 1;
  }

  // public function updateOtorisasi (Request $req) {
  //   $username = \Auth::user()->username;
  //   $nobukti =  $req->nobukti;
  //   $tanggal = date('Y-m-d H:i:s');
  //   $otorisasi = $req->otorisasi;

  //   if ($otorisasi == 0 ) {
  //     $username = '';
  //     $tanggal = null;
  //   }

  //   $update = DB::connection('SML')->update('update DBPPL set IsOtorisasi1 = :otorisasi , OtoUser1 = :username , TglOto1 = :tanggal where nobukti = :nobukti', ['otorisasi' => $otorisasi, 'username' => $username, 'tanggal' => $tanggal, 'nobukti' => $nobukti, ] );
  //   return $update;

  // }


  public function spDelete (Request $req) {

    // $listData = $req->listData;
    // foreach ($listData as $d) {
      // code...
       $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $req->choice,'PR',$req->nobukti,'',$req->urut,'DBPPLDET');
      $values = [
        $req->choice,
        $req->nobukti,
        $req->nourut,
        $req->tanggal,
        $req->urut,
        $req->kodebarang,
        $req->qnt,
        $req->nosat,
        $req->satuan,
        $req->isi,
        $req->keterangan,
        $req->isclose,
        $req->isclosed,
        $req->kddep,
        $req->keterangannama,
        $req->isjasa,
        $req->noso,
        $req->urutso,
        $req->pagen,
        $req->nopocust,
        $req->jmlrecord,
        $req->pjasa

      ];
      DB::connection('SML')->statement('exec sp_PPL ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);
    // }
    return 1;
    // foreach ($penerimaan as $p) {
    //   // code...
    //   array_push($tempPenerimaan, $p);
    // }
    //
    // DB::connection('SML')->statement('exec sp_RSPB ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?' ,$values);
    // return 1;
  }

}
