<?php

namespace App\Http\Controllers\Marketing;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\DBFLMENU;
use App\Model\NewPeriode;
use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Model\VwPPL;



// use App\Http\Controllers\NewMenuController;

class SuratJalanController extends Controller
{

  public function index(Request $req) {
    $kodemenu = '041014';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu , $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }


    // $users = DB::connection("SML")->select('select * from new_users');
    // $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    // $listData = DB::connection('SML')->select('SELECT * FROM DBMERK');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(4);


    // $outstanding = VwPPL::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->where('IsJasa', 0)->where('pAgen', 1)->groupBy('NoBukti');
    $tempOutstanding = DB::connection("SML")->select("
Declare @bulan Int,@tahun Int,@IDuser varchar(20)
select @Bulan = :bulan,@Tahun = :tahun ,@IDUser = :username
DECLARE @TGL DATETIME
SET @TGL=GETDATE()
select 	A.NOBUKTI, B.URUT, B.KODEBRG, C.NamaBrg, A.NOBUKTI+cast(B.URUT as varchar(3)) KeyUrut,
        case when B.NOSAT=1 Then B.QNT when B.NOSAT=2 Then B.QNT2 when B.Nosat=3 then B.qnt2  End Qnt,
        B.SATUAN,
		case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
		     when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
             when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
		End QntOut,
    D.KodeCustSupp,D.NamaCustSupp,A.KodeGdg,A.Tanggal,
    A.Nobukti+cast(B.urut as varchar(3)) KeyNobukti,Isnull(A.TipePPN,0) TipePPN,
    A.NoResi,A.NoPolKend,A.Sopir,A.JumlahTagihan,A.Kodeexp,
    D.namaCustSupp+'('+A.KodeCust+')' CXcust ,
    M2.NAMA+'('+A.KODEKEBUN+')' xcKEBUN,
    M3.ALAMAT ,A.kodekebun,A.Nopesanan,C.PartNumber,M5.NamaMerk,A.TGLKIRIM DUEDATE,A.UserID,

    case when ISNULL(B.NOserah,'')  IN ('','-')
    then
         case when b.nosat=1 then m62.saldoQnt
         when b.nosat=2 then m62.saldoqnt/c.isi2
         when b.nosat=3 then m62.saldoqnt/c.isi3 end
    else
         case when b.nosat=1 then (m62.saldoQnt) + (ISNULL(m63.saldoQnt,0))
         when b.nosat=2 then (m62.saldoqnt/c.isi2) + (m63.saldoqnt/c.isi2)
         when b.nosat=3 then (m62.saldoqnt/c.isi3) + (m63.saldoqnt/c.isi3) end
    End
    SaldoQnt,

    A.TglKirim,ISnull(M7.Nama,'-') namakebun,
    case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
		 when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
         when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
	End -
	case when ISNULL(B.NOserah,'')  IN ('','-')
    then
         case when b.nosat=1 then m62.saldoQnt
         when b.nosat=2 then m62.saldoqnt/c.isi2
         when b.nosat=3 then m62.saldoqnt/c.isi3 end
    else
         case when b.nosat=1 then (m62.saldoQnt) + (ISNULL(m63.saldoQnt,0))
         when b.nosat=2 then (m62.saldoqnt/c.isi2) + (m63.saldoqnt/c.isi2)
         when b.nosat=3 then (m62.saldoqnt/c.isi3) + (m63.saldoqnt/c.isi3) end
    End QNTzx
    ,A.catatan   ,case when datediff(day,getdate(),A.TGLKIRIM)<5 then 'y' else 'n' end KetW
from	dbSO A
left outer join dBSODet B on B.NoBukti=A.NoBukti
left outer join dbBarang C on C.KodeBrg=B.KodeBrg
left outer join dbCustSupp D on D.KodeCustSupp=A.KODECUST
lEFT oUTER JOIN (SELECT NOSO,UrutSO,SUM(iSNULL(QNT,0)) QNT1SPB,SUM(ISNULL(QNT2,0)) QNT2SPB
				FROM dbSPBDet
				GROUP BY NoSO,UrutSO) M1 ON B.NOBUKTI=M1.NoSO AND B.URUT=M1.UrutSO
lEFT oUTER JOIN (SELECT b.NoSo,B.UrutSo,SUM(iSNULL(A.QNT,0)) QNT1RSPB,SUM(ISNULL(A.QNT2,0)) QNT2RSPB
				FROM dbRSPBDet A
				LEFT OUTER JOIN dbSPBDet B ON A.NoSPB=B.NoBukti AND A.UrutSPB=B.Urut
				GROUP BY b.NoSo,B.UrutSo) M4 ON B.NOBUKTI=M4.NoSO AND B.URUT=M4.UrutSO
left outer join dbkebuncustsupp m2 on A.KODEKEBUN=M2.KODEKEBUN AND A.KODECUST=M2.KODECUSTSUPP
LEFT OUTER JOIN DBALAMATCUST M3 ON A.NOALAMATKIRIM=M3.NOMOR   AND A.KODECUST=M3.KODECUSTSUPP
Left Outer Join Dbmerk M5 on C.KodeMerk=M5.kodeMerk
/*LEFT OUTER JOIN (select kodebrg, sum(SALDOQNT)SaldoQnt,BULAN,TAHUN  from DBSTOCKBRG  where kodegdg<>'GTC'
                            AND    BUlan=@bulan and Tahun=@Tahun
                             and kodegdg in (Select kodegdg from dbPemakaigdg
                          where userid=@IDUser)

                          and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=0)
				group by KODEBRG,BULAN,TAHUN
				) M6 ON B.KODEBRG=M6.KODEBRG

LEFT OUTER JOIN (select kodebrg, sum(SALDOQNT)SaldoQnt,BULAN,TAHUN  from DBSTOCKBRG
				  where BUlan=@bulan and Tahun=@Tahun
                        and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDUser)
					    and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=1)
				  group by KODEBRG,BULAN,TAHUN
				) M61 ON B.KODEBRG=M61.KODEBRG*/
left outer join ( SELECT KODEBRG,SUM(SALDOQNT) SALDOQNT FROM (
				  select  KodeBrg, QntAwal SaldoQnt
				  from 	dbStockBrg
				  where         Tahun=year(@TGL) and Bulan=MONTH(@TGL)
				  --where         Tahun=year(GETDATE()) and Bulan=1
				  and KodeGdg <>'GTC'
				  and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDuser)
                  and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=0)
				  union all
				  select 	KodeBrg,
				  SUM(QntDb)-SUM(QntCr) SaldoQnt
				  from 	vwKartuStock
				  where 	year(Tanggal)=@tahun and month(tanggal)=@bulan and Tanggal<=@TGL
				  --where 	year(Tanggal)=year(GETDATE()) and Tanggal<=GETDATE()
				  and Tipe not in ('AWL')
				  and KodeGdg <>'GTC'
				  and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDuser)
                  and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=0)
				  group by KodeBrg) A
				  GROUP BY A.KODEBRG
				)M62 ON B.KODEBRG=M62.KODEBRG

left outer join ( SELECT KODEBRG,SUM(SALDOQNT) SALDOQNT FROM (
			      select KodeBrg, QntAwal SaldoQnt
				  from 	dbStockBrg
				  where         Tahun=@tahun and Bulan=@bulan
				  --where         Tahun=@tahun and Bulan=1
				  and KodeGdg <>'GTC'
				  and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDuser)
                  and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=1)
				  union all
				  select 	KodeBrg,
				  SUM(QntDb)-SUM(QntCr) SaldoQnt
				  from 	vwKartuStock
				  where 	year(Tanggal)=@tahun and month(tanggal)=@bulan and Tanggal<=@TGL
				  --where 	year(Tanggal)=year(GETDATE()) and Tanggal<=@TGL
				  and KodeGdg = Kodegdg and Tipe not in ('AWL')
				  and KodeGdg <>'GTC'
				  and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDUser)
				  and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=1)
				  group by KodeBrg) A
				  GROUP BY A.KODEBRG
				)M63 ON B.KODEBRG=M63.KODEBRG

LEFT OUTER JOIN DBKEBUNCUSTSUPP m7 on a.KODECUST=m7.KODECUSTSUPP and a.KODEKEBUN=m7.KODEKEBUN
where
      Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit)=0
and B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)>0
and
case when ISNULL(B.NOserah,'')  IN ('','-')
    then
         case when b.nosat=1 then isnull(m62.saldoQnt,0)
         when b.nosat=2 then isnull(m62.saldoqnt,0)/c.isi2
         when b.nosat=3 then isnull(m62.saldoqnt,0)/c.isi3 end
    else
         case when b.nosat=1 then /*(m62.saldoQnt) +*/ (ISNULL(m63.saldoQnt,0))
         when b.nosat=2 then /*(m62.saldoqnt/c.isi2) +*/ (m63.saldoqnt/c.isi2)
         when b.nosat=3 then /*(m62.saldoqnt/c.isi3) + */(m63.saldoqnt/c.isi3) end
End

<
case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
	 when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
     when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
End











order by A.NOBUKTI,B.Urut" , ["bulan" => $periode->bulan , "tahun" =>$periode->tahun , "username" =>  \Auth::user()->username]);
    // foreach ($outstanding as $p) {
    //   // code...
    //   array_push($tempOutstanding, $p);
    // }
    $tempOutstanding2 = DB::connection("SML")->select("
Declare @bulan Int,@tahun Int,@IDuser varchar(20)
select @Bulan= :bulan,@Tahun= :tahun ,@IDUser= :username

DECLARE @TGL DATETIME
SET @TGL=GETDATE()
select 	A.NOBUKTI, B.URUT, B.KODEBRG, C.NamaBrg,
        case when B.NOSAT=1 Then B.QNT when B.NOSAT=2 Then B.QNT2 when B.Nosat=3 then B.qnt2  End Qnt,
        B.SATUAN,
		case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
		     when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
                     when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
		End QntOut,
    D.KodeCustSupp,D.NamaCustSupp,A.KodeGdg,A.Tanggal,
    A.Nobukti+cast(B.urut as varchar(3)) KeyNobukti,Isnull(A.TipePPN,0) TipePPN,
    A.NoResi,A.NoPolKend,A.Sopir,A.JumlahTagihan,A.Kodeexp,
    D.namaCustSupp+'('+A.KodeCust+')' CXcust ,  A.NOBUKTI+cast(B.URUT as varchar(3)) KeyUrut,
    M2.NAMA+'('+A.KODEKEBUN+')' xcKEBUN,isnull(A.DP,0) DP,
    M3.ALAMAT ,A.kodekebun,A.Nopesanan,C.PartNumber,M5.NamaMerk,A.TGLKIRIM DUEDATE,A.UserID ,A.RefPR ,
   A.tglKirim,A.TglKirim,ISnull(M7.Nama,'-') namakebun,Isnull(A.RefPR,0) RefPR,A.catatan ,ISnull(D.pBlackLIst,0) pBlackLIst,

    case when ISNULL(B.NOserah,'')  IN ('','-')
    then
         case when b.nosat=1 then m62.saldoQnt
         when b.nosat=2 then m62.saldoqnt/c.isi2
         when b.nosat=3 then m62.saldoqnt/c.isi3 end
    else
         case when b.nosat=1 then (m62.saldoQnt) + (ISNULL(m63.saldoQnt,0))
         when b.nosat=2 then (m62.saldoqnt/c.isi2) + (m63.saldoqnt/c.isi2)
         when b.nosat=3 then (m62.saldoqnt/c.isi3) + (m63.saldoqnt/c.isi3) end
    End
    SaldoQnt ,


    case when ISNULL(B.NOserah,'')  IN ('','-')
    then
         case when b.nosat=1 then    m62.saldoQnt
         when b.nosat=2 then m62.saldoqnt/c.isi2
         when b.nosat=3 then m62.saldoqnt/c.isi3 end
    else
         case when b.nosat=1 then (m62.saldoQnt) + (ISNULL(m63.saldoQnt,0))
         when b.nosat=2 then (m62.saldoqnt/c.isi2) + (m63.saldoqnt/c.isi2)
         when b.nosat=3 then (m62.saldoqnt/c.isi3) + (m63.saldoqnt/c.isi3) end
    End
    SaldoQnt ,

    case when ISNULL(B.NOserah,'')  IN ('','-')
    then
         case when b.nosat=1 then    m62.saldoQntG01
         when b.nosat=2 then m62.saldoqntG01/c.isi2
         when b.nosat=3 then m62.saldoqntG01/c.isi3 end
    else
         case when b.nosat=1 then (m62.SALDOQNtg01) + (ISNULL(m63.SALDOQNtg01,0))
         when b.nosat=2 then (m62.SALDOQNtg01/c.isi2) + (m63.SALDOQNtg01/c.isi2)
         when b.nosat=3 then (m62.SALDOQNtg01/c.isi3) + (m63.SALDOQNtg01/c.isi3) end
    End
    SALDOQNtg01 ,

     case when ISNULL(B.NOserah,'')  IN ('','-')
    then
         case when b.nosat=1 then    m62.saldoQntG02
         when b.nosat=2 then m62.saldoqntG02/c.isi2
         when b.nosat=3 then m62.saldoqntG02/c.isi3 end
    else
         case when b.nosat=1 then (m62.SALDOQNtg02) + (ISNULL(m63.SALDOQNtg02,0))
         when b.nosat=2 then (m62.SALDOQNtg02/c.isi2) + (m63.SALDOQNtg02/c.isi2)
         when b.nosat=3 then (m62.SALDOQNtg02/c.isi3) + (m63.SALDOQNtg02/c.isi3) end
    End
    SALDOQNtg02 ,

     case when ISNULL(B.NOserah,'')  IN ('','-')
    then
         case when b.nosat=1 then    m62.saldoQntG03
         when b.nosat=2 then m62.saldoqntG03/c.isi2
         when b.nosat=3 then m62.saldoqntG03/c.isi3 end
    else
         case when b.nosat=1 then (m62.SALDOQNtg03) + (ISNULL(m63.SALDOQNtg03,0))
         when b.nosat=2 then (m62.SALDOQNtg03/c.isi2) + (m63.SALDOQNtg03/c.isi2)
         when b.nosat=3 then (m62.SALDOQNtg03/c.isi3) + (m63.SALDOQNtg03/c.isi3) end
    End
    SALDOQNtg03 ,


    case when
         case when ISNULL(B.NOserah,'')  IN ('','-')
		 then
				case when b.nosat=1 then m62.saldoQnt
				when b.nosat=2 then m62.saldoqnt/c.isi2
				when b.nosat=3 then m62.saldoqnt/c.isi3 end
		else
				case when b.nosat=1 then (m62.saldoQnt) + (ISNULL(m63.saldoQnt,0))
				when b.nosat=2 then (m62.saldoqnt/c.isi2) + (m63.saldoqnt/c.isi2)
				when b.nosat=3 then (m62.saldoqnt/c.isi3) + (m63.saldoqnt/c.isi3) end
		End
         >
		case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
			when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
			when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
		End THEN
		        case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
			when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
			when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
		END
	ELSE
	     case when ISNULL(B.NOserah,'')  IN ('','-')
		 then
			case when b.nosat=1 then m62.saldoQnt
			when b.nosat=2 then m62.saldoqnt/c.isi2
			when b.nosat=3 then m62.saldoqnt/c.isi3 end
		else
			case when b.nosat=1 then (m62.saldoQnt) + (ISNULL(m63.saldoQnt,0))
			when b.nosat=2 then (m62.saldoqnt/c.isi2) + (m63.saldoqnt/c.isi2)
			when b.nosat=3 then (m62.saldoqnt/c.isi3) + (m63.saldoqnt/c.isi3) end
    End
    END QNTXZ   ,case when datediff(day,getdate(),A.TGLKIRIM)<5 then 'y' else 'n' end KetW
    ,ISNULL(B.NOserah,'') NOSERAH ,m2.KodeKebun,m2.Nama AlamatLokasi
from	dbSO A
left outer join dBSODet B on B.NoBukti=A.NoBukti
left outer join dbBarang C on C.KodeBrg=B.KodeBrg
left outer join dbCustSupp D on D.KodeCustSupp=A.KODECUST
lEFT oUTER JOIN (SELECT NOSO,UrutSO,SUM(iSNULL(QNT,0)) QNT1SPB,SUM(ISNULL(QNT2,0)) QNT2SPB
				FROM dbSPBDet
				GROUP BY NoSO,UrutSO) M1 ON B.NOBUKTI=M1.NoSO AND B.URUT=M1.UrutSO
lEFT oUTER JOIN (SELECT b.NoSo,B.UrutSo,SUM(iSNULL(A.QNT,0)) QNT1RSPB,SUM(ISNULL(A.QNT2,0)) QNT2RSPB
				FROM dbRSPBDet A
				LEFT OUTER JOIN dbSPBDet B ON A.NoSPB=B.NoBukti AND A.UrutSPB=B.Urut
				GROUP BY b.NoSo,B.UrutSo) M4 ON B.NOBUKTI=M4.NoSO AND B.URUT=M4.UrutSO
left outer join dbkebuncustsupp m2 on A.KODEKEBUN=M2.KODEKEBUN AND A.KODECUST=M2.KODECUSTSUPP
LEFT OUTER JOIN DBALAMATCUST M3 ON A.NOALAMATKIRIM=M3.NOMOR   AND A.KODECUST=M3.KODECUSTSUPP
Left Outer Join Dbmerk M5 on C.KodeMerk=M5.kodeMerk
/*
LEFT OUTER JOIN (select kodebrg, sum(SALDOQNT)SaldoQnt,BULAN,TAHUN  from DBSTOCKBRG
                 Where BUlan=@bulan and Tahun=@Tahun  and KOdeGdg<>'GTC'
                 and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDuser)
                 and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=0)
                 group by KODEBRG,BULAN,TAHUN
                 ) M6 ON B.KODEBRG=M6.KODEBRG

LEFT OUTER JOIN (select kodebrg, sum(SALDOQNT)SaldoQnt,BULAN,TAHUN  from DBSTOCKBRG
				  where BUlan=@bulan and Tahun=@Tahun
                  and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDUser)
				  and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=1)
				  group by KODEBRG,BULAN,TAHUN
				) M61 ON B.KODEBRG=M61.KODEBRG
*/

left outer join ( SELECT KODEBRG,SUM(SALDOQNT) SALDOQNT,SUM(SALDOQNTG01) SALDOQNtg01,SUM(SALDOQNTG02) SALDOQNtg02,SUM(SALDOQNTG03) SALDOQNtg03
                FROM (
				  select  KodeBrg, QntAwal SaldoQnt ,
                          CASE WHEN KODEGDG='G01' THEN QntAwal ELSE 0 END SALDOQNTG01,
                          CASE WHEN KODEGDG='G02' THEN QntAwal ELSE 0 END SALDOQNTG02,
                          CASE WHEN KODEGDG='G03' THEN QntAwal ELSE 0 END SALDOQNTG03
                  from 	dbStockBrg
				  where         Tahun=year(@TGL) and Bulan=MONTH(@TGL)
				  --where         Tahun=year(GETDATE()) and Bulan=1
				  and KodeGdg <>'GTC'
				  and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDuser)
                  and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=0)



				  union all
				  select 	KodeBrg,
				  SUM(QntDb)-SUM(QntCr) SaldoQnt,
				  SUM(case when Kodegdg='G01' then QntDb else 0 end)-SUM(case when Kodegdg='G01' then QntCr else 0 end) SaldoQntG01,
				  SUM(case when Kodegdg='G02' then QntDb else 0 end)-SUM(case when Kodegdg='G02' then QntCr else 0 end) SaldoQntG02,
				  SUM(case when Kodegdg='G03' then QntDb else 0 end)-SUM(case when Kodegdg='G03' then QntCr else 0 end) SaldoQntG03
				  from 	vwKartuStock
				  where 	year(Tanggal)=@tahun and month(tanggal)=@bulan and Tanggal<=@TGL
				  --where 	year(Tanggal)=year(GETDATE()) and Tanggal<=GETDATE()
				  and Tipe not in ('AWL')
				  and KodeGdg <>'GTC'
				  and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDuser)

                  and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=0)
				  group by KodeBrg) A
				  GROUP BY A.KODEBRG
				)M62 ON B.KODEBRG=M62.KODEBRG

