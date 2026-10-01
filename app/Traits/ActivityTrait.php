<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait ActivityTrait
{

  public function logActivity($tipe, $user, $no_bukti) {
    DB::table('log_user')->insert([
      'tipe' => $tipe,
      'user' => $user,
      'no_bukti' => $no_bukti
    ]);
  }
  
}
