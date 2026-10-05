<?php

namespace App\Http\Controllers\Purchasing;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Models\VWOutRBeli;
use App\Models\DBFLMENU;


use App\Models\VWRBeliOut;


class ReturPembelianGudangController extends Controller
{


  public function index (Request $req) {

    $kodemenu = '030502';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu, $req->path);
    // $akses = DBFLMENU::where('USERID', \Auth::user()->username)-> where('L1', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

  $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(3);

    $tempoutstanding = DB::connection("SML")->select("
    select A.NOBUKTI,C.TANGGAL,A.KODESUPP,D.NAMACUSTSUPP, :bulan Bulan,:tahun tahun
  ,A.SATUAN,case when A.NOSAT=1 then Isnull(A.Qnt1,0)-ISnull(B.Qnt1,0)
                 when A.NOSAT=2 then Isnull(A.Qnt2,0)-ISnull(B.Qnt2,0)
                 when A.NOSAT=3 then Isnull(A.Qnt2,0)-ISnull(B.Qnt2,0)
        End QntOUt,D.PPN FlagTipe,B.Qnt1,B.Qnt2,A.NOBUKTI,A.URUT ,A.Kodebrg,E.namaBrg
        ,A.noPBL,A.UrutPBL ,Case when F.ppn=0 then 'None'
        when F.ppn=1 then 'Exclude'
        when F.ppn=2 then 'Include'
        End xtipe
  from DBPRRBELIDET A
  left outer join (select NOPRB,UrutPRB,SUM(Qnt1) Qnt1,SUM(Qnt2) Qnt2
  				 from DBRBELIDET Group By NOPRB,UrutPRB
  				 ) b on A.NOBUKTI=B.NOPRB and A.URUT=B.UrutPRB
  Left Outer join DBPRRBELI C on a.NOBUKTI=C.NOBUKTI
  left outer join DBCUSTSUPP D on A.KODESUPP=D.KODECUSTSUPP
  Left Outer join Dbbarang E on A.kodebrg=E.Kodebrg
  Left Outer Join DbBelidet F on A.nopbl=F.nobukti and A.urutPBL=F.urut
  where 	(Isnull(A.Qnt,0) * A.isi)-ISnull(B.Qnt1,0) >0
  and
         Cast(Case when Case when C.IsOtorisasi1=1 then 1 else 0 end+
                         Case when C.IsOtorisasi2=1 then 1 else 0 end+
                         Case when C.IsOtorisasi3=1 then 1 else 0 end+
                         Case when C.IsOtorisasi4=1 then 1 else 0 end+
                         Case when C.IsOtorisasi5=1 then 1 else 0 end=C.MaxOL then 0
                    else 1
               end As Bit) =0



    " , [ "tahun" =>$periode->tahun , "bulan" => $periode->bulan]);


    $collection1 = collect($tempoutstanding)->groupBy('KODESUPP');
    $outstandingArray = [];
    foreach ($collection1 as $p) {
      // code...
      array_push($outstandingArray, $p);
    }


        $tempPenerimaan = DB::connection("SML")->select("


    declare @Tahun int, @Bulan int

    select @Tahun=:tahun, @Bulan=:bulan

    Select 	A.NoBukti, A.NOURUT, A.Tanggal, A.TGLJATUHTEMPO, A.KODESUPP,
            C.NamaCustSupp, C.Alamat1, C.Alamat2, C.Kota,
            C.Alamat1+Char(13)+C.Alamat2+Char(13)+C.kota Alamat,
            A.NOBELI, A.KodeGdg, A.KODEEXP, A.HANDLING, A.KETERANGAN, A.FAKTURSUPP,
            A.KODEVLS, A.KURS, A.PPN, A.TIPEBAYAR, A.HARI, A.TipeDisc, A.DISC, A.DISCRP,
            A.NILAIPOT, A.NILAIDPP, A.NILAIPPN, A.NILAINET, A.ISCETAK, A.NilaiCetak,
            A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
    	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
    	A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.MAXOL,
            Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                           Case when A.IsOtorisasi2=1 then 1 else 0 end+
                           Case when A.IsOtorisasi3=1 then 1 else 0 end+
                           Case when A.IsOtorisasi4=1 then 1 else 0 end+
                           Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                      else 1
                 end As Bit) NeedOtorisasi
            ,Isnull(A.isbatal,0) IsBatal,A.Userbatal,A.Tglbatal
    From dbRBeli A
    Left Outer Join dbCustSupp C on C.KodeCustSupp=A.KodeSupp
    Left Outer Join dbGudang F on F.KodeGdg=A.KodeGdg
    where year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan
    order by A.NoBukti



        " , [ "tahun" =>$periode->tahun , "bulan" => $periode->bulan]);


        $penerimaanArray = [];
        foreach ($tempPenerimaan as $p) {
          array_push($penerimaanArray, $p);
        }



    return view('purchasing.returpembeliangudang' , [
      "periode" => $periode,
      "menul0" => $menul0,
      "tempoutstanding" =>$tempoutstanding,
      "outstandingArray" => $outstandingArray,
      "penerimaanArray" => $penerimaanArray,
      "akses" => $akses
    ]);
  }


