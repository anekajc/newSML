<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Koreksi extends Model
{
		protected $connection = 'sqlsrv';
    // protected $primaryKey = 'id';
    protected $table = 'DBKOREKSISIG';
    protected $fillable = array(
			'NOBUKTI',
			'NOURUT',
			'TANGGAL',
			'KodeGdg',
			'NOTE',
			'IsCetak',
			'NilaiCetak',
			'FlagTipe',
			'JenisTrans',
			'Barcode'
    );
    public $timestamps = false;
}

?>
