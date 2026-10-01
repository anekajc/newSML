<?php

namespace App\Http\Controllers\Purchasing;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use Illuminate\Support\Facades\DB;
use App\Models\VwPPL;
use Illuminate\Auth;


// use App\Http\Controllers\NewMenuController;

class PONonStockController extends Controller
{

  public function index(Request $req) {
    $kodemenu = '04101';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu, $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(3);

    list($ponsTglAwal, $ponsTglAkhir) = $this->periodeRange($periode);

    // Data tab "Outstanding PR" & "Purchase Order" sekarang diambil lewat AJAX
    // (dataOutstandingPR/loadPurchaseOrder) - lihat memory tab-outstanding-pr-hanya-informasi
    // dan pola yang sama di POController@index.
    return view('purchasing.purchaseOrderNonStock' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "ponsTglAwal" => $ponsTglAwal,
      "ponsTglAkhir" => $ponsTglAkhir,
      "tempOutstanding1" => [],
      "tempOutstanding3" => [],
      "tempOutstanding5" => [],

      "level" => $akses->OL,
      "listBarangAll" => [] ,
      "akses" => $akses
    ]);

}

  // Rentang tanggal default tab Purchase Order = satu bulan penuh periode kerja user.
  // Sama persis dengan POController::periodeRange().
  private function periodeRange ($periode) {
    $stamp = mktime(0, 0, 0, (int) $periode->bulan, 1, (int) $periode->tahun);
    return [ date('Y-m-01', $stamp), date('Y-m-t', $stamp) ];
  }

  public function loadAll () {
    $req = new Request([
        'href' => 'pononstock'
    ]);
    $xx = app('App\Http\Controllers\HeaderTableController')
        ->getHeaderTable($req);
    // loadAll() sekarang hanya mengembalikan konfigurasi header tabel, sama seperti
    // POController@loadAll. Data tab "Outstanding PR" diambil per halaman lewat
    // dataOutstandingPR() (server-side paging), data tab "Purchase Order" diambil lewat
    // loadPurchaseOrder() saat tabnya diklik (lazy load).
    return [
      "tempOutstanding1" => [],
      "tempOutstanding3" => [],
      "tempOutstanding5" => [],
      "aliasordered" => $xx['aliasordered'],
      "headertableheader" => $xx['headertableheader'],
      "isnumeric" => $xx['isnumeric'],
      "headertablevalue" => $xx['headertablevalue'],
      "isparsed" => $xx['isparsed'],
      "isshown" => $xx['isshown'],
      "desimal" => $xx['desimal'],

      "aliasordered2" => $xx['aliasordered2'],
      "headertableheader2" => $xx['headertableheader2'],
      "isnumeric2" => $xx['isnumeric2'],
      "headertablevalue2" => $xx['headertablevalue2'],
      "isparsed2" => $xx['isparsed2'],
      "isshown2" => $xx['isshown2'],
      "desimal2" => $xx['desimal2'],
    ];
  }

  /**
   * Data tab "Purchase Order". Dipanggil lewat AJAX hanya saat tab tersebut dibuka,
   * sama persis dengan POController@loadPurchaseOrder - bedanya cuma B.pJasa = 1.
   */
  public function loadPurchaseOrder (Request $req) {
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    list($tglawal, $tglakhir) = $this->periodeRange($periode);

    $inputAwal  = $req->input('tglawal');
    $inputAkhir = $req->input('tglakhir');
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $inputAwal))  { $tglawal  = $inputAwal; }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $inputAkhir)) { $tglakhir = $inputAkhir; }
    if ($tglawal > $tglakhir) { $tglakhir = $tglawal; }

    $tempOutstanding2 = DB::connection("SML")->select("declare @tglawal date, @tglakhir date  ,@pJasa Bit

select @tglawal= :tglawal, @tglakhir= :tglakhir

Select  a.isAut,a.NoBukti, a.Tanggal,a.KodeSupp, b.NamaCustSupp, b.Handling, b.FakturSupp,
        b.TotSubTotal, b.TotDiskon, b.TotTotal, TotDPP, b.TotPPN, TotNet,
        TotSubTotalRp, TotDiskonRp, TotTotalRp, TotDPPRp, TotPPNRp, TotNetRp,
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
       A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
       A.IsOtorisasi3, A.OtoUser3, A.TglOto3,
       A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
       A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
       Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit) NeedOtorisasi,A.IsOtorisasi2
       ,Isnull(A.IsBatal,0) Isbatal,A.UserBatal,A.TglBatal,
       A.FlagTipe,NOSO,NOPOCUST ,A.tglKirim,A.MaxOL,b.qnt as qnt,b.qntbeli as qntbeli