left outer join ( SELECT KODEBRG,SUM(SALDOQNT) SALDOQNT ,SUM(SALDOQNTG01) SALDOQNtg01,SUM(SALDOQNTG02) SALDOQNtg02,SUM(SALDOQNTG03) SALDOQNtg03
		        FROM (
			      select KodeBrg, QntAwal SaldoQnt ,
			      CASE WHEN KODEGDG='G01' THEN QntAwal ELSE 0 END SALDOQNTG01,
                          CASE WHEN KODEGDG='G02' THEN QntAwal ELSE 0 END SALDOQNTG02,
                          CASE WHEN KODEGDG='G03' THEN QntAwal ELSE 0 END SALDOQNTG03
				  from 	dbStockBrg
				  where         Tahun=@tahun and Bulan=@bulan
				  --where         Tahun=@tahun and Bulan=1
				  and KodeGdg <>'GTC'
				  and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDuser)
                  and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=1)
				  union all
				  select 	KodeBrg,
				  SUM(QntDb)-SUM(QntCr) SaldoQnt,
				  SUM(case when Kodegdg='G01' then QntDb else 0 end)-SUM(case when Kodegdg='G01' then QntCr else 0 end) SaldoQntG01,
				  SUM(case when Kodegdg='G02' then QntDb else 0 end)-SUM(case when Kodegdg='G02' then QntCr else 0 end) SaldoQntG02,
				  SUM(case when Kodegdg='G03' then QntDb else 0 end)-SUM(case when Kodegdg='G03' then QntCr else 0 end) SaldoQntG03
				  from 	vwKartuStock
				  where 	year(Tanggal)=@tahun and month(tanggal)=@bulan and Tanggal<=@TGL
				  --where 	year(Tanggal)=year(GETDATE()) and Tanggal<=@TGL
				  and KodeGdg = Kodegdg and Tipe not in ('AWL')
				  and KodeGdg <>'GTC'
				  and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDUser)
				  and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=1)
				  group by KodeBrg) A
				  GROUP BY A.KODEBRG
				)M63 ON B.KODEBRG=M63.KODEBRG


LEFT OUTER JOIN DBKEBUNCUSTSUPP m7 on a.KODECUST=m7.KODECUSTSUPP and a.KODEKEBUN=m7.KODEKEBUN
where
      Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit)=0
and

/*B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)>0  */
case when b.NOSAT=1 then
			B.QNT-(Isnull(m1.QNT1SPB,0)+
			 case when b.nosat=1 then Isnull(B.QntBatal,0)
			 when b.nosat=2 then isnull(b.qntBatal,0) * B.isi
			 when b.nosat=3 then isnull(b.qntBatal,0) * B.isi end)
			+ Isnull(m4.QNT1RSPB,0)
	 when b.NOSAT=2 then
			B.QNT2-(Isnull(m1.QNT2SPB,0)+
			 case when b.nosat=1 then Isnull(B.QntBatal,0)/b.ISI
			 when b.nosat=2 then isnull(b.qntBatal,0)
			 when b.nosat=3 then isnull(b.qntBatal,0) end)
			+ Isnull(m4.QNT2RSPB,0)
	when b.NOSAT=3 then
			B.QNT2-(Isnull(m1.QNT2SPB,0)+
			 case when b.nosat=1 then Isnull(B.QntBatal,0)/b.ISI
			 when b.nosat=2 then isnull(b.qntBatal,0)
			 when b.nosat=3 then isnull(b.qntBatal,0) end)
			+ Isnull(m4.QNT2RSPB,0)
end>0
AND  case when ISNULL(B.NOserah,'')  IN ('','-') then isnull(M62.SALDOQNT,0)
		  Else   isnull(M63.SALDOQNT,0) End
>0


" , ["bulan" => $periode->bulan , "tahun" =>$periode->tahun, "username" =>  \Auth::user()->username]);


$tempOutstanding3 = DB::connection("SML")->select("
declare @Tahun int, @Bulan int

select @Tahun= :tahun , @Bulan= :bulan


select  A.NOBUKTI, A.NOURUT, A.TANGGAL, A.NOSPP, A.KODECUSTSUPP, M1.NamaCustSupp,
        A.NoPolKend, A.Container, A.NoContainer, A.NoSeal, A.Catatan, A.IDUser, A.IsFlag Tipe,
        E.Nobukti Noso, D.NoBukti NoSPP,
        Case when A.isFlag=0 then 'SPB Barang Jadi'
             when A.isFlag=1 then 'SPB Bahan Baku dan Lain-lain'
             else ''
        end MyTipe,
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
        A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
        A.IsOtorisasi3, A.OtoUser3, A.TglOto3,
        A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
        A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
        Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                       Case when A.IsOtorisasi3=1 then 1 else 0 end+
                       Case when A.IsOtorisasi4=1 then 1 else 0 end+
                       Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                  else 1
             end As Bit) NeedOtorisasi
        ,Isnull(A.Isbatal,0) Isbatal,A.Userbatal,A.Tglbatal,F.NoPesanan ,
        ISnull(M7.Nama,'-') namakebun,A.RefUKM,(select top 1 NOSPB from DBRSPBDet where NOSPB=a.NoBukti)NOSPB,
        case when year(A.TGLKIRIM)=1899 then null else A.TGLKIRIM END TGLKIRIM,
        CASE WHEN YEAR(A.TGLTERIMA)=1899 THEN NULL ELSE A.TGLTERIMA END TGLTERIMA,
        ISnull(A.CetakKe,0) CetakKe,case when year(A.TglSPBINVC)=1899 then null else A.TglSPBINVC END TglSPBINVC
        ,case when year(A.TglTerimaBRG)=1899 then null else A.TglTerimaBRG END TglTerimaBRG
from	dbSPB A
Left Outer join (Select nobukti, nospp
                 from dbSPBDet
                 Group by nobukti, nospp) C on C.NoBukti=A.NoBukti
Left Outer join (Select nobukti, NoSO
                 from dbSPPDet
                 Group by nobukti, NoSO) D on D.NoBukti=C.NoSPP
Left Outer join (select NObukti,NOso from DBSPBDET group by Nobukti,NOso) E on A.Nobukti=E.NoBukti
Left Outer JOin DBSO F on E.Noso=F.Nobukti
left outer join vwBrowsCustomer B on B.KodeCust=A.KodeCustSupp and B.Sales=F.KODESLS
Left OUter Join DbCustSupp M1 On A.kodeCustSupp=m1.KodeCustSupp
LEFT OUTER JOIN DBKEBUNCUSTSUPP m7 on a.KODECUSTsupp=m7.KODECUSTSUPP and a.KODEKEBUN=m7.KODEKEBUN
LEFT OUTER JOIN DBRSPB M8 ON A.NOBUKTI=M8.NOSPB
where	year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan  and A.Nobukti not like '%POS%'
  and isnull(A.IsOtorisasi1, 0) = 0
/*and (C.kodegdg in (Select kodegdg from dbPemakaigdg
                          where userid=:0 ) or ISNULL(A.ISBATAL,0)=1) */




order by A.NoBukti
" , [ "tahun" =>$periode->tahun, "bulan" => $periode->bulan ]);


$tempOutstanding6 = DB::connection("SML")->select("
declare @Tahun int, @Bulan int

select @Tahun= :tahun , @Bulan= :bulan


select  A.NOBUKTI, A.NOURUT, A.TANGGAL, A.NOSPP, A.KODECUSTSUPP, M1.NamaCustSupp,
        A.NoPolKend, A.Container, A.NoContainer, A.NoSeal, A.Catatan, A.IDUser, A.IsFlag Tipe,
        E.Nobukti Noso, D.NoBukti NoSPP,
        Case when A.isFlag=0 then 'SPB Barang Jadi'
             when A.isFlag=1 then 'SPB Bahan Baku dan Lain-lain'
             else ''
        end MyTipe,
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
        A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
        A.IsOtorisasi3, A.OtoUser3, A.TglOto3,
        A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
        A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
        Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                       Case when A.IsOtorisasi3=1 then 1 else 0 end+
                       Case when A.IsOtorisasi4=1 then 1 else 0 end+
                       Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                  else 1
             end As Bit) NeedOtorisasi
        ,Isnull(A.Isbatal,0) Isbatal,A.Userbatal,A.Tglbatal,F.NoPesanan ,
        ISnull(M7.Nama,'-') namakebun,A.RefUKM,(select top 1 NOSPB from DBRSPBDet where NOSPB=a.NoBukti)NOSPB,
        case when year(A.TGLKIRIM)=1899 then null else A.TGLKIRIM END TGLKIRIM,
        CASE WHEN YEAR(A.TGLTERIMA)=1899 THEN NULL ELSE A.TGLTERIMA END TGLTERIMA,
        ISnull(A.CetakKe,0) CetakKe,case when year(A.TglSPBINVC)=1899 then null else A.TglSPBINVC END TglSPBINVC
        ,case when year(A.TglTerimaBRG)=1899 then null else A.TglTerimaBRG END TglTerimaBRG
from	dbSPB A
Left Outer join (Select nobukti, nospp
                 from dbSPBDet
                 Group by nobukti, nospp) C on C.NoBukti=A.NoBukti
Left Outer join (Select nobukti, NoSO
                 from dbSPPDet
                 Group by nobukti, NoSO) D on D.NoBukti=C.NoSPP
Left Outer join (select NObukti,NOso from DBSPBDET group by Nobukti,NOso) E on A.Nobukti=E.NoBukti
Left Outer JOin DBSO F on E.Noso=F.Nobukti
left outer join vwBrowsCustomer B on B.KodeCust=A.KodeCustSupp and B.Sales=F.KODESLS
Left OUter Join DbCustSupp M1 On A.kodeCustSupp=m1.KodeCustSupp
LEFT OUTER JOIN DBKEBUNCUSTSUPP m7 on a.KODECUSTsupp=m7.KODECUSTSUPP and a.KODEKEBUN=m7.KODEKEBUN
LEFT OUTER JOIN DBRSPB M8 ON A.NOBUKTI=M8.NOSPB
where	year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan  and A.Nobukti not like '%POS%'
  and isnull(A.IsOtorisasi1, 0) <> 0
/*and (C.kodegdg in (Select kodegdg from dbPemakaigdg
                          where userid=:0 ) or ISNULL(A.ISBATAL,0)=1) */




order by A.NoBukti
" , [ "tahun" =>$periode->tahun, "bulan" => $periode->bulan ]);



$listBarang = [];

// $listBarang = DB::connection('SML')->select(" select a.Kodebrg, a.NamaBrg,I.NamaSubGrp,A.PartNumber,J.NAMAMERK,a.ISI1, a.ISI2, a.ISI3,
//                 A.Sat1,A.Sat2 ,A.Sat3,A.pPPN,Isnull(A.QntMin,0) QntMin ,a.Hrg1_1 , a.Hrg2_1, a.Hrg3_1
//                 from DBbarang a
//                 left OUter JOin DbSubgroup I on A.KodeSubGRp=I.KodeSUbgrp and A.KodeHdGrp=i.KodeHDGrp
//                 Left Outer join DbMerk J on A.KodeMerk=J.KodeMerk
//                 where a.isaktif=1 and A.KodeGrp in ('BJ','JS')
//                  and (A.KodeBrg like '%%') or (a.namaBrg like '%%')
//                 and isnull(A.Isaktif,0)=1
//                 order by a.Kodebrg ASC" );
$listGudang = DB::connection('SML')->select("select KODEGDG, NAMA , ALAMAT from dbgudang" );


