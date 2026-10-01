<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class vwOutBarang extends Model
{
		protected $connection = 'sqlsrv';
    protected $table = 'vwOutBarang';
    protected $fillable = array(
        'NoBukti',
				'Urut',
				'TANGGAL',
				'Kodebrg',
				'namabrg',
				'Kodegdg',
				'NAMAGUDANG',
				'Qntsaldo',
				'QntOut',
				'SisaOut',
				'nosat',
				'satuan',
				'isi',
				'Tipe'
    );
    public $timestamps = false;
}

?>
