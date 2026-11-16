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

class GudangPermintaanKonsinyasiController extends Controller
{

  public function index(Request $req) {
    $kodemenu = '06060';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses1($kodemenu , $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    // $listData = DB::connection('SML')->select('SELECT * FROM DBMERK');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(6);

    $tempOutstanding = DB::connection("SML")->select("
    select 
          A.IsOtorisasi1, A.OtoUser1, A.TglOto1, 
          A.NOBUKTI, A.NOURUT, A.TANGGAL, A.note Keterangan,
          B.URUT, B.KODEBRG, C.NamaBrg, '' Jns_Kertas, '' Ukr_Kertas,
          B.QNT, B.QNT2, B.SAT_1, B.SAT_2, B.NoSat, B.ISI, 
          B.gdgAsal, D.NAMA + ' (' + B.gdgAsal + ')' NamaGgdAsal, 0.00 GSM,
          B.gdgTujuan, E.NAMA + ' (' + B.gdgTujuan + ')' NamaGgdTujuan,
          A.KODECUSTSUPP, F.NamaCustSupp, 
          A.KODESLS, G.Nama NAMASLS, 
          B.pbonus, A.TglKirim, A.RefPR, A.Lokasi, 
          H.Nama NamaLokasi
        from DBPRSAMPLE A
        left outer join DBPRSAMPLEDET B on B.NoBukti = A.NoBukti
        left outer join dbBarang C on C.KodeBrg = B.KodeBrg
        left outer join DBGUDANG D on D.KODEGDG = B.GdgAsal
        left outer join DBGUDANG E on E.KODEGDG = B.GdgTujuan
        left outer join DbCustSupp F on F.KodeCustSupp = A.KODECUSTSUPP
        left outer join dbKaryawan G on A.KODESLS = G.KeyNIK
        left outer join DbKebunCustSupp H on A.Lokasi = H.KodeKebun and A.KODECUSTSUPP = H.KodeCustSupp
        where 
          year(A.TANGGAL) = :tahun and 
          month(A.TANGGAL) = :bulan and 
          A.NOBUKTI <> '' 
          and A.IsOtorisasi1 = 0
          and ISNULL(A.pKonsi, 0) = 1
        order by B.URUT
    ", [
    "tahun" => $periode->tahun, 
    "bulan" => $periode->bulan
    ]);

    // Data sudah otorisasi
    $tempOutstanding3 = DB::connection("SML")->select("
        select 
          A.IsOtorisasi1, A.OtoUser1, A.TglOto1, 
          A.NOBUKTI, A.NOURUT, A.TANGGAL, A.note Keterangan,
          B.URUT, B.KODEBRG, C.NamaBrg, '' Jns_Kertas, '' Ukr_Kertas,
          B.QNT, B.QNT2, B.SAT_1, B.SAT_2, B.NoSat, B.ISI, 
          B.gdgAsal, D.NAMA + ' (' + B.gdgAsal + ')' NamaGgdAsal, 0.00 GSM,
          B.gdgTujuan, E.NAMA + ' (' + B.gdgTujuan + ')' NamaGgdTujuan,
          A.KODECUSTSUPP, F.NamaCustSupp, 
          A.KODESLS, G.Nama NAMASLS, 
          B.pbonus, A.TglKirim, A.RefPR, A.Lokasi, 
          H.Nama NamaLokasi
        from DBPRSAMPLE A
        left outer join DBPRSAMPLEDET B on B.NoBukti = A.NoBukti
        left outer join dbBarang C on C.KodeBrg = B.KodeBrg
        left outer join DBGUDANG D on D.KODEGDG = B.GdgAsal
        left outer join DBGUDANG E on E.KODEGDG = B.GdgTujuan
        left outer join DbCustSupp F on F.KodeCustSupp = A.KODECUSTSUPP
        left outer join dbKaryawan G on A.KODESLS = G.KeyNIK
        left outer join DbKebunCustSupp H on A.Lokasi = H.KodeKebun and A.KODECUSTSUPP = H.KodeCustSupp
        where 
          year(A.TANGGAL) = :tahun and 
          month(A.TANGGAL) = :bulan and 
          A.NOBUKTI <> '' 
          and A.IsOtorisasi1 = 1
          and ISNULL(A.pKonsi, 0) = 1
        order by B.URUT
    ", [
        "tahun" => $periode->tahun, 
        "bulan" => $periode->bulan
    ]);


