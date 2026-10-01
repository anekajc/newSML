<?php

namespace App\Http\Controllers\Master;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
// use App\Model\VWPerkiraan;


// use App\Http\Controllers\NewMenuController;

class MasterSalesController extends Controller
{

  public function index(Request $req) {

    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select("select  b.NAMA nNAMAGROUP, c.NAMAHDGRP nNAMAHDGROUP , d.NamaSubGrp nNAMASUBGROUP, e.Keterangan nNAMASUBKATEGORI, f.NAMAMERK nNAMAMERK , a.*  from DBBARANG a
join DBGROUP b on a.KODEGRP = b.KODEGRP
join DBHDGROUP c on a.KODEGRP = c.KODEGRP and a.KodeHdGrp = c.KODEHDGRP
join dbsubgroup d on a.KODEGRP = d.KodeGrp and a.KodeHdGrp = d.KodeHDGrp and a.KODESUBGRP = d.KodeSubGrp
join DBSubGroupJnsTambah e on a.KODEGRP = e.KodeGrp and a.KodeHdGrp = e.HDGROUP and a.KODESUBGRP = e.KodeSubGrp and a.KODESUBKATEGORI = e.Urut
join DBMERK f on a.KodeMerk = f.KODEMERK where a.KODEGRP = 'BJ'");


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.mastersales' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

  }

  public function spListSupplier () {
    $listSupplier = DB::connection('SML')->select("select * from DBJENISCUSTSUPP");

    return $listSupplier;
  }

  public function spDetailHarga (Request $req) {
    $harga = DB::connection('SML')->select("select * from dbHARGAJUAL where KODEBRG = :kodebarang" , ['kodebarang' => $req->kodebarang]);
    return $harga;
  }

  public function spDetailHargaDetail (Request $req) {
    $harga = DB::connection('SML')->select("select * from dbHARGAJUAL where KODEBRG = :kodebarang and KODEJENISCUSTSUPP = :kodesupplier " , ['kodebarang' => $req->kodebarang , 'kodesupplier' => $req->kodesupplier]);
    return $harga;
  }



  public function spListSelect () {

    $listHeadGroup = DB::connection('SML')->select("select * from DBHDGROUP where KODEGRP = 'BJ'");
    $listSubGroup =  DB::connection('SML')->select("select * from dbSubGroup where KodeGrp = 'BJ'");
    $listSubKategori = DB::connection('SML')->select("select * from DBSubGroupJnsTambah where KodeGrp = 'BJ'");
    $listMerk = DB::connection('SML')->select("select * from DBMERK");

    return [
      'listHeadGroup' => $listHeadGroup ,
      'listSubGroup' => $listSubGroup ,
      'listSubKategori' => $listSubKategori ,
      'listMerk' => $listMerk
    ];
  }

  public function spCheckDBBarang (Request $req) {
    // return $req->kodebarang;
    $checkBarang = DB::connection('SML')->select("select * from dbbarang where KODEBRG like :kodebarang order by KODEBRG desc" , ['kodebarang' => $req->kodebarang]);
    return $checkBarang;
    // select * from DBSubGroupJnsTambah where KodeSubGrp like 'SU%'
    // $listSubKategori = DB::connection('SML')->select("select * from DBSubGroupJnsTambah where KodeSubGrp like :tes" , ['tes' => $req->kodebarang]);
    // return $listSubKategori;
  }


  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('Select A.*,case when A.issales=0 then \'NonSales\'  else \'Sales\' end xsales, B.Nama NamaGdg
        , A.KodeCost, C.NamaCost
from DBKaryawan A
left outer join DBGudang B on B.KodeGdg = A.KodeGdg
left outer join DBCost C on C.KodeCost = A.KodeCost
where issales=1 and aktif=1 and KeyNIK = :keynik
Order by Keynik' , ['keynik' => $req->keynik]);
    return $detail;
  }


  public function loadAll () {
    $listData = DB::connection('SML')->select("Select A.*,case when A.issales=0 then 'NonSales'  else 'Sales' end xsales, B.Nama NamaGdg
        , A.KodeCost, C.NamaCost
from DBKaryawan A
left outer join DBGudang B on B.KodeGdg = A.KodeGdg
left outer join DBCost C on C.KodeCost = A.KodeCost
where issales=1 and aktif=1
Order by Keynik");
    return $listData;
  }

  public function spEdit (Request $req) {
    $edit = DB::connection('SML')->update('update DBKaryawan set KodeGdg = :kodeGdg, KodeCost = :kodeCost where KeyNIK = :keynik' , [
      'kodeGdg' => $req->kodeGdg,
      'kodeCost' => $req->kodeCost,
      'keynik' => $req->keynikTemp
    ]);

    return $edit;
  }

  public function submitAddSalesCust (Request $req) {
    $check = DB::connection('SML')->select('select top 1 1 as ada from DBSalesCustomer where KeyNik=:keynik and KodeCustSupp=:kodeCustSupp', [
      'keynik' => $req->keynik, 'kodeCustSupp' => $req->kodeCustSupp
    ]);
    if ($check) {
      return 'Customer ini sudah terdaftar di sales ini';
    }
    $add = DB::connection('SML')->update('Insert Into DBSalesCustomer (KeyNik,KodeCustSupp,NIK,MingguKe)
    Values(:keynik,:kodeCustSupp,:NIK,:Mingguke)' , [
      'kodeCustSupp' => $req->kodeCustSupp,
      'Mingguke' => $req->Mingguke,
      'keynik' => $req->keynik,
      'NIK' => $req->NIK
    ]);

    return $add;
  }

  public function submitDeleteSalesCust (Request $req) {
    $delete = DB::connection('SML')->update('Delete DBSalesCustomer 
where KeyNik=:keynik and kodecustSupp=:kodeCustSupp ' , [
      'kodeCustSupp' => $req->kodeCustSupp,
      'keynik' => $req->keynik
    ]);

    return $delete;
  }

