<?php


namespace App\Http\Controllers\Marketing;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\DBFLMENU;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;



class VerifikasiPenawaranController extends Controller
{

      public function index(Request $req)
    {
        $kodemenu = '041040';
        $akses = app('App\Http\Controllers\GlobalController')
            ->getAkses($kodemenu, $req->path());
    
        if (!$akses || !$akses->HASACCESS) {
            return redirect('/home');
        }
    
        $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
        $menul0  = app('App\Http\Controllers\NewMenuController')->getMenuL0(4);
    
        $tempOutstanding = [];
    
        // ambil input
        $nobukti  = trim($req->search_nobukti ?? '');
        $customer = trim($req->search_customer ?? '');
        $barang   = trim($req->search_barang ?? '');
    
        $hasFilter = ($nobukti !== '' || $customer !== '' || $barang !== '');
    
        if ($req->has('search') && $hasFilter) {
    
            $tempOutstanding = DB::connection("SML")->select("
            SELECT 
                B.NOBUKTI,
                A.TANGGAL,
                A.KODECUST,
                C.NAMACUSTSUPP,
                A.NAMAPIC,
                B.KODEBRG,
                B.NAMABRG,
                B.IsVerf,
                B.TglVerf,
                B.UserVerf,
                B.HARGA,
                B.ketdet,
                B.Urut,
    
                (CASE 
                    WHEN B.NOSAT=1 THEN ISNULL(B.Qnt,0)
                    WHEN B.NOSAT=2 THEN ISNULL(B.Qnt2,0)
                    WHEN B.NOSAT=3 THEN ISNULL(B.Qnt2,0)
                END) AS QNT,
    
                ISNULL(D.QntSO,0) AS QntSO,
    
                (CASE 
                    WHEN B.NOSAT=1 THEN ISNULL(B.Qnt,0)
                    WHEN B.NOSAT=2 THEN ISNULL(B.Qnt2,0)
                    WHEN B.NOSAT=3 THEN ISNULL(B.Qnt2,0)
                END) - ISNULL(D.QntSO,0) AS Sisa,
    
                D1.tipe,
                D1.NamaMerk
    
            FROM DBPENAWARANSODet B
            LEFT JOIN DBPENAWARANSO A ON A.id = B.IDMASTER
            LEFT JOIN DBCUSTSUPP_PENAWARAN C ON A.KODECUST = C.KODECUSTSUPP
            LEFT JOIN dbrefprdet D1 ON B.norpr = D1.nobukti AND D1.urut = B.urutRPR
    
            LEFT JOIN (
                SELECT 
                    NOtawar,
                    Uruttawar,
                    SUM(
                        CASE 
                            WHEN NOSAT=1 THEN ISNULL(Qnt,0)
                            WHEN NOSAT=2 THEN ISNULL(Qnt2,0)
                            WHEN NOSAT=3 THEN ISNULL(Qnt2,0)
                        END
                    ) QntSO
                FROM DBSODET
                GROUP BY NOtawar, Uruttawar
            ) D ON B.NOBUKTI = D.NOtawar AND B.Urut = D.Uruttawar
    
            WHERE 
            -- YEAR(A.TANGGAL) = ?
            -- AND MONTH(A.TANGGAL) = ?
    
            -- AND 
            (? = '' OR B.NOBUKTI LIKE ?)
            AND (? = '' OR C.NAMACUSTSUPP LIKE ?)
            AND (? = '' OR B.NAMABRG LIKE ?)
    
            ORDER BY B.NOBUKTI, B.Urut
            ", [
                // $periode->tahun,
                // $periode->bulan,
    
                $nobukti, "%$nobukti%",
                $customer, "%$customer%",
                $barang, "%$barang%"
            ]);
        }
    
