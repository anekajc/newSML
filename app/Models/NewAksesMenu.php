<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NewAksesMenu extends Model
{
		protected $connection = 'SML';
    protected $primaryKey = 'id';
    protected $table = 'new_aksesmenu';
    protected $fillable = array(
        'USERID',
				'L1',
        'HASACCESS',
    );
    public $timestamps = false;
}

?>
