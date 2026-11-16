<?php

namespace App\Http\Controllers\Wms;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Models\VwPersiapan;
use App\Models\VWOUTPERSIAPANBRG;
use App\Models\VWOUTPENERIMAANBRGWEB;


class NewPickingController extends Controller
{





  public function index () {

    $kodemenu = '210304';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();


    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaan = VwPersiapan::all()->where('bulan',$periode->bulan )->where('tahun', $periode->tahun)->sortBy('urut')->groupBy('NOBUKTI');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = VwPersiapan::query()->where('bulan', $periode->bulan)->where('tahun', $periode->tahun)->get()->sortBy('urut')->groupBy('NOBUKTI');
    $tempPenerimaan = [];
    foreach ($penerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }


    return view('wms.newpicking' , [
      "periode" => $periode,
      "penerimaanArray" => $tempPenerimaan
    ]);
  }

  public function loadAll () {
    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaan = VwPersiapan::all()->where('bulan',$periode->bulan )->where('tahun', $periode->tahun)->sortBy('urut')->groupBy('NOBUKTI');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = VwPersiapan::query()->where('bulan', $periode->bulan)->where('tahun', $periode->tahun)->get()->sortBy('urut')->groupBy('NOBUKTI');
    $tempPenerimaan = [];

    foreach ($penerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }

    return [
      "penerimaanArray" => $tempPenerimaan
    ];
  }
  // kode3 ambil qnt dari ob g bisa diganti, yang lama qntsisa bisa diganti
  public function spAdd (Request $req) {
    // return 'tes';

    $nobukti = $req->input('nobukti');
    $nobarang = $req->input('nobarang');
    $barcodelokasi = $req->input('barcodelokasi');
    $qntTerima = $req->input('qntTerima');
    $urut = $req->input('urut');
    $tanggal = date("Y-m-d");
    $nosat = $req->input('NOSAT');



    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $check = VWOUTPERSIAPANBRG::all()->where('NoBukti',$nobukti )->where('Kodebrg', $nobarang)->where('Urut',$urut );
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $check = VWOUTPERSIAPANBRG::query()->whereRaw('NoBukti = cast(? as varchar(100))', [$nobukti])->whereRaw('Kodebrg = cast(? as varchar(100))', [$nobarang])->where('Urut', $urut)->get();


    $tempCheck= [];
    foreach ($check as $p) {
      // code...
      array_push($tempCheck, $p);
    }


    if (!$tempCheck){

      return 4;
    }


    $checkSaldo = 0;

    $check2 = DB::connection('SML')->select('exec Sp_CekStockAkhirLok ?,?,?,?',[$nosat, $tanggal,$req->input('barcodelokasi'),  $req->input('nobarang') ]);
    if ($check2) {
    if ((int)$req->input('qntTerima') > (int)$check2[0]->SALDOQNT ) {
      // return 'a';
      $checkSaldo = 1;
    }
    } else {
    // return 'b';
    $checkSaldo = 1;
    }


    if ($checkSaldo) {
    // code...
    return 3;
    }




    $values = [
      $nobukti, $nobarang, $barcodelokasi, $qntTerima, $urut, '' , 0 , \Auth::user()->username
    ];

      DB::connection("SML")->statement('exec Sp_TransPersiapan ?,?,?,?,?,?,?,?',$values);
      return 1;

  }

  public function spAddLPB (Request $req) {
    // return 'tes';

    $nobukti = $req->input('nobukti');
    $urut = $req->input('urut');
    $nobarang = $req->input('nobarang');
    $nobuktiLPB = $req->input('nobuktiLPB');
    $urutLPB = $req->input('urutLPB');
    $nobarangLPB = $req->input('nobarangLPB');
    $barcodelokasi = $req->input('barcodelokasi');
    $qntTerima = $req->input('qntTerima');
    $tanggal = date("Y-m-d");
    $nosat = $req->input('NOSAT');

    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $check = VWOUTPERSIAPANBRG::all()->where('NoBukti',$nobukti )->where('Kodebrg', $nobarang)->where('Urut',$urut );
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $check = VWOUTPERSIAPANBRG::query()->whereRaw('NoBukti = cast(? as varchar(100))', [$nobukti])->whereRaw('Kodebrg = cast(? as varchar(100))', [$nobarang])->where('Urut', $urut)->get();


    $tempCheck= [];
    foreach ($check as $p) {
      // code...
      array_push($tempCheck, $p);
    }


    if (!$tempCheck){

      return 4;
    }


    $checkSaldo = 0;

    $check2 = DB::connection('SML')->select('exec Sp_CekStockAkhirLok ?,?,?,?',[$nosat, $tanggal,$req->input('barcodelokasi'),  $req->input('nobarang') ]);
    if ($check2) {
    if ((int)$req->input('qntTerima') > (int)$check2[0]->SALDOQNT ) {
      // return 'a';
      $checkSaldo = 1;
    }
    } else {
    // return 'b';
    $checkSaldo = 1;
    }


    if ($checkSaldo) {
    // code...
    return 3;
    }

    $values = [
      $nobukti, $nobarang, $barcodelokasi, $qntTerima, $urut, $nobuktiLPB, $urutLPB , \Auth::user()->username
    ];

      DB::connection("SML")->statement('exec Sp_TransPersiapan ?,?,?,?,?,?,?,?',$values);
      return 1;

  }




