<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OutBrgDet2 extends Model
{
		protected $connection = 'SPLSIG';
    // protected $primaryKey = 'id';
    protected $table = 'DBOutBrgDet2';
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
				'NOBATCH',
				'pFOC'
    );
    public $timestamps = false;
}

?>
