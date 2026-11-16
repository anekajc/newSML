<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\NewPeriode;
use App\Traits\BadanUsahaTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Master Badan Usaha (DBBADANUSAHA): PT, CV, UD, ... + setting PPh21 & PPh23.
 * Dipakai dropdown Bentuk Usaha di Master Customer, Supplier, dan Ekspedisi.
 * Pola endpoint sama dengan MasterJenisCustomerController (respon 1 = sukses, string = pesan).
 */
class MasterBadanUsahaController extends Controller
{
    use BadanUsahaTrait;

    public function index(Request $req)
    {
        $periode = NewPeriode::where('user_id', \Auth::User()->username)->first();
        $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

        return view('master.masterbadanusaha', [
            'menul0' => $menul0,
            'periode' => $periode,
        ]);
    }

    public function loadAll()
    {
        return $this->listBadanUsaha();
    }

    /** Validasi isian; kembalikan pesan error atau null. */
    private function validasi($kode, $pph21, $pph23)
    {
        if (! $this->kodeUsahaValid($kode)) {
            return 'Kode Badan Usaha harus diisi huruf/angka (tidak boleh kosong atau -)';
        }
        if (strlen($kode) > 20) {
            return 'Kode Badan Usaha maksimal 20 karakter';
        }
        foreach (['PPh21' => $pph21, 'PPh23' => $pph23] as $label => $nilai) {
            if ($nilai !== '' && $nilai !== null && ! is_numeric($nilai)) {
                return $label.' harus berupa angka';
            }
            if ((float) $nilai < 0 || (float) $nilai > 100) {
                return $label.' harus di antara 0 - 100';
            }
        }
        if ((float) $pph21 <= 0 && (float) $pph23 <= 0) {
            return 'PPh21 dan PPh23 tidak boleh kosong semua, isi minimal salah satu';
        }

        return null;
    }

    public function spAdd(Request $req)
    {
        $kode = strtoupper(trim((string) $req->kode));
        $err = $this->validasi($kode, $req->pph21, $req->pph23);
        if ($err) {
            return $err;
        }

        try {
            $check = DB::connection('SML')->select('SELECT KodeUsaha FROM DBBADANUSAHA WHERE KodeUsaha = :kode', ['kode' => $kode]);
            if ($check) {
                return 'Kode Badan Usaha sudah ada di database';
            }

            DB::connection('SML')->insert('INSERT INTO DBBADANUSAHA (KodeUsaha, PPH21, PPH23) VALUES (:kode, :pph21, :pph23)', [
                'kode' => $kode,
                'pph21' => (float) $req->pph21,
                'pph23' => (float) $req->pph23,
            ]);

            return 1;
        } catch (\Throwable $e) {
            Log::error('MasterBadanUsaha spAdd: '.$e->getMessage());

            return 'Gagal menyimpan Badan Usaha';
        }
    }

    public function spEdit(Request $req)
    {
        $kode = trim((string) $req->kode);
        $err = $this->validasi($kode, $req->pph21, $req->pph23);
        if ($err) {
            return $err;
        }

        try {
            $edit = DB::connection('SML')->update('UPDATE DBBADANUSAHA SET PPH21 = :pph21, PPH23 = :pph23 WHERE KodeUsaha = :kode', [
                'kode' => $kode,
                'pph21' => (float) $req->pph21,
                'pph23' => (float) $req->pph23,
            ]);

            return $edit ? 1 : 'Badan Usaha tidak ditemukan';
        } catch (\Throwable $e) {
            Log::error('MasterBadanUsaha spEdit: '.$e->getMessage());

            return 'Gagal mengedit Badan Usaha';
        }
    }

    public function spDelete(Request $req)
    {
        $kode = trim((string) $req->kode);

        $check = DB::connection('SML')->select('SELECT TOP 1 KODECUSTSUPP FROM DBCUSTSUPP WHERE USAHA = :kode', ['kode' => $kode]);
        if ($check) {
            return 'Badan Usaha '.$kode.' dipakai di Master Customer / Supplier / Ekspedisi';
        }

        DB::connection('SML')->delete('DELETE FROM DBBADANUSAHA WHERE KodeUsaha = :kode', ['kode' => $kode]);

        return 1;
    }

    public function spDetail(Request $req)
    {
        return DB::connection('SML')->select('SELECT KodeUsaha, PPH21, PPH23 FROM DBBADANUSAHA WHERE KodeUsaha = :kode', ['kode' => $req->kode]);
    }
}
