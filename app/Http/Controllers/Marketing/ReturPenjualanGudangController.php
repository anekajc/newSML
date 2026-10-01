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

class ReturPenjualanGudangController extends Controller
{

  public function index(Request $req) {
    $kodemenu = '04105';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu , $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }


    // $tulisan = $req->get('tulisan');
    // $menumenus = $req->get('menul0');


    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    // $listData = DB::connection('SML')->select('SELECT * FROM DBMERK');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(4);


//     $tempOutstanding = DB::connection("SML")->select("select 	month(a.Tanggal) Bulan,YEAR(a.Tanggal) Tahun,a.Tanggal,a.Catatan,
// B.NOBUKTI, B.URUT, B.NoINV NoSC, B.UrutINV UrutSC, B.KODEBRG, C.NAMABRG,
//         B.QNT, B.QNT2, B.SAT_1, B.SAT_2, B.ISI, B.NetW, B.GrossW, '' KetDetail, a.KodeCustSupp, d.NAMACUSTSUPP,a.KodeCustSupp, a.IsOtorisasi1, a.OtoUser1, a.TglOto1, a.NoRPJ , a.NOSO, a.IDUser, case when b.nosat = 1 then b.SAT_1 else b.SAT_2 end SAT , b.SATX
// from	dbPRRJualDet B
// left outer join dbBarang C on C.KodeBrg=B.KodeBrg
// left outer join dbPRRJual a on b.NoBukti=a.NoBukti
//
// left outer join DBCUSTSUPP D on a.KodeCustSupp=d.KODECUSTSUPP
//  where MONTH(a.TANGGAL) = :bulan and YEAR(a.TANGGAL) = :tahun
// ",["bulan" => $periode->bulan , "tahun" =>$periode->tahun]);

$tglawalprj = \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d');
$tglakhirprj = \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d');
$tempOutstanding = $this->queryOutstanding($tglawalprj, $tglakhirprj);

$tglawal = $tglawalprj;
$tglakhir = $tglakhirprj;
$tempPenerimaan = $this->queryPenerimaan($tglawal, $tglakhir, 0, 0);