    public function spKoreksi (Request $req) {
      $values = [
        $req->input('choice'),
        $req->input('nobukti'),
        $req->input('urut'),
        $req->input('kodeBrg'),
        $req->input('inputQnt')
      ];

      DB::connection('SML')->statement('exec Sp_updPersiapan ?,?,?,?,?', $values);
      return 1;
    }

  public function spCheckAdd (Request $req) {

    $nobukti = $req->input('nobukti');
    $nobarang = $req->input('nobarang');
    $urut = $req->input('urut');
    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $check = VWOUTPERSIAPANBRG::all()->where('NoBukti',$nobukti )->where('Kodebrg', $nobarang)->where('Urut',$urut );
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $check = VWOUTPERSIAPANBRG::query()->whereRaw('NoBukti = cast(? as varchar(100))', [$nobukti])->whereRaw('Kodebrg = cast(? as varchar(100))', [$nobarang])->where('Urut', $urut)->get();


    $tempCheck= [];
    foreach ($check as $p) {
      // code...
      array_push($tempCheck, $p);
    }
    $kodegdg='';
    if ($tempCheck &&  $tempCheck[0]){
    $kodegdg = $tempCheck[0]->Kodegdg;
    }


    if (str_contains($nobukti, 'TFL')) {
  $check1 = DB::connection('SML')->select('select TOP 1 kodelokasi from DBOutBrgDET where NOBUKTI = :nobukti and KODEBRG = :nobarang', ["nobukti" => $nobukti,"nobarang" => $nobarang]);
  $lokasi = [
    ["BarcodeLoc" => $check1[0]->kodelokasi,
    "SisaKapasitas" => 0 ,
    "StockLokasi" => 99999,
    "qntMax" => 0

   ]
  ];
} else {

  // Dinonaktifkan 2026-10-21: stok semua barang setahun dihitung dulu, baru disaring ke barang yang di-scan (lambat).
  // $lokasi =  DB::connection('SML')->select("
  // 
  //  declare @Kodegdg Varchar(20)
  // select @Kodegdg= :kodegdg
  // set @Kodegdg=ISNULL(@Kodegdg,'')
  // 
  // select B.BarcodeLoc,10000 qntMax,
  // isnull(b.SALDOQNT,0) StockLokasi ,10000 - isnull(B.SALDOQNT,0) SisaKapasitas
  // ,B.KodeGdg
  // from DBBARANG A
  //  left outer join (select  KODEBRG,BARCODELoc,SUM(SaldoQnt) SaldoQnt,KODEGDG
  // 				from (
  // 					select  KodeBrg,barcode BARCODELoc,QntAwal SaldoQnt ,KODEGDG
  // 					from 	DBSTOCKBRGSIG
  // 					where         Tahun=year(GETDATE()) and  Bulan= case when year(GETDATE())=2024 then 2 else 1 End
  // 
  // 					union all
  // 
  // 					select 	KodeBrg,Barcode BARCODELoc ,
  // 					SUM(QntDb)-SUM(QntCr) SaldoQnt,Kodegdg
  // 					from 	vwKartuStockSIG
  // 					where 	year(Tanggal)=year(GETDATE())  and Tanggal<=GETDATE()
  // 					and Tipe not in ('AWL')
  // 					and KodeGdg <>'GTC'
  // 
  // 					group by KodeBrg,Barcode,Kodegdg
  // 
  // 					) A
  // 
  // 
  // 				group by KODEBRG,BARCODELoc,KODEGDG
  // 				having SUM(SaldoQnt) >0
  //  ) B on A.KODEBRG=B.KODEBRG
  // where A.KODEBRG= :nobarang
  //   and B.KodeGdg like case when @Kodegdg='' then '%%' else @Kodegdg End
  // and B.SALDOQNT>0 and B.KODEGDG <>'GTC' and (B.BARCODELoc like ('%REC%') or B.BARCODELoc like ('%KIT%') /*OR B.BARCODELoc like ('%DIS%')*/ OR B.BARCODELoc like ('%LSA%'))
  // and b.KODEGDG in ('GMPL','GSML','GSML2','GKSI 4','GSAMPLE','GSML3','GTRANSIT')
  // 
  // 
  // union all
  // 
  // select A.BarcodeLoc,A.qntMax,
  // isnull(b.SALDOQNT,0) StockLokasi ,A.qntMax - isnull(B.SALDOQNT,0) SisaKapasitas
  // ,c.KodeGdg
  // from dbBarangLokasiSIG A
  // left outer join dbLokasiSIG C on A.BarcodeLoc=c.BarcodeLoc
  //  left outer join (select  KODEBRG,BARCODE,SUM(SaldoQnt) SaldoQnt,KODEGDG
  // 				from (
  // 					select  KodeBrg,barcode ,QntAwal SaldoQnt ,KODEGDG
  // 					from 	DBSTOCKBRGSIG
  // 					where         Tahun=year(GETDATE()) and  Bulan= case when year(GETDATE())=2024 then 2 else 1 End
  // 
  // 					union all
  // 
  // 					select 	KodeBrg,Barcode ,
  // 					SUM(QntDb)-SUM(QntCr) SaldoQnt,Kodegdg
  // 					from 	vwKartuStockSIG
  // 					where 	year(Tanggal)=year(GETDATE())  and Tanggal<=GETDATE()
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
  // where A.KodeBarang=:nobarang1
  // and B.SALDOQNT>0 and C.KODEGDG <>'GTC'
  // and C.KodeGdg like case when @Kodegdg='' then '%%' else @Kodegdg End
  // and b.KODEGDG in ('GMPL','GSML','GSML2','GKSI 4','GSAMPLE','GSML3','GTRANSIT')
  // and  (A.BarcodeLoc not like ('%REC%') AND A.BARCODELoc NOT like ('%KIT%') AND A.BARCODELoc NOT like ('%DIS%'))
  // ", ["nobarang" => $nobarang,"nobarang1" => $nobarang,"kodegdg" => $kodegdg]);
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
  and b.KODEGDG in ('GMPL','GSML','GSML2','GKSI 4','GSAMPLE','GSML3','GTRANSIT')


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
  and b.KODEGDG in ('GMPL','GSML','GSML2','GKSI 4','GSAMPLE','GSML3','GTRANSIT')
and  (A.BarcodeLoc not like ('%REC%') AND A.BARCODELoc NOT like ('%KIT%') AND A.BARCODELoc NOT like ('%DIS%'))
  ", ["nobarang" => $nobarang,"nobarang1" => $nobarang,"kodegdg" => $kodegdg,"nobarangcari" => $nobarang]);
}

      return ["check" => $tempCheck ,"lokasi" => $lokasi];

  }


