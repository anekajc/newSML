<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class KoreksiDet extends Model
{
		protected $connection = 'sqlsrv';
    // protected $primaryKey = 'id';
    protected $table = 'DBKOREKSISIGDET';
    protected $fillable = array(
				'NOBUKTI',
        'NOURUT',
				'KODEBRG',
				'KODEGDG',
				'SATUAN',
				'NOSAT',
				'ISI',
				'SaldoComp',
				'QntOpname',
				'Selisih',
				'QNTDB',
				'QNTCR',
				'HARGA',
				'HPP',
				'QntOpname1',
				'QntOpname2',
				'Perkiraan',
				'CollyComp',
				'CollyOpname',
				'CollyDb',
				'CollyCr',
				'KodeWarna',
				'KodeCust',
				'Barcode'
    );
    public $timestamps = false;
}

?>
