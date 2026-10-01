<?php


namespace App\Http\Controllers\Accounting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\DBFLMENU;
use App\Model\NewPeriode;
use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;


class GiroDibukaController extends Controller



{

  // $username = \Auth::user()->username;

  public function index(Request $req) {
    $kodemenu = '02027';

    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu, $req->path());
    // $akses = DBFLMENU::where('USERID', \Auth::user()->username)-> where('L1', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }
    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(5);

    $tempListPerkiraanBGC = DB::connection("SML")->select("
    Select Perkiraan, Keterangan, Simbol, cast(IsPPN as tinyint) IsPPN from dbPerkiraan where Tipe=1
 and Perkiraan in (select Perkiraan from dbPostHutPiut where Kode='BANK')
 and  Perkiraan in (select Perkiraan from dbAksesPerkiraan where UserID= :username )
 Order by Perkiraan
    ", [ "username" => $username]);


$tempOutstanding = DB::connection("SML")->select("

 declare @Tahun int, @Bulan int

select @Tahun= :tahun , @Bulan= :bulan

select  A.NoUrut, A.NoBukti, A.Tanggal, A.Note, '' Devisi, A.PerkiraanHd Perkiraan, A.TipeTransHd,
        sum(case when B.Valas='IDR' then 0.00 else B.Debet+B.Kredit end) TotalD,
        sum((B.Debet+B.Kredit)*B.Kurs) TotalRp,
	A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
	A.IsOtorisasi5, A.OtoUser5, A.TglOto5
from dbTrans A
left outer join dbTransaksi B on B.NoBukti=A.NoBukti
where year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan
        and B.TipeTrans in ('BBG','BCG')
group by A.NoUrut, A.NoBukti, A.Tanggal, A.Note, A.TipeTransHd, A.PerkiraanHd,
	A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
	A.IsOtorisasi5, A.OtoUser5, A.TglOto5
Order by A.Nobukti




",[ "tahun" =>$periode->tahun , "bulan" => $periode->bulan ]);


    return view('accounting.girodibuka' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "tempOutstanding" => $tempOutstanding,
      "akses" => $akses,
      "tempListPerkiraanBGC" => $tempListPerkiraanBGC
    ]);

  }

  public function cekGiroExist (Request $req) {
    // return ["nogiro" => $req->nogiro , "bank" => $req->bank];
    $check = DB::connection("SML")->select("select nogiro from dbgiro where nogiro = :nogiro and bank = :bank" , ["nogiro" => $req->nogiro , "bank" => $req->bank]);
    return $check;
  }

 public function getDetailCetak(Request $req)
  {
      $noBukti = $req->input('NOBUKTI');

      $cetak = DB::connection("SML")->select(
          "EXEC dbo.CetakGiroTerima ?",
          [$noBukti]
      );

      $tempCetak1 = [];
      foreach ($cetak as $p) {
          array_push($tempCetak1, $p);
      }

      return $tempCetak1;
  }

