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
use App\Models\VwPPL;

// use App\Http\Controllers\NewMenuController;

class UangMukaJualController extends Controller
{

  public function index(Request $req) {
    $kodemenu = '041013';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu , $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }


    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    // $listData = DB::connection('SML')->select('SELECT * FROM DBMERK');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(4);

    $tglawal1 = \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d');
    $tglakhir1 = \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d');
    $tempOutstanding = $this->queryOutstandingUM($tglawal1, $tglakhir1);

    $tglawal = \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d');
    $tglakhir = \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d');
    $tempOutstanding2 = $this->queryUangMuka($tglawal, $tglakhir, 0);

    return view('marketing.uangmukajual' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "tempOutstanding" => $tempOutstanding,
      "tempOutstanding2" => $tempOutstanding2,
      "akses" => $akses
    ]);

  }

  // Satu query dipakai bareng oleh index() dan loadAll() buat tabel "Outstanding
  // Uang Muka" -- sebelumnya periode-nya dead code (filter year/month di WHERE
  // dikomentari, jadi query ini selalu menampilkan SEMUA baris tanpa peduli
  // periode). Sekarang beneran menyaring pakai rentang tanggal, port 1:1 dari
  // pola periode-picker milik so.blade.php/tab lain di halaman ini (queryUangMuka).
  private function queryOutstandingUM ($tglawal, $tglakhir) {
    return DB::connection("SML")->select("
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
             end As Bit) NeedOtorisasi
        ,Isnull(A.Isbatal,0)IsBatal,A.userbatal,A.Tglbatal,Isnull(A.TipePPN,0) TipePPN,
        A.KODESLS,K.Nama NAMASLS,A.KodePF,L.Nama NAMAPIC,A.NoPesanan,A.catatan,A.OtoPerf,A.userOtoPerf,A.tglOtoPerf ,A.DP
From dbSO A
Left Outer Join dbCustSupp C on c.KodeCustsupp=a.KodeCust
Left Outer Join vwRpDetSO I on I.NoBukti=A.NoBukti
Left Outer Join dbAlamatCust J on J.KodeCustsupp=A.KodeCust and J.Nomor=A.NoAlamatKirim
Left outer join dbKaryawan K ON A.KODESLS=K.KeyNIK
LEFT OUTER JOIN DBPICCUSTSUPP L ON A.KodePF=L.KODEPIC  and A.kodecust=L.kodecustsupp
where A.Tanggal between :tglawal and :tglakhir and A.OtoPerf=1 and A.nobukti not in (select noso from dbumjual) and
Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                       Case when A.IsOtorisasi3=1 then 1 else 0 end+
                       Case when A.IsOtorisasi4=1 then 1 else 0 end+
                       Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                  else 1
             end As Bit)=0

