<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class vwShowDetailAlokasiBrg extends Model
{
		protected $connection = 'sqlsrv';
    protected $table = 'vwShowDetailAlokasiBrg';
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
				'noso'
    );
    public $timestamps = false;
}

?>
