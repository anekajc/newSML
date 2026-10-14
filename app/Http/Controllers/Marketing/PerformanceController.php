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



class PerformanceController extends Controller
{

  // Query bersama index()/loadAll() -- tab "SO Sudah Otorisasi" (OtoPerf=0, DP>0) dan
  // "Perintah Uang Muka" (OtoPerf=1) digabung jadi satu daftar dengan filter status
  // (0=Semua,1=Belum,2=Sudah), sama seperti kreditnote/perintahreturjual.
  private function queryPerformance ($tglawal, $tglakhir, $filterperf) {
    // "Belum" = SO CBD (tipebayar=0) seperti index() programajcSML; kondisi DP>0 di sana
    // sengaja dikomentari (loadAll programajcSML masih DP>0 = sisa lama, tidak diikuti).
    $statusCondition = $filterperf == 1
      ? "isnull(a.OtoPerf,0)=0 and A.tipebayar=0"
      : ($filterperf == 2
        ? "isnull(a.OtoPerf,0)=1"
        : "(isnull(a.OtoPerf,0)=1 or (isnull(a.OtoPerf,0)=0 and A.tipebayar=0))");

    return DB::connection("SML")->select("
declare @Awal date, @Akhir date

select @Awal= :tglawal, @Akhir= :tglakhir

Select 	A.NoBukti, A.NoUrut, A.Tanggal, A.KodeCust, C.NamaCustSupp NamaCust,
A.Handling, A.NoAlamatKirim, J.Alamat AlamatKirim, C.Kota NamaKota,
    I.TotSubTotal, I.TotDiskon, I.TotTotal, I.TotDPP, I.TotPPN, I.TotNet,
    I.TotSubTotalRp, I.TotDiskonRp, I.TotTotalRp, I.TotDPPRp, I.TotPPNRp, I.TotNetRp,
    A.Userid, A.TglInput,
A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
    Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                   Case when A.IsOtorisasi2=1 then 1 else 0 end+
                   Case when A.IsOtorisasi3=1 then 1 else 0 end+
                   Case when A.IsOtorisasi4=1 then 1 else 0 end+
                   Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
              else 1
         end As Bit) NeedOtorisasi,I.subtotalclose,I.ndiskonclose,I.TotTotalclose,I.totdppclose,I.totppnclose,I.totnetclose
    ,Isnull(A.Isbatal,0)IsBatal,A.userbatal,A.Tglbatal,Isnull(A.TipePPN,0) TipePPN,
    A.KODESLS,K.Nama NAMASLS,A.KodePF,L.Nama NAMAPIC,A.NoPesanan,A.catatan,A.OtoPerf,A.userOtoPerf,A.tglOtoPerf ,A.DP
From dbSO A
Left Outer Join dbCustSupp C on c.KodeCustsupp=a.KodeCust
Left Outer Join vwRpDetSO I on I.NoBukti=A.NoBukti
Left Outer Join dbAlamatCust J on J.KodeCustsupp=A.KodeCust and J.Nomor=A.NoAlamatKirim
Left outer join dbKaryawan K ON A.KODESLS=K.KeyNIK
LEFT OUTER JOIN DBPICCUSTSUPP L ON A.KodePF=L.KODEPIC  and A.kodecust=L.kodecustsupp
where A.Tanggal between @Awal and @Akhir and
Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                   Case when A.IsOtorisasi2=1 then 1 else 0 end+
                   Case when A.IsOtorisasi3=1 then 1 else 0 end+
                   Case when A.IsOtorisasi4=1 then 1 else 0 end+
                   Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
              else 1
         end As Bit)=0 and {$statusCondition}
order by A.NoBukti
" , [ "tglawal" => $tglawal , "tglakhir" => $tglakhir ]);
  }

  // Awal/akhir bulan periode aktif. Carbon::now()->month() dulu mengabaikan tahun periode
  // dan meluap ke bulan berikutnya bila hari ini tgl 29-31 (mis. 31 Okt -> month(2) = Maret).
  private function rentangPeriode ($periode) {
    $awal = \Carbon\Carbon::create((int) $periode->tahun, (int) $periode->bulan, 1);
    return [$awal->format('Y-m-d'), $awal->copy()->endOfMonth()->format('Y-m-d')];
  }

  public function index(Request $req) {
    $kodemenu = '041012';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu , $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(4);

    [$tglawal, $tglakhir] = $this->rentangPeriode($periode);
    $tempOutstanding = $this->queryPerformance($tglawal, $tglakhir, 0);

    return view('marketing.performance' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "tglawal" => $tglawal,
      "tglakhir" => $tglakhir,
      "tempOutstanding" => $tempOutstanding,
      "akses" => $akses
    ]);

  }

  public function getDetailCetak(Request $req)
  {
      $noBukti = $req->input('NOBUKTI');

      $cetak = DB::connection("SML")->select(
          "EXEC dbo.Sp_CetakSO2 ?",
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
    [$defAwal, $defAkhir] = $this->rentangPeriode($periode);
    $tglawal = $this->tanggalFilter($req->tglawal) ?: $defAwal;
    $tglakhir = $this->tanggalFilter($req->tglakhir) ?: $defAkhir;
    $filterperf = $req->filterperf !== null ? (int) $req->filterperf : 0;

    $tempOutstanding = $this->queryPerformance($tglawal, $tglakhir, $filterperf);

    return ["tempOutstanding" => $tempOutstanding];
  }

  public function getDetail (Request $req) {
    $nobukti = $req->nobukti;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();




    $detail = DB::connection('SML')->select("declare @Tahun int, @Bulan int

select @Bulan= :bulan , @Tahun= :tahun

Select 	A.NoBukti, A.NoSPB, B.UrutSPB,
	A.NoBukti+right('0000000000'+cast(B.Urut as varchar(10)),10) NoBuktiUrut,
        B.Urut, B.KodeBrg, H.NamaBrg, B.Qnt, B.NoSat, B.Isi, H.Sat1 Satuan,
        B.Qnt2, H.Sat2 SatuanRoll, B.Harga,
        B.DiscP1, B.DiscRp1, B.DiscTot,B.Qnt-B.QNTBATAL Qnt1xClose,(B.Qnt-B.QNTBATAL)/case when isnull(H.Isi2 , 0) = 0 then 1 else h.Isi2 end Qnt2xCLose ,
        (B.Qnt-B.QNTBATAL)/case when isnull(H.Isi3 , 0) = 0 then 1 else h.Isi3 end Qnt3xClose,((([Qnt2]-B.QNTBATAL)*([HARGA]-[Disctot]))*B.[Kurs]) TotalIDRClose
        ,B.SubTotal TotalUSD, B.SubTotalRp TotalIDR, B.NDPPRp NDPP,
        B.NPPNRp NPPN, B.BYAngkut Beban, B.SubTotalRp + B.BYAngkut Total,
        (select Top 1 Z.TANGGAL from DBBELIDET X Left Outer Join DBBELI Z on X.NOBUKTI=Z.NOBUKTI
        Where Z.TANGGAL <A.TANGGAL and B.KODEBRG=B.KODEBRG order by Z.TANGGAL Desc ) THA,
        (select Top 1 X.HARGA from DBBELIDET X Left Outer Join DBBELI Z on X.NOBUKTI=Z.NOBUKTI
        Where Z.TANGGAL <A.TANGGAL and B.KODEBRG=B.KODEBRG order by Z.TANGGAL Desc ) HA ,
        B.Qnt Qnt1x,B.Qnt/ case when isnull(H.Isi2 , 0) = 0 then 1 else h.Isi2 end  Qnt2x,B.Qnt/ case when isnull(H.Isi3 , 0) = 0 then 1 else h.Isi3 end Qnt3x,H.Sat1 SAT1X,H.SAT2 SAT2X,H.SAT3 SAT3X
          ,case when ISnull(B.iscetakKitir,0)=0 then 'Tidak' else 'Ya' End KetPO,C.PartNumber ,D.NAMAMERK,B.Noserah


,Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                       Case when A.IsOtorisasi3=1 then 1 else 0 end+
                       Case when A.IsOtorisasi4=1 then 1 else 0 end+
                       Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                  else 1
             end As Bit) needOto
From dbSO A
Left Outer join dbSODet B on B.NoBukti=a.NoBukti
left outer join dbbarang C on B.kodebrg=C.Kodebrg
Left Outer Join vwSatuanBrg H on H.KodeBrg=B.KodeBrg --and H.NoSat=B.NoSat
LEFT OUTER JOIN DBMERK D ON C.KODEMERK=D.KODEMERK
where Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                       Case when A.IsOtorisasi3=1 then 1 else 0 end+
                       Case when A.IsOtorisasi4=1 then 1 else 0 end+
                       Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                  else 1
             end As Bit) = 0
             and A.NoBukti= :nobukti
order by A.NoBukti, B.Urut

    " , ["bulan" => $periode->bulan ,"tahun" =>$periode->tahun ,  "nobukti" => $nobukti]) ;


    $header = DB::connection("SML")->select("
    declare @Tahun int, @Bulan int,@idUSER VARCHAR(20)

select @Tahun= :tahun, @Bulan=:bulan

Select 	A.NoBukti, A.NoUrut, A.Tanggal, A.KodeCust, C.NamaCustSupp NamaCust,
  A.Handling, A.NoAlamatKirim, J.Alamat AlamatKirim, C.Kota NamaKota,
        I.TotSubTotal, I.TotDiskon, I.TotTotal, I.TotDPP, I.TotPPN, I.TotNet,
        I.TotSubTotalRp, I.TotDiskonRp, I.TotTotalRp, I.TotDPPRp, I.TotPPNRp, I.TotNetRp,
        A.Userid, A.TglInput,
  A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
  A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
  A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
        Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                       Case when A.IsOtorisasi3=1 then 1 else 0 end+
                       Case when A.IsOtorisasi4=1 then 1 else 0 end+
                       Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                  else 1
             end As Bit) NeedOtorisasi,I.subtotalclose,I.ndiskonclose,I.TotTotalclose,I.totdppclose,I.totppnclose,I.totnetclose
        ,Isnull(A.Isbatal,0)IsBatal,A.userbatal,A.Tglbatal,Isnull(A.TipePPN,0) TipePPN,
        A.KODESLS,K.Nama NAMASLS,A.KodePF,L.Nama NAMAPIC,A.NoPesanan,A.catatan,A.OtoPerf,A.userOtoPerf,A.tglOtoPerf ,A.DP
From dbSO A
Left Outer Join dbCustSupp C on c.KodeCustsupp=a.KodeCust
Left Outer Join vwRpDetSO I on I.NoBukti=A.NoBukti
Left Outer Join dbAlamatCust J on J.KodeCustsupp=A.KodeCust and J.Nomor=A.NoAlamatKirim
Left outer join dbKaryawan K ON A.KODESLS=K.KeyNIK
LEFT OUTER JOIN DBPICCUSTSUPP L ON A.KodePF=L.KODEPIC  and A.kodecust=L.kodecustsupp
where /* filter bulan periode dibuang: daftar newSML memakai rentang tanggal bebas, header
         dibatasi bulan periode membuat res.header kosong -> error 'NamaCust' di buttonAdd */
Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                       Case when A.IsOtorisasi3=1 then 1 else 0 end+
                       Case when A.IsOtorisasi4=1 then 1 else 0 end+
                       Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                  else 1
             end As Bit)=0
             and A.NoBukti= :nobukti