        return view('marketing.verifikasipenawaran', [
            "menul0" => $menul0,
            "periode" => $periode,
            "tempOutstanding" => $tempOutstanding,
            "akses" => $akses,
    
            "search_customer" => $customer,
            "search_barang" => $barang,
            "search_nobukti" => $nobukti,
        ]);
    }


 public function getBarang(Request $req)
{
    try {

        $q = trim($req->q ?? '');

        if ($q === '') {
            return response()->json([]);
        }

        if (!$req->mode || $req->mode === 'search') {

            $data = DB::connection("SML")->select("
                SELECT TOP 10 KODEBRG, NAMABRG
                FROM DBBARANG
                WHERE ISAKTIF = 1
                AND (KODEBRG LIKE ? OR NAMABRG LIKE ?)
                ORDER BY KODEBRG
            ", ["%$q%", "%$q%"]);

            return response()->json($data);
        }

          if ($req->mode === 'resolve') {

            $data = DB::connection("SML")->select("
                SELECT TOP 1 KODEBRG, NAMABRG
                FROM DBBARANG
                WHERE ISAKTIF = 1
                AND (
                    KODEBRG = ?
                    OR NAMABRG LIKE ?
                )
            ", [$q, "%$q%"]);

            return response()->json($data);
        }

        return response()->json([]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
}

public function getCustomer(Request $req)
{
    try {

        $q = $req->q;

        if (!$q) {
            return response()->json([]);
        }

        $q = "%".$q."%";

        $data = DB::connection("SML")->select("
            SELECT TOP 10 KODECUSTSUPP, NAMACUSTSUPP
            FROM DBCUSTSUPP
            WHERE JENIS = 1
            AND ISAKTIF = 1
            AND (KODECUSTSUPP LIKE ? OR NAMACUSTSUPP LIKE ?)
            ORDER BY KODECUSTSUPP
        ", [$q, $q]);

        return response()->json($data);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
}

  public function detailBarang (Request $req) {
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

$tempOutstanding2 = DB::connection("SML")->select("declare @tgl dateTime
set @tgl=GETDATE()


select 	x.KODEBRG,z.NAMABRG,x.KODEGDG , sum(SaldoQnt) SaldoQnt
			   from (

				      select KodeBrg,KODEGDG , sum(QNTAWAL) SaldoQnt
					  from 	dbStockBrg
					  where  Tahun=year(@tgl) and Bulan=1/*MONTH(@tgl) */
					  and KOdeGdg not in ('GTC','GSS',(SELECT KODEGDG FROM DBGUDANG WHERE istakeinout=1))
					  and KODEBRG<>''
					  group by KODEBRG,KODEGDG

					  union all

					  select 	KodeBrg,Kodegdg,
					  SUM(QntDb)-SUM(QntCr) SaldoQnt
					  from 	vwKartuStock
					  where 	year(Tanggal)=year(@tgl) /*and month(tanggal)=month(@tgl)*/ and Tanggal<=@tgl
					  and  Tipe not in ('AWL')
					  and KOdeGdg not in ('GTC','G04','GSS',(SELECT KODEGDG FROM DBGUDANG WHERE istakeinout=1))
					  group by KodeBrg, KodeGdg

					) X
					left outer join DBBARANG z on x.KODEBRG=z.KODEBRG
					where z.ISAKTIF=1 and x.KODEBRG = :kodebrg
				group by X.KodeBrg,x.KODEGDG,z.NAMABRG
				having sum(SaldoQnt)<>0
                                order by X.kodebrg

" , ["kodebrg" => $req->kodebrg ]);

return $tempOutstanding2;
  }


public function loadAll(Request $req)
{
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $nobukti  = trim($req->search_nobukti ?? '');
    $customer = trim($req->search_customer ?? '');
    $barang   = trim($req->search_barang ?? '');

    $tempOutstanding = DB::connection("SML")->select("
        SELECT  
            B.NOBUKTI,
            A.TANGGAL,
            B.KODEBRG,
            A.KODECUST,
            C.NAMACUSTSUPP,
            B.NAMABRG,

            (CASE 
                WHEN B.NOSAT=1 THEN ISNULL(B.Qnt,0)
                WHEN B.NOSAT=2 THEN ISNULL(B.Qnt2,0)
                WHEN B.NOSAT=3 THEN ISNULL(B.Qnt2,0)
            END) AS QNT,

            D.QntSO,

            (CASE 
                WHEN B.NOSAT=1 THEN ISNULL(B.Qnt,0)
                WHEN B.NOSAT=2 THEN ISNULL(B.Qnt2,0)
                WHEN B.NOSAT=3 THEN ISNULL(B.Qnt2,0)
            END) - ISNULL(D.QntSO,0) AS Sisa,

            B.HARGA,
            A.NAMAPIC,
            A.FRANCO,
            A.DELIVERY,
            A.VALIDITAS,
            B.ID,
            B.Nosat,
            B.IsVerf,
            B.Nobukti + CAST(B.Urut AS VARCHAR(4)) AS XKey,
            B.Urut,
            D1.tipe,
            D1.NamaMerk,
            B.ketdet,
            B.TglVerf,
            B.UserVerf

        FROM DBPENAWARANSODet B
        LEFT JOIN DBPENAWARANSO A 
            ON A.id = B.IDMASTER

        LEFT JOIN DBCUSTSUPP_PENAWARAN C 
            ON A.KODECUST = C.KODECUSTSUPP

        LEFT JOIN dbrefprdet D1 
            ON B.norpr = D1.nobukti 
            AND D1.urut = B.urutRPR

        LEFT JOIN (
            SELECT 
                NOtawar,
                Uruttawar,
                SUM(
                    CASE 
                        WHEN NOSAT=1 THEN ISNULL(Qnt,0)
                        WHEN NOSAT=2 THEN ISNULL(Qnt2,0)
                        WHEN NOSAT=3 THEN ISNULL(Qnt2,0)
                    END
                ) AS QntSO
            FROM DBSODET
            GROUP BY NOtawar, Uruttawar
        ) D 
            ON B.NOBUKTI = D.NOtawar 
           AND B.Urut = D.Uruttawar

        WHERE 
        --YEAR(A.TANGGAL) >= 2024

        --AND 
        (? = '' OR B.NOBUKTI LIKE ?)
        AND (? = '' OR C.NAMACUSTSUPP LIKE ?)
        AND (? = '' OR B.NAMABRG LIKE ?)

        ORDER BY B.NOBUKTI + CAST(B.Urut AS VARCHAR(4))
    ", [
        $nobukti,  "%$nobukti%",
        $customer, "%$customer%",
        $barang,   "%$barang%"
    ]);

    return [
        "tempOutstanding" => $tempOutstanding
    ];
}

    public function spOtorisasi ( Request $req) {
      // $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

      $res = DB::connection('SML')->update("
      Update DbPenawaranSodet set IsVerf=1,TglVerf= GETDATE() ,UserVerf= :username where NObukti= :nobukti and Urut= :urut
",
      ["username" => \Auth::user()->username , "nobukti" => $req->nobukti , "urut" => $req->urut  ]);

      return 1;
    }

    public function spBatalOtorisasi ( Request $req) {
      // $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

      $res = DB::connection('SML')->update("
      Update DbPenawaranSodet set IsVerf= NULL ,TglVerf= NULL ,UserVerf= '' where NObukti= :nobukti and Urut= :urut
",
      [ "nobukti" => $req->nobukti , "urut" => $req->urut  ]);

      return 1;
    }

    // Backs public/js/headerEngine.js's drag/hide/decimal column layout for
    // #tabel (see verifikasipenawaran.blade.php). Same generic
    // (username, href, reportmode) contract as SOController::loadHeader/
    // simpanHeader and PenawaranSOController::loadHeader/simpanHeader.
    public function loadHeader (Request $req) {
      $header = DB::connection('SML')->select('select * from DBSIMPANHEADER where username = :user and href = :href and reportmode = :mode', [
        'user' => \Auth::user()->username,
        'href' => $req->href,
        'mode' => $req->mode,
      ]);

      return $header;
    }

    public function simpanHeader (Request $req) {
      DB::connection('SML')->update('delete from DBSIMPANHEADER where username = :user and href = :href and reportmode = :mode', [
        'user' => \Auth::user()->username,
        'href' => $req->href,
        'mode' => $req->mode,
      ]);

      DB::connection('SML')->insert('insert into DBSIMPANHEADER (username, href, reportmode, header, issubtotal, isgrandtotal) values (:user, :href, :mode, :header, :issubtotal, :isgrandtotal)', [
        'user' => \Auth::user()->username,
        'href' => $req->href,
        'mode' => $req->mode,
        'header' => $req->header,
        'issubtotal' => $req->issubtotal,
        'isgrandtotal' => $req->isgrandtotal,
      ]);

      return 1;
    }

}