$tempOutstanding4 = DB::connection("SML")->select("
Declare @bulan Int,@tahun Int,@IDuser varchar(20)
select @Bulan= :bulan ,@Tahun= :tahun ,@IDUser= :username
select 	A.NOBUKTI, B.URUT, B.KODEBRG, C.NamaBrg,
        case when B.NOSAT=1 Then B.QNT when B.NOSAT=2 Then B.QNT2 when B.Nosat=3 then B.qnt2  End Qnt,
        B.SATUAN,
		case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
		     when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
                     when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
		End QntOut,
    D.KodeCustSupp,D.NamaCustSupp,A.KodeGdg,A.Tanggal,
    A.Nobukti+cast(B.urut as varchar(3)) KeyNobukti,Isnull(A.TipePPN,0) TipePPN,
    A.NoResi,A.NoPolKend,A.Sopir,A.JumlahTagihan,A.Kodeexp,
    D.namaCustSupp+'('+A.KodeCust+')' CXcust ,  A.NOBUKTI+cast(B.URUT as varchar(3)) KeyUrut,
    M2.NAMA+'('+A.KODEKEBUN+')' xcKEBUN,isnull(A.DP,0) DP,
    M3.ALAMAT ,A.kodekebun,A.Nopesanan,C.PartNumber,M5.NamaMerk,A.TGLKIRIM DUEDATE,A.UserID ,A.RefPR ,

    case when ISNULL(B.NOserah,'')  IN ('','-')
    then
         case when b.nosat=1 then m6.saldoQnt
         when b.nosat=2 then m6.saldoqnt/c.isi2
         when b.nosat=3 then m6.saldoqnt/c.isi3 end
    else
         case when b.nosat=1 then (m6.saldoQnt) + (ISNULL(m61.saldoQnt,0))
         when b.nosat=2 then (m6.saldoqnt/c.isi2) + (m61.saldoqnt/c.isi2)
         when b.nosat=3 then (m6.saldoqnt/c.isi3) + (m61.saldoqnt/c.isi3) end
    End
    SaldoQnt ,A.tglKirim,A.TglKirim,ISnull(M7.Nama,'-') namakebun,

    case when
         case when ISNULL(B.NOserah,'') IN ('','-')
		 then
				case when b.nosat=1 then m6.saldoQnt
				when b.nosat=2 then m6.saldoqnt/c.isi2
				when b.nosat=3 then m6.saldoqnt/c.isi3 end
		else
				case when b.nosat=1 then (m6.saldoQnt) + (ISNULL(m61.saldoQnt,0))
				when b.nosat=2 then (m6.saldoqnt/c.isi2) + (m61.saldoqnt/c.isi2)
				when b.nosat=3 then (m6.saldoqnt/c.isi3) + (m61.saldoqnt/c.isi3) end
		End
         >
		case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
			when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
			when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
		End THEN
		        case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
			when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
			when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
		END
	ELSE
	     case when ISNULL(B.NOserah,'')  IN ('','-')
		 then
			case when b.nosat=1 then m6.saldoQnt
			when b.nosat=2 then m6.saldoqnt/c.isi2
			when b.nosat=3 then m6.saldoqnt/c.isi3 end
		else
			case when b.nosat=1 then (m6.saldoQnt) + (ISNULL(m61.saldoQnt,0))
			when b.nosat=2 then (m6.saldoqnt/c.isi2) + (m61.saldoqnt/c.isi2)
			when b.nosat=3 then (m6.saldoqnt/c.isi3) + (m61.saldoqnt/c.isi3) end
    End


    END QNTXZ

    ,Isnull(A.RefPR,0) RefPR,A.catatan ,ISnull(D.pBlackLIst,0) pBlackLIst
from	dbSO A
left outer join dBSODet B on B.NoBukti=A.NoBukti
left outer join dbBarang C on C.KodeBrg=B.KodeBrg
left outer join dbCustSupp D on D.KodeCustSupp=A.KODECUST
lEFT oUTER JOIN (SELECT NOSO,UrutSO,SUM(iSNULL(QNT,0)) QNT1SPB,SUM(ISNULL(QNT2,0)) QNT2SPB
				FROM dbSPBDet
				GROUP BY NoSO,UrutSO) M1 ON B.NOBUKTI=M1.NoSO AND B.URUT=M1.UrutSO
lEFT oUTER JOIN (SELECT b.NoSo,B.UrutSo,SUM(iSNULL(A.QNT,0)) QNT1RSPB,SUM(ISNULL(A.QNT2,0)) QNT2RSPB
				FROM dbRSPBDet A
				LEFT OUTER JOIN dbSPBDet B ON A.NoSPB=B.NoBukti AND A.UrutSPB=B.Urut
				GROUP BY b.NoSo,B.UrutSo) M4 ON B.NOBUKTI=M4.NoSO AND B.URUT=M4.UrutSO
left outer join dbkebuncustsupp m2 on A.KODEKEBUN=M2.KODEKEBUN AND A.KODECUST=M2.KODECUSTSUPP
LEFT OUTER JOIN DBALAMATCUST M3 ON A.NOALAMATKIRIM=M3.NOMOR   AND A.KODECUST=M3.KODECUSTSUPP
Left Outer Join Dbmerk M5 on C.KodeMerk=M5.kodeMerk
LEFT OUTER JOIN (select kodebrg, sum(SALDOQNT)SaldoQnt,BULAN,TAHUN  from DBSTOCKBRG
                 Where BUlan=@bulan and Tahun=@Tahun  and KOdeGdg<>'GTC'
                 and kodegdg in (Select kodegdg from dbPemakaigdg
                          where userid=@IDuser)
                 and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=0)
                 group by KODEBRG,BULAN,TAHUN
                 ) M6 ON B.KODEBRG=M6.KODEBRG

LEFT OUTER JOIN (select kodebrg, sum(SALDOQNT)SaldoQnt,BULAN,TAHUN  from DBSTOCKBRG
				  where BUlan=@bulan and Tahun=@Tahun
                        and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDUser)
					    and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=1)
				  group by KODEBRG,BULAN,TAHUN
				) M61 ON B.KODEBRG=M61.KODEBRG

LEFT OUTER JOIN DBKEBUNCUSTSUPP m7 on a.KODECUST=m7.KODECUSTSUPP and a.KODEKEBUN=m7.KODEKEBUN
where
      Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit)=0
and

/*B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)>0  */
case when b.NOSAT=1 then
			B.QNT-(Isnull(m1.QNT1SPB,0)+
			 case when b.nosat=1 then Isnull(B.QntBatal,0)
			 when b.nosat=2 then isnull(b.qntBatal,0) * B.isi
			 when b.nosat=3 then isnull(b.qntBatal,0) * B.isi end)
			+ Isnull(m4.QNT1RSPB,0)
	 when b.NOSAT=2 then
			B.QNT2-(Isnull(m1.QNT2SPB,0)+
			 case when b.nosat=1 then Isnull(B.QntBatal,0)/b.ISI
			 when b.nosat=2 then isnull(b.qntBatal,0)
			 when b.nosat=3 then isnull(b.qntBatal,0) end)
			+ Isnull(m4.QNT2RSPB,0)
	when b.NOSAT=3 then
			B.QNT2-(Isnull(m1.QNT2SPB,0)+
			 case when b.nosat=1 then Isnull(B.QntBatal,0)/b.ISI
			 when b.nosat=2 then isnull(b.qntBatal,0)
			 when b.nosat=3 then isnull(b.qntBatal,0) end)
			+ Isnull(m4.QNT2RSPB,0)
end>0
AND  case when ISNULL(B.NOserah,'')  IN ('','-') then isnull(M6.SALDOQNT,0)
		  Else isnull(M6.SALDOQNT,0) + isnull(M61.SALDOQNT,0) End
>0
and Isnull(B.PBooking,0)=1
" , ["bulan" => $periode->bulan , "tahun" =>$periode->tahun, "username" =>  \Auth::user()->username]);



$tempOutstanding5 = DB::connection("SML")->select("
Declare @bulan Int,@tahun Int,@IDuser varchar(20)
select @Bulan= :bulan ,@Tahun= :tahun ,@IDUser= :username
select 	A.NOBUKTI, B.URUT, B.KODEBRG, C.NamaBrg,
        case when B.NOSAT=1 Then B.QNT when B.NOSAT=2 Then B.QNT2 when B.Nosat=3 then B.qnt2  End Qnt,
        B.SATUAN,
		case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
		     when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
                     when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
		End QntOut,
    D.KodeCustSupp,D.NamaCustSupp,A.KodeGdg,A.Tanggal,
    A.Nobukti+cast(B.urut as varchar(3)) KeyNobukti,Isnull(A.TipePPN,0) TipePPN,
    A.NoResi,A.NoPolKend,A.Sopir,A.JumlahTagihan,A.Kodeexp,
    D.namaCustSupp+'('+A.KodeCust+')' CXcust ,  A.NOBUKTI+cast(B.URUT as varchar(3)) KeyUrut,
    M2.NAMA+'('+A.KODEKEBUN+')' xcKEBUN,isnull(A.DP,0) DP,
    M3.ALAMAT ,A.kodekebun,A.Nopesanan,C.PartNumber,M5.NamaMerk,A.TGLKIRIM DUEDATE,A.UserID ,A.RefPR ,

    case when ISNULL(B.NOserah,'')  IN ('','-')
    then
         case when b.nosat=1 then m6.saldoQnt
         when b.nosat=2 then m6.saldoqnt/c.isi2
         when b.nosat=3 then m6.saldoqnt/c.isi3 end
    else
         case when b.nosat=1 then (m6.saldoQnt) + (ISNULL(m61.saldoQnt,0))
         when b.nosat=2 then (m6.saldoqnt/c.isi2) + (m61.saldoqnt/c.isi2)
         when b.nosat=3 then (m6.saldoqnt/c.isi3) + (m61.saldoqnt/c.isi3) end
    End
    SaldoQnt ,A.tglKirim,A.TglKirim,ISnull(M7.Nama,'-') namakebun,

    case when
         case when ISNULL(B.NOserah,'') IN ('','-')
		 then
				case when b.nosat=1 then m6.saldoQnt
				when b.nosat=2 then m6.saldoqnt/c.isi2
				when b.nosat=3 then m6.saldoqnt/c.isi3 end
		else
				case when b.nosat=1 then (m6.saldoQnt) + (ISNULL(m61.saldoQnt,0))
				when b.nosat=2 then (m6.saldoqnt/c.isi2) + (m61.saldoqnt/c.isi2)
				when b.nosat=3 then (m6.saldoqnt/c.isi3) + (m61.saldoqnt/c.isi3) end
		End
         >
		case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
			when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
			when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
		End THEN
		        case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
			when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
			when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
		END
	ELSE
	     case when ISNULL(B.NOserah,'')  IN ('','-')
		 then
			case when b.nosat=1 then m6.saldoQnt
			when b.nosat=2 then m6.saldoqnt/c.isi2
			when b.nosat=3 then m6.saldoqnt/c.isi3 end
		else
			case when b.nosat=1 then (m6.saldoQnt) + (ISNULL(m61.saldoQnt,0))
			when b.nosat=2 then (m6.saldoqnt/c.isi2) + (m61.saldoqnt/c.isi2)
			when b.nosat=3 then (m6.saldoqnt/c.isi3) + (m61.saldoqnt/c.isi3) end
    End


    END QNTXZ

    ,Isnull(A.RefPR,0) RefPR,A.catatan ,ISnull(D.pBlackLIst,0) pBlackLIst
from	dbSO A
left outer join dBSODet B on B.NoBukti=A.NoBukti
left outer join dbBarang C on C.KodeBrg=B.KodeBrg
left outer join dbCustSupp D on D.KodeCustSupp=A.KODECUST
lEFT oUTER JOIN (SELECT NOSO,UrutSO,SUM(iSNULL(QNT,0)) QNT1SPB,SUM(ISNULL(QNT2,0)) QNT2SPB
				FROM dbSPBDet
				GROUP BY NoSO,UrutSO) M1 ON B.NOBUKTI=M1.NoSO AND B.URUT=M1.UrutSO
lEFT oUTER JOIN (SELECT b.NoSo,B.UrutSo,SUM(iSNULL(A.QNT,0)) QNT1RSPB,SUM(ISNULL(A.QNT2,0)) QNT2RSPB
				FROM dbRSPBDet A
				LEFT OUTER JOIN dbSPBDet B ON A.NoSPB=B.NoBukti AND A.UrutSPB=B.Urut
				GROUP BY b.NoSo,B.UrutSo) M4 ON B.NOBUKTI=M4.NoSO AND B.URUT=M4.UrutSO
left outer join dbkebuncustsupp m2 on A.KODEKEBUN=M2.KODEKEBUN AND A.KODECUST=M2.KODECUSTSUPP
LEFT OUTER JOIN DBALAMATCUST M3 ON A.NOALAMATKIRIM=M3.NOMOR   AND A.KODECUST=M3.KODECUSTSUPP
Left Outer Join Dbmerk M5 on C.KodeMerk=M5.kodeMerk
LEFT OUTER JOIN (select kodebrg, sum(SALDOQNT)SaldoQnt,BULAN,TAHUN  from DBSTOCKBRG
                 Where BUlan=@bulan and Tahun=@Tahun  and KOdeGdg<>'GTC'
                 and kodegdg in (Select kodegdg from dbPemakaigdg
                          where userid=@IDuser)
                 and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=0)
                 group by KODEBRG,BULAN,TAHUN
                 ) M6 ON B.KODEBRG=M6.KODEBRG

LEFT OUTER JOIN (select kodebrg, sum(SALDOQNT)SaldoQnt,BULAN,TAHUN  from DBSTOCKBRG
				  where BUlan=@bulan and Tahun=@Tahun
                        and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDUser)
					    and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=1)
				  group by KODEBRG,BULAN,TAHUN
				) M61 ON B.KODEBRG=M61.KODEBRG

LEFT OUTER JOIN DBKEBUNCUSTSUPP m7 on a.KODECUST=m7.KODECUSTSUPP and a.KODEKEBUN=m7.KODEKEBUN
where
      Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit)=0
and

/*B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)>0  */
case when b.NOSAT=1 then
			B.QNT-(Isnull(m1.QNT1SPB,0)+
			 case when b.nosat=1 then Isnull(B.QntBatal,0)
			 when b.nosat=2 then isnull(b.qntBatal,0) * B.isi
			 when b.nosat=3 then isnull(b.qntBatal,0) * B.isi end)
			+ Isnull(m4.QNT1RSPB,0)
	 when b.NOSAT=2 then
			B.QNT2-(Isnull(m1.QNT2SPB,0)+
			 case when b.nosat=1 then Isnull(B.QntBatal,0)/b.ISI
			 when b.nosat=2 then isnull(b.qntBatal,0)
			 when b.nosat=3 then isnull(b.qntBatal,0) end)
			+ Isnull(m4.QNT2RSPB,0)
	when b.NOSAT=3 then
			B.QNT2-(Isnull(m1.QNT2SPB,0)+
			 case when b.nosat=1 then Isnull(B.QntBatal,0)/b.ISI
			 when b.nosat=2 then isnull(b.qntBatal,0)
			 when b.nosat=3 then isnull(b.qntBatal,0) end)
			+ Isnull(m4.QNT2RSPB,0)
end>0
AND  case when ISNULL(B.NOserah,'')  IN ('','-') then isnull(M6.SALDOQNT,0)
		  Else isnull(M6.SALDOQNT,0) + isnull(M61.SALDOQNT,0) End
>0
and Isnull(B.PUrgent,0)=1






