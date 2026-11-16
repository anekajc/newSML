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



class ClosingTransferController extends Controller
{
  public function index(Request $req) {
    $kodemenu = '060411';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses1($kodemenu , $req->path());
    if (!$akses || !$akses->HASACCESS) {
        return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(6);

    // Periode range default buat tab "Closing Transfer Barang" (tempPenerimaan) --
    // satu bulan penuh periode kerja user, sama seperti transferbarang.blade.php.
    // Tidak dipakai buat tempOutstanding ("Transfer Barang"), itu memang selalu
    // menampilkan semua transfer outstanding tanpa filter tanggal.
    $date1 = date('Y-m-01', mktime(0, 0, 0, $periode->bulan, 1, $periode->tahun));
    $date2 = date('Y-m-t', mktime(0, 0, 0, $periode->bulan, 1, $periode->tahun));

    // sheet kiri
    $tempOutstanding = DB::connection("SML")->select("
    SELECT 
        B.NOBUKTI, 
        C.URUT,  
        C.KODEBRG, 
        C.NAMABRG, 
        '' AS Jns_Kertas, 
        '' AS Ukr_Kertas,
        C.QNT, 
        C.QNT2, 
        C.SAT1 AS SAT_1, 
        C.SAT2 AS SAT_2, 
        C.ISI, 
        B.GdgAsal, 
        B.GdgTujuan, 
        C.NMGDGASAL + ' (' + B.gdgAsal + ')' AS NamagdgAsal,
        C.NMGDGTUJUAN + ' (' + B.GdgTujuan + ')' AS NamagdgTujuan, 
        0.00 AS GSM,
        CASE 
            WHEN C.nosat = 1 THEN C.SISA 
            WHEN C.NOSAT = 2 THEN C.SISA2 
        END AS Qntx,
        CASE 
            WHEN C.nosat = 1 THEN C.sat1 
            WHEN C.NOSAT = 2 THEN C.sat2 
        END AS Satx,
        C.qNTTR,
        C.QNT2TR,
        C.IsBatal,
        C.QntBatal,
        C.TglBatal,
        C.UserBatal,
        C.KetBatal,
        C.NObukti + CAST(C.Urut AS VARCHAR(3)) AS KeyN
    FROM dbTransfer A
    LEFT OUTER JOIN (
        SELECT 
            NObukti, 
            GdgAsal,
            GDGTujuan
        FROM dbTransferDET
        GROUP BY NObukti, GdgAsal, GDGTujuan
    ) B ON A.nobukti = B.nobukti
    LEFT OUTER JOIN (
        SELECT 
            A1.NOBUKTI,
            A1.QNT - ISNULL(B.QNT,0) AS SISA,
            A1.URUT,
            A1.KODEBRG,
            C.NAMABRG,
            A1.QNT,
            A1.QNT2,
            C.SAT1,
            C.SAT2,
            A1.ISI,
            D.NAMA AS NMGDGASAL,
            E.NAMA AS NMGDGTUJUAN,
            A1.NOSAT,
            A1.QNT2 - ISNULL(B.QNT2,0) AS SISA2,
            B.QNT AS qNTTR,
            B.QNT2 AS QNT2TR,
            A1.IsBatal,
            A1.QntBatal,
            A1.TglBatal,
            A1.UserBatal,
            A1.KetBatal
        FROM dbtransferdet A1
        LEFT OUTER JOIN (
            SELECT 
                NoTransfer,
                URUTTRANSFER,
                SUM(QNT) AS QNT,
                SUM(QNT2) AS QNT2
            FROM dbTransferdet
            GROUP BY NoTransfer, URUTTRANSFER
        ) B ON A1.NOBUKTI = B.NoTransfer 
            AND A1.URUT = B.URUTTRANSFER
        LEFT OUTER JOIN DBBARANG C ON A1.KODEBRG = C.KODEBRG
        LEFT OUTER JOIN DBGUDANG D ON A1.GDGASAL = D.KODEGDG
        LEFT OUTER JOIN DBGUDANG E ON A1.GDGTUJUAN = E.KODEGDG
        -- WHERE A1.GDGTUJUAN = 'GSM'  
    ) C ON A.NOBUKTI = C.NOBUKTI
    WHERE 
        C.SISA > 0   
        AND ISNULL(C.isbatal,0) = 0
        AND ISNULL(A.pTERIMA,0) = 0
    ORDER BY A.NoBukti
");



    // sheet kanan
    $tempPenerimaan = DB::connection("SML")->select("
    SELECT 
        B.NOBUKTI, 
        C.URUT,  
        C.KODEBRG, 
        C.NAMABRG, 
        '' AS Jns_Kertas, 
        '' AS Ukr_Kertas,
        C.QNT, 
        C.QNT2, 
        C.SAT1 AS SAT_1, 
        C.SAT2 AS SAT_2, 
        C.ISI, 
        B.GdgAsal, 
        B.GdgTujuan, 
        C.NMGDGASAL + ' (' + B.gdgAsal + ')' AS NamagdgAsal,
        C.NMGDGTUJUAN + ' (' + B.GdgTujuan + ')' AS NamagdgTujuan, 
        0.00 AS GSM,
        CASE 
            WHEN C.nosat = 1 THEN C.SISA 
            WHEN C.NOSAT = 2 THEN C.SISA2 
        END AS Qntx,
        CASE 
            WHEN C.nosat = 1 THEN C.sat1 
            WHEN C.NOSAT = 2 THEN C.sat2 
        END AS Satx,
        C.qNTTR,
        C.QNT2TR,
        C.IsBatal,
        C.QntBatal,
        C.TglBatal,
        C.UserBatal,
        C.KetBatal
    FROM dbTransfer A
    LEFT OUTER JOIN (
        SELECT 
            NObukti, 
            GdgAsal,
            GDGTujuan
        FROM dbTransferDET
        GROUP BY NObukti, GdgAsal, GDGTujuan
    ) B ON A.nobukti = B.nobukti
    LEFT OUTER JOIN (
        SELECT 
            A1.NOBUKTI,
            A1.QNT - ISNULL(B.QNT,0) AS SISA,
            A1.URUT,
            A1.KODEBRG,
            C.NAMABRG,
            A1.QNT,
            A1.QNT2,
            C.SAT1,
            C.SAT2,
            A1.ISI,
            D.NAMA AS NMGDGASAL,
            E.NAMA AS NMGDGTUJUAN,
            A1.NOSAT,
            A1.QNT2 - ISNULL(B.QNT2,0) AS SISA2,
            B.QNT AS qNTTR,
            B.QNT2 AS QNT2TR,
            A1.IsBatal,
            A1.QntBatal,
            A1.TglBatal,
            A1.UserBatal,
            A1.KetBatal
        FROM dbtransferdet A1
        LEFT OUTER JOIN (
            SELECT 
                NoTransfer,
                URUTTRANSFER,
                SUM(QNT) AS QNT,
                SUM(QNT2) AS QNT2
            FROM dbTransferdet
            GROUP BY NoTransfer, URUTTRANSFER
        ) B ON A1.NOBUKTI = B.NoTransfer 
            AND A1.URUT = B.URUTTRANSFER
        LEFT OUTER JOIN DBBARANG C ON A1.KODEBRG = C.KODEBRG
        LEFT OUTER JOIN DBGUDANG D ON A1.GDGASAL = D.KODEGDG
        LEFT OUTER JOIN DBGUDANG E ON A1.GDGTUJUAN = E.KODEGDG
        -- WHERE A1.GDGTUJUAN = 'GSM'   
    ) C ON A.NOBUKTI = C.NOBUKTI
    WHERE
        ISNULL(C.isbatal,0) = 1
        AND CAST(C.TglBatal AS DATE) BETWEEN :date1 AND :date2
    ORDER BY A.NoBukti
", [
    "date1" => $date1,
    "date2" => $date2
]);


    return view('gudang.closingtransfer', [
        "menul0" => $menul0,
        "periode" => $periode,
        "tempOutstanding" => $tempOutstanding,
        "tempPenerimaan" => $tempPenerimaan,
        "date1" => $date1,
        "date2" => $date2,
        "akses" => $akses
    ]);
}


  public function loadAll(Request $request)
{
    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    // Periode range utk tab "Closing Transfer Barang" (tempPenerimaan), dikirim dari
    // input date picker di halaman -- default satu bulan penuh periode kerja user
    // kalau belum dipilih (reload pertama kali). tempOutstanding tidak difilter
    // tanggal sama sekali, sesuai index().
    $date1 = $request->date1 ?: date('Y-m-01', mktime(0, 0, 0, $periode->bulan, 1, $periode->tahun));
    $date2 = $request->date2 ?: date('Y-m-t', mktime(0, 0, 0, $periode->bulan, 1, $periode->tahun));

    // Sheet kiri (Outstanding)
    $tempOutstanding = DB::connection("SML")->select("
        SELECT 
            B.NOBUKTI, 
            C.URUT,  
            C.KODEBRG, 
            C.NAMABRG, 
            '' AS Jns_Kertas, 
            '' AS Ukr_Kertas,
            C.QNT, 
            C.QNT2, 
            C.SAT1 AS SAT_1, 
            C.SAT2 AS SAT_2, 
            C.ISI, 
            B.GdgAsal, 
            B.GdgTujuan, 
            C.NMGDGASAL + ' (' + B.gdgAsal + ')' AS NamagdgAsal,
            C.NMGDGTUJUAN + ' (' + B.GdgTujuan + ')' AS NamagdgTujuan, 
            0.00 AS GSM,
            CASE 
                WHEN C.nosat = 1 THEN C.SISA 
                WHEN C.NOSAT = 2 THEN C.SISA2 
            END AS Qntx,
            CASE 
                WHEN C.nosat = 1 THEN C.sat1 
                WHEN C.NOSAT = 2 THEN C.sat2 
            END AS Satx,
            C.qNTTR,
            C.QNT2TR,
            C.IsBatal,
            C.QntBatal,
            C.TglBatal,
            C.UserBatal,
            C.KetBatal,
            C.NObukti + CAST(C.Urut AS VARCHAR(3)) AS KeyN
        FROM dbTransfer A
        LEFT OUTER JOIN (
            SELECT 
                NObukti, 
                GdgAsal,
                GDGTujuan
            FROM dbTransferDET
            GROUP BY NObukti, GdgAsal, GDGTujuan
        ) B ON A.nobukti = B.nobukti
        LEFT OUTER JOIN (
            SELECT 
                A1.NOBUKTI,
                A1.QNT - ISNULL(B.QNT,0) AS SISA,
                A1.URUT,
                A1.KODEBRG,
                C.NAMABRG,
                A1.QNT,
                A1.QNT2,
                C.SAT1,
                C.SAT2,
                A1.ISI,
                D.NAMA AS NMGDGASAL,
                E.NAMA AS NMGDGTUJUAN,
                A1.NOSAT,
                A1.QNT2 - ISNULL(B.QNT2,0) AS SISA2,
                B.QNT AS qNTTR,
                B.QNT2 AS QNT2TR,
                A1.IsBatal,
                A1.QntBatal,
                A1.TglBatal,
                A1.UserBatal,
                A1.KetBatal
            FROM dbtransferdet A1
            LEFT OUTER JOIN (
                SELECT 
                    NoTransfer,
                    URUTTRANSFER,
                    SUM(QNT) AS QNT,
                    SUM(QNT2) AS QNT2
                FROM dbTransferdet
                GROUP BY NoTransfer, URUTTRANSFER
            ) B ON A1.NOBUKTI = B.NoTransfer 
                AND A1.URUT = B.URUTTRANSFER
            LEFT OUTER JOIN DBBARANG C ON A1.KODEBRG = C.KODEBRG
            LEFT OUTER JOIN DBGUDANG D ON A1.GDGASAL = D.KODEGDG
            LEFT OUTER JOIN DBGUDANG E ON A1.GDGTUJUAN = E.KODEGDG
        ) C ON A.NOBUKTI = C.NOBUKTI
        WHERE 
            C.SISA > 0   
            AND ISNULL(C.isbatal,0) = 0
        ORDER BY A.NoBukti
    ");

    // Sheet kanan (Penerimaan / Batal)
    $tempPenerimaan = DB::connection("SML")->select("
        SELECT 
            B.NOBUKTI, 
            C.URUT,  
            C.KODEBRG, 
            C.NAMABRG, 
            '' AS Jns_Kertas, 
            '' AS Ukr_Kertas,
            C.QNT, 
            C.QNT2, 
            C.SAT1 AS SAT_1, 
            C.SAT2 AS SAT_2, 
            C.ISI, 
            B.GdgAsal, 
            B.GdgTujuan, 
            C.NMGDGASAL + ' (' + B.gdgAsal + ')' AS NamagdgAsal,
            C.NMGDGTUJUAN + ' (' + B.GdgTujuan + ')' AS NamagdgTujuan, 
            0.00 AS GSM,
            CASE 
                WHEN C.nosat = 1 THEN C.SISA 
                WHEN C.NOSAT = 2 THEN C.SISA2 
            END AS Qntx,
            CASE 
                WHEN C.nosat = 1 THEN C.sat1 
                WHEN C.NOSAT = 2 THEN C.sat2 
            END AS Satx,
            C.qNTTR,
            C.QNT2TR,
            C.IsBatal,
            C.QntBatal,
            C.TglBatal,
            C.UserBatal,
            C.KetBatal
        FROM dbTransfer A
        LEFT OUTER JOIN (
            SELECT 
                NObukti, 
                GdgAsal,
                GDGTujuan
            FROM dbTransferDET
            GROUP BY NObukti, GdgAsal, GDGTujuan
        ) B ON A.nobukti = B.nobukti
        LEFT OUTER JOIN (
            SELECT 
                A1.NOBUKTI,
                A1.QNT - ISNULL(B.QNT,0) AS SISA,
                A1.URUT,
                A1.KODEBRG,
                C.NAMABRG,
                A1.QNT,
                A1.QNT2,
                C.SAT1,
                C.SAT2,
                A1.ISI,
                D.NAMA AS NMGDGASAL,
                E.NAMA AS NMGDGTUJUAN,
                A1.NOSAT,
                A1.QNT2 - ISNULL(B.QNT2,0) AS SISA2,
                B.QNT AS qNTTR,
                B.QNT2 AS QNT2TR,
                A1.IsBatal,
                A1.QntBatal,
                A1.TglBatal,
                A1.UserBatal,
                A1.KetBatal
            FROM dbtransferdet A1
            LEFT OUTER JOIN (
                SELECT 
                    NoTransfer,
                    URUTTRANSFER,
                    SUM(QNT) AS QNT,
                    SUM(QNT2) AS QNT2
                FROM dbTransferdet
                GROUP BY NoTransfer, URUTTRANSFER
            ) B ON A1.NOBUKTI = B.NoTransfer 
                AND A1.URUT = B.URUTTRANSFER
            LEFT OUTER JOIN DBBARANG C ON A1.KODEBRG = C.KODEBRG
            LEFT OUTER JOIN DBGUDANG D ON A1.GDGASAL = D.KODEGDG
            LEFT OUTER JOIN DBGUDANG E ON A1.GDGTUJUAN = E.KODEGDG
        ) C ON A.NOBUKTI = C.NOBUKTI
        WHERE
            ISNULL(C.isbatal,0) = 1
            AND CAST(C.TglBatal AS DATE) BETWEEN :date1 AND :date2
        ORDER BY A.NoBukti
    ", [
        "date1" => $date1,
        "date2" => $date2
    ]);

    return response()->json([
        "tempOutstanding" => $tempOutstanding,
        "tempPenerimaan" => $tempPenerimaan,
        "date1" => $date1,
        "date2" => $date2
    ]);
}


public function lock(Request $req)
{
    $nobukti = $req->nobukti;
    $mode    = $req->mode;
    $urut    = $req->urut;
    $reason  = trim($req->reason);
    $user    = \Auth::user()->username;
    $tanggal = date('Y-m-d');

    if (!$reason) {
        return response()->json([
            'success' => false,
            'message' => 'Alasan penguncian tidak boleh kosong.'
        ], 422);
    }

    DB::connection('SML')->beginTransaction();
    try {
        if ($mode === 'item') {
            if (is_null($urut)) {
                return response()->json([
                    'success' => false,
                    'message' => 'URUT wajib dikirim untuk mode=item'
                ], 422);
            }

            // ambil data dulu untuk hitung sisa
            $item = DB::connection('SML')->table('DBTRANSFERDET')
                ->where('NOBUKTI', $nobukti)
                ->where('URUT', $urut)
                ->first();

            if (!$item) {
                throw new \Exception("Data tidak ditemukan untuk NOBUKTI $nobukti dan URUT $urut.");
            }

            // hitung SISA / SISA2
            $sisa  = $item->QNT - ($item->QNTTR ?? 0);
            $sisa2 = $item->QNT2 - ($item->QNT2TR ?? 0);

            $qntBatal = $item->NOSAT == 1 ? $sisa : $sisa2;

            DB::connection('SML')->table('DBTRANSFERDET')
                ->where('NOBUKTI', $nobukti)
                ->where('URUT', $urut)
                ->update([
                    'QntBatal'  => $qntBatal,
                    'Ketbatal'  => $reason,
                    'UserBatal' => $user,
                    'TglBatal'  => $tanggal,
                    'IsBatal'   => 1
                ]);

        } else {
            $items = DB::connection('SML')
                ->table('DBTRANSFERDET')
                ->where('NOBUKTI', $nobukti)
                ->get();

            if ($items->isEmpty()) {
                throw new \Exception("Data tidak ditemukan di DBTRANSFERDET.");
            }

            foreach ($items as $item) {
                $sisa  = $item->QNT - ($item->QNTTR ?? 0);
                $sisa2 = $item->QNT2 - ($item->QNT2TR ?? 0);

                $qntBatal = $item->NOSAT == 1 ? $sisa : $sisa2;

                DB::connection('SML')->table('DBTRANSFERDET')
                    ->where('NOBUKTI', $item->NOBUKTI)
                    ->where('URUT', $item->URUT)
                    ->update([
                        'QntBatal'  => $qntBatal,
                        'Ketbatal'  => $reason,
                        'UserBatal' => $user,
                        'TglBatal'  => $tanggal,
                        'IsBatal'   => 1
                    ]);
            }
        }

        DB::connection('SML')->commit();
        return response()->json(['success' => true]);
    } catch (\Throwable $e) {
        DB::connection('SML')->rollBack();
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}


public function unlock(Request $req)
{
    $nobukti = $req->nobukti;
    $urut    = $req->urut;
    $kodebrg = $req->kodebrg;
    $mode    = $req->mode ?? 'all';

    DB::connection('SML')->beginTransaction();
    try {
        if (!$nobukti) {
            return response()->json([
                'success' => false,
                'message' => 'No. Bukti tidak boleh kosong.'
            ], 422);
        }

        $query = DB::connection('SML')
            ->table('DBTRANSFERDET')
            ->where('NOBUKTI', $nobukti);

        if ($mode === 'item') {
            if (!$urut) {
                return response()->json([
                    'success' => false,
                    'message' => 'URUT dan KODEBRG wajib dikirim untuk mode=item'
                ], 422);
            }

            $query->where('URUT', $urut);
        }

        $affected = $query->update([
            'QntBatal'  => 0,
            'Ketbatal'  => '',
            'UserBatal' => '',
            'TglBatal'  => null,
            'IsBatal'   => 0
        ]);

        if ($affected === 0) {
            throw new \Exception("Tidak ada data yang diupdate. Pastikan No. Bukti, URUT, dan KODEBRG sesuai."); 
        }

        DB::connection('SML')->commit();
        return response()->json(['success' => true]);
    } catch (\Throwable $e) {
        DB::connection('SML')->rollBack();
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}


}
