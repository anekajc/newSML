<?php

namespace App\Http\Controllers\Wms;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;





class OpnameGdgInspeksiController extends Controller
{



  public function index () {

    $kodemenu = '210407';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();


    $tempOutstanding = DB::connection('SML')->select('
    select c.NAMABRG , YEAR(a.tanggal) TAHUN, MONTH(a.tanggal) BULAN, a.tanggal, a.nourut , b.* from dbkoreksiopndet b
    left outer join dbkoreksiopn a   on a.nobukti = b.nobukti
    left outer join dbbarang c on c.kodebrg = b.kodebrg
    where ( YEAR(a.tanggal) = :tahun and MONTH(a.tanggal) = :bulan ) and ((selisih <> 0  and b.pVerifikasi = 1 and isnull(b.pVerifikasiAudit,0)=0) or (isnull(pkoreksi,0)=1 and isnull(b.pVerifikasiAudit,0)=0))
    ',["tahun" => $periode->tahun , "bulan"=> $periode->bulan]);

    $tempPenerimaan = DB::connection('SML')->select('
    select c.NAMABRG , YEAR(a.tanggal) TAHUN, MONTH(a.tanggal) BULAN, a.tanggal, a.nourut , b.* from dbkoreksiopndet b
    left outer join dbkoreksiopn a   on a.nobukti = b.nobukti
    left outer join dbbarang c on c.kodebrg = b.kodebrg
    where YEAR(a.tanggal) = :tahun and MONTH(a.tanggal) = :bulan and b.pVerifikasiAudit = 1
    ',["tahun" => $periode->tahun , "bulan"=> $periode->bulan]);




    return view('wms.opnamegdginspeksi' , [
      "periode" => $periode,
      "outstandingArray" => $tempOutstanding,
      "penerimaanArray" => $tempPenerimaan,
      "akses" => $akses
    ]);
  }

  public function getNoBukti (Request $req) {

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    $inisial = DB::connection("SML")->select('select KPN from DBNOMOR');
    $username = \Auth::user()->username;

    $values = [
        $inisial[0]->KPN,
        $periode->bulan,
        $periode->tahun,
        $username
    ];
      $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
    return $noBukti;
  }



  public function getDetailOutstanding (Request $req) {

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    $tempOutstanding = DB::connection('SML')->select('
    select c.NAMABRG, c.ISI1,c.ISI2,c.ISI3, YEAR(a.tanggal) TAHUN, MONTH(a.tanggal) BULAN, a.tanggal, a.nourut , b.* from dbkoreksiopndet b
    left outer join dbkoreksiopn a   on a.nobukti = b.nobukti
    left outer join dbbarang c on c.kodebrg = b.kodebrg
    where YEAR(a.tanggal) = :tahun and MONTH(a.tanggal) = :bulan and a.nobukti = :nobukti
    ',["tahun" => $periode->tahun , "bulan"=> $periode->bulan , "nobukti" => $req->nobukti]);
    return $tempOutstanding;

  }





  public function loadAll () {

    $kodemenu = '210407';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();



    $tempOutstanding = DB::connection('SML')->select('
    select c.NAMABRG , YEAR(a.tanggal) TAHUN, MONTH(a.tanggal) BULAN, a.tanggal, a.nourut , b.* from dbkoreksiopndet b
    left outer join dbkoreksiopn a   on a.nobukti = b.nobukti
    left outer join dbbarang c on c.kodebrg = b.kodebrg
    where ( YEAR(a.tanggal) = :tahun and MONTH(a.tanggal) = :bulan ) and ((selisih <> 0  and b.pVerifikasi = 1 and isnull(b.pVerifikasiAudit,0)=0) or (isnull(pkoreksi,0)=1 and isnull(b.pVerifikasiAudit,0)=0))
    ',["tahun" => $periode->tahun , "bulan"=> $periode->bulan]);

    $tempPenerimaan = DB::connection('SML')->select('
    select c.NAMABRG , YEAR(a.tanggal) TAHUN, MONTH(a.tanggal) BULAN, a.tanggal, a.nourut , b.* from dbkoreksiopndet b
    left outer join dbkoreksiopn a   on a.nobukti = b.nobukti
    left outer join dbbarang c on c.kodebrg = b.kodebrg
    where YEAR(a.tanggal) = :tahun and MONTH(a.tanggal) = :bulan and b.pVerifikasiAudit = 1
    ',["tahun" => $periode->tahun , "bulan"=> $periode->bulan]);

    return [
      "outstandingArray" => $tempOutstanding,
      "penerimaanArray" => $tempPenerimaan,
      "akses" => $akses
    ];
  }


  public function cekBarang (Request $req) {

      return DB::connection('SML')->select("select NAMABRG, KODEBRG, SAT1, SAT2, SAT3, ISI1, ISI2, ISI3 from dbBarang where ISAKTIF = 1 and KODEGRP <> 'JS' and kodebrg = :kodebarang", ["kodebarang" => $req->kodebarang] );
      // select * from dbBarangLokasiSIG where BarcodeLoc = 'GSML2101'
  }




  public function cekLokasi (Request $req) {

      return DB::connection('SML')->select("select * from dblokasisig where BarcodeLoc = :kodelokasi", ["kodelokasi" => $req->kodelokasi] );

  }

  public function spAdd (Request $req) {

    $data = $req->input('tempData');
    $choice = $req->input('choice');
    $nobukti =  $req->input('nobukti');
    $nourut = $req->input('nourut');
    $date = date("Y-m-d");
    $barang = $req->input('tempBarangScan');
    $lokasi = $req->input('tempLokasiScan');
    // $note = $req->input('note');
    // $keterangan = $req->input('keterangan');
    // $urut = 0;
    // 'SML/OPN/00002/0123'
    // if($req->input('jmlRecord') == 0) {

      $check = DB::connection('SML')->select('select * from DBKOREKSIOPN where NOBUKTI = :nobukti',["nobukti" => $nobukti]);
      if ($check) {
        return 2;
      }
    // }


    // foreach ($data as $d) {
      $values = [
        $choice,
        $nobukti,
        $nourut,
        $date, // new date () ?
        '', // note ''
        $req->input('urut'),
        $barang['KODEBRG'],
        $lokasi['KodeGdg'],
        0, // saldocomp sp
        $req->input('qnt1'), // qnt1input
        0, // selisih sp
        0, //qntdb sp
        0, //qntcr sp
        $req->input('nosat'),
        $req->input('isi'),
        $barang['ISI1'],
        $barang['ISI2'],
        $barang['ISI3'],
        $req->input('satuan'),
        0, // 0
        '', // keterangan ''
        0, // saldocomp2 sp
        $req->input('qnt'), // qnt2input
        0, // selisih2 sp
        0, // qnt2db sp
        0, // qnt2cr sp
        0, //iscek 0
        0, //iscek2 0
        '', // tipe param g dipakai
        $lokasi['BarcodeLoc'], // kodelokasi barcodeloc input
        ''//koderak sp
      ];
      DB::connection("SML")->statement('exec SP_KOREKSIOPNWEB ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?',$values);

    // }

    return 1;

  }

  public function spKoreksi (Request $req) {

    $data = $req->input('tempData');
    $choice = $req->input('choice');
    $nobukti =  $req->input('nobukti');
    $nourut = $req->input('nourut');
    $date = date("Y-m-d");
    $barang = $req->input('tempBarangScan');
    // $note = $req->input('note');
    // $keterangan = $req->input('keterangan');
    // $urut = 0;
    // 'SML/OPN/00002/0123'
    // if($req->input('jmlRecord') == 0) {


    // }


    // foreach ($data as $d) {
      $values = [
        $choice,
        $nobukti,
        $nourut,
        $date, // new date () ?
        '', // note ''
        $req->input('urut'),
        $barang['KODEBRG'],
        $barang['KODEGDG'],
        0, // saldocomp sp
        $req->input('qnt1'), // qnt1input
        0, // selisih sp
        0, //qntdb sp
        0, //qntcr sp
        $req->input('nosat'),
        $req->input('isi'),
        $barang['ISI1'],
        $barang['ISI2'],
        $barang['ISI3'],
        $req->input('satuan'),
        0, // 0
        '', // keterangan ''
        0, // saldocomp2 sp
        $req->input('qnt'), // qnt2input
        0, // selisih2 sp
        0, // qnt2db sp
        0, // qnt2cr sp
        0, //iscek 0
        0, //iscek2 0
        '', // tipe param g dipakai
        $barang['BARCODELOC'], // kodelokasi barcodeloc input
        '',//koderak sp
        1
      ];
      DB::connection("SML")->statement('exec SP_KOREKSIOPNWEB ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?',$values);

    // }

    return 1;

  }

  public function spVerifikasiAudit (Request $req ) {

    $nobukti =  $req->nobukti;
    $urut = $req->urut;


    $update = DB::connection('SML')->statement("exec sp_updateinspeksi ?,?", [$nobukti, $urut ] );
    return 1;

  }

  public function spDelete (Request $req) {


    $choice = $req->input('choice');
    $nobukti =  $req->input('nobukti');

    // $note = $req->input('note');
    // $keterangan = $req->input('keterangan');
    // $urut = 0;
    // 'SML/OPN/00002/0123'
    // if($req->input('jmlRecord') == 0) {


    // }


    // foreach ($data as $d) {
      $values = [
        $choice,
        $nobukti,
        '',
        '', // new date () ?
        '', // note ''
        $req->input('urut'),
        '',
        '',
        0, // saldocomp sp
        0, // qnt1input
        0, // selisih sp
        0, //qntdb sp
        0, //qntcr sp
        1,
        0,
        0,
        0,
        0,
        0,
        0, // 0
        '', // keterangan ''
        0, // saldocomp2 sp
        0, // qnt2input
        0, // selisih2 sp
        0, // qnt2db sp
        0, // qnt2cr sp
        0, //iscek 0
        0, //iscek2 0
        '', // tipe param g dipakai
        '', // kodelokasi barcodeloc input
        ''//koderak sp
      ];
      DB::connection("SML")->statement('exec SP_KOREKSIOPNWEB ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?',$values);

    // }

    return 1;

  }






}
