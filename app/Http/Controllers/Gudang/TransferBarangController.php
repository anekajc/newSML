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
use App\Model\VwPPL;
use Illuminate\Auth;


// use App\Http\Controllers\NewMenuController;

class TransferBarangController extends Controller
{

  public function index(Request $req) {
    $kodemenu = '04101';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses1($kodemenu , $req->path());
    
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(6);

    $date1 = date('Y-m-01', mktime(0, 0, 0, $periode->bulan, 1, $periode->tahun));
    $date2 = date('Y-m-t',  mktime(0, 0, 0, $periode->bulan, 1, $periode->tahun));

    $listTransfer = $this->fetchHeaderList($date1, $date2);
    $listPermintaan = $this->fetchPermintaanList();
    $listOutstanding = $this->fetchOutstandingList();

    return view('gudang.transferbarang' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "date1" => $date1,
      "date2" => $date2,
      "listTransfer" => $listTransfer,
      "listPermintaan" => $listPermintaan,
      "listOutstanding" => $listOutstanding,
      "listBarangAll" => [] ,
      "akses" => $akses
    ]);
  }

  // Tab "Permintaan Transfer Barang": permintaan (dbPRTransfer) yang sudah diotorisasi
  // (NeedOtorisasi=0) tapi qty-nya belum sepenuhnya dibuatkan transfer (DBTRANSFERDET
  // yang menunjuk balik ke NOPRTRANSFER/URUTPRTRANSFER-nya belum menutup seluruh QNT).
  // Query asli sebelum konsolidasi ke fetchHeaderList() -- domainnya beda dari
  // dbTransferDet/DBTRANSFER punya fetchHeaderList(), jadi tidak bisa disatukan ke sana.
  private function fetchPermintaanList() {
    return DB::connection("SML")->select("Select A.nobukti, a.NoUrut, a.Tanggal,  A.Note Keterangan, A.NoPenyerahan,
            A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
      A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
      A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
            Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                          Case when A.IsOtorisasi2=1 then 1 else 0 end+
                          Case when A.IsOtorisasi3=1 then 1 else 0 end+
                          Case when A.IsOtorisasi4=1 then 1 else 0 end+
                          Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                      else 1
                end As Bit) NeedOtorisasi,
                B.GDGASAL
    from dbPRTransfer a
    Left Outer Join (SELECT A.NOBUKTI, A.GDGASAL
            FROM DBPRtransferDET A
            LEFT OUTER JOIN (select NOPRTRANSFER,URUTPRTRANSFER ,sum(QNT)Qnt1,SUM(QNT2) Qnt2
                      from DBTRANSFERDET  group by NOPRTRANSFER,URUTPRTRANSFER
                    ) B on A.NoBukti=B.NOPRTRANSFER AND A.Urut=B.URUTPRTRANSFER
            WHERE ISNULL(A.QNT,0)-ISNULL(B.Qnt1,0) >0
            GROUP BY A.NoBukti, A.GDGASAL
            )B ON A.NoBukti=B.NoBukti
    left outer join DBGUDANG C on C.KODEGDG = B.GDGASAL
    where
    Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                          Case when A.IsOtorisasi2=1 then 1 else 0 end+
                          Case when A.IsOtorisasi3=1 then 1 else 0 end+
                          Case when A.IsOtorisasi4=1 then 1 else 0 end+
                          Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                      else 1
                end As Bit)=0
    AND B.NoBukti IS not NULL
    and C.pSampit = 0");
  }

  // Query header gabungan: Non-Otorisasi + Otorisasi + Belum/Sudah Diterima.
  // Otorisasi difilter lewat kolom NeedOtorisasi (0 = sudah, 1 = belum),
  // status terima difilter lewat kolom IsTerima (dari pTERIMA), sama seperti pola
  // di ubahkemasanbarang (filter Otorisasi) dan terimatransferbarang (filter Status Terima).
  private function fetchHeaderList($date1, $date2) {
    $query = "
      SELECT
          B.NOBUKTI,
          X.TANGGAL,
          X.NOTE,
          MIN(D.Nama + ' (' + B.GdgAsal + ')') AS NamagdgAsal,
          MIN(E.Nama + ' (' + B.GdgTujuan + ')') AS NamagdgTujuan,
          ISNULL(X.pTERIMA, 0) AS IsTerima,
          ISNULL(X.IsOtorisasi1, 0) AS IsOtorisasi1,
          X.OtoUser1,
          X.TglOto1,
          CAST(
            CASE WHEN
                (CASE WHEN X.IsOtorisasi1 = 1 THEN 1 ELSE 0 END +
                 CASE WHEN X.IsOtorisasi2 = 1 THEN 1 ELSE 0 END +
                 CASE WHEN X.IsOtorisasi3 = 1 THEN 1 ELSE 0 END +
                 CASE WHEN X.IsOtorisasi4 = 1 THEN 1 ELSE 0 END +
                 CASE WHEN X.IsOtorisasi5 = 1 THEN 1 ELSE 0 END) = X.MaxOL
              THEN 0 ELSE 1
            END AS BIT
          ) AS NeedOtorisasi,
          COUNT(*) AS JmlItem
      FROM dbTransferDet B
      LEFT JOIN dbBarang C ON C.KodeBrg = B.KodeBrg
      LEFT JOIN dbGudang D ON D.Kodegdg = B.GdgAsal
      LEFT JOIN dbGudang E ON E.Kodegdg = B.GdgTujuan
      LEFT JOIN DBTRANSFER X ON B.NOBUKTI = X.NOBUKTI
      WHERE
          ISNULL(B.isbatal, 0) = 0
          AND CAST(X.TANGGAL AS DATE) BETWEEN :date1 AND :date2
      GROUP BY
          B.NOBUKTI, X.TANGGAL, X.NOTE, X.pTERIMA,
          X.IsOtorisasi1, X.OtoUser1, X.TglOto1,
          X.IsOtorisasi2, X.IsOtorisasi3, X.IsOtorisasi4, X.IsOtorisasi5, X.MaxOL
      ORDER BY X.TANGGAL DESC, B.NOBUKTI DESC
    ";

    return DB::connection("SML")->select($query, ["date1" => $date1, "date2" => $date2]);
  }

  // Tab "OutStanding Transfer (Transaksi Belum Terima)": item-level (satu baris per
  // barang), port 1:1 dari tempOutstanding3 versi lama (lihat git history sebelum
  // konsolidasi ke fetchHeaderList()) -- kolom dan datanya sengaja dikembalikan persis
  // seperti versi lama (No Bukti/Tanggal/No. Permintaan/Keterangan/Kode Barang/
  // Nama Barang/Qnt), bukan ringkasan header seperti tab Transaksi. Sengaja TIDAK
  // di-scope ke Periode seperti fetchHeaderList() -- Outstanding adalah antrian
  // "belum diterima" yang bisa menumpuk dari periode-periode lama; versi lama ini pun
  // tidak pernah difilter tanggal sama sekali.
  private function fetchOutstandingList() {
    $query = "
      SELECT
          A.NOBUKTI,
          A1.TANGGAL,
          A.NOPRTRANSFER AS NoPermintaan,
          A1.NOTE AS Keterangan,
          A.KODEBRG,
          C.NAMABRG,
          A.QNT
      FROM DBTRANSFERDET A
      LEFT OUTER JOIN (
          SELECT KODEBRG, NoTransfer, UrutTransfer, SUM(QNT) QNT1, SUM(QNT2) QNT2
          FROM DBTRANSFERDET
          GROUP BY KODEBRG, NoTransfer, UrutTransfer
      ) B ON A.NOBUKTI = B.NoTransfer AND A.URUT = B.UrutTransfer
      LEFT OUTER JOIN DBBARANG C ON A.KODEBRG = C.KODEBRG
      LEFT OUTER JOIN DBTRANSFER A1 ON A.NOBUKTI = A1.NOBUKTI
      WHERE ISNULL(A.NOPRTRANSFER, '') <> ''
        AND A.QNT - ISNULL(B.QNT1, 0) <> 0
      ORDER BY A1.TANGGAL DESC, A.NOBUKTI DESC
    ";

    return DB::connection("SML")->select($query);
  }

  public function loadAll (Request $request) {
    $date1 = $request->date1;
    $date2 = $request->date2;

    if (!$date1 || !$date2) {
      $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
      $date1 = date('Y-m-01', mktime(0, 0, 0, $periode->bulan, 1, $periode->tahun));
      $date2 = date('Y-m-t',  mktime(0, 0, 0, $periode->bulan, 1, $periode->tahun));
    }

    return response()->json([
      "listTransfer" => $this->fetchHeaderList($date1, $date2),
      "listPermintaan" => $this->fetchPermintaanList(),
      "listOutstanding" => $this->fetchOutstandingList()
    ]);
  }

  public function cekOtorisasi (Request $req) {
    $res = DB::connection('SML')->select("select isOtorisasi1 from dbpo where nobukti = :nobukti", ["nobukti" => $req->nobukti ]);
    return $res;
  }

  public function onChangeHeader (Request $req) {
    $query = 'update dbpo set ' . $req->field . ' = :value where nobukti = :nobukti';
    $res = DB::connection('SML')->update($query, ["value" => $req->value , "nobukti" => $req->nobukti]);
    return $res;

  }

    public function onChangeQnt (Request $req) {
    $query = 'update dbtransferdet set Qnt = :QNT, Qnt2 = :QNT2 where NoBukti = :nobukti and Urut = :urut';
    $res = DB::connection('SML')->update($query, ["nobukti"=> $req->NoBukti , "urut" => $req->Urut, "QNT" => $req->QNT, "QNT2" => $req->QNT2]);
    return $res;

  }

  public function deleteTransfer(Request $req) {
    $queryDeleteDetail = 'DELETE FROM dbtransferdet WHERE nobukti = :nobukti';
    $queryDelete = 'DELETE FROM DBTRANSFER WHERE nobukti = :nobukti';

    
    
    // Delete details first
    DB::connection('SML')->delete($queryDeleteDetail, ["nobukti" => $req->nobukti]);
    
    // Delete main record
    $res = DB::connection('SML')->delete($queryDelete, ["nobukti" => $req->nobukti]);
    
    return $res;
  }

  public function updateOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbtransfer set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal where nobukti = :nobukti", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','TRF',$req->nobukti,'',0,'DBTRANSFER');
    
    return $res;
  }
  
  public function updateBatalOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbtransfer set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL where nobukti = :nobukti", [ "nobukti" => $req->nobukti]);
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','TRF',$req->nobukti,$req->pket,0,'DBTRANSFER');
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

  public function getDetailCetak(Request $req)
  {
      $noBukti = $req->input('NOBUKTI');

      $cetak = DB::connection("SML")->select(
          "EXEC dbo.CetakTransfer ?",
          [$noBukti]
      );

      $tempCetak1 = [];
      foreach ($cetak as $p) {
          array_push($tempCetak1, $p);
      }

      return $tempCetak1;
  }

  public function listPelanggan (Request $req) {

    $listData = DB::connection('SML')->select("select Y.KodeCustSupp, Y.NamaCustSupp, Y.Alamat1 Alamat,  
                       Z.namaKota,Y.PPN,Y.HARI,Y.PPN,Y.Kota ,Y.NPPH23  ,Y.NPPH22 NPPH21     
                       from  DBCUSTSUPP Y         
                       Left Outer Join Dbkota Z on Y.kota=Z.KodeKota   
                       where isnull(Y.JENIS,0)=0    
                     and Y.IsAktif=1 
                       order by Y.KODECUSTSUPP");
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
    // $harga = DB::connection('SML')->select("select * from dbHARGAJUAL where KODEBRG = :kodebarang" , ['kodebarang' => $req->kodebarang]);
//     select b.NAMAMERK ,  a.* from dbbarang a
// join DBMERK b on a.KodeMerk = b.KODEMERK
//  where a.KODEGRP = 'BJ' and a.pAgen = 1



    $listData = DB::connection('SML')->select("select a.Kodebrg, a.NamaBrg,I.NamaSubGrp,A.PartNumber,J.NAMAMERK,a.ISI1, a.ISI2, a.ISI3,
                    A.Sat1,A.Sat2 ,A.Sat3,A.pPPN,Isnull(A.QntMin,0) QntMin ,a.Hrg1_1 , a.Hrg2_1, a.Hrg3_1
                    from DBbarang a
                    left OUter JOin DbSubgroup I on A.KodeSubGRp=I.KodeSUbgrp and A.KodeHdGrp=i.KodeHDGrp
                    Left Outer join DbMerk J on A.KodeMerk=J.KodeMerk
                    where a.isaktif=1 and A.KodeGrp in ('BJ','JS')
                     and (A.KodeBrg like '%" . $req->input('search') . "%') or (a.NamaBrg like '%" . $req->input('search') . "%')
                    and isnull(A.Isaktif,0)=1
                    order by a.Kodebrg ASC");
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

      $values = [
        $req->Nobukti,
        $req->Nourut,
        $req->Nob,
        $req->urut,
        \Auth::User()->username,
        -1,
        '',
        '',
        $req->tgl
      ];
      DB::connection('SML')->statement('exec sp_InsertPRTRANSFER ?,?,?,?,?,?,?,?,?', $values);
      return 1;
  }

  // Varian spAdd() untuk menambah beberapa item (checkbox) sekaligus. sp_InsertPRTRANSFER
  // hanya menerima satu @urut dan selalu insert header DBTRANSFER di awal -- karena NOBUKTI
  // adalah primary key di DBTRANSFER, SP itu tidak bisa dipanggil berulang kali dengan
  // Nobukti yang sama (akan bentrok PK pada panggilan kedua). Jadi di sini header di-insert
  // sekali saja, lalu detail di-insert satu per satu untuk tiap urut yang dicentang. Rumus
  // QNT/QNT2 mengikuti persis versi terbaru sp_InsertPRTRANSFER (yang menerima @qty):
  // QNT = qty * ISI, QNT2 = qty / ISI jika NOSAT=1, selain itu QNT2 = qty.
  public function spAddMultiple (Request $req) {
      $uruts = $req->uruts;
      $qtys = $req->qtys;

      if (!is_array($uruts) || count($uruts) === 0 || !is_array($qtys) || count($qtys) !== count($uruts)) {
          return 0;
      }

      DB::connection('SML')->beginTransaction();

      try {
          DB::connection('SML')->insert(
              "insert into DBTRANSFER (NoBukti,NoUrut,Tanggal,IDUser,MaxOL,NOTE)
               select :nobukti, :nourut, :tgl, :iduser, :maxol, NOTE
               from DBPRTRANSFER where NoBukti = :nob",
              [
                  "nobukti" => $req->Nobukti,
                  "nourut" => $req->Nourut,
                  "tgl" => $req->tgl,
                  "iduser" => \Auth::user()->username,
                  "maxol" => -1,
                  "nob" => $req->Nob,
              ]
          );

          foreach ($uruts as $i => $urut) {
              $qty = $qtys[$i];

              DB::connection('SML')->insert(
                  "insert into DBTRANSFERDET (NoBukti,Urut,NoPrTRANSFER,URUTPRTRANSFER,KodeBrg,QNT,QNT2,SAT_1,SAT_2,NOSAT,ISI,GdgAsal,GdgTujuan,QtyMinta)
                   select :nobukti, Urut, NoBukti, Urut, KodeBrg,
                          :qty1 * ISI,
                          CASE WHEN NOSAT = 1 THEN :qty2 / ISI ELSE :qty3 END,
                          SAT_1, SAT_2, NOSAT, ISI, GDGASAL, GDGTUJUAN, QNT2
                   from DBPRTRANSFERDET where NoBukti = :nob and URUT = :urut",
                  [
                      "nobukti" => $req->Nobukti,
                      "qty1" => $qty,
                      "qty2" => $qty,
                      "qty3" => $qty,
                      "nob" => $req->Nob,
                      "urut" => $urut,
                  ]
              );
          }

          DB::connection('SML')->commit();

          return 1;
      } catch (\Throwable $e) {
          DB::connection('SML')->rollBack();
          throw $e;
      }
  }

  public function cekQntStock (Request $req) {

      $values = [
        1,
        date('m-d-Y'),
        $req->gudangAsal,
        $req->kodebrg
      ];
      $list = DB::connection('SML')->select('exec Sp_CekStockAkhir ?,?,?,?', $values);
      return $list;
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

  public function getDetail(Request $req)
{
    $nobukti = $req->nobukti;

    $list = DB::connection('SML')->select("
        DECLARE @nOBUKTI VARCHAR(25)
        SELECT @nOBUKTI = :nobukti

        SELECT 
            B.NOBUKTI, 
            B.URUT,  
            B.KODEBRG, 
            C.NAMABRG, 
            '' AS Jns_Kertas, 
            '' AS Ukr_Kertas,
            B.QNT, 
            B.QNT2, 
            B.SAT_1, 
            B.SAT_2, 
            B.ISI, 
            B.GdgAsal, 
            B.GdgTujuan, 
            B.NOSAT, 
            D.Nama + ' (' + B.GdgAsal + ')' AS NamagdgAsal,
            E.Nama + ' (' + B.GdgTujuan + ')' AS NamagdgTujuan, 
            0.00 AS GSM,
            CASE 
                WHEN B.Nosat = 1 THEN B.Sat_1 
                WHEN B.Nosat = 2 THEN B.Sat_2 
            END AS Satuan,
            CASE 
                WHEN B.Nosat = 1 THEN B.Qnt - ISNULL(F.QNT, 0) 
                WHEN B.Nosat = 2 THEN B.Qnt2 - ISNULL(F.QNT2, 0) 
            END AS QTY,
            G.SaldoQnt
        FROM dbPRTransferDet B
        LEFT OUTER JOIN dbBarang C ON C.KodeBrg = B.KodeBrg
        LEFT OUTER JOIN dbGudang D ON D.Kodegdg = B.GdgAsal
        LEFT OUTER JOIN dbGudang E ON E.Kodegdg = B.GdgTujuan
        LEFT OUTER JOIN (
            SELECT 
                NOPRTRANSFER,
                URUTPRTRANSFER,
                SUM(QNT) AS QNT,
                SUM(QNT2) AS QNT2
            FROM DBTRANSFERDET  
            GROUP BY NOPRTRANSFER, URUTPRTRANSFER
        ) F ON B.NOBUKTI = F.NOPRTRANSFER AND B.URUT = F.URUTPRTRANSFER
        LEFT OUTER JOIN dbPRTransfer X ON B.NOBUKTI = X.NOBUKTI
        LEFT OUTER JOIN DBSTOCKBRG G ON 
            B.GDGTUJUAN = G.KODEGDG 
            AND B.KODEBRG = G.KODEBRG 
            AND MONTH(X.Tanggal) = G.Bulan 
            AND YEAR(X.Tanggal) = G.Tahun
        WHERE 
            B.NoBukti = @nOBUKTI
            AND (
                CASE 
                    WHEN B.Nosat = 1 THEN B.Qnt - ISNULL(F.QNT, 0) 
                    WHEN B.Nosat = 2 THEN B.Qnt2 - ISNULL(F.QNT, 0) 
                    ELSE 0 
                END
            ) > 0
    ", ["nobukti" => $nobukti]);

    $listHeader = DB::connection('SML')->select("
        SELECT 
            A.nobukti, 
            A.NoUrut, 
            A.Tanggal,  
            A.Note AS Keterangan, 
            A.NoPenyerahan,
            A.IsOtorisasi1, A.OtoUser1, A.TglOto1, 
            A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
            A.IsOtorisasi3, A.OtoUser3, A.TglOto3, 
            A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
            A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
            CAST(
                CASE 
                    WHEN (
                        CASE WHEN A.IsOtorisasi1 = 1 THEN 1 ELSE 0 END +
                        CASE WHEN A.IsOtorisasi2 = 1 THEN 1 ELSE 0 END +
                        CASE WHEN A.IsOtorisasi3 = 1 THEN 1 ELSE 0 END +
                        CASE WHEN A.IsOtorisasi4 = 1 THEN 1 ELSE 0 END +
                        CASE WHEN A.IsOtorisasi5 = 1 THEN 1 ELSE 0 END
                    ) = A.MaxOL 
                    THEN 0 ELSE 1 
                END AS BIT
            ) AS NeedOtorisasi,
            B.GDGASAL
        FROM dbPRTransfer A
        LEFT OUTER JOIN (
            SELECT 
                A.NOBUKTI, 
                A.GDGASAL
            FROM DBPRtransferDET A
            LEFT OUTER JOIN (
                SELECT 
                    NOPRTRANSFER,
                    URUTPRTRANSFER,
                    SUM(QNT) AS Qnt1,
                    SUM(QNT2) AS Qnt2
                FROM DBTRANSFERDET  
                GROUP BY NOPRTRANSFER, URUTPRTRANSFER
            ) B ON A.NoBukti = B.NOPRTRANSFER AND A.Urut = B.URUTPRTRANSFER
            WHERE ISNULL(A.QNT,0) - ISNULL(B.Qnt1,0) > 0
            GROUP BY A.NoBukti, A.GDGASAL
        ) B ON A.NoBukti = B.NoBukti
        LEFT OUTER JOIN DBGUDANG C ON C.KODEGDG = B.GDGASAL
        WHERE 
            CAST(
                CASE 
                    WHEN (
                        CASE WHEN A.IsOtorisasi1 = 1 THEN 1 ELSE 0 END +
                        CASE WHEN A.IsOtorisasi2 = 1 THEN 1 ELSE 0 END +
                        CASE WHEN A.IsOtorisasi3 = 1 THEN 1 ELSE 0 END +
                        CASE WHEN A.IsOtorisasi4 = 1 THEN 1 ELSE 0 END +
                        CASE WHEN A.IsOtorisasi5 = 1 THEN 1 ELSE 0 END
                    ) = A.MaxOL 
                    THEN 0 ELSE 1 
                END AS BIT
            ) = 0
            AND B.NoBukti IS NOT NULL
            AND C.pSampit = 0 
            AND A.nobukti = :nobukti
    ", ['nobukti' => $nobukti]);

    return [
        "list" => $list,
        "listHeader" => $listHeader
    ];
}

//   public function getDetail (Request $req) {
//     $nobukti = $req->nobukti;

//     $list = DB::connection('SML')->select("
//           DECLARE @nOBUKTI VARCHAR(25)

//           SELECT @nOBUKTI= :nobukti
//           select 	B.NOBUKTI, B.URUT,  B.KODEBRG, C.NAMABRG, '' Jns_Kertas, '' Ukr_Kertas,
//                   B.QNT, B.QNT2, B.SAT_1, B.SAT_2, B.ISI, B.GdgAsal, B.GdgTujuan, B.NOSAT, D.Nama+' ('+B.gdgAsal+')' NamagdgAsal,
//                   E.Nama+' ('+B.GdgTujuan+')' NamagdgTujuan, 0.00 GSM,
//                   case when B.Nosat=1 then B.Sat_1 when B.Nosat=2 Then  B.Sat_2 End Satuan,
//                   case when B.Nosat=1 then B.Qnt-ISNULL(f.QNT,0) when B.Nosat=2 Then  B.Qnt2-ISNULL(F.QNT,0) End QTY,
//                   G.SaldoQnt
//           from	dbPRTransferDet B
//           left outer join dbBarang C on C.KodeBrg=B.KodeBrg
//           left outer join dbGudang D on d.Kodegdg=B.GdgAsal
//           left outer join dbgudang E on E.kodegdg=B.GdgTujuan
//           LEFT OUTER JOIN (SELECT NOPRTRANSFER,URUTPRTRANSFER,SUM(QNT) QNT,SUM(QNT2) QN2
//                           FROM DBTRANSFERDET GROUP BY NOPRTRANSFER,URUTPRTRANSFER )
//                           F ON b.NOBUKTI=F.NOPRTRANSFER AND b.URUT=F.URUTPRTRANSFER
//           Left outer join dbPRTransfer x on b.nobukti=x.nobukti
//           LEFT OUTER JOIN DBSTOCKBRG G ON b.GDGTUJUAN=G.KODEGDG AND B.KODEBRG=G.KODEBRG and  month(x.tanggal)=g.bulan and year(x.tanggal)=g.tahun
//           where	B.NoBukti=@noBUKTI
// ", ["nobukti" => $nobukti]);

// $listHeader = DB::connection('SML')->select("Select A.nobukti, a.NoUrut, a.Tanggal,  A.Note Keterangan, A.NoPenyerahan,
//         A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
//         A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
//         A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
//               Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
//                             Case when A.IsOtorisasi2=1 then 1 else 0 end+
//                             Case when A.IsOtorisasi3=1 then 1 else 0 end+
//                             Case when A.IsOtorisasi4=1 then 1 else 0 end+
//                             Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
//                         else 1
//                   end As Bit) NeedOtorisasi,
//                   B.GDGASAL
//       from dbPRTransfer a
//       Left Outer Join (SELECT A.NOBUKTI, A.GDGASAL
//               FROM DBPRtransferDET A
//               LEFT OUTER JOIN (select NOPRTRANSFER,URUTPRTRANSFER ,sum(QNT)Qnt1,SUM(QNT2) Qnt2
//                         from DBTRANSFERDET  group by NOPRTRANSFER,URUTPRTRANSFER
//                       ) B on A.NoBukti=B.NOPRTRANSFER AND A.Urut=B.URUTPRTRANSFER
//               WHERE ISNULL(A.QNT,0)-ISNULL(B.Qnt1,0) >0
//               GROUP BY A.NoBukti, A.GDGASAL
//               )B ON A.NoBukti=B.NoBukti
//       left outer join DBGUDANG C on C.KODEGDG = B.GDGASAL
//       where 
//       Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
//                             Case when A.IsOtorisasi2=1 then 1 else 0 end+
//                             Case when A.IsOtorisasi3=1 then 1 else 0 end+
//                             Case when A.IsOtorisasi4=1 then 1 else 0 end+
//                             Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
//                         else 1
//                   end As Bit)=0
//       AND B.NoBukti IS not NULL
//       and C.pSampit = 0 and a.nobukti = :nobukti",['nobukti'=>$nobukti]);

      
//     return [
//       "list" => $list,
//       "listHeader" => $listHeader
//     ];
//   }

  public function getDetailEdit (Request $req) {

    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();

    $nobukti = $req->nobukti;

    $list = DB::connection('SML')->select("select B.NOBUKTI, B.URUT,  B.KODEBRG, B.NOSAT, B.QtyMinta, C.NAMABRG, '' Jns_Kertas, '' Ukr_Kertas,
        B.QNT, B.QNT2, B.SAT_1, B.SAT_2, B.ISI, B.GdgAsal, B.GdgTujuan, D.Nama+' ('+B.gdgAsal+')' NamagdgAsal,
        E.Nama+' ('+B.GdgTujuan+')' NamagdgTujuan, 0.00 GSM, F.sisa + case when B.NOSAT = 1 then B.QNT else B.QNT2 end sisa, B.NOSAT, B.NOPRTRANSFER, B.URUTPRTRANSFER
        from	dbTransferDet B
        left outer join dbBarang C on C.KodeBrg=B.KodeBrg
        left outer join dbGudang D on d.Kodegdg=B.GdgAsal
        left outer join dbgudang E on E.kodegdg=B.GdgTujuan
        left outer join (select A.NOBUKTI, A.URUT, case when A.NOSAT = 1 then A.QNT - isnull (B.Qnt1, 0)else A.QNT2 - ISNULL 
        (B.Qnt2, 0) end sisa 
						from DBPRTRANSFERDET A 
						LEFT OUTER JOIN (
								SELECT 
									NOPRTRANSFER,
									URUTPRTRANSFER,
									SUM(QNT) AS Qnt1,
									SUM(QNT2) AS Qnt2
								FROM DBTRANSFERDET  
								GROUP BY NOPRTRANSFER, URUTPRTRANSFER) B on A.NOBUKTI = B.NOPRTRANSFER and A.URUT = B.URUTPRTRANSFER
								) F on B.NOPRTRANSFER = F.NOBUKTI and B.URUTPRTRANSFER = F.URUT
        where	B.NoBukti= :nobukti
        order by B.Urut", ["nobukti" => $nobukti]);

$listHeader = DB::connection('SML')->select("declare @Tahun int, @Bulan int

        select @Tahun= :tahun, @Bulan= :bulan

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
        from dbTransfer a
        where	year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan
        and isnull(pterima,0)=0 and nobukti = :nobukti
        order by A.NoBukti",['nobukti'=>$nobukti, 'tahun'=>$periode->tahun, 'bulan'=>$periode->bulan]);

      
    return [
      "list" => $list,
      "listHeader" => $listHeader
    ];
  }

  

} 