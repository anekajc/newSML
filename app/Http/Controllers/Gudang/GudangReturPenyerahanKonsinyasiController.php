<?php

namespace App\Http\Controllers\Gudang;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\NewPeriode;
use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Model\DBFLMENU;
use Illuminate\Support\Facades\Auth;



// use App\Http\Controllers\NewMenuController;

class GudangReturPenyerahanKonsinyasiController extends Controller
{
  public function index(Request $req) {
    $kodemenu = '06060';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses1($kodemenu , $req->path());
    if (!$akses || !$akses->HASACCESS) {
        return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(6);

    $tempOutstanding = DB::connection("SML")->select("
        SELECT DISTINCT 
            A.NoBukti, A.NoUrut, A.Tanggal, A.KodeCUSTSUPP, B.NAMACUSTSUPP, C.NamaKota,
            A.IDUser,
            A.IsOtorisasi1, A.OtoUser1, A.TglOto1, 
            A.IsOtorisasi2, A.OtoUser2, A.TglOto2, 
            A.IsOtorisasi3, A.OtoUser3, A.TglOto3, 
            A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
            A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
            CAST(
                CASE 
                    WHEN 
                        (CASE WHEN A.IsOtorisasi1 = 1 THEN 1 ELSE 0 END +
                         CASE WHEN A.IsOtorisasi2 = 1 THEN 1 ELSE 0 END +
                         CASE WHEN A.IsOtorisasi3 = 1 THEN 1 ELSE 0 END +
                         CASE WHEN A.IsOtorisasi4 = 1 THEN 1 ELSE 0 END +
                         CASE WHEN A.IsOtorisasi5 = 1 THEN 1 ELSE 0 END) = A.MaxOL 
                    THEN 0 
                    ELSE 1 
                END 
            AS BIT) AS NeedOtorisasi
        FROM dbRSERAHSAMPLE A
        LEFT OUTER JOIN DBCUSTSUPP B ON A.KODECUSTSUPP = B.KODECUSTSUPP
        LEFT OUTER JOIN DBKOTA C ON B.KOTA = C.KodeKota
        WHERE YEAR(A.Tanggal) = :tahun AND MONTH(A.Tanggal) = :bulan and A.IsOtorisasi1 = 0 AND ISNULL(A.pkonsi, 0) = 1
        ORDER BY A.NoBukti", [
        "tahun" => $periode->tahun,
        "bulan" => $periode->bulan
    ]);

    $tempOutstanding2 = DB::connection("SML")->select("
        SELECT DISTINCT 
            A.NoBukti, A.NoUrut, A.Tanggal, A.KodeCUSTSUPP, B.NAMACUSTSUPP, C.NamaKota,
            A.IDUser,
            A.IsOtorisasi1, A.OtoUser1, A.TglOto1, 
            A.IsOtorisasi2, A.OtoUser2, A.TglOto2, 
            A.IsOtorisasi3, A.OtoUser3, A.TglOto3, 
            A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
            A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
            CAST(
                CASE 
                    WHEN 
                        (CASE WHEN A.IsOtorisasi1 = 1 THEN 1 ELSE 0 END +
                         CASE WHEN A.IsOtorisasi2 = 1 THEN 1 ELSE 0 END +
                         CASE WHEN A.IsOtorisasi3 = 1 THEN 1 ELSE 0 END +
                         CASE WHEN A.IsOtorisasi4 = 1 THEN 1 ELSE 0 END +
                         CASE WHEN A.IsOtorisasi5 = 1 THEN 1 ELSE 0 END) = A.MaxOL 
                    THEN 0 
                    ELSE 1 
                END 
            AS BIT) AS NeedOtorisasi
        FROM dbRSERAHSAMPLE A
        LEFT OUTER JOIN DBCUSTSUPP B ON A.KODECUSTSUPP = B.KODECUSTSUPP
        LEFT OUTER JOIN DBKOTA C ON B.KOTA = C.KodeKota
        WHERE YEAR(A.Tanggal) = :tahun AND MONTH(A.Tanggal) = :bulan and A.IsOtorisasi1 = 1 AND ISNULL(A.pkonsi, 0) = 1
        ORDER BY A.NoBukti", [
        "tahun" => $periode->tahun,
        "bulan" => $periode->bulan
    ]);

