<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\DBFLMENU;
use App\Model\NewPeriode;
use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;





class NewTesxController extends Controller


{

  public function index(Request $req) {
    $kodemenu = '-';
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu);
    // $akses = DBFLMENU::where('USERID', \Auth::user()->username)-> where('L1', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $username = \Auth::user()->username;


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0();



    $perkiraan = DB::connection("SML")->select("Select a.Perkiraan,b.Keterangan
    from dbPosthutpiut a
    left outer join dbperkiraan b on b.perkiraan=a.perkiraan
    where (a.Kode='KAS' or A.IsBeliJual=1)
    and A.perkiraan in (select perkiraan from dbaksesperkiraanBS where userID= :username )
    Order by a.Perkiraan" , ["username" => $username]);



    $tempPenerimaan = DB::connection('SML')->select("
    declare @Tahun int, @Bulan int

select @Tahun= :tahun , @Bulan= :bulan

Select 	A.NoBukti, A.NoUrut, A.Tanggal, A.NoDPP, A.KODECUSTSUPP, A.NamaCustSupp, A.NamaKota, A.Penagih,
	Round(Sum(A.DIBAYAR),0) TotDIBAYAR, Sum(A.LB) TotLB, Sum(A.KL) TotKL,
	A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
	A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi
From vwTransTerimaDPP A
where year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan  and A.pPLD=0
group by A.NoBukti, A.NoUrut, A.Tanggal, A.KODECUSTSUPP, A.NamaCustSupp, A.Penagih,
	A.NoDPP, A.NamaKota,
	A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
	A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi
order by A.NOBUKTI",[
  "tahun" =>$periode->tahun , "bulan" => $periode->bulan
]);



    $tempOutstanding = DB::connection('SML')->select("
    declare @Tahun int, @Bulan int, @Periode Varchar(30)

select @Tahun= :tahun , @Bulan= :bulan

select A.NoBukti+A.KODECUSTSUPP KeyNOBUKTI,
	A.NOBUKTI, A.NoUrut, A.TANGGAL, A.KODECUSTSUPP, A.NAMACUSTSUPP,
	A.Valas, A.Penagih,A.NOFAKTUR
from vwBrowsOutDPP A
where (YEAR(A.TANGGAL)<@Tahun or (YEAR(A.TANGGAL)=@Tahun and MONTH(A.TANGGAL)<=@Bulan))
group by A.NOBUKTI, A.NoUrut, A.TANGGAL, A.KODECUSTSUPP, A.NAMACUSTSUPP,
	A.Valas, A.Penagih,A.NOFAKTUR
order by A.NOBUKTI, A.KODECUSTSUPP",[
  "tahun" =>$periode->tahun , "bulan" => $periode->bulan

]);

