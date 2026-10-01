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

class PerintahReturJualController extends Controller
{

  public function index(Request $req) {
    $kodemenu = '04105';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu , $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    // $listData = DB::connection('SML')->select('SELECT * FROM DBMERK');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(4);


    $tglawal = \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d');
    $tglakhir = \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d');
    $tempOutstanding = $this->queryOutstanding($tglawal, $tglakhir, 0, 0);

    return view('marketing.perintahreturjual' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "tempOutstanding" => $tempOutstanding,
      "akses" => $akses
    ]);

  }

  // Satu query dipakai bareng oleh index() (initial load) dan loadAll() (periode/filter
  // berubah, atau refresh sehabis add/edit/delete) -- dulu ada 2 salinan query yang nyaris
  // identik (satu tanpa xstatus buat index(), satu dengan xstatus buat loadAll()), jadi
  // kolom baru/perubahan skema harus diedit di 2 tempat. filterstatus (status proses SPBR)
  // dan filteroto (status otorisasi) sekarang dua filter independen yang di-AND, bukan satu
  // dropdown gabungan yang saling eksklusif seperti sebelumnya -- user butuh bisa mis.
  // lihat "Belum Otorisasi" + "Selesai" sekaligus.
  //   filterstatus: 0 = Semua, 1 = Belum Diproses, 2 = Proses Sebagian, 3 = Selesai
  //   filteroto:    0 = Semua, 1 = Belum Otorisasi, 2 = Sudah Otorisasi
  private function queryOutstanding ($tglawal, $tglakhir, $filterstatus, $filteroto) {
    return DB::connection("SML")->select("
      select * from (
        select month(a.Tanggal) Bulan, YEAR(a.Tanggal) Tahun, a.Tanggal, a.Catatan, a.NOBUKTI,
          a.KodeCustSupp, d.NAMACUSTSUPP, a.IsOtorisasi1, a.OtoUser1, a.TglOto1, a.NoRPJ, a.NOSO, a.IDUser,
          case when isnull(qntspbr,0) = 0 then 'Belum'
               when isnull(qntspbr,0) < qntprr then 'Sebagian'
               else 'Selesai' end xstatus
        from dbPRRJual a
        left outer join (select nobukti, sum(qnt) qntprr from dbPRRJualDet group by NoBukti) b on b.NoBukti = a.NoBukti
        left outer join (select NoPRRJUAL, sum(qnt) qntspbr from dbSPBRJualdet group by NoPRRJUAL) c on c.NoPRRJUAL = a.NoBukti
        left outer join DBCUSTSUPP D on a.KodeCustSupp = d.KODECUSTSUPP
        where a.TANGGAL between ? and ? and a.pmin = 0
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
    $tglawal = $req->tglawal ?: \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d');
    $tglakhir = $req->tglakhir ?: \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d');
    $filterstatus = $req->filterstatus ?: 0;
    $filteroto = $req->filteroto ?: 0;

    $tempOutstanding = $this->queryOutstanding($tglawal, $tglakhir, $filterstatus, $filteroto);

    return ["tempOutstanding" => $tempOutstanding];
  }


  public function getDetail (Request $req ) {

        $tempOutstanding = DB::connection("SML")->select("select 	month(a.Tanggal) Bulan,YEAR(a.Tanggal) Tahun,a.Tanggal,a.Catatan,
    B.NOBUKTI,A.NOURUT, B.URUT, B.NoINV NoSC, B.UrutINV UrutSC, B.KODEBRG, C.NAMABRG,b.NAMABRG NAMAPRODUK,
            B.QNT,B.QNT1, B.QNT2, B.SAT_1, B.SAT_2, B.ISI, B.NetW, B.GrossW, b.ketdet KetDetail, a.KodeCustSupp, d.NAMACUSTSUPP,a.KodeCustSupp,d.ALAMAT1, a.IsOtorisasi1, a.OtoUser1, a.TglOto1, a.NoRPJ , a.NOSO, a.IDUser, case when b.nosat = 1 then b.SAT_1 else b.SAT_2 end SAT , b.SATX, b.KodeGdg, A.FlagTipe, A.TipePPN,
            b.NoSat, b.nobeli , b.urutbeli, v.qntsisa QntSisaView, v.isi IsiSisaView,  b.FlagKembali,  v.ISI1, v.ISI2
    from	dbPRRJualDet B
    left outer join dbBarang C on C.KodeBrg=B.KodeBrg
    left outer join dbPRRJual a on b.NoBukti=a.NoBukti

    left outer join DBCUSTSUPP D on a.KodeCustSupp=d.KODECUSTSUPP
    left outer join vwBrowsOutPLPR v on v.Nobukti = b.noinv and v.urut = b.urutinv
    where a.Nobukti = :nobukti" , ["nobukti" => $req->nobukti]);
    return $tempOutstanding;
  }


  public function getNoBukti (Request $req) {

    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    if ($req->ppn == 1) {
      $inisial = DB::connection("SML")->select('select PRJ from DBNOMOR');


      $values = [
          $inisial[0]->PRJ,
          $periode->bulan,
          $periode->tahun,
          $username,
      ];

      $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);

      return $noBukti;
 
    } else {
      $values = [
          'PRJN',
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
    $res = DB::connection('SML')->update("update dbprrjual set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal where nobukti = :nobukti", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','PRJ',$req->nobukti,$req->pket,0,'DBPRRJUAL');
   
    return $res;
  }
  public function spBatalOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbprrjual set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL where nobukti = :nobukti", [ "nobukti" => $req->nobukti]);
     $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','PRJ',$req->nobukti,$req->pket,0,'DBPRRJUAL');
    return $res;
  }

  public function spAdd (Request $req) {
    $choice = $req->choice;
    $jmlrecord = $req->jmlrecord;
    $nobukti = $req->nobukti;
    $xurut=0;
  

//  return ["asd" => $nobukti] ;
     $purut = DB::connection('SML')->select('select * from DBPRRJUALDET where Nobukti = :nobukti', ['nobukti' => $nobukti]);
    if ($purut){

        if ($choice=='I' ){

        $purut = DB::connection('SML')->select('select max(urut)+1 xurut from DBPRRJUALDET where Nobukti = :nobukti', ['nobukti' => $nobukti]);
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
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $choice,'PRj',$nobukti,'',$xurut,'DBPRRJUALDET');
    }


    if ($choice == "I" && $jmlrecord == 0) {
      $check = DB::connection('SML')->select('select * from dbprrjual where Nobukti = :nobukti',["nobukti" => $nobukti]);
        if ($check) {
          return 2;
      }
    }

    $values = [
      $choice,
      $nobukti,
      $req->nourut,
      $req->tanggal,
      $req->noinvoice,
      $req->kodecustsupp,
      '', // nopolkend
      '',
      '',
      '', // noseal 10
      $req->catatan,
      $req->urut,
      $req->urutinvoice,
      $req->kodebrg,
      $req->qnt,
      $req->qnt1,
      $req->qnt2,
      $req->sat1,
      $req->nosat,
      $req->isi, // 20
      0, // netw
      0,
      \Auth::User()->username,
      $req->jmlrecord,
      $req->namabrg, // 25
      '', // sopir
      0,
      $req->kodegdg,
      $req->flagtipe,
      $req->ppn, // 30
      $req->noso,
      '', // nobatch
      $req->retursupp,
      $req->sat2,
      '', //satx
      0, // pmin 50
      $req->ketdet,
      $req->nobeli,
      $req->urutbeli

    ];

    DB::connection('SML')->statement('exec sp_TransPRRJUAL ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);
    
    if ($choice !='D'){
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $choice,'PRj',$nobukti,'',$xurut,'DBPRRJUALDET');
    }
    return 1;

  }



  public function spCetak (Request $req)
      {
          $noBukti = $req->input('NOBUKTI');

          $cetak = DB::connection("SML")->select(
              "EXEC sp_CetakPRRJual ?",
              [$noBukti]
          );

          $tempCetak1 = [];
          foreach ($cetak as $p) {
              array_push($tempCetak1, $p);
          }

          return $tempCetak1;
      }

}
