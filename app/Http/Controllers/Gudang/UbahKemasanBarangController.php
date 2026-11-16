<?php

namespace App\Http\Controllers\Gudang;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\DBFLMENU;
use App\Models\NewPeriode;
use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Models\VwPPL;



// use App\Http\Controllers\NewMenuController;

class UbahKemasanBarangController extends Controller {

  public function index(Request $req) {
    $kodemenu = '06023';
    $periode  = app('App\Http\Controllers\GlobalController')->getPeriode();
    $akses = app('App\Http\Controllers\GlobalController')->getAkses1($kodemenu , $req->path());
    $menul0   = app('App\Http\Controllers\NewMenuController')->getMenuL0(6);

    $date1 = date('Y-m-01', mktime(0, 0, 0, $periode->bulan, 1, $periode->tahun));
    $date2 = date('Y-m-t',  mktime(0, 0, 0, $periode->bulan, 1, $periode->tahun));

    $loadAll = $this->fetchList($date1, $date2);

    return view('gudang.ubahkemasanbarang' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "akses" => $akses,
      "date1" => $date1,
      "date2" => $date2,
      "listKMBJ" => $loadAll['listKMBJ'],
      "listSdhOto" => $loadAll['listSdhOto'],
      "gudang" => url('kmbjlistgudang'),
      "barang" => url('kmbjlistbarang')
    ]);
  }

  public function loadAll(Request $req) {
    $date1 = $req->date1;
    $date2 = $req->date2;

    if (!$date1 || !$date2) {
      $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
      $date1 = date('Y-m-01', mktime(0, 0, 0, $periode->bulan, 1, $periode->tahun));
      $date2 = date('Y-m-t',  mktime(0, 0, 0, $periode->bulan, 1, $periode->tahun));
    }

    return $this->fetchList($date1, $date2);
  }

  private function fetchList($date1, $date2) {
    $query = "
      Select * from vwMasterUbahKemasan
      where cast(Tanggal as date) between :date1 and :date2
      ";

    $listBBS = DB::connection("SML")->select($query . " and IsOtorisasi1 = 0
      ", ["date1" => $date1 , "date2" => $date2]);

    $listSdhOto = DB::connection("SML")->select($query . " and IsOtorisasi1 = 1
      ", ["date1" => $date1 , "date2" => $date2]);

    return [
      "listKMBJ" => $listBBS,
      "listSdhOto" => $listSdhOto
    ];
  }

  public function listGudang(Request $req) {
    $filter = $req->get('filter');

    $queryFilter = (!$filter) ? '' : ' and (KodeGdg like :filterKode or Nama like :filterNama) ';
    $query = 'select KodeGdg, Nama, Alamat from dbGudang where isaktif=1 ' . $queryFilter . ' order by KodeGdg';

    $params = (!$filter) ? [] : ["filterKode" => "%$filter%", "filterNama" => "%$filter%"];
    $table = DB::connection('SML')->select($query, $params);

    $kolom = [
                // [0] = nama kolom, [1] = nama header, [2] = tipe data, [3] = isDesimal, [4] = isParamater
                ['KodeGdg', 'Kode Gdg', 'varchar', 0, 1],
                ['Nama', 'Nama Gdg', 'varchar', 0, 0],
                ['Alamat', 'Alamat', 'varchar', 0, 0]
             ];
    $title = "Gudang";

    $direct = $filter && count($table) === 1;
    $direct = $direct && ($table[0]->KodeGdg === $filter || $table[0]->Nama === $filter);

    return ['table' => $table, 'kolom' => $kolom, 'title' => $title, 'direct' => $direct];
  }

  public function listBarang(Request $req) {
    $filter = $req->get('filter');

    if (!$filter) {
        $table = [];
    } else {
      $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();

      $query = "
          Select A.KodeBrg, A.NamaBrg,A.Sat1,A.Sat2, Isnull(b.Qnt,0) QntSaldo, Isnull(b.Qnt2,0) Qnt2Saldo
               , A.sat3, A.isi1, A.isi2, A.isi3
          from dbBarang A
          left Outer Join (select a.KodeGdg,Kodebrg,Sum(SaldoQnt)Qnt,Sum(Saldo2Qnt)Qnt2
                  from DBStockBrg a
                    Left Outer Join dbGudang b On a.KodeGdg=b.KodeGdg where a.Kodegdg=:gudang and Bulan=:bulan and Tahun=:tahun
                  group by a.kodegdg,kodebrg)b On b.kodebrg=a.KodeBrg
          where A.KodeGrp='BJ'
            and (A.KodeBrg like :filterKode or A.NamaBrg like :filterNama)
          and a.isAktif=1
          order by A.KodeBrg
      ";

      $params = ["gudang" => $req->kodegdg, "bulan" => $periode->bulan, "tahun" =>$periode->tahun, "filterKode" => "%$filter%", "filterNama" => "%$filter%"];

      $table = DB::connection('SML')->select($query, $params);
    }

    $kolom = [
                // [0] = nama kolom, [1] = nama header, [2] = tipe data, [3] = isDesimal, [4] = isParamater
                ['KodeBrg', 'Kode Barang', 'varchar', 0, 1],
                ['NamaBrg', 'Nama Barang', 'varchar', 0, 1],
                ['Sat1', 'Sat 1', 'varchar', 0, 1],
                ['Sat2', 'Sat 2', 'varchar', 0, 1],
                ['sat3', '', 'varchar', 0, 1],
                ['isi1', '', 'float', 2, 1],
                ['isi2', '', 'float', 2, 1],
                ['isi3', '', 'float', 2, 1]
             ];
    $title = "Barang";

    $direct = $filter && count($table) === 1;
    $direct = $direct && ($table[0]->KodeBrg === $filter || $table[0]->NamaBrg === $filter);

    return ['table' => $table, 'kolom' => $kolom, 'title' => $title, 'direct' => $direct];
  }

  public function spAdd (Request $req) {
    $data = $req->data;
    $Choice = $data['choice'];
$xurut=0;
//  return ["asd" => $nobukti] ;
     $purut = DB::connection('SML')->select('select * from DBUBAHKEMASANdet where Nobukti = :nobukti', ['nobukti' => $data['nobukti']]);
    if ($purut){

        if ($Choice=='I' ){

        $purut = DB::connection('SML')->select('select max(urut)+1 xurut from DBUBAHKEMASANdet where Nobukti = :nobukti', ['nobukti' => $data['nobukti']]);
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


    if ($Choice =='D'){
      $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $Choice,'UK',$data['nobukti'],'',$xurut,'DBUBAHKEMASANdet');
      }


    if ($Choice == "I" && $data['jmlrecord'] == 0) {
      $check = DB::connection('SML')->select('select * from DBUBAHKEMASAN where NOBUKTI = ?',[$data['nobukti']]);
      if ($check) { return "D;;trans"; }
    }

    $values = [
        'Choice'     => $Choice,
        'NoBukti'    => $data['nobukti'],
        'Tanggal'    => ($Choice=="D") ? "" : $data['tanggal'],
        'Note'       => ($Choice=="D") ? "" : ($data['note'] === null ? "" : $data['note']),
        'urut'       => $data['urut'],
        'Kodebrg'    => ($Choice=="D") ? "" : $data['kodebrg'],
        'Kodegdg'    => ($Choice=="D") ? "" : $data['kodegdg'],
        'Satuan'     => ($Choice=="D") ? "" : $data['satuan'],
        'Nosat'      => ($Choice=="D") ?  0 : $data['nosat'],
        'Isi'        => ($Choice=="D") ?  0 : (float) $data['isi'],
        'QntDB'      => ($Choice=="D") ?  0 : (float) $data['qntdb'],
        'QntCR'      => ($Choice=="D") ?  0 : (float) $data['qntcr'],
        'Harga'      => 0,
        'NoUrut'     => ($Choice=="D") ? "" : $data['nourut'],
        'FlagTipe'   => 0,
        'Biaya'      => ($Choice=="D") ?  0 : (float) $data['biaya'],
        'hpp'        => ($Choice=="D") ?  0 : (float) $data['hpp']
    ];

    $placeholders = implode(',', array_fill(0, count($values), '?'));
    DB::connection('SML')->statement('exec SP_UBAHKEMASAN ' . $placeholders, array_values($values));
    if ($Choice !='D'){
      $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $Choice,'UK',$data['nobukti'],'',$xurut,'DBUBAHKEMASANdet');
      }
    return "T";
  }

  public function onChangeHeader (Request $req) {
    // Nama kolom masuk ke SQL apa adanya (tidak bisa jadi parameter) — batasi ke kolom yang memang
    // boleh diubah dari form.
    $allowedFields = ['NOTE'];
    if (!in_array($req->field, $allowedFields, true)) {
      return response("Field tidak valid", 400);
    }

    $query = 'update DBUBAHKEMASAN set ' . $req->field . ' = :value where NOBUKTI = :nobukti';
    // Textarea kosong sampai sebagai null (ConvertEmptyStringsToNull) — simpan "" seperti spAdd.
    $res = DB::connection('SML')->update($query, ["value" => $req->value ?? "", "nobukti" => $req->nobukti]);
    return $res;
  }

 public function getDetailCetak(Request $req)
  {
      $noBukti = $req->input('NOBUKTI');

      $cetak = DB::connection("SML")->select(
          "EXEC dbo.Sp_CetakUbahKemasan ?",
          [$noBukti]
      );

      $tempCetak1 = [];
      foreach ($cetak as $p) {
          array_push($tempCetak1, $p);
      }

      return $tempCetak1;
  }

  public function getDetail (Request $req) {
    $nobukti = $req->nobukti;

    $list = DB::connection('SML')->select("
      select v.*
           , b.sat1 brgSat1, b.sat2 brgSat2, b.sat3 brgSat3
           , b.isi1 brgIsi1, b.isi2 brgIsi2, b.isi3 brgIsi3
      from vwDetailUbahKemasan v
           left outer join DBBARANG b on v.kodebrg = b.KODEBRG
      where nobukti = :nobukti
      ", ["nobukti" => $nobukti]);

    return [
      "header" => $list,
      "list" => $list
    ];
  }

  public function cekOtorisasi (Request $req) {
    $res = DB::connection('SML')->select("select isOtorisasi1 from DBUBAHKEMASAN where nobukti = :nobukti", ["nobukti" => $req->nobukti ]);
    return $res;
  }

  public function updateOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update DBUBAHKEMASAN set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal where nobukti = :nobukti", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);

    // $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','UK',$req->nobukti,'',0,'DBUBAHKEMASAN');

    return $res;
  }

  public function updateBatalOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update DBUBAHKEMASAN set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL where nobukti = :nobukti", [ "nobukti" => $req->nobukti]);
    // $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','UK',$req->nobukti,$req->pket,0,'DBUBAHKEMASAN');
    return $res;
  }

}