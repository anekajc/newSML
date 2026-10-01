<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class KoreksiStock extends Model
{
		protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $table = 'koreksi_stock';
    protected $fillable = array(
				'no_urut',
        'no_bukti',
				'tanggal',
				'gudang',
				'keterangan',
				'auth_1',
				'auth_user_1',
				'auth_date_1',
				'batal',
				'batal_user',
				'batal_date',
				'deleted'
    );
    public $timestamps = false;
}

?>