order by A.NoBukti
" , [ "tahun" =>$periode->tahun , "bulan" => $periode->bulan ,  "nobukti" => $nobukti]);

    return ["detail" => $detail , "header" => $header];
  }


  public function spOtoPerf (Request $req) {
    // $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("Update DBSO set OtoPerf=1,TglOtoPerf=GetDate(),UserOtoPerf= :username where nobukti= :nobukti", ["username" => \Auth::user()->username , "nobukti" => $req->NOBUKTI ]);
    return 1;
  }

  public function spBatalOtoPerf (Request $req) {
    // $tanggal = date('Y-m-d H:i:s');
    $sudahUM = DB::connection('SML')->select("select top 1 NoBukti from dbumjual where noso= :nobukti", [ "nobukti" => $req->NOBUKTI ]);
    if (count($sudahUM)) {
      return response('Tidak bisa batal otorisasi, uang muka jual ' . $sudahUM[0]->NoBukti . ' sudah dibuat untuk SO ini', 422);
    }
    $res = DB::connection('SML')->update("Update DBSO set OtoPerf='',TglOtoPerf=NULL,UserOtoPerf='' where nobukti= :nobukti", [ "nobukti" => $req->NOBUKTI ]);
    return 1;
  }


// Update DBSO set OtoPerf=1,TglOtoPerf=GetDate(),UserOtoPerf= :username where nobukti= :nobukti





}
