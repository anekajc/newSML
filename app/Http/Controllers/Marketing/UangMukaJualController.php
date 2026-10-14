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

    // Outstanding tanpa filter tanggal (semua SO yang belum dibuatkan uang muka),
    // sama seperti programajcSML.
    $tempOutstanding = $this->queryOutstandingUM(null, null);

    [$tglawal, $tglakhir] = $this->rentangPeriode($periode);
    $tempOutstanding2 = $this->queryUangMuka($tglawal, $tglakhir, 0);

    return view('marketing.uangmukajual' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "tempOutstanding" => $tempOutstanding,
      "tempOutstanding2" => $tempOutstanding2,
      "tglawal" => $tglawal,
      "tglakhir" => $tglakhir,
      "akses" => $akses
    ]);

  }

  // Tanggal awal & akhir bulan periode user. Dulu memakai Carbon::now()->month(),
  // yang memakai tahun berjalan (bukan tahun periode) dan meleset ke bulan
  // berikutnya kalau hari ini tanggal 31.
  private function rentangPeriode ($periode) {
    $awal = \Carbon\Carbon::create((int) $periode->tahun, (int) $periode->bulan, 1);
    return [$awal->format('Y-m-d'), $awal->copy()->endOfMonth()->format('Y-m-d')];
  }

  // Satu query dipakai bareng oleh index() dan loadAll() buat tabel "Outstanding
  // Uang Muka". Di programajcSML tabel ini tidak disaring periode (semua SO yang
  // belum punya UMJ tampil), jadi tanggal kosong = semua. Filter tanggal hanya
  // dipakai kalau user mengisi periode di toolbar.
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
where (:tglawal is null or A.Tanggal >= convert(date, :tglawal2)) and (:tglakhir is null or A.Tanggal < dateadd(day, 1, convert(date, :tglakhir2)))
  and A.OtoPerf=1 and A.nobukti not in (select noso from dbumjual) and
Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                       Case when A.IsOtorisasi3=1 then 1 else 0 end+
                       Case when A.IsOtorisasi4=1 then 1 else 0 end+
                       Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                  else 1
             end As Bit)=0

