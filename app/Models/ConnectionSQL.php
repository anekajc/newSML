<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ConnectionSQL extends Model
{
		protected $connection = 'sqlsrv';
    // protected $primaryKey = 'KODEGDG';
    protected $table = 'SP_VALAS';
    protected $fillable = array(
        'Choice',
				'KodeVls',
				'NamaVls',
				'Simbol',
				'OldKode'
    );
    public $timestamps = false;
}

?>
