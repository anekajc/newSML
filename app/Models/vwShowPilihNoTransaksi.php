<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class vwShowPilihNoTransaksi extends Model
{
		protected $connection = 'sqlsrv';
    protected $table = 'vwShowPilihNoTransaksi';
    protected $fillable = array(
        'NoBukti',
				'TANGGAL',
				'Kodegdg',
				'Tipe'
    );
    public $timestamps = false;
}

?>