  public function getNoBukti (Request $req) {

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    // $inisial = DB::connection("SML")->select('select PBL from DBNOMOR');
    $inisial = DB::connection("SML")->select('select rpb from DBNOMOR');
    $username = \Auth::user()->username;
    // return [$periode->bulan,$inisial[0]->PBL,$username];
    $values = [
        $inisial[0]->rpb,
        $periode->bulan,
        $periode->tahun,
        $username
    ];
    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
    return $noBukti;
  }

  public function getDetailOutstanding (Request $req) {

    // $detailOutstanding = VWOutRBeli::all()->where('NOBUKTI', $req->input('NOBUKTI'))->sortBy('URUT');
$periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $detailOutstanding = DB::connection("SML")->select("
    select A.NOBUKTI,C.TANGGAL,A.KODESUPP,D.NAMACUSTSUPP, :bulan Bulan,:tahun tahun ,  A.Kodegdg
  ,A.SATUAN,case when A.NOSAT=1 then Isnull(A.Qnt1,0)-ISnull(B.Qnt1,0)
                 when A.NOSAT=2 then Isnull(A.Qnt2,0)-ISnull(B.Qnt2,0)
                 when A.NOSAT=3 then Isnull(A.Qnt2,0)-ISnull(B.Qnt2,0)
        End QntOUt,D.PPN FlagTipe,B.Qnt1,B.Qnt2,A.NOBUKTI,A.URUT ,A.Kodebrg,E.namaBrg
        ,A.noPBL,A.UrutPBL ,Case when F.ppn=0 then 'None'
        when F.ppn=1 then 'Exclude'
        when F.ppn=2 then 'Include'
        End xtipe,A.NOSAT,A.Kodegdg,A.Urut
  from DBPRRBELIDET A
  left outer join (select NOPRB,UrutPRB,SUM(Qnt1) Qnt1,SUM(Qnt2) Qnt2
           from DBRBELIDET Group By NOPRB,UrutPRB
           ) b on A.NOBUKTI=B.NOPRB and A.URUT=B.UrutPRB
  Left Outer join DBPRRBELI C on a.NOBUKTI=C.NOBUKTI
  left outer join DBCUSTSUPP D on A.KODESUPP=D.KODECUSTSUPP
  Left Outer join Dbbarang E on A.kodebrg=E.Kodebrg
  Left Outer Join DbBelidet F on A.nopbl=F.nobukti and A.urutPBL=F.urut
  where 	(Isnull(A.Qnt,0) * A.isi)-ISnull(B.Qnt1,0) >0
  and
         Cast(Case when Case when C.IsOtorisasi1=1 then 1 else 0 end+
                         Case when C.IsOtorisasi2=1 then 1 else 0 end+
                         Case when C.IsOtorisasi3=1 then 1 else 0 end+
                         Case when C.IsOtorisasi4=1 then 1 else 0 end+
                         Case when C.IsOtorisasi5=1 then 1 else 0 end=C.MaxOL then 0
                    else 1
               end As Bit) =0

AND a.KODESUPP=:nobukti

    " , [ "tahun" =>$periode->tahun , "bulan" => $periode->bulan, "nobukti" => $req->input('NOBUKTI')]);



    $tempOutstanding = [];
    foreach ($detailOutstanding as $do) {
      // code...
      array_push($tempOutstanding,$do);
    }
    return $tempOutstanding;

  }

  public function getDetailPenerimaan (Request $req) {

    $detailPenerimaan = VWRBeliOut::all()->where('NOBUKTI',$req->input('NOBUKTI') )->sortBy('URUT');
    $tempPenerimaan = [];
    foreach ($detailPenerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }
    return $tempPenerimaan;
  }

  public function getKoreksiAddList (Request $req) {
    $tempPenerimaan = VWRBeliOut::where('NOBUKTI' , $req->input('norpb'))->select('KODEBRG')->get()->toArray();

    $tempAddList = VWOutRBeli::select()->where('NOBUKTI', $req->input('noout'))->whereNotIn('KODEBRG', $tempPenerimaan)->get();

    return $tempAddList;
  }

  public function spAdd (Request $req) {
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $date = $req->input('inputDate');
    $username = \Auth::user()->username;
    $data = $req->input('tempData');
    $nourut = $req->input('nourut');
    $noout = $req->input('noout');
    $norpb = $req->input('norpb');

    $checkSaldo = 0;
    // return [$data,$date,$username,$nourut,$noout,$norpb];

    $check = DB::connection('SML')->select('select * from dbRBeli where NOBUKTI = :nobukti',["nobukti" => $norpb]);
    if ($check) {
      return 2;
    }

    DB::connection('SML')->statement('delete	TempOutstanding where IDUser = :idUser',['idUser' => $username ]);




    foreach ($data as $d) {

      $check2 = DB::connection('SML')->select('exec Sp_CekStockAkhir ?,?,?,?',[$d['NOSAT'], $date, $d['Kodegdg'],  $d['Kodebrg']]);
      // array_push($checkSaldo, ["input" => (float)$d['inputQntTerima']    , "saldoqnt" => (float)$check2[0]->SALDOQNT ]);
      if ($check2) {
        if ((int)$d['inputQntTerima'] > (int)$check2[0]->SALDOQNT ) {
          // return 'a';
          $checkSaldo = 1;
        }
      } else {
        // return 'b';
        $checkSaldo = 1;
      }

      $values = [
        $username,
        $noout,
        $periode->tahun,
        $periode->bulan,
        0,
        'PRB',
        $d['KODESUPP'],
        $d['noPBL'],
        $d['UrutPBL'],
        $d['inputQntTerima'],
        $d['Urut']
      ];

      // DB::connection("SML")->statement('exec sp_RefreshTempOutRbeli ?,?,?,?,?,?,?',$values);
        DB::connection("SML")->statement('exec sp_TempOutPRRBelitrade ?,?,?,?,?,?,?,?,?,?,?',$values);


      if ($checkSaldo) {
        // code...
        return 3;
      }

    }

    $tempValues = [$norpb, $nourut,$date,$data[0]['Kodegdg'], $noout,"","",0,0, $username];
    DB::connection('SML')->statement('exec sp_InsertOutRbeli ?,?,?,?,?,?,?,?,?,?', $tempValues);
    return 1;



  }

  public function loadAll () {

    $kodemenu = '03008';
    $akses = DBFLMENU::where('USERID', \Auth::user()->username)-> where('L1', $kodemenu)->first();
  $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
  $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0();

      $tempoutstanding = DB::connection("SML")->select("
      select A.NOBUKTI,C.TANGGAL,A.KODESUPP,D.NAMACUSTSUPP, :bulan Bulan,:tahun tahun
    ,A.SATUAN,case when A.NOSAT=1 then Isnull(A.Qnt1,0)-ISnull(B.Qnt1,0)
                   when A.NOSAT=2 then Isnull(A.Qnt2,0)-ISnull(B.Qnt2,0)
                   when A.NOSAT=3 then Isnull(A.Qnt2,0)-ISnull(B.Qnt2,0)
          End QntOUt,D.PPN FlagTipe,B.Qnt1,B.Qnt2,A.NOBUKTI,A.URUT ,A.Kodebrg,E.namaBrg
          ,A.noPBL,A.UrutPBL ,Case when F.ppn=0 then 'None'
          when F.ppn=1 then 'Exclude'
          when F.ppn=2 then 'Include'
          End xtipe
    from DBPRRBELIDET A
    left outer join (select NOPRB,UrutPRB,SUM(Qnt1) Qnt1,SUM(Qnt2) Qnt2
    				 from DBRBELIDET Group By NOPRB,UrutPRB
    				 ) b on A.NOBUKTI=B.NOPRB and A.URUT=B.UrutPRB
    Left Outer join DBPRRBELI C on a.NOBUKTI=C.NOBUKTI
    left outer join DBCUSTSUPP D on A.KODESUPP=D.KODECUSTSUPP
    Left Outer join Dbbarang E on A.kodebrg=E.Kodebrg
    Left Outer Join DbBelidet F on A.nopbl=F.nobukti and A.urutPBL=F.urut
    where 	Isnull(A.Qnt1,0)-ISnull(B.Qnt1,0) >0
    and
           Cast(Case when Case when C.IsOtorisasi1=1 then 1 else 0 end+
                           Case when C.IsOtorisasi2=1 then 1 else 0 end+
                           Case when C.IsOtorisasi3=1 then 1 else 0 end+
                           Case when C.IsOtorisasi4=1 then 1 else 0 end+
                           Case when C.IsOtorisasi5=1 then 1 else 0 end=C.MaxOL then 0
                      else 1
                 end As Bit) =0



      " , [ "tahun" =>$periode->tahun , "bulan" => $periode->bulan]);

    $collection1 = collect($tempoutstanding)->groupBy('KODESUPP');
    $outstandingArray = [];
    foreach ($collection1 as $p) {
      // code...
      array_push($outstandingArray, $p);
    }


          $tempPenerimaan = DB::connection("SML")->select("


      declare @Tahun int, @Bulan int

      select @Tahun=:tahun, @Bulan=:bulan

      Select 	A.NoBukti, A.NOURUT, A.Tanggal, A.TGLJATUHTEMPO, A.KODESUPP,
              C.NamaCustSupp, C.Alamat1, C.Alamat2, C.Kota,
              C.Alamat1+Char(13)+C.Alamat2+Char(13)+C.kota Alamat,
              A.NOBELI, A.KodeGdg, A.KODEEXP, A.HANDLING, A.KETERANGAN, A.FAKTURSUPP,
              A.KODEVLS, A.KURS, A.PPN, A.TIPEBAYAR, A.HARI, A.TipeDisc, A.DISC, A.DISCRP,
              A.NILAIPOT, A.NILAIDPP, A.NILAIPPN, A.NILAINET, A.ISCETAK, A.NilaiCetak,
              A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
      	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
      	A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.MAXOL,
              Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                             Case when A.IsOtorisasi2=1 then 1 else 0 end+
                             Case when A.IsOtorisasi3=1 then 1 else 0 end+
                             Case when A.IsOtorisasi4=1 then 1 else 0 end+
                             Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                        else 1
                   end As Bit) NeedOtorisasi
              ,Isnull(A.isbatal,0) IsBatal,A.Userbatal,A.Tglbatal
      From dbRBeli A
      Left Outer Join dbCustSupp C on C.KodeCustSupp=A.KodeSupp
      Left Outer Join dbGudang F on F.KodeGdg=A.KodeGdg
      where year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan
      order by A.NoBukti



          " , [ "tahun" =>$periode->tahun , "bulan" => $periode->bulan]);


          $penerimaanArray = [];
          foreach ($tempPenerimaan as $p) {
            array_push($penerimaanArray, $p);
          }


              return [
                "outstandingArray" => $outstandingArray,
                "penerimaanArray" => $penerimaanArray,
                "akses" => $akses
              ];

      // return view('newreturbeli' , [
      //   "periode" => $periode,
      //   "menul0" => $menul0,
      //   "tempoutstanding" =>$tempoutstanding,
      //   "outstandingArray" => $outstandingArray,
      //   "penerimaanArray" => $penerimaanArray,
      //   "akses" => $akses
      // ]);
  }



    public function spKoreksi (Request $req) {
    $values = [
      $req->input('choice'),
      $req->input('norpb'),
      $req->input('nourut'),
      $req->input('inputDate'),
      $req->input('kodesupp'),
      $req->input('kodegdg'),
      $req->input('noout'),
      $req->input('keterangan'),
      $req->input('faktursupp'),
      $req->input('urut'),
      $req->input('kodebrg'),
      $req->input('urutout'),
      $req->input('qntTerima'),
      $req->input('nosat'),
      $req->input('satuan'),
      $req->input('isi'),
      $req->input('qntTerima1'),
      $req->input('qntTerima2'),
      $req->input('flagtipe'),
      $req->input('nobatch'),
      $req->input('nolpb'),

    ];

    DB::connection('SML')->statement('exec sp_RBeliGudangweb ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?' ,$values);
    return 1;

    }


public function spCetak (Request $req)
  {
      $noBukti = $req->input('NOBUKTI');

      $cetak = DB::connection("SML")->select(
          "EXEC SP_CETAKRBELIGDG ?",
          [$noBukti]
      );

      $tempCetak1 = [];
      foreach ($cetak as $p) {
          array_push($tempCetak1, $p);
      }

      return $tempCetak1;
  }



}
