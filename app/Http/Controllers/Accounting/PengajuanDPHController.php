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

class PengajuanDPHController extends Controller
{

  // Satu rentang tanggal dipakai baik oleh index() (first load) maupun loadAll() (AJAX reload
  // dari picker Periode di toolbar). C.Tanggal dipakai dengan rentang setengah-terbuka
  // [date1, date2+1hari) supaya baris yang timestamp-nya persis di tanggal akhir tidak ikut
  // terbuang, sama seperti PembebananPemakaianController::fetchList() /
  // PengajuanDPHTunaiController::fetchList(). Hasilnya masih di-group per NoBukti (bukan
  // diratakan) karena begitulah view ini sudah memakainya
  // (lastRows = (@json($tempOutstanding)).map(g => g[0])).
  private function fetchList(string $date1, string $date2)
  {
    $rows = DB::connection("SML")->select("
Select
 C.NoBukti, C.Tanggal, C.Valas, 0.00 Nilai,d.DIBAYAR, C.IsOtorisasi1, C.OtoUser1, C.TglOto1,
       C.IsOtorisasi2, C.OtoUser2, C.TglOto2,
       C.IsOtorisasi3, C.OtoUser3, C.TglOto3,
       C.IsOtorisasi4, C.OtoUser4, C.TglOto4,
       C.IsOtorisasi5, C.OtoUser5, C.TglOto5,
       Cast(Case when Case when C.IsOtorisasi1=1 then 1 else 0 end+
                      Case when C.IsOtorisasi2=1 then 1 else 0 end+
                      Case when C.IsOtorisasi3=1 then 1 else 0 end+
                      Case when C.IsOtorisasi4=1 then 1 else 0 end+
                      Case when C.IsOtorisasi5=1 then 1 else 0 end=C.MaxOL then 0
                 else 1
            end As Bit) NeedOtorisasi
        ,C.Userbatal,C.TglBatal,B.KODECUSTSUPP,b.NAMACUSTSUPP,

b.NAMACUSTSUPP,b.KODECUSTSUPP,d.KL
From dbDPHDet a
Left Outer Join DBCUSTSUPP b on a.KODECUSTSUPP=b.KODECUSTSUPP
LEFT OUTER JOIN DBDPH C ON C.NoBukti=a.NoBukti
Left Outer Join (select NoBukti,Sum(Dibayar)dibayar,Sum(KL) KL,KODECUSTSUPP from dbDPHdet Group By NoBukti,KODECUSTSUPP)D on C.NoBukti=D.NoBukti
where
C.Tanggal >= :date1 and C.Tanggal < :date2
AND C.Tipe ='DPH' and A.NOFAKTUR like '%UMB%'
order by C.NoBukti, A.Urut
    ", [
        "date1" => $date1,
        "date2" => date('Y-m-d', strtotime($date2 . ' +1 day')),
    ]);

