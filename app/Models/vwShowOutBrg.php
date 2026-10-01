<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class vwShowOutBrg extends Model
{
		protected $connection = 'sqlsrv';
    protected $table = 'vwShowOutBrg';
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
