<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NewMenuReport extends Model
{
		protected $connection = 'SML';
    // protected $table = 'DBMENU';
		protected $table = 'DBMENUREPORT'; // ganti ke db menu report
        protected $fillable = array(
            'KODEMENU',
            'Keterangan',
            'L0',
            'ACCESS',
            'OL',
            'HeaderMenu',
            'href'
        );
        public $timestamps = false;
}


?>
