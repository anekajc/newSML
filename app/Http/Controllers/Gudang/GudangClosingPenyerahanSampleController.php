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



class GudangClosingPenyerahanSampleController extends Controller
{
  public function index(Request $req) {
    $kodemenu = '060531';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses1($kodemenu , $req->path());
    if (!$akses || !$akses->HASACCESS) {
        return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(6);

    // sheet kiri
    $tempOutstanding = DB::connection("SML")->select("
        SELECT 
            nobukti,
            Tanggal,
            Keterangan,
            KODECUSTSUPP,
            NAMACUSTSUPP,
            KODEBRG,
            NAMABRG,
            NOTE,
            sisa,
            QntCLose,
            Urut,
            nobukti + CAST(Urut AS VARCHAR(10)) AS KeyNobukti
        FROM VWREPORToutSERAHSAMPLE");

    // sheet kanan
    $tempPenerimaan = DB::connection("SML")->select("
        SELECT  
            B.NOBUKTI, 
            B.URUT,  
            B.KODEBRG, 
            C.NAMABRG,
            CASE 
                WHEN B.NOSAT = 1 THEN B.SAT_1 
                ELSE B.SAT_2 
            END AS Satuan,
            B.QntCLose,
            B.UserBatal,
            B.tglbatal,
            B.IsBatal,
            B.NOBUKTI + CAST(B.URUT AS VARCHAR(10)) AS KeyUrut,
            B.ketBatal
        FROM DBSERAHSAMPLEDET B
        LEFT OUTER JOIN dbBarang C ON C.KodeBrg = B.KodeBrg
        WHERE ISNULL(B.QntClose, 0) <> 0
    ");

    return view('gudang.gudangclosingpenyerahansample', [
        "menul0" => $menul0,
        "periode" => $periode,
        "tempOutstanding" => $tempOutstanding,
        "tempPenerimaan" => $tempPenerimaan,
        "akses" => $akses
    ]);
}


  public function loadAll(Request $request)
{
    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    // Sheet kiri
    $tempOutstanding = DB::connection("SML")->select("
    SELECT 
        nobukti,
        Tanggal,
        Keterangan,
        KODECUSTSUPP,
        NAMACUSTSUPP,
        KODEBRG,
        NAMABRG,
        NOTE,
        sisa,
        QntCLose,
        Urut,
        nobukti + CAST(Urut AS VARCHAR(10)) AS KeyNobukti,
        ISBATAL
    FROM VWREPORToutSERAHSAMPLE
    WHERE ISNULL(ISBATAL, 0) = 0");

    // Sheet kanan
    $tempPenerimaan = DB::connection("SML")->select("
        SELECT  
            B.NOBUKTI, 
            B.URUT,  
            B.KODEBRG, 
            C.NAMABRG,
            CASE 
                WHEN B.NOSAT = 1 THEN B.SAT_1 
                ELSE B.SAT_2 
            END AS Satuan,
            B.QntCLose,
            B.UserBatal,
            cast(B.tglbatal as datetime)tglbatal,
            B.IsBatal,
            B.NOBUKTI + CAST(B.URUT AS VARCHAR(10)) AS KeyUrut,
            B.ketBatal
        FROM DBSERAHSAMPLEDET B
        LEFT OUTER JOIN dbBarang C ON C.KodeBrg = B.KodeBrg
        WHERE ISNULL(B.QntClose, 0) <> 0
    ");

    return response()->json([
        "tempOutstanding" => $tempOutstanding,
        "tempPenerimaan" => $tempPenerimaan
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

            $data = DB::connection('SML')
                ->table('VWREPORToutSERAHSAMPLE')
                ->where('nobukti', $nobukti)
                ->where('Urut', $urut)
                ->first();

            if (!$data) {
                throw new \Exception("Data tidak ditemukan di view VWREPORToutSERAHSAMPLE.");
            }

            DB::connection('SML')->table('DBSERAHSAMPLEDET')
                ->where('NOBUKTI', $data->nobukti)
                ->where('URUT', $data->URUT)
                ->update([
                    'QntCLose'  => $data->sisa,
                    'Ketbatal'  => $reason,
                    'userbatal' => $user,
                    'tglbatal'  => $tanggal,
                    'isbatal'   => 1
                ]);
        } else {
            $data = DB::connection('SML')
                ->table('VWREPORToutSERAHSAMPLE')
                ->where('nobukti', $nobukti)
                ->get();

            if ($data->isEmpty()) {
                throw new \Exception("Data tidak ditemukan di view VWREPORToutSERAHSAMPLE.");
            }

            foreach ($data as $item) {
                DB::connection('SML')->table('DBSERAHSAMPLEDET')
                    ->where('NOBUKTI', $item->nobukti)
                    ->where('URUT', $item->URUT)
                    ->update([
                        'QntCLose'  => $item->sisa,
                        'Ketbatal'  => $reason,
                        'userbatal' => $user,
                        'tglbatal'  => $tanggal,
                        'isbatal'   => 1
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
    $mode    = $req->mode ?? 'all';
    $user    = \Auth::user()->username;

    DB::connection('SML')->beginTransaction();
    try {
        if (!$nobukti) {
            return response()->json([
                'success' => false,
                'message' => 'No. Bukti tidak boleh kosong.'
            ], 422);
        }

        $query = DB::connection('SML')->table('DBSERAHSAMPLEDET')->where('NOBUKTI', $nobukti);

        if ($mode === 'item') {
            if (!$urut) {
                return response()->json([
                    'success' => false,
                    'message' => 'URUT tidak boleh kosong untuk mode=item'
                ], 422);
            }

            $query->where('URUT', $urut);
        }

        $affected = $query->update([
            'QntCLose' => 0,
            'Ketbatal' => '',
            'userbatal' => '',
            'tglbatal' => null,
            'isbatal' => 0
        ]);

        if ($affected === 0) {
            throw new \Exception("Tidak ada data yang diupdate. Pastikan No. Bukti dan URUT sesuai.");
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
