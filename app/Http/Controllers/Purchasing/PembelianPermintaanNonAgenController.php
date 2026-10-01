<?php

namespace App\Http\Controllers\Purchasing;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Models\VwPPL;
use App\Models\DBFLMENU;

class PembelianPermintaanNonAgenController extends Controller
{

  public function index(Request $req) {

    $kodemenu = '030101';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu , $req->path());
    if(!$akses || !$akses->HASACCESS) {
        return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(3);

    // Data tabel (listData1/listData2, header kolom, dsb) TIDAK disiapkan di sini karena
    // blade memuatnya sendiri lewat AJAX loadAll() saat halaman ready - menyiapkannya di
    // sini dobel kerja dan bikin buka halaman lemot.
    return view('purchasing.pembelianpermintaannonagen' ,[
        "menul0" => $menul0,
        "periode" => $periode,
        "akses" => $akses,
    ]);
}

  public function loadAll (Request $req)
{
    $queryOtorisasi = '';
  if ($req->isoto != 2 ) {
    $queryOtorisasi = ' and IsOtorisasi1 = ' . $req->isoto;

  };

    // Qnt/QntBatal/QntPO ikut diambil (dengan alias eksplisit, supaya key JSON-nya pasti
    // "Qnt"/"QntBatal"/"QntPO") untuk menghitung badge Status "Sudah/Belum/Batal" di
    // pembelianpermintaannonagen.blade.php (prStatusPR()). Ketiganya dikecualikan dari
    // penurunan kolom default di bawah supaya tidak ikut jadi kolom tabel.
    $outstanding = DB::connection("SML")->select("select NoBukti  , Tanggal  , IsOtorisasi1, TglOto1, OtoUser1, Qnt AS Qnt, QntBatal AS QntBatal, QntPO AS QntPO  from vwppl where  Tanggal between :tglawal and :tglakhir and IsJasa = 0 and pAgen = 0 " . $queryOtorisasi . " order by Tanggal desc, NoBukti desc" , ["tglawal" => $req->tglawal , "tglakhir" => $req->tglakhir ]);
    $collection1 = collect($outstanding)->groupBy('NoBukti');
    $tempOutstanding = [];
    foreach ($collection1 as $groupedData) {
        array_push($tempOutstanding, $groupedData);
    }



    $tempOtorisasi = [];

    $headertable = DB::connection("SML")->select("select *  from dbheadertable where  href= :href and username = :username "  , ["username" => \Auth::user()->username , "href" => $req->href]);
    $isnumberheadertable = [];
    $headertablevalue = [];
    $headertableheader = [];
    $headerisshown = [];
    $aliasOrdered = [];

    $isparsed = 0;
    $headertablealias = [];

    $xxx = DB::connection("SML")->select("select * from DBHEADERTABLEALIAS  where  href = :href "  , ["href" => $req->href ]);
    if ($xxx ) {

      $headertablealias = json_decode($xxx[0]->value);
    } else {

      $headertablealias = [];
    }
    if (count($headertable) > 0) {
      $isnumberheadertable = json_decode($headertable[0]->isnumber);
      $headertablevalue = json_decode($headertable[0]->value);
      $headertableheader = json_decode($headertable[0]->header);
      $isparsed = 0;
      $headerisshown = json_decode($headertable[0]->isshown);
    } else {

      $isparsed = 1;
      // Qnt/QntBatal/QntPO dipakai hanya untuk menghitung badge Status di JS, bukan kolom
      // tabel - lihat komentar di query vwppl di atas.
      $kolomInternal = ['Qnt', 'QntBatal', 'QntPO'];
      if(!$tempOutstanding) {

      } else {
        foreach ($tempOutstanding[0][0] as $key => $value) {

          if (str_contains($key, "Oto") || in_array($key, $kolomInternal, true)) {


          } else {

              array_push($headertablevalue, $key);
            array_push($headertableheader, $key);
            array_push($headerisshown, 1);
            if (strtotime($value)) {

                  array_push( $isnumberheadertable , 2);
            } else if (is_numeric($value)) {
                array_push( $isnumberheadertable , 1);
            } else {

                  array_push($isnumberheadertable,0);
            }
          }



        }
      }




    }

    $aliasOrdered = [];
    if ($headertablealias) {
      foreach ($headertablevalue as $header) {
          foreach ($headertablealias as $item) {
              if ($item->value === $header) {

                    array_push( $aliasOrdered , $item);
                  break;
              }
          }
      }
    } else {
      foreach ($headertablevalue as $header) {


                    array_push( $aliasOrdered , ["value" => $header , "alias" => $header]);


      }
    }

    return [
      "aliasordered" => $aliasOrdered,
      "headertableheader" => $headertableheader ,
      "isnumeric" => $isnumberheadertable,
      "headertablevalue" => $headertablevalue,
      "isparsed" => $isparsed ,
      "isshown" => $headerisshown,
        "listData1" => $tempOutstanding,   // Belum Otorisasi
        "listData2" => $tempOtorisasi      // Sudah Otorisasi
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

    return ["ok" => $res, "otouser" => \Auth::user()->username, "tgloto" => $tanggal];
}
  public function updateBatalOtorisasi(Request $req) {


    $res = DB::connection('SML')->update(
        "UPDATE DBPPL SET isOtorisasi1 = 0, maxol = -1, OtoUser1 = '', TglOto1 = NULL WHERE NoBukti = :nobukti",
        [
            "nobukti" => $req->nobukti
        ]
    );

    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','PR',$req->nobukti,$req->pket,0,'DBPPL');
    return ["ok" => $res];
}
  public function cekOtorisasi (Request $req) {
    $res = DB::connection('SML')->select("select isOtorisasi1 from DBPPL where nobukti = :nobukti", ["nobukti" => $req->nobukti ]);
    return $res;
  }

  public function spDetail (Request $req) {
    return DB::connection('SML')->select(
      "select * from VwPPL where NoBukti = :nobukti order by Urut",
      ["nobukti" => $req->nobukti]
    );
  }

  public function listDepartemen (Request $req) {
    $listDepartemen = DB::connection("SML")->select('select * from DBDEPART');
    return $listDepartemen;
  }

  public function getNoBukti (Request $req) {
    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $inisial = DB::connection("SML")->select('select PR from DBNOMOR');
    $values = [
        $inisial[0]->PR,
        $periode->bulan,
        $periode->tahun,
        $username,
    ];
    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
    return $noBukti;
  }

  public function listBarang (Request $req) {
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
        JOIN DBMERK b ON a.KodeMerk = b.KODEMERK
        WHERE a.KODEGRP = 'BJ'
	AND a.IsAktif = 1
        AND a.pAgen = ?
        AND (a.KODEBRG LIKE ? OR a.NAMABRG LIKE ?)
        ORDER BY a.KODEBRG ASC
    ", [
        $req->isagen,
        $search,
        $search
    ]);

    return response()->json($listData);
  }


  public function spAdd (Request $req) {
    $choice = $req->choice;
    $jmlrecord = $req->jmlrecord;
    $nobukti = $req->nobukti;
    $xurut=0;

     $purut = DB::connection('SML')->select('select * from DBPPLdet where Nobukti = :nobukti', ['nobukti' => $nobukti]);
    if ($purut){

        if ($choice=='I' ){

        $purut = DB::connection('SML')->select('select max(urut)+1 xurut from DBPPLdet where Nobukti = :nobukti', ['nobukti' => $nobukti]);
        $xurut= $purut[0]->xurut;
        }else {
            $xurut = $req->urut;
        }

    }else{
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

  public function spDelete (Request $req) {

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

    return 1;
  }

    public function spCetak (Request $req)
      {
          $noBukti = $req->input('NOBUKTI');

          $cetak = DB::connection("SML")->select(
              "EXEC CetakPR ?",
              [$noBukti]
          );

          $tempCetak1 = [];
          foreach ($cetak as $p) {
              array_push($tempCetak1, $p);
          }

          return $tempCetak1;
      }

}