  public function spCheckAddLPB (Request $req) {

    $nobukti = $req->input('nobukti');
    $nobarang = $req->input('nobarang');
    $urut = $req->input('urut');
    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $check = VWOUTPERSIAPANBRG::all()->where('NoBukti',$nobukti )->where('Kodebrg', $nobarang)->where('Urut',$urut );
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $check = VWOUTPERSIAPANBRG::query()->whereRaw('NoBukti = cast(? as varchar(100))', [$nobukti])->whereRaw('Kodebrg = cast(? as varchar(100))', [$nobarang])->where('Urut', $urut)->get();

    $tempCheck= [];
    foreach ($check as $p) {
      // code...
      array_push($tempCheck, $p);
    }

    $nobuktiLPB = $req->input('nobuktiLPB');
    $nobarangLPB = $req->input('nobarangLPB');
    $urutLPB = $req->input('urutLPB');
      // ->orWhere('trans', 'UKO')->get()
      // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
      // $checkLPB = VWOUTPENERIMAANBRGWEB::all()->where('NoBukti1',$nobuktiLPB )->where('Kodebrg', $nobarangLPB)->where('Urut', $urutLPB)->whereIn('trans', ['UKI' , 'PBL' , 'RSPB','TRI','RPJ']);
      // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
      $checkLPB = VWOUTPENERIMAANBRGWEB::query()->whereRaw('NoBukti1 = cast(? as varchar(100))', [$nobuktiLPB])->whereRaw('Kodebrg = cast(? as varchar(100))', [$nobarangLPB])->where('Urut', $urutLPB)->whereIn('trans', ['UKI' , 'PBL' , 'RSPB','TRI','RPJ'])->get();
      $tempCheckLPB = [];
      foreach ($checkLPB as $p) {
        // code...
        array_push($tempCheckLPB, $p);
      }

      return ["check" => $tempCheck , "checkLPB" => $tempCheckLPB];

  }

  public function getDetailPenerimaan (Request $req) {
    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $detailPenerimaan = VwPersiapan::all()->where('NOBUKTI',$req->input('NOBUKTI') )->sortBy('KODEBRG');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $detailPenerimaan = VwPersiapan::query()->whereRaw('NOBUKTI = cast(? as varchar(100))', [$req->input('NOBUKTI')])->get()->sortBy('KODEBRG');
    $tempPenerimaan = [];
    foreach ($detailPenerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }
    return $tempPenerimaan;
  }





}
