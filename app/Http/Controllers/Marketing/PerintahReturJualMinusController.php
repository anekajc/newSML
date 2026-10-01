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

class PerintahReturJualMinusController extends Controller
{

  public function index(Request $req) {
    $kodemenu = '041051';
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

$tempOutstanding = DB::connection("SML")->select("select month(a.Tanggal) Bulan,YEAR(a.Tanggal) Tahun,a.Tanggal,a.Catatan, a.NOBUKTI,a.KodeCustSupp, d.NAMACUSTSUPP,a.KodeCustSupp,a.IsOtorisasi1, a.OtoUser1, a.TglOto1, a.NoRPJ , a.NOSO, a.IDUser

from dbPRRJual a
left outer join DBCUSTSUPP D on a.KodeCustSupp=d.KODECUSTSUPP
where MONTH(a.TANGGAL) = :bulan and YEAR(a.TANGGAL) = :tahun and a.pmin = 1 and a.IsOtorisasi1 <> 1
",["bulan" => $periode->bulan , "tahun" =>$periode->tahun]);

$tempOutstanding2 = DB::connection("SML")->select("select month(a.Tanggal) Bulan,YEAR(a.Tanggal) Tahun,a.Tanggal,a.Catatan, a.NOBUKTI,a.KodeCustSupp, d.NAMACUSTSUPP,a.KodeCustSupp,a.IsOtorisasi1, a.OtoUser1, a.TglOto1, a.NoRPJ , a.NOSO, a.IDUser

from dbPRRJual a
left outer join DBCUSTSUPP D on a.KodeCustSupp=d.KODECUSTSUPP
where MONTH(a.TANGGAL) = :bulan and YEAR(a.TANGGAL) = :tahun and a.pmin = 1 and a.IsOtorisasi1 = 1
",["bulan" => $periode->bulan , "tahun" =>$periode->tahun]);


  $listGudang = DB::connection('SML')->select("select KODEGDG , NAMA , ALAMAT from dbgudang");
    return view('marketing.perintahreturjualminus' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "tempOutstanding" => $tempOutstanding,
      "tempOutstanding2" => $tempOutstanding2,
      "akses" => $akses,
      "listGudang" => $listGudang
    ]);

  }

  public function loadAll () {


    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $tempOutstanding = DB::connection("SML")->select("select month(a.Tanggal) Bulan,YEAR(a.Tanggal) Tahun,a.Tanggal,a.Catatan, a.NOBUKTI,a.KodeCustSupp, d.NAMACUSTSUPP,a.KodeCustSupp,a.IsOtorisasi1, a.OtoUser1, a.TglOto1, a.NoRPJ , a.NOSO, a.IDUser

    from dbPRRJual a
    left outer join DBCUSTSUPP D on a.KodeCustSupp=d.KODECUSTSUPP
    where MONTH(a.TANGGAL) = :bulan and YEAR(a.TANGGAL) = :tahun and a.pmin = 1 and a.IsOtorisasi1 <> 1
    ",["bulan" => $periode->bulan , "tahun" =>$periode->tahun]);

    $tempOutstanding2 = DB::connection("SML")->select("select month(a.Tanggal) Bulan,YEAR(a.Tanggal) Tahun,a.Tanggal,a.Catatan, a.NOBUKTI,a.KodeCustSupp, d.NAMACUSTSUPP,a.KodeCustSupp,a.IsOtorisasi1, a.OtoUser1, a.TglOto1, a.NoRPJ , a.NOSO, a.IDUser

    from dbPRRJual a
    left outer join DBCUSTSUPP D on a.KodeCustSupp=d.KODECUSTSUPP
    where MONTH(a.TANGGAL) = :bulan and YEAR(a.TANGGAL) = :tahun and a.pmin = 1 and a.IsOtorisasi1 = 1
    ",["bulan" => $periode->bulan , "tahun" =>$periode->tahun]);




    return ["tempOutstanding" => $tempOutstanding,
  "tempOutstanding2" => $tempOutstanding2];
  }


  public function getDetail (Request $req ) {



        $tempOutstanding = DB::connection("SML")->select("select 	month(a.Tanggal) Bulan,YEAR(a.Tanggal) Tahun,a.Tanggal,a.Catatan,
    B.NOBUKTI,A.NOURUT, B.URUT, B.NoINV NoSC, B.UrutINV UrutSC, B.KODEBRG, C.NAMABRG,b.NAMABRG NAMAPRODUK,
            B.QNT,B.QNT1, B.QNT2, B.SAT_1, B.SAT_2, B.ISI, B.NetW, B.GrossW, b.ketdet KetDetail, a.KodeCustSupp, d.NAMACUSTSUPP,a.KodeCustSupp,d.ALAMAT1, a.IsOtorisasi1, a.OtoUser1, a.TglOto1, a.NoRPJ , a.NOSO, a.IDUser, case when b.nosat = 1 then b.SAT_1 else b.SAT_2 end SAT , b.SATX, b.KodeGdg, A.FlagTipe, A.TipePPN,
            b.NoSat, b.nobeli , b.urutbeli, v.qntsisa QntSisaView, v.isi IsiSisaView,  b.FlagKembali,  c.ISI1, c.ISI2
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

  public function listGudang (Request $req) {

    $listData = DB::connection('SML')->select("select KODEGDG , NAMA , ALAMAT from dbgudang");
    return $listData;
  }

  public function listNoInvoice (Request $req) {

    $listData = DB::connection('SML')->select("select distinct A.NOBUKTI, A.TANGGAL, A.NoSO,A.KODEGDG,A.NAMAGDG , a.ppn, a.flagtipe
                from vwBrowsOutInvoicePL A
                where A.KodeCustSupp= :kodecustsupp" , ["kodecustsupp" => $req->kodecustsupp]);
    return $listData;
  }

  public function listBarang (Request $req) {

    $listData = DB::connection('SML')->select("select KodeBrg, NamaBrg, Isi1, Isi2 , SAT1, SAT2 from dbbarang A
                where a.isaktif=1 and A.KodeGrp in ('BJ','JS')
                     and (A.KodeBrg like '%" . $req->input('search') . "%') or (a.NamaBrg like '%" . $req->input('search') . "%')

                    order by a.Kodebrg ASC" );
    return $listData;
  }

  public function listBarangDetail (Request $req) {

    $listData = DB::connection('SML')->select("select KodeBrg, NamaBrg, Isi1, Isi2 , SAT1, SAT2 from dbbarang A
                where a.KodeBrg = :kodebrg" , ["kodebrg" =>  $req->input('kodebrg') ] );
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
    return $res;
  }
  public function spBatalOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbprrjual set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL where nobukti = :nobukti", [ "nobukti" => $req->nobukti]);
    return $res;
  }

  public function spAdd (Request $req) {
    $choice = $req->choice;
    $jmlrecord = $req->jmlrecord;
    $nobukti = $req->nobukti;
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
      1, // pmin 50
      $req->ketdet,
      $req->nobeli,
      $req->urutbeli

    ];

    DB::connection('SML')->statement('exec sp_TransPRRJUAL ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);
    return 1;

  }











}