    $out = [];
    foreach (collect($rows)->groupBy('NoBukti') as $g) {
        array_push($out, $g);
    }
    return $out;
  }

  public function index(Request $req) {
    $kodemenu = '02008';
    $tempListPerkiraan = DB::connection("SML")->select("
    SELECT b.Perkiraan, b.Keterangan, b.Simbol, cast(b.IsPPN as tinyint) IsPPN from dbposthutpiut a
                           left outer join dbperkiraan b on b.perkiraan=a.perkiraan
                           where a.Kode='SLS' and b.perkiraan is not null order by a.Perkiraan


    ");
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu, $req->path());
    // $akses = DBFLMENU::where('USERID', \Auth::user()->username)-> where('L1', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $tempListPerkiraanLB = DB::connection("SML")->select("
    SELECT b.Perkiraan, b.Keterangan, b.Simbol, cast(b.IsPPN as tinyint) IsPPN from dbposthutpiut a
                           left outer join dbperkiraan b on b.perkiraan=a.perkiraan
                           where a.Kode='SLS' and b.perkiraan is not null order by a.Perkiraan


    ");


    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(5);

    $date1 = date('Y-m-01', mktime(0, 0, 0, $periode->bulan, 1, $periode->tahun));
    $date2 = date('Y-m-d');

    $tempOutstanding1 = $this->fetchList($date1, $date2);


    return view('accounting.pengajuandph' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "date1" => $date1,
      "date2" => $date2,
      "tempOutstanding" => $tempOutstanding1,
      "akses" => $akses,
            "tempListPerkiraan" => $tempListPerkiraan,
                  "tempListPerkiraanLB" => $tempListPerkiraanLB,
    ]);

  }

  public function loadAll (Request $req) {

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $date1 = $req->input('date1');
    $date2 = $req->input('date2');
    if (!$date1 || !$date2) {
        $date1 = date('Y-m-01', mktime(0, 0, 0, $periode->bulan, 1, $periode->tahun));
        $date2 = date('Y-m-d');
    }

    return ["tempOutstanding" => $this->fetchList($date1, $date2)];
  }


  public function getListPengajuan (Request $req) {
    $username = \Auth::user()->username;
    $values = [
      $req->tglawal,
      $req->tglakhir,
      $req->valas,
      $req->tipe,
      $username,
      1
    ];



    $res = DB::connection('SML')->update('exec sp_CariHutangJT ?,?,?,?,?,?',$values);

    if ($req->tipelist == 0 ) {
      $tempOutstanding = DB::connection("SML")->select("
      Select * from dbTempHUTPIUTJt
      where IDUSER = :username" , ["username" => $username]);

      return $tempOutstanding;

    } else {
      $tempOutstanding = DB::connection("SML")->select("
      Select * from dbTempHUTPIUTJt
      where IDUSER = :username and kodecustsupp = :kodecustsupp" , ["username" => $username, "kodecustsupp" => $req->kodecustsupp]);

      return $tempOutstanding;
    }



  }

  public function getDetailCetak(Request $req)
  {
      $noBukti = $req->input('NOBUKTI');

      $cetak = DB::connection("SML")->select(
          "EXEC dbo.SP_CETAKDPH ?",
          [$noBukti]
      );

      $tempCetak1 = [];
      foreach ($cetak as $p) {
          array_push($tempCetak1, $p);
      }

      return $tempCetak1;
  }

  public function getDetailKL (Request $req) {


                        $tempOutstanding = DB::connection("SML")->select("
                        select *
                                            from dbKurangBayar

                                            where NoBukti = :nobukti
                                            and NoFaktur = :nofaktur

                        " , ["nobukti" => $req->nobukti , "nofaktur" => $req->nofaktur]);
                    return $tempOutstanding;
  }

  public function getDetailKLEdit (Request $req) {


                        $tempOutstanding = DB::connection("SML")->select("
                        select a.* , b.Keterangan , a.nilaik inputKL from dbkurangbayar a
left outer join DBPERKIRAAN b on a.Perkiraan = b.Perkiraan

                                            where a.NoBukti = :nobukti
                                            and a.NoFaktur = :nofaktur

                        " , ["nobukti" => $req->nobukti , "nofaktur" => $req->nofaktur]);
                    return $tempOutstanding;
  }


    public function spDeleteKLEdit (Request $req) {


                          $tempOutstanding = DB::connection("SML")->update("
                          delete  dbKurangBayar where NoBukti= :nobukti and Urut= :urut and Nofaktur = :nofaktur

                          " , ["nobukti" => $req->nobukti, "urut" => $req->urut , "nofaktur" => $req->nofaktur]);
                      return $tempOutstanding;
    }

    public function spUpdateDPHDet (Request $req) {
      $tempOutstanding = DB::connection("SML")->update("
      update DBDPHDET set DIBAYAR = :dibayar , NoinvoiceBeli = :noinvoice , TglInvoiceBeli = :tanggalinvoice
    where nobukti = :nobukti and urut = :urut

      " , ["dibayar" => $req->dibayar, "noinvoice" => $req->noinvoice,"tanggalinvoice" => $req->tanggalinvoice, "nobukti" => $req->nobukti,"urut" => $req->urut  ]);

    }




    public function spAddKLEdit (Request $req) {
      // $check = DB::connection('SML')->select('select * from dbdphdet where Nobukti = :nobukti and nofaktur = :nofaktur',["nobukti" => $req->nobukti , "nofaktur" => $req->nofaktur]);
      //   if ($check) {
      //     return 4;
      // }
      DB::connection('SML')->statement('exec Sp_dbKurangBayar ?,?,?,?,?,?,?', [
            'I' ,// @Choice char(1),
            $req->nobukti, // @NoBukti Varchar(30),
            $req->perkiraan,// @Perkiraan Varchar(20),
            0,// @Urut Int,
            $req->inputKL,// @NilaiK Numeric(18,2),
            $req->kodecustsupp,// @KodeCustSupp Varchar(15),
            $req->nofaktur,// @NoFaktur Varchar(30)


      ]);
    }


  public function getDetail (Request $req ) {



        $tempOutstanding = DB::connection("SML")->select("
        declare @NoBukti varchar(30)

        select @NoBukti= :nobukti

        Select 	A.IsOtorisasi1, A.NoBukti, A.NoUrut, A.Tanggal, B.KodeCustSupp, C.NamaCustSupp,B2.KL ,B.LB,Isnull(Case When a.Valas='IDR' Then HP.Kredit else HP.KreditD end,0)NilaiNota,
                A.Valas,Penagih,A.Tipe,b.NoFaktur,dibayar,Case When a.Valas='IDR' Then HP.Kredit else HP.KreditD end Nilai,b.Perkiraan,b1.Keterangan ,B.urut
                ,B.NoinvoiceBeli Noinvoice,case when year(b.tglinvoicebeli)=1899 then null else b.TglInvoicebeli End TglInv,Isnull(B.PCopy,0) PCopy
        From dbDPH A
        Left Outer join dbDPHDet B on B.NoBukti=A.NoBukti
        Left Outer Join (select SUM(NilaiK)KL,NoBukti,KodeCustSupp,NOfaktur
                        from dbKurangBayar Group By NoBukti,KodeCustSupp,NOfaktur
                        )B2 On B2.NoBukti=A.NoBukti and B2.KodeCustSupp=B.KodeCustSupp and B.Nofaktur=B2.Nofaktur/*and B.Urut=b2.Urut*/
        Left Outer Join dbPerkiraan B1 On B1.Perkiraan=B.Perkiraan
        Left Outer Join dbCustSupp C on C.KodeCustSupp=B.KodeCustSupp
        Left Outer Join (select NoFaktur,SUM(Kredit-Debet)Kredit,Sum(KreditD-DebetD)KreditD from DBHUTPIUT  where Tipe='HT' and TipeTrans in('T','AWL') Group By NoFaktur) HP on HP.NoFaktur=b.NoFaktur
        Left Outer join dbValas D on D.KodeVls=A.Valas
        where	A.NoBukti=@NoBukti
        order by B.Urut

        " , ["nobukti" => $req->nobukti]);
    return $tempOutstanding;
  }


  public function getNoBukti (Request $req) {
    // return 1;
    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $kode = $req->kode;
    $inisial = DB::connection("SML")->select('select ' . $kode . ' from DBNOMOR');

    $values = [
        $inisial[0]->$kode,
        $periode[0]->bulan,
        $periode[0]->tahun,
        $username,
        $req->simbol
    ];

    $noBukti = DB::connection('SML')->select('exec SP_IsiNobuktiSimbol ?,?,?,?,?',$values);

    return $noBukti;
  }







  public function spOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbdph set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal , tglbatal = NULL, userbatal = '' where nobukti = :nobukti", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
    return $res;
  }
  public function spBatalOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbdph set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL , tglbatal = :tanggal, userbatal = :username where nobukti = :nobukti", [ "nobukti" => $req->nobukti , "username" => \Auth::user()->username , "tanggal" => $tanggal ]);
    return $res;
  }

  public function spAdd (Request $req) {
    // return 'asdw';
    $username = \Auth::user()->username;
    $listData = $req->tempData;
    $listDataKL = $req->tempDataKL ?? [] ;
    $jmlrecord = $req->jmlrecord;

    // return $listData;

    if ($jmlrecord == 0 ) {
      $check = DB::connection('SML')->select('select * from DBDPH where Nobukti = :nobukti',["nobukti" => $req->nobukti]);
        if ($check) {
          return 2;
      }
    }

    foreach ($listData as $d)  {
      $check = DB::connection('SML')->select('select * from dbdphdet where Nobukti = :nobukti and nofaktur = :nofaktur',["nobukti" => $req->nobukti , "nofaktur" => $d['NoFaktur']]);
        if ($check) {
          return 4;
      }





    }

      foreach ($listData as $d)  {

        // kalo 0 ambil dari kredit
        // kalo ada isi kredit - jmldibayar
        // return [$req->choice,
        // $req->nobukti,
        // $req->nourut,
        // $req->tanggal ,
        // $req->valas,
        // '',
        // $req->tipe ,
        // 0 ,
        // $d['KodeCustSupp'],
        // $d['NoFaktur'] ,
        // $d['diBayar'] ,
        // NULL,
        // -1 ,
        // $d['Perkiraan'],
        // $d['tempKL'] ,
        // $d['LB'] ,
        // 0 ,
        // $jmlrecord ,
        // $d['noinvoice'] ? $d['noinvoice'] : '',
        // $d['tanggalinvoice'] ? $d['tanggalinvoice'] : '' ,
        // $d['pCopy'],
        // 1,
        // $username];

        DB::connection('SML')->statement('exec sp_dph ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
          $req->choice,
          $req->nobukti,
          $req->nourut,
          $req->tanggal ,
          $req->valas,
          '',
          $req->tipe ,
          0 ,
          $d['KodeCustSupp'],
          $d['NoFaktur'] ,
          $d['diBayar'] ,
          NULL,
          -1 ,
          $d['Perkiraan'],
          $d['tempKL'] ,
          $d['LB'] ,
          0 ,
          $jmlrecord ,
          $d['noinvoice'] ? $d['noinvoice'] : '',
          $d['tanggalinvoice'] ? $d['tanggalinvoice'] : '' ,
          $d['pCopy'],
          1,
          $username

        ]);

        $jmlrecord = 1;



      }

      foreach ($listDataKL as $d)  {


        DB::connection('SML')->statement('exec Sp_dbKurangBayar ?,?,?,?,?,?,?', [
              'I' ,// @Choice char(1),
              $req->nobukti, // @NoBukti Varchar(30),
              $d['inputPerkiraanKL'],// @Perkiraan Varchar(20),
              0,// @Urut Int,
              $d['inputKL'],// @NilaiK Numeric(18,2),
              $d['KodeCustSupp'],// @KodeCustSupp Varchar(15),
              $d['NoFaktur'],// @NoFaktur Varchar(30)


        ]);



      }

      return 1;

  }

  public function spkoreksi (Request $req) {

    $username = \Auth::user()->username;
    $jmlrecord = $req->jmlrecord;

//     select * from dbdph where NoBukti like '%0525%'
//


    DB::connection('SML')->statement('exec sp_dph ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
      $req->choice,
      $req->nobukti,
      $req->nourut,
      $req->tanggal ,
      $req->valas,
      '',
      $req->tipe ,
      $req->urut,
      $req->kodecustsupp, //
      $req->nofaktur, //
      $req->dibayar, //
      NULL,
      -1 ,
      $req->perkiraan, //
      $req->kl, //
      $req->lb, //
      0 ,
      0 , //
      $req->noinvoice ? $req->noinvoice : '', //
      $req->tglinvoice, //
      $req->pcopy, //
      1,
      $username

    ]);

    return 1;


  }




}
