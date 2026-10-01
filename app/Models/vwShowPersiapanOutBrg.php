<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class vwShowPersiapanOutBrg extends Model
{
		protected $connection = 'sqlsrv';
    protected $table = 'vwShowPersiapanOutBrg';
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