order by A.NoBukti" , ["tglawal" => $tglawal, "tglawal2" => $tglawal, "tglakhir" => $tglakhir, "tglakhir2" => $tglakhir]);
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
        where A.Tanggal >= convert(date, ?) and A.Tanggal < dateadd(day, 1, convert(date, ?)) and Isnull(A.pBeli,0)=0
      ) x
      where (? = 0)
         or (? = 1 and isnull(x.IsOtorisasi1,0) = 0)
         or (? = 2 and isnull(x.IsOtorisasi1,0) <> 0)
    ", [$tglawal, $tglakhir, $filterumj, $filterumj, $filterumj]);
  }

  public function loadAll (Request $req) {


    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    // Outstanding: tanggal kosong = semua (seperti programajcSML).
    $tglawal1 = $this->tanggalFilter($req->tglawal1) ?: null;
    $tglakhir1 = $this->tanggalFilter($req->tglakhir1) ?: null;
    $tempOutstanding = $this->queryOutstandingUM($tglawal1, $tglakhir1);

    [$defAwal, $defAkhir] = $this->rentangPeriode($periode);
    $tglawal = $this->tanggalFilter($req->tglawal) ?: $defAwal;
    $tglakhir = $this->tanggalFilter($req->tglakhir) ?: $defAkhir;
    $filterumj = (int) ($req->filterumj ?: 0);
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
          // Satu SO hanya boleh punya satu uang muka jual (tabel Outstanding juga
          // menyembunyikan SO yang sudah ada di DBUMJUAL). Mencegah dobel input
          // dari layar yang belum di-refresh / dua user bersamaan.
          $checkSO = DB::connection('SML')->select('select NOBUKTI from dbumjual where NOSO = :noso and isnull(pBeli,0) = 0', ["noso" => $req->noso]);
          if ($checkSO) {
            return 4;
          }
        }

        // Sama seperti Uang Muka Beli (tidak boleh melebihi PO): DPP uang muka tidak
        // boleh melebihi DPP SO. Dibandingkan per rupiah penuh karena DPP form
        // bisa selisih beberapa sen akibat pembulatan persentase.
        if ($choice == 'I' || $choice == 'U') {
          $cekSO = DB::connection('SML')->select('select isnull(TotDPP,0) TotDPP from vwRpDetSO where NoBukti = :noso', ["noso" => $req->noso]);
          if ($cekSO && round((float) $req->dppx) > round((float) $cekSO[0]->TotDPP)) {
            return 6;
          }
        }

        // Log, simpan, dan pengecekan dijalankan dalam satu transaksi: kalau salah
        // satu gagal, semuanya dibatalkan (tidak ada log tanpa data atau sebaliknya).
        return DB::connection('SML')->transaction(function () use ($req, $choice, $nobukti, $jmlrecord, $xurut) {

        // UMJ yang sudah diotorisasi (sudah diposting ke piutang/jurnal) tidak boleh
        // diubah atau dihapus - harus batal otorisasi dulu. Baris dikunci sampai
        // transaksi selesai supaya tidak bentrok dengan otorisasi dari user lain.
        if ($choice == 'U' || $choice == 'D') {
          $cekOto = DB::connection('SML')->select('select isnull(IsOtorisasi1,0) IsOtorisasi1 from dbumjual with (updlock, holdlock) where NOBUKTI = :nobukti', ["nobukti" => $nobukti]);
          if ($cekOto && (int) $cekOto[0]->IsOtorisasi1 === 1) {
            return 3;
          }
          $invoice = $this->invoicePemakaiUMJ($nobukti);
          if ($invoice) {
            return ["status" => 7, "invoice" => $invoice];
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
        });
      }

      // Faktur penjualan (belum batal) yang sudah memotong uang muka dari UMJ ini.
      // Di SML pemotongan uang muka tercatat di DBINVOICEPL (NoSPP = SO dari UMJ,
      // NUangMuka <> 0, seperti view REPORTOUTUMJual) dan DBJUAL.NobuktiUM.
      // UMJ seperti ini tidak boleh dibatal otorisasi / dikoreksi / dihapus.
      private function invoicePemakaiUMJ ($nobukti) {
        $pakai = DB::connection('SML')->select("
          select top 1 X.NOBUKTI from (
            select A.NOBUKTI
            from DBINVOICEPL A
            join DBUMJUAL B on A.NoSPP = B.NOSO and isnull(B.pBeli,0) = 0
            where B.NOBUKTI = ? and isnull(A.NUangMuka,0) <> 0 and isnull(A.IsBatal,0) = 0
            union all
            select J.NOBUKTI from DBJUAL J
            where J.NobuktiUM = ? and isnull(J.ISBATAL,0) = 0
          ) X", [$nobukti, $nobukti]);
        return $pakai ? $pakai[0]->NOBUKTI : null;
      }


      public function spOto (Request $req) {
        // $tanggal = date('Y-m-d H:i:s');
     //    $tanggal = date('Y-m-d H:i:s');
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

        // Update status otorisasi, log, posting piutang & posting jurnal dijalankan
        // dalam satu transaksi: kalau posting gagal, status otorisasi ikut dibatalkan
        // (tidak ada UMJ "sudah otorisasi" tanpa piutang/jurnal).
        return DB::connection('SML')->transaction(function () use ($req, $periode) {

        // Cegah otorisasi dua kali (layar belum di-refresh / dua user bersamaan) -
        // otorisasi ulang akan memposting piutang & jurnal lagi. Baris dikunci
        // sampai transaksi selesai.
        $cekOto = DB::connection('SML')->select('select isnull(IsOtorisasi1,0) IsOtorisasi1 from dbumjual with (updlock, holdlock) where NOBUKTI = :nobukti', ["nobukti" => $req->nobukti]);
        if (!$cekOto) {
          return 5;
        }
        if ((int) $cekOto[0]->IsOtorisasi1 === 1) {
          return 3;
        }

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
        });
      }

      public function spBatalOto (Request $req) {
        // $tanggal = date('Y-m-d H:i:s');
     //    $tanggal = date('Y-m-d H:i:s');
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

        if (trim((string) $req->pket) === '') {
          return 4;
        }

        // Satu transaksi seperti spOto: kalau hapus posting gagal, status
        // otorisasi tidak ikut berubah.
        return DB::connection('SML')->transaction(function () use ($req, $periode) {

        // Cegah batal otorisasi dua kali (layar belum di-refresh / dua user bersamaan).
        $cekOto = DB::connection('SML')->select('select isnull(IsOtorisasi1,0) IsOtorisasi1 from dbumjual with (updlock, holdlock) where NOBUKTI = :nobukti', ["nobukti" => $req->nobukti]);
        if (!$cekOto) {
          return 5;
        }
        if ((int) $cekOto[0]->IsOtorisasi1 !== 1) {
          return 3;
        }

        // Uang muka yang sudah dipotong di faktur penjualan tidak boleh dibatal
        // otorisasi - faktur harus dibatalkan/dikoreksi dulu.
        $invoice = $this->invoicePemakaiUMJ($req->nobukti);
        if ($invoice) {
          return ["status" => 7, "invoice" => $invoice];
        }

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
        });
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
