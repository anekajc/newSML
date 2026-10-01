<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AreaSQL extends Model
{
		protected $connection = 'sqlsrv';
    // protected $primaryKey = 'id';
    protected $table = 'DBAREA';
    protected $fillable = array(
        'KODEAREA',
				'NAMAAREA'
    );
    public $timestamps = false;
}

?>
