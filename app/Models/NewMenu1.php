<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NewMenu extends Model
{
		protected $connection = 'sqlsrv';
    protected $table = 'DBMENU';
		// protected $table = 'new_menu';
    protected $fillable = array(
        'KODEMENU',
        'Keterangan',
        'L0',
        'ACCESS',
    );
    public $timestamps = false;
}


?>
