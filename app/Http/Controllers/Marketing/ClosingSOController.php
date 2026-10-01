<?php


namespace App\Http\Controllers\Marketing;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\DBFLMENU;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;



// use App\Http\Controllers\NewMenuController;

class ClosingSOController extends Controller
{

  public function index(Request $req) {
    $kodemenu = '041011';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu , $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }



    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    // Periode filter di toolbar (po-filter-wrap, sama seperti so.blade.php) mengirim
    // tglawal/tglakhir sebagai date-range bebas -- kalau tidak dikirim (initial load),
    // jatuh ke default bulan/tahun periode yang sedang aktif.
    if ($req->tglawal && $req->tglakhir) {
      $tglawal = $req->tglawal;
      $tglakhir = $req->tglakhir;
    } else {
      $tglawal = \Carbon\Carbon::now()->year((int) $periode->tahun)->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d');
      $tglakhir = \Carbon\Carbon::now()->year((int) $periode->tahun)->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d');
    }

    // $listData = DB::connection('SML')->select('SELECT * FROM DBMERK');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(4);


    $tempOutstanding = DB::connection("SML")->select("select 	A.NOBUKTI, B.URUT, B.KODEBRG, C.NamaBrg,
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
    case when b.nosat=1 then m6.saldoQnt
         when b.nosat=2 then m6.saldoqnt/c.isi2
         when b.nosat=3 then m6.saldoqnt/c.isi3 end SaldoQnt,
    A.TglKirim,ISnull(M7.Nama,'-') namakebun,
    case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
		     when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
                     when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
		End -
	  case when b.nosat=1 then m6.saldoQnt
         when b.nosat=2 then m6.saldoqnt/c.isi2
         when b.nosat=3 then m6.saldoqnt/c.isi3 end QNTzx
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
LEFT OUTER JOIN (select kodebrg, sum(SALDOQNT)SaldoQnt,BULAN,TAHUN  from DBSTOCKBRG  where kodegdg<>'GTC'
                            AND    BUlan= :bulan and Tahun= :tahun
				group by KODEBRG,BULAN,TAHUN
				) M6 ON B.KODEBRG=M6.KODEBRG
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
/*B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)>0*/
case when b.NOSAT=1 then
			B.QNT-(Isnull(m1.QNT1SPB,0)+
			 case when b.nosat=1 then Isnull(B.QntBatal,0)
			 when b.nosat=2 then isnull(b.qntBatal,0) * B.isi
			 when b.nosat=3 then isnull(b.qntBatal,0) * B.isi end)
			+ Isnull(cast(m4.QNT1RSPB as numeric(18,3)),0)
	 when b.NOSAT=2 then
			B.QNT2-(Isnull(m1.QNT2SPB,0)+
			 case when b.nosat=1 then Isnull(B.QntBatal,0)/b.ISI
			 when b.nosat=2 then isnull(b.qntBatal,0)
			 when b.nosat=3 then isnull(b.qntBatal,0) end)
			+ Isnull(cast(m4.QNT2RSPB as numeric(18,3)),0)
	when b.NOSAT=3 then
			B.QNT2-(Isnull(m1.QNT2SPB,0)+
			 case when b.nosat=1 then Isnull(B.QntBatal,0)/b.ISI
			 when b.nosat=2 then isnull(b.qntBatal,0)
			 when b.nosat=3 then isnull(b.qntBatal,0) end)
			+ Isnull(cast(m4.QNT2RSPB as numeric(18,3)),0)
end>0.00
and ISnull(B.Isbatal,0)=0
and A.Tanggal between :tglawal and :tglakhir
order by A.NOBUKTI,B.Urut


" , ["bulan" => $periode->bulan , "tahun" =>$periode->tahun, "tglawal" => $tglawal, "tglakhir" => $tglakhir]);


$tempOutstanding2 = DB::connection("SML")->select("select 	A.NOBUKTI, B.URUT, B.KODEBRG, C.NamaBrg,
        case when B.NOSAT=1 Then B.QNT when B.NOSAT=2 Then B.QNT2 when B.Nosat=3 then B.qnt2  End Qnt,
        B.SATUAN,
		case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
		     when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
                     when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
		End QntOut,
    D.KodeCustSupp,D.NamaCustSupp,A.KodeGdg,A.Tanggal,
    A.Nobukti+cast(B.urut as varchar(3)) KeyNobukti,Isnull(A.TipePPN,0) TipePPN,
    A.NoResi,A.NoPolKend,A.Sopir,A.JumlahTagihan,A.Kodeexp,
    D.namaCustSupp+'('+A.KodeCust+')' CXcust ,
    M2.NAMA+'('+A.KODEKEBUN+')' xcKEBUN,
    M3.ALAMAT ,A.kodekebun,A.Nopesanan,C.PartNumber,M5 .NamaMerk,A.TGLKIRIM DUEDATE,A.UserID,
    case when b.nosat=1 then m6.saldoQnt
         when b.nosat=2 then m6.saldoqnt/c.isi2
         when b.nosat=3 then m6.saldoqnt/c.isi2 end SaldoQnt,
    A.TglKirim,ISnull(M7.Nama,'-') namakebun,
    case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
		     when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
                     when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
		End -
	  case when b.nosat=1 then m6.saldoQnt
         when b.nosat=2 then m6.saldoqnt/c.isi2
         when b.nosat=3 then m6.saldoqnt/c.isi2 end QNTzx ,B.Qntbatal,B.UserBatal,B.Ketbatal,B.TglBatal
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
LEFT OUTER JOIN (select kodebrg, sum(SALDOQNT)SaldoQnt,BULAN,TAHUN  from DBSTOCKBRG  where kodegdg<>'GTC'
                            AND    BUlan= :bulan and Tahun= :tahun
				group by KODEBRG,BULAN,TAHUN
				) M6 ON B.KODEBRG=M6.KODEBRG
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
isnull(B.Isbatal,0)=1
and A.Tanggal between :tglawal and :tglakhir
order by A.NOBUKTI,B.Urut
" , ["bulan" => $periode->bulan , "tahun" =>$periode->tahun, "tglawal" => $tglawal, "tglakhir" => $tglakhir]);

    return view('marketing.closingso' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "tempOutstanding" => $tempOutstanding,
      "tempOutstanding2" => $tempOutstanding2,
      "akses" => $akses
    ]);

  }


