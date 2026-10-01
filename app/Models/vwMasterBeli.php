<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class vwMasterBeli extends Model
{
		protected $connection = 'SML';
    protected $table = 'vwMasterBeli';
    // protected $fillable = array(
    //
    // );
    public $timestamps = false;
}

?>
