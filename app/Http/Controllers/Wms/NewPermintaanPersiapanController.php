<?php

namespace App\Http\Controllers\Wms;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
// use App\Models\VWRSPBWEB;
// use App\Models\VwOutSPBRSPB;
// use App\Models\VwOutSPBRSPBLIMIT;
use App\Models\vwOutBarang;
use App\Models\vwShowOutBrg;
use App\Models\vwShowOutBrgperiod;


class NewPermintaanPersiapanController extends Controller
{




  public function index () {

    $kodemenu = '210301';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();



    return view('wms.newpermintaanpersiapan' , [
      "periode" => $periode,
      "akses" => $akses
      // "outstandingArray" => $outstanding,
      // "penerimaanArray" => $penerimaan
    ]);
  }

  public function loadAll () {
    $kodemenu = '210301';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    // Dinonaktifkan 2026-10-21: hasilnya tidak dipakai/dikirim, hanya menambah 6 query menu.
    // $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0();

    // Ditambah 2026-10-21: ?bagian=outstanding|penerimaan|periode -> hanya bagian itu yang diambil,
    // supaya halaman bisa memuat ketiganya bersamaan (paralel). Tanpa parameter = semua, seperti dulu.
    $bagian = request()->input('bagian');

    // sp_sosiapkirim

    // DB::connection("SML")->statement('exec sp_sosiapkirim ?,?,?',[$periode->bulan , $periode->tahun,\Auth::user()->username; ]);
  
    // $outstanding = vwOutBarang::whereMonth('TANGGAL', '=', $periode->bulan)->whereYear('TANGGAL', '=', $periode->tahun)->
    //     orderBy('TANGGAL', 'DESC')->selectRaw('vwOutBarang.*')->get();
//
    //asli
   // Dinonaktifkan 2026-10-21: diganti versi yang bisa dipanggil per bagian (isi sama).
   // $outstandingReguler = DB::connection("SML")->select('exec sp_sosiapkirim ?,?,?',[ $periode->bulan , $periode->tahun ,\Auth::user()->username ]);
   $outstandingReguler = (!$bagian || $bagian === 'outstanding') ? DB::connection("SML")->select('exec sp_sosiapkirim ?,?,?',[ $periode->bulan , $periode->tahun ,\Auth::user()->username ]) : [];
  //   $outstandingReguler = DB::connection("SML")->select('exec sp_sosiapkirim ?,?,?',[ 1 , 2026 ,'sa' ]);
  //   $outstandingReguler = [];


 //asli
//$outstanding = DB::connection("SML")->select('exec sp_sosiapkirimBooking ?,?,?',[ $periode->bulan , $periode->tahun ,\Auth::user()->username ]);
    $outstanding =[];	  
  
// nobukti , tanggal, nama cust, nama barang, pocust, qntso, qty, stock , sat, catatan, lokasipenerima, alamat kirim, duedate, pr, part number, merk, userid
    // $penerimaan = vwShowOutBrg::whereMonth('TANGGAL', '=', $periode->bulan)->whereYear('TANGGAL', '=', $periode->tahun)->
    //     orderBy('NOBUKTI', 'DESC')->selectRaw('vwShowOutBrg.*')->get();

    // $penerimaan = DB::connection("SML")->select("select * from vwShowOutBrg where NOBUKTI LIKE '%/OB/%' and Bulan = :bulan and Tahun = :tahun" , ["bulan" => $periode->bulan ,"tahun" => $periode->tahun  ]);
   // $penerimaan = vwShowOutBrg::all()->where('Bulan' ,$periode->bulan)->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NOBUKTI');
    
//asli
    // Dinonaktifkan 2026-10-21: diganti versi yang bisa dipanggil per bagian (isi sama).
    // $penerimaan = vwShowOutBrg::all()->sortBy('URUT')->groupBy('NOBUKTI');
    $penerimaan = (!$bagian || $bagian === 'penerimaan') ? vwShowOutBrg::all()->sortBy('URUT')->groupBy('NOBUKTI') : [];
  //      $penerimaan =[];
    $tempPenerimaan = [];
    foreach ($penerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }
//asli
    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaanperiod = vwShowOutBrgperiod::all()->where('Bulan' ,$periode->bulan)->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NOBUKTI');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    // $penerimaanperiod = vwShowOutBrgperiod::query()->where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('URUT')->groupBy('NOBUKTI');
    $penerimaanperiod = (!$bagian || $bagian === 'periode') ? vwShowOutBrgperiod::query()->where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('URUT')->groupBy('NOBUKTI') : [];
 //$penerimaanperiod =[];


$tempPenerimaanperiod = [];
    foreach ($penerimaanperiod as $p2) {
      // code...
      array_push($tempPenerimaanperiod, $p2);
    }



    return [
      "outstanding" => $outstanding,
      "outstandingReguler" => $outstandingReguler,
      "penerimaan" => $tempPenerimaan,
      "akses" => $akses,
      "penerimaanperiod" => $tempPenerimaanperiod
    ];
  }