  public function loadAll (Request $req) {

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    if ($req->tglawal && $req->tglakhir) {
      $tglawal = $req->tglawal;
      $tglakhir = $req->tglakhir;
    } else {
      $tglawal = \Carbon\Carbon::now()->year((int) $periode->tahun)->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d');
      $tglakhir = \Carbon\Carbon::now()->year((int) $periode->tahun)->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d');
    }

    $tempOutstanding = DB::connection("SML")->select("select 	A.NOBUKTI, B.URUT, B.KODEBRG, C.NamaBrg,
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
      case when b.nosat=1 then m6.saldoQnt
           when b.nosat=2 then m6.saldoqnt/c.isi2
           when b.nosat=3 then m6.saldoqnt/c.isi3 end SaldoQnt,
      A.TglKirim,ISnull(M7.Nama,'-') namakebun,
      case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
  		     when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
                       when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
  		End -
  	  case when b.nosat=1 then m6.saldoQnt
           when b.nosat=2 then m6.saldoqnt/c.isi2
           when b.nosat=3 then m6.saldoqnt/c.isi3 end QNTzx
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
  LEFT OUTER JOIN (select kodebrg, sum(SALDOQNT)SaldoQnt,BULAN,TAHUN  from DBSTOCKBRG  where kodegdg<>'GTC'
                              AND    BUlan= :bulan and Tahun= :tahun
  				group by KODEBRG,BULAN,TAHUN
  				) M6 ON B.KODEBRG=M6.KODEBRG
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
  /*B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)>0*/
  case when b.NOSAT=1 then
  			B.QNT-(Isnull(m1.QNT1SPB,0)+
  			 case when b.nosat=1 then Isnull(B.QntBatal,0)
  			 when b.nosat=2 then isnull(b.qntBatal,0) * B.isi
  			 when b.nosat=3 then isnull(b.qntBatal,0) * B.isi end)
  			+ Isnull(cast(m4.QNT1RSPB as numeric(18,3)),0)
  	 when b.NOSAT=2 then
  			B.QNT2-(Isnull(m1.QNT2SPB,0)+
  			 case when b.nosat=1 then Isnull(B.QntBatal,0)/b.ISI
  			 when b.nosat=2 then isnull(b.qntBatal,0)
  			 when b.nosat=3 then isnull(b.qntBatal,0) end)
  			+ Isnull(cast(m4.QNT2RSPB as numeric(18,3)),0)
  	when b.NOSAT=3 then
  			B.QNT2-(Isnull(m1.QNT2SPB,0)+
  			 case when b.nosat=1 then Isnull(B.QntBatal,0)/b.ISI
  			 when b.nosat=2 then isnull(b.qntBatal,0)
  			 when b.nosat=3 then isnull(b.qntBatal,0) end)
  			+ Isnull(cast(m4.QNT2RSPB as numeric(18,3)),0)
  end>0.00
  and ISnull(B.Isbatal,0)=0
  and A.Tanggal between :tglawal and :tglakhir
  order by A.NOBUKTI,B.Urut


  " , ["bulan" => $periode->bulan , "tahun" =>$periode->tahun, "tglawal" => $tglawal, "tglakhir" => $tglakhir]);


$tempOutstanding2 = DB::connection("SML")->select("select 	A.NOBUKTI, B.URUT, B.KODEBRG, C.NamaBrg,
        case when B.NOSAT=1 Then B.QNT when B.NOSAT=2 Then B.QNT2 when B.Nosat=3 then B.qnt2  End Qnt,
        B.SATUAN,
		case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
		     when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
                     when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
		End QntOut,
    D.KodeCustSupp,D.NamaCustSupp,A.KodeGdg,A.Tanggal,
    A.Nobukti+cast(B.urut as varchar(3)) KeyNobukti,Isnull(A.TipePPN,0) TipePPN,
    A.NoResi,A.NoPolKend,A.Sopir,A.JumlahTagihan,A.Kodeexp,
    D.namaCustSupp+'('+A.KodeCust+')' CXcust ,
    M2.NAMA+'('+A.KODEKEBUN+')' xcKEBUN,
    M3.ALAMAT ,A.kodekebun,A.Nopesanan,C.PartNumber,M5 .NamaMerk,A.TGLKIRIM DUEDATE,A.UserID,
    case when b.nosat=1 then m6.saldoQnt
         when b.nosat=2 then m6.saldoqnt/c.isi2
         when b.nosat=3 then m6.saldoqnt/c.isi2 end SaldoQnt,
    A.TglKirim,ISnull(M7.Nama,'-') namakebun,
    case when B.NOSAT=1 then B.QNT-(Isnull(m1.QNT1SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT1RSPB,0)
		     when B.NOSAT=2 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
                     when B.NOSAT=3 Then B.QNT2-(Isnull(m1.QNT2SPB,0)+Isnull(B.QntBatal,0))+Isnull(m4.QNT2RSPB,0)
		End -
	  case when b.nosat=1 then m6.saldoQnt
         when b.nosat=2 then m6.saldoqnt/c.isi2
         when b.nosat=3 then m6.saldoqnt/c.isi2 end QNTzx ,B.Qntbatal,B.UserBatal,B.Ketbatal,B.TglBatal
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
LEFT OUTER JOIN (select kodebrg, sum(SALDOQNT)SaldoQnt,BULAN,TAHUN  from DBSTOCKBRG  where kodegdg<>'GTC'
                            AND    BUlan= :bulan and Tahun= :tahun
				group by KODEBRG,BULAN,TAHUN
				) M6 ON B.KODEBRG=M6.KODEBRG
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
isnull(B.Isbatal,0)=1
and A.Tanggal between :tglawal and :tglakhir
order by A.NOBUKTI,B.Urut


" , ["bulan" => $periode->bulan , "tahun" =>$periode->tahun, "tglawal" => $tglawal, "tglakhir" => $tglakhir]);


    return [
      "tempOutstanding" => $tempOutstanding,
      "tempOutstanding2" => $tempOutstanding2,
    ];
  }




  public function spClosingSO (Request $req) {

    if ($req->all == 1) {
      $tanggal = date('Y-m-d H:i:s');
      $res = DB::connection('SML')->update("Update dbsodet set QNTBATAL= :qntbatal,UserBatal= :username ,
                    TglBatal=getdate(),isbatal=1,Ketbatal= :keterangan where Nobukti= :nobukti
                  ", ["qntbatal"=> $req->qntout , "username" => \Auth::user()->username , "keterangan" => $req->keterangan , "nobukti" => $req->NOBUKTI ]);

    } else {
      $tanggal = date('Y-m-d H:i:s');
      $res = DB::connection('SML')->update("Update dbsodet set QNTBATAL= :qntbatal,UserBatal= :username ,
                    TglBatal=getdate(),isbatal=1,Ketbatal= :keterangan where Nobukti= :nobukti
                    and kodebrg= :kodebarang and Urut= :urut", ["qntbatal"=> $req->qntout , "username" => \Auth::user()->username , "keterangan" => $req->keterangan , "nobukti" => $req->NOBUKTI , "kodebarang" => $req->kodebrg , "urut" => $req->urut]);

    }
    return 1;
  }

  public function spOpenSO (Request $req) {

    if ($req->all == 1 ) {
      $tanggal = date('Y-m-d H:i:s');
      $res = DB::connection('SML')->update("Update dbsodet set QNTBATAL=0,isbatal=0,UserBatal= :username ,TglBatal=Null,Ketbatal='' where Nobukti= :nobukti
                             ", [ "username" => \Auth::user()->username ,"nobukti" => $req->NOBUKTI ]);


    } else {
      $tanggal = date('Y-m-d H:i:s');
      $res = DB::connection('SML')->update("Update dbsodet set QNTBATAL=0,isbatal=0,UserBatal= :username ,TglBatal=Null,Ketbatal='' where Nobukti= :nobukti
                             and KodeBrg= :kodebarang and Urut= :urut", [ "username" => \Auth::user()->username ,"nobukti" => $req->NOBUKTI , "kodebarang" => $req->kodebrg , "urut" => $req->urut]);

    }

    return 1;
  }



}