" , ["bulan" => $periode->bulan , "tahun" =>$periode->tahun, "username" =>  \Auth::user()->username]);


    return view('marketing.suratjalan' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "tempOutstanding" => $tempOutstanding,
      "tempOutstanding2" => $tempOutstanding2,
      "tempOutstanding3" => $tempOutstanding3,
      "tempOutstanding4" => $tempOutstanding4,
      "tempOutstanding6" => $tempOutstanding6,

      "tempOutstanding5" => $tempOutstanding5,
      "akses" => $akses,
      "listBarang" => $listBarang,
      "listGudang" => $listGudang
    ]);

  }

  public function loadAll () {

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $tempOutstanding = DB::connection("SML")->select("
Declare @bulan Int,@tahun Int,@IDuser varchar(20)
select @Bulan = :bulan,@Tahun = :tahun ,@IDUser = :username
DECLARE @TGL DATETIME
SET @TGL=GETDATE()
select 	A.NOBUKTI, B.URUT, B.KODEBRG, C.NamaBrg, A.NOBUKTI+cast(B.URUT as varchar(3)) KeyUrut,
        case when B.NOSAT=1 Then B.QNT when B.NOSAT=2 Then B.QNT2 when B.Nosat=3 then B.qnt2  End Qnt,
        B.SATUAN,
    case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
         when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
             when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
    End QntOut,
    D.KodeCustSupp,D.NamaCustSupp,A.KodeGdg,A.Tanggal,
    A.Nobukti+cast(B.urut as varchar(3)) KeyNobukti,Isnull(A.TipePPN,0) TipePPN,
    A.NoResi,A.NoPolKend,A.Sopir,A.JumlahTagihan,A.Kodeexp,
    D.namaCustSupp+'('+A.KodeCust+')' CXcust ,
    M2.NAMA+'('+A.KODEKEBUN+')' xcKEBUN,
    M3.ALAMAT ,A.kodekebun,A.Nopesanan,C.PartNumber,M5.NamaMerk,A.TGLKIRIM DUEDATE,A.UserID,

    case when ISNULL(B.NOserah,'')  IN ('','-')
    then
         case when b.nosat=1 then m62.saldoQnt
         when b.nosat=2 then m62.saldoqnt/c.isi2
         when b.nosat=3 then m62.saldoqnt/c.isi3 end
    else
         case when b.nosat=1 then (m62.saldoQnt) + (ISNULL(m63.saldoQnt,0))
         when b.nosat=2 then (m62.saldoqnt/c.isi2) + (m63.saldoqnt/c.isi2)
         when b.nosat=3 then (m62.saldoqnt/c.isi3) + (m63.saldoqnt/c.isi3) end
    End
    SaldoQnt,

    A.TglKirim,ISnull(M7.Nama,'-') namakebun,
    case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
     when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
         when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
  End -
  case when ISNULL(B.NOserah,'')  IN ('','-')
    then
         case when b.nosat=1 then m62.saldoQnt
         when b.nosat=2 then m62.saldoqnt/c.isi2
         when b.nosat=3 then m62.saldoqnt/c.isi3 end
    else
         case when b.nosat=1 then (m62.saldoQnt) + (ISNULL(m63.saldoQnt,0))
         when b.nosat=2 then (m62.saldoqnt/c.isi2) + (m63.saldoqnt/c.isi2)
         when b.nosat=3 then (m62.saldoqnt/c.isi3) + (m63.saldoqnt/c.isi3) end
    End QNTzx
    ,A.catatan   ,case when datediff(day,getdate(),A.TGLKIRIM)<5 then 'y' else 'n' end KetW
from	dbSO A
left outer join dBSODet B on B.NoBukti=A.NoBukti
left outer join dbBarang C on C.KodeBrg=B.KodeBrg
left outer join dbCustSupp D on D.KodeCustSupp=A.KODECUST
lEFT oUTER JOIN (SELECT NOSO,UrutSO,SUM(iSNULL(QNT,0)) QNT1SPB,SUM(ISNULL(QNT2,0)) QNT2SPB
        FROM dbSPBDet
        GROUP BY NoSO,UrutSO) M1 ON B.NOBUKTI=M1.NoSO AND B.URUT=M1.UrutSO
lEFT oUTER JOIN (SELECT b.NoSo,B.UrutSo,SUM(iSNULL(A.QNT,0)) QNT1RSPB,SUM(ISNULL(A.QNT2,0)) QNT2RSPB
        FROM dbRSPBDet A
        LEFT OUTER JOIN dbSPBDet B ON A.NoSPB=B.NoBukti AND A.UrutSPB=B.Urut
        GROUP BY b.NoSo,B.UrutSo) M4 ON B.NOBUKTI=M4.NoSO AND B.URUT=M4.UrutSO
left outer join dbkebuncustsupp m2 on A.KODEKEBUN=M2.KODEKEBUN AND A.KODECUST=M2.KODECUSTSUPP
LEFT OUTER JOIN DBALAMATCUST M3 ON A.NOALAMATKIRIM=M3.NOMOR   AND A.KODECUST=M3.KODECUSTSUPP
Left Outer Join Dbmerk M5 on C.KodeMerk=M5.kodeMerk
/*LEFT OUTER JOIN (select kodebrg, sum(SALDOQNT)SaldoQnt,BULAN,TAHUN  from DBSTOCKBRG  where kodegdg<>'GTC'
                            AND    BUlan=@bulan and Tahun=@Tahun
                             and kodegdg in (Select kodegdg from dbPemakaigdg
                          where userid=@IDUser)

                          and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=0)
        group by KODEBRG,BULAN,TAHUN
        ) M6 ON B.KODEBRG=M6.KODEBRG

LEFT OUTER JOIN (select kodebrg, sum(SALDOQNT)SaldoQnt,BULAN,TAHUN  from DBSTOCKBRG
          where BUlan=@bulan and Tahun=@Tahun
                        and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDUser)
              and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=1)
          group by KODEBRG,BULAN,TAHUN
        ) M61 ON B.KODEBRG=M61.KODEBRG*/
left outer join ( SELECT KODEBRG,SUM(SALDOQNT) SALDOQNT FROM (
          select  KodeBrg, QntAwal SaldoQnt
          from 	dbStockBrg
          where         Tahun=year(@TGL) and Bulan=MONTH(@TGL)
          --where         Tahun=year(GETDATE()) and Bulan=1
          and KodeGdg <>'GTC'
          and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDuser)
                  and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=0)
          union all
          select 	KodeBrg,
          SUM(QntDb)-SUM(QntCr) SaldoQnt
          from 	vwKartuStock
          where 	year(Tanggal)=@tahun and month(tanggal)=@bulan and Tanggal<=@TGL
          --where 	year(Tanggal)=year(GETDATE()) and Tanggal<=GETDATE()
          and Tipe not in ('AWL')
          and KodeGdg <>'GTC'
          and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDuser)
                  and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=0)
          group by KodeBrg) A
          GROUP BY A.KODEBRG
        )M62 ON B.KODEBRG=M62.KODEBRG

left outer join ( SELECT KODEBRG,SUM(SALDOQNT) SALDOQNT FROM (
            select KodeBrg, QntAwal SaldoQnt
          from 	dbStockBrg
          where         Tahun=@tahun and Bulan=@bulan
          --where         Tahun=@tahun and Bulan=1
          and KodeGdg <>'GTC'
          and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDuser)
                  and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=1)
          union all
          select 	KodeBrg,
          SUM(QntDb)-SUM(QntCr) SaldoQnt
          from 	vwKartuStock
          where 	year(Tanggal)=@tahun and month(tanggal)=@bulan and Tanggal<=@TGL
          --where 	year(Tanggal)=year(GETDATE()) and Tanggal<=@TGL
          and KodeGdg = Kodegdg and Tipe not in ('AWL')
          and KodeGdg <>'GTC'
          and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDUser)
          and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=1)
          group by KodeBrg) A
          GROUP BY A.KODEBRG
        )M63 ON B.KODEBRG=M63.KODEBRG

LEFT OUTER JOIN DBKEBUNCUSTSUPP m7 on a.KODECUST=m7.KODECUSTSUPP and a.KODEKEBUN=m7.KODEKEBUN
where
      Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit)=0
and B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)>0
and
case when ISNULL(B.NOserah,'')  IN ('','-')
    then
         case when b.nosat=1 then isnull(m62.saldoQnt,0)
         when b.nosat=2 then isnull(m62.saldoqnt,0)/c.isi2
         when b.nosat=3 then isnull(m62.saldoqnt,0)/c.isi3 end
    else
         case when b.nosat=1 then /*(m62.saldoQnt) +*/ (ISNULL(m63.saldoQnt,0))
         when b.nosat=2 then /*(m62.saldoqnt/c.isi2) +*/ (m63.saldoqnt/c.isi2)
         when b.nosat=3 then /*(m62.saldoqnt/c.isi3) + */(m63.saldoqnt/c.isi3) end
End

<
case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
   when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
     when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
End











order by A.NOBUKTI,B.Urut" , ["bulan" => $periode->bulan , "tahun" =>$periode->tahun , "username" =>  \Auth::user()->username]);
    // foreach ($outstanding as $p) {
    //   // code...
    //   array_push($tempOutstanding, $p);
    // }
    $tempOutstanding2 = DB::connection("SML")->select("
Declare @bulan Int,@tahun Int,@IDuser varchar(20)
select @Bulan= :bulan,@Tahun= :tahun ,@IDUser= :username

DECLARE @TGL DATETIME
SET @TGL=GETDATE()
select 	A.NOBUKTI, B.URUT, B.KODEBRG, C.NamaBrg,
        case when B.NOSAT=1 Then B.QNT when B.NOSAT=2 Then B.QNT2 when B.Nosat=3 then B.qnt2  End Qnt,
        B.SATUAN,
    case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
         when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
                     when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
    End QntOut,
    D.KodeCustSupp,D.NamaCustSupp,A.KodeGdg,A.Tanggal,
    A.Nobukti+cast(B.urut as varchar(3)) KeyNobukti,Isnull(A.TipePPN,0) TipePPN,
    A.NoResi,A.NoPolKend,A.Sopir,A.JumlahTagihan,A.Kodeexp,
    D.namaCustSupp+'('+A.KodeCust+')' CXcust ,  A.NOBUKTI+cast(B.URUT as varchar(3)) KeyUrut,
    M2.NAMA+'('+A.KODEKEBUN+')' xcKEBUN,isnull(A.DP,0) DP,
    M3.ALAMAT ,A.kodekebun,A.Nopesanan,C.PartNumber,M5.NamaMerk,A.TGLKIRIM DUEDATE,A.UserID ,A.RefPR ,
   A.tglKirim,A.TglKirim,ISnull(M7.Nama,'-') namakebun,Isnull(A.RefPR,0) RefPR,A.catatan ,ISnull(D.pBlackLIst,0) pBlackLIst,

    case when ISNULL(B.NOserah,'')  IN ('','-')
    then
         case when b.nosat=1 then m62.saldoQnt
         when b.nosat=2 then m62.saldoqnt/c.isi2
         when b.nosat=3 then m62.saldoqnt/c.isi3 end
    else
         case when b.nosat=1 then (m62.saldoQnt) + (ISNULL(m63.saldoQnt,0))
         when b.nosat=2 then (m62.saldoqnt/c.isi2) + (m63.saldoqnt/c.isi2)
         when b.nosat=3 then (m62.saldoqnt/c.isi3) + (m63.saldoqnt/c.isi3) end
    End
    SaldoQnt ,


    case when ISNULL(B.NOserah,'')  IN ('','-')
    then
         case when b.nosat=1 then    m62.saldoQnt
         when b.nosat=2 then m62.saldoqnt/c.isi2
         when b.nosat=3 then m62.saldoqnt/c.isi3 end
    else
         case when b.nosat=1 then (m62.saldoQnt) + (ISNULL(m63.saldoQnt,0))
         when b.nosat=2 then (m62.saldoqnt/c.isi2) + (m63.saldoqnt/c.isi2)
         when b.nosat=3 then (m62.saldoqnt/c.isi3) + (m63.saldoqnt/c.isi3) end
    End
    SaldoQnt ,

    case when ISNULL(B.NOserah,'')  IN ('','-')
    then
         case when b.nosat=1 then    m62.saldoQntG01
         when b.nosat=2 then m62.saldoqntG01/c.isi2
         when b.nosat=3 then m62.saldoqntG01/c.isi3 end
    else
         case when b.nosat=1 then (m62.SALDOQNtg01) + (ISNULL(m63.SALDOQNtg01,0))
         when b.nosat=2 then (m62.SALDOQNtg01/c.isi2) + (m63.SALDOQNtg01/c.isi2)
         when b.nosat=3 then (m62.SALDOQNtg01/c.isi3) + (m63.SALDOQNtg01/c.isi3) end
    End
    SALDOQNtg01 ,

     case when ISNULL(B.NOserah,'')  IN ('','-')
    then
         case when b.nosat=1 then    m62.saldoQntG02
         when b.nosat=2 then m62.saldoqntG02/c.isi2
         when b.nosat=3 then m62.saldoqntG02/c.isi3 end
    else
         case when b.nosat=1 then (m62.SALDOQNtg02) + (ISNULL(m63.SALDOQNtg02,0))
         when b.nosat=2 then (m62.SALDOQNtg02/c.isi2) + (m63.SALDOQNtg02/c.isi2)
         when b.nosat=3 then (m62.SALDOQNtg02/c.isi3) + (m63.SALDOQNtg02/c.isi3) end
    End
    SALDOQNtg02 ,

     case when ISNULL(B.NOserah,'')  IN ('','-')
    then
         case when b.nosat=1 then    m62.saldoQntG03
         when b.nosat=2 then m62.saldoqntG03/c.isi2
         when b.nosat=3 then m62.saldoqntG03/c.isi3 end
    else
         case when b.nosat=1 then (m62.SALDOQNtg03) + (ISNULL(m63.SALDOQNtg03,0))
         when b.nosat=2 then (m62.SALDOQNtg03/c.isi2) + (m63.SALDOQNtg03/c.isi2)
         when b.nosat=3 then (m62.SALDOQNtg03/c.isi3) + (m63.SALDOQNtg03/c.isi3) end
    End
    SALDOQNtg03 ,


    case when
         case when ISNULL(B.NOserah,'')  IN ('','-')
     then
        case when b.nosat=1 then m62.saldoQnt
        when b.nosat=2 then m62.saldoqnt/c.isi2
        when b.nosat=3 then m62.saldoqnt/c.isi3 end
    else
        case when b.nosat=1 then (m62.saldoQnt) + (ISNULL(m63.saldoQnt,0))
        when b.nosat=2 then (m62.saldoqnt/c.isi2) + (m63.saldoqnt/c.isi2)
        when b.nosat=3 then (m62.saldoqnt/c.isi3) + (m63.saldoqnt/c.isi3) end
    End
         >
    case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
      when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
      when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
    End THEN
            case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
      when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
      when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
    END
  ELSE
       case when ISNULL(B.NOserah,'')  IN ('','-')
     then
      case when b.nosat=1 then m62.saldoQnt
      when b.nosat=2 then m62.saldoqnt/c.isi2
      when b.nosat=3 then m62.saldoqnt/c.isi3 end
    else
      case when b.nosat=1 then (m62.saldoQnt) + (ISNULL(m63.saldoQnt,0))
      when b.nosat=2 then (m62.saldoqnt/c.isi2) + (m63.saldoqnt/c.isi2)
      when b.nosat=3 then (m62.saldoqnt/c.isi3) + (m63.saldoqnt/c.isi3) end
    End
    END QNTXZ   ,case when datediff(day,getdate(),A.TGLKIRIM)<5 then 'y' else 'n' end KetW
    ,ISNULL(B.NOserah,'') NOSERAH ,m2.KodeKebun,m2.Nama AlamatLokasi
from	dbSO A
left outer join dBSODet B on B.NoBukti=A.NoBukti
left outer join dbBarang C on C.KodeBrg=B.KodeBrg
left outer join dbCustSupp D on D.KodeCustSupp=A.KODECUST
lEFT oUTER JOIN (SELECT NOSO,UrutSO,SUM(iSNULL(QNT,0)) QNT1SPB,SUM(ISNULL(QNT2,0)) QNT2SPB
        FROM dbSPBDet
        GROUP BY NoSO,UrutSO) M1 ON B.NOBUKTI=M1.NoSO AND B.URUT=M1.UrutSO
lEFT oUTER JOIN (SELECT b.NoSo,B.UrutSo,SUM(iSNULL(A.QNT,0)) QNT1RSPB,SUM(ISNULL(A.QNT2,0)) QNT2RSPB
        FROM dbRSPBDet A
        LEFT OUTER JOIN dbSPBDet B ON A.NoSPB=B.NoBukti AND A.UrutSPB=B.Urut
        GROUP BY b.NoSo,B.UrutSo) M4 ON B.NOBUKTI=M4.NoSO AND B.URUT=M4.UrutSO
left outer join dbkebuncustsupp m2 on A.KODEKEBUN=M2.KODEKEBUN AND A.KODECUST=M2.KODECUSTSUPP
LEFT OUTER JOIN DBALAMATCUST M3 ON A.NOALAMATKIRIM=M3.NOMOR   AND A.KODECUST=M3.KODECUSTSUPP
Left Outer Join Dbmerk M5 on C.KodeMerk=M5.kodeMerk
/*
LEFT OUTER JOIN (select kodebrg, sum(SALDOQNT)SaldoQnt,BULAN,TAHUN  from DBSTOCKBRG
                 Where BUlan=@bulan and Tahun=@Tahun  and KOdeGdg<>'GTC'
                 and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDuser)
                 and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=0)
                 group by KODEBRG,BULAN,TAHUN
                 ) M6 ON B.KODEBRG=M6.KODEBRG

LEFT OUTER JOIN (select kodebrg, sum(SALDOQNT)SaldoQnt,BULAN,TAHUN  from DBSTOCKBRG
          where BUlan=@bulan and Tahun=@Tahun
                  and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDUser)
          and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=1)
          group by KODEBRG,BULAN,TAHUN
        ) M61 ON B.KODEBRG=M61.KODEBRG
*/

left outer join ( SELECT KODEBRG,SUM(SALDOQNT) SALDOQNT,SUM(SALDOQNTG01) SALDOQNtg01,SUM(SALDOQNTG02) SALDOQNtg02,SUM(SALDOQNTG03) SALDOQNtg03
                FROM (
          select  KodeBrg, QntAwal SaldoQnt ,
                          CASE WHEN KODEGDG='G01' THEN QntAwal ELSE 0 END SALDOQNTG01,
                          CASE WHEN KODEGDG='G02' THEN QntAwal ELSE 0 END SALDOQNTG02,
                          CASE WHEN KODEGDG='G03' THEN QntAwal ELSE 0 END SALDOQNTG03
                  from 	dbStockBrg
          where         Tahun=year(@TGL) and Bulan=MONTH(@TGL)
          --where         Tahun=year(GETDATE()) and Bulan=1
          and KodeGdg <>'GTC'
          and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDuser)
                  and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=0)



          union all
          select 	KodeBrg,
          SUM(QntDb)-SUM(QntCr) SaldoQnt,
          SUM(case when Kodegdg='G01' then QntDb else 0 end)-SUM(case when Kodegdg='G01' then QntCr else 0 end) SaldoQntG01,
          SUM(case when Kodegdg='G02' then QntDb else 0 end)-SUM(case when Kodegdg='G02' then QntCr else 0 end) SaldoQntG02,
          SUM(case when Kodegdg='G03' then QntDb else 0 end)-SUM(case when Kodegdg='G03' then QntCr else 0 end) SaldoQntG03
          from 	vwKartuStock
          where 	year(Tanggal)=@tahun and month(tanggal)=@bulan and Tanggal<=@TGL
          --where 	year(Tanggal)=year(GETDATE()) and Tanggal<=GETDATE()
          and Tipe not in ('AWL')
          and KodeGdg <>'GTC'
          and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDuser)

                  and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=0)
          group by KodeBrg) A
          GROUP BY A.KODEBRG
        )M62 ON B.KODEBRG=M62.KODEBRG

