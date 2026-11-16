<?php

namespace App\Http\Controllers\Wms;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Models\VWSerahSampleOut;



use App\Models\VWMASTERPRUBAHKEMASAN;
use App\Models\VWOutPRUKO;
use App\Models\VWUbahKemasanOut;

use App\Models\VWOUTPERSIAPANBRGUKWEB;
use App\Models\vwShowOutBrgUKWEB;
use App\Models\VWQCPickingUKWEB;





class UbahKemasanPickingController extends Controller
{




  public function index () {
    // vwShowOutBrgUKWEB
    // WOUTPERSIAPANBRGUKWEB

    $kodemenu = '210313';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $outstanding = VWOUTPERSIAPANBRGUKWEB::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('Urut')->groupBy('NoBukti');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $outstanding = VWOUTPERSIAPANBRGUKWEB::query()->where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('Urut')->groupBy('NoBukti');

    $tempOutstanding = [];
    foreach ($outstanding as $o) {
      // code...
      array_push($tempOutstanding, $o);
    }

    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaan = VWQCPickingUKWEB::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('urut')->groupBy('NoBukti');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = VWQCPickingUKWEB::query()->where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('urut')->groupBy('NoBukti');
    $tempPenerimaan = [];
    foreach ($penerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }


    return view('wms.ubahkemasanpicking' , [
      "periode" => $periode,
      "outstandingArray" => $tempOutstanding,
      "penerimaanArray" => $tempPenerimaan,
      "akses" => $akses
    ]);
  }



  public function getDetailOutstanding (Request $req) {

    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $detailOutstanding = VWOUTPERSIAPANBRGUKWEB::all()->where('NoBukti', $req->input('NOBUKTI'))->sortBy('Urut');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $detailOutstanding = VWOUTPERSIAPANBRGUKWEB::query()->whereRaw('NoBukti = cast(? as varchar(100))', [$req->input('NOBUKTI')])->get()->sortBy('Urut');
    $tempOutstanding = [];
    foreach ($detailOutstanding as $do) {
      // code...
      array_push($tempOutstanding,$do);
    }
    return $tempOutstanding;

  }

  public function getDetailOutstandingInfo (Request $req) {

    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $detailOutstanding = vwShowOutBrgUKWEB::all()->where('NOBUKTI', $req->input('NOBUKTI'))->sortBy('URUT');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $detailOutstanding = vwShowOutBrgUKWEB::query()->whereRaw('NOBUKTI = cast(? as varchar(100))', [$req->input('NOBUKTI')])->get()->sortBy('URUT');
    $tempOutstanding = [];
    foreach ($detailOutstanding as $do) {
      // code...
      array_push($tempOutstanding,$do);
    }
    return $tempOutstanding;

  }

  public function getDetailPenerimaan (Request $req) {
    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $detailPenerimaan = VWQCPickingUKWEB::all()->where('NoBukti',$req->input('NOBUKTI') )->sortBy('urut');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $detailPenerimaan = VWQCPickingUKWEB::query()->whereRaw('NoBukti = cast(? as varchar(100))', [$req->input('NOBUKTI')])->get()->sortBy('urut');
    $tempPenerimaan = [];
    foreach ($detailPenerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }
    return $tempPenerimaan;
  }


  public function loadAll () {

    $kodemenu = '210313';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $outstanding = VWOUTPERSIAPANBRGUKWEB::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('Urut')->groupBy('NoBukti');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $outstanding = VWOUTPERSIAPANBRGUKWEB::query()->where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('Urut')->groupBy('NoBukti');

    $tempOutstanding = [];
    foreach ($outstanding as $o) {
      // code...
      array_push($tempOutstanding, $o);
    }


    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaan = VWQCPickingUKWEB::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('urut')->groupBy('NoBukti');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = VWQCPickingUKWEB::query()->where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('urut')->groupBy('NoBukti');
    $tempPenerimaan = [];
    foreach ($penerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }

    return [
      "outstandingArray" => $tempOutstanding,
      "penerimaanArray" => $tempPenerimaan,
      "akses" => $akses
    ];
  }