From dbPO a Left Outer Join vwMasterPO b on a.NoBukti=b.NoBukti
where a.Tanggal between @tglawal and @tglakhir
and  TotTotalRp>=200000000
and B.pJasa= 1

UNION ALL

Select  a.isAut,a.NoBukti, a.Tanggal,a.KodeSupp, b.NamaCustSupp, b.Handling, b.FakturSupp,
        b.TotSubTotal, b.TotDiskon, b.TotTotal, TotDPP, b.TotPPN, TotNet,
        TotSubTotalRp, TotDiskonRp, TotTotalRp, TotDPPRp, TotPPNRp, TotNetRp,
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
       A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
       A.IsOtorisasi3, A.OtoUser3, A.TglOto3,
       A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
       A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
       Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit) NeedOtorisasi,A.IsOtorisasi2
       ,Isnull(A.IsBatal,0) Isbatal,A.UserBatal,A.TglBatal,
       A.FlagTipe,NOSO,NOPOCUST ,A.tglKirim,A.MaxOL,b.qnt as qnt,b.qntbeli as qntbeli
From dbPO a Left Outer Join vwMasterPO b on a.NoBukti=b.NoBukti
where a.Tanggal between @tglawal and @tglakhir
and  TotTotalRp < 200000000
and B.pJasa= 1

order by Tanggal desc, NoBukti desc" , ["tglawal" => $tglawal , "tglakhir" =>$tglakhir]);

