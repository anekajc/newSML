<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VWPenyerahanOut extends Model
{
		protected $connection = 'SML';
    protected $table = 'VWPenyerahanOut';
    // protected $fillable = array(
    //
    // );
    public $timestamps = false;
}

?>