  public function spCheckAdd (Request $req) {

    $nobukti = $req->input('nobukti');
    $nobarang = $req->input('nobarang');
    $urut = $req->input('urut');
    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $check = VWOUTPERSIAPANBRGUKWEB::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->where('NoBukti',$nobukti )->where('Kodebrg', $nobarang)->where('Urut',$urut );
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $check = VWOUTPERSIAPANBRGUKWEB::query()->where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->whereRaw('NoBukti = cast(? as varchar(100))', [$nobukti])->whereRaw('Kodebrg = cast(? as varchar(100))', [$nobarang])->where('Urut', $urut)->get();


    $tempCheck= [];
    foreach ($check as $p) {
      // code...
      array_push($tempCheck, $p);
    }

    // $variable = $tempCheck[0]->namafield;


//   $lokasi =  DB::connection('SML')->select("select A.BarcodeLoc,A.qntMax
// ,c.KodeGdg
// from dbBarangLokasiSIG A
// left outer join dbLokasiSIG C on A.BarcodeLoc=c.BarcodeLoc
// where A.KodeBarang= :nobarang
// and C.KODEGDG <>'GTC'", ["nobarang" => $nobarang]);


// Dinonaktifkan 2026-10-21: stok semua barang setahun dihitung dulu, baru disaring ke barang yang di-scan (lambat).
// $lokasi =  DB::connection('SML')->select("
// 
// declare @Kodegdg Varchar(20)
// select @Kodegdg= :kodegdg
// set @Kodegdg=ISNULL(@Kodegdg,'')
// 
// select B.BarcodeLoc,10000 qntMax,
// isnull(b.SALDOQNT,0) StockLokasi ,10000 - isnull(B.SALDOQNT,0) SisaKapasitas
// ,B.KodeGdg
// from DBBARANG A
// left outer join (select  KODEBRG,BARCODELoc,SUM(SaldoQnt) SaldoQnt,KODEGDG
//       from (
//         select  KodeBrg,barcode BARCODELoc,QntAwal SaldoQnt ,KODEGDG
//         from 	DBSTOCKBRGSIG
//         where         Tahun=year(GETDATE()) and  Bulan= case when year(GETDATE())=2024 then 2 else 1 End
// 
//         union all
// 
//         select 	KodeBrg,Barcode BARCODELoc ,
//         SUM(QntDb)-SUM(QntCr) SaldoQnt,Kodegdg
//         from 	vwKartuStockSIG
//         where 	year(Tanggal)=year(GETDATE())  and Tanggal<=GETDATE()
//         and Tipe not in ('AWL')
//         and KodeGdg <>'GTC'
// 
//         group by KodeBrg,Barcode,Kodegdg
// 
//         ) A
// 
// 
//       group by KODEBRG,BARCODELoc,KODEGDG
//       having SUM(SaldoQnt) >0
// ) B on A.KODEBRG=B.KODEBRG
// where A.KODEBRG= :nobarang
//   and B.KodeGdg like case when @Kodegdg='' then '%%' else @Kodegdg End
// and B.SALDOQNT>0 and B.KODEGDG <>'GTC' and (B.BARCODELoc like ('%REC%') or B.BARCODELoc like ('%KIT%') /*OR B.BARCODELoc like ('%DIS%')*/ OR B.BARCODELoc like ('%LSA%'))
// and b.KODEGDG in ('GMPL','GSML','GSML2','GKSI 4','GSAMPLE','GSML3')
// 
// 
// union all
// 
// select A.BarcodeLoc,A.qntMax,
// isnull(b.SALDOQNT,0) StockLokasi ,A.qntMax - isnull(B.SALDOQNT,0) SisaKapasitas
// ,c.KodeGdg
// from dbBarangLokasiSIG A
// left outer join dbLokasiSIG C on A.BarcodeLoc=c.BarcodeLoc
// left outer join (select  KODEBRG,BARCODE,SUM(SaldoQnt) SaldoQnt,KODEGDG
//       from (
//         select  KodeBrg,barcode ,QntAwal SaldoQnt ,KODEGDG
//         from 	DBSTOCKBRGSIG
//         where         Tahun=year(GETDATE()) and  Bulan= case when year(GETDATE())=2024 then 2 else 1 End
// 
//         union all
// 
//         select 	KodeBrg,Barcode ,
//         SUM(QntDb)-SUM(QntCr) SaldoQnt,Kodegdg
//         from 	vwKartuStockSIG
//         where 	year(Tanggal)=year(GETDATE())  and Tanggal<=GETDATE()
//         and Tipe not in ('AWL')
//         and KodeGdg <>'GTC'
// 
//         group by KodeBrg,Barcode,Kodegdg
// 
//         ) A
// 
// 
//       group by KODEBRG,BARCODE,KODEGDG
//       having SUM(SaldoQnt) >0
// ) B on A.KodeBarang=B.KODEBRG and A.BarcodeLoc=B.BARCODE and C.KodeGdg=b.KODEGDG
// where A.KodeBarang=:nobarang1
// and B.SALDOQNT>0 and C.KODEGDG <>'GTC'
// and C.KodeGdg like case when @Kodegdg='' then '%%' else @Kodegdg End
// and b.KODEGDG in ('GMPL','GSML','GSML2','GKSI 4','GSAMPLE','GSML3')
// and  (A.BarcodeLoc not like ('%REC%') AND A.BARCODELoc NOT like ('%KIT%') AND A.BARCODELoc NOT like ('%DIS%') and a.BarcodeLoc not like ('%LSA%'))
// ", ["nobarang" => $nobarang,"nobarang1" => $nobarang,"kodegdg" => '']);
// Pengganti: saringan kode barang yang sama dipasang di dalam subquery stok (hasil sama, lebih cepat).
$lokasi =  DB::connection('SML')->select("

declare @Kodegdg Varchar(20)
select @Kodegdg= :kodegdg
set @Kodegdg=ISNULL(@Kodegdg,'')
declare @KodeBrgCari Varchar(100)
select @KodeBrgCari= :nobarangcari

select B.BarcodeLoc,10000 qntMax,
isnull(b.SALDOQNT,0) StockLokasi ,10000 - isnull(B.SALDOQNT,0) SisaKapasitas
,B.KodeGdg
from DBBARANG A
left outer join (select  KODEBRG,BARCODELoc,SUM(SaldoQnt) SaldoQnt,KODEGDG
      from (
        select  KodeBrg,barcode BARCODELoc,QntAwal SaldoQnt ,KODEGDG
        from 	DBSTOCKBRGSIG
        where         Tahun=year(GETDATE()) and  Bulan= case when year(GETDATE())=2024 then 2 else 1 End and KodeBrg=@KodeBrgCari

        union all

        select 	KodeBrg,Barcode BARCODELoc ,
        SUM(QntDb)-SUM(QntCr) SaldoQnt,Kodegdg
        from 	vwKartuStockSIG
        where 	year(Tanggal)=year(GETDATE())  and Tanggal<=GETDATE()
        and Tipe not in ('AWL')
        and KodeGdg <>'GTC' and KodeBrg=@KodeBrgCari

        group by KodeBrg,Barcode,Kodegdg

        ) A


      group by KODEBRG,BARCODELoc,KODEGDG
      having SUM(SaldoQnt) >0
) B on A.KODEBRG=B.KODEBRG
where A.KODEBRG= :nobarang
  and B.KodeGdg like case when @Kodegdg='' then '%%' else @Kodegdg End
and B.SALDOQNT>0 and B.KODEGDG <>'GTC' and (B.BARCODELoc like ('%REC%') or B.BARCODELoc like ('%KIT%') /*OR B.BARCODELoc like ('%DIS%')*/ OR B.BARCODELoc like ('%LSA%'))
and b.KODEGDG in ('GMPL','GSML','GSML2','GKSI 4','GSAMPLE','GSML3')


union all

select A.BarcodeLoc,A.qntMax,
isnull(b.SALDOQNT,0) StockLokasi ,A.qntMax - isnull(B.SALDOQNT,0) SisaKapasitas
,c.KodeGdg
from dbBarangLokasiSIG A
left outer join dbLokasiSIG C on A.BarcodeLoc=c.BarcodeLoc
left outer join (select  KODEBRG,BARCODE,SUM(SaldoQnt) SaldoQnt,KODEGDG
      from (
        select  KodeBrg,barcode ,QntAwal SaldoQnt ,KODEGDG
        from 	DBSTOCKBRGSIG
        where         Tahun=year(GETDATE()) and  Bulan= case when year(GETDATE())=2024 then 2 else 1 End and KodeBrg=@KodeBrgCari

        union all

        select 	KodeBrg,Barcode ,
        SUM(QntDb)-SUM(QntCr) SaldoQnt,Kodegdg
        from 	vwKartuStockSIG
        where 	year(Tanggal)=year(GETDATE())  and Tanggal<=GETDATE()
        and Tipe not in ('AWL')
        and KodeGdg <>'GTC' and KodeBrg=@KodeBrgCari

        group by KodeBrg,Barcode,Kodegdg

        ) A


      group by KODEBRG,BARCODE,KODEGDG
      having SUM(SaldoQnt) >0
) B on A.KodeBarang=B.KODEBRG and A.BarcodeLoc=B.BARCODE and C.KodeGdg=b.KODEGDG
where A.KodeBarang=:nobarang1
and B.SALDOQNT>0 and C.KODEGDG <>'GTC'
and C.KodeGdg like case when @Kodegdg='' then '%%' else @Kodegdg End
and b.KODEGDG in ('GMPL','GSML','GSML2','GKSI 4','GSAMPLE','GSML3')
and  (A.BarcodeLoc not like ('%REC%') AND A.BARCODELoc NOT like ('%KIT%') AND A.BARCODELoc NOT like ('%DIS%') and a.BarcodeLoc not like ('%LSA%'))
", ["nobarang" => $nobarang,"nobarang1" => $nobarang,"kodegdg" => '',"nobarangcari" => $nobarang]);




//   $lokasi =  DB::connection('SML')->select("select A.BarcodeLoc,A.qntMax,
// isnull(b.SALDOQNT,0) StockLokasi ,A.qntMax - isnull(B.SALDOQNT,0) SisaKapasitas
// ,c.KodeGdg
//   from dbBarangLokasiSIG A
//   left outer join dbLokasiSIG C on A.BarcodeLoc=c.BarcodeLoc
//  left outer join (select  KODEBRG,BARCODE,SUM(SaldoQnt) SaldoQnt,KODEGDG
// 				from (
// 					select  KodeBrg,barcode ,QntAwal SaldoQnt ,KODEGDG
// 					from 	DBSTOCKBRGSIG
// 					where         Tahun=year(GETDATE()) and Bulan=MONTH(GETDATE())
//
// 					union all
//
// 					select 	KodeBrg,Barcode ,
// 					SUM(QntDb)-SUM(QntCr) SaldoQnt,Kodegdg
// 					from 	vwKartuStockSIG
// 					where 	year(Tanggal)=year(GETDATE()) and month(tanggal)=MONTH(GETDATE()) and Tanggal<=GETDATE()
// 					and Tipe not in ('AWL')
// 					and KodeGdg <>'GTC'
//
// 					group by KodeBrg,Barcode,Kodegdg
//
// 					) A
//
//
// 				group by KODEBRG,BARCODE,KODEGDG
// 				having SUM(SaldoQnt) >0
//  ) B on A.KodeBarang=B.KODEBRG and A.BarcodeLoc=B.BARCODE and C.KodeGdg=b.KODEGDG
//   where A.KodeBarang= :nobarang
//   and B.SALDOQNT>0 and C.KODEGDG <>'GTC'", ["nobarang" => $nobarang]);


      return ["check" => $tempCheck ,"lokasi" => $lokasi];

  }


  public function spAdd (Request $req) {
    // return 'tes';
    $tempData = $req->input('tempData');
    $username = \Auth::user()->username;
    foreach ($tempData as $d) {
      // code...

      $values = [
        $d['NoBukti'], $d['Kodebrg'], $d['inputKodeLokasi'], $d['Qnt'], $d['Urut'],$username, '', '', \Auth::user()->username
      ];

      DB::connection("SML")->statement('exec Sp_TransPersiapanUKWEB ?,?,?,?,?,?,?,?,?',$values);
    }

    // foreach ($data as $d) {
    //   $values = [$username, $noout,$d['Tahun'],$d['Bulan'],$d['inputQntTerima'],$d['Urut']];
    //   DB::connection("SML")->statement('exec sp_RefreshTempOutBRG ?,?,?,?,?,?',$values);
    // }




    // $values = [
    //   $nobukti, $nobarang, $barcodelokasi, $qntTerima, $urut,$username, $nobuktiLPB, $urutLPB
    // ];
    //
    //   DB::connection("SML")->statement('exec Sp_TransPersiapanUKWEB ?,?,?,?,?,?,?',$values);
    return 1;

  }






}
