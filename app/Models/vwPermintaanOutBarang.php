<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class vwPermintaanOutBarang extends Model
{
		protected $connection = 'sqlsrv';
    protected $table = 'vwPermintaanOutBarang';
    protected $fillable = array(
        'NoBukti',
				'NOURUT',
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
				'isi'
    );
    public $timestamps = false;
}

?>
