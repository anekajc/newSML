<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class vwOUtPOWMSNONSTOCK extends Model
{
		protected $connection = 'SML';
    protected $table = 'OUtPOWMSNONSTOCK';
    protected $fillable = array(
        'NoBukti',
				'Urut',
				'KodeBrg',
				'PartNumber',
				'namaBrg',
				'ISI',
				'QNTOS',
				'QntPO',
				'Satuan',
				'QntBeliSat1',
				'QNT',
				'OS',
				'QntOS2',
				'QntBeli',
				'KODESUPP',
        'NAMACUSTSUPP',
        'TANGGAL',
	'NoSat',
	'Isi'
    );
    public $timestamps = false;
}

?>