  public function detailPenerimaan (Request $req) {
    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaan = vwShowOutBrg::all()->where('NOBUKTI' ,$req->input('nobukti'))->sortBy('URUT');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = vwShowOutBrg::query()->whereRaw('NOBUKTI = cast(? as varchar(100))', [$req->input('nobukti')])->get()->sortBy('URUT');
    $tempPenerimaan = [];
    foreach ($penerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }
    return $tempPenerimaan;
  }

  public function detailPenerimaanperiod (Request $req) {
    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaanperiod = vwShowOutBrgperiod::all()->where('NOBUKTI' ,$req->input('nobukti'))->sortBy('URUT');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaanperiod = vwShowOutBrgperiod::query()->whereRaw('NOBUKTI = cast(? as varchar(100))', [$req->input('nobukti')])->get()->sortBy('URUT');
    $tempPenerimaanperiod = [];
    foreach ($penerimaanperiod as $p2) {
      // code...
      array_push($tempPenerimaanperiod, $p2);
    }
    return $tempPenerimaanperiod;
  }


  public function getNoBukti (Request $req) {

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    // $inisial = DB::connection("SML")->select('select PBL from DBNOMOR');
    $inisial = DB::connection("SML")->select('select OB from DBNOMOR');
    $username = \Auth::user()->username;
    // return [$periode->bulan,$inisial[0]->PBL,$username];
    $values = [
        $inisial[0]->OB,
        $periode->bulan,
        $periode->tahun,
        $username
    ];
    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
    return $noBukti;
  }

  public function spCheckReguler (Request $req) {
    $data = $req->input('tempData');
    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    $username = \Auth::user()->username;

    $check = 1;
    $tempNoBukti = '';
    foreach ($data as $d) {
      // code...
      $checkSOtemp = DB::connection("SML")->select('exec sp_sosiapkirimCheck ?,?,?,?',[ $periode->bulan, $periode->tahun, $username, $d['Kodebrg'] ]);



      if ($checkSOtemp) {
        $check = 0;
        $tempNoBukti = $checkSOtemp[0]->NoBukti;
      }
    }

    return [
      "check" => $check,
      "nobukti" => $tempNoBukti
    ];




  }

