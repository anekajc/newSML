<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DBFLMENU extends Model
{
		protected $connection = 'SML';
    // protected $primaryKey = 'id';
    protected $table = 'DBFLMENU';
    protected $fillable = array(
        'USERID',
				'L1',
        'HASACCESS',
				'ISTAMBAH',
				'ISHAPUS',
				'ISKOREKSI',
				'ISCETAK',
				'ISEXPORT',
				'IsOtorisasi1',
				'IsOtorisasi2',
				'IsOtorisasi3',
				'IsOtorisasi4',
				'IsOtorisasi5',
				'IsBatal',
				'pembatalan'
    );
    public $timestamps = false;
}

?>
