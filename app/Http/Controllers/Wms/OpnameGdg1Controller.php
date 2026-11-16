<?php

namespace App\Http\Controllers\Wms;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;



class OpnameGdg1Controller extends Controller
{




  public function index () {


    $kodemenu = '210406';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();


    $tempMain = DB::connection('SML')->select("
    select c.NAMABRG, YEAR(a.tanggal) TAHUN, MONTH(a.tanggal) BULAN, a.tanggal, a.nourut ,
    case when isnull(B.pVerifikasi,0)=1 then 'Ya' else 'Tidak' End xpverifikasi,
    b.* from dbkoreksiopndet b
    left outer join dbkoreksiopn a   on a.nobukti = b.nobukti
    left outer join dbbarang c on c.kodebrg = b.kodebrg
    where YEAR(a.tanggal) = :tahun and MONTH(a.tanggal) = :bulan
    ",["tahun" => $periode->tahun , "bulan"=> $periode->bulan]);

    // Dinonaktifkan 2026-10-22: dua query di bawah membaca data yang sama dengan $tempMain (hanya
    // disaring selisih & pVerifikasi). Sekarang disaring dari $tempMain (1 query, isi sama persis).
    // $tempOutstanding = DB::connection('SML')->select('
    // select c.NAMABRG, YEAR(a.tanggal) TAHUN, MONTH(a.tanggal) BULAN, a.tanggal, a.nourut , b.* from dbkoreksiopndet b
    // left outer join dbkoreksiopn a   on a.nobukti = b.nobukti
    // left outer join dbbarang c on c.kodebrg = b.kodebrg
    // where isnull(selisih,0) = 0 and YEAR(a.tanggal) = :tahun and MONTH(a.tanggal) = :bulan and b.pVerifikasi = 1
    // ',["tahun" => $periode->tahun , "bulan"=> $periode->bulan]);
    //
    // $tempPenerimaan = DB::connection('SML')->select('
    // select c.NAMABRG, YEAR(a.tanggal) TAHUN, MONTH(a.tanggal) BULAN, a.tanggal, a.nourut , b.* from dbkoreksiopndet b
    // left outer join dbkoreksiopn a   on a.nobukti = b.nobukti
    // left outer join dbbarang c on c.kodebrg = b.kodebrg
    // where isnull(selisih,0) <> 0 and YEAR(a.tanggal) = :tahun and MONTH(a.tanggal) = :bulan and b.pVerifikasi = 1
    // ',["tahun" => $periode->tahun , "bulan"=> $periode->bulan]);
    [$tempOutstanding, $tempPenerimaan] = $this->pisahClearNonClear($tempMain);




    return view('wms.opnamegdg' , [
      "periode" => $periode,
      "outstandingArray" => $tempOutstanding,
      "penerimaanArray" => $tempPenerimaan,
      "mainArray" => $tempMain,
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

    $tempOutstanding = DB::connection('SML')->select("
    select c.NAMABRG, c.ISI1,c.ISI2,c.ISI3, YEAR(a.tanggal) TAHUN, MONTH(a.tanggal) BULAN, a.tanggal, a.nourut , 
    case when isnull(B.pVerifikasi,0)=1 then 'Ya' else 'Tidak' End xpverifikasi,
    b.* from dbkoreksiopndet b
    left outer join dbkoreksiopn a   on a.nobukti = b.nobukti
    left outer join dbbarang c on c.kodebrg = b.kodebrg
    where YEAR(a.tanggal) = :tahun and MONTH(a.tanggal) = :bulan and a.nobukti = :nobukti
    ",["tahun" => $periode->tahun , "bulan"=> $periode->bulan , "nobukti" => $req->nobukti]);
    return $tempOutstanding;

  }


  // public function getDetailPenerimaan (Request $req) {
  //
  //   $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
  //   $tempPenerimaan = DB::connection('SML')->select('
  //   select YEAR(a.tanggal) TAHUN, MONTH(a.tanggal) BULAN, a.tanggal, a.nourut , b.* from dbkoreksiopndet b
  //   left outer join dbkoreksiopn a   on a.nobukti = b.nobukti
  //   where selisih <> 0 and YEAR(a.tanggal) = :tahun and MONTH(a.tanggal) = :bulan and a.nobukti = :nobukti
  //   ',["tahun" => $periode->tahun , "bulan"=> $periode->bulan , "nobukti" => $req->nobukti]);
  //   return $tempPenerimaan;
  // }


  public function loadAll () {

    $kodemenu = '210406';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();



    // Dinonaktifkan 2026-10-22: dua query di bawah membaca data yang sama dengan $tempMain (hanya
    // disaring selisih & pVerifikasi). Sekarang disaring dari $tempMain (1 query, isi sama persis).
    // $tempOutstanding = DB::connection('SML')->select('
    // select c.NAMABRG , YEAR(a.tanggal) TAHUN, MONTH(a.tanggal) BULAN, a.tanggal, a.nourut , b.* from dbkoreksiopndet b
    // left outer join dbkoreksiopn a   on a.nobukti = b.nobukti
    // left outer join dbbarang c on c.kodebrg = b.kodebrg
    // where isnull(selisih,0) = 0 and YEAR(a.tanggal) = :tahun and MONTH(a.tanggal) = :bulan and b.pVerifikasi = 1
    // ',["tahun" => $periode->tahun , "bulan"=> $periode->bulan]);
    //
    // $tempPenerimaan = DB::connection('SML')->select('
    // select c.NAMABRG , YEAR(a.tanggal) TAHUN, MONTH(a.tanggal) BULAN, a.tanggal, a.nourut , b.* from dbkoreksiopndet b
    // left outer join dbkoreksiopn a   on a.nobukti = b.nobukti
    // left outer join dbbarang c on c.kodebrg = b.kodebrg
    // where isnull(selisih,0) <> 0 and YEAR(a.tanggal) = :tahun and MONTH(a.tanggal) = :bulan and b.pVerifikasi = 1
    // ',["tahun" => $periode->tahun , "bulan"=> $periode->bulan]);

    $tempMain = DB::connection('SML')->select("
    select c.NAMABRG, YEAR(a.tanggal) TAHUN, MONTH(a.tanggal) BULAN, a.tanggal, a.nourut ,
     case when isnull(B.pVerifikasi,0)=1 then 'Ya' else 'Tidak' End xpverifikasi,
     b.* from dbkoreksiopndet b
    left outer join dbkoreksiopn a   on a.nobukti = b.nobukti
    left outer join dbbarang c on c.kodebrg = b.kodebrg
    where YEAR(a.tanggal) = :tahun and MONTH(a.tanggal) = :bulan
    ",["tahun" => $periode->tahun , "bulan"=> $periode->bulan]);
    [$tempOutstanding, $tempPenerimaan] = $this->pisahClearNonClear($tempMain);

    return [
      "outstandingArray" => $tempOutstanding,
      "penerimaanArray" => $tempPenerimaan,
      "mainArray" => $tempMain,
      "akses" => $akses
    ];
  }


  // Ditambahkan 2026-10-22: pengganti 2 query lama. Clear = verifikasi & selisih 0 (isnull(selisih,0) = 0
  // and pVerifikasi = 1), Non Clear = verifikasi & selisih <> 0. Hasil sudah dibandingkan dengan query lama.
  private function pisahClearNonClear ($main) {
    $clear = [];
    $nonClear = [];
    foreach ($main as $r) {
      if ((int)$r->pVerifikasi !== 1) continue;
      if ((float)($r->Selisih ?? 0) == 0) {
        $clear[] = $r;
      } else {
        $nonClear[] = $r;
      }
    }
    return [$clear, $nonClear];
  }

  public function cekBarang (Request $req) {
      $date = date("Y-m-d");

      $barang =  DB::connection('SML')->select("select NAMABRG, KODEBRG, SAT1, SAT2, SAT3, ISI1, ISI2, ISI3 from dbBarang where ISAKTIF = 1 and KODEGRP <> 'JS' and kodebrg = :kodebarang", ["kodebarang" => $req->kodebarang] );
      // select * from dbBarangLokasiSIG where BarcodeLoc = 'GSML2101'
      $lokasi = DB::connection('SML')->select('exec Sp_CekStockAkhirLokAll ?,?,?',[1, $date,  $req->kodebarang ]);

      return [
        'barang' =>  $barang,
        'lokasi' => $lokasi

      ];

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


      $checkSaldo = 0;



      $check2 = DB::connection('SML')->select('exec Sp_CekStockAkhirLok ?,?,?,?',[$req->input('nosat'), $date, $lokasi['BarcodeLoc'],  $barang['KODEBRG'] ]);


      if ($check2) {

        // Dinonaktifkan 2026-10-22: (int) membuat stok desimal (mis. 0,5) dianggap 0 lalu ditolak.
        // if ((int)$check2[0]->SALDOQNT == 0) {
        if ((float)$check2[0]->SALDOQNT == 0) {
          $checkSaldo = 1;
        }
      // if ((int)$req->input('qnt') > (int)$check2[0]->SALDOQNT ) {
      //   // return 'a';
      //   $checkSaldo = 1;
      // }
      } else {
      // return 'b';
      $checkSaldo = 1;
      }


      if ($checkSaldo) {
      // code...
      return 3;
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
        ''//koderak sp
      ];
      DB::connection("SML")->statement('exec SP_KOREKSIOPNWEB ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?',$values);

    // }

    return 1;

  }

  public function spVerifikasi (Request $req ) {

    $nobukti =  $req->nobukti;
    $urut = $req->urut;


    $update = DB::connection('SML')->update("update dbkoreksiopndet set pVerifikasi = 1 where nobukti = :nobukti and urut = :urut", ['nobukti' => $nobukti, 'urut' => $urut ] );
    return $update;

  }

  public function spDelete (Request $req) {


    $choice = $req->input('choice');
    $nobukti =  $req->input('nobukti');

    // Ditambahkan 2026-10-22: SP_KOREKSIOPNWEB menolak hapus kalau opname sudah diverifikasi (tidak ada
    // baris pVerifikasi=0), tapi dulu layar tetap "Berhasil hapus opname". Syarat yang sama dicek di sini.
    $belumVerifikasi = DB::connection('SML')->select('select top 1 nobukti from DBKOREKSIOPNDET where nobukti = ? and isnull(pVerifikasi,0) = 0', [$nobukti]);
    if (!$belumVerifikasi) {
      return 3;
    }

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