$collection2 = collect($tempOutstanding2)->groupBy('NOBUKTI');
$tempOutstanding3 = [];
foreach ($collection2 as $p) {
  array_push($tempOutstanding3, $p);
}

    return [
      "tempOutstanding3" => $tempOutstanding3,
    ];
}

  /**
   * Data tab "Outstanding PR" dengan server-side paging DataTables - sama persis dengan
   * POController@dataOutstandingPR (termasuk penghitungan ulang SisaPPL/QNTPO lewat
   * POController::sqlOutstandingPR(), supaya barang yang PR-nya sudah habis diambil PO
   * tidak lagi muncul di sini - lihat catatan di POController@dataOutstandingPR),
   * bedanya cuma filter pjasa (non stock = 1, stock = 0).
   */
  public function dataOutstandingPR (Request $req) {
    $draw   = (int) $req->input('draw', 1);
    $start  = (int) $req->input('start', 0);
    $length = (int) $req->input('length', 10);

    if ($start < 0) { $start = 0; }
    $semua = ($length === -1);
    if (!$semua && ($length < 1 || $length > 500)) { $length = 10; }

    $allowedOrder = [
      'Nobukti', 'Tanggal', 'kodebrg', 'NamaBrg', 'sat', 'Qnt',
      'QNTPO', 'SisaPPL', 'Keterangan', 'QntoutSO', 'QntStock', 'Urut'
    ];
    $orderCol = (string) $req->input('orderCol', '');
    $orderDir = strtolower((string) $req->input('orderDir', 'asc')) === 'desc' ? 'DESC' : 'ASC';

    $orderColSql = [
      'SisaPPL' => '((A.Qnt - isnull(A.QntBatal,0)) - isnull(P.QntPO,0))',
      'QNTPO'   => 'isnull(P.QntPOBruto,0)',
    ];
    if (in_array($orderCol, $allowedOrder, true)) {
      $orderExpr = $orderColSql[$orderCol] ?? ('A.[' . $orderCol . ']');
      $orderBy = $orderExpr . ' ' . $orderDir . ', A.NoBukti, A.Urut';
    } else {
      $orderBy = 'A.Tanggal DESC, A.NoBukti DESC, A.Urut';
    }

    $where = '((A.Qnt - isnull(A.QntBatal,0)) - isnull(P.QntPO,0)) > 0 and isnull(A.pjasa,0) = 1';
    $bind  = [];
    $search = trim((string) $req->input('search', ''));
    if ($search !== '') {
      $where .= " and (A.NoBukti like :cari1 or A.kodebrg like :cari2
                    or A.NamaBrg like :cari3 or A.Keterangan like :cari4)";
      $like = '%' . $search . '%';
      $bind = ["cari1" => $like, "cari2" => $like, "cari3" => $like, "cari4" => $like];
    }

    $sqlPO = POController::sqlOutstandingPR();

    $jml = DB::connection("SML")->select("
      SET NOCOUNT ON
      select count(1) as jml
      from DBO.vwOutPPL A WITH(NOLOCK)
      left outer join ( $sqlPO ) P on P.NoPPL = A.NoBukti and P.UrutPPL = A.Urut
      where $where
    ", $bind);
    $total = count($jml) ? (int) $jml[0]->jml : 0;

    $batasBaris = '';
    if (!$semua) {
      $batas = $start + $length;
      $batasBaris = "where X.NoBaris > $start and X.NoBaris <= $batas";
    }

    $rows = DB::connection("SML")->select("
      SET NOCOUNT ON
      select X.* from (
        select ROW_NUMBER() over (order by $orderBy) as NoBaris,
               A.NoBukti+' '+right('00000000'+cast(A.urut as varchar(8)),8) KeyUrut,
               A.*,
               (A.Qnt - isnull(A.QntBatal,0)) - isnull(P.QntPO,0) SisaPPLBaru,
               isnull(P.QntPOBruto,0) QNTPOBaru,
               CONVERT(varchar(10), A.Tanggal, 23) TanggalBaru
        from DBO.vwOutPPL A WITH(NOLOCK)
        left outer join ( $sqlPO ) P on P.NoPPL = A.NoBukti and P.UrutPPL = A.Urut
        where $where
      ) X
      $batasBaris
      order by X.NoBaris
    ", $bind);

    foreach ($rows as $r) {
      $r->SisaPPL = $r->SisaPPLBaru;
      $r->QNTPO   = $r->QNTPOBaru;
      $r->Tanggal = $r->TanggalBaru;
      unset($r->SisaPPLBaru, $r->QNTPOBaru, $r->TanggalBaru);
    }

    return [
      "draw" => $draw,
      "recordsTotal" => $total,
      "recordsFiltered" => $total,
      "data" => $rows,
    ];
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

  public function updateOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbpo set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal where nobukti = :nobukti", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','PO',$req->nobukti,'',0,'DBPO');

    return $res;
  }
  
  public function updateBatalOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbpo set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL where nobukti = :nobukti", [ "nobukti" => $req->nobukti]);
    // $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','PO',$req->nobukti,$req->pket,0,'DBPPL');
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

    public function listPerkiraan (Request $req) {

    $listData = DB::connection('SML')->select("select Perkiraan,Keterangan from dbPerkiraan where  tipe=1 
                                               and perkiraan in (select perkiraan from DBAKSESPERKIRAAN where userid='sa')
                                               order by Perkiraan");
    return $listData;
  }

  public function listSales (Request $req) {

    $listData = DB::connection('SML')->select("SELECT keynik, nama FROM dbkaryawan where IsSales = 1");
    return $listData;
  }

  public function listCosting (Request $req) {

    $listData = DB::connection('SML')->select("select a.KodeCost, a.NamaCost from dbCost a, dbPerkCost b 
where a.KodeCost=b.KodeCost and b.Perkiraan= :perkiraan
  group by a.KodeCost, a.NamaCost 
 order by a.KodeCost",["perkiraan"=>$req->perkiraan]);
    return $listData;
  }

    public function listSubCosting (Request $req) {

    $listData = DB::connection('SML')->select(" select a.KodeSubCost, a.NamaSubCost from vwSubCost a 
                where a.KodeCost= :kodeCost
                order by a.KodeSubCost",["kodeCost"=>$req->kodeCost]);
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
                                                where a.isaktif=1 and a.IsJasa = 1");
    return $listData;
  }

  public function listBarangJasa () 
  {
    $listData = DB::connection('SML')->select("Select A.KodeBrg, A.NamaBrg,B.NamaMerk,A.PartNumber 
                                                , 0 Stock
                                                from dbBarang A 
                                                Left Outer Join DbMerk B on A.kodeMerk = B.KodeMerk
                                                where a.isAktif=1 
                                                and Isnull(A.isJasa,0)=1  and isnull(A.pagen,0)=0 
                                                order by A.KodeBrg ");
    return $listData;
  }

  public function listBarangJasaNoBukti (Request $req)
  {
    $listData = DB::connection('SML')->select("SELECT a.KodeBrg, a.NamaBrg,a.PartNumber,a.NAMAMERK, a.Sat, a.NoSat, a.Isi, a.Qnt, a.QntPO, a.SisaPPL, a.NoBukti, a.Urut,a.tolerate,A.NosoCust
                                                from vwOutPPL a
                                                where Isjasa= 1 and NoBukti = :nobukti and a.sisaPPL > = 0
                                                order by a.KodeBrg, a.NoSat, a.NoBukti", ["nobukti" => $req->noBukti]);
    return $listData;
  }

  /**
   * Daftar barang outstanding dari SELURUH PR Non Stock sekaligus - dipakai form
   * PO Non Stock saat "+ Dari" browsing barang (sebelumnya menembak listBarangJasa()
   * yang membaca seluruh master barang jasa, tanpa hubungan ke PR sama sekali).
   * Meniru POController@listBarangNonFOC1AllSO, tapi memakai `pjasa` (bukan `Isjasa`)
   * dan `SisaPPL > 0` (bukan `>= 0`) supaya konsisten dengan query tab Outstanding PR
   * (dataOutstandingPR) - barang yang PR-nya sudah habis tidak ikut muncul.
   */
  public function listBarangJasaAll (Request $req)
  {
    // SisaPPL/QntPO dihitung ulang dari agregat dbPOdet (POController::sqlOutstandingPR())
    // supaya sepakat dengan dataOutstandingPR() - barang yang PR-nya sudah habis diambil
    // PO (termasuk yang sempat dibatalkan) tidak ikut muncul di sini.
    $sqlPO = POController::sqlOutstandingPR();
    $listData = DB::connection('SML')->select("
      SELECT a.KodeBrg, a.NamaBrg, a.PartNumber, a.NAMAMERK, a.Sat, a.NoSat, a.Isi,
             a.Qnt, isnull(P.QntPOBruto,0) QntPO,
             (a.Qnt - isnull(a.QntBatal,0)) - isnull(P.QntPO,0) SisaPPL,
             a.NoBukti, a.Urut, a.tolerate, a.NosoCust
      from vwOutPPL a WITH(NOLOCK)
      left outer join ( $sqlPO ) P on P.NoPPL = a.NoBukti and P.UrutPPL = a.Urut
      where isnull(a.pjasa,0) = 1
        and ((a.Qnt - isnull(a.QntBatal,0)) - isnull(P.QntPO,0)) > 0
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
    $choice = $req->Choice;
    $jmlrecord = $req->Jmlrecord;
    $nobukti = $req->NoBukti;
$xurut=0;
//  return ["asd" => $nobukti] ;
     $purut = DB::connection('SML')->select('select * from DBPODET where Nobukti = :nobukti', ['nobukti' => $nobukti]);
    // Dipakai di bawah untuk memastikan sp_PO benar-benar menambah baris detail.
    $jmlDetSebelum = count($purut);
    if ($purut){

        if ($choice=='I' ){

        $purut = DB::connection('SML')->select('select max(urut)+1 xurut from DBPODET where Nobukti = :nobukti', ['nobukti' => $nobukti]);
            // return 'uuu';
        $xurut= $purut[0]->xurut;
        }else { 
            // return 'mmm';
            // Blade mengirim field bernama "Urut" (huruf besar) - $req->urut selalu null,
            // sehingga urut yang dicatat ke log aktivitas ikut kosong.
            $xurut = $req->Urut;
        }
        
    }else{
        // return 'ttt';
        $xurut=1; 
    }
    // return ["asd" => $xurut] ;



if ($choice =='D'){
      $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $choice,'PO',$nobukti,'',$xurut,'DBPODET');
      }


    if ($choice == "I" && $jmlrecord == 0) {
      $check = DB::connection('SML')->select('select * from dbpo where NOBUKTI = :nobukti',["nobukti" => $nobukti]);
        if ($check) {
          return 2;
      }
    }

    // dbPODET punya foreign key FK_DBPODET_DBBARANG ke DBBARANG.KODEBRG. Kalau kode barang
    // yang dikirim tidak ada di master, INSERT detail di dalam sp_PO ditolak SQL Server -
    // TAPI kegagalannya tidak pernah sampai ke PHP: SQL Server hanya membatalkan statement
    // itu, isi prosedurnya jalan terus sampai "Commit tran", jadi header dbPO tetap
    // tersimpan tanpa satu pun baris dbPODET. Akibatnya dokumen tidak muncul di tab
    // Purchase Order (vwMasterPO.TotTotalRp NULL -> tidak lolos filter >=/< 200 juta) dan
    // detailnya terbaca null di form. Dicegat di sini supaya user dapat pesan yang jelas.
    if ($choice != 'D') {
      $cekBrg = DB::connection('SML')->select(
        'select top 1 KODEBRG from DBBARANG where KODEBRG = :kodebrg',
        ["kodebrg" => (string) $req->KodeBrg]
      );
      if (!$cekBrg) {
        return 3;
      }
    }

      $values = [
        $choice, //Choice
        $nobukti, //NoBukti
        $req->NoUrut, //NoUrut
        $req->Tanggal, //Tanggal
        $req->TglJatuhTempo, //TglJatuhTempo

        $req->KodeSupp, //KodeSupp
        0, //Handling
        $req->KodeExp, //KodeExp
        $req->Keterangan, //Keterangan
        '', // FakturSupp

        $req->KodeVls, //KodeVls
        $req->Kurs, //Kurs
        $req->PPn, //PPn
        $req->TipeBayar, //TipeBayar
        $req->Hari, //Hari

        0, //TipeDisc
        0, //Disc
        0, //DiscRp
        $req->Urut, //Urut
        $req->KodeBrg, // KodeBrg
        
        $req->Qnt, //Qnt
        $req->NoSat, //NoSat
        $req->Satuan, //Satuan
        $req->Isi, //Isi
        $req->Harga, //Harga
        
        $req->DiscP, //DiscP
        $req->DiscTot, //DiscTot
        $req->NoPPL, //NoPPL
        0,  //IsClose
        0,  //IsCloseD
        
        '', //Catatan
        0, //IsExp
        0, //Tolerate
        $req->UrutPPL, //UrutPPL
        $req->Kodegdg, //Kodegdg 

        $req->Discpdet2, //Discpdet2
        $req->Discpdet3, //Discpdet3
        0, //Discpdet4
        0, //Discpdet5
        1, //FlagTipe

        $req->NamaBrg, //Namabrg
        0, //IsJasa
        0, //pFirst
        $req->pFOC, //pFOC
        $req->Noso, //Noso

        $jmlrecord, //Jmlrecord
        $req->NOPOCUST, //NOPOCUST
        \Auth::User()->username, //IdUser
        1,  //pJasa
        $req->NPPH23,  //NPPH23

        $req->PERKIRAAN, //PERKIRAAN
        $req->SatX, //SatX 
        $req->COST, //COST
        $req->SUBCOST, //SUBCOST
        $req->TglKirim, //Tglkirim

        $req->PPH21, //PPH21
        $req->NOPNw, //NOPNw
        $req->UrutPNW //UrutPNW

      ];
      DB::connection('SML')->statement('exec sp_PO ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);

      // Jaring pengaman untuk kegagalan diam-diam di dalam sp_PO (lihat catatan pada
      // pengecekan DBBARANG di atas): kalau jumlah baris detail tidak bertambah, item
      // memang tidak tersimpan - jangan laporkan berhasil.
      if ($choice == 'I') {
        $cekDet = DB::connection('SML')->select(
          'select count(1) jml from DBPODET where Nobukti = :nobukti', ['nobukti' => $nobukti]);
        $jmlDetSesudah = count($cekDet) ? (int) $cekDet[0]->jml : 0;
        if ($jmlDetSesudah <= $jmlDetSebelum) {
          return 4;
        }
      }

      DB::connection('SML')->update('exec Sp_UpdatePO ?', [$nobukti]);
     
      if ($choice !='D'){
      $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $choice,'PO',$nobukti,'',$xurut,'DBPODET');
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

    $list = DB::connection('SML')->select("
    declare @NoBukti varchar(30)

select 	@NoBukti= :nobukti

Select 	A.NoBukti, A.NoUrut, A.Tanggal, A.TglJatuhTempo, A.KodeSupp, C.NamaCustSupp, C.Alamat1, C.Alamat2, C.Kota,
        C.Alamat1+Char(13)+C.Alamat2+Char(13)+C.kota Alamat,
	A.Handling, A.Keterangan, A.FakturSupp,IsExp,
	A.KodeVls, D.NamaVls, A.Kurs, A.PPN, A.TipeBayar, A.Hari, A.Disc,
	B.Urut, B.KodeBrg, case when B.NAMABRG='' then E.NAMABRG else B.NamaBrg End NamaBrg, B.Satuan, B.Qnt, B.Nosat, B.Isi,
        B.Harga, B.DISCP, B.DISCTOT, B.NoPPL, B.UrutPPL, A.IsClose,B.IsClose IsCloseD,
        case when A.Kurs=1 then 0.0 else B.SubTotal end TotalUSD,a.KodeExp,F.namaCustSupp NamaExp,
	round(B.SubTotal*A.Kurs,2) TotalIDR, round(B.NDPP*A.Kurs,2) NDPP,
        round(B.NPPN*A.Kurs,2) NPPN,isnull(B.Tolerate,0) Tolerate,
	B.BYAngkut Beban,
	round(B.SubTotal*A.Kurs,2) + B.BYAngkut Total,        
        H.TotDiskon, H.TotDPP, H.TotPPN, H.TotNet,
        A.Kodegdg, I.Nama NamaGDG, I.Alamat ALamatGdg,
        x.TotalX,dbo.terbilang(x.totalx) Terbilang,B.Discp2,B.Discp3,B.Discp4,B.Discp5,
        A.PPN PPNTrans,isnull(B.Tolerate,0) ToleratePO,Isnull(B.Isjasa,0) Isjasa,
        B.Discp DiscP1,x1.NosoCust,Isnull(B.pFoc,0) PFOC,A.NOSO,M1.Nopesanan,A.npph23,A.perkiraan,m2.Keterangan Nmperkiraan,B.SatX
        ,B.Cost,b.subcost, Cs.NamaCost, SCs.NamaSubCost,A.TglKirim ,H.TOtSubtotalRP ,A.NPPH21,'' NoPNW,0 UrutPNW,A.FlagTipe
        From dbPO A
Left Outer join dbPODet B on B.NoBukti=a.NoBukti
Left Outer Join dbCustSupp C on c.KodeCustSupp=a.KodeSupp
Left Outer join dbValas D on D.KodeVls=A.KodeVls
Left Outer join dbBarang E on E.KodeBrg=B.KodeBrg
Left Outer join vwBrowsExpedisi F On F.KodeCustSupp=A.KodeExp
Left Outer Join vwMasterPO H on H.NoBukti=A.NoBukti
Left Outer Join (select KodeBrg,NoBukti,Min(Tanggal)Tanggal from dbKirimDet group by KodeBrg,NoBukti) J On J.KodeBrg=B.KodeBrg and J.NoBukti=B.NoBukti
left outer join (select A.Nobukti, sum(round(B.SubTotal*A.Kurs,2) + B.BYAngkut)  totalx from Dbpo A
                        left outer join dbPODet B on A.nobukti = B.nobukti group By A.NObukti) X on A.nobukti = x.nobukti
Left Outer join dbgudang I on I.Kodegdg=A.kodegdg
Left Outer Join DBPPLDet X1 on B.Noppl=X1.Nobukti and B.UrutPPL=X1.Urut
Left Outer join DbSo M1 on A.Noso=M1.nobukti
Left Outer join dbperkiraan M2 on A.perkiraan=M2.Perkiraan
left outer join dbCost Cs on Cs.KodeCost=B.Cost
left outer join vwSubCost SCs on SCs.KodeCost=b.Cost and SCs.KodeSubCost=b.SubCost
where	A.NoBukti=@NoBukti
order by B.Urut", ["nobukti" => $nobukti]);

    return [
      "list" => $list
    ];
  }
}