  public function submitEditSalesCust (Request $req) {
    $edit = DB::connection('SML')->update('Update DBSalesCustomer set MingguKe=:Mingguke, kodeCustSupp = :kodeCustSupp
 where KeyNik=:keynik and kodeCustSupp=:kodeCustSuppOld' , [
      'kodeCustSupp' => $req->kodeCustSupp,
      'kodeCustSuppOld' => $req->kodeCustSuppOld,
      'keynik' => $req->keynik,
      'Mingguke' => $req->Mingguke
    ]);

    return $edit;
  }

  public function submitAddTarget (Request $req) {
    $check = DB::connection('SML')->select('select top 1 1 as ada from DbTargetsales where KeyNik=:keynik and tahun=:Tahun and isnull(MERK,\'\')=:Merk', [
      'keynik' => $req->keynik, 'Tahun' => $req->Tahun, 'Merk' => (string) $req->Merk
    ]);
    if ($check) {
      return 'Target untuk tahun dan merk ini sudah ada';
    }
    $add = DB::connection('SML')->update('Insert Into DbTargetsales (KeyNik,tahun,Rp1,Rp2,Rp3,Rp4,Rp5,Rp6,Rp7,Rp8,Rp9,Rp10,Rp11,Rp12,MERK,TGTSALES)
         Values(:keynik,:Tahun, 0,0,0,0,0,0,0,0,0,0,0,0,:Merk,:TgtSales)' , [
      'TgtSales' => $req->TgtSales,
      'Merk' => $req->Merk,
      'Tahun' => $req->Tahun,
      'keynik' => $req->keynik
    ]);

    return $add;
  }

  public function submitDeleteTarget (Request $req) {
    // Dulu hanya per KeyNik + tahun, jadi menghapus satu baris target ikut menghapus target
    // SEMUA merk di tahun itu. Blade sekarang ikut mengirim Merk baris yang dihapus.
    if ($req->has('Merk')) {
      DB::connection('SML')->update('Delete DbtargetSales
          where KeyNik=:keynik and tahun=:Tahun and isnull(MERK,\'\') = :Merk' , [
        'Tahun' => $req->Tahun,
        'keynik' => $req->keynik,
        'Merk' => (string) $req->Merk
      ]);
      return 1;
    }

    $delete = DB::connection('SML')->update('Delete DbtargetSales
          where KeyNik=:keynik and tahun=:Tahun' , [
      'Tahun' => $req->Tahun,
      'keynik' => $req->keynik
    ]);

    return $delete;
  }

  public function submitEditTarget (Request $req) {
    $edit = DB::connection('SML')->update('Update DbTargetsales set Rp1=0,Rp2=0,Rp3=0,Rp4=0,Rp5=0,Rp6=0,Rp7=0,Rp8=0,Rp9=0,Rp10=0,Rp11=0,Rp12=0,MERK=:Merk,TGTSALES=:TgtSales
        where KeyNik=:keynik and tahun=:Tahun AND MERK=:MerkOld' , [
      'TgtSales' => $req->TgtSales,
      'keynik' => $req->keynik,
      'Merk' => $req->Merk,
      'MerkOld' => $req->Merk,
      'Tahun' => $req->Tahun
    ]);

    return $edit;
  }

  
  public function spDelete (Request $req) {
    $check = DB::connection('SML')->select('select * from DBPPLDET where kodebrg = :kodebarang', ['kodebarang' => $req->kodebarang] );

    if ($check) {
      return 'Barang digunakkan di permintaan pembelian';
    }
    // return 1;
    $delete1 = DB::connection('SML')->update('delete from dbHARGAJUAL where KODEBRG = :kodebarang' , ['kodebarang' => $req->kodebarang ]);
    $delete = DB::connection('SML')->update('delete from DBBARANG where KODEBRG = :kodebarang' , ['kodebarang' => $req->kodebarang]);
    return $delete;
  }

  public function spDeleteHarga (Request $req) {
    // delete from dbSubGroup where KodeSubGrp = 'SBGRP3'
    $delete = DB::connection('SML')->update('delete from dbHARGAJUAL where KODEBRG = :kodebarang and KODEJENISCUSTSUPP = :kodesupplier' , ['kodebarang' => $req->kodebarang, 'kodesupplier' => $req->kodesupplier ]);
    return $delete;
  }


  public function spAddHarga (Request $req) {
//     insert into dbHARGAJUAL (KODEBRG, KODEJENISCUSTSUPP, HARGA1, HARGA2)
// values ('20101030990001' , 'TES1' , 1000, 2000)

  $check = DB::connection('SML')->select('SELECT * FROM dbHARGAJUAL where KODEBRG = :kodebarang and KODEJENISCUSTSUPP = :kodesupplier'  , ['kodebarang' => $req->kodebarang , 'kodesupplier' => $req->kodesupplier]);
  if ($check) {
    return 'Harga sudah ada di database';
  }

  $add = DB::connection('SML')->update('insert into dbHARGAJUAL (KODEBRG, KODEJENISCUSTSUPP, HARGA1, HARGA2) values ( :kodebarang , :kodesupplier , :harga , :harga2)' , ['kodebarang' => $req->kodebarang ,'kodesupplier' => $req->kodesupplier , 'harga' => $req->harga , 'harga2' => $req->harga2 ] );

  return 1;

  }

  public function spAdd (Request $req) {
    // return [
    //   'kodegroup' => $req->kodegroup ,
    //   'kodeheadgroup' => $req->kodeheadgroup ,
    //   'kodesubgroup' => $req->kodesubgroup ,
    //   'kodesubkategori' => $req->kodesubkategori ,
    //   'kodemerk' => $req->kodemerk ,
    //   'kodebarang' => $req->kodebarang ,
    //   'namabarang' => $req->namabarang ,
    //   'namabarang2' => $req->namabarang2 ,
    //   'isagen' => (int)$req->isagen ,
    //   'satuan' => $req->satuan ,
    //   'satuan2' => $req->satuan2 ,
    //   'satuan3' => $req->satuan3 ,
    //   'isi' => (int)$req->isi ,
    //   'isi2' => (int)$req->isi2 ,
    //   'isi3' => (int)$req->isi3 ,
    //   'qtymin' => (int)$req->qtymin ,
    //   'qtymax' => (int)$req->qtymax ,
    //   'toleransi' => (int)$req->toleransi ,
    //   'isberat' => (int)$req->isberat ,
    //   'isaktif' => (int)$req->isaktif ,
    //   'beratvolume' => $req->beratvolume ,
    //   'partnumber' => $req->partnumber ,
    //   'lokasi' => $req->lokasi ,
    //   'kodesku' => $req->kodesku ,
    //   'proses' => (int)$req->proses ,
    //   'istakein' => (int)$req->istakein ];
    $check = DB::connection('SML')->select('SELECT * FROM DBBARANG where KODEBRG = :kodebarang' , ['kodebarang' => $req->kodebarang]);

    if ($check) {
      return 'Kode Barang sudah ada di database';
    }


      $listData = DB::connection('SML')->update('insert into DBBARANG (KODEGRP , KodeHdGrp ,  KODESUBGRP , KODESUBKATEGORI , KodeMerk, KODEBRG , NAMABRG , NamaBrg2 , pAgen , SAT1, SAT2 , ISI1, ISI2, QntMin, QntMax , Tolerate, pBerat, ISAKTIF, Berat, PartNumber, Mlokasi, SKU , Proses, IsTakeIn , pKontrak, Hrg1_1 , Hrg2_1) values ( :kodegroup , :kodeheadgroup ,  :kodesubgroup , :kodesubkategori , :kodemerk , :kodebarang , :namabarang , :namabarang2 , :isagen , :satuan , :satuan2 , :isi , :isi2 , :qtymin , :qtymax , :toleransi , :isberat , :isaktif , :beratvolume , :partnumber , :lokasi , :kodesku , :proses , :istakein , :iskontrak , :harga , :harga2 )' , [
        'kodegroup' => $req->kodegroup ,
        'kodeheadgroup' => $req->kodeheadgroup ,
        'kodesubgroup' => $req->kodesubgroup ,
        'kodesubkategori' => $req->kodesubkategori ,
        'kodemerk' => $req->kodemerk ,
        'kodebarang' => $req->kodebarang ,
        'namabarang' => $req->namabarang ,
        'namabarang2' => $req->namabarang2 ,
        'isagen' => $req->isagen ,
        'satuan' => $req->satuan ,
        'satuan2' => $req->satuan2 ,
        'isi' => $req->isi ,
        'isi2' => $req->isi2 ,
        'qtymin' => $req->qtymin ,
        'qtymax' => $req->qtymax ,
        'toleransi' => $req->toleransi ,
        'isberat' => $req->isberat ,
        'isaktif' => $req->isaktif ,
        'beratvolume' => $req->beratvolume ,
        'partnumber' => $req->partnumber ,
        'lokasi' => $req->lokasi ,
        'kodesku' => $req->kodesku ,
        'proses' => $req->proses ,
        'istakein' => $req->istakein,
        'iskontrak' => $req->iskontrak,
        'harga' => $req->harga,
        'harga2' => $req->harga2  ] );


      return 1;
       // : ,
      //  : , : , :kodebarang , :namabarang ,
       // :namabarang2 , :isagen ,
       // :satuan, :satuan2 , :satuan3, :isi , :isi2 , :isi3 , :qtymin , :qtymax ,
       // :toleransi , :isberat , isaktif , :beratvolume , :partnumber , :lokasi , :kodesku   ]);
    // insert into DBBARANG (KODEGRP , KODESUBGRP , KODESUBKATEGORI , KodeMerk)
    // values ()
  }

