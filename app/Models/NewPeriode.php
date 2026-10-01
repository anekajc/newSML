<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NewPeriode extends Model
{
		protected $connection = 'SML';
    // protected $primaryKey = 'id';
    protected $table = 'DBPERIODE';
    protected $fillable = array(
        'user_id',
        'bulan',
        'tahun'
    );
    public $timestamps = false;
}

?>