  public function spAdd (Request $req) {

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    $username = \Auth::user()->username;
    $choice = $req->input('choice');
    $tanggal = $req->input('tanggal');
    $tanggal2 = date("Y-m-d");
    $keterangan = $req->input('keterangan');
    $nourut = $req->input('nourut');
    $nobukti = $req->input('nobukti');
    $data = $req->input('tempData');
    $idToken = $username . $req->input('idToken');

    // return [$data,$date,$nourut,$nosj,$norspb];

    $check = DB::connection('SML')->select('select * from DBOutBrg where nobukti = :nobukti',["nobukti" => $nobukti]);
    if ($check) {
      return 2;
    }
    $check = DB::connection('SML')->select('select * from DBOutBrg where idToken = :idToken',["idToken" => $idToken]);
    if ($check) {
      return 3;
    }

    $checkSO = 1;
    $checkSSPSPK = [];
    $flagCheckSSPSPK = 0;

    foreach ($data as $d) {
      // code...
      $checkSOtemp = DB::connection("SML")->select('exec SP_CheckPL ?,?',[ $d['NoBukti'], $d['Urut'] ]);

      if ( $d['Noserah'] != '-') {
        $checkSSPSPK = DB::connection('SML')->select('exec Sp_CekStockAkhirLok ?,?,?,?',[$d['nosat'], $tanggal2 , $d['LocSspSpk'] ,  $d['Kodebrg'] ]);
        if ($checkSSPSPK) {
        if (  (int)$d['QntXz'] > (int)$checkSSPSPK[0]->SALDOQNT ) {
          // return 'a';
          $flagCheckSSPSPK = 1;
        }
        } else {
        // return 'b';
        $flagCheckSSPSPK = 1;
        }
      }

      if ($checkSOtemp) {
        if ($checkSOtemp[0]->Sisa < $d['QntXz']) {
          $checkSO = 0;
        }
      } else {
        $checkSO = 0;
      }
    }

    if ($flagCheckSSPSPK) {
    // code...
    return 4;
    }

    if ($checkSO == 0) {
      return 3;
    }

    // SP_CheckPL
    // $outstanding = DB::connection("SML")->select('exec sp_sosiapkirim ?,?,?',[ 9, 10 ,'zxc']);
    //
    // if ($outstanding) {
    //   return 'asd123';
    // } else {
    //   return 'qwe456';
    // }
    //
    // return [
    //   "a" => $outstanding,
    //   "b" => $outstanding[0],
    //   "c" => $outstanding[0]->NoBukti
    // ];

    DB::connection("SML")->update('
delete dbtempoutbrg where  userinput = :user
delete dbtempoutbrgdet where iduser = :user2
',["user" => $username ,"user2" => $username ]);


    foreach ($data as $d) {
      $values = [
        $choice,
        $nobukti,
        $nourut,
        $tanggal,
        $d['kodegdg'],
        '',
        '',
        $keterangan,
        0,
        $d['Kodebrg'],
        $d['namabrg'],
        $d['QntXz'],
        $d['nosat'],
        $d['satuan'],
        $d['isi'],
        $d['NoBukti'],
        $d['Urut'],
        $username,
        $req->input('tanggalInput'),
        '',
        $d['Tipe'],
        $idToken
      ];
      DB::connection("SML")->statement('exec Sp_TEMPOutBrg ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?',$values);
    }


    $checkTemp = DB::connection("SML")->select('
select A.NOBELI,A.URUTBELI,SUM(A.QNT) QNTPL,CASE WHEN B.NOSAT=1 THEN B.QNT ELSE B.QNT2 END QNTSO
from DBTEMPOutBrgDET A
LEFT OUTER JOIN DBSODET B ON A.NOBELI=B.NOBUKTI AND A.URUTBELI=B.URUT
where A.iduser= :user
GROUP BY A.NOBELI,A.URUTBELI,B.NOSAT,B.QNT,B.QNT2
HAVING SUM(A.QNT) > CASE WHEN B.NOSAT=1 THEN B.QNT ELSE B.QNT2 END ',["user" => $username ]);

$checkSum = DB::connection("SML")->select('select A.KODEBRG,SUM(A.QNT * A.ISI) QNTPL,a.KodeGdg
from DBTEMPOutBrgDET A
LEFT OUTER JOIN DBSODET B ON A.NOBELI=B.NOBUKTI AND A.URUTBELI=B.URUT
where A.iduser= :user
GROUP BY A.KODEBRG,a.KodeGdg',["user" => $username ]);

$flagSum = 0;
// return $checkSum;

// $checkSum = [];
//
// foreach ($checkSumTemp as $cst) {
//   // code...
//   array_push($checkSum, $cst);
// }

// return $checkSum;

// looping check sum per barang/lokasi
foreach ($checkSum as $cs) {
  // code...
  // return $cs;
  $check2 = DB::connection('SML')->select('exec Sp_CekStockAkhir ?,?,?,?',[1 , $tanggal2, $cs->KodeGdg,  $cs->KODEBRG]);


  if ($check2) {
    if ((int)$cs->QNTPL > (int)$check2[0]->SALDOQNT ) {
      // return 'a';
      $flagSum = 1;
    }
  } else {
    // return 'b';
    $flagSum = 1;
  }


}

if ($flagSum) {
  return 8;
}


  if ($checkTemp) {
    return 9;
  }

  // return 1;

  DB::connection("SML")->update('
insert into DBOutBrg
select * from dbtempoutbrg where  userinput = :user
insert into dboutbrgdet ([NOBUKTI]
      ,[URUT]
      ,[KODEBRG]
      ,[NamaBrg]
      ,[KodeGdg]
      ,[KodeRak]
      ,[KodeLokasi]
      ,[QNT]
      ,[NOSAT]
      ,[SATUAN]
      ,[ISI]
      ,[NoBeli]
      ,[UrutBeli]
      ,[NOBATCH]
      ,[pFOC]
      ,[KodeLokTujuan]
      ,[JenisTRansaksi]
      ,[pDebet])

select [NOBUKTI]
      ,[URUT]
      ,[KODEBRG]
      ,[NamaBrg]
      ,[KodeGdg]
      ,[KodeRak]
      ,[KodeLokasi]
      ,[QNT]
      ,[NOSAT]
      ,[SATUAN]
      ,[ISI]
      ,[NoBeli]
      ,[UrutBeli]
      ,[NOBATCH]
      ,[pFOC]
      ,[KodeLokTujuan]
      ,[JenisTRansaksi]
      ,[pDebet] from dbtempoutbrgdet where iduser = :user2
',["user" => $username ,"user2" => $username ]);



      return 1;


  }

  public function spDelete (Request $req) {

    $choice = $req->input('choice');
    $nobukti = $req->input('nobukti');
    $urut = $req->input('urut');


      $values = [
        $choice,
        $nobukti,
        '',
        '',
        '',
        '',
        '',
        '',
        $urut,
        '',
        '',
        0,
        0,
        '',
        0,
        '',
        0,
        '',
        '',
        ''
      ];
      DB::connection("SML")->statement('exec Sp_OutBrg ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?',$values);

      return 1;


  }









}
