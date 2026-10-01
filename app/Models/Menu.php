<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Menu extends Model
{
		protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $table = 'menu';
    protected $fillable = array(
        'keterangan',
        'l0',
        'access',
				'ol',
				'parent',
				'grup',
				'show_acc'
    );
    public $timestamps = false;
}


?>
