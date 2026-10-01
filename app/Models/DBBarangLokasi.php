<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DBBarangLokasi extends Model
{
		protected $connection = 'sqlsrv';
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