  public function loadAll (Request $req) {

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    // Periode filter di toolbar (po-filter-wrap, sama seperti so.blade.php dan
    // giroditerima.blade.php) mengirim tglawal/tglakhir sebagai date-range bebas --
    // kalau tidak dikirim (mis. pemanggilan lama), jatuh ke default bulan/tahun
    // periode yang sedang aktif supaya perilaku awal tidak berubah.
    if ($req->tglawal && $req->tglakhir) {
      $tglawal = $req->tglawal;
      $tglakhir = $req->tglakhir;
    } else {
      $tglawal = \Carbon\Carbon::now()->year((int) $periode->tahun)->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d');
      $tglakhir = \Carbon\Carbon::now()->year((int) $periode->tahun)->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d');
    }

    $tempOutstanding = DB::connection("SML")->select("
    declare @Awal date, @Akhir date

   select @Awal= :tglawal , @Akhir= :tglakhir

   select  A.NoUrut, A.NoBukti, A.Tanggal, A.Note, '' Devisi, A.PerkiraanHd Perkiraan, A.TipeTransHd,
           sum(case when B.Valas='IDR' then 0.00 else B.Debet+B.Kredit end) TotalD,
           sum((B.Debet+B.Kredit)*B.Kurs) TotalRp,
     A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
     A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
     A.IsOtorisasi5, A.OtoUser5, A.TglOto5
   from dbTrans A
   left outer join dbTransaksi B on B.NoBukti=A.NoBukti
   where A.Tanggal between @Awal and @Akhir
           and B.TipeTrans in ('BBG','BCG')
   group by A.NoUrut, A.NoBukti, A.Tanggal, A.Note, A.TipeTransHd, A.PerkiraanHd,
     A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
     A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
     A.IsOtorisasi5, A.OtoUser5, A.TglOto5
   Order by A.Nobukti

    ",[ "tglawal" => $tglawal , "tglakhir" => $tglakhir ]);

    return ["tempOutstanding" => $tempOutstanding];
  }

  public function listPencairanGiroKoreksi (Request $req) {
    $listGiro = DB::connection("SML")->select("
    select NoGiro
      ,Bank
      ,TglGiro
      ,Debet
      ,Kredit
      ,DebetRp
      ,KreditRp
      ,Keterangan
      ,TglBuka
      ,BuktiBuka
      ,UrutBuktiBuka
      ,TglCair
      ,BuktiCair
      ,KeteranganCair
      ,UrutBuktiCair
      ,Kodevls
      ,Kurs
      ,Jumlah
      ,Tipe
      ,FlagSimbol
      ,Kas from DBGIRO where bukticair = :nobukti and UrutBuktiCair = :urut
    " , ["nobukti" => $req->nobukti , "urut" => $req->urut]);

    return $listGiro;



  }

  public function listPencairanGiro (Request $req) {
    $listGiro = DB::connection("SML")->select("
    Select Bank, Nogiro, TglGiro, Case when kodevls='IDR' then KreditRp else Kredit end jumlah,
KodeVls, Kurs, Keterangan
from dbGiro
where Tipe='HT' and TglCair is null and bank = :bank
Order by Bank, Nogiro, TglGiro
    " , ["bank" => $req->bank]);

    return $listGiro;



  }


  public function getDetail (Request $req ) {



        $tempOutstanding = DB::connection("SML")->select("


         declare @NoBukti varchar(30)

        select @NoBukti= :nobukti

        select a.*,x.NAMACUSTSUPP NamaCustSuppL ,z.NAMACUSTSUPP NamaCustSuppL, b.keterangan NamaPerkiraan, d.Keterangan NamaLawan, '' MyID ,
         case when a.valas<>'IDR' then a.debet+ a.kredit else 0 end Jumlah,
         a.DebetRp+a.KreditRp JumlahRp, c.NamaDevisi,
         Case when a.TPHC='C' then '[C]Cash'
           When a.TPHC='T' then '[T]Transfer'
           when a.TPHC='H' then '[H]Hutang Giro'
           when a.TPHC='P' then '[P]Piutang Giro'
                       when a.TPHC='R' then '[R]Tukar Giro'
           else ''
         end MyTPHC, e.NMDEP NamaBag, f.NOURUT,
         Case when a.TipeTrans IN ('BBK','BKK') then a.Lawan
           when a.TipeTrans IN ('BBM','BKM') then a.Perkiraan
           else ''
         end mPerkiraan,
         Case when a.TipeTrans IN ('BBK','BKK') then d.Keterangan
           when a.TipeTrans IN ('BBM','BKM') then b.Keterangan
           else ''
         end mNamaPerkiraan,
         Case when a.TipeTrans IN ('BBK','BKK') then a.Lawan+char(13)+d.Keterangan
           when a.TipeTrans IN ('BBM','BKM') then a.Perkiraan+char(13)+b.Keterangan
           else ''
         end MyPerkiraan,
         Case when a.TipeTrans IN ('BBK','BKK') then a.Perkiraan
           when a.TipeTrans IN ('BBM','BKM') then a.Lawan
           else ''
         end MyLawan,
         Case when a.TipeTrans IN ('BBK','BKK') then b.Keterangan
           when a.TipeTrans IN ('BBM','BKM') then d.Keterangan
           else ''
         end MyNamaLawan,
         Case when a.Debet<>0 then A.Debet
           when a.DebetRp<>0 then A.DebetRp
           else 0
         end MyDebet,
         Case when a.Debet+a.Kredit<>0 then a.Debet+a.Kredit
           when a.DebetRp+a.KreditRp<>0 then a.DebetRp+a.KreditRp
           else 0
         end MyJumlah, v.NamaVls, f.Simbol, f.TipeTransHD,
         ISNULL(GB.NilaiBuka,ISNULL(GC.NilaiCair,0)) JumlahGiro,
         ISNULL(GB.NilaiBukaRp,ISNULL(GC.NilaiCairRp,0)) JumlahGiroRp,
               Cs.NamaCost, SCs.NamaSubCost,
               a.NoBukti+right('0000000000'+cast(a.Urut as varchar(10)),10)+isnull(a.FlagSimbol,'') KeyUrut,
               f.PerkiraanHd, phd.Keterangan NamaPerkiraanHd
       from dbTransaksi a
       left outer join dbperkiraan b on a.perkiraan=b.perkiraan
       left outer join dbdevisi c on c.Devisi=a.Devisi
       left outer join dbPerkiraan d on d.Perkiraan=a.Lawan
       left outer join dbDepart e on e.KDDEP=a.kodebag
       left outer join DBCUSTSUPP x on a.CustSuppL=x.KODECUSTSUPP
       left outer join DBCUSTSUPP z on a.CustSuppP=z.KODECUSTSUPP
       left outer join dbTrans f on f.nobukti=a.nobukti
       left Outer join dbValas v on v.Kodevls=a.Valas
       left outer join dbCost Cs on Cs.KodeCost=a.KodeCost
       left outer join vwSubCost SCs on SCs.KodeCost=a.KodeCost and SCs.KodeSubCost=a.KodeSubCost
       left outer join dbPerkiraan phd on phd.Perkiraan=f.PerkiraanHd
       left outer join
         (select BuktiBuka, UrutBuktiBuka, SUM(case when Tipe='PT' then Debet else Kredit end) NilaiBuka,
           SUM(case when Tipe='PT' then DebetRp else KreditRp end) NilaiBukaRp
         from	DBGIRO where BuktiBuka=@NoBukti
         group by BuktiBuka, UrutBuktiBuka
         ) GB on GB.BuktiBuka=A.NoBukti and GB.UrutBuktiBuka=A.Urut
       left outer join
         (select BuktiCair, UrutBuktiCair, SUM(case when Tipe='PT' then Kredit else Debet end) NilaiCair,
           SUM(case when Tipe='PT' then KreditRp else DebetRp end) NilaiCairRp
         from	DBGIRO where BuktiCair=@NoBukti
         group by BuktiCair, UrutBuktiCair
         ) GC on GC.Bukticair=A.NoBukti and GC.UrutBuktiCair=A.Urut
       where a.NoBukti=@NoBukti
       Order by a.Nobukti, a.Urut



" , ["nobukti" => $req->nobukti]);
    return $tempOutstanding;
  }


  public function getNoBukti (Request $req) {
    // return 1;


    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    // $periode = DB::connection("SML")->select('select TOP 1 * from DBPERIODE where user_id = :username ' , ["username" => $username]);
    // return $periode->bulan;

    if ($req->ppn == 0) {
      $kode = $req->kode . 'N';
      // return $kode;
      // return $periode[0]->bulan;
      $values = [
          $kode,
          $periode->bulan,
          $periode->tahun,
          $username,
      ];
      // return $values;

      $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);

      return $noBukti;


    } else {
      $kode = $req->kode;

      $inisial = DB::connection("SML")->select('select ' . $kode . ' from DBNOMOR');

      $values = [
          $inisial[0]->$kode,
          $periode->bulan,
          $periode->tahun,
          $username,
          // $req->simbol
      ];

      $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?,?',$values);

      return $noBukti;
    }

  }


  public function listPerkiraanHeader (Request $req) {
    $username = \Auth::user()->username;
    // $listData = DB::connection('SML')->select("select Perkiraan, Keterangan , Simbol from DBPERKIRAAN where Perkiraan like '1111%' and Tipe = 1");
    $listData = DB::connection('SML')->select("Select Perkiraan, Keterangan, Simbol, cast(IsPPN as tinyint) IsPPN from dbPerkiraan where Tipe=1
 and Perkiraan in (select Perkiraan from dbPostHutPiut where Kode='GBK')
and  Perkiraan in (select Perkiraan from dbAksesPerkiraan where UserID= :username )

", ["username" => $username ]);


    return $listData;
  }

  public function listGiro (Request $req) {
    $listData = DB::connection('SML')->select("select a.* , '' MyID from dbgiro a
");
  return $listData;

  }

  public function listLawanBGT (Request $req) {
    $username = \Auth::user()->username;
    // $listData = DB::connection('SML')->select("select Perkiraan, Keterangan , Simbol from DBPERKIRAAN where Perkiraan like '1111%' and Tipe = 1");
    $listData = DB::connection('SML')->select("select a.Perkiraan, a.Keterangan from dbPerkiraan a
     left Outer join dbAksesPerkiraan b on b.Perkiraan=a.Perkiraan
    where a.Tipe=1 and a.Perkiraan<>'113200' and b.UserID= :username
    order by a.Perkiraan
" , ["username" => $username]);


    return $listData;
  }

  public function listLawanBGC (Request $req) {
    $username = \Auth::user()->username;
    // $listData = DB::connection('SML')->select("select Perkiraan, Keterangan , Simbol from DBPERKIRAAN where Perkiraan like '1111%' and Tipe = 1");
    $listData = DB::connection('SML')->select(" Select Perkiraan, Keterangan, Simbol, cast(IsPPN as tinyint) IsPPN from dbPerkiraan where Tipe=1
and Perkiraan in (select Perkiraan from dbPostHutPiut where Kode='BANK')
and Perkiraan in (select Perkiraan from dbAksesPerkiraan where UserID= :username )
 Order by Perkiraan
" , ["username" => $username]);


    return $listData;
  }






  public function listValas (Request $req) {

    $listData = DB::connection('SML')->select("select * from DBVALAS");
    return $listData;
  }

  public function listProsesGiro (Request $req) {

    $listData = DB::connection('SML')->select("select * from DBVALAS");
    return $listData;
  }



  public function listDevisi (Request $req) {

    $listData = DB::connection('SML')->select("select Devisi, NamaDevisi from dbdevisi");
    return $listData;
  }


  public function listPTHT (Request $req) {

    $listData = DB::connection('SML')->select("select D.Nobukti,e.NOBUKTI NOUM,A.KODECUSTSUPP,C.NAMACUSTSUPP
                          ,D.Valas,Sum(A.DIBAYAR) DIBAYAR ,X.KL,SUM(LB) LB
                               from DBDPHDET A
                           Left Outer Join dbTransaksi B on A.NoBukti=B.NODPH and A.KodeCUstSupp=B.CustSUppP
                           Left OUter Join DBCUSTSUPP C on A.KODECUSTSUPP=C.KODECUSTSUPP
                           Left Outer JOin DbDPH D on A.Nobukti=D.Nobukti
                           Left Outer Join (select nobukti,KodeCustSupp,Sum(NilaiK) KL from DBKurangBayar group by  nobukti,KodeCustSupp)  X on A.nobukti=X.Nobukti and A.KodeCustSupp=X.kodeCustSupp
                         left outer join (select nobukti,noso from dbUMjual group by nobukti,noso) E on a.nofaktur=e.nobukti
                         left outer join (select nopohd from dbbeli group by NOPOHD) F on e.noso=f.nopohd
                           where B.NoBukti Is null       and f.nopohd is null
                         And D.Tipe= :tipe   and  D.Valas= :valas
                           and Cast(Case when Case when D.IsOtorisasi1=1 then 1 else 0 end+
                           Case when D.IsOtorisasi2=1 then 1 else 0 end+
                           Case when D.IsOtorisasi3=1 then 1 else 0 end+
                          Case when D.IsOtorisasi4=1 then 1 else 0 end+
                                            Case when D.IsOtorisasi5=1 then 1 else 0 end=D.MaxOL then 0
                                          else 1
                                     end As Bit)=0   and E.nobukti is null
                            group by D.Nobukti,A.KODECUSTSUPP,C.NAMACUSTSUPP,E.nobukti
                          ,D.Valas,X.kl" , ["tipe" => $req->tipe , "valas" => $req->valas ]);
    return $listData;
  }

  public function listDPH (Request $req) {

    $listData = DB::connection('SML')->select("select D.Nobukti,e.NOBUKTI NOUM,A.KODECUSTSUPP,C.NAMACUSTSUPP
                          ,D.Valas,Sum(A.DIBAYAR) DIBAYAR ,X.KL,SUM(LB) LB
                               from DBDPHDET A
                           Left Outer Join dbTransaksi B on A.NoBukti=B.NODPH and A.KodeCUstSupp=B.CustSUppP
                           Left OUter Join DBCUSTSUPP C on A.KODECUSTSUPP=C.KODECUSTSUPP
                           Left Outer JOin DbDPH D on A.Nobukti=D.Nobukti
                           Left Outer Join (select nobukti,KodeCustSupp,Sum(NilaiK) KL from DBKurangBayar group by  nobukti,KodeCustSupp)  X on A.nobukti=X.Nobukti and A.KodeCustSupp=X.kodeCustSupp
                         left outer join (select nobukti,noso from dbUMjual group by nobukti,noso) E on a.nofaktur=e.nobukti
                         left outer join (select nopohd from dbbeli group by NOPOHD) F on e.noso=f.nopohd
                           where B.NoBukti Is null       and f.nopohd is null
                         And D.Tipe= 'DPH'   and  D.Valas= :valas
                           and Cast(Case when Case when D.IsOtorisasi1=1 then 1 else 0 end+
                           Case when D.IsOtorisasi2=1 then 1 else 0 end+
                           Case when D.IsOtorisasi3=1 then 1 else 0 end+
                          Case when D.IsOtorisasi4=1 then 1 else 0 end+
                                            Case when D.IsOtorisasi5=1 then 1 else 0 end=D.MaxOL then 0
                                          else 1
                                     end As Bit)=0   and E.nobukti is null
                            group by D.Nobukti,A.KODECUSTSUPP,C.NAMACUSTSUPP,E.nobukti
                          ,D.Valas,X.kl" , ["valas" => $req->valas ]);
    return $listData;
  }

  public function listDPHUHT (Request $req) {

    $listData = DB::connection('SML')->select("select D.Nobukti,e.NOBUKTI NOUM,A.KODECUSTSUPP,C.NAMACUSTSUPP
                          ,D.Valas,Sum(A.DIBAYAR) DIBAYAR ,X.KL,SUM(LB) LB
                               from DBDPHDET A
                           Left Outer Join dbTransaksi B on A.NoBukti=B.NODPH and A.KodeCUstSupp=B.CustSUppP
                           Left OUter Join DBCUSTSUPP C on A.KODECUSTSUPP=C.KODECUSTSUPP
                           Left Outer JOin DbDPH D on A.Nobukti=D.Nobukti
                           Left Outer Join (select nobukti,KodeCustSupp,Sum(NilaiK) KL from DBKurangBayar group by  nobukti,KodeCustSupp)  X on A.nobukti=X.Nobukti and A.KodeCustSupp=X.kodeCustSupp
                         left outer join (select nobukti,noso from dbUMjual group by nobukti,noso) E on a.nofaktur=e.nobukti
                         left outer join (select nopohd from dbbeli group by NOPOHD) F on e.noso=f.nopohd
                           where B.NoBukti Is null       and f.nopohd is null
                         And D.Tipe= 'DPH'   and  D.Valas= :valas
                           and Cast(Case when Case when D.IsOtorisasi1=1 then 1 else 0 end+
                           Case when D.IsOtorisasi2=1 then 1 else 0 end+
                           Case when D.IsOtorisasi3=1 then 1 else 0 end+
                          Case when D.IsOtorisasi4=1 then 1 else 0 end+
                                            Case when D.IsOtorisasi5=1 then 1 else 0 end=D.MaxOL then 0
                                          else 1
                                     end As Bit)=0   and E.nobukti is not null
                            group by D.Nobukti,A.KODECUSTSUPP,C.NAMACUSTSUPP,E.nobukti
                          ,D.Valas,X.kl             " , ["valas" => $req->valas ]);
    return $listData;
  }


  public function listDPP (Request $req) {

    $listData = DB::connection('SML')->select("select D.Nobukti,e.NOBUKTI NOUM,A.KODECUSTSUPP,C.NAMACUSTSUPP
                          ,D.Valas,Sum(A.DIBAYAR) DIBAYAR ,X.KL,SUM(LB) LB
                               from DBDPHDET A
                           Left Outer Join dbTransaksi B on A.NoBukti=B.NODPH and A.KodeCUstSupp=B.CustSUppP
                           Left OUter Join DBCUSTSUPP C on A.KODECUSTSUPP=C.KODECUSTSUPP
                           Left Outer JOin DbDPH D on A.Nobukti=D.Nobukti
                           Left Outer Join (select nobukti,KodeCustSupp,Sum(NilaiK) KL from DBKurangBayar group by  nobukti,KodeCustSupp)  X on A.nobukti=X.Nobukti and A.KodeCustSupp=X.kodeCustSupp
                         left outer join (select nobukti,noso from dbUMjual group by nobukti,noso) E on a.nofaktur=e.nobukti
                         left outer join (select nopohd from dbbeli group by NOPOHD) F on e.noso=f.nopohd
                           where B.NoBukti Is null       and f.nopohd is null
                         And D.Tipe= 'DPP'   and  D.Valas= :valas
                           and Cast(Case when Case when D.IsOtorisasi1=1 then 1 else 0 end+
                           Case when D.IsOtorisasi2=1 then 1 else 0 end+
                           Case when D.IsOtorisasi3=1 then 1 else 0 end+
                          Case when D.IsOtorisasi4=1 then 1 else 0 end+
                                            Case when D.IsOtorisasi5=1 then 1 else 0 end=D.MaxOL then 0
                                          else 1
                                     end As Bit)=0   and E.nobukti is null
                            group by D.Nobukti,A.KODECUSTSUPP,C.NAMACUSTSUPP,E.nobukti
                          ,D.Valas,X.kl" , ["valas" => $req->valas ]);
    return $listData;
  }




  public function listDepartemen (Request $req) {

    $listData = DB::connection('SML')->select("select * from DBDEPART");
    return $listData;
  }


  public function listLawan (Request $req) {

        $username = \Auth::user()->username;
        $listData = [];

        // if ($req->transaksi == 'BBK') {
          $listData = DB::connection('SML')->select("
          select  b.Perkiraan, b.Keterangan,b.Simbol,a.Kode, a.IsLokalOrExim from dbposthutpiut a
 left outer join dbperkiraan b on b.perkiraan=a.perkiraan
where a.Kode='HT' and a.IsLokalOrExim=0 order by a.Perkiraan

                      " );

        // } else {
        //   $listData = DB::connection('SML')->select("
        //   select a.Perkiraan, a.Keterangan, a.Simbol,C.Kode, C.IsLokalOrExim from dbPerkiraan a
        //         left Outer join dbAksesPerkiraan b on b.Perkiraan=a.Perkiraan
        //          Left Outer Join (select perkiraan,kode,IsLokalOrExim from dbPOSTHUTPIUT group by perkiraan,kode,IsLokalOrExim)  C on A.Perkiraan=C.Perkiraan
        //         where a.Tipe=1 and a.Perkiraan <> :perkiraan and b.UserID = :username
        //         and a.perkiraan not in (select Perkiraan from DBPOSTHUTPIUT where Kode='HT' and Perkiraan not in ('116100','21203') )
        //
        //         order by a.Perkiraan     " , ["perkiraan" => $req->perkiraan , "username" => $username ]);
        //
        // }



    return $listData;
  }

  public function listInvoice (Request $req) {

    $listData = DB::connection('SML')->select("declare @awal Varchar(15)
    Select @Awal= :kodecustsupp
    select Cast(1 as Bit) Pilih,Convert(Numeric(18,2),0) Nilai, a.Valas KodeVls, a.Kurs, a.NoFaktur,Min(a.Tanggal) Tanggal, Min(a.JatuhTempo) JatuhTempo,
           SUM(a.Saldo) Saldo, SUM(A.SaldoD) SaldoD
    from dbo.vwHutPiut a
         left outer join (select NoInv from dbkreditNoteDet) b on b.NoInv=a.NoFaktur
    where a.KodeCustSupp=@Awal and B.NoInv is null and a.Tipe='PT'
    Group by a.NoFaktur,a.Valas, a.Kurs
    Having SUM(a.Saldo)<>0 or SUM(A.SaldoD)<>0
    Order by a.Nofaktur
" , ["kodecustsupp" => $req->kodecustsupp]);
    return $listData;

  }

  public function listCustsupp (Request $req) {

    $listData = DB::connection('SML')->select("select kodecustsupp , namacustsupp , alamat1 , PPN , HARI from DBCUSTSUPP where JENIS = 1 and isaktif = 1");
    return $listData;
  }

  public function listDPHBBG (Request $req) {

    $listData = DB::connection('SML')->select("Select A.NoBukti, A.Tanggal, B.KodeCustSupp, C.NamaCustSupp, A.Valas,Z.KURS , sum(B.Dibayar) Dibayar,
sum(isnull(B.KL,0)) KL, sum(isnull(B.LB,0)) LB, max(isnull(B.Perkiraan,'')) Perkiraan
 from dbDPH A
 left outer join dbDPHDet B on B.NoBukti=A.NoBukti
 left outer join dbCustSupp C on C.KodeCustSupp=B.KodeCustSupp
 left outer join dbvalas Z on A.Valas=Z.KODEVLS
 left outer join (select NoDPH, CustSuppP, sum(Debet) Debet from dbTransaksi group by NoDPH, CustSuppP) X on X.NoDPH=A.NoBukti and X.CustSuppP=B.KodeCustSupp
 where  A.Tipe='DPH'
 and Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                               Case when A.IsOtorisasi2=1 then 1 else 0 end+
                               Case when A.IsOtorisasi3=1 then 1 else 0 end+
                               Case when A.IsOtorisasi4=1 then 1 else 0 end+
                               Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                           else 1
                      end As Bit)=0
 group by A.NoBukti, A.Tanggal, B.KodeCustSupp, C.NamaCustSupp, A.Valas,z.KURS ,isnull(X.Debet,0)
 having sum(B.Dibayar+B.KL)-isnull(X.Debet,0)<>0
 Order by A.NoBukti, A.Tanggal, B.KodeCustSupp, C.NamaCustSupp");
    return $listData;
  }
  // public function listPelanggan (Request $req) {
  //
  //   $listData = DB::connection('SML')->select("select kodecustsupp , namacustsupp , alamat1 , PPN , HARI from DBCUSTSUPP where JENIS = 1");
  //   return $listData;
  // }



  public function listCustsuppUMB (Request $req) {

    $listData = DB::connection('SML')->select("Select c.KODESUPP,B.NAMACUSTSUPP
 from DBUMJUAL A
 LEFT OUTER JOIN DBPO C ON A.NOSO=C.NOBUKTI
 LEFT OUTER JOIN DBCUSTSUPP B ON c.KODESUPP=B.KODECUSTSUPP
where  c.KODESUPP is not null


GROUP BY c.KODESUPP,B.NAMACUSTSUPP
ORDER BY c.KODESUPP,B.NAMACUSTSUPP");
    return $listData;
  }

  public function sumBBMUHT (Request $req) {




    $listData = DB::connection('SML')->select("select sum(DPP) SubTotal from tempRUMJual where IDUser= :username and Isnull(Noretur,'')<>''
      and Isnull(Notrans,'')<>''", ["username" =>  \Auth::user()->username]);
    return $listData;
  }


  public function prosesUMB (Request $req) {

    $resx = DB::connection('SML')->update("delete TEMPRUMJUAL where IDUSER = :username", ["username" => \Auth::user()->username]);





    $res = DB::connection('SML')->update("declare @nilaippn numeric(18,2)
set @nilaippn= (select top 1 nilaippn/100.00 from dbnilaippn where GETDATE() between tglawal and tglakhir)
insert into tempRUMJual (NOBUKTI,NORETUR,TANGGAL,NOSO,VALAS,KURS,DPP,PPN,IDUSER,KODESUPP,SUBTOTAL)
select NoFaktur,'' NOretur,B.TANGGAL,b.NOSO,a.Valas,a.Kurs,
SUM(CASE WHEN E.PPN<>2 THEN A.debet- isnull(c.nuangmuka,0) ELSE (A.Debet / (1.0 + @nilaippn)) -
CASE WHEN E.PPN<>2 THEN isnull(c.nuangmuka,0) else (isnull(c.nuangmuka,0) / (1.0 + @nilaippn)) end END) debet,SUM(b.ppnum) ppnum, :username , :custsupp1 ,SUM(A.debet - isnull(c.nuangmuka,0)) SUBTOTAL
from DBHUTPIUT A
left outer join (select NOBUKTI,NOSO,SUM(DPP) dppum,SUM(ppn) ppnum,SUM(SUBTOTAL) subtotalum ,TANGGAL
                                from DBUMJUAL
                                where NoRetur=''
                                group by NOBUKTI,NOSO,TANGGAL) b on a.NoFaktur=b.NOBUKTI
 left outer join (select sum(isnull(nuangmuka,0)) nuangmuka,nopohd,PPN from dbbeli group by nopohd,PPN ) c on b.noso=c.nopohd
 Left outer join dbumjual D on B.nobukti=D.nobukti and isnull(D.NOretur,'')<>''
 left outer join dbpo E on b.NOSO=E.NOBUKTI
where A.Perkiraan IN ('116100','21201')  and a.nofaktur like '%UMB%' and A.tipetrans='L'
 AND a.KODECUSTSUPP= :custsupp2  and D.nobukti is null
group by NoFaktur,b.NOSO,a.Valas,a.Kurs,B.TANGGAL
HAVING SUM(A.debet - isnull(c.nuangmuka,0))>0   ", ["username" => \Auth::user()->username , "custsupp1" => $req->custsupp, "custsupp2" => $req->custsupp]);



$listData = DB::connection('SML')->select("Select *,Nobukti+Noretur KeyBukti,
(select top 1 nilaippn/100.00 from dbnilaippn where GETDATE() between tglawal and tglakhir) ppnx,
(select sum(DPP) SubTotal from tempRUMJual where IDUser= :username1 ) totalqntx
from tempRUMJual
WHERE KODESUPP= :custsupp and IDuser= :username", ["custsupp" => $req->custsupp , "username1" =>  \Auth::user()->username , "username" =>  \Auth::user()->username]);

  return $listData;


  }


  public function listUMB (Request $req) {


    $listData = DB::connection('SML')->select("Select *,Nobukti+Noretur KeyBukti
    from tempRUMJual
    WHERE KODESUPP= :custsupp and IDuser= :username", ["custsupp" => $req->custsupp , "username" =>  \Auth::user()->username]);

      return $listData;



  }



  public function spOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbtrans set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal where nobukti = :nobukti", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
    return $res;
  }
  public function spBatalOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbtrans set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL where nobukti = :nobukti", [ "nobukti" => $req->nobukti]);
    return $res;
  }

  public function spAdd (Request $req) {


    // $urutGiro = DB::connection('SML')->select("select MAX(urut) urut from dbtransaksi where NoBukti = :nobukti " , [ "nobukti" => 'MGX/BGT/00007/0126'] );
    //
    // return [
    //   "asd"=> $urutGiro[0]->urut,
    //   'wer'=> $urutGiro[0]
    // ];

    // sp_TransaksiGiroTerima
    $username = \Auth::user()->username;
    $tempData = $req->tempData;
    $jmlrecord = $req->jmlrecord;
    if ($jmlrecord == 0 ) {
      $check = DB::connection('SML')->select('select * from DBTRANS where Nobukti = :nobukti',["nobukti" => $req->nobukti]);
        if ($check) {
          return 2;
      }
    }

    // DB::connection('SML')->statement('exec sp_TransaksiKasBankDPH ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [$values]);



      // foreach ($tempData as $d)  {
        // code...
        $values = [
          $req->choice,
          $req->nobukti,
          $req->nourut,
          $req->tanggal,
          $req->kepadaterima ? $req->kepadaterima : '',
          0 ,
          $req->kodedevisi,
          $req->kodeperkiraan,
          $req->lawan,
          $req->keterangandet,
          '' , //
          $req->jumlah,
          0,
          $req->valas,
          $req->kurs,
          (int)$req->jumlah * (int)$req->kurs ,
          0 ,
          $req->transaksi ,
          'H',
          $req->transaksi == 'BBG' ? $req->kodecustsupp : '' ,
          $req->transaksi == 'BBG' ? $req->kodecustsupp : '' , //
          $req->urut ,
          '',
          '',
          '',
          '',
          '-',
          $req->departemen ? $req->departemen : '-',
          '',
          '',
          $req->transaksi == 'BBG' ? 'H+' : 'H-' , //
          '' ,
          $req->kodeperkiraan,
          '',
          '',
          '',
          $req->nodph

        ];

  DB::connection('SML')->statement('exec sp_TransaksiGiroBuka ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);
  $urutGiro = DB::connection('SML')->select("select MAX(urut) urut from dbtransaksi where NoBukti = :nobukti " , [ "nobukti" => $req->nobukti] );
  // return ["urutGiro" => $urutGiro];
  // return [
  //   "asd"=> $urutGiro[0]->urut,
  //   'wer'> $urutGiro[0]['urut']
  // ];

  foreach ($tempData as $d) {
    // code...

    DB::connection('SML')->update("insert into dbgiro (NoGiro, Bank , TglGiro, BuktiBuka , UrutBuktiBuka, TglBuka, KodeVls , Kurs , DebetRp , KreditRp , Debet , Kredit , Keterangan , Kas , Tipe)
          values (:nogiro , :bank , :tglgiro , :nobukti , :urut , :tglheader , :valas , :kurs , :debetrp , :kreditrp , :debet , :kredit , :keteranganhd , :lawan , :tipe)
          " , [
            "nogiro" => $d['nogiro'],
            "bank" => $d['bank'],
            "tglgiro" => $d['tanggal'],
            "nobukti" => $req->nobukti,
            "urut" => $urutGiro[0]->urut,
            "tglheader" => $req->tanggal,
            "valas" => $d['valas'],
            "kurs" => $d['kurs'],
            "debetrp" => 0,
            "kreditrp" => (int)$d['nilaigiro'] * (int)$d['kurs'],
            "debet" => 0,
            "kredit" => (int)$d['nilaigiro'],
            "keteranganhd" => $req->kepadaterima ? $req->kepadaterima : '',
            "lawan" => $req->kodeperkiraan,
            "tipe" => $d['tipe']
          ]);
  }


      // return 1;

      return 1;

  }


  public function listPencairanGiroKoreksiBGT (Request $req) {
    $listGiro = DB::connection("SML")->select("
    select NoGiro
      ,Bank
      ,TglGiro
      ,Debet
      ,Kredit
      ,DebetRp
      ,KreditRp
      ,Keterangan
      ,TglBuka
      ,BuktiBuka
      ,UrutBuktiBuka
      ,TglCair
      ,BuktiCair
      ,KeteranganCair
      ,UrutBuktiCair
      ,Kodevls
      ,Kurs
      ,Jumlah
      ,Tipe
      ,FlagSimbol
      ,Kas from DBGIRO where buktibuka = :nobukti and urutbuktibuka = :urut
    " , ["nobukti" => $req->nobukti , "urut" => $req->urut]);

    return $listGiro;



  }

  public function spGiroBGT (Request $req) {
    // newgiro

    if ($req->choice == "I") {
      DB::connection('SML')->update("insert into dbgiro (NoGiro, Bank , TglGiro, BuktiBuka , UrutBuktiBuka, TglBuka, KodeVls , Kurs , DebetRp , KreditRp , Debet , Kredit , Keterangan , Kas , Tipe)
            values (:nogiro , :bank , :tglgiro , :nobukti , :urut , :tglheader , :valas , :kurs , :debetrp , :kreditrp , :debet , :kredit , :keteranganhd , :lawan , :tipe)
            " , [
              "nogiro" => $req->nogiro,
              "bank" => $req->bank,
              "tglgiro" => $req->tanggal,
              "nobukti" => $req->nobukti,
              "urut" => $req->urutbuktibuka,
              "tglheader" => $req->tanggal,
              "valas" => $req->valas,
              "kurs" => $req->kurs,
              "debetrp" => 0,
              "kreditrp" => (int)$req->nilaigiro * (int)$req->kurs,
              "debet" => 0,
              "kredit" => (int)$req->nilaigiro,
              "keteranganhd" => $req->kepadaterima ? $req->kepadaterima : '',
              "lawan" => $req->kodeperkiraan,
              "tipe" => 'HT'
            ]);



            DB::connection('SML')->update(" update dbtransaksi
            set
            debet = :xdebet ,
            debetrp = :xdebetrp
              where nobukti = :buktibuka and urut = :urutbuktibuka
                  " , [
                    "xdebet" => $req->xdebet,
                    "xdebetrp" => $req->xdebetrp,
                    "buktibuka" => $req->nobukti,
                    "urutbuktibuka" => $req->urutbuktibuka,

                  ]);


    } else {

      DB::connection('SML')->update(" delete from dbgiro

        where nogiro = :nogiro and bank = :bank
            " , [

              "nogiro" =>$req->nogiro,
              "bank" => $req->bank,
            ]);


      DB::connection('SML')->update(" update dbtransaksi
      set
      debet = :xdebet ,
      debetrp = :xdebetrp
        where nobukti = :buktibuka and urut = :urutbuktibuka
            " , [
              "xdebet" => $req->xdebet,
              "xdebetrp" => $req->xdebetrp,
              "buktibuka" => $req->nobukti,
              "urutbuktibuka" => $req->urutbuktibuka,

            ]);






            return 1;


    }






    return 1;
  }



    public function spDelete (Request $req) {


      if ($req->transaksi == 'BGT') {
        $xcheck = DB::connection('SML')->select("select * from DBGIRO where buktibuka = :nobukti and UrutBuktiBuka = :urut and BuktiCair <> ''" , [ "nobukti" => $req->nobukti , "urut" => $req->urut] );


      } else {
        $xcheck = DB::connection('SML')->select("select * from DBGIRO where BuktiCair = :nobukti and UrutBuktiCair = :urut" , [ "nobukti" => $req->nobukti , "urut" => $req->urut] );


      }

      if ($xcheck) {


        return 9;
      }



      $username = \Auth::user()->username;

          $values = [
            $req->choice,
            $req->nobukti,
            $req->nourut,
            $req->tanggal,
            $req->kepadaterima ? $req->kepadaterima : '',
            0 ,
            $req->kodedevisi,
            $req->kodeperkiraan,
            $req->lawan,
            $req->keterangandet,
            '' , //
            $req->jumlah,
            0,
            $req->valas,
            $req->kurs,
            (int)$req->jumlah * (int)$req->kurs ,
            0 ,
            $req->transaksi ,
            'H',
            $req->transaksi == 'BBG' ? $req->kodecustsupp : '' ,
            $req->transaksi == 'BBG' ? $req->kodecustsupp : '' , //
            $req->urut ,
            '',
            '',
            '',
            '',
            '-',
            '-',
            '',
            '',
            $req->transaksi == 'BBG' ? 'H+' : 'H-' , //
            '' ,
            $req->kodeperkiraan,
            '',
            '',
            '',
            ''

          ];

          if ($req->transaksi == 'BGT') {
            $xcheck = DB::connection('SML')->update("delete from DBGIRO where buktibuka = :nobukti and UrutBuktiBuka = :urut " , [ "nobukti" => $req->nobukti , "urut" => $req->urut] );


          } else {
            $xcheck = DB::connection('SML')->update("update dbGiro set kredit=0,KreditRp=0,bukticair='', tglcair=null, keteranganCair=''  where BuktiCair = :nobukti and UrutBuktiCair = :urut" , [ "nobukti" => $req->nobukti , "urut" => $req->urut] );


          }



    DB::connection('SML')->statement('exec sp_TransaksiGiroBuka ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);


        return 1;

    }




    public function spAddBGC (Request $req) {


      // $urutGiro = DB::connection('SML')->select("select MAX(urut) urut from dbtransaksi where NoBukti = :nobukti " , [ "nobukti" => 'MGX/BGT/00007/0126'] );
      //
      // return [
      //   "asd"=> $urutGiro[0]->urut,
      //   'wer'=> $urutGiro[0]
      // ];

      // sp_TransaksiGiroTerima
      $username = \Auth::user()->username;
      $tempDataPencairanGiro = $req->tempDataPencairanGiro;
      $jmlrecord = $req->jmlrecord;
      if ($jmlrecord == 0 ) {
        $check = DB::connection('SML')->select('select * from DBTRANS where Nobukti = :nobukti',["nobukti" => $req->nobukti]);
          if ($check) {
            return 2;
        }
      }

      // DB::connection('SML')->statement('exec sp_TransaksiKasBankDPH ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [$values]);



        // foreach ($tempData as $d)  {
          // code...
          $values = [
            $req->choice,
            $req->nobukti,
            $req->nourut,
            $req->tanggal,
            $req->kepadaterima ? $req->kepadaterima : '',
            0 ,
            $req->kodedevisi, // ?
            $req->kodeperkiraan,
            $req->lawan,
            $req->keterangandet ? $req->keterangandet : '',
            '' , //
            $req->xdebet,
            0,
            $req->valas,
            $req->kurs,
            $req->xdebetrp ,
            0 ,
            $req->transaksi ,
            'H',
            $req->transaksi == 'BBG' ? $req->kodecustsupp : '' ,
            $req->transaksi == 'BBG' ? $req->kodecustsupp : '' , //
            0 ,
            '',
            '',
            '',
            '',
            '-',
            '-',
            '',
            '',
            $req->transaksi == 'BBG' ? 'H+' : 'H-' , //
            '' ,
            $req->lawan ,
            '',
            '',
            '',
            ''

          ];

    DB::connection('SML')->statement('exec sp_TransaksiGiroBuka ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);
    $urutGiro = DB::connection('SML')->select("select MAX(urut) urut from dbtransaksi where NoBukti = :nobukti " , [ "nobukti" => $req->nobukti] );

    // return [
    //   "asd"=> $urutGiro[0]->urut,
    //   'wer'> $urutGiro[0]['urut']
    // ];

    foreach ($tempDataPencairanGiro as $d) {
      // code...

      DB::connection('SML')->update(" update DBGIRO set

      debet = :kredit,
      debetrp = :kreditrp,
      TglCair = :tglcair ,
      BuktiCair = :bukticair ,
      keterangancair = :keterangancair ,
      urutbukticair = :urutbukticair
        where nogiro = :nogiro and bank = :bank
            " , [
              "kredit" => (int)$d['jumlah'],
              "kreditrp" => (int)$d['jumlah'] * (int)$d['Kurs'],
              "tglcair" => $req->tanggal,
              // "bank" => $d['Bank'],
              "bukticair" => $req->nobukti,
              "keterangancair" => $d['Keterangan'] ? $d['Keterangan'] : '',
              "urutbukticair" => $urutGiro[0]->urut,
              "nogiro" => $d['Nogiro'],
              "bank" => $d['Bank'],
            ]);
    }


        // return 1;

        return 1;

    }



        public function spDeleteGiroKoreksi (Request $req) {


          // return [
          //   "xdebet" => $req->xdebet,
          //   "xdebetrp" => $req->xdebetrp,
          //   "bukticair" => $req->nobukti,
          //   "urutbukticair" => $req->urutbukticair,
          //
          // ];
          DB::connection('SML')->update(" update dbgiro
          set debet = 0,
          debetrp = 0,
          TglCair = NULL ,
          BuktiCair = '' ,
          keterangancair = '' ,
          urutbukticair = 0
            where nogiro = :nogiro and bank = :bank
                " , [

                  "nogiro" =>$req->nogiro,
                  "bank" => $req->bank,
                ]);


          DB::connection('SML')->update(" update dbtransaksi
          set
          debet = :xdebet ,
          debetrp = :xdebetrp
            where nobukti = :bukticair and urut = :urutbukticair
                " , [
                  "xdebet" => $req->xdebet,
                  "xdebetrp" => $req->xdebetrp,
                  "bukticair" => $req->nobukti,
                  "urutbukticair" => $req->urutbukticair,

                ]);






                return 1;




        }




    public function spAddGiroKoreksi (Request $req) {


      // return [
      //   "xdebet" => $req->xdebet,
      //   "xdebetrp" => $req->xdebetrp,
      //   "bukticair" => $req->nobukti,
      //   "urutbukticair" => $req->urutbukticair,
      //
      // ];
      DB::connection('SML')->update(" update dbgiro
      set debet = :kredit,
      debetrp = :kreditrp,
      TglCair = :tglcair ,
      BuktiCair = :bukticair ,
      keterangancair = :keterangancair ,
      urutbukticair = :urutbukticair
        where nogiro = :nogiro and bank = :bank
            " , [
              "kredit" => $req->kredit,
              "kreditrp" => $req->kreditrp,

              "tglcair" => $req->tanggal,

              "bukticair" => $req->nobukti,
              "urutbukticair" => $req->urutbukticair,
              "keterangancair" => $req->keterangancair ? $req->keterangancair : '',
              "nogiro" =>$req->nogiro,
              "bank" => $req->bank,
            ]);


      DB::connection('SML')->update(" update dbtransaksi
      set
      debet = :xdebet ,
      debetrp = :xdebetrp
        where nobukti = :bukticair and urut = :urutbukticair
            " , [
              "xdebet" => $req->xdebet,
              "xdebetrp" => $req->xdebetrp,
              "bukticair" => $req->nobukti,
              "urutbukticair" => $req->urutbukticair,

            ]);






            return 1;




    }


  public function spAddDPPDPH (Request $req) {


      $username = \Auth::user()->username;

      $jmlrecord = $req->jmlrecord;


      if ($jmlrecord == 0) {
        $check = DB::connection('SML')->select('select * from DBTRANSAKSI where Nobukti = :nobukti',["nobukti" => $req->nobukti]);
          if ($check) {
            return 2;
        }
      }

      $tempData = DB::connection('SML')->select('exec SP_ViewDPH ?,?,?,?,?' , [
        $req->dppdph,
        $req->valas,
        $req->nodph,
        $req->perkiraan,
        $req->custsupp

      ]);

      // return $tempData;

      // $outstandingReguler = DB::connection("SML")->select('exec sp_sosiapkirim ?,?,?',[ $periode->bulan , $periode->tahun ,\Auth::user()->username ]);

      foreach ($tempData as $d)  {
        // return [
        //   'a' => $d->DIBAYAR,
        //   'b' => $req->kurs
        // ];

        DB::connection('SML')->statement('exec sp_TransaksiKasBankDPH ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
          $req->choice,
          $req->nobukti,
          $req->nourut,
          $req->tanggal,
          $req->note ? $req->note : '',
          $req->lampiran ? $req->lampiran : '',
          $req->kodedevisi,
          $req->perkiraanx,
          $req->lawanx,
          $req->keterangan ? $req->keterangan : '', // 10
          $req->keterangan2 ? $req->keterangan2 : '',
          $d->DIBAYAR,
          $req->kredit,
          $req->valas,
          $req->kurs,
          (float) $d->DIBAYAR * (float) $req->kurs,
          $req->kreditrp,
          $req->transaksi,
          $req->tphc,
          $req->custsuppP ? $req->custsuppP : '' , // 20
          $req->custsuppL ? $req->custsuppL : '' ,
          $req->urut,
          $req->noaktivaP ? $req->noaktivaP : '' ,
          $req->noaktivaL ? $req->noaktivaL : '' ,
          $req->statusaktivaP ? $req->statusaktivaP : '',
          $req->statusaktivaL ? $req->statusaktivaL : '',
          $req->nobon ? $req->nobon : '',
          $req->kodedepartemen,
          $req->kodeP ? $req->kodeP : '',
          $req->kodeL ? $req->kodeL : '', // 30
          $req->statusgiro ? $req->statusgiro : '',
          $req->simbol,
          $req->kodeperkiraan,
          $req->flagsimbol ? $req->flagsimbol : '',
          $req->kodecost ? $req->kodecost : '',
          $req->kodesubcost ? $req->kodesubcost : '',
          $req->nodph ?   $req->nodph : '',
          $d->Urut,
          $req->dppdph ? $req->dppdph : '', // 40
          $username,
          $req->tp ? $req->tp : '',
          $req->ppklx ? $req->ppklx : '',
          $d->NOFAKTUR ? $d->NOFAKTUR  : '',
          $req->plok,
          $req->nobons ? $req->nobons : '',
          $jmlrecord,
          $req->notitipan ? $req->notitipan : '',
          $req->uruttitipan,
          $req->keterangandetail ? $req->keterangandetail : '', // 50
          $req->pskb,

        ]);

        $jmlrecord = 1;
      }

      return 1;

  }



  public function spAddTempRUMJUAL (Request $req) {
    $username = \Auth::user()->username;
    $periode = DB::connection("SML")->select('select TOP 1 * from DBPERIODE where user_id = :username ' , ["username" => $username]);
    $kode = 'RUM';
    $inisial = DB::connection("SML")->select('select ' . $kode . ' from DBNOMOR');
    // $inisialx = 'KN';
    // return $inisialx;
    $values = [
        $inisial[0]->$kode,
        $periode[0]->bulan,
        $periode[0]->tahun,
        $username
    ];

    // $x = DB::connection('SML')->select("select * from tempRUMJual where NOBUKTI = :noumb and NOSO = :noso and IDUSER = 'sa' and NORETUR <> ''")


    $noBukti = DB::connection('SML')->select('exec SP_IsiNobuktiSimbol ?,?,?,?',$values);

    $res = DB::connection('SML')->update("insert into tempRUMJual (NOBUKTI,NORETUR,TANGGAL,NOSO,VALAS,KURS,DPP,PPN,IDUSER,KODESUPP,SUBTOTAL,Notrans,uruttrans)
    VALUES ( :noumb , :noretur , :tanggal , :noso , :valas , :kurs , :dpp , :ppn , :username , :kodesupp , :subtotal , :nobukti , :urut) " , [
      "noumb" => $req->noumb,
      "noretur" => $noBukti[0]->Nobukti,
      "tanggal" => $req->tanggal,
      "noso" => $req->noso,
      "valas" => $req->valas,
      "kurs" => $req->kurs,
      "dpp" => $req->dpp,
      "ppn" => $req->ppn,
      "username" => $username,
      "kodesupp" => $req->kodesupp,
      "subtotal" => $req->subtotal,
      "nobukti" => $req->nobukti,
      "urut" => $req->urut,

    ]);


    $listData = DB::connection('SML')->select("Select *,Nobukti+Noretur KeyBukti,
    (select top 1 nilaippn/100.00 from dbnilaippn where GETDATE() between tglawal and tglakhir) ppnx
    , (select sum(DPP) SubTotal from tempRUMJual where IDUser= :username1 ) totalqntx
    from tempRUMJual
    WHERE KODESUPP= :custsupp and IDuser= :username order by nobukti , noretur", ["custsupp" => $req->kodesupp, "username1" =>  \Auth::user()->username , "username" =>  \Auth::user()->username]);

    return $listData;








  }

  public function spDeleteTempRUMJUAL (Request $req) {

    // delete TempRumJual where nobukti=:0 and Noretur=:1
    $res = DB::connection('SML')->update("delete TempRumJual where nobukti= :nobukti and Noretur= :noretur and IDUSER = :username", ["nobukti" => $req->noumb ,"noretur" => $req->noretur,  "username" =>  \Auth::user()->username]);




    $listData = DB::connection('SML')->select("Select *,Nobukti+Noretur KeyBukti,
    (select top 1 nilaippn/100.00 from dbnilaippn where GETDATE() between tglawal and tglakhir) ppnx
    , (select sum(DPP) SubTotal from tempRUMJual where IDUser= :username1 ) totalqntx
    from tempRUMJual
    WHERE KODESUPP= :custsupp and IDuser= :username order by nobukti , noretur", ["custsupp" => $req->kodesupp , "username1" =>  \Auth::user()->username , "username" =>  \Auth::user()->username]);

    return $listData;
  }

  public function spUpdateTempRUMJUAL (Request $req) {
    $username = \Auth::user()->username;
    $res = DB::connection('SML')->update("update TEMPRUMJUAL set DPP = :dpp , PPN = :ppn , subtotal = :subtotal where nobukti= :nobukti and Noretur= :noretur and IDUSER = :username",
    [
      "dpp" => $req->dpp,
      "ppn" => $req->ppn,
      "subtotal" => $req->subtotal,
      "nobukti" => $req->noumb ,
      "noretur" => $req->noretur ? $req->noretur : '' ,
      "username" =>  \Auth::user()->username
    ]);

    $listData = DB::connection('SML')->select("Select *,Nobukti+Noretur KeyBukti,
    (select top 1 nilaippn/100.00 from dbnilaippn where GETDATE() between tglawal and tglakhir) ppnx,
    (select sum(DPP) SubTotal from tempRUMJual where IDUser= :username ) totalqntx
    from tempRUMJual
    WHERE KODESUPP= :custsupp and IDuser= :username order by nobukti , noretur", ["custsupp" => $req->kodesupp , "username1" =>  \Auth::user()->username , "username" =>  \Auth::user()->username]);

    return $listData;


  }












}
