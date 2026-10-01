<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RakSQL extends Model
{
		protected $connection = 'sqlsrv';
    // protected $primaryKey = 'KODEGDG';
    protected $table = 'dbRak';
    protected $fillable = array(
        'KodeRak',
				'NamaRak',
				'Tingkat',
				'Kolom',
				'KodeGdg'
    );
    public $timestamps = false;
}

?>
