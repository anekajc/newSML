<?php

namespace App\Http\Controllers\Berkas;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewPeriode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Menu Berkas > Ganti Password (DBMENUWEB 0005, href gantipassword). Route-nya sudah ada di
// routes/berkas.php tapi controller ini belum ada di newProgramAJC, jadi menu itu selalu
// 500 "Target class does not exist". Diadaptasi dari programajc.
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
    $lama = (string) $req->input('passwordLama');
    $baru = (string) $req->input('password');

    if ($lama === '') {
      return 'Password lama harus diisi';
    }
    if ($baru === '') {
      return 'Password baru harus diisi';
    }
    if ($baru !== (string) $req->input('password2')) {
      return 'Konfirmasi password baru tidak sama';
    }

    $user = \Auth::user();
    if (!Hash::check($lama, $user->password)) {
      return 'Password lama salah';
    }
    if (Hash::check($baru, $user->password)) {
      return 'Password baru sama dengan password lama';
    }

    User::where('username', $user->username)->update(['password' => Hash::make($baru)]);
    return 1;
  }

}
