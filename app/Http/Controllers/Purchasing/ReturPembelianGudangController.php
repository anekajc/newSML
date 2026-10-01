<?php

namespace App\Http\Controllers\Purchasing;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\NewPeriode;
use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Model\VWOutRBeli;
use App\Model\DBFLMENU;


use App\Model\VWRBeliOut;


class ReturPembelianGudangController extends Controller
{

  // Rentang tanggal default = satu bulan penuh periode kerja user (sama seperti Purchase
  // Order/Uang Muka Beli). Dipakai untuk kedua tab, lihat catatan di loadAll().
  private function periodeRange ($periode) {
    $stamp = mktime(0, 0, 0, (int) $periode->bulan, 1, (int) $periode->tahun);
    return [ date('Y-m-01', $stamp), date('Y-m-t', $stamp) ];
  }

  public function index (Request $req) {

    $kodemenu = '030502';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu, $req->path());
    // $akses = DBFLMENU::where('USERID', \Auth::user()->username)-> where('L1', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(3);

    list($rpgTglAwal, $rpgTglAkhir) = $this->periodeRange($periode);

    // Baris kedua tabel digambar JS lewat loadAll() (lihat returpembeliangudang.blade.php),
    // jadi index() tidak lagi perlu menjalankan query outstanding/retur beli di sini.
    return view('purchasing.returpembeliangudang' , [
      "periode" => $periode,
      "menul0" => $menul0,
      "rpgTglAwal" => $rpgTglAwal,
      "rpgTglAkhir" => $rpgTglAkhir,
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

    // VWRBeliOut::all()->where(...) tidak pernah mengembalikan baris (sama seperti
    // getDetailOutstanding() sebelum diperbaiki) - diganti query langsung ke
    // dbRBeli/dbRBeliDet, meniru pola InvoiceReturBeliController@getDetail.
    $detailPenerimaan = DB::connection("SML")->select("
    Select  A.NoBukti NOBUKTI, A.Tanggal TANGGAL, A.KodeSupp KODESUPP, C.NamaCustSupp NAMACUSTSUPP,
            F.Nama Namagdg, B.NoPRB NooutBRg, B.Urut URUT,
            B.KodeBrg KODEBRG, E.NamaBrg NAMABRG, B.Qnt QNT,
            case when B.NoSat=1 then E.Sat1 when B.NoSat=2 then E.Sat2 when B.NoSat=3 then E.Sat3 end Satuan,
            case when P.NOSAT=1 then Isnull(P.Qnt1,0)-Isnull(S.Qnt1,0)
                 else Isnull(P.Qnt2,0)-Isnull(S.Qnt2,0) end QntOS
    From    dbRBeli A
    Left Outer Join dbRBeliDet B on B.NoBukti = A.NoBukti
    Left Outer Join dbCustSupp C on C.KodeCustSupp = A.KodeSupp
    Left Outer Join dbBarang  E on E.KodeBrg = B.KodeBrg
    Left Outer Join dbGudang  F on F.KodeGdg = B.KodeGdg
    Left Outer Join DBPRRBELIDET P on P.NOBUKTI = B.NoPRB and P.URUT = B.UrutPRB
    Left Outer Join (select NOPRB,UrutPRB,SUM(Qnt1) Qnt1,SUM(Qnt2) Qnt2
                     from DBRBELIDET Group By NOPRB,UrutPRB) S
                     on S.NOPRB = B.NoPRB and S.UrutPRB = B.UrutPRB
    where   A.NoBukti = :nobukti
    order by B.Urut
    " , [ "nobukti" => $req->input('NOBUKTI') ]);

    $tempPenerimaan = [];
    foreach ($detailPenerimaan as $p) {
      array_push($tempPenerimaan, $p);
    }
    return $tempPenerimaan;
  }

  public function getKoreksiAddList (Request $req) {
    $tempPenerimaan = VWRBeliOut::where('NOBUKTI' , $req->input('norpb'))->pluck('KODEBRG')->toArray();

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

    if (!$data || !count($data)) {
      return 3;
    }

    $check = DB::connection('SML')->select('select * from dbRBeli where NOBUKTI = :nobukti',["nobukti" => $norpb]);
    if ($check) {
      return 2;
    }

    $result = 1;

    try {
      DB::connection('SML')->transaction(function () use ($data, $date, $username, $noout, $periode, $norpb, $nourut) {
        DB::connection('SML')->statement('delete	TempOutstanding where IDUser = :idUser',['idUser' => $username ]);

        foreach ($data as $d) {

          $check2 = DB::connection('SML')->select('exec Sp_CekStockAkhir ?,?,?,?',[$d['NOSAT'], $date, $d['Kodegdg'],  $d['Kodebrg']]);

          $checkSaldo = 0;
          if ($check2) {
            if ((float)$d['inputQntTerima'] > (float)$check2[0]->SALDOQNT ) {
              $checkSaldo = 1;
            }
          } else {
            $checkSaldo = 1;
          }

          if ($checkSaldo) {
            throw new \RuntimeException('STOK_TIDAK_CUKUP');
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

          DB::connection("SML")->statement('exec sp_TempOutPRRBelitrade ?,?,?,?,?,?,?,?,?,?,?',$values);
        }

        $tempValues = [$norpb, $nourut,$date,$data[0]['Kodegdg'], $noout,"","",0,0, $username];
        DB::connection('SML')->statement('exec sp_InsertOutRbeli ?,?,?,?,?,?,?,?,?,?', $tempValues);
      });
    } catch (\RuntimeException $e) {
      if ($e->getMessage() === 'STOK_TIDAK_CUKUP') {
        return 3;
      }
      throw $e;
    }

    return $result;
  }

  public function loadAll (Request $req) {

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    // Kedua tab difilter rentang tanggal - default satu bulan penuh periode kerja, sama
    // seperti Purchase Order/Uang Muka Beli. Tab "Outstanding Retur Beli" (urut 1) pakai
    // tglawal1/tglakhir1 (C.TANGGAL = tanggal dokumen PRRBELI), tab "Retur Beli" (urut 2)
    // pakai tglawal/tglakhir seperti sebelumnya.
    list($tglawal1, $tglakhir1) = $this->periodeRange($periode);
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglawal1')))  { $tglawal1  = $req->input('tglawal1'); }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglakhir1'))) { $tglakhir1 = $req->input('tglakhir1'); }
    if ($tglawal1 > $tglakhir1) { $tglakhir1 = $tglawal1; }

    list($tglawal, $tglakhir) = $this->periodeRange($periode);
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglawal')))  { $tglawal  = $req->input('tglawal'); }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglakhir'))) { $tglakhir = $req->input('tglakhir'); }
    if ($tglawal > $tglakhir) { $tglakhir = $tglawal; }

    $tempoutstanding = DB::connection("SML")->select("
    declare @tglawal1 date, @tglakhir1 date
    select @tglawal1=:tglawal1, @tglakhir1=:tglakhir1

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
  and C.TANGGAL between @tglawal1 and @tglakhir1
  and
         Cast(Case when Case when C.IsOtorisasi1=1 then 1 else 0 end+
                         Case when C.IsOtorisasi2=1 then 1 else 0 end+
                         Case when C.IsOtorisasi3=1 then 1 else 0 end+
                         Case when C.IsOtorisasi4=1 then 1 else 0 end+
                         Case when C.IsOtorisasi5=1 then 1 else 0 end=C.MaxOL then 0
                    else 1
               end As Bit) =0



    " , [ "tahun" =>$periode->tahun , "bulan" => $periode->bulan, "tglawal1" => $tglawal1, "tglakhir1" => $tglakhir1]);


    $collection1 = collect($tempoutstanding)->groupBy('KODESUPP');
    $outstandingArray = [];
    foreach ($collection1 as $p) {
      // code...
      array_push($outstandingArray, $p);
    }


        $tempPenerimaan = DB::connection("SML")->select("


    declare @tglawal date, @tglakhir date

    select @tglawal=:tglawal, @tglakhir=:tglakhir

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
    where A.Tanggal between @tglawal and @tglakhir
    order by A.NoBukti



        " , [ "tglawal" => $tglawal , "tglakhir" => $tglakhir]);


        $penerimaanArray = [];
        foreach ($tempPenerimaan as $p) {
          array_push($penerimaanArray, $p);
        }

    // Konfigurasi kolom urut 1 (Outstanding Retur Beli) & urut 2 (Retur Beli) sekaligus -
    // sama pola dengan NewPOController@loadAll, lihat cabang 'returpembeliangudang' di
    // HeaderTableController@getHeaderTable.
    $reqHeader = new Request(['href' => 'returpembeliangudang']);
    $header = app('App\Http\Controllers\HeaderTableController')->getHeaderTable($reqHeader);

    return [
      "outstandingArray"   => $outstandingArray,
      "penerimaanArray"    => $penerimaanArray,
      "aliasordered"       => $header['aliasordered'],
      "headertableheader"  => $header['headertableheader'],
      "isnumeric"          => $header['isnumeric'],
      "headertablevalue"   => $header['headertablevalue'],
      "isshown"            => $header['isshown'],
      "desimal"            => $header['desimal'],
      "aliasordered2"      => $header['aliasordered2'],
      "headertableheader2" => $header['headertableheader2'],
      "isnumeric2"         => $header['isnumeric2'],
      "headertablevalue2"  => $header['headertablevalue2'],
      "isshown2"           => $header['isshown2'],
      "desimal2"           => $header['desimal2'],
    ];
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

    $ok = DB::connection('SML')->statement('exec sp_RBeliGudangweb ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?' ,$values);
    return $ok ? 1 : 0;

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
