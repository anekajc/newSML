<?php

namespace App\Http\Controllers\Berkas;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewPeriode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Menu Berkas > Ganti Password (DBMENUWEB 0005, href gantipassword).
// Sistemnya mengikuti programajcSML: cukup password baru + ulangi password baru, tanpa
// password lama. Dulu di sini password lama wajib dicocokkan dengan Hash::check, sehingga
// user yang password-nya masih kosong di DBFLPASS tidak bisa mengganti password sama sekali.
class GantiPasswordController extends Controller
{

  public function index() {

    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();

    return view('berkas.gantipassword' , [
      "menul0" => [],
      "periode" => $periode,
    ]);

  }

  public function gantiPassword (Request $req) {
    $baru = (string) $req->input('password');

    if ($baru === '') {
      return 'Password harus diisi';
    }
    if ($baru !== (string) $req->input('password2')) {
      return 'Password harus sama';
    }

    User::where('username', \Auth::user()->username)->update(['password' => Hash::make($baru)]);
    return 1;
  }

}
