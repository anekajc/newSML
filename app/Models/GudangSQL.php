<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GudangSQL extends Model
{
		protected $connection = 'SML';
    // protected $primaryKey = 'KODEGDG';
    protected $table = 'vwDBGudang';
    protected $fillable = array(
        'KODEGDG',
				'NAMA',
				'Alamat',
				'IsProduksi',
				'IsRusak',
				'istakeinout',
				'pSampit',
				'pPusat',
				'IsCust'
    );
    public $timestamps = false;
}

?>
