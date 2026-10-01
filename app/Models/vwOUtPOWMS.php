<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class vwOUtPOWMS extends Model
{
		protected $connection = 'SML';
    protected $table = 'OUtPOWMS';
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
				'NAMAGDG',
				'NAMAEXP',
				'KODESUPP',
        'NAMACUSTSUPP',
        'TANGGAL',
	'NoSat',
	'Isi',
	'isOtorisasi1'
    );
    public $timestamps = false;
}

?>