$penagih =   DB::connection("SML")->select("select a.Penagih
from dbKaryawan a
Group by Penagih
                       order by a.Penagih");



    return view('newtesx' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "tempOutstanding" => $tempOutstanding,
      "tempPenerimaan" => $tempPenerimaan,
      "akses" => $akses,
      "penagih" => $penagih
    ]);

  }

  public function detailOutstanding (Request $req) {
    $tempOutstanding = DB::connection('SML')->select("


select A.NoBukti+A.KODECUSTSUPP KeyNOBUKTI,
  A.NOBUKTI, A.NoUrut, A.TANGGAL, A.KODECUSTSUPP, A.NAMACUSTSUPP,
  A.Valas, A.Penagih,A.NOFAKTUR
from vwBrowsOutDPP A
where A.NOBUKTI = :nobukti
group by A.NOBUKTI, A.NoUrut, A.TANGGAL, A.KODECUSTSUPP, A.NAMACUSTSUPP,
  A.Valas, A.Penagih,A.NOFAKTUR
order by A.NOBUKTI, A.KODECUSTSUPP",[
  "nobukti" => $req->nodpp]);
  return $tempOutstanding;
  }


    public function detailKoreksi (Request $req) {
      $nobukti = $req->nobukti;

      $listInvoice = DB::connection("SML")->select("declare @NoBukti varchar(30)

      select 	@NoBukti= :nobukti

      Select 	A.*, P.Keterangan NamaPerkiraan
      From vwTransTerimaDPP A
      left outer join dbPerkiraan P on P.Perkiraan=A.Perkiraan
      where	A.NoBukti=@NoBukti
      order by A.KasBank, A.NoFaktur, A.Perkiraan, A.Urut" , ["nobukti" => $nobukti]);

      $listGiro = DB::connection("SML")->select("        declare @NoBukti varchar(30), @Kas varchar(20)

      select 	@NoBukti= :nobukti , @Kas=''

       Select 	A.NoGiro , A.Bank , a.Kodevls , a.Kurs , a.Keterangan , a.TglGiro  , case when A.KodeVls='IDR' then A.DebetRp else Debet end Jumlah
       From DBGIRO A
      where	A.BuktiBuka=@NoBukti
      order by A.NoGiro, A.Bank
      " , ["nobukti" => $nobukti]);

      $listRekap = DB::connection("SML")->select("declare @NoBukti varchar(30)

      select 	@NoBukti= :nobukti

      Select 	A.TipeKasBank, A.MyTipeKasBank, MIN(A.KasBank) MinKasBank, SUM(A.Dibayar) Dibayar, SUM(A.LB) LB, SUM(A.KL) KL
      From vwTransTerimaDPP A
      where	A.NoBukti=@NoBukti
      group by A.TipeKasBank, A.MyTipeKasBank
      order by MIN(A.KasBank)" , ["nobukti" => $nobukti]);




      return [
        "listInvoice" => $listInvoice ,
        "listGiro" => $listGiro,
        "listRekap" => $listRekap
      ];




    }

      public function listPerkiraanLBKL (Request $req) {

        $listData = DB::connection('SML')->select("select * from DBPERKIRAAN where Tipe = 1 and perkiraan in (select perkiraan from DBpostHUTPIUT where KODE = 'SLS')");
        return $listData;
      }

      public function listPerkiraanAdd (Request $req) {

        $listData = DB::connection('SML')->select("Select A.Perkiraan, B.Keterangan, A.Kode from dbPostHutPiut A
               left outer join dbPerkiraan B on B.Perkiraan=A.Perkiraan
               where A.Kode in ('KAS','BANK','GTR')");
        return $listData;
      }

      public function checkGiro (Request $req) {

        $listData = DB::connection('SML')->select("select nogiro, bank from dbgiro where nogiro = :nogiro and bank = :bank" ,
        ["nogiro" => $req->nogiro , "bank" => $req->bank ]);
        return $listData;


      }

      public function spAdd (Request $req) {
        if ($req->choice == "I" && $req->jmlrecord == 0) {
          $check = DB::connection('SML')->select("select * from dbterimadpp where nobukti = :nobukti" ,
          ["nobukti" => $req->nobukti ]);
          if ($check) {
            return 2;
          }

        }
        $listData = $req->tempData;

        foreach ($tempData as $d)  {
          DB::connection("SML")->update(
            "update DBTempTerimaDPP set IsTerima = 1 where urut = :urut and NODPP = :nodpp" ,
            ["urut" => $d['urut'] , "nodpp" => $d['NoDPP']]
          );
          // update DBTempTerimaDPP set IsTerima = 1 where urut = :Urut and NODPP = :nodpp
          DB::connection('SML')->update('exec sp_TransTerimaDPP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [


          ]);




        }

        // sp_TerimaDPPGiro
        // $values =
        // [
        //   , //Choice varchar(1),
        //   , //NoBukti varchar(30),
        //   , //NoUrut varchar(10),
        //   , //Tanggal datetime,
        //   , //NoDPP varchar(30),
        //   , //KodeCustSupp varchar(20),
        //   , //Valas Varchar(5),
        //   , //Kurs numeric(18,2)=1,
        //   , //Penagih Varchar(100)='',
        //   , //Tipe Varchar(3)='',
        //   , //KasBank varchar(20)='',
        //   , //UrutDPP int,
        //   , //Urut int='',
        //   , //NOFAKTUR Varchar(30),
        //   , //DiBayar numeric(18,2)=0,
        //   , //Perkiraan Varchar(30)='',
        //   , //KL Numeric(18,2)=0,
        //   , //LB Numeric(18,2)=0,
        //   , //IDUser varchar(30)='',
        //   , //pPLD Bit=0,
        //   , //KodeCustD varchar(20)=''
        //
        // ];
        $res = DB::connection('SML')->update('exec sp_TransTerimaDPP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?',$values);
        return 1;
      }

      public function spKoreksi (Request $req) {
        $username = \Auth::user()->username;
        $values =
        [
          $req->choice , //Choice varchar(1),
          $req->nobukti , //NoBukti varchar(30),
          $req->nourut , //NoUrut varchar(10),
          $req->tanggal , //Tanggal datetime,
          $req->nodpp , //NoDPP varchar(30),
          $req->kodecust , //KodeCustSupp varchar(20),
          $req->valas , //Valas Varchar(5),
          $req->kurs , //Kurs numeric(18,2)=1,
          $req->penagih , //Penagih Varchar(100)='',
          $req->tipe , //Tipe Varchar(3)='',
          $req->kasbank , //KasBank varchar(20)='',
          $req->urutdpp , //UrutDPP int,
          $req->urut , //Urut int='',
          $req->nofaktur , //NOFAKTUR Varchar(30),
          $req->dibayar , //DiBayar numeric(18,2)=0,
          $req->perkiraan , //Perkiraan Varchar(30)='',
          $req->kl , //KL Numeric(18,2)=0,
          $req->lb , //LB Numeric(18,2)=0,
          $username , //IDUser varchar(30)='',
          0 , //pPLD Bit=0,
          $req->kodecustd, //KodeCustD varchar(20)=''
        ];
        $res = DB::connection('SML')->update('exec sp_TransTerimaDPP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?',$values);
        return 1;

      }

      public function spGiro (Request $req) {

        if ($req->choice == "I") {
          $check = DB::connection('SML')->select("select nogiro, bank from dbgiro where nogiro = :nogiro and bank = :bank" ,
          ["nogiro" => $req->nogiro , "bank" => $req->bank ]);
          if ($check) {
            return 2;
          }

        }

        // sp_TerimaDPPGiro
        $values =
        [
          $req->choice , // Choice char(1),
          $req->bank , // Bank varchar(20),
          $req->nogiro , // NoGiro varchar(20),
          $req->tanggalgiro , // TglGiro datetime,
          $req->valas , // KodeVls varchar(10),
          $req->kurs , // Kurs numeric(18,2),
          $req->nilaigiro , // Jumlah Numeric(18,2),
          $req->tanggal , // TglBuka datetime,
          $req->nobukti, // BuktiBuka varchar(20),
          $req->keterangan ,  // Keteranganbuka varchar(50),
          ''  // Kas varchar(25)
        ];
        $res = DB::connection('SML')->update('exec sp_TerimaDPPGiro ?,?,?,?,?,?,?,?,?,?,?',$values);
        return 1;
      }


      public function listProses (Request $req) {
        $username = \Auth::user()->username;
        $values = [
          $req->nodpp,
          $req->kodecust,
          $req->perkiraan,
          $username
        ];
        $res = DB::connection('SML')->update('exec sp_RefreshTempTerimaDPP ?,?,?,?',$values);

        $listData = DB::connection('SML')->select("Select * from dbTempTerimaDPP where IDUser = :username" , ["username" => $username]);
        return $listData;
      }

// select * from DBPERKIRAAN where Tipe = 1 and perkiraan in (select perkiraan from DBpostHUTPIUT where KODE = 'SLS')






}
