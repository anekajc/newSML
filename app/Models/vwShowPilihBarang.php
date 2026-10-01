<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class vwShowPilihBarang extends Model
{
		protected $connection = 'sqlsrv';
    protected $table = 'vwShowPilihBarang';
    protected $fillable = array(
				'Kodebrg',
				'namabrg',
				'Kodegdg',
				'Tipe'
    );
    public $timestamps = false;
}

?>
