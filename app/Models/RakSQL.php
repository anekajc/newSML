<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RakSQL extends Model
{
		protected $connection = 'SML';
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