left outer join ( SELECT KODEBRG,SUM(SALDOQNT) SALDOQNT ,SUM(SALDOQNTG01) SALDOQNtg01,SUM(SALDOQNTG02) SALDOQNtg02,SUM(SALDOQNTG03) SALDOQNtg03
            FROM (
            select KodeBrg, QntAwal SaldoQnt ,
            CASE WHEN KODEGDG='G01' THEN QntAwal ELSE 0 END SALDOQNTG01,
                          CASE WHEN KODEGDG='G02' THEN QntAwal ELSE 0 END SALDOQNTG02,
                          CASE WHEN KODEGDG='G03' THEN QntAwal ELSE 0 END SALDOQNTG03
          from 	dbStockBrg
          where         Tahun=@tahun and Bulan=@bulan
          --where         Tahun=@tahun and Bulan=1
          and KodeGdg <>'GTC'
          and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDuser)
                  and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=1)
          union all
          select 	KodeBrg,
          SUM(QntDb)-SUM(QntCr) SaldoQnt,
          SUM(case when Kodegdg='G01' then QntDb else 0 end)-SUM(case when Kodegdg='G01' then QntCr else 0 end) SaldoQntG01,
          SUM(case when Kodegdg='G02' then QntDb else 0 end)-SUM(case when Kodegdg='G02' then QntCr else 0 end) SaldoQntG02,
          SUM(case when Kodegdg='G03' then QntDb else 0 end)-SUM(case when Kodegdg='G03' then QntCr else 0 end) SaldoQntG03
          from 	vwKartuStock
          where 	year(Tanggal)=@tahun and month(tanggal)=@bulan and Tanggal<=@TGL
          --where 	year(Tanggal)=year(GETDATE()) and Tanggal<=@TGL
          and KodeGdg = Kodegdg and Tipe not in ('AWL')
          and KodeGdg <>'GTC'
          and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDUser)
          and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=1)
          group by KodeBrg) A
          GROUP BY A.KODEBRG
        )M63 ON B.KODEBRG=M63.KODEBRG


LEFT OUTER JOIN DBKEBUNCUSTSUPP m7 on a.KODECUST=m7.KODECUSTSUPP and a.KODEKEBUN=m7.KODEKEBUN
where
      Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit)=0
and

/*B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)>0  */
case when b.NOSAT=1 then
      B.QNT-(Isnull(m1.QNT1SPB,0)+
       case when b.nosat=1 then Isnull(B.QntBatal,0)
       when b.nosat=2 then isnull(b.qntBatal,0) * B.isi
       when b.nosat=3 then isnull(b.qntBatal,0) * B.isi end)
      + Isnull(m4.QNT1RSPB,0)
   when b.NOSAT=2 then
      B.QNT2-(Isnull(m1.QNT2SPB,0)+
       case when b.nosat=1 then Isnull(B.QntBatal,0)/b.ISI
       when b.nosat=2 then isnull(b.qntBatal,0)
       when b.nosat=3 then isnull(b.qntBatal,0) end)
      + Isnull(m4.QNT2RSPB,0)
  when b.NOSAT=3 then
      B.QNT2-(Isnull(m1.QNT2SPB,0)+
       case when b.nosat=1 then Isnull(B.QntBatal,0)/b.ISI
       when b.nosat=2 then isnull(b.qntBatal,0)
       when b.nosat=3 then isnull(b.qntBatal,0) end)
      + Isnull(m4.QNT2RSPB,0)
end>0
AND  case when ISNULL(B.NOserah,'')  IN ('','-') then isnull(M62.SALDOQNT,0)
      Else   isnull(M63.SALDOQNT,0) End
>0


" , ["bulan" => $periode->bulan , "tahun" =>$periode->tahun, "username" =>  \Auth::user()->username]);


$tempOutstanding3 = DB::connection("SML")->select("
declare @Tahun int, @Bulan int

select @Tahun= :tahun , @Bulan= :bulan


select  A.NOBUKTI, A.NOURUT, A.TANGGAL, A.NOSPP, A.KODECUSTSUPP, M1.NamaCustSupp,
        A.NoPolKend, A.Container, A.NoContainer, A.NoSeal, A.Catatan, A.IDUser, A.IsFlag Tipe,
        E.Nobukti Noso, D.NoBukti NoSPP,
        Case when A.isFlag=0 then 'SPB Barang Jadi'
             when A.isFlag=1 then 'SPB Bahan Baku dan Lain-lain'
             else ''
        end MyTipe,
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
        A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
        A.IsOtorisasi3, A.OtoUser3, A.TglOto3,
        A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
        A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
        Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                       Case when A.IsOtorisasi3=1 then 1 else 0 end+
                       Case when A.IsOtorisasi4=1 then 1 else 0 end+
                       Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                  else 1
             end As Bit) NeedOtorisasi
        ,Isnull(A.Isbatal,0) Isbatal,A.Userbatal,A.Tglbatal,F.NoPesanan ,
        ISnull(M7.Nama,'-') namakebun,A.RefUKM,(select top 1 NOSPB from DBRSPBDet where NOSPB=a.NoBukti)NOSPB,
        case when year(A.TGLKIRIM)=1899 then null else A.TGLKIRIM END TGLKIRIM,
        CASE WHEN YEAR(A.TGLTERIMA)=1899 THEN NULL ELSE A.TGLTERIMA END TGLTERIMA,
        ISnull(A.CetakKe,0) CetakKe,case when year(A.TglSPBINVC)=1899 then null else A.TglSPBINVC END TglSPBINVC
        ,case when year(A.TglTerimaBRG)=1899 then null else A.TglTerimaBRG END TglTerimaBRG
from	dbSPB A
Left Outer join (Select nobukti, nospp
                 from dbSPBDet
                 Group by nobukti, nospp) C on C.NoBukti=A.NoBukti
Left Outer join (Select nobukti, NoSO
                 from dbSPPDet
                 Group by nobukti, NoSO) D on D.NoBukti=C.NoSPP
Left Outer join (select NObukti,NOso from DBSPBDET group by Nobukti,NOso) E on A.Nobukti=E.NoBukti
Left Outer JOin DBSO F on E.Noso=F.Nobukti
left outer join vwBrowsCustomer B on B.KodeCust=A.KodeCustSupp and B.Sales=F.KODESLS
Left OUter Join DbCustSupp M1 On A.kodeCustSupp=m1.KodeCustSupp
LEFT OUTER JOIN DBKEBUNCUSTSUPP m7 on a.KODECUSTsupp=m7.KODECUSTSUPP and a.KODEKEBUN=m7.KODEKEBUN
LEFT OUTER JOIN DBRSPB M8 ON A.NOBUKTI=M8.NOSPB
where	year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan  and A.Nobukti not like '%POS%'
and isnull(A.IsOtorisasi1 , 0) = 0
/*and (C.kodegdg in (Select kodegdg from dbPemakaigdg
                          where userid=:0 ) or ISNULL(A.ISBATAL,0)=1) */




order by A.NoBukti
" , [ "tahun" =>$periode->tahun, "bulan" => $periode->bulan ]);


$tempOutstanding6 = DB::connection("SML")->select("
declare @Tahun int, @Bulan int

select @Tahun= :tahun , @Bulan= :bulan


select  A.NOBUKTI, A.NOURUT, A.TANGGAL, A.NOSPP, A.KODECUSTSUPP, M1.NamaCustSupp,
        A.NoPolKend, A.Container, A.NoContainer, A.NoSeal, A.Catatan, A.IDUser, A.IsFlag Tipe,
        E.Nobukti Noso, D.NoBukti NoSPP,
        Case when A.isFlag=0 then 'SPB Barang Jadi'
             when A.isFlag=1 then 'SPB Bahan Baku dan Lain-lain'
             else ''
        end MyTipe,
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
        A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
        A.IsOtorisasi3, A.OtoUser3, A.TglOto3,
        A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
        A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
        Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                       Case when A.IsOtorisasi3=1 then 1 else 0 end+
                       Case when A.IsOtorisasi4=1 then 1 else 0 end+
                       Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                  else 1
             end As Bit) NeedOtorisasi
        ,Isnull(A.Isbatal,0) Isbatal,A.Userbatal,A.Tglbatal,F.NoPesanan ,
        ISnull(M7.Nama,'-') namakebun,A.RefUKM,(select top 1 NOSPB from DBRSPBDet where NOSPB=a.NoBukti)NOSPB,
        case when year(A.TGLKIRIM)=1899 then null else A.TGLKIRIM END TGLKIRIM,
        CASE WHEN YEAR(A.TGLTERIMA)=1899 THEN NULL ELSE A.TGLTERIMA END TGLTERIMA,
        ISnull(A.CetakKe,0) CetakKe,case when year(A.TglSPBINVC)=1899 then null else A.TglSPBINVC END TglSPBINVC
        ,case when year(A.TglTerimaBRG)=1899 then null else A.TglTerimaBRG END TglTerimaBRG
from	dbSPB A
Left Outer join (Select nobukti, nospp
                 from dbSPBDet
                 Group by nobukti, nospp) C on C.NoBukti=A.NoBukti
Left Outer join (Select nobukti, NoSO
                 from dbSPPDet
                 Group by nobukti, NoSO) D on D.NoBukti=C.NoSPP
Left Outer join (select NObukti,NOso from DBSPBDET group by Nobukti,NOso) E on A.Nobukti=E.NoBukti
Left Outer JOin DBSO F on E.Noso=F.Nobukti
left outer join vwBrowsCustomer B on B.KodeCust=A.KodeCustSupp and B.Sales=F.KODESLS
Left OUter Join DbCustSupp M1 On A.kodeCustSupp=m1.KodeCustSupp
LEFT OUTER JOIN DBKEBUNCUSTSUPP m7 on a.KODECUSTsupp=m7.KODECUSTSUPP and a.KODEKEBUN=m7.KODEKEBUN
LEFT OUTER JOIN DBRSPB M8 ON A.NOBUKTI=M8.NOSPB
where	year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan  and A.Nobukti not like '%POS%'
  and isnull(A.IsOtorisasi1 , 0) <> 0
/*and (C.kodegdg in (Select kodegdg from dbPemakaigdg
                          where userid=:0 ) or ISNULL(A.ISBATAL,0)=1) */




order by A.NoBukti
" , [ "tahun" =>$periode->tahun, "bulan" => $periode->bulan ]);


$tempOutstanding4 = DB::connection("SML")->select("
Declare @bulan Int,@tahun Int,@IDuser varchar(20)
select @Bulan= :bulan ,@Tahun= :tahun ,@IDUser= :username
select 	A.NOBUKTI, B.URUT, B.KODEBRG, C.NamaBrg,
        case when B.NOSAT=1 Then B.QNT when B.NOSAT=2 Then B.QNT2 when B.Nosat=3 then B.qnt2  End Qnt,
        B.SATUAN,
		case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
		     when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
                     when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
		End QntOut,
    D.KodeCustSupp,D.NamaCustSupp,A.KodeGdg,A.Tanggal,
    A.Nobukti+cast(B.urut as varchar(3)) KeyNobukti,Isnull(A.TipePPN,0) TipePPN,
    A.NoResi,A.NoPolKend,A.Sopir,A.JumlahTagihan,A.Kodeexp,
    D.namaCustSupp+'('+A.KodeCust+')' CXcust ,  A.NOBUKTI+cast(B.URUT as varchar(3)) KeyUrut,
    M2.NAMA+'('+A.KODEKEBUN+')' xcKEBUN,isnull(A.DP,0) DP,
    M3.ALAMAT ,A.kodekebun,A.Nopesanan,C.PartNumber,M5.NamaMerk,A.TGLKIRIM DUEDATE,A.UserID ,A.RefPR ,

    case when ISNULL(B.NOserah,'')  IN ('','-')
    then
         case when b.nosat=1 then m6.saldoQnt
         when b.nosat=2 then m6.saldoqnt/c.isi2
         when b.nosat=3 then m6.saldoqnt/c.isi3 end
    else
         case when b.nosat=1 then (m6.saldoQnt) + (ISNULL(m61.saldoQnt,0))
         when b.nosat=2 then (m6.saldoqnt/c.isi2) + (m61.saldoqnt/c.isi2)
         when b.nosat=3 then (m6.saldoqnt/c.isi3) + (m61.saldoqnt/c.isi3) end
    End
    SaldoQnt ,A.tglKirim,A.TglKirim,ISnull(M7.Nama,'-') namakebun,

    case when
         case when ISNULL(B.NOserah,'') IN ('','-')
		 then
				case when b.nosat=1 then m6.saldoQnt
				when b.nosat=2 then m6.saldoqnt/c.isi2
				when b.nosat=3 then m6.saldoqnt/c.isi3 end
		else
				case when b.nosat=1 then (m6.saldoQnt) + (ISNULL(m61.saldoQnt,0))
				when b.nosat=2 then (m6.saldoqnt/c.isi2) + (m61.saldoqnt/c.isi2)
				when b.nosat=3 then (m6.saldoqnt/c.isi3) + (m61.saldoqnt/c.isi3) end
		End
         >
		case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
			when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
			when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
		End THEN
		        case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
			when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
			when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
		END
	ELSE
	     case when ISNULL(B.NOserah,'')  IN ('','-')
		 then
			case when b.nosat=1 then m6.saldoQnt
			when b.nosat=2 then m6.saldoqnt/c.isi2
			when b.nosat=3 then m6.saldoqnt/c.isi3 end
		else
			case when b.nosat=1 then (m6.saldoQnt) + (ISNULL(m61.saldoQnt,0))
			when b.nosat=2 then (m6.saldoqnt/c.isi2) + (m61.saldoqnt/c.isi2)
			when b.nosat=3 then (m6.saldoqnt/c.isi3) + (m61.saldoqnt/c.isi3) end
    End


    END QNTXZ

    ,Isnull(A.RefPR,0) RefPR,A.catatan ,ISnull(D.pBlackLIst,0) pBlackLIst
from	dbSO A
left outer join dBSODet B on B.NoBukti=A.NoBukti
left outer join dbBarang C on C.KodeBrg=B.KodeBrg
left outer join dbCustSupp D on D.KodeCustSupp=A.KODECUST
lEFT oUTER JOIN (SELECT NOSO,UrutSO,SUM(iSNULL(QNT,0)) QNT1SPB,SUM(ISNULL(QNT2,0)) QNT2SPB
				FROM dbSPBDet
				GROUP BY NoSO,UrutSO) M1 ON B.NOBUKTI=M1.NoSO AND B.URUT=M1.UrutSO
lEFT oUTER JOIN (SELECT b.NoSo,B.UrutSo,SUM(iSNULL(A.QNT,0)) QNT1RSPB,SUM(ISNULL(A.QNT2,0)) QNT2RSPB
				FROM dbRSPBDet A
				LEFT OUTER JOIN dbSPBDet B ON A.NoSPB=B.NoBukti AND A.UrutSPB=B.Urut
				GROUP BY b.NoSo,B.UrutSo) M4 ON B.NOBUKTI=M4.NoSO AND B.URUT=M4.UrutSO
left outer join dbkebuncustsupp m2 on A.KODEKEBUN=M2.KODEKEBUN AND A.KODECUST=M2.KODECUSTSUPP
LEFT OUTER JOIN DBALAMATCUST M3 ON A.NOALAMATKIRIM=M3.NOMOR   AND A.KODECUST=M3.KODECUSTSUPP
Left Outer Join Dbmerk M5 on C.KodeMerk=M5.kodeMerk
LEFT OUTER JOIN (select kodebrg, sum(SALDOQNT)SaldoQnt,BULAN,TAHUN  from DBSTOCKBRG
                 Where BUlan=@bulan and Tahun=@Tahun  and KOdeGdg<>'GTC'
                 and kodegdg in (Select kodegdg from dbPemakaigdg
                          where userid=@IDuser)
                 and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=0)
                 group by KODEBRG,BULAN,TAHUN
                 ) M6 ON B.KODEBRG=M6.KODEBRG

