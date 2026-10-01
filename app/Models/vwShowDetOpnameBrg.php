<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class vwShowDetOpnameBrg extends Model
{
		protected $connection = 'sqlsrv';
    protected $table = 'vwShowDetOpnameBrg';
    protected $fillable = array(
			'NOBUKTI',
			'NOURUT',
			'TANGGAL',
			'KodeGdg1',
			'NOTE',
			'URUT',
			'KODEBRG',
			'NAMABRG',
			'KODEGDG',
			'SATUAN',
			'NOSAT',
			'ISI',
			'SaldoComp',
			'QntOpname',
			'Selisih',
			'Barcode',
			'KodeRak',
			'KodeLokasi'
    );
    public $timestamps = false;
}

?>
