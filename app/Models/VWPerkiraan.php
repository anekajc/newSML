<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VWPerkiraan extends Model
{
	protected $connection = 'SML';
    protected $table = 'VWPerkiraan';
    // protected $fillable = array(
    //
    // );
    public $timestamps = false;
}

?>
