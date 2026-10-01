<?php

namespace App\Http\Controllers\Berkas;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// use App\Http\Controllers\NewMenuController;

class KunciPeriodeKerjaController extends Controller
{

  public function index(Request $req) {

    // Daftar user & menu samping tidak dipakai halaman ini (layout newmasterTest mengambil menu
    // lewat AJAX /getmenu) - query-nya dimatikan supaya halaman lebih ringan.
    // $users = DB::connection("SML")->select('select * from DBFLPASS');
    // $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(1);

    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();

    return view('berkas.kunciperiode' , [
      "menul0" => [],
      "periode" => $periode,
    ]);

  }

  public function loadAll () {
    $users = DB::connection("SML")->select('select * from DBFLPASS');
    return $users;

  }

public function kunciPeriodeLoad(Request $req)
{
    $tahun = $req->tahun;

    if (!preg_match('/^\d{4}$/', (string) $tahun)) {
      return response()->json([]);
    }

    $bulanLocked = DB::connection('SML')
        ->table('DBLOCKPERIODE')
        ->where('TAHUN', $tahun)
        ->pluck('BULAN');

    return response()->json($bulanLocked);
}

public function kunciPeriodeToggle(Request $req)
{
    $bulan   = $req->bulan;
    $tahun   = $req->tahun;
    $checked = $req->checked; // '1' = lock (insert), '0' = unlock (delete)

    if (!preg_match('/^\d{4}$/', (string) $tahun)) {
      return response()->json('Tahun harus 4 angka');
    }
    if (!ctype_digit((string) $bulan) || (int) $bulan < 1 || (int) $bulan > 12) {
      return response()->json('Bulan tidak dikenal');
    }
    if ((string) $checked !== '0' && (string) $checked !== '1') {
      return response()->json('Status kunci tidak dikenal');
    }

    if ($checked == 1) {
        $exists = DB::connection('SML')
            ->table('DBLOCKPERIODE')
            ->where('BULAN', $bulan)
            ->where('TAHUN', $tahun)
            ->exists();

        if (!$exists) {
            DB::connection('SML')->table('DBLOCKPERIODE')->insert([
                'BULAN' => $bulan,
                'TAHUN' => $tahun,
            ]);
        }
    } else {
        DB::connection('SML')
            ->table('DBLOCKPERIODE')
            ->where('BULAN', $bulan)
            ->where('TAHUN', $tahun)
            ->delete();
    }

    return response()->json(1);
}

}