LEFT OUTER JOIN (select kodebrg, sum(SALDOQNT)SaldoQnt,BULAN,TAHUN  from DBSTOCKBRG
				  where BUlan=@bulan and Tahun=@Tahun
                        and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDUser)
					    and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=1)
				  group by KODEBRG,BULAN,TAHUN
				) M61 ON B.KODEBRG=M61.KODEBRG

LEFT OUTER JOIN DBKEBUNCUSTSUPP m7 on a.KODECUST=m7.KODECUSTSUPP and a.KODEKEBUN=m7.KODEKEBUN
where
      Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit)=0
and

/*B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)>0  */
case when b.NOSAT=1 then
			B.QNT-(Isnull(m1.QNT1SPB,0)+
			 case when b.nosat=1 then Isnull(B.QntBatal,0)
			 when b.nosat=2 then isnull(b.qntBatal,0) * B.isi
			 when b.nosat=3 then isnull(b.qntBatal,0) * B.isi end)
			+ Isnull(m4.QNT1RSPB,0)
	 when b.NOSAT=2 then
			B.QNT2-(Isnull(m1.QNT2SPB,0)+
			 case when b.nosat=1 then Isnull(B.QntBatal,0)/b.ISI
			 when b.nosat=2 then isnull(b.qntBatal,0)
			 when b.nosat=3 then isnull(b.qntBatal,0) end)
			+ Isnull(m4.QNT2RSPB,0)
	when b.NOSAT=3 then
			B.QNT2-(Isnull(m1.QNT2SPB,0)+
			 case when b.nosat=1 then Isnull(B.QntBatal,0)/b.ISI
			 when b.nosat=2 then isnull(b.qntBatal,0)
			 when b.nosat=3 then isnull(b.qntBatal,0) end)
			+ Isnull(m4.QNT2RSPB,0)
end>0
AND  case when ISNULL(B.NOserah,'')  IN ('','-') then isnull(M6.SALDOQNT,0)
		  Else isnull(M6.SALDOQNT,0) + isnull(M61.SALDOQNT,0) End
>0
and Isnull(B.PBooking,0)=1
" , ["bulan" => $periode->bulan , "tahun" =>$periode->tahun, "username" =>  \Auth::user()->username]);



$tempOutstanding5 = DB::connection("SML")->select("
Declare @bulan Int,@tahun Int,@IDuser varchar(20)
select @Bulan= :bulan ,@Tahun= :tahun ,@IDUser= :username
select 	A.NOBUKTI, B.URUT, B.KODEBRG, C.NamaBrg,
        case when B.NOSAT=1 Then B.QNT when B.NOSAT=2 Then B.QNT2 when B.Nosat=3 then B.qnt2  End Qnt,
        B.SATUAN,
		case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
		     when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
                     when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
		End QntOut,
    D.KodeCustSupp,D.NamaCustSupp,A.KodeGdg,A.Tanggal,
    A.Nobukti+cast(B.urut as varchar(3)) KeyNobukti,Isnull(A.TipePPN,0) TipePPN,
    A.NoResi,A.NoPolKend,A.Sopir,A.JumlahTagihan,A.Kodeexp,
    D.namaCustSupp+'('+A.KodeCust+')' CXcust ,  A.NOBUKTI+cast(B.URUT as varchar(3)) KeyUrut,
    M2.NAMA+'('+A.KODEKEBUN+')' xcKEBUN,isnull(A.DP,0) DP,
    M3.ALAMAT ,A.kodekebun,A.Nopesanan,C.PartNumber,M5.NamaMerk,A.TGLKIRIM DUEDATE,A.UserID ,A.RefPR ,

    case when ISNULL(B.NOserah,'')  IN ('','-')
    then
         case when b.nosat=1 then m6.saldoQnt
         when b.nosat=2 then m6.saldoqnt/c.isi2
         when b.nosat=3 then m6.saldoqnt/c.isi3 end
    else
         case when b.nosat=1 then (m6.saldoQnt) + (ISNULL(m61.saldoQnt,0))
         when b.nosat=2 then (m6.saldoqnt/c.isi2) + (m61.saldoqnt/c.isi2)
         when b.nosat=3 then (m6.saldoqnt/c.isi3) + (m61.saldoqnt/c.isi3) end
    End
    SaldoQnt ,A.tglKirim,A.TglKirim,ISnull(M7.Nama,'-') namakebun,

    case when
         case when ISNULL(B.NOserah,'') IN ('','-')
		 then
				case when b.nosat=1 then m6.saldoQnt
				when b.nosat=2 then m6.saldoqnt/c.isi2
				when b.nosat=3 then m6.saldoqnt/c.isi3 end
		else
				case when b.nosat=1 then (m6.saldoQnt) + (ISNULL(m61.saldoQnt,0))
				when b.nosat=2 then (m6.saldoqnt/c.isi2) + (m61.saldoqnt/c.isi2)
				when b.nosat=3 then (m6.saldoqnt/c.isi3) + (m61.saldoqnt/c.isi3) end
		End
         >
		case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
			when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
			when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
		End THEN
		        case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
			when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
			when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
		END
	ELSE
	     case when ISNULL(B.NOserah,'')  IN ('','-')
		 then
			case when b.nosat=1 then m6.saldoQnt
			when b.nosat=2 then m6.saldoqnt/c.isi2
			when b.nosat=3 then m6.saldoqnt/c.isi3 end
		else
			case when b.nosat=1 then (m6.saldoQnt) + (ISNULL(m61.saldoQnt,0))
			when b.nosat=2 then (m6.saldoqnt/c.isi2) + (m61.saldoqnt/c.isi2)
			when b.nosat=3 then (m6.saldoqnt/c.isi3) + (m61.saldoqnt/c.isi3) end
    End


    END QNTXZ

    ,Isnull(A.RefPR,0) RefPR,A.catatan ,ISnull(D.pBlackLIst,0) pBlackLIst
from	dbSO A
left outer join dBSODet B on B.NoBukti=A.NoBukti
left outer join dbBarang C on C.KodeBrg=B.KodeBrg
left outer join dbCustSupp D on D.KodeCustSupp=A.KODECUST
lEFT oUTER JOIN (SELECT NOSO,UrutSO,SUM(iSNULL(QNT,0)) QNT1SPB,SUM(ISNULL(QNT2,0)) QNT2SPB
				FROM dbSPBDet
				GROUP BY NoSO,UrutSO) M1 ON B.NOBUKTI=M1.NoSO AND B.URUT=M1.UrutSO
lEFT oUTER JOIN (SELECT b.NoSo,B.UrutSo,SUM(iSNULL(A.QNT,0)) QNT1RSPB,SUM(ISNULL(A.QNT2,0)) QNT2RSPB
				FROM dbRSPBDet A
				LEFT OUTER JOIN dbSPBDet B ON A.NoSPB=B.NoBukti AND A.UrutSPB=B.Urut
				GROUP BY b.NoSo,B.UrutSo) M4 ON B.NOBUKTI=M4.NoSO AND B.URUT=M4.UrutSO
left outer join dbkebuncustsupp m2 on A.KODEKEBUN=M2.KODEKEBUN AND A.KODECUST=M2.KODECUSTSUPP
LEFT OUTER JOIN DBALAMATCUST M3 ON A.NOALAMATKIRIM=M3.NOMOR   AND A.KODECUST=M3.KODECUSTSUPP
Left Outer Join Dbmerk M5 on C.KodeMerk=M5.kodeMerk
LEFT OUTER JOIN (select kodebrg, sum(SALDOQNT)SaldoQnt,BULAN,TAHUN  from DBSTOCKBRG
                 Where BUlan=@bulan and Tahun=@Tahun  and KOdeGdg<>'GTC'
                 and kodegdg in (Select kodegdg from dbPemakaigdg
                          where userid=@IDuser)
                 and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=0)
                 group by KODEBRG,BULAN,TAHUN
                 ) M6 ON B.KODEBRG=M6.KODEBRG

LEFT OUTER JOIN (select kodebrg, sum(SALDOQNT)SaldoQnt,BULAN,TAHUN  from DBSTOCKBRG
				  where BUlan=@bulan and Tahun=@Tahun
                        and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDUser)
					    and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=1)
				  group by KODEBRG,BULAN,TAHUN
				) M61 ON B.KODEBRG=M61.KODEBRG

LEFT OUTER JOIN DBKEBUNCUSTSUPP m7 on a.KODECUST=m7.KODECUSTSUPP and a.KODEKEBUN=m7.KODEKEBUN
where
      Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit)=0
and

/*B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)>0  */
case when b.NOSAT=1 then
			B.QNT-(Isnull(m1.QNT1SPB,0)+
			 case when b.nosat=1 then Isnull(B.QntBatal,0)
			 when b.nosat=2 then isnull(b.qntBatal,0) * B.isi
			 when b.nosat=3 then isnull(b.qntBatal,0) * B.isi end)
			+ Isnull(m4.QNT1RSPB,0)
	 when b.NOSAT=2 then
			B.QNT2-(Isnull(m1.QNT2SPB,0)+
			 case when b.nosat=1 then Isnull(B.QntBatal,0)/b.ISI
			 when b.nosat=2 then isnull(b.qntBatal,0)
			 when b.nosat=3 then isnull(b.qntBatal,0) end)
			+ Isnull(m4.QNT2RSPB,0)
	when b.NOSAT=3 then
			B.QNT2-(Isnull(m1.QNT2SPB,0)+
			 case when b.nosat=1 then Isnull(B.QntBatal,0)/b.ISI
			 when b.nosat=2 then isnull(b.qntBatal,0)
			 when b.nosat=3 then isnull(b.qntBatal,0) end)
			+ Isnull(m4.QNT2RSPB,0)
end>0
AND  case when ISNULL(B.NOserah,'')  IN ('','-') then isnull(M6.SALDOQNT,0)
		  Else isnull(M6.SALDOQNT,0) + isnull(M61.SALDOQNT,0) End
>0
and Isnull(B.PUrgent,0)=1