    $tempOutstanding2 = DB::connection("SML")->select("

    declare @Tahun int, @Bulan int
        select @Tahun= :tahun, @Bulan= :bulan

        select distinct A.NoBukti, A.NoUrut, A.Tanggal, A.KodeCustSupp, C.NAMACUSTSUPP, D.NamaKota,
        A.IDUser,A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2, 
        A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
        A.IsOtorisasi5, A.OtoUser5, A.TglOto5,  
        Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
          Case when A.IsOtorisasi2=1 then 1 else 0 end+
          Case when A.IsOtorisasi3=1 then 1 else 0 end+
          Case when A.IsOtorisasi4=1 then 1 else 0 end+
          Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
          else 1
          end As Bit),E.nama namasls,
          A.TglKirim, 
          case when aa.nosat=1 THEN AA.QNT ELSE AA.QNT2 END QNT,
          CASE WHEN AA.NOSAT=1 THEN B.QNT1 ELSE B.QNT2 END QNTSSKONSI, 
          case when aa.nosat=1 THEN AA.QNT - ISNULL(B.QNT1,0) ELSE AA.QNT2 - ISNULL(B.QNT2,0) END QNTSISA, 
          case when aa.nosat=1 THEN AA.SAT_1 ELSE AA.SAT_2 END SAT,
          F.namabrg, aa.kodebrg
        from DBPRSAMPLE A
        LEFT OUTER JOIN DBPRSAMPLEDET AA ON A.NOBUKTI=AA.NOBUKTI
        Left OUter join DBCUSTSUPP C on A.KODECUSTSUPP=C.KODECUSTSUPP
        left Outer join DBKOTA D on C.Kota=D.KodeKota
        Left Outer join DBKaryawan E on A.kodesls=E.Keynik
        LEFT OUTER JOIN (
            select RefPR,KODEBRG,sum(QNT - isnull(Qntbatal,0) )Qnt1,
                   SUM(QNT2 - isnull(qntbatal,0)) Qnt2
		    from DBSODET
            group by RefPR,KODEBRG
        ) B on A.RefPR=B.RefPR AND AA.KODEBRG=B.KODEBRG
        left outer join dbbarang F on aa.kodebrg=f.kodebrg
        where
        ISNULL(AA.QNT,0)-ISNULL(B.Qnt1,0) >0
        and Isnull(A.pKonsi,0) = 1
        order by A.NoBukti
    " , ["tahun" =>$periode->tahun , "bulan" => $periode->bulan ]);

      // Grouping pakai Collection
    $listData = collect($tempOutstanding)->groupBy('NOBUKTI');
    $listData3 = collect($tempOutstanding3)->groupBy('NOBUKTI');

    // $outstanding = VwPPL::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->where('IsJasa', 0)->where('pAgen', 0)->groupBy('NoBukti');
    // $tempOutstanding = [];
    // foreach ($outstanding as $p) {
    //   // code...
    //   array_push($tempOutstanding, $p);
    // }