public function loadGudang () {
    $listData = DB::connection('SML')->select('SELECT KODEGDG, NAMA, Alamat from DBGUDANG');
    return $listData;
  }

public function loadCosting () {
    $listData = DB::connection('SML')->select('SELECT * from DBCOST');
    return $listData;
  }

  public function loadCustSupp () {
    $listData = DB::connection('SML')->select('select KodeCustSupp KodeCust, NamaCustSupp NamaCust, Alamat, Hari DueDate,  JENIS,IsPpn 
               from vwBrowsCust 
               WHERE KodeCustSupp NOT IN (SELECT KODECUSTSUPP FROM DBSALESCUSTOMER) 
              Group by KodeCustSupp, NamaCustSupp, Alamat, Hari , JENIS,IsPpn
               order by KodeCustSupp');
    return $listData;
  }

  public function loadMerk () {
    $listData = DB::connection('SML')->select('select KodeMerk,NamaMerk  from DBmerk  
                                                 order by KodeMerk');
    return $listData;
  }

  public function loadDataSales (Request $req) {
    $listData = DB::connection('SML')->select('
Select  A.Keynik,A.kodeCustSupp,A.Nik,B.namaCustSupp,C.nama,A.Mingguke from dbsalescustomer A
Left outer join DbCustSupp B on a.kodecustsupp = b.kodecustsupp
left outer join Dbkaryawan C on A.Keynik = C.KeyNik
where A.keyNik= :keynik
order by a.kodeCustSupp',['keynik'=>$req->keynik]);
    return $listData;
  }

  public function loadDataSalesEdit (Request $req) {
    $listData = DB::connection('SML')->select('
Select  A.Keynik,A.kodeCustSupp,A.Nik,B.namaCustSupp,C.nama,A.Mingguke from dbsalescustomer A
Left outer join DbCustSupp B on a.kodecustsupp = b.kodecustsupp
left outer join Dbkaryawan C on A.Keynik = C.KeyNik
where A.keyNik= :keynik and A.KodeCustSupp = :kodeCustSupp
order by a.kodeCustSupp',['keynik'=>$req->keynik, 'kodeCustSupp'=>$req->kodeCustSupp]);
    return $listData;
  }
  
  public function loadKaryawan (Request $req) {
    $listData = DB::connection('SML')->select('SELECt NIK from DBKARYAWAN where keynik = :keynik',['keynik'=>$req->keynik]);
    return $listData;
  }

  public function loadDataTarget (Request $req) {
    $listData = DB::connection('SML')->select('Select  A.*, B.nama ,c.namamerk
from DbtargetSales A
left outer join Dbkaryawan B on A.Keynik = B.KeyNik
left outer join dbmerk c on a.merk=c.kodemerk
where A.keyNik= :keynik
order by a.tahun',['keynik'=>$req->keynik]);
    return $listData;
  }

  public function loadDataTargetEdit (Request $req) {
    $listData = DB::connection('SML')->select('Select  A.*, B.nama ,c.namamerk
from DbtargetSales A
left outer join Dbkaryawan B on A.Keynik = B.KeyNik
left outer join dbmerk c on a.merk=c.kodemerk
where A.keyNik= :keynik and A.Tahun = :Tahun and isnull(A.MERK,\'\') = :Merk
order by a.tahun',['keynik'=>$req->keynik, 'Tahun'=>$req->Tahun, 'Merk'=>(string) $req->Merk]);
    return $listData;
  }

}
