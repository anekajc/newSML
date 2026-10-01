<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VWOutPenerimaanBrg extends Model
{
		protected $connection = 'sqlsrv';
    protected $table = 'VWOUTPENERIMAANBRG';
    protected $fillable = array(
        'NoBukti1',
				'Urut',
				'TANGGAL',
				'Kodebrg',
				'NAMABRG',
				'Kodegdg',
				'NAMAGUDANG',
				'QntSaldo',
				'QNTAMBIL',
				'QNTSISA',
				'nosat',
				'Satuan',
				'isi',
				'kodecustsupp'
    );
    public $timestamps = false;
}

?>
