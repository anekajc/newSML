<?php

namespace App\Http\Controllers\Gudang;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\DBFLMENU;
use App\Model\NewPeriode;
use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;



class GudangPenyerahanKonsinyasiController extends Controller
{
  public function index(Request $req) {
    $kodemenu = '06061';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses1($kodemenu , $req->path());
    if (!$akses || !$akses->HASACCESS) {
        return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(6);

    $tempOutstanding = DB::connection("SML")->select("
        SELECT 
            A.NoBukti, A.NoUrut, A.Tanggal, A.KodeCustSupp, 
            C.NAMACUSTSUPP, D.NamaKota,
            A.IDUser, A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
            A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
            A.IsOtorisasi3, A.OtoUser3, A.TglOto3,
            A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
            A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
            0 AS statusOtorisasi,
            E.Nama AS namasls,
            A.TglKirim
        FROM DBPRSAMPLE A
        LEFT JOIN DBCUSTSUPP C ON A.KODECUSTSUPP = C.KODECUSTSUPP
        LEFT JOIN DBKOTA D ON C.Kota = D.KodeKota
        LEFT JOIN DBKaryawan E ON A.kodesls = E.Keynik
        WHERE 
            ISNULL(A.MaxOL, 0) > 0 AND
            (
                CAST(ISNULL(A.IsOtorisasi1, 0) AS INT) +
                CAST(ISNULL(A.IsOtorisasi2, 0) AS INT) +
                CAST(ISNULL(A.IsOtorisasi3, 0) AS INT) +
                CAST(ISNULL(A.IsOtorisasi4, 0) AS INT) +
                CAST(ISNULL(A.IsOtorisasi5, 0) AS INT)
            ) = A.MaxOL
            AND EXISTS (
                SELECT 1
                FROM DBPRSAMPLEDET AA
                LEFT JOIN (
                    SELECT NOPRSAMPLE, URUTPRSAMPLE, SUM(QNT) Qnt1, SUM(QNT2) Qnt2 
                    FROM DBSERAHSAMPLEDET
                    GROUP BY NOPRSAMPLE, URUTPRSAMPLE
                ) BB ON AA.NoBukti = BB.NOPRSAMPLE AND AA.Urut = BB.URUTPRSAMPLE
                WHERE 
                    AA.NOBUKTI = A.NOBUKTI AND 
                    ISNULL(AA.QNT, 0) - ISNULL(BB.Qnt1, 0) > 0 AND
                    ISNULL(A.pKonsi, 0) = 1
            )
        ORDER BY A.NoBukti");

    $tempPenerimaan = DB::connection("SML")->select("
    SELECT 
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
        A.NOBUKTI, A.NOURUT, A.TANGGAL, A.note AS Keterangan, 
        A.IDUSER, A.NoPenyerahan,
        A.KODECUSTSUPP, F.NamaCustSupp,
        A.KODESLS, G.Nama AS NAMASLS,
        AA.RefPR,
        COUNT(B.URUT) AS JumlahItem,
        SUM(CASE WHEN B.NOSAT = 1 THEN ISNULL(B.QNT, 0) ELSE ISNULL(B.QNT2, 0) END) AS TotalQntSample,
        SUM(ISNULL(BB.QNTOUTSTANDING, 0)) AS TotalQntOutstanding
    FROM DBserahSAMPLE A
    LEFT JOIN DBserahSAMPLEDET B ON B.NoBukti = A.NoBukti
    LEFT JOIN DbCustSupp F ON F.KodeCustSupp = A.KODECUSTSUPP
    LEFT JOIN dbKaryawan G ON A.KODESLS = G.KeyNIK
    LEFT JOIN DBPRSAMPLE AA ON B.NOPRSAMPLE = AA.NOBUKTI
    LEFT JOIN (
        SELECT 
            A.NOBUKTI, A.URUT,
            CASE 
                WHEN A.NOSAT = 1 THEN ISNULL(A.QNT, 0) - ISNULL(B.Qnt1, 0) 
                ELSE ISNULL(A.QNT2, 0) - ISNULL(B.Qnt2, 0) 
            END AS QNTOUTSTANDING
        FROM DBPRSAMPLEDET A 
        LEFT JOIN (
            SELECT NOPRSAMPLE, URUTPRSAMPLE, SUM(QNT) AS Qnt1, SUM(QNT2) AS Qnt2
            FROM DBSERAHSAMPLEDET
            GROUP BY NOPRSAMPLE, URUTPRSAMPLE
        ) B ON A.NoBukti = B.NOPRSAMPLE AND A.Urut = B.URUTPRSAMPLE
    ) BB ON BB.NOBUKTI = B.NOPRSAMPLE AND BB.URUT = B.URUTPRSAMPLE
    WHERE MONTH(A.TANGGAL) = :bulan 
        AND YEAR(A.TANGGAL) = :tahun
        and A.IsOtorisasi1 = 0
        AND ISNULL(AA.pKonsi, 0) = 1
    GROUP BY 
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
        A.NOBUKTI, A.NOURUT, A.TANGGAL, A.note, 
        A.IDUSER, A.NoPenyerahan,
        A.KODECUSTSUPP, F.NamaCustSupp,
        A.KODESLS, G.Nama,
        AA.RefPR
    ORDER BY A.NOBUKTI", ["tahun" => $periode->tahun, "bulan" => $periode->bulan]);

    $tempPenerimaan2 = DB::connection("SML")->select("
    SELECT 
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
        A.NOBUKTI, A.NOURUT, A.TANGGAL, A.note AS Keterangan, 
        A.IDUSER, A.NoPenyerahan,
        A.KODECUSTSUPP, F.NamaCustSupp,
        A.KODESLS, G.Nama AS NAMASLS,
        AA.RefPR,
        COUNT(B.URUT) AS JumlahItem,
        SUM(CASE WHEN B.NOSAT = 1 THEN ISNULL(B.QNT, 0) ELSE ISNULL(B.QNT2, 0) END) AS TotalQntSample,
        SUM(ISNULL(BB.QNTOUTSTANDING, 0)) AS TotalQntOutstanding
    FROM DBserahSAMPLE A
    LEFT JOIN DBserahSAMPLEDET B ON B.NoBukti = A.NoBukti
    LEFT JOIN DbCustSupp F ON F.KodeCustSupp = A.KODECUSTSUPP
    LEFT JOIN dbKaryawan G ON A.KODESLS = G.KeyNIK
    LEFT JOIN DBPRSAMPLE AA ON B.NOPRSAMPLE = AA.NOBUKTI
    LEFT JOIN (
        SELECT 
            A.NOBUKTI, A.URUT,
            CASE 
                WHEN A.NOSAT = 1 THEN ISNULL(A.QNT, 0) - ISNULL(B.Qnt1, 0) 
                ELSE ISNULL(A.QNT2, 0) - ISNULL(B.Qnt2, 0) 
            END AS QNTOUTSTANDING
        FROM DBPRSAMPLEDET A 
        LEFT JOIN (
            SELECT NOPRSAMPLE, URUTPRSAMPLE, SUM(QNT) AS Qnt1, SUM(QNT2) AS Qnt2
            FROM DBSERAHSAMPLEDET
            GROUP BY NOPRSAMPLE, URUTPRSAMPLE
        ) B ON A.NoBukti = B.NOPRSAMPLE AND A.Urut = B.URUTPRSAMPLE
    ) BB ON BB.NOBUKTI = B.NOPRSAMPLE AND BB.URUT = B.URUTPRSAMPLE
    WHERE MONTH(A.TANGGAL) = :bulan 
        AND YEAR(A.TANGGAL) = :tahun
        and A.IsOtorisasi1 = 1
        AND ISNULL(AA.pKonsi, 0) = 1
    GROUP BY 
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
        A.NOBUKTI, A.NOURUT, A.TANGGAL, A.note, 
        A.IDUSER, A.NoPenyerahan,
        A.KODECUSTSUPP, F.NamaCustSupp,
        A.KODESLS, G.Nama,
        AA.RefPR
    ORDER BY A.NOBUKTI", ["tahun" => $periode->tahun, "bulan" => $periode->bulan]);

    return view('gudang.gudangpenyerahankonsinyasi', [
        "menul0" => $menul0,
        "periode" => $periode,
        "tempOutstanding" => $tempOutstanding,
        "tempPenerimaan" => $tempPenerimaan,
        "tempPenerimaan2" => $tempPenerimaan2,
        "akses" => $akses
    ]);
}

  public function loadAll(Request $request)
{
    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    $tempOutstanding = DB::connection("SML")->select("
        SELECT 
            A.NoBukti, A.NoUrut, A.Tanggal, A.KodeCustSupp, 
            C.NAMACUSTSUPP, D.NamaKota,
            A.IDUser, A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
            A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
            A.IsOtorisasi3, A.OtoUser3, A.TglOto3,
            A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
            A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
            0 AS statusOtorisasi,
            E.Nama AS NAMASLS,
            A.TglKirim
        FROM DBPRSAMPLE A
        LEFT JOIN DBCUSTSUPP C ON A.KODECUSTSUPP = C.KODECUSTSUPP
        LEFT JOIN DBKOTA D ON C.Kota = D.KodeKota
        LEFT JOIN DBKaryawan E ON A.kodesls = E.Keynik
        WHERE 
            ISNULL(A.MaxOL, 0) > 0 AND
            (
                CAST(ISNULL(A.IsOtorisasi1, 0) AS INT) +
                CAST(ISNULL(A.IsOtorisasi2, 0) AS INT) +
                CAST(ISNULL(A.IsOtorisasi3, 0) AS INT) +
                CAST(ISNULL(A.IsOtorisasi4, 0) AS INT) +
                CAST(ISNULL(A.IsOtorisasi5, 0) AS INT)
            ) = A.MaxOL
            AND EXISTS (
                SELECT 1
                FROM DBPRSAMPLEDET AA
                LEFT JOIN (
                    SELECT NOPRSAMPLE, URUTPRSAMPLE, SUM(QNT) Qnt1, SUM(QNT2) Qnt2 
                    FROM DBSERAHSAMPLEDET
                    GROUP BY NOPRSAMPLE, URUTPRSAMPLE
                ) BB ON AA.NoBukti = BB.NOPRSAMPLE AND AA.Urut = BB.URUTPRSAMPLE
                WHERE 
                    AA.NOBUKTI = A.NOBUKTI AND 
                    ISNULL(AA.QNT, 0) - ISNULL(BB.Qnt1, 0) > 0 AND
                    ISNULL(A.pKonsi, 0) = 1
            )
        ORDER BY A.NoBukti
    ");

    $tempPenerimaan = DB::connection("SML")->select("
    SELECT 
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
        A.NOBUKTI, A.NOURUT, A.TANGGAL, A.note AS Keterangan, 
        A.IDUSER, A.NoPenyerahan,
        A.KODECUSTSUPP, F.NamaCustSupp,
        A.KODESLS, G.Nama AS NAMASLS,
        AA.RefPR,
        COUNT(B.URUT) AS JumlahItem,
        SUM(CASE WHEN B.NOSAT = 1 THEN ISNULL(B.QNT, 0) ELSE ISNULL(B.QNT2, 0) END) AS TotalQntSample,
        SUM(ISNULL(BB.QNTOUTSTANDING, 0)) AS TotalQntOutstanding
    FROM DBserahSAMPLE A
    LEFT JOIN DBserahSAMPLEDET B ON B.NoBukti = A.NoBukti
    LEFT JOIN DbCustSupp F ON F.KodeCustSupp = A.KODECUSTSUPP
    LEFT JOIN dbKaryawan G ON A.KODESLS = G.KeyNIK
    LEFT JOIN DBPRSAMPLE AA ON B.NOPRSAMPLE = AA.NOBUKTI
    LEFT JOIN (
        SELECT 
            A.NOBUKTI, A.URUT,
            CASE 
                WHEN A.NOSAT = 1 THEN ISNULL(A.QNT, 0) - ISNULL(B.Qnt1, 0) 
                ELSE ISNULL(A.QNT2, 0) - ISNULL(B.Qnt2, 0) 
            END AS QNTOUTSTANDING
        FROM DBPRSAMPLEDET A 
        LEFT JOIN (
            SELECT NOPRSAMPLE, URUTPRSAMPLE, SUM(QNT) AS Qnt1, SUM(QNT2) AS Qnt2
            FROM DBSERAHSAMPLEDET
            GROUP BY NOPRSAMPLE, URUTPRSAMPLE
        ) B ON A.NoBukti = B.NOPRSAMPLE AND A.Urut = B.URUTPRSAMPLE
    ) BB ON BB.NOBUKTI = B.NOPRSAMPLE AND BB.URUT = B.URUTPRSAMPLE
    WHERE MONTH(A.TANGGAL) = :bulan 
        AND YEAR(A.TANGGAL) = :tahun
        and A.IsOtorisasi1 = 0
        AND ISNULL(AA.pKonsi, 0) = 1
    GROUP BY 
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
        A.NOBUKTI, A.NOURUT, A.TANGGAL, A.note, 
        A.IDUSER, A.NoPenyerahan,
        A.KODECUSTSUPP, F.NamaCustSupp,
        A.KODESLS, G.Nama,
        AA.RefPR
    ORDER BY A.NOBUKTI", ["tahun" => $periode->tahun, "bulan" => $periode->bulan]);

    $tempPenerimaan2 = DB::connection("SML")->select("
    SELECT 
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
        A.NOBUKTI, A.NOURUT, A.TANGGAL, A.note AS Keterangan, 
        A.IDUSER, A.NoPenyerahan,
        A.KODECUSTSUPP, F.NamaCustSupp,
        A.KODESLS, G.Nama AS NAMASLS,
        AA.RefPR,
        COUNT(B.URUT) AS JumlahItem,
        SUM(CASE WHEN B.NOSAT = 1 THEN ISNULL(B.QNT, 0) ELSE ISNULL(B.QNT2, 0) END) AS TotalQntSample,
        SUM(ISNULL(BB.QNTOUTSTANDING, 0)) AS TotalQntOutstanding
    FROM DBserahSAMPLE A
    LEFT JOIN DBserahSAMPLEDET B ON B.NoBukti = A.NoBukti
    LEFT JOIN DbCustSupp F ON F.KodeCustSupp = A.KODECUSTSUPP
    LEFT JOIN dbKaryawan G ON A.KODESLS = G.KeyNIK
    LEFT JOIN DBPRSAMPLE AA ON B.NOPRSAMPLE = AA.NOBUKTI
    LEFT JOIN (
        SELECT 
            A.NOBUKTI, A.URUT,
            CASE 
                WHEN A.NOSAT = 1 THEN ISNULL(A.QNT, 0) - ISNULL(B.Qnt1, 0) 
                ELSE ISNULL(A.QNT2, 0) - ISNULL(B.Qnt2, 0) 
            END AS QNTOUTSTANDING
        FROM DBPRSAMPLEDET A 
        LEFT JOIN (
            SELECT NOPRSAMPLE, URUTPRSAMPLE, SUM(QNT) AS Qnt1, SUM(QNT2) AS Qnt2
            FROM DBSERAHSAMPLEDET
            GROUP BY NOPRSAMPLE, URUTPRSAMPLE
        ) B ON A.NoBukti = B.NOPRSAMPLE AND A.Urut = B.URUTPRSAMPLE
    ) BB ON BB.NOBUKTI = B.NOPRSAMPLE AND BB.URUT = B.URUTPRSAMPLE
    WHERE MONTH(A.TANGGAL) = :bulan 
        AND YEAR(A.TANGGAL) = :tahun
        and A.IsOtorisasi1 = 1
        AND ISNULL(AA.pKonsi, 0) = 1
    GROUP BY 
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
        A.NOBUKTI, A.NOURUT, A.TANGGAL, A.note, 
        A.IDUSER, A.NoPenyerahan,
        A.KODECUSTSUPP, F.NamaCustSupp,
        A.KODESLS, G.Nama,
        AA.RefPR
    ORDER BY A.NOBUKTI", ["tahun" => $periode->tahun, "bulan" => $periode->bulan]);

    return response()->json([
        "tempOutstanding" => $tempOutstanding,
        "tempPenerimaan" => $tempPenerimaan,
        "tempPenerimaan2" => $tempPenerimaan2
    ]);
}

  public function getDetailCetak(Request $req)
{
    $noBukti = $req->input('NOBUKTI');

    $cetak = DB::connection("SML")->select(
        "EXEC dbo.sp_CetakSerahSample ?",
        [$noBukti]
    );

    $tempCetak1 = [];
    foreach ($cetak as $p) {
        array_push($tempCetak1, $p);
    }

    return $tempCetak1;
}

  public function getDetail (Request $req ) {
    DB::connection('SML')->statement('exec sp_RefreshTempSerahSample ?,?,?', [\Auth::User()->username, $req->nobukti, date('Y-m-d')]);

    $tempOutstanding = DB::connection("SML")->select("
        SELECT 
            A.*,
            C.NAMACUSTSUPP,
            K.Nama AS NamaSls
        FROM TempSerahSample A
        LEFT JOIN DBCUSTSUPP C ON A.KodeCustSupp = C.KODECUSTSUPP
        LEFT JOIN dbKaryawan K ON A.KodeSls = K.KeyNIK
        WHERE A.IDUser = :username
    ", [
        "username" => \Auth::user()->username
    ]);

    // $tempOutstanding = DB::connection("SML")->select("declare @Tahun int, @Bulan int, @Flagmenu tinyint,@UserID Varchar(30)
    //
    // select @Tahun= '' , @Bulan= '', @UserID= :user
    //
    // select distinct A.NoBukti, A.NoUrut, A.Tanggal, A.KodeCustSupp, A.NAMACUSTSUPP, A.NamaKota,
    //   A.NoRPJ, A.NoSO, A.IDUser,
    //   A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.NeedOtorisasi,
    //     A.ISBATAL, A.USERBATAL, A.TglBatal, A.TipePPn,A.Noinv,A.KodeGdg , A.KodeBrg, A.NamaBrg ,A.QNT, A.QNT1, A.QNT2, A.SAT_1, A.SAT_2, A.NOSAT, A.ISI,A.URUT
    // from vwTransPRRJual A
    // Left Outer Join (  SELECT A.NOBUKTI
    //          FROM dbPRRJualDet A
    //          LEFT OUTER JOIN (select noprrjual,urutprrjual,sum(QNT1)Qnt1,SUM(QNT2) Qnt2
    //                                                 from dbSPBRJualDet  group by noprrjual,urutprrjual
    //             ) B on A.NoBukti=B.NoprRJual AND A.Urut=B.URUTPRRJUAL
    //          WHERE ISNULL(A.QNT1,0)-ISNULL(B.Qnt1,0) >0
    //          GROUP BY A.NoBukti
    //         )B ON A.NoBukti=B.NoBukti
    // where A.Kodegdg In(select KodeGdg from DBPemakaiGdg where UserId=@UserId)
    // and NeedOtorisasi=0
    // AND B.NoBukti = :nobukti
    // order by A.NoBukti",[ "user" => \Auth::User()->username , "nobukti" => $req->nobukti]);

    return $tempOutstanding;
  }

  public function getDetailPenerimaan(Request $req) {
    $tempPenerimaan = DB::connection("SML")->select("
        SELECT 
            A.NOBUKTI,
            A.NOURUT,
            CONVERT(VARCHAR(10), A.TANGGAL, 120) AS TANGGAL,
            A.NOTE AS KETERANGAN,
            B.URUT,
            B.KODEBRG,
            C.NamaBrg AS NAMABRG,
            B.QNT AS QNT,
            B.QNT2 AS QNT2,
            B.SAT_1,
            B.SAT_2,
            B.NoSat,
            B.ISI, 
            B.gdgAsal AS GDGASAL,
            D.NAMA + ' (' + B.gdgAsal + ')' AS NAMA_GDGASAL,
            B.gdgTujuan AS GDGTUJUAN,
            E.NAMA + ' (' + B.gdgTujuan + ')' AS NAMA_GDGTUJUAN,
            A.KODECUSTSUPP,
            F.NamaCustSupp AS NAMACUSTSUPP,
            A.KODESLS,
            G.Nama AS NAMASLS,
            B.pbonus,
            B.NOPRSAMPLE,
            B.URUTPRSAMPLE,
            CASE 
                WHEN B.NOSAT = 1 THEN B.Qnt 
                ELSE B.QNT2 
            END AS QNTSAMPLE,
            BB.QNTOUTSTANDING,
            A.IsOtorisasi1
        FROM DBserahSAMPLE A
        LEFT OUTER JOIN DBserahSAMPLEdet B ON B.NoBukti = A.NoBukti
        LEFT OUTER JOIN dbBarang C ON C.KodeBrg = B.KodeBrg
        LEFT OUTER JOIN DBGUDANG D ON D.KODEGDG = B.GdgAsal
        LEFT OUTER JOIN DBGUDANG E ON E.KODEGDG = B.GdgTujuan
        LEFT OUTER JOIN DbCustSupp F ON F.KodeCustSupp = A.KODECUSTSUPP
        LEFT OUTER JOIN dbKaryawan G ON A.KODESLS = G.KeyNIK
        LEFT OUTER JOIN (
            SELECT 
                A.NOBUKTI, A.URUT,
                CASE 
                    WHEN A.NOSAT = 1 THEN ISNULL(A.QNT, 0) - ISNULL(B.Qnt1, 0)
                    ELSE ISNULL(A.QNT2, 0) - ISNULL(B.Qnt2, 0)
                END AS QNTOUTSTANDING
            FROM DBPRSAMPLEDET A 
            LEFT OUTER JOIN (
                SELECT 
                    NOPRSAMPLE,
                    URUTPRSAMPLE, 
                    SUM(QNT) AS Qnt1, 
                    SUM(QNT2) AS Qnt2 
                FROM DBSERAHSAMPLEDET  
                GROUP BY NOPRSAMPLE, URUTPRSAMPLE
            ) B ON A.NoBukti = B.NOPRSAMPLE AND A.Urut = B.URUTPRSAMPLE
            WHERE ISNULL(A.QNT, 0) - ISNULL(B.Qnt1, 0) > 0
        ) BB ON BB.NoBukti = B.NOPRSAMPLE AND BB.URUT = B.URUTPRSAMPLE
        WHERE A.NoBukti = :nobukti
        ORDER BY B.Urut
    ", [
        "nobukti" => $req->nobukti
    ]);

    return $tempPenerimaan;
}

// public function GudangAsal(Request $req)
//   {
//     $data = DB::connection("SML")->select("
//         SELECT 
//         a.KodeGdg, 
//         a.Nama AS NamaGdg, 
//         a.IsRusak 
//         FROM dbGudang a 
//         LEFT OUTER JOIN dbPemakaiGdg b ON b.kodegdg = a.kodegdg 
//         GROUP BY a.KodeGdg, a.Nama, a.IsRusak 
//         ORDER BY a.KodeGdg");

//     return response()->json($data);
//   }

//   public function GudangTujuan(Request $req)
//   {
//     $data = DB::connection("SML")->select("
//         SELECT 
//         a.KodeGdg, 
//         a.Nama AS NamaGdg, 
//         a.IsRusak 
//         FROM dbGudang a 
//         LEFT OUTER JOIN dbPemakaiGdg b ON b.kodegdg = a.kodegdg 
//         GROUP BY a.KodeGdg, a.Nama, a.IsRusak 
//         ORDER BY a.KodeGdg");

//     return response()->json($data);
//   }


  public function getNoBukti (Request $req) {

    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $inisial = DB::connection("SML")->select('select SSK from DBNOMOR');


    $values = [
        $inisial[0]->SSK,
        $periode->bulan,
        $periode->tahun,
        $username,
    ];

    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);

    return $noBukti;
  }

  public function spOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update DBSERAHSAMPLE set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal where nobukti = :nobukti", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
     $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','SSK',$req->nobukti,'',0,'DBSERAHSAMPLE');
    return $res;
  }
  public function spBatalOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update DBSERAHSAMPLE set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL where nobukti = :nobukti", [ "nobukti" => $req->nobukti]);
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','SSK',$req->nobukti,$req->pket,0,'DBSERAHSAMPLE');
    return $res;
  }

  public function spAdd(Request $req)
{
    $tempData = $req->input('tempData');
    $username = \Auth::user()->username;
    $checkSaldo = 0;


    $xurut=0;






    foreach ($tempData as $d) {
        $check2 = DB::connection("SML")->select(
            'exec Sp_CekStockAkhir ?,?,?,?',
            [$d['NOSAT'], $req->tanggal, $d['GDGASAL'], $d['KODEBRG']]
        );

        if ($check2) {
            if ((float)$d['inputQnt'] > (float)$check2[0]->SALDOQNT) {
                $checkSaldo = 1;
                break; 
            }
        } else {
            $checkSaldo = 1;
            break;
        }
    }

    if ($checkSaldo) {
        return 3;
    }

    foreach ($tempData as $d) {
        DB::connection('SML')->statement('exec SP_SerahSample ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
            'I',
            $req->nobukti,
            $req->nourut,
            date('Y-m-d H:i:s', strtotime($req->tanggal)),
            $d['Note'] ?? '',
            (int)$req->urut,
            $d['KODEBRG'],
            $d['GDGASAL'],
            $d['GDGTUJUAN'],
            $d['SAT_1'],
            $d['SAT_2'],
            $d['inputQnt'],
            $d['inputQnt'],
            $d['NOSAT'],
            $d['ISI'],
            $d['IDUser'],
            $d['KodeCustSupp'],
            $d['KodeSls'],
            $d['PBONUS'],
            0,
            $d['NOBUKTI'],
            $d['URUT'],
            1
        ]);
    }

    return 1;
}


  public function spKoreksi(Request $req)
{
    $choice = $req->choice; 
    $nobukti = $req->nobukti;

    if ($choice =='D'){
      $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $req->choice,'SSK',$nobukti,'',$req->urut,'DBSERAHSAMPLEDET');
      }

    $values = [
        $choice,                         
        $nobukti,                         
        '',                              
        '',                               
        $req->note ?? '',                 
        (int)$req->urut,                 
        $req->kodebrg,                   
        $req->kodegdgasal,            
        $req->kodegdgtujuan,          
        $req->sat1,                     
        $req->sat2,                     
        $req->qnt,                     
        $req->qnt2,                    
        $req->nosat,                 
        $req->isi,                    
        \Auth::user()->username,        
        '',                           
        0,                            
        0,                               
        0,                              
        $req->nopr,                
        $req->urutpr,            
        1                             
    ];

    DB::connection('SML')->statement('exec SP_SerahSample ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);


     if ($choice !='D'){
      $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $req->choice,'SSK',$nobukti,'',$req->urut,'DBSERAHSAMPLEDET');
      }
    return 1;
}


  public function onChangeHeader (Request $req) {
    $query = 'update dbserahsample set ' . $req->field . ' = :value where nobukti = :nobukti';
    
    $res = DB::connection('SML')->update($query, ["value" => $req->value , "nobukti" => $req->nobukti]);
    return $res;

  }

}
