<?php

namespace App\Http\Controllers\Gudang;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\DBFLMENU;
use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Model\VwPPL;
use Illuminate\Auth;


// use App\Http\Controllers\NewMenuController;

class PermintaanTransferBarangController extends Controller
{

  public function index(Request $req) {
    $kodemenu = '04101';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses1($kodemenu , $req->path());
    // $akses = DBFLMENU::where('USERID', \Auth::user()->username)-> where('L1', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    // $users = DB::connection("SML")->select('select * from new_users');
    // $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    // $listData = DB::connection('SML')->select('SELECT * FROM DBMERK');

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(6);

    $date1 = $req->date1 ?: date('Y-m-01', strtotime($periode->tahun . '-' . $periode->bulan . '-01'));
    $date2 = $req->date2 ?: date('Y-m-t', strtotime($periode->tahun . '-' . $periode->bulan . '-01'));

    // $outstanding = VwPPL::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->where('IsJasa', 0)->where('pAgen', 1)->groupBy('NoBukti');
    $tempOutstanding = DB::connection("SML")->select("
    DECLARE @Date1 date, @Date2 date

    select @Date1= :date1, @Date2= :date2

    Select A.nobukti, a.NoUrut, a.Tanggal,  A.Note Keterangan, A.NoPenyerahan,
            A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
      A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
      A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
            Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                          Case when A.IsOtorisasi2=1 then 1 else 0 end+
                          Case when A.IsOtorisasi3=1 then 1 else 0 end+
                          Case when A.IsOtorisasi4=1 then 1 else 0 end+
                          Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                      else 1
                end As Bit) NeedOtorisasi
    from dbPRTransfer a
    where	A.Tanggal between @Date1 and @Date2 and A.IsOtorisasi1 = 0
    order by A.NoBukti
    ",["date1" => $date1 , "date2" => $date2]);

    $tempOutstanding2 = DB::connection("SML")->select("
    DECLARE @Date1 date, @Date2 date

    select @Date1= :date1, @Date2= :date2

    Select A.nobukti, a.NoUrut, a.Tanggal,  A.Note Keterangan, A.NoPenyerahan,
            A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
      A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
      A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
            Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                          Case when A.IsOtorisasi2=1 then 1 else 0 end+
                          Case when A.IsOtorisasi3=1 then 1 else 0 end+
                          Case when A.IsOtorisasi4=1 then 1 else 0 end+
                          Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                      else 1
                end As Bit) NeedOtorisasi
    from dbPRTransfer a
    where	A.Tanggal between @Date1 and @Date2 and A.IsOtorisasi1 = 1
    order by A.NoBukti
    ",["date1" => $date1 , "date2" => $date2]);

    return view('gudang.permintaantransferbarang' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "date1" => $date1,
      "date2" => $date2,
      // "users"=> $users,
      "tempOutstanding" => $tempOutstanding,
      "tempOutstanding2" => $tempOutstanding2,
      "listBarangAll" => [] ,
      "akses" => $akses
    ]);

}

  public function loadAll (Request $req) {

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    // Ambil date1/date2 dari filter di toolbar (lihat permintaantransferbarang.blade.php
    // #inputDate1/#inputDate2 -> loadAll()). Kalau kosong (mis. pertama kali render tanpa
    // filter), fallback ke satu bulan periode berjalan, disamakan dengan ubahkemasanbarang.
    $date1 = $req->date1 ?: date('Y-m-01', strtotime($periode->tahun . '-' . $periode->bulan . '-01'));
    $date2 = $req->date2 ?: date('Y-m-t', strtotime($periode->tahun . '-' . $periode->bulan . '-01'));

    $tempOutstanding = DB::connection("SML")->select("
    DECLARE @Date1 date, @Date2 date

    select @Date1= :date1, @Date2= :date2

    Select A.nobukti, a.NoUrut, a.Tanggal,  A.Note Keterangan, A.NoPenyerahan,
            A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
      A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
      A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
            Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                          Case when A.IsOtorisasi2=1 then 1 else 0 end+
                          Case when A.IsOtorisasi3=1 then 1 else 0 end+
                          Case when A.IsOtorisasi4=1 then 1 else 0 end+
                          Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                      else 1
                end As Bit) NeedOtorisasi
    from dbPRTransfer a
    where	A.Tanggal between @Date1 and @Date2 and A.IsOtorisasi1 = 0
    order by A.NoBukti
    ",["date1" => $date1 , "date2" => $date2]);

    $tempOutstanding2 = DB::connection("SML")->select("
    DECLARE @Date1 date, @Date2 date

    select @Date1= :date1, @Date2= :date2

    Select A.nobukti, a.NoUrut, a.Tanggal,  A.Note Keterangan, A.NoPenyerahan,
            A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
      A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
      A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
            Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                          Case when A.IsOtorisasi2=1 then 1 else 0 end+
                          Case when A.IsOtorisasi3=1 then 1 else 0 end+
                          Case when A.IsOtorisasi4=1 then 1 else 0 end+
                          Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                      else 1
                end As Bit) NeedOtorisasi
    from dbPRTransfer a
    where	A.Tanggal between @Date1 and @Date2 and A.IsOtorisasi1 = 1
    order by A.NoBukti
    ",["date1" => $date1 , "date2" => $date2]);

    return [
      "tempOutstanding" => $tempOutstanding,
      "tempOutstanding2" => $tempOutstanding2
    ];
}

  public function cekOtorisasi (Request $req) {
    $res = DB::connection('SML')->select("select isOtorisasi1 from dbprtransfer where nobukti = :nobukti", ["nobukti" => $req->nobukti ]);
    return $res;
  }

  public function onChangeHeader (Request $req) {
    // Nama kolom masuk ke SQL apa adanya (tidak bisa jadi parameter) — batasi ke kolom yang memang
    // bisa diubah dari form (Keterangan).
    $allowedFields = ['NOTE'];
    if (!in_array($req->field, $allowedFields, true)) {
      return response("Field tidak valid", 400);
    }

    $query = 'update dbprtransfer set ' . $req->field . ' = :value where nobukti = :nobukti';
    $res = DB::connection('SML')->update($query, ["value" => $req->value ?? "", "nobukti" => $req->nobukti]);
    return $res;
  }

  // Gudang Asal / Tujuan tidak ada di header dbPRTransfer — disimpan per baris item di
  // dbPRTransferDet (lewat sp_PRTRANSFER). Ganti gudang dari form = pindahkan SEMUA item dokumen
  // ke gudang baru. Respon = jumlah baris item yang ter-update.
  public function onChangeGudang (Request $req) {
    $kolom = ['asal' => 'GdgAsal', 'tujuan' => 'GdgTujuan'];
    if (!isset($kolom[$req->jenis])) {
      return response("Jenis gudang tidak valid", 400);
    }
    $field = $kolom[$req->jenis];
    $lawan = $req->jenis === 'asal' ? 'GdgTujuan' : 'GdgAsal';

    $nobukti = $req->nobukti;
    $kode = trim((string) $req->kode);
    if ($kode === '') {
      return response("Gudang tidak boleh kosong", 400);
    }

    $gudang = DB::connection('SML')->select('select KodeGdg from dbGudang where KodeGdg = :kode', ["kode" => $kode]);
    if (!$gudang) {
      return response("Gudang tidak ditemukan", 400);
    }

    $header = DB::connection('SML')->select('select isOtorisasi1 from dbprtransfer where nobukti = :nobukti', ["nobukti" => $nobukti]);
    if (!$header) {
      return response("No Bukti tidak ditemukan", 400);
    }
    if ((int) $header[0]->isOtorisasi1 === 1) {
      return response("Sudah diotorisasi", 400);
    }

    $sama = DB::connection('SML')->select("select top 1 1 x from dbPRTransferDet where NoBukti = :nobukti and $lawan = :kode", ["nobukti" => $nobukti, "kode" => $kode]);
    if ($sama) {
      return response("Gudang Asal dan Gudang Tujuan tidak boleh sama", 400);
    }

    $res = DB::connection('SML')->update("update dbPRTransferDet set $field = :kode where NoBukti = :nobukti", ["kode" => $kode, "nobukti" => $nobukti]);
    return $res;
  }

  public function updateOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbprtransfer set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal where nobukti = :nobukti", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
   
   $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','PRT',$req->nobukti,'',0,'dbprtransfer');
    return $res;
  }
  
  public function updateBatalOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbprtransfer set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL where nobukti = :nobukti", [ "nobukti" => $req->nobukti]);
     $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','PRT',$req->nobukti,$req->pket,0,'dbprtransfer');
    return $res;
  }

  public function onChangeHeaderSP (Request $req) {
    $query = 'update dbso set ' . $req->field . ' = :value where nobukti = :nobukti';
    $res = DB::connection('SML')->update($query, ["value" => $req->value , "nobukti" => $req->nobukti]);

    $res2 = DB::connection('SML')->select('exec Sp_UpdateSO ?', [$req->bukti]);

    return $res;

  }

  public function spUpdatePO (Request $req) {
    $res = DB::connection('SML')->update('exec Sp_UpdatePO ?', [$req->nobukti]);
    
    return $res;
  }

  public function getDetailCetak(Request $req)
  {
      $noBukti = $req->input('NOBUKTI');

      $cetak = DB::connection("SML")->select(
          "EXEC dbo.CetakprTransfer ?",
          [$noBukti]
      );

      $tempCetak1 = [];
      foreach ($cetak as $p) {
          array_push($tempCetak1, $p);
      }

      return $tempCetak1;
  }

  public function getNoBukti (Request $req) {

    $username = \Auth::user()->username;
    $periode = DB::connection("SML")->select('select TOP 1 * from DBPERIODE where user_id = :username ' , ["username" => $username]);
    $inisial = DB::connection("SML")->select('select PO from DBNOMOR');

    $values = [
        $inisial[0]->PO,
        $periode[0]->bulan,
        $periode[0]->tahun,
        $username,
        // $periode
        // $periode
    ];
    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
    return $noBukti;
  }

  public function listGudangAsal (Request $req) {

    $listData = DB::connection('SML')->select(" select KodeGdg, Nama NamaGdg, Alamat, IsRusak from dbGudang order by KodeGdg ");
    // TEMP DEBUG: filter exclude dimatikan dulu untuk memastikan tabel/koneksi mengembalikan data.
    // Kalau ini sudah muncul isinya, filter != :kodeGudangTujuan akan dipasang kembali.
    return $listData;
  }

    public function listGudangTujuan (Request $req) {

    $listData = DB::connection('SML')->select(" select KodeGdg, Nama NamaGdg, Alamat, IsRusak from dbGudang order by KodeGdg ");
    // TEMP DEBUG: filter exclude dimatikan dulu untuk memastikan tabel/koneksi mengembalikan data.
    return $listData;
  }

  public function listSales (Request $req) {

    $listData = DB::connection('SML')->select("SELECT keynik, nama FROM dbkaryawan where IsSales = 1");
    return $listData;
  }

  public function listValas (Request $req) {

    $listData = DB::connection('SML')->select("SELECT kodevls, namavls, kurs FROM dbvalas");
    return $listData;
  }

    public function loadOutstandingPPL (Request $req) {

    $listData = DB::connection('SML')->select("declare @Tahun int, @Bulan int

              select @Tahun=2018, @Bulan=78

              SET NOCOUNT ON 
              select  A.NoBukti+' '+right('00000000'+cast(A.urut as varchar(8)),8) KeyUrut,
              A.*
              from DBO.vwOutPPL A WITH(NOLOCK)
              where A.SisaPPL>0
              and A.pjasa=0
              order by A.Tanggal, A.NoBukti, A.Urut");
    return $listData;
  }

  public function listGudang (Request $req) {

    $listData = DB::connection('SML')->select("select KODEGDG, NAMA, Alamat from DBGUDANG");
    return $listData;
  }

  public function listPIC (Request $req) {

    $listData = DB::connection('SML')->select("select kodepic, nama from DBPICCUSTSUPP where KODECUSTSUPP =:kodecustsupp" , ["kodecustsupp" => $req->kodecustsupp]);
    return $listData;
  }

  public function listPWO (Request $req) {

    $listData = DB::connection('SML')->select("SELECT A.no_bukti,a.tanggal,a.supplier,d.NAMACUSTSUPP, 
                        b.kode,c.NAMABRG,F.QNT qty,F.nmsat satuan    
                        ,F.NOSAT NOsat ,B.harga  
                        from penawaran_po A     
                        left outer join detail_penawaran_po_barang B on A.id= 
                        B.penawaran_id    

                        left outer join DBBARANG c on b.kode=c.KODEBRG 
                        left outer join DBCUSTSUPP d on A.supplier=d.KODECUSTSUPP 
                        left outer join DBREFPRDET E on B.id_rfq=E.ID 
                        left outer join DBPENAWARANSODET F on E.NOBUKTI=F.NoRPR  
                        and E.URUT=F.UrutRPR 
                        left outer join DBSOdet G on F.NOBUKTI=G.NOtawar and  
                        f.URUT=G.urutTawar 

                        where G.NOBUKTI= :noSo" , ["noSo" => $req->noSo]);
    return $listData;
  }

  public function listBarangFOC (Request $req)
  {
    $listData = DB::connection('SML')->select("select a.Kodebrg, a.NamaBrg,A.partNumber,B.NamaMerk 
                                                from Dbbarang a 
                                                Left Outer join dbmerk B on A.kodemerk=b.KodeMerk
                                                where a.isaktif=1");
    return $listData;
  }

  public function listBarangNonFOC1 (Request $req) 
  {
    $listData = DB::connection('SML')->select("SELECT a.KodeBrg, a.NamaBrg,a.PartNumber,a.NAMAMERK, a.Sat, a.NoSat, a.Isi, a.Qnt, a.QntPO, a.SisaPPL, a.NoBukti, a.Urut,a.tolerate,A.NosoCust 
                                                from vwOutPPL a  
                                                where Isjasa= 0
                                                order by a.KodeBrg, a.NoSat, a.NoBukti");
    return $listData;
  }

  public function listBarangNonFOC2 (Request $req) 
  {
    $listData = DB::connection('SML')->select("SELECT a.KodeBrg, B.NamaBrg, a.Qnt,a.Qnt2, a.SATUAN Sat,A.Qnt-ISnull(C.Qnt,0) SisaPPL,
                        A.Qnt2- Case When a.NoSAT=2 Then ISnull(C.Qnt2,0) When a.NoSAT=3 Then ISnull(C.Qnt2,0) else ISnull(C.Qnt2,0)*a.ISI end  Sisa2PPL, 
                        a.NoSat, a.Isi, a.NoBukti, a.Urut,0 Tolerate 
                        , B.PartNumber 
                         from DBSODET a   
                        Left Outer Join Dbbarang B on A.kodebrg=B.Kodebrg 
                        left Outer Join (select NoPPL,UrutPPL,Sum(case when nosat=1 then Qnt else Qnt*ISI End) - Sum(case when nosat=1 then QntBatal else QntBatal*ISI End) Qnt   
                        ,Sum(case when Nosat=2 then Qnt   
                        when NOSAT=3 then Qnt                         
                        when NOSAT=1 then Qnt/ISI  End )-            
                        Sum(case when Nosat=2 then QntBatal          
                        when NOSAT=3 then QntBatal                    
                        when NOSAT=1 then QntBatal/ISI  End ) Qnt2 from dbPOdet group by NoPPL,UrutPPL) 
                        C on A.nobukti=C.noppl and A.urut=C.urutPPL 
                         where  isnull(B.Isjasa,0)=0 and  nobukti= :noSo and IsCetakKitir=1 
                        And A.Qnt-ISnull(C.Qnt,0)>0        
                        ", ["noSo" => $req->noSo]);
    return $listData;
  }

  public function listNoSo (Request $req) {

    $listData = DB::connection('SML')->select("SELECT A.NOBUKTI,A1.Tanggal, A1.NoPesanan    
                                                from DBSODET A   
                                                Left Outer join DBSO A1 ON A.NOBUKTI=A1.NOBUKTI   
                                                Left Outer Join DBBarang B on  A.KOdebrg=B.Kodebrg   
                                                where A.iscetakkitir=1 and 
                                                Cast(Case when Case when A1.IsOtorisasi1=1 then 1 else 0 end+   
                                                Case when A1.IsOtorisasi2=1 then 1 else 0 end+    
                                                Case when A1.IsOtorisasi3=1 then 1 else 0 end+    
                                                Case when A1.IsOtorisasi4=1 then 1 else 0 end+    
                                                Case when A1.IsOtorisasi5=1 then 1 else 0 end=A1.MaxOL then 0     
                                                else 1             
                                                end As Bit)=0  and A.Nobukti in ( 
                                                  select A.NOBUKTI 
                                                  from DBSODET a    
                                                  left Outer Join (select NoPPL,UrutPPL,Sum(Qnt)- Sum(QntBatal) Qnt from dbPOdet group by NoPPL,UrutPPL )   
                                                  C on A.nobukti=C.noppl and A.urut=C.urutPPL      
                                                  where   IsCetakKitir=1 And A.Qnt-ISnull(C.Qnt,0)>0)  
                                                Group By A.NOBUKTI,A1.Tanggal, A1.NoPesanan  
                                                order by A.NOBUKTI,A1.Tanggal");
    return $listData;
  }

  public function listLokasiPenerima (Request $req)
  {
    $listData = DB::connection('SML')->select("SELECT a.KodeCustsupp, a.NamaCustSupp NamaCust, A.Alamat, A.Telpon 
                            from vwBrowsExpedisi A 
                            where a.isaktif=1 
                            Order by a.kodecustsupp");
    return $listData;
  }

  public function listBackOffice (Request $req) {

    $listData = DB::connection('SML')->select("select keynik, fullname from [user] order by keynik" );
    return $listData;
  }

  public function listBarang (Request $req) {

    $listData = DB::connection('SML')->select(" SELECT A.KODEBRG, A.NAMABRG, A.SAT1 Sat_1, A.Sat2 Sat_2, A.Isi2 Isi, 0 IsSet, 0 IsInspeksi, nFix, 1 Nosat, 0 Urut 
            from dbBarang A where A.KodeGrp = 'BJ' and ISaktif=1 
            and (A.KodeBrg like '%" . $req->input('search') . "%' or a.NamaBrg like '%" . $req->input('search') . "%') order by A.KodeBrg");
    return $listData;
  }

  public function cekKreditHari (Request $req) {
    // $harga = DB::connection('SML')->select("select * from dbHARGAJUAL where KODEBRG = :kodebarang" , ['kodebarang' => $req->kodebarang]);
//     select b.NAMAMERK ,  a.* from dbbarang a
// join DBMERK b on a.KodeMerk = b.KODEMERK
//  where a.KODEGRP = 'BJ' and a.pAgen = 1
    $listData = DB::connection('SML')->select("select hari from dbcustsupp where KODECUSTSUPP = :kodepelanggan", ["kodepelanggan" => $req->kodepelanggan]);
    return $listData;
  }

  public function getSatuanBarang (Request $req) {
    return DB::connection('SML')->select("select SAT1, SAT2,SAT3 , ISI1,ISI2,ISI3 from dbbarang where kodebrg = :kodebarang", ["kodebarang" => $req->kodebarang]);

  }

  public function spAdd (Request $req) {

  $xurut=0;
//  return ["asd" => $nobukti] ;
     $purut = DB::connection('SML')->select('select * from DBPRTRANSFERDET where Nobukti = :nobukti', ['nobukti' => $req->Nobukti]);
    if ($purut){

        if ($req->Choice=='I' ){

        $purut = DB::connection('SML')->select('select max(urut)+1 xurut from DBPRTRANSFERDET where Nobukti = :nobukti', ['nobukti' => $req->Nobukti]);
            // return 'uuu';
        $xurut= $purut[0]->xurut;
        }else { 
            // return 'mmm';
            $xurut = $req->Urut;
        }
        
    }else{
        // return 'ttt';
        $xurut=1; 
    }
    // return ["asd" => $xurut] ;



    if ($req->Choice =='D'){
 
      try {
        $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingDataTrans($req->Choice,'PRT',$req->Nobukti,'',$xurut,'DBPRTRANSFERDET');
      } catch (\Throwable $e) {
        \Log::error('LoggingDataTrans gagal (PRT Delete), dilewati: '.$e->getMessage());
      }
      }


      $values = [
        $req->Choice, //Choice
        $req->Nobukti, //NoBukti
        $req->NoUrut, //NoUrut
        $req->Tanggal, //Tanggal
        $req->Note, //TglJatuhTempo
        $req->Urut, //Urut
        $req->Kodebrg, //KodeExp
        $req->GdgAsal, //Keterangan
        $req->GdgTujuan, //KodeVls
        $req->Sat_1, //Kurs
        $req->Sat_2, //PPn
        $req->Qnt, //TipeBayar
        $req->QNt2, //Hari
        $req->NoSat, //Urut
        $req->Isi, // KodeBrg
        \Auth::User()->username, //IdUser
        ''

      ];
      DB::connection('SML')->statement('exec sp_PRTRANSFER ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);

    if ($req->Choice!='D'){
   
      try {
        $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingDataTrans($req->Choice,'PRT',$req->Nobukti,'',$xurut,'DBPRTRANSFERDET');
      } catch (\Throwable $e) {
        \Log::error('LoggingDataTrans gagal (PRT Add/Update), dilewati: '.$e->getMessage());
      }
      }



      return 1;
  }

  public function spCekHarga (Request $req) {
        $harga = DB::connection('SML')->select("Declare @Kodebrg varchar(15)
                                                Set @Kodebrg=:kodebarang
                                                select top 4 b.NOBUKTI,b.TANGGAL,a.KODEBRG,c.NAMABRG,
                                                a.SATUAN,a.QNT,b.KODEVLS,b.KURS,A.HARGA,b.DISCRP,A.NDPP,
                                                ROW_NUMBER() over(PARTITION By A.kodebrg Order by A.kodebrg) as LineNum
                                                ,A.DISCP,A.HrgNetto,A.DiscTot,D.NamaCustSupp
                                                from DBBELIDET A
                                                left outer join DBBELI b on a.NOBUKTI=b.NOBUKTI
                                                left outer join DBBARANG c on a.KODEBRG=c.KODEBRG
                                                Left Outer join dbcustsupp D on B.kodesupp=D.KodeCustSupp
                                                where A.KODEBRG=@Kodebrg
                                                order by b.TANGGAL desc" ,["kodebarang" => $req->kodebarang]);

      return $harga;
  }

  public function cekPoDet (Request $req) {
        $cekPoDet = DB::connection('SML')->select("SELECT * FROM DBPODET WHERE NOBUKTI = 'MGL/PO/00001/0625'");

      return $cekPoDet;
  }

   public function cekSatuanBarang (Request $req) {
        $cekSatuanBarang = DB::connection('SML')->select("select SAT1, ISI1, SAT2, ISI2, SAT3, ISI3 from DBBARANG where KODEBRG = :KodeBrg", ["KodeBrg"=>$req->KodeBrg]);

      return $cekSatuanBarang;
  }

  public function detailBarangAll (Request $req) {
    $barang = DB::connection('SML')->select(" select a.Kodebrg, a.NamaBrg,I.NamaSubGrp,A.PartNumber,J.NAMAMERK,a.ISI1, a.ISI2, a.ISI3,
                                              A.Sat1,A.Sat2 ,A.Sat3,A.pPPN,Isnull(A.QntMin,0) QntMin ,a.Hrg1_1 , a.Hrg2_1, a.Hrg3_1
                                              from DBbarang a
                                              left OUter JOin DbSubgroup I on A.KodeSubGRp=I.KodeSUbgrp and A.KodeHdGrp=i.KodeHDGrp
                                              Left Outer join DbMerk J on A.KodeMerk=J.KodeMerk
                                              where a.isaktif=1 and A.KodeGrp in ('BJ','JS')
                                              and A.KodeBrg = :kodebrg
                                              and isnull(A.Isaktif,0)=1
                                              order by a.Kodebrg ASC" , 
                                              ["kodebrg" => $req->kodebrg] );

    $harga = DB::connection('SML')->select("declare @kodebrg varchar(50),@nosat tinyint
                                            select @kodebrg= :kodebarang ,@nosat= :nosat
                                            select top 1 B.KODEBRG,b.NOSAT,b.HARGA,c.ISI2,c.ISI3, a.TANGGAL, b.SATUAN,
                                            case when @nosat=1 then              +
                                                            case when NOSAT=1 then HARGA when NOSAT=2 then HARGA/c.ISI2 when NOSAT=3 then HARGA/ISI3 end
                                                    when @nosat=2 then
                                                            case when NOSAT=2 then HARGA when NOSAT=1 then HARGA*c.ISI2 when NOSAT=3 then (HARGA/ISI3)*c.ISI2 end
                                                    when @nosat=3 then
                                                            case when NOSAT=3 then HARGA when NOSAT=1 then HARGA*c.ISI3 when NOSAT=2 then (HARGA/ISI2)*c.ISI3 end
                                            End Xharga
                                            from DBBELI a
                                            left outer join DBBELIDET b on a.NOBUKTI=b.NOBUKTI
                                            left outer join DBBARANG c on b.KODEBRG=c.KODEBRG
                                            where b.KODEBRG=@kodebrg
                                            order by a.TANGGAL desc  ",
                                            ["kodebarang" => $req->kodebrg , "nosat" => $req->nosat]);

    return ["barang" => $barang , "harga" => $harga];
  }

  public function getDetail (Request $req) {
    $nobukti = $req->nobukti;

    $list = DB::connection('SML')->select("declare @NoBukti varchar(30)

select 	@NoBukti= :nobukti

select A.NOBUKTI, A.NOURUT, A.TANGGAL, A.note Keterangan,
        B.URUT, B.KODEBRG, C.NamaBrg, '' Jns_Kertas, '' Ukr_Kertas,
        B.QNT, B.QNT2, B.SAT_1, B.SAT_2, B.NoSat, B.ISI, 
        B.gdgAsal, D.NAMA+' ('+B.gdgAsal+')' NamaGgdAsal, D.Alamat AlamatGdgAsal, 0.00 GSM,
        B.gdgTujuan, E.NAMA+' ('+B.gdgTujuan+')' NamaGgdTujuan, E.Alamat AlamatGdgTujuan,
        A.NoPenyerahan
From DBPRTransfer A
Left Outer Join DBPRTransferDET B on B.NoBukti=A.NoBukti
Left Outer Join dbBarang C On C.KodeBrg=B.KodeBrg
left Outer join DBGUDANG D on D.KODEGDG=B.GdgAsal
left Outer join DBGUDANG E on E.KODEGDG=B.GdgTujuan
where a.NoBukti=@NoBukti
order By B.Urut", ["nobukti" => $nobukti]);

    return [
      "list" => $list
    ];
  }
}