order by A.NoBukti" , ["tglawal" => $tglawal, "tglakhir" => $tglakhir]);
  }

  // Satu query dipakai bareng oleh index() dan loadAll() buat tabel "Uang Muka
  // Otorisasi" -- dulu tabel2 (Belum, tombol Edit/Otorisasi/Delete) dan tabel3
  // (Sudah, tombol Batal Otorisasi/Print) adalah 2 tab terpisah dengan query
  // nyaris identik (cuma beda IsOtorisasi1=0/1). Digabung jadi satu dengan
  // filterumj yang menyaring di server, port 1:1 dari pola queryOutstanding()
  // milik PerintahReturJualController. Tabel "Outstanding Uang Muka" (tabel,
  // bukan konsep otorisasi) sengaja tidak disentuh.
  //   0 = Semua, 1 = Belum Otorisasi, 2 = Sudah Otorisasi
  private function queryUangMuka ($tglawal, $tglakhir, $filterumj) {
    return DB::connection("SML")->select("
      select * from (
        select A.NOBUKTI,A.TANGGAL,A.NOSO,A.VALAS,A.KURS,A.DPP,A.PPN,A.PERSEN,C.NamaCustSupp,
          Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                             Case when A.IsOtorisasi2=1 then 1 else 0 end+
                             Case when A.IsOtorisasi3=1 then 1 else 0 end+
                             Case when A.IsOtorisasi4=1 then 1 else 0 end+
                             Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                        else 1
                   end As Bit) NeedOtorisasi ,
          A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
          A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
          A.IsOtorisasi3, A.OtoUser3, A.TglOto3,
          A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
          A.IsOtorisasi5, A.OtoUser5, A.TglOto5
        from DBUMJUAL A
        Left Outer Join DBSO B on A.Noso=B.Nobukti
        Left Outer join DBCustSupp C on B.KodeCUst=C.KodeCUstSUpp
        where A.Tanggal between ? and ? and Isnull(A.pBeli,0)=0
      ) x
      where (? = 0)
         or (? = 1 and isnull(x.IsOtorisasi1,0) = 0)
         or (? = 2 and isnull(x.IsOtorisasi1,0) <> 0)
    ", [$tglawal, $tglakhir, $filterumj, $filterumj, $filterumj]);
  }

  public function loadAll (Request $req) {


    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $tglawal1 = $req->tglawal1 ?: \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d');
    $tglakhir1 = $req->tglakhir1 ?: \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d');
    $tempOutstanding = $this->queryOutstandingUM($tglawal1, $tglakhir1);

    $tglawal = $req->tglawal ?: \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d');
    $tglakhir = $req->tglakhir ?: \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d');
    $filterumj = $req->filterumj ?: 0;
    $tempOutstanding2 = $this->queryUangMuka($tglawal, $tglakhir, $filterumj);

    return ["tempOutstanding" => $tempOutstanding, "tempOutstanding2" => $tempOutstanding2];
  }

  public function getNoBukti (Request $req) {

    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $inisial = DB::connection("SML")->select('select UMJ from DBNOMOR');

    $values = [
        $inisial[0]->UMJ,
        $periode->bulan,
        $periode->tahun,
        $username,
        // $periode
        // $periode
    ];
    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
    return $noBukti;
  }


    public function getDetail (Request $req) {
      $nobukti = $req->nobukti;
      $periode = app('App\Http\Controllers\GlobalController')->getPeriode();




      $header = DB::connection("SML")->select("
      declare @Tahun int, @Bulan int,@idUSER VARCHAR(20)

  select @Tahun= :tahun, @Bulan=:bulan

  Select 	a.kodevls, a.kurs ,A.NoBukti, A.NoUrut, A.Tanggal, A.KodeCust, C.NamaCustSupp NamaCust,
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
  where
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

      return ["header" => $header];
    }

    public function getDetailUMJ (Request $req) {
      $nobukti = $req->nobukti;
      $noso = $req->noso;
      $periode = app('App\Http\Controllers\GlobalController')->getPeriode();


      $header = DB::connection("SML")->select("
      declare @Tahun int, @Bulan int,@idUSER VARCHAR(20)

  select @Tahun= :tahun, @Bulan=:bulan

  Select 	a.kodevls, a.kurs ,A.NoBukti, A.NoUrut, A.Tanggal, A.KodeCust, C.NamaCustSupp NamaCust,
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
  where A.NoBukti= :nobukti




  order by A.NoBukti
  " , [ "tahun" =>$periode->tahun , "bulan" => $periode->bulan ,  "nobukti" => $noso]);

  $detail = DB::connection("SML")->select("declare @Tahun int, @Bulan int

  select @Tahun= :tahun, @Bulan= :bulan

  select A.NOBUKTI,A.TANGGAL,A.NOSO,A.VALAS,A.KURS,A.DPP,A.PPN,A.PERSEN,A.SUBTOTAL,C.NamaCustSupp,
  Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                         Case when A.IsOtorisasi2=1 then 1 else 0 end+
                         Case when A.IsOtorisasi3=1 then 1 else 0 end+
                         Case when A.IsOtorisasi4=1 then 1 else 0 end+
                         Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                    else 1
               end As Bit) NeedOtorisasi ,
               A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
          A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
          A.IsOtorisasi3, A.OtoUser3, A.TglOto3,
          A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
          A.IsOtorisasi5, A.OtoUser5, A.TglOto5
  from DBUMJUAL A
  Left Outer Join DBSO B on A.Noso=B.Nobukti
  Left Outer join DBCustSupp C on B.KodeCUst=C.KodeCUstSUpp
  where a.nobukti = :nobukti
  and Isnull(A.pBeli,0)=0" , [ "tahun" =>$periode->tahun , "bulan" => $periode->bulan ,  "nobukti" => $nobukti]);


      return ["header" => $header , "detail" => $detail];
    }


      public function spAdd (Request $req) {
        $choice = $req->choice;
        $jmlrecord = $req->jmlrecord;
        $nobukti = $req->nobukti;
	$xurut=0;
        if ($choice == "I" && $jmlrecord == 0) {
          $check = DB::connection('SML')->select('select * from dbumjual where Nobukti = :nobukti',["nobukti" => $nobukti]);
            if ($check) {
              return 2;
          }
        }

		
	if ($choice =='D') {	
		 $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData($choice,'UMJ',$nobukti,'',0,'DBUMJUAL');
	}


          // code...
          $values = [
            $choice,
            $nobukti,
            $req->nourut,
            $req->noso,
            $req->dppx,
            $req->ppnx,
            $req->presentase,
            $req->tanggal,
            $req->valas,
            $req->kurs,
            $req->subtotal,
            $req->maxol,
            $req->pbeli,
            $jmlrecord,
            $req->flagtipe,
            $req->tglest,
            $req->bayar

          ];
          DB::connection('SML')->statement('exec sp_umj ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);
	 if ($choice !='D') {	
    // return [$choice,'UMJ',$nobukti,'',$xurut,'DBUMJUAL'];
		 $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingDataTrans($choice,'UMJ',$nobukti,'',$xurut,'DBUMJUAL');
	}
        return 1;
      }


      public function spOto (Request $req) {
        // $tanggal = date('Y-m-d H:i:s');
     //    $tanggal = date('Y-m-d H:i:s');
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

        $res = DB::connection('SML')->update("Update DBUMJUAL set TGLOTO1 = GetDate() ,  ISOTORISASI1 = 1,  OTOUSER1 = :username where nobukti= :nobukti", ["username" => \Auth::user()->username , "nobukti" => $req->nobukti ]);
          $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData('oto','UMJ',$req->nobukti,'',0,'DBUMJUAL');
	$values = [
               '',
               'DBUMJUAL',
               $periode->bulan,
               $periode->tahun,
               $req->nobukti,
               1
          ];
          DB::connection('SML')->statement('exec sp_ProsesPostingHutPiut ?,?,?,?,?,?', $values);
          DB::connection('SML')->statement('exec sp_ProsesPostingJurnalOto ?,?,?,?,?,?', $values);


        return 1;
      }

      public function spBatalOto (Request $req) {
        // $tanggal = date('Y-m-d H:i:s');
     //    $tanggal = date('Y-m-d H:i:s');
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

        $res = DB::connection('SML')->update("Update DBUMJUAL set TGLOTO1 = NULL ,  ISOTORISASI1 = 0,  OTOUSER1 = '' where nobukti= :nobukti", [ "nobukti" => $req->nobukti ]);
	 $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','UMJ',$req->nobukti,$req->pket,0,'DBUMJUAL');
     $values = [
      '',
      'DBUMJUAL',
      $periode->bulan,
      $periode->tahun,
      $req->nobukti,
      0
    ];
    DB::connection('SML')->statement('exec sp_ProsesPostingHutPiut ?,?,?,?,?,?', $values);
    DB::connection('SML')->statement('exec sp_ProsesPostingJurnalOto ?,?,?,?,?,?', $values);

        return 1;
      }





      public function spCetak (Request $req)
  {
      $noBukti = $req->input('NOBUKTI');

      $cetak = DB::connection("SML")->select(
          "EXEC SP_CetakUMJ ?",
          [$noBukti]
      );

      $tempCetak1 = [];
      foreach ($cetak as $p) {
          array_push($tempCetak1, $p);
      }

      return $tempCetak1;
  }


}