" , ["bulan" => $periode->bulan , "tahun" =>$periode->tahun, "username" =>  \Auth::user()->username]);



    return [
      "tempOutstanding" => $tempOutstanding,
      "tempOutstanding2" => $tempOutstanding2,

      "tempOutstanding3" => $tempOutstanding3,
      "tempOutstanding4" => $tempOutstanding4,
      "tempOutstanding6" => $tempOutstanding6,
      "tempOutstanding5" => $tempOutstanding5
    ];
  }



  public function getNoBukti (Request $req) {

    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();


    if ($req->ppn == 0) {


      $values = [
          'SJN',
          $periode->bulan,
          $periode->tahun,
          $username,
          // $periode
          // $periode
      ];
      $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
      return $noBukti;

    } else {
      $inisial = DB::connection("SML")->select('select SPB from DBNOMOR');

      $values = [
          $inisial[0]->SPB,
          $periode->bulan,
          $periode->tahun,
          $username,
          // $periode
          // $periode
      ];
      $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
      return $noBukti;
    }


  }

  public function spKoreksi (Request $req) {
    $choice = $req->choice;

    $nobukti = $req->nobukti;




      // code...
      $values = [
        $choice,
        $nobukti,
        $req->nourut,
        $req->tanggal,
        $req->nospp ? $req->nospp : '' ,
        $req->kodecustsupp,
        $req->nopol,
        $req->container,
        $req->nocontainer,
        $req->noseal,
        $req->catatan,
        $req->urut,
        $req->urutspp,
        $req->kodebarang,
        $req->qnt,
        $req->qnt2,
        $req->sat1,
        $req->sat2,
        $req->nosat,
        $req->isi,
        $req->netw,
        $req->grossw,
        \Auth::user()->username ,
        0,
        $req->namabarang,
        $req->sopir,
        $req->kodegdg,
        $req->kodeexp,
        $req->noresi,
        $req->jumlahtagihan,
        $req->flagtipe,
        $req->nobatch,
        $req->noso,
        $req->urutso,
        $req->satx



      ];
      DB::connection('SML')->statement('exec sp_spb ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);

    return 1;
  }

  public function spAdd (Request $req) {
    // if ($choice == "I" && $jmlrecord == 0) {
      $check = DB::connection('SML')->select('select * from dbspb where Nobukti = :nobukti',["nobukti" => $req->nobukti]);
        if ($check) {
          return 2;
      }
    // }
    $username = \Auth::user()->username;
    // $periode = DB::connection("SML")->select('select TOP 1 * from DBPERIODE where user_id = :username' , ["username" => $username]);
    // $inisial = DB::connection("SML")->select('select SPB from DBNOMOR');
    $tempData = $req->tempData;

    // return $tempData;
    // $kodekebun = '';
    foreach ($tempData as $d) {
      DB::connection('SML')->update('update TempOutstanding set IsTerima = 1 , qntterima = :qnt where NOBUKTI = :NOBUKTI and URUT = :URUT and IDUser = :username',[ "qnt" => $d['inputQntTerima'] ,  "NOBUKTI" => $req->noso , "URUT" => $d['URUT'], "username" => $username ]);
    // return $noBukti;
      // $kodekebun = $d['kodekebun'];
    }
    // return ["qnt" => $d['inputQntTerima'] ,  "NOBUKTI" => $req->noso , "URUT" => $d['URUT'], "username" => $username];
    $values = [
        $req->nobukti,
        $req->nourut,
        $req->tanggal,
        $req->kodegdg,
        $req->noso,
        $req->catatanso,
        'SPB',
        $username,
        '',
        $req->kodeekspedisi ? $req->kodeekspedisi : '-' ,
        $req->nopol,
        $req->sopir,
        $req->jumlah,
        $req->kodekebun,
        $req->refukm,
        $req->jmlrecord
    ];


    DB::connection('SML')->statement('exec sp_InsertOutstandingSO ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?' ,$values);
    return 1;

  }

  public function getDetail (Request $req) {
    $nobukti = $req->nobukti;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $values = [
      \Auth::user()->username,
      $nobukti,
      $periode->tahun,
      $periode->bulan ,
      1,
      'SPB'
    ];
    DB::connection('SML')->statement('exec sp_RefreshTempOutstandingSO ?,?,?,?,?,?' ,$values);


    $header =  DB::connection("SML")->select("
Declare @bulan Int,@tahun Int,@IDuser varchar(20)
select @Bulan= :bulan,@Tahun= :tahun ,@IDUser= :username

DECLARE @TGL DATETIME
SET @TGL=GETDATE()
select 	A.NOBUKTI, B.URUT, B.KODEBRG, C.NamaBrg,
        case when B.NOSAT=1 Then B.QNT when B.NOSAT=2 Then B.QNT2 when B.Nosat=3 then B.qnt2  End Qnt,
        B.SATUAN,
    case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
         when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
                     when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
    End QntOut,
    D.KodeCustSupp,D.NamaCustSupp,A.KodeGdg,A.Tanggal,
    A.Nobukti+cast(B.urut as varchar(3)) KeyNobukti,Isnull(A.TipePPN,0) TipePPN,
    A.NoResi,A.NoPolKend,A.Sopir,A.JumlahTagihan,A.Kodeexp,
    D.namaCustSupp+'('+A.KodeCust+')' CXcust ,  A.NOBUKTI+cast(B.URUT as varchar(3)) KeyUrut,
    M2.NAMA+'('+A.KODEKEBUN+')' xcKEBUN,isnull(A.DP,0) DP,
    M3.ALAMAT ,A.kodekebun,A.Nopesanan,C.PartNumber,M5.NamaMerk,A.TGLKIRIM DUEDATE,A.UserID ,A.RefPR ,
   A.tglKirim,A.TglKirim,ISnull(M7.Nama,'-') namakebun,Isnull(A.RefPR,0) RefPR,A.catatan ,ISnull(D.pBlackLIst,0) pBlackLIst,

    case when ISNULL(B.NOserah,'')  IN ('','-')
    then
         case when b.nosat=1 then m62.saldoQnt
         when b.nosat=2 then m62.saldoqnt/c.isi2
         when b.nosat=3 then m62.saldoqnt/c.isi3 end
    else
         case when b.nosat=1 then (m62.saldoQnt) + (ISNULL(m63.saldoQnt,0))
         when b.nosat=2 then (m62.saldoqnt/c.isi2) + (m63.saldoqnt/c.isi2)
         when b.nosat=3 then (m62.saldoqnt/c.isi3) + (m63.saldoqnt/c.isi3) end
    End
    SaldoQnt ,


    case when ISNULL(B.NOserah,'')  IN ('','-')
    then
         case when b.nosat=1 then    m62.saldoQnt
         when b.nosat=2 then m62.saldoqnt/c.isi2
         when b.nosat=3 then m62.saldoqnt/c.isi3 end
    else
         case when b.nosat=1 then (m62.saldoQnt) + (ISNULL(m63.saldoQnt,0))
         when b.nosat=2 then (m62.saldoqnt/c.isi2) + (m63.saldoqnt/c.isi2)
         when b.nosat=3 then (m62.saldoqnt/c.isi3) + (m63.saldoqnt/c.isi3) end
    End
    SaldoQnt ,

    case when ISNULL(B.NOserah,'')  IN ('','-')
    then
         case when b.nosat=1 then    m62.saldoQntG01
         when b.nosat=2 then m62.saldoqntG01/c.isi2
         when b.nosat=3 then m62.saldoqntG01/c.isi3 end
    else
         case when b.nosat=1 then (m62.SALDOQNtg01) + (ISNULL(m63.SALDOQNtg01,0))
         when b.nosat=2 then (m62.SALDOQNtg01/c.isi2) + (m63.SALDOQNtg01/c.isi2)
         when b.nosat=3 then (m62.SALDOQNtg01/c.isi3) + (m63.SALDOQNtg01/c.isi3) end
    End
    SALDOQNtg01 ,

     case when ISNULL(B.NOserah,'')  IN ('','-')
    then
         case when b.nosat=1 then    m62.saldoQntG02
         when b.nosat=2 then m62.saldoqntG02/c.isi2
         when b.nosat=3 then m62.saldoqntG02/c.isi3 end
    else
         case when b.nosat=1 then (m62.SALDOQNtg02) + (ISNULL(m63.SALDOQNtg02,0))
         when b.nosat=2 then (m62.SALDOQNtg02/c.isi2) + (m63.SALDOQNtg02/c.isi2)
         when b.nosat=3 then (m62.SALDOQNtg02/c.isi3) + (m63.SALDOQNtg02/c.isi3) end
    End
    SALDOQNtg02 ,

     case when ISNULL(B.NOserah,'')  IN ('','-')
    then
         case when b.nosat=1 then    m62.saldoQntG03
         when b.nosat=2 then m62.saldoqntG03/c.isi2
         when b.nosat=3 then m62.saldoqntG03/c.isi3 end
    else
         case when b.nosat=1 then (m62.SALDOQNtg03) + (ISNULL(m63.SALDOQNtg03,0))
         when b.nosat=2 then (m62.SALDOQNtg03/c.isi2) + (m63.SALDOQNtg03/c.isi2)
         when b.nosat=3 then (m62.SALDOQNtg03/c.isi3) + (m63.SALDOQNtg03/c.isi3) end
    End
    SALDOQNtg03 ,


    case when
         case when ISNULL(B.NOserah,'')  IN ('','-')
     then
        case when b.nosat=1 then m62.saldoQnt
        when b.nosat=2 then m62.saldoqnt/c.isi2
        when b.nosat=3 then m62.saldoqnt/c.isi3 end
    else
        case when b.nosat=1 then (m62.saldoQnt) + (ISNULL(m63.saldoQnt,0))
        when b.nosat=2 then (m62.saldoqnt/c.isi2) + (m63.saldoqnt/c.isi2)
        when b.nosat=3 then (m62.saldoqnt/c.isi3) + (m63.saldoqnt/c.isi3) end
    End
         >
    case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
      when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
      when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
    End THEN
            case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
      when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
      when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
    END
  ELSE
       case when ISNULL(B.NOserah,'')  IN ('','-')
     then
      case when b.nosat=1 then m62.saldoQnt
      when b.nosat=2 then m62.saldoqnt/c.isi2
      when b.nosat=3 then m62.saldoqnt/c.isi3 end
    else
      case when b.nosat=1 then (m62.saldoQnt) + (ISNULL(m63.saldoQnt,0))
      when b.nosat=2 then (m62.saldoqnt/c.isi2) + (m63.saldoqnt/c.isi2)
      when b.nosat=3 then (m62.saldoqnt/c.isi3) + (m63.saldoqnt/c.isi3) end
    End
    END QNTXZ   ,case when datediff(day,getdate(),A.TGLKIRIM)<5 then 'y' else 'n' end KetW
    ,ISNULL(B.NOserah,'') NOSERAH ,m2.KodeKebun,m2.Nama AlamatLokasi
from	dbSO A
left outer join dBSODet B on B.NoBukti=A.NoBukti
left outer join dbBarang C on C.KodeBrg=B.KodeBrg
left outer join dbCustSupp D on D.KodeCustSupp=A.KODECUST
lEFT oUTER JOIN (SELECT NOSO,UrutSO,SUM(iSNULL(QNT,0)) QNT1SPB,SUM(ISNULL(QNT2,0)) QNT2SPB
        FROM dbSPBDet
        GROUP BY NoSO,UrutSO) M1 ON B.NOBUKTI=M1.NoSO AND B.URUT=M1.UrutSO
lEFT oUTER JOIN (SELECT b.NoSo,B.UrutSo,SUM(iSNULL(A.QNT,0)) QNT1RSPB,SUM(ISNULL(A.QNT2,0)) QNT2RSPB
        FROM dbRSPBDet A
        LEFT OUTER JOIN dbSPBDet B ON A.NoSPB=B.NoBukti AND A.UrutSPB=B.Urut
        GROUP BY b.NoSo,B.UrutSo) M4 ON B.NOBUKTI=M4.NoSO AND B.URUT=M4.UrutSO
left outer join dbkebuncustsupp m2 on A.KODEKEBUN=M2.KODEKEBUN AND A.KODECUST=M2.KODECUSTSUPP
LEFT OUTER JOIN DBALAMATCUST M3 ON A.NOALAMATKIRIM=M3.NOMOR   AND A.KODECUST=M3.KODECUSTSUPP
Left Outer Join Dbmerk M5 on C.KodeMerk=M5.kodeMerk
/*
LEFT OUTER JOIN (select kodebrg, sum(SALDOQNT)SaldoQnt,BULAN,TAHUN  from DBSTOCKBRG
                 Where BUlan=@bulan and Tahun=@Tahun  and KOdeGdg<>'GTC'
                 and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDuser)
                 and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=0)
                 group by KODEBRG,BULAN,TAHUN
                 ) M6 ON B.KODEBRG=M6.KODEBRG

LEFT OUTER JOIN (select kodebrg, sum(SALDOQNT)SaldoQnt,BULAN,TAHUN  from DBSTOCKBRG
          where BUlan=@bulan and Tahun=@Tahun
                  and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDUser)
          and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=1)
          group by KODEBRG,BULAN,TAHUN
        ) M61 ON B.KODEBRG=M61.KODEBRG
*/

left outer join ( SELECT KODEBRG,SUM(SALDOQNT) SALDOQNT,SUM(SALDOQNTG01) SALDOQNtg01,SUM(SALDOQNTG02) SALDOQNtg02,SUM(SALDOQNTG03) SALDOQNtg03
                FROM (
          select  KodeBrg, QntAwal SaldoQnt ,
                          CASE WHEN KODEGDG='G01' THEN QntAwal ELSE 0 END SALDOQNTG01,
                          CASE WHEN KODEGDG='G02' THEN QntAwal ELSE 0 END SALDOQNTG02,
                          CASE WHEN KODEGDG='G03' THEN QntAwal ELSE 0 END SALDOQNTG03
                  from 	dbStockBrg
          where         Tahun=year(@TGL) and Bulan=MONTH(@TGL)
          --where         Tahun=year(GETDATE()) and Bulan=1
          and KodeGdg <>'GTC'
          and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDuser)
                  and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=0)



          union all
          select 	KodeBrg,
          SUM(QntDb)-SUM(QntCr) SaldoQnt,
          SUM(case when Kodegdg='G01' then QntDb else 0 end)-SUM(case when Kodegdg='G01' then QntCr else 0 end) SaldoQntG01,
          SUM(case when Kodegdg='G02' then QntDb else 0 end)-SUM(case when Kodegdg='G02' then QntCr else 0 end) SaldoQntG02,
          SUM(case when Kodegdg='G03' then QntDb else 0 end)-SUM(case when Kodegdg='G03' then QntCr else 0 end) SaldoQntG03
          from 	vwKartuStock
          where 	year(Tanggal)=@tahun and month(tanggal)=@bulan and Tanggal<=@TGL
          --where 	year(Tanggal)=year(GETDATE()) and Tanggal<=GETDATE()
          and Tipe not in ('AWL')
          and KodeGdg <>'GTC'
          and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDuser)

                  and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=0)
          group by KodeBrg) A
          GROUP BY A.KODEBRG
        )M62 ON B.KODEBRG=M62.KODEBRG

left outer join ( SELECT KODEBRG,SUM(SALDOQNT) SALDOQNT ,SUM(SALDOQNTG01) SALDOQNtg01,SUM(SALDOQNTG02) SALDOQNtg02,SUM(SALDOQNTG03) SALDOQNtg03
            FROM (
            select KodeBrg, QntAwal SaldoQnt ,
            CASE WHEN KODEGDG='G01' THEN QntAwal ELSE 0 END SALDOQNTG01,
                          CASE WHEN KODEGDG='G02' THEN QntAwal ELSE 0 END SALDOQNTG02,
                          CASE WHEN KODEGDG='G03' THEN QntAwal ELSE 0 END SALDOQNTG03
          from 	dbStockBrg
          where         Tahun=@tahun and Bulan=@bulan
          --where         Tahun=@tahun and Bulan=1
          and KodeGdg <>'GTC'
          and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDuser)
                  and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=1)
          union all
          select 	KodeBrg,
          SUM(QntDb)-SUM(QntCr) SaldoQnt,
          SUM(case when Kodegdg='G01' then QntDb else 0 end)-SUM(case when Kodegdg='G01' then QntCr else 0 end) SaldoQntG01,
          SUM(case when Kodegdg='G02' then QntDb else 0 end)-SUM(case when Kodegdg='G02' then QntCr else 0 end) SaldoQntG02,
          SUM(case when Kodegdg='G03' then QntDb else 0 end)-SUM(case when Kodegdg='G03' then QntCr else 0 end) SaldoQntG03
          from 	vwKartuStock
          where 	year(Tanggal)=@tahun and month(tanggal)=@bulan and Tanggal<=@TGL
          --where 	year(Tanggal)=year(GETDATE()) and Tanggal<=@TGL
          and KodeGdg = Kodegdg and Tipe not in ('AWL')
          and KodeGdg <>'GTC'
          and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDUser)
          and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=1)
          group by KodeBrg) A
          GROUP BY A.KODEBRG
        )M63 ON B.KODEBRG=M63.KODEBRG


LEFT OUTER JOIN DBKEBUNCUSTSUPP m7 on a.KODECUST=m7.KODECUSTSUPP and a.KODEKEBUN=m7.KODEKEBUN
where
      Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit)=0
and

/*B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)>0  */
case when b.NOSAT=1 then
      B.QNT-(Isnull(m1.QNT1SPB,0)+
       case when b.nosat=1 then Isnull(B.QntBatal,0)
       when b.nosat=2 then isnull(b.qntBatal,0) * B.isi
       when b.nosat=3 then isnull(b.qntBatal,0) * B.isi end)
      + Isnull(m4.QNT1RSPB,0)
   when b.NOSAT=2 then
      B.QNT2-(Isnull(m1.QNT2SPB,0)+
       case when b.nosat=1 then Isnull(B.QntBatal,0)/b.ISI
       when b.nosat=2 then isnull(b.qntBatal,0)
       when b.nosat=3 then isnull(b.qntBatal,0) end)
      + Isnull(m4.QNT2RSPB,0)
  when b.NOSAT=3 then
      B.QNT2-(Isnull(m1.QNT2SPB,0)+
       case when b.nosat=1 then Isnull(B.QntBatal,0)/b.ISI
       when b.nosat=2 then isnull(b.qntBatal,0)
       when b.nosat=3 then isnull(b.qntBatal,0) end)
      + Isnull(m4.QNT2RSPB,0)
end>0
AND  case when ISNULL(B.NOserah,'')  IN ('','-') then isnull(M62.SALDOQNT,0)
      Else   isnull(M63.SALDOQNT,0) End
>0 and A.nobukti = :nobukti





" , ["bulan" => $periode->bulan , "tahun" =>$periode->tahun, "username" =>  \Auth::user()->username , "nobukti" =>  $req->nobukti]);


    $detail = DB::connection('SML')->select('select * from TempOutstanding
where NoBukti= :nobukti  and IDUser= :IDUser and QntSisa>0
and Trans= :Trans
order by Tanggal, NoBukti,Urut' , ["nobukti" => $nobukti , "IDUser" => \Auth::User()->username , "Trans" => 'SPB']);
    return ["detail" => $detail,"header" => $header ];
  }

