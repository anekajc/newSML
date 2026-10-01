<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AlokasiBrg extends Model
{
		protected $connection = 'sqlsrv';
    // protected $primaryKey = 'id';
    protected $table = 'DBAlokasiBrg';
    protected $fillable = array(
        'NOBUKTI',
				'NOURUT',
				'TANGGAL',
				'KodeGdg',
				'KodeRak',
				'KodeLokasi',
				'KETERANGAN',
				'IsOtorisasi1',
				'OtoUser1',
				'TglOto1',
				'IsOtorisasi2',
				'OtoUser2',
				'TglOto2',
				'IsOtorisasi3',
				'OtoUser3',
				'TglOto3',
				'IsOtorisasi4',
				'OtoUser4',
				'TglOto4',
				'IsOtorisasi5',
				'OtoUser5',
				'TglOto5',
				'MaxOL',
				'IsBatal',
				'UserBatal',
				'TglBatal',
				'UserInput',
				'TglInput',
				'BarcodeLoc'
    );
    public $timestamps = false;
}

?>