    return view('marketing.returpenjualangudang' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "tempOutstanding" => $tempOutstanding,
      "tempPenerimaan" => $tempPenerimaan,
      "akses" => $akses
    ]);

  }

  // Satu query dipakai bareng oleh index() dan loadAll() buat tabel "Outstanding PRJ"
  // -- dulu difilter fix ke periode aktif (YEAR/MONTH(A.Tanggal)=periode), sekarang
  // pakai filter rentang tanggal spy halaman ini punya filter periode tanggal juga,
  // sama seperti "Transaksi Retur Gudang" (queryPenerimaan()).
  private function queryOutstanding ($tglawal, $tglakhir) {
    return DB::connection("SML")->select("
select distinct d.PPN,A.NoBukti, A.NoUrut, A.Tanggal, A.KodeCustSupp, A.NAMACUSTSUPP, A.NamaKota,
	A.NoRPJ, A.NoSO, A.IDUser,
	A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
	A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi,
    A.ISBATAL, A.USERBATAL, A.TglBatal, A.TipePPn,A.Noinv,A.KodeGdg
from vwTransPRRJual A
Left Outer Join (  SELECT A.NOBUKTI
				 FROM dbPRRJualDet A
				 LEFT OUTER JOIN (select noprrjual,urutprrjual,sum(QNT1)Qnt1,SUM(QNT2) Qnt2
                                                from dbSPBRJualDet  group by noprrjual,urutprrjual
						) B on A.NoBukti=B.NoprRJual AND A.Urut=B.URUTPRRJUAL
				 WHERE ISNULL(A.QNT1,0)-ISNULL(B.Qnt1,0) >0
				 GROUP BY A.NoBukti
				)B ON A.NoBukti=B.NoBukti
left outer join DBCUSTSUPP d on a.KodeCustSupp = d.KODECUSTSUPP
where A.Tanggal between :tglawal and :tglakhir
and A.Kodegdg In(select KodeGdg from DBPemakaiGdg where UserId=:user)
and NeedOtorisasi=0
AND B.NoBukti IS NOT NULL
order by A.NoBukti", ["tglawal" => $tglawal, "tglakhir" => $tglakhir, "user" => \Auth::User()->username]);
  }

  // Satu query dipakai bareng oleh index() dan loadAll() buat tabel "Transaksi Retur
  // Gudang" -- dulu ada 2 salinan query nyaris identik ($tempPenerimaan = belum
  // otorisasi, $tempPenerimaan2 = sudah otorisasi) yang dirender di tab terpisah.
  // Digabung jadi satu tabel dengan filterstatus (status invoice) dan filteroto (status
  // otorisasi) sebagai dua filter independen yang di-AND, port 1:1 dari pola yang sama
  // dipakai PerintahReturJualController::queryOutstanding() (dua dropdown terpisah,
  // bukan satu dropdown gabungan yang saling eksklusif). $tempOutstanding (tab
  // "Outstanding PRJ") sengaja tidak disentuh.
  //   filterstatus: 0 = Semua, 1 = Belum, 2 = Sebagian, 3 = Selesai
  //   filteroto:    0 = Semua, 1 = Belum Otorisasi, 2 = Sudah Otorisasi
  private function queryPenerimaan ($tglawal, $tglakhir, $filterstatus, $filteroto) {
    return DB::connection("SML")->select("
      select * from (
        select a.NoBukti, a.Tanggal, a.NoUrut, a.KodeCustSupp, b.NAMACUSTSUPP, a.IsOtorisasi1, a.OtoUser1, a.TglOto1, a.IDUser, c.Noinv, a.NOSO,
          case when isnull(qntinvr,0) = 0 then 'Belum'
               when isnull(qntinvr,0) < qntspbr then 'Sebagian'
               else 'Selesai' end xstatus
        from dbSPBRJual a
        left outer join DBCUSTSUPP b on a.KodeCustSupp = b.KODECUSTSUPP
        left outer join (select nobukti, noinv, sum(qnt) qntspbr from dbSPBRJualDet group by NoBukti, Noinv) c on a.NoBukti = c.NoBukti
        left outer join (select nospr, sum(qnt) qntinvr from DBINVOICERPJDet group by NOSPR) d on a.NoBukti = d.nospr
        where a.nobukti like '%SPR%' and a.Tanggal between ? and ?
        group by a.NoBukti, a.Tanggal, a.NoUrut, a.KodeCustSupp, b.NAMACUSTSUPP, a.IsOtorisasi1, a.OtoUser1, a.TglOto1, a.IDUser, c.Noinv, a.NOSO, c.qntspbr, d.qntinvr
      ) x
      where (
              (? = 0)
           or (? = 1 and x.xstatus = 'Belum')
           or (? = 2 and x.xstatus = 'Sebagian')
           or (? = 3 and x.xstatus = 'Selesai')
      )
      and (
              (? = 0)
           or (? = 1 and x.IsOtorisasi1 <> 1)
           or (? = 2 and x.IsOtorisasi1 = 1)
      )
    ", [
      $tglawal, $tglakhir,
      $filterstatus, $filterstatus, $filterstatus, $filterstatus,
      $filteroto, $filteroto, $filteroto,
    ]);
  }

  public function loadAll (Request $req) {


    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $tglawalprj = $req->tglawalprj ?: \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d');
    $tglakhirprj = $req->tglakhirprj ?: \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d');
    $tempOutstanding = $this->queryOutstanding($tglawalprj, $tglakhirprj);

    $tglawal = $req->tglawal ?: \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d');
    $tglakhir = $req->tglakhir ?: \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d');
    $filterstatus = $req->filterstatus ?: 0;
    $filteroto = $req->filteroto ?: 0;
    $tempPenerimaan = $this->queryPenerimaan($tglawal, $tglakhir, $filterstatus, $filteroto);

    return [
      "tempOutstanding" => $tempOutstanding,
      "tempPenerimaan" => $tempPenerimaan,
  ];
  }


  public function getDetail (Request $req ) {
    DB::connection('SML')->statement('exec sp_ReTempRJualGdg ?,?,?', [\Auth::User()->username, $req->nobukti, 'SPR']);

    $tempOutstanding = DB::connection("SML")->select("select * from tempoutstanding where IDUser = :username", ["username" => \Auth::User()->username]);


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

  public function getDetailPenerimaan (Request $req) {
    $tempPenerimaan = DB::connection("SML")->select("

    select 	b.* ,C.NAMABRG, '' Jns_Kertas, '' Ukr_Kertas,
        '' KetDetail, d.ALAMAT1, d.NAMACUSTSUPP,  E.CATATAN, e.NoPolKend, e.Sopir, e.NOSO, e.IsOtorisasi1,f.QntSisa
from	dbSPBRJualDet B
left outer join dbBarang C on C.KodeBrg=B.KodeBrg
left outer join dbSPBRJual E on e.NoBukti = b.NoBukti
left outer join DBCUSTSUPP d on d.KODECUSTSUPP = e.KodeCustSupp
left outer join (select b.NoPRRJUAL,b.UrutPRRJUAL,
				case when a.NOSAT=1 then a.QNT1 else a.QNT2 end - sum( ISNULL(case when a.NOSAT=1 then b.QNT1 else b.QNT2 End ,0)) QntSisa
				 from dbPRRJualDet a
				 left outer join dbSPBRJualDet b on a.NoBukti=b.NoPRRJUAL and a.Urut=b.UrutPRRJUAL
				 group by b.NoPRRJUAL,b.UrutPRRJUAL,A.QNT1,a.QNT2,a.NOSAT
				 )F on B.NoPRRJUAL=F.NoPRRJUAL and b.UrutPRRJUAL=f.UrutPRRJUAL
where b.NoBukti = :nobukti
order by B.Urut

    ", ["nobukti" =>$req->nobukti]);

    return $tempPenerimaan;

  }


  public function getNoBukti (Request $req) {

    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    if ($req->ppn == 1) {
      $inisial = DB::connection("SML")->select('select SPR from DBNOMOR');


      $values = [
          $inisial[0]->SPR,
          $periode->bulan,
          $periode->tahun,
          $username,
      ];

      $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);

      return $noBukti;

    } else {
      // $inisial = DB::connection("SML")->select('select SPR from DBNOMOR');


      $values = [
          'SPRN',
          $periode->bulan,
          $periode->tahun,
          $username,
      ];

      $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);

      return $noBukti;
    }

  }


  public function listCustomer (Request $req) {

    $listData = DB::connection('SML')->select("select * from vwBrowsCustSupp where IsCustomer=1");
    return $listData;
  }

  public function listNoInvoice (Request $req) {

    $listData = DB::connection('SML')->select("select distinct A.NOBUKTI, A.TANGGAL, A.NoSO,A.KODEGDG,A.NAMAGDG , a.ppn, a.flagtipe
                from vwBrowsOutInvoicePL A
                where A.KodeCustSupp= :kodecustsupp" , ["kodecustsupp" => $req->kodecustsupp]);
    return $listData;
  }

  public function listBarang (Request $req) {

    $listData = DB::connection('SML')->select("select A.Urut Urut, A.KodeBrg, A.NamaBrg, A.NoSat, A.Satuan,
                case when A.NOSAT=1 then 1 else A.ISI2 end Isi, A.Isi1, A.Isi2,
                A.QntSisa, A.Qnt1Sisa, A.Qnt2Sisa, A.NFix,a.SATX , b.NAMABRG NamaBrgx, a.SAT1 , a.SAT2

                from vwBrowsOutPLPR A
                left join dbbarang b on a.KODEBRG = b.KODEBRG
                where A.NoBukti= :noinvoice

                order by A.Urut" , ["noinvoice" => $req->noinvoice]);
    return $listData;
  }

  public function listNoBeli (Request $req) {

    $listData = DB::connection('SML')->select(" select A.NOBUKTI,A.urut
                       from DBBELIDET A
                       Left Outer join dbPRRJualdet b on a.NOBUKTI=b.nobeli and a.URUT=b.urutbeli
                      left outer join DBPO C on a.NoPO=c.NOBUKTI
                       where A.kodebrg= :kodebrg and B.NoBukti is null
                      and C.Noso= :noso
                       group by A.NOBUKTI,A.Urut" , ["kodebrg" => $req->kodebrg , "noso" => $req->noso]);
    return $listData;
  }

  public function spOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbSPBRJUAL set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal where nobukti = :nobukti", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','RPJ',$req->nobukti,'',0,'dbSPBRJUAL');
    
    return $res;
  }
  public function spBatalOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbSPBRJUAL set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL where nobukti = :nobukti", [ "nobukti" => $req->nobukti]);
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','RPJ',$req->nobukti,$req->pket,0,'dbSPBRJUAL');
    return $res;
  }

  public function spAdd (Request $req) {
    $tempData = $req->input('tempData');
    $username = \Auth::user()->username;

    foreach ($tempData as $d) {
      // code...



      DB::connection("SML")->update('update TempOutstanding set isTerima = 1 , QntTerima = :qntterima , QntReject = :qntreject
where nobukti = :nobukti and urut = :urut and iduser = :username' ,["qntterima" => $d['inputQntTerima'] , "qntreject" => $d['inputQntReject'], "nobukti" => $d['NOBUKTI'] , "urut"=> $d['URUT'] , "username" => \Auth::user()->username ]);
    }

    // exec spinsert
    DB::connection('SML')->statement('exec SP_InsertPRRSPB ?,?,?,?,?,?,?', [
      $req->nobukti,
      $req->nourut,
      $req->noprj,
      \Auth::user()->username,
      -1,
      $req->tanggal,
      'SPR'

    ]);


    return 1;

  }

  public function spKoreksi (Request $req) {
    $choice = $req->choice;
    $nobukti = $req->nobukti;


    $values = [
      $choice,
      $nobukti,
      '',
      '',
      '',
      '',
      '', // nopolkend
      '',
      '',
      '', // noseal 10
      '',
      $req->urut,
      '',
      $req->kodebrg,
      $req->qntTerima,
      $req->qnt1,
      $req->qnt2,
      $req->sat1,
      $req->nosat,
      $req->isi, // 20
      0, // netw
      0,
      \Auth::User()->username,
      0,
      $req->namabrg, // 25
      '', // sopir
      0,
      $req->kodegdg,
      0,
      0, // 30
      '',
      '', // nobatch
      $req->retursupp,
      $req->qntReject,
      $req->qnt1reject,
      $req->qnt2reject,
      $req->sat2

    ];

    DB::connection('SML')->statement('exec sp_TransSPBRJUAL ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);
    return 1;

  }



  public function onChangeHeader (Request $req) {
    $query = 'update dbspbrjual set ' . $req->field . ' = :value where nobukti = :nobukti';



    $res = DB::connection('SML')->update($query, ["value" => $req->value , "nobukti" => $req->nobukti]);
    return $res;

  }











}
