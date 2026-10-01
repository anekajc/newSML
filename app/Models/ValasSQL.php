<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ValasSQL extends Model
{
		protected $connection = 'sqlsrv';
    // protected $primaryKey = 'id';
    protected $table = 'DBVALAS';
    protected $fillable = array(
        'KODEVLS',
				'NAMAVLS',
				'KURS',
				'Simbol'
    );
    public $timestamps = false;
}

?>
