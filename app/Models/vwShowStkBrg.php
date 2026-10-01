<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class vwShowStkBrg extends Model
{
		protected $connection = 'sqlsrv';
    protected $table = 'vwShowStkBrg';
    protected $fillable = array(
				'NoBukti',
				'NOURUT',
				'TANGGAL',
				'Kodebrg',
				'namabrg',
				'nosat',
				'satuan',
				'isi',
				'Kodegdg',
				'NAMAGUDANG',
				'koderak',
				'kodelokasi',
				'Barcode',
				'Qntsaldo',
				'QntOut',
				'SisaOut',
				'URUT'
    );
    public $timestamps = false;
}

?>
