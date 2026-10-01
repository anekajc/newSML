<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NEWDBBELI extends Model
{
		protected $connection = 'SPLSIG';
    // protected $table = 'DBMENU';
    protected $primaryKey = 'id';
		protected $table = 'NEWDBBELI';
    protected $fillable = array(
        'Tanggal',
        'NamaSupplier',
        'NoPo',
        'Gudang',
				'FakturSupp',
        'KodeBarang',
        'NamaBarang',
        'Qty',
        'QtyPO',
        'QtyOS',
        'Satuan',
        'NoSO',
        'NoPOCust',
        'Customer',
        'SuratJalan',
        'NoKendSopir',
				'NoBukti'
    );
    public $timestamps = false;
}


?>
