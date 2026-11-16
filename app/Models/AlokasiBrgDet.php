<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AlokasiBrgDet extends Model
{
		protected $connection = 'SML';
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
