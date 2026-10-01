<?php

namespace App\Http\Controllers\Purchasing;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\DBFLMENU;
use App\Model\NewPeriode;
use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Model\VwPPL;
use Illuminate\Auth;


// use App\Http\Controllers\NewMenuController;

class PerintahReturBeliController extends Controller
{

  // Rentang tanggal default = satu bulan penuh periode kerja user (sama seperti Purchase
  // Order/Uang Muka Beli). Lihat UangMukaBeliController::periodeRange().
  private function periodeRange ($periode) {
    $stamp = mktime(0, 0, 0, (int) $periode->bulan, 1, (int) $periode->tahun);
    return [ date('Y-m-01', $stamp), date('Y-m-t', $stamp) ];
  }

  // Tanggal dari browser bisa berupa tahun setengah jadi (mis. 0002-09-01) saat diketik
  // manual - di luar rentang tipe datetime SQL Server (1753-9999), jadi diabaikan dan
  // dipakai default periode.
  private function tanggalSah ($nilai) {
    return preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $nilai)
        && (int) substr((string) $nilai, 0, 4) >= 1900;
  }

  public function index(Request $req) {
    $kodemenu = '04101';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu, $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(3);
    list($prbTglAwal, $prbTglAkhir) = $this->periodeRange($periode);

    // Baris tabel digambar JS lewat loadAll() (lihat perintahreturbeli.blade.php), jadi
    // index() tidak lagi perlu menyiapkan tempOutstanding1/tempOutstanding3/tempOutstanding5.
    return view('purchasing.perintahreturbeli' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "prbTglAwal" => $prbTglAwal,
      "prbTglAkhir" => $prbTglAkhir,
      "listBarangAll" => [] ,
      "akses" => $akses
    ]);

}

  public function loadAll (Request $req) {
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    list($tglawal, $tglakhir) = $this->periodeRange($periode);
    if ($this->tanggalSah($req->input('tglawal')))  { $tglawal  = $req->input('tglawal'); }
    if ($this->tanggalSah($req->input('tglakhir'))) { $tglakhir = $req->input('tglakhir'); }
    if ($tglawal > $tglakhir) { $tglakhir = $tglawal; }

    $tglawal2 = $tglawal;
    $tglakhir2 = $tglakhir;
    if ($this->tanggalSah($req->input('tglawal2')))  { $tglawal2  = $req->input('tglawal2'); }
    if ($this->tanggalSah($req->input('tglakhir2'))) { $tglakhir2 = $req->input('tglakhir2'); }
    if ($tglawal2 > $tglakhir2) { $tglakhir2 = $tglawal2; }

    // Konfigurasi kolom (urut 1 & urut 2 sekaligus, satu panggilan) - lihat cabang
    // 'perintahreturbeli' di HeaderTableController::getHeaderTable().
    $reqHeader = new Request(['href' => 'perintahreturbeli']);
    $header = app('App\Http\Controllers\HeaderTableController')->getHeaderTable($reqHeader);

    // urut 1 = tabel PRB gabungan (dulu tab "Perintah Retur Beli" + "Sudah Otorisasi") -
    // penyaringan otorisasi dikerjakan di browser lewat modal Filter, sama seperti Purchase
    // Order/Uang Muka Beli. Kolom dialiaskan (NoBukti/Tanggal) supaya sama persis dengan
    // alias di HeaderTableController@getHeaderTable cabang 'perintahreturbeli'. Kolom Status
    // diambil lewat LEFT JOIN ke vwBrowsPrRBeli (Belum/Sebagian/Sudah berdasar qty
    // DBPRRBELIDET vs DBRBELIDET) - lihat prbBadgeStatus() di perintahreturbeli.blade.php.
    $listPRB = DB::connection("SML")->select("declare @tglawal date, @tglakhir date

      select @tglawal= :tglawal, @tglakhir= :tglakhir

      Select  A.NOBUKTI as NoBukti, A.NOURUT, A.TANGGAL as Tanggal, A.TGLJATUHTEMPO,A.KODEEXP, A.HANDLING, A.KETERANGAN, A.FAKTURSUPP,
              A.KODEVLS, A.KURS, A.PPN, A.TIPEBAYAR, A.HARI, A.TipeDisc, A.DISC, A.DISCRP,
              A.NILAIPOT, A.NILAIDPP, A.NILAIPPN, A.NILAINET, A.ISCETAK, A.NilaiCetak,
              A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
        A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
        A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.MAXOL,
              Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                             Case when A.IsOtorisasi2=1 then 1 else 0 end+
                             Case when A.IsOtorisasi3=1 then 1 else 0 end+
                             Case when A.IsOtorisasi4=1 then 1 else 0 end+
                             Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                        else 1
                   end As Bit) NeedOtorisasi
              ,Isnull(A.isbatal,0) IsBatal,A.Userbatal,A.Tglbatal
              ,Isnull(V.status,'Belum') as Status
      From dbPRRBeli A
      Left Outer Join vwBrowsPrRBeli V on V.NOBUKTI = A.NOBUKTI
      where A.Tanggal between @tglawal and @tglakhir
      order by A.Tanggal desc, A.NoBukti desc
      " , ["tglawal" => $tglawal , "tglakhir" => $tglakhir]);

    // urut 2 = tabel "List Retur Jual" - VwOUtPRJUALPRBELI tidak punya kolom tanggal, jadi
    // difilter lewat join ke header SPR (dbSPBRJual, cocok 1:1 lewat Nobukti). Kolom
    // dialiaskan (NomorRetur/Tanggal/KodeBrg/NamaBrg/Satuan/Qty) supaya sama persis dengan
    // alias di HeaderTableController@getHeaderTable cabang 'perintahreturbeli'.
    $listRJual = DB::connection("SML")->select("declare @tglawal2 date, @tglakhir2 date

      select @tglawal2 = :tglawal2, @tglakhir2 = :tglakhir2

      select A.Nobukti as NomorRetur, Cast(B.Tanggal as date) as Tanggal,
             A.Kodebrg as KodeBrg, A.NAMABRG as NamaBrg, A.Satuan as Satuan, A.Qnt as Qty
      from VwOUtPRJUALPRBELI A
        inner join dbSPBRJual B on A.Nobukti = B.NoBukti
      where Cast(B.Tanggal as date) between @tglawal2 and @tglakhir2
      order by B.Tanggal desc, A.Nobukti desc, A.Urut", ["tglawal2" => $tglawal2, "tglakhir2" => $tglakhir2]);

    return [
      "listPRB"            => $listPRB,
      "aliasordered"       => $header['aliasordered'],
      "headertableheader"  => $header['headertableheader'],
      "isnumeric"          => $header['isnumeric'],
      "headertablevalue"   => $header['headertablevalue'],
      "isparsed"           => $header['isparsed'],
      "isshown"            => $header['isshown'],
      "desimal"            => $header['desimal'],
      "listRJual"          => $listRJual,
      "aliasordered2"      => $header['aliasordered2'],
      "headertableheader2" => $header['headertableheader2'],
      "isnumeric2"         => $header['isnumeric2'],
      "headertablevalue2"  => $header['headertablevalue2'],
      "isparsed2"          => $header['isparsed2'],
      "isshown2"           => $header['isshown2'],
      "desimal2"           => $header['desimal2'],
    ];
  }

  public function cekOtorisasi (Request $req) {
    $res = DB::connection('SML')->select("select isOtorisasi1 from DBPRRBELI where nobukti = :nobukti", ["nobukti" => $req->nobukti ]);
    return $res;
  }

  // Update satu kolom header dbPRRBeli (dipakai onblur Keterangan di perintahreturbeli.blade.php).
  // Nama kolom di-whitelist - jangan sisipkan $req->field mentah ke SQL.
  public function onChangeHeader (Request $req) {
    $allowedFields = ['KETERANGAN', 'TIPEBAYAR'];
    if (!in_array($req->field, $allowedFields)) {
      return 0;
    }

    $res = DB::connection('SML')->update(
      'update dbPRRBeli set ' . $req->field . ' = :value where NOBUKTI = :nobukti',
      ["value" => $req->value, "nobukti" => $req->nobukti]
    );

    return $res;
  }

  public function updateOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update DBPRRBELI set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal where nobukti = :nobukti", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
     $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','PRB',$req->nobukti,'',0,'DBPRRBELI');
return $res;
  }
  
  public function updateBatalOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update DBPRRBELI set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL where nobukti = :nobukti", [ "nobukti" => $req->nobukti]);
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','PR',$req->nobukti,$req->pket,0,'DBPRRBELI');
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
                                                where Isjasa= 0 and NoBukti = :nobukti and a.sisaPPL > = 0
                                                order by a.KodeBrg, a.NoSat, a.NoBukti", ["nobukti" => $req->noBukti]);
    return $listData;
  }

    public function listBarangNonFOC1AllSO (Request $req) 
  {
    $listData = DB::connection('SML')->select("SELECT a.KodeBrg, a.NamaBrg,a.PartNumber,a.NAMAMERK, a.Sat, a.NoSat, a.Isi, a.Qnt, a.QntPO, a.SisaPPL, a.NoBukti, a.Urut,a.tolerate,A.NosoCust 
                                                from vwOutPPL a  
                                                where Isjasa= 0 and a.sisaPPL > = 0
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

  public function listNoRJual (Request $req)
  {
    $listData = DB::connection('SML')->select("select NoBukti, KodeCustSupp, NamaCustSupp from vwBrowsPrRBeliJual group by NoBukti, KodeCustSupp, NamaCustSupp");
    return $listData;
  }

  public function listNoBeli (Request $req)
  {
    $listData = DB::connection('SML')->select("select top 100 A.NoBukti, A.Tanggal,A.KodeSupp,C.namaCustSupp,B.Kodegdg 
             , A.FakturSupp
        from dbBeli A  
             left outer join DbBelidet B on A.nobukti=B.Nobukti 
             left outer join DbCustSupp C on A.kodesupp=C.KodeCustSupp 
             left outer join (
                  select NOPBL,URUTPBL,SUM(Qnt1) QNT1  
                  from DBPRRBELIDET Group By NOPBL,URUTPBL
             ) D on B.NOBUKTI=D.NOPBL and B.URUT=D.URUTPBL   
        where Isnull(B.Qnt1Terima,0)-ISnull(B.Qnt1Reject,0)-ISnull(D.QNT1,0)>0 
          and Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end + 
                             Case when A.IsOtorisasi2=1 then 1 else 0 end + 
                             Case when A.IsOtorisasi3=1 then 1 else 0 end + 
                             Case when A.IsOtorisasi4=1 then 1 else 0 end + 
                             Case when A.IsOtorisasi5=1 then 1 else 0 end = A.MaxOL then 0
                   else 1 end As Bit)=0
          and  A.tanggal >= 12/01/2015
          and (A.Tanggal Between DateAdd(Day,-720, A.tanggal) and A.tanggal)
          group by a.NOBUKTI, a.TANGGAL, a.KODESUPP, c.NAMACUSTSUPP, B.KodeGdg, A.FAKTURSUPP
          order by a.tanggal desc");
    return $listData;
  }

  public function listSupplier (Request $req)
  {
    $listData = DB::connection('SML')->select("
        select Y.KodeCustSupp, Y.NamaCustSupp, Y.Alamat1 Alamat
             , Z.namaKota,Y.PPN,Y.HARI,Y.PPN,Y.Kota ,Y.NPPH23  ,Y.NPPH22 NPPH21     
        from DBCUSTSUPP Y         
             left outer join Dbkota Z on Y.kota=Z.KodeKota   
        where isnull(Y.JENIS,0)=0    
          and Y.IsAktif=1 
        order by Y.KODECUSTSUPP");
    return $listData;
  }

  public function listGudang (Request $req)
  {
    $listData = DB::connection('SML')->select("select KodeGdg, Nama, Alamat from dbGudang where isaktif=1 order by KodeGdg");
    return $listData;
  }

  public function listBarangJualTanpaBeli (Request $req)
  {
    $listData = DB::connection('SML')->select("Select A.Kodebrg,A.NamaBrg,A.Sat_1 satuan,A.Qnt, A.Nosat,A.Urut ,B.ISI1,b.ISI2,b.ISI3
from vwBrowsPrRBeliJual A
left outer join dbbarang b on a.kodebrg=b.kodebrg
where NObukti= :kodeJual", ['kodeJual'=>$req->kodeJual]);
    return $listData;
  }

  public function listBarangBeliTanpaJual (Request $req)
  {
    $listData = DB::connection('SML')->select("Select A.URUT, A.KODEBRG, A.NAMABRG, A.NOSAT, A.ISI, A.SATUAN, A.QntTerima,
       A.QntTerima-Sum(Case when A.NOSAT=1 then Case when B.NOSAT=1 then B.QntReject
                                                     when B.NOSAT=2 then B.QntReject*Br.ISI2
                                                     else 0
                                                end
                            when A.NOSAT=2 then Case when B.NOSAT=1 then B.QntReject/Br.ISI2
                                                     when B.NOSAT=2 then B.QntReject
                                                     else 0
                                                end
                        else 0
                   end)-isnull(D.Qnt,0) Qnt,
       A.Qnt1Terima-Sum(Case when A.NOSAT=1 then Case when B.NOSAT=1 then B.Qnt1Reject
                                                      when B.NOSAT=2 then B.Qnt1Reject
                                                      else 0
                                                 end
                             when A.NOSAT=2 then Case when B.NOSAT=1 then B.Qnt1Reject
                                                      when B.NOSAT=2 then B.Qnt1Reject
                                                      else 0
                                                 end
                        else 0
                   end)-isnull(D.Qnt1,0) Qnt1,
       A.Qnt2Terima-Sum(Case when A.NOSAT=1 then Case when B.NOSAT=1 then B.Qnt2Reject
                                                      when B.NOSAT=2 then B.Qnt2Reject
                                                      else 0
                                                 end
                             when A.NOSAT=2 then Case when B.NOSAT=1 then B.Qnt2Reject
                                                      when B.NOSAT=2 then B.Qnt2Reject
                                                      else 0
                                                 end
                        else 0
                   end)-isnull(D.Qnt2,0) Qnt2, Br.NFix,Br.ISI1,Br.ISI2,Br.ISI3
from (Select urut, kodebrg, namabrg, QNT, QntTerima,Qnt1Terima, Qnt2Terima, NOSAT, ISI, SATUAN, NOBUKTI,IsJasa
      from DBBELIDET
      where QntTerima<>0) A
      Left Outer join (Select urut, kodebrg, QNT,  QntReject, Qnt1Reject, Qnt2Reject,  NOSAT, ISI, SATUAN, NOBUKTI
                       from DBBELIDET
                       where Qnt1Reject<>0) B on B.NOBUKTI=A.NOBUKTI and B.KODEBRG=A.KODEBRG
      left outer join DBBARANG Br on Br.KODEBRG=A.KODEBRG
      Left Outer join (Select x.NOPBL, x.URUTPBL, SUM(x.QNT) Qnt, SUM(x.Qnt1) Qnt1, SUM(x.Qnt2) Qnt2
                       from DBPRRBELIDET x
                       group by x.NOPBL, x.URUTPBL) D on D.NOPBL=A.NOBUKTI and D.URUTPBL=A.URUT
where A.NoBukti= :noBeli  and Isnull(A.Isjasa,0)=0   
Group by A.URUT, A.KODEBRG, A.NAMABRG, A.NOSAT, A.ISI, A.SATUAN, Br.NFix, A.QntTerima, A.Qnt1Terima, A.Qnt2Terima,D.Qnt,D.Qnt1, D.Qnt2,Br.ISI1,Br.ISI2,Br.ISI3
Having A.QntTerima-Sum(Case when A.NOSAT=1 then Case when B.NOSAT=1 then B.QntReject
                                                     when B.NOSAT=2 then B.QntReject*Br.ISI2
                                                     else 0
                                                end
                            when A.NOSAT=2 then Case when B.NOSAT=1 then B.QntReject/Br.ISI2
                                                     when B.NOSAT=2 then B.QntReject
                                                     else 0
                                                end
                        else 0
                   end)-isnull(D.Qnt,0)>0
order by A.Urut", ['noBeli'=>$req->noBeli]);
    return $listData;
  }

  public function listBarangJualDanBeli (Request $req)
  {
    $listData = DB::connection('SML')->select("select KODEBRG,NamaBrg,SATUAN,QntTerima,NOSAT,a.ISI1,a.ISI2,a.ISI3,MAX(a.UrutPbl) UrutPbl,MAX(A.UrutPrj) UrutPrj
from (

select  A.KODEBRG,A.NamaBrg,a.SATUAN,A.QntTerima,A.NOSAT,A.URUT,a.ISI1,a.ISI2,a.ISI3,A.UrutPbl,A.UrutPrj
from (
Select A.URUT, A.KODEBRG, A.NAMABRG, A.NOSAT, A.ISI, A.SATUAN, A.QntTerima,
       A.QntTerima-Sum(Case when A.NOSAT=1 then Case when B.NOSAT=1 then B.QntReject
                                                     when B.NOSAT=2 then B.QntReject*Br.ISI2
                                                     else 0
                                                end
                            when A.NOSAT=2 then Case when B.NOSAT=1 then B.QntReject/Br.ISI2
                                                     when B.NOSAT=2 then B.QntReject
                                                     else 0
                                                end
                        else 0
                   end)-isnull(D.Qnt,0) Qnt,
       A.Qnt1Terima-Sum(Case when A.NOSAT=1 then Case when B.NOSAT=1 then B.Qnt1Reject
                                                      when B.NOSAT=2 then B.Qnt1Reject
                                                      else 0
                                                 end
                             when A.NOSAT=2 then Case when B.NOSAT=1 then B.Qnt1Reject
                                                      when B.NOSAT=2 then B.Qnt1Reject
                                                      else 0
                                                 end
                        else 0
                   end)-isnull(D.Qnt1,0) Qnt1,
       A.Qnt2Terima-Sum(Case when A.NOSAT=1 then Case when B.NOSAT=1 then B.Qnt2Reject
                                                      when B.NOSAT=2 then B.Qnt2Reject
                                                      else 0
                                                 end
                             when A.NOSAT=2 then Case when B.NOSAT=1 then B.Qnt2Reject
                                                      when B.NOSAT=2 then B.Qnt2Reject
                                                      else 0
                                                 end
                        else 0
                   end)-isnull(D.Qnt2,0) Qnt2, Br.NFix,Br.ISI1,Br.ISI2,Br.ISI3,A.UrutPbl,0 UrutPrj
from (Select urut, kodebrg, namabrg, QNT, QntTerima,Qnt1Terima, Qnt2Terima, NOSAT, ISI, SATUAN, NOBUKTI,IsJasa,Urut UrutPbl
      from DBBELIDET
      where QntTerima<>0) A
      Left Outer join (Select urut, kodebrg, QNT,  QntReject, Qnt1Reject, Qnt2Reject,  NOSAT, ISI, SATUAN, NOBUKTI
                       from DBBELIDET
                       where Qnt1Reject<>0) B on B.NOBUKTI=A.NOBUKTI and B.KODEBRG=A.KODEBRG
      left outer join DBBARANG Br on Br.KODEBRG=A.KODEBRG
      Left Outer join (Select x.NOPBL, x.URUTPBL, SUM(x.QNT) Qnt, SUM(x.Qnt1) Qnt1, SUM(x.Qnt2) Qnt2
                       from DBPRRBELIDET x
                       group by x.NOPBL, x.URUTPBL) D on D.NOPBL=A.NOBUKTI and D.URUTPBL=A.URUT
where A.NoBukti= :noBeli  and Isnull(A.Isjasa,0)=0   
Group by A.URUT, A.KODEBRG, A.NAMABRG, A.NOSAT, A.ISI, A.SATUAN, Br.NFix, A.QntTerima, A.Qnt1Terima, A.Qnt2Terima,D.Qnt,D.Qnt1, D.Qnt2,Br.ISI1,Br.ISI2,Br.ISI3,A.UrutPbl
Having A.QntTerima-Sum(Case when A.NOSAT=1 then Case when B.NOSAT=1 then B.QntReject
                                                     when B.NOSAT=2 then B.QntReject*Br.ISI2
                                                     else 0
                                                end
                            when A.NOSAT=2 then Case when B.NOSAT=1 then B.QntReject/Br.ISI2
                                                     when B.NOSAT=2 then B.QntReject
                                                     else 0
                                                end
                        else 0
                   end)-isnull(D.Qnt,0)>0
)A


union All
Select A.Kodebrg,A.NamaBrg,A.Sat_1 satuan,A.Qnt,  A.Nosat,A.Urut ,B.ISI1,b.ISI2,b.ISI3,0 UrutPbl,A.Urut UrutPrj
from vwBrowsPrRBeliJual A
left outer join dbbarang b on a.kodebrg=b.kodebrg
where NObukti= :kodeJual
) A

group by 
KODEBRG,NamaBrg,SATUAN,QntTerima,NOSAT,a.ISI1,a.ISI2,a.ISI3,A.UrutPbl,A.UrutPrj
having COUNT(*)>1
", ['noBeli'=>$req->noBeli,'kodeJual'=>$req->kodeJual ]);
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
    $choice = $req->Choice;

    $jmlrecord = $req->Jmlrecord;
    $nobukti = $req->NoBukti;
	$xurut=0;

//  return ["asd" => $nobukti] ;
     $purut = DB::connection('SML')->select('select * from DBPRRBELIDET where Nobukti = :nobukti', ['nobukti' => $nobukti]);
    if ($purut){

        if ($choice=='I' ){

        $purut = DB::connection('SML')->select('select max(urut)+1 xurut from DBPRRBELIDET where Nobukti = :nobukti', ['nobukti' => $nobukti]);
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





    $Urut = 0;

    if ($choice == "I" && $jmlrecord == 0) {
      $check = DB::connection('SML')->select('select * from DBPRRBELI where NOBUKTI = :nobukti',["nobukti" => $nobukti]);
        if ($check) {
          return 2;
      }
    }

    if($choice == "D" || $choice == "U"){
      $Urut = $req->Urut;
    }

            // $values = [
        //         'Choice'     => $Choice,
        //         'NoBukti'    => $data['nobukti'],
        //         'NoUrut'     => ($Choice=="D") ? "" : $data['nourut'],
        //         'Tanggal'    => ($Choice=="D") ? "" : $data['tanggal'],
        //         'KodeSupp'   => ($Choice=="D") ? "" : $data['kodesupp'],
        //         'KodeGdg'    => ($Choice=="D") ? "" : $data['kodegdg'],
        //         'NoBeli'     => ($Choice=="D") ? "" : $NoBeli,
        //         'Keterangan' => ($Choice=="D") ? "" : $data['keterangan'],
        //         'FakturSupp' => ($Choice=="D") ? "" : $data['faktursupp'],
        //         'Urut'       => $data['urut'],
        //         'KodeBrg'    => ($Choice=="D") ? "" : $data['kodebrg'],
        //         'UrutPBL'    => ($Choice=="D") ?  0 : $data['urutpbl'],
        //         'Qnt'        => ($Choice=="D") ?  0 : $Qnt,
        //         'NoSat'      => ($Choice=="D") ?  0 : $data['nosat'],
        //         'Satuan'     => ($Choice=="D") ? "" : $data['satuan'],
        //         'Isi'        => ($Choice=="D") ?  0 : $Isi,
        //         'Qnt1'       => ($Choice=="D") ?  0 : $Qnt1,
        //         'Qnt2'       => ($Choice=="D") ?  0 : $Qnt2,
        //         'FlagTipe'   => 0,
        //         'Nobatch'    => '',
        //         'NORJual'    => ($Choice=="D") ? "" : $NORJual,
        //         'UrutRJual'  => ($Choice=="D") ?  0 : $data['urutrjual'],
        //         'JmlRecord'  => ($Choice=="D") ?  0 : $data['jmlrecord'],
        //         'KETDET'     => ($Choice=="D") ? "" : $data['ketdet'],
        //         'Tipe'       => 0
        //     ];
      $values = [
        $choice, //Choice
        $nobukti, //NoBukti
        $req->NoUrut, //NoUrut
        $req->Tanggal, //Tanggal
        $req->KodeSupp, //KodeSupp

        $req->KodeGdg, //KodeGdg
        $req->NoBeli,
        $req->Keterangan,
        '', //FakturSupp
        $Urut, //Urut

        $req->KodeBrg,
        $req->UrutPBL,
        $req->Qnt,
        $req->NoSat,
        $req->Satuan,
        
        $req->Isi,
        $req->Qnt1, //Qnt
        $req->Qnt2, //Qnt
        0, //FlagTipe
        '', //Nobatch
        $req->NORJual,
        
        $req->UrutRJual,
        $req->Jmlrecord, //jmlrecord
        $req->KETDET,
        0, //Tipee

      ];
      DB::connection('SML')->statement('exec Sp_PRRBeli ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);
 	$tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $choice,'PRB',$nobukti,'',$xurut,'DBPRRBELIDET');
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

    $list = DB::connection('SML')->select("select  A.NOBUKTI, A.NOURUT, A.TANGGAL, A.TGLJATUHTEMPO, B.KODESUPP,
              C.NamaCustSupp, C.Alamat1, C.Alamat2, C.Kota,
              C.Alamat1+Char(13)+C.Alamat2+Char(13)+C.kota Alamat,
              B.NOPBL NOBeli, B.KodeGdg, A.KODEEXP, A.HANDLING, A.KETERANGAN, A.FAKTURSUPP,
              A.KODEVLS, A.KURS, A.PPN, A.TIPEBAYAR, A.HARI, A.TipeDisc, A.DISC, A.DISCRP,
              A.NILAIPOT, A.NILAIDPP, A.NILAIPPN, A.NILAINET, A.ISCETAK, A.NilaiCetak,
              B.URUT, B.KODEBRG,
              -- Fallback ke dbBarang kalau H.NamaBrg kosong/null - terjadi saat NOPBL
              -- (nobukti LPB) sudah tidak ada lagi di DBBELIDET (data yatim).
              Isnull(NullIf(case when B.NOPBL='-' then E.namaBrg else H.NamaBrg end,''), E.NamaBrg) NamaBrg
              , B.QNT, B.NOSAT, B.SATUAN, B.ISI, B.HARGA, B.DISCP, B.DISCTOT,
              B.BYANGKUT, B.NOPBL, B.URUTPBL, B.Qnt2, B.Qnt1, B.HPP,
              B.HRGNETTO, B.NDISKON, B.SUBTOTAL, B.NDPP, B.NPPN, B.NNET,
              E.NFix,Isnull(B.Isjasa,0) Isjasa,B.NoBatch,B.URUTPBL,B.NORJual,B.UrutRJual,B.Isi,B.Satuan ,
              B.NorJual,B.nopbl,b.ketdet,
              E.sat1 brgSat1, E.sat2 brgSat2, E.sat3 brgSat3, 
              E.isi1 brgIsi1, E.isi2 brgIsi2, E.isi3 brgIsi3
      From dbPRRBeli A
      Left Outer join dbPRRBeliDet B on B.NoBukti=A.NoBukti
      Left Outer Join dbCustSupp C on C.KodeCustSupp=B.KodeSupp
      Left Outer join dbValas D on D.KodeVls=A.KodeVls
      Left Outer join dbBarang E on E.KodeBrg=B.KodeBrg
      Left Outer Join dbGudang F on F.KodeGdg=b.KodeGdg
      Left Outer Join dbExpedisi G on G.KodeExp=A.KodeExp
      left outer join DBBELIDET H on H.NOBUKTI = B.NOPBL and H.URUT = B.URUTPBL
      where A.NoBukti = :nobukti
      order by B.Urut", ["nobukti" => $nobukti]);

    return [
      "list" => $list
    ];
  }

      public function spCetak (Request $req)
      {
          $noBukti = $req->input('NOBUKTI');

          $cetak = DB::connection("SML")->select(
              "EXEC sp_CetakPRRbeli ?",
              [$noBukti]
          );

          $tempCetak1 = [];
          foreach ($cetak as $p) {
              array_push($tempCetak1, $p);
          }

          return $tempCetak1;
      }

}
