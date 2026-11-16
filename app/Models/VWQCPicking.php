<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VWQCPicking extends Model
{
		protected $connection = 'SML';
    protected $table = 'VWQCPicking';
    // protected $fillable = array(
    //
    // );
    public $timestamps = false;
}

?>