public function getDetailKoreksi (Request $req) {
  $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

  $header = DB::connection("SML")->select("
  declare @Tahun int, @Bulan int

  select @Tahun= :tahun , @Bulan= :bulan


  select  m9.Nama NamaKebunX,f.AlamatKirim AlamatKirimX,a.nourut, A.SOPIR , A.KODEEXP ,E.NOSO NOSOT ,A.NOBUKTI, A.NOURUT, A.TANGGAL, A.NOSPP, A.KODECUSTSUPP, M1.NamaCustSupp,
          A.NoPolKend, A.Container, A.NoContainer, A.NoSeal, A.Catatan, A.IDUser, A.IsFlag Tipe,
          E.Nobukti Noso, D.NoBukti NoSPP,
          Case when A.isFlag=0 then 'SPB Barang Jadi'
               when A.isFlag=1 then 'SPB Bahan Baku dan Lain-lain'
               else ''
          end MyTipe,
          A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
          A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
          A.IsOtorisasi3, A.OtoUser3, A.TglOto3,
          A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
          A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
          Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                         Case when A.IsOtorisasi2=1 then 1 else 0 end+
                         Case when A.IsOtorisasi3=1 then 1 else 0 end+
                         Case when A.IsOtorisasi4=1 then 1 else 0 end+
                         Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                    else 1
               end As Bit) NeedOtorisasi
          ,Isnull(A.Isbatal,0) Isbatal,A.Userbatal,A.Tglbatal,F.NoPesanan ,
          ISnull(M7.Nama,'-') namakebun,A.RefUKM,(select top 1 NOSPB from DBRSPBDet where NOSPB=a.NoBukti)NOSPB,
          case when year(A.TGLKIRIM)=1899 then null else A.TGLKIRIM END TGLKIRIM,
          CASE WHEN YEAR(A.TGLTERIMA)=1899 THEN NULL ELSE A.TGLTERIMA END TGLTERIMA,
          ISnull(A.CetakKe,0) CetakKe,case when year(A.TglSPBINVC)=1899 then null else A.TglSPBINVC END TglSPBINVC
          ,case when year(A.TglTerimaBRG)=1899 then null else A.TglTerimaBRG END TglTerimaBRG
  from	dbSPB A
  Left Outer join (Select nobukti, nospp
                   from dbSPBDet
                   Group by nobukti, nospp) C on C.NoBukti=A.NoBukti
  Left Outer join (Select nobukti, NoSO
                   from dbSPPDet
                   Group by nobukti, NoSO) D on D.NoBukti=C.NoSPP
  Left Outer join (select NObukti,NOso from DBSPBDET group by Nobukti,NOso) E on A.Nobukti=E.NoBukti
  Left Outer JOin DBSO F on E.Noso=F.Nobukti
  left outer join vwBrowsCustomer B on B.KodeCust=A.KodeCustSupp and B.Sales=F.KODESLS
  Left OUter Join DbCustSupp M1 On A.kodeCustSupp=m1.KodeCustSupp
  LEFT OUTER JOIN DBKEBUNCUSTSUPP m7 on a.KODECUSTsupp=m7.KODECUSTSUPP and a.KODEKEBUN=m7.KODEKEBUN
  LEFT OUTER JOIN DBKEBUNCUSTSUPP m9 on f.KODECUST=m9.KODECUSTSUPP and f.KODEKEBUN=m9.KODEKEBUN
  LEFT OUTER JOIN DBRSPB M8 ON A.NOBUKTI=M8.NOSPB
  where	year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan  and A.Nobukti not like '%POS%' and A.Nobukti = :nobukti
  /*and (C.kodegdg in (Select kodegdg from dbPemakaigdg
                            where userid=:0 ) or ISNULL(A.ISBATAL,0)=1) */




  order by A.NoBukti
  " , [ "tahun" =>$periode->tahun, "bulan" => $periode->bulan , "nobukti" => $req->nobukti ]);

  $detail = DB::connection("SML")->select("
  Declare @nobukti varchar(50)
set @nobukti= :nobukti
select 	case when b.nosat=1 then b.SAT_1 else b.SAT_2 end Satuan,b.SATX, b.Namabrg namabrgx, b.nosat,b.URUTSO, b.NOSO , c.ISI1, c.ISI2 , e.KODEGDG, E.NAMA NAMAGDG, B.NOBUKTI, B.URUT, B.NoSPP NoSC, B.UrutSPP UrutSC, B.KODEBRG, C.NAMABRG, '' Jns_Kertas, ''Ukr_Kertas,
        B.QNT, B.QNT2, B.SAT_1, B.SAT_2, B.ISI, B.NetW, B.GrossW, '' KetDetail,d.qnt QNTRSPB,d.qnt2 QNT2RSPB,d.sat_1 SAT1RSPB,d.sat_2 SAT2RSPB
        ,case when B.Nosat=1 then B.Qnt when B.nosat=2 then B.Qnt2 When B.nosat=3 then B.Qnt2 End * Isnull(C.berat,0) NBerat,
        case when ISnull(C.pBerat,0)=0 then 'Volume' else 'Berat' End JBerat
from	dbSPBDet B
left outer join dbBarang C on C.KodeBrg=B.KodeBrg
left outer join dbrspbdet d on b.nobukti=d.nospb and b.urut=d.urutspb
left outer join dbgudang e on b.kodegdg=e.kodegdg
where	B.NoBukti=@NoBukti
order by B.Urut
  " , [  "nobukti" => $req->nobukti ]);







  return ["detail" => $detail , "header" => $header];
}



  public function spOtorisasi ( Request $req) {
    $res = DB::connection('SML')->update("update dbSPB set IsOtorisasi1 = 1, OtoUser1 = :username , TglOto1 = getDate() , IsBatal = NULL, UserBatal = NULL , TglBatal = NULL , MaxOL = 1  where NoBukti = :nobukti", ["username" => \Auth::user()->username , "nobukti" => $req->NOBUKTI ]);
    return 1;
  }

  public function spBatalOtorisasi ( Request $req) {
    $res = DB::connection('SML')->update("update dbSPB set IsBatal = 1, UserBatal = :username , TglBatal = GETDATE() , IsOtorisasi1 = 0, OtoUser1 = '' , TglOto1 = NULL , maxol = -1 where NoBukti = :nobukti ", ["username" => \Auth::user()->username , "nobukti" => $req->NOBUKTI ]);
    return 1;
  }

  public function listGudang (Request $req) {

    $listData = DB::connection('SML')->select("select kodegdg , nama  from DBGUDANG");
    return $listData;
  }

  public function listEkspedisi (Request $req) {

    $listData = DB::connection('SML')->select("select KODECUSTSUPP , NAMACUSTSUPP from dbcustsupp where JENIS = 2");
    return $listData;
  }

//   public function listBarang (Request $req) {
//     // $harga = DB::connection('SML')->select("select * from dbHARGAJUAL where KODEBRG = :kodebarang" , ['kodebarang' => $req->kodebarang]);
// //     select b.NAMAMERK ,  a.* from dbbarang a
// // join DBMERK b on a.KodeMerk = b.KODEMERK
// //  where a.KODEGRP = 'BJ' and a.pAgen = 1
//     $listData = DB::connection('SML')->select(" select a.Kodebrg, a.NamaBrg,I.NamaSubGrp,A.PartNumber,J.NAMAMERK,a.ISI1, a.ISI2, a.ISI3,
//                     A.Sat1,A.Sat2 ,A.Sat3,A.pPPN,Isnull(A.QntMin,0) QntMin ,a.Hrg1_1 , a.Hrg2_1, a.Hrg3_1
//                     from DBbarang a
//                     left OUter JOin DbSubgroup I on A.KodeSubGRp=I.KodeSUbgrp and A.KodeHdGrp=i.KodeHDGrp
//                     Left Outer join DbMerk J on A.KodeMerk=J.KodeMerk
//                     where a.isaktif=1 and A.KodeGrp in ('BJ','JS')
//                      and (A.KodeBrg like '%%') or (a.namaBrg like '%%')
//                     and isnull(A.Isaktif,0)=1
//                     order by a.Kodebrg ASC" );
//     return $listData;
//   }

  public function listBarang (Request $req) {
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

        $listData = DB::connection('SML')->select("
Declare @bulan Int,@tahun Int,@IDuser varchar(20)
select @Bulan= :bulan, @Tahun= :tahun ,@IDUser= :username

DECLARE @TGL DATETIME
SET @TGL=GETDATE()
select 	B.SATUAN,A.NOBUKTI NOSO, B.URUT URUTSO , B.NOSAT , case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
		     when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
                     when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
		End QntOut, c.ISI1, c.ISI2, c.ISI3,
                c.Sat1,c.Sat2 , c.Sat3 , c.Hrg1_1 , c.Hrg2_1, c.Hrg3_1, c.KodeBrg , c.NamaBrg
from	dbSO A
left outer join dBSODet B on B.NoBukti=A.NoBukti
left outer join dbBarang C on C.KodeBrg=B.KodeBrg
left outer join dbCustSupp D on D.KodeCustSupp=A.KODECUST
lEFT oUTER JOIN (SELECT NOSO,UrutSO,SUM(iSNULL(QNT,0)) QNT1SPB,SUM(ISNULL(QNT2,0)) QNT2SPB
				FROM dbSPBDet
				GROUP BY NoSO,UrutSO) M1 ON B.NOBUKTI=M1.NoSO AND B.URUT=M1.UrutSO
lEFT oUTER JOIN (SELECT b.NoSo,B.UrutSo,SUM(iSNULL(A.QNT,0)) QNT1RSPB,SUM(ISNULL(A.QNT2,0)) QNT2RSPB
				FROM dbRSPBDet A
				LEFT OUTER JOIN dbSPBDet B ON A.NoSPB=B.NoBukti AND A.UrutSPB=B.Urut
				GROUP BY b.NoSo,B.UrutSo) M4 ON B.NOBUKTI=M4.NoSO AND B.URUT=M4.UrutSO
left outer join dbkebuncustsupp m2 on A.KODEKEBUN=M2.KODEKEBUN AND A.KODECUST=M2.KODECUSTSUPP
LEFT OUTER JOIN DBALAMATCUST M3 ON A.NOALAMATKIRIM=M3.NOMOR   AND A.KODECUST=M3.KODECUSTSUPP
Left Outer Join Dbmerk M5 on C.KodeMerk=M5.kodeMerk
/*
LEFT OUTER JOIN (select kodebrg, sum(SALDOQNT)SaldoQnt,BULAN,TAHUN  from DBSTOCKBRG
                 Where BUlan=@bulan and Tahun=@Tahun  and KOdeGdg<>'GTC'
                 and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDuser)
                 and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=0)
                 group by KODEBRG,BULAN,TAHUN
                 ) M6 ON B.KODEBRG=M6.KODEBRG

LEFT OUTER JOIN (select kodebrg, sum(SALDOQNT)SaldoQnt,BULAN,TAHUN  from DBSTOCKBRG
				  where BUlan=@bulan and Tahun=@Tahun
                  and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDUser)
				  and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=1)
				  group by KODEBRG,BULAN,TAHUN
				) M61 ON B.KODEBRG=M61.KODEBRG
*/

left outer join ( SELECT KODEBRG,SUM(SALDOQNT) SALDOQNT,SUM(SALDOQNTG01) SALDOQNtg01,SUM(SALDOQNTG02) SALDOQNtg02,SUM(SALDOQNTG03) SALDOQNtg03
                FROM (
				  select  KodeBrg, QntAwal SaldoQnt ,
                          CASE WHEN KODEGDG='G01' THEN QntAwal ELSE 0 END SALDOQNTG01,
                          CASE WHEN KODEGDG='G02' THEN QntAwal ELSE 0 END SALDOQNTG02,
                          CASE WHEN KODEGDG='G03' THEN QntAwal ELSE 0 END SALDOQNTG03
                  from 	dbStockBrg
				  where         Tahun=year(@TGL) and Bulan=MONTH(@TGL)
				  --where         Tahun=year(GETDATE()) and Bulan=1
				  and KodeGdg <>'GTC'
				  and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDuser)
                  and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=0)



				  union all
				  select 	KodeBrg,
				  SUM(QntDb)-SUM(QntCr) SaldoQnt,
				  SUM(case when Kodegdg='G01' then QntDb else 0 end)-SUM(case when Kodegdg='G01' then QntCr else 0 end) SaldoQntG01,
				  SUM(case when Kodegdg='G02' then QntDb else 0 end)-SUM(case when Kodegdg='G02' then QntCr else 0 end) SaldoQntG02,
				  SUM(case when Kodegdg='G03' then QntDb else 0 end)-SUM(case when Kodegdg='G03' then QntCr else 0 end) SaldoQntG03
				  from 	vwKartuStock
				  where 	year(Tanggal)=@tahun and month(tanggal)=@bulan and Tanggal<=@TGL
				  --where 	year(Tanggal)=year(GETDATE()) and Tanggal<=GETDATE()
				  and Tipe not in ('AWL')
				  and KodeGdg <>'GTC'
				  and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDuser)

                  and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=0)
				  group by KodeBrg) A
				  GROUP BY A.KODEBRG
				)M62 ON B.KODEBRG=M62.KODEBRG

left outer join ( SELECT KODEBRG,SUM(SALDOQNT) SALDOQNT ,SUM(SALDOQNTG01) SALDOQNtg01,SUM(SALDOQNTG02) SALDOQNtg02,SUM(SALDOQNTG03) SALDOQNtg03
		        FROM (
			      select KodeBrg, QntAwal SaldoQnt ,
			      CASE WHEN KODEGDG='G01' THEN QntAwal ELSE 0 END SALDOQNTG01,
                          CASE WHEN KODEGDG='G02' THEN QntAwal ELSE 0 END SALDOQNTG02,
                          CASE WHEN KODEGDG='G03' THEN QntAwal ELSE 0 END SALDOQNTG03
				  from 	dbStockBrg
				  where         Tahun=@tahun and Bulan=@bulan
				  --where         Tahun=@tahun and Bulan=1
				  and KodeGdg <>'GTC'
				  and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDuser)
                  and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=1)
				  union all
				  select 	KodeBrg,
				  SUM(QntDb)-SUM(QntCr) SaldoQnt,
				  SUM(case when Kodegdg='G01' then QntDb else 0 end)-SUM(case when Kodegdg='G01' then QntCr else 0 end) SaldoQntG01,
				  SUM(case when Kodegdg='G02' then QntDb else 0 end)-SUM(case when Kodegdg='G02' then QntCr else 0 end) SaldoQntG02,
				  SUM(case when Kodegdg='G03' then QntDb else 0 end)-SUM(case when Kodegdg='G03' then QntCr else 0 end) SaldoQntG03
				  from 	vwKartuStock
				  where 	year(Tanggal)=@tahun and month(tanggal)=@bulan and Tanggal<=@TGL
				  --where 	year(Tanggal)=year(GETDATE()) and Tanggal<=@TGL
				  and KodeGdg = Kodegdg and Tipe not in ('AWL')
				  and KodeGdg <>'GTC'
				  and kodegdg in (Select kodegdg from dbPemakaigdg where userid=@IDUser)
				  and KodeGdg in (select kodegdg from dbgudang where Isnull(IStakeinOut,0)=1)
				  group by KodeBrg) A
				  GROUP BY A.KODEBRG
				)M63 ON B.KODEBRG=M63.KODEBRG


LEFT OUTER JOIN DBKEBUNCUSTSUPP m7 on a.KODECUST=m7.KODECUSTSUPP and a.KODEKEBUN=m7.KODEKEBUN
where
      Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit)=0
and

/*B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)>0  */
case when b.NOSAT=1 then
			B.QNT-(Isnull(m1.QNT1SPB,0)+
			 case when b.nosat=1 then Isnull(B.QntBatal,0)
			 when b.nosat=2 then isnull(b.qntBatal,0) * B.isi
			 when b.nosat=3 then isnull(b.qntBatal,0) * B.isi end)
			+ Isnull(m4.QNT1RSPB,0)
	 when b.NOSAT=2 then
			B.QNT2-(Isnull(m1.QNT2SPB,0)+
			 case when b.nosat=1 then Isnull(B.QntBatal,0)/b.ISI
			 when b.nosat=2 then isnull(b.qntBatal,0)
			 when b.nosat=3 then isnull(b.qntBatal,0) end)
			+ Isnull(m4.QNT2RSPB,0)
	when b.NOSAT=3 then
			B.QNT2-(Isnull(m1.QNT2SPB,0)+
			 case when b.nosat=1 then Isnull(B.QntBatal,0)/b.ISI
			 when b.nosat=2 then isnull(b.qntBatal,0)
			 when b.nosat=3 then isnull(b.qntBatal,0) end)
			+ Isnull(m4.QNT2RSPB,0)
end>0
AND  case when ISNULL(B.NOserah,'')  IN ('','-') then isnull(M62.SALDOQNT,0)
		  Else   isnull(M63.SALDOQNT,0) End
>0
and A.NOBUKTI = :nobukti" , [ "bulan" => $periode->bulan , "tahun" =>$periode->tahun,  "username" => \Auth::User()->username, "nobukti" => $req->nobukti ] );
        return $listData;
  }



  public function spAddKirimTerima (Request $req) {
    
  $data = DB::connection('SML')->update("UPDATE dbspb
      set TGLKIRIM = :tanggalKirim, TGLTERIMA = :tanggalTerima, TglTerimaBrg = :tanggalTerimaBarang where NoBukti = :kodeInvoiceTemp
      ", ['tanggalTerima'=> $req->tanggalTerima,'tanggalTerimaBarang'=> $req->tanggalTerimaBarang,'tanggalKirim'=> $req->tanggalKirim,'kodeInvoiceTemp'=> $req->kodeInvoiceTemp]);

    
    return $data;

  }

  public function spAddKirimTerimaAcc (Request $req) {
    
  $data = DB::connection('SML')->update("UPDATE dbspb
      set TglSPBINVC = :tanggalKirimAcc where NoBukti = :kodeInvoiceTemp
      ", ['tanggalKirimAcc'=> $req->tanggalKirimAcc,'kodeInvoiceTemp'=> $req->kodeInvoiceTemp]);

    
    return $data;

  }


}
