<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class vwShowOutBrgNonSO extends Model
{
		protected $connection = 'SML';
    protected $table = 'vwShowOutBrgNonSO';
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
