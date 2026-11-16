<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DBBarangLokasi extends Model
{
		protected $connection = 'SML';
    // protected $primaryKey = 'KODEGDG';
    protected $table = 'dbBarangLokasiSIG';
    protected $fillable = array(
        'KodeBarang',
				'BarcodeLoc',
				'Urut'
    );
    public $timestamps = false;
}

?>
