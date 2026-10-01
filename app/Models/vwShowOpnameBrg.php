<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class vwShowOpnameBrg extends Model
{
		protected $connection = 'sqlsrv';
    protected $table = 'vwShowOpnameBrg';
    protected $fillable = array(
			'NOBUKTI',
			'NOURUT',
			'TANGGAL',
			'KodeGdg',
			'NOTE',
			'Barcode',
			'KodeRak',
			'KodeLokasi',
			'Hari',
			'Bln',
			'Thn'
    );
    public $timestamps = false;
}

?>