    return view('gudang.gudangreturpenyerahankonsinyasi', [
        "menul0" => $menul0,
        "periode" => $periode,
        "listData" => $tempOutstanding,
        "listData2" => $tempOutstanding2,
        "akses" => $akses
    ]);
  }

  public function loadAll()
{
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $tempOutstanding = DB::connection("SML")->select("
        SELECT DISTINCT 
            A.NoBukti, A.NoUrut, A.Tanggal, A.KodeCUSTSUPP, B.NAMACUSTSUPP, C.NamaKota,
            A.IDUser,
            A.IsOtorisasi1, A.OtoUser1, A.TglOto1, 
            A.IsOtorisasi2, A.OtoUser2, A.TglOto2, 
            A.IsOtorisasi3, A.OtoUser3, A.TglOto3, 
            A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
            A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
            CAST(
                CASE 
                    WHEN 
                        (CASE WHEN A.IsOtorisasi1 = 1 THEN 1 ELSE 0 END +
                         CASE WHEN A.IsOtorisasi2 = 1 THEN 1 ELSE 0 END +
                         CASE WHEN A.IsOtorisasi3 = 1 THEN 1 ELSE 0 END +
                         CASE WHEN A.IsOtorisasi4 = 1 THEN 1 ELSE 0 END +
                         CASE WHEN A.IsOtorisasi5 = 1 THEN 1 ELSE 0 END) = A.MaxOL 
                    THEN 0 
                    ELSE 1 
                END 
            AS BIT) AS NeedOtorisasi
        FROM dbRSERAHSAMPLE A
        LEFT OUTER JOIN DBCUSTSUPP B ON A.KODECUSTSUPP = B.KODECUSTSUPP
        LEFT OUTER JOIN DBKOTA C ON B.KOTA = C.KodeKota
        WHERE YEAR(A.Tanggal) = :tahun AND MONTH(A.Tanggal) = :bulan and A.IsOtorisasi1 = 0 AND ISNULL(A.pkonsi, 0) = 1
        ORDER BY A.NoBukti
    ", [
        "tahun" => $periode->tahun,
        "bulan" => $periode->bulan
    ]);

    $tempOutstanding2 = DB::connection("SML")->select("
        SELECT DISTINCT 
            A.NoBukti, A.NoUrut, A.Tanggal, A.KodeCUSTSUPP, B.NAMACUSTSUPP, C.NamaKota,
            A.IDUser,
            A.IsOtorisasi1, A.OtoUser1, A.TglOto1, 
            A.IsOtorisasi2, A.OtoUser2, A.TglOto2, 
            A.IsOtorisasi3, A.OtoUser3, A.TglOto3, 
            A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
            A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
            CAST(
                CASE 
                    WHEN 
                        (CASE WHEN A.IsOtorisasi1 = 1 THEN 1 ELSE 0 END +
                         CASE WHEN A.IsOtorisasi2 = 1 THEN 1 ELSE 0 END +
                         CASE WHEN A.IsOtorisasi3 = 1 THEN 1 ELSE 0 END +
                         CASE WHEN A.IsOtorisasi4 = 1 THEN 1 ELSE 0 END +
                         CASE WHEN A.IsOtorisasi5 = 1 THEN 1 ELSE 0 END) = A.MaxOL 
                    THEN 0 
                    ELSE 1 
                END 
            AS BIT) AS NeedOtorisasi
        FROM dbRSERAHSAMPLE A
        LEFT OUTER JOIN DBCUSTSUPP B ON A.KODECUSTSUPP = B.KODECUSTSUPP
        LEFT OUTER JOIN DBKOTA C ON B.KOTA = C.KodeKota
        WHERE YEAR(A.Tanggal) = :tahun AND MONTH(A.Tanggal) = :bulan and A.IsOtorisasi1 = 1 AND ISNULL(A.pkonsi, 0) = 1
        ORDER BY A.NoBukti
    ", [
        "tahun" => $periode->tahun,
        "bulan" => $periode->bulan
    ]);

    return response()->json([
        "outstanding" => $tempOutstanding,
        "outstanding2" => $tempOutstanding2
    ]);
}

  public function updateOtorisasi(Request $req) {
    $tanggal = now();
    $res = DB::connection('SML')->update(
        "UPDATE DBRSERAHSAMPLE SET isOtorisasi1 = 1, maxol = 1, OtoUser1 = :username, TglOto1 = :tanggal WHERE NoBukti = :nobukti",
        [
            "username" => \Auth::user()->username,
            "tanggal" => $tanggal,
            "nobukti" => $req->nobukti
        ]
    );
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','RSK',$req->nobukti,'',0,'DBRSERAHSAMPLE');
    return $res;
}
  public function updateBatalOtorisasi(Request $req) {
    $res = DB::connection('SML')->update(
        "UPDATE DBRSERAHSAMPLE SET isOtorisasi1 = 0, maxol = -1, OtoUser1 = '', TglOto1 = NULL WHERE NoBukti = :nobukti",
        [
            "nobukti" => $req->nobukti
        ]
    );
     $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','PR',$req->nobukti,$req->pket,0,'DBRSERAHSAMPLE');

    return $res;
}
  public function cekOtorisasi (Request $req) {
    $res = DB::connection('SML')->select("select isOtorisasi1 from DBRSERAHSAMPLE where nobukti = :nobukti", ["nobukti" => $req->nobukti ]);
    return $res;
  }

  public function spDetail (Request $req)
{
    $res = DB::connection('SML')->select("
        DECLARE @NoBukti VARCHAR(30);
        SELECT @NoBukti = :nobukti;

        SELECT 
            A.NOBUKTI, 
            A.NOURUT, 
            A.TANGGAL, 
            A.note AS Keterangan,
            B.URUT, 
            B.KODEBRG, 
            C.NamaBrg, 
            '' AS Jns_Kertas, 
            '' AS Ukr_Kertas,
            B.QNT, 
            B.QNT2, 
            B.SAT_1, 
            B.SAT_2, 
            B.NoSat, 
            B.ISI, 
            B.gdgAsal, 
            C.SAT1,
            C.SAT2,
            C.SAT3,
            C.ISI1,
            C.ISI2,
            C.ISI3,
            D.NAMA + ' (' + B.gdgAsal + ')' AS NamaGgdAsal, 
            0.00 AS GSM,
            B.gdgTujuan, 
            E.NAMA + ' (' + B.gdgTujuan + ')' AS NamaGgdTujuan,
            A.KODECUSTSUPP, 
            F.NamaCustSupp,
            A.KODESLS, 
            G.Nama AS NAMASLS,
            B.pbonus,
            B.Noserahsample,
            B.UrutSerahSample,
            (
                (CASE WHEN S.NOSAT = 1 THEN S.Qnt ELSE S.Qnt2 END) -
                (CASE WHEN S.NOSAT = 1 THEN ISNULL(H.Qnt,0) ELSE ISNULL(H.Qnt2,0) END)
            ) AS QntSisa
        FROM DBRSERAHSAMPLE A
        LEFT JOIN DBRSERAHSAMPLEDET B ON B.NoBukti = A.NoBukti
        LEFT JOIN dbBarang C ON C.KodeBrg = B.KodeBrg
        LEFT JOIN DBGUDANG D ON D.KODEGDG = B.GdgAsal
        LEFT JOIN DBGUDANG E ON E.KODEGDG = B.GdgTujuan
        LEFT JOIN DbCustSupp F ON F.KodeCustSupp = A.KODECUSTSUPP
        LEFT JOIN dbKaryawan G ON A.KODESLS = G.KeyNIK
        LEFT JOIN (
            SELECT NOserahSample, UrutSerahsample, SUM(QNT) Qnt, SUM(Qnt2) Qnt2, KODEBRG 
            FROM DBRSERAHSAMPLEDET 
            GROUP BY NOserahSample, UrutSerahsample, KODEBRG
        ) H ON B.NoSerahSample = H.NoSerahSample 
            AND B.UrutSerahSample = H.UrutSerahSample 
            AND B.KodeBrg = H.KodeBrg
        LEFT JOIN DBSERAHSAMPLEDET S ON B.NoSerahSample = S.NoBukti 
            AND B.UrutSerahSample = S.Urut
            AND B.KodeBrg = S.KodeBrg
        WHERE A.NoBukti = @NoBukti
        ORDER BY B.Urut
    ", ['nobukti' => $req->nobukti]);

    return response()->json($res);
}

  // public function spNobukti (Request $req) {
  //   $inisial = DB::connection('SML')->select("SELECT PPL FROM DBNOMOR");
  // }

  public function Sales (Request $req)
  {
    $Sales = DB::connection("SML")->select("
      SELECT 
      KeyNIK AS KodeSls, 
      NIK, 
      Nama AS namaSls 
      FROM dbKaryawan 
      WHERE Aktif = 1");

    return response()->json($Sales);
  }

public function SerahKonsinyasi (Request $req)
{
    $sales   = $req->input('sales');
    $exclude = json_decode($req->input('exclude', '[]'), true);
    $nobukti = $req->input('nobukti');
    $urut    = $req->input('urut');
    $kodebrg = $req->input('kodebrg');

    $query = "
        SELECT 
            A.NOBUKTI,
            A.Urut,
            B.TANGGAL,
            B.KODECUSTSUPP,
            C.NAMACUSTSUPP,
            A.Kodebrg,
            E.NamaBrg,
            A.GdgAsal,
            E.SAT1, E.SAT2, E.SAT3,
            A.Qnt - ISNULL(D.Qnt, 0) AS QntSisa
        FROM DBSERAHSAMPLEDET A
        LEFT JOIN DBSERAHSAMPLE B ON A.NOBUKTI = B.NOBUKTI
        LEFT JOIN DBCUSTSUPP C ON B.KODECUSTSUPP = C.KODECUSTSUPP
        LEFT JOIN (
            SELECT NOserahSample, UrutSerahsample, SUM(QNT) Qnt, SUM(Qnt2) Qnt2, KODEBRG
            FROM DBRSERAHSAMPLEDET
            GROUP BY NOserahSample, UrutSerahsample, KODEBRG
        ) D ON A.NOBUKTI = D.NOserahSample AND A.Urut = D.UrutSerahsample
        LEFT JOIN DBBARANG E ON A.Kodebrg = E.Kodebrg
        WHERE A.Qnt - ISNULL(D.Qnt, 0) > 0
          AND ISNULL(B.IsOtorisasi1, 0) = 1
          AND ISNULL(B.pkonsi, 0) = 1
    ";

    $bindings = [];

    if (!empty($nobukti)) {
        $query .= " AND A.NOBUKTI = ? ";
        $bindings[] = $nobukti;
    }

    if (!empty($urut)) {
        $query .= " AND A.Urut = ? ";
        $bindings[] = $urut;
    }

    if (!empty($kodebrg)) {
        $query .= " AND A.Kodebrg = ? ";
        $bindings[] = $kodebrg;
    }

    if (!empty($sales)) {
        $query .= " AND B.KodeSls = ? ";
        $bindings[] = $sales;
    }

    if (!empty($exclude)) {
        $excludeClauses = [];
        foreach ($exclude as $item) {
            if (!empty($item['nobukti']) && !empty($item['urut'])) {
                $excludeClauses[] = "NOT (A.NOBUKTI = ? AND A.Urut = ?)";
                $bindings[] = $item['nobukti'];
                $bindings[] = $item['urut'];
            }
        }
        if (!empty($excludeClauses)) {
            $query .= " AND " . implode(" AND ", $excludeClauses);
        }
    }

    $query .= "
        GROUP BY 
            A.NOBUKTI,
            A.Urut,
            B.TANGGAL,
            B.KODECUSTSUPP,
            C.NAMACUSTSUPP,
            A.Kodebrg,
            E.NamaBrg,
            A.GdgAsal,
            E.SAT1, E.SAT2, E.SAT3,
            A.Qnt, D.Qnt, B.NOURUT
        ORDER BY B.TANGGAL, B.NOURUT, A.NOBUKTI, A.KODEBRG
    ";

    $data = DB::connection('SML')->select($query, $bindings);

    return response()->json($data);
}

  public function getDetailCetak(Request $req)
{
    $noBukti = $req->input('NOBUKTI');

    $cetak = DB::connection("SML")->select(
        "EXEC dbo.SP_CetakRSAMPLE ?",
        [$noBukti]
    );

    $tempCetak1 = [];
    foreach ($cetak as $p) {
        array_push($tempCetak1, $p);
    }

    return $tempCetak1;
}

  public function getNoBukti (Request $req) {

    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $inisial = DB::connection("SML")->select('select RSK from DBNOMOR');


    $values = [
        $inisial[0]->RSK,
        $periode->bulan,
        $periode->tahun,
        $username,
    ];

    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);

    return $noBukti;
  }

  public function listBarang (Request $req)
{
    $nobukti = $req->input('nobukti', ''); 
    $urut    = $req->input('urut', ''); 
    $kodebrg = $req->input('kodebrg', ''); 
    $search  = "%" . $req->input('search', '') . "%";

    $bindings = [$nobukti, $search, $search, $search];
    $filter = "A.NOBUKTI = ? 
               AND (A.Kodebrg LIKE ? OR E.NamaBrg LIKE ? OR E.partNumber LIKE ?)
               AND (CASE WHEN A.NOSAT=1 THEN A.Qnt ELSE A.Qnt2 END) - 
                   (CASE WHEN A.NOSAT=1 THEN ISNULL(D.Qnt,0) ELSE ISNULL(D.Qnt2,0) END) > 0
               AND ISNULL(B.pkonsi,0) = 1";

    if ($urut !== '') {
        $filter .= " AND A.Urut = ? ";
        $bindings[] = $urut;
    }

    if ($kodebrg !== '') {
        $filter .= " AND A.Kodebrg = ? ";
        $bindings[] = $kodebrg;
    }

    $sql = "
        SELECT 
            A.Kodebrg,
            E.NamaBrg,
            E.SAT1, E.SAT2, E.SAT3,
            CASE WHEN A.NOSAT = 1 THEN A.Qnt ELSE A.Qnt2 END Qnt,
            CASE WHEN A.NOSAT = 1 THEN ISNULL(D.Qnt,0) ELSE ISNULL(D.Qnt2,0) END QntRetur,
            (CASE WHEN A.NOSAT = 1 THEN A.Qnt ELSE A.Qnt2 END) -
            (CASE WHEN A.NOSAT = 1 THEN ISNULL(D.Qnt,0) ELSE ISNULL(D.Qnt2,0) END) QntSisa,
            A.NOSAT,
            CASE 
                WHEN A.NOSAT = 1 THEN E.SAT1 
                WHEN A.NOSAT = 2 THEN E.SAT2 
                WHEN A.NOSAT = 3 THEN E.SAT3 
            END Satuan,
            A.NOBUKTI,
            A.Urut,
            B.TANGGAL,
            B.KODECUSTSUPP,
            C.NAMACUSTSUPP,
            A.GdgAsal
        FROM DBSERAHSAMPLEDET A
        LEFT JOIN DBSERAHSAMPLE B ON A.NOBUKTI = B.NOBUKTI
        LEFT JOIN DBCUSTSUPP C ON B.KODECUSTSUPP = C.KODECUSTSUPP
        LEFT JOIN (
            SELECT NOserahSample, UrutSerahsample, SUM(QNT) Qnt, SUM(Qnt2) Qnt2, KODEBRG 
            FROM DBRSERAHSAMPLEDET 
            GROUP BY NOserahSample, UrutSerahsample, KODEBRG  
        ) D ON A.NOBUKTI = D.NOserahSample AND A.Urut = D.UrutSerahsample 
        LEFT JOIN DBBARANG E ON A.Kodebrg = E.Kodebrg 
        WHERE $filter
        GROUP BY 
            A.NOBUKTI, A.Urut, B.TANGGAL, B.KODECUSTSUPP, C.NAMACUSTSUPP,
            A.Kodebrg, E.NamaBrg, A.GdgAsal, B.NOURUT, A.NOSAT,
            A.QNT, A.QNT2, D.QNT, D.QNT2, E.SAT1, E.SAT2, E.SAT3
        ORDER BY B.TANGGAL, B.NOURUT, A.NOBUKTI, A.KODEBRG
    ";

    $listData = DB::connection('SML')->select($sql, $bindings);
    return response()->json($listData);
}


  public function spAdd(Request $req)
{
    $choice = $req->choice;
    $nobukti = $req->nobukti;
    $jmlrecord = $req->jmlrecord;
    $xurut=0;

//  return ["asd" => $nobukti] ;
     $purut = DB::connection('SML')->select('select * from DBRSERAHSAMPLEDET where Nobukti = :nobukti', ['nobukti' => $nobukti]);
    if ($purut){

        if ($choice=='I' ){

        $purut = DB::connection('SML')->select('select max(urut)+1 xurut from DBRSERAHSAMPLEDET where Nobukti = :nobukti', ['nobukti' => $nobukti]);
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
    // return ["asd" => $xurut] ;


if ($choice =='D'){
      $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $req->choice,'RSK',$nobukti,'',$xurut,'DBRSERAHSAMPLEDET');
      }


    if ($choice == "I" && $jmlrecord == 0) {
        $check = DB::connection('SML')->select('SELECT * FROM DBRSERAHSAMPLE WHERE Nobukti = :nobukti', ['nobukti' => $nobukti]);
        if ($check) {
            return 2;
        }
    }

    $username = \Auth::user()->username;

    $values = [
        $choice,                      
        $nobukti,                      
        strval($req->nourut),        
        date('Y-m-d H:i:s', strtotime($req->tanggal)), 
        $req->note,              
        (int)$req->urut,                 
        $req->kodebarang,            
        $req->gdgasal,                
        $req->gdgtujuan,    
        $req->sat_1,                   
        $req->sat_2,                   
        (float)$req->qnt,            
        (float)$req->qnt2,           
        (int)$req->nosat,            
        (float)$req->isi,            
        $username,                     
        $req->kodecustsupp,              
        (int)$req->kodesls,             
        (int)($req->pbonus ?? 0),        
        (int)($req->maxol),      
        $req->noserahsample,       
        (int)($req->urutserahsample),
        1       
    ];

    DB::connection('SML')->statement('EXEC SP_RSerahSample ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);
if ($choice !='D'){
      $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $req->choice,'RSK',$nobukti,'',$xurut,'DBRSERAHSAMPLEDET');
      }
    return 1;
}


  public function spDelete(Request $req)
{
    $choice = $req->choice;
    $nobukti = $req->nobukti;

    $username = \Auth::user()->username;

    $values = [
        $choice,                               
        $nobukti,                               
        strval($req->nourut),                  
        date('Y-m-d H:i:s', strtotime($req->tanggal)), 
        $req->note ?? '',                     
        (int)$req->urut,                      
        $req->kodebarang,                     
        $req->gdgasal ?? '',               
        $req->gdgtujuan ?? '',                
        $req->sat_1 ?? '',                 
        $req->sat_2 ?? '',                
        (float)$req->qnt,                    
        (float)($req->qnt2 ?? $req->qnt),   
        (int)($req->nosat ?? 1),              
        (float)($req->isi ?? 1),            
        $username,                           
        $req->kodecustsupp ?? '',              
        (int)($req->kodesls ?? 0),              
        (int)($req->pbonus ?? 0),               
        (int)($req->maxol ?? 0),                
        $req->noserahsample ?? '',             
        (int)($req->urutserahsample ?? 0),     
        1                
    ];

    DB::connection('SML')->statement('EXEC SP_RSerahSample ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);
     $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $req->choice,'RSK',$nobukti,'',$req->urut,'DBRSERAHSAMPLEDET');
    return 1;
}

}
