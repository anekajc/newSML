<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NewMenu extends Model
{
		protected $connection = 'SML';
    // protected $table = 'DBMENU';
		protected $table = 'DBMENUWEB';
    protected $fillable = array(
        'KODEMENU',
        'Keterangan',
        'L0',
        'ACCESS',
				'OL',
				'TipeTrans',
				'HeaderMenu',
				'href'
    );
    public $timestamps = false;
}


?>
