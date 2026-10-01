<?php

namespace App\Http\Controllers\Gudang;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\DBFLMENU;
use App\Model\NewPeriode;
use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Model\VwPPL;



// use App\Http\Controllers\NewMenuController;

class PembebananSampleController extends Controller {

  public function index(Request $req) {
    $kodemenu = '06055';
    $periode  = app('App\Http\Controllers\GlobalController')->getPeriode();
    $akses = app('App\Http\Controllers\GlobalController')->getAkses1($kodemenu , $req->path());
    $menul0   = app('App\Http\Controllers\NewMenuController')->getMenuL0(6);

    $loadAll = $this->loadAll();

    return view('gudang.pembebanansample' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "akses" => $akses,
      "listBBS" => $loadAll['listBBS'],
      "listSdhOto" => $loadAll['listSdhOto'],
      "noserahsample" => url('bbslistnoserahsample'),
      "sales" => url('bbslistsales'),
      "customer" => url('bbslistcustomer'),
      "gudang" => url('bbslistgudang'),
      "barang" => url('bbslistbarang')
    ]);
  }

  public function loadAll() {
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    
    $query = "
      declare @Tahun int, @Bulan int, @Flagmenu tinyint,@UserID Varchar(30)

      select @Tahun= :tahun, @Bulan= :bulan

      select distinct A.NoBukti, A.NoUrut, A.Tanggal, 
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2, 
        A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
        A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
          A.ISBATAL, A.USERBATAL, A.TglBatal,
          A.IDUSER,
           Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                             Case when A.IsOtorisasi2=1 then 1 else 0 end+
                             Case when A.IsOtorisasi3=1 then 1 else 0 end+
                             Case when A.IsOtorisasi4=1 then 1 else 0 end+
                             Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                        else 1
                   end As Bit) NeedOtorisasi,A.KodeSLS,B.Nama namaSLS,A.KodeCustSupp,C.NAMACUSTSUPP
      from DBBEBANSAMPLE A
      Left Outer Join dbKaryawan b on A.KodeSLS=B.KeyNIK
      Left outer join DBCUSTSUPP c on A.KodeCustSupp=C.KODECUSTSUPP
      where YEAR(A.Tanggal)=@Tahun and MONTH(A.Tanggal)=@Bulan
      ";
    
    $listBBS = DB::connection("SML")->select($query . " and A.IsOtorisasi1 = 0 order by A.NoBukti
      ", ["bulan" => $periode->bulan , "tahun" =>$periode->tahun]);
    
    $listSdhOto = DB::connection("SML")->select($query . " and A.IsOtorisasi1 = 1 order by A.NoBukti
      ", ["bulan" => $periode->bulan , "tahun" =>$periode->tahun]);

    return [
      "listBBS" => $listBBS,
      "listSdhOto" => $listSdhOto
    ];
  }

  public function listNoSerahSample(Request $req)
{
    $filter = $req->get('filter');

    $queryFilter = (!$filter) ? '' : ' AND (A.Nobukti LIKE :filterNb)';

    $query = "
        SELECT 
            A.Nobukti,
            A.Tanggal,
            A.KodeSls,
            A.Nama AS NamaSales,
            A.KODECUSTSUPP,
            A.NAMACUSTSUPP
        FROM VWSSAMPLE A
        WHERE A.QNT - ISNULL(A.QNTR, 0) - ISNULL(A.QNTB, 0) > 0
        $queryFilter
        GROUP BY 
            A.Nobukti, 
            A.Tanggal, 
            A.KodeSls, 
            A.Nama, 
            A.KODECUSTSUPP, 
            A.NAMACUSTSUPP
    ";

    $params = (!$filter) ? [] : ["filterNb" => "%$filter%"];
    $table = DB::connection('SML')->select($query, $params);

    $kolom = [
        ['Nobukti', 'No. Bukti', 'varchar', 0, 1],
        ['Tanggal', 'Tanggal', 'date', 0, 0],
        ['KodeSls', 'Kode Sales', 'varchar', 0, 1],
        ['NamaSales', 'Nama Sales', 'varchar', 0, 1],
        ['KODECUSTSUPP', 'Kode Customer', 'varchar', 0, 1],
        ['NAMACUSTSUPP', 'Nama Customer', 'varchar', 0, 1],
    ];

    $title = "No Serah Sample";

    $direct = $filter && count($table) === 1;
    $direct = $direct && ($table[0]->Nobukti === $filter);

    return [
        'table' => $table,
        'kolom' => $kolom,
        'title' => $title,
        'direct' => $direct
    ];
}

public function getDetailCetak(Request $req)
  {
      $noBukti = $req->input('NOBUKTI');

      $cetak = DB::connection("SML")->select(
          "EXEC dbo.SP_CetakBebanSample ?",
          [$noBukti]
      );

      $tempCetak1 = [];
      foreach ($cetak as $p) {
          array_push($tempCetak1, $p);
      }

      return $tempCetak1;
  }

  
  // public function listNoSerahSample(Request $req) {
  //   $filter = $req->get('filter');

  //   $queryFilter = (!$filter) ? '' : ' and (A.Nobukti like :filterNb) ';
  //   $query = "
  //     select A.Nobukti,A.tanggal,A.Nama,A.KODECUSTSUPP,A.NAMACUSTSUPP   
  //     FROM VWSSAMPLE A           
  //     WHERE A.QNT-ISNULL(A.QNTR,0)-ISNULL(A.QNTB,0)>0 
  //   " . $queryFilter . " GROUP BY A.Nobukti,A.tanggal";

  //   $params = (!$filter) ? [] : ["filterNb" => "%$filter%"];
  //   $table = DB::connection('SML')->select($query, $params);

  //   $kolom = [
  //               // [0] = nama kolom, [1] = nama header, [2] = tipe data, [3] = isDesimal, [4] = isParamater
  //               ['Nobukti', 'No. Bukti', 'varchar', 0, 1],
  //               ['tanggal', 'Tanggal', 'date', 0, 0]
  //            ];
  //   $title = "No Serah Sample";

  //   $direct = $filter && count($table) === 1;
  //   $direct = $direct && ($table[0]->Nobukti === $filter);

  //   return ['table' => $table, 'kolom' => $kolom, 'title' => $title, 'direct' => $direct];
  // }
  
  public function listSales(Request $req) {
    $filter = $req->get('filter');

    $queryFilter = (!$filter) ? '' : ' and (A.KeyNik like :filterKode or A.Nama like :filterNama or A.NIK like :filterNIK) ';
    $query = "
      select A.KeyNik KodeSls, A.NIK, A.Nama NamaSls 
      from DBKaryawan a
      Where Aktif=1 and IsSales=1 
    " . $queryFilter . " order by A.KeyNik";

    $params = (!$filter) ? [] : ["filterKode" => "%$filter%", "filterNama" => "%$filter%", "filterNIK" => "%$filter%"];
    $table = DB::connection('SML')->select($query, $params);

    $kolom = [
                // [0] = nama kolom, [1] = nama header, [2] = tipe data, [3] = isDesimal, [4] = isParamater
                ['KodeSls', 'No Sales', 'varchar', 0, 1],
                ['NIK', 'NIK', 'varchar', 0, 0],
                ['NamaSls', 'Nama', 'varchar', 0, 0]
             ];
    $title = "Sales";

    $direct = $filter && count($table) === 1;
    $direct = $direct && ($table[0]->KodeSls === $filter || $table[0]->NamaSls === $filter);

    return ['table' => $table, 'kolom' => $kolom, 'title' => $title, 'direct' => $direct];
  }
  
  public function listCustomer(Request $req) {
    $filter = $req->get('filter');

    $queryFilter = (!$filter) ? '' : ' and (Y.KodeCustSupp like :filterKode or Y.NamaCustSupp like :filterNama) ';
    $query = "
      select Y.KodeCustSupp, Y.NamaCustSupp, Y.Alamat1 Alamat, 
      Z.namaKota,Y.PPN,Y.HARI,Y.PPN,Y.Kota      
      from  DBCUSTSUPP Y         
      Left Outer Join Dbkota Z on Y.kota=Z.KodeKota   
      where Y.KodeCustSupp=Y.KodeCustSupp and Y.KOta=Z.KodeKOta  
      and isnull(Y.JENIS,0)=1  and Y.isaktif=1  
    " . $queryFilter . " order by Y.KODECUSTSUPP";

    $params = (!$filter) ? [] : ["filterKode" => "%$filter%", "filterNama" => "%$filter%"];
    $table = DB::connection('SML')->select($query, $params);

    $kolom = [
                // [0] = nama kolom, [1] = nama header, [2] = tipe data, [3] = isDesimal, [4] = isParamater
                ['KodeCustSupp', 'Kode', 'varchar', 0, 1],
                ['NamaCustSupp', 'Nama', 'varchar', 0, 0],
                ['Alamat', 'Alamat', 'varchar', 0, 0],
                ['namaKota', 'Kota', 'varchar', 0, 0]
             ];
    $title = "Customer";

    $direct = $filter && count($table) === 1;
    $direct = $direct && ($table[0]->KodeCustSupp === $filter || $table[0]->NamaCustSupp === $filter);

    return ['table' => $table, 'kolom' => $kolom, 'title' => $title, 'direct' => $direct];
  }
  
  public function listCustomer_x(Request $req) {
    $query = "
        select Y.KodeCustSupp, Y.NamaCustSupp, Y.Alamat1 Alamat, 
        Z.namaKota,Y.PPN,Y.HARI,Y.PPN,Y.Kota      
        from  DBCUSTSUPP Y         
        Left Outer Join Dbkota Z on Y.kota=Z.KodeKota   
        where Y.KodeCustSupp=Y.KodeCustSupp and Y.KOta=Z.KodeKOta  
        and isnull(Y.JENIS,0)=1  and Y.isaktif=1  
         order by Y.KODECUSTSUPP
    ";

    $table = DB::connection('SML')->select($query);
    $kolom = [
                // [0] = nama kolom, [1] = nama header, [2] = tipe data, [3] = isDesimal, [4] = isParamater
                ['KodeCustSupp', 'Kode', 'varchar', 0, 1],
                ['NamaCustSupp', 'Nama', 'varchar', 0, 0],
                ['Alamat', 'Alamat', 'varchar', 0, 0],
                ['namaKota', 'Kota', 'varchar', 0, 0]
             ];
    $title = "Customer";

    return ['table' => $table, 'kolom' => $kolom, 'title' => $title];
  }
  
  public function listBarang(Request $req) {
    $filter = $req->get('filter');
    $noserahsample = $req->noserahsample;

    $queryFilter = (!$filter) ? '' : ' and (A.Nobukti like :filterNb or A.KODEBRG like :filterKode or A.NAMABRG like :filterNama) ';
    $query = "
      select A.Nobukti,A.URUT,A.tanggal,A.KODEBRG,A.NAMABRG,A.QNT    
      ,A.QNTR,A.QNTB,A.QNT-ISNULL(A.QNTR,0)-ISNULL(A.QNTB,0) QNTSISA,A.nfix 
             , Br.sat1, Br.isi1, Br.sat2, Br.isi2, Br.sat3, Br.isi3
      FROM VWSSAMPLE A           
           left outer join DBBARANG Br on Br.KODEBRG=A.KODEBRG   
       WHERE A.QNT-ISNULL(A.QNTR,0)-ISNULL(A.QNTB,0)>0  
    " . $queryFilter . " AND A.NOBUKTI= :noserahsample";

    $params = (!$filter) ? ["noserahsample" => $noserahsample] : ["filterNb" => "%$filter%", "filterKode" => "%$filter%", "filterNama" => "%$filter%", "noserahsample" => $noserahsample];
    $table = DB::connection('SML')->select($query, $params);

    $kolom = [
                // [0] = nama kolom, [1] = nama header, [2] = tipe data, [3] = isDesimal, [4] = isParamater
                ['Nobukti', 'No. Penyerahan', 'varchar', 0, 0],
                ['tanggal', 'Tanggal', 'date', 0, 0],
                ['KODEBRG', 'Kode Brg', 'varchar', 0, 1],
                ['NAMABRG', 'Nama Brg', 'varchar', 0, 1],
                ['QNT', 'Qnt', 'float', 2, 1],
                ['QNTR', 'Qnt Retur', 'float', 2, 0],
                ['QNTB', 'Qnt B. Sample', 'float', 2, 0],
                ['QNTSISA', 'Sisa', 'float', 2, 0],
                ['URUT', '', 'int', 0, 1],
                ['sat1', '', 'varchar', 0, 1],
                ['sat2', '', 'varchar', 0, 1],
                ['sat3', '', 'varchar', 0, 1],
                ['isi1', '', 'float', 2, 1],
                ['isi2', '', 'float', 2, 1],
                ['isi3', '', 'float', 2, 1]
             ];
    $title = "Barang";

    $direct = $filter && count($table) === 1;
    $direct = $direct && ($table[0]->KODEBRG === $filter || $table[0]->NAMABRG === $filter);

    return ['table' => $table, 'kolom' => $kolom, 'title' => $title, 'direct' => $direct];
  }
  
  public function listGudang(Request $req) {
    $filter = $req->get('filter');

    $queryFilter = (!$filter) ? '' : ' and (KodeGdg like :filterKode or Nama like :filterNama) ';
    $query = 'select KodeGdg, Nama, Alamat from dbGudang where isaktif=1 ' . $queryFilter . ' order by KodeGdg';

    $params = (!$filter) ? [] : ["filterKode" => "%$filter%", "filterNama" => "%$filter%"];
    $table = DB::connection('SML')->select($query, $params);

    $kolom = [
                // [0] = nama kolom, [1] = nama header, [2] = tipe data, [3] = isDesimal, [4] = isParamater
                ['KodeGdg', 'Kode Gudang', 'varchar', 0, 1],
                ['Nama', 'Nama Gudang', 'varchar', 0, 0],
                ['Alamat', 'Alamat', 'varchar', 0, 0]
             ];
    $title = "Gudang";

    $direct = $filter && count($table) === 1;
    $direct = $direct && ($table[0]->KodeGdg === $filter || $table[0]->Nama === $filter);

    return ['table' => $table, 'kolom' => $kolom, 'title' => $title, 'direct' => $direct];
  }

  public function listSatuan (Request $req) {
    $barang = $req->get('kodebrg');
    $listData = DB::connection('SML')->select("select SAT1, SAT2, SAT3 from DBBARANG where KODEBRG = ?", [$barang]);
    return $listData;
  }
  
  public function getStockSerahSample(Request $req) {
    $stock = DB::connection('SML')->select('select case when NOSAT=1 then QNT else QNT2 end qntsample from DBSERAHSAMPLEDET where NOBUKTI = ? and URUT= ?',[$req->noserahsample, $req->urutserahsample]);

    return [
      "stock" => $stock
    ];
  }

  public function spAdd (Request $req) {
    $data = $req->data;
    $Choice = $data['choice'];

    $xurut=0;
//  return ["asd" => $nobukti] ;
     $purut = DB::connection('SML')->select('select * from DBBEBANSAMPLEdet where Nobukti = :nobukti', ['nobukti' => $data['nobukti']]);
    if ($purut){

        if ($Choice=='I' ){

        $purut = DB::connection('SML')->select('select max(urut)+1 xurut from DBBEBANSAMPLEdet where Nobukti = :nobukti', ['nobukti' => $data['nobukti']]);
            // return 'uuu';
        $xurut= $purut[0]->xurut;
        }else { 
            // return 'mmm';
            $xurut = $data['urut'];
        }
        
    }else{
        // return 'ttt';
        $xurut=1; 
    }
    // return ["asd" => $xurut] ;


    if ($Choice =='D'){
      $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData($Choice,'BBS',$data['nobukti'],'',$xurut,'DBBEBANSAMPLEdet');
      }



    if ($Choice == "I" && $data['jmlrecord'] == 0) {
      $check = DB::connection('SML')->select('select * from DBBEBANSAMPLE where NOBUKTI = ?',[$data['nobukti']]);
      if ($check) { return "D;;trans"; }
    }

    if ($Choice != "D") {
      $ISI = ((float) $data['isi'] != 0) ? (float) $data['isi'] : 1;
      $QNT = $data['qnt'];
      $QNT1 = 0; $SAT_1 = '';
      $QNT2 = 0; $SAT_2 = '';
      if ((int) $data['nosat'] === 1) {
        $QNT1 = $QNT;
        $QNT2 = (float) $QNT / $ISI;
      } elseif ((int) $data['nosat'] === 2 || (int) $data['nosat'] === 3) {
        $QNT1 = (float) $QNT / $ISI;
        $QNT2 = $QNT;
      }
    }

    $values = [
        'Choice'            => $Choice,
        'NOBUKTI'           => $data['nobukti'],
        'NoUrut'            => ($Choice=="D") ? "" : $data['nourut'],
        'TANGGAL'           => ($Choice=="D") ? "" : $data['tanggal'],
        'Catatan'           => "",
        'URUT'              => $data['urut'],
        'KODEBRG'           => ($Choice=="D") ? "" : $data['kodebrg'],
        'NoSat'             => ($Choice=="D") ?  0 : $data['nosat'],
        'QNT'               => ($Choice=="D") ?  0 : $QNT,
        'QNT1'              => ($Choice=="D") ?  0 : $QNT1,
        'QNT2'              => ($Choice=="D") ?  0 : $QNT2,
        'SAT_1'             => ($Choice=="D") ? "" : $data['satuan1'],
        'SAT_2'             => ($Choice=="D") ? "" : $data['satuan2'],
        'SAT'               => ($Choice=="D") ? "" : $data['satuan'],
        'ISI'               => ($Choice=="D") ?  0 : $ISI,
        'IDUser'            => ($Choice=="D") ? "" : \Auth::User()->username,
        'IsEmpty'           => ($Choice=="D") ?  0 : $data['jmlrecord'],
        'kodegdg'           => ($Choice=="D") ? "" : $data['kodegdg'],
        'KodeSls'           => ($Choice=="D") ?  0 : $data['kodesls'],
        'KodeCustSupp'      => ($Choice=="D") ? "" : $data['kodecust'],
        'NoRSerahsample'    => ($Choice=="D") ? "" : $data['noserahsample'],
        'UrutRSerahSample'  => ($Choice=="D") ?  0 : $data['urutserahsample']
    ];

    $placeholders = implode(',', array_fill(0, count($values), '?'));
    DB::connection('SML')->statement('exec sp_BEBANSAMPLE ' . $placeholders, array_values($values));
    if ($Choice !='D'){
      $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData($Choice,'BBS',$data['nobukti'],'',$xurut,'DBBEBANSAMPLEdet');
      }
    return "T";
  }


  public function getDetail(Request $req) {
    $nobukti = $req->nobukti;

    $list = DB::connection('SML')->select("
        DECLARE @nobukti VARCHAR(50)
        SET @nobukti = :nobukti

        SELECT  
            B.NOBUKTI, 
            B.URUT, 
            B.KODEBRG, 
            C.NAMABRG,
            B.QNT1, 
            B.QNT2, 
            B.SAT1, 
            B.SAT2, 
            B.ISI,
            B.KODEGDG,
            D.NAMA AS namaGDG,
            A.KodeSLS,
            E.Nama AS NamaSLS,        
            B.Qnt,
            B.Sat,
            A.TANGGAL,
            A.NOURUT,
            A.KodeCustSupp,
            F.NamaCustSupp,              
            B.NoRSerahSample,
            B.UrutRSerahSample,
            C.SAT1 AS brgSat1, 
            C.SAT2 AS brgSat2, 
            C.SAT3 AS brgSat3, 
            C.ISI1 AS brgIsi1, 
            C.ISI2 AS brgIsi2, 
            C.ISI3 AS brgIsi3
        FROM DBBEBANSAMPLEDET B
        LEFT OUTER JOIN DBBEBANSAMPLE A ON B.NOBUKTI = A.NOBUKTI
        LEFT OUTER JOIN DBBARANG C ON C.KODEBRG = B.KODEBRG
        LEFT OUTER JOIN DBGUDANG D ON B.KODEGDG = D.KODEGDG
        LEFT OUTER JOIN DBKARYAWAN E ON A.KodeSLS = E.KeyNIK
        LEFT OUTER JOIN DBCUSTSUPP F ON A.KodeCustSupp = F.KodeCustSupp
        WHERE B.NOBUKTI = @nobukti
        ORDER BY B.URUT
    ", ["nobukti" => $nobukti]);

    return [
        "header" => $list,
        "list" => $list
    ];
}


  public function cekOtorisasi (Request $req) {
    $res = DB::connection('SML')->select("select isOtorisasi1 from DBBEBANSAMPLE where nobukti = :nobukti", ["nobukti" => $req->nobukti ]);
    return $res;
  }

  public function updateOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update DBBEBANSAMPLE set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal where nobukti = :nobukti", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','BBS',$req->nobukti,'',0,'DBBEBANSAMPLE');
    return $res;
  }

  public function updateBatalOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update DBBEBANSAMPLE set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL where nobukti = :nobukti", [ "nobukti" => $req->nobukti]);
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','BBS',$req->nobukti,$req->pket,0,'DBBEBANSAMPLE');
    return $res;
  }

}