    return view('gudang.gudangpermintaankonsinyasi' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "listData2" => $tempOutstanding2,
      "listData" => $listData,
      "listData3" => $listData3,
      "akses" => $akses
    ]);

  }

  public function loadAll()
{
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    // Ambil data transaksi sample
    $tempOutstanding = DB::connection("SML")->select("
   select 
          A.IsOtorisasi1, A.OtoUser1, A.TglOto1, 
          A.NOBUKTI, A.NOURUT, A.TANGGAL, A.note Keterangan,
          B.URUT, B.KODEBRG, C.NamaBrg, '' Jns_Kertas, '' Ukr_Kertas,
          B.QNT, B.QNT2, B.SAT_1, B.SAT_2, B.NoSat, B.ISI, 
          B.gdgAsal, D.NAMA + ' (' + B.gdgAsal + ')' NamaGgdAsal, 0.00 GSM,
          B.gdgTujuan, E.NAMA + ' (' + B.gdgTujuan + ')' NamaGgdTujuan,
          A.KODECUSTSUPP, F.NamaCustSupp, 
          A.KODESLS, G.Nama NAMASLS, 
          B.pbonus, A.TglKirim, A.RefPR, A.Lokasi, 
          H.Nama NamaLokasi
        from DBPRSAMPLE A
        left outer join DBPRSAMPLEDET B on B.NoBukti = A.NoBukti
        left outer join dbBarang C on C.KodeBrg = B.KodeBrg
        left outer join DBGUDANG D on D.KODEGDG = B.GdgAsal
        left outer join DBGUDANG E on E.KODEGDG = B.GdgTujuan
        left outer join DbCustSupp F on F.KodeCustSupp = A.KODECUSTSUPP
        left outer join dbKaryawan G on A.KODESLS = G.KeyNIK
        left outer join DbKebunCustSupp H on A.Lokasi = H.KodeKebun and A.KODECUSTSUPP = H.KodeCustSupp
        where 
          year(A.TANGGAL) = :tahun and 
          month(A.TANGGAL) = :bulan and 
          A.NOBUKTI <> '' 
          and ISNULL(A.IsOtorisasi1, 0) = 0
          and ISNULL(A.pKonsi, 0) = 1
        order by B.URUT
    ", [
        "tahun" => $periode->tahun,
        "bulan" => $periode->bulan
    ]);

    // Data sudah otorisasi
    $tempOutstanding3 = DB::connection("SML")->select("
        select 
          A.IsOtorisasi1, A.OtoUser1, A.TglOto1, 
          A.NOBUKTI, A.NOURUT, A.TANGGAL, A.note Keterangan,
          B.URUT, B.KODEBRG, C.NamaBrg, '' Jns_Kertas, '' Ukr_Kertas,
          B.QNT, B.QNT2, B.SAT_1, B.SAT_2, B.NoSat, B.ISI, 
          B.gdgAsal, D.NAMA + ' (' + B.gdgAsal + ')' NamaGgdAsal, 0.00 GSM,
          B.gdgTujuan, E.NAMA + ' (' + B.gdgTujuan + ')' NamaGgdTujuan,
          A.KODECUSTSUPP, F.NamaCustSupp, 
          A.KODESLS, G.Nama NAMASLS, 
          B.pbonus, A.TglKirim, A.RefPR, A.Lokasi, 
          H.Nama NamaLokasi
        from DBPRSAMPLE A
        left outer join DBPRSAMPLEDET B on B.NoBukti = A.NoBukti
        left outer join dbBarang C on C.KodeBrg = B.KodeBrg
        left outer join DBGUDANG D on D.KODEGDG = B.GdgAsal
        left outer join DBGUDANG E on E.KODEGDG = B.GdgTujuan
        left outer join DbCustSupp F on F.KodeCustSupp = A.KODECUSTSUPP
        left outer join dbKaryawan G on A.KODESLS = G.KeyNIK
        left outer join DbKebunCustSupp H on A.Lokasi = H.KodeKebun and A.KODECUSTSUPP = H.KodeCustSupp
        where 
          year(A.TANGGAL) = :tahun and 
          month(A.TANGGAL) = :bulan and 
          A.NOBUKTI <> '' 
          and ISNULL(A.IsOtorisasi1, 0) = 1
          and ISNULL(A.pKonsi, 0) = 1
        order by B.URUT
    ", [
        "tahun" => $periode->tahun,
        "bulan" => $periode->bulan
    ]);

    // $collection1 = collect($tempOutstanding)->sortBy(function($item) {
    //   return $item->NOURUT;
    // })->groupBy('NOBUKTI');

    // $tempOutstanding1 = [];
    // foreach ($collection1 as $p) {
    //     array_push($tempOutstanding1, $p);
    // }

    // Ambil data outstanding
    $tempOutstanding2 = DB::connection("SML")->select("
        declare @Tahun int, @Bulan int
        select @Tahun = :tahun, @Bulan = :bulan

        select distinct A.NoBukti, A.NoUrut, A.Tanggal, A.KodeCustSupp, C.NAMACUSTSUPP, D.NamaKota,
        A.IDUser,A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2, 
        A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
        A.IsOtorisasi5, A.OtoUser5, A.TglOto5,  
        Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
          Case when A.IsOtorisasi2=1 then 1 else 0 end+
          Case when A.IsOtorisasi3=1 then 1 else 0 end+
          Case when A.IsOtorisasi4=1 then 1 else 0 end+
          Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
          else 1
          end As Bit) status,
        E.nama namasls,
        A.TglKirim, 
        case when aa.nosat=1 THEN AA.QNT ELSE AA.QNT2 END QNT,
        CASE WHEN AA.NOSAT=1 THEN B.QNT1 ELSE B.QNT2 END QNTSSKONSI, 
        case when aa.nosat=1 THEN AA.QNT - ISNULL(B.QNT1,0) ELSE AA.QNT2 - ISNULL(B.QNT2,0) END QNTSISA, 
        case when aa.nosat=1 THEN AA.SAT_1 ELSE AA.SAT_2 END SAT,
        F.namabrg, aa.kodebrg
        from DBPRSAMPLE A
        LEFT OUTER JOIN DBPRSAMPLEDET AA ON A.NOBUKTI=AA.NOBUKTI
        Left OUter join DBCUSTSUPP C on A.KODECUSTSUPP=C.KODECUSTSUPP
        left Outer join DBKOTA D on C.Kota=D.KodeKota
        Left Outer join DBKaryawan E on A.kodesls=E.Keynik
        LEFT OUTER JOIN (
            select RefPR,KODEBRG,sum(QNT - isnull(Qntbatal,0) )Qnt1,
                   SUM(QNT2 - isnull(qntbatal,0)) Qnt2
		    from DBSODET
            group by RefPR,KODEBRG
        ) B on A.RefPR=B.RefPR AND AA.KODEBRG=B.KODEBRG
        left outer join dbbarang F on aa.kodebrg=f.kodebrg
        where
        ISNULL(AA.QNT,0)-ISNULL(B.Qnt1,0) >0
        and Isnull(A.pKonsi,0) = 1
        order by A.NoBukti
    ", [
        "tahun" => $periode->tahun,
        "bulan" => $periode->bulan
    ]);

    $listData = collect($tempOutstanding)->groupBy('NOBUKTI');
    $listData3 = collect($tempOutstanding3)->groupBy('NOBUKTI');
    // $collection1 = collect($tempOutstanding)->groupBy('NOBUKTI');
    $belumotorisasi = [];
    foreach ($listData as $p) {
      // code...
      array_push($belumotorisasi, $p);
    }
    $sudahotorisasi = [];
    foreach ($listData3 as $p) {
      // code...
      array_push($sudahotorisasi, $p);
    }

    return [
        "belum_otorisasi" => $belumotorisasi,
        "sudah_otorisasi" => $sudahotorisasi,
        "outstanding" => $tempOutstanding2
    ];
}

  public function updateOtorisasi(Request $req) {
    $tanggal = now();
    $res = DB::connection('SML')->update(
        "UPDATE DBPRSAMPLE SET isOtorisasi1 = 1, maxol = 1, OtoUser1 = :username, TglOto1 = :tanggal WHERE NoBukti = :nobukti",
        [
            "username" => \Auth::user()->username,
            "tanggal" => $tanggal,
            "nobukti" => $req->nobukti
        ]
    );
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','PRK',$req->nobukti,'',0,'DBPRSAMPLE');
    return $res;
}
  public function updateBatalOtorisasi(Request $req) {
    $res = DB::connection('SML')->update(
        "UPDATE DBPRSAMPLE SET isOtorisasi1 = 0, maxol = -1, OtoUser1 = '', TglOto1 = NULL WHERE NoBukti = :nobukti",
        [
            "nobukti" => $req->nobukti
        ]
    );
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','PRK',$req->nobukti,$req->pket,0,'DBPRSAMPLE');
    return $res;
}
  public function cekOtorisasi (Request $req) {
    $res = DB::connection('SML')->select("select isOtorisasi1 from DBPRSAMPLE where nobukti = :nobukti", ["nobukti" => $req->nobukti ]);
    return $res;
  }

  public function spDetail (Request $req) {
    $res = DB::connection('SML')->select("declare @NoBukti varchar(30)

    select  @NoBukti= :nobukti

    select 
      A.NOBUKTI, A.NOURUT, A.TANGGAL, A.note Keterangan,
      A.IsOtorisasi1,
      B.URUT, B.KODEBRG, C.NamaBrg, '' Jns_Kertas, '' Ukr_Kertas,
      B.QNT, B.QNT2, B.SAT_1, B.SAT_2, B.NoSat, B.ISI, 
      B.gdgAsal, D.NAMA+' ('+B.gdgAsal+')' NamaGgdAsal, 0.00 GSM,
      B.gdgTujuan, E.NAMA+' ('+B.gdgTujuan+')' NamaGgdTujuan,
      A.KODECUSTSUPP, F.NamaCustSupp, A.KODESLS, G.Nama NAMASLS,
      B.pbonus, A.TglKirim, A.RefPR, A.Lokasi, H.Nama NamaLokasi
    From DBPRSAMPLE A
    Left Outer Join DBPRSAMPLEDET B on B.NoBukti=A.NoBukti
    Left Outer Join dbBarang C On C.KodeBrg=B.KodeBrg
    Left Outer Join DBGUDANG D on D.KODEGDG=B.GdgAsal
    Left Outer Join DBGUDANG E on E.KODEGDG=B.GdgTujuan
    Left Outer Join DbCustSupp F on F.KOdeCustSupp=A.KODECUSTSUPP
    Left Outer Join dbKaryawan G ON A.KODESLS=G.KeyNIK
    Left Outer Join DbKebunCustSupp H on A.Lokasi=H.KodeKebun and A.kodecustsupp=H.kodecustsupp
    where A.NoBukti=@NoBukti and A.nobukti<>''
    order By B.Urut", ["nobukti" => $req->nobukti]);

    return $res;
}

  // public function spNobukti (Request $req) {
  //   $inisial = DB::connection('SML')->select("SELECT PPL FROM DBNOMOR");
  // }

  public function Customer(Request $req)
{
    $search = trim($req->search ?? "");

    $sql = "
        SELECT 
          Y.KodeCustSupp, 
          Y.NamaCustSupp, 
          Y.Alamat1 AS Alamat,  
          Z.NamaKota, 
          Y.PPN, 
          Y.HARI, 
          Y.Kota      
        FROM DBCUSTSUPP Y         
        LEFT OUTER JOIN DBKOTA Z ON Y.Kota = Z.KodeKota   
        WHERE ISNULL(Y.JENIS, 0) = 1  
          AND Y.IsAktif = 1
    ";

    if ($search !== "") {
        $sql .= " AND (Y.KodeCustSupp LIKE ? OR Y.NamaCustSupp LIKE ?)";
        $Customer = DB::connection("SML")->select($sql, ["%$search%", "%$search%"]);
    } else {
        $sql .= " ORDER BY Y.KodeCustSupp";
        $Customer = DB::connection("SML")->select($sql);
    }

    return response()->json($Customer);
}

public function getDetailCetak(Request $req)
  {
      $noBukti = $req->input('NOBUKTI');

      $cetak = DB::connection("SML")->select(
          "EXEC dbo.SP_CetakPRSAMPLE ?",
          [$noBukti]
      );

      $tempCetak1 = [];
      foreach ($cetak as $p) {
          array_push($tempCetak1, $p);
      }

      return $tempCetak1;
  }

public function Sales(Request $req)
{
    $search = trim($req->search ?? "");

    $sql = "
      SELECT 
        KeyNIK AS KodeSls, 
        NIK, 
        Nama AS namaSls 
      FROM dbKaryawan 
      WHERE Aktif = 1
    ";

    if ($search !== "") {
        $sql .= " AND (KeyNIK LIKE ? OR Nama LIKE ?)";
        $Sales = DB::connection("SML")->select($sql, ["%$search%", "%$search%"]);
    } else {
        $sql .= " ORDER BY Nama";
        $Sales = DB::connection("SML")->select($sql);
    }

    return response()->json($Sales);
}

public function Gudang (Request $req)
{
    $search = trim($req->search ?? "");

    $sql = "
        SELECT 
          a.KodeGdg, 
          a.Nama AS NamaGdg, 
          a.IsRusak 
        FROM dbGudang a 
        LEFT OUTER JOIN dbPemakaiGdg b ON b.kodegdg = a.kodegdg 
        GROUP BY a.KodeGdg, a.Nama, a.IsRusak
        HAVING 1=1
    ";

    if ($search !== "") {
        $sql .= " AND (a.KodeGdg LIKE ? OR a.Nama LIKE ?)";
        $data = DB::connection("SML")->select($sql, ["%$search%", "%$search%"]);
    } else {
        $sql .= " ORDER BY a.KodeGdg";
        $data = DB::connection("SML")->select($sql);
    }

    return response()->json($data);
}

  // public function listLokasi (Request $req) {
  //   $listData = DB::connection('SML')->
  //   select("SELECT KodeKebun, Nama AS namaKebun FROM DbKebunCustSuppWHERE kodecustsupp = :kodeCustSupp ", ['kodecustSupp'=>$req->kodeCustSupp]);
  //   return $listData;
  // }

public function listLokasi(Request $req)
{
    $search = trim($req->search ?? "");
    $kodeCustSupp = $req->kodeCustSupp;

    $sql = "
        SELECT KodeKebun, Nama AS namaKebun
        FROM DbKebunCustSupp
        WHERE kodecustsupp = ?
    ";

    $bindings = [$kodeCustSupp];

    if ($search !== "") {
        $sql .= " AND (KodeKebun LIKE ? OR Nama LIKE ?)";
        $bindings[] = "%$search%";
        $bindings[] = "%$search%";
    }

    $sql .= " ORDER BY KodeKebun";

    $data = DB::connection("SML")->select($sql, $bindings);

    return response()->json($data);
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
    $inisial = DB::connection("SML")->select('select PRK from DBNOMOR');
    // $inisial = DB::connection("SML")->select('select SPR from DBNOMOR');
    // return [$periode->bulan,$inisial[0]->PBL,$username];
    $values = [
      $inisial[0]->PRK,
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
        a.Kodebrg, 
        a.NamaBrg,
        a.partNumber,
        B.NamaMerk,
        a.SAT1,
        a.SAT2,
        a.SAT3,
        a.ISI1,
        a.ISI2,
        a.ISI3
    FROM Dbbarang a
    LEFT OUTER JOIN dbmerk B ON A.kodemerk = B.KodeMerk 
    WHERE a.isaktif = 1 AND a.KODEGRP = 'BJ'
    AND (a.Kodebrg LIKE ? OR a.NamaBrg LIKE ?)
    ORDER BY a.Kodebrg", [
    $search,
    $search
  ]);

    return response()->json($listData);
  }

  public function cekRefPR(Request $req)
{
    try {
        $refpr = $req->input('refpr');
        $nobukti = $req->input('nobukti');

        $exists = DB::connection('SML')->selectOne("
            SELECT TOP 1 NOBUKTI 
            FROM DBPRSAMPLE 
            WHERE RefPR = ? AND NOBUKTI != ?
        ", [$refpr, $nobukti]);

        return response()->json(['exists' => $exists ? true : false]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

  public function spAdd(Request $req)
{
    $choice = $req->choice;
    $nobukti = $req->nobukti;
    $jmlrecord = $req->jmlrecord;
    $xurut=0;
//  return ["asd" => $nobukti] ;
     $purut = DB::connection('SML')->select('select * from DBPRSAMPLEDET where Nobukti = :nobukti', ['nobukti' => $nobukti]);
    if ($purut){

        if ($choice=='I' ){

        $purut = DB::connection('SML')->select('select max(urut)+1 xurut from DBPRSAMPLEDET where Nobukti = :nobukti', ['nobukti' => $nobukti]);
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
      $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $req->choice,'PRK',$nobukti,'',$xurut,'DBPRSAMPLEDET');
      }


    if ($choice == "I" && $jmlrecord == 0) {
        $check = DB::connection('SML')->select('SELECT * FROM DBPRSAMPLE WHERE Nobukti = :nobukti', ['nobukti' => $nobukti]);
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
        $req->note ?? '',                       
        (int)$req->urut,          
        $req->kodebarang,                  
        $req->gdgasal,                    
        $req->gdgtujuan ?? '',             
        $req->sat_1,                  
        $req->sat_2,                      
        (float)$req->qnt,                 
        (float)$req->qnt2,               
        (int)$req->nosat,               
        (float)$req->isi,                 
        $username,                      
        $req->kodecustsupp,               
        $req->kodesls,                   
        (int)$req->pbonus,                
        (int)$req->maxol,                 
        $req->tglkirim,                   
        $req->refpr,                      
        1,       
        $req->lokasi ?? ''               
    ];

    DB::connection('SML')->statement('exec SP_PRSAMPLE ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);

    if ($choice == "U" && $jmlrecord == 1) {
        DB::connection('SML')->table('DBPRSAMPLE')
            ->where('NOBUKTI', $nobukti)
            ->where('NOURUT', strval($req->nourut))
            ->update([
                'NOTE' => $req->note,
                'KODESLS' => $req->kodesls
            ]);
    }

    if ($choice !='D'){
      $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $req->choice,'PRK',$nobukti,'',$xurut,'DBPRSAMPLEDET');
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
        '',
        $req->sat_1 ?? $req->satuan,
        $req->sat_2 ?? $req->satuan,
        (float)$req->qnt,
        (float)($req->qnt2 ?? $req->qnt),
        (int)$req->nosat,
        (float)$req->isi,
        $username,
        $req->kodecustsupp ?? '',
        $req->kodesls ?? '',
        (int)($req->pbonus ?? 0),
        (int)($req->maxol ?? 0),
        $req->tglkirim ?? $req->tanggal,
        $req->refpr ?? '',
        1,
        $req->lokasi ?? ''
    ];

    DB::connection('SML')->statement('exec SP_PRSAMPLE ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $req->choice,'PRK',$nobukti,'',$xurut,'DBPRSAMPLEDET');
    return 1;
}

  public function onChangeHeader(Request $req) {
    $allowedFields = ['NOTE', 'TglKirim', 'KODESLS'];

    if (!in_array($req->field, $allowedFields)) {
        return response("Field tidak valid", 400);
    }

    $query = "UPDATE DBPRSAMPLE SET {$req->field} = :value WHERE NoBukti = :nobukti";

    $res = DB::connection('SML')->update($query, [
        "value" => $req->value,
        "nobukti" => $req->nobukti
    ]);

    return $res;
    }
    
}
