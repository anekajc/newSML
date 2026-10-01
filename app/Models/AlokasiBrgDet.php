<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AlokasiBrgDet extends Model
{
		protected $connection = 'sqlsrv';
    // protected $primaryKey = 'id';
    protected $table = 'DBAlokasiBrgDet';
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
