<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LokasiSQL extends Model
{
		protected $connection = 'SML';
    // protected $primaryKey = 'KODEGDG';
    protected $table = 'dbLokasiSIG';
    protected $fillable = array(
        'KodeLokasi',
				'NamaLokasi',
				'KodeGdg',
				'KodeRak',
				'BarcodeLoc'
    );
    public $timestamps = false;
}

?>
