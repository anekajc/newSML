<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class vwShowAlokasiBrg extends Model
{
		protected $connection = 'sqlsrv';
    protected $table = 'vwShowAlokasiBrg';
    protected $fillable = array(
        'NOBUKTI',
				'URUT',
				'KODEBRG',
				'NamaBrg',
				'KodeGdg',
				'KodeRak',
				'KodeLokasi',
				'QNT',
				'NOSAT',
				'SATUAN',
				'ISI',
				'NoBeli',
				'UrutBeli',
				'NOURUT',
				'TANGGAL',
				'BarcodeLoc',
				'KETERANGAN',
				'IsOtorisasi1',
				'QNTSISA',
				'kodecustsupp',
				'namacustsupp',
				'noso'
    );
    public $timestamps = false;
}

?>
