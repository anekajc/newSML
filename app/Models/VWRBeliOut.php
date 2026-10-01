<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VWRBeliOut extends Model
{
		protected $connection = 'SML';
    protected $table = 'VWRBeliOut';
    // protected $fillable = array(
    //
    // );
    public $timestamps = false;
}

?>
