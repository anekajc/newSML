<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VWStkBrg extends Model
{
		protected $connection = 'sqlsrv';
    protected $table = 'VWSTKBRG';
    protected $fillable = array(
        'KODEBRG',
				'NAMABRG',
				'KODEGDG',
				'BARCODE',
				'SALDOQNT',
				'SAT1',
				'ISI1'
    );
    public $timestamps = false;
}

?>
